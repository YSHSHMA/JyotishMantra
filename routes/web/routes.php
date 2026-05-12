<?php

use App\Enums\ViewPaths\Web\ProductCompare;
use App\Enums\ViewPaths\Web\ShopFollower;
use App\Http\Controllers\Web\ProductCompareController;
use App\Http\Controllers\Web\Shop\ShopFollowerController;
use Illuminate\Support\Facades\Route;
use App\Enums\ViewPaths\Web\Pages;
use App\Enums\ViewPaths\Web\Review;
use App\Enums\ViewPaths\Web\UserLoyalty;
use App\Http\Controllers\Web\CurrencyController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ReviewController;
use App\Http\Controllers\Web\UserLoyaltyController;
use App\Http\Controllers\Payment_Methods\SslCommerzPaymentController;
use App\Http\Controllers\Payment_Methods\StripePaymentController;
use App\Http\Controllers\Payment_Methods\PaymobController;
use App\Http\Controllers\Payment_Methods\FlutterwaveV3Controller;
use App\Http\Controllers\Payment_Methods\PaytmController;
use App\Http\Controllers\Payment_Methods\PaypalPaymentController;
use App\Http\Controllers\Payment_Methods\PaytabsController;
use App\Http\Controllers\Payment_Methods\LiqPayController;
use App\Http\Controllers\Payment_Methods\RazorPayController;
use App\Http\Controllers\Payment_Methods\SenangPayController;
use App\Http\Controllers\Payment_Methods\MercadoPagoController;
use App\Http\Controllers\Payment_Methods\BkashPaymentController;
use App\Http\Controllers\Payment_Methods\PaystackController;
use App\Http\Controllers\Web\ProductDetailsController;
use App\Http\Controllers\Web\KundaliController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Web\TourVisitController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Web\WebController;
use App\Http\Controllers\RestAPI\v1\SahityaController;
use App\Http\Controllers\RestAPI\v1\SangeetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::get('safal-view', 'Web\PujaBookController@test');
Route::get('team/kabir-shah', 'TeamController@kabir')->name('team-kabir');
Route::get('team/varshaa-chaurasia', 'TeamController@varshaa')->name('team-varshaa');
// Route::get('team/himanshu-sharma', 'TeamController@himanshu')->name('team-himanshu');
Route::get('team/rahul-bathri', 'TeamController@rahul')->name('team-rahul');
Route::get('team/rishi-shriwas', 'TeamController@rishi')->name('team-rishi');

Route::get('download', 'Web\WebController@download_app')->name('download-app');
Route::get('maintenance-mode', 'Web\WebController@maintenance_mode')->name('maintenance-mode');
Route::get('error', 'Web\WebController@error_page')->name('error');
Route::get('GetOrderDetails', 'Web\ShipwayCroneController@GetOrderDetails')->name('GetOrderDetails');
Route::get('unpublishExpiredBanners', 'Web\ShipwayCroneController@unpublishExpiredBanners')->name('unpublishExpiredBanners');
Route::get('SpecialPoojaDate', 'Web\ShipwayCroneController@SpecialPoojaDate')->name('SpecialPoojaDate');
Route::get('rejectedPoojaDate', 'Web\ShipwayCroneController@rejectedPoojaDate')->name('rejectedPoojaDate');
Route::get('live-stream/{streamKey}', 'Web\LiveStreamController@LiveStreamNow')->name('live-stream');
//puja book single page
Route::get('pujabooknow/{slug}','Web\PujaBookController@pujabookNow')->name('pujabooknow');
Route::post('pujaleadStore/{slug}','Web\PujaBookController@pujaleadstore')->name('pujaleadStore');
Route::post('paymentRequest', 'Customer\PaymentController@puja_pending_payment_request')->name('paymentRequest');
Route::get('puja-pending-web-payment', 'Customer\PaymentController@admin_pooja_pending_web_payment_success')->name('puja-pending-web-payment');
// Chadhvava Book Now
Route::get('chadhavabooknow/{slug}','Web\PujaBookController@chadhavabookNow')->name('chadhavabooknow');
Route::post('chadhavaleadStore/{slug}','Web\PujaBookController@chadhavaleadstore')->name('chadhavaleadStore');
Route::post('chadhavapaymentRequest', 'Customer\PaymentController@chadhava_pending_payment_request')->name('chadhavapaymentRequest');
Route::get('chadhava-pending-web-payment', 'Customer\PaymentController@admin_chadhava_pending_web_payment_success')->name('chadhava-pending-web-payment');

// Temple and Mandir Service Single Page
Route::get('mandir/{slug}', 'Web\TempleServiceBookController@mandir_site')->name('temple.mandir');
Route::get('mandirservice/customer/check', 'Web\TempleServiceBookController@mandirCustomerCheck')->name('mandirservice.customer.check');
Route::post('mandirservice/booking', 'Web\TempleServiceBookController@mandirBooking')->name('mandirservice.booking');
Route::post('mandirservice/paymentRequestMandir', 'Customer\PaymentController@mandir_payment_request')->name('mandirservice.paymentRequestMandir');
Route::get('mandirservice/mandirpaymentRequest', 'Customer\PaymentController@mandir_web_payment_success')->name('mandirservice.mandirpaymentRequest');
Route::get('mandirservice/order/fail', 'Customer\PaymentController@mandir_order_fail');
Route::get('mandirservice/payment-success/{order_id}/{qr}', 'Web\TempleServiceBookController@mandirpaymentsuccess')->name('mandirservice.payment-success');
Route::get('mandirservice/show-qr-detail/{order_id}', 'Web\TempleServiceBookController@mandirShowQrDetail')->name('mandirservice.show-qr-detail');

Route::get('templeservice/{slug}', 'Web\TempleServiceBookController@templeservicebookNow')->name('temple.templeservice');
Route::get('/temple/package-timeslots/{package}',  'Web\TempleServiceBookController@getPackageTimeSlots')->name('temple.package.timeslots');
Route::post('temple/customer/checkOrCreate', 'Web\TempleServiceBookController@templeCustomerCheck')->name('temple.customer.checkOrCreate');
Route::post('temple/service/save', 'Web\TempleServiceBookController@templeServiceSave')->name('temple.service.save');
Route::get('temple/lead-data-temple/{mobile}', 'Web\TempleServiceBookController@GetLeadData')->name('temple.lead-data-temple');
Route::post('temple/paymentRequestTemple', 'Customer\PaymentController@temple_payment_request')->name('temple.paymentRequestTemple');
Route::get('temple/templepaymentRequest', 'Customer\PaymentController@temple_web_payment_success')->name('temple.templepaymentRequest');
Route::get('temple/order/fail', 'Customer\PaymentController@temple_order_fail');
Route::get('temple/payment-success/{order_id}/{qr}', 'Web\TempleServiceBookController@templepaymentsuccess')->name('temple.payment-success');
Route::get('temple/show-qr-detail/{order_id}', 'Web\TempleServiceBookController@templeShowQrDetail')->name('temple.show-qr-detail');

//offline puja service
Route::get('offlinepooja/order/fail', 'Customer\PaymentController@offlinepooja_order_fail');
Route::get('counselling/order/fail', 'Customer\PaymentController@counselling_order_fail');
// Guruji Puja Service Payment
Route::post('GurujipaymentRequest', 'Customer\PaymentController@guruji_puja_pending_payment_request')->name('GurujipaymentRequest');
Route::get('guruji-puja-pending-web-payment', 'Customer\PaymentController@guruji_pooja_pending_web_payment_success')->name('guruji-puja-pending-web-payment');
// Guruji Counselling SErvice Payment
Route::post('GurujipaymentRequestCounseling', 'Customer\PaymentController@guruji_counselling_pending_payment_request')->name('GurujipaymentRequestCounseling');
Route::get('guruji-counselling-pending-web-payment', 'Customer\PaymentController@guruji_counselling_pending_web_payment_success')->name('guruji-counselling-pending-web-payment');
// Sahitya 14/11/2025 By Ranu
Route::group(['prefix' => 'sahitya'], function () {
    Route::get('bhagvad-geeta', [SahityaController::class, 'getBhagvadGeetaData']);
    Route::get('/', [SahityaController::class, 'sahitya']);

});
Route::group(['prefix' => 'sangeet'], function () {
    Route::get('category/{id?}', [SangeetController::class, 'sangeet_category']);
});
Route::group(['namespace' => 'Web', 'middleware' => ['maintenance_mode', 'guestCheck']], function () {
    Route::group(['prefix' => 'product-compare', 'as' => 'product-compare.'], function () {
        Route::controller(ProductCompareController::class)->group(function () {
            Route::get(ProductCompare::INDEX[URI], 'index')->name('index');
            Route::post(ProductCompare::INDEX[URI], 'add');
            Route::get(ProductCompare::DELETE[URI], 'delete')->name('delete');
            Route::get(ProductCompare::DELETE_ALL[URI], 'deleteAllCompareProduct')->name('delete-all');
        });
    });
    Route::post(ShopFollower::SHOP_FOLLOW[URI], [ShopFollowerController::class, 'followOrUnfollowShop'])->name('shop-follow');
});

