<?php

namespace App\Http\Controllers\AllController;

use App\Contracts\Repositories\CouponRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\EventApproTransactionRepositoryInterface;
use App\Contracts\Repositories\EventartistRepositoryInterface;
use App\Contracts\Repositories\EventCategoryRepositoryInterface;
use App\Contracts\Repositories\EventOrderRepositoryInterface;
use App\Contracts\Repositories\EventOrganizerRepositoryInterface;
use App\Contracts\Repositories\EventPackageRepositoryInterface;
use App\Contracts\Repositories\EventsRepositoryInterface;
use App\Contracts\Repositories\EventsReviewRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\AllPaths\EventPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponAddRequest;
use App\Http\Requests\Admin\CouponUpdateRequest;
use App\Http\Requests\Admin\EventsAddRequest;
use App\Http\Requests\Admin\EventsUpdateRequest;
use App\Http\Requests\Event\ArtistRequest;
use App\Http\Requests\Event\SponsorRequest;
use Illuminate\Http\Request;
use App\Services\EventOrganizeService;
use App\Services\EventsService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use App\Models\Seller;
use App\Models\VendorEmployees;
use App\Models\VendorRoles;
use App\Services\EventSponsorService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventOrgController extends Controller
{
    use FileManagerTrait;
    protected $relationId;
    public function __construct(
        private readonly EventOrganizerRepositoryInterface       $EventOrganizerRepo,
        private readonly EventartistRepositoryInterface      $EventartistRepo,
        private readonly TranslationRepositoryInterface      $translationRepo,

        private readonly EventPackageRepositoryInterface       $EventpackeRepo,
        private readonly EventCategoryRepositoryInterface       $EventscategoryRepo,
        private readonly EventsRepositoryInterface       $EventsRepo,
        private readonly EventOrderRepositoryInterface $EventOrder,
        private readonly EventApproTransactionRepositoryInterface     $EventapproRepo,
        private readonly EventsReviewRepositoryInterface $EventReviewRepo,
        private readonly CouponRepositoryInterface   $couponRepo,
        private readonly CustomerRepositoryInterface $customerRepo,

    ) {
        $this->middleware(function ($request, $next) {
            if (auth('event')->check()) {
                $this->relationId = auth('event')->user()->relation_id;
            } elseif (auth('event_employee')->check()) {
                $this->relationId = auth('event_employee')->user()->relation_id;
            } else {
                $this->relationId = null;
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        $orderStatus = [
            'pending' => \App\Models\Events::where('event_organizer_id', $this->relationId)
                ->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('status', 0)
                            ->whereIn('is_approve', [0, 1, 2, 3, 4]);
                    })
                        ->orWhere(function ($q2) {
                            $q2->where('status', 1)
                                ->whereIn('is_approve', [0, 2, 3, 4]);
                        });
                })
                ->count(),
            'upcomming' => \App\Models\Events::where('event_organizer_id', $this->relationId)->where('is_approve', 1)->where('status', 1)->whereRaw(" DATE(?) < STR_TO_DATE(
                IF(INSTR(start_to_end_date, ' - ' ) > 0,
                SUBSTRING_INDEX(start_to_end_date, ' - ', 1),
                start_to_end_date
                ), '%Y-%m-%d') ", [now()->format('Y-m-d')])->count(),
            'running' => \App\Models\Events::where('event_organizer_id', $this->relationId)->where('is_approve', 1)->where('status', 1)->where(function ($query) {
                $query->whereRaw("
                DATE(?) BETWEEN
                STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', 1), '%Y-%m-%d')
                AND
                STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', -1), '%Y-%m-%d')
                ", [now()->format('Y-m-d')])
                    ->orWhereRaw("
                DATE(?) =
                STR_TO_DATE(start_to_end_date, '%Y-%m-%d')
                ", [now()->format('Y-m-d')]);
            })->count(),
            'complete' => \App\Models\Events::where('event_organizer_id', $this->relationId)->where('is_approve', 1)->where('status', 1)->whereRaw("DATE(?) > STR_TO_DATE(
                IF(INSTR(start_to_end_date, ' - ') > 0,
                SUBSTRING_INDEX(start_to_end_date, ' - ', -1),
                start_to_end_date
                ), '%Y-%m-%d')", [now()->format('Y-m-d')])->count(),
            'canceled' => \App\Models\Events::where('event_organizer_id', $this->relationId)->where('status', 2)->count(),
        ];
        $tourInformation = \App\Models\EventOrganizer::where('id', $this->relationId)->first();
        $dashboardData = [
            'totalEarning' => $tourInformation['org_withdrawable_ready'],
            'pendingWithdraw' => $tourInformation['org_withdrawable_pending'],
            "adminCommission" => $tourInformation['org_total_commission'],
            "withdrawn" => $tourInformation['org_collected_cash'],
            'collectedTotalTax' => $tourInformation['org_total_tax'],
        ];
        return view(EventPath::DASHBOARD[VIEW], compact('dashboardData', 'orderStatus'));
    }

    public function profileUpdate(Request $request, $id)
    {
        $getData = $this->EventOrganizerRepo->getFirstWhere(params: ['id' => $id]);
        if (empty($getData)) {
            return back();
        }
        $vendor = Seller::where('id', auth('event')->id())->first();
        return view(EventPath::PROFILEUPDATE[VIEW], compact('getData', 'vendor'));
    }

    public function profileUpdate2(Request $request, EventOrganizeService $service)
    {
        $checkData = \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'event')->first();
        if (empty($checkData['all_doc_info'])) {
            $getUniqueArray = [
                'full_name' => 2,
                'contact_number' => 2,
                'email_address' => 2,
                'organizer_name' => 2,
                'itr_return' => 2,
                'itr_return_image' => 2,
                "organizer_address" => 2,
                'user_image' => 2,
                "aadhar_number" => 2,
                'aadhar_image' => 2,
                'organizer_pan_no' => 2,
                'pan_card_image' => 2,
                'gst_no' => 2,
                'bank_name' => 2,
                'branch_name' => 2,
                'beneficiary_name' => 2,
                'ifsc_code' => 2,
                'account_no' => 2,
                'account_type' => 2,
                'cancelled_cheque_image' => 2,
            ];
            \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'event')->update(["all_doc_info" => json_encode($getUniqueArray)]);
        }
        $vendor = \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'event')->first();
        $organizerData = $this->EventOrganizerRepo->getFirstWhere(['id' => $this->relationId]);
        $allData = $service->ReCorrectEventData($request, $organizerData, $vendor);
        \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'event')->update($allData['vendor']);
        \App\Models\EventOrganizer::where('id', $this->relationId)->update($allData['event']);
        return response()->json(['message' => $request->all(), 'status' => 1, 'data' => []], 200);
    }

    public function profileEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)(?!.*\s).{8,}$/',
                'same:confirm_password',
            ],
            'confirm_password' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->get('password')[0] ?? 'Unknown error'], 200);
        }

        if (auth('event')->check()) {
            $seller = Seller::where('id', auth('event')->id())->where(['relation_id' => $id, 'type' => 'event'])->first();
        } elseif (auth('event_employee')->check()) {
            $seller = VendorEmployees::where('id', auth('event_employee')->id())->where(['relation_id' => $id, 'type' => 'event'])->first();
        }
        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }
        $seller->password = bcrypt($request->password);
        $seller->save();
        return response()->json(['message' => translate('password_updated_successfully')]);
    }

    public function AddArtist(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(EventPath::ADDARTIST[VIEW], compact('languages', 'defaultLanguage'));
    }

    public function StoreArtist(ArtistRequest $request, EventsService $service)
    {
        $array = $service->getAddartistData($request);
        $array['created_by'] = $this->relationId;
        $array['status'] = 0;
        $insert = $this->EventartistRepo->add(data: $array);
        $this->translationRepo->add(request: $request, model: 'App\Models\Eventartist', id: $insert->id);
        Toastr::success(translate('Events_Artist_added_successfully'));
        return redirect()->route(EventPath::ADDARTIST[REDIRECT]);
    }

    public function ArtistList(Request $request)
    {
        $getData = $this->EventartistRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['created_by' => [0, $this->relationId]], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(EventPath::ARTISTLIST[VIEW], compact('getData'));
    }

    public function ArtistEdit(Request $request, $id)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $type = $request->type ?? 'edit';
        $getData = $this->EventartistRepo->getFirstWhere(params: ['id' => $id]);
        return view(EventPath::ARTISTUPDATE[VIEW], compact('type', 'languages', 'defaultLanguage', 'getData'));
    }

    public function ArtistUpdate(ArtistRequest $request, EventsService $service)
    {
        $getData = $this->EventartistRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($getData['created_by'] == $this->relationId) {
            $array = $service->getUpdateartistData($request, $getData);
            $this->EventartistRepo->update(id: $request['id'], data: $array);
            $this->translationRepo->update(request: $request, model: 'App\Models\Eventartist', id: $request['id']);
            Toastr::success(translate('Events_Artist_Updated_successfully'));
        } elseif ($getData['status'] == 1) {
            Toastr::error(translate('Event performer active status so notify admin'));
        } else {
            Toastr::error(translate('Event_artist_updated_by_admin_only'));
        }
        return redirect()->route(EventPath::ADDARTIST[REDIRECT]);
    }

    public function AddCoupon(Request $request)
    {
        $vendorId = $this->relationId;
        $searchValue = $request['searchValue'];
        $coupons = $this->couponRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $searchValue,
            filters: ['added_by' => 'event', 'vendorId' => $vendorId],
            dataLimit: getWebConfig(name: 'pagination_limit')
        );
        $customers = $this->customerRepo->getListWhereNotIn([0]);
        return view(EventPath::ADDCOUPON[VIEW], compact('coupons', 'customers', 'searchValue'));
    }

    public function StoreCoupon(CouponAddRequest $request)
    {
        if (($request['discount_type'] == 'amount' && $request['discount'] > $request['min_purchase'])) {
            Toastr::error(translate('the_minimum_purchase_amount_must_be_greater_than_discount_amount'));
            return redirect()->back();
        } elseif ($request['discount_type'] == 'percentage' && $request['discount'] >= 100) {
            Toastr::error(translate('when_discount_type percentage,discount_amount_will_be_less_than_100'));
            return redirect()->back();
        }
        $data = [
            'coupon_type' => $request['coupon_type'],
            'title' => $request['title'],
            'code' => $request['code'],
            'start_date' => $request['start_date'],
            'expire_date' => $request['expire_date'],
            'status' => 1,
            'min_purchase' => currencyConverter(amount: ($request['min_purchase'] ?? 0)),
            'coupon_bearer' => $request['coupon_bearer'],
            'seller_id' => $this->relationId,
            'customer_id' => $request['customer_id'] ?? 0,
            'limit' => $request['limit'],
            "added_by" => "seller",
            'discount_type' => $request['discount_type'],
            'discount' => $request['discount_type'] == 'amount' ? currencyConverter(amount: $request['discount']) : $request['discount'],
            'max_discount' => currencyConverter(amount: $request['max_discount'] != null ? $request['max_discount'] : $request['discount']),
        ];
        $this->couponRepo->add(data: $data);
        Toastr::success(translate('coupon_added_successfully'));
        Helpers::editDeleteLogs('Offers & Deals', 'Coupon', 'Insert');
        return back();
    }

    public function QuickView(Request $request)
    {
        $coupon = $this->couponRepo->getFirstWhere(['id' => $request['id']]);
        return response()->json([
            'view' => view(EventPath::QUICK_VIEW[VIEW], compact('coupon'))->render(),
        ]);
    }
    public function CouponDelete(Request $request)
    {
        $coupon = $this->couponRepo->getFirstWhere(['added_by' => 'seller', 'coupon_bearer' => 'event', 'id' => $request['id']]);
        if (in_array(($coupon['seller_id'] ?? "p"), [$this->relationId, 0])) {
            $this->couponRepo->delete(params: ['id' => $request['id']]);
            Toastr::success(translate('coupon_deleted_successfully'));
        } else {
            Toastr::warning(translate('coupon_not_found'));
        }
        return redirect()->back();
    }
    public function CouponEdit(Request $request)
    {
        $sellers = \App\Models\Seller::where(['status' => 'approved'])->get();
        $customers = $this->customerRepo->getListWhereNotIn([0]);
        $coupon = $this->couponRepo->getFirstWhere(['id' => $request['id']]);
        if ($coupon) {
            return view(EventPath::COUPONUPDATE[VIEW], compact('coupon', 'customers', 'sellers'));
        }
        Toastr::error(translate('invalid_Coupon'));
        return redirect()->route(EventPath::ADDCOUPON[ROUTE]);
    }

    public function CouponUpdate(CouponUpdateRequest $request)
    {
        $coupon = $this->couponRepo->getFirstWhere(['id' => $request['id']]);
        if ($coupon) {
            if (($request['discount_type'] == 'amount' && $request['discount'] > $request['min_purchase'])) {
                Toastr::error(translate('the_minimum_purchase_amount_must_be_greater_than_discount_amount'));
                return redirect()->back();
            } elseif ($request['discount_type'] == 'percentage' && $request['discount'] >= 100) {
                Toastr::error(translate('when_discount_type percentage,discount_amount_will_be_less_than_100'));
                return redirect()->back();
            }
            $data = [
                'coupon_type' => $request['coupon_type'],
                'title' => $request['title'],
                'code' => $request['code'],
                'start_date' => $request['start_date'],
                'expire_date' => $request['expire_date'],
                'status' => 1,
                'min_purchase' => currencyConverter(amount: ($request['min_purchase'] ?? 0)),
                'coupon_bearer' => 'event',
                'seller_id' => $this->relationId,
                'customer_id' => $request['customer_id'] ?? 0,
                'limit' => $request['limit'],
                "added_by" => "seller",
                'discount_type' => $request['discount_type'],
                'discount' => $request['discount_type'] == 'amount' ? currencyConverter(amount: $request['discount']) : $request['discount'],
                'max_discount' => currencyConverter(amount: $request['max_discount'] != null ? $request['max_discount'] : $request['discount']),
            ];
            $this->couponRepo->update(id: $request['id'], data: $data);
            Toastr::success(translate('coupon_updated_successfully'));
        } else {
            Toastr::error(translate('invalid_Coupon'));
        }
        return redirect()->route(EventPath::ADDCOUPON[ROUTE]);
    }

    public function CouponUpdateStatus(Request $request)
    {
        $coupon = $this->couponRepo->getFirstWhere(['added_by' => 'seller', 'coupon_bearer' => 'event', 'id' => $request['id']]);
        if ($coupon) {
            $this->couponRepo->update(id: $coupon['id'], data: ['status' => ((($coupon['status'] ?? 0) == 1) ? 0 : 1)]);
        }
        return response()->json([
            'status' => 1,
            'message' => translate('coupon_status_updated')
        ]);
    }

    public function AddPos()
    {
        $getEventData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], dataLimit: "all", filters: ['organizer' => 'outside', 'event_organizer_id' => $this->relationId, 'is_approve' => 1, 'status' => 1, 'upcomming' => 1]);
        return view(EventPath::ADDPOS[VIEW], compact('getEventData'));
    }
    public function PosgetVenuelist(Request $request)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $request['event_id']]);
        $venueName = [];
        $temp = [];
        $data = json_decode($getData['all_venue_data'] ?? "[]", true);
        foreach ($data as $value) {
            $venue = $value['en_event_venue'];
            $rawDate = $value['date'];
            $formattedDate = date('d-m-Y', strtotime($rawDate)) . " " . $value['start_time'];
            if (!isset($temp[$venue])) {
                $temp[$venue] = [
                    'name' => $venue,
                    'id' => $value['id'],
                    'date' => []
                ];
            }
            $dateKey = $venue . "_" . $rawDate;
            if (!isset($temp[$venue]['date'][$dateKey])) {
                $packageNew = [];
                if ($value['package_list']) {
                    foreach ($value['package_list'] as $key => $vas) {
                        $packageNew[$key] = $vas;
                        $packageNew[$key]['package_name'] = (\App\Models\EventPackage::where('id', $vas['package_name'])->first()['package_name'] ?? "");
                        $packageNew[$key]['package_id'] = $vas['package_name'];
                    }
                }
                $temp[$venue]['date'][$dateKey] = [
                    'date' => $formattedDate,
                    'id' => $value['id'],
                    'package' => $packageNew,
                    'seats'=> json_decode($getData['layout_auditorium']??"{}",true),
                ];
            }
        }
        $venueName = array_values($temp);
        foreach ($venueName as &$v) {
            $v['date'] = array_values($v['date']);
        }

        return response()->json([
            "data" => $venueName,
            'status' => 1,
            'message' => translate('coupon_status_updated')
        ]);
    }

    public function PosgetSponsorlist(Request $request)
    {
        $event = \App\Models\Events::find($request->event_id);
        $sponsorIds = json_decode($event['sponsor_id']??"[]", true);
        if(!$sponsorIds){
            $sponsorIds = [];
        }
        // package_id
        $sponsorlist = \App\Models\EventSponsor::where('status', 1)->where('type', $request['type'])->whereRaw('JSON_SEARCH(package_id, "one", ?) IS NOT NULL', [$request['package_id']])->whereIn("id",$sponsorIds)->get();
        return response()->json([
            "data" => $sponsorlist,
            'status' => 1,
            'message' => translate('get-sponsor_successfully')
        ]);
    }

    public function EventAdd(Request $request)
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $category_list = $this->EventscategoryRepo->getListWhere(filters: ['status' => 1], dataLimit: "all");
        $organizer_list = $this->EventOrganizerRepo->getFirstWhere(params: ['id' => $this->relationId, 'status' => 1, 'is_approve' => 1]); //
        $package_list = $this->EventpackeRepo->getListWhere(filters: ['status' => 1], dataLimit: "all");
        $artist_list = $this->EventartistRepo->getListWhere(filters: ['status' => 1, 'created_by' => [0, $this->relationId]], dataLimit: "all");
        $sponsorlist = \App\Models\EventSponsor::where('status', 1)->get();
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(EventPath::EVENTMANAG[VIEW], compact('language', 'googleMapsApiKey', 'artist_list', 'defaultLanguage', 'category_list', 'organizer_list', 'package_list', 'sponsorlist'));
    }

    public function EventStore(EventsAddRequest $request, EventsService $service)
    {
        $array = $service->getAddData($request);
        $insert = $this->EventsRepo->add(data: $array);
        $this->translationRepo->add(request: $request, model: 'App\Models\Events', id: $insert->id);
        Toastr::success(translate('Events_added_successfully'));
        Helpers::editDeleteLogs('Event', 'Event', 'Insert');
        return redirect()->route(EventPath::EVENTMANAGLIST[REDIRECT]);
    }
    public function EventAddAuditorium(Request $request)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $request['id']]);
        return view(EventPath::EVENTMANAGAUDITORIUM[VIEW], compact('getData'));
    }
    public function EventStoreAuditorium(Request $request)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($getData) {
            $getevent = $request['data'];
            $this->EventsRepo->update(id: $request['id'], data: ['layout_auditorium' => $getevent]);
        }
        return response()->json([
            'status' => 1,
            'message' => translate('updated_successfully')
        ]);
    }
    public function EventList(Request $request)
    {
        $getData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['organizer' => 'outside', 'event_organizer_id' => $this->relationId, 'is_approve' => $request->get('is_approve')], relations: ['categorys', 'eventArtist', 'EventOrder', 'organizers']);
        return view(EventPath::EVENTMANAGLIST[VIEW], compact('getData'));
    }

    public function EventPending(Request $request)
    {
        $getData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['organizer' => 'outside', 'event_organizer_id' => $this->relationId, 'status_and_isactive' => 1, 'is_approve' => $request->get('is_approve')], relations: ['categorys', 'eventArtist', 'EventOrder', 'organizers']);
        return view(EventPath::EVENTMANAGPENDING[VIEW], compact('getData'));
    }
    public function EventUpcomming(Request $request)
    {
        $getData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['organizer' => 'outside', 'event_organizer_id' => $this->relationId, 'is_approve' => 1, 'status' => 1, 'upcomming' => 1], relations: ['categorys', 'eventArtist', 'EventOrder', 'organizers']);
        return view(EventPath::EVENTMANAGUPCOMMING[VIEW], compact('getData'));
    }
    public function EventRunning(Request $request)
    {
        $getData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['organizer' => 'outside', 'event_organizer_id' => $this->relationId, 'is_approve' => 1, 'status' => 1, 'global_event' => 1], relations: ['categorys', 'eventArtist', 'EventOrder', 'organizers']);
        return view(EventPath::EVENTMANAGRUNNING[VIEW], compact('getData'));
    }
    public function EventComplate(Request $request)
    {
        $getData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['organizer' => 'outside', 'event_organizer_id' => $this->relationId, 'is_approve' => 1, 'status' => 1, 'completed' => 1], relations: ['categorys', 'eventArtist', 'EventOrder', 'organizers']);
        return view(EventPath::EVENTMANAGCOMPLATE[VIEW], compact('getData'));
    }
    public function EventCancel(Request $request)
    {
        $getData = $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['organizer' => 'outside', 'event_organizer_id' => $this->relationId, 'status' => 2], relations: ['categorys', 'eventArtist', 'EventOrder', 'organizers']);
        return view(EventPath::EVENTMANAGCANCEL[VIEW], compact('getData'));
    }

    public function EventOrderRunning(Request $request)
    {
        $Event_ids =  \App\Models\Events::where('event_organizer_id', $this->relationId)
            ->where('is_approve', 1)
            ->where('status', 1)
            ->whereRaw("STR_TO_DATE(
            IF(INSTR(start_to_end_date, ' - ') > 0, 
                SUBSTRING_INDEX(start_to_end_date, ' - ', -1), 
                start_to_end_date
            ), '%Y-%m-%d'
        ) >= ?", [now()->format('Y-m-d')])
            ->pluck('id')->toArray();

        $getOrder =  $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'all') ? $request->get('searchValue') : ""), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['event_id' => $Event_ids, 'organizer_id' => $this->relationId, 'status' => 1, 'transaction_status' => 1, 'start_to_end_date' => (($request->get('show') == 'all') ? $request->get('start_to_end_date') : "")], relations: ['userdata', 'orderitem', 'eventid']);
        $order_list_array =  $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'all') ? $request->get('searchValue') : ""), dataLimit: 'all', filters: ['event_id' => $Event_ids, 'organizer_id' => $this->relationId, 'status' => 1, 'transaction_status' => 1, 'start_to_end_date' => (($request->get('show') == 'all') ? $request->get('start_to_end_date') : "")], relations: ['userdata', 'orderitem', 'eventid']);

        $order_array = [
            'amount' => 0,
            'coupon_amount' => 0,
            'admin_commission' => 0,
            'gst_amount' => 0,
            'final_amount' => 0
        ];
        if ($order_list_array) {
            foreach ($order_list_array as $k => $val) {
                $order_array['amount'] += $val['amount'];
                $order_array['coupon_amount'] += $val['coupon_amount'];
                $order_array['admin_commission'] += $val['admin_commission'];
                $order_array['gst_amount'] += $val['gst_amount'];
                $order_array['final_amount'] += $val['final_amount'];
            }
        }

        $getevent = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'event') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'groupby_event' => 1,
            'event_id' => $Event_ids,
            'organizer_id' => $this->relationId,
            'start_to_end_date' => (($request->get('show') == 'event') ? $request->get('start_to_end_date') : "")
        ], dataLimit: getWebConfig(name: 'pagination_limit'));

        $event_list_array = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'event') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'groupby_event' => 1,
            'event_id' => $Event_ids,
            'organizer_id' => $this->relationId,
            'start_to_end_date' => (($request->get('show') == 'event') ? $request->get('start_to_end_date') : "")
        ], dataLimit: 'all');
        $event_array = [
            'amount' => 0,
            'coupon_amount' => 0,
            'admin_commission' => 0,
            'gst_amount' => 0,
            'final_amount' => 0
        ];
        if ($event_list_array) {
            foreach ($event_list_array as $k => $val) {
                $event_array['amount'] += $val['amount'];
                $event_array['coupon_amount'] += $val['coupon_amount'];
                $event_array['admin_commission'] += $val['admin_commission'];
                $event_array['gst_amount'] += $val['gst_amount'];
                $event_array['final_amount'] += $val['final_amount'];
            }
        }
        return view(EventPath::EVENTORDERRUNING[VIEW], compact('getOrder', 'order_array', 'getevent', 'event_array'));
    }
    public function EventOrderComplate(Request $request)
    {
        $Event_ids =  \App\Models\Events::where('event_organizer_id', $this->relationId)
            ->where('is_approve', 1)
            ->where('status', 1)
            ->whereRaw("STR_TO_DATE(
            IF(INSTR(start_to_end_date, ' - ') > 0, 
                SUBSTRING_INDEX(start_to_end_date, ' - ', -1), 
                start_to_end_date
            ), '%Y-%m-%d'
        ) < ?", [now()->format('Y-m-d')])
            ->pluck('id')->toArray();

        $getOrder =  $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'all') ? $request->get('searchValue') : ""), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['event_id' => $Event_ids, 'organizer_id' => $this->relationId, 'status' => 1, 'transaction_status' => 1, 'start_to_end_date' => (($request->get('show') == 'all') ? $request->get('start_to_end_date') : "")], relations: ['userdata', 'orderitem', 'eventid']);
        $order_list_array =  $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'all') ? $request->get('searchValue') : ""), dataLimit: 'all', filters: ['event_id' => $Event_ids, 'status' => 1, 'organizer_id' => $this->relationId, 'transaction_status' => 1, 'start_to_end_date' => (($request->get('show') == 'all') ? $request->get('start_to_end_date') : "")], relations: ['userdata', 'orderitem', 'eventid']);

        $order_array = [
            'amount' => 0,
            'coupon_amount' => 0,
            'admin_commission' => 0,
            'gst_amount' => 0,
            'final_amount' => 0
        ];
        if ($order_list_array) {
            foreach ($order_list_array as $k => $val) {
                $order_array['amount'] += $val['amount'];
                $order_array['coupon_amount'] += $val['coupon_amount'];
                $order_array['admin_commission'] += $val['admin_commission'];
                $order_array['gst_amount'] += $val['gst_amount'];
                $order_array['final_amount'] += $val['final_amount'];
            }
        }

        $getevent = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'event') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'groupby_event' => 1,
            'event_id' => $Event_ids,
            'organizer_id' => $this->relationId,
            'start_to_end_date' => (($request->get('show') == 'event') ? $request->get('start_to_end_date') : "")
        ], dataLimit: getWebConfig(name: 'pagination_limit'));
        $event_list_array = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'event') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'groupby_event' => 1,
            'event_id' => $Event_ids,
            'organizer_id' => $this->relationId,
            'start_to_end_date' => (($request->get('show') == 'event') ? $request->get('start_to_end_date') : "")
        ], dataLimit: 'all');
        $event_array = [
            'amount' => 0,
            'coupon_amount' => 0,
            'admin_commission' => 0,
            'gst_amount' => 0,
            'final_amount' => 0
        ];
        if ($event_list_array) {
            foreach ($event_list_array as $k => $val) {
                $event_array['amount'] += $val['amount'];
                $event_array['coupon_amount'] += $val['coupon_amount'];
                $event_array['admin_commission'] += $val['admin_commission'];
                $event_array['gst_amount'] += $val['gst_amount'];
                $event_array['final_amount'] += $val['final_amount'];
            }
        }
        return view(EventPath::EVENTORDERCOMPLATE[VIEW], compact('getOrder', 'order_array', 'getevent', 'event_array'));
    }
    public function EventOrderRefund(Request $request)
    {
        $Event_ids =  \App\Models\Events::where('event_organizer_id', $this->relationId)
            ->where('is_approve', 1)
            ->pluck('id')->toArray();

        $getOrder =  $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'all') ? $request->get('searchValue') : ""), dataLimit: getWebConfig(name: 'pagination_limit'), filters: ['event_id' => $Event_ids, 'organizer_id' => $this->relationId, 'status__transaction_status' => 1, 'start_to_end_date' => (($request->get('show') == 'all') ? $request->get('start_to_end_date') : "")], relations: ['userdata', 'orderitem', 'eventid']);
        $order_list_array =  $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'all') ? $request->get('searchValue') : ""), dataLimit: 'all', filters: ['event_id' => $Event_ids, 'status__transaction_status' => 1, 'organizer_id' => $this->relationId, 'start_to_end_date' => (($request->get('show') == 'all') ? $request->get('start_to_end_date') : "")], relations: ['userdata', 'orderitem', 'eventid']);

        $order_array = [
            'amount' => 0,
            'coupon_amount' => 0,
            'admin_commission' => 0,
            'gst_amount' => 0,
            'final_amount' => 0
        ];
        if ($order_list_array) {
            foreach ($order_list_array as $k => $val) {
                $order_array['amount'] += $val['amount'];
                $order_array['coupon_amount'] += $val['coupon_amount'];
                $order_array['admin_commission'] += $val['admin_commission'];
                $order_array['gst_amount'] += $val['gst_amount'];
                $order_array['final_amount'] += $val['final_amount'];
            }
        }

        $getevent = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'event') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'status__transaction_status_filter' => 1,
            'event_id' => $Event_ids,
            'organizer_id' => $this->relationId,
            'start_to_end_date' => (($request->get('show') == 'event') ? $request->get('start_to_end_date') : "")
        ], dataLimit: getWebConfig(name: 'pagination_limit'));
        $event_list_array = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('show') == 'event') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'status__transaction_status_filter' => 1,
            'event_id' => $Event_ids,
            'organizer_id' => $this->relationId,
            'start_to_end_date' => (($request->get('show') == 'event') ? $request->get('start_to_end_date') : "")
        ], dataLimit: 'all');
        $event_array = [
            'amount' => 0,
            'coupon_amount' => 0,
            'admin_commission' => 0,
            'gst_amount' => 0,
            'final_amount' => 0
        ];
        if ($event_list_array) {
            foreach ($event_list_array as $k => $val) {
                $event_array['amount'] += $val['amount'];
                $event_array['coupon_amount'] += $val['coupon_amount'];
                $event_array['admin_commission'] += $val['admin_commission'];
                $event_array['gst_amount'] += $val['gst_amount'];
                $event_array['final_amount'] += $val['final_amount'];
            }
        }
        return view(EventPath::EVENTORDERRUNNING[VIEW], compact('getOrder', 'order_array', 'getevent', 'event_array'));
    }

    public function AddSponsor()
    {
        $packageList = \App\Models\EventPackage::where('status', 1)->get();
        return view(EventPath::ADDSPONSOR[VIEW], compact('packageList'));
    }

    public function StoreSponsor(SponsorRequest $request, EventSponsorService $service)
    {
        $array = $service->getAddData($request);
        $array['create_by_vendor'] = $this->relationId;
        \App\Models\EventSponsor::insert($array);
        Toastr::success(translate('Event_Sponsor_added_successfully'));
        return redirect()->route(EventPath::ADDSPONSOR[REDIRECT]);
    }

    public function SponsorList(Request $request)
    {
        return view(EventPath::SPONSORLIST[VIEW]);
    }
    public function SponsorListFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('searchValue', '');
        $searchByType = $request->input('search_by_type', '');
        $searchByCabId = $request->input('search_by_status', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $start_date = $request->get('start_date') ?? '';
        $end_date = $request->get('end_date') ?? '';
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $querys = \App\Models\EventSponsor::query();
        $querys->when($searchValue, function ($qu1) use ($searchValue) {
            $qu1->Where('company_name', 'like', "%$searchValue%")
                ->orWhere('type', "%$searchValue%")
                ->orWhere('name', "%$searchValue%")
                ->orWhere('phone', "%$searchValue%");
        })
            // ->when(isset($searchByType), function ($query) use ($searchByType) {
            //     return $query->whereHas('Tour', function ($q) use ($searchByType) {
            //         $q->where('use_date', $searchByType);
            //     });
            // })
            ->when(isset($searchByCabId) && in_array($searchByCabId, [0, 1]), function ($query) use ($searchByCabId) {
                return $query->where('status', $searchByCabId);
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('created_at', [$start_date, $end_date]);
            })->where('create_by_vendor', $this->relationId);
        $recordsTotal = \App\Models\EventSponsor::when(isset($searchByType), function ($query) use ($searchByType) {
            // return $query->whereHas('Tour', function ($q) use ($searchByType) {
            //     $q->where('use_date', $searchByType); // or ->where('use_date', $searchByType)
            // });
        })->when(isset($searchByCabId) && in_array($searchByCabId, [0, 1]), function ($query) use ($searchByCabId) {
            return $query->where('status', $searchByCabId);
        })->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
            $query4->whereBetween('created_at', [$start_date, $end_date]);
        })->where('create_by_vendor', $this->relationId)->count();

        $recordsFiltered = $querys->count();
        $data = $querys->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {

            $options = '
            <div class="options-container">
                <a class="btn btn-info btn-sm btn-icon"  target="_blank" rel="noopener noreferrer" href="' . route('event-vendor.sponsor.sponsor_update', ['id' => $item['id']]) . '" data-toggle="tooltip" title="Update Lead"><i class="tio-edit"></i></a>
                <a href="' . route('event-vendor.sponsor.delete', [$item['id']]) . '"
                                                     class="btn btn-icon bg-label-danger btn-danger btn-sm mr-1"
                                                     onclick="return confirm(\'Are your sure, you want to Delete Recode\');" data-toggle="tooltip" aria-label="Close"
                                                     title="Delete Recode"><i class="tio-delete"></i></a>
                                                     </div>';


            $formId = 'temple-status' . $item->id . '-form';
            $inputId = 'temple-status' . $item->id;
            $checked = $item->status == 1 ? 'checked' : '';
            $routeUrl = route('event-vendor.sponsor.update-status', ['id' => $item['id']]);
            $csrf = csrf_token();

            $statusForm = '
        <form action="' . $routeUrl . '" method="post" id="' . $formId . '">
            <input type="hidden" name="_token" value="' . $csrf . '">
            <input type="hidden" name="id" value="' . $item->id . '">
            <label class="switcher mx-auto">
                <input type="checkbox" class="switcher_input toggle-switch-message" onclick="toggleswitchs(this)" name="status"
                    id="' . $inputId . '" value="1" ' . $checked . '
                    data-modal-id="toggle-status-modal-tour-order"
                    data-toggle-id="' . $inputId . '"
                    data-on-title="Want to Turn ON Sponsor status"
                    data-off-title="Want to Turn OFF Sponsor status"
                    data-on-message="<p>If enabled, this Event Sponsor will be available on the website and customer app</p>"
                    data-off-message="<p>If disabled, this Event Sponsor will be hidden from the website and customer app</p>">
                <span class="switcher_control"></span>
            </label>
        </form>';

            $packageList = "";
            if ($item['package_id'] && json_decode($item['package_id'] ?? "[]", true)) {
                foreach (json_decode($item['package_id'] ?? "[]", true) as $value) {
                    $names = (\App\Models\EventPackage::where('id', $value)->first()['package_name'] ?? "");
                    $packageList .= "<span>" . $names . "</span><br>";
                }
            }
            return [
                'id' => $start + $key + 1,
                'image' => "<img src=" . getValidImage(path: 'storage\app\public\event\sponsor/' . $item['image'], type: 'backend-product') . " style='max-width: 43px;'>",
                'type' => ucwords($item['type'] ?? ""),
                'name' => ucwords($item['name'] ?? ""),
                'company_name' => ucwords($item['company_name'] ?? ""),
                'phone' => $item['phone'],
                "package" => $packageList,
                "status" => $statusForm,
                'create_by' => date('d M,Y h:i A', strtotime($item['created_at'])),
                'option' => $options
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }

    public function SponsorUpdateStatus(Request $request)
    {
        $getData =  \App\Models\EventSponsor::find($request['id']);
        if ($getData) {
            $data['status'] = (($getData['status'] == 1) ? 0 : 1);
            \App\Models\EventSponsor::where('id', $request['id'])->update($data);
        }
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    public function SponsorDelete(Request $request, EventSponsorService $service)
    {
        $getData =  \App\Models\EventSponsor::find($request['id']);
        if ($getData) {
            $getData->delete();
            Toastr::success(translate('Event_Sponsor_Deleted_successfully'));
        } else {
            Toastr::error(translate('Event_Sponsor_Deleted_Failed'));
        }
        return redirect()->route(EventPath::ADDSPONSOR[REDIRECT]);
    }
    public function SponsorEdit(Request $request)
    {
        $getData =  \App\Models\EventSponsor::find($request['id']);
        if ($getData) {
            $sponsorlist = \App\Models\EventSponsor::where('status', 1)->get();
            $packageList = \App\Models\EventPackage::where('status', 1)->get();
            $selectedPackages = json_decode($getData['package_id'] ?? '[]', true);
            return view(EventPath::SPONSORUPDATE[VIEW], compact('packageList', 'selectedPackages', 'getData', 'sponsorlist'));
        }
        Toastr::error(translate('Event_Sponsor_Recode_not_found'));
        return redirect()->route(EventPath::ADDSPONSOR[REDIRECT]);
    }
    public function SponsorUpdate(SponsorRequest $request, EventSponsorService $service)
    {
        $getData =  \App\Models\EventSponsor::find($request['id']);
        if ($getData) {
            $array = $service->getUpdateData($request, $getData);
            \App\Models\EventSponsor::where('id', $request['id'])->update($array);
            Toastr::success(translate('Event_Sponsor_updated_successfully'));
        }
        return redirect()->route(EventPath::ADDSPONSOR[REDIRECT]);
    }
    public function EventOrderView(Request $request)
    {
        $getData = \App\Models\EventOrder::with(['orderitem', 'eventid'])->find($request['order_id']);
        if ($getData) {
            $html = '<table class="table order-item-table">';
            $html .= '<thead>
                    <tr>
                        <td>Sno.</td>
                        <td>package Name</td>
                        <td>No of seats</td>
                        <td>Amount</td>
                        <td>Final Amount</td>
                        <td>Print</td>
                    </tr>
                </thead>
                <tbody>';
            if ($getData['orderitem']) {
                $p = 1;
                foreach ($getData['orderitem'] as $key => $value) {
                    $html .= "
                                    <tr>
                                        <td>" . $p . "</td>
                                        <td>" . ($value['category']['package_name'] ?? "") . "</td>
                                        <td>" . $value['no_of_seats'] . "</td>
                                        <td>" . ($value['amount'] / $value['no_of_seats'] ?? 0) . "</td>
                                        <td>" . $value['amount'] . "</td>
                                        <td><i class='tio-print'></i></td>
                                    </tr>";
                    $p++;
                }
            }
            $html .= '</tbody>
        </table>';
            return response()->json(['success' => 1, 'data' => $html]);
        } else {
            return response()->json(['success' => 0, 'data' => '']);
        }
    }

    public function EventUpdate(Request $request)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $request['id']], relations: ['translations']);
        if ($getData) {
            $language = getWebConfig(name: 'pnc_language') ?? null;
            $defaultLanguage = $language[0];
            $category_list = $this->EventscategoryRepo->getListWhere(filters: ['status' => 1], dataLimit: "all");
            $organizer_list = $this->EventOrganizerRepo->getFirstWhere(params: ['id' => $this->relationId, 'status' => 1, 'is_approve' => 1]); //
            $package_list = $this->EventpackeRepo->getListWhere(filters: ['status' => 1], dataLimit: "all");
            $artist_list = $this->EventartistRepo->getListWhere(filters: ['status' => 1], dataLimit: "all");
            $googleMapsApiKey = config('services.google_maps.api_key');
            $sponsorlist = \App\Models\EventSponsor::where('status', 1)->get();
            return view(EventPath::EVENTMANAGUPDATE[VIEW], compact('language', 'googleMapsApiKey', 'artist_list', 'defaultLanguage', 'category_list', 'organizer_list', 'package_list', 'getData', 'sponsorlist'));
        } else {
            Toastr::error(translate('Events_Data_Not_found'));
            return redirect()->route(EventPath::EVENTMANAGLIST[REDIRECT]);
        }
    }

    public function EventEdits(EventsUpdateRequest $request, EventsService $service, $id)
    {
        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $id]);
        if ($getData['is_approve'] == 0 || $getData['status'] == 0) {
            $array = $service->getUpdateData($request, $getData);
            if ($array) {
                $insert = $this->EventsRepo->update(id: $id, data: $array);
                $this->translationRepo->update(request: $request, model: 'App\Models\Events', id: $id);
                Helpers::editDeleteLogs('Event', 'Event', 'Update');
                Toastr::success(translate('Events_update_successfully'));
            } else {
                Toastr::error(translate('Event_update_failed_because_tickets_have_already_booked.'));
            }
        } else {
            Toastr::error(translate('Event performer active status so notify admin'));
        }
        return redirect()->route(EventPath::EVENTMANAGLIST[REDIRECT]);
    }

    public function EventDetailsOverview(Request $request)
    {
        $name = $request['name'] ?? "null";
        $view_type = (($request['id'] == null) ? 1 : 2);

        $getData = $this->EventsRepo->getFirstWhere(params: ['id' => $request['id']], relations: ['organizers']);
        $order_list = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('name') == 'order') ? $request->get('searchValue') : ""), relations: ['eventid', 'userdata'], filters: [
            'event_id' => $request['id'],
            'order_status' => $request->get('order-status') ?? 1,
            'status' => 1,
            'venue_id' => (($request->get('name') == 'order') ? ($request->get('venue_id') ?? '') : "")
        ], dataLimit: getWebConfig(name: 'pagination_limit'));


        $getevent = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('name') == 'apevent') ? $request->get('searchValue') : ""), relations: ['eventid'], filters: [
            'transaction_status' => 1,
            'status' => 1,
            'groupby_event' => 1,
            'organizer_id' => $request->get('organizer'),
            'start_to_end_date' => (($request->get('name') == 'apevent') ? $request->get('start_to_end_date') : "")
        ], dataLimit: getWebConfig(name: 'pagination_limit'));

        if ($request->get('name') == 'review') {
            $event_reviews = $this->EventReviewRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['userdata'], filters: ['event_id' => $request['id']], dataLimit: getWebConfig(name: 'pagination_limit'));
        } else {
            $event_reviews = $this->EventReviewRepo->getListWhere(orderBy: ['id' => 'desc'], relations: ['userdata'], filters: ['event_id' => $request['id']], dataLimit: getWebConfig(name: 'pagination_limit'));
        }
        if ($request->get('name') == 'refund') {
            $order_refund_list = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['eventid', 'userdata'], filters: ['event_id' => ($request['id'] ?? ''),  'status__transaction_status' => 1, 'venue_id' => ($request->get('venue_id') ?? '')], dataLimit: getWebConfig(name: 'pagination_limit'));
        } else {
            $order_refund_list = $this->EventOrder->getListWhere(orderBy: ['id' => 'desc'], relations: ['eventid', 'userdata'], filters: ['event_id' => ($request['id'] ?? ''),  'status__transaction_status' => 1, 'venue_id' => ($request->get('venue_id') ?? '')], dataLimit: getWebConfig(name: 'pagination_limit'));
        }
        // dd($order_refund_list);
        return view(EventPath::EVENTOVERVIEW[VIEW], compact('name', 'event_reviews', 'getevent', 'order_refund_list', 'view_type', 'getData', 'order_list'));
    }




    public function EventSupportTicket(Request $request)
    {
        $vendorId = $this->relationId;
        $support_list = \App\Models\VendorSupportTicket::where(['created_by' => 'vendor', 'type' => 'event'])->get();
        $message_list = \App\Models\VendorSupportTicketConv::where(['created_by' => 'vendor', 'type' => 'event', 'vendor_id' => $vendorId])
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->with(['Event'])->paginate(10, ['*'], 'page');

        return view(EventPath::EVENTINBOX[VIEW], compact('message_list', 'support_list'));
    }

    public function EventSupportTicketStore(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:vendor_support_tickets,id',
            'created_by' => 'required|in:admin,vendor',
            'type' => 'required|in:event',
            'query_title' => 'required',
            'message' => 'required',
        ]);

        $save_ticket = new \App\Models\VendorSupportTicketConv();
        $save_ticket->ticket_id = $request->ticket_id;
        $save_ticket->created_by = $request->created_by;
        $save_ticket->type = $request->type;
        $save_ticket->vendor_id = $this->relationId;
        $save_ticket->query_title = $request->query_title;
        $save_ticket->status = 'open';
        $save_ticket->save();

        $ticket_his = new \App\Models\VendorSupportTicketConvHis();
        $ticket_his->ticket_issue_id = $save_ticket->id;
        $ticket_his->sender_type = 'user';
        $ticket_his->message = $request->message;
        $ticket_his->save();
        Toastr::success(translate('ticket_created_successfully'));
        return back();
    }
    public function EventSupportTicketStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:vendor_support_tickets_conv,id',
        ]);
        $ticket_his = \App\Models\VendorSupportTicketConv::find($request->id);
        $ticket_his->status = $request->get('status', 'close');
        $ticket_his->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function EventSupportTicketView(Request $request)
    {
        $supportTicket = \App\Models\VendorSupportTicketConv::with(['Event', 'conversations'])->find($request->id);
        \App\Models\VendorSupportTicketConvHis::where('ticket_issue_id', $request->id)->update(['read_user_status' => 1]);
        return view(EventPath::EVENTINBOXVIEW[VIEW], compact('supportTicket'));
    }
    public function EventSupportTicketReplay(Request $request)
    {
        $request->validate([
            'ticket_issue_id' => 'required|integer|exists:vendor_support_tickets_conv,id',
            "sender_type" => "required|in:admin,user",
            'replay' => "required",
        ]);
        $attachedPaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/support-ticket', $imageName);
                $attachedPaths[] = $imageName;
            }
        }

        $ticket_his = new \App\Models\VendorSupportTicketConvHis();
        $ticket_his->ticket_issue_id = $request->ticket_issue_id;
        $ticket_his->sender_type = $request->sender_type;
        $ticket_his->message = $request->replay;
        $ticket_his->attached = json_encode($attachedPaths);
        $ticket_his->save();
        Toastr::success(translate('Message_sent_successfully'));
        return back();
    }

    // admin
    public function AdminSupportTicket(Request $request)
    {
        $vendorId = $this->relationId;
        $support_list = \App\Models\VendorSupportTicket::where(['created_by' => 'admin', 'type' => 'event'])->get();
        $message_list = \App\Models\VendorSupportTicketConv::where(['created_by' => 'admin', 'type' => 'event', 'vendor_id' => $vendorId])->with(['Event'])
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->paginate(10, ['*'], 'page');
        return view(EventPath::EVENTADMININBOX[VIEW], compact('message_list', 'support_list'));
    }

    public function withdrawRequests(Request $request)
    {
        $vendorId = $this->relationId;
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['vendor_id' => $vendorId, 'type' => "event"])->with(['Event'])->paginate(10, ['*'], 'page');
        return view(EventPath::WITHDRAW[VIEW], compact('withdrawRequests'));
    }
    public function GetVendorInfo(Request $request)
    {
        $amounts = 0;
        $amounts = \App\Models\EventOrganizer::select('org_withdrawable_ready as wallet_amount')->where('id', $request['id'])->first()['wallet_amount'] ?? 0;

        $tour_data = \App\Models\EventOrganizer::select('beneficiary_name as bank_holder_name', 'bank_name', 'branch_name as bank_branch', 'ifsc_code', 'account_no as account_number')->where('id', $request['id'])->first();
        if ($tour_data) {
            return response()->json(['success' => 1, 'amount' => $amounts, 'bank_info' => $tour_data, 'message' => "Vendor Withdrawal Info"], 200);
        } else {
            return response()->json(['success' => 0, 'amount' => 0, 'bank_info' => [], 'message' => "Not Found Vendor"], 200);
        }
    }

    public function AddWithdrawalRequest(Request $request)
    {
        if (!\App\Models\WithdrawalAmountHistory::where(['vendor_id' => $this->relationId, 'type' => "event", 'status' => 0])->exists()) {
            if ($request['req_amount'] <= $request['wallet_amount']) {
                $withdrawal  =  new \App\Models\WithdrawalAmountHistory();
                $withdrawal->type = "event";
                $withdrawal->vendor_id = $this->relationId;
                $withdrawal->ex_id = (($request->ex_id) ? $request->ex_id : "");
                $withdrawal->holder_name = $request['holder_name'] ?? "";
                $withdrawal->bank_name = $request['bank_name'] ?? "";
                $withdrawal->ifsc_code = $request['ifsc_code'] ?? "";
                $withdrawal->account_number = $request['account_number'] ?? "";
                $withdrawal->upi_code = $request['upi_code'] ?? '';
                $withdrawal->old_wallet_amount = $request['wallet_amount'];
                $withdrawal->req_amount = $request['req_amount'];
                $withdrawal->save();
                if ($request->ex_id) {
                } else {
                    \App\Models\EventOrganizer::where('id', $this->relationId)->update(['org_withdrawable_pending' => $request['req_amount']]);
                }
                Toastr::success(translate('Payment_request_sent_successfully'));
            } else {
                Toastr::error(translate('Payment_Request_failed'));
            }
        } else {
            Toastr::error(translate('A_payment_request_has_already_been_sent'));
        }
        return back();
    }

    public function WithdrawalRequestView(Request $request)
    {
        $vendorId = $this->relationId;
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['vendor_id' => $vendorId, 'type' => "event"])->with(['Event'])->where('id', $request['id'])->first();
        return view(EventPath::WITHDRAWVIEW[VIEW], compact('withdrawRequests'));
    }

    public function transactionHistory(Request $request)
    {
        $vendorId = $this->relationId;
        $transactionhistory = \App\Models\EventApproTransaction::where(['organizer_id' => $vendorId, 'types' => "event_approve"])->with(['EventData'])->paginate(10);
        return view('all-views.event.withdraw.transaction', compact('transactionhistory'));
    }
    public function FCMUpdates(Request $request)
    {
        request()->session()->put('device_fcm', $request['type']);
        if ($request['type'] == 'owner') {
            \App\Models\Seller::where('id', auth('event')->id())->update(['cm_firebase_token' => $request['fcm']]);
        }
        return back();
    }
    public function FCMUpdatesdelete(Request $request)
    {
        session()->forget('device_fcm');
        return back();
    }

    public function TodayEventList(Request $request)
    {
        $today = date('Y-m-d');
        $searchValue = $request['searchValue'];
        $eventQuery = \App\Models\Events::when($searchValue, function ($query) use ($searchValue) {
            $query->where('event_name', 'like', "%$searchValue%");
            $query->orWhere('unique_id', 'like', "%$searchValue%");
        })->where('is_approve', 1)
            ->where('status', 1)
            ->where('event_organizer_id', $this->relationId)
            ->whereRaw("
            JSON_CONTAINS(
                JSON_EXTRACT(all_venue_data, '$[*].date'),
                JSON_QUOTE(?)
            )
        ", [$today]);
        $getevent = $eventQuery->paginate(getWebConfig(name: 'pagination_limit'));
        return view(EventPath::QRTODAYLIST[VIEW], compact('getevent'));
    }

    public function EventQRVerify(Request $request, $id, $venue)
    {
        if (auth('event')->check() || auth('event_employee')->check()) {
            $searchValue = $request['search'];
            $getOrderList = \App\Models\EventOrder::with(['orderitem', 'eventid', 'userdata'])
                ->when($searchValue, function ($query) use ($searchValue) {
                    $query->where('order_no', 'like', "%$searchValue%");
                    $query->orWhere(function ($query) use ($searchValue) {
                        $query->whereHas('userdata', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%")
                                ->orWhere('phone', 'like', "%$searchValue%")
                                ->orWhere('email', 'like', "%$searchValue%");
                        });
                    });
                })->where('event_id', $id)->where('transaction_status', 1)->where('status', 1)->where('venue_id', $venue)
                ->paginate(getWebConfig(name: 'pagination_limit'));

            return view(EventPath::QRTODAYINFORMATION[VIEW], compact('getOrderList'));
        } else {
            return redirect()->route('vendor.auth.login')->with('error', 'Please login to access this page.');
        }
    }

    public function EventQRVerifySubmit(Request $request, $id, $member)
    {
        $getData = \App\Models\EventOrderItems::where('order_id', $id)->first();
        if (!$getData) {
            return response()->json(['error' => 1, 'message' => "Order not found"], 200);
        }
        $getUserData = json_decode($getData['user_information'] ?? '[]', true);
        $userIndex = collect($getUserData)->search(fn($item) => $item['id'] == $member);
        if ($userIndex === false) {
            return response()->json(['error' => 1, 'message' => "User not found"], 200);
        }
        if ($getUserData[$userIndex]['verify'] == 1) {
            return response()->json(['error' => 1, 'message' => "User already verified"], 200);
        }
        $getUserData[$userIndex]['verify'] = 1;
        $getUserData[$userIndex]['time'] = date('d-m-Y h:i A');
        $getData->user_information = json_encode($getUserData);
        $getData->save();
        return response()->json(['success' => 1, 'message' => "User verified successfully"], 200);
    }

    public function AddEmployee(Request $request)
    {
        $roleList = VendorRoles::where('type', 'event')->get();
        return view(EventPath::ADDEMPLOYEE[VIEW], compact('roleList'));
    }

    public function StoreEmployee(Request $request)
    {
        $request->validate([
            'identify_number' => 'required|unique:vendor_employee,identify_number',
            'name' => 'required',
            'email' => 'required|unique:vendor_employee,email|unique:sellers,email',
            'em_phone' => 'required|unique:vendor_employee,phone|unique:sellers,phone',
            'password' => 'required',
            'emp_role_id' => 'required',
        ]);

        $employee = new VendorEmployees();
        $employee->identify_number = $request['identify_number'];
        $employee->name = $request['name'];
        $employee->type = 'event';
        $employee->phone = $request['em_phone'];
        $employee->email = $request['email'];
        $employee->emp_role_id = $request['emp_role_id'];
        $employee->password = bcrypt($request['password']);
        if ($request['image']) {
            $fileName = $imageName = time() . '_' . uniqid() . '.' . $request['image']->getClientOriginalExtension();
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('event/employee')) {
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('event/employee');
            }
            \Illuminate\Support\Facades\Storage::disk('public')->put('event/employee/' . $fileName, file_get_contents($request['image']));
            $employee->image = $imageName;
        }
        $employee->relation_id = $this->relationId;
        $employee->save();
        Toastr::success(translate('Events_Employee_added_successfully'));
        return redirect()->route(EventPath::EMPLOYEELIST[REDIRECT]);
    }

    public function EmployeeList(Request $request)
    {
        $getData = VendorEmployees::where('type', 'event')->where('relation_id', $this->relationId)->paginate(getWebConfig(name: 'pagination_limit'));
        return view(EventPath::EMPLOYEELIST[VIEW], compact('getData'));
    }

    public function EmployeeStatusUpdate(Request $request)
    {
        $data = VendorEmployees::where('type', 'event')->where('id', $request['id'])->first();
        $data->status = $request->get('status', 0);
        $data->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    public function Employeedelete(Request $request)
    {
        $old_data = VendorEmployees::where('type', 'event')->where('id', $request['id'])->where('relation_id', $this->relationId)->first();
        if ($old_data) {
            $filePath = "event/employee/" . $old_data['image'];
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
            }
            $old_data->delete();
            Toastr::success(translate('Employee_Deleted_successfully'));
            return response()->json(['success' => 1, 'message' => translate('Employee_deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Employee_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Not_found_data')], 400);
        }
    }

    public function EmployeeEdit(Request $request)
    {
        $old_data = VendorEmployees::where('type', 'event')->where('id', $request['id'])->where('relation_id', $this->relationId)->first();
        if ($old_data) {
            $roleList = VendorRoles::where('type', 'event')->get();
            return view(EventPath::EMPLOYEEUPDATE[VIEW], compact('roleList', 'old_data'));
        }
        return redirect()->route(EventPath::EMPLOYEELIST[REDIRECT]);
    }

    public function EmployeeUpdate(Request $request, $id)
    {
        $request->validate([
            'identify_number' => 'required|unique:vendor_employee,identify_number,' . $id,
            'name'            => 'required',
            'email'           => 'required|unique:vendor_employee,email,' . $id . ',id|unique:sellers,email',
            'em_phone'        => 'required|unique:vendor_employee,phone,' . $id . ',id|unique:sellers,phone',
            'emp_role_id'     => 'required',
        ]);

        $employee = VendorEmployees::where('id', $id)->where('relation_id', $this->relationId)->first();
        $employee->identify_number = $request['identify_number'];
        $employee->name = $request['name'];
        $employee->phone = $request['em_phone'];
        $employee->email = $request['email'];
        $employee->emp_role_id = $request['emp_role_id'];
        if ($request['image']) {
            $filePath = "event/employee/" . $employee->image;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
            }
            $fileName = $imageName = time() . '_' . uniqid() . '.' . $request['image']->getClientOriginalExtension();
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('event/employee')) {
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('event/employee');
            }
            \Illuminate\Support\Facades\Storage::disk('public')->put('event/employee/' . $fileName, file_get_contents($request['image']));
            $employee->image = $imageName;
        }
        $employee->save();
        Toastr::success(translate('Events_Employee_updated_successfully'));
        return redirect()->route(EventPath::EMPLOYEELIST[REDIRECT]);
    }

    public function CheckEmailPhone(Request $request)
    {
        $query = VendorEmployees::where($request['type'], $request['value']);
        if ($request['status'] == 1) {
            $query->where('id', '!=', $request['id']);
        }
        $getData = $query->first();
        if ($getData) {
            return response()->json(['success' => 1, 'message' => "Data Find", 'data' => $getData], 200);
        } else {
            $sellercheck = Seller::where('email', $request['value'])->orWhere('phone', $request['value'])->first();
            if ($sellercheck) {
                return response()->json(['success' => 1, 'message' => "Data Find", 'data' => $getData], 200);
            }
            return response()->json(['success' => 0, 'message' => 'Not Found'], 200);
        }
    }
}
