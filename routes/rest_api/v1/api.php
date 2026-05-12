<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\RestAPI\v1\AkhandJyotiController;
use App\Http\Controllers\RestAPI\v1\AstroController;
use App\Http\Controllers\RestAPI\v1\VideoController;
use App\Http\Controllers\RestAPI\v1\AstrologerControler;
use App\Http\Controllers\RestAPI\v1\CitiesControler;
use App\Http\Controllers\RestAPI\v1\TempleControler;
use App\Http\Controllers\RestAPI\v1\ServiceControler;
use App\Http\Controllers\RestAPI\v1\AppSectionController;
use App\Http\Controllers\RestAPI\v1\CitiesController;
use App\Http\Controllers\RestAPI\v1\HotelsController;
use App\Http\Controllers\RestAPI\v1\RestaurantController;
use App\Http\Controllers\RestAPI\v1\SangeetController;
use App\Http\Controllers\RestAPI\v1\ServicesController;
use App\Http\Controllers\RestAPI\v1\TempleController;
use App\Http\Controllers\RestAPI\v1\EventController;
use App\Http\Controllers\RestAPI\v1\BlogController;
use App\Http\Controllers\RestAPI\v1\SahityaController;
use App\Http\Controllers\RestAPI\v1\DonateController;
use App\Http\Controllers\RestAPI\v1\UserLogsController;
use App\Http\Controllers\RestAPI\v1\YoutubeController;
use App\Http\Controllers\RestAPI\v1\BirthJournalController;
use App\Http\Controllers\RestAPI\v1\JaapController;
use App\Http\Controllers\RestAPI\v1\TourController;
use App\Http\Controllers\RestAPI\v1\BhagwanController;
use App\Http\Controllers\RestAPI\v1\CollectorController;
use App\Http\Controllers\RestAPI\v1\DocumentVerifyController;
use App\Http\Controllers\RestAPI\v1\MandirController;
use App\Http\Controllers\RestAPI\v1\OfflinepoojaController;
use App\Http\Controllers\RestAPI\v1\SelfVehicleController;
use App\Http\Controllers\RestAPI\v1\TempleDarshan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['namespace' => 'RestAPI\v1', 'prefix' => 'v1', 'middleware' => ['api_lang']], function () {

    Route::group(['prefix' => 'auth', 'namespace' => 'auth'], function () {
        Route::post('register', 'PassportAuthController@register');
        Route::post('login', 'PassportAuthController@login');
        Route::get('logout', 'PassportAuthController@logout')->middleware('auth:api');
        Route::post('customer-login', 'PassportAuthController@customer_login');
        Route::post('customer-register', 'PassportAuthController@customer_register');
        Route::post('customer-email-update', 'PassportAuthController@customer_email_update');
        Route::post('app-download-store', 'PassportAuthController@app_download_store');



        Route::post('check-phone', 'PhoneVerificationController@check_phone');
        Route::post('resend-otp-check-phone', 'PhoneVerificationController@resend_otp_check_phone');
        Route::post('verify-phone', 'PhoneVerificationController@verify_phone');

        Route::post('check-email', 'EmailVerificationController@check_email');
        Route::post('resend-otp-check-email', 'EmailVerificationController@resend_otp_check_email');
        Route::post('verify-email', 'EmailVerificationController@verify_email');

        Route::post('forgot-password', 'ForgotPassword@reset_password_request');
        Route::post('verify-otp', 'ForgotPassword@otp_verification_submit');
        Route::put('reset-password', 'ForgotPassword@reset_password_submit');

        Route::post('social-login', 'SocialAuthController@social_login');
        Route::post('update-phone', 'SocialAuthController@update_phone');
    });

    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
    });
    Route::post("fcm_token_Update", "CustomerController@FcmTokenUpdate");

    Route::group(['prefix' => 'shipping-method', 'middleware' => 'apiGuestCheck'], function () {
        Route::get('detail/{id}', 'ShippingMethodController@get_shipping_method_info');
        Route::get('by-seller/{id}/{seller_is}', 'ShippingMethodController@shipping_methods_by_seller');
        Route::post('choose-for-order', 'ShippingMethodController@choose_for_order');
        Route::get('chosen', 'ShippingMethodController@chosen_shipping_methods');

        Route::get('check-shipping-type', 'ShippingMethodController@check_shipping_type');
    });

    Route::group(['prefix' => 'cart', 'middleware' => 'apiGuestCheck'], function () {
        Route::get('/', 'CartController@cart');
        Route::post('add', 'CartController@add_to_cart');
        Route::put('update', 'CartController@update_cart');
        Route::delete('remove', 'CartController@remove_from_cart');
        Route::delete('remove-all', 'CartController@remove_all_from_cart');
    });

    Route::group(['prefix' => 'customer/order', 'middleware' => 'apiGuestCheck'], function () {
        Route::get('get-order-by-id', 'CustomerController@get_order_by_id');
    });

    Route::post('faq', 'GeneralController@faq');
    Route::post('terms-conditions', 'GeneralController@TermAndConditions');
    Route::group(['prefix' => 'support-ticket', 'controller' => \App\Http\Controllers\RestAPI\v1\GeneralController::class], function () {
        Route::group(['prefix' => 'vendor'], function () {
            Route::post('issues', 'SupportIssuess');
            Route::post('create-support-ticket', 'SupportCreateTicket')->middleware(['auth:seller-api', 'api_lang']);
            Route::post('get-support-ticket', 'SupportgetTicket')->middleware(['auth:seller-api', 'api_lang']);
            Route::post('get-ticket-id', 'SupportgetTicketId')->middleware(['auth:seller-api', 'api_lang']);
            Route::post('reply', 'SupportReply')->middleware(['auth:seller-api', 'api_lang']);
            Route::post('close', 'SupportTicketClose')->middleware(['auth:seller-api', 'api_lang']);
        });
        Route::group(['prefix' => 'admin'], function () {
            Route::post('admin-support-ticket', 'AdminSupportgetTicket')->middleware(['auth:seller-api', 'api_lang']);
            Route::post('admin-ticket-id', 'SupportgetTicketId')->middleware(['auth:seller-api', 'api_lang']);
            Route::post('reply', 'SupportReply')->middleware(['auth:seller-api', 'api_lang']);
            Route::post('close', 'SupportTicketClose')->middleware(['auth:seller-api', 'api_lang']);
        });
    });

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', 'NotificationController@list');
        Route::get('/seen', 'NotificationController@notification_seen')->middleware('auth:api');
    });

    Route::group(['prefix' => 'attributes'], function () {
        Route::get('/', 'AttributeController@get_attributes');
    });

    Route::group(['prefix' => 'flash-deals'], function () {
        Route::get('/', 'FlashDealController@get_flash_deal');
        Route::get('products/{deal_id}', 'FlashDealController@get_products');
    });

    Route::group(['prefix' => 'deals'], function () {
        Route::get('featured', 'DealController@get_featured_deal');
    });

    Route::group(['prefix' => 'dealsoftheday'], function () {
        Route::get('deal-of-the-day', 'DealOfTheDayController@get_deal_of_the_day_product');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('reviews/{product_id}', 'ProductController@get_product_reviews');
        Route::get('rating/{product_id}', 'ProductController@get_product_rating');
        Route::get('counter/{product_id}', 'ProductController@counter');
        Route::get('shipping-methods', 'ProductController@get_shipping_methods');
        Route::get('social-share-link/{product_id}', 'ProductController@social_share_link');
        Route::post('reviews/submit', 'ProductController@submit_product_review')->middleware('auth:api');
        Route::put('review/update', 'ProductController@updateProductReview')->middleware('auth:api');
        Route::get('review/{product_id}/{order_id}', 'ProductController@getProductReviewByOrder')->middleware('auth:api');
        Route::delete('review/delete-image', 'ProductController@deleteReviewImage')->middleware('auth:api');
    });

    Route::group(['middleware' => 'apiGuestCheck'], function () {
        Route::group(['prefix' => 'products'], function () {
            Route::get('same-day-delivery', 'ProductController@get_same_day_delivery');
            Route::get('same-day-delivery/product-data', 'ProductController@get_same_day_delivery_product');
            Route::get('same-day-delivery/seller', 'ProductController@get_same_day_delivery_seller');
            Route::get('latest', 'ProductController@get_latest_products');
            Route::get('featured', 'ProductController@get_featured_products');
            Route::get('top-rated', 'ProductController@get_top_rated_products');
            Route::any('search', 'ProductController@get_searched_products');
            Route::post('filter', 'ProductController@product_filter');
            Route::any('suggestion-product', 'ProductController@get_suggestion_product');
            Route::get('details/{slug}', 'ProductController@get_product');
            Route::get('related-products/{product_id}', 'ProductController@get_related_products');
            Route::get('best-sellings', 'ProductController@get_best_sellings');
            Route::get('home-categories', 'ProductController@get_home_categories');
            Route::get('discounted-product', 'ProductController@get_discounted_product');
            Route::get('most-demanded-product', 'ProductController@get_most_demanded_product');
            Route::get('shop-again-product', 'ProductController@get_shop_again_product')->middleware('auth:api');
            Route::get('just-for-you', 'ProductController@just_for_you');
            Route::get('most-searching', 'ProductController@get_most_searching_products');
        });

        Route::group(['prefix' => 'seller'], function () {
            Route::get('{seller_id}/products', 'SellerController@get_seller_products');
            Route::get('{seller_id}/seller-best-selling-products', 'SellerController@get_seller_best_selling_products');
            Route::get('{seller_id}/seller-featured-product', 'SellerController@get_sellers_featured_product');
            Route::get('{seller_id}/seller-recommended-products', 'SellerController@get_sellers_recommended_products');
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', 'CategoryController@get_categories');
            Route::get('products/{category_id}', 'CategoryController@get_products');
            Route::get('/find-what-you-need', 'CategoryController@find_what_you_need');
        });

        Route::group(['prefix' => 'brands'], function () {
            Route::get('/', 'BrandController@get_brands');
            Route::get('products/{brand_id}', 'BrandController@get_products');
        });

        Route::group(['prefix' => 'customer'], function () {
            Route::put('cm-firebase-token', 'CustomerController@update_cm_firebase_token');

            Route::get('get-restricted-country-list', 'CustomerController@get_restricted_country_list');
            Route::get('get-restricted-zip-list', 'CustomerController@get_restricted_zip_list');

            Route::group(['prefix' => 'address'], function () {
                Route::post('add', 'CustomerController@add_new_address');
                Route::get('list', 'CustomerController@address_list');
                Route::delete('/', 'CustomerController@delete_address');
            });

            Route::group(['prefix' => 'order'], function () {
                Route::get('place', 'OrderController@place_order');
                Route::get('offline-payment-method-list', 'OrderController@offline_payment_method_list');
                Route::post('place-by-offline-payment', 'OrderController@place_order_by_offline_payment');
                Route::get('details', 'CustomerController@get_order_details');
            });
        });
    });

    // customer
    Route::group(['prefix' => 'customer/supports', 'middleware' => 'auth:api'], function () {
        Route::get('issue-type', 'CustomerController@CustomerIssuesType');
        Route::post('issues', 'CustomerController@CustomerIssuess');
        Route::post('create-support-ticket', 'CustomerController@CustomerCreateSupportTicket');
        Route::post('get-support-ticket', 'CustomerController@CustomergetSupportTicket');
        Route::post('get-ticket-id', 'CustomerController@CustomergetTicketId');
        Route::post('reply', 'CustomerController@CustomerReply');
        Route::post('close', 'CustomerController@CustomerTicketClose');
    });

    Route::group(['prefix' => 'customer', 'middleware' => 'auth:api'], function () {
        Route::get('info', 'CustomerController@info');
        Route::put('update-profile', 'CustomerController@update_profile');
        Route::get('account-delete/{id}', 'CustomerController@account_delete');
        Route::post('astro-user-profile-update', 'CustomerController@AstroUserProfileUpdate');

        Route::group(['prefix' => 'address'], function () {
            Route::get('get/{id}', 'CustomerController@get_address');
            Route::post('update', 'CustomerController@update_address');
        });

        Route::group(['prefix' => 'support-ticket'], function () {
            Route::post('create', 'CustomerController@create_support_ticket');
            Route::get('get', 'CustomerController@get_support_tickets');
            Route::get('conv/{ticket_id}', 'CustomerController@get_support_ticket_conv');
            Route::post('reply/{ticket_id}', 'CustomerController@reply_support_ticket');
            Route::get('close/{id}', 'CustomerController@support_ticket_close');
        });

        Route::group(['prefix' => 'compare'], function () {
            Route::get('list', 'CompareController@list');
            Route::post('product-store', 'CompareController@compare_product_store');
            Route::delete('clear-all', 'CompareController@clear_all');
            Route::get('product-replace', 'CompareController@compare_product_replace');
        });

        Route::group(['prefix' => 'wish-list'], function () {
            Route::get('/', 'CustomerController@wish_list');
            Route::post('add', 'CustomerController@add_to_wishlist');
            Route::delete('remove', 'CustomerController@remove_from_wishlist');
        });

        Route::group(['prefix' => 'order'], function () {
            Route::get('place-by-wallet', 'OrderController@place_order_by_wallet');
            Route::get('refund', 'OrderController@refund_request');
            Route::post('refund-store', 'OrderController@store_refund');
            Route::get('refund-details', 'OrderController@refund_details');
            Route::get('list', 'CustomerController@get_order_list');
            Route::post('deliveryman-reviews/submit', 'ProductController@submit_deliveryman_review')->middleware('auth:api');
            Route::post('again', 'OrderController@order_again');
        });
        // Chatting
        Route::group(['prefix' => 'chat'], function () {
            Route::get('list/{type}', 'ChatController@list');
            Route::get('get-messages/{type}/{id}', 'ChatController@get_message');
            Route::post('send-message/{type}', 'ChatController@send_message');
            Route::post('seen-message/{type}', 'ChatController@seen_message');
            Route::get('search/{type}', 'ChatController@search');
        });

        //wallet
        Route::group(['prefix' => 'wallet'], function () {
            Route::get('list', 'UserWalletController@list');
            Route::get('bonus-list', 'UserWalletController@bonus_list');
            Route::post('astro-wallet-update', 'UserWalletController@AstroWalletUpdate');
        });
        //loyalty
        Route::group(['prefix' => 'loyalty'], function () {
            Route::get('list', 'UserLoyaltyController@list');
            Route::post('loyalty-exchange-currency', 'UserLoyaltyController@loyalty_exchange_currency');
        });
    });

    Route::group(['prefix' => 'customer', 'middleware' => 'apiGuestCheck'], function () {
        Route::group(['prefix' => 'order'], function () {
            Route::get('digital-product-download/{id}', 'OrderController@digital_product_download');
            Route::get('digital-product-download-otp-verify', 'OrderController@digital_product_download_otp_verify');
            Route::post('digital-product-download-otp-resend', 'OrderController@digital_product_download_otp_resend');
        });
    });

    Route::group(['prefix' => 'digital-payment', 'middleware' => 'apiGuestCheck'], function () {
        Route::post('/', [PaymentController::class, 'payment']);
    });

    Route::group(['prefix' => 'add-to-fund', 'middleware' => 'auth:api'], function () {
        Route::post('/', [PaymentController::class, 'customer_add_to_fund_request']);
    });

    Route::group(['prefix' => 'order'], function () {
        Route::get('track', 'OrderController@track_by_order_id');
        Route::get('cancel-order', 'OrderController@order_cancel');
        Route::post('track-order', 'OrderController@track_order');
    });

    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', 'BannerController@get_banners');
    });

    Route::group(['prefix' => 'seller'], function () {
        Route::get('/', 'SellerController@get_seller_info');
        Route::get('list/{type}', 'SellerController@getSellerList');
        Route::get('more', 'SellerController@more_sellers');
    });
    Route::get('shopView/{slug}', 'SellerController@getShopSlug');

    Route::group(['prefix' => 'coupon', 'middleware' => 'auth:api'], function () {
        Route::get('apply', 'CouponController@apply');
    });
    Route::get('coupon/list', 'CouponController@list')->middleware('auth:api');
    Route::get('coupon/applicable-list', 'CouponController@applicable_list')->middleware('auth:api');
    Route::get('coupons/{seller_id}/seller-wise-coupons', 'CouponController@get_seller_wise_coupon');

    Route::get('get-guest-id', 'GeneralController@get_guest_id');

    //map api
    Route::group(['prefix' => 'mapapi'], function () {
        Route::get('place-api-autocomplete', 'MapApiController@place_api_autocomplete');
        Route::get('distance-api', 'MapApiController@distance_api');
        Route::get('place-api-details', 'MapApiController@place_api_details');
        Route::get('geocode-api', 'MapApiController@geocode_api');
    });

    Route::post('contact-us', 'GeneralController@contact_store');
    Route::put('customer/language-change', 'CustomerController@language_change')->middleware('auth:api');

    Route::group(['prefix' => 'astro', 'controller' => AstroController::class], function () {
        Route::post('panchang', 'panchang');
        Route::post('planate/position', 'planate_position');
        Route::post('hora', 'hora');
        Route::post('old-hora', 'old_hora');
        Route::post('chaughadiya', 'chaughadiya');
        Route::post('old-chaughadiya', 'old_chaughadiya');
        Route::post('kundali', 'kundali');
        Route::post('kundali/milan', 'kundali_milan');
        Route::get('panchang-events', 'panchang_events');
        Route::get('/import-muhurats', 'importFromJson');
        Route::get('muhurat', 'muhurat');
        Route::get('special-muhurat', 'special_muhurat');
        Route::post('fav-mantra', 'fav_mantra');
        Route::post('fav-lord', 'fav_lord');
        Route::post('fast', 'fast');
        Route::post('fav-time', 'fav_time');
        // get rashi
        Route::get('rashi/list', 'rashi_list');
        // youTube video url
        Route::get('youtube/video/category', 'youtube_video_category');
        Route::get('rashi/detail/{name}/{lang}', 'rashi_detail');
        Route::get('save-user-kundali/{user_id}', 'save_kundali_list');
        Route::delete('delete-kundali/{id}', 'delete_kundali');
        Route::delete('delete-kundali-milan/{id}', 'delete_kundali_milan');
        Route::get('save-user-kundali-milan-list/{user_id}', 'save_kundali_milan_list');
        Route::get('events-name', 'events_name');
        Route::get('north-charts', 'north_charts');
        Route::get('milan-male-charts', 'milan_male_charts');
        Route::get('milan-female-charts', 'milan_female_charts');
        Route::get('south-charts', 'south_charts');
        Route::get('get-by-youtube-category/{category_id}', 'getByYoutube_category');
        Route::get('moonimage', 'moonimage');
        Route::get('getVideosBySubcategory/{id}', 'getVideosBySubcategory');
        Route::get('counselling', 'counselling');
        Route::get('counselling_detail/{id}', 'counselling_detail');
        Route::get('shubhmuhurat', 'shubhmuhurat');
        Route::get('shubhmuhurat_detail/{id}', 'shubhmuhurat_detail');
        Route::get('videoByPlaylist', 'videoByPlaylist');
        Route::get('app-section', 'appSection');
        Route::post('generate-pdf', 'generatePdf');
    });

    Route::group(['prefix' => 'counselling', 'controller' => AstroController::class], function () {
        Route::get('order/list', 'counselling_order_list');
        Route::get('order/detail/{orderId}', 'counselling_order_detail');
    });
    //appsection 
    Route::group(['prefix' => 'appsection', 'controller' => AppSectionController::class], function () {
        Route::get('app-section', 'appSection')->middleware('auth:api');
    });
    //video 
    Route::group(['prefix' => 'video', 'controller' => VideoController::class], function () {
        Route::get('video-list-type', 'video_list_type');
        Route::get('video-by-listType/{subcategory_id?}/{list_type?}', 'videoBylistType');
    });

    Route::group(['prefix' => 'calculator', 'controller' => AstroController::class], function () {
        Route::post('rashi-namakshar', 'rashi_namakshar');
        Route::post('kalsarp-dosha', 'kalsarp_dosha');
        Route::post('manglik-dosh', 'manglik_dosh');
        Route::post('pitra-dosha', 'pitra_dosha');
        Route::post('vimshottari-dasha', 'vimshottari_dasha');
        Route::post('mool-ank', 'mool_ank');
        Route::post('gem-suggestion', 'gem_suggestion');
        Route::post('rudraksha-suggestion', 'rudraksha_suggestion');
        Route::post('prayer-suggestion', 'prayer_suggestion');
        Route::post('maha-vimshottari', 'maha_vimshottari');
    });
    Route::group(['prefix' => 'akhand/jyoti', 'controller' => AkhandJyotiController::class], function () {
        Route::post('create', 'create');
        Route::post('update', 'update');
        Route::get('list', 'list');
        Route::get('get/status', 'getStatus');
    });
    Route::group(['prefix' => 'astrologer', 'controller' => AstrologerControler::class], function () {
        Route::post('inaugration', 'inaugration');
        Route::post('login', 'login');
        Route::get('logout', 'logout');
        Route::post('change/password', 'change_password');
        Route::get('profile/detail', 'profile_detail');
        Route::post('wallet-history', 'astro_wallet_history');
        // Route::get('category','category');
        // Route::get('skill','skill');
        Route::get('gift', 'gift');
        Route::post('update/profile', 'update_profile');
        Route::post('update/address', 'update_address');
        // Astrologer Category
        Route::get('category', 'astrologerCategory');
        Route::post('astro-by-category', 'astrologerbycatrgory');
        // Route::post('update/document','update_document');
        // Route::post('update/skill','update_skill');
        // Route::post('update/detail','update_detail');
        Route::post('update/availability', 'update_availability');
        Route::get('assigned/order', 'assigned_order');
        Route::get('order/count', 'order_count');
        // Route::get('transaction', 'transaction');
        Route::get('wallet/balance', 'wallet_balance');
        Route::post('wallet/withdraw/request', 'wallet_withdraw_request');
        Route::post('wallet/history', 'astro_wallet_history');
        Route::get('wallet/withdraw/list', 'wallet_withdraw_list');
        // Route::get('service/review','service_review');
        // Route::get('user/review/{serviceId}','user_review');
        // Route::post('order/status/changed','order_status_changed');
        // Route::post('update/notification','update_notification');

        Route::group(['prefix' => 'counselling'], function () {
            // Route::group(['prefix'=>'update'],function (){
            //     Route::post('service/charges','counselling_update_service_charges');
            // });

            Route::group(['prefix' => 'order'], function () {
                Route::get('detail/{orderId}', 'counselling_order_detail'); //
                Route::post('report/upload', 'counselling_order_report_upload'); //
            });
        });

        Route::group(['prefix' => 'kundali'], function () {
            Route::get('new-old', 'KundaliNewOrder');
            Route::post('assign-order', 'KundaliOrderAssign')->middleware(['auth:influencer_api', 'api_lang']);
            Route::get('kundali-order-details/{id}', 'KundaliOrderDetails')->middleware(['auth:influencer_api', 'api_lang']);
            Route::get('pending', 'KundaliPendingOrder')->middleware(['auth:influencer_api', 'api_lang']);
            Route::get('reject', 'KundaliRejectOrder')->middleware(['auth:influencer_api', 'api_lang']);
            Route::get('approval', 'KundaliApproalOrder')->middleware(['auth:influencer_api', 'api_lang']);
            Route::post('upload-kundali', 'KundalipdfUpload')->middleware(['auth:influencer_api', 'api_lang']);
        });

        Route::group(['prefix' => 'pooja'], function () {
            // Route::group(['prefix'=>'update'],function (){
            //     Route::post('service/charges','pooja_update_service_charges');
            // });

            Route::group(['prefix' => 'order'], function () {
                Route::get('details', 'pooja_order_detail');
                Route::post('schedule/time', 'pooja_order_schedule_time');
                Route::post('golive', 'pooja_order_golive');
                Route::post('complete', 'pooja_order_complete');
            });

            // Route::get('category/change/list','category_change_list');
        });

        Route::group(['prefix' => 'vip'], function () {
            Route::group(['prefix' => 'order'], function () {
                Route::get('details', 'vip_order_detail');
                Route::post('schedule/time', 'vip_order_schedule_time');
                Route::post('golive', 'vip_order_golive');
                Route::post('complete', 'vip_order_complete');
            });
        });

        Route::group(['prefix' => 'anushthan'], function () {
            Route::group(['prefix' => 'order'], function () {
                Route::get('details', 'anushthan_order_detail');
                Route::post('schedule/time', 'anushthan_order_schedule_time');
                Route::post('golive', 'anushthan_order_golive');
                Route::post('complete', 'anushthan_order_complete');
            });
        });

        Route::group(['prefix' => 'chadhava'], function () {
            Route::group(['prefix' => 'order'], function () {
                Route::get('details', 'chadhava_order_detail');
                Route::post('schedule/time', 'chadhava_order_schedule_time');
                Route::post('golive', 'chadhava_order_golive');
                Route::post('complete', 'chadhava_order_complete');
            });
        });

        Route::group(['prefix' => 'offlinepooja'], function () {
            // Route::group(['prefix'=>'update'],function (){
            //     Route::post('service/charges','counselling_update_service_charges');
            // });

            Route::group(['prefix' => 'order'], function () {
                Route::get('detail/{orderId}', 'offlinepooja_order_detail');
                Route::post('status/changed', 'offlinepooja_order_status_changed');
            });
        });
    });

    // guruji
    Route::group(['prefix' => 'guruji', 'controller' => AstrologerControler::class], function () {
        Route::get('/', 'guruji_all')->name('guruji-all');
        Route::get('detail', 'guruji_detail')->name('guruji-detail');
        Route::get('puja', 'guruji_puja')->name('guruji-pooja');
        Route::post('puja/lead', 'guruji_puja_lead_store')->name('guruji-puja-lead');
        Route::post('puja/sankalp/store', 'guruji_puja_sankalp_store')->name('guruji-puja-sankalp-store');
        Route::get('counselling', 'guruji_counselling')->name('guruji-counselling');
        Route::post('counselling/lead', 'guruji_counselling_lead_store')->name('guruji-counselling-lead');
        Route::post('counselling/sankalp/store', 'guruji_counselling_sankalp_store')->name('guruji-counselling-sankalp-store');
    });

    Route::group(['prefix' => 'offlinepooja', 'controller' => OfflinepoojaController::class], function () {
        Route::get('category', 'category');
        Route::get('list', 'list');
        Route::get('details', 'details');
        Route::get('policy', 'policy');
        Route::post('addreview', 'add_review');
        Route::get('getreviews', 'get_review');
        Route::get('coupon/list', 'coupon_list');
        Route::post('coupon/apply', 'coupon_apply');
        Route::post('lead/store', 'lead_store');
        Route::post('lead/payment-type', 'lead_update_payment_type');
        Route::post('placeorder', 'place_order');
        Route::get('temple', 'get_temple');
        Route::post('adduserdetail', 'add_user_detail');
        Route::post('edituserdetail', 'edit_user_detail');
        Route::get('city', 'city_data');
        Route::get('city/details', 'city_details');

        Route::group(['prefix' => 'user/order'], function () {
            Route::get('list', 'order_list');
            Route::get('details', 'order_details');
            Route::post('remaining/pay', 'remaining_pay');
            Route::get('schedule/amount', 'schedule_amount');
            Route::post('schedule/pay', 'schedule_pay');
            Route::get('cancel/amount', 'cancel_amount');
            Route::post('cancel', 'order_cancel');
        });
    });

    Route::post('/stop-live-broadcast', [YoutubeController::class, 'stopLiveBroadcast']);
    Route::post('/create-live-broadcast', [YoutubeController::class, 'createLiveBroadcast']);
    Route::post('/create-live-stream', [YoutubeController::class, 'createLiveStream']);
    Route::post('/bind-live-broadcast', [YoutubeController::class, 'bindLiveBroadcastToStream']);
    Route::post('/transition-live-broadcast', [YoutubeController::class, 'transitionLiveBroadcast']);
    Route::get('/check-stream-health/{streamId}', [YoutubeController::class, 'checkStreamHealth']);


    // Pooja Services API
    Route::group(['prefix' => 'pooja', 'controller' => ServicesController::class], function () {
        Route::post('chadhavasearch', "ChadahvaSearchName")->name('chadhavasearch');
        Route::get('/', 'pooja');
        Route::get('/sub-category/{subCategoryId}', 'servicesBySubCategory');
        Route::get('sname/{slug}', 'getServiceBySlug');
        Route::get('getpooja-category/{id}', 'getpooja_category');
        Route::post('lead-store/', 'LeadStore');
        Route::post('pooja-sankalp/{orderid}', 'SankalpStore');
        Route::post('pooja-sankalp-update/{orderid}', 'SankalpUpdate');
        Route::get('orders/', 'getAllOrders');
        Route::get('all-orders/', 'getAllServiceOrders');
        Route::post('search-pooja/', 'SearchPooja');
        Route::get('pooja-details/{orderid}/', 'poojaDetails');
        Route::get('chadhava-details-order/{orderid}/', 'chadhavaDetailsOrder');
        Route::get('pooja-list', 'poojaList');
        //Vip Pooja Routes
        Route::get('allvip-pooja', 'getallVipPooja');
        Route::get('vip-details/{slug}', 'vipDetails');
        Route::post('viplead-store/', 'VipLeadStore');
        Route::post('vip-sankalp/{orderid}', 'VipSankalpStore');
        Route::post('vip-sankalp-update/{orderid}', 'VipSankalpUpdate');
        // Anushthan Routes
        Route::get('all-anushthan', 'getallAnushthan');
        Route::get('anushthan-details/{slug}', 'AnushthanDetails');
        Route::post('anushthanlead-store/', 'AnushthanLeadStore');
        Route::post('anushthan-sankalp/{orderid}', 'AnushthanSankalpStore');
        Route::post('anushthan-sankalp-update/{orderid}', 'AnushthanSankalpUpdate');
        // Chadhava Routes
        Route::get('all-chadhava', 'getallChadhava');
        Route::get('chadhava-details/{id}', 'ChadhavaDetails');
        Route::post('chadhavalead-store/', 'ChadhavaLeadStore');
        Route::post('chadhava-sankalp/{orderid}', 'ChadhavaSankalpStore');
        Route::post('chadhava-sankalp-update/{orderid}', 'ChadhavaSankalpUpdate');
        Route::get('list', 'coupon_list');
        Route::post('apply', 'coupon_apply');
        // Dann Add
        Route::post('charity-store/', 'charityStore');
        Route::post('pooja-place-order', 'poojaPlaceOrder');
        Route::post('chadhava-place-order', 'chadhavaPlaceOrder');
        Route::post('counselling-sankalp-store/', 'CounsellingSankalpStore');
        Route::post('counselling-sankalp-update/{orderid}', 'CounsellingSankalpUpdate');
        Route::post('counsellinglead-store/', 'CounsellingLeadStore');
        Route::get('wallet-balance/{customer_id}', 'getWalletBalance');
        // Track API for the Pooja
        Route::get('servicetrack/{orderid}', 'getServiceOrderTrack');
        Route::get('chadhavatrack/{orderid}', 'getChadhavaOrderTrack');
        Route::get('prashadtrack/{orderid}', 'getPrashadOrderTrack');
        // Service Review
        Route::get('ReviewService/{slug}', 'servicereview');
        Route::get('review-vip-pooja/{slug}', 'vippoojareview');
        Route::get('review-anushthan/{slug}', 'anushthanreview');
        Route::get('review-chadhava/{slug}', 'chadhavareview');
        Route::get('review-counselling/{slug}', 'counsellingreview');
        // invoice
        Route::get('onlinepooja/invoice/{id}', 'pooja_invoice');
        Route::get('counselling/invoice/{id}', 'counselling_invoice');
        Route::get('offlinepooja/invoice/{id}', 'offlinepooja_invoice');
        Route::get('vip/invoice/{id}', 'vip_invoice');
        Route::get('anushthan/invoice/{id}', 'anushthan_invoice');
        Route::get('chadhava/invoice/{id}', 'chadhava_invoice');
        Route::get('livekey/{key}', 'liveStreamData');
        Route::get('puja-devotee', 'pujadevotee');
    });



    //sangeet
    Route::group(['prefix' => 'sangeet', 'controller' => SangeetController::class], function () {
        Route::get('category/{id?}', 'sangeet_category');
        Route::get('get-by-sangeet-category/{category_id}', 'getBySangeet_category');
        Route::get('language', 'sangeet_language');
        Route::get('sangeet-details', 'getSangeetDetails');
        Route::get('sangeet-all-details', 'getSangeetAllDetails');
    });

    // Temple API
    Route::group(['prefix' => 'temple', 'controller' => TempleController::class], function () {
        Route::get('/', 'temple')->middleware('auth:api');
        Route::get('/temple', 'temple')->middleware('auth:api');
        Route::post('search-temple', 'SearchTemple');
        Route::get('category_list', 'category_list')->middleware('auth:api');
        Route::post('gettemple', 'getTemple')->middleware('auth:api');
        Route::post('gettemplebyid', 'gettemplebyid')->middleware('auth:api');
        Route::post('templeaddcomment', 'templeaddcomment')->middleware('auth:api');
        Route::post('gettemplecomment', 'gettemplecomment')->middleware('auth:api');
    });

    Route::group(['prefix' => 'temple', 'controller' => TempleDarshan::class], function () {
        Route::post('lead-add', 'LeadAdd')->middleware('auth:api');
        Route::post('darshan-booking', 'DarshanBooking')->middleware('auth:api');
        Route::post('leads-update', 'LeadUpdates')->middleware('auth:api');
        Route::post('booking-success', 'BookingSuccess');
        Route::get('booking-list', 'BookingList')->middleware('auth:api');
        Route::get('booking-list/{id}', 'BookingList')->middleware('auth:api');
        Route::get('temple-packages/{id}', 'templePackages')->middleware('auth:api');
        Route::get('temple-purohit/{templeId}', 'templePurohit')->middleware('auth:api');

        Route::get('temple-time-slot', 'templeTimeSlot')->middleware('auth:api');
        Route::get('temple-booking-package', 'PujaGetPackages')->middleware('auth:api');

        Route::post('puja-get-booking-leadupdate', 'PujaGetBookingleadUpdate')->middleware('auth:api');
        Route::post('puja-get-booking-all', 'PujaGetBookingAll')->middleware('auth:api');
    });

    // Hotel API
    Route::group(['prefix' => 'hotel', 'controller' => HotelsController::class], function () {
        Route::post('gethotels', 'gethotels');
        Route::post('gethotelbyid', 'gethotelbyid');
        Route::post('hoteladdcomment', 'hoteladdcomment');
        Route::post('gethotelcomment', 'gethotelcomment');
    });

    //Restaurant API
    Route::group(['prefix' => 'restaurant', 'controller' => RestaurantController::class], function () {
        Route::post('getrestaurant', 'getrestaurant');
        Route::post('getrestaurantbyid', 'getrestaurantbyid');
        Route::post('restaurantaddcomment', 'restaurantaddcomment');
        Route::post('getrestaurantcomment', 'getrestaurantcomment');
    });


    // Cities API
    Route::group(['prefix' => 'cities', 'controller' => CitiesController::class], function () {
        Route::get('/', 'cities');
        Route::post('getcities', 'getcities');
        Route::post('getcitiesbyid', 'getcitiesbyid');
        Route::post('citiesaddcomment', 'citiesaddcomment');
        Route::post('getcitycomment', 'getcitycomment');
    });

    Route::group(['prefix' => 'event', 'controller' => EventController::class], function () {
        Route::post("getevent", 'EventList');
        Route::get("getcategory", 'CategoryList');
        Route::post('geteventbyid', 'GetEventById');
        Route::post('event-leads', 'EventLeads');
        Route::post('event-lead-update', 'EventLeadsUpdate');
        Route::post('eventorder', 'EventOrder');
        Route::post('eventcoupon', 'EventCoupon');
        Route::post("ordersuccess", 'ordersuccess');
        Route::post('addinterested', 'AddInterested');
        Route::post('Eventaddcomment', 'Eventaddcomment');
        Route::post('geteventcomment', 'geteventcomment');
        Route::post('pay-request', 'paymentRequest');
        Route::post('createorganizer', 'createorganizer');
        Route::post('organizergetbyid', 'organizergetbyid');
        Route::post('organizerupdate', 'organizerupdate');
        Route::post('eventorderlist', 'eventorderlist');
        Route::post('event-order-pass', 'EventOrderPass');
        Route::post('get-qr-code', 'GetQRCodes');
    });

    //blog
    Route::group(['prefix' => 'blog', 'controller' => BlogController::class], function () {
        Route::get('/', 'blog');
        Route::get('get-blog-detail/{title_slug}', 'getBlogBySlug');
        Route::get('category-by-blog', 'getBlogByCategory');
        Route::get('category-blog', 'getBlogCategory');
    });

    Route::group(['prefix' => 'birth_journal', 'controller' => BirthJournalController::class], function () {
        Route::post("getbirthjournal", 'GetBirthJournal');
        Route::post("getbirthjournalbyid", 'GetBirthJournalById');
        Route::post("getcountry", 'GetCountry');
        Route::post("getstate", 'GetState');
        Route::post("createbirthpdf", 'CreateBirthPdf')->middleware('auth:api');
        Route::post("getbirthpdf", 'GetBirthPdf');
        Route::get("invoice/{id}", 'GetInvoice');
        Route::post("create-leads", 'CreateLeads')->middleware('auth:api');
    });

    //sahitya 
    Route::group(['prefix' => 'sahitya', 'controller' => SahityaController::class], function () {
        Route::get('bhagvad-geeta', 'getBhagvadGeetaData');
        Route::get('bhagvad-geeta-all', 'getAllChapterData');
        Route::get('/', 'sahitya');
        Route::get('ram-shalaka', 'ram_shalaka');
        //    Route::get('/bhagavad-geeta/chapters',  'getBhagvadGeetaChapters');
        //    Route::get('bhagavad-geeta', 'getBhagvadGeetaVerses');
    });
    Route::group(['prefix' => 'bhagwan', 'controller' => BhagwanController::class], function () {
        Route::get('/', 'getBhagwanImage');
        Route::post('bhagwan-logs', 'BhagwanLogs');
        Route::get('bhagwan-sangeet', 'getBhagwanSangeet');
        Route::post('get-by-category-name', 'getByCategoryName');
        Route::get('get-bhagwan-chadhava/{bhagwan_id}', 'getBhagwanChadhava');
        Route::get('wallpaper', 'getBhagwanWallpaper');
    });
    Route::group(['prefix' => 'jaap', 'controller' => JaapController::class], function () {
        Route::get('/', 'getAllJaap');
        Route::get('mantra/{id}', 'getMantraByJaap');
        Route::post('jaap-count', 'jaapCount');
        Route::delete('delete-jaap-count/{id}', 'deleteJaapCount');
        Route::get('get-jaap-count', 'getJaapCount');
        Route::post('ram-lekhan', 'RamLekhan');
        Route::get('get-ram-lekhan', 'getRamLekhan');
        Route::delete('delete-ram-lekhan/{id}', 'deleteRamLekhan');
        Route::post('add-mantra', 'storemantra');
        Route::post('sankalp', 'storeSankalp');
    });
    //use logs
    Route::group(['prefix' => 'userlogs', 'controller' => UserlogsController::class], function () {
        Route::post('/', [UserlogsController::class, 'logRequest']);
    });

    Route::group(['prefix' => 'donate', 'controller' => DonateController::class], function () {
        Route::get("getcategory", 'getCategory');
        Route::get("getpurpose", 'getPurpose');
        Route::post("donatetrust", 'DonateTrust');
        Route::post("trustget", 'TrustGet');
        Route::post("donateamount", 'DonateAmount');
        Route::post("donateamountsuccess", 'DonateAmountSuccess');
        Route::post("donateorder", 'DonateOrder');
        Route::post("donateamountupdate", 'DonateAmountUpdate');
        Route::post("cancel-subscription", 'CancelSubscription');
        Route::get("invoice/{id}", 'DonateInvoice');
        Route::get("twoal-a-certificate/{id}", 'create_donate_cetificate');
        Route::post('pan-card-verified-check', 'PanCardVerified');

        Route::post('license-number-verified-check', 'LicenseNumberVerifiedCheck');
    });

    Route::group(['prefix' => 'tour', 'controller' => TourController::class], function () {
        Route::get('banner', 'TourBannerShow');
        Route::post('coupon-list-type', 'couponListType')->middleware('auth:api');
        Route::post('booking-tab-tour', "BookingTabTourCalculations");
        Route::get('tour-category', 'AllCategory');
        Route::post('tour', 'AllTour')->name('tour-list');
        Route::get('get-states', "GetAllState")->middleware('auth:api');
        Route::post('get-citie-filter', "GetCitiesFilters")->middleware('auth:api');
        Route::post('get-cities', 'CitiesTour');
        Route::post('create-lead', 'TourLeads');
        Route::post('tour-by-id', 'TourById');
        Route::post('tour-find-seat-availability','TourSeatAvailability');
        Route::get('new-tours', 'NewTopTour');
        Route::post("tour-traveller-info", "travellerInfo");
        Route::post('tour-booking', [PaymentController::class, 'TourBookingApi']);
        Route::get('tour-payamount-success', [PaymentController::class, 'TourBookingSuccess']);
        Route::post('tour-booking-list', 'BookingList');
        Route::post('tour-booking-remming-pay', 'BookingOrderRemmimgPay');
        Route::post('tour-booking-policy', 'BookingOrderPolicy');
        Route::post('tour-order-cancel', 'UserTourOrderCancel');
        Route::get('tour-order-invoice/{id}', 'TourOrderInvoiceDownload')->name('tour-order-invoice');
        Route::post('add-tour-comment', 'touraddcomment');
        Route::post('get-tour-comment', 'gettourcomment');
        Route::post('search', "SearchName")->name('search');
        Route::post('tour-coupon-apply', "TourCoupon")->name('tour-coupon-apply');
        Route::post('tour-get-distance', "TourGetDistance");
        Route::post('traveller-dashboard', "dashboard");

        Route::post('cab-profile', "CabProfile");
        Route::post('cab-profile-update', "CabProfileUpdate");
        Route::post('traveller-withdrawal', "TravellerWithdrawal");
        Route::post('traveller-withdrawal-history', "TravellerWithdrawalHistory");
        Route::post('traveller-order-amount-history', "TravellerOrderAmountHistory");

        Route::post('cab-withdrawal', "CabWithdrawal");

        Route::post('pending-tour', "TourPending")->name('pending-tour');
        Route::post('tour-assign', "TourAssign")->name('tour-assign');
        Route::post('tour-assign-confirm', "TourAssignConfirm")->name('tour-assign-confirm');
        Route::post('tour-assign-cab-driver', "TourAssignCabDriver");
        Route::post('cab-tour-order-cancel', "CabTourOrdercancel");
        Route::post('cab-send-otp', "TourCabOtpSend")->name('cab-send-otp');
        Route::post('cab-inactive-update', "CabInactiveUpdate");
        Route::post('cab-tour-view', "TourCabView")->name('cab-tour-view');
        Route::post('cab-otp-verify', "TourCabOtpVerify")->name('cab-otp-verify');
        Route::get('get-type', "GetTypes");
        Route::get('get-cab-list', "GetCabList");
        Route::get('get-package-list', "GetPackageList");
        Route::get('get-language-list', "GetLanguageList");
        Route::post('tour-order-view', "VendorTourOrderView");

        Route::post('add-tour', "AddTour");
        Route::post('tour-list', "TourList");
        Route::post('tour-status-change', "TourStatusChage");
        Route::post('tour-get-id', "TourGetId");
        Route::post('tour-image-remove', "TourImageRemove");
        Route::post('tour-update', "TourUpdate");
        Route::post('tour-delete', "TourDelete");
        Route::post('tour-order-accept', 'TourOrderAccept');
        //add cab
        Route::post("add-traveller-cab", "TravellerAddCab");
        Route::post("traveller-cab-list", "TravellerCabList");
        Route::post("traveller-cab-single", "TravellerCabSingle");
        Route::post("traveller-cab-update", "TravellerCabUpdate");
        Route::post("traveller-cab-delete", "TravellerCabDelete");

        //add drivers
        Route::post("add-traveller-driver", "TravellerAddDriver");
        Route::post("traveller-driver-list", "TravellerDriverList");
        Route::post("traveller-driver-single", "TravellerDriverSingle");
        Route::post("traveller-driver-update", "TravellerDriverUpdate");
        Route::post("traveller-driver-delete", "TravellerDriverDelete");

        // vendor
        Route::group(['prefix' => 'vendor'], function () {
            Route::get('all', "all_vendor");
            Route::get('tour', 'vendor_tour');
        });
    });

    Route::group(['prefix' => 'tour-support-ticket', 'controller' => TourController::class], function () {
        Route::group(['prefix' => 'vendor'], function () {
            Route::post('issues', 'TourSupportIssuess');
            Route::post('create-support-ticket', 'TourSupportCreateTicket');
            Route::post('get-support-ticket', 'TourSupportgetTicket');
            Route::post('get-ticket-id', 'TourSupportgetTicketId');
            Route::post('reply', 'TourSupportReply');
            Route::post('close', 'TourSupportTicketClose');
        });
        Route::group(['prefix' => 'admin'], function () {
            Route::post('admin-support-ticket', 'AdminSupportgetTicket');
            Route::post('admin-ticket-id', 'AdminSupportgetTicketId');
            Route::post('reply', 'AdminSupportReply');
            Route::post('close', 'AdminSupportTicketClose');
        });
    });

    Route::group(['prefix' => 'service', 'controller' => AstroController::class], function () {
        Route::get('get/reviews/{serviceId}', 'get_review');
        Route::post('add/review', 'add_review')->middleware('auth:api');
    });
    Route::group(['prefix' => 'darshan', 'controller' => TempleDarshan::class], function () {
        Route::get('vip-pass/{barcode}', 'CreateVIPPass');
        Route::get('vip-invoice/{id}', 'DarshanOrderInvoice');
        Route::post('aadhar-send-otp', 'AadharSendOtp');
        Route::post('aadhar-otp-verify', 'AadharOtpVerify');
        Route::post('aadhar-details', 'AadharDetailsGet');
        Route::post('temple-darshan-booking-limit-check', 'TempleDarshanLimitCheck');
    });
    Route::group(['prefix' => 'document-verify', 'controller' => DocumentVerifyController::class], function () {
        Route::post("gst-number", "GstNumberVerify");
        Route::post("bank-account", "BankAccountVerify");
    });
    Route::group(['prefix' => 'self-vehicle', 'controller' => SelfVehicleController::class], function () {
        Route::post('coupon-apply', "CouponApply");
        Route::get('self-vehicle-invoice/{id}', "SelfVehicleInvoice");
    });

    Route::group(['controller' => CollectorController::class], function () {
        Route::group(['prefix' => 'collector'], function () {
            Route::post('login', 'login');
            Route::get('logout', 'logout');
            Route::get('dashboard', 'dashboard');
            Route::get('temple/detail', 'temple_detail');
            Route::get('remaining-temple', 'remaining_temple');
            Route::post('sdm/store', 'sdm_store');
            Route::get('collector-sdm-list', 'collectorSDM');
            Route::post('sdm-temple-list', 'SDMTempleList');
            Route::get('datewise/amount', 'collectorDatewiseAmount');
        });
        
        Route::group(['prefix' => 'sdm'], function () {
            Route::get('dashboard', 'sdm_dashboard');
            Route::get('temple/detail', 'sdm_temple_detail');
            Route::get('remaining-temple', 'sdm_remaining_temple');
            Route::post('employee/store', 'employee_store');
            Route::get('sdm-employee-list', 'SDMEmployeeList');
            Route::get('datewise/amount', 'sdmDatewiseAmount');

            Route::group(['prefix' => 'employee'], function () {
                Route::get('dashboard', 'sdm_employee_dashboard');
                Route::get('temple/detail', 'sdm_employee_temple_detail');
            });
        });
    });

    // mandir
    Route::group(['prefix' => 'mandir', 'controller' => MandirController::class], function () {
        Route::get('detail', 'detail');
        Route::get('slider/images', 'slider_images');
        Route::get('package', 'package');
        Route::get('customer/check', 'customer_check');
        Route::post('booking', 'booking');
        Route::get('package-timeslot',  'get_package_timeslot');
    });

    Route::get('user-suggestion-list', 'CustomerController@UserSuggestionList');
    Route::get('purohit-all-employee-list', 'CustomerController@PurohitAllEmployeeList');
    Route::post('collect-paymant-order-update', 'CustomerController@CollectPaymantOrderUpdate');
});
