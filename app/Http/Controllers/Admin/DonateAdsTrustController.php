<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\DonateAllTransactionRepositoryInterface;
use App\Contracts\Repositories\DonateCategoryRepositoryInterface;
use App\Contracts\Repositories\DonateTrustAdsRepositoryInterface;
use App\Contracts\Repositories\DonateTrustRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\DonateAdsTrustPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DonateTrustAdsRequest;
use App\Services\DonateTrustAdsService;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Traits\Payment;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Utils\Helpers;

class DonateAdsTrustController extends Controller
{

    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface  $translationRepo,
        private readonly DonateCategoryRepositoryInterface  $donatecate,
        private readonly DonateTrustRepositoryInterface  $donatetrust,
        private readonly DonateTrustAdsRepositoryInterface  $donateads,
        private readonly DonateAllTransactionRepositoryInterface $donateTrans,
    ) {}

    public function AddTrust(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $all_categorys = $this->donatecate->getListWhere(filters: ['status' => 1, 'types' => "category"], dataLimit:"all");
        $all_purpose = $this->donatecate->getListWhere(filters: ['status' => 1, 'types' => "porpose"], dataLimit:"all");
        $all_trust = []; //$this->donatetrust->getListWhere(filters:['status'=>1,'is_approve'=>1], dataLimit:"all");
        $unit_list = ["KG" => "KG", "Gram" => "Gram", "Liter" => "Liter", "Meter" => "Meter", "Centimeter" => "Centimeter", "Inch" => "Inch", "Pound" => "Pound", "Ounce" => "Ounce", "Milliliter" => "Milliliter", "Foot" => "Foot", "Yard" => "Yard", "Mile" => "Mile", "Kilometer" => "Kilometer", "Litre" => "Litre", "Square Meter" => "Square Meter", "Hectare" => "Hectare", "Acre" => "Acre", "Kilowatt" => "Kilowatt", "Watt" => "Watt", "Kilocalorie" => "Kilocalorie", "Calorie" => "Calorie", "Joule" => "Joule", "Pascal" => "Pascal", "Newton" => "Newton", "Pound per Square Inch" => "Pound per Square Inch", "British Thermal Unit" => "British Thermal Unit", "Hertz" => "Hertz", "Kilohertz" => "Kilohertz", "Revolutions per Minute" => "Revolutions per Minute", "Second" => "Second", "Minute" => "Minute", "Hour" => "Hour", "Day" => "Day", "Week" => "Week", "Month" => "Month", "Year" => "Year", "Person" => "Person", "Pieces" => "Pieces"];
        asort($unit_list);
        return view(DonateAdsTrustPath::ADDADS[VIEW], compact('unit_list', 'all_purpose', 'all_trust', 'all_categorys', 'defaultLanguage', 'languages'));
    }

    public function StoreTrust(DonateTrustAdsRequest $request, DonateTrustAdsService $service)
    {
        $dataArray  = $service->getAddData($request);
        $insert = $this->donateads->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\DonateAds', id: $insert->id);
        Toastr::success(translate('Trust_ads_added_successfully'));
        Helpers::editDeleteLogs('Donate', 'Ads Trust', 'Insert');
        return redirect()->route(DonateAdsTrustPath::LIST[REDIRECT]);
    }

    public function ADsList(Request $request)
    {
        $ads_list = $this->donateads->getListWhere(orderBy: ['id' => 'desc'], filters: ['type' => $request->get('type')], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(DonateAdsTrustPath::LIST[VIEW], compact('ads_list'));
    }
    public function AdsStatus(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->donateads->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function AdsDelete(Request $request, DonateTrustAdsService $service)
    {
        $old_data = $this->donateads->getFirstWhere(params: ['id' => $request->get('id')]);
        if (!empty($old_data)) {
            $service->deleteAdsImage($old_data);
        }
        $this->donateads->delete(params: ['id' => $request->get('id')]);
        $this->translationRepo->delete(model: 'App\Models\DonateAds', id: $request->get('id'));
        Toastr::success(translate('Ads_Deleted_successfully'));
        Helpers::editDeleteLogs('Donate', 'Ads Trust', 'Delete');
        return response()->json(['success' => 1, 'message' => translate('Ads_Deleted_successfully')], 200);
    }

    public function AdsUpdate(Request $request, $id)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $all_categorys = $this->donatecate->getListWhere(filters: ['status' => 1, 'types' => "category"], dataLimit:"all");
        $all_purpose = $this->donatecate->getListWhere(filters: ['status' => 1, 'types' => "porpose"], dataLimit:"all");
        $all_trust = []; //$this->donatetrust->getListWhere(filters:['status'=>1,'is_approve'=>1], dataLimit:"all");
        $unit_list = ["KG" => "KG", "Gram" => "Gram", "Liter" => "Liter", "Meter" => "Meter", "Centimeter" => "Centimeter", "Inch" => "Inch", "Pound" => "Pound", "Ounce" => "Ounce", "Milliliter" => "Milliliter", "Foot" => "Foot", "Yard" => "Yard", "Mile" => "Mile", "Kilometer" => "Kilometer", "Litre" => "Litre", "Square Meter" => "Square Meter", "Hectare" => "Hectare", "Acre" => "Acre", "Kilowatt" => "Kilowatt", "Watt" => "Watt", "Kilocalorie" => "Kilocalorie", "Calorie" => "Calorie", "Joule" => "Joule", "Pascal" => "Pascal", "Newton" => "Newton", "Pound per Square Inch" => "Pound per Square Inch", "British Thermal Unit" => "British Thermal Unit", "Hertz" => "Hertz", "Kilohertz" => "Kilohertz", "Revolutions per Minute" => "Revolutions per Minute", "Second" => "Second", "Minute" => "Minute", "Hour" => "Hour", "Day" => "Day", "Week" => "Week", "Month" => "Month", "Year" => "Year", "Person" => "Person", "Pieces" => "Pieces"];
        asort($unit_list);
        $old_data = $this->donateads->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        return view(DonateAdsTrustPath::UPDATEADS[VIEW], compact('old_data', 'unit_list', 'all_purpose', 'all_trust', 'all_categorys', 'defaultLanguage', 'languages'));
    }

    public function AdsUpdateSave(DonateTrustAdsRequest $request, DonateTrustAdsService $service)
    {
        $old_data = $this->donateads->getFirstWhere(params: ['id' => $request->get('id')]);
        $dataArray  = $service->getUpdateData($request, $old_data);
        $this->donateads->update(id: $request->get('id'), data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\DonateAds', id: $request->get('id'));
        Toastr::success(translate('Trust_ads_update_successfully'));
        Helpers::editDeleteLogs('Donate', 'Ads Trust', 'Update');
        return redirect()->route(DonateAdsTrustPath::LIST[REDIRECT]);
    }

    public function AdsDetails(Request $request, $id)
    {
        $old_data = $this->donateads->getFirstWhere(params: ['id' => $id], relations: ['Purpose', 'Trusts', 'category']);
        if ($old_data) {
            $ads_transaction = $this->donateTrans->getListWhere(filters: ['type' => 'donate_ads', 'trust_id' => $old_data['trust_id'], 'ads_id' => $id], relations: ['users'],dataLimit: getWebConfig(name: 'pagination_limit'));            
            return view(DonateAdsTrustPath::ADSINFO[VIEW], compact('id', 'old_data', 'ads_transaction'));
        } else {
            return redirect()->route(DonateAdsTrustPath::LIST[REDIRECT]);
        }
    }

    public function AdsAdminCommission(Request $request, $id)
    {
        $data['admin_commission'] = $request->get('admin_commission', 0);
        $this->donateads->update(id: $id, data: $data);
        Toastr::success(translate('ads_Commission_update_successfully'));
        return back();
    }

    public function AdsAmountReqSend(Request $request, $id, $status)
    {
        $data['is_approve'] = 2;
        if (!empty($request->get('amount'))) {
            $data['approve_amount'] = $request->get('amount');
            $old_data = $this->donateads->getFirstWhere(params: ['id' => $id], relations: ['Trusts']);

            $getAlready = $this->donateTrans->getFirstWhere(params: ['ads_id' => $old_data['id'], 'trust_id' => $old_data['trust_id'], 'type' => 'ad_approval', 'amount_status' => 0]);
            if (!$getAlready) {
                $inserts =  $this->donateTrans->add(data: ['amount' => $request->get('amount'), 'amount_status' => 0, 'ads_id' => $old_data['id'], 'trust_id' => $old_data['trust_id'], 'type' => 'ad_approval']);
                $transaction_id = $inserts->id;
            } else {
                $this->donateTrans->update(id: $getAlready['id'], data: ['amount' => $request->get('amount'), 'amount_status' => 0, 'ads_id' => $old_data['id'], 'trust_id' => $old_data['trust_id'], 'type' => 'ad_approval']);
                $transaction_id = $getAlready['id'];
            }

            $member_name = '';
            $member_phone = '';
            if (!empty($old_data['Trusts']['memberlist']) && is_string($old_data['Trusts']['memberlist'])) {
                $memberlists = json_decode($old_data['Trusts']['memberlist'], true);
                if (is_array($memberlists) && !empty($memberlists)) {
                    $member_name = $memberlists[0]['member_name'] ?? '';
                    $member_phone = $memberlists[0]['member_phone_no'] ?? '';
                }
            }
            $payer = new Payer(
                $member_name,
                $old_data['Trusts']['trust_email'],
                $member_phone,
                ''
            );

            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = Currency::find($default)->code;
            }
            $additional_data['trust_id'] = $old_data['trust_id'];
            $additional_data['ads_id'] = $old_data['id'];
            $additional_data['member_name'] = $member_name;
            $additional_data['type'] = 'ad_approval';
            $additional_data['member_phone'] = $member_phone;
            $additional_data['donate_all_transaction_id'] = $transaction_id;
            $additional_data['business_name'] = 'Trust Ad Amount';
            $additional_data['business_logo'] = asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo');

            $payment_info = new PaymentInfo(
                success_hook: 'add_fund_to_wallet_success',
                failure_hook: 'add_fund_to_wallet_fail',
                currency_code: $currency_code,
                payment_method: 'razor_pay',
                payment_platform: "web",
                payer_id: $old_data['id'],
                receiver_id: '100',
                additional_data: $additional_data,
                payment_amount: $request->get('amount'),
                external_redirect_link: route('payment.success-transaction-adstust'),
                attribute: 'add_funds_to_wallet',
                attribute_id: idate("U"),
            );
            $receiver_info = new Receiver('receiver_name', 'example.png');
            $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
            $this->donateTrans->update(id: $transaction_id, data: ['transction_link' => $redirect_link]);
            $getAlready = $this->donateads->getFirstWhere(params: ['id' => $id], relations: ['Trusts']);
            $data['req_send_date']  = date('Y-m-d H:i:s');
            \App\Utils\Helpers::TemplateTextEmail('tour', 'ads_approval_request', ['attachment' => $redirect_link, 'trust_name' => $getAlready['Trusts']['trust_name'] ?? "", 'ad_name' => $getAlready['name'] ?? '', 'vendor_email' => $getAlready['Trusts']['trust_email'] ?? ""]);
        }
        $this->donateads->update(id: $id, data: $data);
        Toastr::success(translate('Ads_trust_verification_status_change_successfully'));
        return back();
    }

    public function DonateTrustList(Request $request)
    {
        $id = $request->get('id');
        $ads_transaction = $this->donatetrust->getListWhere(filters: ['category_id' => $id, 'status' => 1, 'is_approve' => 1], dataLimit:"all");
        if (!empty($ads_transaction) && count($ads_transaction) > 0) {
            return response()->json(['success' => 1, 'data' => $ads_transaction], 200);
        } else {
            return response()->json(['success' => 0, 'data' => []], 200);
        }
    }
}