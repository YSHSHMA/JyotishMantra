<?php

namespace App\Http\Controllers\AllController;

use App\Contracts\Repositories\CitiesRepositoryInterface;
use App\Contracts\Repositories\CountryRepositoryInterface;
use App\Contracts\Repositories\DonateTrustAdsRepositoryInterface;
use App\Contracts\Repositories\DonateTrustRepositoryInterface;
use App\Contracts\Repositories\StateRepositoryInterface;
use App\Contracts\Repositories\TemplesRepositoryInterface;
use App\Contracts\Repositories\TourOrderRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\AllPaths\TrusteesPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TemplesAddRequest;
use App\Models\DonateAllTransaction;
use App\Models\DonateCategory;
use App\Models\DonateTrust;
use App\Models\Seller;
use App\Models\VendorEmployees;
use App\Models\Cities;
use App\Models\VendorRoles;
use App\Models\TrustPanditTransection;
use App\Models\PanditTransectionHistory;
use App\Models\Country;
use App\Models\DarshanOrder;
use App\Models\DarshanOrderMembers;
use App\Models\TemplePackageUpgradeHistory;
use App\Models\Purohit;
use App\Models\ServiceTax;
use App\Models\States;
use App\Models\Temple;
use App\Models\UserAadhaarKyc;
use App\Models\PaymentRequest;
use App\Models\TempleCategory;
use App\Models\TrustPuja;
use App\Models\TrustPujaOrder;
use App\Models\TempleLeadMaster;
use App\Models\TempleLeadDetail;
use App\Models\TempleOrderDetails;
use App\Models\TempleServicePrice;
use App\Models\TempleServiceSlot;
use App\Models\TempleOrderMaster;
use App\Models\User;
use App\Models\WithdrawalAmountHistory;
use App\Services\DonateTrustAdsService;
use App\Services\TemplesService;
use App\Services\GalleryService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Illuminate\Support\Facades\File;


class TrusteesController extends Controller
{
    use FileManagerTrait;
    protected $relationId;
    protected $logintype;
    protected $PurohitsId;
    protected $purohitsEmpId;
    public function __construct(
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly DonateTrustRepositoryInterface  $donateTrustRepo,
        private readonly DonateTrustAdsRepositoryInterface  $donateads,

        private readonly CitiesRepositoryInterface          $citiesRepo,
        private readonly TemplesRepositoryInterface         $templeRepo,
        private readonly CountryRepositoryInterface $countryRepo,
        private readonly StateRepositoryInterface $stateRepo,
        private readonly TourOrderRepositoryInterface  $tourorder,
    ) {
        $this->middleware(function ($request, $next) {
            if (auth('trust')->check()) {
                $this->relationId = auth('trust')->user()->relation_id;
                $this->logintype = 'trust';
                $this->PurohitsId = 0;
                $this->purohitsEmpId = 0;
            } elseif (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')) {
                $this->relationId = auth('trust_employee')->user()->relation_id;
                $this->logintype = 'pandit_employee';
                $this->PurohitsId = auth('trust_employee')->user()->purohit_id;
                $this->purohitsEmpId = auth('trust_employee')->user()->id;
            } elseif (auth('trust_employee')->check()) {
                $this->relationId = auth('trust_employee')->user()->relation_id;
                $this->logintype = 'employee';
                $this->PurohitsId = 0;
                $this->purohitsEmpId = auth('trust_employee')->user()->id;
            } elseif (auth('purohit')->check()) {
                $this->logintype = 'purohit';
                $this->PurohitsId = auth('purohit')->user()->id;
                $this->purohitsEmpId = 0;
                $this->relationId = (\App\Models\Purohit::with(['temple'])->where('id', auth('purohit')->user()->id)->first()['temple']['trust_id'] ?? 0);
            } else {
                $this->relationId = null;
                $this->logintype = '';
                $this->PurohitsId = 0;
                $this->purohitsEmpId = 0;
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        if ($this->logintype == 'trust') {
            $tourInformation = DonateTrust::where('id', $this->relationId)->first();
            $dashboardData = [
                'totalEarning' => $tourInformation['trust_total_amount'] ?? 0,
                'pendingWithdraw' => $tourInformation['trust_req_withdrawal_amount'] ?? 0,
                "adminCommission" => $tourInformation['admin_commission'] ?? 0,
                "withdrawn" => $tourInformation['trust_total_withdrawal'] ?? 0,
                "gst_total_amount" => $tourInformation['gst_total_amount'] ?? 0,
            ];
        } elseif ($this->logintype == 'pandit_employee' || $this->logintype == 'employee' || $this->logintype == 'purohit') {
            if ($this->logintype == 'pandit_employee' || $this->logintype == 'employee') {
                $tourInformation = VendorEmployees::where('id', $this->purohitsEmpId)->first();
            } else {
                $tourInformation = Purohit::where('id', $this->PurohitsId)->first();
            }
            $dashboardData = [
                'totalEarning' => $tourInformation['withdrawal_amount'] ?? 0,
                'pendingWithdraw' => $tourInformation['requested_amount'] ?? 0,
                "adminCommission" => $tourInformation['platform_fee'] ?? 0,
                "withdrawn" => $tourInformation['collected_amount'] ?? 0,
                "gst_total_amount" => $tourInformation['gst_amount'] ?? 0,
                "trust_fee" => $tourInformation['trust_fee'] ?? 0,
            ];
        }
        $withdrawalMethods = \App\Models\WithdrawalMethod::where(['is_active' => 1])->get();


        $types = session()->get('statistics_type') ?? "yearEarn";

        $query = \App\Models\DonateAllTransaction::select(DB::raw('SUM(final_amount) as y'));
        if ($types === 'yearEarn') {
            $query->addSelect(DB::raw("YEAR(created_at) as x"))->groupBy(DB::raw("YEAR(created_at)"));
        } elseif ($types === 'MonthEarn') {
            $query->addSelect(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as x"))->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"));
        } elseif ($types === 'WeekEarn') {
            $query->addSelect(DB::raw("CONCAT('Week ', WEEK(created_at), ' of ', DATE_FORMAT(created_at, '%M %Y')) as x"))->whereMonth('created_at', date('m'))->groupBy(DB::raw("YEARWEEK(created_at)"));
        } else {
            $query->addSelect(DB::raw("created_at as x"))->groupBy('created_at');
        }
        $query->where('amount_status', 1)
            ->whereIn('type', ['donate_trust', 'donate_ads'])
            ->where('trust_id', $this->relationId);
        $data_query = $query->get();
        $month_amount = [];
        $month_days = [];
        if ($data_query) {
            foreach ($data_query as $ke => $vale) {
                $month_amount[] = $vale['y'];
                $month_days[] = $vale['x'];
            }
        }

        return view(TrusteesPath::DASHBOARD[VIEW], compact('withdrawalMethods', 'dashboardData', 'month_amount', 'month_days'));
    }
    public function PujaDashboard()
    {
        if ($this->logintype == 'trust') {
            $tourInformation = DonateTrust::where('id', $this->relationId)->first();
            $dashboardData = [
                'totalEarning' => $tourInformation['trust_total_amount'] ?? 0,
                'pendingWithdraw' => $tourInformation['trust_req_withdrawal_amount'] ?? 0,
                "adminCommission" => $tourInformation['admin_commission'] ?? 0,
                "withdrawn" => $tourInformation['trust_total_withdrawal'] ?? 0,
                "gst_total_amount" => $tourInformation['gst_total_amount'] ?? 0,
                'trust_fee' => 0,
                "purohit_collected_amount"=> ($tourInformation['purohit_collected_amount'] ?? 0),
            ];
        } elseif ($this->logintype == 'employee' || $this->logintype == 'pandit_employee' || $this->logintype == 'purohit') {
            if ($this->logintype == 'purohit') {
                $tourInformation = Purohit::where('id', $this->PurohitsId)->first();
            } else {
                $tourInformation = VendorEmployees::where('id', $this->purohitsEmpId)->first();
            }
            $dashboardData = [
                'totalEarning' => $tourInformation['withdrawal_amount'] ?? 0,
                'pendingWithdraw' => $tourInformation['requested_amount'] ?? 0,
                "adminCommission" => $tourInformation['platform_fee'] ?? 0,
                "withdrawn" => $tourInformation['collected_amount'] ?? 0,
                "gst_total_amount" => $tourInformation['gst_amount'] ?? 0,
                "trust_fee" => $tourInformation['trust_fee'] ?? 0,
                "purohit_collected_amount"=> \App\Models\PanditTransectionHistory::where(['trust_id' => $this->relationId, 'status' => 0])->when(($this->logintype == 'pandit_employee'),function($q){
                                                                                                        $q->where(['purohit_id' => $this->PurohitsId, 'emp_id' => $this->purohitsEmpId]);
                                                                                                    })->when(($this->logintype == 'purohit'),function($q){
                                                                                                        $q->where(['purohit_id' => $this->PurohitsId]);
                                                                                                    })->where('order_id', '!=', '')->sum('debit'),
            ];
        }
        return view(TrusteesPath::PUJADASHBOARD[VIEW], compact('dashboardData'));
    }
    public function orderStatistics(Request $request)
    {
        session()->put('statistics_type', $request['type']);
        $query = \App\Models\DonateAllTransaction::select(DB::raw('SUM(final_amount) as y'));
        if ($request['type'] === 'yearEarn') {
            $query->addSelect(DB::raw("YEAR(created_at) as x"))->groupBy(DB::raw("YEAR(created_at)"));
        } elseif ($request['type'] === 'MonthEarn') {
            $query->addSelect(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as x"))->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"));
        } elseif ($request['type'] === 'WeekEarn') {
            $query->addSelect(DB::raw("CONCAT('Week ', WEEK(created_at), ' of ', DATE_FORMAT(created_at, '%M %Y')) as x"))->whereMonth('created_at', date('m'))->groupBy(DB::raw("YEARWEEK(created_at)"));
        } else {
            $query->addSelect(DB::raw("created_at as x"))->groupBy('created_at');
        }
        $query->where('amount_status', 1)
            ->whereIn('type', ['donate_trust', 'donate_ads'])
            ->where('trust_id', $this->relationId);
        $data_query = $query->get();
        $month_amount = [];
        $month_days = [];
        if ($data_query) {
            foreach ($data_query as $ke => $vale) {
                $month_amount[] = $vale['y'];
                $month_days[] = $vale['x'];
            }
        }
        return response()->json(['view' => view('all-views.trustees.dashboard.chart', compact('month_amount', 'month_days'))->render()], 200);
    }

    public function AdsAdd(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $all_categorys = DonateCategory::where(['status' => 1, 'type' => "category"])->get();
        $all_purpose = DonateCategory::where(['status' => 1, 'type' => "porpose"])->get();
        $unit_list = ["KG" => "KG", "Gram" => "Gram", "Liter" => "Liter", "Meter" => "Meter", "Centimeter" => "Centimeter", "Inch" => "Inch", "Pound" => "Pound", "Ounce" => "Ounce", "Milliliter" => "Milliliter", "Foot" => "Foot", "Yard" => "Yard", "Mile" => "Mile", "Kilometer" => "Kilometer", "Litre" => "Litre", "Square Meter" => "Square Meter", "Hectare" => "Hectare", "Acre" => "Acre", "Kilowatt" => "Kilowatt", "Watt" => "Watt", "Kilocalorie" => "Kilocalorie", "Calorie" => "Calorie", "Joule" => "Joule", "Pascal" => "Pascal", "Newton" => "Newton", "Pound per Square Inch" => "Pound per Square Inch", "British Thermal Unit" => "British Thermal Unit", "Hertz" => "Hertz", "Kilohertz" => "Kilohertz", "Revolutions per Minute" => "Revolutions per Minute", "Second" => "Second", "Minute" => "Minute", "Hour" => "Hour", "Day" => "Day", "Week" => "Week", "Month" => "Month", "Year" => "Year", "Person" => "Person", "Pieces" => "Pieces"];
        asort($unit_list);
        return view(TrusteesPath::ADSADD[VIEW], compact('all_purpose', 'unit_list', 'all_categorys', 'languages', 'defaultLanguage'));
    }

    public function AdsStore(Request $request, DonateTrustAdsService $service)
    {
        $request->validate([
            'name' => 'required|array',
            'name.*' => 'required|string|min:1',
            'purpose_id' => 'required',
            'set_type' => 'required',
            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
        ]);
        $request['type'] = 'outsite';
        if (\App\Models\DonateTrust::where('id', $this->relationId)->where('status', 1)->where('is_approve', 1)->exists()) {
            $request['category_id'] = \App\Models\DonateTrust::where('id', $this->relationId)->first()['category_id'] ?? 0;
            $request['trust_id'] = $this->relationId;
            $request['admin_commission'] = 5;
            $dataArray  = $service->getAddData($request);
            $insert = $this->donateads->add(data: $dataArray);
            $this->translationRepo->add(request: $request, model: 'App\Models\DonateAds', id: $insert->id);
            Toastr::success(translate('Trust_ads_added_successfully'));
            Helpers::editDeleteLogs('Donate', 'Ads Trust', 'Insert');
        } else {
            Toastr::error(translate('Your_profile_is_not_active,_on_hold,_or_pending._You_can`t_create_a_ads_donation.'));
        }
        return redirect()->route(TrusteesPath::ADSLIST[REDIRECT]);
    }

    public function AdsList(Request $request)
    {
        $ads_list = $this->donateads->getListWhere(orderBy: ['id' => 'desc'], filters: ['is_approve' => $request->get('is_approve'), 'trust_id' => $this->relationId], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TrusteesPath::ADSLIST[VIEW], compact('ads_list'));
    }

    public function AdsStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->donateads->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function AdsUpdate(Request $request, $id)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $all_purpose = DonateCategory::where(['status' => 1, 'type' => "porpose"])->get();
        $unit_list = ["KG" => "KG", "Gram" => "Gram", "Liter" => "Liter", "Meter" => "Meter", "Centimeter" => "Centimeter", "Inch" => "Inch", "Pound" => "Pound", "Ounce" => "Ounce", "Milliliter" => "Milliliter", "Foot" => "Foot", "Yard" => "Yard", "Mile" => "Mile", "Kilometer" => "Kilometer", "Litre" => "Litre", "Square Meter" => "Square Meter", "Hectare" => "Hectare", "Acre" => "Acre", "Kilowatt" => "Kilowatt", "Watt" => "Watt", "Kilocalorie" => "Kilocalorie", "Calorie" => "Calorie", "Joule" => "Joule", "Pascal" => "Pascal", "Newton" => "Newton", "Pound per Square Inch" => "Pound per Square Inch", "British Thermal Unit" => "British Thermal Unit", "Hertz" => "Hertz", "Kilohertz" => "Kilohertz", "Revolutions per Minute" => "Revolutions per Minute", "Second" => "Second", "Minute" => "Minute", "Hour" => "Hour", "Day" => "Day", "Week" => "Week", "Month" => "Month", "Year" => "Year", "Person" => "Person", "Pieces" => "Pieces"];
        asort($unit_list);
        $old_data = $this->donateads->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        return view(TrusteesPath::ADSUPDATE[VIEW], compact('old_data', 'unit_list', 'all_purpose', 'defaultLanguage', 'languages'));
    }

    public function AdsUpdateSave(Request $request, DonateTrustAdsService $service)
    {
        $old_data = $this->donateads->getFirstWhere(params: ['id' => $request->get('id')]);
        $request->validate([
            'name' => 'required|array',
            'name.*' => 'required|string|min:1',
            'purpose_id' => 'required',
            'set_type' => 'required',
            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
        ]);
        $request['type'] = 'outsite';
        if (\App\Models\DonateTrust::where('id', $this->relationId)->where('status', 1)->where('is_approve', 1)->exists()) {
            $request['category_id'] = \App\Models\DonateTrust::where('id', $this->relationId)->first()['category_id'] ?? 0;
            $request['trust_id'] = $this->relationId;
            $dataArray  = $service->getUpdateData($request, $old_data);
            $this->donateads->update(id: $request->get('id'), data: $dataArray);
            $this->translationRepo->update(request: $request, model: 'App\Models\DonateAds', id: $request->get('id'));
            Toastr::success(translate('Trust_ads_update_successfully'));
            Helpers::editDeleteLogs('Donate', 'Ads Trust', 'Update');
        } else {
            Toastr::error(translate('Your_profile_is_not_active,_on_hold,_or_pending._You_can`t_create_a_ads_donation.'));
        }
        return redirect()->route(TrusteesPath::ADSLIST[REDIRECT]);
    }

    public function AdsDelete(Request $request, DonateTrustAdsService $service)
    {
        if (DonateAllTransaction::where('ads_id', $request->get('id'))->where('amount_status', 1)->where('type', 'donate_ads')->count() > 0) {
            Toastr::error(translate('This_donation_ad_cannot_be_deleted_because_it_already_has_donors'));
            return response()->json(['success' => 0, 'message' => translate('This_donation_ad_cannot_be_deleted_because_it_already_has_donors')], 200);
        } else {
            $old_data = $this->donateads->getFirstWhere(params: ['id' => $request->get('id')]);
            if (!empty($old_data)) {
                $service->deleteAdsImage($old_data);
            }
            $this->donateads->delete(params: ['id' => $request->get('id')]);
            $this->translationRepo->delete(model: 'App\Models\DonateAds', id: $request->get('id'));
            Toastr::success(translate('Ads_Deleted_successfully'));
            Helpers::editDeleteLogs('Donate', 'Ads Trust', 'Delete');
        }
        return response()->json(['success' => 1, 'message' => translate('Ads_Deleted_successfully')], 200);
    }

    public function AdsDetails(Request $request, $id)
    {
        $old_data = $this->donateads->getFirstWhere(params: ['id' => $id], relations: ['Purpose', 'Trusts', 'category']);
        if ($old_data) {
            $ads_transaction = DonateAllTransaction::where(['type' => 'donate_ads', 'trust_id' => $old_data['trust_id'], 'ads_id' => $id])->with(['users'])->paginate(10);
            return view(TrusteesPath::ADSDETAILS[VIEW], compact('id', 'old_data', 'ads_transaction'));
        } else {
            return redirect()->route(TrusteesPath::ADSLIST[REDIRECT]);
        }
    }

    public function TrustSupportTicket(Request $request)
    {
        $vendorId = $this->relationId;
        $support_list = \App\Models\VendorSupportTicket::where(['created_by' => 'vendor', 'type' => 'trust'])->get();
        $message_list = \App\Models\VendorSupportTicketConv::where(['created_by' => 'vendor', 'type' => 'trust', 'vendor_id' => $vendorId])
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->with(['Trust'])->paginate(10, ['*'], 'page');

        return view(TrusteesPath::TRUSTINBOX[VIEW], compact('message_list', 'support_list'));
    }