Route::group(['namespace' => 'Web', 'middleware' => ['maintenance_mode', 'guestCheck']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/shop', 'HomeController@front')->name('shop');
    Route::get('quick-view', 'WebController@getQuickView')->name('quick-view');
    Route::get('searched-products', 'WebController@searched_products')->name('searched-products');

    Route::group(['middleware' => ['customer']], function () {
        Route::controller(ReviewController::class)->group(function () {
            Route::post(Review::ADD[URI], 'add')->name('review.store');
            Route::post(Review::ADD_DELIVERYMAN_REVIEW[URI], 'addDeliveryManReview')->name('submit-deliveryman-review');
            Route::post(Review::DELETE_REVIEW_IMAGE[URI], 'deleteReviewImage')->name('delete-review-image');
        });
    });

    Route::get('checkout-details', 'WebController@checkout_details')->name('checkout-details');
    Route::get('checkout-shipping', 'WebController@checkout_shipping')->name('checkout-shipping');
    Route::get('checkout-payment', 'WebController@checkout_payment')->name('checkout-payment');
    Route::get('checkout-review', 'WebController@checkout_review')->name('checkout-review');
    Route::get('checkout-complete', 'WebController@checkout_complete')->name('checkout-complete');
    Route::post('offline-payment-checkout-complete', 'WebController@offline_payment_checkout_complete')->name('offline-payment-checkout-complete');
    Route::get('order-placed', 'WebController@order_placed')->name('order-placed');
    Route::get('shop-cart', 'WebController@shop_cart')->name('shop-cart');
    Route::post('order_note', 'WebController@order_note')->name('order_note');
    Route::get('digital-product-download/{id}', 'WebController@getDigitalProductDownload')->name('digital-product-download');
    Route::post('digital-product-download-otp-verify', 'WebController@getDigitalProductDownloadOtpVerify')->name('digital-product-download-otp-verify');
    Route::post('digital-product-download-otp-reset', 'WebController@getDigitalProductDownloadOtpReset')->name('digital-product-download-otp-reset');
    Route::get('pay-offline-method-list', 'WebController@pay_offline_method_list')->name('pay-offline-method-list')->middleware('guestCheck');

    //wallet payment
    Route::get('checkout-complete-wallet', 'WebController@checkout_complete_wallet')->name('checkout-complete-wallet');

    Route::post('subscription', 'WebController@subscription')->name('subscription');
    Route::get('search-shop', 'WebController@search_shop')->name('search-shop');

    Route::get('categories', 'WebController@all_categories')->name('categories');
    Route::get('category-ajax/{id}', 'WebController@categories_by_category')->name('category-ajax');

    Route::get('brands', 'WebController@all_brands')->name('brands');


    // rashi
    Route::get('rashi/{rashislug}', 'WebController@rashi_detail')->name('rashi-detail');

    // calculator route
    Route::get('calculator/{slug}', 'WebController@calculator')->name('calculator');

    // kundali route 
    Route::post('kundali', 'WebController@kundali')->name('kundali');
    Route::post('kundali-milan', 'WebController@kundali_milan')->name('kundali.milan');

    // panchang
    Route::get('panchang', 'WebController@panchang')->name('panchang');

    // ram shalaka
    Route::get('ram-shalaka', 'WebController@ram_shalaka')->name('ram-shalaka');

    // chaughadiya
    Route::get('chaughadiya', 'WebController@chaughadiya')->name('chaughadiya');

    // all_donate
    Route::get('donate', 'WebController@all_donate')->name('all-donate');

    Route::get('all-donate' . '/{slug}', 'WebController@DonateAdsLeads')->name('all-donate_ads');
    Route::post('donate-leads' . '/{slug}', 'WebController@DonateAdsSaveLeads')->name('donate-leads');
    Route::post('donate-lead-update', 'WebController@DonateLeadUpdates')->name('donate-lead-update');

    Route::get('donate-ads' . '/{slug}', 'WebController@DonateAds')->name('donate-trust');
    Route::get('donate-trust' . '/{slug}', 'WebController@DonateTrust')->name('all-donate_trust');
    Route::post('donate-payment-request', 'WebController@DonateRequest')->name('donate-payment-request');
    Route::get('donate-web-payment', [PaymentController::class, 'donate_payment'])->name('donate-web-payment');
    Route::get('donate-success' . '/{id}', 'WebController@Donatesuccesspage')->name('donate-success');
    Route::post('donor-submit', 'WebController@DonateSubmit')->name('donor-submit');
    Route::get("trust-puja-orders/{phone}", [PaymentController::class, 'TrustPujaOrder'])->name('trust-puja-orders');
    Route::get("trust-puja-booking-success/{id}", 'WebController@TrustPujaSuccess')->name('trust-puja-booking-success');

    //eventRoute

    Route::get("event-details/{id}", 'WebController@EventDetails')->name('event-details');
    Route::get("event-interested", 'WebController@EventInterested')->name('event-interested');
    Route::get('event', 'WebController@Event')->name("event");
    Route::get('event-leads/{id}', 'WebController@EventLeads')->name("events-leads");
    Route::get('event-booking/{id}', 'WebController@EventBooking')->name("event-booking")->middleware('customer');
    Route::post('event-booking-leads-update', 'WebController@EventBookingLeadUpdate')->name("event-booking-leads-update");
    Route::post('event-booking-leads-qty-update', 'WebController@EventBookingLeadQtyUpdate')->name("event-booking-leads-qty-update");
    Route::post('/event-payment-request/{id}/{lead}', [PaymentController::class, 'Eventpayment'])->name('event-payment-request');
    Route::get('event-pay-success/{id}/{lead}', [PaymentController::class, 'EventpaySuccess'])->name("event_pay_success");
    Route::get('event-booking-success/{id}', 'WebController@EventSuccessPage')->name("event-booking-success")->middleware('customer');
    Route::post("event-booking-free/{id}/{lead}", 'WebController@EventBookingFree')->name('event-booking-free')->middleware('customer');
    Route::post('event-booking-complete/{id}', 'WebController@EventCompletePage')->name("event-booking-complete")->middleware('customer');
    Route::get('event-create-pdf-invoice/{id}', 'UserProfileController@CreatePdfInvoice')->name('event-create-pdf-invoice')->middleware('customer');
    Route::get('event-order-details/{id}', 'UserProfileController@CreateEventPass')->name('event-order-details')->middleware('customer');
    Route::post('add-event-review', 'UserProfileController@EventComment')->name('add-event-review')->middleware('customer');
    Route::get('event-order-details/{id}/{num}', 'UserProfileController@CreateEventPass')->name('event-order-details2');
    Route::get('verify-code-event-pass/{id}/{num}', 'UserProfileController@verifyCodeEventPass')->name('verify-code-event-pass');

    // all_puja
    Route::get('all-puja', 'WebController@all_puja')->name('all-puja');

    // astrology_counseling
    Route::get('all-astrology-counseling', 'WebController@all_astrology_counseling')->name('all-astrology-counseling');

    // darshan
    // darshan
    Route::get('darshan', 'WebController@DarshanList')->name('darshan');
    Route::get('temple-details/{slug}', "WebController@TempleDetails")->name('temple-details');
    Route::get('vip-darshan-lead', "WebController@VipDarshanLead")->name('vip-darshan-lead');
    Route::get('vip-darshan-booking/{slug}/{lead}', "WebController@VipDarshanDetails")->name('vip-darshan-booking');
    Route::get('vip-darshan-lead-update', "WebController@VipDarshanLeadUpdate")->name('vip-darshan-lead-update');
    Route::get('vip-darshan-booking-pay/{slug}/{lead}', "WebController@VipDarshanBookings")->name('vip-darshan-booking-pay');
    Route::post('vip-darshan-lead-person-update', "WebController@VipDarshanUpdatePersons")->name('vip-darshan-lead-person-update');
    Route::post('temple-darshan-pay-online-now', [PaymentController::class, 'TempleDarshanBookingPay'])->name('temple-darshan-pay-online-now');
    Route::get('vip-darshan-booking-pay-received', [PaymentController::class, 'TempleDarshanBookingReceived'])->name('vip-darshan-booking-pay-received');
    Route::get('vip-darshan-booking-success/{slug}', "WebController@VipDarshanBookSuccess")->name('vip-darshan-booking-success');

    //darshan without login
    Route::get('/vip-darshan/{slug}', 'DarshanController@showForm')->name('vip.darshan.form');
    Route::post('/vip-darshan/submit', 'DarshanController@submitForm')->name('vip.darshan.submit');
    Route::post('/vip-darshan/cashsubmit', 'DarshanController@cashsubmitForm')->name('vip.darshan.cashsubmit');
    Route::get('vip-darshan-paymant-success/{slug}', [PaymentController::class,'withoutLoginTempleDarshanBookingReceived'])->name('vip-darshan-paymant-success');
    Route::get('vip-darshan-success/{slug}', 'DarshanController@vipDarshanSuccess')->name('vip-darshan-success');
    Route::post('/vip-darshan/payment/callback', 'DarshanController@razorpayCallback')->name('vip.darshan.payment.callback');
    Route::get('trust-vip-darshan-ticket/{id}', [PaymentController::class,'TrustVipDarshanTicketBookingReceived'])->name('trust-vip-darshan-ticket');

    Route::get('darshan-detail', 'WebController@darshan_detail')->name('darshan-detail');
    Route::post("temple-add-comment", 'WebController@TempleAddComment')->name('temple-add-comment')->middleware('customer');
    //cities 
    Route::get('near-cities/{id}', 'WebController@NearCitiesDetails')->name('near-cities');
    Route::post("cities-add-comment", 'WebController@CitiesAddComment')->name("cities-add-comment")->middleware('customer');

    Route::get('near-hotel/{id}', 'WebController@NearHotelDetails')->name('near-hotel');
    Route::post("hotel-add-comment", 'WebController@HotelAddComment')->name('hotel-add-comment')->middleware('customer');

    Route::get('near-restaurant/{id}', 'WebController@NearRestaurantDetails')->name('near-restaurant');
    Route::post("restaurant-add-comment", 'WebController@RestaurantAddComment')->name('restaurant-add-comment')->middleware('customer');

    Route::get('vendors', 'WebController@all_sellers')->name('vendors');
    Route::get('seller-profile/{id}', 'WebController@seller_profile')->name('seller-profile');

    Route::get('flash-deals/{id}', 'WebController@flash_deals')->name('flash-deals');

    Route::controller(PageController::class)->group(function () {
        Route::get(Pages::ABOUT_US[URI], 'getAboutUsView')->name('about-us');
        Route::get(Pages::CONTACTS[URI], 'getContactView')->name('contacts');
        Route::get(Pages::HELP_TOPIC[URI], 'getHelpTopicView')->name('helpTopic');
        Route::get(Pages::REFUND_POLICY[URI], 'getRefundPolicyView')->name('refund-policy');
        Route::get(Pages::RETURN_POLICY[URI], 'getReturnPolicyView')->name('return-policy');
        Route::get(Pages::PRIVACY_POLICY[URI], 'getPrivacyPolicyView')->name('privacy-policy');
        Route::get(Pages::CANCELLATION_POLICY[URI], 'getCancellationPolicyView')->name('cancellation-policy');
        Route::get(Pages::TERMS_AND_CONDITION[URI], 'getTermsAndConditionView')->name('terms');
    });
    
    Route::get('register-with-us', 'WebController@registerCreate')->name('register-with-us');
    Route::get('astrologers/check/email/{email}', 'WebController@checkEmail');
    Route::get('astrologers/check/mobileno/{mobile}', 'WebController@checkMobile');
    Route::post('register-personal-detail', 'WebController@registerPersonalDetail');
    Route::post('register-skill-detail', 'WebController@registerSkillDetail');
    Route::post('register-other-detail', 'WebController@registerOtherDetail');
    Route::post('register/pandit/pooja', 'WebController@register_pandit_pooja')->name('register.pandit.pooja');


    //astrologer booking
    Route::prefix('astro')->as('astro.')->controller(WebController::class)->group(function () {
        Route::get('register', 'add_astrologer')->name('add');
        // Route::post('register', 'store_astrologer')->name('store');
        Route::post('check-exist', 'astrologer_check')->name('check-exist');
        Route::post('process', 'astrologer_process')->name('process');

    });

    // Epooja Booking Routes
    Route::get('/all-puja', 'ProductDetailsController@all_puja')->name('all-puja');
    Route::get('/epooja/{slug}', 'ProductDetailsController@pooja')->name('epooja');
    Route::get('/poojastore/{slug}', 'ProductDetailsController@poojastore')->name('poojastore');
    Route::get('/poojacart/{encoded_id}', 'ProductDetailsController@poojacart')->name('poojacart');
    Route::POST('/poojaproduct/{encoded_id}', 'ProductDetailsController@poojaproductstore')->name('poojaproduct');
    Route::get('/sankalp/{order_id}', 'ProductDetailsController@sankalp')->name('sankalp');
    Route::get('/poojaCheckout/{order_id}', 'ProductDetailsController@poojaCheckout')->name('poojaCheckout');
    Route::post('/update-cart-quantity', 'ProductDetailsController@updateCartQuantity')->name('updateCartQuantity');
    Route::post('/delete-cart-quantity', 'ProductDetailsController@deleteQuantity')->name('deleteQuantity');
    // offline pooja
    Route::group(['prefix' => 'offline/pooja', 'as' => 'offline.pooja.', 'controller' => ProductDetailsController::class], function () {
        Route::get('all', 'offline_pooja_all')->name('all');
        Route::get('detail/{slug}', 'offline_pooja_detail')->name('detail');
        Route::post('lead/store', 'offline_pooja_lead_store')->name('lead.store');
        Route::get('/order/book/{encoded_id}', 'offline_pooja_order_book')->name('order.book');
        Route::get('/user/detail/{order_id}', 'offline_pooja_user_detail')->name('user.detail');
        Route::post('/user/store/', 'offline_pooja_user_store')->name('user.store');
        Route::post('/offlinepooja/update-payment-mode', [ProductDetailsController::class, 'updatePaymentMode'])->name('offlinepooja.updatePaymentMode');
        // Route::get('order-details/{order_id}', 'order_details')->name('order.details');   
    });
    // counselling
    Route::controller(ProductDetailsController::class)->as('counselling.')->prefix('counselling')->group(function () {
        // Route::get('astro/{slug}', 'counselling_index')->name('index');
        Route::get('astrology', 'counselling_index')->name('astrology');
        Route::get('load-more', 'load_more');
        Route::get('details/{slug}', 'counselling_details')->name('details');
        Route::post('lead/store', 'counselling_lead_store')->name('lead.store');
        Route::post('store/customer', 'counselling_store_customer')->name('store.customer');
        Route::get('/order/book/{encoded_id}', 'ProductDetailsController@order_book')->name('order.book');
        Route::get('/user/detail/{order_id}', 'ProductDetailsController@user_detail')->name('user.detail');
        Route::post('/user/store', 'ProductDetailsController@user_store')->name('user.store');
        // Route::get('/order/placed/{order_id}','ProductDetailsController@order_placed')->name('order.placed');
    });

    // Vip Pooja Routes
    Route::controller(ProductDetailsController::class)->as('vip.')->prefix('vip')->group(function () {
        Route::get('/vippooja/{slug}', 'ProductDetailsController@vip_details')->name('details');
        Route::post('lead/store', 'vip_lead_store')->name('lead.store');
        Route::get('/order/book/{encoded_id}', 'ProductDetailsController@vip_order_book')->name('order.book');
        Route::get('/user/detail/{order_id}', 'ProductDetailsController@vipuser_detail')->name('user.detail');
        Route::post('/user/store/', 'ProductDetailsController@vipuser_store')->name('user.store');
    });
    Route::controller(ProductDetailsController::class)->as('anushthan.')->prefix('anushthan')->group(function () {
        Route::get('/anushthan/{slug}', 'ProductDetailsController@anushthan_details')->name('details');
        Route::post('lead/store', 'anushthan_lead_store')->name('lead.store');
        Route::get('/order/book/{encoded_id}', 'ProductDetailsController@anushthan_order_book')->name('order.book');
        Route::get('/user/detail/{order_id}', 'ProductDetailsController@anushthanuser_detail')->name('user.detail');
        Route::post('/user/store/', 'ProductDetailsController@anushthanuser_store')->name('user.store');
    });

    // Chadhava Routes
    Route::controller(ProductDetailsController::class)->as('chadhava.')->prefix('chadhava')->group(function () {
        Route::get('details/{slug}', 'chadhava_details')->name('details');
        Route::post('add-chadhava-product', 'addChadhavaProduct')->name('add-chadhava-product');
        Route::post('delete-chadhava-product', 'DeleteProductChadhava')->name('delete-chadhava-product');
        Route::post('update-chadhava-product', 'UpdateProductChadhava')->name('update-chadhava-product');
        Route::post('lead/store', 'chadhava_lead_store')->name('lead.store');
        Route::get('/order/book/{encoded_id}', 'chadhavaOrderBook')->name('order.book');
        Route::get('/user/detail/{order_id}', 'chadhavaUserDetail')->name('user.detail');
        Route::post('/user/store', 'chadhavaUserStore')->name('user.store');
        Route::get('all-chadhava', 'all_chadhava')->name('all-chadhava');
        Route::get('getChadhava', 'get_Chadhavas')->name('getChadhava');
    });
    Route::controller(ProductDetailsController::class)->as('guruji.')->prefix('guruji')->group(function () {
        Route::get('/', 'guruji_all')->name('guruji_all');  
        Route::get('/guruji-data', 'guruji_data')->name('guruji-data');  
        Route::get('/{name}', 'guruji_personal_pooja')->name('guruji_personal_pooja');
        //Puja book Url
        Route::get('{guruji}/book-puja/{slug}', 'book_puja')->name('book-puja');
        Route::post('/gurujipujaLead/{slug}','panditpujaleadstore')->name('gurujipujaLead');
        // All Guruji puja indivisual
        Route::get('individual/{name}', 'individual_pooja')->name('individual');
        //Events book Url
        Route::get('{guruji}/book-event/{slug}', 'book_event')->name('book-event');
        //conselling Book Url
        Route::get('{guruji}/book-conselling/{slug}', 'book_conselling')->name('book-conselling');
        Route::post('gurujicounsellingLead/{slug}','panditcounsellingleadstore')->name('gurujicounsellingLead');
        Route::get('yajman/detail/{order_id}', 'yajman_detail')->name('yajman.detail');
        Route::post('yajman/store/', 'yajman_detail_store')->name('yajman.store');
    });

    // start same day delivery
    Route::get('/same-day-delivery', 'ProductListController@same_day_delivery')->name('same-day-delivery');
    Route::get('/same-day-delivery/index', 'ProductListController@same_day_delivery_index')->name('same-day-delivery.index');
    Route::get('/same-day-delivery/products', 'ProductListController@same_day_delivery_products')->name('same-day-delivery.products');
    Route::get('/same-day-delivery/sellers', 'ProductListController@same_day_delivery_sellers')->name('same-day-delivery.sellers');

    // end same day delivery


    Route::get('/product/{slug}', 'ProductDetailsController@index')->name('product');
    Route::get('products/{slug?}', 'ProductListController@products')->name('products-slug');
    Route::get('products', 'ProductListController@products')->name('products');
    // Route::get('products', 'ProductListController@productsById'); // fallback
    Route::post('ajax-filter-products', 'ShopViewController@ajax_filter_products')->name('ajax-filter-products'); // Theme fashion, ALl purpose
    Route::get('orderDetails', 'WebController@orderdetails')->name('orderdetails');
    Route::get('discounted-products', 'WebController@discounted_products')->name('discounted-products');
    Route::post('/products-view-style', 'WebController@product_view_style')->name('product_view_style');

    Route::post('review-list-product', 'WebController@review_list_product')->name('review-list-product');
    Route::post('review-list-shop', 'WebController@review_list_shop')->name('review-list-shop'); // theme fashion
    //Chat with seller from product details
    Route::get('chat-for-product', 'WebController@chat_for_product')->name('chat-for-product');

    Route::get('wishlists', 'WebController@viewWishlist')->name('wishlists')->middleware('customer');
    Route::post('store-wishlist', 'WebController@storeWishlist')->name('store-wishlist');
    Route::post('delete-wishlist', 'WebController@deleteWishlist')->name('delete-wishlist');
    Route::get('delete-wishlist-all', 'WebController@delete_wishlist_all')->name('delete-wishlist-all')->middleware('customer');

    Route::controller(CurrencyController::class)->group(function () {
        Route::post('/currency', 'changeCurrency')->name('currency.change');
    });

    // // end theme_aster compare list
    Route::get('searched-products-for-compare', 'WebController@searched_products_for_compare_list')->name('searched-products-compare'); // theme fashion compare list

    //profile Route
    Route::get('user-profile', 'UserProfileController@user_profile')->name('user-profile')->middleware('customer'); //theme_aster
    Route::get('user-account-delete', 'UserProfileController@user_account_delete'); //account delete info

    Route::get('user-account', 'UserProfileController@user_account')->name('user-account')->middleware('customer');
    Route::post('user-account-update', 'UserProfileController@user_update')->name('user-update');
    Route::post('user-account-picture', 'UserProfileController@user_picture')->name('user-picture');
    Route::get('account-address-add', 'UserProfileController@account_address_add')->name('account-address-add');
    Route::get('account-address', 'UserProfileController@account_address')->name('account-address');
    Route::post('account-address-store', 'UserProfileController@address_store')->name('address-store');
    Route::get('account-address-delete', 'UserProfileController@address_delete')->name('address-delete');
    ROute::get('account-address-edit/{id}', 'UserProfileController@address_edit')->name('address-edit');
    Route::post('account-address-update', 'UserProfileController@address_update')->name('address-update');
    Route::get('account-payment', 'UserProfileController@account_payment')->name('account-payment');
    Route::get('account-order', 'UserProfileController@account_order')->name('account-order')->middleware('customer');

    Route::get('account-order-product', 'UserProfileController@account_order_product')->name('account-order-product')->middleware('customer');
    Route::get('account-order-pooja', 'UserProfileController@account_order_pooja')->name('account-order-pooja')->middleware('customer');
    Route::get('account-order-vip', 'UserProfileController@account_order_vip')->name('account-order-vip')->middleware('customer');
    Route::get('account-order-anushthan', 'UserProfileController@account_order_anushthan')->name('account-order-anushthan')->middleware('customer');
    Route::get('account-order-chadhava', 'UserProfileController@account_order_chadhava')->name('account-order-chadhava')->middleware('customer');
    Route::get('account-order-offlinepooja', 'UserProfileController@account_order_offlinepooja')->name('account-order-offlinepooja')->middleware('customer');

    Route::get('account-order-counselling', 'UserProfileController@account_order_counselling')->name('account-order-counselling')->middleware('customer');
    Route::get('account-order-event', 'UserProfileController@account_order_event')->name('account-order-event')->middleware('customer');
    Route::get('account-order-tour', 'UserProfileController@account_order_tour')->name('account-order-tour')->middleware('customer');
    Route::get('account-order-donate', 'UserProfileController@account_order_donate')->name('account-order-donate')->middleware('customer');
    Route::get('donate-order-details/{id}', 'UserProfileController@donate_order_details')->name('donate-order-details')->middleware('customer');

    Route::get('account-vehicle-booking-order', 'UserProfileController@account_vehicle_booking_order')->name('account-vehicle-booking-order')->middleware('customer');

    Route::get('account-order-darshan', 'UserProfileController@account_order_Darshan')->name('account-order-darshan')->middleware('customer');
    Route::get('darshan-order-details/{id}', 'UserProfileController@DarshanOrderDetails')->name('darshan-order-details')->middleware('customer');
    Route::post('vip-darshan-add-review', 'UserProfileController@DarshanAddReviews')->name('vip-darshan-add-review');

    Route::get('account-order-details', 'UserProfileController@account_order_details')->name('account-order-details')->middleware('customer');
    Route::get('account-order-details-vendor-info', 'UserProfileController@account_order_details_seller_info')->name('account-order-details-vendor-info')->middleware('customer');
    Route::get('account-order-details-delivery-man-info', 'UserProfileController@account_order_details_delivery_man_info')->name('account-order-details-delivery-man-info')->middleware('customer');
    Route::get('account-order-details-reviews', 'UserProfileController@account_order_details_reviews')->name('account-order-details-reviews')->middleware('customer');
    Route::get('generate-invoice/{id}', 'UserProfileController@generate_invoice')->name('generate-invoice');
    Route::get('account-wishlist', 'UserProfileController@account_wishlist')->name('account-wishlist'); //add to card not work
    Route::get('refund-request/{id}', 'UserProfileController@refund_request')->name('refund-request');
    Route::get('refund-details/{id}', 'UserProfileController@refund_details')->name('refund-details');
    Route::post('refund-store', 'UserProfileController@store_refund')->name('refund-store');
    Route::get('account-tickets', 'UserProfileController@account_tickets')->name('account-tickets');
    Route::get('order-cancel/{id}', 'UserProfileController@order_cancel')->name('order-cancel');
    Route::post('ticket-submit', 'UserProfileController@submitSupportTicket')->name('ticket-submit');
    Route::get('account-delete/{id}', 'UserProfileController@account_delete')->name('account-delete');
    Route::get('refer-earn', 'UserProfileController@refer_earn')->name('refer-earn')->middleware('customer');
    Route::get('user-coupons', 'UserProfileController@user_coupons')->name('user-coupons')->middleware('customer');
    Route::get('ecommerce-coupons', 'UserProfileController@ecommerce_coupons')->name('ecommerce-coupons');
    Route::get('chadhava-coupons', 'UserProfileController@chadhava_coupons')->name('chadhava-coupons');
    Route::get('counselling-coupons', 'UserProfileController@counselling_coupons')->name('counselling-coupons');
    Route::get('pooja-coupons', 'UserProfileController@pooja_coupons')->name('pooja-coupons');
    Route::get('vippooja-coupons', 'UserProfileController@vippooja_coupons')->name('vippooja-coupons');
    Route::get('offlinepooja-coupons', 'UserProfileController@offlinepooja_coupons')->name('offlinepooja-coupons');
    Route::get('instancevippooja-coupons', 'UserProfileController@instancevippooja_coupons')->name('instancevippooja-coupons');
    Route::get('anushthanpooja-coupons', 'UserProfileController@anushthanpooja_coupons')->name('anushthanpooja-coupons');
    Route::get('instanceanushthanpooja-coupons', 'UserProfileController@instanceanushthanpooja_coupons')->name('instanceanushthanpooja-coupons');
    // saved kundali and kundali milan
    Route::get('saved-kundali', 'UserProfileController@saved_kundali')->name('saved.kundali')->middleware('customer');
    Route::get('saved-kundali-show/{id}', 'UserProfileController@saved_kundali_show')->name('saved.kundali.show')->middleware('customer');
    Route::get('saved-kundali-milan', 'UserProfileController@saved_kundali_milan')->name('saved.kundali.milan')->middleware('customer');
    Route::get('saved-kundali-show-milan/{id}', 'UserProfileController@saved_kundali_milan_show')->name('saved.kundali.milan.show')->middleware('customer');
    Route::group(['prefix' => 'kundali-pdf', 'as' => 'kundali-pdf.'], function () {
        Route::get('/', [KundaliController::class, 'index'])->name('view');
        Route::post('create-kundli-leads', [KundaliController::class, 'CreateKundliLeads'])->name('create-kundli-leads');
        Route::get('kundli-paypdf' . '/{id}', [KundaliController::class, 'Kundlipaypdf'])->name('kundli-paypdf')->middleware('customer');
        Route::post('pdfkundalipaid', [KundaliController::class, 'pdfkundalipaid'])->name('pdfkundalipaid');
        Route::get('kundali-payment-success' . '/{id}', [KundaliController::class, 'kundalipaySuccess'])->name('kundali-payment-success');
        Route::get('/{type}/{id}', [KundaliController::class, 'JanamPatrikaPdf'])->name('information');
    });
    Route::controller(TourVisitController::class)->as('tour.')->prefix('tour')->group(function () {
        Route::post("add-remaining-amount" . '/{id}', [PaymentController::class, "addTourRemainingpay"])->name('add_remaining_amount');
        Route::get("tour-remaining-payment-success" . '/{id}', [PaymentController::class, "TourRemainingpaysuccess"])->name('tour-remaining-payment-success');
        Route::get('change-tour-plane' . '/{lead}/{id}', 'ChangePlaneBooking')->name('change-tour-plane');
        Route::get('test-push-noti', 'test_push_noti');
        Route::get('/', 'TourIndex')->name('index');
        Route::get('tour-visit' . '/{id}', 'TourVisit')->name('tour-visit-id');
        Route::get('tour-visit', 'TourVisit')->name('tour-visit');
        Route::get('tour-list' . '/{id}', 'Tourlist')->name('tour-list');
        Route::post('visit-leads' . '/{id}', 'VisitLeads')->name('visit-leads');
        Route::post('tour-booking-tab', 'TourBookingTabs')->name('booking-tab-amount');
        Route::get('cabs' . '/{id}', 'TravellersCab')->name('traveller-cab');
        Route::post('create-lead', 'TourLeads')->name('create-lead');
        Route::get('tour-booking' . '/{id}', 'TourBooking')->name('tour-booking');
        
        Route::get('tour-pay-success' . '/{id}/{lead}', [PaymentController::class, 'TourSuccess'])->name('tour-pay-success');
        
        Route::get('tour-booking-success' . '/{id}', 'TourBookingSuccess')->name('tour-booking-success');
        Route::get('tour-booking-failed' . '/{id}', 'TourBookingFailed')->name('tour-booking-failed');
        
        Route::get('view-details' . '/{id}', 'TourViewDetails')->name('view-details');
        Route::post('review', 'TourReviews')->name('add-review');
        
        Route::get('tour-pdf-invoice' . '/{id}', 'TourInvoice')->name('tour-pdf-invoice');
        
        //cancel
        Route::post('create-ticket', 'TourCancelTicket')->name('create-ticket');
        Route::post('cancel-order-resonance', 'TourCancelResonance')->name('cancel-order-resonance');
        Route::post('related-order-view', 'RelatedOrderViews')->name('related-order-view');
        
        // tour vendor
        Route::get('all-vendor', 'all_vendor')->name('all-vendor');
        Route::get('vendor-tour/{id}', 'vendor_tour')->name('vendor-tour');
        Route::get('/{id}', 'TourVisitLeads')->name('tourvisit');
    });

    Route::post('tour-payment-request' . '/{id}', [PaymentController::class, 'TourBookingPay'])->name('tour-payment-request');

    Route::get('wallet-payment-success' . '/{id}', [PaymentController::class, 'AllWalletSuccess'])->name('all-pay-wallet-payment-success');
    Route::get('wallet-amount-success' . '/{id}/{lead}', [PaymentController::class, 'AllWalletSuccess'])->name('all-pay-wallet-payment-success-2');
    Route::get('wallet-payment-success' . '/{id}/{lead}', [PaymentController::class, 'AllWalletSuccess']);

    // paid kundli
    Route::get('saved-paid-kundali', 'UserProfileController@saved_paid_kundali')->name('saved.paid.kundali')->middleware('customer');
    Route::get('saved-paid-kundali-milan', 'UserProfileController@saved_paid_kundali_milan')->name('saved.paid.kundali.milan')->middleware('customer');
    Route::get('saved-paid-kundali-milan-show/{id}', 'UserProfileController@saved_paid_kundali_milan_show')->name('saved.paid-kundali-milan.show')->middleware('customer');
    Route::get('kundali-generate-invoice' . '/{id}', "UserProfileController@GenerateInvoice")->name('kundali-generate-invoice')->middleware('customer');

    // Pooja Service Account Details Routes
    Route::get('account-service-oder', 'UserProfileController@account_service_order')->name('account-service-oder')->middleware('customer');
    Route::get('generate-invoice-service/{id}', 'UserProfileController@generate_invoice_service')->name('generate-invoice-service');
    Route::get('account-service-certificate/{order_id}', 'UserProfileController@account_service_certificate')->name('account-service-certificate');
    Route::get('download-certificate/{id}', 'UserProfileController@downloadCertificate')->name('download-certificate');
    Route::get('account-service-order-details/{order_id}', 'UserProfileController@account_service_order_details')->name('account-service-order-details')->middleware('customer');
    Route::get('account-service-order-details-reviews/{order_id}', 'UserProfileController@account_service_order_details_reviews')->name('account-service-order-details-reviews')->middleware('customer');
    Route::get('account-service-pandit-details/{order_id}', 'UserProfileController@account_service_pandit_details')->name('account-service-pandit-details')->middleware('customer');
    Route::get('account-service-order-track/{order_id}', 'UserProfileController@account_service_order_track')->name('account-service-order-track')->middleware('customer');
    Route::get('account-service-sankalp/{order_id}', 'UserProfileController@account_service_sankalp')->name('account-service-sankalp')->middleware('customer');
    Route::post('sanklpUpdate/{order_id}', 'UserProfileController@sankalp_update')->name('sanklpUpdate');
    Route::get('account-service-order-user-name/{no}', 'UserProfileController@account_service_order_user_name')->name('account-service-order-user-name')->withoutMiddleware('customer');
    Route::get('account-service-review/{order_id}', 'UserProfileController@account_service_review')->name('account-service-review')->middleware('customer');
    Route::post('submit_service_review/{order_id}', 'UserProfileController@submit_service_review')->name('submit_service_review');

    // Counselling Account Details Routes
    Route::get('account-counselling-order-details/{order_id}', 'UserProfileController@account_counselling_order_details')->name('account-counselling-order-details')->middleware('customer');
    Route::get('account-counselling-order-track/{order_id}', 'UserProfileController@account_counselling_order_track')->name('account-counselling-order-track')->middleware('customer');
    Route::get('consultation-generate-invoice-service/{id}', 'UserProfileController@consultation_generate_invoice_service')->name('consultation-generate-invoice-service');
    Route::get('account-counselling-order-user-detail/{order_id}', 'UserProfileController@account_counselling_order_user_detail')->name('account-counselling-order-user-detail')->middleware('customer');
    Route::post('account-counselling-order-user-update', 'UserProfileController@account_counselling_order_user_update')->name('account-counselling-order-user-update')->middleware('customer');
    Route::get('account-counselling-order-user-name/{no}', 'UserProfileController@account_counselling_order_user_name')->name('account-counselling-order-user-name')->withoutMiddleware('customer');
    Route::get('account-counselling-review/{order_id}', 'UserProfileController@account_counselling_review')->name('account-counselling-review')->middleware('customer');
    Route::post('submit-counselling-review/{order_id}', 'UserProfileController@submit_counselling_review')->name('submit_counselling_review');
    // Offline Pooja Account Details Routes
    Route::get('account-offlinepooja-order-details/{order_id}', 'UserProfileController@account_offlinepooja_order_details')->name('account-offlinepooja-order-details')->middleware('customer');
    Route::get('account-offlinepooja-pandit-details/{order_id}', 'UserProfileController@account_offlinepooja_pandit_details')->name('account-offlinepooja-pandit-details')->middleware('customer');
    Route::get('account-offlinepooja-order-track/{order_id}', 'UserProfileController@account_offlinepooja_order_track')->name('account-offlinepooja-order-track')->middleware('customer');
    Route::get('account-offlinepooja-certificate/{order_id}', 'UserProfileController@account_offlinepooja_certificate')->name('account-offlinepooja-certificate');
    Route::get('generate-invoice-offlinepooja/{id}', 'UserProfileController@generate_invoice_offlinepooja')->name('generate-invoice-offlinepooja');
    Route::get('account-offlinepooja-order-user-name/{no}', 'UserProfileController@account_offlinepooja_order_user_name')->name('account-offlinepooja-order-user-name')->withoutMiddleware('customer');
    Route::get('offlinepoojadownload-certificate/{id}', 'UserProfileController@offlinepoojadownloadCertificate')->name('offlinepoojadownload-certificate');
    Route::get('account-offlinepooja-sankalp/{order_id}', 'UserProfileController@account_offlinepooja_sankalp')->name('account-offlinepooja-sankalp')->middleware('customer');
    Route::post('offlinepoojasanklpUpdate/{order_id}', 'UserProfileController@offlinepoojasankalp_update')->name('offlinepoojasanklpUpdate');
    Route::get('account-offlinepooja-review/{order_id}', 'UserProfileController@account_offlinepooja_review')->name('account-offlinepooja-review')->middleware('customer');
    Route::post('submit-offlinepooja-review/{order_id}', 'UserProfileController@submit_offlinepooja_review')->name('submit_offlinepooja_review');
    Route::get('offlinepooja-schedule/{order_id}', 'UserProfileController@offlinepooja_schedule')->name('offlinepooja-schedule')->middleware('customer');
    Route::get('offlinepooja-remaining-pay/{order_id}/{customer_id}', 'UserProfileController@offlinepooja_remainingpay')->name('offlinepooja-remaining-pay')->middleware('customer');
    Route::get('offlinepooja-cancle-order/{order_id}', 'UserProfileController@offlinepooja_cancle_order')->name('offlinepooja-cancle-order')->middleware('customer');
    Route::post('offlinepooja-cancle-order-submit', 'UserProfileController@offlinepooja_cancle_order_submit')->name('offlinepooja-cancle-order-submit')->middleware('customer');
    // VIP Account Details Routes
    Route::get('account-vip-order-details/{order_id}', 'UserProfileController@account_vip_order_details')->name('account-vip-order-details')->middleware('customer');
    Route::get('account-vip-pandit-details/{order_id}', 'UserProfileController@account_vip_pandit_details')->name('account-vip-pandit-details')->middleware('customer');
    Route::get('account-vip-order-track/{order_id}', 'UserProfileController@account_vip_order_track')->name('account-vip-order-track')->middleware('customer');
    Route::get('account-vip-certificate/{order_id}', 'UserProfileController@account_vip_certificate')->name('account-vip-certificate');
    Route::get('generate-invoice-vip/{id}', 'UserProfileController@generate_invoice_vip')->name('generate-invoice-vip');
    Route::get('account-vip-order-user-name/{no}', 'UserProfileController@account_vip_order_user_name')->name('account-vip-order-user-name')->withoutMiddleware('customer');
    Route::get('VIPdownload-certificate/{id}', 'UserProfileController@VIPdownloadCertificate')->name('VIPdownload-certificate');
    Route::get('account-vip-sankalp/{order_id}', 'UserProfileController@account_vip_sankalp')->name('account-vip-sankalp')->middleware('customer');
    Route::post('VIPsanklpUpdate/{order_id}', 'UserProfileController@VIPsankalp_update')->name('VIPsanklpUpdate');
    Route::get('account-vip-review/{order_id}', 'UserProfileController@account_vip_review')->name('account-vip-review')->middleware('customer');
    Route::post('submit-vip-review/{order_id}', 'UserProfileController@submit_vip_review')->name('submit_vip_review');
    // Anushthan Account Details Routes
    Route::get('account-anushthan-order-details/{order_id}', 'UserProfileController@account_anushthan_order_details')->name('account-anushthan-order-details')->middleware('customer');
    Route::get('account-anushthan-pandit-details/{order_id}', 'UserProfileController@account_anushthan_pandit_details')->name('account-anushthan-pandit-details')->middleware('customer');
    Route::get('account-anushthan-order-track/{order_id}', 'UserProfileController@account_anushthan_order_track')->name('account-anushthan-order-track')->middleware('customer');
    Route::get('account-anushthan-certificate/{order_id}', 'UserProfileController@account_anushthan_certificate')->name('account-anushthan-certificate');
    Route::get('generate-invoice-anushthan/{id}', 'UserProfileController@generate_invoice_anushthan')->name('generate-invoice-anushthan');
    Route::get('account-anushthan-order-user-name/{no}', 'UserProfileController@account_anushthan_order_user_name')->name('account-anushthan-order-user-name')->withoutMiddleware('customer');
    Route::get('Anushthandownload-certificate/{id}', 'UserProfileController@AnushthandownloadCertificate')->name('Anushthandownload-certificate');
    Route::get('account-anushthan-sankalp/{order_id}', 'UserProfileController@account_anushthan_sankalp')->name('account-anushthan-sankalp')->middleware('customer');
    Route::post('AnushthansanklpUpdate/{order_id}', 'UserProfileController@Anushthansankalp_update')->name('AnushthansanklpUpdate');
    Route::get('history-order', 'UserProfileController@history_order')->name('history-order')->middleware('customer');
    Route::get('feedback', 'UserProfileController@feedback_list')->name('feedback')->middleware('customer');
    Route::post('feedback-store', 'UserProfileController@feedback_store')->name('feedback-store')->middleware('customer');
    Route::post('feedback-update', 'UserProfileController@feedback_update')->name('feedback-update')->middleware('customer');
    Route::get('account-anushthan-review/{order_id}', 'UserProfileController@account_anushthan_review')->name('account-anushthan-review')->middleware('customer');
    Route::post('submit-anushthan-review/{order_id}', 'UserProfileController@submit_anushthan_review')->name('submit_anushthan_review');

    // Chadhva Account Details Routes
    Route::get('account-chadhava-order-details/{order_id}', 'UserProfileController@account_chadhava_order_details')->name('account-chadhava-order-details')->middleware('customer');
    Route::get('account-chadhava-pandit-details/{order_id}', 'UserProfileController@account_chadhava_pandit_details')->name('account-chadhava-pandit-details')->middleware('customer');
    Route::get('account-chadhava-order-track/{order_id}', 'UserProfileController@account_chadhava_order_track')->name('account-chadhava-order-track')->middleware('customer');
    Route::get('account-chadhava-certificate/{order_id}', 'UserProfileController@account_chadhava_certificate')->name('account-chadhava-certificate');
    Route::get('account-chadhava-sankalp/{order_id}', 'UserProfileController@account_chadhava_sankalp')->name('account-chadhava-sankalp')->middleware('customer');
    Route::post('chadhavasanklpUpdate/{order_id}', 'UserProfileController@chadhava_sankalp_update')->name('chadhavasanklpUpdate');
    Route::get('chadhava-certificate/{id}', 'UserProfileController@ChadhavadownloadCertificate')->name('chadhava-certificate');
    Route::get('generate-invoice-chadhava/{id}', 'UserProfileController@generate_invoice_chadhava')->name('generate-invoice-chadhava');
    Route::get('account-chadhava-review/{order_id}', 'UserProfileController@account_chadhava_review')->name('account-chadhava-review')->middleware('customer');
    Route::post('submit-chadhava-review/{order_id}', 'UserProfileController@submit_chadhava_review')->name('submit_chadhava_review');

    // Chatting start
    Route::get('chat/{type}', 'ChattingController@chat_list')->name('chat')->middleware('customer');
    Route::get('messages', 'ChattingController@messages')->name('messages');
    Route::post('messages-store', 'ChattingController@messages_store')->name('messages_store');
    // chatting end

    //Support Ticket
    Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.'], function () {
        Route::get('{id}', 'UserProfileController@single_ticket')->name('index');
        Route::post('{id}', 'UserProfileController@comment_submit')->name('comment');
        Route::get('delete/{id}', 'UserProfileController@support_ticket_delete')->name('delete');
        Route::get('close/{id}', 'UserProfileController@support_ticket_close')->name('close');
    });

    Route::get('wallet-account', 'UserWalletController@my_wallet_account')->name('wallet-account'); //theme fashion
    Route::get('wallet', 'UserWalletController@index')->name('wallet')->middleware('customer');
    Route::get('get-bank-detail/{phone}', 'UserWalletController@get_bank_detail')->middleware('customer');
    Route::post('store-bank-detail', 'UserWalletController@store_bank_detail')->name('store-bank-detail')->middleware('customer');

    Route::controller(UserLoyaltyController::class)->group(function () {
        Route::get(UserLoyalty::LOYALTY[URI], 'index')->name('loyalty')->middleware('customer');
        Route::post(UserLoyalty::EXCHANGE_CURRENCY[URI], 'getLoyaltyExchangeCurrency')->name('loyalty-exchange-currency');
        Route::get(UserLoyalty::GET_CURRENCY_AMOUNT[URI], 'getLoyaltyCurrencyAmount')->name('ajax-loyalty-currency-amount');
    });

    Route::group(['prefix' => 'track-order', 'as' => 'track-order.'], function () {
        Route::get('', 'UserProfileController@track_order')->name('index');
        Route::get('result-view', 'UserProfileController@track_order_result')->name('result-view');
        Route::get('last', 'UserProfileController@track_last_order')->name('last');
        Route::any('result', 'UserProfileController@track_order_result')->name('result');
        Route::get('order-wise-result-view', 'UserProfileController@track_order_wise_result')->name('order-wise-result-view');
    });

    //sellerShop
    Route::get('shopView/{id}', 'ShopViewController@seller_shop')->name('shopView');
    Route::get('ajax-shop-vacation-check', 'ShopViewController@ajax_shop_vacation_check')->name('ajax-shop-vacation-check'); //theme fashion
    Route::post('shopView/{id}', 'WebController@seller_shop_product');
    // Route::post('shop-follow', 'ShopFollowerController@shop_follow')->name('shop_follow');

    //top Rated
    Route::get('top-rated', 'WebController@top_rated')->name('topRated');
    Route::get('best-sell', 'WebController@best_sell')->name('bestSell');
    Route::get('new-product', 'WebController@new_product')->name('newProduct');

    Route::group(['prefix' => 'contact', 'as' => 'contact.'], function () {
        Route::post('store', 'WebController@contact_store')->name('store');
        Route::get('/code/captcha/{tmp}', 'WebController@captcha')->name('default-captcha');
    });


    // self driving by satish
    Route::get('self-vehicle', 'WebController@SelfVehicleList')->name('self-vehicle');
    Route::get('self-vehicle-choose/{slug}', 'WebController@SelfVehicleChoose')->name('self-vehicle-choose');
    Route::get('self-vehicle-details/{slug}', 'WebController@SelfVehicleDetails')->name('self-vehicle-details');
    Route::post('self-driving-lead/{id}', 'WebController@SelfVehicleLeads')->name('self-driving-lead');
    Route::get('self-vehicle-booking/{slug}/{lead}', 'WebController@SelfVehicleBooking')->name('self-vehicle-booking');
    Route::post('self-vehicle-lead-update/{lead}', 'WebController@SelfVehicleleadUpdate')->name('self-vehicle-lead-update');
    Route::get('self-vehicle-paymant-request/{lead}', [PaymentController::class, 'SelfVehiclePaymantRequest'])->name('self-vehicle-paymant-request');
    Route::get('self-vehicle-booking-pay-received/{lead}', [PaymentController::class, 'SelfVehiclePaymantReceived'])->name('self-vehicle-booking-pay-received');
    Route::get('self-vehicle-booking-success/{slug}', 'WebController@SelfVehicleBookingSuccess')->name('self-vehicle-booking-success');
    Route::get('self-vehicle-order-view/{id}', 'UserProfileController@SelfVehicleOrderView')->name('self-vehicle-order-view');
    Route::post('create-ticket-self-vehicle', 'UserProfileController@SelfVehicleRefundRequest')->name('create-ticket-self-vehicle');
    Route::post('self-vehicle-review-update/{id}', 'UserProfileController@SelfVehicleReviewUpdate')->name('self-vehicle-review-update');
});

