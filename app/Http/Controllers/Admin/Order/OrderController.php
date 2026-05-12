<?php

namespace App\Http\Controllers\Admin\Order;

use App\Contracts\Repositories\BusinessSettingRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\DeliveryCountryCodeRepositoryInterface;
use App\Contracts\Repositories\DeliveryManTransactionRepositoryInterface;
use App\Contracts\Repositories\DeliveryManWalletRepositoryInterface;
use App\Contracts\Repositories\DeliveryZipCodeRepositoryInterface;
use App\Contracts\Repositories\LoyaltyPointTransactionRepositoryInterface;
use App\Contracts\Repositories\OrderDetailRepositoryInterface;
use App\Contracts\Repositories\OrderExpectedDeliveryHistoryRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\OrderStatusHistoryRepositoryInterface;
use App\Contracts\Repositories\ShippingAddressRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Enums\GlobalConstant;
use App\Enums\ViewPaths\Admin\Order;
use App\Enums\WebConfigKey;
use App\Events\OrderStatusEvent;
use App\Exports\OrderExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\UploadDigitalFileAfterSellRequest;
use App\Models\Chadhava_orders;
use App\Models\OfflinePoojaOrder;
use App\Models\Service_order;
use App\Models\OrderDetail;
use App\Repositories\DeliveryManRepository;
use App\Repositories\OrderTransactionRepository;
use App\Repositories\WalletTransactionRepository;
use App\Services\DeliveryCountryCodeService;
use App\Services\DeliveryManTransactionService;
use App\Services\DeliveryManWalletService;
use App\Services\OrderStatusHistoryService;
use App\Traits\CustomerTrait;
use App\Traits\FileManagerTrait;
use App\Traits\PdfGenerator;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\View as PdfView;

use App\Utils\Helpers;
use App\Models\ShipRocket as ModelsShipRocket;
use App\Models\Order as ModelsOrder;
use App\Models\Order_Pickup;
use App\Models\Seller;
use App\Models\SellerWallet;
use App\Models\User;

class OrderController extends BaseController
{
    use CustomerTrait;
    use PdfGenerator;
    use FileManagerTrait {
        delete as deleteFile;
        update as updateFile;
    }

