<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\AdminWalletRepositoryInterface;
use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\DeliveryManRepositoryInterface;
use App\Contracts\Repositories\OrderDetailRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\OrderTransactionRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Repositories\VendorWalletRepositoryInterface;
use App\Enums\ViewPaths\Admin\Dashboard;
use App\Http\Controllers\BaseController;
use App\Models\Chadhava;
use App\Models\Chadhava_orders;
use App\Models\DonateAds;
use App\Models\DonateAllTransaction;
use App\Models\DonateTrust;
use App\Models\EventOrder;
use App\Models\EventOrganizer;
use App\Models\Events;
use App\Models\OfflinePoojaOrder;
use App\Models\PoojaOffline;
use App\Models\Service;
use App\Models\Service_order;
use App\Models\ServiceTransaction;
use App\Models\Vippooja;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\File;
use DB;

class DashboardController extends BaseController
{
    public function __construct(
        private readonly AdminWalletRepositoryInterface      $adminWalletRepo,
        private readonly CustomerRepositoryInterface         $customerRepo,
        private readonly OrderTransactionRepositoryInterface $orderTransactionRepo,
        private readonly ProductRepositoryInterface          $productRepo,
        private readonly DeliveryManRepositoryInterface      $deliveryManRepo,
        private readonly OrderRepositoryInterface            $orderRepo,
        private readonly OrderDetailRepositoryInterface      $orderDetailRepo,
        private readonly BrandRepositoryInterface            $brandRepo,
        private readonly VendorRepositoryInterface           $vendorRepo,
        private readonly VendorWalletRepositoryInterface     $vendorWalletRepo,
    ) {}

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|RedirectResponse|null
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        return $this->dashboard();
    }

    public function dashboard(): View
    {
        $mostRatedProducts = $this->productRepo->getTopRatedList()->take(DASHBOARD_DATA_LIMIT);
        $topSellProduct = $this->productRepo->getTopSellList(relations: ['orderDetails'])->take(DASHBOARD_TOP_SELL_DATA_LIMIT);
        $topCustomer = $this->orderRepo->getTopCustomerList(relations: ['customer'], dataLimit: 'all')->take(DASHBOARD_DATA_LIMIT);
        $topRatedDeliveryMan = $this->deliveryManRepo->getTopRatedList(filters: ['seller_id' => 0], relations: ['deliveredOrders'], dataLimit: 'all')->take(DASHBOARD_DATA_LIMIT);
        $topVendorByEarning = $this->vendorWalletRepo->getListWhere(orderBy: ['total_earning' => 'desc'], relations: ['seller.shop'])->take(DASHBOARD_DATA_LIMIT);
        $topVendorByOrderReceived = $this->orderRepo->getTopVendorListByOrderReceived(relations: ['seller.shop'], dataLimit: 'all')->take(DASHBOARD_DATA_LIMIT);

        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');
        $inhouseEarningStatisticsData = $this->orderTransactionRepo->getEarningStatisticsData(
            sellerIs: 'admin',
            dataRange: ['from' => $from, 'to' => $to],
            groupBy: ['year', 'month']
        );
        $sellerEarningStatisticsData = $this->orderTransactionRepo->getEarningStatisticsData(
            sellerIs: 'seller',
            dataRange: ['from' => $from, 'to' => $to],
            groupBy: ['year', 'month']
        );

        $commissionEarningStatisticsData = $this->orderTransactionRepo->getCommissionEarningStatisticsData(
            dataRange: ['from' => Carbon::now()->startOfYear()->format('Y-m-d'), 'to' => Carbon::now()->endOfYear()->format('Y-m-d')],
            groupBy: ['year', 'month'],
        );

        $data = self::getOrderStatusData();
        $admin_wallet = $this->adminWalletRepo->getFirstWhere(params: ['admin_id' => 1]);

        $from = now()->startOfYear()->format('Y-m-d');
        $to = now()->endOfYear()->format('Y-m-d');
        $range = range(1, 12);
        $label = ["Jan", "Feb", "Mar", "April", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $inHouseOrderEarningArray = $this->getOrderStatisticsData(from: $from, to: $to, range: $range, type: 'month', userType: 'admin');
        $vendorOrderEarningArray = $this->getOrderStatisticsData(from: $from, to: $to, range: $range, type: 'month', userType: 'seller');
        $dateType = 'yearEarn';
        $data += [
            'order' => $this->orderRepo->getListWhere(dataLimit: 'all')->count(),
            'brand' => $this->brandRepo->getListWhere(dataLimit: 'all')->count(),
            'topSellProduct' => $topSellProduct,
            'mostRatedProducts' => $mostRatedProducts,
            'topVendorByEarning' => $topVendorByEarning,
            'top_customer' => $topCustomer,
            'top_store_by_order_received' => $topVendorByOrderReceived,
            'topRatedDeliveryMan' => $topRatedDeliveryMan,
            'inhouse_earning' => $admin_wallet['inhouse_earning'] ?? 0,
            'commission_earned' => $admin_wallet['commission_earned'] ?? 0,
            'delivery_charge_earned' => $admin_wallet['delivery_charge_earned'] ?? 0,
            'pending_amount' => $admin_wallet['pending_amount'] ?? 0,
            'total_tax_collected' => $admin_wallet['total_tax_collected'] ?? 0,
            'getTotalCustomerCount' => $this->customerRepo->getList()->count(),
            'getTotalVendorCount' => $this->vendorRepo->getListWhere(dataLimit: 'all')->count(),
            'getTotalDeliveryManCount' => $this->deliveryManRepo->getListWhere(filters: ['seller_id' => 0], dataLimit: 'all')->count(),
        ];

        //pooja dashboard
        $counts = Service_order::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->whereIn('type', ['pooja', 'counselling', 'vip', 'anushthan'])
            ->get();
        $poojaTransAmount = 0;
        $poojaTransaction = ServiceTransaction::with('chadhavaOrder')->with('serviceOrder')->with('astrologer')->whereNot('type', 'offlinepooja')->get();
        foreach ($poojaTransaction as $key => $poojaTrans) {
            $serviceId   = null;
            $serviceJson = [];
            if (in_array($poojaTrans->type, ['pooja','counselling','anushthan','vip'])) {
                if (!empty($poojaTrans->serviceOrder)) {
                    $serviceId = $poojaTrans->serviceOrder->service_id;
                    if ($poojaTrans->serviceOrder->type === 'pooja' && !empty($poojaTrans->astrologer?->is_pandit_pooja)) {
                        $serviceJson = json_decode($poojaTrans->astrologer->is_pandit_pooja, true);
                    } elseif ($poojaTrans->serviceOrder->type === 'vip' && !empty($poojaTrans->astrologer?->is_pandit_vippooja)) {
                        $serviceJson = json_decode($poojaTrans->astrologer->is_pandit_vippooja, true);
                    } elseif ($poojaTrans->serviceOrder->type === 'anushthan' && !empty($poojaTrans->astrologer?->is_pooja_anushthan)) {
                        $serviceJson = json_decode($poojaTrans->astrologer->is_pooja_anushthan, true);
                    }
                }
            } elseif ($poojaTrans->type === 'chadhava') {
                if (!empty($poojaTrans->chadhavaOrder) && !empty($poojaTrans->astrologer?->is_pandit_chadhava)) {
                    $serviceId   = $poojaTrans->chadhavaOrder->service_id;
                    $serviceJson = json_decode($poojaTrans->astrologer->is_pandit_chadhava, true);
                }
            }
            if ($serviceId && is_array($serviceJson) && isset($serviceJson[$serviceId])) {
                $poojaTransAmount += (float) $serviceJson[$serviceId];
            }         
            // dd($serviceJson,$serviceId,$poojaTransAmount);
        }
        $serviceCustomerIds = Service_order::pluck('customer_id');
        $chadhavaCustomerIds = Chadhava_orders::pluck('customer_id');
        $poojaData = [
            'services' => Service::where('status', 1)->count() + Vippooja::where('status', 1)->count() + Chadhava::where('status', 1)->count(),
            'serviceOrder' => Service_order::get(),
            'chadhavaOrder' => Chadhava_orders::get(),
            'poojaCustomers' => $serviceCustomerIds->merge($chadhavaCustomerIds)->unique()->values(),
            'poojaWallet' => Service_order::where('order_status', '!=', 2)->sum('pay_amount') + Chadhava_orders::where('order_status', '!=', 2)->sum('pay_amount'),
            'poojaCommission' => ServiceTransaction::selectRaw('SUM((amount * commission) / 100) as total_commission')->whereNot('type', 'offlinepooja')->value('total_commission'),
            'poojaTax' => ServiceTransaction::selectRaw('SUM((amount * tax) / 100) as total_tax')->whereNot('type', 'offlinepooja')->value('total_tax'),
            'givenAmount' => $poojaTransAmount,
        ];
        // $poojaData['poojaPandit'] = $poojaData['poojaWallet'] - $poojaData['poojaCommission'] - $poojaData['poojaTax'];

        //pandit dashboard
        $panditTransAmount = 0;
        $panditTransaction = ServiceTransaction::with('offlinepoojaOrder')->with('astrologer')->where('type', 'offlinepooja')->get();
        foreach ($panditTransaction as $key => $pTrans) {
            $serviceId = $pTrans['offlinepoojaOrder']['service_id'];
            $serviceJson = json_decode($pTrans['astrologer']['is_pandit_offlinepooja'], true);
            if (isset($serviceJson[$serviceId])) {
                $panditTransAmount += $serviceJson[$serviceId];
            }
        }
        $panditData = [
            'services' => PoojaOffline::where('status', 1)->count(),
            'serviceOrder' => OfflinePoojaOrder::get(),
            'poojaCustomers' => OfflinePoojaOrder::pluck('customer_id')->unique()->values(),
            'poojaWallet' => OfflinePoojaOrder::where('status', '!=', 2)->sum('pay_amount'),
            'poojaCommission' => ServiceTransaction::selectRaw('SUM((amount * commission) / 100) as total_commission')->where('type', 'offlinepooja')->value('total_commission'),
            'poojaTax' => ServiceTransaction::selectRaw('SUM((amount * tax) / 100) as total_tax')->where('type', 'offlinepooja')->value('total_tax'),
            'givenAmount' => $panditTransAmount,
        ];
        // $panditData['poojaPandit'] = $panditData['poojaWallet'] - $panditData['poojaCommission'] - $panditData['poojaTax'];
        // dd($panditData);

        //event dashaboard
        $amounts = DB::table('event_orders')
            ->join('events', 'event_orders.event_id', '=', 'events.id')
            ->select('events.organizer_by', DB::raw('SUM(event_orders.final_amount) as total_amount'))
            ->groupBy('events.organizer_by')
            ->get();
        $eventData = [
            'organizers' => EventOrganizer::where('is_approve', 1)->where('status', 1)->count(),
            'organizerEvents' => Events::where('organizer_by', 'outside')->where('is_approve', 1)->where('status', 1)->count(),
            'outEvents' => Events::where('organizer_by', 'inhouse')->where('is_approve', 1)->where('status', 1)->count(),
            'orders' => EventOrder::where('transaction_status', 1)->where('status', 1)->count(),
            'organizerAmount' => $amounts->where('organizer_by', 'outside')->sum('total_amount'),
            'ourAmount' => $amounts->where('organizer_by', 'inhouse')->sum('total_amount'),
            'commission' => EventOrder::sum('admin_commission'),
            'runningEvents' => Events::whereRaw("CASE WHEN start_to_end_date LIKE '% - %' THEN ? BETWEEN SUBSTRING_INDEX(start_to_end_date, ' - ', 1) AND SUBSTRING_INDEX(start_to_end_date, ' - ', -1) ELSE start_to_end_date = ? END", [Carbon::today()->format('Y-m-d'), Carbon::today()->format('Y-m-d')])->where('status', 1)->where('is_approve', 1)->count(),
            'completedEvents' => Events::whereRaw("DATE(CASE WHEN start_to_end_date LIKE '% - %' THEN SUBSTRING_INDEX(start_to_end_date, ' - ', 2) ELSE start_to_end_date END) < ?", [Carbon::today()->format('Y-m-d')])->where('status', 1)->where('is_approve', 1)->count(),
            'canceledEvents' => Events::where('is_approve', 2)->count(),
            'upcommingEvents' => Events::whereRaw("DATE(CASE WHEN start_to_end_date LIKE '% - %' THEN SUBSTRING_INDEX(start_to_end_date, ' - ', 1) ELSE start_to_end_date END) >= ?", [Carbon::today()->format('Y-m-d')])->where('status', 1)->where('is_approve', 1)->count(),
        ];
        // donate dashboard
        $amounts = DB::table('donate_all_transaction')->join('donate_ads', 'donate_all_transaction.ads_id', '=', 'donate_ads.id')->select('donate_ads.type', DB::raw('SUM(donate_all_transaction.final_amount) as total_amount'))->groupBy('donate_ads.type')->get();
        $doneteData = [
            'totalTrust' => DonateTrust::where('is_approve', 1)->where('status', 1)->count(),
            'totalTurstAds' => DonateAds::where('type', 'outsite')->where('is_approve', 1)->where('status', 1)->count(),
            'totalOurAds' => DonateAds::where('type', 'inhouse')->where('is_approve', 1)->where('status', 1)->count(),
            'totalDonets' => DonateAllTransaction::whereIn('type', ['donate_ads', 'donate_trust'])->count(),
            'inhouseAmount' => $amounts->where('type', 'inhouse')->sum('total_amount'),
            'outsideAmount' => $amounts->where('type', 'outsite')->sum('total_amount'),
            'allAds' =>  DonateAds::count(),
            'pendingAds' =>  DonateAds::where('is_approve', 1)->where('status', 0)->count(),
            'runningAds' =>  DonateAds::where('is_approve', 1)->where('status', 1)->count(),

        ];

        return view(Dashboard::VIEW[VIEW], compact('data', 'inhouseEarningStatisticsData', 'sellerEarningStatisticsData', 'commissionEarningStatisticsData', 'inHouseOrderEarningArray', 'vendorOrderEarningArray', 'label', 'dateType', 'poojaData', 'eventData', 'doneteData', 'panditData'));
    }

    public function getOrderStatus(Request $request): JsonResponse
    {
        session()->put('statistics_type', $request['statistics_type']);
        $data = self::getOrderStatusData();
        return response()->json(['view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()], 200);
    }

    public function getOrderStatusData(): array
    {
        $orderQuery = $this->orderRepo->getListWhere(dataLimit: 'all');
        $storeQuery = $this->vendorRepo->getListWhere(dataLimit: 'all');
        $productQuery = $this->productRepo->getListWhere(dataLimit: 'all');
        $customerQuery = $this->customerRepo->getListWhere(dataLimit: 'all');
        $totalSaleQuery = $this->orderDetailRepo->getListWhere(filters: ['delivery_status' => 'delivered']);
        $failedQuery = $this->orderRepo->getListWhere(filters: ['order_status' => 'failed'], dataLimit: 'all');
        $pendingQuery = $this->orderRepo->getListWhere(filters: ['order_status' => 'pending'], dataLimit: 'all');
        $returnedQuery = $this->orderRepo->getListWhere(filters: ['order_status' => 'returned'], dataLimit: 'all');
        $canceledQuery = $this->orderRepo->getListWhere(filters: ['order_status' => 'canceled'], dataLimit: 'all');
        $confirmedQuery = $this->orderRepo->getListWhere(filters: ['order_status' => 'confirmed'], dataLimit: 'all');
        $deliveredQuery = $this->orderRepo->getListWhere(filters: ['order_status' => 'delivered'], dataLimit: 'all');
        $processingQuery = $this->orderRepo->getListWhere(filters: ['order_status' => 'processing'], dataLimit: 'all');
        $outForDeliveryQuery = $this->orderRepo->getListWhere(filters: ['order_status' => 'out_for_delivery'], dataLimit: 'all');

        return [
            'order' => self::getCommonQueryOrderStatus($orderQuery),
            'store' => self::getCommonQueryOrderStatus($storeQuery),
            'failed' => self::getCommonQueryOrderStatus($failedQuery),
            'pending' => self::getCommonQueryOrderStatus($pendingQuery),
            'product' => self::getCommonQueryOrderStatus($productQuery),
            'customer' => self::getCommonQueryOrderStatus($customerQuery),
            'returned' => self::getCommonQueryOrderStatus($returnedQuery),
            'canceled' => self::getCommonQueryOrderStatus($canceledQuery),
            'confirmed' => self::getCommonQueryOrderStatus($confirmedQuery),
            'delivered' => self::getCommonQueryOrderStatus($deliveredQuery),
            'processing' => self::getCommonQueryOrderStatus($processingQuery),
            'total_sale' => self::getCommonQueryOrderStatus($totalSaleQuery),
            'out_for_delivery' => self::getCommonQueryOrderStatus($outForDeliveryQuery),
        ];
    }

    public function getCommonQueryOrderStatus($query)
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today' ? 1 : 0;
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 1 : 0;

        return $query->when($today, function ($query) {
            return $query->where('created_at', '>=', now()->startOfDay())
                ->where('created_at', '<', now()->endOfDay());
        })->when($this_month, function ($query) {
            return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        })->count();
    }


    public function getEarningStatistics(Request $request): JsonResponse
    {
        $dateType = $request['type'];
        $inhouseLabel = [];
        $inhouseEarningStatisticsData = [];
        if ($dateType == 'yearEarn') {
            $inhouseLabel = ["Jan", "Feb", "Mar", "April", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            $inhouseEarningStatisticsData = $this->orderTransactionRepo->getEarningStatisticsData(
                sellerIs: 'admin',
                dataRange: ['from' => Carbon::now()->startOfYear()->format('Y-m-d'), 'to' => Carbon::now()->endOfYear()->format('Y-m-d')],
                groupBy: ['year', 'month'],
            );
        } elseif ($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $inhouseLabel = range(1, date('d', strtotime($to)));
            $inhouseEarningStatisticsData = $this->orderTransactionRepo->getEarningStatisticsData(
                sellerIs: 'admin',
                dataRange: ['from' => $from, 'to' => $to],
                groupBy: ['day'],
                dateEnd: date('d', strtotime($to)),
            );
        } elseif ($dateType == 'WeekEarn') {
            $from = Carbon::now()->startOfWeek()->format('Y-m-d');
            $to = Carbon::now()->endOfWeek()->format('Y-m-d');
            $inhouseLabel = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $inhouseEarningStatisticsData = $this->orderTransactionRepo->getEarningStatisticsData(
                sellerIs: 'admin',
                dataRange: ['from' => $from, 'to' => $to],
                groupBy: ['day'],
                dateStart: date('d', strtotime($from)),
                dateEnd: date('d', strtotime($to)),
            );
        }

        $sellerLabel = [];
        $sellerEarningStatisticsData = [];
        if ($dateType == 'yearEarn') {
            $sellerLabel = ["Jan", "Feb", "Mar", "April", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            $sellerEarningStatisticsData = $this->orderTransactionRepo->getEarningStatisticsData(
                sellerIs: 'seller',
                dataRange: ['from' => Carbon::now()->startOfYear()->format('Y-m-d'), 'to' => Carbon::now()->endOfYear()->format('Y-m-d')],
                groupBy: ['year', 'month'],
            );
        } elseif ($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d', strtotime($to));
            $sellerLabel = range(1, $number);
            $sellerEarningStatisticsData = $this->orderTransactionRepo->getEarningStatisticsData(
                sellerIs: 'seller',
                dataRange: ['from' => $from, 'to' => $to],
                groupBy: ['day'],
                dateEnd: $number,
            );
        } elseif ($dateType == 'WeekEarn') {
            $from = Carbon::now()->startOfWeek()->format('Y-m-d');
            $to = Carbon::now()->endOfWeek()->format('Y-m-d');
            $sellerLabel = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $sellerEarningStatisticsData = $this->orderTransactionRepo->getEarningStatisticsData(
                sellerIs: 'seller',
                dataRange: ['from' => $from, 'to' => $to],
                groupBy: ['day'],
                dateStart: date('d', strtotime($from)),
                dateEnd: date('d', strtotime($to)),
            );
        }

        $commissionLabel = [];
        $commissionEarningStatisticsData = [];
        if ($dateType == 'yearEarn') {
            $commissionLabel = array("Jan", "Feb", "Mar", "April", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
            $commissionEarningStatisticsData = $this->orderTransactionRepo->getCommissionEarningStatisticsData(
                dataRange: ['from' => Carbon::now()->startOfYear()->format('Y-m-d'), 'to' => Carbon::now()->endOfYear()->format('Y-m-d')],
                groupBy: ['year', 'month'],
            );
        } elseif ($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d', strtotime($to));
            $commissionLabel = range(1, $number);
            $commissionEarningStatisticsData = $this->orderTransactionRepo->getCommissionEarningStatisticsData(
                dataRange: ['from' => $from, 'to' => $to],
                groupBy: ['day'],
                dateEnd: $number,
            );
        } elseif ($dateType == 'WeekEarn') {

            $from = Carbon::now()->startOfWeek()->format('Y-m-d');
            $to = Carbon::now()->endOfWeek()->format('Y-m-d');
            $commissionLabel = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $commissionEarningStatisticsData = $this->orderTransactionRepo->getCommissionEarningStatisticsData(
                dataRange: ['from' => $from, 'to' => $to],
                groupBy: ['day'],
                dateStart: date('d', strtotime($from)),
                dateEnd: date('d', strtotime($to)),
            );
        }

        $data = [
            'inhouse_label' => $inhouseLabel,
            'inhouse_earn' => array_values($inhouseEarningStatisticsData),
            'seller_label' => $sellerLabel,
            'seller_earn' => array_values($sellerEarningStatisticsData),
            'commission_label' => $commissionLabel,
            'commission_earn' => array_values($commissionEarningStatisticsData)
        ];
        return response()->json($data);
    }
    public function getOrderStatistics(Request $request): JsonResponse
    {
        $dateType = $request['type'];
        $from = null;
        $to = null;
        $type = null;
        $range = null;
        if ($dateType == 'yearEarn') {
            $from = Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');
            $range = range(1, 12);
            $type = 'month';
            $keyRange = ["Jan", "Feb", "Mar", "April", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        } elseif ($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $endRange = date('d', strtotime($to));
            $range = range(1, $endRange);
            $type = 'day';
            $keyRange = $range;
        } elseif ($dateType == 'WeekEarn') {

            $from = Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d');
            $to = Carbon::now()->endOfWeek(Carbon::SATURDAY)->format('Y-m-d');
            $range = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $type = 'day_of_week';
            $keyRange = $range;
        }

        $inHouseOrderEarningArray = $this->getOrderStatisticsData(from: $from, to: $to, range: $range, type: $type, userType: 'admin');
        $vendorOrderEarningArray = $this->getOrderStatisticsData(from: $from, to: $to, range: $range, type: $type, userType: 'seller');
        $label = $keyRange ?? [];
        $inHouseOrderEarningArray = array_values($inHouseOrderEarningArray);
        $vendorOrderEarningArray = array_values($vendorOrderEarningArray);
        return response()->json([
            'view' => view(Dashboard::ORDER_STATISTICS[VIEW], compact('inHouseOrderEarningArray', 'vendorOrderEarningArray', 'label', 'dateType'))->render(),
        ]);
    }
    protected function getOrderStatisticsData($from, $to, $range, $type, $userType): array
    {
        $orderEarnings = $this->orderRepo->getListWhereBetween(
            filters: [
                'seller_is' => $userType,
                'payment_status' => 'paid'
            ],
            selectColumn: 'order_amount',
            whereBetween: 'created_at',
            whereBetweenFilters: [$from, $to],
        );
        $orderEarningArray = [];
        foreach ($range as $value) {
            $matchingEarnings = $orderEarnings->where($type, $value);
            if ($matchingEarnings->count() > 0) {
                $orderEarningArray[$value] = number_format($matchingEarnings->sum('sums'), 2, '.', '');
            } else {
                $orderEarningArray[$value] = 0;
            }
        }
        return $orderEarningArray;
    }
    public function generateQrCode(Request $request)
    {
        $url = $request->url;
        $builder = new Builder(
            data: $url,
            writer: new PngWriter(),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            logoPath: public_path('assets/front-end/img/logo-png.png'),
            logoResizeToWidth: 90
        );

        $result = $builder->build();
        $folder = storage_path('app/public/qrcodes');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }
        $filePath = $folder . "/mahakal-qr-url.png";
        $result->saveToFile($filePath);
        $webPath = asset("storage/app/public/qrcodes/mahakal-qr-url.png"). '?v=' . time();
        return '<img src="' . $webPath . '" alt="QR Code" class="img-fluid mt-2">';
    }
}