//check done
Route::group(['prefix' => 'cart', 'as' => 'cart.', 'namespace' => 'Web'], function () {
    Route::post('variant_price', 'CartController@variant_price')->name('variant_price');
    Route::post('add', 'CartController@addToCart')->name('add');
    Route::post('update-variation', 'CartController@update_variation')->name('update-variation'); //theme fashion
    Route::post('remove', 'CartController@removeFromCart')->name('remove');
    Route::get('remove-all', 'CartController@remove_all_cart')->name('remove-all'); //theme fashion
    Route::post('nav-cart-items', 'CartController@updateNavCart')->name('nav-cart');
    Route::post('floating-nav-cart-items', 'CartController@update_floating_nav')->name('floating-nav-cart-items'); // theme fashion floating nav
    Route::post('updateQuantity', 'CartController@updateQuantity')->name('updateQuantity');
    Route::post('updateQuantity-guest', 'CartController@updateQuantity_guest')->name('updateQuantity.guest');
    Route::post('order-again', 'CartController@order_again')->name('order-again')->middleware('customer');
});

//Seller shop apply
Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'namespace' => 'Web'], function () {
    Route::post('apply', 'CouponController@apply')->name('apply');
    Route::get('remove', 'CouponController@removeCoupon')->name('remove');
    Route::post('offlinepoojacouponapply', 'CouponController@offlinepoojacouponapply')->name('offlinepoojacouponapply');
    Route::get('offlinepoojaremovecoupon/{code}', 'CouponController@offlinepoojaremoveCouponPooja')->name('offlinepoojaremovecoupon');
    Route::post('couponapply', 'CouponController@couponapply')->name('couponapply');
    Route::get('poojaremovecoupon/{code}', 'CouponController@removeCouponPooja')->name('poojaremovecoupon');
    Route::post('coupon-list-type', 'CouponController@couponListType')->name('coupon-list-type');
});
//check done