    public function TrustSupportTicketStore(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:vendor_support_tickets,id',
            'created_by' => 'required|in:admin,vendor',
            'type' => 'required|in:trust',
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

    public function TrustSupportTicketStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:vendor_support_tickets_conv,id',
        ]);
        $ticket_his = \App\Models\VendorSupportTicketConv::find($request->id);
        $ticket_his->status = $request->get('status', 'close');
        $ticket_his->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function TrustSupportTicketView(Request $request)
    {
        $supportTicket = \App\Models\VendorSupportTicketConv::with(['Trust', 'conversations'])->find($request->id);
        \App\Models\VendorSupportTicketConvHis::where('ticket_issue_id', $request->id)->update(['read_user_status' => 1]);
        return view(TrusteesPath::TRUSTINBOXVIEW[VIEW], compact('supportTicket'));
    }

    public function TrustSupportTicketReplay(Request $request)
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
        Toastr::success(translate('ticket_Added_successfully'));
        return back();
    }

    public function AdminSupportTicket(Request $request)
    {
        $vendorId = $this->relationId;
        $support_list = \App\Models\VendorSupportTicket::where(['created_by' => 'admin', 'type' => 'trust'])->get();
        $message_list = \App\Models\VendorSupportTicketConv::where(['created_by' => 'admin', 'type' => 'trust', 'vendor_id' => $vendorId])
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->with(['Trust'])->paginate(10, ['*'], 'page');
        return view(TrusteesPath::TRUSTADMININBOX[VIEW], compact('message_list', 'support_list'));
    }

    public function withdrawRequests(Request $request)
    {
        $vendorId = $this->relationId;
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['vendor_id' => $vendorId])->when(true, function ($q) {
            if ($this->logintype == 'pandit_employee') {
                $q->where('ex_id', $this->purohitsEmpId)->where('type', 'purohit');
            } elseif ($this->logintype == 'purohit') {
                $templeList = VendorEmployees::where('purohit_id', $this->PurohitsId)->get();
                $templeIds = $templeList->pluck('id')->toArray();
                $q->whereIn('ex_id', $templeIds)->where('type', 'purohit');
            } else {
                $q->where('type', 'trust');
            }
        })->with(['Trust'])->paginate(10, ['*'], 'page');
        return view(TrusteesPath::TRUSTWITHDRAW[VIEW], compact('withdrawRequests'));
    }

    public function GetVendorInfo(Request $request)
    {
        if ($this->logintype == 'purohit') {
            $amounts = Purohit::select('withdrawal_amount')->where('id', $this->PurohitsId)->first()['withdrawal_amount'] ?? 0;
            $tour_data = Purohit::select('holdername as bank_holder_name', 'bankname as bank_name', 'ifsccode as ifsc_code', 'account_num as account_number')->where('id', $this->PurohitsId)->first();
            $purohitList = Purohit::select('holdername as bank_holder_name', 'bankname as bank_name', 'ifsccode as ifsc_code', 'account_num as account_number')->where('status', 1)->where('id', $this->PurohitsId)->first();
            $bankLists = \App\Models\PanditTransectionHistory::select('bank_holder_name', 'bank_name', 'ifsc_code', 'account_number')->where(['purohit_id' => $this->PurohitsId])->where('account_number', '!=', $purohitList['account_number'])->where('account_number', '!=', '')->whereNotNull('account_number')->groupBy('account_number')->get();
            $bankListdata = collect([$purohitList])->merge($bankLists)->toArray();
        } elseif ($this->logintype == 'pandit_employee') {
            $amounts = \App\Models\VendorEmployees::select('withdrawal_amount')->where('id', $this->purohitsEmpId)->first()['withdrawal_amount'] ?? 0;
            $tour_data = \App\Models\VendorEmployees::select('holdername as bank_holder_name', 'bankname as bank_name', 'ifsccode as ifsc_code', 'account_num as account_number')->where('id', $this->purohitsEmpId)->first();
            $purohitList = VendorEmployees::select('holdername as bank_holder_name', 'bankname as bank_name', 'ifsccode as ifsc_code', 'account_num as account_number')->where('status', 1)->where('id', $this->purohitsEmpId)->first();
            $bankLists = \App\Models\PanditTransectionHistory::select('bank_holder_name', 'bank_name', 'ifsc_code', 'account_number')->where(['purohit_id' => $this->PurohitsId])->where('emp_id', $this->purohitsEmpId)->where('account_number', '!=', $purohitList['account_number'])->where('account_number', '!=', '')->whereNotNull('account_number')->groupBy('account_number')->get();
            $bankListdata = collect([$purohitList])->merge($bankLists)->toArray();
        } else {
            $amounts = DonateTrust::select('trust_total_amount as wallet_amount')->where('id', $request['id'])->first()['wallet_amount'] ?? 0;
            $tour_data = DonateTrust::select('beneficiary_name as bank_holder_name', 'bank_name', 'ifsc_code', 'account_no as account_number')->where('id', $request['id'])->first();
            $purohitList = DonateTrust::select('beneficiary_name as bank_holder_name', 'bank_name', 'ifsc_code', 'account_no as account_number')->where('status', 1)->where('id', $request['id'])->first();
            $bankLists = \App\Models\WithdrawalAmountHistory::select('holder_name as bank_holder_name', 'bank_name', 'ifsc_code', 'account_number')->where('type', 'trust')->where(['vendor_id' => $request['id']])->where('ex_id', 0)->where('account_number', '!=', $purohitList['account_number'])->where('account_number', '!=', '')->whereNotNull('account_number')->groupBy('account_number')->get();
            $bankListdata = collect([$purohitList])->merge($bankLists)->toArray();
        }
        if ($tour_data) {
            return response()->json(['success' => 1, 'amount' => $amounts, 'banklistdata' => $bankListdata, 'bank_info' => $tour_data, 'message' => "Vendor Withdrawal Info"], 200);
        } else {
            return response()->json(['success' => 0, 'amount' => 0, 'banklistdata' => [], 'bank_info' => [], 'message' => "Not Found Vendor"], 200);
        }
    }
    public function GetVendorEmployeeInfo(Request $request)
    {
        $amounts = \App\Models\Purohit::select('withdrawal_amount')->where('id', $request['id'])->first()['withdrawal_amount'] ?? 0;
        $purohitList = Purohit::select('holdername as bank_holder_name', 'bankname as bank_name', 'ifsccode as ifsc_code', 'account_num as account_number')->where('status', 1)->where('id', $request['id'])->first();
        $bankLists = \App\Models\PanditTransectionHistory::select('bank_holder_name', 'bank_name', 'ifsc_code', 'account_number')->where(['purohit_id' => $request['id']])->where('account_number', '!=', $purohitList['account_number'])->where('account_number', '!=', '')->whereNotNull('account_number')->groupBy('account_number')->get();
        $bankListdata = collect([$purohitList])->merge($bankLists)->toArray();
        if ($amounts) {
            return response()->json(['success' => 1, 'amount' => $amounts, 'bank_info' => $purohitList, 'banklistdata' => $bankListdata, 'message' => "Vendor Withdrawal Info"], 200);
        } else {
            return response()->json(['success' => 0, 'amount' => 0, 'bank_info' => [], 'message' => "Not Found Vendor"], 200);
        }
    }

    public function AddWithdrawalEmployeeRequest(Request $request)
    {
        if (!\App\Models\PanditTransectionHistory::where(['purohit_id' => $request['purohit_id'], 'status' => 0])->exists()) {
            if ($request['req_amount'] <= $request['wallet_amount']) {
                $getPandit =  \App\Models\purohit::with(['temple'])->where('id', $request['purohit_id'] ?? "")->first();
                $withdrawal  =  new \App\Models\PanditTransectionHistory();
                $withdrawal->type = 'puja';
                $withdrawal->temple_id = $getPandit['temple_id'];
                $withdrawal->trust_id = $getPandit['temple']['trust_id'] ?? "";
                $withdrawal->purohit_id = $request['purohit_id'] ?? "";
                $withdrawal->debit = $request['req_amount'] ?? "";
                $withdrawal->balance = ($request['wallet_amount'] ?? 0) - ($request['req_amount'] ?? 0);
                $withdrawal->request_by = "pandit";
                $withdrawal->bank_holder_name = $request['holder_name'] ?? "";
                $withdrawal->bank_name = $request['bank_name'] ?? "";
                $withdrawal->ifsc_code = $request['ifsc_code'] ?? "";
                $withdrawal->account_number = $request['account_number'] ?? "";
                $withdrawal->status = 0;
                $withdrawal->save();
                \App\Models\purohit::where('id', $request['purohit_id'] ?? "")->update(['requested_amount' => $request['req_amount']]);
                $dataemail['type'] = "Trustees";
                $dataemail['name'] = $request['holder_name'] ?? "";
                $dataemail['bank_name'] = $request['bank_name'] ?? "";
                $dataemail['ifsc_code'] = $request['ifsc_code'] ?? "";
                $dataemail['account_number'] = $request['account_number'] ?? "";
                $dataemail['old_wallet_amount'] = $request['wallet_amount'];
                $dataemail['req_amount'] = $request['req_amount'];
                $dataemail['booking_date'] = date('d M,Y h:i A');
                $dataemail['vendor_email'] = getWebConfig('company_email');
                if (getWebConfig('company_email') && filter_var(getWebConfig('company_email'), FILTER_VALIDATE_EMAIL)) {
                    Helpers::TemplateTextEmail('tour', 'vendor_payment_withdrawal_request', $dataemail);
                }
                //Whatsapp
                $admin_phones = \App\Models\Admin::where('admin_role_id', 1)->orderBy('id', 'asc')->first();
                $dataemail['admin_phone'] = $admin_phones['phone'];
                $dataemail['admin_name'] = $admin_phones['name'];
                Helpers::whatsappMessage('ecom', 'vendor_payment_withdrawal_request', $dataemail);
                Toastr::success(translate('Payment_request_sent_successfully'));
            } else {
                Toastr::error(translate('Payment_Request_failed'));
            }
        } else {
            Toastr::error(translate('A_payment_request_has_already_been_sent'));
        }
        return back();
    }

    public function AddWithdrawalRequest(Request $request)
    {
        if ($this->logintype == 'purohit') {
            Toastr::error(translate('Payment_Request_Not_Sent'));
            return back();
        } elseif ($this->logintype == 'pandit_employee') {
            $EmpId = $this->purohitsEmpId;
            $getData = \App\Models\VendorEmployees::find($this->purohitsEmpId);
            $InType = 'purohit';
            $old_total_amount = ($getData['withdrawal_amount'] ?? 0);
        } else {
            $EmpId = 0;
            $getData = \App\Models\DonateTrust::find($this->relationId);
            $old_total_amount = ($getData['trust_total_amount'] ?? 0);
            $InType = 'trust';
        }
        if (!\App\Models\WithdrawalAmountHistory::where(['vendor_id' => $this->relationId, 'status' => 0])->when(true, function ($q) {
            if ($this->logintype == 'pandit_employee') {
                $q->where('ex_id', $this->purohitsEmpId)->where('type', 'purohit');
            } elseif ($this->logintype == 'purohit') {
                $q->where('ex_id', $this->purohitsEmpId)->where('type', 'purohit');
            } else {
                $q->where('type', 'trust');
            }
        })->exists()) {
            if ($request['req_amount'] <= $old_total_amount) {
                $withdrawal  =  new \App\Models\WithdrawalAmountHistory();
                $withdrawal->type = $InType;
                $withdrawal->vendor_id = $this->relationId;
                $withdrawal->ex_id = ($EmpId);
                $withdrawal->holder_name = $request['holder_name'] ?? "";
                $withdrawal->bank_name = $request['bank_name'] ?? "";
                $withdrawal->ifsc_code = $request['ifsc_code'] ?? "";
                $withdrawal->account_number = $request['account_number'] ?? "";
                $withdrawal->upi_code = $request['upi_code'] ?? '';
                $withdrawal->old_wallet_amount = $old_total_amount;
                $withdrawal->req_amount = $request['req_amount'];
                $withdrawal->save();

                if ($this->logintype == 'purohit') {
                    $getData->update(['requested_amount' => $request['req_amount']]);
                } elseif ($this->logintype == 'pandit_employee') {
                    $getPandit =  \App\Models\VendorEmployees::with(['Temple'])->where('id', $this->purohitsEmpId ?? "")->first();
                    $withdrawal  =  new \App\Models\PanditTransectionHistory();
                    $withdrawal->type = 'puja';
                    $withdrawal->temple_id = $getPandit['temple_id'];
                    $withdrawal->trust_id = $getPandit['temple']['trust_id'] ?? "";
                    $withdrawal->purohit_id = $this->PurohitsId ?? "";
                    $withdrawal->emp_id = $this->purohitsEmpId ?? "";
                    $withdrawal->debit = $request['req_amount'] ?? "";
                    $withdrawal->balance = ($old_total_amount) - ($request['req_amount'] ?? 0);
                    $withdrawal->request_by = "pandit";
                    $withdrawal->bank_holder_name = $request['holder_name'] ?? "";
                    $withdrawal->bank_name = $request['bank_name'] ?? "";
                    $withdrawal->ifsc_code = $request['ifsc_code'] ?? "";
                    $withdrawal->account_number = $request['account_number'] ?? "";
                    $withdrawal->status = 0;
                    $withdrawal->save();
                    $getData->update(['requested_amount' => $request['req_amount']]);
                } else {
                    $getData->update(['trust_req_withdrawal_amount' => $request['req_amount']]);
                }
                $dataemail['type'] = "Trustees";
                $dataemail['name'] = $request['holder_name'] ?? "";
                $dataemail['bank_name'] = $request['bank_name'] ?? "";
                $dataemail['ifsc_code'] = $request['ifsc_code'] ?? "";
                $dataemail['account_number'] = $request['account_number'] ?? "";
                $dataemail['upi_code'] = $request['upi_code'] ?? '';
                $dataemail['old_wallet_amount'] = $request['wallet_amount'];
                $dataemail['req_amount'] = $request['req_amount'];
                $dataemail['booking_date'] = date('d M,Y h:i A');
                $dataemail['vendor_email'] = getWebConfig('company_email');
                if (getWebConfig('company_email') && filter_var(getWebConfig('company_email'), FILTER_VALIDATE_EMAIL)) {
                    Helpers::TemplateTextEmail('tour', 'vendor_payment_withdrawal_request', $dataemail);
                }
                //Whatsapp
                $admin_phones = \App\Models\Admin::where('admin_role_id', 1)->orderBy('id', 'asc')->first();
                $dataemail['admin_phone'] = $admin_phones['phone'];
                $dataemail['admin_name'] = $admin_phones['name'];
                Helpers::whatsappMessage('ecom', 'vendor_payment_withdrawal_request', $dataemail);
                Toastr::success(translate('Payment_request_sent_successfully'));
            } else {
                Toastr::error(translate('Payment_Request_failed'));
            }
        } else {
            Toastr::error(translate('A_payment_request_has_already_been_sent'));
        }
        return back();
    }

    public function DonationHistory(Request $request)
    {
        if ($request->type) {
            $ads_transaction = DonateAllTransaction::where('type', $request->type)->where('amount_status', 1)->where('trust_id', $this->relationId)->with(['users', 'getTrust', 'adsTrust'])->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));
        } else {
            $ads_transaction = DonateAllTransaction::whereIn('type', ['donate_ads', 'donate_trust'])->where('amount_status', 1)->where('trust_id', $this->relationId)->with(['users', 'getTrust', 'adsTrust'])->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));
        }
        return view("all-views.trustees.ads.history", compact('ads_transaction'));
    }

    public function DonationDetails(Request $request, $id)
    {
        $getDonated = DonateAllTransaction::where('amount_status', 1)->where('id', $id)->with(['users', 'getTrust', 'adsTrust'])->first();
        if ($getDonated) {
            return view("all-views.trustees.ads.view-details", compact('getDonated'));
        } else {
            return back();
        }
    }

    public function WithdrawalRequestView(Request $request)
    {
        $vendorId = $this->relationId;
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['vendor_id' => $vendorId])->whereIn('type', ['trust', 'purohit'])->with(['Trust', 'PanditEmp'])->where('id', $request['id'])->first();
        return view('all-views/trustees/withdraw/view', compact('withdrawRequests'));
    }

    public function profileUpdate(Request $request, $id)
    {
        $getData  = $this->donateTrustRepo->getFirstWhere(params: ['id' => $id], relations: ['category']);
        if (empty($getData)) {
            return back();
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $categoryList = \App\Models\DonateCategory::where('status', 1)->where('type', 'category')->get();
        $vendor = \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'trust')->first();
        return view(TrusteesPath::PROFILEUPDATE[VIEW], compact('vendor', 'categoryList', 'getData', 'languages', 'defaultLanguage'));
    }

    public function profileEdit(Request $request, $id)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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

        if (auth('trust')->check()) {
            $seller = Seller::where('id', auth('trust')->id())->where(['relation_id' => $id, 'type' => 'trust'])->first();
        } elseif (auth('trust_employee')->check()) {
            $seller = VendorEmployees::where('id', auth('trust_employee')->id())->where(['relation_id' => $id, 'type' => 'trust'])->first();
        } elseif (auth('purohit')->check()) {
            $seller = \App\Models\Purohit::with(['temple'])->where('id', auth('purohit')->user()->id)->first();
        }
        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }
        $seller->password = bcrypt($request->password);
        $seller->save();
        return response()->json(['message' => translate('password_updated_successfully')]);
    }

    public function FCMUpdates(Request $request)
    {
        request()->session()->put('device_fcm', $request['type']);
        if ($request['type'] == 'owner') {
            \App\Models\Seller::where('id', auth('trust')->id())->update(['cm_firebase_token' => $request['fcm']]);
        }
        return back();
    }

    public function profileUpdate2(Request $request, DonateTrustAdsService $service)
    {
        $checkData = \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'trust')->first();
        if (empty($checkData['all_doc_info'])) {
            $getUniqueArray = [
                'name' => 2,
                'trust_name' => 2,
                "trust_category" => 2,
                'trust_email' => 2,
                'full_address' => 2,
                'description' => 2,
                "members" => 2,
                "website_link" => 2,
                'user_image' => 2,
                "gallery_image" => 2,
                'pan_card' => 2,
                'pan_card_image' => 2,
                'trust_pan_card' => 2,
                'trust_pan_card_image' => 2,
                'twelve_a_certificate' => 2,
                'eighty_g_certificate' => 2,
                'niti_aayog_certificate' => 2,
                'csr_certificate' => 2,
                'e_anudhan_certificate' => 2,
                'frc_certificate' => 2,
                'bank_name' => 2,
                'beneficiary_name' => 2,
                'ifsc_code' => 2,
                'account_type' => 2,
                'account_no' => 2,
                'cancelled_cheque_image' => 2,
                'twelve_a_number' => 2,
                'eighty_g_number' => 2,
                'niti_aayog_number' => 2,
                'csr_number' => 2,
                'e_anudhan_number' => 2,
                'frc_number' => 2,
                "gst_number" => 2,
            ];
            \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'trust')->update(["all_doc_info" => json_encode($getUniqueArray)]);
        }
        $vendor = \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'trust')->first();
        $organizerData = DonateTrust::where('id', $this->relationId)->first();
        $allData = $service->ReCorrectTrustData($request, $organizerData, $vendor);

        \App\Models\Seller::where('relation_id', $this->relationId)->where('type', 'trust')->update($allData['vendor']);
        \App\Models\DonateTrust::where('id', $this->relationId)->update($allData['trust']);
        return response()->json(['message' => $request->all(), 'status' => 1, 'data' => []], 200);
    }

    public function DeleteImage(Request $request, $id, $name)
    {
        $getData = DonateTrust::where('id', $id)->first();
        $vendor = \App\Models\Seller::where('relation_id', $id)->where('type', 'trust')->first();
        $check_validate = json_decode($vendor['all_doc_info'] ?? '[]', true);
        if ($check_validate['gallery_image'] == 2 || $check_validate['gallery_image'] == 0) {
            if ($getData && $getData->gallery_image) {
                $images = [];
                $galleryImages = json_decode($getData->gallery_image, true);

                foreach ($galleryImages as $photo) {
                    if ($photo === $name) {
                        $imagePath = storage_path('app/public/donate/trust/' . $name);
                        if (\Illuminate\Support\Facades\File::exists($imagePath)) {
                            \Illuminate\Support\Facades\File::delete($imagePath);
                        }
                    } else {
                        $images[] = $photo;
                    }
                }
                $getData->gallery_image = json_encode($images);
                $getData->save();
            }
            Toastr::success('Image deleted successfully!');
            return back()->with('success', 'Image deleted successfully!');
        } else {
            Toastr::error('Image deleted Failed!');
            return back()->with('success', 'Image deleted Failed!');
        }
    }

    public function AddEmployee(Request $request)
    {
        $roleList = VendorRoles::where('type', 'trust')->get();
        $templeList = Temple::where('status', 1)->where('trust_id', $this->relationId)->get();
        $templeIds = $templeList->pluck('id')->toArray();
        $purohitsList = Purohit::whereIn('temple_id', $templeIds)
            ->with('temple')
            ->get();

        return view(TrusteesPath::ADDEMPLOYEE[VIEW], compact('roleList', 'templeList', 'purohitsList'));
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
            'temple_id' => 'required',
        ]);

        $employee = new VendorEmployees();
        $employee->identify_number = $request['identify_number'];
        $employee->name = $request['name'];
        $employee->type = 'trust';
        $employee->phone = $request['em_phone'];
        $employee->email = $request['email'];
        $employee->emp_role_id = $request['emp_role_id'];
        $employee->temple_id = $request['temple_id'];
        $employee->purohit_id = $request['purohit_id'];
        $employee->password = bcrypt($request['password']);
        $employee->selected_services = json_encode($request['selected_services']);
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
        Toastr::success(translate('Trustees_Employee_added_successfully'));
        return redirect()->route(TrusteesPath::EMPLOYEELIST[REDIRECT]);
    }

    public function EmployeeList(Request $request)
    {
        $getData = VendorEmployees::where('type', 'trust')
            ->when((auth('purohit')->check()), function ($q) use ($request) {
                $q->where('purohit_id', auth('purohit')->user()->id);
            })
            ->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')), function ($q) use ($request) {
                $q->where('id', auth('trust_employee')->user()->id);
            })->where('relation_id', $this->relationId)
            ->paginate(getWebConfig(name: 'pagination_limit'));
        foreach ($getData as $employee) {
            if (is_string($employee->selected_services)) {
                $employee->selected_services = json_decode($employee->selected_services, true);
            }
        }
        $purohitIds = $getData->pluck('purohit_id')->filter()->toArray();
        $purohitsList = Purohit::whereIn('id', $purohitIds)->with('temple')->get()->keyBy('id');
        return view(TrusteesPath::EMPLOYEELIST[VIEW], compact('getData', 'purohitsList'));
    }

    public function EmployeeStatusUpdate(Request $request)
    {
        $data = VendorEmployees::where('type', 'trust')->where('id', $request['id'])->first();
        $data->status = $request->get('status', 0);
        $data->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    public function Employeedelete(Request $request)
    {
        $old_data = VendorEmployees::where('type', 'trust')->where('id', $request['id'])->where('relation_id', $this->relationId)->first();
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
        $old_data = VendorEmployees::where('type', 'trust')->where('id', $request['id'])->where('relation_id', $this->relationId)->when(($this->logintype == 'purohit' || $this->logintype == 'pandit_employee'), function ($q) {
            $q->where('purohit_id', $this->PurohitsId);
        })->first();
        if ($old_data) {
            $roleList = VendorRoles::where('type', 'trust')->get();
            $templeList = Temple::where('status', 1)->where('trust_id', $old_data->relation_id)->get();
            $templeIds = $templeList->pluck('id')->toArray();
            $purohitsList = Purohit::whereIn('temple_id', $templeIds)->with('temple')->get();
            return view(TrusteesPath::EMPLOYEEUPDATE[VIEW], compact('roleList', 'old_data', 'templeList', 'purohitsList'));
        }
        return redirect()->route(TrusteesPath::EMPLOYEELIST[REDIRECT]);
    }

    public function EmployeeUpdate(Request $request, $id)
    {
        $request->validate([
            'identify_number' => 'required|unique:vendor_employee,identify_number,' . $id,
            'name'            => 'required',
            'email'           => 'required|unique:vendor_employee,email,' . $id . ',id|unique:sellers,email',
            'em_phone'        => 'required|unique:vendor_employee,phone,' . $id . ',id|unique:sellers,phone|unique:purohits,mobile',
            'emp_role_id'     => 'required',
        ]);

        $employee = VendorEmployees::where('id', $id)->where('relation_id', $this->relationId)->first();
        $employee->identify_number = $request['identify_number'];
        $employee->name = $request['name'];
        $employee->phone = $request['em_phone'];
        $employee->email = $request['email'];
        $employee->purohit_id = $request['purohit_id'];
        $employee->emp_role_id = $request['emp_role_id'];
        $employee->temple_id = $request['temple_id'];
        $employee->selected_services = $request->filled('selected_services') ? json_encode(json_decode($request['selected_services'], true)) : json_encode([]);
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
        Toastr::success(translate('Trustees_Employee_updated_successfully'));
        return redirect()->route(TrusteesPath::EMPLOYEELIST[REDIRECT]);
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


    public function TempleDarshanList(Request $request)
    {
        $temple = Temple::where('trust_id', $this->relationId)->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));
        return view(TrusteesPath::DARSHANTEMPLELIST[VIEW], compact('temple'));
    }

    public function TempleDarshanStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $temple = Temple::find($request['id']);
        if ($temple) {
            $temple->update($data);
        }
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function TempleDarshanEdit(Request $request)
    {
        $temple = $this->templeRepo->getFirstWhere(params: ['id' => $request['id']], relations: ['translations']);
        $citiesList = Cities::orderBy('city', 'asc')->get();
        $stateList = States::orderBy('name', 'asc')->get();
        $countryList = Country::orderBy('name', 'asc')->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $templecategory = TempleCategory::where('status', 1)->orderBy('name', 'asc')->get();
        $googleMapsApiKey =  config('services.google_maps.api_key');
        return view(TrusteesPath::DARSHANTEMPLEUPDATE[VIEW], compact('temple', 'countryList', 'templecategory', 'googleMapsApiKey', 'citiesList', 'stateList', 'languages', 'defaultLanguage'));
    }

    public function TempleDarshanUpdate(TemplesAddRequest $request, TemplesService $templeService, $id)
    {
        $temple = $this->templeRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        if (!empty($request['country_id'])) {
            if (is_numeric($request['country_id'])) {
                $request['country_id'] = (int) $request['country_id'];
            } else {
                $getcountry = $this->countryRepo->getFirstWhere(params: ['name' => (trim($request['country_id']))]);
                if (!$getcountry) {
                    $insert = $this->countryRepo->add(data: ['name' => (trim($request['country_id'])), 'sortname' => $request['country_id_short_name']]);
                    $request['country_id'] = $insert->id;
                } else {
                    $request['country_id'] = $getcountry->id;
                }
            }
        }

        if (!empty($request['state_id'])) {
            if (is_numeric($request['state_id'])) {
                $request['state_id'] = (int) $request['state_id'];
            } else {
                $getstate = $this->stateRepo->getFirstWhere(params: ['name' => strtoupper(trim($request['state_id']))]);
                if (!$getstate) {
                    $insert = $this->stateRepo->add(data: ['name' => strtoupper(trim($request['state_id'])), 'country_id' => $request['country_id']]);
                    $request['state_id'] = $insert->id;
                } else {
                    $request['state_id'] = $getstate->id;
                }
            }
        }


        if (!empty($request['city_id'])) {
            if (is_numeric($request['city_id'])) {
                $request['city_id'] = (int) $request['city_id'];
            } else {
                $cityName = ucwords(trim($request['city_id']));
                $getcities = $this->citiesRepo->getFirstWhere(params: ['city' => $cityName]);
                if (!$getcities) {
                    $insert = $this->citiesRepo->add(data: ['city' => $cityName, 'country_id'  => $request['country_id'], 'state_id' => $request['state_id'], 'short_desc'  => '', 'description' => '', 'images'      => '', 'famous_for'  => '', 'latitude'    => $request['city_latitude'] ?? '', 'longitude'   => $request['city_longitude'] ?? '']);
                    $request['city_id'] = $insert->id;
                } else {
                    $request['city_id'] = $getcities->id;
                }
            }
        }
        $vipDarshan = [];

        $serviceTax = \App\Models\ServiceTax::first();
        $platform_gst_percent = $serviceTax ? $serviceTax->platform_fee : 0;

        if ($request['vipdarshan'] && count($request['vipdarshan']) > 0) {
            $vi_id = 0;
            foreach ($request['vipdarshan'] as $vips) {
                if ($vips['name']) {
                    $vipDarshan[$vi_id]['id'] = $vi_id + 1;
                    $vipDarshan[$vi_id]['name'] = $vips['name'];
                    $vipDarshan[$vi_id]['description'] = $vips['description'];
                    if ($vips['children'] && count($vips['children']) > 0) {
                        $ch1 = 0;
                        foreach ($vips['children'] as $vi_ch) {
                            if ($vi_ch['name'] && ($vi_ch['price'] >= 0)) {
                                $vipDarshan[$vi_id]['package'][$ch1]['id'] = $ch1 + 1;
                                $vipDarshan[$vi_id]['package'][$ch1]['name'] = $vi_ch['name'];
                                $vipDarshan[$vi_id]['package'][$ch1]['price'] = $vi_ch['price'] ?? 0;
                                $vipDarshan[$vi_id]['package'][$ch1]['limit'] = $vi_ch['limit'] ?? 0;
                                $vipDarshan[$vi_id]['package'][$ch1]['today_price'] = $vi_ch['today_price'] ?? ($vi_ch['price'] ?? 0);
                                $vipDarshan[$vi_id]['package'][$ch1]['receipt_price'] = $vi_ch['receipt_price'] ?? 0;

                                $vipDarshan[$vi_id]['package'][$ch1]['platform_fee'] = (string) ($vi_ch['platform_fee'] ?? 0);
                                $platform_fee_value = (float) $vipDarshan[$vi_id]['package'][$ch1]['platform_fee'];

                                $platform_gst_amount   = round(($platform_fee_value * $platform_gst_percent) / 100, 2);
                                $platform_base_price   = round($platform_fee_value - $platform_gst_amount, 2);

                                $vipDarshan[$vi_id]['package'][$ch1]['platform_gst']        = number_format($platform_gst_amount, 2, '.', '');
                                $vipDarshan[$vi_id]['package'][$ch1]['platform_base_price'] = number_format($platform_base_price, 2, '.', '');


                                if ($vi_ch['include'] && count($vi_ch['include']) > 0) {
                                    $ch_inc1 = 0;
                                    foreach ($vi_ch['include'] as $vi_inc) {
                                        if ($vi_inc['name']) {
                                            $vipDarshan[$vi_id]['package'][$ch1]['include'][$ch_inc1]['id'] = $ch_inc1 + 1;
                                            $vipDarshan[$vi_id]['package'][$ch1]['include'][$ch_inc1]['name'] = $vi_inc['name'];
                                            $ch_inc1++;
                                        }
                                    }
                                }
                                if ($vi_ch['subchildren'] && count($vi_ch['subchildren']) > 0) {
                                    $ch_last = 0;
                                    foreach ($vi_ch['subchildren'] as $vi_onl) {
                                        if ($vi_onl['start_time'] && $vi_onl['end_time']) {
                                            $vipDarshan[$vi_id]['package'][$ch1]['date'][$ch_last]['id'] = $ch_last + 1;
                                            $vipDarshan[$vi_id]['package'][$ch1]['date'][$ch_last]['time'] = date('h:i A', strtotime($vi_onl['start_time'])) . ' - ' . date('h:i A', strtotime($vi_onl['end_time']));
                                            $ch_last++;
                                        }
                                    }
                                }
                                $ch1++;
                            }
                        }
                    }
                    $vi_id++;
                }
            }
        }
        $dataArray = $templeService->getUpdateTempleData(request: $request, temple: $temple, updateBy: 'admin');
        $dataArray['vip_plans'] = json_encode($vipDarshan);
        $this->templeRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\Temple', id: $id);
        Helpers::editDeleteLogs('Temple', 'Temple', 'Update');
        Toastr::success(translate('temple_updated_successfully'));
        return redirect()->route(TrusteesPath::DARSHANTEMPLELIST[REDIRECT]);
    }

    public function getCities(Request $request, TemplesService $templeService)
    {
        $parentId = $request['id'];
        $filter = ['id' => $parentId];
        $citiesList = cities::where('state_id', $parentId)->get();

        $dropdown = $templeService->getStatesDropdown(request: $request, cities: $citiesList);


        $childStates = '';
        if (count($citiesList) == 1) {
            $subCities = $this->citiesRepo->getListWhere(filters: ['state_id' => $citiesList[0]['id']], dataLimit: 'all');
            $childStates = $templeService->getStatesDropdown(request: $request, cities: $subCities);
        }


        return response()->json([
            'select_tag' => $dropdown,
            'sub_cities' => count($citiesList) == 1 ? $childStates : '',
        ], 200);
    }

    public function TempleDarshanBooking(Request $request)
    {
        $getData = DarshanOrder::where('status', 1)->with(['Temple', 'userData', 'Members'])
            ->when($request->searchValue, function ($query) use ($request) {
                $query->orWhere('order_id', 'like', "%$request->searchValue%");
                $query->orWhere('title', 'like', "%$request->searchValue%");
                $query->orWhere('package_name', 'like', "%$request->searchValue%");
                $query->orWhereHas('Temple', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                });
                $query->orWhereHas('userData', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                    $q->orWhere('phone', 'like', "%$request->searchValue%");
                });
            })->withCount([
                'Members as total_counts' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->whereIn('verify', [0, 1]);
                },
            ])->where('date', '>', date('d-m-Y'))->whereHas('Temple', function ($q) use ($request) {
                $q->where('trust_id', $this->relationId);
            })->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));

        return view(TrusteesPath::DARSHANTEMPLEBOOKING[VIEW], compact('getData'));
    }

    public function TempleDarshanTodayBooking(Request $request)
    {
        $getData = DarshanOrder::where('status', 1)->with(['Temple', 'userData', 'Members'])
            ->when($request->searchValue, function ($query) use ($request) {
                $query->orWhere('order_id', 'like', "%$request->searchValue%");
                $query->orWhere('title', 'like', "%$request->searchValue%");
                $query->orWhere('package_name', 'like', "%$request->searchValue%");
                $query->orWhereHas('Temple', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                });
                $query->orWhereHas('userData', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                    $q->orWhere('phone', 'like', "%$request->searchValue%");
                });
            })->withCount([
                'Members as total_counts' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->whereIn('verify', [0, 1]);
                },
                'Members as verified_count' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where('verify', 1);
                },
                'Members as not_verified_count' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where('verify', 0);
                },
            ])->where('date', '=', date('d-m-Y'))->whereHas('Temple', function ($q) use ($request) {
                $q->where('trust_id', $this->relationId);
            })->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));

        return view(TrusteesPath::DARSHANTEMPLETODAYBOOKING[VIEW], compact('getData'));
    }

    public function TempleDarshanBookingComplete(Request $request)
    {
        $getData = DarshanOrder::where('status', 1)->with(['Temple', 'userData', 'Members'])
            ->when($request->searchValue, function ($query) use ($request) {
                $query->orWhere('order_id', 'like', "%$request->searchValue%");
                $query->orWhere('title', 'like', "%$request->searchValue%");
                $query->orWhere('package_name', 'like', "%$request->searchValue%");
                $query->orWhereHas('Temple', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                });
                $query->orWhereHas('userData', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->searchValue%");
                    $q->orWhere('phone', 'like', "%$request->searchValue%");
                });
            })->withCount([
                'Members as total_counts' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->whereIn('verify', [0, 1]);
                },
                'Members as verified_count' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where('verify', 1);
                },
                'Members as not_verified_count' => function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where('verify', 0);
                },
            ])->where('date', '<', date('d-m-Y'))->whereHas('Temple', function ($q) use ($request) {
                $q->where('trust_id', $this->relationId);
            })->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));
        return view(TrusteesPath::DARSHANTEMPLEBOOKINGCOMPLETE[VIEW], compact('getData'));
    }

    public function TempleDarshanBookingInfo(Request $request)
    {
        $getData = DarshanOrder::where('id', $request['id'])->with(['Temple', 'userData', 'Members'])->first();
        return view(TrusteesPath::DARSHANTEMPLEBOOKINGINFO[VIEW], compact('getData'));
    }

    public function TempleBookingMemberCheck(Request $request)
    {
        $getData = DarshanOrderMembers::where('barcode', $request['barcode'])->with(['darshanOrder'])->whereHas('darshanOrder', function ($q) use ($request) {
            $q->where('date', '=', date('d-m-Y'));
            $q->whereHas('Temple', function ($q2) use ($request) {
                $q2->where('trust_id', $this->relationId);
            });
        })->first();
        if ($getData) {
            if ($request['type'] == 'verify') {
                $getData->verify = 1;
                $getData->save();
            }
            return response()->json(['success' => 1, 'message' => 'success', 'data' => $getData], 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function TempleGallerys(Request $request)
    {
        $gallery = \App\Models\Gallery::where('temple_id', $request['id'])->first();
        return view(TrusteesPath::DARSHANTEMPLEGALLERY[VIEW], compact('gallery'));
    }

    public function TempleImageRemove($id, $name, GalleryService $service)
    {
        $gallery = \App\Models\Gallery::where('id', $id)->first();
        if ($gallery) {
            $datas = $service->image_remove($gallery, $name);
            $gallery->images = json_encode($datas['images']);
            $gallery->save();
            Toastr::success('temple Gallery Deleted successfully');
        } else {
            Toastr::error('temple Data Not-Found');
        }
        return back();
    }

    public function TempleGalleryUpdate(Request $request, $id, GalleryService $service)
    {
        $gallery = \App\Models\Gallery::where('id', $id)->first();
        if ($gallery) {
            $request['lang'] = ['en'];
            $request['title'] = [''];
            $datas = $service->updateData($request, $gallery['images']);
            $gallery->images = $datas['images'];
            $gallery->save();
            Toastr::success('temple Gallery Updated successfully');
        } else {
            Toastr::error('temple Data Not-Found');
        }
        return back();
    }

    public function TempleDarshanBookingListings(Request $request)
    {
        $templeList = Temple::where('trust_id', $this->relationId)->orderBy('id', 'desc')->get();
        return view(TrusteesPath::DARSHANTEMPLEBOOKINGLISTING[VIEW], compact('templeList'));
    }
    public function getPurohits($temple_id)
    {
        $purohits = Purohit::where('temple_id', $temple_id)->where('status', 1)->orderBy('id', 'desc')
            ->get(['id', 'name']);
        return response()->json($purohits);
    }
    public function TempleDarshanBookingFilters(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->get('searchValue') ?? '';
        $temple_ids = $request->get('temple_id') ?? '';
        $start_date = $request->get('start_date') ?? '';
        $end_date = $request->get('end_date') ?? '';
        // Base query
        $query = DarshanOrderMembers::where('verify', 1)
            ->with(['darshanOrder.Temple'])
            ->whereHas('darshanOrder.Temple', function ($q3) {
                $q3->where('trust_id', $this->relationId);
            })
            ->when($temple_ids, function ($query3) use ($temple_ids) {
                $query3->whereHas('darshanOrder.Temple', function ($q3) use ($temple_ids) {
                    $q3->where('id', $temple_ids);
                });
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('updated_at', [$start_date, $end_date]);
            })->when($start_date && empty($end_date), function ($query) use ($start_date) {
                $query->whereDate('updated_at', date('Y-m-d', strtotime($start_date)));
            })
            ->when(empty($start_date) && $end_date, function ($query) use ($end_date) {
                $query->whereDate('updated_at', date('Y-m-d', strtotime($end_date)));
            });

        // Apply search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('aadhar', 'like', "%{$searchValue}%")
                    ->orWhere('barcode', 'like', "%{$searchValue}%")
                    ->orWhereHas('darshanOrder', function ($q2) use ($searchValue) {
                        $q2->where('order_id', 'like', "%{$searchValue}%")
                            ->orWhere('title', 'like', "%{$searchValue}%")
                            ->orWhere('package_name', 'like', "%{$searchValue}%")
                            ->orWhereHas('Temple', function ($q3) use ($searchValue) {
                                $q3->where('name', 'like', "%{$searchValue}%");
                            });
                    });
            });
        }


        // Total before filter
        $recordsTotal = DarshanOrderMembers::where('verify', 1)->with(['darshanOrder.Temple'])
            ->whereHas('darshanOrder.Temple', function ($q3) {
                $q3->where('trust_id', $this->relationId);
            })
            ->when($temple_ids, function ($query3) use ($temple_ids) {
                $query3->whereHas('darshanOrder.Temple', function ($q2) use ($temple_ids) {
                    $q2->where('id', $temple_ids);
                });
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('updated_at', [$start_date, $end_date]);
            })->when($start_date && empty($end_date), function ($query) use ($start_date) {
                $query->whereDate('updated_at', date('Y-m-d', strtotime($start_date)));
            })
            ->when(empty($start_date) && $end_date, function ($query) use ($end_date) {
                $query->whereDate('updated_at', date('Y-m-d', strtotime($end_date)));
            })->count();

        // Filtered count
        $recordsFiltered = $query->count();

        // Paginate
        $data = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        // Format data
        $formattedData = $data->map(function ($item, $key) use ($start) {
            return [
                'id' => $start + $key + 1,
                'useinfo' =>
                '<strong>Name:</strong> ' . e($item->name) . '<br>' .
                    '<strong>Aadhar:</strong> ' . e($item->aadhar) . '<br>' .
                    (($item->phone) ? '<strong>phone No.:</strong> ' . e($item->phone) . '<br>' : '') .
                    '<strong>Barcode:</strong> ' . e($item->barcode),
                'date' => date('d M,Y h:i A', strtotime($item->updated_at)),
                'order_id' => optional($item->darshanOrder)->order_id,
                'package_name' => optional($item->darshanOrder)->package_name,
                'title' => optional($item->darshanOrder)->title,
                'temple_name' => optional(optional($item->darshanOrder)->Temple)->name
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }


    public function PujaCreate(Request $request)
    {
        $pujaList = TrustPuja::where('trust_id', $this->relationId)->orderBy('id', 'desc')->get();
        return view(TrusteesPath::PUJACREATE[VIEW], compact('pujaList'));
    }

    public function PujaSave(Request $request)
    {
        $request->validate([
            'puja_name' => 'required',
            'rprice' => 'required|numeric',
            'pprice' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->rprice) {
                        $fail('Purchase Price must be less than or equal to Regular price (Retailer Price).');
                    }
                }
            ],
        ]);
        try {
            TrustPuja::create([
                'trust_id' => $this->relationId,
                'puja_name' => $request->puja_name,
                'rprice' => $request->rprice,
                'pprice' => $request->pprice,
                'discount' => ($request->rprice - $request->pprice),
            ]);
            Toastr::success('Pooja added successfully');
            return redirect()->route('trustees-vendor.puja-management.puja-list');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function PujaEdit(Request $request)
    {
        $getpuja = TrustPuja::where('trust_id', $this->relationId)->where('id', $request['id'])->first();
        return view(TrusteesPath::PUJAUPDATE[VIEW], compact('getpuja'));
    }

    public function PujaUpdate(Request $request)
    {
        $request->validate([
            'puja_name' => 'required',
            'rprice' => 'required|numeric',
            'pprice' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->rprice) {
                        $fail('Purchase Price must be less than or equal to Regular price (Retailer Price).');
                    }
                }
            ],
        ]);
        $getData = TrustPuja::where('trust_id', $this->relationId)->find($request['id']);
        if ($getData) {
            try {
                $getData->puja_name = $request->puja_name;
                $getData->rprice = $request->rprice;
                $getData->pprice = $request->pprice;
                $getData->discount = ($request->rprice - $request->pprice);
                $getData->save();
                Toastr::success('Puja updated successfully');
                return redirect()->route('trustees-vendor.puja-management.puja-list');
            } catch (\Exception $e) {
                return back()->with('error', 'Something went wrong: ' . $e->getMessage());
            }
        } else {
            Toastr::error('Invalid Id');
            return redirect()->route('trustees-vendor.puja-management.puja-list');
        }
    }

    public function PujaDelete(Request $request)
    {
        $old_data = TrustPuja::where('trust_id', $this->relationId)->find($request['id']);
        if ($old_data) {
            $old_data->delete();
            Toastr::success('Puja Deleted successfully');
            return response()->json(['success' => 1, 'message' => 'Puja Deleted successfully'], 200);
        } else {
            Toastr::error('Puja Deleted Failed');
            return response()->json(['success' => 0, 'message' => 'Puja Deleted Failed'], 400);
        }
    }

    public function PujaBookingCreate(Request $request)
    {
        $pujaList = TrustPuja::where('trust_id', $this->relationId)->orderBy('id', 'desc')->get();
        return view(TrusteesPath::PUJABOOKINGCREATE[VIEW], compact('pujaList'));
    }

    public function PujaBookingsave(Request $request)
    {
        $request->validate([
            'puja_id' => 'required',
            'user_name' => 'required',
            'person_phone' => 'required',
            'payment_mode' => 'required|in:cash,online',
        ]);
        $userfind = User::where('phone', ($request->input('person_phone') ?? ""))->first();
        if ($userfind) {
            $user_id = $userfind['id'];
        } else {
            $user = new User();
            $user->phone = $request->input('person_phone');
            $user->name = $request->input('user_name');
            $user->f_name = (explode(" ", $request->input('user_name'))[0] ?? "");
            $user->l_name = (explode(" ", $request->input('user_name'))[1] ?? "");
            $user->email = $request->input('person_phone');
            $user->password =  bcrypt('12345678');
            $user->verify_otp = $request->input('verify_otp') ?? 1;
            $user->save();
            $user_id = $user->id ?? "";
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        $userfind = User::where('phone', ($request->input('person_phone') ?? ""))->first();
        $old_data = TrustPuja::where('trust_id', $this->relationId)->find($request['puja_id']);
        if ($old_data && $request['payment_mode']) {
            $pujaOrder = new TrustPujaOrder();
            $pujaOrder->puja_name = $old_data['puja_name'];
            $pujaOrder->trust_id = $this->relationId;
            $pujaOrder->user_name = $request['user_name'];
            $pujaOrder->user_phone = $request['person_phone'];
            $pujaOrder->rprice = $old_data['rprice'];
            $pujaOrder->pprice = $old_data['pprice'];
            $pujaOrder->discount = $old_data['discount'];
            $gst_tax =  ServiceTax::find(1);
            $pujaOrder->tax = $gst_tax['trust_puja_tax'];
            $pujaOrder->tax_amount = (($old_data['pprice'] * $gst_tax['trust_puja_tax']) / 100);
            $admin_amount = ((($old_data['pprice'] - $pujaOrder->tax_amount) * $gst_tax['trust_puja_admin_tax']) / 100);
            $final_amount = ($old_data['pprice'] - $admin_amount);
            $pujaOrder->admin_commission = $admin_amount;
            $pujaOrder->final_amount = $final_amount;
            if ($request['payment_mode'] == 'cash') {
                $pujaOrder->transaction_id = 'Cash';
                $pujaOrder->paymant_method = 'Offline';
                $pujaOrder->payment_status = 1;
                $getTrustdata = DonateTrust::where('id', $this->relationId)->first();
                if ($getTrustdata && $getTrustdata['trust_total_amount'] >= ($admin_amount + $pujaOrder->tax_amount)) {
                    if ($gst_tax['trust_puja_admin_tax'] > 0 || $gst_tax['trust_puja_tax'] > 0) {
                        DonateTrust::where('id', $this->relationId)->update(['trust_total_amount' => DB::raw('trust_total_amount - ' . ($admin_amount + $pujaOrder->tax_amount))]);
                        $createWith = new WithdrawalAmountHistory();
                        $createWith->type = 'trust';
                        $createWith->vendor_id = $this->relationId;
                        $createWith->req_amount = ($admin_amount + $pujaOrder->tax_amount);
                        $createWith->approval_amount = ($admin_amount + $pujaOrder->tax_amount);
                        $createWith->message = "You have booked the puja in cash, so the amount has been debited accordingly.";
                        $createWith->status = 1;
                        $createWith->transcation_id = 'wallet';
                        $createWith->payment_method = 'wallet';
                        $createWith->save();
                        $pujaOrder->save();
                    } else {
                        $pujaOrder->save();
                    }
                    $dataemail['orderId'] = $pujaOrder->order_id;
                    $dataemail['admin_name'] = $request['user_name'];
                    $dataemail['admin_phone'] = $request['person_phone'];
                    $dataemail['rprice'] =  $pujaOrder->rprice;
                    $dataemail['discount'] = $old_data['discount'];
                    $dataemail['tax_amount'] = $pujaOrder->tax_amount;
                    $dataemail['paymant_model'] = 'Cash';
                    $dataemail['final_amount'] = ($old_data['pprice'] + $pujaOrder->tax_amount);
                    Helpers::whatsappMessage('donate', 'trust_puja_order_message', $dataemail);
                } else {
                    return response()->json(['success' => 0, 'message' => 'Please Wallet Rechage', 'data' => []], 200);
                }
                return response()->json(['success' => 1, 'status' => 1, 'message' => 'Puja get Successfully', 'data' => $pujaOrder], 200);
            } else {
                // $pujaOrder->save();
                $wallet_amount = 0;
                $total_amount = ($old_data['pprice'] + $pujaOrder->tax_amount);
                $onlinepay = ($old_data['pprice'] + $pujaOrder->tax_amount);
                $data = [
                    'additional_data' => [
                        'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                        'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                        'payment_mode' => 'web',
                        'customer_id' => $userfind['id'],
                        "order_id" => $request['person_phone'],
                        "order_ids" => $pujaOrder,
                        "amount" => ($old_data['pprice'] ?? 0),
                        "user_name" => ($request['user_name'] ?? ''),
                        "user_email" => '',
                        "user_phone" => $request['person_phone'],
                        'total_amount' => $total_amount,
                        'wallet_amount' => $wallet_amount,
                        "online_pay" => $onlinepay,
                        'page_name' => 'trust_puja_order',
                        'success_url' => route('trust-puja-orders', [$request['person_phone']]),
                    ],
                    'user_id' =>  $userfind['id'],
                    "order_id" => $request['person_phone'],
                    'payment_amount' => $onlinepay
                ];
                $url_open = \App\Http\Controllers\Customer\PaymentController::TrustPujaBooking($data);

                $dataemail['admin_phone'] = $request['person_phone'];
                $dataemail['admin_name'] = ($request['user_name'] ?? '');
                $dataemail['payment_link'] = ($url_open ?? '');
                $dataemail['service_name'] = $pujaOrder->puja_name;
                $dataemail['final_amount'] = $onlinepay;
                Helpers::whatsappMessage('donate', 'trust_puja_order_paymant_link_message', $dataemail);

                $qrCode = new \Endroid\QrCode\QrCode($url_open);
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $folder = storage_path('app/public/qrcodes');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                }
                $filePath = $folder . "/trustpujabookingamount.png";
                $result->saveToFile($filePath);
                $imageData = "<img class='upload-img-view' src='" . getValidImage(path: 'storage/app/public/qrcodes/trustpujabookingamount.png', type: 'backend-product') . "' alt=''>";
                return response()->json(['success' => 1, 'status' => 2, 'message' => 'Puja get Successfully', 'data' => $imageData, 'url' => $url_open], 200);
            }
            return response()->json(['success' => 1, 'status' => 1, 'message' => 'Puja get Successfully', 'data' => $pujaOrder], 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Puja Deleted Failed', 'data' => []], 200);
        }
    }

    public function PujaBookingOrderInfo(Request $request)
    {
        $request->validate([
            'orderid' => 'required',
        ]);
        $old_data = TrustPujaOrder::where('trust_id', $this->relationId)->where('order_id', $request['orderid'])->first();
        if ($old_data) {
            return response()->json(['success' => 1, 'message' => 'Puja data get', 'data' => $old_data], 200);
        }
        return response()->json(['success' => 0, 'message' => 'Order Id Not Found', 'data' => []], 200);
    }

    public function PujaBookingOrderList(Request $request)
    {
        return view(TrusteesPath::PUJABOOKINGORDERLIST[VIEW]);
    }

    public function PujaBookingOrderFilters(Request $request)
    {

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->get('searchValue') ?? '';
        $puja_name = $request->get('puja_name') ?? '';
        $start_date = $request->get('start_date') ?? '';
        $end_date = $request->get('end_date') ?? '';
        // Base query
        $query = TrustPujaOrder::where('payment_status', 1)->where('trust_id', $this->relationId)
            ->when($puja_name, function ($query3) use ($puja_name) {
                $query3->where('puja_name', $puja_name);
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('updated_at', [$start_date, $end_date]);
            })->when($start_date && empty($end_date), function ($query) use ($start_date) {
                $query->whereDate('updated_at', date('Y-m-d', strtotime($start_date)));
            })
            ->when(empty($start_date) && $end_date, function ($query) use ($end_date) {
                $query->whereDate('updated_at', date('Y-m-d', strtotime($end_date)));
            });

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('order_id', 'like', "%{$searchValue}%")
                    ->orWhere('puja_name', 'like', "%{$searchValue}%")
                    ->orWhere('user_name', 'like', "%{$searchValue}%")
                    ->orWhere('user_phone', 'like', "%{$searchValue}%")
                    ->orWhere('paymant_method', 'like', "%{$searchValue}%");
            });
        }


        // Total before filter
        $recordsTotal = TrustPujaOrder::where('payment_status', 1)->where('trust_id', $this->relationId)
            ->when($puja_name, function ($query3) use ($puja_name) {
                $query3->where('puja_name', $puja_name);
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('updated_at', [$start_date, $end_date]);
            })->when($start_date && !$end_date, function ($query5) use ($start_date) {
                $query5->whereDate('updated_at', date('Y-m-d', strtotime($start_date)));
            })
            ->when(!$start_date && $end_date, function ($query6) use ($end_date) {
                $query6->whereDate('updated_at', date('Y-m-d', strtotime($end_date)));
            })->count();

        // Filtered count
        $recordsFiltered = $query->count();

        // Paginate
        $data = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        // Format data
        $formattedData = $data->map(function ($item, $key) use ($start) {
            return [
                'id' => $start + $key + 1,
                'order_id' => ($item->order_id),
                'useinfo' =>
                '<strong>Name:</strong> ' . e($item->user_name) . '<br>' .
                    '<strong>Phone No.:</strong> ' . e($item->user_phone),
                'puja_name' => ($item->puja_name),
                'payment_paltform' => (($item->transaction_id == 'Cash') ? 'Cash' : 'Online'),
                "payment_summary" => '<strong>RPrice:</strong> ' . e($item->rprice) . '<br>' .
                    '<strong>Discount Amount:</strong> ' . e($item->discount) . '<br>' .
                    '<strong>Tax Amount:</strong> ' . e($item->tax_amount) . '<br>' .
                    '<strong>Admin Amount:</strong> ' . e($item->admin_commission),
                'date' => date('d M,Y h:i A', strtotime($item->updated_at)),
                'final_amount' => ($item->final_amount),
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }


    public function DarshanBookingCreate(Request $request)
    {
        $templeList = Temple::with(['Trust'])->where('trust_id', $this->relationId)->orderBy('id', 'desc')->get();
        $bookingListnow = DarshanOrder::where('status', 1)->get();
        $templeIds = $templeList->pluck('id');
        $purohits = Purohit::whereIn('temple_id', $templeIds)->with('temple')->get();

        return view(TrusteesPath::DARSHANBOOKINGCREATE[VIEW], compact('templeList', 'bookingListnow', 'purohits'));
    }

    public function DarshanBookingsave(Request $request)
    {

        $request->validate([
            'temple_id' => 'required',
            'package_id' => 'required',
            'person_phone' => 'required',
            'user_name' => 'required',
            'payment_mode' => 'required|in:cash,online',
        ]);
        $old_data = Temple::where('trust_id', $this->relationId)->find($request['temple_id']);
        if ($old_data && $request['payment_mode'] && $old_data->vip_plans) {
            $vipPlans = collect(json_decode($old_data->vip_plans, true));
            $matched = collect($vipPlans)->flatMap(fn($plan) => $plan['package'] ?? [])->first(fn($package) => (int) $package['id'] === (int) $request['package_id']);
            if (!$matched) {
                return response()->json(['error' => 'Invalid Package Select', 'data' => []], 200);
            }
            $userfind = User::where('phone', ($request->input('person_phone') ?? ""))->first();
            if ($userfind) {
                $user_id = $userfind['id'];
            } else {
                $user = new User();
                $user->phone = $request->input('person_phone');
                $user->name = $request->input('user_name');
                $user->f_name = (explode(" ", $request->input('user_name'))[0] ?? "");
                $user->l_name = (explode(" ", $request->input('user_name'))[1] ?? "");
                $user->email = $request->input('person_phone');
                $user->password =  bcrypt('12345678');
                $user->verify_otp = $request->input('verify_otp') ?? 1;
                $user->save();
                $user_id = $user->id ?? "";
                $data = [
                    'customer_id' => ($user->id ?? "")
                ];
                Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
            }
            // $darshanOrder = new DarshanOrder();
            // $darshanOrder->user_id = $user_id;
            // $darshanOrder->temple_id = $request['temple_id'];
            // $darshanOrder->purohit_id = $request['purohit_id'];
            // $darshanOrder->package_id = $request['package_id'];
            // $darshanOrder->title = $vipPlans->where('id', $request['package_id'])->pluck('name')->first();
            // $darshanOrder->package_name = $matched['name'];
            // $darshanOrder->date = $request['date'];
            // $darshanOrder->time = date('h:i A') . " - " . date('h:i A', strtotime('+2 hours'));
            // $peopleQty = count(json_decode($request['userList'] ?? "[]", true));
            // $darshanOrder->price = $matched['price'] * $peopleQty;
            // $darshanOrder->receipt_price = $matched['receipt_price'] * $peopleQty;
            // $darshanOrder->platform_fee = $matched['platform_fee'] * $peopleQty;
            // $darshanOrder->platform_base_price = $matched['platform_base_price'] * $peopleQty;
            // $darshanOrder->platform_gst = $matched['platform_gst'] * $peopleQty;
            // $darshanOrder->people_qty = $peopleQty;

            // $darshanOrder->final_amount = 
            //     ($matched['price'] * $peopleQty) +
            //     ($matched['receipt_price'] * $peopleQty) +
            //     ($matched['platform_fee'] * $peopleQty);

            $darshanOrder = new DarshanOrder();
            $darshanOrder->user_id = $user_id;
            $darshanOrder->temple_id = $request['temple_id'];
            $darshanOrder->purohit_id = $request['purohit_id'];
            $darshanOrder->package_id = $request['package_id'];
            $darshanOrder->title = $vipPlans->where('id', $request['package_id'])->pluck('name')->first();
            $darshanOrder->package_name = $matched['name'];
            $darshanOrder->date = $request['date'];
            $darshanOrder->time = date('h:i A') . " - " . date('h:i A', strtotime('+2 hours'));
            $peopleQty = count(json_decode($request['userList'] ?? "[]", true));
            $darshanOrder->price = $matched['price'] * $peopleQty;
            $darshanOrder->receipt_price = $matched['receipt_price'] * $peopleQty;
            $darshanOrder->people_qty = $peopleQty;

            if ($request['payment_mode'] !== 'cash') {
                $darshanOrder->platform_fee = $matched['platform_fee'] * $peopleQty;
                $darshanOrder->platform_base_price = $matched['platform_base_price'] * $peopleQty;
                $darshanOrder->platform_gst = $matched['platform_gst'] * $peopleQty;
            }

            $darshanOrder->final_amount =
                ($matched['price'] * $peopleQty) +
                ($matched['receipt_price'] * $peopleQty) +
                ($request['payment_mode'] !== 'cash' ? ($matched['platform_fee'] * $peopleQty) : 0);


            $darshanOrder->status = 0;
            $darshan_memberbook = [];
            if ($request['userList'] && json_decode($request['userList'] ?? "[]", true)) {
                $peopleInfo = json_decode($request['userList'] ?? "[]", true);
                for ($iq = 0; $iq < count($peopleInfo); $iq++) {
                    $darshan_memberbook[$iq]['name'] = $peopleInfo[$iq]['name'] ?? '';
                    $darshan_memberbook[$iq]['address'] = $peopleInfo[$iq]['address'] ?? '';
                    $darshan_memberbook[$iq]['phone'] = $peopleInfo[$iq]['phone'] ?? '';
                    $darshan_memberbook[$iq]['aadhar'] = $peopleInfo[$iq]['aadhar'] ?? '';
                    $darshan_memberbook[$iq]['aadhar_verify_status'] = 0;
                }
            }
            if ($request['payment_mode'] == 'cash') {
                $darshanOrder->transaction_id = 'Cash';
                $darshanOrder->payment_method = 'Offline';
                $darshanOrder->payment_mode = 'complete';
                $darshanOrder->platform = 'admin';
                $darshanOrder->status = 1;
                $darshanOrder->save();
                if ($darshan_memberbook) {
                    foreach ($darshan_memberbook as $key1 => $value1) {
                        $member = new DarshanOrderMembers();
                        $member->darshan_id = $darshanOrder->id;
                        $member->name = $value1['name'];
                        $member->address = $value1['address'];
                        $member->phone = $value1['phone'];
                        $member->aadhar = $value1['aadhar'];
                        $member->aadhar_verify_status = $value1['aadhar_verify_status'];
                        $member->save();
                    }
                }
                $dataemail['orderId'] = $darshanOrder->order_id;
                $dataemail['admin_name'] = $request['user_name'];
                $dataemail['admin_phone'] = $request['person_phone'];
                $dataemail['rprice'] =  $matched['price'] * count(json_decode($request['userList'] ?? "[]", true));
                $dataemail['discount'] = 0;
                $dataemail['tax_amount'] = 0;
                $dataemail['paymant_model'] = 'Cash';
                $dataemail['final_amount'] = ($matched['price'] * $peopleQty) +  ($matched['receipt_price'] * $peopleQty) +
                    ($matched['platform_fee'] * $peopleQty);

                TrustPanditTransection::create([
                    'order_id'       => $darshanOrder->order_id,
                    'temple_id'      => $darshanOrder->temple_id,
                    'trust_id'       => $old_data->trust_id,
                    'pandit_id'      => $darshanOrder->purohit_id ?? null,
                    'package_id'     => $matched['id'],
                    'package_price'  => $matched['price'],
                    'payment_method' => "cash",
                    'payment_status' => "complete"
                ]);
                Helpers::whatsappMessage('donate', 'vip_darshan_ticket_order_message', $dataemail);
                return response()->json(['success' => 1, 'status' => 1, 'message' => 'Ticket get Successfully', 'data' => $darshanOrder], 200);
            } else {
                $wallet_amount = 0;
                $total_amount = ($matched['price'] * $peopleQty) + ($matched['receipt_price'] * $peopleQty)  + $matched['platform_fee'];
                $onlinepay = $total_amount;
                $data = [
                    'additional_data' => [
                        'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                        'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                        'payment_mode' => 'web',
                        'customer_id' => $user_id,
                        "order_id" => $darshanOrder->id,
                        "memberList" => ($darshan_memberbook),
                        "darshanInfo" => $darshanOrder,
                        "amount" => ($onlinepay ?? 0),
                        "user_name" => ($request['user_name'] ?? ''),
                        "user_email" => '',
                        "user_phone" => $request['person_phone'],
                        'total_amount' => $total_amount,
                        'wallet_amount' => $wallet_amount,
                        "online_pay" => $onlinepay,
                        'page_name' => 'trust_vip_darshan_ticket',
                        'success_url' => route('trust-vip-darshan-ticket', ['id' => $request['person_phone']]),
                    ],
                    'user_id' => $user_id,
                    'payment_amount' => $onlinepay,
                    "order_id" => $request['person_phone'],
                    "attribute" => "trust_vip_darshan_ticket",
                    "external_redirect_link" => route('trust-vip-darshan-ticket', ['id' => $request['person_phone']]),
                ];
                $url_open = \App\Http\Controllers\Customer\PaymentController::TrustVIPTicketBooking($data);

                $dataemail['admin_phone'] = $request['person_phone'];
                $dataemail['admin_name'] = ($request['user_name'] ?? '');
                $dataemail['payment_link'] = ($url_open ?? '');
                $dataemail['service_name'] = $vipPlans->where('id', $request['package_id'])->pluck('name')->first();
                $dataemail['final_amount'] = $onlinepay;
                Helpers::whatsappMessage('donate', 'trust_puja_order_paymant_link_message', $dataemail);

                $qrCode = new \Endroid\QrCode\QrCode($url_open);
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $folder = storage_path('app/public/qrcodes');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                }
                $filePath = $folder . "/vipdarshanticketbookingamount.png";
                $result->saveToFile($filePath);
                $imageData = "<img class='upload-img-view' src='" . getValidImage(path: 'storage/app/public/qrcodes/vipdarshanticketbookingamount.png', type: 'backend-product') . "' alt=''>";
                $query12 = parse_url($url_open, PHP_URL_QUERY);
                parse_str($query12, $params12);
                $paymentId = $params12['payment_id'] ?? null;
                return response()->json(['success' => 1, 'status' => 2, 'message' => 'get url Successfully', 'data' => $imageData, 'url' => $url_open, 'paymentID' => $paymentId], 200);
            }
            return response()->json(['success' => 1, 'status' => 1, 'message' => 'Successfully', 'data' => $darshanOrder], 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Temple Recode Not Found', 'data' => []], 200);
        }
    }

    public function VipticketBookingOrderInfo(Request $request)
    {
        $request->validate([
            'orderid' => 'required',
        ]);
        $old_data = DarshanOrder::with(['Members', 'Temple', 'Purohit'])->whereHas('Temple', function ($q) use ($request) {
            $q->where('trust_id', $this->relationId);
        })->where('order_id', $request['orderid'])->first();
        if ($old_data) {
            foreach ($old_data['Members'] as $key => $value) {
                $old_data['Members'][$key]['qrcode'] = \Milon\Barcode\DNS1D::getBarcodePNG($value['barcode'], 'C128', '3', '80');
            }
            return response()->json(['success' => 1, 'message' => 'Get Order Infomation', 'data' => $old_data], 200);
        }
        return response()->json(['success' => 0, 'message' => 'Order Id Not Found', 'data' => []], 200);
    }

    public function purohit_View(Request $request)
    {
        $templeIds = Temple::where('trust_id', $this->relationId)->pluck('id');
        $temple = Temple::where('trust_id', $this->relationId)->orderBy('id', 'desc')->paginate(getWebConfig(name: 'pagination_limit'));
        $purohitList = Purohit::where('status', 1)->whereIn('temple_id', $templeIds)->with('temple')->get();
        return view('all-views.trustees.purohit.purohit-list', compact('temple', 'purohitList'));
    }

    public function purohitStore(Request $request)
    {
        $purohit = null;
        $imagePath = null;

        if ($request->purohit_id) {
            $purohit = Purohit::find($request->purohit_id);
        }

        if ($request->hasFile('profile')) {
            $folderName = 'purohit_images/' . str_replace(' ', '_', strtolower($request->name));
            if (!Storage::disk('public')->exists($folderName)) {
                Storage::disk('public')->makeDirectory($folderName);
            }
            if ($purohit && $purohit->profile && Storage::disk('public')->exists($purohit->profile)) {
                Storage::disk('public')->delete($purohit->profile);
            }
            $fileName = time() . '.' . $request->file('profile')->getClientOriginalExtension();
            $imagePath = $request->file('profile')->storeAs($folderName, $fileName, 'public');
        } else {
            $imagePath = $purohit?->profile;
        }
        $GetData = Purohit::updateOrCreate(
            ['id' => $request->purohit_id],
            [
                'temple_id'   => $request->temple_id,
                'name'        => $request->name,
                'mobile'      => $request->mobile,
                'profile'     => $imagePath,
                'holdername'  => $request->holdername,
                'bankname'    => $request->bankname,
                'account_num' => $request->account_num,
                'ifsccode'    => $request->ifsccode,
                'address'     => $request->address,
                'description' => $request->description,
                'relation_id' => \App\Models\Temple::where('id', $request->temple_id)->first()['trust_id'] ?? 0,
            ]
        );
        if ($GetData->wasRecentlyCreated) {
            $GetData->update([
                'password' => bcrypt('12345678'),
            ]);
        }

        return redirect()->back()->with(
            'success',
            $request->purohit_id ? 'Purohit updated successfully!' : 'Purohit added successfully!'
        );
    }




    public function purohitList(Request $request, $id)
    {
        $purohitList = Purohit::where('temple_id', $id)->with('temple')->get();
        return view('all-views.trustees.darshan.purohit.list', compact('purohitList'));
    }


    public function vipdarshanBookingList(Request $request)
    {
        $query = DarshanOrder::with(['purohit', 'Temple', 'Members'])
            ->where('status', 1)
            ->where('is_hidden', 1)
            ->whereHas('Temple', function ($q) {
                $q->where('trust_id', $this->relationId);
            });
        $templeIds = $query->pluck('temple_id')->unique();
        $purohits = Purohit::whereIn('temple_id', $templeIds)->with('temple')->get();

        // Payment Status 
        if ($request->filled('payment_status')) {
            $query->whereHas('members', function ($q) use ($request) {
                $q->where('payment_method', $request->payment_status);
            });
        }
        // Month Status
        if ($request->filled('monthFilter')) {
            $month = (int) $request->month;
            $query->where(function ($q) use ($month) {
                $q->whereMonth('created_at', $month)->orWhereMonth('date', $month);
            });
        }
        // Package Name 
        if ($request->filled('package_name')) {
            $query->where('package_name', $request->package_name);
        }
        // Purohit Name 
        if ($request->filled('purohit_id')) {
            $query->where('purohit_id', $request->purohit_id);
        }
        // Start to End Date Filter (created_at OR date) 
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('created_at', [$request->from_date, $request->to_date])->orWhereBetween('date', [$request->from_date, $request->to_date]);
            });
        } elseif ($request->filled('from_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date)->orWhereDate('date', '>=', $request->from_date);
            });
        } elseif ($request->filled('to_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_date)->orWhereDate('date', '<=', $request->to_date);
            });
        }

        $darshanList = $query->orderBy('id', 'desc')->get();
        $packages = DarshanOrder::select('package_name')->distinct()->get();

        // ====== Summary counts ======
        $totalOrders = $query->count();
        $onlineOrders = (clone $query)->where('payment_method', 'razor_pay')->count();
        $offlineOrders = (clone $query)->where('payment_method', 'Offline')->count();
        $razorPayAmount = (clone $query)->where('payment_method', 'razor_pay')->sum('price');
        $offlineAmount  = (clone $query)->where('payment_method', 'Offline')->sum('price');
        $paltformFeeAmount  = (clone $query)->sum('platform_fee');
        $recepintAmount  = (clone $query)->sum('receipt_price');
        return view('all-views.trustees.darshan_dt.darshan-booking-list', compact(
            'darshanList',
            'packages',
            'purohits',
            'totalOrders',
            'onlineOrders',
            'offlineOrders',
            'razorPayAmount',
            'offlineAmount',
            'paltformFeeAmount',
            'recepintAmount'
        ));
    }

    public function fetchOrders(Request $request)
    {
        $offset = (int) $request->get('offset', 0);
        $orders = DarshanOrder::with(['userData', 'Temple', 'purohit', 'Members'])
            ->where('is_hidden', 0)
            ->whereHas('Temple', function ($q2) {
                $q2->where('trust_id', $this->relationId);
            })
            ->latest()
            ->skip($offset)
            ->take(5)
            ->get();

        return response()->json($orders);
    }

    public function hideOrder(Request $request, $order_id)
    {
        $order = DarshanOrder::findOrFail($order_id);
        $order->is_hidden = 1;
        $order->payment_mode = "complete";
        $order->save();
        return response()->json(['success' => true]);
    }
    public function updatePurohit(Request $request)
    {
        $order = DarshanOrder::find($request->id);
        if (!$order) {
            return response()->json(['success' => false]);
        }
        $order->purohit_id = $request->purohit_id;
        $order->save();

        // -------- TrustPanditTransection Update/Create ----------
        $transection = TrustPanditTransection::where('order_id', $order->order_id)->first();
        if ($order->payment_method == 'razor_pay') {
            $paymentMethod = 'online';
            $paymentStatus = 'pending';
        } else {
            $paymentMethod = 'cash';
            $paymentStatus = 'complete';
        }
        if ($order->purohit_id) {
            if ($transection) {
                $transection->pandit_id = $order->purohit_id;
                $transection->payment_method = $paymentMethod;
                $transection->payment_status = $paymentStatus;
                $transection->save();
            } else {
                TrustPanditTransection::create([
                    'order_id'       => $order->order_id,
                    'temple_id'      => $order->temple_id,
                    'trust_id'       => $order->temple->trust_id ?? null,
                    'pandit_id'      => $order->purohit_id,
                    'package_id'     => $order->package_id ?? null,
                    'package_price'  => $order->package_price ?? 0,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                ]);
            }
        } else {
            if ($transection) {
                $transection->pandit_id = null;
                $transection->save();
            }
        }

        $purohitName = $order->purohit?->name ?? 'N/A';
        return response()->json([
            'success' => true,
            'purohit_name' => $purohitName
        ]);
    }

    public function paymentCheckStatus(Request $request)
    {

        if (str_starts_with($request->id, "qr_")) {
            $get_Razorpay = \App\Models\Setting::where(['key_name' => 'razor_pay'])->first();
            $RAZORPAY_KEY_ID = '';
            $RAZORPAY_KEY_SECRET = '';
            $RAZORPAY_ACCOUNT_NUMBER = '';
            if ($get_Razorpay['mode'] == 'live') {
                $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
                $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
                $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['live_values']['account_number'] ?? '';
            } else {
                $RAZORPAY_KEY_ID = $get_Razorpay['test_values']['api_key'];
                $RAZORPAY_KEY_SECRET = $get_Razorpay['test_values']['api_secret'];
                $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['test_values']['account_number'] ?? '';
            }
            $url = "https://api.razorpay.com/v1/payments/qr_codes/$request->id/payments";
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => $RAZORPAY_KEY_ID . ":" . $RAZORPAY_KEY_SECRET,
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC
            ]);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                return response()->json([
                    'error' => curl_error($ch)
                ], 500);
            }
            curl_close($ch);
            $data = json_decode($response, true);
            $checkpayment = PaymentRequest::where("transaction_id", $request->id)->first();
            if (!$checkpayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ]);
            }
            if ($data && (($data['entity'] ?? "") == "collection") && (($data['count'] ?? "") > 0) && (($data['items'][0]['status'] ?? "") == 'captured')) {
                $checkpayment2 = PaymentRequest::where("transaction_id", $request->id)->first();
                $checkpayment2->transaction_id  = ($data['items'][0]['id'] ?? "");
                $checkpayment2->payment_method = ($data['items'][0]['method'] ?? "");
                $checkpayment2->is_paid = 1;
                $checkpayment2->save();
            }
            $checkpayment = PaymentRequest::where("id", $checkpayment->id)->first();
            $additionalData = json_decode($checkpayment->additional_data ?? "[]", true);
            $getPujaOLd = TempleOrderDetails::where('order_id', ($additionalData['order_id'] ?? null))->first();
            if ($getPujaOLd && $checkpayment['is_paid'] == 1 && empty($checkpayment['expires'])) {
                //     if ($getPujaOLd->purohit_id) {
                //         Purohit::where('id', $getPujaOLd->purohit_id)->update(['withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . $getPujaOLd->base_price)]);
                //     }
                if($getPujaOLd['type'] == 'puja'){
                    DonateTrust::where('id', $getPujaOLd->trust_id)->update(['purohit_collected_amount' => \Illuminate\Support\Facades\DB::raw('purohit_collected_amount + ' . ($getPujaOLd->base_price ?? 0))]);
                }else{
                    DonateTrust::where('id', $getPujaOLd->trust_id)->update(['trust_total_amount' => \Illuminate\Support\Facades\DB::raw('trust_total_amount + ' . (($getPujaOLd->receipt_fee ?? 0) + ($getPujaOLd->base_price ?? 0)))]);
                }
                PaymentRequest::where('id', $checkpayment->id)->update(['expires' => date('Y-m-d h:i:s')]);
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $checkpayment->transaction_id,
                    'payment_amount' => $checkpayment->payment_amount,
                    'order_id' => $additionalData['order_id'] ?? null,
                    'payment_mode' => $checkpayment->payment_mode,
                ],
                'is_paid' => $checkpayment->is_paid,
                'purohit_id' => $getPujaOLd['purohit_id'] ?? 0,
                'purohit_name' => (Purohit::where('id', ($getPujaOLd['purohit_id'] ?? 0))->first()['name'] ?? ""),
            ]);
        } else {
            $checkpayment = PaymentRequest::find($request->id);
            if (!$checkpayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ]);
            }

            $additionalData = json_decode($checkpayment->additional_data ?? "[]", true);
            $getPujaOLd = TempleOrderDetails::where('order_id', ($additionalData['order_id'] ?? null))->where('type', '!=', 'puja')->first();
            if ($getPujaOLd && $checkpayment['is_paid'] == 1 && empty($checkpayment['expires'])) {
                // if ($getPujaOLd->purohit_id) {
                //     Purohit::where('id', $getPujaOLd->purohit_id)->update(['withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . $getPujaOLd->base_price)]);
                // }
                DonateTrust::where('id', $getPujaOLd->trust_id)->update(['trust_total_amount' => \Illuminate\Support\Facades\DB::raw('trust_total_amount + ' . ($getPujaOLd->receipt_fee ?? 0) + ($getPujaOLd->base_price ?? 0))]);
                PaymentRequest::where('id', $request->id)->update(['expires' => date('Y-m-d h:i:s')]);
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $checkpayment->transaction_id,
                    'payment_amount' => $checkpayment->payment_amount,
                    'order_id' => $additionalData['order_id'] ?? null,
                    'payment_mode' => $checkpayment->payment_mode,
                ],
                'is_paid' => $checkpayment->is_paid,
                'purohit_id' => ($getPujaOLd['purohit_id'] ?? 0),
                'purohit_name' => (Purohit::where('id', ($getPujaOLd['purohit_id'] ?? 0))->first()['name'] ?? ""),
            ]);
        }
    }

    public function purohitTransaction()
    {
        $transactions = TrustPanditTransection::with(['temple', 'purohit', 'package', 'templeOrder'])->whereHas('temple', function ($q) {
            $q->where('trust_id', $this->relationId);
        })->get();
        $onlineTotal = $transactions->where('payment_method', 'online')->sum('package_price');
        $onlineCount = $transactions->where('payment_method', 'online')->count();

        $cashTotal = $transactions->where('payment_method', 'cash')->sum('package_price');
        $cashCount = $transactions->where('payment_method', 'cash')->count();

        $pendingCount = $transactions->where('payment_status', 'pending')->count();
        $completeCount = $transactions->where('payment_status', 'complete')->count();
        return view('all-views.trustees.purohit.purohit-transaction-list', compact(
            'transactions',
            'onlineTotal',
            'onlineCount',
            'cashTotal',
            'cashCount',
            'pendingCount',
            'completeCount'
        ));
    }
    public function purohitTransactionHistory()
    {
        $transactions = TrustPanditTransection::with(['temple', 'purohit', 'package', 'templeOrder'])->whereHas('temple', function ($q) {
            $q->where('trust_id', $this->relationId);
        })->get();
        $onlineTotal = $transactions->where('payment_method', 'online')->sum('package_price');
        $onlineCount = $transactions->where('payment_method', 'online')->count();

        $cashTotal = $transactions->where('payment_method', 'cash')->sum('package_price');
        $cashCount = $transactions->where('payment_method', 'cash')->count();

        $pendingCount = $transactions->where('payment_status', 'pending')->count();
        $completeCount = $transactions->where('payment_status', 'complete')->count();
        return view('all-views.trustees.purohit.purohit-transaction-history', compact(
            'transactions',
            'onlineTotal',
            'onlineCount',
            'cashTotal',
            'cashCount',
            'pendingCount',
            'completeCount'
        ));
    }
    // public function purohitTransactionHistory()
    // {
    //     return view('all-views.trustees.purohit.purohit-transaction-history');
    // }

    public function lead_list_show(Request $request)
    {
        $LeadList = TempleLeadMaster::with([
            'temple',
            'user',
            'details.package',
            'details.purohit'
        ])
            ->where('status', 0)->whereHas('temple', function ($q) {
                $q->where('trust_id', $this->relationId);
            })->get();

        return view('all-views.trustees.temple.lead.list', compact('LeadList'));
    }
    public function order_list_show(Request $request)
    {
        $OrderList = TempleOrderMaster::with(['temple', 'user', 'details.package', 'details.purohit', 'upgradeHistory'])
            ->where('status', 1)
            ->whereHas('temple', function ($q) {
                $q->where('trust_id', $this->relationId);
            })
            ->when($request->payment_status, function ($q) use ($request) {
                $q->where('payment_mode', $request->payment_status);
            })
            ->when($request->booking_status, function ($q) use ($request) {
                $q->where('booking_status', $request->booking_status);
            })
            ->when($request->purohit_id, function ($q) use ($request) {
                $q->whereHas('details', function ($d) use ($request) {
                    $d->where('purohit_id', $request->purohit_id);
                });
            })
            ->whereHas('details', function ($q1) {
                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ""))->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                    $q->where('type', 'puja');
                })->when((auth('purohit')->check()), function ($q) {
                    $q->where('purohit_id', auth('purohit')->user()->id);
                    $q->where('type', 'puja');
                });
            })
            ->orderBy('id', 'desc')->get();

        // $templeIds = $OrderList->pluck('temple')->pluck('id')->unique();
        $trustIds  = $OrderList->pluck('temple')->pluck('trust_id')->unique();
        $templeIds = \App\Models\Temple::where('trust_id', $this->relationId)->pluck('id')->unique();

        $purohits = Purohit::whereIn('temple_id', $templeIds)->where('status', 1)->orderBy('name', 'asc')
            ->get(['id', 'name']);
        $packagesdata = TempleServicePrice::whereIn('temple_id', $templeIds)->where('trust_id', $this->relationId)
            ->where('status', 1)->whereHas('serviceget', function ($q2) {
                $q2->where('name', 'puja');
            })->orderBy('base_price', 'asc')->get();
        return view(
            'all-views.trustees.temple.order.list',
            compact('OrderList', 'purohits', 'packagesdata')
        );
    }

    public function OrderListShowFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('search_by_name', '');
        $payment_mode = $request->input('payment_mode', '');
        $payment_status = $request->input('payment_status', '');
        $temple_name = $request->input('temple_name', '');
        $end_date = $request->input('end_date', '');
        $start_date = $request->input('start_date', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $print_status = $request->input('print_status', 0);
        $purohit_id = $request->input('purohit_id', '');

        $columnName = $request->input("columns.$orderColumnIndex.data");
        $query = TempleOrderMaster::with(['temple', 'user', 'details', 'details.package', 'details.purohit', 'upgradeHistory']);
        $query->when(!empty($payment_mode), function ($query) use ($payment_mode) {
            return $query->where(['payment_mode' => $payment_mode]);
        })->when(!empty($payment_status), function ($query) use ($payment_status) {
            return $query->where(['booking_status' => $payment_status]);
        })->when(!empty($temple_name), function ($query) use ($temple_name) {
            $query->whereHas('temple', function ($q) use ($temple_name) {
                $q->where('id', $temple_name);
            });
        })->whereHas('details', function ($q1) use ($print_status, $request) {
            $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                $q->where('type', 'puja');
            })->when((auth('purohit')->check()), function ($q) {
                $q->where('purohit_id', auth('purohit')->user()->id);
                $q->where('type', 'puja');
            })->when(($request->purohit_id), function ($query) use ($request) {
                $query->where('purohit_id', $request->purohit_id);
            });
        })
            ->where('trust_id', $this->relationId)
            ->whereHas('details', function ($q) use ($start_date, $end_date) {
                if ($start_date && empty($end_date)) {
                    $q->where('booking_date', date('Y-m-d', strtotime($start_date)));
                } elseif ($start_date && $end_date) {
                    $q->whereBetween('booking_date', [$start_date, $end_date]);
                }
            });

        $query->where('status', 1);
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where(function ($querys) use ($searchValue) {
                    $querys->where('order_id', 'like', "%$searchValue%")
                        ->orWhereHas('temple', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        })
                        ->orWhereHas('user', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        });
                });
            });
        }

        $recordsTotal = TempleOrderMaster::with(['temple', 'user', 'details', 'details.package', 'details.purohit', 'upgradeHistory'])->when(!empty($payment_mode), function ($query) use ($payment_mode) {
            return $query->where(['payment_mode' => $payment_mode]);
        })->when(!empty($payment_status), function ($query) use ($payment_status) {
            return $query->where(['booking_status' => $payment_status]);
        })->where('trust_id', $this->relationId)
            ->when(!empty($temple_name), function ($query) use ($temple_name) {
                $query->whereHas('temple', function ($q) use ($temple_name) {
                    $q->where('id', $temple_name);
                });
            })
            ->whereHas('details', function ($q1) use ($print_status) {
                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ""))->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                    $q->where('type', 'puja');
                })->when((auth('purohit')->check()), function ($q) {
                    $q->where('purohit_id', auth('purohit')->user()->id);
                    $q->where('type', 'puja');
                })->when(($print_status == 1), function ($query) {
                    return $query->where(['print_status' => 0]);
                });
            })
            ->where('trust_id', $this->relationId)->where('status', 1)
            ->whereHas('details', function ($q) use ($start_date, $end_date) {
                if ($start_date && empty($end_date)) {
                    $q->where('booking_date',  date('Y-m-d', strtotime($start_date)));
                } elseif ($start_date && $end_date) {
                    $q->whereBetween('booking_date', [$start_date, $end_date]);
                }
            })->count();
        $query->when(!empty($payment_status), function ($query) use ($payment_status) {
            return $query->where(['booking_status' => $payment_status]);
        })->where('trust_id', $this->relationId);
        $recordsFiltered = (clone $query)->count();
        $data = (clone $query)->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $formattedData = $data->map(function ($item, $key) use ($start) {
            $OrderId = '<span class="font-weight-bolder">' . $item['order_id'] . '</span><br>';
            $OrderId .= '<span style="display: ruby-text;">' . date('d M,Y h:i A', strtotime($item['created_at'])) . '</span><br>';
            if ($item->booking_status == 'pending') {
                $OrderId .=   '<span class="order-status-text badge badge-danger">Pending</span>';
            } elseif ($item->booking_status == 'confirmed') {
                $OrderId .=  '<span class="order-status-text badge badge-success">Confirmed</span>';
            } elseif ($item->booking_status == 'cancelled') {
                $OrderId .=  '<span class="badge badge-warning">Cancelled</span>';
            }

            $OrderId .=  '<br>';
            if ($item->upgradeHistory->isNotEmpty()) {
                foreach ($item->upgradeHistory as $uh) {
                    $OrderId .= '<div class="p-2 mb-2 border rounded bg-light">
                                                <strong>Puja Upgrade:</strong>' . optional($item->details->where('type', 'puja')->first()->package)->varient_name . '<br>
                                                <strong>Old Amount:</strong> ₹' . $uh->old_amount . '
                                                <br>
                                                <strong>New Amount:</strong> ₹' . $uh->new_amount . '
                                                <br>
                                                <strong>Difference:</strong> ₹' . $uh->new_amount - $uh->old_amount . '
                                                <br>
                                                <small class="text-muted">Upgraded on: ' . $uh->upgraded_at . '</small>
                                            </div>';
                }
            } else {
                $OrderId .=  '<span class="badge badge-secondary"></span>';
            }

            $purohit_name = '-';
            if ($item->details && $item->details->count() > 0) {
                $purohitName = $item->details->first()->purohit->name ?? '-';
                $purohit_name = $purohitName;
            }

            $service_list = '';
            $purohit_id = '';
            $employe_id = '';
            $platform_puja = ((($item['transaction_id'] ?? "") == 'cash') ? 'cash' : 'online');
            $print_status = 1;
            $pujacheck = 0;
            $TotalPanditFee = 0;
            $TotalGst = 0;
            $TotalPlatformFee = 0;
            $TotalTrustFees = 0;
            $first_customerGet = '';
            if ($item['details'] && count($item['details']) > 0) {
                foreach ($item['details'] as $va) {
                    $service_list .= '<span>' . ucwords($va['type']) . '</span><br>';
                    if (strtolower($va['type']) == 'puja') {
                        $purohit_id = $va['purohit_id'];
                        $employe_id = $va['emp_id'];
                        $print_status = $va['print_status'];
                        $pujacheck = 1;
                    }
                    $TotalPanditFee += (int)$va['base_price'];
                    $TotalGst +=  (int)$va['gst'];
                    $TotalPlatformFee +=  (int)$va['platform_fee'];
                    $TotalTrustFees +=  (int)$va['receipt_fee'];
                    if (empty($first_customerGet)) {
                        $first_customerGet = (json_decode($va['customers'] ?? "[]", true)[0]['name'] ?? "");
                    }
                }
            }

            if (($item['platform'] ?? "") == 'qr') {
                $messageText = 'info';
            } elseif (($item['platform'] ?? "") == 'counter' || ($item['platform'] ?? "") == 'purohit') {
                $messageText = 'warning';
            } elseif (($item['platform'] ?? "") == 'web') {
                $messageText = 'primary';
            } elseif (($item['platform'] ?? "") == 'app') {
                $messageText = 'success';
            } else {
                $messageText = 'secondary';
            }

            $options = '<div class="d-flex justify-content-center gap-2 order-append-child">
                                                <button type="button" class="btn btn-success btn-sm show-order-details-now" data-toggle="modal" data-orderid="' . $item->order_id . '"
                                                    data-target="#leadDetailsModal' . $item->id . '" title="' . translate('Order Check') . '" data-toggle="tooltip" data-placement="left">
                                                    <i class="tio tio-info"></i>
                                                </button>';
            if ((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) || auth('purohit')->check()) {
            } else {
                $options .= ' <button type="button" class="btn btn-warning btn-sm " data-toggle="modal"
                                                    data-target="#puhrohitModal" data-id="' . $item->order_id . '" title="' . translate('Change the purohit Ji only for Puja Ticket') . '" data-toggle="tooltip" data-placement="top">
                                                    <i class="tio tio-user"></i>
                                                </button>';
            }

            if (strtolower($item->payment_mode) == 'cash' && $item->booking_status == 'pending') {
                $options .= '<button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                    data-target="#cashConfirmModal" data-id="' . $item->order_id . '" title="' . translate('Payment Confirm By Cash') . '" data-toggle="tooltip" data-placement="left">
                                                    <i class="tio tio-checkmark-circle"></i>
                                                </button>';
            }
            if ($item->is_upgraded == 0 && $pujacheck == 1) {
                $options .= '<button type="button" class="btn btn-info btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#upgradePackage"
                                                    data-id="' . $item->order_id . '"
                                                    title="' . translate('Change the purohit Ji only for Puja Ticket') . '"
                                                    data-toggle="tooltip"
                                                    data-placement="top">
                                                    <i class="tio tio-new-message"></i>
                                                </button>';
            }

            $options .= ' <div>';


            return [
                'id' => $start + $key + 1,
                'order_id' => $OrderId,
                'temple_name' => "<span class='single-line-show'>" . ($item['temple']['name'] ?? "") . "</span><br><span class='font-weight-bolder single-line-show'>" . $purohit_name . "</span>",
                'yajman_name' => "<span class='single-line-show'>" . ($item['user']['name'] ?? ($first_customerGet)) . " (" . ($item['total_people_count'] ?? 0) . ")</span>",
                'pandit_amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalPanditFee ?? 0), currencyCode: getCurrencyCode()),
                'trust_amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalTrustFees ?? 0), currencyCode: getCurrencyCode()),
                'platform_fee' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalPlatformFee ?? 0), currencyCode: getCurrencyCode()),
                'gst' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalGst ?? 0), currencyCode: getCurrencyCode()),
                'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['total_amount'] ?? 0), currencyCode: getCurrencyCode()),
                'platform' => '<span class="badge badge-' . $messageText . ' text-white">' . ucwords($item->platform ?? "") . '</span>',
                'payment_mode' =>  "<span class='badge badge-" . (($item->payment_mode == 'cash') ? 'info' : (($item->payment_mode == 'free') ? 'secondary' : 'success')) . " text-white'>" . ucfirst($item['payment_mode']) . "</span>",
                'create_by' => date('d M,Y h:i A'),
                'service_name' => $service_list,
                'action' => $options,
                'order_ids' => $item->order_id,
                'order_status' => $item->booking_status,
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
            'footerData' => [
                'totalOrders' => $data->count(),
            ]
        ]);
    }

    public function recepit_index(Request $request, $mode = 'all')
    {
        $query = TempleOrderMaster::with(['temple', 'user', 'details.package'])
            ->where('status', 1)
            ->whereHas('temple', function ($q) {
                $q->where('trust_id', $this->relationId);
            });
        $query->whereHas('details', function ($q1) {
            $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                $q->where('type', 'puja');
            })->when((auth('purohit')->check()), function ($q) {
                $q->where('purohit_id', auth('purohit')->user()->id);
                $q->where('type', 'puja');
            });
        });
        if ($mode === 'cash') {
            $query->where('payment_mode', 'cash');
        } elseif ($mode === 'online') {
            $query->where('payment_mode', 'online');
        }

        $OrderList = $query->get();
        $templeIds = $OrderList->pluck('temple.id')->unique();

        $purohits = Purohit::where('relation_id', $this->relationId)->where('status', 1)->orderBy('name', 'asc')->get(['id', 'name']);
        return view('all-views.trustees.temple.recepit.add-new', compact('OrderList', 'mode', 'purohits'));
    }

    public function recepitQrScanners(Request $request)
    {
        $order = collect();
        if (($request["type"] ?? "") == "all-order" && $request['id']) {
            $order = TempleOrderMaster::with(['temple', 'user', 'details.package'])
                ->where('id', $request['id'])
                ->first();
        } elseif (($request["type"] ?? "") == 'single-order' && $request['id']) {
            $order = TempleOrderMaster::with(['temple', 'user', 'details' => function ($query) use ($request) {
                $query->where('id', $request['id'])->with('package');
            }])
                ->whereHas('details', function ($query) use ($request) {
                    $query->where('id', $request['id']);
                })
                ->first();
        } elseif (($request["type"] ?? "") == "puja-slip" && $request['id']) {
            $order = TempleOrderMaster::with(['temple', 'user', 'details' => function ($query) use ($request) {
                $query->where('id', $request['id'])->with('package');
            }])
                ->whereHas('details', function ($query) use ($request) {
                    $query->where('id', $request['id']);
                })
                ->first();
        }
        return view('all-views.trustees.temple.qr-code-scanner.index', compact('order'));
    }

    public function recepitQrverifyUpdateStatus(Request $request)
    {
        $users = $request->input('user');
        $users = json_decode($users ?? "[]", true);
        if (empty($users)) {
            return response()->json([
                'success' => false,
                'message' => 'No users selected for verification'
            ]);
        }
        foreach ($users as $userData) {
            $detailId = $userData['order_id'] ?? null;
            $userId = $userData['user_id'] ?? null;
            if (!$detailId) {
                continue;
            }
            $orderDetail = TempleOrderDetails::find($detailId);
            if ($orderDetail) {
                $customers = json_decode($orderDetail->customers, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($customers)) {
                    $customerIndex = $userId;
                    if (isset($customers[$customerIndex])) {
                        if (($customers[$customerIndex]['verify_status'] ?? "0") == '0') {
                            $customers[$customerIndex]['verify_status'] = 1;
                            $customers[$customerIndex]['verify_date'] = date('d-m-Y h:i A');
                            if (auth('trust')->check()) {
                                $seller = auth('trust')->id();
                                $sellertype = 'vendor';
                            } elseif (auth('trust_employee')->check()) {
                                $seller = auth('trust_employee')->id();
                                $sellertype = 'employee';
                            } elseif (auth('purohit')->check()) {
                                $seller = auth('purohit')->id();
                                $sellertype = 'purohit';
                            }
                            $customers[$customerIndex]['verify_userid'] = $seller;
                            $customers[$customerIndex]['verify_usertype'] = $sellertype;
                            $orderDetail->customers = json_encode($customers);
                            $orderDetail->save();
                        } elseif (strtolower($orderDetail['type'] ?? "") == 'locker' && ($customers[$customerIndex]['verify_status'] ?? "0") == '1') {
                            $customers[$customerIndex]['verifyend_status'] = 1;
                            $customers[$customerIndex]['end_date'] = date('d-m-Y h:i A');
                            if (auth('trust')->check()) {
                                $seller = auth('trust')->id();
                                $sellertype = 'vendor';
                            } elseif (auth('trust_employee')->check()) {
                                $seller = auth('trust_employee')->id();
                                $sellertype = 'employee';
                            } elseif (auth('purohit')->check()) {
                                $seller = auth('purohit')->id();
                                $sellertype = 'purohit';
                            }
                            $customers[$customerIndex]['verifyend_userid'] = $seller;
                            $customers[$customerIndex]['verifyend_usertype'] = $sellertype;
                            $orderDetail->customers = json_encode($customers);
                            $orderDetail->save();
                        }
                    }
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Verification Successfully'
        ]);
    }

    public function ServiceBookingUsersList()
    {
        return view('all-views.trustees.temple.order.user-order-list');
    }

    public function ServiceBookingUsersListFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('search_by_name', '');
        $payment_mode = $request->input('payment_mode', '');
        $end_date = $request->input('end_date', '');
        $start_date = $request->input('start_date', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columnName = $request->input("columns.$orderColumnIndex.data");
        $query = TempleOrderMaster::with(['temple', 'user', 'details', 'details.package']);
        $query->when(!empty($payment_mode), function ($query) use ($payment_mode) {
            return $query->where(['payment_mode' => $payment_mode]);
        })
            ->where('payment_status', 1)->where('trust_id', $this->relationId)
            ->whereHas('details', function ($q) use ($start_date, $end_date) {
                if ($start_date && empty($end_date)) {
                    $q->where('booking_date', date('Y-m-d', strtotime($start_date)));
                } elseif ($start_date && $end_date) {
                    $q->whereBetween('booking_date', [$start_date, $end_date]);
                }
                $q->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')), function ($qr) {
                    $qr->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                    $qr->where('type', 'puja');
                })->when((auth('purohit')->check()), function ($qr) {
                    $qr->where('purohit_id', auth('purohit')->user()->id);
                    $qr->where('type', 'puja');
                });
            });

        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where(function ($querys) use ($searchValue) {
                    $querys->where('order_id', 'like', "%$searchValue%")
                        ->orWhereHas('temple', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        })
                        ->orWhereHas('user', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        });
                });
            });
        }
        $recordsTotal = TempleOrderMaster::when(!empty($payment_mode), function ($query) use ($payment_mode) {
            return $query->where(['payment_mode' => $payment_mode]);
        })->where('payment_status', 1)->where('trust_id', $this->relationId)
            ->whereHas('details', function ($q) use ($start_date, $end_date) {
                if ($start_date && empty($end_date)) {
                    $q->where('booking_date',  date('Y-m-d', strtotime($start_date)));
                } elseif ($start_date && $end_date) {
                    $q->whereBetween('booking_date', [$start_date, $end_date]);
                }
                $q->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')), function ($qr) {
                    $qr->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                    $qr->where('type', 'puja');
                })->when((auth('purohit')->check()), function ($qr) {
                    $qr->where('purohit_id', auth('purohit')->user()->id);
                    $qr->where('type', 'puja');
                });
            })->count();
        $query->where('payment_status', 1)->where('trust_id', $this->relationId);
        $recordsFiltered = (clone $query)->count();
        $footterCreate = (clone $query)->get();
        $data = (clone $query)->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $totalAmount = 0;
        $totalPanditAmount = 0;
        $totalTrustAmount = 0;
        $totalGstAmount = 0;
        $totalPlatformAmount = 0;
        $totalVerifiedCustomers = 0;
        $serviceCounts = [];
        $footterCreate->map(function ($item, $key) use (&$totalAmount, &$totalPanditAmount, &$totalTrustAmount, &$totalGstAmount, &$totalPlatformAmount, &$totalVerifiedCustomers, &$serviceCounts) {
            $orderVerifiedCustomers = 0;
            $totalPendingCustomers = 0;
            $totalAmount += (float)($item['total_amount'] ?? 0);

            if ($item['details'] && count($item['details']) > 0) {
                foreach ($item['details'] as $va) {
                    if (strtolower($va['type']) == 'puja') {
                        $totalPanditAmount += (float)($va['base_price'] ?? 0);
                    } else {
                        $totalTrustAmount += (float)($va['base_price'] ?? 0);
                    }
                    $totalTrustAmount += (float)($va['receipt_fee'] ?? 0);
                    $totalGstAmount += (float)($va['gst'] ?? 0);
                    $totalPlatformAmount += (float)($va['platform_fee'] ?? 0);

                    $getcustomers = json_decode($va['customers'] ?? "[]", true);
                    $serviceType = ucwords($va['type'] ?? 'Unknown');
                    if (!isset($serviceCounts[$serviceType])) {
                        $serviceCounts[$serviceType] = [
                            'total' => 0,
                            "verify" => 0,
                            "notverify" => 0,
                        ];
                    }
                    foreach ($getcustomers as $value) {
                        $serviceCounts[$serviceType]['total']++;

                        if (($value['verify_status'] ?? 0) == '1') {
                            $serviceCounts[$serviceType]['verify']++;
                            $totalVerifiedCustomers++;
                        } else {
                            $serviceCounts[$serviceType]['notverify']++;
                        }
                    }
                }
            }
        });
        $formattedData = $data->map(function ($item, $key) use ($start) {
            $OrderId = '<span>' . $item['order_id'] . '</span>';
            if ($item->booking_status == 'pending') {
                $OrderId .=   '<span class="order-status-text badge badge-warning">Pending</span>';
            } elseif ($item->booking_status == 'confirmed') {
                $OrderId .=  '<span class="order-status-text badge badge-success">Confirmed</span>';
            } elseif ($item->booking_status == 'cancelled') {
                $OrderId .=  '<span class="badge badge-danger">Cancelled</span>';
            }

            $purohit_name = '-';
            if ($item->details && $item->details->count() > 0) {
                $purohitName = $item->details->first()->purohit->name ?? '-';
                $purohit_name = $purohitName;
            }

            $service_list = '';
            $orderVerifiedCustomers = 0;
            $TotalPanditFee = 0;
            $TotalGst =  0;
            $TotalPlatformFee = 0;
            $TotalTrustFees =  0;
            $first_customerGet = '';
            if ($item['details'] && count($item['details']) > 0) {
                foreach ($item['details'] as $va) {

                    if (strtolower($va['type']) == 'puja') {
                        $TotalPanditFee += (int)$va['base_price'];
                    } else {
                        $TotalTrustFees += (int)$va['base_price'];
                    }
                    $TotalTrustFees +=  (int)$va['receipt_fee'];
                    $TotalGst +=  (int)$va['gst'];
                    $TotalPlatformFee +=  (int)$va['platform_fee'];

                    $getcustomers = json_decode($va['customers'] ?? "[]", true);
                    $service_list .=  '<table class="table">';
                    foreach ($getcustomers as $keys => $value) {
                        $service_list .=  '<tr>';
                        if ($keys == 0) {
                            $service_list .= ' <td rowspan="' . count($getcustomers) . '"><span>' . ucwords($va['type']) . '</span></td>';
                        }
                        $service_list .= '<td><span style="display: ruby-text;margin-top: 3px;" class="text-' . (($value['verify_status'] ?? "" == '1') ? "success" : "danger") . '">' . $value['name'] . '</span></td>';
                        $service_list .= '<td><span style="display: ruby-text;margin-top: 3px;">' . ($value['verify_date'] ?? "") . '</span><br><span style="display: ruby-text;margin-top: 3px;">' . ($value['end_date'] ?? "") . '</span></td>';
                        if (($value['verify_usertype'] ?? "") == 'employee' || ($value['verifyend_usertype'] ?? "") == 'employee') {
                            $vendorName = \App\Models\VendorEmployees::where('id', ($value['verify_userid'] ?? ""))->first()['name'] ?? "";
                            $vendorName_last = \App\Models\VendorEmployees::where('id', ($value['verifyend_userid'] ?? ""))->first()['name'] ?? "";
                        } elseif (($value['verify_usertype'] ?? "") == 'vendor' || ($value['verifyend_usertype'] ?? "") == 'vendor') {
                            $vendorName2 = \App\Models\Seller::where('id', ($value['verify_userid'] ?? ""))->first();
                            $vendorName3 = \App\Models\Seller::where('id', ($value['verifyend_userid'] ?? ""))->first();
                            $vendorName = ($vendorName2['f_name'] ?? "") . " " . ($vendorName2['l_name'] ?? "");
                            $vendorName_last = ($vendorName3['f_name'] ?? "") . " " . ($vendorName3['l_name'] ?? "");
                        }
                        $service_list .= '<td><span style="display: ruby-text;margin-top: 3px;">' . ($vendorName ?? "") . ' (' . ($value['verify_usertype'] ?? "") . ')</span><br><span style="display: ruby-text;margin-top: 3px;">' . ((($value['verifyend_usertype'] ?? "") != "") ?  $vendorName_last . " (" . ($value['verifyend_usertype'] ?? "") . ")" : '') . '</span></td>
                               </tr> ';
                    }
                    $service_list .= '</table><br>';
                    if (empty($first_customerGet)) {
                        $first_customerGet = (json_decode($va['customers'] ?? "[]", true)[0]['name'] ?? "");
                    }
                }
            }

            if (($item['platform'] ?? "") == 'qr') {
                $messageText = 'info';
            } elseif (($item['platform'] ?? "") == 'counter' || ($item['platform'] ?? "") == 'purohit') {
                $messageText = 'warning';
            } elseif (($item['platform'] ?? "") == 'web') {
                $messageText = 'primary';
            } elseif (($item['platform'] ?? "") == 'app') {
                $messageText = 'success';
            } else {
                $messageText = 'secondary';
            }

            return [
                'id' => $start + $key + 1,
                'order_id' => $OrderId,
                'temple_name' => ($item['temple']['name'] ?? "") . "<br><span class='font-weight-bolder'>" . $purohit_name . "</span>",
                'service' =>  ' <button type="button" 
                        class="btn btn-sm btn-outline-primary view-details" 
                        data-id="' . $item->id . '" 
                        data-html="' . e($service_list) . '">
                        <i class="tio tio-invisible"></i>
                    </button>',
                'yajman_name' => ($item['user']['name'] ?? ($first_customerGet)) . " (" . ($item['total_people_count'] ?? 0) . ")",
                'pandit_amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalPanditFee ?? 0), currencyCode: getCurrencyCode()),
                'trust_amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalTrustFees ?? 0), currencyCode: getCurrencyCode()),
                'platform_fee' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalPlatformFee ?? 0), currencyCode: getCurrencyCode()),
                'gst' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalGst ?? 0), currencyCode: getCurrencyCode()),
                'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['total_amount'] ?? 0), currencyCode: getCurrencyCode()),
                'platform' => '<span class="badge badge-' . $messageText . ' text-white">' . ucwords($item->platform ?? "") . '</span>',
                'payment_mode' =>  "<span class='badge badge-" . (($item->payment_mode == 'cash') ? 'info' : (($item->payment_mode == 'free') ? 'secondary' : 'success')) . " text-white'>" . ucfirst($item['payment_mode']) . "</span>",
                'create_by' => date('d M,Y h:i A'),
                'create_by' => date('d M,Y h:i A', strtotime($item['created_at'] ?? "")),
                'order_ids' => $item->order_id,
                'order_status' => $item->booking_status,
            ];
        });

        $serviceSummary = '';
        if (!empty($serviceCounts)) {
            $serviceItems = [];
            foreach ($serviceCounts as $serviceType => $count) {
                $serviceItems[] = "$serviceType: " . $count['total'] . " (Verify : " . $count['verify'] . " , Pending :  " . $count['notverify'] . ") , <br>";
            }
            $serviceSummary = implode('', $serviceItems);
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
            'footerData' => [
                'totalAmount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalAmount), currencyCode: getCurrencyCode()),
                'totalPanditAmount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPanditAmount), currencyCode: getCurrencyCode()),
                'totalTrustAmount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalTrustAmount), currencyCode: getCurrencyCode()),
                'totalGstAmount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalGstAmount), currencyCode: getCurrencyCode()),
                'totalPlatformAmount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPlatformAmount), currencyCode: getCurrencyCode()),
                'verifiedCustomers' => $totalVerifiedCustomers,
                'totalOrders' => $data->count(),
                'serviceSummary' => $serviceSummary
            ]
        ]);
    }
    public function ServiceBookingReceiptListFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('search_by_name', '');
        $payment_mode = $request->input('payment_mode', '');
        $payment_status = $request->input('payment_status', '');
        $temple_name = $request->input('temple_name', '');
        $end_date = $request->input('end_date', '');
        $start_date = $request->input('start_date', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $print_status = $request->input('print_status', 0);

        $columnName = $request->input("columns.$orderColumnIndex.data");
        $query = TempleOrderMaster::with(['temple', 'user', 'details', 'details.package']);
        $query->when(!empty($payment_mode), function ($query) use ($payment_mode) {
            return $query->where(['payment_mode' => $payment_mode]);
        })->when(!empty($payment_status), function ($query) use ($payment_status) {
            return $query->where(['booking_status' => $payment_status]);
        })->when(!empty($temple_name), function ($query) use ($temple_name) {
            $query->whereHas('temple', function ($q) use ($temple_name) {
                $q->where('id', $temple_name);
            });
        })->whereHas('details', function ($q1) use ($print_status) {
            $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                $q->where('type', 'puja');
            })->when((auth('purohit')->check()), function ($q) {
                $q->where('purohit_id', auth('purohit')->user()->id);
                $q->where('type', 'puja');
            })->when(($print_status == 1), function ($query) {
                return $query->where(['print_status' => 0]);
            });
        })
            ->where('trust_id', $this->relationId)
            ->whereHas('details', function ($q) use ($start_date, $end_date) {
                if ($start_date && empty($end_date)) {
                    $q->where('booking_date', date('Y-m-d', strtotime($start_date)));
                } elseif ($start_date && $end_date) {
                    $q->whereBetween('booking_date', [$start_date, $end_date]);
                }
            });

        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where(function ($querys) use ($searchValue) {
                    $querys->where('order_id', 'like', "%$searchValue%")
                        ->orWhereHas('temple', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        })
                        ->orWhereHas('user', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        });
                });
            });
        }
        $recordsTotal = TempleOrderMaster::when(!empty($payment_mode), function ($query) use ($payment_mode) {
            return $query->where(['payment_mode' => $payment_mode]);
        })->when(!empty($payment_status), function ($query) use ($payment_status) {
            return $query->where(['booking_status' => $payment_status]);
        })->where('trust_id', $this->relationId)
            ->when(!empty($temple_name), function ($query) use ($temple_name) {
                $query->whereHas('temple', function ($q) use ($temple_name) {
                    $q->where('id', $temple_name);
                });
            })
            ->whereHas('details', function ($q1) use ($print_status) {
                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ""))->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                    $q->where('type', 'puja');
                })->when((auth('purohit')->check()), function ($q) {
                    $q->where('purohit_id', auth('purohit')->user()->id);
                    $q->where('type', 'puja');
                })->when(($print_status == 1), function ($query) {
                    return $query->where(['print_status' => 0]);
                });
            })
            ->whereHas('details', function ($q) use ($start_date, $end_date) {
                if ($start_date && empty($end_date)) {
                    $q->where('booking_date',  date('Y-m-d', strtotime($start_date)));
                } elseif ($start_date && $end_date) {
                    $q->whereBetween('booking_date', [$start_date, $end_date]);
                }
            })->count();
        $query->when(!empty($payment_status), function ($query) use ($payment_status) {
            return $query->where(['booking_status' => $payment_status]);
        })->where('trust_id', $this->relationId);
        $recordsFiltered = (clone $query)->count();
        $footterCreate = (clone $query)->get();
        $data = (clone $query)->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $totalAmount = 0;
        $totalVerifiedCustomers = 0;
        $serviceCounts = [];
        $footterCreate->map(function ($item, $key) use (&$totalAmount, &$totalVerifiedCustomers, &$serviceCounts) {
            $orderVerifiedCustomers = 0;
            $totalPendingCustomers = 0;
            $totalAmount += (float)($item['total_amount'] ?? 0);
            if ($item['details'] && count($item['details']) > 0) {
                foreach ($item['details'] as $va) {

                    $getcustomers = json_decode($va['customers'] ?? "[]", true);
                    $serviceType = ucwords($va['type'] ?? 'Unknown');
                    if (!isset($serviceCounts[$serviceType])) {
                        $serviceCounts[$serviceType] = [
                            'total' => 0,
                            "verify" => 0,
                            "notverify" => 0,
                        ];
                    }
                    foreach ($getcustomers as $value) {
                        $serviceCounts[$serviceType]['total']++;

                        if (($value['verify_status'] ?? 0) == '1') {
                            $serviceCounts[$serviceType]['verify']++;
                            $totalVerifiedCustomers++;
                        } else {
                            $serviceCounts[$serviceType]['notverify']++;
                        }
                    }
                }
            }
        });
        $formattedData = $data->map(function ($item, $key) use ($start) {
            $OrderId = '<span class="font-weight-bolder">' . $item['order_id'] . '</span><br>';
            $OrderId .= '<span style="display: ruby-text;">' . date('d M,Y h:i A', strtotime($item['created_at'])) . '</span><br>';
            if ($item->booking_status == 'pending') {
                $OrderId .=   '<span class="order-status-text badge badge-danger">Pending</span>';
            } elseif ($item->booking_status == 'confirmed') {
                $OrderId .=  '<span class="order-status-text badge badge-success">Confirmed</span>';
            } elseif ($item->booking_status == 'cancelled') {
                $OrderId .=  '<span class="order-status-text badge badge-warning">Cancelled</span>';
            }

            $purohit_name = '-';
            if ($item->details && $item->details->count() > 0) {
                $purohitName = $item->details->first()->purohit->name ?? '-';
                $purohit_name = $purohitName;
            }

            $service_list = '';
            $purohit_id = '';
            $employe_id = '';
            $platform_puja = ((($item['transaction_id'] ?? "") == 'cash') ? 'cash' : 'online');
            $print_status = 1;
            $TotalPanditFee = 0;
            $TotalGst = 0;
            $TotalPlatformFee = 0;
            $TotalTrustFees = 0;
            $first_customerGet = '';
            if ($item['details'] && count($item['details']) > 0) {
                foreach ($item['details'] as $va) {
                    $service_list .= '<span>' . ucwords($va['type']) . '</span><br>';
                    if (strtolower($va['type']) == 'puja') {
                        $purohit_id = $va['purohit_id'];
                        $employe_id = $va['emp_id'];
                        $print_status = $va['print_status'];
                        $TotalPanditFee += (int)$va['base_price'];
                    } else {
                        $TotalTrustFees +=  (int)$va['base_price'];
                    }
                    $TotalGst +=  (int)$va['gst'];
                    $TotalPlatformFee +=  (int)$va['platform_fee'];
                    $TotalTrustFees +=  (int)$va['receipt_fee'];
                    if (empty($first_customerGet)) {
                        $first_customerGet = (json_decode($va['customers'] ?? "[]", true)[0]['name'] ?? "");
                    }
                }
            }

            if (($item['platform'] ?? "") == 'qr') {
                $messageText = 'info';
            } elseif (($item['platform'] ?? "") == 'counter' || ($item['platform'] ?? "") == 'purohit') {
                $messageText = 'warning';
            } elseif (($item['platform'] ?? "") == 'web') {
                $messageText = 'primary';
            } elseif (($item['platform'] ?? "") == 'app') {
                $messageText = 'success';
            } else {
                $messageText = 'secondary';
            }

            $options = '<div class="d-flex justify-content-center gap-2 order-append-child">
                                            <button type="button" class="btn btn-success btn-sm show-order-details-now" data-orderid="' . $item->order_id . '" data-toggle="modal"
                                                data-target="#leadDetailsModal' . $item->id . '"
                                                title=' . translate('Order Check') . '" data-toggle="tooltip"
                                                data-placement="left">
                                                <i class="tio tio-info"></i>
                                            </button>';
            // $hasPuja = collect($item['details'] ?? [])->contains('type', 'puja');
            // if ($hasPuja) {
            //     $options .= '<button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
            //                                         data-target="#puhrohitModal" data-id="' . $item->order_id . '" data-purohit="' . $purohit_id . '"
            //                                         title="' . translate('Change the purohit Ji only for Puja Ticket') . '"
            //                                         data-toggle="tooltip" data-placement="top">
            //                                         <i class="tio tio-user"></i>
            //                                     </button>';
            // }

            if (strtolower($item->payment_mode) == 'cash' && $item->booking_status != 'confirmed') {
                $options .= '<button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                    data-target="#cashConfirmModal" data-id="' . $item->order_id . '"
                                                    title="' . translate('Payment Confirm By Cash') . '"
                                                    data-toggle="tooltip" data-placement="left">
                                                    <i class="tio tio-checkmark-circle"></i>
                                                </button>';
            }

            if ($item->booking_status == 'confirmed' && (!auth('purohit')->check() && (!auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") != 'Sub Pandit')))) {
                $options .= '<button type="button" class="btn btn-info btn-sm" onclick="printNow(this)" data-id="' . $item->order_id . '" data-purohit="' . $purohit_id . '" data-platform="' . $platform_puja . '" data-purohit_name="' . $purohit_name . '" data-employee="' . $employe_id . '" data-employee_status="' . (($item['platform'] == 'purohit' && $print_status == 0) ? 1 : 0) . '"
                                                    title="' . translate('print') . '"
                                                    data-toggle="tooltip" data-placement="left">
                                                    <i class="tio tio-print"></i>
                                                </button>';
            }
            $options .= '<div>';
            return [
                'id' => $start + $key + 1,
                'order_id' => $OrderId,
                'temple_name' => "<span class='single-line-show'>" . ($item['temple']['name'] ?? "") . "</span><br><span class='font-weight-bolder single-line-show'>" . $purohit_name . "</span>",
                'yajman_name' => "<span class='single-line-show'>" . ($item['user']['name'] ?? ($first_customerGet)) . " (" . ($item['total_people_count'] ?? 0) . ")</span>",
                'pandit_amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalPanditFee ?? 0), currencyCode: getCurrencyCode()),
                'trust_amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalTrustFees ?? 0), currencyCode: getCurrencyCode()),
                'platform_fee' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalPlatformFee ?? 0), currencyCode: getCurrencyCode()),
                'gst' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $TotalGst ?? 0), currencyCode: getCurrencyCode()),
                'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['total_amount'] ?? 0), currencyCode: getCurrencyCode()),
                'platform' => '<span class="badge badge-' . $messageText . ' text-white">' . ucwords($item->platform ?? "") . '</span>',
                'payment_mode' =>  "<span class='badge badge-" . (($item->payment_mode == 'cash') ? 'info' : (($item->payment_mode == 'free') ? 'secondary' : 'success')) . " text-white'>" . ucfirst($item['payment_mode']) . "</span>",
                'create_by' => date('d M,Y h:i A'),
                'service_name' => $service_list,
                'action' => $options,
                'order_ids' => $item->order_id,
                'order_status' => $item->booking_status,
            ];
        });

        $serviceSummary = '';
        if (!empty($serviceCounts)) {
            $serviceItems = [];
            foreach ($serviceCounts as $serviceType => $count) {
                $serviceItems[] = "$serviceType: " . $count['total'] . " (Verify : " . $count['verify'] . " , Pending :  " . $count['notverify'] . ") , <br>";
            }
            $serviceSummary = implode('', $serviceItems);
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
            'footerData' => [
                'totalAmount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalAmount), currencyCode: getCurrencyCode()),
                'verifiedCustomers' => $totalVerifiedCustomers,
                'totalOrders' => $data->count(),
                'serviceSummary' => $serviceSummary
            ]
        ]);
    }

    public function PanditServiceBookingListFilter(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->input('search.value', '') ?? $request->input('search_by_name', '');
        $payment_mode = $request->input('payment_mode', '');
        $payment_status = $request->input('payment_status', '');
        $temple_name = $request->input('temple_name', '');
        $purohit_id = $request->input('purohit_id', '');
        $emp_id = $request->input('emp_id', '');
        $print_status = $request->input('print_status', 'n');
        $end_date = $request->input('end_date', '');
        $start_date = $request->input('start_date', '');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $columnName = $request->input("columns.$orderColumnIndex.data");

        $query = TempleOrderDetails::with(['order', 'temple', 'purohit'])->where('type', 'puja')
            ->whereHas('order', function ($q) use ($payment_mode, $payment_status) {
                $q->when(!empty($payment_mode), function ($query) use ($payment_mode) {
                    return $query->where(['payment_mode' => $payment_mode]);
                })
                    ->when(!empty($payment_status), function ($query) use ($payment_status) {
                        return $query->where(['booking_status' => $payment_status]);
                    });
            })
            ->when(!empty($purohit_id), function ($query) use ($purohit_id) {
                return $query->where(['purohit_id' => $purohit_id]);
            })
            ->when(($print_status == 0 && $print_status != null), function ($query) use ($print_status) {
                return $query->where(['print_status' => $print_status]);
            })
            ->when(($print_status == 1), function ($query) use ($print_status) {
                return $query->where(['print_status' => $print_status]);
            })
            ->when(!empty($emp_id), function ($query) use ($emp_id) {
                return $query->where(['emp_id' => $emp_id]);
            })
            ->when(($start_date || $end_date), function ($q) use ($start_date, $end_date) {
                if ($start_date && empty($end_date)) {
                    $q->where('booking_date', date('Y-m-d', strtotime($start_date)));
                } elseif ($start_date && $end_date) {
                    $q->whereBetween('booking_date', [$start_date, $end_date]);
                }
            })
            ->where('trust_id', $this->relationId);
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where(function ($querys) use ($searchValue) {
                    $querys->where('order_id', 'like', "%$searchValue%")
                        ->orWhere('type_order_id', 'like', "%$searchValue%")
                        ->orWhereHas('temple', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        });
                });
            });
        }


        $recordsTotal = TempleOrderDetails::with(['order', 'temple', 'purohit'])->where('type', 'puja')
            ->whereHas('order', function ($q) use ($payment_mode, $payment_status) {
                $q->when(!empty($payment_mode), function ($query) use ($payment_mode) {
                    return $query->where(['payment_mode' => $payment_mode]);
                })
                    ->when(!empty($payment_status), function ($query) use ($payment_status) {
                        return $query->where(['booking_status' => $payment_status]);
                    });
            })
            ->when(!empty($purohit_id), function ($query) use ($purohit_id) {
                return $query->where(['purohit_id' => $purohit_id]);
            })
            ->when(($print_status == 0 && $print_status != null), function ($query) use ($print_status) {
                return $query->where(['print_status' => $print_status]);
            })
            ->when(($print_status == 1), function ($query) use ($print_status) {
                return $query->where(['print_status' => $print_status]);
            })
            ->when(($start_date || $end_date), function ($q) use ($start_date, $end_date) {
                if ($start_date && empty($end_date)) {
                    $q->where('booking_date', date('Y-m-d', strtotime($start_date)));
                } elseif ($start_date && $end_date) {
                    $q->whereBetween('booking_date', [$start_date, $end_date]);
                }
            })
            ->where('trust_id', $this->relationId)->count();
        $recordsFiltered = (clone $query)->count();
        $data = (clone $query)->orderBy($columnName ?? 'id', $orderDirection ?? 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $totalAmount = 0;
        $totalVerifiedCustomers = 0;
        $serviceCounts = [];

        $formattedData = $data->map(function ($item, $key) use ($start) {
            $OrderId = '<span>' . $item['order_id'] . '</span><br>' . (date('d M,Y h:i A', strtotime($item['created_at']))) . '<br>';
            if ($item->booking_status == 'pending') {
                $OrderId .=   '<span class="order-status-text badge badge-warning">Pending</span>';
            } elseif ($item->booking_status == 'confirmed') {
                $OrderId .=  '<span class="order-status-text badge badge-success">Confirmed</span>';
            } elseif ($item->booking_status == 'cancelled') {
                $OrderId .=  '<span class="badge badge-danger">Cancelled</span>';
            }

            $purohit_name = '-';
            if ($item->purohit) {
                $purohitName = $purohit_name = $item->purohit->name ?? '-';
            }

            $service_list = '';
            $purohit_id = $item->purohit_id;
            $employe_id = ($item->emp_id ?? 0);
            $platform_puja = ((($item['order']['transaction_id'] ?? "") == 'cash') ? 'cash' : 'online');
            if (($item['order']['platform'] ?? "") == 'qr') {
                $messageText = 'info';
            } elseif (($item['order']['platform'] ?? "") == 'counter' || ($item['order']['platform'] ?? "") == 'purohit') {
                $messageText = 'warning';
            } elseif (($item['order']['platform'] ?? "") == 'web') {
                $messageText = 'primary';
            } elseif (($item['order']['platform'] ?? "") == 'app') {
                $messageText = 'success';
            } else {
                $messageText = 'secondary';
            }

            $options = '<div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#leadDetailsModal' . $item->order->id . '"
                                                title=' . translate('Order Check') . '" data-toggle="tooltip"
                                                data-placement="left">
                                                <i class="tio tio-info"></i>
                                            </button>';
            if ($item->booking_status == 'confirmed' && (!auth('purohit')->check() && (!auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") != 'Sub Pandit')))) {
                $options .= '<button type="button" class="btn btn-info btn-sm" onclick="printNow(this)" data-id="' . $item->order_id . '" data-purohit="' . $purohit_id . '" data-platform="' . $platform_puja . '" data-purohit_name="' . $purohit_name . '" data-employee="' . $employe_id . '" data-employee_status="' . ((($item['order']['platform'] ?? "") == 'purohit' && $item['print_status'] == 0) ? 1 : 0) . '"
                                                    title="' . translate('print') . '"
                                                    data-toggle="tooltip" data-placement="left">
                                                    <i class="tio tio-print"></i>
                                                </button>';
            }
            $options .= '<div>';

            $first_customerGet = (json_decode($item->customers ?? "[]", true)[0]['name'] ?? "");

            return [
                'id' => $start + $key + 1,
                'order_id' => $OrderId,
                'temple_name' => ($item['temple']['name'] ?? "") . "<br><span class='font-weight-bolder'>" . $purohit_name . "</span>",
                'yajman_name' => ($item['order']['user']['name'] ?? ($first_customerGet)) . " (" . ($item['order']['total_people_count'] ?? 0) . ")",
                'base_price' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['base_price'] ?? 0), currencyCode: getCurrencyCode()),
                'gst' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['gst'] ?? 0), currencyCode: getCurrencyCode()),
                'platform_fee' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['platform_fee'] ?? 0), currencyCode: getCurrencyCode()),
                'receipt_fee' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['receipt_fee'] ?? 0), currencyCode: getCurrencyCode()),
                'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['final_amount'] ?? 0), currencyCode: getCurrencyCode()),
                'platform' => '<span class="badge badge-' . $messageText . ' text-white">' . ucwords($item->order->platform ?? "") . '</span>',
                'payment_mode' =>  "<span class='badge badge-" . (($item['order']->payment_mode == 'cash') ? 'info' : (($item['order']->payment_mode == 'free') ? 'secondary' : 'success')) . " text-white'>" . ucfirst($item['order']['payment_mode']) . "</span>",
                'create_by' => date('d M,Y h:i A', strtotime($item['created_at'])),
                'action' => $options,
                'checkbox_order_id' => $item['order_id'],
                'checkbox_payment_status' => $item['payment_status'],
                'checkbox_printstatus' => $item['print_status'],
                'employee_id' => $employe_id,
                'employee_status' => ((($item['order']['platform'] ?? "") == 'purohit' && $item['print_status'] == 0) ? 1 : 0),
                'purohit_id' => $purohit_id,
                'order_ids' => $item->order_id,
                'order_status' => $item->booking_status,
            ];
        });

        $serviceSummary = '';
        if (!empty($serviceCounts)) {
            $serviceItems = [];
            foreach ($serviceCounts as $serviceType => $count) {
                $serviceItems[] = "$serviceType: " . $count['total'] . " (Verify : " . $count['verify'] . " , Pending :  " . $count['notverify'] . ") , <br>";
            }
            $serviceSummary = implode('', $serviceItems);
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
            'footerData' => [
                'totalAmount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalAmount), currencyCode: getCurrencyCode()),
                'verifiedCustomers' => $totalVerifiedCustomers,
                'totalOrders' => $data->count(),
                'serviceSummary' => $serviceSummary
            ]
        ]);
    }

    public function OrderLuggagePhoneUpdate(Request $request)
    {
        $getData = \App\Models\TempleOrderDetails::where('id', $request['id'])->first();
        if ($getData) {
            $jsonData = json_decode($getData['locker_items'] ?? "[]", true);
            $customerData = json_decode($getData['customers'] ?? "[]", true);
            if ($request['type'] == 'mobile') {
                $jsonData['mobile'] = $request['value'];
            } elseif ($request['type'] == 'luggage') {
                $jsonData['luggage'] = $request['value'];
            }
            $getData->locker_items = json_encode($jsonData);
            if (($customerData[0]['verify_status'] ?? 0) == 1) {
                return response()->json(['success' => false, "message" => '']);
            } else {
                $getData->save();
            }
            return response()->json(['success' => true, "message" => '']);
        } else {
            return response()->json(['success' => false, "message" => '']);
        }
    }

    public function getOrderDetails(Request $request)
    {
        $order = TempleOrderMaster::with(['temple', 'user', 'details.package'])
            ->where('order_id', $request->order_id)
            ->first();
        if (!$order) {
            return response()->json(['success' => false]);
        }
        $puja_Details = $order->details->filter(function ($detail) {
            return $detail->type === 'puja';
        });
        $html2 = '';
        $folder = storage_path('app/public/qrcodes');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }
        if ($puja_Details->isNotEmpty()) {
            $pujaDetails = $puja_Details->first();
            $qrCode = new \Endroid\QrCode\QrCode(route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => 'puja-slip', 'id' => $pujaDetails['id']]));
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            $folder = storage_path('app/public/qrcodes');
            if (!\Illuminate\Support\Facades\File::exists($folder)) {
                \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
            }
            $filePath = $folder . "/vendor-cash-booking-qrs1.png";
            $result->saveToFile($filePath);
            $webPath = asset("storage/app/public/qrcodes/vendor-cash-booking-qrs1.png") . '?v=' . time();
            $html2 .= view('all-views.trustees.temple.partials.order_details', [
                'order' => $order,
                'detail' => $pujaDetails,
                'qrUrl' => $webPath,
                "puja__status" => 1,
            ])->render();
        }
        $html3 = '';
        if ($order) {
            $pujaDetails = $order->details[0] ?? [];
            $qrCode = new \Endroid\QrCode\QrCode(route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => 'all-order', 'id' => $order->id]));
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            $folder = storage_path('app/public/qrcodes');
            if (!\Illuminate\Support\Facades\File::exists($folder)) {
                \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
            }
            $filePath = $folder . "/vendor-cash-booking-qrnew2.png";
            $result->saveToFile($filePath);
            $webPath = asset("storage/app/public/qrcodes/vendor-cash-booking-qrnew2.png") . '?v=' . time();
            $html3 .= view('all-views.trustees.temple.partials.order_details', [
                'order' => $order,
                'detail' => $pujaDetails,
                'qrUrl' => $webPath,
                "puja__status" => 2,
            ])->render();
        }
        $html = '';
        foreach ($order->details as $in => $detail) {
            // $qrFileName = "qr_{$detail->id}.png";
            // $filePath = $folder . "/" . $qrFileName;
            // $webPath = asset("storage/qrcodes/{$qrFileName}") . '?v=' . time();

            // $builder = Builder::create()
            // ->writer(new PngWriter())
            // ->data(route('trustees-vendor.recepit-management.show', $detail->id))
            // ->encoding(new Encoding('UTF-8'))
            // ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            // ->size(300)
            // ->margin(10)
            // ->logoPath(public_path('assets/front-end/img/logo-png.png'))
            // ->logoResizeToWidth(90)
            // ->build();
            // $builder->saveToFile($filePath);
            $qrCode = new \Endroid\QrCode\QrCode(route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => 'single-order', 'id' => $detail->id]));
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            $folder = storage_path('app/public/qrcodes');
            if (!\Illuminate\Support\Facades\File::exists($folder)) {
                \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
            }
            $filePath = $folder . "/vendor-cash-booking-qr" . $in . ".png";
            $result->saveToFile($filePath);
            $webPath = asset("storage/app/public/qrcodes/vendor-cash-booking-qr" . $in . ".png") . '?v=' . time();
            $html .= view('all-views.trustees.temple.partials.order_details', [
                'order' => $order,
                'detail' => $detail,
                'qrUrl' => $webPath,
                'puja__status' => 0
            ])->render();
        }
        return response()->json(['success' => true, 'html' => $html, 'html2' => $html2, 'html3' => $html3]);
    }

    public function getOrderPujaSlipDetails(Request $request)
    {
        $MultiOrders = $request['order_ids'];
        $orderList = TempleOrderMaster::with(['temple', 'user', 'details.package'])->whereIn('order_id', $MultiOrders)->get();
        if (!$orderList) {
            return response()->json(['success' => false]);
        }
        $html2 = '';
        $folder = storage_path('app/public/qrcodes');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }
        $html3 = '';
        foreach ($orderList as $key => $order) {
            $puja_Details = $order->details->filter(function ($detail) {
                return $detail->type === 'puja';
            });
            if ($puja_Details->isNotEmpty()) {
                $pujaDetails = $puja_Details->first();
                $qrCode = new \Endroid\QrCode\QrCode(route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => 'puja-slip', 'id' => $pujaDetails['id']]));
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $folder = storage_path('app/public/qrcodes');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                }
                $filePath = $folder . "/vendor-cash-booking-qrs1.png";
                $result->saveToFile($filePath);
                $webPath = asset("storage/app/public/qrcodes/vendor-cash-booking-qrs1.png") . '?v=' . time();
                $html3 .= view('all-views.trustees.temple.partials.order_details', [
                    'order' => $order,
                    'detail' => $pujaDetails,
                    'qrUrl' => $webPath,
                    "puja__status" => 1,
                ])->render();
            }
        }

        return response()->json(['success' => true, 'html2' => $html3]);
    }


    public function thermalPrint($order_id)
    {
        $order = TempleOrderMaster::with(['temple', 'user', 'details.package', 'details.timeslot', 'details.purohit'])->where('order_id', $order_id)->first();

        if (!$order) {
            return response()->make('Order not found', 404);
        }

        return view('all-views.trustees.temple.thermal_print', compact('order'));
    }

    public function confirmCashPayment(Request $request)
    {
        if (auth('trust')->check()) {
            $relationEmployees = auth('trust')->user()->relation_id;
            $confirmedBy = auth('trust')->user()->relation_id; //auth('trust')->id();
        } elseif (auth('trust_employee')->check()) {
            $relationEmployees = auth('trust_employee')->user()->relation_id;
            $confirmedBy = auth('trust_employee')->user()->relation_id; //auth('trust_employee')->id();
        } elseif (auth('purohit')->check()) {
            $relationEmployees = auth('purohit')->user()->relation_id;
            $confirmedBy = auth('purohit')->user()->relation_id;
        } else {
            $relationEmployees = null;
            $confirmedBy = null;
        }
        $order = TempleOrderMaster::where('order_id', $request->order_id)->where('status', 1)->first();
        $getPujaOLd = TempleOrderDetails::where('order_id', ($request->order_id ?? null))->where('type', 'puja')->first();
        if ($getPujaOLd) {
            DonateTrust::where('id', $getPujaOLd->trust_id)->update([
                'trust_total_withdrawal' => \Illuminate\Support\Facades\DB::raw('trust_total_withdrawal + ' . ($getPujaOLd->receipt_fee)),
                'purohit_collected_amount' => \Illuminate\Support\Facades\DB::raw('purohit_collected_amount + ' . ($getPujaOLd->base_price)),
                'gst_total_amount' => \Illuminate\Support\Facades\DB::raw('gst_total_amount + ' . ($getPujaOLd->gst)),
                'admin_commission' => \Illuminate\Support\Facades\DB::raw('admin_commission + ' . ($getPujaOLd->platform_fee)),
            ]);
            //     $lastEmpId = TempleOrderDetails::where('purohit_id', $getPujaOLd->purohit_id)->where('type', 'puja')->latest('id')->value('emp_id');
            //     $employees = \App\Models\VendorEmployees::where('purohit_id', $getPujaOLd->purohit_id)->where('status', 1)->orderBy('id')->pluck('id')->toArray();
            //     if (empty($employees)) {
            //         $nextEmpId = 0;
            //     } elseif ($lastEmpId == 0) {
            //         $nextEmpId = $employees[0];
            //     } else {
            //         $currentIndex = array_search($lastEmpId, $employees);
            //         if ($currentIndex === false || !isset($employees[$currentIndex + 1])) {
            //             $nextEmpId = $employees[0];
            //         } else {
            //             $nextEmpId = $employees[$currentIndex + 1];
            //         }
            //     }
            //     if ($nextEmpId) {
            //         \App\Models\VendorEmployees::where('id', $nextEmpId)->update(['withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . $getPujaOLd->base_price)]);
            //     }
            //     if ($getPujaOLd->purohit_id) {
            //         Purohit::where('id', $getPujaOLd->purohit_id)->update(['withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . $getPujaOLd->base_price)]);
            //     }
            //     DonateTrust::where('id', $getPujaOLd->trust_id)->update(['trust_total_amount' => \Illuminate\Support\Facades\DB::raw('trust_total_amount + ' . $getPujaOLd->receipt_fee)]);
            //     $orderGet = TempleOrderDetails::updateOrCreate(
            //         [
            //             'order_id' => $request->order_id,
            //             'type'     => 'puja',
            //         ],
            //         [
            //             "emp_id" => $nextEmpId,
            //         ]
            //     );
        }
        if (!$order) {
            return response()->json(['success' => false]);
        }
        $order->booking_status = 'confirmed';
        $order->payment_status = '1';
        $order->payment_mode = 'cash';
        $order->payment_confirmed_by =  $confirmedBy;
        $order->payment_confirmed_at = now();
        $order->save();
        if ($order->details && $order->details->count() > 0) {
            foreach ($order->details as $detail) {
                $detail->payment_status = '1';
                $detail->booking_status = 'confirmed';
                $detail->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function confirmPurohitPayment(Request $request)
    {
        $purohitId = $request->purohit_id;
        $orderId = $request->order_id;
        $order = TempleOrderMaster::where('order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found']);
        }
        if ($order->booking_status !== 'confirmed' || $order->payment_status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Purohit can only be assigned after booking confirmation and full payment.'
            ]);
        }
        $orderDetail = TempleOrderDetails::where('order_id', $order->order_id)->where('type', 'puja')->first();

        if (!$orderDetail) {
            return response()->json(['success' => false, 'message' => 'No puja order detail found.']);
        }

        $previousPurohitId = $orderDetail->purohit_id;
        $amount = ($orderDetail->base_price ?? 0) * ($orderDetail->people_count ?? 0);
        $orderDetail->update(['purohit_id' => $purohitId]);
        $existingTran = TrustPanditTransection::where('order_id', $orderId)->first();

        if ($existingTran) {
            $existingTran->update(['pandit_id' => $purohitId]);
        } else {
            TrustPanditTransection::create([
                'order_id'       => $orderId,
                'type_order_id'  => $orderDetail->type_order_id,
                'temple_id'      => $orderDetail->temple_id ?? null,
                'trust_id'       => $orderDetail->trust_id ?? null,
                'pandit_id'      => $purohitId,
                'package_id'     => $orderDetail->package_id,
                'package_price'  => $amount,
                'payment_method' => "cash",
                'payment_status' => "complete"
            ]);
        }
        // Step 3: Handle previous pandit history (if reassigning)
        if (!empty($previousPurohitId) && $previousPurohitId != $purohitId) {
            $prevHistory = PanditTransectionHistory::where('order_id', $orderId)
                ->where('purohit_id', $previousPurohitId)
                ->first();

            if ($prevHistory) {
                // Deduct previous pandit's balance
                $prevBalance = PanditTransectionHistory::where('purohit_id', $previousPurohitId)
                    ->orderByDesc('id')
                    ->value('balance') ?? 0;

                $newPrevBalance = max(0, $prevBalance - $amount);

                PanditTransectionHistory::create([
                    'order_id'    => $orderId,
                    'temple_id'   => $orderDetail->temple_id,
                    'trust_id'    => $orderDetail->trust_id,
                    'purohit_id'  => $previousPurohitId,
                    'type'        => $orderDetail->type,
                    'debit'       => $amount,
                    'balance'     => $newPrevBalance,
                    'debit_date'  => now(),
                ]);
            }
        }
        // Step 4: Add new pandit credit
        $existingBalance = PanditTransectionHistory::where('purohit_id', $purohitId)->orderByDesc('id')->value('balance') ?? 0;

        $newBalance = $existingBalance + $amount;
        PanditTransectionHistory::create([
            'order_id'    => $orderId,
            'temple_id'   => $orderDetail->temple_id,
            'trust_id'    => $orderDetail->trust_id,
            'purohit_id'  => $purohitId,
            'type'        => $orderDetail->type,
            'credit'      => $amount,
            'balance'     => $newBalance,
            'credit_date' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Purohit assigned successfully.']);
    }
    //package _upgrade
    public function confirmPackage(Request $request)
    {

        $orderId = $request->order_id;
        $order = TempleOrderMaster::where('order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found']);
        }
        // PUJA DETAIL ONLY
        $orderDetail = TempleOrderDetails::where('order_id', $order->order_id)->where('type', 'puja')->first();
        if (!$orderDetail) {
            return response()->json(['success' => false, 'message' => 'No puja order detail found.']);
        }
        /** -----------------------------------------------------
         *  NEW: Store Package Upgrade History
         * ----------------------------------------------------- */
        if ($request->package_id && $request->package_id != $orderDetail->package_id) {
            $oldPackageId = $orderDetail->package_id;
            $purohitId = $orderDetail->purohit_id;
            $newPackageId = $request->package_id;
            $oldAmount = $orderDetail->final_amount;
            // Get NEW package amount
            $newPkg = TempleServicePrice::find($newPackageId);
            $newAmount = 0;
            if ($newPkg) {
                $basePrice   = $newPkg->base_price;
                $platformFee = ($newPkg->platform_fee_percentage ?? 0);
                $receiptFee  = ($newPkg->receipt_fee_percentage ?? 0);
                $gstAmount = ($orderDetail->type == 'bhojan' || $orderDetail->type == 'locker') ? 0 : ($basePrice * $newPkg->gst_rate / 100);
                $totalPricePerCustomer = $basePrice + $gstAmount + $platformFee + $receiptFee;
                $newAmount = $totalPricePerCustomer * $orderDetail->people_count;
            }


            $upgradeHistory = TemplePackageUpgradeHistory::create([
                'order_id'       => $orderId,
                'temple_id'      => $orderDetail->temple_id,
                'trust_id'       => $orderDetail->trust_id,
                'purohit_id'     => $purohitId,

                'old_package_id' => $oldPackageId,
                'new_package_id' => $newPackageId,

                'old_amount'     => $oldAmount,
                'new_amount'     => $newAmount,

                'upgraded_at'    => now()
            ]);

            // update new package into order details
            $orderDetail->package_id = $newPackageId;
            $orderDetail->base_price = $newAmount;
            $orderDetail->save();

            // ---- UPDATE ORDER MASTER ----
            $order->update([
                'upgrade_id'  => $upgradeHistory->id,
                'is_upgraded' => 1
            ]);
        }
        /** -----------------------------------------------------
         *  END Upgrade History
         * ----------------------------------------------------- */
        $existingTran = TrustPanditTransection::where('order_id', $orderId)->first();
        $amount = ($orderDetail->base_price ?? 0) * ($orderDetail->people_count ?? 0);
        if ($existingTran) {
            $existingTran->update(['pandit_id' => $orderDetail->purohit_id]);
        } else {
            TrustPanditTransection::create([
                'order_id'       => $orderId,
                'type_order_id'  => $orderDetail->type_order_id,
                'temple_id'      => $orderDetail->temple_id ?? null,
                'trust_id'       => $orderDetail->trust_id ?? null,
                'pandit_id'      => $orderDetail->purohit_id,
                'package_id'     => $orderDetail->package_id,
                'package_price'  => $amount,
                'payment_method' => "cash",
                'payment_status' => "complete"
            ]);
        }
        // Step 3: Handle previous pandit history (if reassigning)
        if (!empty($previousPurohitId) && $previousPurohitId != $orderDetail->purohit_id) {
            $prevHistory = PanditTransectionHistory::where('order_id', $orderId)->where('purohit_id', $previousPurohitId)->first();
            if ($prevHistory) {
                // Deduct previous pandit's balance
                $prevBalance = PanditTransectionHistory::where('purohit_id', $previousPurohitId)
                    ->orderByDesc('id')
                    ->value('balance') ?? 0;

                $newPrevBalance = max(0, $prevBalance - $amount);

                PanditTransectionHistory::create([
                    'order_id'    => $orderId,
                    'temple_id'   => $orderDetail->temple_id,
                    'trust_id'    => $orderDetail->trust_id,
                    'purohit_id'  => $previousPurohitId,
                    'type'        => $orderDetail->type,
                    'debit'       => $amount,
                    'balance'     => $newPrevBalance,
                    'debit_date'  => now(),
                ]);
            }
        }
        // Step 4: Add new pandit credit
        $existingBalance = PanditTransectionHistory::where('purohit_id', $orderDetail->purohit_id)->orderByDesc('id')->value('balance') ?? 0;
        $newBalance = $existingBalance + $amount;
        PanditTransectionHistory::create([
            'order_id'    => $orderId,
            'temple_id'   => $orderDetail->temple_id,
            'trust_id'    => $orderDetail->trust_id,
            'purohit_id'  => $orderDetail->purohit_id,
            'type'        => $orderDetail->type,
            'credit'      => $amount,
            'balance'     => $newBalance,
            'credit_date' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Upgrade the Package successfully.']);
    }


    public function PurohitTotalAmountGet(Request $request)
    {

        $purohit_id = $request->get('purohit_id');
        $emp_id = $request->get('emp_id');
        $query = TempleOrderDetails::with(['order', 'temple', 'purohit'])->where('type', 'puja')
            ->when(!empty($purohit_id), function ($query) use ($purohit_id) {
                return $query->where(['purohit_id' => $purohit_id]);
            })
            ->when(!empty($emp_id), function ($query) use ($emp_id) {
                return $query->where(['emp_id' => $emp_id]);
            })
            ->where('trust_id', $this->relationId);

        //satish
        if (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')) {
            $keys = 'base_price';
        } elseif (auth('purohit')->check()) {
            $keys = 'base_price';
        } else {
            $keys = 'receipt_fee';
        }

        $getTotalcashAmount =  (clone $query)->whereHas('order', function ($q) {
            $q->where(['payment_mode' => 'cash']);
        })->where(['booking_status' => 'confirmed'])->sum($keys);
        $getTotalonlineAmount =  (clone $query)->whereHas('order', function ($q) {
            $q->where(['payment_mode' => 'online']);
        })->where(['booking_status' => 'confirmed'])->sum($keys);
        $getTotalPendingAmount =  (clone $query)->where(['booking_status' => 'pending'])->sum($keys);
        $getTotalAmount = $getTotalonlineAmount + $getTotalcashAmount;

        $total_order = (clone $query)->count();
        $complete_order = (clone $query)->where(['booking_status' => 'confirmed'])->count();
        $pending_order = (clone $query)->where(['booking_status' => 'pending'])->count();
        $cancelled_order = (clone $query)->where(['booking_status' => 'cancelled'])->count();
        return response()->json(['success' => true, 'data' => [
            'cash_amount' => $getTotalcashAmount,
            'online_amount' => $getTotalonlineAmount,
            'pending_amount' => $getTotalPendingAmount,
            'total_amount' => $getTotalAmount,
            'total_order' => $total_order,
            'complete_order' => $complete_order,
            'pending_order' => $pending_order,
            'cancelled_order' => $cancelled_order
        ]]);
    }



    public function order_create(Request $request)
    {
        $relationEmployees = null;
        $confirmedBy = null;

        if (auth('trust')->check()) {
            $relationEmployees = auth('trust')->user()->relation_id;
            $confirmedBy = auth('trust')->id();
            $temples = \App\Models\Temple::where('trust_id', $relationEmployees)->get();
        } elseif (auth('trust_employee')->check()) {
            $employee = auth('trust_employee')->user();
            $relationEmployees = $employee->relation_id;
            $confirmedBy = $employee->id;
            $temples = \App\Models\Temple::where('id', $employee->temple_id)->get();
        } elseif (auth('purohit')->check()) {
            $confirmedBy = auth('purohit')->user()->id;
            $temples = \App\Models\Temple::where('id', auth('purohit')->user()->temple_id)->get();
            $relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id', auth('purohit')->user()->id)->first()['temple']['trust_id'] ?? 0);
        } else {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Temple selection via AJAX
        if ($request->ajax() && $request->temple_id) {
            $temple = \App\Models\Temple::find($request->temple_id);
            $plans = json_decode($temple->package_service ?? '[]', true);
            $packageIds = collect($plans)->pluck('id')->filter()->toArray();

            $packages = TempleServicePrice::where('temple_id', $temple->id)
                ->where('trust_id', $temple->trust_id)
                ->whereIn('package_id', $packageIds)
                ->where('status', 1)
                ->get();
            $html = view('all-views.trustees.temple.order.partials.tabs', compact('plans', 'temple', 'packages'))->render();
            return response()->json(['html' => $html]);
        }

        $query = TempleOrderMaster::with(['temple', 'user', 'details.package'])
            ->where('status', 1)
            ->whereHas('temple', function ($q) {
                $q->where('trust_id', $this->relationId);
            });
        $query->whereHas('details', function ($q1) {
            $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                $q->where('type', 'puja');
            })->when((auth('purohit')->check()), function ($q) {
                $q->where('purohit_id', auth('purohit')->user()->id);
                $q->where('type', 'puja');
            });
        });
        $OrderList = $query->get();
        $purohits = Purohit::where('relation_id', $this->relationId)->where('status', 1)->orderBy('name', 'asc')->get(['id', 'name']);
        return view('all-views.trustees.temple.order.create-order', compact('temples', 'OrderList', 'purohits'));
    }
    public function getSlots(Request $request)
    {
        $slots = \App\Models\TempleServiceSlot::where('temple_service_prices_id', $request->package_id)
            ->where('is_available', 1)
            ->select('id', 'start_time', 'end_time')
            ->get();

        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }
    public function storePooja(Request $request)
    {
        $lastOrder = TempleLeadMaster::select('id')->latest()->first();
        $lastId = !empty($lastOrder['id']) ? (100001 + $lastOrder['id']) : 100001;
        $custo_mers = $request->customers ?? [];
        $payment_mode_type = ($request['payment_mode'] ?? "");
        $payment_mode_qrCode = 0;
        if (($request['payment_mode'] ?? "") == 'qr_code') {
            $payment_mode_type = 'online';
            $payment_mode_qrCode = 1;
        }

        $customerIds = [];

        if (!empty($custo_mers)) {
            $customers = [];
            $pq = 0;
            foreach ($custo_mers as $index => $cust) {
                $customers[$pq] = $cust;
                $pq++;
            }
            foreach ($customers as $index => $cust) {
                $numbering = '(' . str_pad($index + 1, 2, '0', STR_PAD_LEFT) . ')';
                $personName  = trim($cust['name']) . ' ' . $numbering;
                $cleanPhone = trim($cust['mobile']);
                $aadhaarNo   = trim($cust['aadhaar']);
                $address     = trim($cust['address']);
                $personPhone = preg_replace('/\D/', '', $cleanPhone);
                if (!empty($personPhone) && $index === 0 && (strlen((string)$personPhone) > 9)) {
                    $user = User::where('phone', $personPhone)->first();
                    if (!$user) {
                        $nameParts = explode(' ', $personName);
                        $firstName = $nameParts[0] ?? '';
                        $lastName  = $nameParts[1] ?? '';
                        $verifyOTP = rand(100000, 999999);
                        $user = User::create([
                            'name'        => $personName,
                            'f_name'      => $firstName,
                            'l_name'      => $lastName,
                            'phone'       => $personPhone,
                            'email'       => 'user@mahakal.com',
                            'password'    => bcrypt('12345678'),
                            'verify_otp'  => $verifyOTP,
                        ]);

                        $data = ['customer_id' => $user->id];
                        \App\Utils\Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
                        $customerIds[] = $user->id;
                    } else {
                        $customerIds[] = $user['id'];
                    }
                } else {
                    $customerIds[] = 0;
                }
                $customers[$index]['name'] = $personName;
            }
        }
        $packageData = TempleServicePrice::find($request->package_id);
        if ($packageData) {
            $basePrice   = $packageData->base_price * count($customers);
            $platformFee = ($packageData->platform_fee_percentage ?? 0) * count($customers); //($packageData->platform_fee_percentage / 100) * $basePrice;
            $receiptFee  = ($packageData->receipt_fee_percentage ?? 0) * count($customers); // ($packageData->receipt_fee_percentage / 100) * $basePrice;

            $gstAmount = ($request->type == 'bhojan' || $request->type == 'locker') ? 0 : (($basePrice * $packageData->gst_rate / 100));

            $totalPricePerCustomer = $basePrice + $gstAmount + $platformFee + $receiptFee;
            $totalPrice = $totalPricePerCustomer;
        } else {
            $totalPrice = 0;
            $totalPricePerCustomer = 0;
        }
        $orderId = 'MCOM' . $lastId;
        $trust = Temple::select('trust_id')->where('id', $request->temple_id)->first();

        $leads = TempleLeadMaster::create([
            'temple_id'    => $request->temple_id,
            'user_id'      => $customerIds[0] ?? null,
            'trust_id'     => $trust->trust_id ?? null,
            'order_id'     => $orderId,
            'customer_qty' => count($customers),
            'amount'       => $totalPrice,
        ]);
        $purohits = Purohit::where('temple_id', $request->temple_id)->where('status', 1)->get();
        if ($request->type == 'puja') {
            $panditId = (($request['purohit_id']) ? $request['purohit_id'] : ($purohits->count() ? $purohits->random()->id : 0));
        } else {
            $panditId =  0;
        }
        $typeOrderId = match ($request->type) {
            'puja'    => 'PJ' . $lastId,
            'darshan' => 'DS' . $lastId,
            'bhojan'  => 'BJ' . $lastId,
            default   => 'LK' . $lastId,
        };

        $data = TempleLeadDetail::create([
            'package_id'     => $request->package_id,
            'amount'         => $totalPrice,
            'booking_date'   => $request->date,
            'order_id'       => $orderId,
            'type'           => $request->type,
            'type_order_id'  => $typeOrderId,
            'customer_qty'   => count($customers),
            'customers'      => json_encode($customers),
            'pandit_id'      => $panditId,
            'time_slot_id'   => $request->slot_id ?? null,
            'locker_items'   => $request->locker_items ? json_encode($request->locker_items) : null,
        ]);
        // $url_open = \App\Http\Controllers\Customer\PaymentController::temple_payment_request($request);
        // if ($urls == 1) {
        //     return back()->with('success', 'Order created Successfully!');
        // } else {
        //     return back()->with('success', 'Order Faild Unsccessfully!');
        // }

        if ($payment_mode_type === 'cash' || $payment_mode_type === 'free') {
            $leads->update(['payment_mode' => $payment_mode_type, 'payment_status' => 1, 'status' => 1,]);
            $order = TempleOrderMaster::updateOrCreate(
                ['order_id' => $leads->order_id ?? ('ORD' . time())],
                [
                    'lead_id'            => $leads->id,
                    'user_id'            => $leads->user_id,
                    'temple_id'          => $leads->temple_id,
                    'trust_id'           => $leads->trust_id,
                    'total_people_count' => $leads->customer_qty,
                    'total_amount'       => $leads->amount,
                    'transaction_id'     => $leads->payment_mode,
                    'booking_status'     => 'confirmed',
                    'platform'           => ((auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit'))) ? 'purohit' : 'counter'),
                    'payment_mode'       => $payment_mode_type,
                    'status'             => 1,
                    'payment_status'     => 1,
                ]
            );
            $whatsapp_message_data = [];
            $whatsapp_message_data['type'] = 'text-with-media';
            $whatsapp_message_data['temple_name'] = $leads['temple']['name'];

            $leadDetails = TempleLeadDetail::where('order_id', $leads->order_id)->where('status', 1)->with('package')->get();
            foreach ($leadDetails as $detail) {
                $customers = json_decode($detail->customers, true) ?? [];
                $basePrice  = ($detail->package->base_price ?? 0) * count($customers);
                $gstRate    = $detail->type == 'locker' || $detail->type == 'bhojan' ? 0 : $detail->package->gst_rate;
                $platformFeePercent = (($detail->package->platform_fee_percentage ?? 0) * count($customers));
                $receiptFeePercent  = (($detail->package->receipt_fee_percentage ?? 0) * count($customers));

                $gstAmount = ($gstRate > 0) ? (($basePrice * $gstRate) / 100) : 0;
                $timeSlot = TempleServiceSlot::where('id', $detail->time_slot_id)->where('temple_service_prices_id', $detail->package_id)->first();

                $orderGet = TempleOrderDetails::updateOrCreate(
                    [
                        'order_id' => $detail->order_id,
                        'type'     => $detail->type,
                    ],
                    [
                        'package_id'     => $detail->package_id,
                        'temple_id'      => $leads->temple_id,
                        'trust_id'       => $leads->trust_id,
                        'people_count'   => $detail->customer_qty,
                        'gst'            => $gstAmount,
                        'base_price'     => $basePrice,
                        'platform_fee'   => $platformFeePercent,
                        'receipt_fee'    => $receiptFeePercent,
                        'final_amount'   => $detail->amount,
                        'booking_date'   => $detail->booking_date,
                        'customers'      => $detail->customers,
                        'type_order_id'   => $detail->type_order_id,
                        'time_slot'      => $timeSlot ? ($timeSlot->start_time . ' - ' . $timeSlot->end_time) : 'pending',
                        'locker_items'   => $request->locker_items ? json_encode($request->locker_items) : null,
                        'purohit_id'     => $panditId,
                        'booking_status' => 'confirmed',
                        'status'         => 1,
                        'payment_status' => 1,
                    ]
                );
                if ($detail->type == 'puja' && auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')) {
                    \App\Models\TempleOrderDetails::updateOrCreate(
                        [
                            'order_id' => $detail->order_id,
                            'type'     => $detail->type,
                        ],
                        ['emp_id' => auth('trust_employee')->user()->id]
                    );
                }
                $getTypes = TempleOrderDetails::where('order_id', $detail->order_id)->first();

                if ($getTypes && strtolower($getTypes->type ?? '') === 'puja') {
                    DonateTrust::where('id', $getTypes->trust_id)->update([
                        'trust_total_withdrawal' => \Illuminate\Support\Facades\DB::raw('trust_total_withdrawal + ' . ($getTypes->receipt_fee)),
                        'purohit_collected_amount' => \Illuminate\Support\Facades\DB::raw('purohit_collected_amount + ' . ($getTypes->base_price)),
                        'gst_total_amount' => \Illuminate\Support\Facades\DB::raw('gst_total_amount + ' . ($getTypes->gst)),
                        'admin_commission' => \Illuminate\Support\Facades\DB::raw('admin_commission + ' . ($getTypes->platform_fee)),
                    ]);
                } else {
                    DonateTrust::where('id', $getTypes->trust_id)->update([
                        'trust_total_withdrawal' => \Illuminate\Support\Facades\DB::raw('trust_total_withdrawal + ' . (($getTypes->receipt_fee??0) + ($getTypes->base_price??0))),
                        'gst_total_amount' => \Illuminate\Support\Facades\DB::raw('gst_total_amount + ' . ($getTypes->gst)),
                        'admin_commission' => \Illuminate\Support\Facades\DB::raw('admin_commission + ' . ($getTypes->platform_fee)),
                    ]);
                }

                $purohits = Purohit::where('temple_id', $getTypes->temple_id)->where('id', $getTypes->purohit_id)
                    ->where('status', 1)
                    ->get();
                $order->load('temple');
                $orderGet->load('package');

                Toastr::success(translate($payment_mode_type . ' payment recorded successfully!'));
                $memberNames = collect($customers)->pluck('name')->filter()->implode(', ');

                $serviceType = $detail->type == 'puja' ? 'Pooja Booking' : ($detail->type == 'darshan' ? 'Darshan Booking' : ($detail->type == 'bhojan' ? 'Bhojan Booking' : 'Locker Booking'));

                $whatsapp_message_data[$detail->type] = [
                    'Service' => $serviceType,
                    'Package Name' => $detail['package']['varient_name'],
                    'Booking Date' => date('d-m-Y', strtotime($detail->booking_date)),
                    'Amount' => webCurrencyConverter($detail['amount']),
                ];

                // Add time slot
                if (!empty($detail['time_slot_id'])) {
                    $whatsapp_message_data[$detail->type]['Time Slot'] =
                        $detail['timeslot']['start_time'] . '-' . $detail['timeslot']['end_time'];
                }
                $customers = json_decode($detail['customers'], true);
                $lockerItems = json_decode($detail['locker_items'], true);

                if (!empty($customers)) {
                    $whatsapp_message_data[$detail->type]['Customers'] =
                        collect($customers)->pluck('name')->implode(', ');
                }

                if (!empty($lockerItems)) {
                    $whatsapp_message_data[$detail->type]['Locker Items'] =
                        collect($lockerItems)->map(fn($v, $k) => "$k($v)")->implode(', ');
                }
            }

            // email
            $userInfo = User::where('id', ($leads->user_id ?? ""))->first();
            // $service_name = TempleServicePrice::where('temple_id', $leads->temple_id)->where('package_id', $detail->package_id)->where('status', 1)->first();
            // invoice
            $lead = $leads;
            $url = route('temple.show-qr-detail', ['order_id' => $lead->order_id]);
            $qrCode = new \Endroid\QrCode\QrCode($url);
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            $folder = storage_path('app/public/temple/qrcodes');
            if (!\Illuminate\Support\Facades\File::exists($folder)) {
                \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
            }
            $filePath = $folder . "/" . $lead->order_id . ".png";
            $result->saveToFile($filePath);
            $imageData = "<img class='upload-img-view' src='" . getValidImage(path: 'storage/app/public/temple/qrcodes/' . $lead->order_id . '.png', type: 'backend-product') . "' alt='' style='width:130px'>";

            $mpdf_view = \View::make('web-views.temple.invoice', compact('userInfo', 'lead', 'leadDetails', 'imageData'));
            Helpers::gen_mpdf_temple_Pdf($mpdf_view, 'temple_order_', $leads['order_id']);
            $whatsapp_message_data['attachment'] = asset('storage/app/public/temple/invoice/temple_order_' . $leads['order_id'] . '.pdf');

            // whatsapp msg  
            if ($userInfo) {
                $whatsapp_message_data['orderId'] = $leads->order_id;
                $whatsapp_message_data['final_amount'] = $leads->amount;
                $whatsapp_message_data['customer_id'] = $userInfo->id;
                $whatsapp_message_data['type'] = 'text-with-media';
                $messages =  Helpers::whatsappMessage('temple', 'Service Booking', $whatsapp_message_data);
            }
            // email
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of Your Order #' . $leads->order_id;
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make(
                    'admin-views.email.email-template.temple-template',
                    compact('userInfo', 'lead', 'leadDetails')
                )->render();
                Helpers::emailSendMessage($data);
            }
            if ($request->ajax()) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Order created successfully!',
                    "url" => '',
                    'data' => $order,
                    'purohit_id' => $panditId,
                    'purohit_name' => (Purohit::where('id', $panditId)->first()['name'] ?? ""),
                ]);
            } else {
                return back()->with('success', 'Order created successfully!');
            }
        }
        if ($payment_mode_qrCode == 1) {
            TempleLeadMaster::where('id', $lead['id'] ?? $leads?->id)->update([
                'payment_mode' => $leads->amount > 0 ? $payment_mode_type : 'free',
                'status' => 0,
                'payment_status' => 0,
            ]);
            $order = TempleOrderMaster::updateOrCreate(
                ['order_id' => $leads->order_id ?? 'ORD' . time()],
                [
                    'lead_id'            => $leads->id,
                    'user_id'            => $leads->user_id,
                    'temple_id'          => $leads->temple_id,
                    'trust_id'           => $leads->trust_id,
                    'total_people_count' => $leads->customer_qty,
                    'total_amount'       => $leads->amount,
                    'payment_mode'       => $payment_mode_type,
                    'booking_status'     => 'pending',
                    'payment_status'     => 0,
                    'platform'           => ((auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit'))) ? 'purohit' : 'counter'),
                ]
            );
            $leadDetails = TempleLeadDetail::where('order_id', $leads->order_id)->where('status', 1)->with('package')->get();
            foreach ($leadDetails as $detail) {
                $customers = json_decode($detail->customers, true);
                $customerCount = is_array($customers) ? count($customers) : 0;
                $baseAmount = $detail->amount;
                $basePrice = $detail->package->base_price ?? 0;
                $gstRate = $detail->package->gst_rate ?? 0;
                $platformFeePercent = $detail->package->platform_fee_percentage  ?? 0;
                $receiptFeePercent  = $detail->package->receipt_fee_percentage  ?? 0;
                $gstAmount   = ($gstRate > 0) ? (($gstRate / 100) * $baseAmount) : 0;
                $timeSlot = TempleServiceSlot::where('id', $detail->time_slot_id)->where('temple_service_prices_id', $detail->package_id)->first();
                $timeSlotText = $timeSlot
                    ? (\Carbon\Carbon::parse($timeSlot->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($timeSlot->end_time)->format('h:i A'))
                    : 'pending';
                TempleOrderDetails::updateOrCreate(
                    [
                        'order_id' => $detail->order_id,
                        'type'     => $detail->type,
                    ],
                    [
                        'package_id'     => $detail->package_id,
                        'temple_id'      => $leads->temple_id,
                        'trust_id'       => $leads->trust_id,
                        'purohit_id'     => $detail->pandit_id,
                        'people_count'   => $customerCount,
                        'gst'            => $gstAmount,
                        'base_price'     => $basePrice,
                        'platform_fee'   => $platformFeePercent,
                        'receipt_fee'    => $receiptFeePercent,
                        'final_amount'   => $baseAmount,
                        'booking_date'   => $detail->booking_date,
                        'customers'      => $detail->customers,
                        'type_order_id'   => $detail->type_order_id,
                        'time_slot'      => $timeSlot ? ($timeSlot->start_time . ' - ' . $timeSlot->end_time) : 'pending',
                        'locker_items' => $request->locker_items ? json_encode($request->locker_items) : ($detail->locker_items ?? '{}'),
                        'booking_status' => 'pending',
                        'status'         => 1,
                        'payment_status' => 0,
                    ]
                );
                if ($detail->type == 'puja' && auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit')) {
                    \App\Models\TempleOrderDetails::updateOrCreate(
                        [
                            'order_id' => $detail->order_id,
                            'type'     => $detail->type,
                        ],
                        ['emp_id' => auth('trust_employee')->user()->id]
                    );
                }
            }

            $get_Razorpay = \App\Models\Setting::where(['key_name' => 'razor_pay'])->first();
            $RAZORPAY_KEY_ID = '';
            $RAZORPAY_KEY_SECRET = '';
            $RAZORPAY_ACCOUNT_NUMBER = '';
            if ($get_Razorpay['mode'] == 'live') {
                $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
                $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
                $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['live_values']['account_number'] ?? '';
            } else {
                $RAZORPAY_KEY_ID = $get_Razorpay['test_values']['api_key'];
                $RAZORPAY_KEY_SECRET = $get_Razorpay['test_values']['api_secret'];
                $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['test_values']['account_number'] ?? '';
            }
            $api = new \Razorpay\Api\Api($RAZORPAY_KEY_ID, $RAZORPAY_KEY_SECRET);
            $userInfo = User::where('id', ($leads->user_id ?? ""))->first();
            $email = $userInfo['email'];
            $contact = $userInfo['phone'];
            $url = "https://api.razorpay.com/v1/customers";
            $data = [
                "name" => $userInfo['name'],
                "email" => $email,
                "contact" => $contact,
                "fail_existing" => "0",
                "type" => "vendor",
            ];
            $headers = [
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode("$RAZORPAY_KEY_ID:$RAZORPAY_KEY_SECRET")
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode == 200 || $httpCode == 201) {
                $contact_data = json_decode($response, true);
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Failed to create contact!',
                        "url" => '',
                        'imageData' => '',
                        "paymentId" => '',
                        "response" => json_decode($response, true)
                    ]);
                } else {
                    return back()->with('error', 'failed!');
                }
            }
            if ($contact_data['id']) {
                $url = "https://api.razorpay.com/v1/payments/qr_codes";
                $data = [
                    "type" => "upi_qr",
                    "name" => "mahakal",
                    "usage" => "single_use",
                    "fixed_amount" => true,
                    "payment_amount" => (float)($leads->amount) * 100,
                    "description" => "For Store 1",
                    "customer_id" => $contact_data['id'],
                    "close_by" => now()->addMinutes(10)->timestamp,
                    "notes" => [
                        "purpose" => "Test UPI QR Code notes"
                    ]
                ];
                $headers = [
                    "Content-Type: application/json",
                    "Authorization: Basic " . base64_encode("$RAZORPAY_KEY_ID:$RAZORPAY_KEY_SECRET")
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpCode == 200 || $httpCode == 201) {
                    $qrData = json_decode($response, true);
                    $UserPaymant = new \App\Models\PaymentRequest();
                    $currency_model = \App\Utils\Helpers::get_business_settings('currency_model');
                    if ($currency_model == 'multi_currency') {
                        $currency_code = 'USD';
                    } else {
                        $default = \App\Models\BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                        $currency_code = \App\Models\Currency::find($default)->code;
                    }
                    $customer = \App\Models\User::where("id", ($leads->user_id ?? ""))->first();
                    $payer = [
                        "name" => $customer['f_name'] . ' ' . $customer['l_name'],
                        "email" => $customer['email'],
                        "phone" => $customer['phone'],
                    ];
                    $UserPaymant->payer_id = ($leads->user_id ?? "");
                    $UserPaymant->receiver_id = '100';
                    $UserPaymant->payment_amount = ($leads->amount);
                    $UserPaymant->success_hook = 'digital_payment_success_custom';
                    $UserPaymant->failure_hook = 'digital_payment_fail';
                    $UserPaymant->transaction_id = ($qrData['id'] ?? "");
                    $UserPaymant->currency_code = $currency_code;
                    $UserPaymant->payment_method = 'razor_pay';
                    $additional_data = [
                        'business_name' => '',
                        'business_logo' => '',
                        'payment_mode'  => ($leads->amount) > 0 ? $payment_mode_qrCode : 'free',
                        'leads_id'      => $leads['id'] ?? $leads?->id ?? null,
                        'temple_id'     => $leads['temple_id'] ?? $leads?->temple_id ?? null,
                        'order_id'      => $leads['order_id'] ?? $leads?->order_id ?? null,
                        'customer_id'   => $leads['user_id'] ?? $leads?->user_id ?? null,
                        'final_amount'  => $leads['amount'] ?? $leads?->amount ?? 0,
                    ];
                    $UserPaymant->additional_data = json_encode(array_merge($additional_data, $qrData));
                    $UserPaymant->is_paid = 0;
                    $UserPaymant->payer_information = json_encode($payer);
                    $UserPaymant->external_redirect_link = null;
                    $UserPaymant->receiver_information = json_encode(["name" => 'receiver_name', "image" => 'example.png']);
                    $UserPaymant->attribute_id = idate("U");
                    $UserPaymant->attribute = 'Temple Service';
                    $UserPaymant->payment_platform = "vendor";
                    $UserPaymant->created_at = date('Y-m-d H:i:s');
                    $UserPaymant->updated_at = date('Y-m-d H:i:s');
                    $UserPaymant->save();
                    if ($request->ajax()) {
                        $dataemain['customer_id'] = ($customerIds[0] ?? 0);
                        $dataemain['attachment'] = ($qrData['image_url'] ?? "");
                        $dataemain['payment_link'] = ('');
                        $dataemain['type'] = 'text-with-media';
                        $dataemain['service_name'] = $packageData['varient_name'] ?? "";
                        $dataemain['final_amount'] = $totalPrice;
                        if (($qrData['image_url'] ?? "")) {
                            Helpers::whatsappMessage('donate', 'trust_puja_order_paymant_link_message', $dataemain);
                        }
                        return response()->json([
                            'status'  => true,
                            'message' => 'create Qr Code!',
                            "url" => '',
                            'imageData' => "<img class='upload-img-view' src='" . ($qrData['image_url'] ?? "") . "' alt=''>",
                            "paymentId" => ($qrData['id'] ?? ""),
                            "response" => $qrData
                        ]);
                    } else {
                        return ($qrData['image_url'] ?? "");
                    }
                } else {
                    if ($request->ajax()) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Failed to create Qr Code!',
                            "url" => '',
                            'imageData' => '',
                            "paymentId" => '',
                            "response" => json_decode($response, true)
                        ]);
                    } else {
                        return back()->with('error', 'failed!');
                    }
                }
            }
        } else {
            $request['platforms'] = ((auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") == 'Sub Pandit'))) ? 'purohit' : 'counter');
            $url_open = \App\Http\Controllers\Customer\PaymentController::temple_customer_payment_request($request, $leads);
        }
        $dataemail['customer_id'] = ($customerIds[0] ?? 0);
        $dataemail['payment_link'] = ($url_open ?? '');
        $dataemail['service_name'] = $packageData['varient_name'] ?? "";
        $dataemail['final_amount'] = $totalPrice;
        Helpers::whatsappMessage('donate', 'trust_puja_order_paymant_link_message', $dataemail);

        $qrCode = new \Endroid\QrCode\QrCode($url_open);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $folder = storage_path('app/public/qrcodes');
        if (!\Illuminate\Support\Facades\File::exists($folder)) {
            \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
        }
        $filePath = $folder . "/vip-darshan-ticket-booking-amount.png";
        $result->saveToFile($filePath);
        // $imageData = "<img class='upload-img-view' src='" . getValidImage(path: 'storage/app/public/qrcodes/vip-darshan-ticket-booking-amount.png?v=' . time(), type: 'backend-product') . "' alt=''>";


        $imagePath = 'storage/app/public/qrcodes/vip-darshan-ticket-booking-amount.png';
        $imageData = "<img class='upload-img-view' src='" . getValidImage(path: $imagePath, type: 'backend-product') . "?v=" . time() . "' alt=''>";

        $query12 = parse_url($url_open, PHP_URL_QUERY);
        parse_str($query12, $params12);
        $paymentId = $params12['payment_id'] ?? null;

        if ($request->ajax()) {
            return response()->json([
                'status'  => true,
                'message' => 'Order created successfully!',
                "url" => $url_open,
                'imageData' => $imageData,
                "paymentId" => $paymentId,
            ]);
        } else {
            return back()->with('success', 'Order created successfully!');
        }
    }

    public function PanditOrderList(Request $request)
    {
        $templeList = Temple::with(['Trust'])->where('trust_id', $this->relationId)->orderBy('id', 'desc')->get();
        $bookingListnow = DarshanOrder::where('status', 1)->get();
        $templeIds = $templeList->pluck('id');
        $purohits = Purohit::whereIn('temple_id', $templeIds)->with('temple')->get();
        $query = TempleOrderMaster::with(['temple', 'user', 'details.package'])
            ->where('status', 1)
            ->whereHas('temple', function ($q) {
                $q->where('trust_id', $this->relationId);
            });
        $OrderList = $query->get();
        return view(TrusteesPath::PANDITLIST[VIEW], compact('purohits', 'OrderList'));
    }
    public function purohitBalanceSheet(Request $request)
    {
        $pending_withdral = PanditTransectionHistory::where('purohit_id', $request['id'])->where('status', 0)->get();
        return view(TrusteesPath::PANDITBALANCESHEET[VIEW], compact('pending_withdral'));
    }

    public function purohitBalanceSheetFilter(Request $request, $ids)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $end_date = $request->input('end_date', '');
        $start_date = $request->input('start_date', '');
        $orderColumnIndex = $request->input('order.0.column');
        $query = PanditTransectionHistory::when(!empty($start_date), function ($q) use ($start_date, $end_date) {
            if ($start_date && empty($end_date)) {
                $q->whereDate('created_at', date('Y-m-d', strtotime($start_date)));
            } elseif ($start_date && $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            }
        });

        $recordsTotal = PanditTransectionHistory::where('trust_id', $this->relationId)->whereIn('status', [0, 1])->where('purohit_id', $ids)->count();

        $query->where('trust_id', $this->relationId)->whereIn('status', [0, 1])->where('purohit_id', $ids);
        $recordsFiltered = (clone $query)->count();

        $record_balances = (clone $query)->orderBy('id', 'desc')->first()['balance'] ?? 0;
        $data = (clone $query)->orderBy('id', 'asc')
            ->skip($start)
            ->take($length)
            ->get();
        $formattedData = $data->map(function ($item, $key) {
            return [
                'date' => date('d-m-Y h:i A', strtotime($item['created_at'])),
                'credit' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['credit'] ?? 0), currencyCode: getCurrencyCode()) . (($item['credit'] > 0) ? " (" . ucwords(\App\Models\TempleOrderMaster::where('order_id', $item['order_id'])->first()['payment_mode'] ?? "") . ")" : ''),
                'debit' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['debit'] ?? 0), currencyCode: getCurrencyCode()),
                'balance' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['balance'] ?? 0), currencyCode: getCurrencyCode()),
                'notes' => ($item['note'] ?? ""),
                'status' => $item['status'] ?? 0,
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
            'footerData' => [
                'totalAmount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: $record_balances), currencyCode: getCurrencyCode())
            ]
        ]);
    }

    public function OrderDetailsModalData(Request $request)
    {
        $getData = TempleOrderDetails::with(['order','timeslot', 'package', 'purohit'])->where('order_id', $request['order_id'])->get();
        $html = \View::make('all-views.trustees.temple.order.partials.modal-view-order-details', compact('getData'))->render();
        return response()->json([
            'html' => $html,
        ], 200);
    }

    public function PurohitToGetEmployee(Request $request)
    {
        $getEmployeeData = \App\Models\VendorEmployees::select('id', 'name')->where('type', 'trust')->where('purohit_id', $request['id'])->get();
        if ($request['id'] && $getEmployeeData && count($getEmployeeData) > 0) {
            return response()->json(['data' => $getEmployeeData, 'status' => 1], 200);
        } else {
            return response()->json(['data' => [], 'status' => 0,], 200);
        }
    }

    public function PrintStatusUpdates(Request $request)
    {
        if ($request['type'] == 'puja') {
            TempleOrderDetails::where('type', 'puja')->where('order_id', $request['orderid'])->update(['print_status' => 1]);
        } else {
            TempleOrderDetails::where('type', '!=', 'puja')->where('order_id', $request['orderid'])->update(['print_status' => 1]);
        }
        return response()->json(['data' => [], 'status' => 1], 200);
    }

    public function AllOrderStatusCheck(Request $request)
    {
        $getRecodes = TempleOrderDetails::with(['order','purohit'])->whereIn('order_id', json_decode($request['order_id'] ?? "[]", true))->where('booking_status', 'confirmed')->first();
        if ($getRecodes) {
            $printbutton = '';
             if ($getRecodes['booking_status'] == 'confirmed' && (!auth('purohit')->check() && (!auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? "") != 'Sub Pandit')))) {
                $printbutton .= '<button type="button" class="btn btn-info btn-sm print-button-append" onclick="printNow(this)" data-id="' . $getRecodes['order_id'] . '" data-purohit="' . $getRecodes['purohit_id'] . '" data-platform="' . ($getRecodes['order']['payment_mode']??"") . '" data-purohit_name="' . ($getRecodes['purohit']['name']??"") . '" data-employee="' . $getRecodes['emp_id'] . '" data-employee_status="' . (($getRecodes['order']['platform'] == 'purohit' && $getRecodes['print_status'] == 0) ? 1 : 0) . '"
                                                    title="' . translate('print') . '"
                                                    data-toggle="tooltip" data-placement="left">
                                                    <i class="tio tio-print"></i>
                                                </button>';
            }
            return response()->json(['data' => $getRecodes['order_id'],'printbutton'=>$printbutton, 'status' => 1], 200);
        } else {
            return response()->json(['data' => [], 'status' => 0], 200);
        }
    }
}
