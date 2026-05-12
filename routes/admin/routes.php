<?php

use App\Enums\ViewPaths\Admin\AddonSetup;
use App\Enums\ViewPaths\Admin\AllPagesBanner;
use App\Enums\ViewPaths\Admin\Attribute;
use App\Enums\ViewPaths\Admin\Video;
use App\Enums\ViewPaths\Admin\VideoCategory;
use App\Enums\ViewPaths\Admin\VideoSubCategory;
use App\Enums\ViewPaths\Admin\SangeetCategory;
use App\Enums\ViewPaths\Admin\SangeetSubCategory;
use App\Enums\ViewPaths\Admin\SangeetLanguage;
use App\Enums\ViewPaths\Admin\Sangeet;
use App\Enums\ViewPaths\Admin\ChadhavaPath;
use App\Enums\ViewPaths\Admin\PanchangMoonImage;
use App\Enums\ViewPaths\Admin\AppSection;
use App\Enums\ViewPaths\Admin\Brand;
use App\Enums\ViewPaths\Admin\Rashi;
use App\Enums\ViewPaths\Admin\MasikRashi;
use App\Enums\ViewPaths\Admin\VarshikRashi;
use App\Enums\ViewPaths\Admin\Katha;
use App\Enums\ViewPaths\Admin\SaptahikVratKatha;
use App\Enums\ViewPaths\Admin\PradoshKatha;
use App\Enums\ViewPaths\Admin\CalendarDay;
use App\Enums\ViewPaths\Admin\CalendarNakshatra;
use App\Enums\ViewPaths\Admin\CalendarVikramSamvat;
use App\Enums\ViewPaths\Admin\CalendarHindiMonth;
use App\Enums\ViewPaths\Admin\FestivalHindiMonth;
use App\Enums\ViewPaths\Admin\FestivalAdd;
use App\Enums\ViewPaths\Admin\Festival;
use App\Enums\ViewPaths\Admin\FastFestival;
use App\Enums\ViewPaths\Admin\Calculator;
use App\Enums\ViewPaths\Admin\Astrologer;
use App\Enums\ViewPaths\Admin\Banner;
use App\Enums\ViewPaths\Admin\BusinessSettings;
use App\Enums\ViewPaths\Admin\Cart;
use App\Enums\ViewPaths\Admin\Chatting;
use App\Enums\ViewPaths\Admin\Contact;
use App\Enums\ViewPaths\Admin\Coupon;
use App\Enums\ViewPaths\Admin\Currency;
use App\Enums\ViewPaths\Admin\Dashboard;
use App\Enums\ViewPaths\Admin\DatabaseSetting;
use App\Enums\ViewPaths\Admin\DeliveryMan;
use App\Enums\ViewPaths\Admin\DeliveryManCash;
use App\Enums\ViewPaths\Admin\DeliverymanWithdraw;
use App\Enums\ViewPaths\Admin\DeliveryRestriction;
use App\Enums\ViewPaths\Admin\EmergencyContact;
use App\Enums\ViewPaths\Admin\EnvironmentSettings;
use App\Enums\ViewPaths\Admin\FeaturesSection;
use App\Enums\ViewPaths\Admin\FileManager;
use App\Enums\ViewPaths\Admin\GoogleMapAPI;
use App\Enums\ViewPaths\Admin\HelpTopic;
use App\Enums\ViewPaths\Admin\InhouseProductSale;
use App\Enums\ViewPaths\Admin\InhouseShop;
use App\Enums\ViewPaths\Admin\Language;
use App\Enums\ViewPaths\Admin\Mail;
use App\Enums\ViewPaths\Admin\MostDemanded;
use App\Enums\ViewPaths\Admin\Notification;
use App\Enums\ViewPaths\Admin\OfflinePaymentMethod;
use App\Enums\ViewPaths\Admin\Pages;
use App\Enums\ViewPaths\Admin\PaymentMethod;
use App\Enums\ViewPaths\Admin\POS;
use App\Enums\ViewPaths\Admin\POSOrder;
use App\Enums\ViewPaths\Admin\PushNotification;
use App\Enums\ViewPaths\Admin\Recaptcha;
use App\Enums\ViewPaths\Admin\RefundRequest;
use App\Enums\ViewPaths\Admin\RefundTransaction;
use App\Enums\ViewPaths\Admin\Review;
use App\Enums\ViewPaths\Admin\ShippingMethod;
use App\Enums\ViewPaths\Admin\ShippingType;
use App\Enums\ViewPaths\Admin\SiteMap;
use App\Enums\ViewPaths\Admin\SMSModule;
use App\Enums\ViewPaths\Admin\SocialLoginSettings;
use App\Enums\ViewPaths\Admin\SocialMedia;
use App\Enums\ViewPaths\Admin\SocialMediaChat;
use App\Enums\ViewPaths\Admin\SoftwareUpdate;
use App\Enums\ViewPaths\Admin\ThemeSetup;
use App\Enums\ViewPaths\Admin\Vendor;
use App\Enums\ViewPaths\Admin\Profile;
use App\Enums\ViewPaths\Admin\cities;
use App\Enums\ViewPaths\Admin\EventOrganizerPath;
use App\Enums\ViewPaths\Admin\EventpackagePath;
use App\Enums\ViewPaths\Admin\EventsPath;
use App\Enums\ViewPaths\Admin\EventcategoryPath;
use App\Enums\ViewPaths\Admin\BirthJournalPath;
use App\Enums\ViewPaths\Admin\Sahitya;
use App\Enums\ViewPaths\Admin\BhagavadGita;
use App\Enums\ViewPaths\Admin\ValmikiRamayan;
use App\Enums\ViewPaths\Admin\TulsidasRamayan;
use App\Http\Controllers\Admin\Sahitya\SahityaController;
use App\Http\Controllers\Admin\Sahitya\BhagavadGitaController;
use App\Http\Controllers\Admin\Sahitya\ValmikiRamayanController;
use App\Http\Controllers\Admin\Sahitya\TulsidasRamayanController;
use App\Http\Controllers\Admin\Bhagwan\BhagwanController;
use App\Http\Controllers\Admin\Jaap\jaapcontroller;
use App\Enums\ViewPaths\Admin\DonateAdsTrustPath;
use App\Enums\ViewPaths\Admin\DonateCategoryPath;
use App\Enums\ViewPaths\Admin\DonateTrustPath;
use App\Enums\ViewPaths\Admin\Bhagwan;
use App\Enums\ViewPaths\Admin\Jaap;

use App\Enums\ViewPaths\Admin\TourAndTravelPath;
use App\Enums\ViewPaths\Admin\TourCabPath;
use App\Enums\ViewPaths\Admin\TourPackagePath;
use App\Enums\ViewPaths\Admin\TourVisitPath;
use App\Enums\ViewPaths\Admin\TourBookingPath;
use App\Enums\ViewPaths\Admin\TourRePolicyPath;

use App\Http\Controllers\Admin\TourAndTravelController;
use App\Http\Controllers\Admin\TourCabController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\TourVisitController;
use App\Http\Controllers\Admin\TourBookingController;
use App\Http\Controllers\Admin\TourRefundPolicyController;

use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\EventOrganizerController;
use App\Http\Controllers\Admin\EventPackageController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\BirthJournalController;

use App\Http\Controllers\Admin\DonateCategoryController;
use App\Http\Controllers\Admin\DonatePurposeController;
use App\Http\Controllers\Admin\DonateTrustController;
use App\Http\Controllers\Admin\DonateAdsTrustController;

use App\Http\Controllers\Admin\CategoryShippingCostController;
use App\Http\Controllers\Admin\ChattingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Deliveryman\DeliveryManCashCollectController;
use App\Http\Controllers\Admin\Deliveryman\DeliveryManController;
use App\Http\Controllers\Admin\Deliveryman\DeliverymanWithdrawController;
use App\Http\Controllers\Admin\Deliveryman\EmergencyContactController;
use App\Http\Controllers\Admin\EmailTemplatesController;
use App\Http\Controllers\Admin\HelpAndSupport\HelpTopicController;
use App\Http\Controllers\Admin\InhouseProductSaleController;
use App\Http\Controllers\Admin\Order\RefundController;
use App\Http\Controllers\Admin\ThirdParty\PaymentMethodController;
use App\Http\Controllers\Admin\Notification\NotificationController;
use App\Http\Controllers\Admin\Payment\OfflinePaymentMethodController;
use App\Http\Controllers\Admin\POS\CartController;
use App\Http\Controllers\Admin\POS\POSController;
use App\Http\Controllers\Admin\POS\POSOrderController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\Promotion\AllPagesBannerController;
use App\Http\Controllers\Admin\Promotion\MostDemandedController;
use App\Http\Controllers\Admin\Report\RefundTransactionController;
use App\Http\Controllers\Admin\Settings\AddonController;
use App\Http\Controllers\Admin\Settings\BusinessSettingsController;
use App\Http\Controllers\Admin\Settings\CurrencyController;
use App\Http\Controllers\Admin\Settings\DatabaseSettingController;
use App\Http\Controllers\Admin\Settings\DeliverymanSettingsController;
use App\Http\Controllers\Admin\Settings\DeliveryRestrictionController;
use App\Http\Controllers\Admin\Settings\EnvironmentSettingsController;
use App\Http\Controllers\Admin\Settings\FeaturesSectionController;
use App\Http\Controllers\Admin\Settings\FileManagerController;
use App\Http\Controllers\Admin\Settings\InhouseShopController;
use App\Http\Controllers\Admin\Settings\LanguageController;
use App\Http\Controllers\Admin\Settings\OrderSettingsController;
use App\Http\Controllers\Admin\Settings\PagesController;
use App\Http\Controllers\Admin\Notification\PushNotificationSettingsController;
use App\Http\Controllers\Admin\Settings\SellerSettingsController;
use App\Http\Controllers\Admin\Settings\SiteMapController;
use App\Http\Controllers\Admin\Settings\SocialMediaSettingsController;
use App\Http\Controllers\Admin\Settings\SoftwareUpdateController;
use App\Http\Controllers\Admin\Settings\ThemeController;
use App\Http\Controllers\Admin\ThirdParty\SMSModuleController;
use App\Http\Controllers\Admin\Shipping\ShippingMethodController;
use App\Http\Controllers\Admin\Shipping\ShippingTypeController;
use App\Http\Controllers\Admin\ThirdParty\GoogleMapAPIController;
use App\Http\Controllers\Admin\ThirdParty\MailController;
use App\Http\Controllers\Admin\ThirdParty\RecaptchaController;
use App\Http\Controllers\Admin\ThirdParty\SocialLoginSettingsController;
use App\Http\Controllers\Admin\ThirdParty\SocialMediaChatController;
use App\Http\Controllers\SharedController;
use Illuminate\Support\Facades\Route;
use App\Enums\ViewPaths\Admin\Product;
use App\Enums\ViewPaths\Admin\Category;
use App\Enums\ViewPaths\Admin\Customer;
use App\Enums\ViewPaths\Admin\Employee;
use App\Enums\ViewPaths\Admin\FlashDeal;
use App\Enums\ViewPaths\Admin\CustomRole;
use App\Enums\ViewPaths\Admin\Order;
use App\Enums\ViewPaths\Admin\FeatureDeal;
use App\Enums\ViewPaths\Admin\SubCategory;
use App\Enums\ViewPaths\Admin\DealOfTheDay;
use App\Enums\ViewPaths\Admin\SupportTicket;
use App\Enums\ViewPaths\Admin\CustomerWallet;
use App\Enums\ViewPaths\Admin\SubSubCategory;
use App\Enums\ViewPaths\Admin\WithdrawalMethod;
use App\Http\Controllers\Admin\Promotion\BannerController;
use App\Http\Controllers\Admin\Promotion\CouponController;
use App\Http\Controllers\Admin\Customer\CustomerController;
use App\Http\Controllers\Admin\Customer\CustomerLoyaltyController;
use App\Http\Controllers\Admin\Customer\CustomerWalletController;
use App\Http\Controllers\Admin\Employee\CustomRoleController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Order\OrderController;
use App\Http\Controllers\Admin\Product\AttributeController;
use App\Http\Controllers\Admin\Video\VideoController;
use App\Http\Controllers\Admin\Video\VideoCategoryController;
use App\Http\Controllers\Admin\Video\VideoSubCategoryController;
use App\Http\Controllers\Admin\Sangeet\SangeetCategoryController;
use App\Http\Controllers\Admin\Sangeet\SangeetSubCategoryController;
use App\Http\Controllers\Admin\Sangeet\SangeetLanguageController;
use App\Http\Controllers\Admin\Sangeet\SangeetController;
use App\Http\Controllers\Admin\PanchangMoonImage\PanchangMoonImageController;
use App\Http\Controllers\Admin\AppSection\AppSectionController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\HelpAndSupport\SupportTicketController;
use App\Http\Controllers\Admin\HelpAndSupport\ContactController;
//start astrology-sec
use App\Http\Controllers\Admin\Astrology\RashiController;
use App\Http\Controllers\Admin\Astrology\MasikRashiController;
use App\Http\Controllers\Admin\Astrology\VarshikRashiController;
use App\Http\Controllers\Admin\Astrology\KathaController;
use App\Http\Controllers\Admin\Astrology\SaptahikKathaController;
use App\Http\Controllers\Admin\Astrology\PradoshKathaController;
use App\Http\Controllers\Admin\Astrology\CalendarDayController;
use App\Http\Controllers\Admin\Astrology\CalendarNakshatraController;
use App\Http\Controllers\Admin\Astrology\CalendarVikramSamvatController;
use App\Http\Controllers\Admin\Astrology\CalendarHindiMonthController;
use App\Http\Controllers\Admin\Astrology\FestivalHindiMonthController;
use App\Http\Controllers\Admin\Astrology\FestivalAddController;
use App\Http\Controllers\Admin\Astrology\FestivalController;
use App\Http\Controllers\Admin\Astrology\FastFestivalController;
// end astrology-sec
use App\Http\Controllers\Admin\Product\BrandController;
use App\Http\Controllers\Admin\Vendor\VendorController;
use App\Http\Controllers\Admin\Product\ReviewController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Product\CategoryController;
use App\Http\Controllers\Admin\Product\SubCategoryController;
use App\Http\Controllers\Admin\Promotion\FlashDealController;
use App\Http\Controllers\Admin\Product\SubSubCategoryController;
use App\Http\Controllers\Admin\Promotion\DealOfTheDayController;
use App\Http\Controllers\Admin\Promotion\FeaturedDealController;
use App\Http\Controllers\Admin\Vendor\WithdrawalMethodController;
use App\Http\Controllers\Admin\TransactionReportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProductStockReportController;
use App\Http\Controllers\Admin\SellerProductSaleReportController;
use App\Http\Controllers\Admin\ProductReportController;
use App\Http\Controllers\Admin\OrderReportController;
use App\Http\Controllers\Admin\ProductWishlistReportController;
use App\Http\Controllers\Admin\Settings\ReactSettingsController;
use App\Enums\ViewPaths\Admin\ReactSetup;
// Services controller
use App\Enums\ViewPaths\Admin\ServiceDetails;
use App\Http\Controllers\Admin\Service\ServiceController;
//FAQ's controller
use App\Enums\ViewPaths\Admin\FAQPath;
use App\Http\Controllers\Admin\Service\FAQController;
//Package's controller
use App\Enums\ViewPaths\Admin\Package;
use App\Http\Controllers\Admin\Counselling\CounsellingOrderController;
use App\Http\Controllers\Admin\Packages\PackageController;
use App\Http\Controllers\Admin\Pooja\PoojaOrderController;
use App\Http\Controllers\Admin\Pooja\GurujiPujaOrderController;
// cities Management Controller and Path
// use App\Enums\ViewPaths\Admin\cities;
use App\Enums\ViewPaths\Admin\CitiesVisits as AdminCitiesVisits;
use App\Enums\ViewPaths\Admin\TempleCategoryEnum;
use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\CitiesVisits;
use App\Http\Controllers\Admin\TempleCategoryController;
use App\Http\Controllers\Admin\Temple\VisitController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\RestaurantController;
// Temple Management Controller and Path
use App\Enums\ViewPaths\Admin\TemplePath;
use App\Http\Controllers\Admin\TempleController;
// Gallery 's Conroller
use App\Enums\ViewPaths\Admin\GalleryPath;
use App\Enums\ViewPaths\Admin\HotelsEnums;
use App\Enums\ViewPaths\Admin\RamShalaka;
use App\Enums\ViewPaths\Admin\RestaurantsPath;
use App\Enums\ViewPaths\Admin\SelfDrivingPath;
use App\Enums\ViewPaths\Admin\TourTypePath;
use App\Enums\ViewPaths\Admin\VendorPermissionPath;
use App\Enums\ViewPaths\Admin\VendorSuppTicket;
use App\Enums\ViewPaths\Admin\VideoListType;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\Astrology\AstrologerController;
use App\Http\Controllers\Admin\Pooja\PrashadOrderController;
use App\Http\Controllers\Admin\Astrology\CalculatorController;
use App\Http\Controllers\Admin\Pandit\PanditController;
use App\Http\Controllers\Admin\Pooja\AnushthanPoojaOrderController;
use App\Http\Controllers\Admin\Pooja\ChadhavaOrderController;
use App\Http\Controllers\Admin\Chadhava\ChadhavaController;
use App\Http\Controllers\Admin\CollectorController;
use App\Http\Controllers\Admin\Counselling\GurujiCounsellingOrderController;
use App\Http\Controllers\Admin\Email\EmailController;
use App\Http\Controllers\Admin\GeneralReviewController;
use App\Http\Controllers\Admin\HelpAndSupport\VendorTicketController;
use App\Http\Controllers\Admin\Pooja\VipPoojaOrderController;
use App\Http\Controllers\Admin\Video\VideoListTypeController;
use App\Http\Controllers\Admin\JsonController;
use App\Http\Controllers\Admin\Service\PoojaForecastController;
use App\Http\Controllers\Admin\Service\PujaScheduleController;
use App\Http\Controllers\Admin\Leads\LeadsController;
use App\Http\Controllers\Admin\Service\PujaRecordsController;
use App\Http\Controllers\Admin\Muhurat\MuhuratController;
use App\Http\Controllers\Admin\Pooja\OfflinePoojaOrderController;
use App\Http\Controllers\Admin\Sahitya\RamShalakaController;
use App\Http\Controllers\Admin\SelfDrivingController;
use App\Http\Controllers\Admin\ServiceBookingController;
use App\Http\Controllers\Admin\TourTypeController;
use App\Http\Controllers\Admin\VendorPermissionModule;
use App\Http\Controllers\Admin\Visitor\VisitorController;
use App\Http\Controllers\ServiceTaxController;
use App\Http\Controllers\Admin\Whatsapp\WhatsappController;


// Route to show the form to add a new JSON entry
Route::get('/admin/add-json-muhurat', [JsonController::class, 'create'])->name('admin.add-json');
Route::get('admin/book/pooja-payment-success', [ServiceBookingController::class, 'pooja_payment_success']);
Route::get('/pooja-forecast/run', [PoojaForecastController::class, 'runForecast']);
// Route to handle the form submission for adding a new JSON entry
Route::post('/admin/add-json', [JsonController::class, 'store'])->name('admin.store-json');

// pooja pending payment 
Route::post('admin/pooja/pending/payment/request', 'Customer\PaymentController@admin_pooja_pending_payment_request')->name('admin.pooja.pending.payment.request');
Route::get('admin-pooja-pending-web-payment', 'Customer\PaymentController@admin_pooja_pending_web_payment_success')->name('admin-pooja-pending-web-payment');
Route::get('pooja/order/fail', 'Customer\PaymentController@admin_pooja_order_fail');

// chadhava pending payment 
Route::post('admin/chadhava/pending/payment/request', 'Customer\PaymentController@admin_chadhava_pending_payment_request')->name('admin.chadhava.pending.payment.request');
Route::get('admin-chadhava-pending-web-payment', 'Customer\PaymentController@admin_chadhava_pending_web_payment_success')->name('admin-chadhava-pending-web-payment');
Route::get('chadhava/order/fail', 'Customer\PaymentController@admin_chadhava_order_fail');

// Route to list all JSON entries
Route::get('/admin/json-list', [JsonController::class, 'index'])->name('admin.json-list');

// Route to show the edit form for a specific JSON entry
Route::get('/admin/edit-json/{type}/{index}', [JsonController::class, 'edit'])->name('admin.edit-json');

// Route to update a specific JSON entry
Route::post('/admin/edit-json/{index}', [JsonController::class, 'update'])->name('admin.update-json');

Route::post('change-language', [SharedController::class, 'changeLanguage'])->name('change-language');
Route::get('shiprocket/delivery/track/{id}', [OrderController::class, 'shiprocket_delivery_track']);