/*Auth::routes();*/
Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
    return response()->json([
        'errors' => $errors
    ], 401);
})->name('authentication-failed');

Route::group(['namespace' => 'Customer', 'prefix' => 'customer', 'as' => 'customer.'], function () {

    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', 'LoginController@captcha')->name('default-captcha');
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit');
        Route::post('loginotp', 'LoginController@submitotp')->name('loginotp');
        Route::get('logout', 'LoginController@logout')->name('logout');
        Route::get('get-login-modal-data', 'LoginController@get_login_modal_data')->name('get-login-modal-data');

        Route::get('sign-up', 'RegisterController@register')->name('sign-up');
        Route::post('sign-up', 'RegisterController@submit');

        Route::get('check/{id}', 'RegisterController@check')->name('check');

        // Customer Default Verify
        Route::post('verify', 'RegisterController@verify')->name('verify');

        // Customer Ajax Verify for theme except default
        Route::post('ajax-verify', 'RegisterController@ajax_verify')->name('ajax_verify');
        Route::post('resend-otp', 'RegisterController@resend_otp')->name('resend_otp');

        Route::get('update-phone/{id}', 'SocialAuthController@editPhone')->name('update-phone');
        Route::post('update-phone/{id}', 'SocialAuthController@updatePhone');

        Route::get('login/{service}', 'SocialAuthController@redirectToProvider')->name('service-login');
        Route::get('login/{service}/callback', 'SocialAuthController@handleProviderCallback')->name('service-callback');

        Route::get('recover-password', 'ForgotPasswordController@reset_password')->name('recover-password');
        Route::post('forgot-password', 'ForgotPasswordController@reset_password_request')->name('forgot-password');
        Route::get('otp-verification', 'ForgotPasswordController@otp_verification')->name('otp-verification');
        Route::post('otp-verification', 'ForgotPasswordController@otp_verification_submit');
        Route::get('reset-password', 'ForgotPasswordController@reset_password_index')->name('reset-password');
        Route::post('reset-password', 'ForgotPasswordController@reset_password_submit');
        Route::post('resend-otp-reset-password', 'ForgotPasswordController@ajax_resend_otp')->name('resend-otp-reset-password');
    });

    Route::group([], function () {
        Route::get('set-payment-method/{name}', 'SystemController@set_payment_method')->name('set-payment-method');
        Route::get('set-shipping-method', 'SystemController@set_shipping_method')->name('set-shipping-method');
        Route::post('choose-shipping-address', 'SystemController@choose_shipping_address')->name('choose-shipping-address');
        Route::post('choose-shipping-address-other', 'SystemController@choose_shipping_address_other')->name('choose-shipping-address-other');
        Route::post('choose-billing-address', 'SystemController@choose_billing_address')->name('choose-billing-address');

        Route::group(['prefix' => 'reward-points', 'as' => 'reward-points.', 'middleware' => ['auth:customer']], function () {
            Route::get('convert', 'RewardPointController@convert')->name('convert');
        });
    });

    Route::post('/web-payment-request', 'PaymentController@payment')->name('web-payment-request');
    Route::post('/customer-add-fund-request', 'PaymentController@customer_add_to_fund_request')->name('add-fund-request');
    // Services add Payment Getway
    Route::post('/services-payment-request', 'PaymentController@servicespayment')->name('services-payment-request');
    Route::post('/counselling-payment-request', 'PaymentController@counsellingpayment')->name('counselling-payment-request');
    Route::post('/counselling-pending-payment-request', 'PaymentController@counselling_pending_payment')->name('counselling-pending-payment-request');
    // Vip/Anushthan Pooja Payment Getway
    Route::post('/vip-payment-request', 'PaymentController@vippooja_payment')->name('vip-payment-request');
    Route::post('/chadhava-payment-request', 'PaymentController@chadhava_payment')->name('chadhava-payment-request');
    Route::post('/anushthan-payment-request', 'PaymentController@anushthan_payment')->name('anushthan-payment-request');
    Route::post('/offlinepooja-payment-request', 'PaymentController@offlinepooja_payment')->name('offlinepooja-payment-request');
    Route::post('/offlinepooja-pending-payment-request', 'PaymentController@offlinepooja_pending_payment')->name('offlinepooja-pending-payment-request');
    Route::post('/offlinepooja-remaining-payment-request', 'PaymentController@offlinepooja_remaining_payment')->name('offlinepooja-remaining-payment-request');
    Route::post('/offlinepooja-schedule-payment-request', 'PaymentController@offlinepooja_schedule_payment')->name('offlinepooja-schedule-payment-request');
});

