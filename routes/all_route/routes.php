<?php

use App\Enums\ViewPaths\AllPaths\EventPath;
use App\Enums\ViewPaths\AllPaths\LoginPath;
use App\Enums\ViewPaths\AllPaths\SelfDrivingPath;
use App\Enums\ViewPaths\AllPaths\TourPath;
use App\Enums\ViewPaths\AllPaths\TrusteesPath;
use App\Http\Controllers\Admin\DonateTrustController;
use App\Http\Controllers\AllController\CommanController;
use App\Http\Controllers\AllController\EventOrgController;
use App\Http\Controllers\AllController\SelfDrivingController;
use App\Http\Controllers\AllController\TourController;
use App\Http\Controllers\AllController\TrusteesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'event-vendor', 'as' => 'event-vendor.'], function () {

    Route::group(['middleware' => [\App\Http\Middleware\EventOrgMiddleware::class]], function () {
        Route::controller(EventOrgController::class)->group(function () {
            Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
                Route::get(EventPath::DASHBOARD[URL], 'dashboard')->name('index');
            });

            Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['employeemodules:Profile']], function () {
                Route::get(EventPath::PROFILEUPDATE[URL] . '/{id}', 'profileUpdate')->name('update');
                Route::post('profile-updates', 'profileUpdate2')->name('update2');
                Route::patch(EventPath::PROFILEUPDATE[URI] . '/{id}', 'profileEdit')->name('profile-edit');
            });

            Route::group(['prefix' => 'artist', 'as' => 'artist.', 'middleware' => ['employeemodules:Artist Management']], function () {
                Route::get(EventPath::ADDARTIST[URL], 'AddArtist')->name('add-artist');
                Route::post(EventPath::ADDARTIST[URI], 'StoreArtist')->name('store-artist');
                Route::get(EventPath::ARTISTLIST[URI], 'ArtistList')->name('list');
                Route::get(EventPath::ARTISTUPDATE[URI] . '/{id}', 'ArtistEdit')->name('artist_update');
                Route::post(EventPath::ARTISTUPDATE[URI] . '/{id}', 'ArtistUpdate')->name('update-artist');
            });

            Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['employeemodules:Coupon Management']], function () {
                Route::get(EventPath::ADDCOUPON[URL], 'AddCoupon')->name('add-coupon');
                Route::post(EventPath::ADDCOUPON[URI], 'StoreCoupon')->name('store-coupon');
                Route::get(EventPath::QUICK_VIEW[URL],'QuickView')->name('quick-view');
                Route::get(EventPath::COUPONLIST[URI], 'CouponList')->name('list');
                Route::get(EventPath::COUPONUPDATE[URI] . '/{id}', 'CouponEdit')->name('coupon_update');
                Route::delete(EventPath::COUPONDELETE[URI] . '/{id}', 'CouponDelete')->name('delete');
                Route::post(EventPath::COUPONUPDATE[URI] . '/{id}', 'CouponUpdate')->name('update-coupon');
                Route::get(EventPath::COUPONUPDATESTATUS[URI] . '/{id}', 'CouponUpdateStatus')->name('update-status');
            });

            Route::group(['prefix' => 'sponsor', 'as' => 'sponsor.', 'middleware' => ['employeemodules:Sponsor Management']], function () {
                Route::get(EventPath::ADDSPONSOR[URL], 'AddSponsor')->name('add-sponsor');
                Route::post(EventPath::ADDSPONSOR[URI], 'StoreSponsor')->name('store-sponsor');
                Route::get(EventPath::SPONSORLIST[URI], 'SponsorList')->name('sponsor-list');
                Route::get(EventPath::SPONSORLIST[URL], 'SponsorListFilter')->name('sponsor-list-filter');
                Route::get(EventPath::SPONSORUPDATE[URI] . '/{id}', 'SponsorEdit')->name('sponsor_update');
                Route::get(EventPath::SPONSORDELETE[URI] . '/{id}', 'SponsorDelete')->name('delete');
                Route::post(EventPath::SPONSORUPDATE[URI] . '/{id}', 'SponsorUpdate')->name('update-sponsor');
                Route::post(EventPath::SPONSORUPDATESTATUS[URI] . '/{id}', 'SponsorUpdateStatus')->name('update-status');
            });

            Route::group(['prefix' => 'pos', 'as' => 'pos.', 'middleware' => ['employeemodules:POS Management']], function () {
                Route::get(EventPath::ADDPOS[URL], 'AddPos')->name('add-pos');
                Route::post(EventPath::POSVENUELIST[URL], 'PosgetVenuelist')->name('get-venue-list');
                Route::post(EventPath::POSVENUELIST[URI], 'PosgetSponsorlist')->name('get-sponsor-list');
                Route::post(EventPath::ADDPOS[URI], 'StoreSponsor')->name('store-sponsor');
                Route::get(EventPath::POSLIST[URI], 'SponsorList')->name('pos-list');
                Route::get(EventPath::POSLIST[URL], 'SponsorListFilter')->name('sponsor-list-filter');
                Route::get(EventPath::POSVIEW[URI] . '/{id}', 'SponsorEdit')->name('sponsor_update');
            });

            Route::group(['prefix' => 'event-management', 'as' => 'event-management.', 'middleware' => ['employeemodules:Event Management']], function () {
                Route::get(EventPath::EVENTMANAG[URL], 'EventAdd')->name('add-event');
                Route::get(EventPath::EVENTMANAGAUDITORIUM[URL]."/{id}", 'EventAddAuditorium')->name('add-auditorium');
                Route::post(EventPath::EVENTMANAGAUDITORIUM[URL]."/{id}", 'EventStoreAuditorium')->name('store-auditorium');
                Route::post(EventPath::EVENTMANAG[URI], 'EventStore')->name('store-event');

                Route::get(EventPath::EVENTMANAGLIST[URI], 'EventList')->name('event-list');
                Route::get(EventPath::EVENTMANAGPENDING[URI], 'EventPending')->name('event-pending');
                Route::get(EventPath::EVENTMANAGUPCOMMING[URI], 'EventUpcomming')->name('event-upcomming');
                Route::get(EventPath::EVENTMANAGRUNNING[URI], 'EventRunning')->name('event-running');
                Route::get(EventPath::EVENTMANAGCOMPLATE[URI], 'EventComplate')->name('event-complate');
                Route::get(EventPath::EVENTMANAGCANCEL[URI], 'EventCancel')->name('event-cancel');

                Route::get(EventPath::EVENTMANAGUPDATE[URL] . '/{id}', 'EventUpdate')->name('event-update');
                Route::post(EventPath::EVENTMANAGUPDATE[URI] . '/{id}', 'EventEdits')->name('event-edit');

                Route::get(EventPath::EVENTOVERVIEW[URI] . '/{id}', 'EventDetailsOverview')->name('event-detail-overview');
            });
            Route::group(['prefix' => 'event-order', 'as' => 'event-order.', 'middleware' => ['employeemodules:Order Management']], function () {
                Route::get(EventPath::EVENTORDERRUNING[URI], 'EventOrderRunning')->name('running');
                Route::get(EventPath::EVENTORDERCOMPLATE[URI], 'EventOrderComplate')->name('complate');
                Route::get(EventPath::EVENTORDERRUNNING[URI], 'EventOrderRefund')->name('refund');
                Route::post(EventPath::EVENTORDERVIEWS[URI], 'EventOrderView')->name('event-order-view');
            });

            Route::group(['prefix' => 'messages', 'as' => 'messages.', 'middleware' => ['employeemodules:Support Management']], function () {
                Route::get(EventPath::EVENTINBOX[URL], "EventSupportTicket")->name('index');
                Route::post(EventPath::EVENTINBOX[URL], "EventSupportTicketStore")->name('store-inbox');
                Route::post(EventPath::EVENTINBOXSTATUS[URL], "EventSupportTicketStatus")->name('status');
                Route::get(EventPath::EVENTINBOXVIEW[URL] . '/{id}', "EventSupportTicketView")->name('singleTicket');
                Route::post(EventPath::EVENTINBOXVIEW[URL] . "/{id}", "EventSupportTicketReplay")->name('replay');
            });

            // from admin
            Route::group(['prefix' => 'message', 'as' => 'message.', 'middleware' => ['employeemodules:Support Management']], function () {
                Route::get(EventPath::EVENTADMININBOX[URL], "AdminSupportTicket")->name('index');
                Route::post(EventPath::EVENTADMININBOX[URL], "EventSupportTicketStore")->name('store-inbox');

                Route::get(EventPath::EVENTINBOXVIEW[URL] . '/{id}', "EventSupportTicketView")->name('singleTicket');
                Route::post(EventPath::EVENTINBOXVIEW[URL] . "/{id}", "EventSupportTicketReplay")->name('replay');
            });

            Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.', 'middleware' => ['employeemodules:Transaction Management']], function () {
                Route::get(EventPath::WITHDRAW[URL], "withdrawRequests")->name('index');
                Route::post("get-vendor-info", 'GetVendorInfo')->name('get-vendor-data');
                Route::post("payment-request-send", 'AddWithdrawalRequest')->name('add-request-admin-send');
                Route::get("withdraw-request-view/{id}", 'WithdrawalRequestView')->name('withdraw-request-view');
            });
            Route::group(['prefix' => 'transaction', 'as' => 'transaction.', 'middleware' => ['employeemodules:Transaction Management']], function () {
                Route::get('/', "transactionHistory")->name('index');
            });


            Route::group(['prefix' => 'fcm-update', 'as' => 'fcm-update.'], function () {
                Route::post('owner', "FCMUpdates")->name('owners');
                Route::get('delete', "FCMUpdatesdelete")->name('delete');
            });

            Route::group(['prefix' => 'qr-code-verify', 'as' => 'qr-code-verify.', 'middleware' => ['employeemodules:Qr Management']], function () {
                Route::get(EventPath::QRTODAYLIST[URL], "TodayEventList")->name('index');
                Route::get(EventPath::QRTODAYINFORMATION[URL] . "/{id}/{venue}", "EventQRVerify")->name('view');
                Route::post(EventPath::QRTODAYSUBMIT[URL] . "/{id}/{num}", "EventQRVerifySubmit")->name('verify');
                Route::post(EventPath::QRTODAYSUBMIT[URL] . "/{id}", "EventQRVerifySubmit");
            });
            Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['employeemodules:Employee']], function () {
                Route::get(EventPath::ADDEMPLOYEE[URL], 'AddEmployee')->name('add-employee');
                Route::post(EventPath::ADDEMPLOYEE[URI], 'StoreEmployee')->name('store-employee');
                Route::get(EventPath::EMPLOYEELIST[URI], 'EmployeeList')->name('employee-list');
                Route::get(EventPath::EMPLOYEEUPDATE[URI] . '/{id}', 'EmployeeEdit')->name('employee-edit');
                Route::post(EventPath::EMPLOYEEUPDATE[URI] . '/{id}', 'EmployeeUpdate')->name('employee-update');

                Route::post(EventPath::EMPLOYEESTATUSUPDATE[URI], 'EmployeeStatusUpdate')->name('employee-status-update');
                Route::post(EventPath::EMPLOYEESTATUSUPDATE[URL], 'Employeedelete')->name('employee_delete');

                Route::post(EventPath::CHECHEMAILPHONE[URI], 'CheckEmailPhone')->name('check-value');
            });
        });
    });
});

