<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Contracts\Repositories\BusinessSettingRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\SubscriptionRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Customer;
use App\Enums\ExportFileNames\Admin\Customer as CustomerExport;
use App\Events\CustomerStatusUpdateEvent;
use App\Exports\CustomerListExport;
use App\Exports\SubscriberListExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\CustomerRequest;
use App\Http\Requests\Admin\CustomerUpdateSettingsRequest;
use App\Services\CustomerService;
use App\Models\Chadhava_orders;
use App\Models\Service_order;
use App\Models\AppDownload;
use App\Models\User;
use App\Models\UserFeedback;
use App\Models\UserWithdrawBalance;
use App\Models\WalletTransaction;
use App\Traits\PaginatorTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly CustomerRepositoryInterface        $customerRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
        private readonly OrderRepositoryInterface           $orderRepo,
        private readonly SubscriptionRepositoryInterface    $subscriptionRepo,
        private readonly BusinessSettingRepositoryInterface $businessSettingRepo,
    ) {}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getListView($request);
    }

    public function getListView(Request $request): View
    {
        $customers = $this->customerRepo->getListWhere(
            orderBy: ['created_at' => 'desc'],
            searchValue: $request->get('searchValue'),
            filters: ['withCount' => 'orders'],
            relations: ['orders'],
            dataLimit: getWebConfig(name: 'pagination_limit')
        );
        return view(Customer::LIST[VIEW], [
            'customers' => $customers,
        ]);
    }
    public function checked_order()
    {
        $customer = User::where('checked', 0)->update(['checked' => 1]);
        if ($customer) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
        // return redirect()->route('admin.anushthan.order.list', ['status' => 'all']);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $this->customerRepo->update(id: $request['id'], data: ['is_active' => $request->get('status', 0)]);
        $this->customerRepo->deleteAuthAccessTokens(id: $request['id']);
        $customer = $this->customerRepo->getFirstWhere(params: ['id' => $request['id']]);
        event(new CustomerStatusUpdateEvent(key: $request['status'] ? 'customer_unblock_message' : 'customer_block_message', type: 'customer', lang: $customer['app_language'] ?? getDefaultLanguage(), status: $request['status'] ? 'unblocked' : 'blocked', fcmToken: $customer['cm_firebase_token']));
        return response()->json(['message' => translate('update_successfully')]);
    }

    public function getView(Request $request, $id): View|RedirectResponse
    {
        $customer = $this->customerRepo->getFirstWhere(params: ['id' => $id]);
        if (isset($customer)) {
            $orders = $this->orderRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request['searchValue'], filters: ['customer_id' => $id, 'is_guest' => '0']);
            $allOrder = Service_order::with(['order', 'services', 'vippoojas', 'chadhava', 'chadhavaOrders'])->where('customer_id', $id)->get();
            $poojaorders = Service_order::with('leads')->with('services')->where(['customer_id' => $id, 'type' => 'pooja'])->paginate(10);
            $viporders = Service_order::with('leads')->with('vippoojas')->where(['customer_id' => $id, 'type' => 'vip'])->paginate(10);
            $anushthanOrder = Service_order::with('leads')->with('vippoojas')->where(['customer_id' => $id, 'type' => 'anushthan'])->paginate(10);
            $counsellingOrder = Service_order::where(['customer_id' => $id, 'type' => 'counselling'])->with('services')->paginate(10);
            $ChadhavaOrder = Chadhava_orders::where(['customer_id' => $id, 'type' => 'chadhava'])->with('chadhava')->paginate(10);
            // dd($allOrder);
            $offlinepoojaOrder_all = \App\Models\OfflinePoojaOrder::where('customer_id', $id)->with('offlinePooja')->orderBy('id', 'desc')->get()
                ->map(function ($item) {
                    $item->type = 'offlinepooja';
                    return $item;
                });

            $eventOrders_all = \App\Models\EventOrder::where('user_id', $id)->whereIn('transaction_status', ['1', '2', '3'])->with(['eventid'])->orderBy('id', 'desc')->get()->map(function ($order) {
                $order->type = 'event';
                return $order;
            });
            $donateOrders_all = \App\Models\DonateAllTransaction::where('user_id', $id)->where('amount_status', 1)->with(['getTrust', 'adsTrust'])->orderBy('id', 'desc')->get()->map(function ($order) {
                $order->type = 'donate';
                return $order;
            });
            $tourOrders_all = \App\Models\TourOrder::where('user_id', $id)->whereIn('amount_status', [1, 2, 3])->with(['Tour'])->orderBy('id', 'desc')->get()->map(function ($order) {
                $order->type = 'tour';
                return $order;
            });

            $kundalis_order_all = \App\Models\BirthJournalKundali::where('user_id', $id)->where('payment_status', 1)
                ->whereHas('birthJournal', function ($query) {
                    $query->where('name', 'kundali');
                })
                ->with(['birthJournal' => function ($query) {
                    $query->where('name', 'kundali');
                }])->orderBy('id', 'desc')->get()->map(function ($order) {
                    $order->type = 'kundli';
                    return $order;
                });

            $kundali_milan_order_all = \App\Models\BirthJournalKundali::where('user_id', $id)->where('payment_status', 1)
                ->whereHas('birthJournal', function ($query) {
                    $query->where('name', 'kundali_milan');
                })
                ->with(['birthJournal' => function ($query) {
                    $query->where('name', 'kundali_milan');
                }])->orderBy('id', 'desc')->get()->map(function ($order) {
                    $order->type = 'kundli milan';
                    return $order;
                });
            $ecomm_order_all = \App\Models\Order::withSum('orderDetails', 'qty')->where(['customer_id' => $id, 'is_guest' => '0'])->orderBy('id', 'desc')->get()->map(function ($order) {
                $order->type = 'shop';
                return $order;
            });
            $allOrders_alls = collect()
                ->merge($allOrder)
                ->merge($offlinepoojaOrder_all)
                ->merge($eventOrders_all)
                ->merge($donateOrders_all)
                ->merge($tourOrders_all)
                ->merge($kundalis_order_all)
                ->merge($kundali_milan_order_all)
                ->merge($ecomm_order_all);
            $sortedOrders = $allOrders_alls->sortByDesc('created_at')->values();
            $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('all_order');
            $perPage = getWebConfig(name: 'pagination_limit');
            $offset = ($page - 1) * $perPage;
            $paginatedOrders = new \Illuminate\Pagination\LengthAwarePaginator(
                $sortedOrders->slice($offset, $perPage)->values(),
                $sortedOrders->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query(), 'pageName' => 'all_order']
            );

            $ToutOrders = \App\Models\TourOrder::when(isset($request['search']) && ($request['type'] == 'tour'), function ($query) use ($request) {
                $query->where('order_id', 'like', "%" . $request['search'] . "%");
            })->where('amount_status', 1)->where('user_id', $id)->orderBy('id', 'DESC')->paginate(getWebConfig(name: 'pagination_limit'), ['*'], 'tour-page', request('tour-page', 1));
            $eventOrders = \App\Models\EventOrder::when(isset($request['search']) && ($request['type'] == 'event'), function ($query) use ($request) {
                $query->where('order_no', 'like', "%" . $request['search'] . "%");
            })->where('transaction_status', 1)->where('user_id', $id)->orderBy('id', 'DESC')->paginate(getWebConfig(name: 'pagination_limit'), ['*'], 'event-page', request('event-page', 1));
            $donateOrders = \App\Models\DonateAllTransaction::when(isset($request['search']) && ($request['type'] == 'donate'), function ($query) use ($request) {
                $query->where('trans_id', 'like', "%" . $request['search'] . "%");
                $query->orWhere('type', 'like', "%" . $request['search'] . "%");
            })->whereIn('type', ['donate_trust', 'donate_ads'])->where('amount_status', 1)->where('user_id', $id)->orderBy('id', 'DESC')->paginate(getWebConfig(name: 'pagination_limit'), ['*'], 'donate-page', request('donate-page', 1));
            $kundaliOrders = \App\Models\BirthJournalKundali::when(
                isset($request['search']) && $request['type'] == 'kundali',
                function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('order_id', 'like', "%" . $request['search'] . "%")
                            ->orWhereHas('birthJournal', function ($q2) use ($request) {
                                $q2->where('name', 'like', "%" . $request['search'] . "%");
                            });
                    });
                }
            )->where('payment_status', 1)->where('user_id', $id)->with(['birthJournal'])->orderBy('id', 'DESC')->paginate(getWebConfig(name: 'pagination_limit'), ['*'], 'kundali-page', request('kundali-page', 1));
            return view(Customer::VIEW[VIEW], ['customer' => $customer, 'orders' => $orders, 'poojaorders' => $poojaorders, 'viporders' => $viporders, 'allOrder' => $allOrder, 'anushthanOrder' => $anushthanOrder, 'counsellingOrder' => $counsellingOrder, 'ChadhavaOrder' => $ChadhavaOrder, 'paginatedOrders' => $paginatedOrders, "ToutOrders" => $ToutOrders, "eventOrders" => $eventOrders, "donateOrders" => $donateOrders, 'kundaliOrders' => $kundaliOrders]);
        }
        Toastr::error(translate('customer_Not_Found'));
        return back();
    }

    /**
     * @param $id
     * @param CustomerService $customerService
     * @return RedirectResponse
     */
    public function delete($id, CustomerService $customerService): RedirectResponse
    {
        $customer = $this->customerRepo->getFirstWhere(params: ['id' => $id]);
        $customerService->deleteImage(data: $customer);
        $this->customerRepo->delete(params: ['id' => $id]);
        Toastr::success(translate('customer_deleted_successfully'));
        Helpers::editDeleteLogs('Customer', 'Customer', 'Delete');
        return back();
    }

    public function getSubscriberListView(Request $request): View|Application
    {
        $subscription_list = $this->subscriptionRepo->getListWhere(searchValue: $request['searchValue']);
        return view(Customer::SUBSCRIBER_LIST[VIEW], compact('subscription_list'));
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $customers = $this->customerRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            filters: ['withCount' => 'orders'],
            relations: ['orders'],
            dataLimit: 'all'
        );
        return Excel::download(
            new CustomerListExport([
                'customers' => $customers,
                'searchValue' => $request->get('searchValue'),
                'active' => $this->customerRepo->getListWhere(filters: ['is_active' => 1])->count(),
                'inactive' => $this->customerRepo->getListWhere(filters: ['is_active' => 0])->count(),
            ]),
            CustomerExport::EXPORT_XLSX
        );
    }

    public function exportSubscribersList(Request $request): BinaryFileResponse
    {
        $subscription = $this->subscriptionRepo->getListWhere(searchValue: $request['searchValue'], dataLimit: 'all');
        return Excel::download(
            new SubscriberListExport([
                'subscription' => $subscription,
                'search' => $request['searchValue'],
            ]),
            CustomerExport::SUBSCRIBER_LIST_XLSX
        );
    }

    public function getCustomerSettingsView(): View
    {
        $wallet = $this->businessSettingRepo->getListWhere(filters: [['type', 'like', 'wallet_%']]);
        $loyaltyPoint = $this->businessSettingRepo->getListWhere(filters: [['type', 'like', 'loyalty_point_%']]);
        $refEarning = $this->businessSettingRepo->getListWhere(filters: [['type', 'like', 'ref_earning_%']]);

        $data = [];
        foreach ($wallet as $setting) {
            $data[$setting->type] = $setting->value;
        }
        foreach ($loyaltyPoint as $setting) {
            $data[$setting->type] = $setting->value;
        }
        foreach ($refEarning as $setting) {
            $data[$setting->type] = $setting->value;
        }
        return view(Customer::SETTINGS[VIEW], compact('data'));
    }

    public function update(CustomerUpdateSettingsRequest $request): View|RedirectResponse
    {
        if (env('APP_MODE') === 'demo') {
            Toastr::info(translate('update_option_is_disable_for_demo'));
            return back();
        }
        $this->businessSettingRepo->updateOrInsert(type: 'wallet_status', value: $request->get('customer_wallet', 0));
        $this->businessSettingRepo->updateOrInsert(type: 'loyalty_point_status', value: $request->get('customer_loyalty_point', 0));
        $this->businessSettingRepo->updateOrInsert(type: 'wallet_add_refund', value: $request->get('refund_to_wallet', getWebConfig('wallet_add_refund')));
        $this->businessSettingRepo->updateOrInsert(type: 'loyalty_point_exchange_rate', value: $request->get('loyalty_point_exchange_rate', getWebConfig('loyalty_point_exchange_rate')));
        $this->businessSettingRepo->updateOrInsert(type: 'loyalty_point_item_purchase_point', value: $request->get('item_purchase_point', getWebConfig('loyalty_point_item_purchase_point')));
        $this->businessSettingRepo->updateOrInsert(type: 'loyalty_point_minimum_point', value: $request->get('minimun_transfer_point',  getWebConfig('loyalty_point_minimum_point')));
        $this->businessSettingRepo->updateOrInsert(type: 'ref_earning_status', value: $request->get('ref_earning_status', 0));
        $this->businessSettingRepo->updateOrInsert(type: 'ref_earning_exchange_rate', value: currencyConverter(amount: $request->get('ref_earning_exchange_rate', getWebConfig('ref_earning_exchange_rate'))));
        $this->businessSettingRepo->updateOrInsert(type: 'add_funds_to_wallet', value: $request->get('add_funds_to_wallet', getWebConfig('add_funds_to_wallet')));

        if ($request->has('minimum_add_fund_amount') && $request->has('maximum_add_fund_amount')) {
            if ($request['maximum_add_fund_amount'] > $request['minimum_add_fund_amount']) {
                $this->businessSettingRepo->updateOrInsert(type: 'minimum_add_fund_amount', value: currencyConverter(amount: $request->get('minimum_add_fund_amount', 1)));
                $this->businessSettingRepo->updateOrInsert(type: 'maximum_add_fund_amount', value: currencyConverter(amount: $request->get('maximum_add_fund_amount', 0)));
            } else {
                Toastr::error(translate('minimum_amount_cannot_be_greater_than_maximum_amount'));
                return back();
            }
        }

        Toastr::success(translate('customer_settings_updated_successfully'));
        return back();
    }

    public function getCustomerList(Request $request): JsonResponse
    {
        $customers = $this->customerRepo->getCustomerNameList(
            request: $request,
            dataLimit: getWebConfig(name: 'pagination_limit')
        );
        return response()->json($customers);
    }
    public function add(CustomerRequest $request, CustomerService $customerService): RedirectResponse
    {
        $this->customerRepo->add($customerService->getCustomerData(request: $request));
        Toastr::success(('customer_added_successfully'));
        Helpers::editDeleteLogs('Customer', 'Customer', 'Insert');
        return redirect()->back();
    }
    public function app_download(Request $request)
    {
        $appDownloads = AppDownload::query();
        if ($request->has('state') && $request->state !== 'all') {
            $appDownloads->where('state', $request->state);
        }
        $appDownloads = $appDownloads->orderBy('created_at', 'desc')->paginate(10);
        $states = AppDownload::select('state')->distinct()->pluck('state');
        return view('admin-views.customer.app-download', compact('appDownloads', 'states'));
    }

    public function send_app_link(Request $request)
    {
        $result = [];
        foreach ($request->customer as $id) {
            $data = [
                'customer_id' => ($id ?? "")
            ];
            $result[] = Helpers::whatsappMessage('whatsapp', 'App Download', $data);
        }
        return response()->json(['result' => $result], 200);
    }

    public function withdraw_list($type)
    {
        if ($type == 'pending') {
            $type = 0;
        } elseif ($type == 'approve') {
            $type = 1;
        } elseif ($type == 'complete') {
            $type = 2;
        } else {
            Toastr::success(('parameter_is_wrong'));
            return redirect()->back();
        }
        $list = UserWithdrawBalance::where('status', $type)->orderBy('created_at', 'desc')->paginate(10);
        if ($list) {
            return view('admin-views.customer.withdraw.list', compact('list'));
        }
        Toastr::success(('an error occurred'));
        return redirect()->back();
    }

    public function withdraw_approve(Request $request)
    {
        $update = UserWithdrawBalance::where('id', $request->id)->update(['status' => 1]);
        if ($update) {
            Toastr::success(('withdraw amount approved'));
            return redirect()->back();
        }
        Toastr::success(('an error occurred'));
        return redirect()->back();
    }

    public function withdraw_complete(Request $request)
    {
        $updateWithdrawBal = UserWithdrawBalance::where('id', $request->id)->update(['status' => 2]);
        if ($updateWithdrawBal) {
            $getWithdrawData = UserWithdrawBalance::where('id', $request->id)->first();
            $remainingWalletBal = $getWithdrawData['current_balance'] - $getWithdrawData['request_balance'];
            $updateUserWallet = User::where('phone', $request->phone)->update(['wallet_balance' => $remainingWalletBal]);
            if ($updateUserWallet) {
                $getUserId = User::where('phone', $request->phone)->get()->value('id');
                $store = new WalletTransaction();
                $store->user_id = $getUserId;
                $store->transaction_id = \Str::uuid();
                $store->debit = $getWithdrawData->request_balance;
                $store->balance = $remainingWalletBal;
                $store->transaction_type = 'wallet_withdrwal';
                $store->reference = 'wallet withdrwal';
                $store->save();
                Toastr::success(('withdraw amount completed'));
                return redirect()->back();
            }
        }
        Toastr::success(('an error occurred'));
        return redirect()->back();
    }

    public function feedback_list()
    {
        $feedbackList = UserFeedback::with('user')->paginate(10);
        if ($feedbackList) {
            return view('admin-views.customer.feedback.list', compact('feedbackList'));
        }
        Toastr::error(('an error occurred'));
        return redirect()->back();
    }

    public function feedback_status(Request $request)
    {
        $feedbackApprove = UserFeedback::where('id', $request->id)->update(['status' => 1]);
        if ($feedbackApprove) {
            Toastr::success(('Feedback_approved'));
            return redirect()->back();
        }
        Toastr::error(('an error occurred'));
        return redirect()->back();
    }
}
