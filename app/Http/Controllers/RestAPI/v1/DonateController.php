<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Contracts\Repositories\DonateCategoryRepositoryInterface;
use App\Contracts\Repositories\DonateTrustAdsRepositoryInterface;
use App\Contracts\Repositories\DonateTrustRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\DonateAds;
use App\Models\DonateAllTransaction;
use App\Models\DonateTrust;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\DonateLeads;
use App\Models\UserPanCardVerified;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DonateController extends Controller
{

    public function __construct(
        private readonly DonateCategoryRepositoryInterface     $categoryRepo,
        private readonly DonateTrustAdsRepositoryInterface     $AdsRepo,
        private readonly DonateTrustRepositoryInterface     $trustRepo,
    ) {}

    public function getCategory()
    {
        $donate_translation = [];
        $getData = $this->categoryRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['types' => 'category', 'status' => 1], dataLimit: 'all');
        if (!empty($getData) && count($getData) > 0) {
            foreach ($getData as $ke => $img) {
                $translations = $img->translations()->pluck('value', 'key')->toArray();
                $donate_translation[$ke]["en_name"] = $img['name'];
                $donate_translation[$ke]["hi_name"] = $translations['name'] ?? '';
                $donate_translation[$ke]['id'] =  $img['id'];
                $donate_translation[$ke]['slug'] = $img['slug'];
                $donate_translation[$ke]['image'] =  getValidImage(path: 'storage/app/public/donate/category/' . $img['image'], type: 'product');;
            }
            return response()->json(['status' => 1, 'message' => 'Category List', 'recode' => count($donate_translation), 'data' => $donate_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Category', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function getPurpose()
    {
        $getData = $this->categoryRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['types' => 'porpose', 'status' => 1], dataLimit: 'all');
        $donate_translation = [];
        if (!empty($getData) && count($getData) > 0) {
            foreach ($getData as $ke => $img) {
                $translations = $img->translations()->pluck('value', 'key')->toArray();
                $donate_translation[$ke]["en_name"] = $img['name'];
                $donate_translation[$ke]["hi_name"] = $translations['name'] ?? '';
                $donate_translation[$ke]['id'] =  $img['id'];
                $donate_translation[$ke]['slug'] = $img['slug'];
                $donate_translation[$ke]['image'] =  getValidImage(path: 'storage/app/public/donate/purpose/' . $img['image'], type: 'product');
            }
            return response()->json(['status' => 1, 'message' => 'Purpose List', 'recode' => count($donate_translation), 'data' => $donate_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Purpose', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function DonateTrust(Request $request)
    {
        $request->validate([
            'type' => 'required|in:ads,trust,ads_inhouse',
        ]);
        $type = $request->get('type');
        if ($request->input('type') === 'trust') {
            $request->validate([
                'trust_category_id' => 'required',
            ]);
            $gettrustData = $this->trustRepo->getListWhere(orderBy: ['id' => 'desc'], filters: ['category_id' => $request->get('trust_category_id'), 'is_approve' => 1, 'status' => 1], relations: ['translations'], dataLimit: 'all');
            if (!empty($gettrustData) && count($gettrustData) > 0) {
                $donate_translation = [];
                foreach ($gettrustData as $key => $value) {
                    $translations = $value->translations()->pluck('value', 'key')->toArray();
                    $donate_translation[$key]['en_trust_name'] =  $value['trust_name'];
                    $donate_translation[$key]['hi_trust_name'] =  ($translations['trust_name'] ?? "");
                    $donate_translation[$key]['en_description'] =  $value['description'];
                    $donate_translation[$key]['hi_description'] =  ($translations['description'] ?? "");
                    $donate_translation[$key]['auto_pay_set_status'] =  $value['auto_pay_set_status'] ?? 1;
                    $donate_translation[$key]['id'] =  $value['id'];
                    $donate_translation[$key]['slug'] =  ($value['slug'] ?? "");
                    // $images = json_decode($value['gallery_image'], true);
                    // $donate_translation[$key]['image'] = getValidImage(path: 'storage/app/public/donate/trust/' . $images[0] ?? '', type: 'product');
                    $donate_translation[$key]['image'] = getValidImage(path: 'storage/app/public/donate/trust/' . $value['theme_image'] ?? '', type: 'product');
                }
                return response()->json(['status' => 1, 'message' => 'Trust List', 'recode' => count($donate_translation), 'data' => $donate_translation], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Not Found Trust List', 'recode' => 0, 'data' => []], 400);
            }
        } else {
            if ($request->input('type') === 'ads_inhouse') {
                $getadsData = $this->AdsRepo->getListWhere(orderBy: ['id' => 'desc'], filters: [
                    'type' => 'inhouse',
                    'purpura_id' => $request->get('trust_category_id'),
                    'is_approve' => 1,
                    'status' => 1,
                    'select_raw' => "IFNULL(SUM(t.amount),0) as collected_amount, dp.slug as p_type, 'indonate' as showvalue",
                    'joins' => [
                        [
                            'table' => 'donate_categories as dc',
                            'on' => ['dc.id', 'donate_ads.category_id'],
                            'extra' => [['dc.status', '=', 1]]
                        ],
                        [
                            'table' => 'donate_categories as dp',
                            'on' => ['dp.id', 'donate_ads.purpose_id'],
                            'extra' => [['dp.status', '=', 1]]
                        ],
                        [
                            'table' => 'donate_all_transaction as t',
                            'on' => ['t.ads_id', 'donate_ads.id'],
                            'extra' => [
                                ['t.type', '=', 'donate_ads'],
                                ['t.amount_status', '=', 1],
                            ]
                        ]
                    ],
                    'group_by' => ['donate_ads.id', 'dc.slug', 'dp.slug'],
                    'having_raw' => '(donate_ads.set_requirement_amount IS NULL OR donate_ads.set_requirement_amount = 0 OR collected_amount < donate_ads.set_requirement_amount)',
                    'date_range_apply' => 1
                ], relations: ['translations', 'category', 'Trusts', 'Purpose'], dataLimit: 'all');
            } else {
                $getadsData = $this->AdsRepo->getListWhere(orderBy: ['id' => 'desc'], filters: [
                    'purpura_id' => $request->get('trust_category_id'),
                    'is_approve' => 1,
                    'status' => 1,
                    'select_raw' => "IFNULL(SUM(t.amount),0) as collected_amount, dp.slug as p_type, 'indonate' as showvalue",
                    'joins' => [
                        [
                            'table' => 'donate_categories as dc',
                            'on' => ['dc.id', 'donate_ads.category_id'],
                            'extra' => [['dc.status', '=', 1]]
                        ],
                        [
                            'table' => 'donate_categories as dp',
                            'on' => ['dp.id', 'donate_ads.purpose_id'],
                            'extra' => [['dp.status', '=', 1]]
                        ],
                        [
                            'table' => 'donate_all_transaction as t',
                            'on' => ['t.ads_id', 'donate_ads.id'],
                            'extra' => [
                                ['t.type', '=', 'donate_ads'],
                                ['t.amount_status', '=', 1],
                            ]
                        ]
                    ],
                    'group_by' => ['donate_ads.id', 'dc.slug', 'dp.slug'],
                    'having_raw' => '(donate_ads.set_requirement_amount IS NULL OR donate_ads.set_requirement_amount = 0 OR collected_amount < donate_ads.set_requirement_amount)',
                    'date_range_apply' => 1
                ], relations: ['translations', 'category', 'Trusts', 'Purpose'], dataLimit: 'all');
            }
            if (!empty($getadsData) && count($getadsData) > 0) {
                $donate_translation = [];
                foreach ($getadsData as $key => $value) {
                    $translations = $value->translations()->pluck('value', 'key')->toArray();
                    $donate_translation[$key]['en_name'] =  $value['name'];
                    $donate_translation[$key]['hi_name'] =  ($translations['name'] ?? "");
                    $donate_translation[$key]['en_description'] =  $value['description'];
                    $donate_translation[$key]['hi_description'] =  ($translations['description'] ?? "");
                    $donate_translation[$key]['purpose_id'] =  $value['purpose_id'];
                    $target = trim($value['set_requirement_amount'] ?? 0);
                    $collected = \App\Models\DonateAllTransaction::where('type', 'donate_ads')->where('ads_id', $value['id'])->where('amount_status', 1)->sum('amount');
                    $progress = $target > 0 ? round(($collected / $target) * 100) : 0;

                    $donate_translation[$key]['req_amount'] =  ((string)$target ?? 0);
                    $donate_translation[$key]['req_collected'] =  ((string)$collected ?? 0);
                    $donate_translation[$key]['req_progress'] =  (int)$progress;

                    $donate_translation[$key]['auto_pay_set_status'] =  ($value['auto_pay_set_status'] ?? 1);
                    $donate_translation[$key]['id'] =  $value['id'];
                    $donate_translation[$key]['slug'] =  ($value['slug'] ?? '');
                    $donate_translation[$key]['image'] = getValidImage(path: 'storage/app/public/donate/ads/' . $value['image'], type: 'product');
                }
                return response()->json(['status' => 1, 'message' => 'Ads List', 'recode' => count($donate_translation), 'data' => $donate_translation], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Not Found Ads List', 'recode' => 0, 'data' => []], 400);
            }
        }
    }

    public function TrustGet(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $request->validate([
            'type' => 'required|in:ads,trust',
            'id' => 'required',
        ]);
        $type = $request->get('type');
        $id = $request->get('id');
        if ($request->input('type') === 'trust') {
            //$this->trustRepo->getFirstWhere(params: ['id' => $request->get('id'), 'is_approve' => 1, 'status' => 1], relations: ['translations']);
            $gettrustData = DonateTrust::where(function ($query) use ($request) {
                $query->where('id', $request->id)
                    ->orWhere('slug', $request->id);
            })->where(['is_approve' => 1, 'status' => 1])->with(['translations'])->first();
            if (!empty($gettrustData)) {
                $donate_translation = [];
                $translations = $gettrustData->translations()->pluck('value', 'key')->toArray();
                $donate_translation['en_trust_name'] =  $gettrustData['trust_name'] ?? "";
                $donate_translation['hi_trust_name'] =  $translations['trust_name'] ?? "";
                $donate_translation['en_description'] =  $gettrustData['description'] ?? "";
                $donate_translation['hi_description'] =  $translations['description'] ?? "";
                $donate_translation['id'] =  $gettrustData['id'];
                $donate_translation['auto_pay_set_status'] =  1;
                if (!empty($gettrustData['gallery_image']) && json_decode($gettrustData['gallery_image'], true)) {
                    $images = json_decode($gettrustData['gallery_image'], true);
                    foreach ($images as $key => $img) {
                        $donate_translation['image'][$key] = getValidImage(path: 'storage/app/public/donate/trust/' . ($img ?? ''), type: 'product');
                    }
                }
                return response()->json(['status' => 1, 'message' => 'Trust List', 'recode' => 1, 'data' => $donate_translation], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Not Found Trust List', 'recode' => 0, 'data' => []], 400);
            }
        } else {
            // $getadsData = $this->AdsRepo->getFirstWhere(params: ['id' => $request->get('id'), 'is_approve' => 1, 'status' => 1], relations: ['category', 'Trusts', 'Purpose']);
            $getadsData = DonateAds::where(function ($query) use ($request) {
                $query->where('id', $request->id)
                    ->orWhere('slug', $request->id);
            })->where(['is_approve' => 1, 'status' => 1])->with(['category', 'Trusts', 'Purpose'])->first();
            if (!empty($getadsData)) {
                $donate_translation = [];
                $translations = $getadsData->translations()->pluck('value', 'key')->toArray();
                $donate_translation['en_name'] =  $getadsData['name'] ?? "";
                $donate_translation['hi_name'] =  $translations['name'] ?? "";
                $donate_translation['en_description'] =  $getadsData['description'] ?? "";
                $donate_translation['hi_description'] =  $translations['description'] ?? "";

                $donate_translation['set_type'] =  ($getadsData['set_type'] ?? "");
                $donate_translation['set_amount'] =  ($getadsData['set_amount'] ?? "");
                $donate_translation['set_title'] =  ($getadsData['set_title'] ?? "");
                $donate_translation['set_number'] =  ($getadsData['set_number'] ?? "");
                $donate_translation['set_unit'] =  ($getadsData['set_unit'] ?? "");
                $donate_translation['auto_pay_set_status'] =  $getadsData['auto_pay_set_status'] ?? 1;
                $donate_translation["en_trust_name"] = ($getadsData['trusts']['trust_name'] ?? "");

                $getTrust = \App\Models\DonateTrust::where('id', ($getadsData['trust_id'] ?? ""))->first();
                $trust_name = [];
                if (!empty($getTrust)) {
                    $trust_name = $getadsData['trusts']->translations()->pluck('value', 'key')->toArray();
                }
                $donate_translation["hi_trust_name"] = ($trust_name['trust_name'] ?? '');
                $donate_translation['id'] =  $getadsData['id'];
                $getProductList = json_decode($getadsData['set_json'] ?? "[]", true);
                $productList = [];
                if(($getProductList['en']??"") && count($getProductList['en']) > 0){
                    foreach ($getProductList['en'] as $inkey => $val) {
                        $productList[] = [
                            "id"=>$val['id']??0,
                            "set_amount"=>$val['set_amount'],
                            "en_set_title"=>$val["set_title"],
                            "hi_set_title"=>$getProductList['in'][$inkey]['set_title']??"",
                            "set_number"=>$val['set_number'],
                            "set_unit"=>$val['set_unit'],
                            "image"=>getValidImage(path: 'storage/app/public/donate/ads/' .($val['image']??""), type: 'product'),
                        ];
                    }
                }
                $donate_translation['product_list'] =  $productList;

                $donate_translation['image'] = getValidImage(path: 'storage/app/public/donate/ads/' . $getadsData['image'], type: 'product');
                return response()->json(['status' => 1, 'message' => 'get Ads Data', 'recode' => 1, 'data' => $donate_translation], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Not Found Ads Data', 'recode' => 0, 'data' => []], 400);
            }
        }
    }

    public function DonateAmount(Request $request)
    {

        $request->validate([
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'type' => 'required|in:ads,trust',
            'id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if ($request->input('type') == 'ads') {
                    if (!DonateAds::where(function ($query) use ($value) {
                        $query->where('id', $value)
                            ->orWhere('slug', $value);
                    })->where('is_approve', 1)->where('status', 1)->exists()) {
                        $fail('The selected Id is invalid.');
                    }
                } else {
                    if (!DonateTrust::where(function ($query) use ($value) {
                        $query->where('id', $value)
                            ->orWhere('slug', $value);
                    })->where('is_approve', 1)->where('status', 1)->exists()) {
                        $fail('The selected Id is invalid.');
                    }
                }
            },],
            'name' => 'required',
            'phone' => 'required',
            'amount' => 'required',
            'pan_card' => "nullable|numeric"
        ]);
        // dd($request->all());
        $userdata = User::where('id', $request->get('user_id'))->where('is_active', 1)->first();


        $trust_ids = 0;
        $ads_ids = 0;
        if ($request->get('type') == 'ads') {
            $getadsuse =  DonateAds::where(function ($query) use ($request) {
                $query->where('id', $request->id)
                    ->orWhere('slug', $request->id);
            })->where('is_approve', 1)->where('status', 1)->first();
            $ads_ids = $getadsuse['id'] ?? 0;
            if (!empty($getadsuse)) {
                $trust_ids = $getadsuse['trust_id'];
            }
        }
        if ($request->get('type') == 'trust') {
            $trust_ids = $request->get('id');
        }
        $AllTransaction = new DonateAllTransaction();
        $AllTransaction->type =  (($request->get('type') == 'trust') ? 'donate_trust' : 'donate_ads');
        $AllTransaction->user_id =  ($request->get('user_id') ?? "");
        $AllTransaction->user_name =  ($request->get('name') ?? "");
        $AllTransaction->user_phone =  ($request->get('phone') ?? "");
        $AllTransaction->pan_card =  ($request->get('pan_card') ?? "");
        $AllTransaction->trust_id =  $trust_ids;
        $AllTransaction->ads_id =  $ads_ids;
        $AllTransaction->information = json_encode($request['information'] ?? []);
        $AllTransaction->amount =  ($request->get('amount') ?? "");
        $AllTransaction->save();

        $leads = DonateLeads::create([
            'amount' => $request->get('amount') ?? 0,
            'trust_id' => $trust_ids,
            'ads_id' => $ads_ids,
            'user_id' => $request->get('user_id') ?? '',
            "information" => json_encode($request['information'] ?? []),
            'type' => $request->get('type') == 'trust' ? 'donate trust' : 'ads Donate',
            'status' => 0,
        ]);
        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $currency_code = 'USD';
        } else {
            $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = Currency::find($default)->code;
        }

        $paymentReq = new PaymentRequest();
        $paymentReq->payer_id = $request->get('user_id');
        $paymentReq->receiver_id = '100';
        $paymentReq->payment_amount = $request->get('amount');
        $paymentReq->currency_code = $currency_code;
        $paymentReq->is_paid = 0;
        $paymentReq->payment_platform = 'app';
        $paymentReq->receiver_information = json_encode(['name' => 'receiver_name', "image" => "example.png"]);
        $paymentReq->payer_information = json_encode(["name" => $userdata['name'], "email" => $userdata['email'], "phone" => $userdata['phone'], "address" => '']);
        $paymentReq->additional_data = json_encode(["business_name" => "Mahakal.com", "business_logo" => "", "payment_mode" => "web", "leads_id" => $leads->id, "trust_id" => $trust_ids, "ads_id" => $ads_ids, "transaction_id" => $AllTransaction->id, "customer_id" => $request->get('user_id'), "payment_request_from" => "web"]);
        $paymentReq->save();

        DonateAllTransaction::where('id', $AllTransaction->id)->update(['payment_requests_id' => $paymentReq->id]);
        return response()->json(['status' => 1, 'message' => 'pay now', 'recode' => 1, 'data' => ['id' => $paymentReq->id]], 200);
    }

    public function DonateAmountUpdate(Request $request)
    {
        $request->validate([
            'id' => ['required', function ($attribute, $value, $fail) {
                if (!PaymentRequest::where('id', $value)->where('is_paid', 0)->exists()) {
                    $fail('Already Paid.');
                }
            },],
            "frequency" => "required|in:one_time,weekly,monthly,quarterly,yearly",
            "amount" => "required",
            "user_name" => "required",
            "user_phone" => "required"
        ]);
        $getdata = PaymentRequest::find($request->get('id'));
        DonateAllTransaction::where('payment_requests_id',  $request->get('id'))->update(['amount' => ($request['amount'] ?? 0), 'frequency' => ($request['frequency'] ?? ''), 'user_name' => ($request['user_name'] ?? 0), 'user_phone' => ($request['user_phone'] ?? 0), 'pan_card' => ($request->get('pan_card') ?? ""), 'platform' => "app"]);
        if (!empty($getdata['additional_data']) && json_decode($getdata['additional_data'], true)) {
            DonateLeads::where('id', (json_decode($getdata['additional_data'], true)['leads_id'] ?? ""))->update(['amount' => ($request['amount'] ?? 0), 'frequency' => ($request['frequency'] ?? ''), 'platform' => "app", 'information' => json_encode($request['information'] ?? [])]);
        }
        PaymentRequest::where('id',  $request->get('id'))->update(['payment_amount' => $request->get('amount')]);
        if (in_array(($request['frequency'] ?? ''), ['weekly', 'monthly', 'quarterly', 'yearly'])) {
            $transaction = DonateAllTransaction::where('payment_requests_id',  $request->get('id'))->first();
            $subscription_id = \App\Http\Controllers\Customer\PaymentController::createSubscriptionPlan(($request['frequency'] ?? ''), ($request['amount'] ?? 0), $transaction->user_id, $transaction->id);
            $transaction->subscription_id = $subscription_id['id'];
            $transaction->save();
            return response()->json(['status' => 2, 'message' => 'amount update successfully', 'recode' => 1, 'data' => ['id' =>  $request->get('id'), 'subscription_id' => $subscription_id['id']]], 200);
        }
        return response()->json(['status' => 1, 'message' => 'amount update successfully', 'recode' => 1, 'data' => ['id' =>  $request->get('id'), 'subscription_id' => ""]], 200);
    }

    public function CancelSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:donate_all_transaction,subscription_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $config = DB::table('addon_settings')->where('key_name', 'razor_pay')->where('settings_type', 'payment_config')->first();
        $razor = false;
        if (!is_null($config) && $config->mode == 'live') {
            $razor = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $razor = json_decode($config->test_values);
        }
        if ($razor) {
            $config = array(
                'api_key' => $razor->api_key,
                'api_secret' => $razor->api_secret
            );
            Config::set('razor_config', $config);
        }
        $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
        try {
            $razorpaySub = $api->subscription->fetch($request['id']);
            $razorpaySub->cancel();
            \App\Models\DonationSubscription::where('subscription_id', $request['id'])
                ->update([
                    'status'     => 'cancelled',
                    'ended_at'   => now(),
                    'updated_at' => now()
                ]);
            return response()->json(['status' => 1, 'message' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 0,
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function DonateAmountSuccess(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'amount' => 'required',
            'transaction_id' => "required",
            'payment_method' => "required",
            'wallet_type' => 'required|in:0,1',
            'online_pay' => 'required_unless:transaction_id,wallet',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $getIdRequest = PaymentRequest::where('id', $request->get('id'))->first();
            if ($getIdRequest['additional_data'] && json_decode($getIdRequest['additional_data'], true)) {
                $additional_data = json_decode($getIdRequest['additional_data'], true);

                if ($request->wallet_type == 1 && ($request['online_pay'] ?? 0) > 0) {
                    User::where('id', $additional_data['customer_id'])->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance + ' . $request['online_pay'])]);
                    $wallet_transaction = new \App\Models\WalletTransaction();
                    $wallet_transaction->user_id = $additional_data['customer_id'];
                    $wallet_transaction->transaction_id = (($request->transaction_id) ? $request->transaction_id : \Illuminate\Support\Str::uuid());
                    $wallet_transaction->reference = 'add_funds_to_wallet';
                    $wallet_transaction->transaction_type = 'add_fund';
                    $wallet_transaction->balance = User::where('id', $additional_data['customer_id'])->first()['wallet_balance'];
                    $wallet_transaction->credit = $request['online_pay'];
                    $wallet_transaction->save();
                }
                $additional_data['leads_id'];

                $admin_commission = 0;
                $final_amount = ($request->amount ?? 0); //$getIdRequest['payment_amount'];

                $trustData = DonateTrust::where('id', $additional_data['trust_id'])->first();
                $AdsData = DonateAds::where('id', $additional_data['ads_id'])->first();
                if (!empty($additional_data['trust_id'])) {
                    if (!empty($request->ads_id) && $request->ads_id > 0) {
                        if (!empty($AdsData) && isset($AdsData['admin_commission']) && $AdsData['admin_commission'] > 0) {
                            $admin_commission = ((($request->amount ?? 0) * $AdsData['admin_commission']) / 100);
                            $final_amount = (($request->amount ?? 0) - $admin_commission);
                        } else {
                            $admin_commission = ((($request->amount ?? 0) * $trustData['ad_commission']) / 100);
                            $final_amount = (($request->amount ?? 0) - $admin_commission);
                        }
                    } else {
                        $admin_commission = ((($request->amount ?? 0) * $trustData['donate_commission']) / 100);
                        $final_amount = (($request->amount ?? 0) - $admin_commission);
                    }
                }
                $user = User::where('id', $additional_data['customer_id'])->first();
                if ($request->wallet_type == 1) {
                    if ($user['wallet_balance'] >= $request['amount']) {
                        User::where('id', $user['id'])->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance - ' . $request['amount'])]);
                        DonateLeads::where('id', $additional_data['leads_id'])->update(['status' => 1]);
                        $wallet_transaction = new \App\Models\WalletTransaction();
                        $wallet_transaction->user_id = $additional_data['customer_id'];
                        $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                        $wallet_transaction->reference = 'Donate';
                        $wallet_transaction->transaction_type = 'donate';
                        $wallet_transaction->balance = User::where('id', $additional_data['customer_id'])->first()['wallet_balance'];
                        $wallet_transaction->debit = $request->amount;
                        $wallet_transaction->save();
                        $updateTransaction = [
                            'admin_commission' => $admin_commission,
                            'pan_card' => ($request->pan_card ?? ''),
                            'amount' => $request->amount,
                            'final_amount' => $final_amount,
                            'amount_status' => 1,
                            'transaction_id' => 'wallet',
                            'information' => json_encode(json_decode($request['information']??"[]",true)),
                        ];
                        if (!empty($request['user_name'])) {
                            $updateTransaction['user_name'] = $request['user_name'] ?? "";
                        }
                        if (!empty($request['user_phone'])) {
                            $updateTransaction['user_phone'] = $request['user_phone'] ?? "";
                        }
                        DonateAllTransaction::where('id', $additional_data['transaction_id'])->update($updateTransaction);
                        \Illuminate\Support\Facades\DB::commit();
                    } else {
                        return response()->json(['status' => 0, 'message' => 'please wallet Amount Check', 'data' => []], 200);
                    }
                } else {
                    $updateTransaction = [
                        'admin_commission' => $admin_commission,
                        'pan_card' => ($request->pan_card ?? ''),
                        'amount' => $request->amount,
                        'final_amount' => $final_amount,
                        'amount_status' => 1,
                        'transaction_id' => $request->get('transaction_id'),
                        'information' => json_encode(json_decode($request['information']??"[]",true))
                    ];
                    if (!empty($request['user_name'])) {
                        $updateTransaction['user_name'] = $request['user_name'] ?? "";
                    }
                    
                    if (!empty($request['user_phone'])) {
                        $updateTransaction['user_phone'] = $request['user_phone'] ?? "";
                    }
                    DonateAllTransaction::where('id', $additional_data['transaction_id'])->update($updateTransaction);
                    DonateLeads::where('id', $additional_data['leads_id'])->update(['status' => 1]);
                    \Illuminate\Support\Facades\DB::commit();
                }
            }
            PaymentRequest::where('id', $request->get('id'))->update(['transaction_id' => $request->get('transaction_id'), 'payment_method' => $request->get('payment_method'), 'is_paid' => 1]);
            \Illuminate\Support\Facades\DB::commit();

            $orderData = DonateAllTransaction::where('id', $additional_data['transaction_id'])->where('user_id',  $additional_data['customer_id'])->with(['users', 'getTrust', 'adsTrust'])->first();
            $message_data['person_phone'] =  $orderData['user_phone'];
            $message_data['pan_card'] =  $orderData['pan_card'] ?? '';
            $mpdf_view2 = \Illuminate\Support\Facades\View::make('web-views.donate.invoice', compact('orderData'));
            Helpers::gen_mpdf_Pdf($mpdf_view2, 'donate_order', $additional_data['transaction_id']);
            $message_data['attachment'] = asset('storage/app/public/donate/invoice/donate_order' . $additional_data['transaction_id'] . '.pdf');
            $message_data['type'] = 'text-with-media';
            Helpers::whatsappMessage('donate', 'Donation Success', $message_data);

            $orderData = DonateAllTransaction::where('id', $additional_data['transaction_id'])->with(['getTrust', 'adsTrust'])->first();
            $message_data2['trust_name'] =  $orderData['getTrust']['trust_name'] ?? "Mahakal.com";
            $message_data2['ad_name'] =  $orderData['adsTrust']['name'] ?? '';
            $message_data2['booking_date'] =  date('d M,Y H:i A', strtotime($orderData['created_at']));
            $message_data2['order_amount'] =  $orderData['amount'];
            $message_data2['admin_commission'] =  $orderData['admin_commission'];
            $message_data2['final_amount'] =  $orderData['final_amount'];
            $message_data2['vendor_email'] =   $orderData['getTrust']['trust_email'] ?? "Mahakal.com";
            $message_data2['seller_id'] = \App\Models\Seller::where('relation_id', $orderData['trust_id'])->where('type', 'trust')->first()['id'] ?? 0;
            Helpers::whatsappMessage('donate', 'donation_trust_receipt', $message_data2);

            if (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', strtoupper($orderData['pan_card'] ?? '')) && \App\Models\UserPanCardVerified::where('pan_number', strtoupper($orderData['pan_card'] ?? ''))->exists()) {
                $message_data['customer_id'] =  $orderData['user_id'];
                $message_data['person_phone'] =  $orderData['user_phone'];
                $message_data['pan_card'] =  strtoupper($orderData['pan_card'] ?? '');
                $orderData = DonateAllTransaction::where('id', $additional_data['transaction_id'])->where('user_id', $orderData['user_id'])->with(['users', 'getTrust', 'adsTrust'])->first();
                if (empty($orderData['ertiga_certificate'] ?? '')) {
                    \App\Http\Controllers\RestAPI\v1\DonateController::create_donate_cetificate($orderData['id']);
                    $message_data['attachment']  = getValidImage(path: 'storage/app/public/donate/certificate/' . '80g_' . $orderData['trans_id'] . '.jpg', type: 'product');
                } else {
                    $message_data['attachment']  = getValidImage(path: 'storage/app/public/donate/certificate/' . ($orderData['ertiga_certificate'] ?? ''), type: 'product');
                }
                $message_data['type'] = 'text-with-media';
                Helpers::whatsappMessage('donate', 'Donation Success pdf', $message_data);
            }
            return response()->json(['status' => 1, 'message' => 'Donate Successfully', 'recode' => 1, 'data' => []], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage(), 'data' => []], 200);
        }
    }

    public function DonateOrder(Request $request)
    {
        $request->validate([
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
        ]);
        if (!empty($request->id)) {
            $getData = DonateAllTransaction::where('id', $request->id)->where('user_id', $request->user_id)->with(['getTrust', 'adsTrust'])->first();
            $orderList['id'] = $getData['id'];
            $orderList['order_id'] = $getData['trans_id'];
            $orderList['type'] = $getData['type'];
            $orderList['amount'] = $getData['amount'];
            $orderList['amount_status'] = $getData['amount_status'];
            $orderList['subscription_id'] = ($getData['subscription_id'] ?? "");
            $orderList['subscription_status'] = (\App\Models\DonationSubscription::where('subscription_id', ($getData['subscription_id'] ?? ""))->first()['status'] ?? "");
            $orderList['frequency'] = ($getData['frequency'] ?? "");
            $orderList['date'] = date('d-m-Y h:i:s A', strtotime($getData['created_at']));
            $gettrans_ads = [];
            if (!empty($getData['adsTrust'])) {
                $gettrans_ads = $getData['adsTrust']->translations()->pluck('value', 'key')->toArray();
            }
            $orderList['en_ads_name'] = ($getData['adsTrust']['name'] ?? "");
            $orderList['hi_ads_name'] = ($gettrans_ads['name'] ?? "");

            $gettrans_trust = [];
            if (!empty($getData['getTrust'])) {
                $gettrans_trust = $getData['getTrust']->translations()->pluck('value', 'key')->toArray();
            }
            $orderList['en_trust_name'] = ($getData['getTrust']['trust_name'] ?? "");
            $order_information = json_decode($getData['information'] ?? "[]", true);
            $getadsnew = json_decode($getData['adsTrust']['set_json'] ?? "[]", true);
            $newArray = [];

            if ($order_information && !empty($getadsnew) && count($order_information) > 0) {
                $adsData = $getadsnew['en'] ?? $getadsnew['in'] ?? [];
                $adsById = [];
                foreach ($adsData as $adsItem) {
                    $adsById[$adsItem['id']] = $adsItem;
                }
                foreach ($order_information as $inlist) {
                    $id = $inlist['id'] ?? null;
                    if ($id !== null) {
                        $newItem = ['id' => $id];
                        $images = '';
                        if (isset($adsById[$id])) {
                            $adsItem = $adsById[$id];
                            $newItem['name'] = $adsItem['set_title'] ?? '';
                            $newItem['title'] = $adsItem['set_title'] ?? '';
                            $newItem['amount'] = $adsItem['set_amount'] ?? $inlist['amount'] ?? '';
                            $images = $adsItem['image'];
                        } else {
                            $newItem['name'] = $inlist['title'] ?? '';
                            $newItem['title'] = $inlist['title'] ?? '';
                            $newItem['amount'] = $inlist['amount'] ?? '';
                        }
                        $newItem['qty'] = $inlist['qty'] ?? '';
                        $newItem['fullamount'] = $inlist['fullamount'] ?? '';
                        $newItem['image'] =  getValidImage(path: 'storage/app/public/donate/ads/'.$images, type: 'backend-product');
                        $newArray[] = $newItem;
                    }
                }
            }
            $orderList['information'] = $newArray;
            $orderList['hi_trust_name'] = ($gettrans_trust['trust_name'] ?? "");
            $orderList['invoice_url'] = url('api/v1/donate/invoice/' . $getData['id'] ?? '');
            if (empty($getData['ertiga_certificate'] ?? '') && (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', strtoupper($getData['pan_card'])) && \App\Models\UserPanCardVerified::where('pan_number', strtoupper($getData['pan_card']))->exists())) {
                $this->create_donate_cetificate($getData['id']);
                $orderList['ertiga_certificate'] = getValidImage(path: 'storage/app/public/donate/certificate/' . '80g_' . $getData['trans_id'] . '.jpg', type: 'product');
            } else {
                $orderList['ertiga_certificate'] = $getData['pan_card'] ? getValidImage(path: 'storage/app/public/donate/certificate/' . ($getData['ertiga_certificate'] ?? ''), type: 'product') : '';
            }
            if ($getData['type'] == 'donate_trust') {
                $orderList['image'] =  getValidImage(path: 'storage/app/public/donate/trust/' . $getData['getTrust']['theme_image'], type: 'product');;
            } else {
                $orderList['image'] =  getValidImage(path: 'storage/app/public/donate/ads/' . $getData['adsTrust']['image'], type: 'product');;
            }
        } else {
            $getData = DonateAllTransaction::when((isset($request['subscription']) && ($request['subscription'] == 0 || $request['subscription'] == 1)), function ($query) use ($request) {
                if ($request['subscription'] == 1) {
                    $query->where('subscription_id', '!=', '');
                } else {
                    $query->where('subscription_id', '==', '');
                }
            })->where('user_id', $request->user_id)->with(['getTrust', 'adsTrust'])->where('amount_status', 1)->orderBy('id', "desc")->get();
            $orderList = [];
            if ($getData) {
                foreach ($getData as $key => $value) {
                    $orderList[$key]['id'] = $value['id'];
                    $orderList[$key]['order_id'] = $value['trans_id'];
                    $orderList[$key]['type'] = $value['type'];
                    $orderList[$key]['amount'] = $value['amount'];
                    $orderList[$key]['amount_status'] = $value['amount_status'];
                    $orderList[$key]['subscription_id'] = ($value['subscription_id'] ?? "");
                    $orderList[$key]['frequency'] = ($value['frequency'] ?? "");
                    $orderList[$key]['date'] = date('d-m-Y h:i:s A', strtotime($value['created_at']));
                    $gettrans_ads = [];
                    if (!empty($value['adsTrust'])) {
                        $gettrans_ads = $value['adsTrust']->translations()->pluck('value', 'key')->toArray();
                    }
                    $orderList[$key]['en_ads_name'] = ($value['adsTrust']['name'] ?? "");
                    $orderList[$key]['hi_ads_name'] = ($gettrans_ads['name'] ?? "");

                    $gettrans_trust = [];
                    if (!empty($value['getTrust'])) {
                        $gettrans_trust = $value['getTrust']->translations()->pluck('value', 'key')->toArray();
                    }
                    $orderList[$key]['en_trust_name'] = ($value['getTrust']['trust_name'] ?? "");
                    $orderList[$key]['hi_trust_name'] = ($gettrans_trust['trust_name'] ?? "");
                    if ($value['type'] == 'donate_trust') {
                        $orderList[$key]['image'] =  getValidImage(path: 'storage/app/public/donate/trust/' . $value['getTrust']['theme_image'], type: 'product');;
                    } else {
                        $orderList[$key]['image'] =  getValidImage(path: 'storage/app/public/donate/ads/' . $value['adsTrust']['image'], type: 'product');;
                    }
                }
            }
        }
        if (!empty($orderList) && count($orderList) > 0) {
            return response()->json(['status' => 1, 'message' => 'Donate Successfully', 'recode' => count($orderList), 'data' => $orderList], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 400);
    }

    public function DonateInvoice(Request $request, $id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $orderData = DonateAllTransaction::where('id', $id)->with(['users', 'getTrust', 'adsTrust'])->first();
        if ($orderData) {
            $mpdf_view = \Illuminate\Support\Facades\View::make('web-views.donate.invoice', compact('orderData'));
            \App\Utils\Helpers::gen_mpdf($mpdf_view, 'donate_order_', $orderData['id']);
            return response()->json(["status" => 1, "message" => "Invoice generated successfully."], 200);
        } else {
            return response()->json(["status" => 0, "message" => "Invoice generated Failed."], 400);
        }
    }

    public function TwoalACertificate(Request $request)
    {
        $orderData = DonateAllTransaction::where('id', $request->id)->with(['users', 'getTrust', 'adsTrust'])->first();

        if (!empty($orderData['pan_card'])) {
            $mpdf_view = \Illuminate\Support\Facades\View::make('web-views.donate.eighty-g-certificate', compact('orderData'));
            \App\Utils\Helpers::gen_mpdf($mpdf_view, '80G_', $request->id);
            return response()->json(["status" => 1, "message" => "80G generated successfully."], 200);
        } else {
            return response()->json(["status" => 0, "message" => "User Didn't Provide Pan-Card."], 400);
        }
    }

    static public function create_donate_cetificate($id)
    {
        $getData = DonateAllTransaction::where('id', $id)->with(['getTrust', 'adsTrust', 'PancardValid'])->first();
        $certificate = \Intervention\Image\Facades\Image::make(public_path('assets/back-end/img/certificate/format/ertiga-certificate-format.png'));
        $imageWidth = $certificate->width();
        $imageHeight = $certificate->height();
        $centerX = $imageWidth / 2;
        $centerY = 730;
        $userName = $getData['user_name'];
        if ($getData['PancardValid'] && ($getData['PancardValid']['full_name'] ?? '')) {
            $userName = ($getData['PancardValid']['full_name'] ?? $getData['user_name']);
        }
        $certificate->text(ucwords($userName), $centerX, $centerY, function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(90);
            $font->color('#0000');
            $font->align('center');
            $font->valign('center');
        });

        $serviceName = wordwrap("Pan Card " . (strtoupper($getData['pan_card'])) . ", has made a voluntary donation of " . webCurrencyConverter((float) ($getData['amount'] ?? 0)) . " to " . ($getData['getTrust']?->getRawOriginal('trust_name') ?? 'Mahakal.com Organization') . " on " . date('d M,Y h:i A', strtotime($getData['created_at'])) . " through " . (($getData['transaction_id'] == 'wallet') ? 'Wallet' : 'Online') . ".", 62, "\n", false);

        $certificate->text($serviceName, $centerX, ($centerY + 178), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(37);
            $font->color('#955a00');
            $font->align('center');
            $font->valign('center');
        });

        $certificate->text($getData['trans_id'], ($centerX - 240), ($centerY + 525), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(35);
            $font->color('#ad8429');
            $font->align('right');
        });

        $certificate->text(strtoupper($getData['pan_card']), ($centerX + 120), ($centerY + 525), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(35);
            $font->color('#ad8429');
        });


        $certificate->text(date('d M,Y h:i A', strtotime($getData['created_at'])), ($centerX - 100), ($centerY + 650), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(35);
            $font->color('#ad8429');
            $font->align('right');
        });

        $certificate->text((($getData['transaction_id'] == 'wallet') ? 'Wallet' : 'Online'), ($centerX + 170), ($centerY + 625), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(35);
            $font->color('#ad8429');
        });

        $certificate->text(webCurrencyConverter((float) ($getData['amount'] ?? 0)), ($centerX - 270), ($centerY + 745), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(35);
            $font->color('#ad8429');
            $font->align('right');
        });

        $certificate->text(Helpers::get_business_settings("company_email") ?? '', ($centerX - 385), ($centerY + 850), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(40);
            $font->color('#ad8429');
            $font->align('center');
            $font->valign('top');
        });
        $certificate->text(\Illuminate\Support\Str::after(url('/'), '://'), ($centerX), ($centerY + 850), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(40);
            $font->color('#ad8429');
            $font->align('center');
            $font->valign('top');
        });

        $certificate->text($getData['getTrust']?->getRawOriginal('gst_number') ?? '', ($centerX + 350), ($centerY + 850), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(40);
            $font->color('#ad8429');
            $font->align('center');
            $font->valign('top');
        });

        $certificate->text(Helpers::get_business_settings("company_phone") ?? '', ($centerX - 370), ($centerY + 970), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(40);
            $font->color('#ad8429');
            $font->align('center');
            $font->valign('top');
        });

        $certificate->text(($getData['getTrust']['eighty_g_number'] ?? (Helpers::get_business_settings("eighty_g_number") ?? '')), ($centerX), ($centerY + 990), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(26);
            $font->color('#ad8429');
            $font->align('center');
            $font->valign('top');
        });


        $certificate->text(($getData['getTrust']['trust_pan_card'] ?? (Helpers::get_business_settings("trust_pan_card") ?? '')), ($centerX + 290), ($centerY + 970), function ($font) {
            $font->file(public_path('fonts/NotoSans-Regular.ttf'));
            $font->size(40);
            $font->color('#ad8429');
            $font->align('left');
            $font->valign('top');
        });

        $certificatePath = 'app/public/donate/certificate/80g_' . $getData['trans_id'] . '.jpg';
        if (!file_exists(storage_path('app/public/donate/certificate'))) {
            mkdir(storage_path('app/public/donate/certificate'), 0777, true);
        }
        $certificate->save(storage_path($certificatePath));
        DonateAllTransaction::where('id', $getData['id'])->update(['ertiga_certificate' => '80g_' . $getData['trans_id'] . '.jpg']);
        return response()->download(storage_path($certificatePath), '80g_' . $getData['trans_id'] . '.jpg');
    }

    public function PanCardVerified(Request $request)
    {
        $request->validate([
            'pancard' => ['required', 'string', 'max:10'],
        ]);

        try {
            $checkData = UserPanCardVerified::where('pan_number', $request['pancard'])->first();
            if ($checkData) {
                return response()->json([
                    'status' => 1,
                    'message' => 'success.',
                    'data' => [],
                ], 200);
            }
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://quickekyc.com/api/v1/pan/pan', [
                'key' => getWebConfig(name: 'aadhaar_verification_key'),
                'id_number' => $request->pancard,
            ]);

            $message_data = $response->json();
            if ($response->successful()) {
                if ($message_data['status'] == 'success') {
                    $message_data1 = [
                        'full_name' => $message_data['data']['full_name'] ?? "",
                        'pan_number' => $message_data['data']['pan_number'] ?? "",
                        'category' => $message_data['data']['category'] ?? "",
                    ];
                    UserPanCardVerified::insert($message_data1);
                    return response()->json([
                        'status' => 1,
                        'message' => $message_data['message'] ?? 'successfully.',
                        'data' => $message_data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => $message_data['message'],
                        'data' => $message_data,
                    ], 200);
                }
            }

            return response()->json([
                'status' => 0,
                'message' => $message_data['message'] ?? 'Failed.',
                'error' => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    public function LicenseNumberVerifiedCheck(Request $request)
    {
        $request->validate([
            'license_number' => ['required'],
            'dob' => ['required']
        ]);

        try {
            $checkData = \App\Models\UserLicenseNumberVerified::where('license_number', $request['license_number'])->first();
            if ($checkData) {
                return response()->json([
                    'status' => 1,
                    'message' => 'success.',
                    'data' => [],
                ], 200);
            }
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://quickekyc.com/api/v1/driving-license/driving-license', [
                'key' => getWebConfig(name: 'aadhaar_verification_key'),
                'id_number' => $request->license_number,
                'dob' => $request->dob,
            ]);
            $message_data = $response->json();
            if ($response->successful()) {
                if ($message_data['status'] == 'success') {
                    $message_data1 = [
                        "license_number" =>  $message_data['data']['license_number'] ?? "",
                        "state" =>  $message_data['data']['state'] ?? "",
                        "name" =>  $message_data['data']['name'] ?? "",
                        "permanent_address" =>  $message_data['data']['permanent_address'] ?? "",
                        "permanent_zip" =>  $message_data['data']['permanent_zip'] ?? "",
                        "temporary_address" =>  $message_data['data']['temporary_address'] ?? "",
                        "temporary_zip" =>  $message_data['data']['temporary_zip'] ?? "",
                        "citizenship" =>  $message_data['data']['citizenship'] ?? "",
                        "ola_name" =>  $message_data['data']['ola_name'] ?? "",
                        "ola_code" =>  $message_data['data']['ola_code'] ?? "",
                        "gender" =>  $message_data['data']['gender'] ?? "",
                        "father_or_husband_name" =>  $message_data['data']['father_or_husband_name'] ?? "",
                        "dob" =>  $message_data['data']['dob'] ?? "",
                        "doe" =>  $message_data['data']['doe'] ?? "",
                        "transport_doe" =>  $message_data['data']['transport_doe'] ?? "",
                        "doi" =>  $message_data['data']['doi'] ?? "",
                        "transport_doi" =>  $message_data['data']['transport_doi'] ?? "",
                        "profile_image" =>  $message_data['data']['profile_image'] ?? "",
                        "has_image" =>  $message_data['data']['has_image'] ?? "",
                        "blood_group" =>  $message_data['data']['blood_group'] ?? "",
                        "vehicle_classes" =>  json_encode($message_data['data']['vehicle_classes'] ?? "[]"),
                        "less_info" =>  $message_data['data']['less_info'] ?? "",
                        "additional_check" =>  json_encode($message_data['data']['additional_check'] ?? "[]"),
                        "initial_doi" =>  $message_data['data']['initial_doi'] ?? ""
                    ];
                    \App\Models\UserLicenseNumberVerified::insert($message_data1);
                    return response()->json([
                        'status' => 1,
                        'message' => $message_data['message'] ?? 'successfully.',
                        'data' => $message_data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => $message_data['message'],
                        'data' => $message_data,
                    ], 200);
                }
            }

            return response()->json([
                'status' => 0,
                'message' => $message_data['message'] ?? 'Failed.',
                'error' => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 200);
        }
    }
}
