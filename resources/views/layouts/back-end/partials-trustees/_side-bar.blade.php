@php
use App\Enums\ViewPaths\Vendor\Chatting;
use App\Enums\ViewPaths\Vendor\Product;
use App\Enums\ViewPaths\Vendor\Profile;
use App\Enums\ViewPaths\Vendor\Refund;
use App\Enums\ViewPaths\Vendor\Review;
use App\Enums\ViewPaths\Vendor\DeliveryMan;
use App\Enums\ViewPaths\Vendor\EmergencyContact;
use App\Models\Order;
use App\Models\RefundRequest;
use App\Models\Shop;
use App\Enums\ViewPaths\Vendor\Order as OrderEnum;
use App\Utils\Helpers;
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
$logintype = 'trust';
$PurohitsId = 0;
$purohitsEmpId = 0;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
$logintype = 'employee';
$PurohitsId = auth('trust_employee')->user()->purohit_id;
$purohitsEmpId = auth('trust_employee')->user()->id;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
$logintype = 'purohit';
$PurohitsId = auth('purohit')->user()->id;
$purohitsEmpId = 0;
}
@endphp
<div id="sidebarMain" class="d-none">
    <aside style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <?php
                    if ($logintype == 'trust') {
                        $shop = \App\Models\DonateTrust::where('id', ($relationEmployees ?? 0))->first();
                        $logoImage =  getValidImage(path: 'storage/app/public/donate/trust/' . ($shop['theme_image'] ?? ''), type: 'backend-logo');
                    } elseif ($logintype == 'employee') {
                        $shop = \App\Models\VendorEmployees::where('id', ($purohitsEmpId ?? 0))->first();
                        $logoImage =  getValidImage(path: 'storage/app/public/event/employee/' . ($shop['image'] ?? ''), type: 'backend-logo');
                    } elseif ($logintype == 'purohit') {
                        $shop = \App\Models\Purohit::where('id', ($PurohitsId ?? 0))->first();
                        $logoImage =  getValidImage(path: 'storage/app/public/' . ($shop['profile'] ?? ''), type: 'backend-logo');
                    }
                    ?>
                    <a class="navbar-brand" href="{{ route('trustees-vendor.dashboard.index') }}" aria-label="Front">
                        @if (isset($shop))
                        <img class="navbar-brand-logo-mini for-seller-logo" src="{{ $logoImage }}" alt="{{ translate('logo') }}">
                        @else
                        <img class="navbar-brand-logo-mini for-seller-logo"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/900x400/img1.jpg') }}"
                            alt="{{ translate('logo') }}">
                        @endif
                    </a>
                    <button type="button" class="d-none js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>

                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-3">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                            data-template="<div class=&quot;tooltip d-none d-sm-block&quot; role=&quot;tooltip&quot;><div class=&quot;arrow&quot;></div><div class=&quot;tooltip-inner&quot;></div></div>"></i>
                    </button>
                </div>
                <div class="navbar-vertical-content">
                    <div class="sidebar--search-form pb-3 pt-4">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input" placeholder="{{ translate('search_menu') . '...' }}">
                        </div>
                    </div>
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        @if($logintype == 'trust')
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/dashboard*') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.dashboard.index') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('dashboard') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Dashboard', 'Puja Dashboard', 'View'))
                        @if($logintype == 'trust')
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Puja_Dashboard') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @endif
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/puja_dashboard/view') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('trustees-vendor.puja_dashboard.view') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Dashboard') }}
                                </span>
                            </a>
                        </li>
                        @endif

                        @if (Helpers::Employee_modules_permission('Temple Lead Management', 'Temple Lead', 'View') && $logintype == 'trust')
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Temple_Lead_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @if (Helpers::Employee_modules_permission('Temple Lead Management', 'Temple Lead', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/lead-management/lead-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.lead-management.lead-list') }}">
                                <i class="tio-format-bullets nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Temple_Lead') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif

                        @if (Helpers::Employee_modules_permission('Temple Order Management', 'Ticket Generate', 'View') || Helpers::Employee_modules_permission('Temple Order Management', 'Temple Order', 'View'))
                        <!-- Order Booking -->
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Temple_Order_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @if (Helpers::Employee_modules_permission('Temple Order Management', 'Ticket Generate', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/order-management/create-ticket') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.order-management.create-ticket') }}">
                                <i class="tio-coin nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Ticket_Generate') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Temple Order Management', 'Temple Order', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/order-management/order-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.order-management.order-list') }}">
                                <i class="tio-coin nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Temple_Order') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if (Helpers::Employee_modules_permission('Temple Receipt Management', 'Cash Receipt', 'View') || Helpers::Employee_modules_permission('Temple Receipt Management', 'Online Receipt', 'View') || Helpers::Employee_modules_permission('Temple Receipt Management', 'Order Receipt', 'View') || Helpers::Employee_modules_permission('Temple Receipt Management', 'Receipt scanner', 'View') || Helpers::Employee_modules_permission('Temple Receipt Management', 'Order List', 'View'))
                        <!-- Recepit Booking Information -->
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Temple_Receipt_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @if (Helpers::Employee_modules_permission('Temple Receipt Management', 'Cash Receipt', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/recepit-management/cashrecepit') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.recepit-management.cashrecepit') }}">
                                <i class="tio-money-vs nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Cash_Receipt') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Temple Receipt Management', 'Online Receipt', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/recepit-management/onlinerecepit') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.recepit-management.onlinerecepit') }}">
                                <i class="tio-globe nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Online_Receipt') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Temple Receipt Management', 'Order Receipt', 'View'))

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/recepit-management/recepit') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.recepit-management.recepit') }}">
                                <i class="tio-receipt-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Order_Receipt') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Temple Receipt Management', 'Receipt scanner', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/recepit-management/recepit-qr-scanner') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.recepit-management.recepit-qr-scanner') }}">
                                <i class="tio-receipt-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Receipt_scanner') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Temple Receipt Management', 'Order List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/recepit-management/order-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.recepit-management.order-list') }}">
                                <i class="tio-receipt-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('order List') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif

                        @if(Helpers::Employee_modules_permission('Pandit Order Management', 'Order List', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Pandit_Order_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Pandit Order Management', 'Order List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/pandit-order-management/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.pandit-order-management.list') }}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Pandit_Order_list') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if ((Helpers::Employee_modules_permission('Employee', 'Add Employee', 'View')
                        || Helpers::Employee_modules_permission('Employee', 'Employee List', 'View')) && ($logintype == 'trust' || $logintype == 'purohit'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Employee_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Employee', 'Add Employee', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/employee/add-employee') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.employee.add-employee') }}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_Employee') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Employee', 'Employee List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/employee/employee-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.employee.employee-list') }}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Employee_List') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if ((Helpers::Employee_modules_permission('Ads Management', 'Add Ads', 'View') || Helpers::Employee_modules_permission('Ads Management', 'Ads List', 'View')) && $logintype == 'trust')
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('ads_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Ads Management', 'Add Ads', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/ads-management/add') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.ads-management.add') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(Helpers::Employee_modules_permission('Ads Management', 'Ads List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/ads-management/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.ads-management.list') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('list') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if (Helpers::Employee_modules_permission('Donation Management', 'Donation History', 'View') && $logintype == 'trust')
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/donation-history/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('trustees-vendor.donation-history.list') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('donation-history') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if ((Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'View')
                        || Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Booking', 'View')
                        || Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'View')
                        || Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Booking Complete', 'View')
                        || Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Booking User List', 'View')
                        ) && $logintype == 'trust')

                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Temple_VIP_darshan') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/vip-darshan/temple-list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('trustees-vendor.vip-darshan.temple-list') }}">
                                <i class="tio-home_vs nav-icon">home_vs</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('temple_list') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Booking', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/vip-darshan/temple-booking') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('trustees-vendor.vip-darshan.temple-booking') }}">
                                <i class="tio-home_vs nav-icon">home_vs</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('temple_booking') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Today Booking', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/vip-darshan/temple-today-booking') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('trustees-vendor.vip-darshan.temple-today-booking') }}">
                                <i class="tio-home_vs nav-icon">home_vs</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('temple_today_booking') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Booking Complete', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/vip-darshan/temple-booking-complete') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('trustees-vendor.vip-darshan.temple-booking-complete') }}">
                                <i class="tio-home_vs nav-icon">home_vs</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('temple_booking_complete') }}
                                </span>
                            </a>
                        </li>
                        @endif

                        @if (Helpers::Employee_modules_permission('VIP Darshan Management', 'Temple Booking User List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/vip-darshan/vip-booking-list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('trustees-vendor.vip-darshan.darshan-booking-listings') }}">
                                <i class="tio-home_vs nav-icon">home_vs</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('temple_booking_user_list') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif

                        @if ((Helpers::Employee_modules_permission('VIP Darshan Booking', 'Darshan Booking', 'View')) && $logintype == 'trust' )
                        <li class="nav-item">
                            <small class="nav-subtitle">VIP Darshan Booking</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @if (Helpers::Employee_modules_permission('VIP Darshan Booking', 'Darshan Booking', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/darshan-booking/darshan-booking') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('trustees-vendor.darshan-booking.darshan-booking') }}">
                                <i class="tio-mma nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Darshan Booking
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif


                        <!-- //////////////////////////////////////////////// -->
                        {{--
                        @if (Helpers::Employee_modules_permission('Puja Management', 'Puja Management', 'View')
                        || Helpers::Employee_modules_permission('Puja Management', 'Puja Booking', 'View')
                        || Helpers::Employee_modules_permission('Puja Management', 'Puja Order', 'View')
                        )
                        <li class="nav-item">
                            <small class="nav-subtitle">Puja Management</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Puja Management', 'Puja Management', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/puja-management/puja-list') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('trustees-vendor.puja-management.puja-list') }}">
                            <i class="tio-neighborhood nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                Puja Management
                            </span>
                        </a>
                        </li>
                        @endif

                        @if (Helpers::Employee_modules_permission('Puja Management', 'Puja Booking', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/puja-management/puja-booking-create') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('trustees-vendor.puja-management.puja-booking-create') }}">
                                <i class="tio-mma nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Puja Booking
                                </span>
                            </a>
                        </li>
                        @endif

                        @if (Helpers::Employee_modules_permission('Puja Management', 'Puja Order', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/puja-management/puja-booking-list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('trustees-vendor.puja-management.puja-booking-list') }}">
                                <i class="tio-shopping_cart_add nav-icon">shopping_cart_add</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Puja Order
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        --}}
                        <!-- ////////////////////////////////////////////////// -->
                        @if (Helpers::Employee_modules_permission('Support Management', 'From Vendor', 'View')
                        || Helpers::Employee_modules_permission('Support Management', 'From Admin', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('help_&_support') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/support-ticket*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="{{ translate('support_Ticket') }}">
                                <i class="tio-chat nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('support_Ticket') }}
                                    @if (\App\Models\VendorSupportTicketConv::where('type', 'trust')->where('created_by', 'vendor')->where('vendor_id', ($relationEmployees??0))->where('status', 'open')->count() > 0)
                                    <span class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                    @endif
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('trustees-vendor/messages/*') || Request::is('trustees-vendor/message/*') ? 'block' : 'none' }}">
                                @if (Helpers::Employee_modules_permission('Support Management', 'From Vendor', 'View'))
                                <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/messages/*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('trustees-vendor.messages.index') }}">
                                        <i class="tio-support nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{ translate('from_vendor') }}
                                            @if (\App\Models\VendorSupportTicketConv::where('type', 'trust')->where('created_by', 'vendor')->where('vendor_id', ($relationEmployees??0))->where('status', 'open')->count() > 0)
                                            <span
                                                class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::Employee_modules_permission('Support Management', 'From Admin', 'View'))
                                <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/message/*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('trustees-vendor.message.index') }}">
                                        <i class="tio-support nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{ translate('from_admin') }}
                                            @if (\App\Models\VendorSupportTicketConv::where('type', 'trust')->where('created_by', 'admin')->where('vendor_id', ($relationEmployees??0))->where('status', 'open')->count() > 0)
                                            <span
                                                class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Withdrawal Management', 'List', 'View'))
                        <li class="nav-item {{ Request::is('trustees-vendor/withdraw') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ translate('withdraw_section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/withdraw') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('trustees-vendor.withdraw.index') }}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('withdraws') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        <!-- @if (Helpers::Employee_modules_permission('Withdrawal Management', 'List', 'View')) -->
                        @if($logintype == 'trust')
                        <li class="nav-item {{ Request::is('trustees-vendor/purohit-data') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ translate('Pandit_Transections') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/purohit-data') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('trustees-vendor.purohit-data.purohit-add') }}">
                                <i class="tio tio-group-add nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('Add purohit') }}
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/purohit-data') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('trustees-vendor.purohit-data.purohit-transaction') }}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('purohit-transaction') }}
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('trustees-vendor/purohit-data') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('trustees-vendor.purohit-data.purohit-transaction-history') }}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('purohit-transaction-History') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        <!-- @endif -->

                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>