$is_published = 0;
try {
    $full_data = include('Modules/Gateways/Addon/info.php');
    $is_published = $full_data['is_published'] == 1 ? 1 : 0;
} catch (\Exception $exception) {
}

if (!$is_published) {
    Route::group(['prefix' => 'payment'], function () {

        //SSLCOMMERZ
        Route::group(['prefix' => 'sslcommerz', 'as' => 'sslcommerz.'], function () {
            Route::get('pay', [SslCommerzPaymentController::class, 'index'])->name('pay');
            Route::post('success', [SslCommerzPaymentController::class, 'success'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::post('failed', [SslCommerzPaymentController::class, 'failed'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::post('canceled', [SslCommerzPaymentController::class, 'canceled'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //STRIPE
        Route::group(['prefix' => 'stripe', 'as' => 'stripe.'], function () {
            Route::get('pay', [StripePaymentController::class, 'index'])->name('pay');
            Route::get('token', [StripePaymentController::class, 'payment_process_3d'])->name('token');
            Route::get('success', [StripePaymentController::class, 'success'])->name('success');
        });

        //RAZOR-PAY
        Route::group(['prefix' => 'razor-pay', 'as' => 'razor-pay.'], function () {
            Route::get('pay', [RazorPayController::class, 'index']);
            Route::post('payment', [RazorPayController::class, 'payment'])->name('payment')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //PAYPAL
        Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function () {
            Route::get('pay', [PaypalPaymentController::class, 'payment']);
            Route::any('success', [PaypalPaymentController::class, 'success'])->name('success')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::any('cancel', [PaypalPaymentController::class, 'cancel'])->name('cancel')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //SENANG-PAY
        Route::group(['prefix' => 'senang-pay', 'as' => 'senang-pay.'], function () {
            Route::get('pay', [SenangPayController::class, 'index']);
            Route::any('callback', [SenangPayController::class, 'return_senang_pay']);
        });

        //PAYTM
        Route::group(['prefix' => 'paytm', 'as' => 'paytm.'], function () {
            Route::get('pay', [PaytmController::class, 'payment']);
            Route::any('response', [PaytmController::class, 'callback'])->name('response')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //FLUTTERWAVE
        Route::group(['prefix' => 'flutterwave-v3', 'as' => 'flutterwave-v3.'], function () {
            Route::get('pay', [FlutterwaveV3Controller::class, 'initialize'])->name('pay');
            Route::get('callback', [FlutterwaveV3Controller::class, 'callback'])->name('callback');
        });

        //PAYSTACK
        Route::group(['prefix' => 'paystack', 'as' => 'paystack.'], function () {
            Route::get('pay', [PaystackController::class, 'index'])->name('pay');
            Route::post('payment', [PaystackController::class, 'redirectToGateway'])->name('payment');
            Route::get('callback', [PaystackController::class, 'handleGatewayCallback'])->name('callback');
        });

        //BKASH

        Route::group(['prefix' => 'bkash', 'as' => 'bkash.'], function () {
            // Payment Routes for bKash
            Route::get('make-payment', [BkashPaymentController::class, 'make_tokenize_payment'])->name('make-payment');
            Route::any('callback', [BkashPaymentController::class, 'callback'])->name('callback');
        });

        //Liqpay
        Route::group(['prefix' => 'liqpay', 'as' => 'liqpay.'], function () {
            Route::get('payment', [LiqPayController::class, 'payment'])->name('payment');
            Route::any('callback', [LiqPayController::class, 'callback'])->name('callback');
        });

        //MERCADOPAGO
        Route::group(['prefix' => 'mercadopago', 'as' => 'mercadopago.'], function () {
            Route::get('pay', [MercadoPagoController::class, 'index'])->name('index');
            Route::post('make-payment', [MercadoPagoController::class, 'make_payment'])->name('make_payment');
        });

        //PAYMOB
        Route::group(['prefix' => 'paymob', 'as' => 'paymob.'], function () {
            Route::any('pay', [PaymobController::class, 'credit'])->name('pay');
            Route::any('callback', [PaymobController::class, 'callback'])->name('callback');
        });

        //PAYTABS
        Route::group(['prefix' => 'paytabs', 'as' => 'paytabs.'], function () {
            Route::any('pay', [PaytabsController::class, 'payment'])->name('pay');
            Route::any('callback', [PaytabsController::class, 'callback'])->name('callback');
            Route::any('response', [PaytabsController::class, 'response'])->name('response');
        });

        //Pay Fast
        Route::group(['prefix' => 'payfast', 'as' => 'payfast.'], function () {
            Route::get('pay', [PayFastController::class, 'payment'])->name('payment');
            Route::any('callback', [PayFastController::class, 'callback'])->name('callback');
        });
    });
}

Route::get('web-payment', 'Customer\PaymentController@web_payment_success')->name('web-payment-success');
Route::get('service-web-payment', 'Customer\PaymentController@service_web_payment_success')->name('service-web-payment-success');

Route::get('offlinepooja-web-payment', 'Customer\PaymentController@offlinepooja_web_payment_success')->name('offline-web-payment-success');
Route::get('offlinepooja-pending-web-payment', 'Customer\PaymentController@offlinepooja_pending_web_payment_success')->name('offlinepooja-pending-web-payment-success');
Route::get('offlinepooja-remaining-web-payment', 'Customer\PaymentController@offlinepooja_remaining_web_payment_success')->name('offline-remaining-web-payment-success');
Route::get('offlinepooja-schedule-web-payment', 'Customer\PaymentController@offlinepooja_schedule_web_payment_success')->name('offline-schedule-web-payment-success');

Route::get('anushthan-web-payment', 'Customer\PaymentController@anushthan_web_payment_success')->name('anushthan-web-payment-success');
Route::get('vip-web-payment', 'Customer\PaymentController@vippooja_web_payment_success')->name('vip-web-payment-success');
Route::get('chadhava-web-payment', 'Customer\PaymentController@chadhava_web_payment_success')->name('chadhava-web-payment-success');

Route::get('payment-success', 'Customer\PaymentController@success')->name('payment-success');
Route::get('payment-fail', 'Customer\PaymentController@fail')->name('payment-fail');
Route::get('payment/success-transaction', 'Customer\PaymentController@success_event')->name('payment.success-transaction');
Route::get('payment/event-order-transaction-success', 'Customer\PaymentController@eventordersuccess')->name('payment.event-order-transaction-success');
Route::get('payment/success-transaction-adstust', 'Customer\PaymentController@success_adsApprove')->name('payment.success-transaction-adstust');

Route::get('counselling-web-payment', 'Customer\PaymentController@counselling_web_payment_success')->name('counselling-web-payment-success');
Route::get('counselling-pending-web-payment', 'Customer\PaymentController@counselling_pending_web_payment_success')->name('counselling-pending-web-payment-success');

Route::get("payment/birth_journal_success", 'Customer\PaymentController@BirthJournalSuccess')->name('payment.birth_journal_success');
Route::get("payment/birth_journal_kundli_success", 'Customer\PaymentController@BirthJournalKundliSuccess')->name('payment.birth_journal_kundli_success');
Route::get('booking-vendor-message/{id}', 'Web\WebController@tourOrdervendorBookingMessage')->name('booking-vendor-message');

Route::get("mahakal-qr-code", "Web\UserProfileController@MahakalQrCodes");
Route::get("mahakal-qr-scan", "Web\UserProfileController@MahakalQrScan")->name('mahakal-qr-scan');

Route::get('donate-create-pdf-invoice' . '/{id}', 'Web\WebController@DonateInvoice')->name('donate-create-pdf-invoice');
Route::get('service/order/book/report/{type}', 'Customer\PaymentController@service_order_book_report')->name('service.order.book.report');
Route::get('tour-booking/{slug}', 'Web\TourVisitController@TourBookingPage');
Route::get('/test', function () {
    return view('welcome');
});

// By Himanshu
// Route::get("getMyQR/{url}", "Web\UserProfileController@generateQrCode");

Route::post('razorpay/webhook', [\App\Http\Controllers\WebhookController::class, 'handle']);

