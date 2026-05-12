<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Contracts\Repositories\DeliveryManRepositoryInterface;
use App\Contracts\Repositories\DeliveryZipCodeRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\OrderTransactionRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\ReviewRepositoryInterface;
use App\Contracts\Repositories\ShippingAddressRepositoryInterface;
use App\Contracts\Repositories\ShopRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Repositories\VendorWalletRepositoryInterface;
use App\Contracts\Repositories\WithdrawRequestRepositoryInterface;
use App\Enums\ExportFileNames\Admin\Vendor as VendorExport;
use App\Enums\ViewPaths\Admin\Vendor;
use App\Enums\WebConfigKey;
use App\Events\VendorRegistrationMailEvent;
use App\Events\VendorStatusUpdateEvent;
use App\Events\WithdrawStatusUpdateEvent;
use App\Exports\SellerListExport;
use App\Exports\SellerWithdrawRequest;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VendorAddRequest;
use App\Models\Seller;
use App\Models\SellerCashHistorie;
use App\Models\SellerWallet;
use App\Services\ShopService;
use App\Services\VendorService;
use App\Traits\CommonTrait;
use App\Traits\PaginatorTrait;
use App\Traits\PushNotificationTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VendorController extends BaseController
{
    use PaginatorTrait;
    use CommonTrait;
    use PushNotificationTrait;

    public function __construct(
        private readonly VendorRepositoryInterface $vendorRepo,
        private readonly OrderRepositoryInterface $orderRepo,
        private readonly ProductRepositoryInterface $productRepo,
        private readonly ReviewRepositoryInterface $reviewRepo,
        private readonly DeliveryManRepositoryInterface $deliveryManRepo,
        private readonly OrderTransactionRepositoryInterface $orderTransactionRepo,
        private readonly ShippingAddressRepositoryInterface $shippingAddressRepo,
        private readonly DeliveryZipCodeRepositoryInterface $deliveryZipCodeRepo,
        private readonly WithdrawRequestRepositoryInterface $withdrawRequestRepo,
        private readonly VendorWalletRepositoryInterface $vendorWalletRepo,
        private readonly ShopRepositoryInterface $shopRepo,
        private readonly VendorService $vendorService,
        private readonly ShopService $shopService,
    ) {}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getListView($request);
    }

    public function getListView(Request $request): View
    {
        $current_date = date('Y-m-d');
        if (!empty($request['status'] ?? "")) {
            $sellers = $this->vendorRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request['searchValue'], filters: ['status' => ($request['status'] ?? "")], relations: ['orders', 'product'], dataLimit: getWebConfig(name: WebConfigKey::PAGINATION_LIMIT));
        } else {
            $sellers = $this->vendorRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request['searchValue'], relations: ['orders', 'product'], dataLimit: getWebConfig(name: WebConfigKey::PAGINATION_LIMIT));
        }
        return view(Vendor::LIST[VIEW], compact('sellers', 'current_date'));
    }

    public function getAddView(Request $request): View
    {
        return view(Vendor::ADD[VIEW]);
    }
    public function add(VendorAddRequest $request): JsonResponse
    {
        $vendor = $this->vendorRepo->add(data: $this->vendorService->getAddData($request));
        $this->shopRepo->add($this->shopService->getAddShopDataForRegistration(request: $request, vendorId: $vendor['id']));
        $this->vendorWalletRepo->add($this->vendorService->getInitialWalletData(vendorId: $vendor['id']));
        $data = [
            'name' => $request['f_name'],
            'status' => 'pending',
            'subject' => translate('Vendor_Registration_Successfully_Completed'),
            'title' => translate('Registration_Complete') . '!',
            'message' => translate('congratulation') . '!' . translate('Your_registration_request_has_been_send_to_admin_successfully') . '!' . translate('Please_wait_until_admin_reviewal') . '.',
        ];
        event(new VendorRegistrationMailEvent($request['email'], $data));
        Helpers::editDeleteLogs('Vendor', 'Vendor', 'Insert');
        return response()->json(['message' => translate('vendor_added_successfully')]);
    }

    public function updateStatus(Request $request): RedirectResponse
    {
        $vendor = $this->vendorRepo->getFirstWhere(params: ['id' => $request['id']]);
        $this->vendorRepo->update(id: $request['id'], data: ['status' => $request['status']]);
        if ($request['status'] == "approved") {
            Toastr::success(translate('Vendor_has_been_approved_successfully'));
        } else if ($request['status'] == "rejected") {
            Toastr::info(translate('Vendor_has_been_rejected_successfully'));
        } else if ($request['status'] == "suspended") {
            $this->vendorRepo->update(id: $request['id'], data: ['auth_token' => Str::random(80)]);
            Toastr::info(translate('Vendor_has_been_suspended_successfully'));
        } else if ($request['status'] == "onlyapproved") {
            Toastr::info(translate('Vendor_has_been_uploaded_product_approve'));
        }
        if ($vendor['status'] == 'pending') {
            if ($request['status'] == "approved") {
                $data = [
                    'name' => $vendor['f_name'],
                    'status' => 'approved',
                    'subject' => translate('Vendor_Registration_Approved'),
                    'title' => translate('registration_Approved') . '!',
                    'message' => translate('your_registration_request_has_been_approved_by_admin') . '.' . translate('now_you_can_complete_your_store_setting_and_start_selling_your_product_on') . getWebConfig('company_name') . '.',
                ];
            } elseif ($request['status'] == "rejected") {
                $data = [
                    'name' => $vendor['f_name'],
                    'status' => 'denied',
                    'subject' => translate('Vendor_Registration_Denied'),
                    'title' => translate('registration_Denied') . '!',
                    'message' => translate('your_registration_request_has_been_denied_by_admin') . '.' . translate('please_contact_with_admin_or_support_center_if_you_have_any_queries') . '.',
                ];
            } elseif ($request['status'] == 'onlyapproved') {
                $data = [
                    'name' => $vendor['f_name'],
                    'status' => 'approved',
                    'subject' => translate('Vendor_Registration_Approved'),
                    'title' => translate('registration_Approved') . '!',
                    'message' => translate('your_registration_request_has_been_approved_by_admin') . '.' . translate('now_you_can_complete_your_store_setting_and_start_selling_your_product_on') . getWebConfig('company_name') . '.',
                ];
            }
        } else {
            if ($request['status'] == "suspended") {
                $data = [
                    'name' => $vendor['f_name'],
                    'status' => 'suspended',
                    'subject' => translate('Account_Suspended'),
                    'title' => translate('Your_Account_Has_Been_Suspended') . '!',
                    'message' => translate('your_account_access_has_been_suspended_by_admin') . '.' . translate('from_now_you_can_access_your_app_and_panel_again') . ' ' . translate('please_contact_us_for_any_queries,_we’re_always_happy_to_help') . '.',
                ];
            } else {
                $data = [
                    'name' => $vendor['f_name'],
                    'status' => 'approved',
                    'subject' => translate('Account_Activate'),
                    'title' => translate('you’ve_got_access_to_your_account_again') . '!',
                    'message' => translate('your_account_suspension_has_been_revoked_by_admin') . '.' . translate('from_now_you_can_access_your_app_and_panel_again') . ' ' . translate('please_contact_us_for_any_queries,_we’re_always_happy_to_help') . '.',
                ];
            }
        }
        event(new VendorRegistrationMailEvent($vendor['email'], $data));
        return back();
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $sellers = $this->vendorRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['searchValue'],
            relations: ['orders', 'product'],
            dataLimit: 'all'
        );

        $active = $sellers->where('status', 'approved')->count();
        $inactive = $sellers->where('status', '!=', 'approved')->count();
        $data = [
            'sellers' => $sellers,
            'search' => $request['searchValue'],
            'active' => $active,
            'inactive' => $inactive,
        ];
        return Excel::download(new SellerListExport($data), VendorExport::EXPORT_XLSX);
    }

    public function getOrderListView(Request $request, $seller_id): View
    {
        $orders = $this->orderRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['searchValue'],
            filters: ['seller_id' => $seller_id, 'seller_is' => 'seller'],
            dataLimit: getWebConfig(name: WebConfigKey::PAGINATION_LIMIT),
        );
        $seller = $this->vendorRepo->getFirstWhere(params: ['id' => $seller_id]);
        return view(Vendor::ORDER_LIST[VIEW], compact('orders', 'seller'));
    }

    public function getProductListView(Request $request, $seller_id): View
    {
        $filters = ['seller_id' => $seller_id, 'added_by' => 'seller'];
        $products = $this->productRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['searchValue'],
            filters: $filters,
            relations: ['translations'],
            dataLimit: getWebConfig(name: WebConfigKey::PAGINATION_LIMIT)
        );
        $seller = $this->vendorRepo->getFirstWhere(params: ['id' => $seller_id]);
        return view(Vendor::PRODUCT_LIST[VIEW], compact('products', 'seller'));
    }

    public function updateSalesCommission(Request $request, $id): RedirectResponse
    {
        if ($request['status'] == 1 && $request['commission'] == null) {
            Toastr::error(translate('you_did_not_set_commission_percentage_field'));
            return back();
        }
        $this->vendorRepo->update(id: $id, data: ['sales_commission_percentage' => $request['commission_status'] == 1 ? $request['commission'] : null]);
        Toastr::success(translate('Commission_percentage_for_this_seller_has_been_updated'));
        Helpers::editDeleteLogs('Vendor', 'Commission Percent', 'Insert');
        return back();
    }

    public function getOrderDetailsView($order_id, $seller_id): View
    {
        $country_restrict_status = getWebConfig(name: 'delivery_country_restriction');
        $zip_restrict_status = getWebConfig(name: 'delivery_zip_code_area_restriction');

        $countries = $country_restrict_status ? $this->get_delivery_country_array() : COUNTRIES;
        $zip_codes = $zip_restrict_status ? $this->deliveryZipCodeRepo->getListWhere(dataLimit: 'all') : 0;

        $order = $this->orderRepo->getFirstWhere(
            params: ['id' => $order_id],
            relations: ['shipping', 'customer'],
        );

        $physical_product = false;
        foreach ($order->details as $product) {
            if (isset($product->product) && $product->product->product_type == 'physical') {
                $physical_product = true;
            }
        }

        $shipping_method = getWebConfig(name: 'shipping_method');

        $delivery_men = $this->deliveryManRepo->getListWhereIn(
            filters: ['is_active' => 1, 'order_seller' => $order['seller_is'], 'seller_id' => $order['seller_id'], 'shipping_method' => $shipping_method],
            dataLimit: 'all',
        );

        $shipping_address = $this->shippingAddressRepo->getFirstWhere(params: ['id' => $order['shipping_address']]);
        $total_delivered = $this->orderRepo->getListWhere(
            filters: ['seller_id' => $order['seller_id'], 'seller_is' => 'seller', 'order_status' => 'delivered', 'order_type' => 'default_type'],
            dataLimit: 'all',
        )->count();

        $linked_orders = $this->orderRepo->getListWhereNotIn(
            filters: ['order_group_id' => $order['order_group_id']],
            whereNotIn: ['order_group_id' => ['def-order-group'], 'id' => [$order['id']]],
            dataLimit: 'all',
        );
        if ($order['order_type'] == 'default_type') {
            $orderCount = $this->orderRepo->getListWhereCount(filters: ['customer_id' => $order['customer_id']]);
        } else {
            $orderCount = $this->orderRepo->getListWhereCount(filters: ['customer_id' => $order['customer_id'], 'order_type' => 'POS']);
        }
        return view(Vendor::ORDER_DETAILS[VIEW], compact(
            'order',
            'seller_id',
            'delivery_men',
            'linked_orders',
            'physical_product',
            'shipping_address',
            'total_delivered',
            'countries',
            'zip_codes',
            'zip_restrict_status',
            'country_restrict_status',
            'orderCount'
        ));
    }

    public function getView(Request $request, $id, $tab = null): View|RedirectResponse
    {

        $seller = $this->vendorRepo->getFirstWhere(params: ['id' => $id, 'withCount' => ['orders', 'product']], relations: ['orders', 'product']);
        if (!$seller) {
            return redirect()->route('admin.sellers.seller-list');
        } else {
            if (empty($seller['all_doc_info'])) {
                $doc_encode = [];
                $loops = ['f_name', 'phone', 'image', 'email', 'user_image', 'bank_name', 'branch', 'ifsc', 'account_no', 'holder_name', 'cancel_check', 'gst', 'aadhar_number', 'aadhar_front_image', 'aadhar_back_image', 'pan_number', 'pancard_image', 'name', 'address', 'pincode', 'gumasta', 'fassai_no', 'fassai_image', 'contact', 'banner'];
                foreach ($loops as $key => $va) {
                    $doc_encode[$va] = 0;
                }
                $this->vendorRepo->update(id: $id, data: ['all_doc_info' => json_encode($doc_encode)]);
            }
        }
        $seller = $this->vendorRepo->getFirstWhere(
            params: ['id' => $id, 'withCount' => ['orders', 'product']],
            relations: ['orders', 'product', 'shop']
        );
        $seller?->product?->map(function ($product) {
            $product['rating'] = $product?->reviews->pluck('rating')->sum();
            $product['rating_count'] = $product->reviews->count();
            $product['single_rating_5'] = 0;
            $product['single_rating_4'] = 0;
            $product['single_rating_3'] = 0;
            $product['single_rating_2'] = 0;
            $product['single_rating_1'] = 0;
            foreach ($product->reviews as $review) {
                $rating = $review->rating;
                if ($rating > 0) {
                    match ($rating) {
                        5 => $product->single_rating_5++,
                        4 => $product->single_rating_4++,
                        3 => $product->single_rating_3++,
                        2 => $product->single_rating_2++,
                        1 => $product->single_rating_1++,
                    };
                }
            }
        });
        $seller['single_rating_5'] = $seller?->product->pluck('single_rating_5')->sum();
        $seller['single_rating_4'] = $seller?->product->pluck('single_rating_4')->sum();
        $seller['single_rating_3'] = $seller?->product->pluck('single_rating_3')->sum();
        $seller['single_rating_2'] = $seller?->product->pluck('single_rating_2')->sum();
        $seller['single_rating_1'] = $seller?->product->pluck('single_rating_1')->sum();
        $seller['total_rating'] = $seller?->product->pluck('rating')->sum();
        $seller['rating_count'] = $seller->product->pluck('rating_count')->sum();
        $seller['average_rating'] = $seller['total_rating'] / ($seller['rating_count'] == 0 ? 1 : $seller['rating_count']);

        if (!isset($seller)) {
            Toastr::error(translate('vendor_not_found_It_may_be_deleted'));
            return back();
        }

        if ($tab == 'order') {
            return $this->getOrderListTabView(request: $request, seller: $seller);
        } else if ($tab == 'product') {
            return $this->getProductListTabView(request: $request, seller: $seller);
        } else if ($tab == 'setting') {
            return $this->getSettingListTabView(request: $request, seller: $seller, id: $id);
        } else if ($tab == 'transaction') {
            return $this->getTransactionListTabView(request: $request, seller: $seller);
        } else if ($tab == 'review') {
            return $this->getReviewListTabView(request: $request, seller: $seller);
        }


        return view(Vendor::VIEW[VIEW], [
            'seller' => $seller,
            'current_date' => date('Y-m-d'),
        ]);
    }

    public function getOrderListTabView(Request $request, $seller): View
    {
        $orders = $this->orderRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['searchValue'],
            filters: ['seller_id' => $seller['id'], 'seller_is' => 'seller', 'order_type' => 'default_type'],
            dataLimit: getWebConfig(name: WebConfigKey::PAGINATION_LIMIT),
        );
        $pendingOrder = $this->orderRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['searchValue'],
            filters: ['seller_id' => $seller['id'], 'seller_is' => 'seller', 'order_type' => 'default_type', 'order_status' => 'pending'],
            dataLimit: 'all',
        )->count();
        $deliveredOrder = $this->orderRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['searchValue'],
            filters: ['seller_id' => $seller['id'], 'seller_is' => 'seller', 'order_type' => 'default_type', 'order_status' => 'delivered'],
            dataLimit: 'all',
        )->count();

        return view(Vendor::VIEW_ORDER[VIEW], compact('seller', 'orders', 'pendingOrder', 'deliveredOrder'));
    }

    public function getProductListTabView(Request $request, $seller): View
    {
        $products = $this->productRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['searchValue'],
            filters: ['seller_id' => $seller['id'], 'added_by' => 'seller'],
            relations: ['translations'],
            dataLimit: getWebConfig(name: WebConfigKey::PAGINATION_LIMIT)
        );
        return view(Vendor::VIEW_PRODUCT[VIEW], compact('seller', 'products'));
    }

    public function getSettingListTabView(Request $request, $seller, $id): View
    {
        return view(Vendor::VIEW_SETTING[VIEW], compact('seller'));
    }
    public function updateSetting(Request $request, $id): RedirectResponse
    {
        if ($request->has('commission')) {
            request()->validate([
                'commission' => 'required|numeric|min:1',
            ]);
            if ($request['commission_status'] == 1 && $request['commission'] == null) {
                Toastr::error(translate('you_did_not_set_commission_percentage_field.'));
            } else {
                $this->vendorRepo->update(id: $id, data: ['sales_commission_percentage' => $request['commission_status'] == 1 ? $request['commission'] : null]);
                Toastr::success(translate('commission_percentage_for_this_seller_has_been_updated.'));
            }
        }
        if ($request->has('gst')) {
            if ($request['gst_status'] == 1 && $request['gst'] == null) {
                Toastr::error(translate('you_did_not_set_GST_number_field.'));
            } else {
                $this->vendorRepo->update(id: $id, data: ['gst' => $request['gst_status'] == 1 ? $request['gst'] : null]);
                Toastr::success(translate('GST_number_for_this_seller_has_been_updated.'));
            }
        }
        if ($request->has('seller_pos_update')) {
            $this->vendorRepo->update(id: $id, data: ['pos_status' => $request->get('seller_pos', 0)]);
            Toastr::success(translate('vendor_pos_permission_updated'));
        }
        return redirect()->back();
    }

    public function getTransactionListTabView(Request $request, $seller): View
    {
        $filters = [
            'seller_is' => 'seller',
            'seller_id' => $seller['id'],
            'status' => $request['status'] ?? 'all'

        ];
        $transactions = $this->orderTransactionRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request['searchValue'],
            filters: $filters,
            relations: ['order.customer'],
            dataLimit: getWebConfig(name: WebConfigKey::PAGINATION_LIMIT),
        );
        return view(Vendor::VIEW_TRANSACTION[VIEW], compact('seller', 'transactions'));
    }

    public function getReviewListTabView(Request $request, $seller): View
    {
        if ($request->has('searchValue')) {
            $product_id = $this->productRepo->getListWhere(
                searchValue: $request['searchValue'],
                filters: ['added_by' => 'seller', 'seller_id' => $seller['id']],
                dataLimit: 'all'
            )->pluck('id')->toArray();
            $filtersBy = [
                'product_id' => $product_id,
            ];
            $reviews = $this->reviewRepo->getListWhereIn(
                orderBy: ['id' => 'desc'],
                filters: ['added_by' => 'seller'],
                whereInFilters: $filtersBy,
                relations: ['product'],
                nullFields: ['delivery_man_id'],
                dataLimit: getWebConfig(name: 'pagination_limit')
            );
        } else {
            $reviews = $this->reviewRepo->getListWhereIn(
                orderBy: ['id' => 'desc'],
                filters: ['product_user_id' => $seller['id']],
                relations: ['product', 'customer'],
                dataLimit: getWebConfig(name: 'pagination_limit')
            );
        }
        return view(Vendor::VIEW_REVIEW[VIEW], [
            'seller' => $seller,
            'reviews' => $reviews,
        ]);
    }

    public function getWithdrawView($withdraw_id, $seller_id): View|RedirectResponse
    {
        $withdrawRequest = $this->withdrawRequestRepo->getFirstWhere(params: ['id' => $withdraw_id], relations: ['seller']);
        if ($withdrawRequest) {
            $withdrawalMethod = is_array($withdrawRequest['withdrawal_method_fields']) ? $withdrawRequest['withdrawal_method_fields'] : json_decode($withdrawRequest['withdrawal_method_fields']);
            $direction = session('direction');
            return view(Vendor::WITHDRAW_VIEW[VIEW], compact('withdrawRequest', 'withdrawalMethod', 'direction'));
        }
        Toastr::error(translate('withdraw_request_not_found'));
        return back();
    }

    public function getWithdrawListView(Request $request): View
    {
        $withdrawRequests = $this->withdrawRequestRepo->getListWhereNull(
            orderBy: ['id' => 'desc'],
            filters: ['approved' => $request['approved']],
            nullFilters: ['delivery_man_id'],
            relations: ['seller'],
            dataLimit: getWebConfig(name: 'pagination_limit')
        );
        return view(Vendor::WITHDRAW_LIST[VIEW], compact('withdrawRequests'));
    }

    public function exportWithdrawList(Request $request): BinaryFileResponse
    {
        $withdrawRequests = $this->withdrawRequestRepo->getListWhereNull(
            orderBy: ['id' => 'desc'],
            filters: ['approved' => $request['approved']],
            nullFilters: ['delivery_man_id'],
            relations: ['seller'],
            dataLimit: 'all'
        );

        $withdrawRequests->map(function ($query) {
            $query->shop_name = isset($query->seller) ? $query->seller->shop->name : '';
            $query->shop_phone = isset($query->seller) ? $query->seller->shop->contact : '';
            $query->shop_address = isset($query->seller) ? $query->seller->shop->address : '';
            $query->shop_email = isset($query->seller) ? $query->seller->email : '';
            $query->withdrawal_amount = setCurrencySymbol(amount: usdToDefaultCurrency(amount: $query->amount), currencyCode: getCurrencyCode(type: 'default'));
            $query->status = $query->approved == 0 ? 'Pending' : ($query->approved == 1 ? 'Approved' : 'Denied');
            $query->note = $query->transaction_note;
            $query->withdraw_method_name = isset($query->withdraw_method) ? $query->withdraw_method->method_name : '';
            if (!empty($query->withdrawal_method_fields)) {
                foreach (json_decode($query->withdrawal_method_fields) as $key => $field) {
                    $query[$key] = $field;
                }
            }
        });

        $pending = $withdrawRequests->where('approved', 0)->count();
        $approved = $withdrawRequests->where('approved', 1)->count();
        $denied = $withdrawRequests->where('approved', 2)->count();

        return Excel::download(
            new SellerWithdrawRequest([
                'withdraw_request' => $withdrawRequests,
                'filter' => session('withdraw_status_filter'),
                'pending' => $pending,
                'approved' => $approved,
                'denied' => $denied,
            ]),
            'Seller-Withdraw-Request.xlsx'
        );
    }


    public function withdrawStatus(Request $request, $id): RedirectResponse
    {
        $withdrawData = [
            'approved' => $request['approved'],
            'transaction_note' => $request['note'],
        ];

        $withdraw = $this->withdrawRequestRepo->getFirstWhere(params: ['id' => $id], relations: ['seller']);
        if (isset($withdraw->seller->cm_firebase_token) && $withdraw->seller->cm_firebase_token) {
            event(new WithdrawStatusUpdateEvent(key: 'withdraw_request_status_message', type: 'seller', lang: $withdraw->deliveryMan?->app_language ?? getDefaultLanguage(), status: $request['approved'], fcmToken: $withdraw->seller?->cm_firebase_token));
        }

        if ($request['approved'] == 1) {
            $this->vendorWalletRepo->getFirstWhere(params: ['seller_id' => $withdraw['seller_id']])->increment('withdrawn', $withdraw['amount']);
            $this->vendorWalletRepo->getFirstWhere(params: ['seller_id' => $withdraw['seller_id']])->decrement('pending_withdraw', $withdraw['amount']);

            $this->withdrawRequestRepo->update(id: $id, data: $withdrawData);
            Toastr::success(translate('Vendor_Payment_has_been_approved_successfully'));
            return redirect()->route('admin.sellers.withdraw_list');
        }

        $this->vendorWalletRepo->getFirstWhere(params: ['seller_id' => $withdraw['seller_id']])->increment('total_earning', $withdraw['amount']);
        $this->vendorWalletRepo->getFirstWhere(params: ['seller_id' => $withdraw['seller_id']])->decrement('pending_withdraw', $withdraw['amount']);
        $this->withdrawRequestRepo->update(id: $id, data: $withdrawData);

        Toastr::info(translate('Vendor_Payment_request_has_been_Denied_successfully'));
        return redirect()->route('admin.sellers.withdraw_list');
    }

    public function DocVerifiedResend(Request $request)
    {
        $id = $request->vendor_id;
        $getvendor = $this->vendorRepo->getFirstWhere(params: ["id" => $id]);
        $update_seller_status = 0;
        if ($getvendor && json_decode($getvendor['all_doc_info'], true) && $request['arrays']) {
            $doc_encode = json_decode($getvendor['all_doc_info'], true);
            foreach (json_decode($getvendor['all_doc_info'], true) as $key => $value) {
                foreach ($request['arrays'] as $key12 => $value12) {
                    if ($value12['name'] == $key) {
                        $doc_encode[$key] = $value12['value'];
                        if ($update_seller_status == 0 && $value12['value'] == 2) {
                            $update_seller_status = 2;
                        }
                    }
                }
            }

            if ($update_seller_status == 2) {
                $notis = new \App\Models\Notification();
                $notis->sent_by = $id;
                $notis->sent_to = "seller";
                $notis->title = "Profile Review";
                $notis->description = $request['reason'];
                $notis->notification_count = 1;
                $notis->image = '';
                $notis->status = 0;
                $notis->created_at = date('Y-m-d H:i:s');
                $notis->save();
            }
            $this->vendorRepo->update(id: $id, data: ['all_doc_info' => json_encode($doc_encode), "reupload_doc_status" => (($update_seller_status == 2) ? 2 : 1), 'update_seller_status' => $update_seller_status]);
        }
        return response()->json(['message' => translate('vendor_successfully')]);
    }

    public function DocVerifiedSuccess(Request $request)
    {
        $id = $request->vendor_id;
        $getvendor = $this->vendorRepo->getFirstWhere(params: ["id" => $id], relations: ['shop']);
        $update_seller_status = 0;
        if ($getvendor && json_decode($getvendor['all_doc_info'], true)) {
            foreach (json_decode($getvendor['all_doc_info'], true) as $key => $value) {
                if ($update_seller_status == 0 && $value == 2) {
                    $update_seller_status = 1;
                    break;
                }
            }
        }
        if ($update_seller_status == 0) {
            $this->vendorRepo->update(id: $id, data: ['verify_status' => 1, 'update_seller_status' => 0]);
            $data = [
                'shop_name' => ($getvendor['shop']['name'] ?? ""),
                "f_name" => ($getvendor['f_name'] ?? ""),
                "l_name" => ($getvendor['l_name'] ?? ""),
                'email' => ($getvendor['email'] ?? ""),
                "phone" => ($getvendor['phone'] ?? ""),
                "gst" => ($getvendor['gst'] ?? ""),
                "shop_address" => (!empty($getvendor['shop']['building_no'])?$getvendor['shop']['building_no'].' '.$getvendor['shop']['address']:$getvendor['shop']['address']),
                'city_name' => ($getvendor['shop']['city_name'] ?? ""),
                "state_name" => ($getvendor['shop']['state_name'] ?? ""),
                "country_name" => ($getvendor['shop']['country_name'] ?? ""),
                "zipcode" => ($getvendor['shop']['pincode'] ?? ""),
                "longitude" => ($getvendor['shop']['longitude'] ?? ""),
                "latitude" => ($getvendor['shop']['latitude'] ?? ""),
                "fssai_code" => ($getvendor['shop']['fassai_no'] ?? ""),
            ];
            $data_wareHouse =  \App\Utils\Helpers::ShipWayWarehouseCreate($data);
            $this->vendorRepo->update(id: $id, data: ['warehouse_id' => ($data_wareHouse['warehouse_response']['warehouse_id'] ?? "")]);
            return response()->json(['status' => 1]);
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function checked_order()
    {
        $seller = Seller::where('checked', 0)->update(['checked' => 1]);
        if ($seller) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
    }

    // Cash Withdraw request
    public function getCashWithdrawView($withdraw_id, $seller_id)
    {
        $cashwithdrawRequest = SellerCashHistorie::where('id', $withdraw_id)->where('seller_id', $seller_id)->with('seller')->first();
        if ($cashwithdrawRequest) {
            return view('admin-views.seller.cash-withdraw-view', compact('cashwithdrawRequest'));
        }
        Toastr::error(translate('withdraw_request_not_found'));
        return back();
    }

    public function getCashWithdrawlist(Request $request)
    {
        $cashwithdrawlist = SellerCashHistorie::when($request->has('status'), function ($query) use ($request) {
            return $query->where('status', $request->approved);
        })->orderBy('id', 'desc')->with(['seller'])->paginate(getWebConfig('pagination_limit'));
        return view('admin-views.seller.cash-withdraw-list', compact('cashwithdrawlist'));
    }
    public function cashwithdrawStatus(Request $request, $id)
    {
        $withdrawData = [
            'status' => $request['status'],
            'transaction_note' => $request['note'],
        ];

        $withdraw = SellerCashHistorie::where('id', $id)->with('seller')->first();
        // dd($withdrawData);
        if (isset($withdraw->seller->cm_firebase_token) && $withdraw->seller->cm_firebase_token) {
            event(new WithdrawStatusUpdateEvent(key: 'cash_withdraw_request_status_message', type: 'seller', lang: $withdraw->deliveryMan?->app_language ?? getDefaultLanguage(), status: $request['status'], fcmToken: $withdraw->seller?->cm_firebase_token));
        }
        // dd($request['status'] == 1);
        if ($request['status'] == 1) {
            SellerWallet::where('seller_id', $withdraw->seller_id)->increment('total_earning', $withdraw->amount);
            $collectedCash = SellerWallet::where('seller_id', $withdraw->seller_id)->get()->value('collected_cash');
            $requestAmount = SellerCashHistorie::where('seller_id', $withdraw->seller_id)->where('status', 0)->get()->value('amount');
            SellerWallet::where('seller_id', $withdraw->seller_id)->update([
                'collected_cash' => $collectedCash - $requestAmount
            ]);
            SellerCashHistorie::where('id', $id)->update($withdrawData);
            Toastr::success(translate('Vendor_cash_Payment_has_been_approved_successfully'));
            return redirect()->route('admin.sellers.cash-withdraw-list');
        }

        Toastr::info(translate('Vendor_Payment_request_has_been_Denied_successfully'));
        return redirect()->route('admin.sellers.cash-withdraw-list');
    }
}
