<?php

namespace App\Providers;

use App\Utils\Helpers;
use App\Enums\GlobalConstant;
use App\Models\Banner;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\SocialMedia;
use App\Models\Tag;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\Product;
use App\Traits\AddonHelper;
use App\Traits\ThemeHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

ini_set('memory_limit',-1);
ini_set('upload_max_filesize','180M');
ini_set('post_max_size','200M');

class AppServiceProvider extends ServiceProvider
{

    use AddonHelper;
    use ThemeHelper;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Amirami\Localizator\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        if (!App::runningInConsole()) {
            Paginator::useBootstrap();

            Config::set('addon_admin_routes', $this->get_addon_admin_routes());
            Config::set('get_payment_publish_status', $this->get_payment_publish_status());

            Config::set('get_theme_routes', $this->get_theme_routes());

            try {
                if (Schema::hasTable('business_settings')) {

                    $web = BusinessSetting::all();
                    $settings = Helpers::get_settings($web, 'colors');
                    $data = json_decode($settings['value'], true);

                    $web_config = [
                        'primary_color' => $data['primary'],
                        'secondary_color' => $data['secondary'],
                        'primary_color_light' => isset($data['primary_light']) ? $data['primary_light'] : '',
                        'name' => Helpers::get_settings($web, 'company_name'),
                        'phone' => Helpers::get_settings($web, 'company_phone'),
                        'web_logo' => Helpers::get_settings($web, 'company_web_logo'),
                        'mob_logo' => Helpers::get_settings($web, 'company_mobile_logo'),
                        'fav_icon' => Helpers::get_settings($web, 'company_fav_icon'),
                        'email' => Helpers::get_settings($web, 'company_email'),
                        'about' => Helpers::get_settings($web, 'about_us'),
                        'footer_logo' => Helpers::get_settings($web, 'company_footer_logo'),
                        'copyright_text' => Helpers::get_settings($web, 'company_copyright_text'),
                        'decimal_point_settings' => !empty(\App\Utils\Helpers::get_business_settings('decimal_point_settings')) ? \App\Utils\Helpers::get_business_settings('decimal_point_settings') : 0,
                        'seller_registration' => BusinessSetting::where(['type' => 'seller_registration'])->first()->value,
                        'wallet_status' => Helpers::get_business_settings('wallet_status'),
                        'loyalty_point_status' => Helpers::get_business_settings('loyalty_point_status'),
                        'guest_checkout_status' => Helpers::get_business_settings('guest_checkout'),
                    ];

                    if ((!Request::is('admin') && !Request::is('admin/*') && !Request::is('seller/*') && !Request::is('vendor/*')) || Request::is('vendor/auth/registration/*') ) {
                        $flash_deals = FlashDeal::with(['products.product.reviews', 'products.product' => function ($query) {
                            $query->active()->with(['wishList' => function ($query) {
                                return $query->where('customer_id', Auth::guard('customer')->user() ? Auth::guard('customer')->user()->id : 0);
                            }]);
                        }])->where(['deal_type' => 'flash_deal', 'status' => 1])
                            ->whereDate('start_date', '<=', date('Y-m-d'))
                            ->whereDate('end_date', '>=', date('Y-m-d'))
                            ->first();

                        $featured_deals = Product::active()
                            ->with([
                                'seller.shop',
                                'flashDealProducts.featureDeal',
                                'flashDealProducts.featureDeal' => function ($query) {
                                    return $query->whereDate('start_date', '<=', date('Y-m-d'))
                                        ->whereDate('end_date', '>=', date('Y-m-d'));
                                }
                            ])
                            ->withCount('reviews')
                            ->whereHas('flashDealProducts.featureDeal', function ($query) {
                                $query->whereDate('start_date', '<=', date('Y-m-d'))
                                    ->whereDate('end_date', '>=', date('Y-m-d'));
                            })
                            ->get();

                        if ($featured_deals) {
                            foreach ($featured_deals as $product) {
                                $flash_deal_status = 0;
                                $flash_deal_end_date = 0;

                                foreach ($product->flashDealProducts as $deal) {
                                    $flash_deal_status = $deal->flashDeal ? 1 : $flash_deal_status;
                                    $flash_deal_end_date = isset($deal->flashDeal->end_date) ? date('Y-m-d H:i:s', strtotime($deal->flashDeal->end_date)) : $flash_deal_end_date;
                                }

                                $product['flash_deal_status'] = $flash_deal_status;
                                $product['flash_deal_end_date'] = $flash_deal_end_date;
                            }
                        }

                        $shops = Shop::whereHas('seller', function ($query) {
                            return $query->approved();
                        })->take(9)->get();

                        $recaptcha = Helpers::get_business_settings('recaptcha');
                        $socials_login = Helpers::get_business_settings('social_login');
                        $social_login_text = false;
                        foreach ($socials_login as $socialLoginService) {
                            if (isset($socialLoginService) && $socialLoginService['status'] == true) {
                                $social_login_text = true;
                            }
                        }

                        $popup_banner = Banner::inRandomOrder()->where('theme', theme_root_path())->where(['published' => 1, 'banner_type' => 'Popup Banner'])->first();

                        $header_banner = Banner::where('banner_type', 'Header Banner')->where('published', 1)->latest()->first();

                        $paymentGatewayPublishedStatus = 0; // Set a default value

                        $paymentPublishedStatus = config('get_payment_publish_status');
                        if (isset($paymentPublishedStatus[0]['is_published'])) {
                            $paymentGatewayPublishedStatus = $paymentPublishedStatus[0]['is_published'];
                        }

                        $paymentsGatewaysList = [];
                        $paymentGatewaysQuery = Setting::whereIn('settings_type', ['payment_config'])->where('is_active', 1);
                        if ($paymentGatewayPublishedStatus == 1) {
                            $paymentsGatewaysList = $paymentGatewaysQuery->select('key_name', 'additional_data')->get();
                        } else {
                            $paymentsGatewaysList = $paymentGatewaysQuery->whereIn('key_name', GlobalConstant::DEFAULT_PAYMENT_GATEWAYS)->select('key_name', 'additional_data')->get();
                        }

                        $referralEarningStatus = BusinessSetting::where('type', 'ref_earning_status')->first()->value ?? 0;

                        $web_config += [
                            'cookie_setting' => Helpers::get_settings($web, 'cookie_setting'),
                            'announcement' => Helpers::get_business_settings('announcement'),
                            'currency_model' => Helpers::get_business_settings('currency_model'),
                            'currencies' => Currency::where('status', 1)->get(),
                            'main_categories' => Category::with(['childes.childes'])->where('position', 0)->priority()->get(),
                            'business_mode' => Helpers::get_business_settings('business_mode'),
                            'social_media' => SocialMedia::where('active_status', 1)->get(),
                            'ios' => Helpers::get_business_settings('download_app_apple_stroe'),
                            'android' => Helpers::get_business_settings('download_app_google_stroe'),
                            'refund_policy' => Helpers::get_business_settings('refund-policy'),
                            'return_policy' => Helpers::get_business_settings('return-policy'),
                            'cancellation_policy' => Helpers::get_business_settings('cancellation-policy'),
                            'flash_deals' => $flash_deals,
                            'featured_deals' => $featured_deals,
                            'shops' => $shops,
                            'brand_setting' => Helpers::get_business_settings('product_brand'),
                            'discount_product' => Product::with(['reviews'])->active()->withCount('reviews')->where('discount', '!=', 0)->count(),
                            'recaptcha' => $recaptcha,
                            'socials_login' => $socials_login,
                            'social_login_text' => $social_login_text,
                            'popup_banner' => $popup_banner,
                            'header_banner' => $header_banner,
                            'payments_list' => $paymentsGatewaysList, // fashion_theme
                            'ref_earning_status' => $referralEarningStatus,
                        ];

                        if (theme_root_path() == "theme_fashion") {

                            $features_section = [
                                'features_section_top' => BusinessSetting::where('type', 'features_section_top')->first() ? BusinessSetting::where('type', 'features_section_top')->first()->value : [],
                                'features_section_middle' => BusinessSetting::where('type', 'features_section_middle')->first() ? BusinessSetting::where('type', 'features_section_middle')->first()->value : [],
                                'features_section_bottom' => BusinessSetting::where('type', 'features_section_bottom')->first() ? BusinessSetting::where('type', 'features_section_bottom')->first()->value : [],
                            ];

                            $tags = Tag::orderBy('visit_count', 'desc')->take(15)->get();

                            $total_discount_products = Product::active()->withCount('reviews')->where('discount', '!=', '0')->count();

                            $web_config += [
                                'tags' => $tags,
                                'features_section' => $features_section,
                                'total_discount_products' => $total_discount_products,
                                'products_stock_limit' => Helpers::get_settings($web, 'stock_limit')->value,
                            ];
                        }
                    }

                    //language
                    $language = BusinessSetting::where('type', 'language')->first();

                    //currency
                    \App\Utils\Helpers::currency_load();

                    View::share(['web_config' => $web_config, 'language' => $language]);

                    Schema::defaultStringLength(191);
                }
            } catch (\Exception $exception) {

            }
        }

        /**
         * Paginate a standard Laravel Collection.
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         * @return array
         */

        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

    }
}
