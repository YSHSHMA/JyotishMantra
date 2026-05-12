<?php

namespace App\Http\Controllers\AllController;

use App\Contracts\Repositories\TourAndTravelRepositoryInterface;
use App\Contracts\Repositories\TourCabManageRepositoryInterface;
use App\Contracts\Repositories\TourCabRepositoryInterface;
use App\Contracts\Repositories\TourDriverManageRepositoryInterface;
use App\Contracts\Repositories\TourOrderRepositoryInterface;
use App\Contracts\Repositories\TourTypeRepositoryInterface;
use App\Contracts\Repositories\TourVisitPlaceRepositoryInterface;
use App\Contracts\Repositories\TourVisitRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\AllPaths\LoginPath;
use App\Enums\ViewPaths\AllPaths\TourPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourAndTravelRequest;
use App\Http\Requests\Admin\TourVisitPlaceRequest;
use App\Http\Requests\Admin\TourVisitRequest;
use App\Http\Requests\Tour\CabRequest;
use App\Http\Requests\Tour\DriverRequest;
use App\Models\TourFollowup;
use App\Models\TourLeads;
use App\Models\TourOrder;
use App\Models\TourVisits;
use App\Models\User;
use App\Services\TourAndTravelService;
use App\Services\TourVisitService;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\Double;
use SimplePie\Cache\Redis;
use Illuminate\Support\Str;
use App\Utils\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Traits\TourVisitValidation;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    use FileManagerTrait;
    use TourVisitValidation;
    protected $relationId;
    public function __construct(
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly TourAndTravelRepositoryInterface  $tourtraveller,
        private readonly TourOrderRepositoryInterface  $tourorder,
        private readonly TourTypeRepositoryInterface  $tourtypeRepo,
        private readonly TourVisitRepositoryInterface  $tourvisitRepo,
        private readonly TourVisitPlaceRepositoryInterface  $tourvisitplac,
        private readonly TourCabManageRepositoryInterface  $tourtravellercabRepo,
        private readonly TourCabRepositoryInterface $tourcabRepo,
        private readonly TourDriverManageRepositoryInterface  $tourtravellerdriverRepo,
    ) {
        $this->middleware(function ($request, $next) {
            if (auth('tour')->check()) {
                $this->relationId = auth('tour')->user()->relation_id;
            } elseif (auth('tour_employee')->check()) {
                $this->relationId = auth('tour_employee')->user()->relation_id;
            } else {
                $this->relationId = null;
            }

            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $OrderInfo = \App\Models\TourOrder::whereIn('status', [1, 0])->where('refund_status', 0)->where('amount_status', 1);
        $orderStatus = [
            'pending' => \App\Models\TourOrder::whereIn('status', [1, 0])->where(['refund_status' => 0, 'pickup_status' => 0, 'amount_status' => 1, 'drop_status' => 0, 'cab_assign' => 0])
                ->where('pickup_date', '>', \Carbon\Carbon::today()->toDateString())
                ->whereHas('accept', function ($query) {
                    $query->where('status', 1)->where('traveller_id', auth('tour')->user()->relation_id);
                })
                ->where(function ($q) {
                    $q->whereNull('cancel_vendor_list')
                        ->orWhere('cancel_vendor_list', '[]')
                        ->orWhere('cancel_vendor_list', '')
                        ->orWhereRaw(
                            "NOT JSON_CONTAINS(cancel_vendor_list, ?)",
                            [json_encode((string) auth('tour')->user()->relation_id)]
                        );
                })
                ->withCabOrderCheck(auth('tour')->user()->relation_id)->with(['accept'])->count(),
            'confirmed' => (clone $OrderInfo)->where('pickup_status', 0)->where('drop_status', 0)->where('cab_assign', auth('tour')->user()->relation_id)->whereRaw("JSON_CONTAINS(traveller_cab_id, '0')")->count(),
            'assigned' => (clone $OrderInfo)->where('pickup_status', 0)->where('drop_status', 0)->where('cab_assign', auth('tour')->user()->relation_id)->whereRaw("NOT JSON_CONTAINS(traveller_cab_id, '0')")->count(),
            'pickup' => (clone $OrderInfo)->where('pickup_status', 1)->where('drop_status', 0)->where('cab_assign', auth('tour')->user()->relation_id)->count(),
            'complete' => (clone $OrderInfo)->where('pickup_status', 1)->where('drop_status', 1)->where('cab_assign', auth('tour')->user()->relation_id)->count(),
            'canceled' => \App\Models\TourAndTravel::where('id', auth('tour')->user()->relation_id)->first()['cancel_order'],
        ];
        $tourInformation = \App\Models\TourAndTravel::where('id', auth('tour')->user()->relation_id)->first();
        $dashboardData = [
            'totalEarning' => $tourInformation['wallet_amount'],
            'pendingWithdraw' => $tourInformation['withdrawal_pending_amount'],
            "adminCommission" => $tourInformation['admin_commission'],
            "withdrawn" => $tourInformation['withdrawal_amount'],
            'collectedTotalTax' => $tourInformation['gst_amount'],
        ];
        $withdrawalMethods = \App\Models\WithdrawalMethod::where(['is_active' => 1])->get();

        $query = \App\Models\TourOrder::select(\Illuminate\Support\Facades\DB::raw('SUM(final_amount) as y'));
        $types = session()->get('statistics_type') ?? "yearEarn";
        if ($types === 'yearEarn') {
            $query->addSelect(\Illuminate\Support\Facades\DB::raw("YEAR(pickup_date) as x"))->groupBy(\Illuminate\Support\Facades\DB::raw("YEAR(pickup_date)"));
        } elseif ($types === 'MonthEarn') {
            $query->addSelect(\Illuminate\Support\Facades\DB::raw("DATE_FORMAT(pickup_date, '%Y-%m') as x"))->groupBy(\Illuminate\Support\Facades\DB::raw("DATE_FORMAT(pickup_date, '%Y-%m')"));
        } elseif ($types === 'WeekEarn') {
            $query->addSelect(\Illuminate\Support\Facades\DB::raw("CONCAT('Week ', WEEK(pickup_date), ' of ', DATE_FORMAT(pickup_date, '%M %Y')) as x"))->whereMonth('pickup_date', date('m'))->groupBy(\Illuminate\Support\Facades\DB::raw("YEARWEEK(pickup_date)"));
        } else {
            $query->addSelect(\Illuminate\Support\Facades\DB::raw("pickup_date as x"))->groupBy('pickup_date');
        }
        $query->where('cab_assign', auth('tour')->user()->relation_id)
            ->where('drop_status', 1)
            ->where('amount_status', 1)
            ->whereIn('refund_status', [0, 2]);
        $data_query = $query->get();
        $month_amount = [];
        $month_days = [];
        if ($data_query) {
            foreach ($data_query as $ke => $vale) {
                $month_amount[] = $vale['y'];
                $month_days[] = $vale['x'];
            }
        }
        return view(TourPath::DASHBOARD[VIEW], compact('month_amount', 'month_days', 'orderStatus', 'dashboardData', 'withdrawalMethods'));
    }


    public function orderStatistics(Request $request)
    {
        session()->put('statistics_type', $request['type']);
        // $data = \App\Models\TourOrder::select(\Illuminate\Support\Facades\DB::raw('SUM(final_amount) as y'),\Illuminate\Support\Facades\DB::raw('pickup_date as x'))->where('cab_assign',auth('tour')->user()->relation_id)->where('drop_status',1)->where('amount_status',1)->whereIn('refund_status',[0,2])->groupBy('pickup_date')->get();

        $query = \App\Models\TourOrder::select(\Illuminate\Support\Facades\DB::raw('SUM(final_amount) as y'));
        if ($request['type'] === 'yearEarn') {
            $query->addSelect(\Illuminate\Support\Facades\DB::raw("YEAR(pickup_date) as x"))->groupBy(\Illuminate\Support\Facades\DB::raw("YEAR(pickup_date)"));
        } elseif ($request['type'] === 'MonthEarn') {
            $query->addSelect(\Illuminate\Support\Facades\DB::raw("DATE_FORMAT(pickup_date, '%Y-%m') as x"))->groupBy(\Illuminate\Support\Facades\DB::raw("DATE_FORMAT(pickup_date, '%Y-%m')"));
        } elseif ($request['type'] === 'WeekEarn') {
            $query->addSelect(\Illuminate\Support\Facades\DB::raw("CONCAT('Week ', WEEK(pickup_date), ' of ', DATE_FORMAT(pickup_date, '%M %Y')) as x"))->whereMonth('pickup_date', date('m'))->groupBy(\Illuminate\Support\Facades\DB::raw("YEARWEEK(pickup_date)"));
        } else {
            $query->addSelect(\Illuminate\Support\Facades\DB::raw("pickup_date as x"))->groupBy('pickup_date');
        }
        $query->where('cab_assign', auth('tour')->user()->relation_id)
            ->where('drop_status', 1)
            ->where('amount_status', 1)
            ->whereIn('refund_status', [0, 2]);
        $data_query = $query->get();
        $month_amount = [];
        $month_days = [];
        if ($data_query) {
            foreach ($data_query as $ke => $vale) {
                $month_amount[] = $vale['y'];
                $month_days[] = $vale['x'];
            }
        }
        return response()->json(['view' => view('all-views.tour.dashboard.chart', compact('month_amount', 'month_days'))->render()], 200);
    }

    public function profileUpdate(Request $request, $id)
    {
        $getData  = $this->tourtraveller->getFirstWhere(params: ['id' => $id]);
        if (empty($getData)) {
            return back();
        }
        $bankLists = \App\Models\WithdrawalAmountHistory::where(['type' => 'tour', 'vendor_id' => auth('tour')->user()->relation_id])->where('account_number', '!=', $getData['account_number'])->where('account_number', '!=', '')->whereNotNull('account_number')->groupBy('account_number')->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(TourPath::PROFILEUPDATE[VIEW], compact('getData', 'languages', 'defaultLanguage', 'googleMapsApiKey', 'bankLists'));
    }

    public function profileEdit(TourAndTravelRequest $request, TourAndTravelService $service, $id)
    {
        $dataArray = $service->getUpdateTourData($request);
        // dd($dataArray);
        $this->tourtraveller->update(id: $id, data: $dataArray);
        $sellers = \App\Models\Seller::where('relation_id', $id)->where('type', 'tour')->first();
        $sellers->f_name = explode(' ', $dataArray['person_name'])[0] ?? $dataArray['person_name'];
        $sellers->l_name =  explode(' ', $dataArray['person_name'])[1] ?? '';
        $sellers->phone = $dataArray['person_phone'];
        if (isset($dataArray['image']) && !empty($dataArray['image'])) {
            $sellers->image = $dataArray['image'];
        }
        $sellers->email = $dataArray['person_email'];

        $sellers->bank_name = $dataArray['bank_name'];
        $sellers->branch = $dataArray['bank_branch'];
        $sellers->ifsc = $dataArray['ifsc_code'];
        $sellers->account_no = $dataArray['account_number'];
        $sellers->holder_name = $dataArray['bank_holder_name'];

        $sellers->update_seller_status = 3;

        if (isset($dataArray['aadhaar_card_image']) && !empty($dataArray['aadhaar_card_image'])) {
            $sellers->aadhar_front_image = $dataArray['aadhaar_card_image'];
        }
        if (isset($dataArray['pan_card_image']) && !empty($dataArray['pan_card_image'])) {
            $sellers->pancard_image = $dataArray['pan_card_image'];
        }
        $sellers->save();
        // $this->translationRepo->update(request: $request, model: 'App\Models\TourAndTravel', id: $id);
        Toastr::success(translate('Tour_&_Traveller_updated_successfully'));
        return redirect()->route(TourPath::DASHBOARD[REDIRECT]);
    }
    public function AddVendorLead(Request $request)
    {
        $tourData = $this->tourvisitRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1, 'use_date_status' => 1, 'created_id' => [0, $this->relationId]], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        return view(TourPath::TOURLEADADD[VIEW], compact('tourData', 'googleMapsApiKey'));
    }
    public function SaveVendorLead(Request $request)
    {
        $request->validate([
            'platform'        => 'required',
            'person_phone'    => 'required|regex:/^\+?[0-9]{10,15}$/',
            'user_name'       => 'required|string|max:100',
            'tour_id'         => 'required|integer|exists:tour_visits,id',
            'cities_name'     => 'required|string|max:100',
            'country_name'    => 'required|string|max:100',
            'state_name'      => 'required|string|max:100',
            'pickup_location' => 'required|string|max:255',
            'pickup_lat'  => 'required|numeric|gte:-90|lte:90',
            'pickup_long' => 'required|numeric|gte:-180|lte:180',
            'startandend_date' => 'required|date|after_or_equal:today',
            'time'            => 'required|date_format:h:i A',
            'booking_package' => 'required|json',
            'amount'          => 'required|numeric|min:0',
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
            $user->verify_otp = 0;
            $user->save();
            $user_id = $user->id ?? "";
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        DB::beginTransaction();
        try {
            $leads = new TourLeads();
            $leads->tour_id = $request->tour_id ?? 0;
            $leads->package_id = 0;
            $leads->user_id = $user_id;
            $leads->amount = $request->amount;
            $leads->platform = $request->platform ?? "";
            $leads->coupon_id = $request->coupon_id;
            $leads->coupan_amount = $request->coupon_amount;
            $leads->booking_package = $request['booking_package'] ?? "[]";
            $leads->part_payment = ((!empty($request['part_payment'])) ? $request['part_payment'] : 'full');
            $leads->pickup_address = $request->pickup_location;
            $leads->pickup_date = $request->startandend_date;
            $leads->pickup_time = $request->time;
            $leads->pickup_long = $request->pickup_long;
            $leads->pickup_lat = $request->pickup_lat;
            $packages = json_decode($request->booking_package ?? '[]', true);
            $cabPackage = collect($packages)->firstWhere('type', 'cab');
            if (!$cabPackage) {
                $cabPackage = collect($packages)->firstWhere('type', 'per_head');
            }
            $userQty = ($cabPackage ? (int) $cabPackage['qty'] : 0);
            $leads->qty = ($userQty ?? 0);
            $leads->via_online = $request->amount;
            $leads->create_by_vendor = $this->relationId;
            $leads->amount_status = 0;
            $leads->status = 1;
            $leads->save();
            $tourData = TourVisits::where('id',  $request->tour_id)->first();
            $tourLeads = TourLeads::where('id',  $leads->id)->first();
            if ($tourData['use_date'] == 1 && $tourData['is_person_use'] == 0) {
                $getseats = \App\Models\TourOrder::where('tour_id', $request->tour_id)->where('amount_status', 1)->where('status', 1)->where('available_seat_cab_id', ($userQty ?? 0))->sum('qty');
                if (($userQty - $getseats) < $tourLeads['qty']) {
                    DB::rollBack();
                    Toastr::error('Currently ' . ($userQty - $getseats) . ' seats are available');
                    return redirect()->route('tour-vendor.lead.add-lead');
                }
            }

            $coupon_amount = 0;
            // $final_amount = $tourLeads['amount'] ?? 0;
            $final_amount = (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)));
            $final_amount_admin_commission =  ($tourLeads['amount'] ?? 0);
            $gst_amount = $admin_commission = 0;
            $tourstax = \App\Models\ServiceTax::find(1);
            if ($tourstax['tour_tax']) {
                $booking_package1 = json_decode($leads['booking_package'] ?? "[]", true);
                $gst_amount = collect($booking_package1)->sum('tax_price');
                $final_amount = $final_amount - $gst_amount;
            }
            if ($tourData['tour_commission']) {
                $admin_commission = ((($final_amount_admin_commission - $gst_amount) * $tourData['tour_commission']) / 100);
                $final_amount = ($final_amount - $admin_commission);
            }
            if ($final_amount < 0) {
                DB::rollBack();
                Toastr::error('Please Enter Valid amount');
                return redirect()->route('tour-vendor.lead.add-lead');
            }

            $qrData = [];
            $QR_codeimage = "";
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
            $userInfo = User::where('id', ($tourLeads['user_id'] ?? ""))->first();
            $email = $userInfo['email'];
            $contact = $userInfo['phone'];
            $url = "https://api.razorpay.com/v1/customers";
            $data = [
                "name" => $userInfo['name'],
                "email" => $email,
                "contact" => $contact,
                "fail_existing" => "0",
                "type" => "Tour Order",
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
                DB::rollBack();
                Toastr::error('failure to create client');
                return redirect()->route('tour-vendor.lead.add-lead');
            }
            if ($contact_data['id']) {
                $url = "https://api.razorpay.com/v1/payments/qr_codes";
                $data = [
                    "type" => "upi_qr",
                    "name" => "mahakal",
                    "usage" => "single_use",
                    "fixed_amount" => true,
                    "payment_amount" => (float)((($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)))) * 100,
                    "description" => "Customer Tour Order",
                    "customer_id" => $contact_data['id'],
                    "close_by" => now()->addMinutes(10)->timestamp,
                    "notes" => [
                        "purpose" => "UPI QR Code notes"
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
                    $QR_codeimage = ($qrData['image_url'] ?? "");
                } else {
                    DB::rollBack();
                    Toastr::error('failure to Create Qr code paymant');
                    return redirect()->route('tour-vendor.lead.add-lead');
                }
            }


            $user = User::where("id", $tourLeads['user_id'])->first();
            $event_booking = new TourOrder();
            $event_booking->user_id = $user['id'];
            $event_booking->tour_id = $request->tour_id;
            $event_booking->package_id = $tourLeads['package_id'];
            $event_booking->coupon_amount = $tourLeads['coupan_amount'] ?? 0;
            $event_booking->coupon_id = $tourLeads['coupon_id'] ?? '';
            $event_booking->order_amount = ($tourLeads['amount'] ?? 0);
            $event_booking->amount = (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)));
            $event_booking->qty = $tourLeads['qty'];

            $packages = json_decode($request->booking_package ?? '[]', true);
            $cabPackage = collect($packages)->firstWhere('type', 'cab');

            $event_booking->available_seat_cab_id = $cabPackage['id'] ?? 0;
            $event_booking->total_seats_cab = $userQty ?? 0;
            $event_booking->pickup_address = $tourLeads['pickup_address'];
            $event_booking->pickup_date = $tourLeads['pickup_date'];
            $event_booking->pickup_time = $tourLeads['pickup_time'];
            $event_booking->pickup_lat = $tourLeads['pickup_lat'];
            $event_booking->pickup_long = $tourLeads['pickup_long'];
            $event_booking->gst_amount = $gst_amount;
            $event_booking->admin_commission = $admin_commission;
            $event_booking->final_amount = $final_amount;
            $event_booking->payment_method = 'razor_pay';
            $event_booking->payment_platform = 'web';
            $event_booking->leads_id = $tourLeads['id'];
            $event_booking->use_date = $tourData['use_date'];
            $event_booking->part_payment = (($tourLeads['part_payment'] == 'part' || $tourLeads['part_payment'] == 'custom') ? $tourLeads['part_payment'] : 'full');

            $event_booking->traveller_id = ($tourData['created_id'] ?? 0);
            $event_booking->cab_assign = 0;
            $event_booking->booking_package = $tourLeads['booking_package'];

            $event_booking->pickup_otp = mt_rand(1000, 9999);
            $event_booking->drop_opt = mt_rand(1000, 9999);
            $event_booking->amount_status = 0;
            $event_booking->status = 1;
            // dd($event_booking);
            $event_booking->save();
            TourLeads::where('id', $tourLeads['id'])->update(['order_id' => $event_booking->id]);
            $additional_data = [
                'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                'payment_mode' => 'web',
                'leads_id' => $tourLeads['id'],
                'package_id' => $tourLeads['package_id'],
                'customer_id' => $tourLeads['user_id'],
                "order_id" => $event_booking->id,
                "tour_id" => $request->tour_id,
                "amount" => $tourLeads['amount'],
                "user_name" => $user['name'],
                "user_email" => $user['email'],
                "user_phone" => $user['phone'],
                'qrData' => $qrData,
            ];
            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = \App\Models\BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = \App\Models\Currency::find($default)->code;
            }
            $customer = User::where("id", $tourLeads['user_id'])->first();
            $payer = new \App\Library\Payer(
                $customer['f_name'] . ' ' . $customer['l_name'],
                $customer['email'],
                $customer['phone'],
                ''
            );
            if (empty($customer['phone'])) {
                DB::rollBack();
                Toastr::error(translate('please_update_your_phone_number'));
                return redirect()->route('tour-vendor.lead.add-lead');
            }

            $payment_info = new \App\Library\Payment(
                success_hook: 'digital_payment_success_custom',
                failure_hook: 'digital_payment_fail',
                currency_code: $currency_code,
                payment_method: 'razor_pay',
                payment_platform: 'web',
                payer_id: $customer['id'],
                receiver_id: '100',
                additional_data: $additional_data,
                payment_amount: (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0))),
                external_redirect_link: route('tour.tour-pay-success', [$tourData['slug'], 'lead' => ($tourLeads['id'] ?? '')]),
                attribute: 'tour_order',
                attribute_id: idate("U")
            );
            DB::commit();
            $receiver_info = new \App\Library\Receiver('receiver_name', 'example.png');
            $redirect_link = \App\Traits\Payment::generate_link($payer, $payment_info, $receiver_info);
            $parsed_url = parse_url($redirect_link);
            $query_string = $parsed_url['query'];
            parse_str($query_string, $query_params);
            $tourOrder = TourOrder::with(['Tour'])->where('id', $event_booking->id)->first();
            $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
            $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
            $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
            $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
            $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
            $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
            $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
            $message_data['payment_link'] = $redirect_link;
            $leadUpdate = TourLeads::find($tourLeads['id']);
            $leadUpdate->paymant_link = $redirect_link;
            $leadUpdate->save();
            $message_data['customer_id'] = $user['id'];
            if (!empty($QR_codeimage)) {
                $message_data['type'] = 'text-with-media';
                $qrImageContent = file_get_contents($QR_codeimage);
                $filename = 'qr_' . time() . '.png';
                $dir = storage_path('app/public/qr_codes');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                $filename = 'qr_' . time() . '.png';
                $path = $dir . '/' . $filename;
                file_put_contents($path, $qrImageContent);
                $message_data['attachment'] = asset('storage/app/public/qr_codes/' . $filename);
            } elseif ($tourOrder['Tour']['tour_image']) {
                $message_data['type'] = 'text-with-media';
                $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['tour_image'] ?? '');
            }
            $remain_amount = (($tourLeads['part_payment'] == 'custom') ? (($tourOrder['order_amount'] ?? 0) - ($request['custom_amount_payment'] ?? 0)) : (($tourOrder['part_payment'] == 'part') ?  ($tourOrder['amount'] ?? 0) : (0)));
            $message_data['remain_amount'] = webCurrencyConverter(amount: (float)$remain_amount ?? 0);
            if (($request['whatsapp_msg'] ?? 0) == 1) {
                Helpers::whatsappMessage('tour', 'Tour booking payment link', $message_data);
            }
            if (($request['itinerary_pdf_send'] ?? 0) == 1) {
                $message_data2['orderId'] = ($tourOrder['order_id'] ?? '');
                $message_data2['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
                $message_data2['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
                $message_data2['time'] = ($tourOrder['pickup_time'] ?? '');
                $message_data2['place_name'] = ($tourOrder['pickup_address'] ?? '');
                $message_data2['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
                $message_data2['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
                $message_data2['customer_id'] = $user['id'];
                $message_data2['link'] =  route('tour.tourvisit', ['id' => ($tourOrder['Tour']['slug'] ?? "")]);
                if ($tourOrder['Tour']['itineraryupload']) {
                    $message_data2['type'] = 'text-with-media';
                    $message_data2['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['itineraryupload'] ?? '');
                    Helpers::whatsappMessage('tour', 'share itinerary pdf', $message_data2);
                }
            }
            Toastr::success('Booking Success');
            return redirect()->route('tour-vendor.lead.lead-list');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage());
            return redirect()->route('tour-vendor.lead.add-lead');
        }
    }

    public function LeadVendorList(Request $request)
    {
        return view(TourPath::TOURLEADLIST[VIEW]);
    }
    public function TourLeadListFilter(Request $request)
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
        $querys = TourLeads::query();
        $querys->with(['Tour', 'TourOrder', 'userData', 'followby'])->when($searchValue, function ($qu1) use ($searchValue) {
            $qu1->Where('order_id', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            $qu1->orwhereHas('userData', function ($q2) use ($searchValue) {
                $q2->where('name', 'like', "%$searchValue%");
                $q2->orWhere('email', 'like', "%$searchValue%");
                $q2->orWhere('phone', 'like', "%$searchValue%");
            });
            $qu1->orWhereHas('Tour', function ($q3) use ($searchValue) {
                $q3->where('tour_name', 'like', "%$searchValue%");
            });
        })
            ->when(isset($searchByType), function ($query) use ($searchByType) {
                return $query->whereHas('Tour', function ($q) use ($searchByType) {
                    $q->where('use_date', $searchByType); // or ->where('use_date', $searchByType)
                });
            })->when(isset($searchByCabId) && in_array($searchByCabId, [0, 1, 2]), function ($query) use ($searchByCabId) {
                return $query->where('amount_status', $searchByCabId)->where('status', '!=', 3);
            })->when(isset($searchByCabId) && in_array($searchByCabId, [3]), function ($query) use ($searchByCabId) {
                return $query->where('status', $searchByCabId);
            })
            ->when($start_date && $end_date, function ($query4) use ($start_date, $end_date) {
                $query4->whereBetween('created_at', [$start_date, $end_date]);
            })->where('create_by_vendor', $this->relationId);
        $recordsTotal = TourLeads::with(['Tour', 'TourOrder', 'userData', 'followby'])->when(isset($searchByType), function ($query) use ($searchByType) {
            return $query->whereHas('Tour', function ($q) use ($searchByType) {
                $q->where('use_date', $searchByType); // or ->where('use_date', $searchByType)
            });
        })->when(isset($searchByCabId) && in_array($searchByCabId, [0, 1, 2]), function ($query) use ($searchByCabId) {
            return $query->where('amount_status', $searchByCabId)->where('status', '!=', 3);
        })->when(isset($searchByCabId) && in_array($searchByCabId, [3]), function ($query) use ($searchByCabId) {
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
                                                
                <input type="checkbox" id="options-toggle-' . $item['id'] . '" class="options-toggle-checkbox" style="display: none;"> 
                <div class="d-flex justify-content-center">';
            if ($item['amount_status'] != 1 && $item['status'] != 3) {
                $options .= ' <a href="' . route('tour-vendor.lead.leads-close-update', [$item['id']]) . '"
                                                     class="btn btn-icon bg-label-danger waves-effect btn-sm mr-1"
                                                     onclick="return confirm(\'Are your sure, you want to Close Ticket\');" data-toggle="tooltip" aria-label="Close"
                                                     title="Close Ticket"><i class="tio-call_cancelled">call_cancelled</i></a>';
            }

            $options .= '   <label for="options-toggle-' . $item['id'] . '" 
                        class="btn btn-icon bg-label-primary waves-effect waves-light options-toggle-label btn-sm"
                        data-toggle="tooltip" 
                        title="Show Options"
                        data-bs-original-title="Show Options">
                        <i class="tio-menu_vs">menu_vs</i>
                    </label>
                    &nbsp;';
            if ($item['amount_status'] != 1 && $item['status'] != 3) {
                $options .= '<a class="btn btn-info btn-sm btn-icon"  target="_blank" rel="noopener noreferrer" href="' . route('tour-vendor.lead.tour-admin-lead-edit', ['id' => $item['id']]) . '" data-toggle="tooltip" title="Update Lead"><i class="tio-edit"></i></a>';
            }
            $options .= '</div>
                
            <div class="options-content">
            <div class="d-flex justify-content-center gap-2">
                                            <a href="javascript:0"
                                                class="btn btn-icon bg-label-info waves-effect waves-light myactionbtn"
                                                data-leadsId="' . $item['id'] . '" onclick="followHistory(this)"
                                                data-toggle="tooltip" aria-label="Follow Up History"
                                                data-bs-original-title="Follow Up History"><i
                                                    class="tio-history"></i></a>
                                            <a href="tel:' . ($item['userData']['phone'] ?? '') . '"
                                                class="btn btn-icon bg-label-warning waves-effect waves-light myactionbtn"
                                                data-toggle="tooltip" aria-label="Call" data-bs-original-title="Call"><i
                                                    class="tio-call"></i></a>
                                                     <a data-href="' . route('tour-vendor.lead.tour-whatsapp-message', [$item['id']]) . '" class="btn btn-icon bg-label-success waves-effect waves-light myactionbtn" data-toggle="tooltip" aria-label="whatsapp" data-bs-original-title="whatsapp" onclick="sendmessgaeUser(this)"><i class="tio-whatsapp" title="whatsapp"></i>
                                                <span class="btn-status btn-sm-status btn-status-danger">' . $item['whatsapp_hit'] . '</span>
                                            </a>
                                        </div>
                                         
                    </div></div> ';


            if (($item['Tour']['use_date'] ?? "") == 1) {
                $tour_types = "Special Tour(With Date)";
            } elseif (($item['Tour']['use_date'] ?? "") == 2) {
                $tour_types =   "Daily Tour(With Address)";
            } elseif (($item['Tour']['use_date'] ?? "") == 3) {
                $tour_types =  "Daily Tour(WithOut Address)";
            } elseif (($item['Tour']['use_date'] ?? "") == 4) {
                $tour_types =  "Special Tour(Without Date)";
            } else {
                $tour_types = "Cities Tour";
            }
            $tour_info = '<span class="font-weight-bolder" data-toggle="tooltip" title="' . e($item['Tour']['tour_name'] ?? "") . '">'
                . e(Str::limit($item['Tour']['tour_name'] ?? "", 25)) .
                '</span><br>';
            $tour_info .= '<span class="font-weight-bolder" data-toggle="tooltip" title="' . e($tour_types ?? "") . '">'
                . e(Str::limit($tour_types ?? "", 20)) .
                '</span><br>';
            if (!empty($item['Tour']['package_list'] ?? "") && json_decode($item['Tour']['package_list'], true)) {
                foreach (json_decode($item['Tour']['package_list'], true) as $val) {
                    if ($val['id'] == ($item['package_id'] ?? "")) {
                        $cab_name = \App\Models\TourCab::where('id', ($val['cab_id'] ?? ""))->first();
                        $tour_info .=  ($cab_name['name'] ?? "") . '
                                        <a data-toggle="tooltip" data-html="true" title="';
                        if (!empty($val['package_id'] ?? '')) {
                            foreach ($val['package_id'] as $pn) {
                                $tour_info .= '<p>Package added : <strong>' . (\App\Models\TourPackage::where('id', ($pn ?? ''))->first()['name'] ?? '') . '</strong></p>';
                            }
                        }
                        $tour_info .= '">
                                            <i class="tio-info"></i>
                                        </a>';
                        break;
                    }
                }
            }
            $platformColor = '#6c757d';
            if ($item['platform'] === 'web') {
                $platformColor = '#007bff';
            } else if ($item['platform'] === 'app') {
                $platformColor = '#17a2b8';
            } else if ($item['platform'] === 'instagram') {
                $platformColor = '#e1306c';
            } else if ($item['platform'] === 'facebook') {
                $platformColor = '#1877f2';
            } else if ($item['platform'] === 'ads') {
                $platformColor = '#ff9800';
            } else if ($item['platform'] === 'admin') {
                $platformColor = '#28a745';
            }
            if ($item['order_id']) {
                $tour_info .= '<span class="font-weight-bolder">ID : ' . ($item['TourOrder']['order_id'] ?? "") . '</span><br>';
            }
            $tour_info .= '<span class="font-weight-bolder">' . webCurrencyConverter(amount: (float)($item['TourOrder']['order_amount'] ?? 0)) . "</span>  <span style='
                                        background-color:" . $platformColor . ";
                                        color: white;
                                        align-items: center;
                                        border-radius: 0 4px 4px 0;
                                        padding: 5px 12px;
                                    '>" . $item['platform'] . "</span>";
            /////////////
            $user_info = '<span class="font-weight-bolder">' . ($item['userData']['name'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['userData']['email'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . ($item['userData']['phone'] ?? "") . '</span><br>
            <span class="font-weight-bolder">' . date('d M,Y h:i A', strtotime($item['created_at'])) . '</span><br>';
            ///////////////////////
            $statusForm = '';
            $closeTicket = "";
            if ($item['status'] == 3) {
                $closeTicket = "Close Ticket";
            }
            if ($item['amount_status'] == 0) {
                $statusForm = 'Pending';
            } elseif ($item['amount_status'] == 1) {
                $statusForm = 'Success';
            } elseif ($item['amount_status'] == 2) {
                $statusForm = 'Failed';
            }
            return [
                'id' => $start + $key + 1,
                'platform' => $item['platform'],
                'use_info' => $user_info,
                'tour_name' => $tour_info,
                'create_by' => date('d M,Y h:i A', strtotime($item['created_at'])),
                'via_wallet' => $item['via_wallet'],
                'via_online' => $item['via_online'],
                'status' => $statusForm,
                'follow_by' => ((!empty($item['followby']['follow_by'] ?? "")) ? ($item['followby']['follow_by'] ?? "") : 'pending'),
                'next_date' => ((!empty($item['followby']['next_date'] ?? "")) ? date('d M,Y', strtotime($item['followby']['next_date'] ?? "")) : 'pending'),
                'last_date' => ((!empty($item['followby']['last_date'] ?? "")) ? date('d M,Y', strtotime($item['followby']['last_date'] ?? "")) : 'pending'),
                'option' => $options,
                'closeTicket' => $closeTicket,
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData
        ]);
    }

    public function TourLeadCloseupdate(Request $request)
    {
        $lead = TourLeads::find($request['id']);
        if ($lead) {
            $lead->status = 3;
            $lead->save();
            Toastr::success(translate('lead_ticket_close_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function TourLeadsFollow($id)
    {
        $followlist = TourFollowup::where('lead_id', $id)->get();
        if ($followlist) {
            return response()->json($followlist);
        } else {
            return response()->json([], 200);
        }
    }

    public function TourLeadsFollowUp(Request $request)
    {
        $follows = [
            'lead_id' => $request->input('lead_id'),
            'message' => $request->input('message'),
            'last_date' => $request->input('last_date'),
            'next_date' => $request->input('next_date'),
            'follow_by' => $request->input('follow_by'),
            'follow_by_id' => $request->input('follow_by_id'),
        ];
        TourFollowup::create($follows);
        Toastr::success(translate('lead_follow_up_successfully'));
        return response()->json(['success' => true], 200);;
    }

    public function TourLeadMessages(Request $request, $id)
    {
        $leads = TourLeads::where('id', $id)->first();
        $request = new Request(['id' => $leads['order_id']]);
        \App\Http\Controllers\Admin\TourVisitController::CustomerTourRemainingPay($request, true);
        $lead = TourLeads::where('id', $id)->with(['Tour'])->first();
        if ($lead) {
            $message_data = [
                'title_name' => ($lead['Tour']['tour_name'] ?? ''),
                'customer_id' => ($lead['user_id'] ?? ""),
                'final_amount' => $lead['amount'],
                'type' => 'text-with-media',
                'attachment' =>  getValidImage(path: 'storage\app\public\tour_and_travels\tour_visit' . $lead['Tour']['tour_image'], type: 'backend-product'),
                'link' => route('tour.tour-visit-id', ['id' => $lead['Tour']['slug']]),
                "payment_link" => $lead['paymant_link'] ?? "",
            ];
            Helpers::whatsappMessage('tour', 'tour_leads_message', $message_data);
            TourLeads::where('id', $id)->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
            return response()->json(['success' => true], 200);
        } else {
            Toastr::error(translate('lead_Not_found'));
            return response()->json(['success' => false], 200);
        }
    }

    public function TourLeadEditForm(Request $request)
    {
        $tourData = $this->tourvisitRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1, 'use_date_status' => 1, 'created_id' => [0, $this->relationId]], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        $getData = TourLeads::with(['userData'])->where('id', $request['id'])->first();
        if ($getData) {
            return view(TourPath::UPDATELEADADMIN[VIEW], compact('getData', 'tourData', 'googleMapsApiKey'));
        } else {
            Toastr::error(translate('lead_Not_found'));
            return back();
        }
    }
    public function TourLeadUpdateForm(Request $request)
    {
        $request->validate([
            'platform'        => 'required',
            'person_phone'    => 'required|regex:/^\+?[0-9]{10,15}$/',
            'user_name'       => 'required|string|max:100',
            'tour_id'         => 'required|integer|exists:tour_visits,id',
            'cities_name'     => 'required|string|max:100',
            'country_name'    => 'required|string|max:100',
            'state_name'      => 'required|string|max:100',
            'pickup_location' => 'required|string|max:255',
            'pickup_lat'  => 'required|numeric|gte:-90|lte:90',
            'pickup_long' => 'required|numeric|gte:-180|lte:180',
            'startandend_date' => 'required|date|after_or_equal:today',
            'time'            => 'required|date_format:h:i A',
            'booking_package' => 'required|json',
            'amount'          => 'required|numeric|min:0',
        ]);
        if (!$request['id']) {
            return back();
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
            $user->verify_otp = 0;
            $user->save();
            $user_id = $user->id ?? "";
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        DB::beginTransaction();
        try {
            $leads = TourLeads::find($request['id']);
            $leads->tour_id = $request->tour_id ?? 0;
            $leads->package_id = 0;
            $leads->user_id = $user_id;
            $leads->amount = $request->amount;
            $leads->platform = $request->platform ?? "";
            $leads->coupon_id = $request['coupon_id'] ?? 0;
            $leads->coupan_amount = $request['coupon_amount'] ?? 0;
            $leads->booking_package = $request['booking_package'] ?? "[]";
            $leads->part_payment = ((!empty($request['part_payment'])) ? $request['part_payment'] : 'full');
            $leads->pickup_address = $request->pickup_location;
            $leads->pickup_date = $request->startandend_date;
            $leads->pickup_time = $request->time;
            $leads->pickup_long = $request->pickup_long;
            $leads->pickup_lat = $request->pickup_lat;
            $packages = json_decode($request->booking_package ?? '[]', true);
            $cabPackage = collect($packages)->firstWhere('type', 'cab');
            if (!$cabPackage) {
                $cabPackage = collect($packages)->firstWhere('type', 'per_head');
            }
            $userQty = ($cabPackage ? (int) $cabPackage['qty'] : 0);
            $leads->qty = ($userQty ?? 0);
            $leads->via_online = $request->amount;
            $leads->create_by_vendor = $this->relationId;
            $leads->amount_status = 0;
            $leads->status = 1;
            $leads->save();
            $tourData = TourVisits::where('id',  $request->tour_id)->first();
            $tourLeads = TourLeads::where('id',  $leads->id)->first();
            if ($tourData['use_date'] == 1 && $tourData['is_person_use'] == 0) {
                $getseats = \App\Models\TourOrder::where('tour_id', $request->tour_id)->where('amount_status', 1)->where('status', 1)->where('available_seat_cab_id', ($userQty ?? 0))->sum('qty');
                if (($userQty - $getseats) < $tourLeads['qty']) {
                    DB::rollBack();
                    Toastr::error('Currently ' . ($userQty - $getseats) . ' seats are available');
                    return redirect()->route('tour-vendor.lead.tour-admin-lead-edit', ['id' => $request['id']]);
                }
            }
            $coupon_amount = 0;
            $final_amount = (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)));
            $final_amount_admin_commission = ($tourLeads['amount'] ?? 0);
            $gst_amount = $admin_commission = 0;
            $tourstax = \App\Models\ServiceTax::find(1);
            if ($tourstax['tour_tax']) {
                // $booking_package1 = json_decode($leads['booking_package'] ?? "[]", true);
                // $gst_amount = collect($booking_package1)->sum('tax_price');
                $gst_amount = (($final_amount * $tourstax['tour_tax']) / 100);
                $final_amount = $final_amount - $gst_amount;
            }
            if ($tourData['tour_commission']) {
                // $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                $admin_commission = ((($final_amount_admin_commission - $gst_amount) * $tourData['tour_commission']) / 100);
                $final_amount = ($final_amount - $admin_commission);
            }

            if ($final_amount < 0) {
                DB::rollBack();
                Toastr::error('Please Enter Valid amount');
                return redirect()->route('tour-vendor.lead.tour-admin-lead-edit', ['id' => $request['id']]);
            }
            $qrData = [];
            $QR_codeimage = "";
            $user = User::where("id", $tourLeads['user_id'])->first();
            if (!empty($leads->order_id ?? "")) {
                $event_booking = TourOrder::find($leads->order_id);
            } else {
                $event_booking = new TourOrder();
            }
            $event_booking->user_id = $user['id'];
            $event_booking->tour_id = $request->tour_id;
            $event_booking->package_id = $tourLeads['package_id'];
            $event_booking->coupon_amount = $tourLeads['coupan_amount'] ?? 0;
            $event_booking->coupon_id = $tourLeads['coupon_id'] ?? '';
            $event_booking->order_amount = $tourLeads['amount'] ?? 0;
            $event_booking->amount = (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)));
            $event_booking->qty = $tourLeads['qty'];

            $packages = json_decode($request->booking_package ?? '[]', true);
            $cabPackage = collect($packages)->firstWhere('type', 'cab');

            $event_booking->available_seat_cab_id = $cabPackage['id'] ?? 0;
            $event_booking->total_seats_cab = $userQty ?? 0;
            $event_booking->pickup_address = $tourLeads['pickup_address'];
            $event_booking->pickup_date = $tourLeads['pickup_date'];
            $event_booking->pickup_time = $tourLeads['pickup_time'];
            $event_booking->pickup_lat = $tourLeads['pickup_lat'];
            $event_booking->pickup_long = $tourLeads['pickup_long'];
            $event_booking->gst_amount = $gst_amount;
            $event_booking->admin_commission = $admin_commission;
            $event_booking->final_amount = $final_amount;
            $event_booking->payment_method = 'razor_pay';
            $event_booking->payment_platform = 'web';
            $event_booking->leads_id = $tourLeads['id'];
            $event_booking->use_date = $tourData['use_date'];
            $event_booking->part_payment = (($tourLeads['part_payment'] == 'part' || $tourLeads['part_payment'] == 'custom') ? $tourLeads['part_payment'] : 'full');

            $event_booking->traveller_id = ($tourData['created_id'] ?? 0);
            $event_booking->cab_assign = 0;
            $event_booking->booking_package = $tourLeads['booking_package'];

            $event_booking->pickup_otp = mt_rand(1000, 9999);
            $event_booking->drop_opt = mt_rand(1000, 9999);
            $event_booking->amount_status = 0;
            $event_booking->status = 1;
            $event_booking->save();

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
            $userInfo = User::where('id', ($tourLeads['user_id'] ?? ""))->first();
            $email = $userInfo['email'];
            $contact = $userInfo['phone'];
            $url = "https://api.razorpay.com/v1/customers";
            $data = [
                "name" => $userInfo['name'],
                "email" => $email,
                "contact" => $contact,
                "fail_existing" => "0",
                "type" => "Tour Order",
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
                DB::rollBack();
                Toastr::error('failure to create client');
                return redirect()->route('tour-vendor.lead.tour-admin-lead-edit', ['id' => $request['id']]);
            }
            if ($contact_data['id']) {
                $url = "https://api.razorpay.com/v1/payments/qr_codes";
                $data = [
                    "type" => "upi_qr",
                    "name" => "mahakal",
                    "usage" => "single_use",
                    "fixed_amount" => true,
                    "payment_amount" => (float)((($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0)))) * 100,
                    "description" => "Customer Tour Order",
                    "customer_id" => $contact_data['id'],
                    "close_by" => now()->addMinutes(10)->timestamp,
                    "notes" => [
                        "purpose" => "UPI QR Code notes"
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
                    $QR_codeimage = ($qrData['image_url'] ?? "");
                } else {
                    DB::rollBack();
                    Toastr::error('failure to Create Qr code paymant');
                    return redirect()->route('tour-vendor.lead.tour-admin-lead-edit', ['id' => $request['id']]);
                }
            }   
            \App\Models\TourLeads::where('id', $tourLeads['id'])->update(['order_id' => $event_booking->id]);
            $additional_data = [
                'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                'payment_mode' => 'web',
                'leads_id' => $tourLeads['id'],
                'package_id' => $tourLeads['package_id'],
                'customer_id' => $tourLeads['user_id'],
                "order_id" => $event_booking->id,
                "tour_id" => $request->tour_id,
                "amount" => $tourLeads['amount'],
                "user_name" => $user['name'],
                "user_email" => $user['email'],
                "user_phone" => $user['phone'],
                'qrData' => $qrData,
            ];
            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = \App\Models\BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = \App\Models\Currency::find($default)->code;
            }
            $customer = User::where("id", $tourLeads['user_id'])->first();
            $payer = new \App\Library\Payer(
                $customer['f_name'] . ' ' . $customer['l_name'],
                $customer['email'],
                $customer['phone'],
                ''
            );
            if (empty($customer['phone'])) {
                DB::rollBack();
                Toastr::error(translate('please_update_your_phone_number'));
                return redirect()->route('tour-vendor.lead.tour-admin-lead-edit', ['id' => $request['id']]);
            }

            $payment_info = new \App\Library\Payment(
                success_hook: 'digital_payment_success_custom',
                failure_hook: 'digital_payment_fail',
                currency_code: $currency_code,
                payment_method: 'razor_pay',
                payment_platform: 'web',
                payer_id: $customer['id'],
                receiver_id: '100',
                additional_data: $additional_data,
                payment_amount: (($tourLeads['part_payment'] == 'custom') ? ($request['custom_amount_payment'] ?? 0) : (($tourLeads['part_payment'] == 'part') ?  (($tourLeads['amount'] ?? 0) / 2) : ($tourLeads['amount'] ?? 0))),
                external_redirect_link: route('tour.tour-pay-success', [$tourData['slug'], 'lead' => ($tourLeads['id'] ?? '')]),
                attribute: 'tour_order',
                attribute_id: idate("U")
            );
            DB::commit();
            $receiver_info = new \App\Library\Receiver('receiver_name', 'example.png');
            $redirect_link = \App\Traits\Payment::generate_link($payer, $payment_info, $receiver_info);
            $parsed_url = parse_url($redirect_link);
            $query_string = $parsed_url['query'];
            parse_str($query_string, $query_params);

            $tourOrder = TourOrder::with(['Tour'])->where('id', $event_booking->id)->first();
            $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
            $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
            $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
            $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
            $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
            $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
            $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
            $message_data['payment_link'] = $redirect_link;
            $leads->paymant_link = $redirect_link;
            $leads->save();
            $message_data['customer_id'] = $user['id'];


            if (!empty($QR_codeimage)) {
                $message_data['type'] = 'text-with-media';
                $qrImageContent = file_get_contents($QR_codeimage);
                $dir = storage_path('app/public/qr_codes');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                $filename = 'qr_' . time() . '.png';
                $path = $dir . '/' . $filename;
                file_put_contents($path, $qrImageContent);
                $message_data['attachment'] = asset('storage/app/public/qr_codes/' . $filename);
            } elseif ($tourOrder['Tour']['tour_image']) {
                $message_data['type'] = 'text-with-media';
                $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['tour_image'] ?? '');
            }
            $remain_amount = (($tourLeads['part_payment'] == 'custom') ? (($tourOrder['order_amount'] ?? 0) - ($request['custom_amount_payment'] ?? 0)) : (($tourOrder['part_payment'] == 'part') ?  ($tourOrder['amount'] ?? 0) : (0)));
            $message_data['remain_amount'] = webCurrencyConverter(amount: (float)$remain_amount ?? 0);
            if (($request['whatsapp_msg'] ?? 0) == 1) {
                Helpers::whatsappMessage('tour', 'Tour booking payment link', $message_data);
            }
            if (($request['itinerary_pdf_send'] ?? 0) == 1) {
                $message_data2['orderId'] = ($tourOrder['order_id'] ?? '');
                $message_data2['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
                $message_data2['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
                $message_data2['time'] = ($tourOrder['pickup_time'] ?? '');
                $message_data2['place_name'] = ($tourOrder['pickup_address'] ?? '');
                $message_data2['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
                $message_data2['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
                $message_data2['customer_id'] = $user['id'];
                $message_data2['link'] =  route('tour.tourvisit', ['id' => ($tourOrder['Tour']['slug'] ?? "")]);
                if ($tourOrder['Tour']['itineraryupload']) {
                    $message_data2['type'] = 'text-with-media';
                    $message_data2['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['itineraryupload'] ?? '');
                    Helpers::whatsappMessage('tour', 'share itinerary pdf', $message_data2);
                }
            }
            Toastr::success('Booking Success');
            return redirect()->route('tour-vendor.lead.lead-list');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage());
            return redirect()->route('tour-vendor.lead.tour-admin-lead-edit', ['id' => $request['id']]);
        }
    }
    public function TourGetFormDiv(Request $request)
    {
        $tourData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $request['tour_id']], relations: ['TourPlane']);
        $html_cab_list_price = '';
        $packagesData = collect(json_decode($tourData['package_list_price'] ?? "[]", true));
        $packageIds = $packagesData->pluck('package_id')->toArray();
        $getTourLeads = TourLeads::where('id', $request['lead_id'])->first();
        if ($getTourLeads) {
            $bookingDatas = collect(json_decode($getTourLeads['booking_package'] ?? "[]", true));
            // dd($bookingDatas);
        }

        $itinerary = '';
        if (isset($tourData['TourPlane']) && count($tourData['TourPlane']) > 0) {
            foreach ($tourData['TourPlane'] as $key => $va) {
                $itinerary .= '<div class="col-md-12 mt-2">
                                            <div class="card">
                                                <div class="card-body row">
                                                    <div class="col-md-2 small font-weight-bold">' . translate('days') . ' ' . ($key + 1) . ' &nbsp;&nbsp;<i class="tio-calendar_note" style="font-size: 19px;">calendar_note</i>
                                                    </div>
                                                    <div class="col-md-10 p-0">
                                                        <div style="border: 1px solid #b8d0e5;border-radius: 4px;" class="small">
                                                            <div class="font-weight-bold" style="background: linear-gradient(90deg, #c7dffe 0%, #d8f2ff 100%); padding: 6px 10px;">
                                                                ' . htmlspecialchars($va['name']) . ' , ' . htmlspecialchars($va['time']) . '
                                                            </div>
                                                            <div class="px-2">' . $va['description'] . '
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
            }
        }
        $packages = \App\Models\TourPackage::select('id', 'name', 'type', "seats", 'title', 'hotel_type')->whereIn('id', $packageIds)->get()->keyBy('id');
        $merged = $packagesData->map(function ($item) use ($packages, $tourData) {
            $package = $packages[$item['package_id']] ?? null;
            return [
                'id'        => $item['package_id'],
                'package_id' => $item['package_id'],
                'package_name' => $package?->name,
                'package_seats' => $package?->seats,
                'package_title' => $package?->title,
                'package_hotel_type' => (($package?->type == 'hotel') ? $package?->hotel_type : $package?->name),
                'day'       => $item['day'] ?? 1,
                'per_price' => $item['per_price'] ?? 0,
                'pprice'    => $item['pprice'] ?? 0,
                'included'  => $item['included'] ?? 0,
                'type'      => $package?->type,
                "tour_use_type" => ((($tourData['is_person_use'] ?? "") == 0) && (in_array($tourData['use_date'], [1, 2, 3, 4])) ? 1 : 0),
            ];
        });
        $grouped = $merged->groupBy('type');
        $htmlOutput = '';

        if (!empty($grouped)) {
            foreach ($grouped as $type => $items) {
                $htmlOutput .= $this->renderPackageHtml($items, $type);
            }
        }

        $htmldateTime = '
        <div class="col-md-3 mb-3">
            <label for="days" class="form-label">Days</label><br>
            <span class="font-weight-bolder">' . ($tourData['number_of_day'] ?? "") . 'D/' . ($tourData['number_of_night'] ?? "") . 'N </span>
        </div>

        <div class="col-md-3 mb-3">
            <label for="cities_name" class="form-label">Cities</label>
            <input type="text" id="cities_name_min" name="cities_name" class="form-control" value="' . $tourData['cities_name'] . '" ' . (($tourData['cities_name']) ? "readonly" : "") . '>
            <input type="hidden" id="country_name_min" name="country_name" class="form-control" value="' . $tourData['country_name'] . '">
            <input type="hidden" id="cities_lat_min" name="state_name" class="form-control" value="' . $tourData['lat'] . '">
            <input type="hidden" id="cities_long_min" name="state_name" class="form-control" value="' . $tourData['long'] . '">
        </div>
            <div class="col-md-3 mb-3">
                <label for="state_name" class="form-label">State Name</label>
                <input type="text" id="state_name_min" name="state_name" class="form-control" value="' . $tourData['state_name'] . '" ' . (($tourData['state_name']) ? "readonly" : "") . '>
            </div>

            <div class="col-md-3 mb-3">
                <label for="use_date" class="form-label">Use Date</label>
                <select id="use_date_tour" class="form-control" name="use_date" disabled>
                                    <option value="0" ' . ((old('use_date', $tourData['use_date']) == 0) ? 'selected' : '') . '>Cities Tour</option>
                                    <option value="1" ' . ((old('use_date', $tourData['use_date']) == 1) ? 'selected' : '') . '>Special Tour(With Date)</option>
                                    <option value="4" ' . ((old('use_date', $tourData['use_date']) == 4) ? 'selected' : '') . '>Special Tour(Without Date)</option>
                                    <option value="2" ' . ((old('use_date', $tourData['use_date']) == 2) ? 'selected' : '') . '>Daily Tour(With Address)</option>
                                    <option value="3" ' . ((old('use_date', $tourData['use_date']) == 3) ? 'selected' : '') . '>Daily Tour(WithOut Address)</option>
                                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label for="pickup_location" class="form-label">Pickup Location</label>
                <input type="text" id="pickup_location" name="pickup_location" autocomplete="off" class="form-control getAddress_google" value="' . $tourData['pickup_location'] . '" ' . (($tourData['pickup_location']) ? "readonly" : "") . ' onkeyup="getlocations()">
                <input type="hidden" name="pickup_lat" class="form-control pickup_lat" value="' . $tourData['pickup_lat'] . '">
                <input type="hidden" name="pickup_long" class="form-control pickup_long" value="' . $tourData['pickup_long'] . '">
                <input type="hidden" value="' . $tourData['is_person_use'] . '" class="is_person_use_tour">
                <input type="hidden" value="' . $tourData['id'] . '" class="tour_ids">
                <span class="address_error_message"></span>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="startandend_date" class="form-label">Start & End Date</label>
                <input type="text" name="startandend_date" class="form-control hasDatepicker" autocomplete="off" value="' . (explode(' - ', $tourData['startandend_date'])[0] ?? '')   . '" ' . ((explode(' - ', $tourData['startandend_date'])[0] ?? '') ? "readonly" : '') . '>
            </div>
            <div class="col-md-3 mb-3">';
        if ($tourData['time_slot'] && json_decode($tourData['time_slot'], true)) {
            $htmldateTime .=  '<label for="startandend_date" class="form-label">' . translate('Time Slot') . '</label>
                                                                            <select name="time" class="form-control" onchange="$(`.pickup_time`).val($(this).val())">
                                                                                <option value="" selected disabled>Select Time Slot</option>';
            foreach (json_decode($tourData['time_slot'], true) as $vva) {
                $htmldateTime .=  '<option value="' . $vva . '">' . $vva . '</option>';
            }
            $htmldateTime .=  ' </select>';
        } else {
            $htmldateTime .=  ' <label for="startandend_date" class="form-label">' . translate('Arrival Time') . '</label>
                                        <input type="text" name="time" class="form-control pickupopen_time" value="' . ($tourData['pickup_time'] ?? '') . '" id="opentime" onkeyup="$(`.pickup_time`).val(this.value)" onchange="$(`.pickup_time`).val(this.value)" onclick="window.$timepicker.open()" autocomplete="off">';
        }
        $htmldateTime .= '
            </div>
        ';
        if (($tourData['is_person_use'] ?? "") == 1) {
            $htmldateTime .= ' <div class="col-md-3 mb-3">
                            <span class="font-weight-bold"><input type="checkbox" class="only-pickup extracharges-transport" data-id="only-pickup" data-type="Pickup" data-type1="pick" onclick="transportOption(this)">&nbsp;Only Pickup</span><br>
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-droup extracharges-transport" data-id="only-droup" data-type="Drop" data-type1="drop" onclick="transportOption(this)">&nbsp;Only Droup</span><br>
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-both extracharges-transport" data-id="only-both" data-type="Both" data-type1="both" onclick="transportOption(this)">&nbsp;Both</span><br>
                                                                    <span class="extransportPrice font-wight-bolder text-primary font-size-13"></span></div><br>';
        } else if (($tourData['is_person_use'] ?? "") == 0 && ($tourData['use_date'] == 2 || $tourData['use_date'] == 3)) {
            $htmldateTime .= '<div class="col-md-3 mb-3">
                            <span class="font-weight-bold"><input type="checkbox" class="only-pickup extracharges-transport" data-type="one_way">&nbsp;One Way</span><br>
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-droup extracharges-transport" data-type="two_way" checked>&nbsp;Two Way</span><br>
                                                       
                                                       <input type="radio" name="oneusedistance" class="out_side_div d-none" value="two_way" onclick="calculateDistance()" checked data-ex_distance="' . ($tourData['ex_distance'] ?? 0) . '">';
        }


        if (($tourData['is_person_use'] ?? "") == 1) {
            if ($tourData['cab_list_price'] && json_decode($tourData['cab_list_price'] ?? "[]", true)) {
                foreach (json_decode($tourData['cab_list_price'] ?? "[]", true) as $kper => $persons) {
                    $html_cab_list_price .= '<div class="row my-2">
                                                            <div class="col-4">
                                                                <div class="font-weight-bold">
                                                                    <span>Group of ' . $persons['min'] . '  ' . (($persons['min'] == $persons['max']) ? '' : ' - ' . $persons['max']) . ' (Per Person) </span><br>
                                                                    <a class="personMessageShow personMessageShow' . $kper . ' text-primary small d-sm-block d-none"></a>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="font-weight-bold">
                                                                    <span>' . webCurrencyConverter(amount: $persons['price'] ?? 0) . '</span><br>
                                                                    <span class="total_cab_and_perhead_price total_cab_and_perhead_price' . $kper . '">0</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-center">
                                                               <div>
                                                                    <div class="small" style="display: inline-flex;">
                                                                    <input type="number" 
                                                                            class="form-control per_head_' . $persons['id'] . ' text-center cab_qty_input cab_qty_input' . $kper . '" 
                                                                            value="0" 
                                                                            min="' . $persons['min'] . '" 
                                                                            max="' . $persons['max'] . '" 
                                                                            data-price="' . $persons['price'] . '"
                                                                            data-id="' . $persons['id'] . '"
                                                                        oninput="this.value = this.value.replace(/[^0-9]/g,\'\');updateCabTotal(' . $kper . ', this);" onchange="this.value = this.value.replace(/[^0-9]/g,\'\');updateCabTotal(' . $kper . ', this);">
                                                                   </div>
                                                                    <span class="OnepersonMessageShow OnepersonMessageShow' . $kper . ' text-danger small"></span>
                                                                </div>
                                                            </div>
                                                        </div>';
                }
            }
            $InfoVendor = [
                "html_cab_list_price" => $html_cab_list_price,
                "package_html_show" => $htmlOutput,
                "htmldateTime" => $htmldateTime,
                "is_person_use" => $tourData['is_person_use'],
                "days" => $tourData['number_of_day'] . "D" . "/" . $tourData['number_of_night'] . "N",
                "cities_name" => $tourData['cities_name'],
                "country_name" => $tourData['country_name'],
                "state_name" => $tourData['state_name'],
                "use_date" => $tourData['use_date'],
                "pickup_time" => $tourData['pickup_time'],
                "pickup_location" => $tourData['pickup_location'],
                "pickup_lat" => $tourData['pickup_lat'],
                "pickup_long" => $tourData['pickup_long'],
                "percentage_off" => $tourData['percentage_off'],
                "startandend_date" => $tourData['startandend_date'],
                "ex_transport_price" => json_decode($tourData['ex_transport_price'], true),
                "exclusion" => $tourData['exclusion'] ?? "",
                "inclusion" => $tourData['inclusion'] ?? "",
                "itinerary" => $itinerary ?? "",
            ];
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully'), "info" => $InfoVendor, 'data' => $tourData], 200);
        } elseif (($tourData['is_person_use'] ?? "") == 0) {
            $packages_price = 0;
            if (!empty($tourData['package_list_price']) && is_array(json_decode($tourData['package_list_price'], true)) && (in_array($tourData['use_date'], [1, 2, 3, 4]))) {
                foreach (json_decode($tourData['package_list_price'], true) as $plis) {
                    $packages_price += $plis['pprice'];
                }
            }
            if ($tourData['cab_list_price'] && json_decode($tourData['cab_list_price'] ?? "[]", true)) {
                foreach (json_decode($tourData['cab_list_price'] ?? "[]", true) as $kper => $cab) {

                    $price = (($cab['price'] ?? 0));
                    $cabId = $cab['cab_id'] ?? '';
                    $id    = $cab['cab_id'] ?? ''; //$cab['id'] ?? '';
                    $getCabInfo = \App\Models\TourCab::where('id', $cab['cab_id'] ?? '')->first();

                    $html_cab_list_price .= '<div class="row my-2">
                                    <div class="col-4">
                                        <div class="font-weight-bold">
                                            <span>' . $getCabInfo['name'] . ' (seats:' . $getCabInfo['seats'] . ')</span><br>
                                            <a class="personMessageShow personMessageShow' . $kper . ' text-primary small d-sm-block d-none"></a>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="font-weight-bold">
                                            <span>' . webCurrencyConverter(amount: ($price + $packages_price)) . '</span><br>
                                            <span class="total_cab_and_perhead_price total_cab_and_perhead_price' . $kper . '">0</span>
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div>
                                            <div class="small" style="display: inline-flex;">
                                                <input type="number" 
                                                    class="form-control text-center cab_id_' . $cabId . ' cab_qty_input cab_qty_input' . $kper . '" 
                                                    value="0" 
                                                    min="1" 
                                                    max="' . (($tourData['use_date'] == 1 || $tourData['use_date'] == 4 || $tourData['use_date'] == 2 || $tourData['use_date'] == 3) ? $getCabInfo['seats'] : 99) . '"
                                                    data-id="' . $id . '" 
                                                    data-id="' . $cabId . '"
                                                    data-price="' . $price . '"
                                                    data-packageincl="' . $packages_price . '"
                                                    data-seats="' . $getCabInfo['seats'] . '"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g,\'\');updateCab_Total(' . $kper . ', this);" 
                                                    onchange="this.value = this.value.replace(/[^0-9]/g,\'\');updateCab_Total(' . $kper . ', this);">
                                            </div>
                                            <span class="OnepersonMessageShow OnepersonMessageShow' . $kper . ' text-danger small"></span>
                                        </div>
                                    </div>
                                </div>';
                }
            }

            $InfoVendor = [
                "html_cab_list_price" => $html_cab_list_price,
                "package_html_show" => $htmlOutput,
                "htmldateTime" => $htmldateTime,
                "is_person_use" => $tourData['is_person_use'],
                "days" => $tourData['number_of_day'] . "D" . "/" . $tourData['number_of_night'] . "N",
                "cities_name" => $tourData['cities_name'],
                "country_name" => $tourData['country_name'],
                "state_name" => $tourData['state_name'],
                "use_date" => $tourData['use_date'],
                "pickup_time" => $tourData['pickup_time'],
                "pickup_location" => $tourData['pickup_location'],
                "pickup_lat" => $tourData['pickup_lat'],
                "pickup_long" => $tourData['pickup_long'],
                "percentage_off" => $tourData['percentage_off'],
                "startandend_date" => $tourData['startandend_date'],
                "ex_transport_price" => json_decode($tourData['ex_transport_price'] ?? [], true),
                "exclusion" => $tourData['exclusion'] ?? "",
                "inclusion" => $tourData['inclusion'] ?? "",
                "itinerary" => $itinerary ?? "",
            ];
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully'), "info" => $InfoVendor, 'data' => $tourData], 200);
        } else {
            $InfoVendor = [
                "html_cab_list_price" => $html_cab_list_price,
                "package_html_show" => $htmlOutput,
                "htmldateTime" => $htmldateTime,
                "is_person_use" => $tourData['is_person_use'],
                "days" => $tourData['number_of_day'] . "D" . "/" . $tourData['number_of_night'] . "N",
                "cities_name" => $tourData['cities_name'],
                "country_name" => $tourData['country_name'],
                "state_name" => $tourData['state_name'],
                "use_date" => $tourData['use_date'],
                "pickup_time" => $tourData['pickup_time'],
                "pickup_location" => $tourData['pickup_location'],
                "pickup_lat" => $tourData['pickup_lat'],
                "pickup_long" => $tourData['pickup_long'],
                "percentage_off" => $tourData['percentage_off'],
                "startandend_date" => $tourData['startandend_date'],
                "ex_transport_price" => json_decode($tourData['ex_transport_price'] ?? [], true),
                "exclusion" => $tourData['exclusion'] ?? "",
                "inclusion" => $tourData['inclusion'] ?? "",
                "itinerary" => $itinerary ?? "",
            ];
            return response()->json(['success' => 0, 'message' => translate('status_updated_successfully'), "info" => $InfoVendor, 'data' => $tourData], 200);
        }
    }

    function renderPackageHtml($items, $type)
    {
        $html = "<div class='col-md-12'><h5 class='mt-3'>" . ucfirst($type) . " Packages</h5>";

        foreach ($items as $kper => $value) {
            $html .= '<div class="row my-2">
                    <div class="col-4">
                        <div class="font-weight-bold">
                            <span>' . ($value['package_hotel_type'] ?? ucfirst($type)) . ' - ' . ($value['package_name']) . ' (' . ($value['package_title']) . ' , Person: ' . ($value['package_seats']) . ')</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="small font-weight-bold">
                            <span>' . webCurrencyConverter(amount: $value['pprice'] ?? 0) . '</span><br>
                            <span class="person_total_amounts_' . ($value['type'] ?? "") . '' . $kper . ' person_total_amounts_' . ($value['type'] ?? "") . '"></span>
                        </div>
                    </div>
                    <div class="col-4 text-center">                       
                        <div>
                            <div class="font-weight-bolder" style="display: inline-flex;">';
            if (($value['included'] ?? 0) == 1) {
                $html .= 'included';
            } elseif ($value['tour_use_type'] == 1) {
                $html .= 'included';
                $html .= '<input type="hidden" class="form-control text-center package_per_head_max person_per_input_' . ($value['type'] ?? "") . '' . $kper . ' person_per_input_' . ($value['type'] ?? "") . '"  value="0" min="0" data-type="' . ($value['type'] ?? "") . '" data-price="' . ($value['pprice'] ?? "") . '" data-hotel_type="' . ($value['package_hotel_type'] ?? "") . '" data-id="' . ($value['id'] ?? "") . '" data-seats="' . ($value['package_seats'] ?? "") . '" oninput="this.value = this.value.replace(/[^0-9]/g,\'\');updatepackageTotal(' . $kper . ', this)"  onchange="this.value = this.value.replace(/[^0-9]/g,\'\');updatepackageTotal(' . $kper . ', this)" ' . (($value['tour_use_type'] == 1) ? "readonly" : " ") . '>';
            } else {
                $html .= '<input type="number" class="form-control text-center other_packages_' . ($value['id'] ?? "") . ' package_per_head_max person_per_input_' . ($value['type'] ?? "") . '' . $kper . ' person_per_input_' . ($value['type'] ?? "") . '"  value="0" min="0" data-type="' . ($value['type'] ?? "") . '" data-price="' . ($value['pprice'] ?? "") . '" data-hotel_type="' . ($value['package_hotel_type'] ?? "") . '" data-id="' . ($value['id'] ?? "") . '" data-seats="' . ($value['package_seats'] ?? "") . '" oninput="this.value = this.value.replace(/[^0-9]/g,\'\');updatepackageTotal(' . $kper . ', this)"  onchange="this.value = this.value.replace(/[^0-9]/g,\'\');updatepackageTotal(' . $kper . ', this)">';
            }
            $html .= '</div>
                            <span class="OnepersonMessageShow OnepersonMessageShow' . $kper . ' text-danger small"></span>
                        </div>
                    </div>
                </div>';
        }
        $html .= "</div>";

        return $html;
    }

    public function orderPending(Request $request)
    {
        $pending_order = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('type') == 'ok') ? $request->get('searchValue') : ''), relations: ['userData', 'company', 'Tour', 'accept'], filters: ['amount_status' => 1, 'status' => [0, 1], 'pickup_status' => 0, 'drop_status' => 0, 'cab_assign_id' => 0, 'refund_status' => 0, 'accept' => 1, 'accept_user' => auth('tour')->user()->relation_id, "cancel_vendor_list" => auth('tour')->user()->relation_id], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourPath::ORDERPENDING[VIEW], compact('pending_order'));
    }

    public function orderCancel(Request $request, $id)
    {
        $getData = $this->tourorder->getFirstWhere(params: ['id' => $id, 'cab_assign' => auth('tour')->user()->relation_id]);
        if ($getData) {
            $cancel_vendor_list = json_decode($getData['cancel_vendor_list'] ?? '[]', true);
            if (!is_array($cancel_vendor_list)) {
                $cancel_vendor_list = [];
            }
            $vendorId = auth('tour')->user()->relation_id;
            if (!in_array($vendorId, $cancel_vendor_list)) {
                $cancel_vendor_list[] = (string)$vendorId;
            }
            $this->tourorder->update(id: $id, data: ['cab_assign' => 0, 'traveller_cab_id' => 0, 'traveller_driver_id' => 0, 'on_load' => 0, 'cancel_vendor_list' => json_encode($cancel_vendor_list)]);
            \App\Models\TourAndTravel::where('id', auth('tour')->user()->relation_id)->update(['cancel_order' =>  \Illuminate\Support\Facades\DB::raw('cancel_order + 1')]);
        }
        return back();
    }

    public function UserCancelOrder(Request $request)
    {
        $cancel_order = TourOrder::whereNotNull('cancel_vendor_list')
            ->where('cancel_vendor_list', '!=', '[]')
            ->where('cancel_vendor_list', '!=', '')
            ->whereRaw("JSON_CONTAINS(cancel_vendor_list, ?)", [json_encode((string) auth('tour')->user()->relation_id)])
            ->paginate(getWebConfig(name: 'pagination_limit'));
        if ($cancel_order) {
            return view(TourPath::ORDERCANCEL[VIEW], compact('cancel_order'));
        }
        Toastr::success('working');
        return back();
    }

    public function orderConfirm(Request $request)
    {
        $confirm_order = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('type') == 'ok') ? $request->get('searchValue') : ''), relations: ['userData', 'company', 'Tour'], filters: ["assign_status" => 'cab_driver_assign_not', 'amount_status' => 1, 'status' => [1, 0], 'pickup_status' => 0, 'drop_status' => 0, 'cab_assign_id' => auth('tour')->user()->relation_id, 'refund_status' => 0], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourPath::ORDERCONFIRM[VIEW], compact('confirm_order'));
    }

    public function orderAssigned(Request $request)
    {
        $confirm_order = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('type') == 'ok') ? $request->get('searchValue') : ''), relations: ['userData', 'company', 'Tour'], filters: ["assign_status" => 'cab_driver_assign', 'amount_status' => 1, 'status' => [1, 0], 'pickup_status' => 0, 'cab_assign_id' => auth('tour')->user()->relation_id, 'refund_status' => 0], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourPath::ORDERASSIGNED[VIEW], compact('confirm_order'));
    }

    public function orderPickUp(Request $request)
    {
        $pickup_order = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('type') == 'ok') ? $request->get('searchValue') : ''), relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1, 'status' => 1, 'pickup_status' => 1, 'drop_status' => 0, 'cab_assign_id' => auth('tour')->user()->relation_id, 'refund_status' => 0], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourPath::ORDERPICKUP[VIEW], compact('pickup_order'));
    }

    public function orderComplete(Request $request)
    {
        $complete_order = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('type') == 'ok') ? $request->get('searchValue') : ''), relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1, 'status' => 1, 'pickup_status' => 1, 'drop_status' => 1, 'cab_assign_id' => auth('tour')->user()->relation_id, 'refund_status' => 0], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourPath::ORDERCOMPLETE[VIEW], compact('complete_order'));
    }

    public function orderDetails(Request $request, $id)
    {
        $getData = $this->tourorder->getFirstWhere(params: ['id' => $id], relations: ['userData', 'company', 'Tour']);
        $company_list = $this->tourtraveller->getListWhere(filters: ['id' => auth('tour')->user()->relation_id, 'status' => 1, 'is_approve' => 1], dataLimit: "all");
        $cabDetails = $this->tourtravellercabRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['traveller_id' => auth('tour')->user()->relation_id, 'status' => 1], relations: ['Cabs'], dataLimit: "all");
        $travellerDetails = $this->tourtravellerdriverRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['traveller_id' => auth('tour')->user()->relation_id, 'status' => 1], dataLimit: "all");
        return view(TourPath::ORDERDETAILS[VIEW], compact('getData', 'company_list', 'cabDetails', 'travellerDetails'));
    }

    public function orderAssignAccept(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'cab_id' => 'required',
        ]);
        $check_cab = $this->tourorder->getFirstWhere(params: ['id' => $request->id, 'status' => 1, 'pickup_status' => 0, 'drop_status' => 0, 'cab_assign' => 0]);
        if ($check_cab) {
            $this->tourorder->update(id: $request->id, data: ['status' => 1, 'pickup_status' => 0, 'drop_status' => 0, 'traveller_id' => $request->cab_id, 'cab_assign' => $request->cab_id]);
            Toastr::success('Assign Cab Successfully');
        } else {
            Toastr::error('Already Assign');
        }
        return back();
    }

    public function ordercabdriverAssign(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tour_order,id',
            'traveller_cab_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $cabIds = is_string($value) ? json_decode($value, true) : $value;
                    $cabIds = is_array($cabIds) ?  $cabIds : explode(',', (string) $cabIds);

                    $currentTour = \App\Models\TourOrder::with(['Tour'])->find($request->id);
                    if (!$currentTour) return;

                    if ($currentTour['Tour']['tour_type'] == 'cities_tour') {
                        $bookingPackage = json_decode($currentTour['booking_package'], true);
                        if (is_array($bookingPackage) && $currentTour['Tour']['tour_type'] == 'cities_tour') {
                            foreach ($bookingPackage as $item) {
                                if (isset($item['type']) && ($item['type'] === 'cab' || $item['type'] === 'per_head') && isset($item['qty'])) {
                                    $qty_cities = $item['qty'];
                                    break;
                                }
                            }
                        }
                        if (($currentTour['Tour']['is_person_use'] == 0) && $qty_cities > count($cabIds)) {
                            $fail("Please select a $qty_cities Cabs.");
                        }
                    }

                    $pickupDate = $currentTour->pickup_date;
                    $days_numberdays = \App\Models\TourVisits::where('id', $currentTour->tour_id)->value('number_of_day');
                    $days_numbernight = \App\Models\TourVisits::where('id', $currentTour->tour_id)->value('number_of_night');
                    if ($days_numberdays > $days_numbernight) {
                        $days = $days_numberdays;
                    } else {
                        $days = $days_numbernight;
                    }
                    $endDate = date('Y-m-d', strtotime("+$days days", strtotime($pickupDate . ' -1 day')));

                    $currentTour->update(['drop_date' => $endDate]);

                    foreach ($cabIds as $cabId) {
                        $overlapping = \App\Models\TourOrder::where('drop_status', 0)
                            ->where('id', '!=', $request->id)
                            ->where(function ($query) use ($cabId) {
                                $query->whereJsonContains('traveller_cab_id', (string) $cabId) // JSON case
                                    ->orWhereRaw("FIND_IN_SET(?, traveller_cab_id)", [$cabId]); // Comma-separated case
                            })
                            ->where(function ($query) use ($pickupDate, $endDate) {
                                $query->whereBetween('pickup_date', [$pickupDate, $endDate])
                                    ->orWhereBetween('drop_date', [$pickupDate, $endDate])
                                    ->orWhereRaw('? BETWEEN pickup_date AND drop_date', [$pickupDate])
                                    ->orWhereRaw('? BETWEEN pickup_date AND drop_date', [$endDate]);
                            })
                            ->exists();

                        if ($overlapping) {
                            if ($currentTour['Tour']['use_date'] == 1 || $currentTour['Tour']['use_date'] == 4) {
                                $cabs_data  = \App\Models\TourCabManage::where('id', $cabId)->with(['Cabs'])->first();
                                $getcheckQty = \App\Models\TourOrder::where(function ($query) use ($cabId, $request) {
                                    $query->whereRaw("JSON_CONTAINS(traveller_cab_id, ?)", [json_encode((string) $cabId)])
                                        ->orWhere('id', $request->id);
                                }) //whereRaw("JSON_CONTAINS(traveller_cab_id, ?)", [json_encode((string) $cabId)])
                                    ->where('tour_id', $currentTour['tour_id'])
                                    ->where('pickup_status', 0)
                                    ->where('pickup_date', [$pickupDate])
                                    ->select('booking_package')
                                    ->get()
                                    ->map(function ($tourVisit) {
                                        $packages = json_decode($tourVisit->booking_package, true);
                                        $cabPackage = collect($packages)->firstWhere('type', 'cab');
                                        if (!$cabPackage) {
                                            $cabPackage = collect($packages)->firstWhere('type', 'per_head');
                                        }
                                        return $cabPackage ? (int) $cabPackage['qty'] : 0;
                                    })->sum();
                                if ($cabs_data && $cabs_data['Cabs']['seats'] > 0) {
                                    if ($getcheckQty > $cabs_data['Cabs']['seats']) {
                                        $fail("Cab Name " . $cabs_data['Cabs']['name'] . " is already booked for the given seats. " . ($getcheckQty - $cabs_data['Cabs']['seats']) . " Seats are Not Available.");
                                    }
                                } else {
                                    $fail("Cab Name " . $cabs_data['Cabs']['name'] . " is already booked for the given seats.");
                                }
                            } else {
                                $fail("Cab ID $cabId is already booked for the given date range.");
                            }
                        }
                    }
                },
            ],

            'traveller_driver_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Ensure value is a string before decoding
                    $driverIds = is_string($value) ? json_decode($value, true) : $value;
                    $driverIds = is_array($driverIds) ? $driverIds : explode(',', (string) $driverIds);

                    $currentTour = \App\Models\TourOrder::with(['Tour'])->find($request->id);
                    if (!$currentTour) return;

                    if ($currentTour['Tour']['tour_type'] == 'cities_tour') {
                        $bookingPackage = json_decode($currentTour['booking_package'], true);
                        if (is_array($bookingPackage) && $currentTour['Tour']['tour_type'] == 'cities_tour') {
                            foreach ($bookingPackage as $item) {
                                if (isset($item['type']) && ($item['type'] === 'cab' || $item['type'] === 'per_head') && isset($item['qty'])) {
                                    $qty_cities = $item['qty'];
                                    break;
                                }
                            }
                        }
                        if (($currentTour['Tour']['is_person_use'] == 0) && $qty_cities > count($driverIds)) {
                            $fail("Please select a $qty_cities Driver.");
                        }
                    }

                    $pickupDate = $currentTour->pickup_date;
                    $days_numberdays = \App\Models\TourVisits::where('id', $currentTour->tour_id)->value('number_of_day');
                    $days_numbernight = \App\Models\TourVisits::where('id', $currentTour->tour_id)->value('number_of_night');
                    if ($days_numberdays > $days_numbernight) {
                        $days = $days_numberdays;
                    } else {
                        $days = $days_numbernight;
                    }
                    $endDate = date('Y-m-d', strtotime("+$days days", strtotime($pickupDate . ' -1 day')));

                    foreach ($driverIds as $driverId) {
                        $overlapping = \App\Models\TourOrder::where('drop_status', 0)
                            ->where('id', '!=', $request->id)
                            ->where(function ($query) use ($driverId) {
                                $query->whereJsonContains('traveller_driver_id', (string) $driverId) // JSON case
                                    ->orWhereRaw("FIND_IN_SET(?, traveller_driver_id)", [$driverId]); // Comma-separated case
                            })
                            ->where(function ($query) use ($pickupDate, $endDate) {
                                $query->whereBetween('pickup_date', [$pickupDate, $endDate])
                                    ->orWhereBetween('drop_date', [$pickupDate, $endDate])
                                    ->orWhereRaw('? BETWEEN pickup_date AND drop_date', [$pickupDate])
                                    ->orWhereRaw('? BETWEEN pickup_date AND drop_date', [$endDate]);
                            })
                            ->exists();

                        if ($overlapping) {
                            if ($currentTour['Tour']['use_date'] == 1 || $currentTour['Tour']['use_date'] == 4) {
                                $cabIds = is_string($value) ? json_decode($value, true) : $value;
                                $cabIds = is_array($cabIds) ?  $cabIds : explode(',', (string) $cabIds);

                                $cabs_data  = \App\Models\TourCabManage::where('id', $cabIds[0])->with(['Cabs'])->first();
                                $getcheckQty = \App\Models\TourOrder::where(function ($query) use ($cabIds, $request) {
                                    $query->whereRaw("JSON_CONTAINS(traveller_cab_id, ?)", [json_encode((string) $cabIds[0])])
                                        ->orWhere('id', $request->id);
                                })
                                    //whereRaw("JSON_CONTAINS(traveller_cab_id, ?)", [json_encode((string) $cabIds[0])])
                                    ->where('tour_id', $currentTour['tour_id'])->where('pickup_status', 0)
                                    ->where('pickup_date', [$pickupDate])
                                    ->select('booking_package')
                                    ->get()
                                    ->map(function ($tourVisit) {
                                        $packages = json_decode($tourVisit->booking_package, true);
                                        $cabPackage = collect($packages)->firstWhere('type', 'cab');
                                        if (!$cabPackage) {
                                            $cabPackage = collect($packages)->firstWhere('type', 'per_head');
                                        }
                                        return $cabPackage ? (int) $cabPackage['qty'] : 0;
                                    })->sum();
                                if ($cabs_data && $cabs_data['Cabs']['seats'] > 0) {
                                    if ($getcheckQty > $cabs_data['Cabs']['seats']) {
                                        $fail("This Cab is full and Driver Already Assign.");
                                        $fail("Driver ID $driverId is already booked for the given seats. Only " . ($getcheckQty - $cabs_data['Cabs']['seats']) . " seats are available.");
                                    }
                                } else {
                                    $fail("Driver ID $driverId is already booked for the given seats. Only " . ($getcheckQty - $cabs_data['Cabs']['seats']) . " seats are available.");
                                }
                            } else {
                                $fail("Driver ID $driverId is already booked for the given date range.");
                            }
                        }
                    }
                },
            ],
        ]);
        \App\Models\TourOrder::where('id', $request->id)->update(['traveller_cab_id' => ($request->traveller_cab_id), 'traveller_driver_id' => ($request->traveller_driver_id)]);

        $tourOrder = \App\Models\TourOrder::where('id', $request->id)->with(['Tour', 'Driver', 'CabsManage'])->withDriverInfo($request->id)->first();

        if ($tourOrder['driver_data'] && json_decode($tourOrder['driver_data'], true)) {
            foreach (json_decode($tourOrder['driver_data'], true) as $kk => $infos) {
                $message_data['driver_name'] = ($infos['name'] ?? ''); //($tourOrder['Driver']['name'] ?? '');
                $message_data['driver_number'] = "+91" . ($infos['phone'] ?? ''); //($tourOrder['Driver']['phone'] ?? '');
                $message_data['vehicle_name'] = (json_decode($tourOrder['Cabs_data'] ?? '[]', true)[$kk]['cab_name'] ?? ''); //($tourOrder['CabsManage']['Cabs']['name'] ?? '');
                $message_data['vehicle_number'] = (json_decode($tourOrder['Cabs_data'] ?? '[]', true)[$kk]['reg_number'] ?? ''); //($tourOrder['CabsManage']['reg_number'] ?? '');
                $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
                $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
                $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
                $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
                $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
                $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
                $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
                $message_data['customer_id'] = $tourOrder['user_id'];
                if ($tourOrder['Tour']['tour_image']) {
                    $message_data['type'] = 'text-with-media';
                    $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['tour_image'] ?? '');
                }
                $remain_amount = ((!empty($tourOrder['part_payment']) && $tourOrder['part_payment'] == 'part') ? $tourOrder['amount'] : 0);
                $message_data['remain_amount'] = webCurrencyConverter(amount: (float)$remain_amount ?? 0);
                Helpers::whatsappMessageVendorSend('tour', 'driver_reminder', $message_data);
            }
        }

        Toastr::success('Assign Cab Successfully');
        return back();
    }

    public function orderReminderMessage(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:tour_order,id',
        ]);
        $tourOrder = \App\Models\TourOrder::where('id', $request->id)->with(['Tour', 'Driver', 'CabsManage'])->withDriverInfo($request->id)->first();

        $driverNames = '';
        $driverNumber = '';
        $VehicelsNames = '';
        $VehicelsNumber = '';
        $pq = 1;
        if ($tourOrder['driver_data'] && json_decode($tourOrder['driver_data'], true)) {
            foreach (json_decode($tourOrder['driver_data'], true) as $kk => $infos) {
                $driverNames .= $pq . ")" . ($infos['name'] ?? '') . "  ";
                $driverNumber .= $pq . ")" . "+91" . ($infos['phone'] ?? '') . "  ";
                $VehicelsNames .= $pq . ")" . (json_decode($tourOrder['Cabs_data'] ?? '[]', true)[$kk]['cab_name'] ?? '') . "  ";
                $VehicelsNumber .= $pq . ")" . (json_decode($tourOrder['Cabs_data'] ?? '[]', true)[$kk]['reg_number'] ?? '') . "  ";
                $pq++;
            }
        }
        $message_data['driver_name'] = $driverNames; //($tourOrder['Driver']['name'] ?? '');
        $message_data['driver_number'] = $driverNumber; //($tourOrder['Driver']['phone'] ?? '');
        $message_data['vehicle_name'] = $VehicelsNames; //($tourOrder['CabsManage']['Cabs']['name'] ?? '');
        $message_data['vehicle_number'] = $VehicelsNumber; //($tourOrder['CabsManage']['reg_number'] ?? '');

        $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
        $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
        $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
        $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
        $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
        $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
        $message_data['final_amount'] = webCurrencyConverter(amount: (float)($tourOrder['amount'] + $tourOrder['coupon_amount']) ?? 0);
        $message_data['customer_id'] = $tourOrder['user_id'];
        Helpers::whatsappMessage('tour', 'Reminder of tour date', $message_data);
        Toastr::success(translate('User_Reminder_sent_successfully'));
        return back();
    }
    public function addTour(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $cab_list = \App\Models\TourCab::where('status', 1)->orderBy('id', 'desc')->get();
        $package_list = \App\Models\TourPackage::where('status', 1)->orderBy('id', 'desc')->get();
        $typeList = $this->tourtypeRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        $travelar_list = \App\Models\TourAndTravel::where('status', 1)->where('id', auth('tour')->user()->relation_id)->where('is_approve', 1)->orderBy('id', 'desc')->get();
        return view(TourPath::ADDTOUR[VIEW], compact('googleMapsApiKey', 'languages', 'defaultLanguage', 'cab_list', 'package_list', 'typeList', 'travelar_list'));
    }

    public function tourSave(Request $request, TourVisitService $service)
    {
        $step = $request->input('step', 1);
        $rules = $this->getTourVisitRules($step, $request);
        $messages = [
            'created_id.required' => 'Please provide Traveller Id / Your Profile Pending,Hold.',
            'created_id.numeric'  => 'Created ID must be a number.',
            'created_id.exists'   => 'The selected Created ID does not exist in Tour & Travel records.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors(), 'step' => $step], 422);
        }
        $dataArray = $service->getTourVisitData($request);
        if ($request->has('step') == 1 && (($request['id'] ?? "") == '')) {
            $insert = $this->tourvisitRepo->add(data: $dataArray);
            $id_tour = $insert->id;
            $this->translationRepo->add(request: $request, model: 'App\Models\TourVisits', id: $insert->id);
        } else {
            $insert = $this->tourvisitRepo->update(id: $request['id'], data: $dataArray);
            $id_tour = $request['id'];
            if ($request['step'] == 1) {
                $this->translationRepo->update(request: $request, model: 'App\Models\TourVisits', id: $request['id']);
            }
        }
        return response()->json(['success' => true, 'message' => 'Step ' . $request['step'] . ' data saved successfully', 'tour_id' => $id_tour]);
        // Toastr::success(translate('Tour_Visit_added_successfully'));
        // return redirect()->route(TourPath::TOURLIST[REDIRECT]);
    }

    public function tourList(Request $request)
    {
        $getDatalist = $this->tourvisitRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['created_id' => [auth('tour')->user()->relation_id, 0]], dataLimit: getWebConfig(name: 'pagination_limit'));
        if (!empty($getDatalist)) {
            foreach ($getDatalist as $key => $value) {
                $getcheckbox  = \App\Models\TourOrderAccept::where('traveller_id', auth('tour')->user()->relation_id)->where('tour_id', $value['id'])->first();
                $getDatalist[$key]['accept_type'] = $getcheckbox['status'] ?? 0;
            }
        }
        return view(TourPath::TOURLIST[VIEW], compact('getDatalist'));
    }

    public function tourView(Request $request)
    {
        $getData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $request['id']]);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $cab_list = \App\Models\TourCab::where('status', 1)->orderBy('id', 'desc')->get();
        $package_list = \App\Models\TourPackage::where('status', 1)->orderBy('id', 'desc')->get();
        $typeList = $this->tourtypeRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        $travelar_list = \App\Models\TourAndTravel::where('status', 1)->where('id', auth('tour')->user()->relation_id)->where('is_approve', 1)->orderBy('id', 'desc')->get();
        return view(TourPath::TOURVIEW[VIEW], compact('getData', 'languages', 'defaultLanguage', 'typeList', 'cab_list', 'package_list', 'googleMapsApiKey', 'travelar_list'));
    }

    public function tourUpdate(Request $request)
    {
        $getData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $request['id']]);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $cab_list = \App\Models\TourCab::where('status', 1)->orderBy('id', 'desc')->get();
        $package_list = \App\Models\TourPackage::where('status', 1)->orderBy('id', 'desc')->get();
        $typeList = $this->tourtypeRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['status' => 1], dataLimit: "all");
        $googleMapsApiKey = config('services.google_maps.api_key');
        $travelar_list = \App\Models\TourAndTravel::where('status', 1)->where('id', auth('tour')->user()->relation_id)->where('is_approve', 1)->orderBy('id', 'desc')->get();
        return view(TourPath::TOURUPDATE[VIEW], compact('getData', 'googleMapsApiKey', 'languages', 'defaultLanguage', 'cab_list', 'package_list', 'typeList', 'travelar_list'));
    }

    public function tourImgDelete($id, $name)
    {
        $getData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $id, 'created_id' => auth('tour')->user()->relation_id]);
        if (!empty($getData)) {
            $dataArray = [];
            if (!empty($getData['image']) && json_decode($getData['image'])) {
                foreach (json_decode($getData['image'], true) as $key => $value) {
                    if ($name == $value) {
                        if (Storage::disk('public')->exists('tour_and_travels/tour_visit/' . $name)) {
                            Storage::disk('public')->delete('tour_and_travels/tour_visit/' . $name);
                        }
                    } else {
                        $dataArray[] = $value;
                    }
                }
            }
            $this->tourvisitRepo->update(id: $id, data: ["image" => json_encode($dataArray)]);
            Toastr::success(translate('Tour_Visit_image_deleted_successfully'));
        } else {
            Toastr::error(translate('Travel_tour_visit_image_will_be_deleted_by_administrator_only'));
        }
        return back();
    }

    public function tourEdit(Request $request, TourVisitService $service)
    {
        $step = $request->input('step', 1);
        $rules = $this->getTourVisitRules($step, $request);
        $messages = [
            'created_id.required' => 'Please provide Traveller Id / Your Profile Pending,Hold.',
            'created_id.numeric'  => 'Created ID must be a number.',
            'created_id.exists'   => 'The selected Created ID does not exist in Tour & Travel records.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors(), 'step' => $step], 422);
        }
        $getData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $request['id'], 'created_id' => auth('tour')->user()->relation_id]);
        if (!empty($getData)) {
            $dataArray = $service->getUpdateTourData($request, $getData);
            $this->tourvisitRepo->update(id: $request['id'], data: $dataArray);
            if ($request['step'] == 1) {
                $this->translationRepo->update(request: $request, model: 'App\Models\TourVisits', id: $request['id']);
            }
            // Toastr::success(translate('Tour_Visit_updated_successfully'));
            $messages = 'Step ' . $request['step'] . ' data saved successfully';
        } else {
            $messages = translate('Travel_tour_visit_will_be_updated_by_administrator_only');
        }
        return response()->json(['success' => true, 'message' => $messages, 'tour_id' => $request['id']]);
        // return redirect()->route(TourPath::TOURLIST[REDIRECT]);
    }
    public function tourDelete(Request $request, TourVisitService $service)
    {
        $getData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $request['id'], 'status' => 0, 'created_id' => auth('tour')->user()->relation_id]);
        if (!empty($getData)) {
            $service->removeimages($getData);
            $this->tourvisitRepo->delete(params: ['id' => $request['id']]);
            Toastr::success(translate('Tour_visit_Deleted_successfully'));
            return back();
        } else {
            Toastr::error(translate('Travel_tour_visit_will_be_deleted_by_administrator_only'));
            return back();
        }
    }

    public function tourDetails(Request $request, $id)
    {
        $name = 'null';
        $getData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $id]);
        $order_list = \App\Models\TourOrder::where('tour_id', $getData['id'])->where('traveller_id', auth('tour')->user()->relation_id)->where('status', '!=', 2)->with(['userData', 'company'])->paginate(10, ['*'], 'page1');
        $refund_list = \App\Models\TourOrder::where('tour_id', $getData['id'])->where('traveller_id', auth('tour')->user()->relation_id)->where('status', 2)->with(['userData', 'company'])->paginate(10, ['*'], 'page2');
        $tour_reviews = \App\Models\TourReviews::where('tour_id', $getData['id'])->with(['userData'])->paginate(10, ['*'], 'page3');

        return view(TourPath::TOUROVERVIEW[VIEW], compact('name', 'getData', 'order_list', 'refund_list', 'tour_reviews'));
    }

    public function tourVisit(Request $request)
    {
        $getData = $this->tourvisitplac->getListWhere(orderBy: ['id' => 'desc'], filters: ['tour_visit_id' => $request['id']], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $tour_visit_id = $request['id'];
        return view(TourPath::TOURVISITLIST[VIEW], compact('getData', 'languages', 'defaultLanguage', 'tour_visit_id'));
    }

    public function tourVisitStore(TourVisitPlaceRequest $request, TourVisitService $service)
    {
        $getData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $request->tour_visit_id, 'created_id' => auth('tour')->user()->relation_id]);
        if ($getData) {
            $dataArray = $service->getTourVisitPlace($request);
            $insert = $this->tourvisitplac->add(data: $dataArray);
            $this->translationRepo->add(request: $request, model: 'App\Models\TourVisitPlace', id: $insert->id);
            Toastr::success(translate('Tour_Visit_place_added_successfully'));
        } else {
            Toastr::error(translate('Travel_tour_site_will_be_created_by_administrator_only'));
        }
        return redirect()->route(TourPath::TOURVISITLIST[REDIRECT], [$request->tour_visit_id]);
    }

    public function tourVisitUpdate(Request $request)
    {
        $getData = $this->tourvisitplac->getFirstWhere(params: ['id' => $request['id']], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(TourPath::TOURVISITUPDATE[VIEW], compact('getData', 'languages', 'defaultLanguage'));
    }

    public function tourVisitEdit(TourVisitPlaceRequest $request, TourVisitService $service)
    {
        $getData = $this->tourvisitRepo->getFirstWhere(params: ['id' => $request->tour_visit_id, 'created_id' => auth('tour')->user()->relation_id]);
        if ($getData) {
            $old_data = $this->tourvisitplac->getFirstWhere(params: ['id' => $request->id]);
            $dataArray = $service->getTourVisitPlaceupdate($request, $old_data);
            $this->tourvisitplac->update(id: $request['id'], data: $dataArray);
            $this->translationRepo->update(request: $request, model: 'App\Models\TourVisitPlace', id: $request['id']);
            Toastr::success(translate('Tour_Visit_place_updated_successfully'));
        } else {
            Toastr::error(translate('Travel_tour_site_will_be_updated_by_administrator_only'));
        }
        return redirect()->route(TourPath::TOURVISITLIST[REDIRECT], [$request->tour_visit_id]);
    }

    public function TourAccept(Request $request)
    {
        $getData = \App\Models\TourOrderAccept::where('traveller_id', auth('tour')->user()->relation_id)->where('tour_id', $request->tour_id)->first();
        $checkOrder = \App\Models\TourOrder::where('cab_assign', auth('tour')->user()->relation_id)->where('tour_id', $request->tour_id)->where('drop_status', 0)->first();
        // if ($checkOrder) {
        //     return response()->json(['success' => 0, 'message' => translate('There_are_still_some_orders_left_on_this_tour'), 'data' => []], 200);
        // } else {
        $Pending_tour_check =  \App\Models\TourOrder::whereIn('status', [1, 0])->where(['refund_status' => 0, 'pickup_status' => 0, 'amount_status' => 1, 'drop_status' => 0, 'cab_assign' => 0])
            ->where('pickup_date', '>', \Carbon\Carbon::today()->toDateString())
            ->where('tour_id', $request->tour_id)
            ->whereHas('accept', function ($query) {
                $query->where('tour_order_accept.status', 1);
            })->withCabOrderCheck(auth('tour')->user()->relation_id)->with(['accept'])->count();
        if (($request->status ?? 0) == 0 && $Pending_tour_check > 0) {
            return response()->json(['success' => 0, 'message' => translate('All pending orders for this tour must be confirmed first'), 'data' => []], 200);
        } else {
            if (!empty($getData)) {
                $saveData = \App\Models\TourOrderAccept::find($getData['id']);
            } else {
                $saveData = new \App\Models\TourOrderAccept();
            }
            $saveData->tour_id = $request->tour_id;
            $saveData->traveller_id = auth('tour')->user()->relation_id;
            $saveData->status = $request->status;
            $saveData->save();
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully'), 'data' => $saveData], 200);
        }
        // }
    }

    public function tourVisitImgDelete($id, $name)
    {
        $getData = $this->tourvisitplac->getFirstWhere(params: ['id' => $id], relations: ['TourVisit']);
        if (!empty($getData) && !empty($getData['TourVisit'][0] ?? '') && $getData['TourVisit'][0]['created_id'] == auth('tour')->user()->relation_id) {
            $dataArray = [];
            if (!empty($getData['images']) && json_decode($getData['images'])) {
                foreach (json_decode($getData['images'], true) as $key => $value) {
                    if ($name == $value) {
                        if (Storage::disk('public')->exists('tour_and_travels/tour_visit_place/' . $name)) {
                            Storage::disk('public')->delete('tour_and_travels/tour_visit_place/' . $name);
                        }
                    } else {
                        $dataArray[] = $value;
                    }
                }
            }
            $this->tourvisitplac->update(id: $id, data: ["images" => json_encode($dataArray)]);
            Toastr::success(translate('Tour_Visit_place_deleted_successfully'));
        } else {
            Toastr::error(translate('Travel_tour_site_will_be_deleted_by_administrator_only'));
        }
        return back();
    }

    public function tourVisitDelete(Request $request, TourVisitService $service)
    {
        $getData = $this->tourvisitplac->getFirstWhere(params: ['id' => $request->id], relations: ['TourVisit']);
        if (!empty($getData) && !empty($getData['TourVisit'][0] ?? '') && $getData['TourVisit'][0]['created_id'] == auth('tour')->user()->relation_id) {
            $old_data = $this->tourvisitplac->getFirstWhere(params: ['id' => $request->id]);
            $service->removeplaceimages($old_data);
            $this->tourvisitplac->delete(params: ['id' => $request->id]);
            $this->translationRepo->delete(model: 'App\Models\TourVisitPlace', id: $request->id);
            Toastr::success(translate('Tour_visit_deleted_successfully'));
        } else {
            Toastr::error(translate('Travel_tour_site_will_be_deleted_by_administrator_only'));
        }
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function CabList(Request $request)
    {
        $getData = $this->tourtravellercabRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['traveller_id' => auth('tour')->user()->relation_id], relations: ['Cabs'], dataLimit: getWebConfig(name: 'pagination_limit'));
        $cab_list = $this->tourcabRepo->getListWhere(orderBy: ['id' => 'desc'], dataLimit: "all");
        return view(TourPath::CABLIST[VIEW], compact('cab_list', 'getData'));
    }

    public function CabStore(CabRequest $request)
    {
        $dataArray = [
            "traveller_id" => auth('tour')->user()->relation_id,
            "cab_id" => $request['cab_id'],
            "model_number" => $request['model_number'],
            "reg_number" => $request['reg_number'],
            "status" => 0,
            "fuel_type" => $request['fuel_type'],
            "image" => json_encode(['']),
        ];
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $images1 = [];
            if (is_array($imageFile)) {
                foreach ($imageFile as $img) {
                    $filename = time() . '-tourcab-' . $img->getClientOriginalName();
                    $img->storeAs('public/tour_and_travels/tour_traveller_cab', $filename);
                    $images1[] = $filename;
                }
            } else {
                $filename = time() . '-tourcab-' . $imageFile->getClientOriginalName();
                $imageFile->storeAs('public/tour_and_travels/tour_traveller_cab', $filename);
                $images1[] = $filename;
            }

            $dataArray['image'] = json_encode($images1);
        }
        $this->tourtravellercabRepo->add(data: $dataArray);
        return back();
    }

    public function CabStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourtravellercabRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    public function CabUpdate(Request $request)
    {
        $traveller_data =  $this->tourtravellercabRepo->getFirstWhere(params: ['id' => $request['id']]);
        $cab_list = $this->tourcabRepo->getListWhere(orderBy: ['id' => 'desc'], dataLimit: "all");
        return view(TourPath::CABUPDATE[VIEW], compact('cab_list', 'traveller_data'));
    }

    public function CabRemoveImage(Request $request, $id, $name)
    {
        $gallery_list = $this->tourtravellercabRepo->getFirstWhere(params: ['id' => $id]);
        if ($gallery_list) {
            if (Storage::disk('public')->exists('/tour_and_travels/tour_traveller_cab/' . $name)) {
                Storage::disk('public')->delete('/tour_and_travels/tour_traveller_cab/' . $name);
            }
            $dataImage = json_decode($gallery_list['image'] ?? "['']", true);
            if ($dataImage) {
                foreach ($dataImage as $index => $image) {
                    if ($image == $name) {
                        unset($dataImage[$index]);
                        break;
                    }
                }
                $dataImage = array_values($dataImage);
            }
            $array = ['image' => json_encode($dataImage)];
            $this->tourtravellercabRepo->update(id: $id, data: $array);
            Toastr::success(translate('image_Deleted_successfully'));
        } else {
            Toastr::success(translate('image_Deleted_Failed'));
        }
        return redirect()->route('tour-vendor.tour_cab_management.cab-update', ['id' => $id]);
    }

    public function CabEdit(CabRequest $request)
    {
        $traveller_data =  $this->tourtravellercabRepo->getFirstWhere(params: ['id' => $request['id']]);
        if (empty($traveller_data)) {
            return back();
        }
        $dataArray = [
            "cab_id" => $request['cab_id'],
            "model_number" => $request['model_number'],
            "reg_number" => $request['reg_number'],
            "fuel_type" => $request['fuel_type'],
        ];
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $images1 = json_decode($traveller_data['image'] ?? "['']", true);
            if (is_array($imageFile)) {
                foreach ($imageFile as $img) {
                    $filename = time() . '-tourcab-' . $img->getClientOriginalName();
                    $img->storeAs('public/tour_and_travels/tour_traveller_cab', $filename);
                    $images1[] = $filename;
                }
            } else {
                $filename = time() . '-tourcab-' . $imageFile->getClientOriginalName();
                $imageFile->storeAs('public/tour_and_travels/tour_traveller_cab', $filename);
                $images1[] = $filename;
            }

            $dataArray['image'] = json_encode($images1);
        }
        $this->tourtravellercabRepo->update(id: $request['id'], data: $dataArray);
        Toastr::success(translate('Traveller_cab_Updated_successfully'));
        return redirect()->route(TourPath::CABLIST[REDIRECT]);
    }
    public function CabTravellerDelete(Request $request)
    {
        $old_data = $this->tourtravellercabRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_cab/' . $old_data['image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_cab/' . $old_data['image']);
            }
            $this->tourtravellercabRepo->delete(params: ['id' => $request['id']]);
            Toastr::success(translate('Traveller_cab_Deleted_successfully'));
            return response()->json(['success' => 1, 'message' => translate('Traveller_cab_Deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Traveller_cab_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Traveller_cab_Deleted_Failed')], 400);
        }
    }

    public function CabDriverList(Request $request)
    {
        $getData = $this->tourtravellerdriverRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['traveller_id' => auth('tour')->user()->relation_id], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourPath::DRIVERLIST[VIEW], compact('getData'));
    }

    public function DriverStore(DriverRequest $request)
    {
        $dataArray = [
            "traveller_id" => auth('tour')->user()->relation_id,
            "name" => $request['name'],
            "phone" => $request['phone'],
            "email" => ($request['email'] ?? ""),
            "gender" => $request['gender'],
            "dob" => $request['dob'],
            "year_ex" => $request['year_ex'],
            "license_number" => $request['license_number'],
            "pan_number" => $request['pan_number'],
            "aadhar_number" => $request['aadhar_number'],
            "status" => 0,
        ];
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $dataArray['image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['image']);
        }
        if ($request->hasFile('license_image')) {
            $imageFile = $request->file('license_image');
            $dataArray['license_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['license_image']);
        }
        if ($request->hasFile('pan_image')) {
            $imageFile = $request->file('pan_image');
            $dataArray['pan_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['pan_image']);
        }
        if ($request->hasFile('aadhar_image')) {
            $imageFile = $request->file('aadhar_image');
            $dataArray['aadhar_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['aadhar_image']);
        }
        $this->tourtravellerdriverRepo->add(data: $dataArray);
        return back();
    }

    public function DriverStatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->tourtravellerdriverRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function DriverDetele(Request $request)
    {
        $old_data = $this->tourtravellerdriverRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['image']);
            }
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['license_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['license_image']);
            }
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image']);
            }
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image']);
            }
            $this->tourtravellerdriverRepo->delete(params: ['id' => $request['id']]);
            Toastr::success(translate('Traveller_cab_Deleted_successfully'));
            return response()->json(['success' => 1, 'message' => translate('Traveller_cab_Deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Traveller_cab_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Traveller_cab_Deleted_Failed')], 400);
        }
    }

    public function DriverUpdate(Request $request)
    {
        $getData = $this->tourtravellerdriverRepo->getFirstWhere(params: ['id' => $request['id']]);
        return view(TourPath::DRIVERUPDATE[VIEW], compact('getData'));
    }
    public function DriverEdit(DriverRequest $request)
    {
        $old_data = $this->tourtravellerdriverRepo->getFirstWhere(params: ['id' => $request['id']]);
        if (empty($old_data)) {
            return back();
        }
        $dataArray = [
            "name" => $request['name'],
            "phone" => $request['phone'],
            "email" => ($request['email'] ?? ""),
            "gender" => $request['gender'],
            "dob" => $request['dob'],
            "year_ex" => $request['year_ex'],
            "license_number" => $request['license_number'],
            "pan_number" => $request['pan_number'],
            "aadhar_number" => $request['aadhar_number'],
        ];
        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['image']);
            }
            $imageFile = $request->file('image');
            $dataArray['image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['image']);
        }
        if ($request->hasFile('license_image')) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['license_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['license_image']);
            }
            $imageFile = $request->file('license_image');
            $dataArray['license_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['license_image']);
        }
        if ($request->hasFile('pan_image')) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image']);
            }
            $imageFile = $request->file('pan_image');
            $dataArray['pan_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['pan_image']);
        }
        if ($request->hasFile('aadhar_image')) {
            if (Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image'])) {
                Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image']);
            }
            $imageFile = $request->file('aadhar_image');
            $dataArray['aadhar_image'] = time() . '-tourdriver' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/tour_and_travels/tour_traveller_driver', $dataArray['aadhar_image']);
        }
        $this->tourtravellerdriverRepo->update(id: $request['id'], data: $dataArray);
        Toastr::success(translate('Traveller_driver_Updated_successfully'));
        return redirect()->route(TourPath::DRIVERLIST[REDIRECT]);
    }

    public function withdrawRequests(Request $request)
    {
        $vendorId = auth('tour')->user()->relation_id;
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['vendor_id' => $vendorId, 'type' => "tour"])->with(['Tour', 'TourVisit'])->paginate(10, ['*'], 'page');
        return view(TourPath::WITHDRAW[VIEW], compact('withdrawRequests'));
    }

    public function withdrawRequestadd(Request $request)
    {
        $vendorId = auth('tour')->id();
        // $withdrawMethod = \App\Models\WithdrawalMethod::where('id',$request['withdraw_method'])->first;
        // $wallet = $this->vendorWalletRepo->getFirstWhere(params:['seller_id'=> auth('tour')->id()]);
        // if (($wallet['total_earning']) >= currencyConverter($request['amount']) && $request['amount'] > 1) {
        //     $this->withdrawRequestRepo->add($this->withdrawRequestService->getWithdrawRequestData(
        //         withdrawMethod:$withdrawMethod,
        //         request:$request,
        //         addedBy: 'vendor',
        //         vendorId: $vendorId
        //     ));
        //     $totalEarning = $wallet['total_earning'] - currencyConverter($request['amount']);
        //     $pendingWithdraw = $wallet['pending_withdraw'] + currencyConverter($request['amount']);
        //     $this->vendorWalletRepo->update(
        //         id:$wallet['id'],
        //         data: $this->vendorWalletService->getVendorWalletData(totalEarning:$totalEarning,pendingWithdraw:$pendingWithdraw)
        //     );
        //     Toastr::success(translate('withdraw_request_has_been_sent'));
        // }else{
        //     Toastr::error(translate('invalid_request').'!');
        // }
        return redirect()->back();
    }


    public function TourSupportTicket(Request $request)
    {
        $vendorId = auth('tour')->user()->relation_id;
        $support_list = \App\Models\VendorSupportTicket::where(['created_by' => 'vendor', 'type' => 'tour'])->get();
        $message_list = \App\Models\VendorSupportTicketConv::where(['created_by' => 'vendor', 'type' => 'tour', 'vendor_id' => $vendorId])
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->with(['Tour'])->paginate(10, ['*'], 'page');

        return view(TourPath::INBOX[VIEW], compact('message_list', 'support_list'));
    }

    public function TourSupportTicketStore(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:vendor_support_tickets,id',
            'created_by' => 'required|in:admin,vendor',
            'type' => 'required|in:tour',
            'query_title' => 'required',
            'message' => 'required',
        ]);

        $save_ticket = new \App\Models\VendorSupportTicketConv();
        $save_ticket->ticket_id = $request->ticket_id;
        $save_ticket->created_by = $request->created_by;
        $save_ticket->type = $request->type;
        $save_ticket->vendor_id = auth('tour')->user()->relation_id;
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

    public function TourSupportTicketStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:vendor_support_tickets_conv,id',
        ]);
        $ticket_his = \App\Models\VendorSupportTicketConv::find($request->id);
        $ticket_his->status = $request->get('status', 'close');
        $ticket_his->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function TourSupportTicketView(Request $request)
    {

        $supportTicket = \App\Models\VendorSupportTicketConv::with(['Tour', 'conversations'])->find($request->id);
        \App\Models\VendorSupportTicketConvHis::where('ticket_issue_id', $request->id)->update(['read_user_status' => 1]);
        return view(TourPath::INBOXVIEW[VIEW], compact('supportTicket'));
    }

    public function TourSupportTicketReplay(Request $request)
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
        $vendorId = auth('tour')->user()->relation_id;
        $support_list = \App\Models\VendorSupportTicket::where(['created_by' => 'admin', 'type' => 'tour'])->get();
        $message_list = \App\Models\VendorSupportTicketConv::where(['created_by' => 'admin', 'type' => 'tour', 'vendor_id' => $vendorId])->with(['Tour'])
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->paginate(10, ['*'], 'page');
        return view(TourPath::ADMININBOX[VIEW], compact('message_list', 'support_list'));
    }
    public function FCMUpdates(Request $request)
    {
        request()->session()->put('device_fcm', $request['type']);
        if ($request['type'] == 'owner') {
            \App\Models\Seller::where('id', auth('tour')->id())->update(['cm_firebase_token' => $request['fcm']]);
        }
        return back();
    }
    public function FCMUpdatesdelete(Request $request)
    {
        session()->forget('device_fcm');
        return back();
    }

    public function GetVendorInfo(Request $request)
    {
        $amounts = 0;
        if (!empty($request['tour_order_id'])) {
            $tourOrder = \App\Models\TourOrder::select('amount', 'tour_id')->where('id', $request['tour_order_id'])->with(['Tour'])->first();
            if ($tourOrder) {
                $eventtax = \App\Models\ServiceTax::find(1);
                $gst_amount = 0;
                $admin_commission = 0;
                $final_amount = $tourOrder['amount'];
                if ($eventtax['tour_tax']) {
                    $gst_amount = (($final_amount * ($eventtax['tour_tax'] ?? 0)) / 100);
                    $final_amount = $final_amount - $gst_amount;
                }
                if ($tourOrder['Tour']['tour_commission'] ?? 0) {
                    $admin_commission = (($final_amount * $tourOrder['Tour']['tour_commission'] ?? 0) / 100);
                    $final_amount = ($final_amount - $admin_commission);
                }
                $admin_commission2 = (($final_amount * 30) / 100);
                $amounts = ($final_amount - $admin_commission2);
            }
        } else {
            $amounts = \App\Models\TourAndTravel::select('wallet_amount')->where('id', $request['id'])->first()['wallet_amount'] ?? 0;
        }
        $tour_data = \App\Models\TourAndTravel::select('bank_holder_name', 'bank_name', 'bank_branch', 'ifsc_code', 'account_number')->where('id', $request['id'])->first();
        $bankLists = \App\Models\WithdrawalAmountHistory::select('holder_name as bank_holder_name', 'bank_name', 'branch_code as bank_branch', 'ifsc_code', 'account_number')->where(['type' => 'tour', 'vendor_id' => auth('tour')->user()->relation_id])->where('account_number', '!=', $tour_data['account_number'])->where('account_number', '!=', '')->whereNotNull('account_number')->groupBy('account_number')->get();
        $bankListdata = collect([$tour_data])->merge($bankLists)->toArray();
        if ($tour_data) {
            return response()->json(['success' => 1, 'amount' => $amounts, 'bank_info' => $tour_data, 'banklistdata' => $bankListdata, 'message' => "Vendor Withdrawal Info"], 200);
        } else {
            return response()->json(['success' => 0, 'amount' => 0, 'bank_info' => [], 'message' => "Not Found Vendor"], 200);
        }
    }

    public function WithdrawalRequestView(Request $request)
    {
        $vendorId = auth('tour')->user()->relation_id;
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['vendor_id' => $vendorId, 'type' => "tour"])->with(['Tour', 'TourVisit'])->where('id', $request['id'])->first();
        return view('all-views/tour/withdraw/view', compact('withdrawRequests'));
    }
    public function AddWithdrawalRequest(Request $request)
    {
        if ($request['req_amount'] <= $request['wallet_amount']) {
            $withdrawal  =  new \App\Models\WithdrawalAmountHistory();
            $withdrawal->type = "tour";
            $withdrawal->vendor_id = auth('tour')->user()->relation_id;
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
                \App\Models\TourOrder::where('id', $request->ex_id)->update(['advance_withdrawal_amount' => $request['req_amount']]);
            } else {
                \App\Models\TourAndTravel::where('id', auth('tour')->user()->relation_id)->update(['withdrawal_pending_amount' => $request['req_amount']]);
            }
            Toastr::success(translate('Payment_request_sent_successfully'));
        } else {
            Toastr::error(translate('Payment_Request_failed'));
        }
        return back();
    }
    public function VendorCollectedAmount(Request $request)
    {
        $getData = \App\Models\TourOrder::with(['company', 'Tour', 'userData'])->where('id', $request->order_id)->where('cab_assign', auth('tour')->user()->relation_id)->where('advance_withdrawal_amount', 0)->first();
        if ($getData && ($getData['part_payment'] == "part" || $getData['part_payment'] == 'custom')) {
            $collectAmount = 0;
            if ($getData['part_payment'] == "part") {
                $collectAmount = $getData['amount'];
            } else {
                $collectAmount = ($getData['order_amount'] - $getData['amount']);
            }
            if (($request['amount'] ?? 0) >= $collectAmount) {
                $getData->part_payment = "full";
                $getData->save();
                $getData->increment('amount', $request->input('amount', 0));
                $getData->increment('final_amount', $request->input('amount', 0));
                $getData->increment('advance_withdrawal_amount', $request->input('amount', 0));
                $vendor = \App\Models\TourAndTravel::where('id', auth('tour')->user()->relation_id)->first();
                $vendor->increment('withdrawal_amount', $request->input('amount', 0));
                $vendor->save();
                TourLeads::where('id', ($getData['leads_id'] ?? 0))->update(['via_wallet' => $request->input('amount', 0), 'amount' => (($getData['part_payment'] == 'custom') ? ($getData['order_amount'] - $getData['amount']) : $getData['amount'])]);

                $message_data['orderId'] = $dataemail['orderId'] = ($getData['order_id'] ?? '');
                $message_data['title_name'] = ($getData['Tour']['tour_name'] ?? '');
                $message_data['booking_date'] = ($getData['pickup_date'] ?? '');
                $message_data['time'] = ($getData['pickup_time'] ?? '');
                $message_data['place_name'] = ($getData['pickup_address'] ?? '');
                $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($getData['Tour']['tour_type'] ?? ''))));
                $message_data['final_amount'] = $dataemail['final_amount'] = webCurrencyConverter(amount: (float)$getData['amount'] ?? 0);
                $message_data['customer_id'] = $getData['user_id'];
                if ($getData['Tour']['tour_image']) {
                    $message_data['type'] = 'text-with-media';
                    $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $getData['Tour']['tour_image'] ?? '');
                }
                $message_data['remain_amount'] = webCurrencyConverter(amount: 0);
                Helpers::whatsappMessage('tour', 'customer_message_cash_given', $message_data);

                $dataemail['admin_name'] = $vendor['company_name'];
                $dataemail['admin_phone'] = $vendor['phone_no'];
                Helpers::whatsappMessage('tour', 'vendor_message_cash_collect', $dataemail);

                $withdrawal  =  new \App\Models\WithdrawalAmountHistory();
                $withdrawal->type = "tour";
                $withdrawal->vendor_id = auth('tour')->user()->relation_id;
                $withdrawal->ex_id = ($getData['id'] ?? "");
                $withdrawal->holder_name = "";
                $withdrawal->bank_name = "";
                $withdrawal->ifsc_code = "";
                $withdrawal->account_number =  "";
                $withdrawal->upi_code = '';
                $withdrawal->old_wallet_amount = 0;
                $withdrawal->req_amount = $request->input('amount', 0);
                $withdrawal->approval_amount = $request->input('amount', 0);
                $withdrawal->message = "Cash Collected";
                $withdrawal->status = 1;
                $withdrawal->transcation_id = "wallet";
                $withdrawal->payment_method = "wallet";
                $withdrawal->save();
                Toastr::success('Collect Successfully');
            } else {
                Toastr::error('Amount is invalid, please correct it');
            }
        }
        return back();
    }

    public function PasswordChange(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
        ]);
        $passwordResetData = \App\Models\Seller::where(['type' => "tour", 'id' => $id])->first();
        if (!$passwordResetData) {
            Toastr::error(translate('invalid_URL'));
            return back()->withInput();
        }
        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $passwordResetData->password)) {
            Toastr::error(translate('Old password does not match'));
            return back()->withInput();
        }
        $passwordResetData->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $passwordResetData->save();
        if ($passwordResetData) {
            Toastr::success(translate('Password_reset_successfully'));
            return redirect()->route('tour-vendor.dashboard.index');
        } else {
            Toastr::error(translate('invalid_URL'));
            return back()->withInput();
        }
    }
}
