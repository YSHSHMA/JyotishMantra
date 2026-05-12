<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\PaymentController;
/*
|--------------------------------------------------------------------------
| React API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::group(['namespace' => 'RestAPI\v4', 'prefix' => 'v4', 'middleware' => ['api_lang']], function () {
    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
    });
});

Route::group(['namespace' => 'RestAPI\v4', 'prefix' => 'v4', 'middleware' => ['api_lang', 'react']], function () {

    Route::group(['prefix' => 'auth', 'namespace' => 'auth'], function () {
        Route::post('register', 'PassportAuthController@register');
        Route::post('login', 'PassportAuthController@login');

        Route::post('check-phone', 'PhoneVerificationController@check_phone');
        Route::post('resend-otp-check-phone', 'PhoneVerificationController@resend_otp_check_phone');
        Route::post('verify-phone', 'PhoneVerificationController@verify_phone');

        Route::post('check-email', 'EmailVerificationController@check_email');
        Route::post('resend-otp-check-email', 'EmailVerificationController@resend_otp_check_email');
        Route::post('verify-email', 'EmailVerificationController@verify_email');

        Route::post('forgot-password', 'ForgotPassword@reset_password_request');
        Route::post('resend-otp-reset-password', 'ForgotPassword@resend_otp');
        Route::post('verify-otp', 'ForgotPassword@otp_verification_submit');
        Route::get('verify-email-token', 'ForgotPassword@verify_email_token');
        Route::put('reset-password', 'ForgotPassword@reset_password_submit');

        Route::post('social-login', 'SocialAuthController@social_login');
        Route::post('update-phone', 'SocialAuthController@update_phone');
    });

    Route::get('common-track-order', 'OrderController@common_track_order');

    Route::group(['prefix' => 'shipping-method','middleware'=>'apiGuestCheck'], function () {
        Route::get('detail/{id}', 'ShippingMethodController@get_shipping_method_info');
        Route::get('by-seller/{id}/{seller_is}', 'ShippingMethodController@shipping_methods_by_seller');
        Route::post('choose-for-order', 'ShippingMethodController@choose_for_order');
        Route::get('chosen', 'ShippingMethodController@chosen_shipping_methods');

        Route::get('check-shipping-type','ShippingMethodController@check_shipping_type');
    });

     Route::group(['prefix' => 'cart','middleware'=>'apiGuestCheck'], function () {
        Route::get('/', 'CartController@cart');
        Route::post('add', 'CartController@add_to_cart');
        Route::put('update', 'CartController@update_cart');
        Route::delete('remove', 'CartController@remove_from_cart');
        Route::delete('remove-all','CartController@remove_all_from_cart');

    });

    Route::get('faq', 'GeneralController@faq');
    Route::post('subscription', 'GeneralController@subscription');
    Route::get('social-media', 'GeneralController@social_media');

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', 'NotificationController@get_notifications');
    });

    Route::group(['prefix' => 'brands'], function () {
        Route::get('products/{brand_id}', 'BrandController@get_products');
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

    Route::get('deal-of-the-day', 'DealOfTheDayController@get_deal_of_the_day_product');

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoryController@get_categories');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('reviews/{product_id}', 'ProductController@get_product_reviews');
        Route::get('rating/{product_id}', 'ProductController@get_product_rating');
        Route::get('counter/{product_id}', 'ProductController@counter');
        Route::get('shipping-methods', 'ProductController@get_shipping_methods');
        Route::get('social-share-link/{product_id}', 'ProductController@social_share_link');
        Route::post('reviews/submit', 'ProductController@submit_product_review')->middleware('auth:api');
    });

    Route::group(['middleware' => 'apiGuestCheck'], function () {
        Route::group(['prefix' => 'products'], function () {
            Route::get('latest', 'ProductController@get_latest_products');
            Route::get('featured', 'ProductController@get_featured_products');
            Route::get('top-rated', 'ProductController@get_top_rated_products');
            Route::any('search', 'ProductController@get_searched_products');
            Route::post('product-filter', 'ProductController@product_filter');
            Route::get('details/{slug}', 'ProductController@get_product');
            Route::get('related-products/{product_id}', 'ProductController@get_related_products');
            Route::get('best-sellings', 'ProductController@get_best_sellings');
            Route::get('home-categories', 'ProductController@get_home_categories');
            ROute::get('discounted-product', 'ProductController@get_discounted_product');
            Route::get('most-demanded-product', 'ProductController@get_most_demanded_product');
        });

        Route::group(['prefix' => 'seller'], function () {
            Route::get('{seller_id}/products', 'SellerController@get_seller_products');
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('products/{category_id}', 'CategoryController@get_products');
            Route::get('popular-categories', 'CategoryController@popular_categories');
        });

        Route::group(['prefix' => 'brands'], function () {
            Route::get('products/{brand_id}', 'BrandController@get_products');
        });

        Route::group(['prefix' => 'customer'], function () {

            Route::get('get-restricted-country-list', 'CustomerController@get_restricted_country_list');
            Route::get('get-restricted-zip-list', 'CustomerController@get_restricted_zip_list');

            Route::group(['prefix' => 'address'], function () {
                Route::post('add', 'CustomerController@add_new_address');
                Route::put('update', 'CustomerController@update_address');
                Route::get('list', 'CustomerController@address_list');
            });

            Route::group(['prefix' => 'order'], function () {
                Route::get('place', 'OrderController@place_order');
                Route::get('offline-payment-method-list', 'OrderController@offline_payment_method_list');
                Route::post('place-by-offline-payment', 'OrderController@place_order_by_offline_payment');
                Route::get('details', 'CustomerController@get_order_details');
                Route::post('again', 'OrderController@order_again');
            });
        });
    });

    Route::group(['prefix' => 'customer', 'middleware' => 'auth:api'], function () {
        Route::get('info', 'CustomerController@info');
        Route::put('update-profile', 'CustomerController@update_profile');
        Route::put('cm-firebase-token', 'CustomerController@update_cm_firebase_token');
        Route::get('account-delete','CustomerController@account_delete');

        Route::group(['prefix' => 'address'], function () {
            Route::get('get/{id}', 'CustomerController@get_address');
            Route::delete('delete', 'CustomerController@delete_address');
        });

        Route::group(['prefix' => 'support-ticket'], function () {
            Route::post('create', 'CustomerController@create_support_ticket');
            Route::get('get', 'CustomerController@get_support_tickets');
            Route::get('conv/{ticket_id}', 'CustomerController@get_support_ticket_conv');
            Route::post('reply/{ticket_id}', 'CustomerController@reply_support_ticket');
            Route::post('close', 'CustomerController@support_ticket_close');
        });

        Route::group(['prefix' => 'compare'], function () {
            Route::get('list', 'CompareController@list');
            Route::post('product-store', 'CompareController@compare_product_store');
            Route::delete('clear-all', 'CompareController@clear_all');
            Route::get('product-replace', 'CompareController@compareProductReplace');
        });

        Route::group(['prefix' => 'wish-list'], function () {
            Route::get('/', 'CustomerController@wish_list');
            Route::post('add', 'CustomerController@add_to_wishlist');
            Route::delete('remove', 'CustomerController@remove_from_wishlist');
            Route::delete('clear-all', 'CustomerController@removeAllWishlist');
        });

        Route::group(['prefix' => 'order'], function () {
            Route::get('list', 'CustomerController@get_order_list');
            Route::get('get-order-by-id', 'CustomerController@get_order_by_id');
            Route::get('place-by-wallet', 'OrderController@place_order_by_wallet');
            Route::get('refund', 'OrderController@refund_request');
            Route::post('refund-store', 'OrderController@store_refund');
            Route::get('refund-details', 'OrderController@refund_details');
            Route::post('deliveryman-reviews/submit', 'ProductController@submit_deliveryman_review');
            Route::get('digital-product-download/{id}', 'OrderController@digital_product_download');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice');
        });

        Route::group(['prefix' => 'deliveryman'], function () {
            Route::get('info/{id}', 'DeliveryManController@deliveryman_info');
        });

        // Chatting
        Route::group(['prefix' => 'chat'], function () {
            Route::get('list/{type}', 'ChatController@list');
            Route::get('get-messages/{type}/{id}', 'ChatController@get_message');
            Route::post('send-message/{type}', 'ChatController@send_message');
        });

        //wallet
        Route::group(['prefix' => 'wallet'], function () {
            Route::get('list', 'UserWalletController@list');
        });
        //loyalty
        Route::group(['prefix' => 'loyalty'], function () {
            Route::get('list', 'UserLoyaltyController@list');
            Route::post('loyalty-exchange-currency', 'UserLoyaltyController@loyalty_exchange_currency');
        });

        //seller
        Route::get('visit-again-shops', 'SellerController@get_recent_ordered_shops');
    });

    Route::group(['prefix' => 'customer', 'middleware' => 'apiGuestCheck'], function () {
        Route::group(['prefix' => 'order'], function () {
            Route::get('digital-product-download/{id}', 'OrderController@digital_product_download');
            Route::get('digital-product-download-otp-verify', 'OrderController@digital_product_download_otp_verify');
            Route::post('digital-product-download-otp-resend', 'OrderController@digital_product_download_otp_resend');
        });
    });

    Route::group(['prefix' => 'digital-payment','middleware'=>'apiGuestCheck'], function () {
        Route::post('/', [PaymentController::class, 'payment']);
    });

    Route::group(['prefix' => 'add-to-fund','middleware'=>'auth:api'], function () {
        Route::get('bonus-list', 'UserWalletController@bonus_list');
        Route::post('/', [PaymentController::class, 'customer_add_to_fund_request']);
    });

    Route::group(['prefix' => 'order'], function () {
        Route::get('track', 'OrderController@track_order');
        Route::get('cancel-order','OrderController@order_cancel');
    });

    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', 'BannerController@get_banners');
    });

    Route::group(['prefix' => 'seller'], function () {
        Route::get('/', 'SellerController@get_seller_info');
        Route::get('{seller_id}/all-products', 'SellerController@get_seller_all_products');
        Route::get('top', 'SellerController@get_top_sellers');
        Route::get('all', 'SellerController@get_all_sellers');
    });

    Route::group(['prefix' => 'coupon','middleware' => 'auth:api'], function () {
        Route::get('apply', 'CouponController@apply');
        Route::get('list', 'CouponController@list');
    });
    Route::get('get-guest-id', 'GeneralController@get_guest_id');

    //map api
    Route::group(['prefix' => 'mapapi'], function () {
        Route::get('place-api-autocomplete', 'MapApiController@place_api_autocomplete');
        Route::get('distance-api', 'MapApiController@distance_api');
        Route::get('place-api-details', 'MapApiController@place_api_details');
        Route::get('geocode-api', 'MapApiController@geocode_api');
    });

    Route::post('contact-us', 'GeneralController@contact_store');
});