Route::group(['prefix' => 'tour-vendor', 'as' => 'tour-vendor.'], function () {

    Route::group(['middleware' => [\App\Http\Middleware\TourMiddleware::class]], function () {
        Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::DASHBOARD[URL], 'index')->name('index');

                Route::post(TourPath::DASHBOARD[URL], 'withdrawRequestadd')->name('withdraw-request');
                Route::get('order-statistics', 'orderStatistics')->name('order-statistics');
            });
        });

        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::PROFILEUPDATE[URL] . '/{id}', 'profileUpdate')->name('update');
                Route::post(TourPath::PROFILEUPDATE[URI] . '/{id}', 'profileEdit')->name('profile-edit');
                Route::post("update-password" . '/{id}', 'PasswordChange')->name('password-update');
            });
        });

        Route::group(['prefix' => 'tour_cab_management', 'as' => 'tour_cab_management.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::CABLIST[URL], 'CabList')->name('cab-list');
                Route::post(TourPath::CABLIST[URI], 'CabStore')->name('cab-store');
                Route::post(TourPath::CABSTATUSUPDATE[URL], 'CabStatusUpdate')->name('cab_status-update');
                Route::get(TourPath::CABUPDATE[URL] . "/{id}", 'CabUpdate')->name('cab-update');
                Route::post(TourPath::CABUPDATE[URL], 'CabEdit')->name('cab-edit');
                Route::get(TourPath::CABUPDATE[URI] . "/{id}/{name}", 'CabRemoveImage')->name('cab-delete-image');
                Route::post(TourPath::CABTRAVELLERDELETE[URL], 'CabTravellerDelete')->name('traveller-cab-delete');

                Route::get(TourPath::DRIVERLIST[URL], 'CabDriverList')->name('cab-driver-list');
                Route::post(TourPath::DRIVERLIST[URI], 'DriverStore')->name('driver-store');
                Route::post(TourPath::DRIVERSTATUSUPDATE[URL], 'DriverStatusUpdate')->name('driver_status-update');
                Route::post(TourPath::DRIVERDETELE[URL], 'DriverDetele')->name('traveller-driver-delete');
                Route::get(TourPath::DRIVERUPDATE[URL] . "/{id}", 'DriverUpdate')->name('driver-update');
                Route::post(TourPath::DRIVERUPDATE[URL], 'DriverEdit')->name('driver-edit');
            });
        });

        Route::group(['prefix' => 'lead', 'as' => 'lead.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::TOURLEADADD[URL], 'AddVendorLead')->name('add-lead');
                Route::post(TourPath::TOURLEADADD[URL], 'SaveVendorLead')->name('tour-lead-save');
                Route::get(TourPath::TOURLEADLIST[URL], 'LeadVendorList')->name('lead-list');
                Route::get(TourPath::TOURLEADLISTFILTER[URL], 'TourLeadListFilter')->name('lead-list-filter');
                Route::get(TourPath::LEADSCLOSEUPDATE[URI] . '/{id}', 'TourLeadCloseupdate')->name('leads-close-update');
                Route::post(TourPath::LEADSGET[URI], 'TourLeadsFollowUp')->name('tour-follow-up');
                Route::get(TourPath::LEADSGET[URI] . '/{id}', 'TourLeadsFollow')->name('tour-follow-list');
                Route::get(TourPath::LEADMESSAGE[URL] . '/{id}', 'TourLeadMessages')->name('tour-whatsapp-message');
                Route::post(TourPath::TOURLEADLIST[URI], "TourGetFormDiv")->name('get-tour-info-div');

                Route::get(TourPath::UPDATELEADADMIN[URL]."/{id}", 'TourLeadEditForm')->name('tour-admin-lead-edit');
            Route::post(TourPath::UPDATELEADADMIN[URI]."/{id}", 'TourLeadUpdateForm')->name('tour-admin-lead-update');
            });
        });
        Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::ORDERPENDING[URL], "orderPending")->name('pending');
                Route::get('cancel-order/{id}', "orderCancel")->name('cancel-order');
                Route::get(TourPath::ORDERCONFIRM[URL], "orderConfirm")->name('confirm');
                Route::get(TourPath::ORDERASSIGNED[URL], "orderAssigned")->name('assigned');
                Route::get(TourPath::ORDERPICKUP[URL], "orderPickUp")->name('pickup');
                Route::get(TourPath::ORDERCOMPLETE[URL], "orderComplete")->name('complete');
                Route::get(TourPath::ORDERCANCEL[URL], "UserCancelOrder")->name('user-cancel');

                Route::get(TourPath::ORDERDETAILS[URL] . '/{id}', "orderDetails")->name('details');
                Route::post(TourPath::ORDERDETAILS[URL], "orderAssignAccept")->name('assign-accept');
                Route::post(TourPath::ORDERCDASSIGN[URL], "ordercabdriverAssign")->name('cab-driver-assign');

                Route::get(TourPath::ORDERREMINDERMESSAGE[URL], "orderReminderMessage")->name('tour-order-reminder-message');
            });
        });

        Route::group(['prefix' => 'tour_visits', 'as' => 'tour_visits.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::ADDTOUR[URL], "addTour")->name('add-tour');
                Route::post(TourPath::ADDTOUR[URL], "tourSave")->name('insert-tour');
                Route::get(TourPath::TOURLIST[URL], "tourList")->name('tour-list');
                Route::get(TourPath::TOURUPDATE[URL] . "/{id}", "tourUpdate")->name('update');
                Route::get(TourPath::TOURVIEW[URL] . "/{id}", "tourView")->name('view');
                Route::post(TourPath::TOURUPDATE[URL], "tourEdit")->name('tour-edit');
                Route::get(TourPath::TOURIMGDELETE[URL] . '/{id}/{name}', "tourImgDelete")->name('tour-delete-image');
                Route::delete(TourPath::TOURDELETE[URL] . "/{id}", "tourDelete")->name('tour-delete');

                Route::get(TourPath::TOUROVERVIEW[URL] . '/{id}', "tourDetails")->name('overview');

                //////add visit                
                Route::get(TourPath::TOURVISITLIST[URL] . "/{id}", "tourVisit")->name('add-visit');
                Route::post(TourPath::TOURVISITLIST[URL], "tourVisitStore")->name('visit_place_store');
                Route::post(TourPath::TOURVISITDELETE[URL], "tourVisitDelete")->name('delete-place');
                Route::get(TourPath::TOURVISITIMGDELETE[URL] . "/{id}/{name}", "tourVisitImgDelete")->name('visit-delete-image');
                Route::get(TourPath::TOURVISITUPDATE[URL] . "/{id}", "tourVisitUpdate")->name('tour-visit-update');
                Route::post(TourPath::TOURVISITUPDATE[URL], "tourVisitEdit")->name('visit_place_edit');
                Route::post(TourPath::TOURACCEPT[URL], 'TourAccept')->name('accept-tour');
            });
        });
        // from vendor
        Route::group(['prefix' => 'messages', 'as' => 'messages.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::INBOX[URL], "TourSupportTicket")->name('index');
                Route::post(TourPath::INBOX[URL], "TourSupportTicketStore")->name('store-inbox');
                Route::post(TourPath::INBOXSTATUS[URL], "TourSupportTicketStatus")->name('status');
                Route::get(TourPath::INBOXVIEW[URL] . '/{id}', "TourSupportTicketView")->name('singleTicket');
                Route::post(TourPath::INBOXVIEW[URL] . "/{id}", "TourSupportTicketReplay")->name('replay');
            });
        });
        // from admin
        Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::ADMININBOX[URL], "AdminSupportTicket")->name('index');
                Route::post(TourPath::ADMININBOX[URL], "AdminSupportTicketStore")->name('store-inbox');

                // Route::post(TourPath::INBOXSTATUS[URL], "AdminSupportTicketStatus")->name('status');
                Route::get(TourPath::INBOXVIEW[URL] . '/{id}', "TourSupportTicketView")->name('singleTicket');
                Route::post(TourPath::INBOXVIEW[URL] . "/{id}", "TourSupportTicketReplay")->name('replay');
            });
        });


        Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::get(TourPath::WITHDRAW[URL], "withdrawRequests")->name('index');
                Route::post("get-vendor-info", 'GetVendorInfo')->name('get-vendor-data');
                Route::post("payment-request-send", 'AddWithdrawalRequest')->name('add-request-admin-send');
                Route::get("withdraw-request-view/{id}", 'WithdrawalRequestView')->name('withdraw-request-view');

                Route::post("vendor-collected-amount", 'VendorCollectedAmount')->name('vendor-collected-amount');
            });
        });
        Route::group(['prefix' => 'fcm-update', 'as' => 'fcm-update.'], function () {
            Route::controller(TourController::class)->group(function () {
                Route::post('owner', "FCMUpdates")->name('owners');
                Route::get('delete', "FCMUpdatesdelete")->name('delete');
            });
        });
        Route::group(['prefix' => 'self-driving', 'as' => 'self-driving.'], function () {
            Route::controller(SelfDrivingController::class)->group(function () {
                Route::post(SelfDrivingPath::VEHICLECATEGORYGET[URI], 'VehicleCategoryGet')->name('vehicle_category');
                Route::post(SelfDrivingPath::GETCABS[URI], 'GetCabList')->name('get-cab-list');
                Route::get(SelfDrivingPath::ADDCAB[URI], "AddVehicle")->name('add-vehicle');
                Route::post(SelfDrivingPath::SELFDRIVINGADD[URI], 'StoreSelfDriving')->name('self-driving-store');
                Route::get(SelfDrivingPath::SELFDRIVINGLIST[URI], 'SelfDrivingList')->name('self-vehicle-list');
                Route::get(SelfDrivingPath::SELFDRIVINGEDIT[URI] . '/{id}', 'SelfDrivingEdit')->name('self-driving-edit');
                Route::post(SelfDrivingPath::SELFDRIVINGEDIT[URL] . '/{id}', 'SelfDrivingUpdate')->name('self-driving-update');
                Route::post(SelfDrivingPath::SELFDRIVINGDELETE[URI] . '/{id}', 'SelfDrivingDelete')->name('self-driving-delete');
                Route::get(SelfDrivingPath::SELFDRIVINGDREMOVEIMG[URI] . '/{id}/{name}', 'SelfDrivingImageRemove')->name('self-driving-delete-image');
                Route::get(SelfDrivingPath::SELFDRIVINGLISTFILTER[URI], 'SelfDrivingListFilter')->name('self-drivinglist-filter');
                Route::post(SelfDrivingPath::SELFDRIVINGSTATUSUPDATE[URI], 'SelfDrivingStatusUpdate')->name('self-status-update');
            });
        });
    });
});