    public function __construct(
        private readonly OrderRepositoryInterface                        $orderRepo,
        private readonly CustomerRepositoryInterface                     $customerRepo,
        private readonly VendorRepositoryInterface                       $vendorRepo,
        private readonly BusinessSettingRepositoryInterface              $businessSettingRepo,
        private readonly DeliveryCountryCodeRepositoryInterface          $deliveryCountryCodeRepo,
        private readonly DeliveryZipCodeRepositoryInterface              $deliveryZipCodeRepo,
        private readonly DeliveryManRepository                           $deliveryManRepo,
        private readonly ShippingAddressRepositoryInterface              $shippingAddressRepo,
        private readonly OrderExpectedDeliveryHistoryRepositoryInterface $orderExpectedDeliveryHistoryRepo,
        private readonly OrderDetailRepositoryInterface                  $orderDetailRepo,
        private readonly WalletTransactionRepository                     $walletTransactionRepo,
        private readonly DeliveryManWalletRepositoryInterface            $deliveryManWalletRepo,
        private readonly DeliveryManTransactionRepositoryInterface       $deliveryManTransactionRepo,
        private readonly OrderStatusHistoryRepositoryInterface           $orderStatusHistoryRepo,
        private readonly OrderTransactionRepository                      $orderTransactionRepo,
        private readonly LoyaltyPointTransactionRepositoryInterface      $loyaltyPointTransactionRepo,
    ) {}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, $type = 'all'): View
    {
        return $this->getListView(request: $request, status: $type);
    }

    public function getListView(object $request, string $status): View
    {

        $searchValue = $request['searchValue'];

        $filter = $request['filter'];
        $from = $request['from'];
        $to = $request['to'];

        $this->orderRepo->updateWhere(params: ['checked' => 0], data: ['checked' => 1]);

        $vendorId = $request['seller_id'] == '0' ? 1 : $request['seller_id'];
        if ($request['seller_id'] == null) {
            $vendorIs = 'all';
        } elseif ($request['seller_id'] == 'all') {
            $vendorIs = $request['seller_id'];
        } elseif ($request['seller_id'] == '0') {
            $vendorIs = 'admin';
        } else {
            $vendorIs = 'seller';
        }

        $dateType = $request['date_type'];
        $filters = [
            'order_status' => $status,
            'filter' => $request['filter'] ?? 'all',
            'date_type' => $dateType,
            'from' => $request['from'],
            'to' => $request['to'],
            'delivery_man_id' => $request['delivery_man_id'],
            'customer_id' => $request['customer_id'],
            'seller_id' => $vendorId,
            'seller_is' => $vendorIs,
        ];

        $orders = $this->orderRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request['searchValue'], filters: $filters, relations: ['customer', 'seller.shop'], dataLimit: getWebConfig(name: WebConfigKey::PAGINATION_LIMIT));
        $sellers = $this->vendorRepo->getByStatusExcept(status: 'pending', relations: ['shop']);

        $customer = "all";
        if (isset($request['customer_id']) && $request['customer_id'] != 'all' && !is_null($request->customer_id) && $request->has('customer_id')) {
            $customer = $this->customerRepo->getFirstWhere(params: ['id' => $request['customer_id']]);
        }

        $vendorId = $request['seller_id'];
        $customerId = $request['customer_id'];

        return view(Order::LIST[VIEW], compact(
            'orders',
            'searchValue',
            'from',
            'to',
            'status',
            'filter',
            'sellers',
            'customer',
            'vendorId',
            'customerId',
            'dateType',
        ));
    }

    public function exportList(Request $request, $status): BinaryFileResponse|RedirectResponse
    {
        $vendorId = $request['seller_id'] == '0' ? 1 : $request['seller_id'];
        if ($request['seller_id'] == null) {
            $vendorIs = 'all';
        } elseif ($request['seller_id'] == 'all') {
            $vendorIs = $request['seller_id'];
        } elseif ($request['seller_id'] == '0') {
            $vendorIs = 'admin';
        } else {
            $vendorIs = 'seller';
        }

        $filters = [
            'order_status' => $status,
            'filter' => $request['filter'] ?? 'all',
            'date_type' => $request['date_type'],
            'from' => $request['from'],
            'to' => $request['to'],
            'delivery_man_id' => $request['delivery_man_id'],
            'customer_id' => $request['customer_id'],
            'seller_id' => $vendorId,
            'seller_is' => $vendorIs,
        ];

        $orders = $this->orderRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request['searchValue'], filters: $filters, relations: ['customer', 'seller.shop'], dataLimit: 'all');

        /** order status count  */
        $status_array = [
            'pending' => 0,
            'confirmed' => 0,
            'processing' => 0,
            'out_for_delivery' => 0,
            'delivered' => 0,
            'returned' => 0,
            'failed' => 0,
            'canceled' => 0,
        ];
        $orders?->map(function ($order) use (&$status_array) { // Pass by reference using &
            if (isset($status_array[$order->order_status])) {
                $status_array[$order->order_status]++;
            }
            $order?->orderDetails?->map(function ($details) use ($order) {
                $order['total_qty'] += $details->qty;
                $order['total_price'] += $details->qty * $details->price + ($details->tax_model == 'include' ? $details->qty * $details->tax : 0);
                $order['total_discount'] += $details->discount;
                $order['total_tax'] += $details->tax_model == 'exclude' ? $details->tax : 0;
            });
        });
        /** order status count  */

        /** date */
        $date_type = $request->date_type ?? '';
        $from = match ($date_type) {
            'this_year' => date('Y-01-01'),
            'this_month' => date('Y-m-01'),
            'this_week' => Carbon::now()->subDays(7)->startOfWeek()->format('Y-m-d'),
            default => $request['from'] ?? '',
        };
        $to = match ($date_type) {
            'this_year' => date('Y-12-31'),
            'this_month' => date('Y-m-t'),
            'this_week' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            default => $request['to'] ?? '',
        };
        /** end  */
        $seller = [];
        if ($request['seller_id'] != 'all' && $request->has('seller_id') && $request->seller_id != 0) {
            $seller = $this->vendorRepo->getFirstWhere(['id' => $request['seller_id']]);
        }
        $customer = [];
        if ($request['customer_id'] != 'all' && $request->has('customer_id')) {
            $customer = $this->customerRepo->getFirstWhere(['id' => $request['customer_id']]);
        }

        $data = [
            'orders' => $orders,
            'order_status' => $status,
            'seller' => $seller,
            'customer' => $customer,
            'status_array' => $status_array,
            'searchValue' => $request['searchValue'],
            'order_type' => $filter ?? 'all',
            'from' => $from,
            'to' => $to,
            'date_type' => $date_type,
            'defaultCurrencyCode' => getCurrencyCode(),
        ];
        return Excel::download(new OrderExport($data), 'Orders.xlsx');
    }

    public function getView(string|int $id, DeliveryCountryCodeService $service): View
    {
        $countryRestrictStatus = getWebConfig(name: 'delivery_country_restriction');
        $zipRestrictStatus = getWebConfig(name: 'delivery_zip_code_area_restriction');
        $deliveryCountry = $this->deliveryCountryCodeRepo->getList(dataLimit: 'all');
        $countries = $countryRestrictStatus ? $service->getDeliveryCountryArray(deliveryCountryCodes: $deliveryCountry) : GlobalConstant::COUNTRIES;
        $zipCodes = $zipRestrictStatus ? $this->deliveryZipCodeRepo->getList(dataLimit: 'all') : 0;
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $order = $this->orderRepo->getFirstWhere(params: ['id' => $id], relations: ['details.productAllStatus', 'verificationImages', 'shipping', 'seller.shop', 'offlinePayments', 'deliveryMan']);

        $physicalProduct = false;
        if (isset($order->details)) {
            foreach ($order->details as $product) {
                if (isset($product->product) && $product->product->product_type == 'physical') {
                    $physicalProduct = true;
                }
            }
        }

        $whereNotIn = [
            'order_group_id' => ['def-order-group'],
            'id' => [$order['id']],
        ];
        $linkedOrders = $this->orderRepo->getListWhereNotIn(filters: ['order_group_id' => $order['order_group_id']], whereNotIn: $whereNotIn, dataLimit: 'all');
        $totalDelivered = $this->orderRepo->getListWhere(filters: ['seller_id' => $order['seller_id'], 'order_status' => 'delivered', 'order_type' => 'default_type'], dataLimit: 'all')->count();
        $shippingMethod = getWebConfig('shipping_method');

        $sellerId = 0;
        if ($order['seller_is'] == 'seller' && $shippingMethod == 'sellerwise_shipping') {
            $sellerId = $order['seller_id'];
        }
        $filters = [
            'is_active' => 1,
            'seller_id' => $sellerId,
        ];
        $deliveryMen = $this->deliveryManRepo->getListWhere(filters: $filters, dataLimit: 'all');
        if ($order['order_type'] == 'default_type') {
            $orderCount = $this->orderRepo->getListWhereCount(filters: ['customer_id' => $order['customer_id']]);
            return view(Order::VIEW[VIEW], compact(
                'order',
                'linkedOrders',
                'deliveryMen',
                'totalDelivered',
                'companyName',
                'companyWebLogo',
                'physicalProduct',
                'countryRestrictStatus',
                'zipRestrictStatus',
                'countries',
                'zipCodes',
                'orderCount'
            ));
        } else {
            $orderCount = $this->orderRepo->getListWhereCount(filters: ['customer_id' => $order['customer_id'], 'order_type' => 'POS']);
            return view(Order::VIEW_POS[VIEW], compact('order', 'companyName', 'companyWebLogo', 'orderCount'));
        }
    }

    public function generateInvoice(string|int $id): void
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $order = $this->orderRepo->getFirstWhere(params: ['id' => $id], relations: ['seller', 'shipping', 'details']);
        $vendor = $this->vendorRepo->getFirstWhere(params: ['id' => $order['details']->first()->seller_id]);

        $mpdf_view = PdfView::make(
            Order::GENERATE_INVOICE[VIEW],
            compact('order', 'vendor', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo')
        );
        $this->generatePdf($mpdf_view, 'order_invoice_', $order['id']);
    }

    public function updateStatus(
        Request                       $request,
        DeliveryManTransactionService $deliveryManTransactionService,
        DeliveryManWalletService      $deliveryManWalletService,
        OrderStatusHistoryService     $orderStatusHistoryService,
    ): JsonResponse {
        //whatsap
        if ($request['order_status'] == 'confirmed') {
            $orderId = \App\Models\Order::latest()->value('id');

            $order = \App\Models\Order::latest()->first();
            $productId = \App\Models\OrderDetail::where('order_id', $orderId)->value('product_id');
            $productName = \App\Models\Product::where('id', $productId)->value('name');
            $userInfo = \App\Models\User::where('id', ($order->customer_id ?? ""))->first();

            $message_data = [
                'product_name' => $productName,
                'orderId' => $orderId,
                'order_amount' => webCurrencyConverter(amount: (float)$order->order_amount ?? 0),
                'customer_id' => ($order->customer_id ?? ""),
            ];
            $messages =  Helpers::whatsappMessage('ecom', 'Confirmed', $message_data);
        }

        if ($request['order_status'] == 'processing') {
            $response = Helpers::ShipwayGetCarrierrates($request->input('fromPincode'), $request->input('toPincode'), $request->input('paymentType'), $request->input('order_weight'), $request->input('box_length'), $request->input('box_breadth'), $request->input('box_height'));
            $rateCards = collect($response['rate_card']);
            // dd($rateCards);
            $lowest_price = $rateCards->map(function ($item) {
                $item['total_cost'] = $item['delivery_charge'] + $item['rto_charge'];
                return $item;
            })->sortBy('total_cost')->first();
            $deliveryCharge = '';
            if ($request->input('paymentType') == 'cod') {
                $charge1 = $lowest_price['delivery_charge'] + ($lowest_price['delivery_charge'] * (18 / 100));
                $charge2 =  $lowest_price['cod_charges'] + ($lowest_price['cod_charges'] * (18 / 100));
                $deliveryCharge = $charge1 + $charge2;
            } else {
                $deliveryCharge = $lowest_price['delivery_charge'] + ($lowest_price['delivery_charge'] * (18 / 100));
            }
            if ($lowest_price['zone'] == 1) {
                $orderWeight = [
                    'order_weight' => $request->input('order_weight'),
                    'box_length' => $request->input('box_length'),
                    'box_breadth' => $request->input('box_breadth'),
                    'box_height' => $request->input('box_height'),
                    'delivery_type' => 'self_delivery',
                    'delivery_partner' => 'self_delivery',
                    'delivery_order_id' => $request->id,
                    'order_status' => $request->input('order_status'),
                    'delivery_charge' => $deliveryCharge
                ];
                ModelsOrder::where('id', $request->id)->update(array_merge(
                    $orderWeight,
                    ['order_status' => 'pickup']
                ));
                $orderId = \App\Models\Order::latest()->value('id');
                $order = \App\Models\Order::latest()->first();
                $productId = \App\Models\OrderDetail::where('order_id', $orderId)->value('product_id');
                $productName = \App\Models\Product::where('id', $productId)->value('name');
                $userInfo = \App\Models\User::where('id', ($order->customer_id ?? ""))->first();

                $message_data = [
                    'product_name' => $productName,
                    'orderId' => $orderId,
                    'order_amount' => webCurrencyConverter(amount: (float)$order->order_amount ?? 0),
                    'customer_id' => ($order->customer_id ?? ""),
                ];
                $messages =  Helpers::whatsappMessage('ecom', 'Processing', $message_data);
                Toastr::success(translate('Order Successfully Assign to Self.'));
                return response()->json(['order_status' => $request['order_status']]);
            } else {
                $placeOrder = Helpers::ShipWayorderPlace($request->id, $request->input('order_weight'), $request->input('box_length'), $request->input('box_breadth'), $request->input('box_height'));
                if (isset($placeOrder) && $placeOrder['success'] == true) {
                    // dd($placeOrder);
                    $orderWeight = [
                        'order_weight' => $request->input('order_weight'),
                        'box_length' => $request->input('box_length'),
                        'box_breadth' => $request->input('box_breadth'),
                        'box_height' => $request->input('box_height'),
                        'delivery_partner' => 'shipway',
                        'delivery_type' => 'shipway',
                        'delivery_order_id' => $request->id,
                        'order_status' => $request->input('order_status'),
                        'delivery_channel_id' => $request->input('warehouse_id'), //warehouse id
                        'delivery_shipment_id' => $lowest_price['carrier_id'], //carrie_id
                        'delivery_service_name' => $lowest_price['courier_name'],
                        'delivery_charge' => $deliveryCharge
                    ];
                    $carrierData = [
                        'courier_name' => $lowest_price['courier_name'],
                        'carrier_id' => $lowest_price['carrier_id'],
                        'delivery_charge' => $deliveryCharge,
                        'payment_type' => $request->input('paymentType') == 'prepaid' ? 'P' : ($request->input('paymentType') == 'cod' ? 'C' : ''),
                        'order_ids' => $request->id,
                        'warehouse_id' => $request->input('warehouse_id'),
                        'return_warehouse_id' => $request->input('warehouse_id'),
                    ];
                    // dd($orderWeight,$carrierData);
                    $orderExists = Order_Pickup::where('order_ids', $request->id)->exists();
                    if (!$orderExists) {
                        Order_Pickup::create($carrierData);
                    } else {
                        Toastr::success(translate('Order_pickup_already_exists'));
                    }
                    ModelsOrder::where('id', $request->id)->update($orderWeight);

                    // return response()->json(['order_status' => $request['order_status']]);
                    $responseLabel = Helpers::ShipWayorderLabelGenration($request->id);
                    // dd($responseLabel);
                    if (isset($responseLabel) && $responseLabel['success'] == true) {
                        if (isset($responseLabel['awb_response'])) {
                            $awbresponse = $responseLabel['awb_response'];
                            // dd($awbresponse);
                            if (isset($awbresponse['success']) && $awbresponse['success'] == true) {
                                $trakingnumber = [
                                    'third_party_delivery_tracking_id' => $awbresponse['AWB'],
                                    'shippingurl' => $awbresponse['shipping_url'],
                                    'message' => $awbresponse['message'],
                                ];
                                $pickupData = [
                                    'awb' => $awbresponse['AWB'],
                                    'shippingurl' => $awbresponse['shipping_url'],
                                    'message' => $awbresponse['message'],
                                ];
                                // dd($trakingnumber,$pickupData);
                                ModelsOrder::where('id', $request->id)->update($trakingnumber);
                                Order_Pickup::where('order_ids', $request->id)->update($pickupData);
                                $orderId = \App\Models\Order::latest()->value('id');
                                $order = \App\Models\Order::latest()->first();
                                $productId = \App\Models\OrderDetail::where('order_id', $orderId)->value('product_id');
                                $productName = \App\Models\Product::where('id', $productId)->value('name');
                                $userInfo = \App\Models\User::where('id', ($order->customer_id ?? ""))->first();
                                $message_data = [
                                    'product_name' => $productName,
                                    'orderId' => $orderId,
                                    'order_amount' => webCurrencyConverter(amount: (float)$order->order_amount ?? 0),
                                    'customer_id' => ($order->customer_id ?? ""),
                                    'tracking' => ($awbresponse['AWB'] ?? ""),
                                ];
                                $messages =  Helpers::whatsappMessage('ecom', 'Pickup', $message_data);
                                Toastr::success(translate('AWB No. already assigned.'));
                                return response()->json(['order_status' => $request['order_status']]);
                            } else {
                                $pickupData = [
                                    'message' => $awbresponse['error'] ?? 'An error occurred',
                                ];
                                Order_Pickup::where('order_ids', $request->id)->update($pickupData);
                                ModelsOrder::where('id', $request->id)->update(['order_status' => 'confirmed']);
                                Toastr::error(translate('Order_processing_not_to_delivery_portal'));
                                return response()->json(['order_status' => $request['order_status']]);
                            }
                        } else {
                            Toastr::error(translate('AWB response missing in ShipWay response.'));
                            return response()->json(['error' => 'AWB response missing in ShipWay response.'], 400);
                        }
                    } else if (isset($response['success']) && $response['success'] == true) {
                        // ModelsOrder::where('id', $request->id)->update(['order_status' => 'processing']);
                        Order_Pickup::where('order_ids', $request->id)->update(['message' => $response['message']]);
                        ModelsOrder::where('id', $request->id)->update(['order_status' => 'confirmed', 'message' => $response['message']]);
                        Toastr::error(translate('Order_proccessng_not_to_delivery_portal'));
                        return response()->json(['order_status' => $request['order_status']]);
                    }
                } else if (isset($placeOrder) && $placeOrder['success'] == false) {
                    ModelsOrder::where('id', $request->id)->update(['order_status' => 'confirmed']);
                    $errorMessage = $placeOrder['message'];
                    Toastr::error(translate($errorMessage));
                    return response()->json(['order_status' => $request['order_status']]);
                }
            }
            return response()->json($response);
        } else if ($request['order_status'] == 'pickup') {
            $response = Helpers::ShipwayCreatemanifest($request->id);
            if (isset($response['status']) && $response['status'] == true) {
                ModelsOrder::where('id', $request->id)->update([
                    'manifest_id' => $response['manifest_ids'],
                    'order_status' => $request->input('order_status')
                ]);
                Toastr::success(translate($response['message']));
                return response()->json(['order_status' => $request['order_status']]);
            } else if (isset($response['status']) && $response['status'] == false) {
                $errorMessage = $response['message'];
                ModelsOrder::where('id', $request->id)->update(['order_status' => 'processing']);
                Toastr::error(translate($errorMessage));
                return response()->json(['order_status' => $request['order_status']]);
            }
        }
        $order = $this->orderRepo->getFirstWhere(params: ['id' => $request['id']], relations: ['customer', 'seller.shop', 'deliveryMan']);

        if (!$order['is_guest'] && !isset($order['customer'])) {
            return response()->json(['customer_status' => 0], 200);
        }

        if ($order['payment_method'] != 'cash_on_delivery' && $request['order_status'] == 'delivered' && $order['payment_status'] != 'paid') {
            return response()->json(['payment_status' => 0], 200);
        }

        $this->orderRepo->updateStockOnOrderStatusChange($request['id'], $request['order_status']);
        $this->orderRepo->update(id: $request['id'], data: ['order_status' => $request['order_status']]);

        event(new OrderStatusEvent(key: $request['order_status'], type: 'customer', order: $order));

        if ($request['order_status'] == 'canceled') {
            ModelsOrder::where('id', $request->id)->update(['added_by' => 'admin']);
            event(new OrderStatusEvent(key: 'canceled', type: 'delivery_man', order: $order));
        }
        if ($order['seller_is'] == 'seller') {
            if ($request['order_status'] == 'canceled') {
                event(new OrderStatusEvent(key: 'canceled', type: 'seller', order: $order));
            } elseif ($request['order_status'] == 'delivered') {
                event(new OrderStatusEvent(key: 'delivered', type: 'seller', order: $order));
            }
        }

        $loyaltyPointStatus = getWebConfig(name: 'loyalty_point_status');

        if ($loyaltyPointStatus == 1 && !$order['is_guest'] && $request['order_status'] == 'delivered' && ($order['payment_method'] == 'cash_on_delivery' ? $order['payment_status'] == 'unpaid' : $order['payment_status'] == 'paid')) {
            $this->loyaltyPointTransactionRepo->addLoyaltyPointTransaction(userId: $order['customer_id'], reference: $order['id'], amount: usdToDefaultCurrency(amount: $order['order_amount'] - $order['shipping_cost']), transactionType: 'order_place');
        }

        $refEarningStatus = getWebConfig(name: 'ref_earning_status') ?? 0;
        $refEarningExchangeRate = getWebConfig(name: 'ref_earning_exchange_rate') ?? 0;

        if (!$order['is_guest'] && $refEarningStatus == 1 && $request['order_status'] == 'delivered' && ($order['payment_method'] == 'cash_on_delivery' ? $order['payment_status'] == 'unpaid' : $order['payment_status'] == 'paid')) {

            $customer = $this->customerRepo->getFirstWhere(params: ['id' => $order['customer_id']]);
            $isFirstOrder = $this->orderRepo->getListWhereCount(filters: ['customer_id' => $order['customer_id'], 'order_status' => 'delivered', 'payment_status' => 'paid']);
            $referredByUser = $this->customerRepo->getFirstWhere(params: ['id' => $customer['referred_by']]);

            if ($isFirstOrder == 1 && isset($customer->referred_by) && isset($referredByUser)) {
                $this->walletTransactionRepo->addWalletTransaction(
                    user_id: $referredByUser['id'],
                    amount: floatval($refEarningExchangeRate),
                    transactionType: 'add_fund_by_admin',
                    reference: 'earned_by_referral'
                );
            }
        }

        if ($order['delivery_man_id'] && $request->order_status == 'delivered') {
            $deliverymanWallet = $this->deliveryManWalletRepo->getFirstWhere(params: ['delivery_man_id' => $order['delivery_man_id']]);
            $cashInHand = $order['payment_method'] == 'cash_on_delivery' ? $order['order_amount'] : 0;

            if (empty($deliverymanWallet)) {
                $deliverymanWalletData = $deliveryManWalletService->getDeliveryManData(id: $order['delivery_man_id'], deliverymanCharge: $order['deliveryman_charge'], cashInHand: $cashInHand);
                $this->deliveryManWalletRepo->add(data: $deliverymanWalletData);
            } else {
                $deliverymanWalletData = [
                    'current_balance' => $deliverymanWallet['current_balance'] + currencyConverter($order['deliveryman_charge']) ?? 0,
                    'cash_in_hand' => $deliverymanWallet['cash_in_hand'] + currencyConverter($cashInHand) ?? 0,
                ];

                $this->deliveryManWalletRepo->updateWhere(params: ['delivery_man_id' => $order['delivery_man_id']], data: $deliverymanWalletData);
                $orderId = \App\Models\Order::latest()->value('id');
                $order = \App\Models\Order::latest()->first();
                $productId = \App\Models\OrderDetail::where('order_id', $orderId)->value('product_id');
                $productName = \App\Models\Product::where('id', $productId)->value('name');
                $userInfo = \App\Models\User::where('id', ($order->customer_id ?? ""))->first();

                $message_data = [
                    'product_name' => $productName,
                    'orderId' => $orderId,
                    'order_amount' => webCurrencyConverter(amount: (float)$order->order_amount ?? 0),
                    'customer_id' => ($order->customer_id ?? ""),
                ];
                $messages =  Helpers::whatsappMessage('ecom', 'Delivered', $message_data);
            }

            if ($order['deliveryman_charge'] && $request['order_status'] == 'delivered') {
                $deliveryManTransactionData = $deliveryManTransactionService->getDeliveryManTransactionData(amount: $order['deliveryman_charge'], addedBy: 'admin', id: $order['delivery_man_id'], transactionType: 'deliveryman_charge');
                $this->deliveryManTransactionRepo->add($deliveryManTransactionData);
            }
        }

        $orderStatusHistoryData = $orderStatusHistoryService->getOrderHistoryData(orderId: $request['id'], userId: 0, userType: 'admin', status: $request['order_status']);
        $this->orderStatusHistoryRepo->add($orderStatusHistoryData);

        $transaction = $this->orderTransactionRepo->getFirstWhere(params: ['order_id' => $order['id']]);
        if (isset($transaction) && $transaction['status'] == 'disburse') {
            return response()->json($request['order_status']);
        }

        if ($request['order_status'] == 'delivered' && $order['seller_id'] != null) {
            $taxSummary = OrderDetail::where('order_id', $order->id)->get()->groupBy('tax')
                ->map(function ($items) {
                    return $items->sum('tax');
                });
            $taxSum = $taxSummary->sum();
            $finalamount =  $order->delivery_charge + $order->admin_commission;
            $orderData = ModelsOrder::where('id', $order['id'])->update(['seller_adv_amount' => $finalamount]);
            $this->orderRepo->manageWalletOnOrderStatusChange(order: $order, receivedBy: 'seller');
            $advamount = ModelsOrder::where('seller_id', $order['seller_id'])->sum('seller_adv_amount');
            SellerWallet::where('seller_id', $order['seller_id'])->update([
                'seller_adv_amount' => '-' . $advamount,
            ]);
            $this->orderDetailRepo->updateWhere(params: ['order_id' => $order['id']], data: ['delivery_status' => 'delivered']);
            $orderId = \App\Models\Order::latest()->value('id');
                $order = \App\Models\Order::latest()->first();
                $productId = \App\Models\OrderDetail::where('order_id', $orderId)->value('product_id');
                $productName = \App\Models\Product::where('id', $productId)->value('name');
                $userInfo = \App\Models\User::where('id', ($order->customer_id ?? ""))->first();

                $message_data = [
                    'product_name' => $productName,
                    'orderId' => $orderId,
                    'order_amount' => webCurrencyConverter(amount: (float)$order->order_amount ?? 0),
                    'customer_id' => ($order->customer_id ?? ""),
                ];
                $messages =  Helpers::whatsappMessage('ecom', 'Delivered', $message_data);
        }

        return response()->json($request['order_status']);
    }

    public function updateAddress(Request $request): RedirectResponse
    {
        $order = $this->orderRepo->getFirstWhere(params: ['id' => $request['order_id']], relations: ['seller.shop', 'deliveryMan']);
        $shippingAddressData = json_decode(json_encode($order['shipping_address_data']), true);
        $billingAddressData = json_decode(json_encode($order['billing_address_data']), true);
        $commonAddressData = [
            'contact_person_name' => $request['name'],
            'phone' => $request['phone_number'],
            'country' => $request['country'],
            'city' => $request['city'],
            'zip' => $request['zip'],
            'address' => $request['address'],
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            'updated_at' => now(),
        ];

        if ($request['address_type'] == 'shipping') {
            $shippingAddressData = array_merge($shippingAddressData, $commonAddressData);
        } elseif ($request['address_type'] == 'billing') {
            $billingAddressData = array_merge($billingAddressData, $commonAddressData);
        }

        $updateData = [];
        if ($request['address_type'] == 'shipping') {
            $updateData['shipping_address_data'] = json_encode($shippingAddressData);
        } elseif ($request['address_type'] == 'billing') {
            $updateData['billing_address_data'] = json_encode($billingAddressData);
        }

        if (!empty($updateData)) {
            $this->orderRepo->update(id: $request['order_id'], data: $updateData);
        }

        if ($order->seller_is == 'seller') {
            OrderStatusEvent::dispatch('order_edit_message', 'seller', $order);
        }

        if ($order->delivery_type == 'self_delivery' && $order->delivery_man_id) {
            OrderStatusEvent::dispatch('order_edit_message', 'delivery_man', $order);
        }

        Toastr::success(translate('successfully_updated'));
        return back();
    }

    public function updateDeliverInfo(Request $request): RedirectResponse
    {
        $updateData = [
            'delivery_type' => 'third_party_delivery',
            'delivery_service_name' => $request['delivery_service_name'],
            'third_party_delivery_tracking_id' => $request['third_party_delivery_tracking_id'],
            'delivery_man_id' => null,
            'deliveryman_charge' => 0,
            'expected_delivery_date' => null,
        ];
        $this->orderRepo->update(id: $request['order_id'], data: $updateData);

        Toastr::success(translate('updated_successfully'));
        return back();
    }

    public function addDeliveryMan(string|int $order_id, string|int $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }

        $orderData = $this->orderRepo->getFirstWhere(params: ['id' => $order_id]);
        $order = [
            'seller_is' => $orderData->seller_is,
            'delivery_man_id' => $delivery_man_id,
            'delivery_type' => 'self_delivery',
            'delivery_service_name' => null,
            'third_party_delivery_tracking_id' => null,
        ];
        $this->orderRepo->update(id: $order_id, data: $order);

        $order = $this->orderRepo->getFirstWhere(params: ['id' => $order_id], relations: ['seller.shop', 'deliveryMan']);

        event(new OrderStatusEvent(key: 'new_order_assigned_message', type: 'delivery_man', order: $order));
        /** for seller product send notification */
        if ($order['seller_is'] == 'seller') {
            event(new OrderStatusEvent(key: 'delivery_man_assign_by_admin_message', type: 'seller', order: $order));
        }
        /** end */

        return response()->json(['status' => true], 200);
    }

    public function updateAmountDate(Request $request): JsonResponse
    {
        $userId = 0;
        $status = $this->orderRepo->updateAmountDate(request: $request, userId: $userId, userType: 'admin');
        $order = $this->orderRepo->getFirstWhere(params: ['id' => $request['order_id']], relations: ['customer', 'deliveryMan']);

        $fieldName = $request['field_name'];
        $message = '';
        if ($fieldName == 'expected_delivery_date') {
            OrderStatusEvent::dispatch('expected_delivery_date', 'delivery_man', $order);
            $message = translate("expected_delivery_date_added_successfully");
        } elseif ($fieldName == 'deliveryman_charge') {
            OrderStatusEvent::dispatch('delivery_man_charge', 'delivery_man', $order);
            $message = translate("deliveryman_charge_added_successfully");
        }

        return response()->json(['status' => $status, 'message' => $message], $status ? 200 : 403);
    }

    public function getCustomers(Request $request): JsonResponse
    {
        $allCustomer = ['id' => 'all', 'text' => 'All customer'];
        $customers = $this->customerRepo->getCustomerNameList(request: $request)->toArray();
        array_unshift($customers, $allCustomer);

        return response()->json($customers);
    }

    public function updatePaymentStatus(Request $request): JsonResponse
    {
        $order = $this->orderRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($order['payment_status'] == 'paid') {
            return response()->json(['error' => translate('when_payment_status_paid_then_you_can`t_change_payment_status_paid_to_unpaid') . '.']);
        }

        if ($order['is_guest'] == '0' && !isset($order['customer'])) {
            return response()->json(['customer_status' => 0], 200);
        }
        $this->orderRepo->update(id: $request['id'], data: ['payment_status' => $request['payment_status']]);
        return response()->json($request['payment_status']);
    }

    public function filterInHouseOrder(): RedirectResponse
    {
        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            session()->put('show_inhouse_orders', 0);
        } else {
            session()->put('show_inhouse_orders', 1);
        }
        return back();
    }

    public function uploadDigitalFileAfterSell(UploadDigitalFileAfterSellRequest $request): RedirectResponse
    {
        $orderDetails = $this->orderDetailRepo->getFirstWhere(['id' => $request['order_id']]);
        $digitalFileAfterSell = $this->updateFile(dir: 'product/digital-product/', oldImage: $orderDetails['digital_file_after_sell'], format: $request['digital_file_after_sell']->getClientOriginalExtension(), image: $request->file('digital_file_after_sell'), fileType: 'file');

        if ($this->orderDetailRepo->update(id: $orderDetails['id'], data: ['digital_file_after_sell' => $digitalFileAfterSell])) {
            Toastr::success(translate('digital_file_upload_successfully'));
        } else {
            Toastr::error(translate('digital_file_upload_failed'));
        }
        return back();
    }

    public function getOrderData(): JsonResponse
    {
        $newOrder = $this->orderRepo->getListWhere(filters: ['checked' => 0], dataLimit: 'all')->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_order' => $newOrder]
        ]);
    }

    public function getorderpooja(): JsonResponse
    {
        $newPooja = Service_order::where('status', 0)->where('type', 'pooja')->where('checked', 0)->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_pooja' => $newPooja]
        ]);
    }

    public function getOrderofflinepooja(): JsonResponse
    {
        $newOfflinePooja = OfflinePoojaOrder::where('status', 0)->where('checked', 0)->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_offlinepooja' => $newOfflinePooja]
        ]);
    }

    public function getordercounselling(): JsonResponse
    {
        $counselling = Service_order::where('status', 0)->where('type', 'counselling')->where('checked', 0)->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_counselling' => $counselling]
        ]);
    }

    public function getOrdervip(): JsonResponse
    {
        $vip = Service_order::where('status', 0)->where('type', 'vip')->where('checked', 0)->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_vip' => $vip]
        ]);
    }
    public function getOrderChadhava(): JsonResponse
    {
        $chadhava = Chadhava_orders::where('status', 0)->where('type', 'chadhava')->where('checked', 0)->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_chadhava' => $chadhava]
        ]);
    }
    public function getOrderAnushthan(): JsonResponse
    {
        $anushthan = Service_order::where('status', 0)->where('type', 'anushthan')->where('checked', 0)->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_anushthan' => $anushthan]
        ]);
    }

    public function getCustomerList(): JsonResponse
    {
        $customer = User::where('checked', 0)->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_customer' => $customer]
        ]);
    }
    public function getVendorRegister(): JsonResponse
    {
        $newVendor = Seller::where('checked', 0)->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_vendor' => $newVendor]
        ]);
    }

   
    public function rejected_all_order(): JsonResponse
    {
        $rejectedOrders = Service_order::where('type', 'pooja')
            ->where('status', 0)
            ->where('is_completed', 0)
            ->where('booking_date', '<', date('Y-m-d', strtotime('-1 day')))
            ->pluck('id');
        if ($rejectedOrders->isNotEmpty()) {
            $rejectOrderDetails = Service_order::whereIn('id', $rejectedOrders)->update(['order_status' => 6, 'status' => 6]);
        } else {
            $rejectOrderDetails = 0;
        }
        return response()->json([
            'success' => 1,
            'data' => ['rejectedOrder' => $rejectOrderDetails]
        ]);
    }
    public function delivery_partner(Request $request)
    {
        $orderId = $request->order_id;
        if ($request->delivery_partner == 'shipway') {
            $orderWeight = [
                'order_weight' => $request->input('order_weight'),
                'box_length' => $request->input('box_length'),
                'box_breadth' => $request->input('box_breadth'),
                'box_height' => $request->input('box_height')
            ];
            ModelsOrder::where('id', $request->order_id)->update($orderWeight);
            $placeOrder = Helpers::ShipWayorderPlace($orderId);
            // dd($placeOrder);
            if (isset($placeOrder) && $placeOrder['success'] == true) {
                ModelsOrder::where('id', $orderId)->update(['delivery_partner' => $request->delivery_partner, 'delivery_order_id' => $request->order_id]);
                Toastr::success(translate('order assigned to shipway successfully'));
                return back();
            } else if (isset($placeOrder) && $placeOrder['status'] == 400) {
                Toastr::error(translate('bad_request'));
                return back();
            } else if (isset($placeOrder) && $placeOrder['status'] == 401) {
                Toastr::error(translate('Unauthorized'));
                return back();
            } else if (isset($placeOrder) && $placeOrder['status'] == 403) {
                Toastr::error(translate($placeOrder['message']));
                return back();
            } else {
                Toastr::error(translate('unable_to_create_order_on_shipway'));
                return back();
            }
        }
        Toastr::error(translate('unable to login in Shipway'));
        return back();
    }
    public function delivery_cancel(Request $request)
    {
        $orderId = $request->order_id;
        $deliveryOrderId = $request->delivery_order_id;
        if ($request->delivery_partner == 'shipway') {
            Helpers::ShipWayorderChancel($deliveryOrderId);
            // upd{te detail
            ModelsOrder::where('id', $orderId)->update([
                'delivery_partner' => 'self',
                'delivery_order_id' => null,
                'delivery_channel_id' => null,
                'delivery_shipment_id' => null,
                'order_status' => 'canceled'
            ]);
            // ModelsShipRocket::where('order_id',$orderId)->delete();
            Toastr::success(translate('order canceled from shipway successfully'));
            return back();
        }
        Toastr::error(translate('unable to login in Shipway'));
        return back();
    }
    public function delivery_shipmentcancel(Request $request)
    {
        // dd($request->all());
        $trackingId = $request->input('third_party_delivery_tracking_id');
        $partner = $request->input('delivery_partner');
        $orderId = $request->input('delivery_order_id');
        $warehouseId = $request->input('delivery_channel_id');
        if ($partner == 'shipway') {
            $shipwayResponse = Helpers::ShipwayCancelShipment($trackingId);
            if (isset($shipwayResponse['success']) && $shipwayResponse['success'] == true) {
                Helpers::ShipWayorderChancel($orderId);
                $errorMessage = $shipwayResponse['message'] ?? 'Shipment canceled from Shipway successfully.';
                ModelsOrder::where('id', $orderId)->update([
                    'delivery_order_id'      => null,
                    'delivery_shipment_id'   => null,
                    'order_status'           => 'canceled',
                    'order_weight'           => null,
                    'box_length'             => null,
                    'box_breadth'            => null,
                    'box_height'             => null,
                    'delivery_partner'       => null,
                    'delivery_type'          => null,
                    'third_party_delivery_tracking_id'          => null,
                    'delivery_channel_id'    => $warehouseId,
                    'delivery_service_name'  => null,
                    'delivery_charge'        => null,
                    'message'                => $errorMessage
                ]);
                Order_Pickup::where('order_ids', $orderId)->delete();
                Toastr::success(translate($errorMessage));
                return back();
            }
            if (isset($shipwayResponse['error']) && $shipwayResponse['error'] === true) {
                $errorMessage = $shipwayResponse['message'] ?? 'Unknown error occurred while canceling shipment.';
                Toastr::error(translate('Failed to cancel shipment: ' . $errorMessage));
                return back();
            }
        }
        Toastr::error(translate('Unable to connect to Shipway.'));
        return back();
    }

    public function delivery_getcarries($fromPincode, $toPincode, $paymentType)
    {
        $response = Helpers::ShipwayGetCarrierrates($fromPincode, $toPincode, $paymentType);
        return response()->json($response);
    }
    public function delivery_pickup(Request $request)
    {
        // dd($request->all());
        $pickupOrder = [
            'pickup_date' => $request->pickup_date,
            'pickup_time' => $request->pickup_time,
            'office_close_time' => $request->office_close_time,
            'package_count' => $request->package_count,
            'carrier_id' => $request->carrier_id,
            'warehouse_id' => $request->warehouse_id,
            'return_warehouse_id' => $request->return_warehouse_id,
            'payment_type' => $request->payment_type,
            'order_ids' => $request->order_ids
        ];

        Order_Pickup::create($pickupOrder);
        // dd($orderData);
        $orderId = $request->order_ids;
        $response = Helpers::ShipWayorderPickup($orderId);
        if (isset($response['success']) && $response['success'] === true) {
            Order_Pickup::where('order_ids', $orderId)->update([
                'message' => $response['message']
            ]);
            Toastr::success(translate('order canceled from shipway successfully'));
        }

        return back();
    }
    // public function delivery_partner(Request $request){
    //     $shiprocketToken = "";
    //     $orderId = $request->order_id;
    //     // dd($request->input());
    //     if($request->delivery_partner == 'shiprocket'){
    //         $shiprocketLogin = json_decode(Helpers::shiprocketLogin(),true);
    //         if(isset($shiprocketLogin['token'])){
    //             $shiprocketToken =$shiprocketLogin['token'];
    //         }
    //         if(!empty($shiprocketToken)){
    //             $placeOrder = Helpers::shiprocketPlaceOrder($orderId,$shiprocketToken);
    //             // dd($placeOrder);
    //             if(isset($placeOrder) && $placeOrder['status_code'] == 1){
    //                 // update detail
    //                 ModelsOrder::where('id',$orderId)->update(['delivery_partner'=>$request->delivery_partner,'delivery_order_id'=>$placeOrder['order_id'],'delivery_channel_id'=>$placeOrder['channel_order_id'],'delivery_shipment_id'=>$placeOrder['shipment_id']]);

    //                 ModelsShipRocket::insert(['order_id'=>$orderId,'delivery_order_id'=>$placeOrder['order_id'],'delivery_channel_id'=>$placeOrder['channel_order_id'],'delivery_shipment_id'=>$placeOrder['shipment_id']]);

    //                 // logout ship rocket
    //                 Helpers::shiprocketLogout($shiprocketToken);

    //                 Toastr::success(translate('order assigned to shiprocket successfully'));
    //                 return back();
    //             }
    //             else if(isset($placeOrder) && $placeOrder['status_code'] == 400){
    //                 Toastr::error(translate('incorrect_data'));
    //                 return back();
    //             }
    //             else if(isset($placeOrder) && $placeOrder['status_code'] == 422){
    //                 Toastr::error(translate('data_is_missing'));
    //                 return back();
    //             }
    //             else if(isset($placeOrder) && $placeOrder['status_code'] == 402){
    //                 Toastr::error(translate($placeOrder['message']));
    //                 return back();
    //             }
    //             else{
    //                 Toastr::error(translate('unable_to_create_order_on_shiprocket'));
    //                 return back();
    //             }
    //         }
    //         Toastr::error(translate('unable to login in shiprocket'));
    //         return back();
    //     }
    //     else{
    //         $placeOrder = Helpers::porterPlaceOrder($orderId);
    //         if($placeOrder){
    //             if(isset($result_data['type']) && $result_data['type'] == 'restricted_location'){
    //                 Toastr::success(translate('Porter is unable to deliver this order'));
    //                 return back();
    //             }else if(isset($result_data['type']) && $result_data['type'] == 'insufficient_wallet_balance'){
    //                 Toastr::error(translate('Unable to Place Order, Insufficient wallet balance'));
    //                 return back();
    //             }else{
    //                 // update detail
    //                 ModelsOrder::where('id',$orderId)->update(['delivery_partner'=>$request->delivery_partner,'delivery_order_id'=>$placeOrder['order_id']]);

    //                 PorterOrder::insert(['order_id'=>$orderId,'delivery_order_id'=>$placeOrder['order_id'],'delivery_request_id'=>$placeOrder['request_id'],'estimated_pickup_time'=>$placeOrder['estimated_pickup_time'],'estimated_fare_details'=>$placeOrder['estimated_fare_details'],'status' => 'pending','tracking_url' => $placeOrder['tracking_url']]);

    //                 Toastr::success(translate('order assigned to porter successfully'));
    //                 return back();
    //             }

    //         }
    //         Toastr::error(translate('unable to assign order to porter'));
    //         return back();
    //     }
    // }

    // public function delivery_cancel(Request $request){
    //     $shiprocketToken = "";
    //     $orderId = $request->order_id;
    //     $deliveryOrderId = $request->delivery_order_id;
    //     if($request->delivery_partner == 'shiprocket'){
    //         $shiprocketLogin = json_decode(Helpers::shiprocketLogin(),true);
    //         if(isset($shiprocketLogin['token'])){
    //             $shiprocketToken =$shiprocketLogin['token'];
    //         }
    //         if(!empty($shiprocketToken)){
    //             $cancelOrder = Helpers::shiprocketCancelOrder($deliveryOrderId,$shiprocketToken);
    //             if(isset($cancelOrder) && $cancelOrder['status_code']==500){
    //                 Toastr::error(translate('incorrect_data'));
    //                 return back();
    //             }
    //             else if(isset($cancelOrder) && $cancelOrder['status_code']==422){
    //                 Toastr::error(translate('data_is_missing'));
    //                 return back();
    //             }
    //             else if(isset($cancelOrder) && $cancelOrder['status_code']==402){
    //                 Toastr::error(translate($cancelOrder['message']));
    //                 return back();
    //             }
    //             else {
    //                 // update detail
    //                 ModelsOrder::where('id',$orderId)->update(['delivery_partner'=>'self','delivery_order_id'=>null,'delivery_channel_id'=>null,'delivery_shipment_id'=>null]);
    //                 ModelsShipRocket::where('order_id',$orderId)->delete();

    //                 // logout ship rocket
    //                 Helpers::shiprocketLogout($shiprocketToken);

    //                 Toastr::success(translate('order canceled from shiprocket successfully'));
    //                 return back();
    //             }
    //             Toastr::error(translate('unable to cancel order from shiprocket'));
    //             return back();
    //         }
    //         Toastr::error(translate('unable to login in shiprocket'));
    //         return back();
    //     }
    //     else if($request->delivery_partner == 'porter'){
    //         $cancelOrder = Helpers::porterCancelOrder($deliveryOrderId);
    //         if($cancelOrder){
    //             // update detail
    //             ModelsOrder::where('id',$orderId)->update(['delivery_partner'=>'self','delivery_order_id'=>null]);
    //             PorterOrder::where('order_id',$orderId)->delete();

    //             Toastr::success(translate('order canceled from porter successfully'));
    //             return back();
    //         }
    //         Toastr::error(translate('unable to cancel order from porter'));
    //         return back();
    //     }
    // }

    public function porter_delivery_track($deliveryOrderId)
    {
        $trackOrder = Helpers::porterTrackOrder($deliveryOrderId);
        if ($trackOrder) {
            if (isset($trackOrder['status']) && $trackOrder['status'] == 'cancelled') {
                PorterOrder::where('delivery_order_id', $deliveryOrderId)->update(['status' => 'canceled']);
            } else if (isset($trackOrder['status']) && $trackOrder['status'] == "open") {
                PorterOrder::where('delivery_order_id', $deliveryOrderId)->update(['status' => 'processing']);
            } else if (isset($trackOrder['status']) && $trackOrder['status'] == "accepted") {
                $partner = array();
                if (isset($trackOrder['partner_info']) && $trackOrder['partner_info'] != '') {
                    if (isset($trackOrder['partner_info']['name']) && $trackOrder['partner_info']['name'] != '') {
                        $partner['name'] = $trackOrder['partner_info']['name'];
                    }
                    if (isset($trackOrder['partner_info']['mobile']) && $trackOrder['partner_info']['mobile'] != '') {
                        $partner['mobile'] = $trackOrder['partner_info']['mobile']['mobile_number'];
                    }
                    if (isset($trackOrder['partner_info']['vehicle_number']) && $trackOrder['partner_info']['vehicle_number'] != '') {
                        $partner['vehicle_number'] = $trackOrder['partner_info']['vehicle_number'];
                    }
                }
                PorterOrder::where('delivery_order_id', $deliveryOrderId)->update(['status' => 'processing', 'partner_info' => json_encode($partner)]);
            } else if (isset($trackOrder['status']) && $trackOrder['status'] == "live") {
                $partner = array();
                if (isset($trackOrder['partner_info']) && $trackOrder['partner_info'] != '') {
                    if (isset($trackOrder['partner_info']['name']) && $trackOrder['partner_info']['name'] != '') {
                        $partner['name'] = $trackOrder['partner_info']['name'];
                    }
                    if (isset($trackOrder['partner_info']['mobile']) && $trackOrder['partner_info']['mobile'] != '') {
                        $partner['mobile'] = $trackOrder['partner_info']['mobile']['mobile_number'];
                    }
                    if (isset($trackOrder['partner_info']['vehicle_number']) && $trackOrder['partner_info']['vehicle_number'] != '') {
                        $partner['vehicle_number'] = $trackOrder['partner_info']['vehicle_number'];
                    }
                }
                PorterOrder::where('delivery_order_id', $deliveryOrderId)->update(['status' => 'picked_up', 'partner_info' => json_encode($partner)]);
            } else if (isset($trackOrder['status']) && $trackOrder['status'] == "completed") {
                $partner = array();
                if (isset($trackOrder['partner_info']) && $trackOrder['partner_info'] != '') {
                    if (isset($trackOrder['partner_info']['name']) && $trackOrder['partner_info']['name'] != '') {
                        $partner['name'] = $trackOrder['partner_info']['name'];
                    }
                    if (isset($trackOrder['partner_info']['mobile']) && $trackOrder['partner_info']['mobile'] != '') {
                        $partner['mobile'] = $trackOrder['partner_info']['mobile']['mobile_number'];
                    }
                    if (isset($trackOrder['partner_info']['vehicle_number']) && $trackOrder['partner_info']['vehicle_number'] != '') {
                        $partner['vehicle_number'] = $trackOrder['partner_info']['vehicle_number'];
                    }
                }
                PorterOrder::where('delivery_order_id', $deliveryOrderId)->update(['status' => 'picked_up', 'partner_info' => json_encode($partner)]);
            }
            return response()->json('data updated successfully');
        }
        return response()->json('unable to track order');
    }
}