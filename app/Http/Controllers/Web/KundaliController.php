<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BirthJournal;
use App\Models\BirthJournalKundali;
use App\Models\Country;
use App\Models\KundaliLeads;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\CartManager;

use App\Utils\Helpers;
use App\Traits\Payment;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;


class KundaliController extends Controller
{
    public function __construct(
        private BirthJournal      $birthjo,
        private KundaliLeads      $createleads,
        private BirthJournalKundali  $birthjokun,
    ) {}

    public function index()
    {

        $kundali_info = $this->birthjo->where('status', 1)->get();

        return view('web-views.kundali-pdf.index', compact('kundali_info'));
    }

    public function JanamPatrikaPdf(Request $request, $type,$id)
    {
        $kundali_info = $this->birthjo->where('status', 1)->find($id);
        return view('web-views.kundali-pdf.kundali-information', compact('kundali_info'));
    }

    public function CreateKundliLeads(Request $request)
    {
        $userfind = User::where('phone', $request->input('person_phone'))->first();
        if ($userfind) {
            Auth::guard('customer')->loginUsingId($userfind['id']);
        } else {
            $user = new User();
            $user->phone = $request->input('person_phone');
            $user->name = $request->input('person_name');
            $user->f_name = (explode(" ", $request->input('person_name'))[0] ?? "");
            $user->l_name = (explode(" ", $request->input('person_name'))[1] ?? "");
            $user->email = $request->input('person_phone');
            $user->password =  bcrypt('12345678');
            $user->save();
            Auth::guard('customer')->loginUsingId($user->id);
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        $lead =  $this->createleads->create([
            'user_id' => Auth::guard('customer')->id(),
            'kundali_id' => $request['kundali_id'],
            'amount' => $request['amount'],
            'phone_no' => $request['person_phone'],
            'user_name' => $request['person_name'],
            'booking_date' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->route('kundali-pdf.kundli-paypdf', ['id' => $request['kundali_id'], 'leads' => urlencode($lead->id)]);
    }

    public function Kundlipaypdf(Request $request, $id)
    {
        $kundali_info = $this->birthjo->where('status', 1)->find($id);
        $leads_id = $request->get('leads');
        $astrologyapiKey = config('services.astrologyapi_map.api_key');
        $user_id = Auth::guard('customer')->id();
        $country = Country::all();
        return view('web-views.kundali-pdf.paypdf', compact('kundali_info', 'astrologyapiKey', 'user_id', 'country', 'leads_id'));
    }

    public function pdfkundalipaid(Request $request)
    {

        $checkkundalitype = BirthJournal::find($request->input("kundali_id"));
        $birthkundli = new BirthJournalKundali();
        $birthkundli->birth_journal_id = $request->input("kundali_id");
        $birthkundli->user_id = $request->input("user_id");
        $birthkundli->name = $request->input("username");
        $birthkundli->email = $request->input("useremail") ?? "";
        $birthkundli->gender = $request->input("usergender");
        $birthkundli->phone_no = $request->input("usermobile") ?? "";
        $birthkundli->bod = $request->input("userdob");
        $birthkundli->time = $request->input("usertime");
        $birthkundli->country_id = $request->input("usercountry");
        $birthkundli->state = $request->input("userplaces");
        $birthkundli->lat = $request->input("userlat");
        $birthkundli->log = $request->input("userlon");
        $birthkundli->language = $request->input("userlanguage");
        $birthkundli->tzone = $request->input("usertzone");
        $birthkundli->chart_style = $request->input("userchartstyle");
        $birthkundli->payment_status = 0;
        $birthkundli->amount = $request->input("useramount");
        if ($checkkundalitype['name'] == 'kundali_milan') {
            $birthkundli->female_name = $request->input("username_female");
            $birthkundli->female_email = $request->input("useremail_female") ?? "";
            $birthkundli->female_gender = $request->input("usergender_female");
            $birthkundli->female_phone_no = $request->input("usermobile_female") ?? "";
            $birthkundli->female_dob = $request->input("userdob_female");
            $birthkundli->female_time = $request->input("usertime_female");
            $birthkundli->female_country_id = $request->input("usercountry_female");
            $birthkundli->female_place = $request->input("userplaces_female");
            $birthkundli->female_lat = $request->input("userlat_female");
            $birthkundli->female_long = $request->input("userlon_female");
            $birthkundli->female_tzone = $request->input("usertzone_female");
        }

        $birthkundli->save();

        $insertedId = $birthkundli->id;

        //create pay link

        if ($request->wallet_type == '1') {
            $user = User::find($request->user_id);
            if ($user['wallet_balance'] >= $request['useramount']) {
                User::where('id', $user['id'])->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance - ' . $request['useramount'])]);
                $request['insertedId'] = $insertedId;
                \App\Http\Controllers\Customer\PaymentController::BirthJournalSuccess($request);
                $wallet_transaction = new \App\Models\WalletTransaction();
                $wallet_transaction->user_id = $user['id'];;
                $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                $wallet_transaction->reference = 'kundli order';
                $wallet_transaction->transaction_type = 'kundli_order';
                $wallet_transaction->balance = User::where('id', $user['id'])->first()['wallet_balance'];
                $wallet_transaction->debit = $request->useramount;
                $wallet_transaction->save();
                $findData =  BirthJournalKundali::with('birthJournal')->find($insertedId);
                if ($findData && $findData['birthJournal']['name'] == 'kundali') {
                    $url = route('saved.paid.kundali');
                } else {
                    $url = route('saved.paid.kundali.milan');
                }
                return response()->json(['message' => 'success', 'code' => 200, 'link' => $url], 200);
            } else {

                // wallet dedication
                $wallet_amount = ($request['wallet_balance']);
                $total_amount = $request['useramount'];
                $onlinepay = ($request['useramount'] - $user['wallet_balance']);
                $findData =  BirthJournalKundali::with('birthJournal')->find($insertedId);
                if ($findData && $findData['birthJournal']['name'] == 'kundali') {
                    $url = route('saved.paid.kundali');
                } else {
                    $url = route('saved.paid.kundali.milan');
                }
                $data = [
                    'additional_data' => [
                        'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                        'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                        'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',

                        'order_id' => $insertedId,
                        'leads_id' => $request->input("leads"),
                        'user_id' => $request->input("user_id"),
                        'kundli_lead_id' => $request->input("leads"),
                        'birth_journal_id' => $request->input("kundali_id"),
                        'amount' => $request->input('useramount'),

                        'customer_id' => $request->input("user_id"),
                        "user_name" => $user['name'],
                        "user_email" => $user['email'],
                        "user_phone" => $user['phone'],
                        'total_amount' => $total_amount,
                        'wallet_amount' => $wallet_amount,
                        "online_pay" => $onlinepay,
                        'page_name' => 'kundli_order',
                        'success_url' => $url,
                    ],
                    'user_id' => $user['id'],
                    'payment_method' => 'razor_pay',
                    'payment_platform' => 'web',
                    'payment_amount' => $onlinepay,
                    'attribute' => "Kundli Order",
                    'external_redirect_link' => route('all-pay-wallet-payment-success', [$insertedId]),
                ];
                $url_open = \App\Http\Controllers\Customer\PaymentController::Wallet_amount_add($data);
                return response()->json(['message' => 'success', 'code' => 200, 'link' => $url_open], 200);
            }
        } else {

            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = Currency::find($default)->code;
            }
            $additional_data['order_id'] = $insertedId;
            $additional_data['leads_id'] = $request->input("leads");
            $additional_data['user_id'] = $request->input("user_id");
            $additional_data['kundli_lead_id'] = $request->input("leads");
            $additional_data['birth_journal_id'] = $request->input("kundali_id");
            $additional_data['amount'] = $request->input('useramount');
            $additional_data['user_name'] = $request->input('username');
            $additional_data['user_email'] = $request->input('useremail');
            $additional_data['user_phone'] = $request->input('usermobile');
            $additional_data['business_name'] = 'Birth journal';
            $additional_data['business_logo'] = asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo');
            $payer = new Payer(
                $request->input('username'),
                $request->input('useremail'),
                $request->input('usermobile'),
                ''
            );
            $payment_info = new PaymentInfo(
                success_hook: 'digital_payment_success_custom',
                failure_hook: 'digital_payment_fail',
                currency_code: $currency_code,
                payment_method: 'razor_pay',
                payment_platform: "web",
                payer_id: $insertedId,
                receiver_id: '100',
                additional_data: $additional_data,
                payment_amount: $request->input('useramount'),
                external_redirect_link: route('payment.birth_journal_kundli_success'),
                attribute: 'Birth_journal',
                attribute_id: idate("U"),
            );
            $receiver_info = new Receiver('receiver_name', 'example.png');
            $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

            return response()->json(['message' => 'success', 'code' => 200, 'link' => $redirect_link], 200);
        }
    }

    public function PayuMoney(Request $request) {}
    public function kundalipaySuccess(Request $request, $id)
    {
        $findData =  BirthJournalKundali::with('birthJournal')->find($id);
        $data['type'] = $findData['birthJournal']['name'];
        return view('web-views.kundali-pdf.kundali-pay-success', compact('findData', 'data'));
    }
}