Route::group(['prefix' => 'trustees-vendor', 'as' => 'trustees-vendor.'], function () {
    Route::group(['middleware' => [\App\Http\Middleware\TrusteesMiddleware::class]], function () {
        Route::controller(TrusteesController::class)->group(function () {
            Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
                Route::get('/', 'dashboard')->name('index');
                Route::get('order-statistics', 'orderStatistics')->name('order-statistics');
            });

            Route::group(['prefix' => 'ads-management', 'as' => 'ads-management.', 'middleware' => ['employeemodules:Ads Management']], function () {
                Route::get(TrusteesPath::ADSADD[URI], 'AdsAdd')->name('add');
                Route::post(TrusteesPath::ADSADD[URI], 'AdsStore')->name('ad-store');
                Route::post(TrusteesPath::ADSADD[URL], 'AdsStatusUpdate')->name('status-update');
                Route::get(TrusteesPath::ADSUPDATE[URI] . '/{id}', 'AdsUpdate')->name('ads-update');
                Route::post(TrusteesPath::ADSUPDATE[URI], 'AdsUpdateSave')->name('ads-updatesave');
                Route::post(TrusteesPath::ADSDELETE[URI], 'AdsDelete')->name('ad-trust-delete');
                Route::get(TrusteesPath::ADSDETAILS[URI] . '/{id}', 'AdsDetails')->name('ads-details');
                Route::get(TrusteesPath::ADSLIST[URI], 'AdsList')->name('list');
            });

            Route::group(['prefix' => 'messages', 'as' => 'messages.', 'middleware' => ['employeemodules:Support Management']], function () {
                Route::get(TrusteesPath::TRUSTINBOX[URL], "TrustSupportTicket")->name('index');
                Route::post(TrusteesPath::TRUSTINBOX[URL], "TrustSupportTicketStore")->name('store-inbox');
                Route::post(TrusteesPath::TRUSTINBOXSTATUS[URL], "TrustSupportTicketStatus")->name('status');
                Route::get(TrusteesPath::TRUSTINBOXVIEW[URL] . '/{id}', "TrustSupportTicketView")->name('singleTicket');
                Route::post(TrusteesPath::TRUSTINBOXVIEW[URL] . "/{id}", "TrustSupportTicketReplay")->name('replay');
            });

            // from admin
            Route::group(['prefix' => 'message', 'as' => 'message.', 'middleware' => ['employeemodules:Support Management']], function () {
                Route::get(TrusteesPath::TRUSTADMININBOX[URL], "AdminSupportTicket")->name('index');
                Route::post(TrusteesPath::TRUSTADMININBOX[URL], "TrustSupportTicketStore")->name('store-inbox');

                Route::get(TrusteesPath::TRUSTINBOXVIEW[URL] . '/{id}', "TrustSupportTicketView")->name('singleTicket');
                Route::post(TrusteesPath::TRUSTINBOXVIEW[URL] . "/{id}", "TrustSupportTicketReplay")->name('replay');
            });
            Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.', 'middleware' => ['employeemodules:Withdrawal Management']], function () {
                Route::get(TrusteesPath::TRUSTWITHDRAW[URL], "withdrawRequests")->name('index');
                Route::post("get-vendor-info", 'GetVendorInfo')->name('get-vendor-data');
                Route::post("payment-request-send", 'AddWithdrawalRequest')->name('add-request-admin-send');
                Route::get("withdraw-request-view/{id}", 'WithdrawalRequestView')->name('withdraw-request-view');
                
                Route::post("get-vendor-employee-info", 'GetVendorEmployeeInfo')->name('get-vendor-employee-data');
                Route::post("payment-request-employee-send", 'AddWithdrawalEmployeeRequest')->name('add-employee-request-admin-send');
            });
            Route::group(['prefix' => 'donation-history', 'as' => 'donation-history.', 'middleware' => ['employeemodules:Donation Management']], function () {
                Route::get("list", 'DonationHistory')->name('list');
                Route::get("view/{id}", 'DonationDetails')->name('view');
            });
            Route::group(['prefix' => 'fcm-update', 'as' => 'fcm-update.'], function () {
                Route::post('owner', "FCMUpdates")->name('owners');
            });
            //pending
            Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['employeemodules:Profile']], function () {
                Route::get(TrusteesPath::PROFILEUPDATE[URL] . '/{id}', 'profileUpdate')->name('update');
                Route::post(TrusteesPath::PROFILEUPDATE[URL], 'profileUpdate2')->name('update2');
                Route::patch(TrusteesPath::PROFILEUPDATE[URI] . '/{id}', 'profileEdit')->name('profile-edit');
                Route::get('delete-image/{id}/{photo}', 'DeleteImage')->name('delete-image');
            });

            Route::group(['prefix' => 'trustees-withdrawal', 'as' => 'trustees-withdrawal.', 'middleware' => ['employeemodules:Withdrawal Management']], function () {
                Route::controller(DonateTrustController::class)->group(function () {
                    Route::get('/', 'WithdrawalList')->name('index');
                    Route::get('view/{id}', 'WithdrawalReqView')->name('withdraw-request-view');
                    Route::get('request-reject/{id}', 'WithdrawalReqReject')->name('rejects');
                    Route::get('create-contact/{id}/{type}', 'RazorpaycreateContact')->name('payment-req-approval-admin');
                });
            });
            Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['employeemodules:Employee']], function () {
                Route::get(TrusteesPath::ADDEMPLOYEE[URL], 'AddEmployee')->name('add-employee');
                Route::post(TrusteesPath::ADDEMPLOYEE[URI], 'StoreEmployee')->name('store-employee');
                Route::get(TrusteesPath::EMPLOYEELIST[URI], 'EmployeeList')->name('employee-list');
                Route::get(TrusteesPath::EMPLOYEEUPDATE[URI] . '/{id}', 'EmployeeEdit')->name('employee-edit');
                Route::post(TrusteesPath::EMPLOYEEUPDATE[URI] . '/{id}', 'EmployeeUpdate')->name('employee-update');

                Route::post(TrusteesPath::EMPLOYEESTATUSUPDATE[URI], 'EmployeeStatusUpdate')->name('employee-status-update');
                Route::post(TrusteesPath::EMPLOYEESTATUSUPDATE[URL], 'Employeedelete')->name('employee_delete');
                Route::post(TrusteesPath::CHECHEMAILPHONE[URI], 'CheckEmailPhone')->name('check-value');
            });

            Route::group(['prefix' => 'vip-darshan', 'as' => 'vip-darshan.', 'middleware' => ['employeemodules:VIP Darshan Management']], function () {
                Route::get(TrusteesPath::DARSHANTEMPLELIST[URI], 'TempleDarshanList')->name('temple-list');
                Route::post(TrusteesPath::DARSHANTEMPLESTATUS[URI], 'TempleDarshanStatusUpdate')->name('status-update');
                Route::get(TrusteesPath::DARSHANTEMPLEUPDATE[URI] . "/{id}", 'TempleDarshanEdit')->name('temple-edit');
                Route::post(TrusteesPath::DARSHANTEMPLEUPDATE[URI] . "/{id}", 'TempleDarshanUpdate')->name('temple-update');
                Route::get(TrusteesPath::GET_CITIES[URI], 'getCities')->name('get-cities');

                Route::get(TrusteesPath::DARSHANTEMPLEGALLERY[URI] . "/{id}", 'TempleGallerys')->name('add-gallery');
                Route::get(TrusteesPath::DARSHANTEMPLEGALLERYREMOVE[URI] . '/{id}/{name}', 'TempleImageRemove')->name('delete-image');
                Route::post(TrusteesPath::DARSHANTEMPLEGALLEUPDATE[URI] . '/{id}', 'TempleGalleryUpdate')->name('update-gallery-image');

                Route::get(TrusteesPath::DARSHANTEMPLEBOOKINGLISTING[URI], 'TempleDarshanBookingListings')->name('darshan-booking-listings');
                Route::get(TrusteesPath::DARSHANTEMPLEBOOKINGLISTING[URL], 'TempleDarshanBookingFilters')->name('darshan-booking-filters');

                Route::get(TrusteesPath::DARSHANTEMPLEBOOKING[URI], 'TempleDarshanBooking')->name('temple-booking');
                Route::get(TrusteesPath::DARSHANTEMPLETODAYBOOKING[URI], 'TempleDarshanTodayBooking')->name('temple-today-booking');
                Route::get(TrusteesPath::DARSHANTEMPLEBOOKINGCOMPLETE[URI], 'TempleDarshanBookingComplete')->name('temple-booking-complete');
                Route::post(TrusteesPath::DARSHANBOOKINGMEMBERCHECK[URI], 'TempleBookingMemberCheck')->name('check-member-valid');
                Route::get(TrusteesPath::DARSHANTEMPLEBOOKINGINFO[URI] . '/{id}', 'TempleDarshanBookingInfo')->name('darshan-booking-information');
            });
            Route::group(['prefix' => 'puja-management', 'as' => 'puja-management.', 'middleware' => ['employeemodules:Puja Management']], function () {
                Route::get(TrusteesPath::PUJACREATE[URI], 'PujaCreate')->name('puja-list');
                Route::post(TrusteesPath::PUJACREATE[URL], 'PujaSave')->name('puja-save');
                Route::get(TrusteesPath::PUJAUPDATE[URI] . '/{id}', 'PujaEdit')->name('puja-edit');
                Route::post(TrusteesPath::PUJAUPDATE[URL] . "/{id}", 'PujaUpdate')->name('puja-update');
                Route::post(TrusteesPath::PUJADELETE[URI], 'PujaDelete')->name('puja-delete');

                Route::get(TrusteesPath::PUJABOOKINGCREATE[URI], 'PujaBookingCreate')->name('puja-booking-create');
                Route::post(TrusteesPath::PUJABOOKINGCREATE[URL], 'PujaBookingsave')->name('puja-booking-save');
                Route::post(TrusteesPath::PUJABOOKINGORDERID[URI], 'PujaBookingOrderInfo')->name('puja-booking-order-id');
                Route::get(TrusteesPath::PUJABOOKINGORDERLIST[URI], 'PujaBookingOrderList')->name('puja-booking-list');
                Route::get(TrusteesPath::PUJABOOKINGORDERLIST[URL], 'PujaBookingOrderFilters')->name('puja-booking-filters');
            });
             Route::group(['prefix' => 'pandit-order-management', 'as' => 'pandit-order-management.', 'middleware' => ['employeemodules:Pandit Order Management']], function () {
                Route::get(TrusteesPath::PANDITLIST[URI], 'PanditOrderList')->name('list');
                // Route::get(TrusteesPath::PANDITLIST[URL], 'PanditOrderList')->name('list-filter');
            });
            Route::group(['prefix' => 'darshan-booking', 'as' => 'darshan-booking.', 'middleware' => ['employeemodules:VIP Darshan Booking']], function () {
                Route::get(TrusteesPath::DARSHANBOOKINGCREATE[URI], 'DarshanBookingCreate')->name('darshan-booking');
                Route::post(TrusteesPath::DARSHANBOOKINGCREATE[URL], 'DarshanBookingsave')->name('darshan-booking-save');
                Route::post(TrusteesPath::DARSHANBOOKINGORDERID[URI], 'VipticketBookingOrderInfo')->name('vip-darshan-booking-order-id');
                Route::get('/get-purohits/{temple_id}', 'getPurohits')->name('get.purohits');
                Route::get('get-bookingList', 'vipdarshanBookingList')->name('get.bookingList');
                Route::get('get-ordersBookFetch', 'fetchOrders')->name('get.ordersBookFetch');
                Route::patch('get-ordersHide/{order_id}', 'hideOrder')->name('get.ordersHide');
                Route::post('update-purohit', 'updatePurohit')->name('get.update-purohit');
                Route::post('payment-check-status', 'paymentCheckStatus')->name('get.payment-check-status');
            });
            Route::group(['prefix' => 'lead-management', 'as' => 'lead-management.', 'middleware' => ['employeemodules:Temple Lead Management']], function () {
                Route::controller(TrusteesController::class)->group(function () {
                    Route::get('leadlist', 'lead_list_show')->name('lead-list');
                });
            });
            Route::group(['prefix' => 'puja_dashboard', 'as' => 'puja_dashboard.', 'middleware' => ['employeemodules:Dashboard']], function () {
                Route::controller(TrusteesController::class)->group(function () {
                    Route::get('view', 'PujaDashboard')->name('view');
                });
            });
            Route::group(['prefix' => 'order-management', 'as' => 'order-management.', 'middleware' => ['employeemodules:Temple Order Management']], function () {
                Route::controller(TrusteesController::class)->group(function () {
                    Route::get('orderlist', 'order_list_show')->name('order-list');
                    Route::get('order-list-filter', 'OrderListShowFilter')->name('order-list-filter');
                    Route::get('create-ticket', 'order_create')->name('create-ticket');
                    Route::get('get-slots', 'getSlots')->name('get-slots');
                    Route::post('store-pooja', 'storePooja')->name('store-pooja');

                    Route::post('all-order-status-check','AllOrderStatusCheck')->name('multi-order-status-check');
                });
            });
            Route::group(['prefix' => 'recepit-management', 'as' => 'recepit-management.', 'middleware' => ['employeemodules:Temple Receipt Management']], function () {
                Route::controller(TrusteesController::class)->group(function () {
                    Route::get('cashrecepit', 'recepit_index')->name('cashrecepit')->defaults('mode', 'cash');;
                    Route::get('onlinerecepit', 'recepit_index')->name('onlinerecepit')->defaults('mode', 'online');;
                    Route::get('recepit', 'recepit_index')->name('recepit');
                    Route::get('get-order-details', 'getOrderDetails')->name('get-order-details');
                    Route::post('get-order-details-puja-slip', 'getOrderPujaSlipDetails')->name('get-order-details-puja-slip');
                    Route::get('thermal-print/{order_id}', 'thermalPrint')->name('thermal-print');
                    Route::post('cash/confirm',  'confirmCashPayment')->name('cash.confirm');
                    Route::get('temple-qr-show/{id}',  'TempleQrCodeScanner')->name('show');
                    Route::post('purohit-confirm', 'confirmPurohitPayment')->name('purohit.confirm');
                    Route::post('purohit-amount-get', 'PurohitTotalAmountGet')->name('purohit-amount-get');

                    Route::get('recepit-qr-scanner', 'recepitQrScanners')->name('recepit-qr-scanner');
                    Route::post('verify-service-update-status', 'recepitQrverifyUpdateStatus')->name('verify-service-update-status');

                    Route::get('order-list', 'ServiceBookingUsersList')->name('order-list');
                    Route::get('order-list-booking-filter', 'ServiceBookingUsersListFilter')->name('order-list-booking-filter');
                    Route::get('order-list-booking-receipt-filter', 'ServiceBookingReceiptListFilter')->name('order-list-booking-receipt-filter');
                    Route::get('pandit-order-list-receipt-filter', 'PanditServiceBookingListFilter')->name('pandit-order-list-receipt-filter');
                    Route::post('order-luggage-phone-update', 'OrderLuggagePhoneUpdate')->name('order-luggage-phone-update');
                    Route::post('/package-upgrade','packageUpgrade')->name('package.upgrade');
                    Route::post('package-confirm', 'confirmPackage')->name('package.confirm');

                    Route::post('order-details-modal-data','OrderDetailsModalData')->name('order-details-modal-data');

                    Route::get('purohit-to-get-employee','PurohitToGetEmployee')->name('purohit-to-get-employee');
                    Route::get('print-status-update','PrintStatusUpdates')->name('print-status-update');
                });
            });
        });
        // Pandit CRUD
        Route::group(['prefix' => 'purohit-data', 'as' => 'purohit-data.'], function () {
            Route::controller(TrusteesController::class)->group(function () {
                Route::get("purohit-add", 'purohit_View')->name('purohit-add');
                Route::get('purohit-transaction', 'purohitTransaction')->name('purohit-transaction');
                Route::get('purohit-transaction-history', 'purohitTransactionHistory')->name('purohit-transaction-history');
                Route::get("purohitview/{id}", 'purohitList')->name('purohitview');
                Route::post("purohit-list", 'purohitStore')->name('purohit-list');
                Route::get("purohit-balance-sheet/{id}", 'purohitBalanceSheet')->name('purohit-balance-sheet');
                Route::get("purohit-balance-sheet-filters/{id}", 'purohitBalanceSheetFilter')->name('purohit-balance-sheet-filters');
            });
        });
    });
});