Route::group(['prefix' => 'login'], function () {
    Route::get('{loginUrl}', [LoginController::class, 'index']);
    Route::get('recaptcha/{tmp}', [LoginController::class, 'generateReCaptcha'])->name('recaptcha');
    Route::post('/', [LoginController::class, 'login'])->name('login');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['admin']], function () {

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get(Dashboard::VIEW[URI], 'index')->name('index');
            Route::post(Dashboard::ORDER_STATUS[URI], 'getOrderStatus')->name('order-status');
            Route::get(Dashboard::EARNING_STATISTICS[URI], 'getEarningStatistics')->name('earning-statistics');
            Route::get(Dashboard::ORDER_STATISTICS[URI], 'getOrderStatistics')->name('order-statistics');
            Route::post('grcode', 'generateQrCode')->name('grcode');
        });
    });

    //leads
    Route::group(['prefix' => 'leads', 'as' => 'leads.', 'middleware' => ['module:leads']], function () {
        Route::controller(LeadsController::class)->group(function () {
            // API/Data route
            Route::get('leads', 'getLeadsData')->name('lead-list');
            Route::get('Showleads', 'leadsList')->name('Showleads');
            Route::get('addNewLeads', 'add_NewLead')->name('addNewLeads');
            Route::get('get-services', 'getServicesByType')->name('getServicesByType');
            Route::get('get-packages', 'getPackagesByService')->name('getPackagesByService');
            Route::get('check-customer-exits/{no}', 'UesetoCheck')->name('check-customer-exits');
            Route::post('add-new-leads', 'AddNewGenerate_leads')->name('add-new-leads');
            // follow the leads
            Route::get('lead-delete/{leadId}', 'lead_delete')->name('lead-delete');
            Route::post('lead-follow-up', 'followup_store')->name('lead-follow-up');
            Route::get('get-follows-list/{leadId}', 'getFollowList')->name('get-follows-list');
            Route::get('send-whatsapp-leads/{leadId}', 'send_whatsapp_leads')->name('send-whatsapp-leads');

            //offlinepuja
            Route::get('offlinepooja/leads', 'offlineLeads')->name('offline-pooja-leads');
            Route::get('offlinepooja/Showleads', 'offlineLeadsList')->name('offline-Showleads');
            Route::get('offlinepooja/addNewLeads', 'offline_add_NewLead')->name('offline-addNewLeads');
            Route::get('offlinepooja/get-packages', 'offline_getPackagesByService')->name('offline-getPackagesByService');
            Route::get('offlinepooja/check-customer-exits/{no}', 'offline_UesetoCheck')->name('offline-check-customer-exits');
            Route::post('offlinepooja-add-new-leads', 'offline_AddNewGenerate_leads')->name('offline-add-new-leads');
            Route::get('offlinepooja/lead-delete/{leadId}', 'offline_lead_delete')->name('offline-lead-delete');

            Route::post('offlinepooja/lead-follow-up', 'offline_followup_store')->name('offline-lead-follow-up');
            Route::get('offlinepooja/get-follows-list/{id}', 'offline_getFollowList')->name('offline-get-follows-list');
            Route::get('offlinepooja/send-whatsapp-leads/{leadId}', 'offline_send_whatsapp_leads')->name('offline-send-whatsapp-leads');
            Route::get('product-leads', 'productLeadsList')->name('product-leads');
        });
    });

    //Pooja Records
    Route::group(['prefix' => 'pujarecords', 'as' => 'pujarecords.', 'middleware' => ['module:pujarecords']], function () {
        Route::controller(PujaRecordsController::class)->group(function () {
            // API/Data route
            Route::get('puja-list', 'index')->name('puja-list');
            Route::get('puja-devotee-list', 'getDevotee')->name('puja-devotee-list');
            Route::get('puja-details/{service}/{date}', 'dateDetailsPage')->name('puja-details');
            Route::get('puja-export', 'filteredExport')->name('puja-export');
            Route::get('export-form/{service}', 'exportForm')->name('export-form');
            Route::get('export-download', 'exportDownload')->name('export-download');
            Route::get('export-filtered', 'exportFiltered')->name('export-filtered');
            Route::get('pujaschedule-list', 'pujascheduleindex')->name('pujaschedule-list');
            Route::get('pujareview-list', 'pujareview')->name('pujareview-list');
            Route::get('status-update', 'pujareviewstatus')->name('status-update');
            Route::post('chanage-comment', 'chanagecommentUpdate')->name('chanage-comment');
            Route::post('delete-comment/{order_id}', 'deletecomment')->name('delete-comment');
            //new Kanika
            Route::get('pooja-transaction-list', 'poojaTransactionList')->name('pooja-transaction-list');
        });
    });

    Route::group(['prefix' => 'pujaschedule', 'as' => 'pujaschedule.', 'middleware' => ['module:pujaschedule']], function () {
        Route::controller(PujaScheduleController::class)->group(function () {
            //pujaschedule
            Route::get('pujaschedule-list', 'index')->name('pujaschedule-list');
            Route::post('update-pooja-time', 'updatePoojaTime')->name('update-pooja-time');
            Route::post('update-pooja-week', 'updatePoojaWeek')->name('update-pooja-week');
        });
    });


    //Muhurat Records
    Route::group(['prefix' => 'muhurat', 'as' => 'muhurat.', 'middleware' => ['module:muhurat']], function () {
        Route::controller(MuhuratController::class)->group(function () {
            Route::get('muhurat-list', 'getList')->name('muhurat-list');
            Route::post('update-muhurat/{id}', 'muhurat_update')->name('update-muhurat');
            Route::post('add-muhurat', 'muhurat_store')->name('add-muhurat');
        });
    });

    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    Route::group(['prefix' => 'pos', 'as' => 'pos.', 'middleware' => ['module:POS']], function () {
        Route::controller(POSController::class)->group(function () {
            Route::get(POS::INDEX[URI], 'index')->name('index');
            Route::any(POS::CHANGE_CUSTOMER[URI], 'changeCustomer')->name('change-customer');
            Route::post(POS::UPDATE_DISCOUNT[URI], 'updateDiscount')->name('update-discount');
            Route::post(POS::COUPON_DISCOUNT[URI], 'getCouponDiscount')->name('coupon-discount');
            Route::get(POS::QUICK_VIEW[URI], 'getQuickView')->name('quick-view');
            Route::get(POS::SEARCH[URI], 'getSearchedProductsView')->name('search-product');
        });
        Route::controller(CartController::class)->group(function () {
            Route::post(Cart::VARIANT[URI], 'getVariantPrice')->name('get-variant-price');
            Route::post(Cart::QUANTITY_UPDATE[URI], 'updateQuantity')->name('update-quantity');
            Route::get(Cart::GET_CART_IDS[URI], 'getCartIds')->name('get-cart-ids');
            Route::get(Cart::CLEAR_CART_IDS[URI], 'clearSessionCartIds')->name('clear-cart-ids');
            Route::post(Cart::ADD[URI], 'addToCart')->name('add-to-cart');
            Route::post(Cart::REMOVE[URI], 'removeCart')->name('remove-cart');
            Route::any(Cart::CART_EMPTY[URI], 'emptyCart')->name('empty-cart');
            Route::any(Cart::CHANGE_CART[URI], 'changeCart')->name('change-cart');
            Route::get(Cart::NEW_CART_ID[URI], 'addNewCartId')->name('new-cart-id');
        });
        Route::controller(POSOrderController::class)->group(function () {
            Route::post(POSOrder::ORDER_DETAILS[URI] . '/{id}', 'index')->name('order-details');
            Route::post(POSOrder::ORDER_PLACE[URI], 'placeOrder')->name('place-order');
            Route::any(POSOrder::CANCEL_ORDER[URI], 'cancelOrder')->name('cancel-order');
            Route::any(POSOrder::HOLD_ORDERS[URI], 'getAllHoldOrdersView')->name('view-hold-orders');
        });
    });
    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::controller(ProfileController::class)->group(function () {
            Route::get(Profile::INDEX[URI], 'index')->name('index');
            Route::get(Profile::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Profile::UPDATE[URI] . '/{id}', 'update');
            Route::patch(Profile::UPDATE[URI] . '/{id}', 'updatePassword');
        });
    });
    Route::group(['prefix' => 'products', 'as' => 'products.', 'middleware' => ['module:In-House Product']], function () {
        Route::controller(ProductController::class)->group(function () {
            Route::get(Product::LIST[URI] . '/{type}', 'index')->name('list');
            Route::get(Product::ADD[URI], 'getAddView')->name('add');
            Route::post(Product::ADD[URI], 'add')->name('store');
            Route::get(Product::VIEW[URI] . '/{addedBy}/{id}', 'getView')->name('view');
            Route::post(Product::SKU_COMBINATION[URI], 'getSkuCombinationView')->name('sku-combination');
            Route::post(Product::FEATURED_STATUS[URI], 'updateFeaturedStatus')->name('featured-status');
            Route::get(Product::GET_CATEGORIES[URI], 'getCategories')->name('get-categories');
            Route::post(Product::UPDATE_STATUS[URI], 'updateStatus')->name('status-update');
            Route::get(Product::BARCODE_VIEW[URI] . '/{id}', 'getBarcodeView')->name('barcode');
            Route::get(Product::EXPORT_EXCEL[URI] . '/{type}', 'exportList')->name('export-excel');
            Route::get(Product::STOCK_LIMIT[URI] . '/{type}', 'getStockLimitListView')->name('stock-limit-list');
            Route::delete(Product::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(Product::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Product::UPDATE[URI] . '/{id}', 'update');
            Route::get(Product::DELETE_IMAGE[URI], 'deleteImage')->name('delete-image');
            Route::get(Product::GET_VARIATIONS[URI], 'getVariations')->name('get-variations');
            Route::post(Product::UPDATE_QUANTITY[URI], 'updateQuantity')->name('update-quantity');
            Route::get(Product::BULK_IMPORT[URI], 'getBulkImportView')->name('bulk-import');
            Route::post(Product::BULK_IMPORT[URI], 'importBulkProduct');
            Route::get(Product::UPDATED_PRODUCT_LIST[URI], 'updatedProductList')->name('updated-product-list');
            Route::post(Product::UPDATED_SHIPPING[URI], 'updatedShipping')->name('updated-shipping');
            Route::post(Product::DENY[URI], 'deny')->name('deny');
            Route::post(Product::APPROVE_STATUS[URI], 'approveStatus')->name('approve-status');
            Route::get(Product::SEARCH[URI], 'getSearchedProductsView')->name('search-product');
        });
    });

    Route::group(['prefix' => 'orders', 'as' => 'orders.', 'middleware' => ['module:Product Order']], function () {
        Route::controller(OrderController::class)->group(function () {
            Route::get(Order::LIST[URI] . '/{status}', 'index')->name('list');
            Route::get(Order::EXPORT_EXCEL[URI] . '/{status}', 'exportList')->name('export-excel');
            Route::get(Order::GENERATE_INVOICE[URI] . '/{id}', 'generateInvoice')->name('generate-invoice')->withoutMiddleware(['module:order_management']);
            Route::get(Order::VIEW[URI] . '/{id}', 'getView')->name('details');
            Route::post(Order::UPDATE_ADDRESS[URI], 'updateAddress')->name('address-update'); // update address from order details
            Route::post(Order::UPDATE_DELIVERY_INFO[URI], 'updateDeliverInfo')->name('update-deliver-info');
            Route::get(Order::ADD_DELIVERY_MAN[URI] . '/{order_id}/{d_man_id}', 'addDeliveryMan')->name('add-delivery-man');
            Route::post(Order::UPDATE_AMOUNT_DATE[URI], 'updateAmountDate')->name('amount-date-update');
            Route::get(Order::CUSTOMERS[URI], 'getCustomers')->name('customers');
            Route::post(Order::PAYMENT_STATUS[URI], 'updatePaymentStatus')->name('payment-status');
            Route::get(Order::IN_HOUSE_ORDER_FILTER[URI], 'filterInHouseOrder')->name('inhouse-order-filter');
            Route::post(Order::DIGITAL_FILE_UPLOAD_AFTER_SELL[URI], 'uploadDigitalFileAfterSell')->name('digital-file-upload-after-sell');
            Route::post(Order::UPDATE_STATUS[URI], 'updateStatus')->name('status');
            Route::get(Order::GET_DATA[URI], 'getOrderData')->name('get-order-data');
            Route::get(Order::GET_POOJA[URI], 'getOrderpooja')->name('get-order-pooja');
            Route::get(Order::GET_OFFLINEPOOJA[URI], 'getOrderofflinepooja')->name('get-order-offlinepooja');
            Route::get(Order::GET_COUNSELLING[URI], 'getOrdercounselling')->name('get-order-counselling');

            Route::get(Order::GET_VIP[URI], 'getOrdervip')->name('get-order-vip');
            Route::get(Order::GET_CHADHAVA[URI], 'getOrderChadhava')->name('get-order-chadhava');
            Route::get(Order::GET_ANUSHTHAN[URI], 'getOrderAnushthan')->name('get-order-anushthan');
            Route::get(Order::GET_CUSTOMER[URI], 'getCustomerList')->name('get-customer-list');
            Route::get(Order::GET_VENDOR[URI], 'getVendorRegister')->name('get-vendor-register');

            //delivery partner 
            Route::post('delivery/partner', 'delivery_partner')->name('delivery.partner');
            Route::post('delivery/cancel', 'delivery_cancel')->name('delivery.cancel');
            Route::post('delivery/shipmentcancel', 'delivery_shipmentcancel')->name('delivery.shipmentcancel');
            Route::post('delivery/shipmentcancel', 'delivery_shipmentcancel')->name('delivery.shipmentcancel');
            Route::get('delivery/getcarries/{fromPincode}/{toPincode}/{paymentType}', 'delivery_getcarries')->name('delivery.getcarries');
            Route::post('delivery/pickup', 'delivery_pickup')->name('delivery.pickup');
        });
    });

    // Whatsapp Template
    Route::group(['prefix' => 'whatsapp', 'as' => 'whatsapp.', 'middleware' => ['module:Whatsapp Template']], function () {
        Route::controller(WhatsappController::class)->group(function () {
            Route::get('offline-pooja-template', 'offline_pooja_template')->name('offline-pooja-template');
            Route::get('pooja-template', 'pooja_template')->name('pooja-template');
            Route::get('vip-anushthan-template', 'vip_anushthan_template')->name('vip-anushthan-template');
            Route::get('ecom-template', 'ecom_template')->name('ecom-template');
            Route::get('event-template', 'event_template')->name('event-template');
            Route::get('donation-template', 'donation_template')->name('donation-template');
            Route::get('kundali-template', 'kundali_template')->name('kundali-template');
            Route::post('kundali-template-update/{id}', 'kundali_template_update')->name('kundali-template-update');
            Route::get('tours-template', 'tours_template')->name('tours-template');
            Route::get('chadhava-template', 'chadhava_template')->name('chadhava-template');
            Route::get('counsltancy-template', 'counsltancy_template')->name('counsltancy-template');
            Route::get('whatsapp-panel', 'whatsapp_panel')->name('whatsapp-panel');
            Route::get('temple-darshan-template', 'templeDarshanTemplate')->name('temple-darshan-template');
            Route::get('send-whatsapp-message', 'send_whatsapp_message')->name('send-whatsapp-message');
            Route::post('offline-pooja-template-update/{id}', 'offline_pooja_template_update')->name('offline-pooja-template-update');
            Route::post('pooja-template-update/{id}', 'pooja_template_update')->name('pooja-template-update');
            Route::post('vip-anushthan-template-update/{id}', 'vip_anushthan_template_update')->name('vip-anushthan-template-update');
            Route::post('chadhava-template-update/{id}', 'chadhava_template_update')->name('chadhava-template-update');
            Route::post('counsltancy-template-update/{id}', 'counsltancy_template_update')->name('counsltancy-template-update');
            Route::post('tours-template-update/{id}', 'tours_template_update')->name('tours-template-update');
            Route::post('event-template-update/{id}', 'event_template_update')->name('event-template-update');
            Route::post('ecom-template-update/{id}', 'ecom_template_update')->name('ecom-template-update');
            Route::post('donation-template-update/{id}', 'donation_template_update')->name('donation-template-update');
            Route::post('temple-darshan-template-update/{id}', 'temple_darshan_template_update')->name('temple-darshan-template-update');

            Route::post('create-session', 'create_session')->name('create-session');
            Route::post('check-session', 'check_session')->name('check-session');
            Route::post('logout-session', 'logout_session')->name('logout-session');
            Route::post('send-test-message', 'send_test')->name('send-test-message');
            Route::post('all-send-message', 'all_send_test')->name('all-send-message');
        });
    });
    // email
    Route::group(['prefix' => 'email', 'as' => 'email.', 'middleware' => ['module:Email Template']], function () {
        Route::controller(EmailController::class)->group(function () {
            Route::get('email-template', 'email_template')->name('email-template');
            Route::post('update-email-template/{id}', 'update_email_template')->name('update-email-template');
            Route::get("create-template", "CreateTemplate")->name('create-template');
            Route::post("save-email-template", "SaveEmailTemplate")->name('save-email-template');
            Route::get("email-template-list", "EmailTemplateList")->name('email-template-list');
            Route::get("email-template-update/{id}", "EmailTemplateUpdate")->name('email-template-update');
            Route::get("sendEmailTesting" . "/{id}", "sendEmailTesting")->name('sendEmailTesting');
            Route::post("email-template-update", "EmailTemplateEdit")->name('edit-email-template');
        });
    });
    // pooja order
    Route::group(['prefix' => 'pooja', 'as' => 'pooja.', 'middleware' => ['module:Pooja Order']], function () {
        Route::controller(PoojaOrderController::class)->group(function () {
            Route::get('orders/list/{status}', 'orders_list')->name('orders.list');
            Route::get('checked-status', 'checked_order')->name('orders.checked-status');
            Route::get('orders/orderbypooja', 'orders_by_pooja')->name('orders.orderbypooja');
            // Route::get('orders/upcommingpooja', 'upcomming_pooja')->name('orders.upcommingpooja');
            Route::get('orders/details/{id}', 'orders_details')->name('orders.details');
            Route::post('orders/assign/pandit/{id}', 'orders_assign_pandit')->name('orders.assign.pandit');
            Route::post('orders/status/{id}', 'orders_status')->name('orders.status');
            Route::post('orders/status_times/{id}', 'status_times')->name('orders.status_times');
            Route::post('orders/live_streams/{id}', 'live_streams')->name('orders.live_streams');
            Route::post('orders/pooja_videos/{id}', 'pooja_videos')->name('orders.pooja_videos');
            Route::post('orders/cancel_poojas/{id}', 'cancel_poojas')->name('orders.cancel_poojas');
            // All Order Are Pendit Assinge
            Route::post('orders/assign/pandit', 'all_orders_assign_pandit')->name('orders.assign.allpandit');
            // All Oder Are Assingen
            Route::post('orders/allstatus', 'all_orders_status')->name('orders.allstatus');
            Route::post('orders/status_time', 'status_time')->name('orders.status_time');
            Route::post('orders/live_stream', 'live_stream')->name('orders.live_stream');
            Route::post('orders/pooja_video', 'pooja_video')->name('orders.pooja_video');
            Route::post('orders/cancel_pooja', 'cancel_pooja')->name('orders.cancel_pooja');
            Route::get('orders/generate/invoice/{id}', 'orders_generate_invoice')->name('orders.generate.invoice');
            Route::get('lead/list', 'lead_list')->name('lead.list');
            Route::get('orders/lead-delete/{id}', 'lead_delete')->name('orders.lead-delete');
            Route::post('orders/lead-follow-up', 'followup_store')->name('orders.lead-follow-up');
            Route::get('orders/get-follows-list/{lead_no}', 'getFollowList')->name('orders.get-follows-list');
            Route::get('/get-order-details', 'getOrderDetails')->name('get-order-details');
            Route::get('orders/AllSingleOrder/{service_id}/{booking_date}/{status}', 'all_single_order')->name('orders.AllSingleOrder');
            Route::get('orders/SingleOrderdetails/{booking_date}/{service_id}/{status}', 'single_orders_details')->name('orders.SingleOrderdetails');
            Route::post('orders/updatedOrder', 'order_rejected_update')->name('orders.updatedOrder');
            Route::post('orders/prashad-status/{id}', 'prashad_orders_status')->name('orders.prashad-status');
            Route::get('orders/getpandit/{serviceid}/{bookdate}', 'orders_getpandit')->name('orders.getpandit');
            Route::get('orders/get-pandit-order-count/{panditid}/{bookdate}', 'orders_getpanditordercount');
            Route::get('orders/memberspdf/{service_id}/{booking_date}/{status}', 'downloadMemberList')->name('orders.memberspdf');
            Route::get('orders/send-whatsapp-leads/{id}', 'send_whatsapp_leads')->name('orders.send-whatsapp-leads');
            Route::get('orders/orderbycompleted', 'orders_by_completed')->name('orders.orderbycompleted');
            Route::post('orders/update_pooja_video', 'update_pooja_video')->name('orders.update_pooja_video');
            Route::get('orders/completepuja/{booking_date}/{service_id}/{status}', 'completed_puja')->name('orders.completepuja');
            Route::get('search-customers', 'searchcustomer')->name('search-customers');
            Route::get('get-customer-orders', 'getCustomerOrders')->name('get-customer-orders');
            Route::post('block-orders', 'blockSelectedOrders')->name('block-orders');
            Route::post('orders/update_pooja_certificate', 'update_certificate')->name('orders.update_certificate');

        });
    });
    // Astrologer ya Guruji Or Pandit Puja Order
    Route::group(['prefix' => 'pandit', 'as' => 'pandit.', 'middleware' => ['module:Pandit Order']], function () {
        Route::controller(GurujiPujaOrderController::class)->group(function () {
            Route::get('orders/list/{status}', 'orders_list')->name('orders.list');
            Route::get('orders/lead/list', 'lead_list')->name('orders.lead.list');
            Route::get('orders/details/{id}', 'orders_details')->name('orders.details');
            Route::get('orders/generate/invoice/{id}', 'orders_generate_invoice')->name('orders.generate.invoice');
            Route::post('orders/status/{id}', 'orders_status')->name('orders.status');
            Route::post('orders/status_times/{id}', 'status_times')->name('orders.status_times');
            Route::post('orders/live_streams/{id}', 'live_streams')->name('orders.live_streams');
            Route::post('orders/pooja_videos/{id}', 'pooja_videos')->name('orders.pooja_videos');
            Route::post('orders/update_certificate/{id}','updateCertificate')->name('orders.update_certificate');
        });
    });

    // guruji counselling order 
    Route::group(['prefix' => 'pandit/counselling/order', 'as' => 'pandit.counselling.order.', 'middleware' => ['module:Consultation Order']], function () {
        Route::controller(GurujiCounsellingOrderController::class)->group(function () {
            Route::get('list/{status}', 'orders_list')->name('list');
            // Route::get('checked-status', 'checked_order')->name('checked-status');
            Route::get('details/{id}', 'orders_details')->name('details');
            Route::get('generate/invoice/{id}', 'orders_generate_invoice')->name('generate.invoice');
            Route::post('report/{id}', 'orders_report')->name('report');
            Route::post('report/verify/{id}', 'orders_report_verify')->name('report.verify');
            Route::post('customer/report/reject', 'orders_report_reject')->name('report.reject');
            Route::post('status/{id}', 'orders_status')->name('status');
            Route::post('pending/payment/request', 'pending_payment_request')->name('pending.payment.request');
            Route::get('get-customer-orders', 'getCustomerOrders')->name('get-customer-orders');
            Route::post('block-orders', 'blockSelectedOrders')->name('block-orders');
            Route::get('lead/list', 'lead_list')->name('lead.list');
            Route::get('lead-delete/{id}', 'lead_delete')->name('lead-delete');
            Route::post('lead-follow-up', 'followup_store')->name('lead-follow-up');
            Route::get('get-follows-list/{id}', 'getFollowList')->name('get-follows-list');
            Route::get('orders/send-whatsapp-leads/{id}', 'send_whatsapp_leads')->name('orders.send-whatsapp-leads');
        });
    });
   
    //VIP pooja order
    Route::group(['prefix' => 'vippooja/order', 'as' => 'vippooja.order.', 'middleware' => ['module:Vip Order']], function () {
        Route::controller(VipPoojaOrderController::class)->group(function () {
            Route::get('list/{status}', 'orders_list')->name('list');
            Route::get('list/data', 'VippoojaData')->name('list-data');
            Route::get('vippooja/data', 'VippoojaData')->name('data');
            Route::get('checked-status', 'checked_order')->name('checked-status');
            Route::get('orderbyvippooja', 'orders_by_vippooja')->name('orderbyvippooja');
            Route::get('instanceorder', 'instance_orders')->name('instanceorder');
            Route::post('pandit', 'orders_assign_pandit')->name('pandit');
            Route::get('details/{id}', 'orders_details')->name('details');

            Route::post('status/{id}', 'orders_status')->name('status');
            Route::post('status_times/{id}', 'status_times')->name('status_times');
            Route::post('live_streams/{id}', 'live_streams')->name('live_streams');
            Route::post('pooja_videos/{id}', 'pooja_videos')->name('pooja_videos');
            Route::post('cancel_poojas/{id}', 'cancel_poojas')->name('cancel_poojas');
            // All Order Are Pendit Assinge
            Route::post('assign/pandit', 'all_orders_assign_pandit')->name('assign.allpandit');
            Route::post('assign/allinstancepandit', 'all_orders_instance_assgin_pandit')->name('assign.allinstancepandit');
            // All Oder Are Assingen
            Route::post('allstatus', 'all_orders_status')->name('allstatus');
            Route::post('status_time', 'status_time')->name('status_time');
            Route::post('live_stream', 'live_stream')->name('live_stream');
            Route::post('pooja_video', 'pooja_video')->name('pooja_video');
            Route::post('cancel_pooja', 'cancel_pooja')->name('cancel_pooja');
            // Instance VIP Pooja Routes Assign
            Route::post('allinstancestatus', 'all_instance_status')->name('allinstancestatus');
            Route::post('time_instance', 'time_instance')->name('time_instance');
            Route::post('stream_instance', 'stream_instance')->name('stream_instance');
            Route::post('video_instance', 'video_instance')->name('video_instance');
            Route::post('cancel_instance', 'cancel_instance')->name('cancel_instance');

            Route::get('generate/invoice/{id}', 'orders_generate_invoice')->name('generate.invoice');
            Route::get('lead/list', 'lead_list')->name('lead.list');
            Route::get('lead-delete/{id}', 'lead_delete')->name('lead-delete');
            Route::post('lead-follow-up', 'followup_store')->name('lead-follow-up');
            Route::get('/get-follow-list/{id}', 'getFollowList')->name('get-follow-list');
            Route::get('SingleOrder/{service_id}/{booking_date}/{status}', 'all_single_order')->name('SingleOrder');
            Route::get('SingleOrderdetails/{booking_date}/{service_id}/{status}', 'single_orders_details')->name('SingleOrderdetails');
            Route::get('instanceOrderdetails/{booking_date}/{service_id}/{status}', 'instance_orders_details')->name('instanceOrderdetails');
            Route::get('get-order-details', 'getOrderDetails')->name('get-order-details');
            Route::post('updatedOrder', 'order_rejected_update')->name('updatedOrder');
            Route::post('orders/prashad-status/{id}', 'prashad_orders_status')->name('orders.prashad-status');
            Route::get('getpandit/{serviceid}/{bookdate}', 'orders_getpandit')->name('getpandit');
            Route::get('memberspdf/{service_id}/{booking_date}/{status}', 'downloadMemberList')->name('memberspdf');
            Route::get('orders/send-whatsapp-leads/{id}', 'send_whatsapp_leads')->name('orders.send-whatsapp-leads');
            Route::get('get-customer-orders', 'getCustomerOrders')->name('get-customer-orders');
            Route::post('block-orders', 'blockSelectedOrders')->name('block-orders');
            Route::post('update-certificate','update_vip_certificate')->name('update-certificate');
        });
    });

    //anushthan pooja order
    Route::group(['prefix' => 'anushthan/order', 'as' => 'anushthan.order.', 'middleware' => ['module:Anushthan Order']], function () {
        Route::controller(AnushthanPoojaOrderController::class)->group(function () {
            Route::get('list/{status}', 'orders_list')->name('list');
            Route::get('list/data', 'AnushthanData')->name('list-data');
            Route::get('anushthan/data', 'AnushthanData')->name('data');
            Route::get('checked-status', 'checked_order')->name('checked-status');
            Route::get('orderbyanushthan', 'orders_by_anushthan')->name('orderbyanushthan');
            Route::get('instanceorder', 'instance_orders')->name('instanceorder');
            Route::post('pandit', 'orders_assign_pandit')->name('pandit');
            Route::get('details/{id}', 'orders_details')->name('details');

            Route::post('status/{id}', 'orders_status')->name('status');
            Route::post('status_times/{id}', 'status_times')->name('status_times');
            Route::post('stream_instances/{id}', 'live_streams')->name('live_streams');
            Route::post('pooja_videos/{id}', 'pooja_videos')->name('pooja_videos');
            Route::post('cancel_poojas/{id}', 'cancel_poojas')->name('cancel_poojas');
            // All Order Are Pendit Assinge
            Route::post('assign/pandit', 'all_orders_assign_pandit')->name('assign.allpandit');
            // All Oder Are Assingen
            Route::post('allstatus', 'all_orders_status')->name('allstatus');
            Route::post('status_time', 'status_time')->name('status_time');
            Route::post('live_stream', 'live_stream')->name('live_stream');
            Route::post('pooja_video', 'pooja_video')->name('pooja_video');
            Route::post('cancel_pooja', 'cancel_pooja')->name('cancel_pooja');
            // Instance Anusthan Pooja Routes Assign
            Route::post('allinstancestatus', 'all_instance_status')->name('allinstancestatus');
            Route::post('time_instance', 'time_instance')->name('time_instance');
            Route::post('stream_instance', 'stream_instance')->name('stream_instance');
            Route::post('video_instance', 'video_instance')->name('video_instance');
            Route::post('cancel_instance', 'cancel_instance')->name('cancel_instance');

            Route::get('generate/invoice/{id}', 'orders_generate_invoice')->name('generate.invoice');
            Route::get('lead/list', 'lead_list')->name('lead.list');
            Route::get('lead-delete/{id}', 'lead_delete')->name('lead-delete');
            Route::post('lead-follow-up', 'followup_store')->name('lead-follow-up');
            Route::get('/get-follow-list/{id}', 'getFollowList')->name('get-follow-list');
            Route::get('SingleOrder/{service_id}/{booking_date}/{status}', 'all_single_order')->name('SingleOrder');
            Route::get('memberspdf/{service_id}/{booking_date}/{status}', 'downloadMemberList')->name('memberspdf');
            Route::get('SingleOrderdetails/{booking_date}/{service_id}/{status}', 'single_orders_details')->name('SingleOrderdetails');
            Route::get('instanceOrderdetails/{booking_date}/{service_id}/{status}', 'instance_orders_details')->name('instanceOrderdetails');
            Route::get('get-order-details', 'getOrderDetails')->name('get-order-details');
            Route::post('updatedOrder', 'order_rejected_update')->name('updatedOrder');
            Route::post('orders/prashad-status/{id}', 'prashad_orders_status')->name('orders.prashad-status');
            Route::get('getpandit/{serviceid}/{bookdate}', 'orders_getpandit')->name('getpandit');
            Route::get('orders/send-whatsapp-leads/{id}', 'send_whatsapp_leads')->name('orders.send-whatsapp-leads');
            Route::get('get-customer-orders', 'getCustomerOrders')->name('get-customer-orders');
            Route::post('block-orders', 'blockSelectedOrders')->name('block-orders');
            Route::post('update-certificate', 'update_anushthan_certificate')->name('update-certificate');
        });
    });

    //Chadhava  order
    Route::group(['prefix' => 'chadhava/order', 'as' => 'chadhava.order.', 'middleware' => ['module:Chadhava Order']], function () {
        Route::controller(ChadhavaOrderController::class)->group(function () {
            Route::get('list/{status}', 'orders_list')->name('list');
            Route::get('checked-status', 'checked_order')->name('checked-status');
            Route::get('orderbychadhava', 'orders_by_chadhava')->name('orderbychadhava');
            Route::get('details/{id}', 'orders_details')->name('details');
            Route::post('assign/pandit/{id}', 'orders_assign_pandit')->name('assign.pandit');
            Route::post('status/{id}', 'orders_status')->name('status');
            Route::post('status_times/{id}', 'status_times')->name('status_times');
            Route::post('live_streams/{id}', 'live_streams')->name('live_streams');
            Route::post('pooja_videos/{id}', 'pooja_videos')->name('pooja_videos');
            Route::post('cancel_poojas/{id}', 'cancel_poojas')->name('cancel_poojas');
            // All Order Are Pendit Assinge
            Route::post('assign/pandit', 'all_orders_assign_pandit')->name('assign.allpandit');
            // All Oder Are Assingen
            Route::post('allstatus', 'all_orders_status')->name('allstatus');
            Route::post('status_time/', 'status_time')->name('status_time');
            Route::post('live_stream/', 'live_stream')->name('live_stream');
            Route::post('pooja_video/', 'pooja_video')->name('pooja_video');
            Route::post('cancel_pooja/', 'cancel_pooja')->name('cancel_pooja');
            Route::get('generate/invoice/{id}', 'orders_generate_invoice')->name('generate.invoice');
            Route::get('lead/list', 'lead_list')->name('lead.list');
            Route::get('lead-delete/{id}', 'lead_delete')->name('lead-delete');
            Route::post('lead-follow-up', 'followup_store')->name('lead-follow-up');
            Route::get('/get-follow-list/{id}', 'getFollowList')->name('get-follow-list');
            Route::get('SingleOrder/{service_id}/{booking_date}/{status}', 'all_single_order')->name('SingleOrder');
            Route::get('memberspdf/{service_id}/{booking_date}/{status}', 'downloadMemberList')->name('memberspdf');
            Route::get('SingleOrderdetails/{booking_date}/{service_id}/{status}', 'single_orders_details')->name('SingleOrderdetails');
            Route::get('get-order-details', 'getOrderDetails')->name('get-order-details');
            Route::post('updatedOrder', 'order_rejected_update')->name('updatedOrder');
            Route::get('getpandit/{serviceid}/{bookdate}', 'orders_getpandit')->name('getpandit');
            Route::get('orders/send-whatsapp-leads/{id}', 'send_whatsapp_leads')->name('orders.send-whatsapp-leads');
            Route::get('orderbycompleted', 'orders_by_completed')->name('orderbycompleted');
            Route::post('update_chadhava_video', 'update_chadhava_video')->name('update_chadhava_video');
            Route::get('completechadhava/{booking_date}/{service_id}/{status}', 'completed_chadhava')->name('completechadhava');
            Route::get('get-customer-orders', 'getCustomerOrders')->name('get-customer-orders');
            Route::post('block-orders', 'blockSelectedOrders')->name('block-orders');
            Route::post('update_chadhava_certificate', 'update_certificate')->name('update_chadhava_certificate');
        });
    });

    Route::get('offlinepooja-checked-status', [OfflinePoojaOrderController::class, 'checked_order'])->name('offlinepooja-checked-status');
    //OFFLINE pooja order
    Route::group(['prefix' => 'offlinepooja/order', 'as' => 'offlinepooja.order.', 'middleware' => ['module:Offline Order']], function () {
        Route::controller(OfflinePoojaOrderController::class)->group(function () {
            Route::get('list/{status}', 'orders_list')->name('list');
            // Route::get('orderbyvippooja', 'orders_by_vippooja')->name('orderbyvippooja');
            Route::get('details/{orderId}', 'orders_details')->name('details');
            Route::post('assign/pandit/{orderId}', 'orders_assign_pandit')->name('assign.pandit');
            Route::post('status/{orderId}', 'orders_status')->name('status');
            Route::post('status_times/{orderId}', 'status_times')->name('status_times');
            Route::post('live_streams/{orderId}', 'live_streams')->name('live_streams');
            // Route::post('pooja_videos/{id}', 'pooja_videos')->name('pooja_videos');
            Route::post('schedule_poojas/{orderId}', 'schedule_poojas')->name('schedule_poojas');
            Route::post('live_url_poojas/{orderId}', 'live_url_poojas')->name('live_url_poojas');
            Route::post('cancel_poojas/{orderId}', 'cancel_poojas')->name('cancel_poojas');
            Route::get('refund/amount/{orderId}', 'refund_amount')->name('refund.amount');
            Route::get('get-customer-orders', 'getCustomerOrders')->name('get-customer-orders');
            Route::post('block-orders', 'blockSelectedOrders')->name('block-orders');
            // All Order Are Pendit Assinge
            // Route::post('assign/pandit', 'all_orders_assign_pandit')->name('assign.allpandit');
            // Route::post('assign/allinstancepandit', 'all_orders_instance_assgin_pandit')->name('assign.allinstancepandit');
            // All Oder Are Assingen
            // Route::post('allstatus', 'all_orders_status')->name('allstatus');
            // Route::post('status_time', 'status_time')->name('status_time');
            // Route::post('live_stream', 'live_stream')->name('live_stream');
            // Route::post('pooja_video', 'pooja_video')->name('pooja_video');
            // Route::post('cancel_pooja', 'cancel_pooja')->name('cancel_pooja');
            // Instance VIP Pooja Routes Assign
            // Route::post('allinstancestatus', 'all_instance_status')->name('allinstancestatus');
            // Route::post('time_instance', 'time_instance')->name('time_instance');
            // Route::post('stream_instance', 'stream_instance')->name('stream_instance');
            // Route::post('video_instance', 'video_instance')->name('video_instance');
            // Route::post('cancel_instance', 'cancel_instance')->name('cancel_instance');

            Route::get('generate/invoice/{orderId}', 'orders_generate_invoice')->name('generate.invoice');
            Route::get('lead/list', 'lead_list')->name('lead.list');
            Route::get('lead-delete/{id}', 'lead_delete')->name('lead-delete');
            Route::post('lead-follow-up', 'followup_store')->name('lead-follow-up');
            Route::get('/get-follow-list/{id}', 'getFollowList')->name('get-follow-list');
            // Route::get('SingleOrder/{service_id}/{status}', 'all_single_order')->name('SingleOrder');
            // Route::get('SingleOrderdetails/{booking_date}/{service_id}/{status}', 'single_orders_details')->name('SingleOrderdetails');
            // Route::get('instanceOrderdetails/{booking_date}/{service_id}/{status}', 'instance_orders_details')->name('instanceOrderdetails');
            // Route::get('get-order-details', 'getOrderDetails')->name('get-order-details');
            // Route::post('updatedOrder', 'order_rejected_update')->name('updatedOrder');
            // Route::post('orders/prashad-status/{id}', 'prashad_orders_status')->name('orders.prashad-status');
            // Route::get('getpandit/{serviceid}/{bookdate}', 'orders_getpandit')->name('getpandit');
            Route::get('orders/send-whatsapp-leads/{id}', 'send_whatsapp_leads')->name('orders.send-whatsapp-leads');
            Route::post('pending/payment/request', 'pending_payment_request')->name('pending.payment.request');
        });
    });

    //counselling order
    Route::group(['prefix' => 'counselling/order', 'as' => 'counselling.order.', 'middleware' => ['module:Consultation Order']], function () {
        Route::controller(CounsellingOrderController::class)->group(function () {
            Route::get('list/{status}', 'orders_list')->name('list');
            Route::get('checked-status', 'checked_order')->name('checked-status');
            Route::get('details/{id}', 'orders_details')->name('details');
            Route::post('assign/astrologer/{id}', 'orders_assign_astrologer')->name('assign.astrologer');
            Route::post('report/{id}', 'orders_report')->name('report');
            Route::post('report/verify/{id}', 'orders_report_verify')->name('report.verify');
            Route::post('customer/report/reject', 'orders_report_reject')->name('report.reject');
            Route::post('status/{id}', 'orders_status')->name('status');
            Route::get('generate/invoice/{id}', 'orders_generate_invoice')->name('generate.invoice');
            Route::get('lead/list', 'lead_list')->name('lead.list');
            Route::get('lead-delete/{id}', 'lead_delete')->name('lead-delete');
            Route::post('lead-follow-up', 'followup_store')->name('lead-follow-up');
            Route::get('get-follows-list/{id}', 'getFollowList')->name('get-follows-list');
            Route::get('orders/send-whatsapp-leads/{id}', 'send_whatsapp_leads')->name('orders.send-whatsapp-leads');
            Route::post('pending/payment/request', 'pending_payment_request')->name('pending.payment.request');
            Route::get('get-customer-orders', 'getCustomerOrders')->name('get-customer-orders');
            Route::post('block-orders', 'blockSelectedOrders')->name('block-orders');
        });
    });

    // Prashaed Order
    Route::group(['prefix' => 'prashad/order', 'as' => 'prashad.order.', 'middleware' => ['module:Prashad Order']], function () {
        Route::controller(PrashadOrderController::class)->group(function () {
            Route::get('list/{status}', 'orders_list')->name('list');
            Route::get('orderbyprashad', 'orders_by_prashad')->name('orderbyprashad');
            Route::get('prashadamDetails/{service_id}/{date}', 'prashad_details')->name('prashadamDetails');
            Route::post('prashadstatus/{service_id}/{date}', 'updatePrashadamStatus')->name('prashadstatus');
            Route::post('shipwaydelivery/{order_id}', 'shipwayorder')->name('shipwaydelivery');
            Route::post('shipwayCancel/{order_id}', 'shipwaycancel')->name('shipwayCancel');
        });
    });

    
    // Attribute
    Route::group(['prefix' => 'attribute', 'as' => 'attribute.', 'middleware' => ['module:Attribute Setup']], function () {
        Route::controller(AttributeController::class)->group(function () {
            Route::get(Attribute::LIST[URI], 'index')->name('view');
            Route::post(Attribute::STORE[URI], 'add')->name('store');
            Route::get(Attribute::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Attribute::UPDATE[URI] . '/{id}', 'update');
            Route::post(Attribute::DELETE[URI], 'delete')->name('delete');
        });
    });

    // Rashi
    Route::group(['prefix' => 'rashi', 'as' => 'rashi.', 'middleware' => ['module:Rashi']], function () {
        Route::controller(RashiController::class)->group(function () {
            Route::get(Rashi::LIST[URI], 'index')->name('list');
            Route::get(Rashi::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Rashi::ADD[URI], 'add');
            Route::get(Rashi::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Rashi::UPDATE[URI] . '/{id}', 'update');
            Route::post(Rashi::DELETE[URI], 'delete')->name('delete');
            Route::get(Rashi::EXPORT[URI], 'exportList')->name('export');
            Route::post(Rashi::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // MasikRashi
    Route::group(['prefix' => 'masikrashi', 'as' => 'masikrashi.', 'middleware' => ['module:Masik Rashi']], function () {
        Route::controller(MasikRashiController::class)->group(function () {
            Route::get(MasikRashi::LIST[URI], 'index')->name('list');
            Route::get(MasikRashi::ADD[URI], 'getAddView')->name('add-new');
            Route::post(MasikRashi::ADD[URI], 'add');
            Route::get(MasikRashi::UPDATE[URI] . '/{id}', 'edit')->name('update');
            Route::post(MasikRashi::UPDATE[URI] . '/{id}', 'update')->name('edits');
            Route::post(MasikRashi::DELETE[URI], 'delete')->name('delete');
            Route::get(MasikRashi::EXPORT[URI], 'exportList')->name('export');
            Route::post(MasikRashi::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // VarshikRashi
    Route::group(['prefix' => 'varshikrashi', 'as' => 'varshikrashi.', 'middleware' => ['module:Varshik Rashi']], function () {
        Route::controller(VarshikRashiController::class)->group(function () {
            Route::get(VarshikRashi::LIST[URI], 'index')->name('list');
            Route::get(VarshikRashi::ADD[URI], 'getAddView')->name('add-new');
            Route::post(VarshikRashi::ADD[URI], 'add');
            Route::get(VarshikRashi::UPDATE[URI] . '/{id}', 'edit')->name('update');
            Route::post(VarshikRashi::UPDATE[URI] . '/{id}', 'update');
            Route::post(VarshikRashi::DELETE[URI], 'distroy')->name('delete');
            Route::get(VarshikRashi::EXPORT[URI], 'exportList')->name('export');
            Route::post(VarshikRashi::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // Katha
    Route::group(['prefix' => 'katha', 'as' => 'katha.', 'middleware' => ['module:Katha']], function () {
        Route::controller(KathaController::class)->group(function () {
            Route::get(Katha::LIST[URI], 'index')->name('list');
            Route::get(Katha::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Katha::ADD[URI], 'add');
            Route::get(Katha::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Katha::UPDATE[URI] . '/{id}', 'update');
            Route::post(Katha::DELETE[URI], 'delete')->name('delete');
            Route::get(Katha::EXPORT[URI], 'exportList')->name('export');
            Route::post(Katha::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });
    // SaptahikVratKatha
    Route::group(['prefix' => 'saptahikvratkatha', 'as' => 'saptahikvratkatha.', 'middleware' => ['module:Saptahik Katha']], function () {
        Route::controller(SaptahikKathaController::class)->group(function () {
            Route::get(SaptahikVratKatha::LIST[URI], 'index')->name('list');
            Route::get(SaptahikVratKatha::ADD[URI], 'getAddView')->name('add-new');
            Route::post(SaptahikVratKatha::ADD[URI], 'add');
            Route::get(SaptahikVratKatha::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(SaptahikVratKatha::UPDATE[URI] . '/{id}', 'update');
            Route::post(SaptahikVratKatha::DELETE[URI], 'delete')->name('delete');
            Route::get(SaptahikVratKatha::EXPORT[URI], 'exportList')->name('export');
            Route::post(SaptahikVratKatha::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // PradoshKatha
    Route::group(['prefix' => 'pradoshkatha', 'as' => 'pradoshkatha.', 'middleware' => ['module:Pradosh Katha']], function () {
        Route::controller(PradoshKathaController::class)->group(function () {
            Route::get(PradoshKatha::LIST[URI], 'index')->name('list');
            Route::get(PradoshKatha::ADD[URI], 'getAddView')->name('add-new');
            Route::post(PradoshKatha::ADD[URI], 'add');
            Route::get(PradoshKatha::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(PradoshKatha::UPDATE[URI] . '/{id}', 'update');
            Route::post(PradoshKatha::DELETE[URI], 'delete')->name('delete');
            Route::get(PradoshKatha::EXPORT[URI], 'exportList')->name('export');
            Route::post(PradoshKatha::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // CalendarDay
    Route::group(['prefix' => 'calendarday', 'as' => 'calendarday.', 'middleware' => ['module:Calendar Day']], function () {
        Route::controller(CalendarDayController::class)->group(function () {
            Route::get(CalendarDay::LIST[URI], 'index')->name('list');
            Route::get(CalendarDay::ADD[URI], 'getAddView')->name('add-new');
            Route::post(CalendarDay::ADD[URI], 'add');
            Route::get(CalendarDay::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(CalendarDay::UPDATE[URI] . '/{id}', 'update');
            Route::post(CalendarDay::DELETE[URI], 'delete')->name('delete');
            Route::get(CalendarDay::EXPORT[URI], 'exportList')->name('export');
            Route::post(CalendarDay::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // CalendarNakshatra
    Route::group(['prefix' => 'calendarnakshatra', 'as' => 'calendarnakshatra.', 'middleware' => ['module:Calendar Nakshatra']], function () {
        Route::controller(CalendarNakshatraController::class)->group(function () {
            Route::get(CalendarNakshatra::LIST[URI], 'index')->name('list');
            Route::get(CalendarNakshatra::ADD[URI], 'getAddView')->name('add-new');
            Route::post(CalendarNakshatra::ADD[URI], 'add');
            Route::get(CalendarNakshatra::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(CalendarNakshatra::UPDATE[URI] . '/{id}', 'update');
            Route::post(CalendarNakshatra::DELETE[URI], 'delete')->name('delete');
            Route::get(CalendarNakshatra::EXPORT[URI], 'exportList')->name('export');
            Route::post(CalendarNakshatra::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // CalendarVikramSamvat
    Route::group(['prefix' => 'calendarvikramsamvat', 'as' => 'calendarvikramsamvat.', 'middleware' => ['module:Calendar Vikram Samvat']], function () {
        Route::controller(CalendarVikramSamvatController::class)->group(function () {
            Route::get(CalendarVikramSamvat::LIST[URI], 'index')->name('list');
            Route::get(CalendarVikramSamvat::ADD[URI], 'getAddView')->name('add-new');
            Route::post(CalendarVikramSamvat::ADD[URI], 'add');
            Route::get(CalendarVikramSamvat::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(CalendarVikramSamvat::UPDATE[URI] . '/{id}', 'update');
            Route::post(CalendarVikramSamvat::DELETE[URI], 'delete')->name('delete');
            Route::get(CalendarVikramSamvat::EXPORT[URI], 'exportList')->name('export');
            Route::post(CalendarVikramSamvat::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // CalendarHindiMonth
    Route::group(['prefix' => 'calendarhindimonth', 'as' => 'calendarhindimonth.', 'middleware' => ['module:Calendar Hindi Month']], function () {
        Route::controller(CalendarHindiMonthController::class)->group(function () {
            Route::get(CalendarHindiMonth::LIST[URI], 'index')->name('list');
            Route::get(CalendarHindiMonth::ADD[URI], 'getAddView')->name('add-new');
            Route::post(CalendarHindiMonth::ADD[URI], 'add');
            Route::get(CalendarHindiMonth::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(CalendarHindiMonth::UPDATE[URI] . '/{id}', 'update');
            Route::post(CalendarHindiMonth::DELETE[URI], 'delete')->name('delete');
            Route::get(CalendarHindiMonth::EXPORT[URI], 'exportList')->name('export');
            Route::post(CalendarHindiMonth::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // FestivalHindiMonth
    Route::group(['prefix' => 'festivalhindimonth', 'as' => 'festivalhindimonth.', 'middleware' => ['module:Fast Festival']], function () {
        Route::controller(FestivalHindiMonthController::class)->group(function () {
            Route::get(FestivalHindiMonth::LIST[URI], 'index')->name('list');
            Route::get(FestivalHindiMonth::ADD[URI], 'getAddView')->name('add-new');
            Route::post(FestivalHindiMonth::ADD[URI], 'add');
            Route::get(FestivalHindiMonth::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(FestivalHindiMonth::UPDATE[URI] . '/{id}', 'update');
            Route::post(FestivalHindiMonth::DELETE[URI], 'delete')->name('delete');
            Route::get(FestivalHindiMonth::EXPORT[URI], 'exportList')->name('export');
            Route::post(FestivalHindiMonth::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    //video
    Route::group(['prefix' => 'video', 'as' => 'video.', 'middleware' => ['module:Youtube']], function () {
        Route::controller(VideoController::class)->group(function () {
            Route::get(Video::LIST[URI], 'index')->name('list');
            Route::get(Video::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Video::ADD[URI], 'add');
            Route::get(Video::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Video::UPDATE[URI] . '/{id}', 'update');
            Route::delete(Video::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(Video::EXPORT[URI], 'exportList')->name('export');
            Route::post(Video::STATUS[URI], 'updateStatus')->name('status-update');
            Route::post('subcategories', [VideoController::class, 'getSubcategories'])->name('subcategories');
            Route::get('list_details/{id}', 'list_view_details')->name('list_details');
            Route::post('update-url-status', [VideoController::class, 'updateUrlStatus'])->name('update-url-status');
        });
    });

    //videolisttype
    Route::group(['prefix' => 'videolisttype', 'as' => 'videolisttype.', 'middleware' => ['module:Youtube']], function () {
        Route::controller(VideoListTypeController::class)->group(function () {
            Route::get(VideoListType::LIST[URI], 'index')->name('view');
            Route::post(VideoListType::STORE[URI], 'add')->name('store');
            Route::get(VideoListType::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(VideoListType::UPDATE[URI] . '/{id}', 'update');
            Route::post(VideoListType::DELETE[URI], 'delete')->name('delete');
            Route::post(VideoListType::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });


    //videocategory
    Route::group(['prefix' => 'videocategory', 'as' => 'videocategory.', 'middleware' => ['module:Youtube']], function () {
        Route::controller(VideoCategoryController::class)->group(function () {
            Route::get(VideoCategory::LIST[URI], 'index')->name('view');
            Route::post(VideoCategory::STORE[URI], 'add')->name('store');
            Route::get(VideoCategory::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(VideoCategory::UPDATE[URI] . '/{id}', 'update');
            Route::post(VideoCategory::DELETE[URI], 'delete')->name('delete');
            Route::post(VideoCategory::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    //videosubcategory

    Route::group(['prefix' => 'videosubcategory', 'as' => 'videosubcategory.', 'middleware' => ['module:Youtube']], function () {
        Route::controller(VideoSubCategoryController::class)->group(function () {
            Route::get(VideoSubCategory::LIST[URI], 'index')->name('view');
            Route::post(VideoSubCategory::STORE[URI], 'add')->name('store');
            Route::get(VideoSubCategory::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(VideoSubCategory::UPDATE[URI] . '/{id}', 'update');
            Route::post(VideoSubCategory::DELETE[URI], 'delete')->name('delete');
            Route::post(VideoSubCategory::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });
    //sangeetcategory
    Route::group(['prefix' => 'sangeetcategory', 'as' => 'sangeetcategory.', 'middleware' => ['module:Sangeet']], function () {
        Route::controller(SangeetCategoryController::class)->group(function () {
            Route::get(SangeetCategory::LIST[URI], 'index')->name('view');
            Route::post(SangeetCategory::STORE[URI], 'add')->name('store');
            Route::get(SangeetCategory::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(SangeetCategory::UPDATE[URI] . '/{id}', 'update');
            Route::post(SangeetCategory::DELETE[URI], 'delete')->name('delete');
            Route::post(SangeetCategory::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });


    //sangeetsubcategory
    Route::group(['prefix' => 'sangeetsubcategory', 'as' => 'sangeetsubcategory.', 'middleware' => ['module:Sangeet']], function () {
        Route::controller(SangeetSubCategoryController::class)->group(function () {
            Route::get(SangeetSubCategory::LIST[URI], 'index')->name('view');
            Route::post(SangeetSubCategory::STORE[URI], 'add')->name('store');
            Route::get(SangeetSubCategory::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(SangeetSubCategory::UPDATE[URI] . '/{id}', 'update');
            Route::post(SangeetSubCategory::DELETE[URI], 'delete')->name('delete');
            Route::post(SangeetSubCategory::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });



    //sangeetlanguage
    Route::group(['prefix' => 'sangeetlanguage', 'as' => 'sangeetlanguage.', 'middleware' => ['module:Sangeet']], function () {
        Route::controller(SangeetLanguageController::class)->group(function () {
            Route::get(SangeetLanguage::LIST[URI], 'index')->name('view');
            Route::post(SangeetLanguage::STORE[URI], 'add')->name('store');
            Route::get(SangeetLanguage::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(SangeetLanguage::UPDATE[URI] . '/{id}', 'update');
            Route::post(SangeetLanguage::DELETE[URI], 'delete')->name('delete');
            Route::post(SangeetLanguage::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });


    //sangeet
    Route::group(['prefix' => 'sangeet', 'as' => 'sangeet.', 'middleware' => ['module:Sangeet']], function () {
        Route::controller(SangeetController::class)->group(function () {
            Route::get(Sangeet::LIST[URI], 'index')->name('list');
            Route::get(Sangeet::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Sangeet::ADD[URI], 'add');
            Route::get(Sangeet::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Sangeet::UPDATE[URI] . '/{id}', 'update');
            Route::delete(Sangeet::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(Sangeet::EXPORT[URI], 'exportList')->name('export');
            Route::post(Sangeet::STATUS[URI], 'updateStatus')->name('status-update');
            Route::post('subcategories', [SangeetController::class, 'getSubcategories'])->name('subcategories');
            Route::get('add_details/{id}', 'add_details')->name('add_details');
            Route::post('details', [SangeetController::class, 'storeSangeetDetails'])->name('storeDetails');
            Route::get('details/{sangeet_id}', [SangeetController::class, 'viewSangeetDetails'])->name('details');
            Route::get('all', [SangeetController::class, 'viewAllDetails'])->name('all');
            Route::patch('/{id}/soft-delete', [SangeetController::class, 'softDelete'])->name('soft-delete');
            Route::get('edit-details/{id}', 'editDetails')->name('editDetails');
            Route::put('details/{sangeet_id}', [SangeetController::class, 'updateSangeetDetails'])->name('update.details');
            Route::post('/status/{id}', [SangeetController::class, 'updateDetailStatus'])->name('updateDetailStatus');
            // Route::get('export', [SangeetController::class, 'export'])->name('export');
            //Route::post('/import-sangeet', [SangeetController::class, 'import'])->name('import');
            Route::get('/recover', [SangeetController::class, 'recover'])->name('recover');
            Route::patch('/restore/{id}', [SangeetController::class, 'restore'])->name('restore');
        });
    });

    // Festivaladd
    Route::group(['prefix' => 'festivaladd', 'as' => 'festivaladd.', 'middleware' => ['module:Fast Festival']], function () {
        Route::controller(FestivalAddController::class)->group(function () {
            Route::get(FestivalAdd::LIST[URI], 'index')->name('list');
            Route::get(FestivalAdd::ADD[URI], 'getAddView')->name('add-new');
            Route::post(FestivalAdd::ADD[URI], 'add');
            Route::get(FestivalAdd::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(FestivalAdd::UPDATE[URI] . '/{id}', 'update');
            Route::post(FestivalAdd::DELETE[URI], 'delete')->name('delete');
            Route::get(FestivalAdd::EXPORT[URI], 'exportList')->name('export');
            Route::post(FestivalAdd::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    //panchang moon image
    Route::group(['prefix' => 'panchangmoonimage', 'as' => 'panchangmoonimage.', 'middleware' => ['module:Panchnage Moon']], function () {
        Route::controller(PanchangMoonImageController::class)->group(function () {
            Route::get(PanchangMoonImage::LIST[URI], 'index')->name('view');
            Route::post(PanchangMoonImage::STORE[URI], 'add')->name('store');
            Route::get(PanchangMoonImage::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(PanchangMoonImage::UPDATE[URI] . '/{id}', 'update');
            Route::post(PanchangMoonImage::DELETE[URI], 'delete')->name('delete');
            Route::post(PanchangMoonImage::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    //app section
    Route::group(['prefix' => 'appsection', 'as' => 'appsection.', 'middleware' => ['module:App Section']], function () {
        Route::controller(AppSectionController::class)->group(function () {
            Route::get(AppSection::LIST[URI], 'index')->name('view');
            Route::post(AppSection::STORE[URI], 'add')->name('store');
            Route::get(AppSection::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(AppSection::UPDATE[URI] . '/{id}', 'update');
            Route::post(AppSection::DELETE[URI], 'delete')->name('delete');
            Route::post(AppSection::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });
    //sahitya
    Route::group(['prefix' => 'sahitya', 'as' => 'sahitya.', 'middleware' => ['module:Sahitya']], function () {
        Route::controller(SahityaController::class)->group(function () {
            Route::get(Sahitya::LIST[URI], 'index')->name('view');
            Route::post(Sahitya::STORE[URI], 'add')->name('store');
            Route::get(Sahitya::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Sahitya::UPDATE[URI] . '/{id}', 'update');
            Route::post(Sahitya::DELETE[URI], 'delete')->name('delete');
            Route::post(Sahitya::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    //bhagavad gita
    Route::group(['prefix' => 'bhagavadgita', 'as' => 'bhagavadgita.', 'middleware' => ['module:Sahitya']], function () {
        Route::controller(BhagavadGitaController::class)->group(function () {
            Route::get(BhagavadGita::LIST[URI], 'index')->name('list');
            Route::get(BhagavadGita::ADD[URI], 'getAddView')->name('add-new');
            Route::post(BhagavadGita::ADD[URI], 'add');
            Route::get(BhagavadGita::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(BhagavadGita::UPDATE[URI] . '/{id}', 'update');
            Route::delete(BhagavadGita::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(BhagavadGita::EXPORT[URI], 'exportList')->name('export');
            Route::post(BhagavadGita::STATUS[URI], 'updateStatus')->name('status-update');
            Route::get('add_verse/{id}', 'add_verse')->name('add_verse');
            Route::post('details', [BhagavadGitaController::class, 'storeVerseDetails'])->name('storeDetails');
            Route::get('details/{chapter_id}', [BhagavadGitaController::class, 'viewVerseDetails'])->name('details');
            Route::get('verse-details/{id}', [BhagavadGitaController::class, 'viewAllDetails'])->name('all-details');
            Route::patch('/{id}/soft-delete', [BhagavadGitaController::class, 'softDelete'])->name('soft-delete');
            Route::get('edit-verse/{id}', 'editVerse')->name('editVerse');
            Route::put('details/{chapter_id}', [BhagavadGitaController::class, 'updateVerseDetails'])->name('update.details');
            Route::post('/status/{id}', [BhagavadGitaController::class, 'updateDetailStatus'])->name('updateDetailStatus');
            Route::get('/recover', [BhagavadGitaController::class, 'recover'])->name('recover');
            Route::patch('/restore/{id}', [BhagavadGitaController::class, 'restore'])->name('restore');
            Route::get('bhagavad-gita-json', [BhagavadGitaController::class, 'json'])->name('bhagavad-gita-json');
        });
    });
    //valmiki ramayan
    Route::group(['prefix' => 'valmikiramayan', 'as' => 'valmikiramayan.', 'middleware' => ['module:Sahitya']], function () {
        Route::controller(ValmikiRamayanController::class)->group(function () {
            Route::get(ValmikiRamayan::LIST[URI], 'index')->name('view');
            Route::post(ValmikiRamayan::STORE[URI], 'add')->name('store');
            Route::get(ValmikiRamayan::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(ValmikiRamayan::UPDATE[URI] . '/{id}', 'update');
            Route::post(ValmikiRamayan::DELETE[URI], 'delete')->name('delete');
            Route::post(ValmikiRamayan::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });


    //tulsidas ramayan
    Route::group(['prefix' => 'tulsidasramayan', 'as' => 'tulsidasramayan.', 'middleware' => ['module:Sahitya']], function () {
        Route::controller(TulsidasRamayanController::class)->group(function () {
            Route::get(TulsidasRamayan::LIST[URI], 'index')->name('view');
            Route::post(TulsidasRamayan::STORE[URI], 'add')->name('store');
            Route::get(TulsidasRamayan::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(TulsidasRamayan::UPDATE[URI] . '/{id}', 'update');
            Route::post(TulsidasRamayan::DELETE[URI], 'delete')->name('delete');
            Route::post(TulsidasRamayan::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });
    //ram shalaka
    Route::group(['prefix' => 'ramshalaka', 'as' => 'ramshalaka.', 'middleware' => ['module:Sahitya']], function () {
        Route::controller(RamShalakaController::class)->group(function () {
            Route::get(RamShalaka::LIST[URI], 'index')->name('view');
            Route::post(RamShalaka::STORE[URI], 'add')->name('store');
            Route::get(RamShalaka::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(RamShalaka::UPDATE[URI] . '/{id}', 'update');
            Route::post(RamShalaka::DELETE[URI], 'delete')->name('delete');
            Route::post(RamShalaka::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });
    //bhagwan
    Route::group(['prefix' => 'bhagwan', 'as' => 'bhagwan.', 'middleware' => ['module:bhagwan']], function () {
        Route::controller(BhagwanController::class)->group(function () {
            Route::get(Bhagwan::LIST[URI], 'index')->name('list');
            Route::get(Bhagwan::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Bhagwan::ADD[URI], 'add');
            Route::get(Bhagwan::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Bhagwan::UPDATE[URI] . '/{id}', 'update');
            Route::delete(Bhagwan::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(Bhagwan::EXPORT[URI], 'exportList')->name('export');
            Route::post(Bhagwan::STATUS[URI], 'updateStatus')->name('status-update');
            Route::get(Bhagwan::DELETE_IMAGE[URI] . '{id}/{name}', 'deleteImage')->name('delete-image');
            Route::post('store-event-image', [BhagwanController::class, 'storeEventImage'])->name('store-event-image');
            Route::post('update_event', [BhagwanController::class, 'updateEvent'])->name('update_event');
            Route::get(Bhagwan::BHAGWANLOGS[URI], 'BhagwanLogsList')->name('bhagwan-logs-list');
        });
    });

    //jaap
    Route::group(['prefix' => 'jaap', 'as' => 'jaap.', 'middleware' => ['module:Jaap']], function () {
        Route::controller(jaapcontroller::class)->group(function () {
            Route::get(Jaap::LIST[URI], 'index')->name('view');
            Route::post(Jaap::STORE[URI], 'add')->name('store');
            Route::get(Jaap::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Jaap::UPDATE[URI] . '/{id}', 'update');
            Route::post(Jaap::DELETE[URI], 'delete')->name('delete');
            Route::post(Jaap::STATUS[URI], 'updateStatus')->name('status-update');
            Route::get(Jaap::JAAPUSER[URI], 'jaapUserList')->name('jaap-user-list');
        });
    });

    //visitor
    Route::group(['prefix' => 'visitor', 'as' => 'visitor.', 'middleware' => ['module:visitor']], function () {
        Route::controller(VisitorController::class)->group(function () {
            Route::get('visitor', 'visitor')->name('visitor-list');
            Route::get('visitor/data', 'ShowVisitorData')->name('visitor-data');
        });
    });

    // Festival
    Route::group(['prefix' => 'festival', 'as' => 'festival.', 'middleware' => ['module:astrology_management']], function () {
        Route::controller(FestivalController::class)->group(function () {
            Route::get(Festival::LIST[URI], 'index')->name('list');
            Route::get(Festival::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Festival::ADD[URI], 'add');
            Route::get(Festival::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Festival::UPDATE[URI] . '/{id}', 'newupdate');
            Route::post(Festival::DELETE[URI], 'delete')->name('delete');
            Route::get(Festival::EXPORT[URI], 'exportList')->name('export');
            Route::post(Festival::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    //fast festival
    Route::group(['prefix' => 'fastfestival', 'as' => 'fastfestival.', 'middleware' => ['module:Fast Festival']], function () {
        Route::controller(FastFestivalController::class)->group(function () {
            Route::get(FastFestival::LIST[URI], 'index')->name('list');
            Route::get(FastFestival::ADD[URI], 'getAddView')->name('add-new');
            Route::post(FastFestival::ADD[URI], 'add');
            Route::get(FastFestival::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(FastFestival::UPDATE[URI] . '/{id}', 'update');
            Route::post(FastFestival::DELETE[URI], 'delete')->name('delete');
            Route::get(FastFestival::EXPORT[URI], 'exportList')->name('export');
            Route::post(FastFestival::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // Calculator
    Route::group(['prefix' => 'calculator', 'as' => 'calculator.', 'middleware' => ['module:Calculator']], function () {
        Route::controller(CalculatorController::class)->group(function () {
            Route::get(Calculator::LIST[URI], 'index')->name('list');
            Route::get(Calculator::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Calculator::ADD[URI], 'add');
            Route::get(Calculator::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Calculator::UPDATE[URI] . '/{id}', 'update');
            Route::delete(Calculator::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(Calculator::EXPORT[URI], 'exportList')->name('export');
        });
    });

    // astrologers
    Route::group(['prefix' => 'astrologers', 'as' => 'astrologers.', 'middleware' => ['module:Astrologer & Pandit']], function () {
        Route::controller(AstrologerController::class)->group(function () {
            Route::group(['prefix' => 'block', 'as' => 'block.'], function () {
                Route::get(Astrologer::BLOCK_LIST[URI], 'block_list')->name('list');
            });
            Route::group(['prefix' => 'manage', 'as' => 'manage.'], function () {
                Route::get(Astrologer::MANAGE_LIST[URI], 'manage_list')->name('list');
                Route::get(Astrologer::MANAGE_ADD[URI], 'getManageAddView')->name('add-new');
                Route::post(Astrologer::MANAGE_ADD[URI], 'addManage');
                Route::get('add-details/{id}', 'getManageDetailView')->name('add-details');
                Route::post('update-detail/{id}', 'updateManageDetail')->name('update-detail');
                Route::get(Astrologer::MANAGE_UPDATE[URI] . '/{id}', 'getManageUpdateView')->name('update');
                Route::post(Astrologer::MANAGE_UPDATE[URI] . '/{id}', 'updateManage');
                Route::post(Astrologer::MANAGE_DELETE[URI], 'deleteManage')->name('delete');
                Route::get(Astrologer::MANAGE_PACKAGE[URI] . '/{id}', 'addPackage')->name('add-package');
                Route::post(Astrologer::MANAGE_PACKAGE_STORE[URI], 'storePackage')->name('store-package');
                Route::get(Astrologer::MANAGE_DETAIL[URI] . '/{id}', 'addDetail')->name('add-detail');
                Route::post(Astrologer::MANAGE_DETAIL_STORE[URI], 'storeDetail')->name('store-detail');
                Route::get(Astrologer::MANAGE_ADDITIONAL_DETAIL[URI] . '/{id}', 'addAdditionalDetail')->name('add-additional-detail');
                Route::post(Astrologer::MANAGE_ADDITIONAL_DETAIL_STORE[URI], 'storeAdditionalDetail')->name('store-additional-detail');
                Route::get(Astrologer::MANAGE_GALLERY[URI] . '/{id}', 'addGallery')->name('add-gallery');
                Route::post(Astrologer::MANAGE_GALLERY[URI] . '/{id}', 'storeGallery')->name('add-gallery');
                Route::delete(Astrologer::MANAGE_GALLERY_DELETE[URI] . '/{id}/{key}', 'deleteGallery')->name('delete-gallery');
                Route::get(Astrologer::MANAGE_COUNSELLING[URI] . '/{id}', 'addCounselling')->name('add-counselling');
                Route::post(Astrologer::MANAGE_COUNSELLING_STORE[URI], 'storeCounselling')->name('store-counselling');
                Route::get(Astrologer::MANAGE_DETAIL_OVERVIEW[URI] . '/{id}', 'detailManageOverview')->name('detail.overview');
                Route::get(Astrologer::MANAGE_DETAIL_ORDER[URI] . '/{id}', 'detailManageOrder')->name('detail.order');
                Route::get(Astrologer::MANAGE_DETAIL_SERVICE[URI] . '/{id}', 'detailManageService')->name('detail.service');
                Route::get(Astrologer::MANAGE_DETAIL_SETTING[URI] . '/{id}', 'detailManageSetting')->name('detail.setting');
                Route::get(Astrologer::MANAGE_DETAIL_TRANSACTION[URI] . '/{id}', 'detailManageTransaction')->name('detail.transaction');
                Route::get(Astrologer::MANAGE_DETAIL_TRANSACTION_HISTORY[URI] . '/{id}', 'detailManageTransactionHistory')->name('detail.transaction.history');
                Route::get(Astrologer::MANAGE_DETAIL_REVIEW[URI] . '/{id}', 'detailManageReview')->name('detail.review');
                Route::get('check/email/{email}', 'check_email')->name('check.email');
                Route::get('check/mobileno/{mobileno}', 'check_mobileno')->name('check.mobileno');
                Route::post('pandit/pooja', 'pandit_pooja')->name('pandit.pooja');
                Route::post('commission/update', 'commission_update')->name('commission.update');
                Route::get('user/review/list/{type}/{service_id}/{astro_id}', 'user_review_list')->name('user.review.list');
                Route::get('user/review/delete/{type}/{id}', 'user_review_delete')->name('user.review.delete');
                Route::post(Astrologer::MANAGE_STATUS[URI], 'statusManage')->name('status');
                Route::get(Astrologer::MANAGE_DETAIL_HISTORY[URI] . '/{id}', 'detailManageHistory')->name('detail.history');
                Route::get('order-data', 'order_data')->name('order-data');
                Route::get('astro-transection', 'pandit_transection')->name('astro-transection');
                Route::get('astro-talk', 'astrologer_talk')->name('astro-talk');
                Route::get('astrologers/{id}', 'astro_wallet_history')->name('astrologers');
                Route::get('guruji-transaction', 'guruji_transaction')->name('guruji.transaction');
                Route::get('create-service/{id}', 'show_service')->name('update.services');
                Route::post('save-service/{astrologerId}','saveService')->name('save.service');
            });
            Route::group(['prefix' => 'pending', 'as' => 'pending.'], function () {
                Route::get(Astrologer::PENDING_LIST[URI], 'pending_list')->name('list');
            });
            Route::group(['prefix' => 'review', 'as' => 'review.'], function () {
                Route::get(Astrologer::REVIEW_LIST[URI], 'review_list')->name('list');
            });
            Route::group(['prefix' => 'gift', 'as' => 'gift.'], function () {
                Route::get(Astrologer::GIFT_LIST[URI], 'gift_list')->name('list');
                Route::get(Astrologer::GIFT_ADD[URI], 'getGiftAddView')->name('add-new');
                Route::post(Astrologer::GIFT_ADD[URI], 'addGift');
                Route::get(Astrologer::GIFT_UPDATE[URI] . '/{id}', 'getGiftUpdateView')->name('update');
                Route::post(Astrologer::GIFT_UPDATE[URI] . '/{id}', 'giftUpdate');
                Route::post(Astrologer::GIFT_STATUS[URI], 'statusGift')->name('status');
            });
            Route::group(['prefix' => 'skill', 'as' => 'skill.'], function () {
                Route::get(Astrologer::SKILL_LIST[URI], 'skill_list')->name('list');
                Route::post(Astrologer::SKILL_ADD[URI], 'add_skills')->name('add');
                Route::get(Astrologer::SKILL_UPDATE[URI] . '/{id}', 'getSkillUpdateView')->name('update');
                Route::post(Astrologer::SKILL_UPDATE[URI] . '/{id}', 'SkillUpdate');
                Route::post(Astrologer::SKILL_STATUS[URI], 'statusSkill')->name('status');
            });
            Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                Route::get(Astrologer::CATEGORY_LIST[URI], 'category_list')->name('list');
                Route::get(Astrologer::CATEGORY_ADD[URI], 'getCategoryAddView')->name('add-new');
                Route::post(Astrologer::CATEGORY_ADD[URI], 'addCategory');
                Route::get(Astrologer::CATEGORY_UPDATE[URI] . '/{id}', 'getCategoryUpdateView')->name('update');
                Route::post(Astrologer::CATEGORY_UPDATE[URI] . '/{id}', 'categoryUpdate');
                Route::post(Astrologer::CATEGORY_STATUS[URI], 'statusCategory')->name('status');
            });
            Route::group(['prefix' => 'comission', 'as' => 'comission.'], function () {
                Route::get(Astrologer::COMISSION_LIST[URI], 'comission_list')->name('list');
            });

            Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function () {
                Route::get('list/{status}', 'withdraw_list')->name('list');
                Route::post('approve', 'withdraw_approve')->name('approve');
                Route::post('complete', 'withdraw_complete')->name('complete');
            });
        });
    });

    // service tax setting
    Route::group(['prefix' => 'service/tax', 'as' => 'service.tax.', 'middleware' => ['module:Service Tax']], function () {
        Route::controller(ServiceTaxController::class)->group(function () {
            Route::get('list', 'service_tax_list')->name('list');
            Route::post('update', 'service_tax_update')->name('update');
        });
    });

    Route::group(['prefix' => 'auth/2fa', 'as' => 'auth.2fa.'], function () {
        Route::controller(ProfileController::class)->group(function () {
            Route::post('show/qr', 'show_qr')->name('show.qr');
            Route::post('active', 'active')->name('active');
            Route::post('login/check', 'login_check')->name('login.check')->withoutMiddleware('admin');
            Route::post('login/submit', 'login_submit')->name('login.submit')->withoutMiddleware('admin');
        });
    });

    // Remote Access
    Route::group(['prefix' => 'remote/access', 'as' => 'remote.access.', 'middleware' => ['module:Remote Access']], function () {
        Route::get('list', [ProfileController::class, 'remote_access_list'])->name('list');
        Route::post('add', [ProfileController::class, 'remote_access_add'])->name('add');
        Route::post('update', [ProfileController::class, 'remote_access_update'])->name('update');
        Route::post('delete', [ProfileController::class, 'remote_access_delete'])->name('delete');
    });

    // logs
    // Route::get('logs', [ProfileController::class,'logs_list'])->name('logs');
    Route::group(['middleware' => ['module:Logs']], function () {
        Route::get('auth/logs', [ProfileController::class, 'auth_logs_list'])->name('auth.logs');
        Route::get('logs', [ProfileController::class, 'logs_list'])->name('logs');
        Route::get('pwd-change-logs', [ProfileController::class, 'pwd_change_logs_list'])->name('pwd-change-logs');
    });

    //pandit
    // Route::group(['prefix' => 'pandit', 'as' => 'pandit.','middleware'=>['module:pandit_management']], function () {
    //     Route::controller(PanditController::class)->group(function (){
    //         Route::get('pending/list', 'pending_list')->name('pending.list');
    //         Route::get('list', 'pandit_list')->name('list');
    //         Route::get('add', 'pandit_create')->name('add');
    //         Route::post('add', 'pandit_store');
    //         Route::post('verify', 'pandit_verify')->name('verify');
    //         Route::post('pooja', 'pandit_pooja')->name('pooja');

    //         Route::group(['prefix' => 'experties', 'as' => 'experties.'], function () {
    //             Route::get('list', 'experties_list')->name('list');
    //             Route::post('add', 'experties_add')->name('add');
    //             Route::post('update', 'experties_update')->name('update');
    //             // Route::get('delete/{id}', 'experties_delete')->name('delete');
    //             Route::post('status', 'experties_status')->name('status');
    //         });
    //     });
    // });

    // Brand
    Route::group(['prefix' => 'brand', 'as' => 'brand.', 'middleware' => ['module:Brand']], function () {
        Route::controller(BrandController::class)->group(function () {
            Route::get(Brand::LIST[URI], 'index')->name('list');
            Route::get(Brand::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Brand::ADD[URI], 'add');
            Route::get(Brand::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Brand::UPDATE[URI] . '/{id}', 'update');
            Route::post(Brand::DELETE[URI], 'delete')->name('delete');
            Route::get(Brand::EXPORT[URI], 'exportList')->name('export');
            Route::post(Brand::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    // Category
    Route::group(['prefix' => 'category', 'as' => 'category.', 'middleware' => ['module:Category Setup']], function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::get(Category::LIST[URI], 'index')->name('view');
            Route::post(Category::ADD[URI], 'add')->name('store');
            Route::get(Category::UPDATE[URI], 'getUpdateView')->name('update');
            Route::post(Category::UPDATE[URI], 'update');
            Route::post(Category::DELETE[URI], 'delete')->name('delete');
            Route::post(Category::STATUS[URI], 'updateStatus')->name('status');
            Route::get(Category::EXPORT[URI], 'getExportList')->name('export');
        });
    });

    // Sub Category
    Route::group(['prefix' => 'sub-category', 'as' => 'sub-category.', 'middleware' => ['module:Category Setup']], function () {
        Route::controller(SubCategoryController::class)->group(function () {
            Route::get(SubCategory::LIST[URI], 'index')->name('view');
            Route::post(SubCategory::ADD[URI], 'add')->name('store');
            Route::get(SubCategory::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(SubCategory::UPDATE[URI] . '/{id}', 'update');
            Route::post(SubCategory::DELETE[URI], 'delete')->name('delete');
            Route::get(SubCategory::EXPORT[URI], 'getExportList')->name('export');
        });
    });

    // Sub Sub Category
    Route::group(['prefix' => 'sub-sub-category', 'as' => 'sub-sub-category.', 'middleware' => ['module:Category Setup']], function () {
        Route::controller(SubSubCategoryController::class)->group(function () {
            Route::get(SubSubCategory::LIST[URI], 'index')->name('view');
            Route::post(SubSubCategory::ADD[URI], 'add')->name('store');
            Route::get(SubSubCategory::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(SubSubCategory::UPDATE[URI] . '/{id}', 'update');
            Route::post(SubSubCategory::DELETE[URI], 'delete')->name('delete');
            Route::post(SubSubCategory::GET_SUB_CATEGORY[URI], 'getSubCategory')->name('getSubCategory');
            Route::get(SubSubCategory::EXPORT[URI], 'getExportList')->name('export');
        });
    });
    // Services Modules New
    Route::group(['prefix' => 'service', 'as' => 'service.', 'middleware' => ['module:Pooja Managment']], function () {
        Route::controller(ServiceController::class)->group(function () {
            Route::get(ServiceDetails::LIST[URI], 'index')->name('list');
            Route::get(ServiceDetails::ADD[URI], 'getAddView')->name('add-new');
            Route::post(ServiceDetails::ADD[URI], 'add')->name('add-new');
            Route::get(ServiceDetails::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(ServiceDetails::UPDATE[URI] . '/{id}', 'update');
            Route::get(ServiceDetails::VIEW[URI] . '/{addedBy}/{id}', 'getViewService')->name('views');
            Route::delete(ServiceDetails::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(ServiceDetails::DELETE_IMAGE[URI] . '{id}/{name}', 'deleteImage')->name('delete-image');
            Route::post(ServiceDetails::STATUS[URI], 'updateStatus')->name('status-update');
            Route::get(ServiceDetails::GET_CATEGORIES[URI], 'getCategories')->name('get-categories');
            Route::get(ServiceDetails::SCHEDULE[URI] . '/{id}',  'pooja_schedule')->name('schedule');
            Route::post(ServiceDetails::EVENTUPDATE[URI] . '/{id}', 'event_Update')->name('eventUpdate');
            Route::get(ServiceDetails::EVENTUPDATETIME[URI] . '/{id}', 'getEvents')->name('get');
            Route::post(ServiceDetails::DENY[URI], 'deny')->name('deny');
            Route::post(ServiceDetails::APPROVE_STATUS[URI], 'approveStatus')->name('approve-status');
            // Pooja Shadule
            // In routes/web.php
            Route::post('/pooja_prashad', 'pooja_prashad')->name('pooja_prashad');

            Route::get('/get-packages-dropdown', [ServiceController::class, 'getPackagesDropdown']);
            Route::get('/get-packages-dropdown-vip', [ServiceController::class, 'getPackagesDropdownVIP']);
            Route::post('/schedule-delete', [ServiceController::class, 'ScheduleDelete']);

            // Coubselling Routes
            Route::get(ServiceDetails::COUNSELLING_LIST[URI], 'counselling_index')->name('counselling.list');
            Route::get(ServiceDetails::COUNSELLING_ADD[URI], 'counselling_getAddView')->name('counselling.add-new');
            Route::post(ServiceDetails::COUNSELLING_ADD[URI], 'counselling_add')->name('counselling.add-new');
            Route::get(ServiceDetails::COUNSELLING_UPDATE[URI] . '/{id}', 'counselling_getUpdateView')->name('counselling.update');
            Route::post(ServiceDetails::COUNSELLING_UPDATE[URI] . '/{id}', 'counselling_update');
            Route::delete(ServiceDetails::COUNSELLING_DELETE[URI] . '/{id}', 'counselling_delete')->name('counselling.delete');
            Route::get(ServiceDetails::COUNSELLING_DELETE_IMAGE[URI], 'counsellingDeleteImage')->name('delete-image');
            Route::post(ServiceDetails::COUNSELLING_STATUS[URI], 'counselling_updateStatus')->name('counselling.status-update');

            // VIP POOJA
            Route::get(ServiceDetails::VIP_LIST[URI], 'vip_index')->name('vip.list');
            Route::get(ServiceDetails::VIP_ADD[URI], 'vip_getAddView')->name('vip.add-new');
            Route::post(ServiceDetails::VIP_ADD[URI], 'vip_add')->name('vip.add-new');
            Route::get(ServiceDetails::VIP_UPDATE[URI] . '/{id}', 'vip_getUpdateView')->name('vip.update');
            Route::post(ServiceDetails::VIP_UPDATE[URI] . '/{id}', 'vip_update');
            Route::delete(ServiceDetails::VIP_DELETE[URI] . '/{id}', 'vip_delete')->name('vip.delete');
            Route::get(ServiceDetails::VIP_DELETE_IMAGE[URI], 'vipDeleteImage')->name('vip.delete-image');
            Route::post(ServiceDetails::VIP_STATUS[URI], 'vip_updateStatus')->name('vip.status-update');
            Route::get(ServiceDetails::VIP_VIEW[URI] . '/{addedBy}/{id}', 'vip_getView')->name('vip.view');
            Route::post('/vip_prashad', 'vip_prashad')->name('vip.vip_prashad');
            // OFFLINE POOJA
            Route::get(ServiceDetails::ADD_OFFLINE_POOJA[URI], 'offline_pooja_getAddView')->name('offline.pooja.add-new');
            Route::get(ServiceDetails::OFFLINE_POOJA_LIST[URI], 'offline_pooja_index')->name('offline.pooja.list');
            Route::post(ServiceDetails::ADD_OFFLINE_POOJA[URI], 'offline_pooja_add')->name('offline.pooja.add-new');
            Route::get(ServiceDetails::OFFLINE_POOJA_UPDATE[URI] . '/{id}', 'offline_pooja_getUpdateView')->name('offline.pooja.update');
            Route::post(ServiceDetails::OFFLINE_POOJA_UPDATE[URI] . '/{id}', 'offline_pooja_update');
            Route::delete(ServiceDetails::OFFLINE_POOJA_DELETE[URI] . '/{id}', 'offline_pooja_delete')->name('offline.pooja.delete');
            Route::get(ServiceDetails::OFFLINE_POOJA_DELETE_IMAGE[URI], 'offline_pooja_DeleteImage')->name('offline.pooja.delete-image');
            Route::post(ServiceDetails::OFFLINE_POOJA_STATUS[URI], 'offline_pooja_updateStatus')->name('offline.pooja.status-update');
            Route::get(ServiceDetails::OFFLINE_POOJA_REFUND_POLICY_LIST[URI], 'offline_pooja_refund_policy_index')->name('offline.pooja.refund.policy.list');
            Route::post(ServiceDetails::ADD_OFFLINE_POOJA_REFUND_POLICY[URI], 'offline_pooja_refund_policy_add')->name('offline.pooja.refund.policy.add-new');
            Route::get(ServiceDetails::OFFLINE_POOJA_REFUND_POLICY_UPDATE[URI] . '/{id}', 'offline_pooja_refund_policy_getUpdateView')->name('offline.pooja.refund.policy.update');
            Route::post(ServiceDetails::OFFLINE_POOJA_REFUND_POLICY_UPDATE[URI] . '/{id}', 'offline_pooja_refund_policy_update');
            Route::delete(ServiceDetails::OFFLINE_POOJA_REFUND_POLICY_DELETE[URI] . '/{id}', 'offline_pooja_refund_policy_delete')->name('offline.pooja.refund.policy.delete');
            Route::post(ServiceDetails::OFFLINE_POOJA_REFUND_POLICY_STATUS[URI], 'offline_pooja_refund_policy_updateStatus')->name('offline.pooja.refund.policy.status-update');
            Route::get(ServiceDetails::OFFLINE_POOJA_SCHEDULE_LIST[URI], 'offline_pooja_schedule_index')->name('offline.pooja.schedule.list');
            Route::post(ServiceDetails::ADD_OFFLINE_POOJA_SCHEDULE[URI], 'offline_pooja_schedule_add')->name('offline.pooja.schedule.add-new');
            Route::get(ServiceDetails::OFFLINE_POOJA_SCHEDULE_UPDATE[URI] . '/{id}', 'offline_pooja_schedule_getUpdateView')->name('offline.pooja.schedule.update');
            Route::post(ServiceDetails::OFFLINE_POOJA_SCHEDULE_UPDATE[URI] . '/{id}', 'offline_pooja_schedule_update');
            Route::delete(ServiceDetails::OFFLINE_POOJA_SCHEDULE_DELETE[URI] . '/{id}', 'offline_pooja_schedule_delete')->name('offline.pooja.schedule.delete');
            Route::post(ServiceDetails::OFFLINE_POOJA_SCHEDULE_STATUS[URI], 'offline_pooja_schedule_updateStatus')->name('offline.pooja.schedule.status-update');
            // Route::get(ServiceDetails::VIP_VIEW[URI].'/{addedBy}/{id}', 'vip_getView')->name('vip.view');
            // Route::post('/vip_prashad','vip_prashad')->name('vip.vip_prashad');
            // category
            Route::get(ServiceDetails::OFFLINE_POOJA_CATEGORY_LIST[URI], 'offline_pooja_category_index')->name('offline.pooja.category.list');
            Route::post(ServiceDetails::ADD_OFFLINE_POOJA_CATEGORY[URI], 'offline_pooja_category_add')->name('offline.pooja.category.add-new');
            Route::get(ServiceDetails::OFFLINE_POOJA_CATEGORY_UPDATE[URI] . '/{id}', 'offline_pooja_category_getUpdateView')->name('offline.pooja.category.update');
            Route::post(ServiceDetails::OFFLINE_POOJA_CATEGORY_UPDATE[URI] . '/{id}', 'offline_pooja_category_update');
            Route::post(ServiceDetails::OFFLINE_POOJA_CATEGORY_STATUS[URI], 'offline_pooja_category_updateStatus')->name('offline.pooja.category.status-update');
            Route::get('/offline-pooja-get-packages-dropdown', [ServiceController::class, 'getOfflinePoojaPackagesDropdown']);

            // city
            Route::get(ServiceDetails::OFFLINE_POOJA_CITY_LIST[URI], 'offline_pooja_city_index')->name('offline.pooja.city.list');
            Route::post(ServiceDetails::ADD_OFFLINE_POOJA_CITY[URI], 'offline_pooja_city_add')->name('offline.pooja.city.add-new');
            Route::get(ServiceDetails::OFFLINE_POOJA_CITY_UPDATE[URI] . '/{id}', 'offline_pooja_city_getUpdateView')->name('offline.pooja.city.update');
            Route::post(ServiceDetails::OFFLINE_POOJA_CITY_UPDATE[URI] . '/{id}', 'offline_pooja_city_update');
            Route::post(ServiceDetails::OFFLINE_POOJA_CITY_STATUS[URI], 'offline_pooja_city_updateStatus')->name('offline.pooja.city.status-update');
        });
    });
    // CHADHAVA CONTROLLER
    Route::group(['prefix' => 'chadhava', 'as' => 'chadhava.', 'middleware' => ['module:Chadhava Managment']], function () {
        Route::controller(ChadhavaController::class)->group(function () {
            Route::get(ChadhavaPath::LIST[URI], 'index')->name('list');
            Route::get(ChadhavaPath::ADD[URI], 'getAddView')->name('add-new');
            Route::post(ChadhavaPath::ADD[URI], 'add')->name('add-new');
            Route::get(ChadhavaPath::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(ChadhavaPath::UPDATE[URI] . '/{id}', 'update');
            Route::get(ChadhavaPath::VIEW[URI] . '/{addedBy}/{id}', 'getView')->name('view');
            Route::delete(ChadhavaPath::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(ChadhavaPath::DELETE_IMAGE[URI] . '{id}/{name}', 'deleteImage')->name('delete-image');
            Route::post(ChadhavaPath::STATUS[URI], 'updateStatus')->name('status-update');
            Route::post(ChadhavaPath::APPROVE_STATUS[URI], 'approveStatus')->name('approve-status');
            Route::get('/get-packages-dropdown', [ServiceController::class, 'getPackagesDropdown']);
            Route::get(ChadhavaPath::VIEW[URI] . '/{addedBy}/{id}', 'getView')->name('view');
        });
    });
    //FAQ's Modules 
    Route::group(['prefix' => 'faq', 'as' => 'faq.', 'middleware' => ['module:FAQ']], function () {
        Route::controller(FAQController::class)->group(function () {
            //category
            Route::get(FAQPath::CATEGORY[URI], 'addCategory')->name('category');
            Route::post(FAQPath::CATEGORY[URI], 'CategoryStore')->name('category-store');
            Route::post(FAQPath::CATEGORYSTATUS[URI], 'CategoryStatusUpdate')->name('category-status-update');
            Route::get(FAQPath::CATEGORYUPDATE[URI] . '/{id}', 'CategoryUpdate')->name('category-update');
            Route::post(FAQPath::CATEGORYUPDATE[URI], 'CategoryEdit')->name('category-edit');
            Route::post(FAQPath::CATEGORYDELETE[URI], 'CategoryDelete')->name('category-delete');
            Route::get(FAQPath::LIST[URI], 'index')->name('list');
            Route::get(FAQPath::ADD[URI], 'getAddView')->name('add-new');
            Route::post(FAQPath::ADD[URI], 'add')->name('add-new');
            Route::get(FAQPath::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(FAQPath::UPDATE[URI] . '/{id}', 'update');
            Route::post(FAQPath::STATUS[URI], 'updateStatus')->name('status-update');
            Route::delete(FAQPath::DELETE[URI] . '/{id}', 'delete')->name('delete');
        });
    });

    //Package's Modules 
    Route::group(['prefix' => 'package', 'as' => 'package.', 'middleware' => ['module:Pooja Managment']], function () {
        Route::controller(PackageController::class)->group(function () {
            Route::get(Package::LIST[URI], 'index')->name('list');
            Route::post(Package::ADD[URI], 'add')->name('store');
            Route::get(Package::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Package::UPDATE[URI] . '/{id}', 'update');
            Route::delete(Package::DELETE[URI] . '/{id}', 'delete')->name('delete');
        });
    });

Route::group(['prefix' => 'state', 'as' => 'state.'], function () {
        Route::controller(CitiesController::class)->group(function () {
            Route::get("list",'StateShow')->name('list');
            Route::get("state-list-filter",'StateListFilter')->name('state-list-filter');
            Route::post("upload-image-logo",'StateImageUpdate')->name('upload-image-logo');
        });
    });
    Route::group(['prefix' => 'cities', 'as' => 'cities.'], function () {
        Route::controller(CitiesController::class)->group(function () {
            Route::get(cities::LIST[URI], 'index')->name('view');
            Route::post(cities::EDIT[SAVE], 'store')->name('store');
            Route::get(cities::INDEX[URI], 'list')->name('list');
            Route::get(cities::EDIT[URI] . '/{id}', 'update')->name('update');
            Route::post(cities::EDIT[URI] . '/{id}', 'edit')->name('edit');


            Route::get(cities::GALLERY[URI] . '/{id}', 'gallery')->name('gallery');
            Route::post(cities::GALLERY[URI] . '/{id}', 'add_gallery')->name('gallery_add');
            Route::get(cities::GALLERY[URL] . '/{id}/{name}', 'delete')->name('delete-image');
            Route::get('slider-image-remove' . '/{id}/{name}', 'SliderImageRemove')->name('delete-image-slider');

            Route::get(cities::REVIEW[URI], 'review_list')->name('review');
            Route::delete(cities::REVIEW[URL] . '/{id}', 'review_delete')->name('review-delete');
            Route::post(cities::REVIEW[SAVE], 'review_status')->name('review-status-update');
        });
    });

    Route::group(['prefix' => 'citie_visit', 'as' => 'citie_visit.'], function () {
        Route::controller(CitiesVisits::class)->group(function () {
            Route::get(AdminCitiesVisits::LIST[URI] . "/{id}", 'list')->name('list');
            Route::post(AdminCitiesVisits::STORE[URI], 'store')->name('store');
            Route::get(AdminCitiesVisits::UPDATE[URI] . "/{id}", 'update')->name('update');
            Route::post(AdminCitiesVisits::UPDATE[EDIT], 'edit_function')->name('edit_cities_visit');
            Route::delete(AdminCitiesVisits::DELETE[URI] . "/{id}", 'delete_citie_visit')->name('delete');
        });
    });

    Route::group(['prefix' => 'event-managment/category', 'as' => 'event-managment.category.'], function () {
        Route::controller(EventCategoryController::class)->group(function () {
            Route::get(EventcategoryPath::ADD[URI], 'index')->name('add');
            Route::post(EventcategoryPath::ADD[URI], 'store')->name('store');
            Route::get(EventcategoryPath::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::post(EventcategoryPath::UPDATE[URI] . '/{id}', 'edit')->name('edit');
            Route::post(EventcategoryPath::STATUS[URI], 'changeStatus')->name('status-update');
            Route::post(EventcategoryPath::DELETE[URI], 'delete')->name('delete');
        });
    });

    Route::group(['prefix' => 'event-managment/event_package', 'as' => 'event-managment.event_package.'], function () {
        Route::controller(EventPackageController::class)->group(function () {
            Route::get(EventpackagePath::ADD[URI], 'index')->name('add');
            Route::post(EventpackagePath::ADD[URI], 'store')->name('store');
            Route::get(EventpackagePath::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::post(EventpackagePath::UPDATE[URI] . '/{id}', 'edit')->name('edit');
            Route::post(EventpackagePath::STATUS[URI], 'changeStatus')->name('status-update');
            Route::post(EventpackagePath::DELETE[URI], 'delete')->name('delete');
        });
    });

    Route::group(['prefix' => 'event-managment/organizers', 'as' => 'event-managment.organizers.'], function () {
        Route::controller(EventOrganizerController::class)->group(function () {
            Route::get(EventOrganizerPath::ADD[URI], 'index')->name('add');
            Route::post(EventOrganizerPath::ADD[URI], 'store')->name('store');
            Route::get(EventOrganizerPath::LIST[URI], 'list')->name('list');
            Route::post(EventOrganizerPath::STATUS[URI], 'changeStatus')->name('status-update');
            Route::post(EventOrganizerPath::DELETE[URI], 'delete')->name('delete');
            Route::get(EventOrganizerPath::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::get(EventOrganizerPath::VIEW[URI] . '/{id}', 'view_information')->name('information');
            Route::post(EventOrganizerPath::UPDATE[URI] . '/{id}', 'edit')->name('edit');
            Route::post(EventOrganizerPath::STATUS[URL], 'verification_status')->name('verification-status');
            Route::post('doc-verified-resend', 'DocVerifiedResend')->name('doc-verified-resend');
        });
    });
    Route::group(['prefix' => 'new-order-message', 'as' => 'new-order-message.'], function () {
        Route::controller(ProfileController::class)->group(function () {
            Route::post('message', 'newOrderMessage')->name('message');
        });
    });
    Route::group(['prefix' => 'event-managment/event', 'as' => 'event-managment.event.'], function () {
        Route::controller(EventsController::class)->group(function () {
            Route::get(EventsPath::ARTIST[URI], 'add_artist')->name('artist');
            Route::post(EventsPath::ARTIST[URI], 'artist_store')->name('artist_store');
            Route::post(EventsPath::ARTIST_UPDATE[URL], 'artist_statuschange')->name('artist_status-update');
            Route::post(EventsPath::ARTIST_DELETE[URI], 'artist_delete')->name('artist_delete');
            Route::get(EventsPath::ARTIST_UPDATE[URI] . '/{id}', 'artist_update')->name('artist_update');
            Route::post(EventsPath::ARTIST_UPDATE[URI] . '/{id}', 'artist_edit')->name('artist_edit');
            Route::get(EventsPath::ADD[URI], 'index')->name('add');
            Route::post(EventsPath::ADD[URI], 'store')->name('store');
            Route::get(EventsPath::LIST[URI], 'list')->name('list');
            Route::get(EventsPath::LIST1[URI], 'list_all')->name('pending');
            Route::get(EventsPath::LIST2[URI], 'list_all')->name('upcomming');
            Route::get(EventsPath::LIST1[URL], 'list_all')->name('booking');
            Route::get(EventsPath::LIST3[URI], 'list_all')->name('completed');
            Route::get(EventsPath::LIST2[URL], 'list_all')->name('canceled');
            Route::get("user-refund" . '/{id}', 'UserRefund')->name('refund-amount');

            Route::post(EventsPath::STATUS[URI], 'changeStatus')->name('status-update');
            Route::get(EventsPath::STATUS[URL] . '/{id}/{status}', 'event_approvel')->name('event_approvel');
            Route::post(EventsPath::DELETE[URI], 'delete')->name('delete');
            Route::get(EventsPath::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::post(EventsPath::UPDATE[URI] . '/{id}', 'edit')->name('edit');
            Route::get(EventsPath::VIEW[URI] . '/{id}', 'information')->name('information');

            // 
            Route::get(EventsPath::OVERALL[URI] . '/{id}', 'event_details')->name('event-detail-overview');
            Route::get(EventsPath::OVERALL[URL], 'event_details')->name('event-overview');

            //order 
            Route::post(EventsPath::ORDER[URL], 'event_order_view')->name('event-order-view');

            //commission

            Route::post(EventsPath::COMMISSION[URI] . '/{id}', 'commission_update')->name('commission_update');

            //event-amount-calculation
            Route::get(EventsPath::COMMISSION[URL] . '/{id}', 'EventAmountCalculation')->name('event_amount_calculation');
            Route::post(EventsPath::COMM_STATUS[URI], 'CommentStatusUpdate')->name('comment-status-update');

            //

            Route::get(EventsPath::REJECT[URI], 'SendRequestReject');
            Route::get(EventsPath::PAYREQ[URI] . '/{id}/{status}', 'RequestApproveAmount')->name('requestapprove');
        });
    });

    Route::group(['prefix' => 'event-managment/event-booking', 'as' => 'event-managment.event-booking.'], function () {
        Route::controller(EventsController::class)->group(function () {
            Route::get(EventsPath::BookingLIST[URI], 'BookingList')->name('list');
            Route::get('order-details/{id}', 'EventOrderDetails')->name('user-booking-details');
            Route::get('event-invoice/{id}', 'EventBookingInvoice')->name('booking-invoice');
        });
    });

    Route::group(['prefix' => 'event-managment/leads', 'as' => 'event-managment.leads.'], function () {
        Route::controller(EventsController::class)->group(function () {
            Route::get(EventsPath::LEADS[URI], 'EventLeads')->name('list');
            Route::get(EventsPath::LEADS[URL] . '/{id}', 'EventLeadsDelete')->name('lead-delete');
            Route::get(EventsPath::LEADSFOLLOW[URI] . '/{id}', 'EventLeadsFollow')->name('event-follow-list');
            Route::post(EventsPath::LEADSFOLLOW[URL], 'EventLeadsFollowUp')->name('event-follow-up');
        });
    });

    Route::group(['prefix' => 'event-managment/event-withdrawal', 'as' => 'event-managment.event-withdrawal.'], function () {
        Route::controller(EventsController::class)->group(function () {
            Route::get('/', 'WithdrawalList')->name('list');
            Route::get('view/{id}', 'WithdrawalReqView')->name('withdraw-request-view');
            Route::get('request-reject/{id}', 'WithdrawalReqReject')->name('rejects');
            Route::get('create-contact/{id}/{type}', 'RazorpaycreateContact')->name('payment-req-approval-admin');
        });
    });

    Route::group(['prefix' => 'birth_journal', 'as' => 'birth_journal.'], function () {
        Route::controller(BirthJournalController::class)->group(function () {
            Route::get(BirthJournalPath::ADD[URI], 'Add')->name('add_kundali');
            Route::post(BirthJournalPath::ADD[URI], 'Store')->name('store');
            Route::post(BirthJournalPath::STATUS[URI], 'StatusUpdate')->name('status-update');
            Route::post(BirthJournalPath::DELETE[URI], 'deleted')->name('remove');
            Route::get(BirthJournalPath::LIST[URI], 'list')->name('kundali_list');
            Route::get(BirthJournalPath::UPDATE[URI] . '/{id}', 'Update')->name('update');
            Route::post(BirthJournalPath::UPDATE[URI] . '/{id}', 'UpdateSave')->name('updatesave');

            Route::get(BirthJournalPath::ALLORDER[URI], 'OrderList')->name('orders.all_list');
            Route::get(BirthJournalPath::PENDING[URI], 'OrderPending')->name('orders.pending');
            Route::get(BirthJournalPath::COMPLETED[URI], 'OrderCompleted')->name('orders.completed');
            Route::get(BirthJournalPath::PAIDKUNDLI[URI], 'PaidKundli')->name('paid_kundli');
            Route::get(BirthJournalPath::VIEWKUNDLI[URI] . '/{id}', 'KundliMilandetails')->name('view-kundali-milan');
            Route::get('re-upload-birth-pdf/{id}', 'ReUploadBithPDF')->name('reupload-birth-pdf');

            Route::get(BirthJournalPath::VIEWKUNDLI[URL] . '/{id}', 'KundliMilanVerify')->name('verify-kundali-milan');

            Route::get('orders/reject-kundali-milan/{id}', 'KundliMilanReject')->name('reject-kundali-milan');

            Route::post(BirthJournalPath::PAIDKUNDLI[URL] . '/{id}', 'KundliMilanUploadPDF')->name('kundali-milan-uploadPDF');


            //kundli_leads

            Route::get(BirthJournalPath::LEADS[URI], 'KundaliLeads')->name('kundli_leads');
            Route::get(BirthJournalPath::LEADSDELETE[URI] . '/{id}', 'KundaliLeadsDelete')->name('lead-delete');
            Route::post('lead-follow-up', 'followup_store')->name('lead-follow-up');
            Route::get(BirthJournalPath::LEADSFOLLOW[URI] . '/{id}', 'getFollowList')->name('get-follows-list');
            Route::post('assign-astrologer' . '/{id}', "AssignAstrologer")->name('order.assign-astrologer');
            Route::get('generate-invoice' . '/{id}', "GenerateInvoice")->name('order.generate-invoice');
        });
    });

    Route::group(['prefix' => 'donate_management', 'as' => 'donate_management.'], function () {
        //category
        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::controller(DonateCategoryController::class)->group(function () {
                Route::get(DonateCategoryPath::ADDCATEGORY[URI], 'AddCategory')->name('add');
                Route::post(DonateCategoryPath::ADDCATEGORY[URI], 'StoreCategory')->name('store');
                Route::post(DonateCategoryPath::ADDCATESTATUS[URI], 'CategoryStatus')->name('status-update');
                Route::post(DonateCategoryPath::ADDCATEDELETE[URI], 'CategoryDelete')->name('delete');
                Route::get(DonateCategoryPath::ADDCATEUPDATE[URI] . '/{id}', 'CategoryUpdate')->name('update');
                Route::post(DonateCategoryPath::ADDCATEUPDATE[URI], 'CategoryUpdateSave')->name('updatestore');
            });
        });
        Route::group(['prefix' => 'donated', 'as' => 'donated.'], function () {
            Route::controller(DonateTrustController::class)->group(function () {
                Route::get(DonateTrustPath::DONATED[URI], 'DonatedList')->name('list');
                Route::get(DonateTrustPath::DONATEDVIEW[URI] . "/{id}", 'DonatedViewInfo')->name('view');
            });
        });
        //purpose
        Route::group(['prefix' => 'purpose', 'as' => 'purpose.'], function () {
            Route::controller(DonatePurposeController::class)->group(function () {
                Route::get(DonateCategoryPath::ADDPURPOSE[URI], 'AddPurpose')->name('add');
                Route::post(DonateCategoryPath::ADDPURPOSE[URI], 'StorePurpose')->name('store');
                Route::post(DonateCategoryPath::ADDPURSTATUS[URI], 'PurposeStatus')->name('status-update');
                Route::post(DonateCategoryPath::ADDPURDELETE[URI], 'PurposeDelete')->name('delete');
                Route::get(DonateCategoryPath::ADDPURUPDATE[URI] . '/{id}', 'PurposeUpdate')->name('update');
                Route::post(DonateCategoryPath::ADDPURUPDATE[URI], 'PurposeUpdateSave')->name('updatestore');
            });
        });
        //trust
        Route::group(['prefix' => 'trust', 'as' => 'trust.'], function () {
            Route::controller(DonateTrustController::class)->group(function () {
                Route::get(DonateTrustPath::ADDTRUST[URI], 'AddTrust')->name('add');
                Route::post(DonateTrustPath::ADDTRUST[URI], 'StoreTrust')->name('store');
                Route::get(DonateTrustPath::ADDTRUSTLIST[URI], 'TrustList')->name('list');
                Route::post(DonateTrustPath::ADDTRUSTSTATUS[URI], 'TrustStatus')->name('status-update');
                Route::post(DonateTrustPath::ADDTRUSTDELETE[URI], 'TrustDelete')->name('delete');
                Route::get(DonateTrustPath::ADDTRUSTUPDATE[URI] . '/{id}', 'TrustUpdate')->name('update');
                Route::post(DonateTrustPath::ADDTRUSTUPDATE[URI], 'TrustUpdateSave')->name('updatestore');
                Route::get(DonateTrustPath::REMOVETGALLERY[URI], 'GalleryImageDelete')->name('gallery-image-remove');
                Route::get(DonateTrustPath::TRUSTDETAIL[URI] . '/{id}', 'TrustDetails')->name('trust-detail');
                Route::post(DonateTrustPath::TRUSTCOMMISSION[URI] . '/{id}', 'TrustAdminCommission')->name('commission_update');
                Route::match(['get', 'post'], DonateTrustPath::TRUSTVERIFY[URI] . '/{id}/{status}', 'TrustVerifyDocUpload')->name('trust_verify_approvel');
                Route::get(DonateTrustPath::TRUSTAPPROVED[URI], 'TrustApproved')->name('approved');
                Route::get(DonateTrustPath::TRUSTCANCELED[URI], 'TrustCanceled')->name('canceled');
                Route::get(DonateTrustPath::TRUSTPENDING[URI], 'TrustPending')->name('pending');

                Route::get(DonateTrustPath::TRUSTREQAPPROV[URI] . '/{id}/{status}', 'TrustReqApproval')->name('requestapprove');
                Route::post("doc-verified-resend", 'DocVerifiedResend')->name('doc_verified_resend');
                Route::get("approve-profile-hold/{id}/{type}", 'ApproveProfileHold')->name('approve-profile-hold');
            });
        });

        Route::group(['prefix' => 'trustees-withdrawal', 'as' => 'trustees-withdrawal.'], function () {
            Route::controller(DonateTrustController::class)->group(function () {
                Route::get('/', 'WithdrawalList')->name('index');
                Route::get('view/{id}', 'WithdrawalReqView')->name('withdraw-request-view');
                Route::get('create-contact/{id}/{type}', 'RazorpaycreateContact')->name('payment-req-approval-admin');
                Route::get('request-reject/{id}', 'WithdrawalReqReject')->name('rejects');
            });
        });

        // ad_trust
        Route::group(['prefix' => 'ad_trust', 'as' => 'ad_trust.'], function () {
            Route::controller(DonateAdsTrustController::class)->group(function () {
                Route::get(DonateAdsTrustPath::ADDADS[URI], 'AddTrust')->name('add');
                Route::post(DonateAdsTrustPath::ADDADS[URI], 'StoreTrust')->name('store');
                Route::get(DonateAdsTrustPath::LIST[URI], 'ADsList')->name('list');
                Route::post(DonateAdsTrustPath::DELETE[URI], 'AdsDelete')->name('delete');
                Route::post(DonateAdsTrustPath::ADSSTATUS[URI], 'AdsStatus')->name('status-update');
                Route::get(DonateAdsTrustPath::UPDATEADS[URI] . '/{id}', 'AdsUpdate')->name('update');
                Route::post(DonateAdsTrustPath::UPDATEADS[URI], 'AdsUpdateSave')->name('updatestore');

                Route::post(DonateAdsTrustPath::ADDADS[URL], 'DonateTrustList')->name('api-donate-trust-list');

                Route::get(DonateAdsTrustPath::ADSINFO[URI] . '/{id}', 'AdsDetails')->name('ads-details');
                Route::post(DonateAdsTrustPath::ADSCOMMISSION[URI] . '/{id}', 'AdsAdminCommission')->name('commission_update');
                Route::get(DonateAdsTrustPath::ADSAPPROVAL[URI] . '/{id}/{status}', 'AdsAmountReqSend')->name('trust_ads_verify_approvel');
            });
        });

        Route::group(['prefix' => 'donate_lead', 'as' => 'donate_lead.'], function () {
            Route::controller(DonateTrustController::class)->group(function () {
                Route::get(DonateTrustPath::LEADS[URI], 'DonateLeads')->name('list');
                Route::post(DonateTrustPath::LEADFOLLOWUP[URI], 'DonateLeadFollowUp')->name('lead-follow-up');
                Route::get(DonateTrustPath::LEADDELETE[URI], 'DonateLeadDelete')->name('lead-delete');
                Route::get(DonateTrustPath::LEADFOLLOWUP[URL] . '/{id}', 'DonateLeadFollowList')->name('donate-follow-list');
            });
        });


        Route::group(['prefix' => 'trustees-puja-booking', 'as' => 'trustees-puja-booking.'], function () {
            Route::controller(DonateTrustController::class)->group(function () {
                Route::get(DonateTrustPath::TRUSTPUJABOOKING[URI], 'TrustPujaBooking')->name('index');
                Route::get(DonateTrustPath::TRUSTPUJABOOKING[URL], 'TrustPujaBookingFilters')->name('trust-puja-booking-filter');
            });
        });
    });


    Route::group(['prefix' => 'temple/category', 'as' => 'temple.category.'], function () {
        Route::controller(TempleCategoryController::class)->group(function () {
            Route::get(TempleCategoryEnum::ADD[URI], 'index')->name('add');
            Route::post(TempleCategoryEnum::ADD[URI], 'add_category')->name('add');
            Route::get(TempleCategoryEnum::LIST[URI], 'list')->name('list');
            Route::get(TempleCategoryEnum::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');

            Route::post(TempleCategoryEnum::UPDATE[URI] . '/{id}', 'update')->name('edit');
            Route::delete(TempleCategoryEnum::DELETE[URI] . '/{id}', 'delete')->name('delete');

            Route::post(TempleCategoryEnum::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    Route::group(['prefix' => 'temple', 'as' => 'temple.'], function () {
        Route::controller(TempleController::class)->group(function () {
            Route::get(TemplePath::ADD[URI], 'index')->name('add');
            Route::post(TemplePath::ADD[URI], 'add_temple')->name('add');
            Route::get(TemplePath::LIST[URI], 'list')->name('list');
            Route::get(TemplePath::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(TemplePath::UPDATE[URI] . '/{id}', 'update');
            Route::delete(TemplePath::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(TemplePath::DELETE_IMAGE[URI], 'deleteImage')->name('delete-image');
            Route::post(TemplePath::STATUS[URI], 'updateStatus')->name('status-update');
            Route::get(TemplePath::GET_CITIES[URI], 'getCities')->name('get-cities');

            Route::get(TemplePath::REVIEW[URI], 'review_list')->name('review');
            Route::delete(TemplePath::REVIEW[URL] . '/{id}', 'review_delete')->name('review-delete');
            Route::post(TemplePath::REVIEW[SAVE], 'review_status')->name('review-status-update');
    
            Route::get('templepackage', 'temple_package')->name('templepackage');
            Route::get('templepackageprice', 'temple_package_price')->name('templepackageprice');
            Route::post('storePackage', 'store_package_master')->name('storePackage');
            Route::get('templepackagedelete/{id}', 'delete_package_master')->name('templepackagedelete');
            Route::post('templepackagestatus/{id}', 'update_package_status')->name('templepackagestatus');
            Route::post('templevariantstatus/{id}', 'update_variant_status')->name('templevariantstatus');
            Route::get('addpackage/{id}', 'package_temple_create')->name('addpackage');
            Route::post('savepackage_services/{id}', 'savepackage_services')->name('savepackage_services');
            Route::post('storePackagePrice', 'savepackage_price')->name('storePackagePrice');
            Route::post('editPackagePrice/{priceId}/{templeId}', 'editpackage_price')->name('editPackagePrice');
            Route::get('packageeditprice/{priceId}/{templeId}', 'package_editprice')->name('packageeditprice');

            Route::get('temple-pandit-withdrawal-history','TemplePanditWithdrawal')->name('temple-pandit-withdrawal-history');
            Route::get('temple-pandit-withdrawal-history-filter','TemplePanditWithdrawalFilter')->name('temple-pandit-withdrawal-history-filter');
            Route::get('temple-pandit-withdrawal-view/{id}','TemplePanditWithdrawalView')->name('temple-pandit-withdrawal-view');
            Route::get('withdrawal-rejects/{id}','TemplePanditWithdrawalReject')->name('withdrawal-rejects');
            Route::get('withdrawal-req-pandit-approval-admin/{id}/{type}','TemplePanditWithdrawalapproval')->name('withdrawal-req-pandit-approval-admin');
            Route::get('temple/get-districts', 'getDistricts')->name('temple/get-districts');           
            
        });
    });

    // lead
    Route::group(['prefix' => 'temple/darshan-leads', 'as' => 'temple.darshan-leads.'], function () {
        Route::controller(TempleController::class)->group(function () {
            Route::get(TemplePath::DARSHANLEAD[URI], 'DarshanLeads')->name('leads-list');
            Route::get(TemplePath::LEADSDELETE[URI] . '/{id}', 'DarshanLeadDelete')->name('leads-delete');
            Route::post(TemplePath::LEADSGET[URI], 'DarshanLeadsFollowUp')->name('darshan-follow-up');
            Route::get(TemplePath::LEADSGET[URI] . '/{id?}', 'DarshanLeadsFollow')->name('darshan-follow-list');
            Route::get(TemplePath::LEADSENDWHATSAPP[URI] . '/{id?}', 'DarshanSendWhatsappLead')->name('send-whatsapp-leads');
        });
    });
    Route::group(['prefix' => 'temple/darshan-bookings', 'as' => 'temple.darshan-bookings.'], function () {
        Route::controller(TempleController::class)->group(function () {
            Route::get(TemplePath::VIPDARSHANBOOKING[URI], 'VipDarshanBooking')->name('booking-list');
            Route::get(TemplePath::VIPDARSHANBOOKINGINFO[URI] . "/{id}", 'VipDarshanBookingInfo')->name('darshan-booking-information');
        });
    });

    Route::group(['prefix' => 'temple/gallery', 'as' => 'temple.gallery.'], function () {
        Route::controller(GalleryController::class)->group(function () {
            Route::get(GalleryPath::LIST[URI] . '/{id}', 'gallery_list')->name('list');
            Route::post(GalleryPath::LIST[URI] . '/{id}', 'gallery_add')->name('list');
            Route::post(GalleryPath::ADD[URI], 'add_gallery')->name('store');
            Route::get(GalleryPath::UPDATE[URI] . "/{id}", 'update_gallery')->name('update');
            Route::post(GalleryPath::UPDATE[URI] . "/{id}", 'edit_gallery');
            Route::delete(GalleryPath::DELETE[URI] . '/{id}/{key}', 'remove_image')->name('remove_image');
            Route::delete(GalleryPath::DELETE[URI] . '/{id}', 'delete_gallery')->name('delete');

            Route::get(GalleryPath::NEWADD[URI] . '/{id}', 'add_new_gallery')->name('add_new');
            Route::post(GalleryPath::NEWADD[URI] . '/{id}', 'update_new_gallery')->name('add_new');
        });
    });

    Route::group(['prefix' => 'temple/hotel', 'as' => 'temple.hotel.'], function () {
        Route::controller(HotelController::class)->group(function () {
            Route::get(HotelsEnums::ADD[URI], 'index')->name('add');
            Route::post(HotelsEnums::ADD[URI], 'store')->name('store');
            Route::get(HotelsEnums::LIST[URI], 'list')->name('list');
            Route::delete(HotelsEnums::DELETE[URI] . "/{id}", 'delete')->name('delete');
            Route::post(HotelsEnums::STATUS[URI], 'updateStatus')->name('status-update');
            Route::get(HotelsEnums::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::post(HotelsEnums::UPDATE[URI] . '/{id}', 'edit')->name('edit');
            Route::get(HotelsEnums::GALLERY[URI] . '/{id}', 'gallery')->name('gallery');
            Route::post(HotelsEnums::GALLERY[URI] . '/{id}', 'gallery_add')->name('gallery_add');

            Route::get(HotelsEnums::GALLERY[URL] . "/{id}/{name}", 'deleteImage')->name('delete-image');

            Route::get(HotelsEnums::REVIEW[URI], 'review_list')->name('review');
            Route::delete(HotelsEnums::REVIEW[URL] . '/{id}', 'review_delete')->name('review-delete');
            Route::post(HotelsEnums::REVIEW[SAVE], 'review_status')->name('review-status-update');
        });
    });

    Route::group(['prefix' => 'temple/restaurants', 'as' => 'temple.restaurants.'], function () {
        Route::controller(RestaurantController::class)->group(function () {
            Route::get(RestaurantsPath::ADD[URI], 'index')->name('add');
            Route::post(RestaurantsPath::ADD[URI], 'store')->name('store');
            Route::get(RestaurantsPath::LIST[URI], 'list')->name('list');
            Route::post(RestaurantsPath::STATUS[URI], 'updateStatus')->name('status-update');
            Route::delete(RestaurantsPath::DELETE[URI] . "/{id}", 'delete')->name('delete');
            Route::get(RestaurantsPath::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::post(RestaurantsPath::UPDATE[URI] . '/{id}', 'edit')->name('edit');
            Route::get(RestaurantsPath::GALLERY[URI] . '/{id}', 'gallery')->name('gallery');
            Route::post(RestaurantsPath::GALLERY[URI] . '/{id}', 'gallery_add')->name('gallery_add');

            Route::get(RestaurantsPath::GALLERY[URL] . "/{id}/{name}", 'deleteImage')->name('delete-image');

            Route::get(RestaurantsPath::REVIEW[URI], 'review_list')->name('review');
            Route::delete(RestaurantsPath::REVIEW[URL] . '/{id}', 'review_delete')->name('review-delete');
            Route::post(RestaurantsPath::REVIEW[SAVE], 'review_status')->name('review-status-update');
        });
    });
    // Banner
    Route::group(['prefix' => 'banner', 'as' => 'banner.', 'middleware' => ['module:Banner Setup']], function () {
        Route::controller(BannerController::class)->group(function () {
            Route::get(Banner::LIST[URI], 'index')->name('list');
            Route::post(Banner::ADD[URI], 'add')->name('store');
            Route::post(Banner::DELETE[URI], 'delete')->name('delete');
            Route::post(Banner::STATUS[URI], 'updateStatus')->name('status');
            Route::get(Banner::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Banner::UPDATE[URI] . '/{id}', 'update');
        });
    });

    // Customer Routes, Customer wallet Routes, Customer Loyalty Routes
    Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['module:Customers']], function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get(Customer::LIST[URI], 'getListView')->name('list');
            Route::get(Customer::VIEW[URI] . '/{user_id}', 'getView')->name('view');
            Route::post(Customer::UPDATE[URI], 'updateStatus')->name('status-update');
            Route::delete(Customer::DELETE[URI], 'delete')->name('delete');
            Route::get(Customer::SUBSCRIBER_LIST[URI], 'getSubscriberListView')->name('subscriber-list');
            Route::get(Customer::SUBSCRIBER_EXPORT[URI], 'exportSubscribersList')->name('subscriber-list.export');
            Route::get(Customer::EXPORT[URI], 'exportList')->name('export');
            Route::get(Customer::SEARCH[URI], 'getCustomerList')->name('customer-list-search');
            Route::post(Customer::ADD[URI], 'add')->name('add');
            Route::get('/app-download', 'app_download')->name('app.download');
            Route::get('checked-status', 'checked_order')->name('checked-status');
            Route::post('sendapplink', 'send_app_link')->name('sendapplink');
            Route::get('feedback-list', 'feedback_list')->name('feedback-list');
            Route::post('feedback-status', 'feedback_status')->name('feedback-status');
        });

        Route::group(['prefix' => 'wallet', 'as' => 'wallet.'], function () {
            Route::controller(CustomerWalletController::class)->group(function () {
                Route::get(CustomerWallet::REPORT[URI], 'index')->name('report');
                Route::post(CustomerWallet::ADD[URI], 'addFund')->name('add-fund');
                Route::get(CustomerWallet::EXPORT[URI], 'exportList')->name('export');
                Route::get(CustomerWallet::BONUS_SETUP[URI], 'getBonusSetupView')->name('bonus-setup');
                Route::post(CustomerWallet::BONUS_SETUP[URI], 'addBonusSetup');
                Route::post(CustomerWallet::BONUS_SETUP_UPDATE[URI], 'update')->name('bonus-setup-update');
                Route::post(CustomerWallet::BONUS_SETUP_STATUS[URI], 'updateStatus')->name('bonus-setup-status');
                Route::get(CustomerWallet::BONUS_SETUP_EDIT[URI] . '/{id}', 'getUpdateView')->name('bonus-setup-edit');
                Route::delete(CustomerWallet::BONUS_SETUP_DELETE[URI], 'deleteBonus')->name('bonus-setup-delete');
            });
        });

        Route::group(['prefix' => 'loyalty', 'as' => 'loyalty.'], function () {
            Route::controller(CustomerLoyaltyController::class)->group(function () {
                Route::get(Customer::LOYALTY_REPORT[URI], 'index')->name('report');
                Route::get(Customer::LOYALTY_EXPORT[URI], 'exportList')->name('export');
            });
        });
        Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function () {
            Route::controller(CustomerController::class)->group(function () {
                Route::get('list/{pending}', 'withdraw_list')->name('list');
                Route::post('approve', 'withdraw_approve')->name('approve');
                Route::post('complete', 'withdraw_complete')->name('complete');
            });
        });
    });

    Route::group(['prefix' => 'report', 'as' => 'report.', 'middleware' => ['module:Sales & Transaction Report']], function () {
        Route::controller(InhouseProductSaleController::class)->group(function () {
            Route::get(InhouseProductSale::VIEW[URI], 'index')->name('inhouse-product-sale');
        });
    });

    Route::group(['middleware' => ['module:Business Setup']], function () {
        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
            Route::controller(CustomerController::class)->group(function () {
                Route::get(Customer::SETTINGS[URI], 'getCustomerSettingsView')->name('customer-settings');
                Route::post(Customer::SETTINGS[URI], 'update');
            });
        });
    });

    Route::group(['prefix' => 'sellers', 'as' => 'sellers.', 'middleware' => ['module:Vendor']], function () {
        Route::controller(VendorController::class)->group(function () {
            Route::get(Vendor::LIST[URI], 'index')->name('seller-list');
            Route::get(Vendor::ADD[URI], 'getAddView')->name('add');
            Route::POST(Vendor::ADD[URI], 'add');
            Route::get(Vendor::ORDER_LIST[URI] . '/{seller_id}', 'getOrderListView')->name('order-list');
            Route::post(Vendor::STATUS[URI], 'updateStatus')->name('updateStatus');
            Route::get(Vendor::EXPORT[URI], 'exportList')->name('export');
            Route::get(Vendor::PRODUCT_LIST[URI] . '/{seller_id}', 'getProductListView')->name('product-list');

            Route::post(Vendor::SALES_COMMISSION_UPDATE[URI] . '/{id}', 'updateSalesCommission')->name('sales-commission-update');
            Route::get(Vendor::ORDER_DETAILS[URI] . '/{order_id}/{seller_id}', 'getOrderDetailsView')->name('order-details');
            Route::get(Vendor::VIEW[URI] . '/{id}/{tab?}', 'getView')->name('view');
            Route::post(Vendor::UPDATE_SETTING[URI] . '/{id}', 'updateSetting')->name('update-setting');

            Route::get(Vendor::WITHDRAW_LIST[URI], 'getWithdrawListView')->name('withdraw_list');
            Route::get(Vendor::WITHDRAW_LIST_EXPORT[URI], 'exportWithdrawList')->name('withdraw-list-export-excel');
            Route::get(Vendor::WITHDRAW_VIEW[URI] . '/{withdraw_id}/{seller_id}', 'getWithdrawView')->name('withdraw_view');
            Route::post(Vendor::WITHDRAW_STATUS[URI] . '/{id}', 'withdrawStatus')->name('withdraw_status');
            Route::post("doc-verified-resend", 'DocVerifiedResend')->name('doc_verified_resend');
            Route::post("doc-verified-success", 'DocVerifiedSuccess')->name('doc_verified_success');
            Route::get('checked-status', 'checked_order')->name('checked-status');
            Route::get('cash-withdraw-list', 'getCashWithdrawlist')->name('cash-withdraw-list');
            Route::get('cash-withdraw-view/{withdraw_id}/{seller_id}', 'getCashWithdrawView')->name('cash_withdraw_view');
            Route::post('cash-withdraw-status/{id}', 'cashwithdrawStatus')->name('cash-withdraw-status');
        });

        Route::group(['prefix' => 'withdraw-method', 'as' => 'withdraw-method.'], function () {
            Route::controller(WithdrawalMethodController::class)->group(function () {
                Route::get(WithdrawalMethod::LIST[URI], 'index')->name('list');
                Route::get(WithdrawalMethod::ADD[URI], 'getAddView')->name('add');
                Route::post(WithdrawalMethod::ADD[URI], 'add');
                Route::delete(WithdrawalMethod::DELETE[URI] . '/{id}', 'delete')->name('delete');
                Route::post(WithdrawalMethod::DEFAULT_STATUS[URI], 'updateDefaultStatus')->name('default-status');
                Route::post(WithdrawalMethod::STATUS[URI], 'updateStatus')->name('status-update');
                Route::get(WithdrawalMethod::UPDATE[URI] . '/{id}', 'getUpdateView')->name('edit');
                Route::post(WithdrawalMethod::UPDATE[URI], 'update')->name('update');
            });
        });
    });

    Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
        Route::controller(EmployeeController::class)->group(function () {
            Route::get(Employee::LIST[URI], 'index')->name('list');
            Route::get(Employee::ADD[URI], 'getAddView')->name('add-new');
            Route::post(Employee::ADD[URI], 'add')->name('add-new');
            Route::get(Employee::EXPORT[URI], 'exportList')->name('export');
            Route::get(Employee::VIEW[URI] . '/{id}', 'getView')->name('view');
            Route::get(Employee::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Employee::UPDATE[URI] . '/{id}', 'update');
            Route::post(Employee::STATUS[URI], 'updateStatus')->name('status');
        });
    });

    Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:Employee']], function () {
        Route::controller(CustomRoleController::class)->group(function () {
            Route::get(CustomRole::ADD[URI], 'index')->name('create');
            Route::post(CustomRole::ADD[URI], 'add')->name('store');
            Route::get(CustomRole::LIST[URI], 'list')->name('list');
            Route::get(CustomRole::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(CustomRole::UPDATE[URI] . '/{id}', 'update');
            Route::post(CustomRole::STATUS[URI], 'updateStatus')->name('employee-role-status');
            Route::post(CustomRole::DELETE[URI], 'delete')->name('delete');
            Route::get(CustomRole::EXPORT[URI], 'exportList')->name('export');
        });
    });

    /*  report */
    Route::group(['prefix' => 'report', 'as' => 'report.', 'middleware' => ['module:Sales & Transaction Report']], function () {
        Route::group(['prefix' => 'transaction', 'as' => 'transaction.'], function () {
            Route::controller(RefundTransactionController::class)->group(function () {
                Route::get(RefundTransaction::INDEX[URI], 'index')->name('refund-transaction-list');
                Route::get(RefundTransaction::EXPORT[URI], 'getRefundTransactionExport')->name('refund-transaction-export');
                Route::get(RefundTransaction::GENERATE_PDF[URI], 'getRefundTransactionExport')->name('refund-transaction-summary-pdf');
            });
        });
    });

    Route::group(['prefix' => 'report', 'as' => 'report.', 'middleware' => ['module:Sales & Transaction Report']], function () {
        Route::controller(ReportController::class)->group(function () {
            Route::get('earning', 'earning_index')->name('earning');
            Route::get('admin-earning', 'admin_earning')->name('admin-earning');
            Route::get('admin-earning-excel-export', 'admin_earning_excel_export')->name('admin-earning-excel-export');
            Route::post('admin-earning-duration-download-pdf', 'admin_earning_duration_download_pdf')->name('admin-earning-duration-download-pdf');
            Route::get('seller-earning', 'seller_earning')->name('seller-earning');
            Route::get('seller-earning-excel-export', 'seller_earning_excel_export')->name('seller-earning-excel-export');
            Route::any('set-date', 'set_date')->name('set-date');
        });

        Route::controller(OrderReportController::class)->group(function () {
            Route::get('order', 'order_list')->name('order');
            Route::get('order-report-excel', 'order_report_export_excel')->name('order-report-excel');
            Route::get('order-report-pdf', 'exportOrderReportInPDF')->name('order-report-pdf');
        });

        Route::controller(ProductReportController::class)->group(function () {
            Route::get('all-product', 'all_product')->name('all-product');
            Route::get('all-product-excel', 'all_product_export_excel')->name('all-product-excel');
        });

        Route::controller(SellerProductSaleReportController::class)->group(function () {
            Route::get('seller-report', 'seller_report')->name('seller-report');
            Route::get('seller-report-excel', 'seller_report_excel')->name('seller-report-excel');
        });
    });

    Route::group(['prefix' => 'transaction', 'as' => 'transaction.', 'middleware' => ['module:Sales & Transaction Report']], function () {
        Route::controller(TransactionReportController::class)->group(function () {
            Route::get('order-transaction-list', 'order_transaction_list')->name('order-transaction-list');
            Route::get('pdf-order-wise-transaction', 'pdf_order_wise_transaction')->name('pdf-order-wise-transaction');
            Route::get('order-transaction-export-excel', 'order_transaction_export_excel')->name('order-transaction-export-excel');
            Route::get('order-transaction-summary-pdf', 'order_transaction_summary_pdf')->name('order-transaction-summary-pdf');
            Route::get('expense-transaction-list', 'expense_transaction_list')->name('expense-transaction-list');
            Route::get('pdf-order-wise-expense-transaction', 'pdf_order_wise_expense_transaction')->name('pdf-order-wise-expense-transaction');
            Route::get('expense-transaction-export-excel', 'expense_transaction_export_excel')->name('expense-transaction-export-excel');
            Route::get('expense-transaction-summary-pdf', 'expense_transaction_summary_pdf')->name('expense-transaction-summary-pdf');

            Route::get('wallet-bonus', 'wallet_bonus')->name('wallet-bonus');
        });
    });

    Route::group(['prefix' => 'stock', 'as' => 'stock.', 'middleware' => ['module:Product Report']], function () {
        Route::controller(ProductStockReportController::class)->group(function () {
            //product stock report
            Route::get('product-stock', 'index')->name('product-stock');
            Route::get('product-stock-export', 'export')->name('product-stock-export');
            Route::post('ps-filter', 'filter')->name('ps-filter');
        });

        Route::controller(ProductWishlistReportController::class)->group(function () {
            //product in wishlist report
            Route::get('product-in-wishlist', 'index')->name('product-in-wishlist');
            Route::get('wishlist-product-export', 'export')->name('wishlist-product-export');
        });
    });

    /*  end report */
    // Reviews
    Route::group(['prefix' => 'reviews', 'as' => 'reviews.', 'middleware' => ['module:Customers']], function () {
        Route::controller(ReviewController::class)->group(function () {
            Route::get(Review::LIST[URI], 'index')->name('list')->middleware('actch');
            Route::get(Review::STATUS[URI], 'updateStatus')->name('status');
            Route::get(Review::EXPORT[URI], 'exportList')->name('export')->middleware('actch');
            Route::get(Review::SEARCH[URI], 'getCustomerList')->name('customer-list-search');
            Route::any(Review::SEARCH_PRODUCT[URI], 'search')->name('search-product');
        });
    });

    // Coupon
    Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:Offers & Setup']], function () {
        Route::controller(CouponController::class)->group(function () {
            Route::get(Coupon::ADD[URI], 'getAddListView')->name('add')->middleware('actch');
            Route::post(Coupon::ADD[URI], 'add');
            Route::get(Coupon::EXPORT[URI], 'exportList')->name('export')->middleware('actch');
            Route::get(Coupon::QUICK_VIEW[URI], 'quickView')->name('quick-view-details');
            Route::get(Coupon::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update')->middleware('actch');
            Route::post(Coupon::UPDATE[URI] . '/{id}', 'update');
            Route::get(Coupon::STATUS[URI] . '/{id}/{status}', 'updateStatus')->name('status');
            Route::post(Coupon::SELLER_LIST[URI], 'getSellerList')->name('ajax-get-seller');
            Route::delete(Coupon::DELETE[URI] . '/{id}', 'delete')->name('delete');
        });
    });

    Route::group(['prefix' => 'deal', 'as' => 'deal.', 'middleware' => ['module:Offers & Setup']], function () {
        Route::controller(FlashDealController::class)->group(function () {
            Route::get(FlashDeal::LIST[URI], 'index')->name('flash');
            Route::post(FlashDeal::LIST[URI], 'add');
            Route::get(FlashDeal::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(FlashDeal::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::post(FlashDeal::STATUS[URI], 'updateStatus')->name('status-update');
            Route::post(FlashDeal::DELETE[URI], 'delete')->name('delete-product');
            Route::get(FlashDeal::ADD_PRODUCT[URI] . '/{deal_id}', 'getAddProductView')->name('add-product');
            Route::post(FlashDeal::ADD_PRODUCT[URI] . '/{deal_id}', 'addProduct');
            Route::any(FlashDeal::SEARCH[URI], 'search')->name('search-product');
        });

        Route::controller(DealOfTheDayController::class)->group(function () {
            Route::get(DealOfTheDay::LIST[URI], 'index')->name('day');
            Route::post(DealOfTheDay::LIST[URI], 'add');
            Route::post(DealOfTheDay::STATUS[URI], 'updateStatus')->name('day-status-update');
            Route::get(DealOfTheDay::UPDATE[URI] . '/{id}', 'getUpdateView')->name('day-update');
            Route::post(DealOfTheDay::UPDATE[URI] . '/{id}', 'update');
            Route::post(DealOfTheDay::DELETE[URI], 'delete')->name('day-delete');
        });

        Route::controller(FeaturedDealController::class)->group(function () {
            Route::get(FeatureDeal::LIST[URI], 'index')->name('feature');
            Route::get(FeatureDeal::UPDATE[URI] . '/{id}', 'getUpdateView')->name('edit');
            Route::post(FeatureDeal::UPDATE[URI], 'update')->name('featured-update');
            Route::post(FeatureDeal::STATUS[URI], 'updateStatus')->name('feature-status');
        });
    });

    /** notification and push notification */
    Route::group(['prefix' => 'push-notification', 'as' => 'push-notification.', 'middleware' => ['module:Notifications']], function () {
        Route::controller(PushNotificationSettingsController::class)->group(function () {
            Route::get(PushNotification::INDEX[URI], 'index')->name('index');
            Route::post(PushNotification::UPDATE[URI], 'updatePushNotificationMessage')->name('update');
            Route::get(PushNotification::FIREBASE_CONFIGURATION[URI], 'getFirebaseConfigurationView')->name('firebase-configuration');
            Route::post(PushNotification::FIREBASE_CONFIGURATION[URI], 'getFirebaseConfigurationUpdate');
        });
    });
    Route::group(['prefix' => 'notification', 'as' => 'notification.', 'middleware' => ['module:Notifications']], function () {
        Route::controller(NotificationController::class)->group(function () {
            Route::get(Notification::INDEX[URI], 'index')->name('index');
            Route::post(Notification::INDEX[URI], 'add');
            Route::get(Notification::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Notification::UPDATE[URI] . '/{id}', 'update');
            Route::post(Notification::DELETE[URI], 'delete')->name('delete');
            Route::post(Notification::UPDATE_STATUS[URI], 'updateStatus')->name('update-status');
            Route::post(Notification::RESEND_NOTIFICATION[URI], 'resendNotification')->name('resend-notification');
        });
    });
    /* end notification */
    Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.', 'middleware' => ['module:Help & Support']], function () {
        Route::controller(SupportTicketController::class)->group(function () {
            Route::get(SupportTicket::LIST[URI], 'index')->name('view');
            Route::post(SupportTicket::STATUS[URI], 'updateStatus')->name('status');
            Route::get(SupportTicket::VIEW[URI] . '/{id}', 'getView')->name('singleTicket');
            Route::post(SupportTicket::VIEW[URI] . '/{id}', 'reply')->name('replay');
            Route::get(SupportTicket::LIST[URL], 'IndexTicket')->name('view-ticket');

            //new support
            Route::get(SupportTicket::TYPELIST[URI], 'AddType')->name('type-add');
            Route::post(SupportTicket::TYPELIST[URI], 'StoreType')->name('type-store');

            Route::get(SupportTicket::TYPEUPDATE[URI] . '/{id}', 'UpdateType')->name('type-update');
            Route::post(SupportTicket::TYPEUPDATE[URI], 'EditType')->name('type-edit');

            Route::post(SupportTicket::TYPESTATUSUPDATE[URI], 'statusUpdate')->name('status-update');
            Route::delete(SupportTicket::TYPEDELETE[URI] . '/{id}', 'DeleteType')->name('delete-type');

            //
            Route::get(SupportTicket::ISSUELIST[URI], 'AddIssue')->name('issue-add');
            Route::post(SupportTicket::ISSUELIST[URI], 'StoreIssue')->name('issue-store');
            Route::get(SupportTicket::ISSUEUPDATE[URI] . '/{id}', 'UpdateIssue')->name('issue-update');
            Route::post(SupportTicket::ISSUEUPDATE[URI], 'EditIssue')->name('issue-edit');

            Route::post(SupportTicket::ISSUESTATUSUPDATE[URI], 'statusUpdateIssue')->name('status-update-issue');
            Route::delete(SupportTicket::ISSUEDELETE[URI] . '/{id}', 'DeleteIssue')->name('delete-type-issue');
        });
    });
    Route::group(['prefix' => 'vendor-support-ticket', 'as' => 'vendor-support-ticket.', 'middleware' => ['module:Help & Support']], function () {
        Route::controller(VendorTicketController::class)->group(function () {
            Route::get(VendorSuppTicket::ISSUELIST[URI], 'AddIssue')->name('view');
            Route::post(VendorSuppTicket::ISSUELIST[URI], 'StoreIssue')->name('issue-store');
            Route::get(VendorSuppTicket::ISSUEUPDATE[URI] . '/{id}', 'UpdateIssue')->name('issue-update');
            Route::post(VendorSuppTicket::ISSUEUPDATE[URI], 'EditIssue')->name('issue-edit');

            Route::post(VendorSuppTicket::ISSUESTATUSUPDATE[URI], 'statusUpdateIssue')->name('status-update-issue');
            Route::delete(VendorSuppTicket::ISSUEDELETE[URI] . '/{id}', 'DeleteIssue')->name('delete-type-issue');
            Route::group(['prefix' => 'vendor-inbox', 'as' => 'vendor-inbox.'], function () {
                Route::get(VendorSuppTicket::VENDORLIST[URI], 'ListVendorIssue')->name('view');
                Route::post(VendorSuppTicket::VENDORISSUESTATUS[URI], 'VendorIssueStatus')->name('status');
                Route::get(VendorSuppTicket::VENDORSINGLE[URI] . '/{id}', 'VendorIssueGetSingle')->name('singleTicket');
                Route::post(VendorSuppTicket::VENDORSINGLE[URI] . "/{id}", "VendorSupportTicketReplay")->name('replay');
            });
            Route::group(['prefix' => 'admin-inbox', 'as' => 'admin-inbox.'], function () {
                Route::get(VendorSuppTicket::ADMINLIST[URI], 'ListAdminIssue')->name('view');
                Route::post(VendorSuppTicket::ADMINLIST[URI], 'ListAdminStore')->name('store-inbox');

                Route::post(VendorSuppTicket::ADMINISSUESTATUS[URI], 'AdminIssueStatus')->name('status');
                Route::get(VendorSuppTicket::ADMINSINGLE[URI] . '/{id}', 'VendorIssueGetSingle')->name('singleTicket');
                Route::post(VendorSuppTicket::ADMINSINGLE[URI] . "/{id}", "AdminSupportTicketReplay")->name('replay');
            });
        });
    });
    Route::group(['prefix' => 'messages', 'as' => 'messages.'], function () {
        Route::controller(ChattingController::class)->group(function () {
            Route::get(Chatting::INDEX[URI] . '/{type}', 'index')->name('index');
            Route::get(Chatting::MESSAGE[URI], 'getMessageByUser')->name('message');
            Route::post(Chatting::MESSAGE[URI], 'addAdminMessage');
        });
    });

    Route::group(['prefix' => 'contact', 'as' => 'contact.', 'middleware' => ['module:Help & Support']], function () {
        Route::controller(ContactController::class)->group(function () {
            Route::get(Contact::LIST[URI], 'index')->name('list');
            Route::get(Contact::VIEW[URI] . '/{id}', 'getView')->name('view');
            Route::post(Contact::FILTER[URI], 'getListByFilter')->name('filter');
            Route::post(Contact::DELETE[URI], 'delete')->name('delete');
            Route::post(Contact::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::post(Contact::ADD[URI], 'add')->name('store');
            Route::post(Contact::SEND_MAIL[URI] . '/{id}', 'sendMail')->name('send-mail');
        });
    });

    Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.', 'middleware' => ['module:Delivery Men']], function () {
        Route::controller(DeliveryManController::class)->group(function () {
            Route::get(DeliveryMan::LIST[URI], 'index')->name('list');
            Route::get(DeliveryMan::ADD[URI], 'getAddView')->name('add');
            Route::post(DeliveryMan::ADD[URI], 'add');
            Route::post(DeliveryMan::STATUS[URI], 'updateStatus')->name('status-update');
            Route::get(DeliveryMan::EXPORT[URI], 'exportList')->name('export');
            Route::get(DeliveryMan::UPDATE[URI] . '/{id}', 'getUpdateView')->name('edit');
            Route::post(DeliveryMan::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::delete(DeliveryMan::DELETE[URI] . '/{id}', 'delete')->name('delete');
            Route::get(DeliveryMan::EARNING_STATEMENT_OVERVIEW[URI] . '/{id}', 'getEarningOverview')->name('earning-statement-overview');
            Route::get(DeliveryMan::EARNING_OVERVIEW[URI] . '/{id}', 'getOrderWiseEarningView')->name('order-wise-earning');
            Route::post(DeliveryMan::ORDER_WISE_EARNING_LIST_BY_FILTER[URI] . '/{id}', 'getOrderWiseEarningListByFilter')->name('order-wise-earning-list-by-filter');
            Route::get(DeliveryMan::ORDER_HISTORY_LOG[URI] . '/{id}', 'getOrderHistoryList')->name('order-history-log');
            Route::get(DeliveryMan::ORDER_HISTORY_LOG_EXPORT[URI] . '/{id}', 'getOrderHistoryListExport')->name('order-history-log-export');
            Route::get(DeliveryMan::RATING[URI] . '/{id}', 'getRatingView')->name('rating');
            Route::get(DeliveryMan::ORDER_HISTORY[URI] . '/{order}', 'getOrderStatusHistory')->name('ajax-order-status-history');
        });

        Route::controller(DeliveryManCashCollectController::class)->group(function () {
            Route::get(DeliveryManCash::LIST[URI] . '/{id}', 'index')->name('collect-cash');
            Route::post(DeliveryManCash::ADD[URI] . '/{id}', 'getCashReceive')->name('cash-receive');
        });

        Route::controller(DeliverymanWithdrawController::class)->group(function () {
            Route::get(DeliverymanWithdraw::LIST[URI], 'index')->name('withdraw-list');
            Route::post(DeliveryManWithdraw::LIST[URI], 'getFiltered');
            Route::get(DeliverymanWithdraw::EXPORT_LIST[URI], 'exportList')->name('withdraw-list-export');
            Route::get(DeliverymanWithdraw::VIEW[URI] . '/{withdraw_id}', 'getView')->name('withdraw-view');
            Route::post(DeliverymanWithdraw::UPDATE[URI] . '/{id}', 'updateStatus')->name('withdraw-update-status');
        });
        Route::group(['prefix' => 'emergency-contact', 'as' => 'emergency-contact.'], function () {
            Route::controller(EmergencyContactController::class)->group(function () {
                Route::get(EmergencyContact::LIST[URI], 'index')->name('index');
                Route::post(EmergencyContact::ADD[URI], 'add')->name('add');
                Route::post(EmergencyContact::STATUS[URI], 'updateStatus')->name('ajax-status-change');
                Route::delete(EmergencyContact::DELETE[URI], 'delete')->name('destroy');
            });
        });
    });

    Route::group(['prefix' => 'most-demanded', 'as' => 'most-demanded.', 'middleware' => ['module:promotion_management']], function () {
        Route::controller(MostDemandedController::class)->group(function () {
            Route::get(MostDemanded::LIST[URI], 'index')->name('index');
            Route::post(MostDemanded::ADD[URI], 'add')->name('store');
            Route::get(MostDemanded::UPDATE[URI] . '/{id}', 'getUpdateView')->name('edit');
            Route::post(MostDemanded::UPDATE[URI] . '/{id}', 'update')->name('update');
            Route::post(MostDemanded::DELETE[URI], 'delete')->name('delete');
            Route::post(MostDemanded::STATUS[URI], 'updateStatus')->name('status-update');
        });
    });

    Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function () {
        Route::controller(AllPagesBannerController::class)->group(function () {
            Route::get(AllPagesBanner::LIST[URI], 'index')->name('all-pages-banner');
            Route::post(AllPagesBanner::ADD[URI], 'add')->name('all-pages-banner-store');
            Route::get(AllPagesBanner::UPDATE[URI] . '/{id}', 'getUpdateView')->name('all-pages-banner-edit');
            Route::post(AllPagesBanner::UPDATE[URI], 'update')->name('all-pages-banner-update');
            Route::post(AllPagesBanner::STATUS[URI], 'updateStatus')->name('all-pages-banner-status');
            Route::post(AllPagesBanner::DELETE[URI], 'delete')->name('all-pages-banner-delete');
        });
    });

    Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function () {

        Route::group(['middleware' => ['module:System Setup']], function () {
            Route::controller(BusinessSettingsController::class)->group(function () {
                Route::get(BusinessSettings::OTP_SETUP[URI], 'getOtpSetupView')->name('otp-setup');
                Route::post(BusinessSettings::OTP_SETUP[URI], 'updateOtpSetup');
            });
        });

        Route::group(['middleware' => ['module:Page & Media']], function () {
            Route::controller(PagesController::class)->group(function () {
                Route::get(Pages::TERMS_CONDITION[URI], 'index')->name('terms-condition');
                Route::post(Pages::TERMS_CONDITION[URI], 'updateTermsCondition')->name('update-terms');

                Route::get(Pages::PRIVACY_POLICY[URI], 'getPrivacyPolicyView')->name('privacy-policy');
                Route::post(Pages::PRIVACY_POLICY[URI], 'updatePrivacyPolicy')->name('privacy-policy');

                Route::get(Pages::ABOUT_US[URI], 'getAboutUsView')->name('about-us');
                Route::post(Pages::ABOUT_US[URI], 'updateAboutUs')->name('about-update');

                Route::get(Pages::VIEW[URI] . '/{page}', 'getPageView')->name('page');
                Route::post(Pages::VIEW[URI] . '/{page}', 'updatePage')->name('page-update');
            });

            Route::controller(SocialMediaSettingsController::class)->group(function () {
                Route::get(SocialMedia::VIEW[URI], 'index')->name('social-media');
                Route::get(SocialMedia::LIST[URI], 'getList')->name('fetch');
                Route::post(SocialMedia::ADD[URI], 'add')->name('social-media-store');
                Route::post(SocialMedia::GET_UPDATE[URI], 'getUpdate')->name('social-media-edit');
                Route::post(SocialMedia::UPDATE[URI], 'update')->name('social-media-update');
                Route::post(SocialMedia::DELETE[URI], 'delete')->name('social-media-delete');
                Route::post(SocialMedia::STATUS[URI], 'updateStatus')->name('social-media-status-update');
            });

            Route::controller(BusinessSettingsController::class)->group(function () {
                Route::post(BusinessSettings::MAINTENANCE_MODE[URI], 'updateSystemMode')->name('maintenance-mode');

                Route::get(BusinessSettings::COOKIE_SETTINGS[URI], 'getCookieSettingsView')->name('cookie-settings');
                Route::post(BusinessSettings::COOKIE_SETTINGS[URI], 'updateCookieSetting');

                // Route::get(BusinessSettings::OTP_SETUP[URI], 'getOtpSetupView')->name('otp-setup');
                // Route::post(BusinessSettings::OTP_SETUP[URI], 'updateOtpSetup');

                Route::get(BusinessSettings::ANALYTICS_INDEX[URI], 'getAnalyticsView')->name('analytics-index');
                Route::post(BusinessSettings::ANALYTICS_UPDATE[URI], 'updateAnalytics')->name('analytics-update');
            });

            Route::controller(RecaptchaController::class)->group(function () {
                Route::get(Recaptcha::VIEW[URI], 'index')->name('captcha');
                Route::post(Recaptcha::VIEW[URI], 'update');
            });

            Route::controller(GoogleMapAPIController::class)->group(function () {
                Route::get(GoogleMapAPI::VIEW[URI], 'index')->name('map-api');
                Route::post(GoogleMapAPI::VIEW[URI], 'update');
            });

            Route::controller(FeaturesSectionController::class)->group(function () {
                Route::get(FeaturesSection::VIEW[URI], 'index')->name('features-section');
                Route::post(FeaturesSection::UPDATE[URI], 'update')->name('features-section.submit');
                Route::post(FeaturesSection::DELETE[URI], 'delete')->name('features-section.icon-remove');

                Route::get(FeaturesSection::COMPANY_RELIABILITY[URI], 'getCompanyReliabilityView')->name('company-reliability');
                Route::post(FeaturesSection::COMPANY_RELIABILITY[URI], 'updateCompanyReliability');
            });
        });

        Route::group(['prefix' => 'language', 'as' => 'language.', 'middleware' => ['module:System Setup']], function () {
            Route::controller(LanguageController::class)->group(function () {
                Route::get(Language::LIST[URI], 'index')->name('index');
                Route::post(Language::ADD[URI], 'add')->name('add-new');
                Route::post(Language::STATUS[URI], 'updateStatus')->name('update-status');
                Route::get(Language::DEFAULT_STATUS[URI], 'updateDefaultStatus')->name('update-default-status');
                Route::post(Language::UPDATE[URI], 'update')->name('update');
                Route::get(Language::DELETE[URI] . '/{lang}', 'delete')->name('delete');
                Route::get(Language::TRANSLATE_VIEW[URI] . '/{lang}', 'getTranslateView')->name('translate');
                Route::get(Language::TRANSLATE_LIST[URI] . '/{lang}', 'getTranslateList')->name('translate.list');
                Route::post(Language::TRANSLATE_ADD[URI] . '/{lang}', 'updateTranslate')->name('translate-submit');
                Route::post(Language::TRANSLATE_REMOVE[URI] . '/{lang}', 'deleteTranslateKey')->name('remove-key');
                Route::any(Language::TRANSLATE_AUTO[URI] . '/{lang}', 'getAutoTranslate')->name('auto-translate');
            });
        });

        Route::group(['prefix' => 'web-config', 'as' => 'web-config.', 'middleware' => ['module:Business Setup']], function () {
            Route::controller(BusinessSettingsController::class)->group(function () {
                Route::get(BusinessSettings::INDEX[URI], 'index')->name('index')->middleware('actch');
                Route::post(BusinessSettings::INDEX[URI], 'updateSettings')->name('update');

                Route::get(BusinessSettings::APP_SETTINGS[URI], 'getAppSettingsView')->name('app-settings');
                Route::post(BusinessSettings::APP_SETTINGS[URI], 'updateAppSettings');

                Route::get(BusinessSettings::LOGIN_URL_SETUP[URI], 'getLoginSetupView')->name('login-url-setup');
                Route::post(BusinessSettings::LOGIN_URL_SETUP[URI], 'updateLoginSetupView');
            });

            Route::controller(EnvironmentSettingsController::class)->group(function () {
                Route::get(EnvironmentSettings::VIEW[URI], 'index')->name('environment-setup');
                Route::post(EnvironmentSettings::VIEW[URI], 'update');
            });

            Route::controller(SiteMapController::class)->group(function () {
                Route::get(SiteMap::VIEW[URI], 'index')->name('mysitemap');
                Route::get(SiteMap::DOWNLOAD[URI], 'getFile')->name('mysitemap-download');
            });

            Route::controller(DatabaseSettingController::class)->group(function () {
                Route::get(DatabaseSetting::VIEW[URI], 'index')->name('db-index');
                Route::post(DatabaseSetting::DELETE[URI], 'delete')->name('clean-db');
            });

            Route::group(['prefix' => 'theme', 'as' => 'theme.'], function () {
                Route::controller(ThemeController::class)->group(function () {
                    Route::get(ThemeSetup::VIEW[URI], 'index')->name('setup');
                    Route::post(ThemeSetup::UPLOAD[URI], 'upload')->name('install');
                    Route::post(ThemeSetup::ACTIVE[URI], 'activation')->name('activation');
                    Route::post(ThemeSetup::STATUS[URI], 'publish')->name('publish');
                    Route::post(ThemeSetup::DELETE[URI], 'delete')->name('delete');
                    Route::post(ThemeSetup::NOTIFY_SELLER[URI], 'notifyAllTheSellers')->name('notify-all-the-sellers');
                });
            });
        });
    });

    Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function () {

        Route::group(['middleware' => ['module:3rd Party']], function () {
            Route::controller(SMSModuleController::class)->group(function () {
                Route::get(SMSModule::VIEW[URI], 'index')->name('sms-module');
                Route::put(SMSModule::UPDATE[URI], 'update')->name('addon-sms-set');
            });
        });

        Route::group(['prefix' => 'shipping-method', 'as' => 'shipping-method.', 'middleware' => ['module:Business Setup']], function () {
            Route::controller(ShippingMethodController::class)->group(function () {
                Route::get(ShippingMethod::INDEX[URI], 'index')->name('index');
                Route::post(ShippingMethod::INDEX[URI], 'add');
                Route::get(ShippingMethod::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
                Route::post(ShippingMethod::UPDATE[URI] . '/{id}', 'update');
                Route::post(ShippingMethod::UPDATE_STATUS[URI], 'updateStatus')->name('update-status');
                Route::post(ShippingMethod::DELETE[URI], 'delete')->name('delete');
                Route::post(ShippingMethod::UPDATE_SHIPPING_RESPONSIBILITY[URI], 'updateShippingResponsibility')->name('update-shipping-responsibility');
            });
        });

        Route::group(['prefix' => 'shipping-type', 'as' => 'shipping-type.'], function () {
            Route::post(ShippingType::INDEX[URI], [ShippingTypeController::class, 'addOrUpdate'])->name('index');
        });

        Route::group(['prefix' => 'category-shipping-cost', 'as' => 'category-shipping-cost.', 'middleware' => ['module:system_settings']], function () {
            Route::controller(CategoryShippingCostController::class)->group(function () {
                Route::post('store', 'add')->name('store');
            });
        });

        Route::group(['prefix' => 'mail', 'as' => 'mail.', 'middleware' => ['module:3rd Party']], function () {
            Route::controller(MailController::class)->group(function () {
                Route::get(Mail::VIEW[URI], 'index')->name('index');
                Route::post(Mail::UPDATE[URI], 'update')->name('update');
                Route::post(Mail::UPDATE_SENDGRID[URI], 'updateSendGrid')->name('update-sendgrid');
                Route::post(Mail::SEND[URI], 'send')->name('send');
            });
        });

        Route::group(['prefix' => 'order-settings', 'as' => 'order-settings.', 'middleware' => ['module:Business Setup']], function () {
            Route::controller(OrderSettingsController::class)->group(function () {
                Route::get(BusinessSettings::ORDER_VIEW[URI], 'index')->name('index');
                Route::post(BusinessSettings::ORDER_UPDATE[URI], 'update')->name('update-order-settings');
            });
        });

        Route::group(['prefix' => 'seller-settings', 'as' => 'seller-settings.', 'middleware' => ['module:Business Setup']], function () {
            Route::controller(SellerSettingsController::class)->group(function () {
                Route::get(BusinessSettings::SELLER_VIEW[URI], 'index')->name('index')->middleware('actch');
                Route::post(BusinessSettings::SELLER_SETTINGS_UPDATE[URI], 'update')->name('update-seller-settings');
            });
        });

        Route::group(['prefix' => 'delivery-man-settings', 'as' => 'delivery-man-settings.', 'middleware' => ['module:Business Setup']], function () {
            Route::controller(DeliverymanSettingsController::class)->group(function () {
                Route::get(BusinessSettings::DELIVERYMAN_VIEW[URI], 'index')->name('index');
                Route::post(BusinessSettings::DELIVERYMAN_VIEW_UPDATE[URI], 'update')->name('update');
            });
        });

        Route::group(['prefix' => 'payment-method', 'as' => 'payment-method.', 'middleware' => ['module:3rd Party']], function () {
            Route::controller(PaymentMethodController::class)->group(function () {
                Route::get(PaymentMethod::LIST[URI], 'index')->name('index')->middleware('actch');
                Route::get(PaymentMethod::PAYMENT_OPTION[URI], 'getPaymentOptionView')->name('payment-option');
                Route::post(PaymentMethod::PAYMENT_OPTION[URI], 'updatePaymentOption');
                Route::put(PaymentMethod::UPDATE_CONFIG[URI], 'UpdatePaymentConfig')->name('addon-payment-set');
            });
        });
        Route::group(['prefix' => 'offline-payment-method', 'as' => 'offline-payment-method.', 'middleware' => ['module:3rd Party']], function () {
            Route::controller(OfflinePaymentMethodController::class)->group(function () {
                Route::get(OfflinePaymentMethod::INDEX[URI], 'index')->name('index')->middleware('actch');
                Route::get(OfflinePaymentMethod::ADD[URI], 'getAddView')->name('add')->middleware('actch');
                Route::post(OfflinePaymentMethod::ADD[URI], 'add')->middleware('actch');
                Route::get(OfflinePaymentMethod::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update')->middleware('actch');
                Route::post(OfflinePaymentMethod::UPDATE[URI] . '/{id}', 'update')->middleware('actch');
                Route::post(OfflinePaymentMethod::DELETE[URI], 'delete')->name('delete')->middleware('actch');
                Route::post(OfflinePaymentMethod::UPDATE_STATUS[URI], 'updateStatus')->name('update-status')->middleware('actch');
            });
        });


        Route::group(['prefix' => 'delivery-restriction', 'as' => 'delivery-restriction.', 'middleware' => ['module:Business Setup']], function () {
            Route::controller(DeliveryRestrictionController::class)->group(function () {
                Route::get(DeliveryRestriction::VIEW[URI], 'index')->name('index');
                Route::post(DeliveryRestriction::ADD[URI], 'add')->name('add-delivery-country');
                Route::delete(DeliveryRestriction::DELETE[URI], 'delete')->name('delivery-country-delete');
                Route::post(DeliveryRestriction::ZIPCODE_ADD[URI], 'addZipCode')->name('add-zip-code');
                Route::delete(DeliveryRestriction::ZIPCODE_DELETE[URI], 'deleteZipCode')->name('zip-code-delete');
                Route::post(DeliveryRestriction::COUNTRY_RESTRICTION[URI], 'countryRestrictionStatusChange')->name('country-restriction-status-change');
                Route::post(DeliveryRestriction::ZIPCODE_RESTRICTION[URI], 'zipcodeRestrictionStatusChange')->name('zipcode-restriction-status-change');
            });
        });

        Route::group(['prefix' => 'email-templates', 'as' => 'email-templates.', 'middleware' => ['module:system_settings']], function () {
            Route::controller(EmailTemplatesController::class)->group(function () {
                Route::get('index', 'index')->name('index');
            });
        });
    });

    Route::group(['prefix' => 'system-settings', 'as' => 'system-settings.'], function () {
        Route::controller(SoftwareUpdateController::class)->group(function () {
            Route::get(SoftwareUpdate::VIEW[URI], 'index')->name('software-update');
            Route::post(SoftwareUpdate::VIEW[URI], 'update');
        });
    });

    Route::group(['prefix' => 'currency', 'as' => 'currency.', 'middleware' => ['module:System Setup']], function () {
        Route::controller(CurrencyController::class)->group(function () {
            Route::get(Currency::LIST[URI], 'index')->name('view')->middleware('actch');
            Route::post(Currency::ADD[URI], 'add')->name('store');
            Route::get(Currency::UPDATE[URI] . '/{id}', 'getUpdateView')->name('update');
            Route::post(Currency::UPDATE[URI] . '/{id}', 'update');
            Route::post(Currency::DELETE[URI], 'delete')->name('delete');
            Route::post(Currency::STATUS[URI], 'status')->name('status');
            Route::post(Currency::DEFAULT[URI], 'updateSystemCurrency')->name('system-currency-update');
        });
    });

    Route::group(['prefix' => 'addon', 'as' => 'addon.', 'middleware' => ['module:System Setup']], function () {
        Route::controller(AddonController::class)->group(function () {
            Route::get(AddonSetup::VIEW[URI], 'index')->name('index');
            Route::post(AddonSetup::PUBLISH[URI], 'publish')->name('publish');
            Route::post(AddonSetup::ACTIVATION[URI], 'activation')->name('activation');
            Route::post(AddonSetup::UPLOAD[URI], 'upload')->name('upload');
            Route::post(AddonSetup::DELETE[URI], 'delete')->name('delete');
        });
    });

    Route::group(['prefix' => 'react', 'as' => 'react.', 'middleware' => ['module:React Website Configuration']], function () {
        Route::controller(ReactSettingsController::class)->group(function () {
            Route::get(ReactSetup::VIEW[URI], 'index')->name('index');
            Route::post(ReactSetup::ACTIVATION[URI], 'activation')->name('activation');
        });
    });

    Route::group(['prefix' => 'social-login', 'as' => 'social-login.', 'middleware' => ['module:3rd Party']], function () {
        Route::controller(SocialLoginSettingsController::class)->group(function () {
            Route::get(SocialLoginSettings::VIEW[URI], 'index')->name('view');
            Route::post(SocialLoginSettings::UPDATE[URI] . '/{service}', 'update')->name('update');
            Route::post(SocialLoginSettings::APPLE_UPDATE[URI] . '/{service}', 'updateAppleLogin')->name('update-apple');
        });
    });

    Route::group(['prefix' => 'social-media-chat', 'as' => 'social-media-chat.', 'middleware' => ['module:3rd Party']], function () {
        Route::controller(SocialMediaChatController::class)->group(function () {
            Route::get(SocialMediaChat::VIEW[URI], 'index')->name('view');
            Route::post(SocialMediaChat::UPDATE[URI] . '/{service}', 'update')->name('update');
        });
    });

    Route::group(['prefix' => 'product-settings', 'as' => 'product-settings.', 'middleware' => ['module:Business Setup']], function () {
        Route::controller(BusinessSettingsController::class)->group(function () {
            Route::get(BusinessSettings::PRODUCT_SETTINGS[URI], 'getProductSettingsView')->name('index');
            Route::post(BusinessSettings::PRODUCT_SETTINGS[URI], 'updateProductSettings');
        });

        Route::controller(InhouseShopController::class)->group(function () {
            Route::get(InhouseShop::VIEW[URI], 'index')->name('inhouse-shop');
            Route::post(InhouseShop::VIEW[URI], 'update');
            Route::post(InhouseShop::TEMPORARY_CLOSE[URI], 'getTemporaryClose')->name('inhouse-shop-temporary-close');
            Route::post(InhouseShop::VACATION_ADD[URI], 'addVacation')->name('vacation-add');
        });
    });

    Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.', 'middleware' => ['module:Announcement Setup']], function () {
        Route::controller(BusinessSettingsController::class)->group(function () {
            Route::get(BusinessSettings::ANNOUNCEMENT[URI], 'getAnnouncementView')->name('announcement');
            Route::post(BusinessSettings::ANNOUNCEMENT[URI], 'updateAnnouncement');
        });
    });

    Route::group(['prefix' => 'file-manager', 'as' => 'file-manager.', 'middleware' => ['module:Page & Media']], function () {
        Route::controller(FileManagerController::class)->group(function () {
            Route::get(FileManager::VIEW[URI] . '/{folderPath?}', 'getFoldersView')->name('index');
            Route::get(FileManager::DOWNLOAD[URI] . '/{file_name}', 'download')->name('download');
            Route::post(FileManager::IMAGE_UPLOAD[URI], 'upload')->name('image-upload');
        });
    });

    Route::group(['prefix' => 'helpTopic', 'as' => 'helpTopic.', 'middleware' => ['module:Page & Media']], function () {
        Route::controller(HelpTopicController::class)->group(function () {
            Route::get(HelpTopic::LIST[URI], 'index')->name('list');
            Route::post(HelpTopic::ADD[URI], 'add')->name('add-new');
            Route::get(HelpTopic::STATUS[URI] . '/{id}', 'updateStatus')->name('status');
            Route::get(HelpTopic::UPDATE[URI] . '/{id}', 'getUpdateResponse')->name('update');
            Route::post(HelpTopic::UPDATE[URI] . '/{id}', 'update');
            Route::post(HelpTopic::DELETE[URI], 'delete')->name('delete');
        });
    });

    Route::group(['prefix' => 'refund-section', 'as' => 'refund-section.', 'middleware' => ['module:Refund Request']], function () {
        Route::group(['prefix' => 'refund', 'as' => 'refund.'], function () {
            Route::controller(RefundController::class)->group(function () {
                Route::get(RefundRequest::LIST[URI] . '/{status}', 'index')->name('list');
                Route::get(RefundRequest::EXPORT[URI] . '/{status}', 'exportList')->name('export');
                Route::get(RefundRequest::DETAILS[URI] . '/{id}', 'getDetailsView')->name('details');
                Route::post(RefundRequest::UPDATE_STATUS[URI], 'updateRefundStatus')->name('refund-status-update');
            });
        });
    });

    Route::group(['prefix' => 'tour_and_travels', 'as' => 'tour_and_travels.'], function () {
        Route::controller(TourAndTravelController::class)->group(function () {
            Route::get(TourAndTravelPath::ADDTRAVEL[URI], 'AddTravels')->name('add-traveller');
            Route::post(TourAndTravelPath::ADDTRAVEL[URI], 'AddTraveller')->name('insert-traveller');
            Route::get(TourAndTravelPath::TRAVELLIST[URI], 'TravellerList')->name('traveller-list');
            Route::post(TourAndTravelPath::TRAVELSTATUS[URI], 'StatusUpdate')->name('status-update');
            Route::get(TourAndTravelPath::TRAVELUPDATE[URI] . '/{id}', 'TravellerUpdate')->name('update');
            Route::post(TourAndTravelPath::TRAVELUPDATE[URI] . '/{id}', 'Travelleredit')->name('edit');
            Route::get(TourAndTravelPath::TRAVELVIEW[URI] . '/{id}', 'TravellerView')->name('information');
            Route::delete(TourAndTravelPath::TRAVELDELETE[URI] . '/{id}', 'TravellerDelete')->name('traveller-delete');
            Route::post(TourAndTravelPath::TRAVELSTATUS[URL] . '/{id}', 'TravelleApproval')->name('traveller-company-status');
            Route::post(TourAndTravelPath::VENDORTOURCOMMISSION[URI] . '/{id}', 'VendorTourCommission')->name('vendor_commission_update');

            Route::group(['prefix' => 'cab', 'as' => 'cab.'], function () {
                Route::post('cab-store', 'CabStore')->name('cab-store');
                Route::post('cab-status-update', 'CabStatusUpdate')->name('cab_status-update');
                Route::get('cab-image-remove/{id}/{name}', 'CabImageRemove')->name('delete-image');
                Route::get('cab-update/{id}', 'CabUpdate')->name('cab-update');
                Route::post('cab-edit', 'CabEdit')->name('cab-edit');
                Route::post('cab-delete', 'CabTravellerDelete')->name('traveller-cab-delete');
            });
            Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
                Route::post('driver-store', 'DriverStore')->name('driver-store');
                Route::post('driver-status-update', 'DriverStatusUpdate')->name('driver_status-update');
                Route::post('traveller-driver-delete', 'DriverDetele')->name('traveller-driver-delete');
                Route::get('driver-update/{id}', 'DriverUpdate')->name('driver-update');
                Route::post('driver-edit', 'DriverEdit')->name('driver-edit');
            });
        });
    });
    Route::group(['prefix' => "tour_package", 'as' => "tour_package."], function () {
        Route::controller(TourPackageController::class)->group(function () {
            Route::get(TourPackagePath::ADDPACKAGE[URI], 'PackageList')->name('view');
            Route::post(TourPackagePath::ADDPACKAGE[URI], 'PackageAdd')->name('store');
            Route::post(TourPackagePath::ADDHOTELPACKAGE[URI], 'HotelPackageAdd')->name('add-hotel-package');
            Route::get(TourPackagePath::ADDHOTELPACKAGE[URI], 'getHotelspackage')->name('get-hotel-package');
            Route::delete(TourPackagePath::ADDHOTELPACKAGE[URI], 'deleteHotelspackage')->name('delete-hotel-package');
            Route::post(TourPackagePath::PACKAGESTATUS[URI], 'PackageStatus')->name('status-update');
            Route::get(TourPackagePath::PACKAGEUPDATE[URI] . '/{id}', 'PackageUpdate')->name('update');
            Route::post(TourPackagePath::PACKAGEUPDATE[URI] . '/{id}', 'PackageEdit')->name('edit');
            Route::post(TourPackagePath::PACKAGEDELETE[URI], 'PackageDelete')->name('delete');
        });
    });
    Route::group(['prefix' => "tour_vehicle_setting", 'as' => "tour_vehicle_setting."], function () {
        Route::controller(TourCabController::class)->group(function () {
            Route::get(TourCabPath::VEHICLELIST[URI], 'VehicleListing')->name('view');
            Route::get(TourCabPath::VEHICLELIST[URL], 'VehicleListingFilters')->name('vehicle-list-filter');
            Route::get(TourCabPath::VEHICLEADD[URI], 'VehicleAdd')->name('add');
            Route::post(TourCabPath::VEHICLEADD[URL], 'VehicleStore')->name('store');
            Route::get(TourCabPath::VEHICLEUPDATE[URL] . '/{id}', 'VehicleEdit')->name('vehicel-edit');
            Route::post(TourCabPath::VEHICLEUPDATE[URI] . '/{id}', 'VehicleUpdate')->name('vehicel-update');
            Route::post(TourCabPath::VEHICLEDELETE[URI] . '/{id}', 'VehicleDelete')->name('vehicle-delete');
            Route::post(TourCabPath::VEHICLESTATUSUPDATE[URI], 'VehicleStatusUpdate')->name('status-update');
        });
    });
    Route::group(['prefix' => "tour_cab_service", 'as' => "tour_cab_service."], function () {
        Route::controller(TourCabController::class)->group(function () {
            Route::get(TourCabPath::ADDCAB[URI], 'CabList')->name('view');
            Route::post(TourCabPath::VEHICLECATEGORYGET[URI], 'VehicleCategoryGet')->name('vehicle_category');
            Route::post(TourCabPath::ADDCAB[URI], 'CabAdd')->name('store');
            Route::post(TourCabPath::CABSTATUS[URI], 'CabStatus')->name('status-update');
            Route::get(TourCabPath::CABUPDATE[URI] . '/{id}', 'CabUpdate')->name('update');
            Route::post(TourCabPath::CABUPDATE[URI] . '/{id}', 'CabEdit')->name('edit');
            Route::post(TourCabPath::CABDELETE[URI], 'CabDelete')->name('delete');
        });
    });

    Route::controller(SelfDrivingController::class)->group(function () {
        Route::group(['prefix' => "driving-policy", 'as' => "driving-policy."], function () {
            Route::get(SelfDrivingPath::SELFDRIVINGADDPOLICY[URI], 'AddDrivingPolicy')->name('driving-policy');
            Route::post(SelfDrivingPath::SELFDRIVINGADDPOLICY[URI], 'StoreDrivingPolicy')->name('policy-store');
            Route::get(SelfDrivingPath::SELFDRIVINGPOLICYFILTER[URI], 'DrivingPolicyFilter')->name('policy-list-filter');
            Route::post(SelfDrivingPath::SELFDRIVINGPOLICYSTATUSUPDATE[URI], 'DrivingPolicyStatusUpdate')->name('status-update');
            Route::post(SelfDrivingPath::SELFDRIVINGPOLICYDELETE[URI] . "/{id}", 'DrivingPolicyDelete')->name('policy-delete');
            Route::get(SelfDrivingPath::SELFDRIVINGPOLICYDUPDATE[URI] . "/{id}", 'DrivingPolicyEdit')->name('policy-edit');
            Route::post(SelfDrivingPath::SELFDRIVINGPOLICYDUPDATE[URL] . '/{id}', 'DrivingPolicyUpdate')->name('policy-update');
        });
        Route::group(['prefix' => "driving-cancellation-policy", 'as' => "driving-cancellation-policy."], function () {
            Route::get(SelfDrivingPath::CANCELLATIONPOLICY[URI], 'AddCancellationpolicy')->name('driving-cancellation-policy');
            Route::post(SelfDrivingPath::CANCELLATIONPOLICY[URI], 'StoreCancellationpolicy')->name('cancellation-store');
            Route::get(SelfDrivingPath::CANCELLATIONPOLICYFILTER[URI], 'CancellationpolicyFilter')->name('cancellation-list-filter');
            Route::post(SelfDrivingPath::CANCELLATIONPOLICYSTATUSUPDATE[URI], 'CancellationpolicyStatusUpdate')->name('status-update');
            Route::post(SelfDrivingPath::CANCELLATIONPOLICYDELETE[URI] . '/{id}', 'CancellationpolicyDelete')->name('cancellation-delete');
            Route::get(SelfDrivingPath::CANCELLATIONPOLICYUPDATE[URI] . '/{id}', 'CancellationpolicyEdit')->name('cancellation-edit');
            Route::post(SelfDrivingPath::CANCELLATIONPOLICYUPDATE[URL] . '/{id}', 'CancellationpolicyUpdate')->name('cancellation-update');
        });
        Route::group(['prefix' => "self-driving-management", 'as' => "self-driving-management."], function () {
            Route::get(SelfDrivingPath::SELFDRIVINGADD[URI], 'AddSelfDriving')->name('self-driving-add');
            Route::post(SelfDrivingPath::SELFDRIVINGADD[URL], 'StoreSelfDriving')->name('self-driving-store');
            Route::get(SelfDrivingPath::SELFDRIVINGLIST[URI], 'SelfDrivingList')->name('self-driving-list');
            Route::get(SelfDrivingPath::SELFDRIVINGLISTFILTER[URI], 'SelfDrivingListFilter')->name('self-drivinglist-filter');
            Route::post(SelfDrivingPath::SELFDRIVINGSTATUSUPDATE[URI], 'SelfDrivingStatusUpdate')->name('status-update');
            Route::post(SelfDrivingPath::SELFDRIVINGDELETE[URI], 'SelfDrivingDelete')->name('self-driving-delete');
            Route::get(SelfDrivingPath::SELFDRIVINGUPDATE[URI] . "/{id}", 'SelfDrivingEdit')->name('self-driving-edit');
            Route::post(SelfDrivingPath::SELFDRIVINGUPDATE[URL] . "/{id}", 'SelfDrivingUpdate')->name('self-driving-update');
            Route::post(SelfDrivingPath::GETCABS[URI], 'GetCabList')->name('get-cab-list');
            Route::get(SelfDrivingPath::SELFVEHICLELEAD[URI], 'SelfVehicleLead')->name('self-driving-lead');
            Route::get(SelfDrivingPath::SELFVEHICLELEAD[URL], 'SelfVehicleLeadFilter')->name('self-driving-lead-filter');
            Route::get(SelfDrivingPath::SELFVEHICLELEADDELETE[URI] . "/{id}", 'SelfVehicleLeadDelete')->name('leads-delete');
            Route::post(SelfDrivingPath::SELFVEHICLELEFOLLOWUP[URI], 'SelfVehicleFollowUp')->name('self-vehicle-follow-up');
            Route::get(SelfDrivingPath::SELFVEHICLELEFOLLOWUP[URL] . '/{id}', 'SelfVehicleGetFollowUp')->name('self-vehicle-get-follow-up');
            Route::get(SelfDrivingPath::SELFVEHICLELEFOLLOWUP[URL] . '/{id}', 'SelfVehicleWhatsappMessage')->name('self-vehicle-whatsapp-message');

            Route::get(SelfDrivingPath::SELFVEHICLELEORDERPENDING[URI], 'SelfVehiclePendingOrder')->name('self-vehicle-pending-order');
            Route::get(SelfDrivingPath::SELFVEHICLELEORDERPENDING[URL], 'SelfVehiclePendingOrderFilter')->name('self-vehicle-pending-filter');
            Route::get(SelfDrivingPath::SELFVEHICLELEORDERVIEW[URI] . '/{id}', 'SelfVehicleOrderView')->name('order-view-details');
            Route::get(SelfDrivingPath::SELFVEHICLELEORDERCONFIRM[URI], 'SelfVehicleConfirmOrder')->name('self-vehicle-confirm-order');
            Route::get(SelfDrivingPath::SELFVEHICLELEORDERPICKUP[URI], 'SelfVehiclePickUpOrder')->name('self-vehicle-pickup-order');
            Route::get(SelfDrivingPath::SELFVEHICLELEORDERDROP[URI], 'SelfVehicleDropOrder')->name('self-vehicle-droup-order');
        });
    });
    Route::group(['prefix' => "tour_type", 'as' => "tour_type."], function () {
        Route::controller(TourTypeController::class)->group(function () {
            Route::get(TourTypePath::ADDTYPE[URI], 'TypeList')->name('view');
            Route::post(TourTypePath::ADDTYPE[URI], 'TypeAdd')->name('store');
            Route::post(TourTypePath::TYPESTATUS[URI], 'TypeStatus')->name('status-update');
            Route::get(TourTypePath::TYPEUPDATE[URI] . '/{id}', 'TypeUpdate')->name('update');
            Route::post(TourTypePath::TYPEUPDATE[URI] . '/{id}', 'TypeEdit')->name('edit');
            Route::post(TourTypePath::TYPEDELETE[URI], 'TypeDelete')->name('delete');
        });
    });

    Route::group(['prefix' => "tour-refund-policy", 'as' => "tour-refund-policy."], function () {
        Route::controller(TourRefundPolicyController::class)->group(function () {
            Route::get(TourRePolicyPath::ADDPOLICY[URI], 'PolicyList')->name('list');
            Route::post(TourRePolicyPath::ADDPOLICY[URI], 'PolicyAdd')->name('store');
            Route::post(TourRePolicyPath::POLICYSTATUS[URI], 'PolicyStatus')->name('status-update');
            Route::get(TourRePolicyPath::POLICYUPDATE[URI] . '/{id}', 'PolicyUpdate')->name('update');
            Route::post(TourRePolicyPath::POLICYUPDATE[URI] . '/{id}', 'PolicyEdit')->name('edit');
            Route::post(TourRePolicyPath::POLICYDELETE[URI], 'PolicyDelete')->name('delete');
        });
    });

    Route::group(['prefix' => 'tour_visits', 'as' => 'tour_visits.'], function () {
        Route::controller(TourVisitController::class)->group(function () {
            Route::get(TourVisitPath::ADDTRAVEL[URI], 'AddTour')->name('add-tour');
            Route::post(TourVisitPath::ADDTRAVEL[URL], 'SaveTour')->name('insert-tour');
            Route::get(TourVisitPath::TRAVELLIST[URI], 'TourList')->name('tour-list');
            Route::get(TourVisitPath::TRAVELLIST[URL], 'TourListFilter')->name('tour-list-filter');
            Route::post(TourVisitPath::TRAVELSTATUS[URI], 'StatusUpdate')->name('status-update');
            Route::get(TourVisitPath::TRAVELUPDATE[URI] . '/{id}', 'TourUpdate')->name('update');
            Route::post(TourVisitPath::TRAVELUPDATE[URI], 'Touredit')->name('edit');
            Route::delete(TourVisitPath::TRAVELDELETE[URI] . '/{id}', 'TourDelete')->name('tour-delete');
            Route::get(TourVisitPath::TRAVELVIEW[URI] . '/{id}', 'TourView')->name('overview');

            Route::get(TourVisitPath::IMAGEREMOVE[URI] . '/{id}/{name}', 'TourImageRemove')->name('delete-image');

            // visit route map
            Route::get(TourVisitPath::VISIT[URI] . '/{id}', 'VisitList')->name('add-visit');
            Route::post(TourVisitPath::VISIT[URI], 'VisitStore')->name('visit_place_store');
            Route::post(TourVisitPath::VISITSTATUS[URI], 'VisitPlaceStatus')->name('place-status-update');
            Route::post(TourVisitPath::VISITDELETE[URI], 'VisitPlaceDelete')->name('delete-place');

            Route::get('visit-update' . '/{id}', 'VisitPlaceUpdate')->name('visit-update');
            Route::post('visit-update', 'VisitPlaceEdit')->name('visit_place_edit');
            Route::get('visit-delete-image' . '/{id}/{name}', 'VisitPlaceImageRemove')->name('visit-delete-image');

            // leads 
            Route::get(TourVisitPath::LEADS[URL], 'TourLeads')->name('leads');
            Route::get(TourVisitPath::LEADLISTFILTER[URI], 'TourLeadListFilter')->name('lead-list-filter');
            Route::get(TourVisitPath::LEADSDELETE[URI] . '/{id}', 'TourLeadDelete')->name('leads-delete');
            Route::get(TourVisitPath::LEADSCLOSEUPDATE[URI] . '/{id}', 'TourLeadCloseupdate')->name('leads-close-update');
            Route::post(TourVisitPath::LEADSGET[URI], 'TourLeadsFollowUp')->name('tour-follow-up');
            Route::get(TourVisitPath::LEADSGET[URI] . '/{id}', 'TourLeadsFollow')->name('tour-follow-list');
            Route::post('company-booking-order-get', 'CompanyBookingGet')->name('company-booking-order-get');
            Route::post('company-booking-settlement', 'CompanyBookingSettlement')->name('company-booking-settlement');
            Route::post('comment-status-update', 'CommentStatusUpdate')->name('comment-status-update');
            Route::post('commission-update' . '/{id}', 'CommissionUpdate')->name('commission_update');
            Route::get(TourVisitPath::LEADMESSAGE[URL] . '/{id}', 'TourLeadMessages')->name('tour-whatsapp-message');
            Route::get(TourVisitPath::CREATELEADADMIN[URL], 'TourLeadCreateForm')->name('tour-admin-lead-create');
            Route::get(TourVisitPath::UPDATELEADADMIN[URL]."/{id}", 'TourLeadEditForm')->name('tour-admin-lead-edit');
            Route::post(TourVisitPath::UPDATELEADADMIN[URI]."/{id}", 'TourLeadUpdateForm')->name('tour-admin-lead-update');

            Route::get("customer-tour-remaining-pay/{id}", 'CustomerTourRemainingPay')->name('customer-tour-remaining-pay');
        });
    });

    Route::group(['prefix' => 'tour-lead', 'as' => 'tour-lead.'], function () {
        Route::controller(TourVisitController::class)->group(function () {
            Route::post(TourVisitPath::CREATELEADHTMLGET[URL], 'TourGetFormDiv')->name('get-tour-info-div');
            Route::post(TourVisitPath::CREATELEADADMIN[URI], 'TourLeadSave')->name('tour-lead-save');
        });
    });

    Route::group(['prefix' => 'tour-visits-booking', 'as' => 'tour-visits-booking.'], function () {
        Route::controller(TourBookingController::class)->group(function () {
            Route::get(TourBookingPath::ALL[URI], 'BookingList')->name('all-list');
            Route::get(TourBookingPath::PENDING[URI], 'BookingPending')->name('pending-booking');
            Route::get(TourBookingPath::CONFIRM[URI], 'BookingConfirm')->name('confirm-booking');
            Route::get(TourBookingPath::COMPLETED[URI], 'BookingCompleted')->name('complete-booking');
            Route::get(TourBookingPath::CANCEL[URI], 'BookingCancel')->name('cancel-booking');
            Route::get(TourBookingPath::DETAILS[URI] . '/{id}', 'BookingDetails')->name('user-booking-details');
            Route::post(TourBookingPath::ASSIGNEDCAB[URI], 'AssignedCab')->name('assigned-cab');
            Route::post(TourBookingPath::UPDATE_DATE[URI], 'BookingDateUpdate')->name('update-booking-date');
            Route::post(TourBookingPath::REFUND[URI], 'BookingRefund')->name('refund');
        });
    });

    Route::group(['prefix' => 'tour_withdrawal', 'as' => 'tour_withdrawal.'], function () {
        Route::controller(TourBookingController::class)->group(function () {
            Route::get('/', 'WithdrawalList')->name('index');
            Route::get('view/{id}', 'WithdrawalReqView')->name('withdraw-request-view');
            Route::get('request-reject/{id}', 'WithdrawalReqReject')->name('rejects');

            Route::get('create-contact/{id}/{type}', 'RazorpaycreateContact')->name('payment-req-approval-admin');
        });
    });
    // vendor permission module
    Route::group(['prefix' => 'permission-module', 'as' => 'permission-module.'], function () {
        Route::controller(VendorPermissionModule::class)->group(function () {
            Route::post('phone-check', 'PhoneCheck')->name('phone-check');
            Route::get(VendorPermissionPath::MODULE[URI], 'AddPerMissionModule')->name('module');
            Route::post(VendorPermissionPath::MODULE[URI], 'UpdatePerMissionModule')->name('update-module');
            Route::get(VendorPermissionPath::ROLE[URI], 'AddPermissionRoles')->name('role');
            Route::post(VendorPermissionPath::ROLE[URI], 'StorePermissionRoles')->name('add-role');
            Route::post(VendorPermissionPath::ROLESTATUS[URI], 'PermissionRolesStatus')->name('vendor-role-status');
            Route::post(VendorPermissionPath::ROLEDELETE[URI], 'PermissionRolesDelete')->name('role-delete');
            Route::get(VendorPermissionPath::ROLEUPDATE[URI] . "/{id}", 'PermissionRolesUpdate')->name('role-update');
            Route::post(VendorPermissionPath::ROLEUPDATE[URI], 'PermissionRolesEdit')->name('role-edit');
            Route::get(VendorPermissionPath::LIST[URI], 'ListPermissions')->name('list');

            Route::get(VendorPermissionPath::USERLIST[URI], 'UserList')->name('user-list');

            Route::post(VendorPermissionPath::USERSTATUS[URI], 'UserStatusUpdate')->name('vendor-user-status-update');
            Route::get(VendorPermissionPath::USERSTATUS[URL], 'UserDeleted')->name('vendor-user-delete');
        });
    });

    Route::group(['prefix' => 'book', 'as' => 'book.', 'controller' => ServiceBookingController::class], function () {
        Route::get('type', 'type')->name('type');
        Route::get('pooja', 'pooja')->name('pooja');
        Route::get('package', 'package')->name('package');
        Route::post('order/place', 'order_place')->name('order.place');
        Route::get('sankalp', 'sankalp')->name('sankalp');
        // Route::get('pooja-payment-success', 'pooja_payment_success');
        // Route::post('service', 'service_store')->name('service');

        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('check', 'user_check')->name('check');
            Route::post('register', 'user_register')->name('register');
        });
    });

    Route::group(['prefix' => 'general/review', 'as' => 'general.review.', 'controller' => GeneralReviewController::class], function () {
        Route::get('add', 'add')->name('add');
        Route::post('store', 'store')->name('store');
        Route::get('list', 'list')->name('list');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update', 'update')->name('update');
        Route::get('status', 'status')->name('status');
        Route::delete('delete/{id}', 'delete')->name('delete');
        Route::get('tour-event-temple-all-review-filter','TourEventTempleAllReviewFilter')->name('tour-event-temple-all-review-filter');
    });

    Route::group(['prefix' => 'collector', 'as' => 'collector.', 'controller' => CollectorController::class], function () {
        // Route::get('add', 'add')->name('add');
        Route::get('list', 'list')->name('list');
        Route::get('get-temple', 'get_temple')->name('get-temple');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::get('get-selected-temple', 'get_selected_temple')->name('get-selected-temple');
        Route::post('update', 'update')->name('update');
        Route::get('view/{id}', 'view')->name('view');
        Route::post('status', 'status')->name('status');
    });

    Route::group(['prefix' => 'sdm', 'as' => 'sdm.', 'controller' => CollectorController::class], function () {
        Route::get('list', 'sdm_list')->name('list');
        Route::get('get-temple', 'sdm_get_temple')->name('get-temple');
        Route::post('store', 'sdm_store')->name('store');
        Route::get('edit/{id}', 'sdm_edit')->name('edit');
        Route::get('get-selected-temple', 'sdm_get_selected_temple')->name('get-selected-temple');
        Route::post('update', 'sdm_update')->name('update');
        Route::get('view/{id}', 'sdm_view')->name('view');
        Route::post('status', 'sdm_status')->name('status');

        Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
            Route::get('list', 'employee_list')->name('list');
            Route::get('get-temple', 'employee_get_temple')->name('get-temple');
            Route::post('store', 'employee_store')->name('store');
            Route::get('edit/{id}', 'employee_edit')->name('edit');
            Route::get('get-selected-temple', 'employee_get_selected_temple')->name('get-selected-temple');
            Route::post('update', 'employee_update')->name('update');
            Route::get('view/{id}', 'employee_view')->name('view');
            Route::post('status', 'employee_status')->name('status');
        });
    });
});
