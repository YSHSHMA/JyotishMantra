<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\DealOfTheDay;
use App\Models\FlashDeal;
use App\Models\HelpTopic;
use App\Models\ShippingType;
use App\Models\Shop;
use App\Models\SocialMedia;
use App\Utils\Helpers;
use App\Utils\ProductManager;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use function App\Utils\payment_gateways;

class ConfigController extends Controller
{
    public function configuration(Request $request)
    {
        $currency = Currency::where(['status'=>1])->select('id','name','symbol','code','exchange_rate','status')->get();
        $social_login = [];
        foreach (getWebConfig(name: 'social_login') as $social) {
            $config = [
                'login_medium' => $social['login_medium'],
                'status' => (bool)$social['status']
            ];
            $social_login[] = $config;
        }

        $languages = getWebConfig(name: 'language');
        $lang_array = [];
        foreach ($languages as $language) {
            $lang_array[] = array(
                'code' => $language['code'],
                'name' => Helpers::get_language_name($language['code']),
                'status' => $language['status'],
                'default' => $language['default'],
                'direction' => isset($language['direction']) ? $language['direction'] : 'ltr',
            );
        }

        $offline_payment = null;
        $offline_payment_status = getWebConfig(name: 'offline_payment')['status'] == 1 ?? 0;
        if($offline_payment_status){
            $offline_payment = [
                'name' => 'offline_payment',
                'image' => dynamicAsset(path: 'public/assets/back-end/img/pay-offline.png'),
            ];
        }

        $payment_methods = payment_gateways();
        $payment_methods->map(function ($payment) {
            $payment->additional_datas = json_decode($payment->additional_data);

            unset(
                $payment->mode,
                $payment->live_values,
                $payment->test_values,
                $payment->additional_data,
                $payment->id,
                $payment->settings_type,
                $payment->is_active,
                $payment->created_at,
                $payment->updated_at
            );
        });

        $admin_shipping = ShippingType::where('seller_id',0)->first();
        $shipping_type = isset($admin_shipping)==true?$admin_shipping->shipping_type:'order_wise';

        $companyShopBanner = getWebConfig(name: 'shop_banner');
        $company_logo = dynamicStorage(path: "storage/app/public/company/").'/'.BusinessSetting::where(['type'=>'company_web_logo'])->first()->value;
        $company_cover_image = dynamicStorage(path: "storage/app/public/logo/").'/'.$companyShopBanner;
        $company_fav_icon = dynamicStorage(path: "storage/app/public/company/").'/'.BusinessSetting::where(['type'=>'company_fav_icon'])->first()->value;
        $footer_logo = dynamicStorage(path: "storage/app/public/company/").'/'.BusinessSetting::where(['type'=>'company_footer_logo'])->first()->value;
        $android = BusinessSetting::where(['type'=>'download_app_google_stroe'])->first()->value;
        $android = json_decode($android)->link;
        $shops = Shop::whereHas('seller', function ($query) {
                    return $query->approved();
                })
                ->select('id', 'seller_id', 'name', 'slug','image', 'vacation_start_date', 'vacation_end_date', 'vacation_note', 'vacation_status', 'temporary_close')
                ->get();
        $brands = Brand::active()->take(15)->select('id', 'name', 'image', 'status')->get();

        $ios = BusinessSetting::where(['type'=>'download_app_apple_stroe'])->first()->value;
        $ios = json_decode($ios)->link;

        $flashDeal = FlashDeal::where(['deal_type' => 'flash_deal', 'status' => 1])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->first();

        $featuredDeal = FlashDeal::where(['status' => 1])
            ->where(['deal_type' => 'feature_deal'])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('end_date', '>=', date('Y-m-d'))->count();

        $configData = [
            'brand_setting' => BusinessSetting::where('type', 'product_brand')->first()->value,
            'brands' => $brands,
            'shops' => $shops,
            'digital_product_setting' => BusinessSetting::where('type', 'digital_product')->first()->value,
            'system_default_currency' => (int)getWebConfig(name: 'system_default_currency'),
            'digital_payment' => (bool)getWebConfig(name: 'digital_payment')['status'] ?? 0,
            'cash_on_delivery' => (bool)getWebConfig(name: 'cash_on_delivery')['status'] ?? 0,
            'seller_registration' => BusinessSetting::where('type', 'seller_registration')->first()->value,
            'pos_active' => BusinessSetting::where('type','seller_pos')->first()->value,
            'company_name' => getWebConfig(name: 'company_name'),
            'company_slug' => Str::slug(getWebConfig(name: 'company_name'), '-'),
            'company_address' => getWebConfig(name: 'shop_address'),
            'company_phone' => getWebConfig(name: 'company_phone'),
            'company_email' => getWebConfig(name: 'company_email'),
            'company_logo' => $company_logo,
            'company_cover_image' => $company_cover_image,
            'company_fav_icon' => $company_fav_icon,
            'footer_logo' => $footer_logo,
            'ios' => $ios,
            'android' => $android,
            'social_media' => SocialMedia::where('active_status', 1)->select('id', 'name', 'link', 'active_status')->get(),
            'copyright_text' => BusinessSetting::where(['type'=>'company_copyright_text'])->first()->value,
            'delivery_country_restriction' => getWebConfig(name: 'delivery_country_restriction'),
            'delivery_zip_code_area_restriction' => getWebConfig(name: 'delivery_zip_code_area_restriction'),
            'base_urls' => [
                'product_image_url' => ProductManager::product_image_path('product'),
                'product_thumbnail_url' => ProductManager::product_image_path('thumbnail'),
                'digital_product_url' => dynamicStorage(path: 'storage/app/public/product/digital-product'),
                'brand_image_url' => dynamicStorage(path: 'storage/app/public/brand'),
                'customer_image_url' => dynamicStorage(path: 'storage/app/public/profile'),
                'banner_image_url' => dynamicStorage(path: 'storage/app/public/banner'),
                'category_image_url' => dynamicStorage(path: 'storage/app/public/category'),
                'review_image_url' => dynamicStorage(path: 'storage/app/public'),
                'seller_image_url' => dynamicStorage(path: 'storage/app/public/seller'),
                'shop_image_url' => dynamicStorage(path: 'storage/app/public/shop'),
                'notification_image_url' => dynamicStorage(path: 'storage/app/public/notification'),
                'delivery_man_image_url' => dynamicStorage(path: 'storage/app/public/delivery-man'),
                'flag_image_url' => dynamicStorage(path: 'public/assets/front-end/img/flags'),
                'delivery_man_verification_image' => dynamicStorage(path: 'storage/app/public/delivery-man/verification-image'),
                'support_ticket_image_url' => dynamicStorage(path: 'storage/app/public/support-ticket'),
                'chatting_image_url' => dynamicStorage(path: 'storage/app/public/chatting'),
            ],
            'currency_list' => $currency,
            'currency_symbol_position' => getWebConfig(name: 'currency_symbol_position') ?? 'right',
            'business_mode'=> getWebConfig(name: 'business_mode'),
            'maintenance_mode' => (bool)getWebConfig(name: 'maintenance_mode') ?? 0,
            'language' => $lang_array,
            'unit' => Helpers::units(),
            'shipping_method' => getWebConfig(name: 'shipping_method'),
            'email_verification' => (bool)getWebConfig(name: 'email_verification'),
            'phone_verification' => (bool)getWebConfig(name: 'phone_verification'),
            'country_code' => getWebConfig(name: 'country_code'),
            'social_login' => $social_login,
            'currency_model' => getWebConfig(name: 'currency_model'),
            'forgot_password_verification' => getWebConfig(name: 'forgot_password_verification'),
            'announcement'=> getWebConfig(name: 'announcement'),
            'pixel_analytics'=> getWebConfig(name: 'pixel_analytics'),
            'software_version'=>env('SOFTWARE_VERSION'),
            'decimal_point_settings'=>getWebConfig(name: 'decimal_point_settings'),
            'inhouse_selected_shipping_type'=>$shipping_type,
            'billing_input_by_customer'=>getWebConfig(name: 'billing_input_by_customer'),
            'minimum_order_limit'=>getWebConfig(name: 'minimum_order_limit'),
            'wallet_status'=>getWebConfig(name: 'wallet_status'),
            'loyalty_point_status'=>getWebConfig(name: 'loyalty_point_status'),
            'loyalty_point_exchange_rate'=>getWebConfig(name: 'loyalty_point_exchange_rate'),
            'loyalty_point_minimum_point'=>getWebConfig(name: 'loyalty_point_minimum_point'),
            'payment_methods' => $payment_methods,
            'payment_method_image_path' => dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image'),
            'offline_payment' => $offline_payment,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'seller_login_url' => route('vendor.auth.login'),
            'minimum_order_amount_status'=> getWebConfig(name: 'minimum_order_amount_status'),
            'minimum_order_amount'=> getWebConfig(name: 'minimum_order_amount'),
            'minimum_order_amount_by_seller'=> getWebConfig(name: 'minimum_order_amount_by_seller'),
            'free_delivery_status'=>getWebConfig(name: 'free_delivery_status'),
            'free_delivery_responsibility'=>getWebConfig(name: 'free_delivery_responsibility'),
            'download_app_google_store'=>getWebConfig(name: 'download_app_google_stroe'),
            'download_app_apple_store'=>getWebConfig(name: 'download_app_apple_stroe'),
            'flash_deal'=> (bool) $flashDeal,
            'featured_deal'=> (bool) $featuredDeal>0,
            'refer_earning_status' => getWebConfig(name: 'ref_earning_status'),
            'deal_of_the_day_status' => (bool)DealOfTheDay::where('deal_of_the_days.status', 1)->first(),
            'whatsapp' => getWebConfig(name: 'whatsapp'),
            'default_location' => getWebConfig(name: 'default_location'),
            'recaptcha' => getWebConfig(name: 'recaptcha'),
            'web_color' => getWebConfig(name: 'colors'),
            'add_funds_to_wallet' => getWebConfig(name: 'add_funds_to_wallet'),
            'order_verification'=> getWebConfig(name: 'order_verification'),
            'guest_checkout'=> getWebConfig(name: 'guest_checkout'),
            'inhouse_temporary_close'=>getWebConfig(name: 'temporary_close'),
            'inhouse_vacation_add'=>getWebConfig(name: 'vacation_add'),
            'react_setup'=> getWebConfig(name: 'react_setup'),
            'refund_policy'=> ['status' => getWebConfig(name: 'refund-policy') ? getWebConfig(name: 'refund-policy')['status'] : 0],
            'return_policy'=> ['status' => getWebConfig(name: 'return-policy') ? getWebConfig(name: 'return-policy')['status'] : 0],
            'cancellation_policy'=> ['status' => getWebConfig(name: 'cancellation-policy') ? getWebConfig(name: 'cancellation-policy')['status'] : 0],
            'cookie_setting'=> getWebConfig(name: 'cookie_setting'),
        ];

        $pagesTypeArray = ['about_us', 'privacy_policy', 'faq', 'terms_and_conditions', 'refund_policy', 'return_policy', 'cancellation_policy', 'features_section_bottom'];

        if ($request['pages'] && in_array($request['pages'], $pagesTypeArray)) {
            $pagesData = [
                'about_us' => getWebConfig(name: 'about_us'),
                'privacy_policy' => getWebConfig(name: 'privacy_policy'),
                'faq' => HelpTopic::status()->orderBy('ranking')->select('id','question','answer','ranking','status')->get(),
                'terms_and_conditions' => getWebConfig(name: 'terms_condition'),
                'refund_policy' => getWebConfig(name: 'refund-policy'),
                'return_policy' => getWebConfig(name: 'return-policy'),
                'cancellation_policy' => getWebConfig(name: 'cancellation-policy'),
                'features_section_bottom' => getWebConfig(name: 'features_section_bottom'),
            ];

            return response()->json([
                $request['pages'] => $pagesData[$request['pages']]
            ]);
        }

        return response()->json($configData);
    }
}

