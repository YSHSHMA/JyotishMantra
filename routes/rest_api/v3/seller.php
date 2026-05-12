<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Seller Mobile APP API Routes
|--------------------------------------------------------------------------
|*/

Route::group(['namespace' => 'RestAPI\v3\seller', 'prefix' => 'v3/seller', 'middleware' => ['api_lang']], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'auth'], function () {
        Route::post('login', 'LoginController@login');
        Route::post('forgot-password', 'ForgotPasswordController@reset_password_request');
        Route::post('verify-otp', 'ForgotPasswordController@otp_verification_submit');
        Route::put('reset-password', 'ForgotPasswordController@reset_password_submit');
    });

    Route::group(['prefix' => 'registration', 'namespace' => 'auth'], function () {
        Route::post('/', 'RegisterController@store');
    });

    Route::group(['middleware' => ['seller_api_auth']], function () {
        Route::put('language-change', 'SellerController@language_change');
        Route::get('seller-info', 'SellerController@seller_info');
        Route::get('get-earning-statitics', 'SellerController@get_earning_statitics');
        Route::get('order-statistics', 'SellerController@order_statistics');
        Route::get('account-delete', 'SellerController@account_delete');
        Route::get('seller-delivery-man', 'SellerController@seller_delivery_man');
        Route::get('shop-product-reviews', 'SellerController@shop_product_reviews');
        Route::get('shop-product-reviews-status', 'SellerController@shop_product_reviews_status');
        Route::put('seller-update', 'SellerController@seller_info_update');
        Route::get('monthly-earning', 'SellerController@monthly_earning');
        Route::get('monthly-commission-given', 'SellerController@monthly_commission_given');
        Route::put('cm-firebase-token', 'SellerController@update_cm_firebase_token');

        Route::get('shop-info', 'SellerController@shop_info');
        Route::get('transactions', 'SellerController@transaction');
        Route::put('shop-update', 'SellerController@shop_info_update');

        Route::put('vacation-add', 'ShopController@vacation_add');
        Route::put('temporary-close', 'ShopController@temporary_close');

        Route::get('withdraw-method-list', 'SellerController@withdraw_method_list');
        Route::post('balance-withdraw', 'SellerController@withdraw_request');
        Route::delete('close-withdraw-request', 'SellerController@close_withdraw_request');

        Route::get('top-delivery-man', 'ProductController@top_delivery_man');

        Route::group(['prefix' => 'brands'], function () {
            Route::get('/', 'BrandController@getBrands');
        });

        Route::get('categories', 'ProductController@get_categories');

        Route::group(['prefix' => 'products'], function () {
            Route::post('upload-images', 'ProductController@upload_images');
            Route::post('upload-digital-product', 'ProductController@upload_digital_product');
            Route::post('add', 'ProductController@add_new');
            Route::get('list', 'ProductController@list');
            Route::get('details/{id}', 'ProductController@details');
            Route::get('stock-out-list', 'ProductController@stock_out_list');
            Route::put('status-update', 'ProductController@status_update');
            Route::get('edit/{id}', 'ProductController@edit');
            Route::put('update/{id}', 'ProductController@update');
            Route::get('review-list/{id}', 'ProductController@review_list');
            Route::put('quantity-update', 'ProductController@product_quantity_update');
            Route::delete('delete/{id}', 'ProductController@delete');
            Route::get('barcode/generate', 'ProductController@barcode_generate');
            Route::get('top-selling-product', 'ProductController@top_selling_products');
            Route::get('most-popular-product', 'ProductController@most_popular_products');
            Route::get('delete-image', 'ProductController@deleteImage');
            Route::get('get-product-images/{id}', 'ProductController@getProductImages');
        });

        Route::group(['prefix' => 'orders'], function () {
            Route::get('list', 'OrderController@list');
            Route::get('/{id}', 'OrderController@details');
            Route::put('order-detail-status/{id}', 'OrderController@order_detail_status');
            Route::put('assign-delivery-man', 'OrderController@assign_delivery_man');
            Route::put('order-wise-product-upload', 'OrderController@digital_file_upload_after_sell');
            Route::put('delivery-charge-date-update', 'OrderController@amount_date_update');

            Route::post('assign-third-party-delivery', 'OrderController@assign_third_party_delivery');
            Route::post('update-payment-status', 'OrderController@update_payment_status');

            Route::post('address-update', 'OrderController@address_update');
        });
        Route::group(['prefix' => 'refund'], function () {
            Route::get('list', 'RefundController@list');
            Route::get('refund-details', 'RefundController@refund_details');
            Route::post('refund-status-update', 'RefundController@refund_status_update');
        });

        Route::group(['prefix' => 'coupon'], function () {
            Route::get('list', 'CouponController@list');
            Route::post('store', 'CouponController@store');
            Route::put('update/{id}', 'CouponController@update');
            Route::put('status-update/{id}', 'CouponController@status_update');
            Route::delete('delete/{id}', 'CouponController@delete');
            Route::post('check-coupon', 'CouponController@check_coupon');
            Route::get('customers', 'CouponController@customers');
        });

        Route::group(['prefix' => 'shipping'], function () {
            Route::get('get-shipping-method', 'shippingController@get_shipping_type');
            Route::get('selected-shipping-method', 'shippingController@selected_shipping_type');
            Route::get('all-category-cost', 'shippingController@all_category_cost');
            Route::post('set-category-cost', 'shippingController@set_category_cost');
        });

        Route::group(['prefix' => 'shipping-method'], function () {
            Route::get('list', 'ShippingMethodController@list');
            Route::post('add', 'ShippingMethodController@store');
            Route::get('edit/{id}', 'ShippingMethodController@edit');
            Route::put('status', 'ShippingMethodController@status_update');
            Route::put('update/{id}', 'ShippingMethodController@update');
            Route::delete('delete/{id}', 'ShippingMethodController@delete');
        });

        Route::group(['prefix' => 'messages'], function () {
            Route::get('list/{type}', 'ChatController@list');
            Route::get('get-message/{type}/{id}', 'ChatController@get_message');
            Route::post('send/{type}', 'ChatController@send_message');
            Route::get('search/{type}', 'ChatController@search');
        });

        Route::group(['prefix' => 'pos'], function () {
            Route::get('get-categories', 'POSController@get_categories');
            Route::get('customers', 'POSController@customers');
            Route::post('customer-store', 'POSController@customer_store');
            Route::get('products', 'POSController@get_product_by_barcode');
            Route::get('product-list', 'POSController@product_list');
            Route::post('place-order', 'POSController@place_order');
            Route::get('get-invoice', 'POSController@get_invoice');
        });

        Route::group(['prefix' => 'delivery-man'], function () {
            Route::get('list', 'DeliveryManController@list');
            Route::post('store', 'DeliveryManController@store');
            Route::put('update/{id}', 'DeliveryManController@update');
            Route::get('details/{id}', 'DeliveryManController@details');
            Route::post('status-update', 'DeliveryManController@status');
            Route::get('delete/{id}', 'DeliveryManController@delete');
            Route::get('reviews/{id}', 'DeliveryManController@reviews');
            Route::get('order-list/{id}', 'DeliveryManController@order_list');
            Route::get('order-status-history/{id}', 'DeliveryManController@order_status_history');
            Route::get('earning/{id}', 'DeliveryManController@earning');

            Route::post('cash-receive', 'DeliveryManCashCollectController@cash_receive');
            Route::get('collect-cash-list/{id}', 'DeliveryManCashCollectController@list');

            Route::group(['prefix' => 'withdraw'], function () {
                Route::get('list', 'DeliverymanWithdrawController@list');
                Route::get('details/{id}', 'DeliverymanWithdrawController@details');
                Route::put('status-update', 'DeliverymanWithdrawController@status_update');
            });

            Route::group(['prefix' => 'emergency-contact'], function () {
                Route::get('list', 'EmergencyContactController@list');
                Route::post('store', 'EmergencyContactController@store');
                Route::put('update', 'EmergencyContactController@update');
                Route::put('status-update', 'EmergencyContactController@status_update');
                Route::delete('delete', 'EmergencyContactController@destroy');
            });
        });

        Route::group(['prefix' => 'notification'], function () {
            Route::get('/', 'ShopController@notification_index');
            Route::get('/view', 'ShopController@seller_notification_view');
        });
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('{seller_id}/all-products', 'ProductController@get_seller_all_products');
    });
    Route::post('ls-lib-update', 'LsLibController@lib_update');

    Route::group(['prefix' => 'employee', 'middleware' => ['seller_api_auth']], function () {
        Route::get('permission-module', 'TrustController@GetPermissionModule');
    });
    Route::group(['prefix' => 'trust', 'middleware' => ['seller_api_auth']], function () {
        Route::get('dashboard', 'TrustController@Dashboard');
        Route::get('trust-puja-dashboard','TrustController@TrustPujaDashboard');

        Route::get('purohit-dashboard', 'TrustController@PurohitDashboard');
        Route::get('temple-all-list', "TrustController@TempleAllList");
        Route::get('category-list', "TrustController@CategoryList");
        Route::get('bank-list', "TrustController@BankList");
        Route::get('profile', "TrustController@profileGet");
        Route::post('profileUpdate', "TrustController@profileUpdate");

        Route::get('temple-list', 'TrustController@TempleListings');
        Route::get('get-package-list', 'TrustController@TemplePackagesList');
        Route::get('get-package-datetime-list', 'TrustController@TemplePackagedateTimeList');
        Route::get('darshan-order-list', 'TrustController@DarshanOrderList');
        Route::post('darshan-order-verify', 'TrustController@DarshanOrderverify');
        Route::post('verified-user-list-get', 'TrustController@VerifiedUserFilter');

        //trust ticket pass
        Route::post('temple-package-list', 'TrustController@TemplePackageList');
        Route::post('temple-vip-ticket-booking', 'TrustController@TempleVipTicketBooking');
        Route::post('vip-ticket-get-order-id', 'TrustController@VipTicketgetorderId');
        Route::post('vip-ticket-generator-order-id', 'TrustController@VipTicketgeneratorOrderId');

        //puja sleep ticket
        Route::post('create-puja', 'TrustController@CreatePuja');
        Route::get('puja-list', 'TrustController@PujaList');
        Route::post('puja-edit', 'TrustController@PujaEdit');
        Route::post('puja-update', 'TrustController@PujaUpdate');
        Route::post('puja-delete', 'TrustController@PujaDelete');

        //puja booking
        Route::post('puja-booking', 'TrustController@pujaBooking');
        Route::post('puja-booking-get-order-id', 'TrustController@PujaBookinggetorderId');
        Route::post('puja-sleep-generator-order-id', 'TrustController@PujaSleepgeneratorOrderId');

        //trust donate order list
        Route::get('ads-list', 'TrustController@AdsList');
        Route::post('trust-donate-order', 'TrustController@TrustDonateOrder');

        //trust Order Scanner
        Route::post('get-recepit-url', 'TrustController@RecepitScannerUrl');
        Route::post('recepit-scanner-verify-success', 'TrustController@ScannerRecepitVerify');

        Route::post('list-order-user-verified',"TrustController@OrderListUserVerified");
        Route::get('order-details', 'TrustController@OrderDetails');
        Route::get('purohit-employee',"TrustController@PurohitEmployee");

        Route::group(['prefix' => 'employee'], function () {
            Route::get('service-list', 'TrustController@EmployeeServiceList');
            Route::post('add', 'TrustController@EmployeeAdd');
            Route::get('list', 'TrustController@EmployeeList');
            Route::get('single/{id}', 'TrustController@EmployeeGetById');
            Route::post('update/{id}', 'TrustController@EmployeeUpdate');
            Route::get('status-update/{id}', 'TrustController@EmployeeStatusUpdate');
        });

        Route::group(['prefix' => 'purohit'], function () {
            Route::get('list', 'TrustController@PurohitList');
            Route::post('add', 'TrustController@PurohitAdd');
            Route::post('update', 'TrustController@PurohitUpdate');
            Route::get('status-update/{id}', 'TrustController@PurohitStatusUpdate');
        });

        Route::post('trust-old-payment-success',"TrustController@TrustPaymentSettlement");

        //trust wallet
        Route::group(['prefix' => 'withdrawal'], function () {
            Route::post('add', 'TrustController@WithdrawalReqAdd');
            Route::get('list', 'TrustController@WithdrawalList');
        });
         Route::group(['prefix' => 'password'], function () {
            Route::post('reset-password', 'TrustController@reset_password_submit');
        });        
    });
    Route::group(['prefix' => 'event', 'middleware' => ['seller_api_auth']], function () {
        Route::get('dashboard', 'OrganizerController@Dashboard');
        Route::get('profile-view', 'OrganizerController@ProfileView');
        Route::post('profile-update', 'OrganizerController@ProfileUpdate');

        Route::group(['prefix' => 'employee'], function () {
            Route::get('permission', 'OrganizerController@PermissionList');
            Route::post('add-employee', 'OrganizerController@AddEmployee');
            Route::get('employe-list', 'OrganizerController@EmployeeList');
            Route::post('employee-get-byid', 'OrganizerController@EmployeeGetById');
            Route::post('employee-update', 'OrganizerController@EmployeeUpdate');
            Route::post('employee-delete', 'OrganizerController@EmployeeDelete');
            Route::post('employee-status', 'OrganizerController@EmployeeStatusUpdate');
        });

        Route::group(['prefix' => 'artist'], function () {
            Route::post('add-artist', 'OrganizerController@AddArtist');
            Route::get('artist-list', 'OrganizerController@ArtistList');
            Route::post('artist-get-byid', 'OrganizerController@ArtistGetById');
            Route::post('artist-update', 'OrganizerController@ArtistUpdate');
            Route::post('artist-delete', 'OrganizerController@ArtistDelete');
            Route::post('artist-status', 'OrganizerController@ArtistStatusUpdate');
        });
        Route::group(['prefix' => 'event'], function () {
            Route::get('category', 'OrganizerController@EventCategory');
            Route::get('package-list', 'OrganizerController@EventPackageList');
            Route::post('add', 'OrganizerController@AddEvent');
            Route::post('list', 'OrganizerController@EventList');
            Route::post('get-byid', 'OrganizerController@EventGetById');
            Route::post('update', 'OrganizerController@EventUpdate');
            Route::post('event-image-remove', 'OrganizerController@EventImageRemove');
            Route::post('delete', 'OrganizerController@EventDelete');
            Route::post('status-update', 'OrganizerController@EventStatusUpdate');
            Route::get('user-verify-list', 'OrganizerController@EventUserVerifyList');
            Route::get("today-event-list", "OrganizerController@EventTodayList");
            Route::post("scanner-qr-code", "OrganizerController@EventScannerQrCode");
        });
        Route::group(['prefix' => 'event-order'], function () {
            Route::get('event-list', 'OrganizerController@EventList_BookingPage');
            Route::post('create-order', 'OrganizerController@CreateOrder');
        });
        Route::group(['prefix' => 'order'], function () {
            Route::post('list', 'OrganizerController@OrderList');
        });
        Route::group(['prefix' => 'withdrawal'], function () {
            Route::post('add', 'OrganizerController@WithdrawalReqAdd');
            Route::get('list', 'OrganizerController@WithdrawalList');
        });
    });
    
});
