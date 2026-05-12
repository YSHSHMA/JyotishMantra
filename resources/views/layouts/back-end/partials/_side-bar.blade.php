@php
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
use App\Enums\ViewPaths\Admin\Calculator;
use App\Enums\ViewPaths\Admin\FastFestival;
use App\Enums\ViewPaths\Admin\Astrologer;
use App\Enums\ViewPaths\Admin\BusinessSettings;
use App\Enums\ViewPaths\Admin\Category;
use App\Enums\ViewPaths\Admin\Chatting;
use App\Enums\ViewPaths\Admin\Currency;
use App\Enums\ViewPaths\Admin\Customer;
use App\Enums\ViewPaths\Admin\CustomerWallet;
use App\Enums\ViewPaths\Admin\Dashboard;
use App\Enums\ViewPaths\Admin\DatabaseSetting;
use App\Enums\ViewPaths\Admin\DealOfTheDay;
use App\Enums\ViewPaths\Admin\DeliveryMan;
use App\Enums\ViewPaths\Admin\DeliverymanWithdraw;
use App\Enums\ViewPaths\Admin\DeliveryRestriction;
use App\Enums\ViewPaths\Admin\Employee;
use App\Enums\ViewPaths\Admin\EnvironmentSettings;
use App\Enums\ViewPaths\Admin\FeatureDeal;
use App\Enums\ViewPaths\Admin\FeaturesSection;
use App\Enums\ViewPaths\Admin\FlashDeal;
use App\Enums\ViewPaths\Admin\GoogleMapAPI;
use App\Enums\ViewPaths\Admin\HelpTopic;
use App\Enums\ViewPaths\Admin\InhouseProductSale;
use App\Enums\ViewPaths\Admin\Mail;
use App\Enums\ViewPaths\Admin\OfflinePaymentMethod;
use App\Enums\ViewPaths\Admin\Order;
use App\Enums\ViewPaths\Admin\Pages;
use App\Enums\ViewPaths\Admin\Product;
use App\Enums\ViewPaths\Admin\PushNotification;
use App\Enums\ViewPaths\Admin\Recaptcha;
use App\Enums\ViewPaths\Admin\RefundRequest;
use App\Enums\ViewPaths\Admin\SiteMap;
use App\Enums\ViewPaths\Admin\SMSModule;
use App\Enums\ViewPaths\Admin\SocialLoginSettings;
use App\Enums\ViewPaths\Admin\SocialMedia;
use App\Enums\ViewPaths\Admin\SoftwareUpdate;
use App\Enums\ViewPaths\Admin\SubCategory;
use App\Enums\ViewPaths\Admin\SubSubCategory;
use App\Enums\ViewPaths\Admin\ThemeSetup;
use App\Enums\ViewPaths\Admin\Vendor;
use App\Enums\ViewPaths\Admin\InhouseShop;
use App\Enums\ViewPaths\Admin\SocialMediaChat;
use App\Enums\ViewPaths\Admin\ShippingMethod;
use App\Enums\ViewPaths\Admin\PaymentMethod;
use App\Enums\ViewPaths\Admin\ServiceDetails;
use App\Enums\ViewPaths\Admin\FAQPath;
use App\Enums\ViewPaths\Admin\CitiesPath;
use App\Enums\ViewPaths\Admin\TemplePath;
use App\Enums\ViewPaths\Admin\ChadhavaPath;
use App\Enums\ViewPaths\Admin\Sahitya;
use App\Enums\ViewPaths\Admin\BhagavadGita;
use App\Enums\ViewPaths\Admin\ValmikiRamayan;
use App\Enums\ViewPaths\Admin\TulsidasRamayan;
use App\Enums\ViewPaths\Admin\Bhagwan;
use App\Enums\ViewPaths\Admin\Jaap;
use App\Enums\ViewPaths\Admin\RamShalaka;
use App\Utils\Helpers;
@endphp
<div id="sidebarMain" class="d-none">
    <aside
        class="bg-white js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered text-start">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    @php($eCommerceLogo = getWebConfig(name: 'company_web_logo'))
                    <a class="navbar-brand" href="{{ route('admin.dashboard.index') }}" aria-label="Front">
                        <img class="navbar-brand-logo-mini for-web-logo"
                            src="{{ getValidImage('storage/app/public/company/' . $eCommerceLogo, type: 'backend-logo') }}"
                            alt="{{ translate('logo') }}">
                    </a>
                    <button type="button"
                        class="d-none js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                            data-template="<div class=&quot;tooltip d-none d-sm-block&quot; role=&quot;tooltip&quot;><div class=&quot;arrow&quot;></div><div class=&quot;tooltip-inner&quot;></div></div>"></i>
                    </button>
                </div>
                <div class="navbar-vertical-content">
                    <div class="sidebar--search-form pb-3 pt-4">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control"
                                id="search-bar-input" placeholder="{{ translate('search_menu') . '...' }}">
                        </div>
                    </div>
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        @if (Helpers::modules_check('Dashboard'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/dashboard' . Dashboard::VIEW[URI]) ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                title="{{ translate('dashboard') }}" href="{{ route('admin.dashboard.index') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('dashboard') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::modules_check('POS'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/pos*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" title="{{ translate('POS') }}"
                                href="{{ route('admin.pos.index') }}">
                                <i class="tio-shopping nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('POS') }}</span>
                            </a>
                        </li>
                        @endif
                        <!-- start leads-->
                        <li class="nav-item">
                            <small class="nav-subtitle" title="{{ translate('leads') }}">
                                {{ translate('all_leads_managment') }}
                            </small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::modules_permission_check('All Service', 'Lead', 'view'))
                        <li class="navbar-vertical-aside-has-menu 
                            {{ request()->is('admin/leads/leads') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.leads.lead-list') }}"
                                title="{{ translate('lead_list') }}">
                                <i class="tio-person nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('lead_list') }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('All Service', 'Product Lead', 'view'))
                        <li class="navbar-vertical-aside-has-menu 
                            {{ request()->is('admin/leads/product-leads') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.leads.product-leads') }}"
                            title="{{ translate('Product Leads') }}">
                            <i class="tio-person nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('Product Leads') }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Offline Pooja', 'Lead', 'view'))
                        <li class="navbar-vertical-aside-has-menu 
                            {{ request()->is('admin/leads/offlinepooja/leads') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.leads.offline-pooja-leads') }}"
                                title="{{ translate('offline_puja_leads') }}">
                                <i class="tio-person nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('offline_puja_leads') }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Tour', 'Tour Lead', 'view'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_visits/leads*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.tour_visits.leads') }}"
                                title="{{ translate('Tour_leads') }}">
                                <i class="tio-person nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Tour_leads') }}
                                    {{--
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        {{ \App\Models\TourLeads::count() }}
                                </span>
                                --}}
                                </span>
                            </a>
                        </li>
                        @endif
                        <!-- end leads-->

                        <!-- Start Pooja Records-->
                        @if (Helpers::modules_check('Pooja Review') || Helpers::modules_check('Pooja Schedule') ||Helpers::modules_check('Pooja Records') || Helpers::modules_check('Pooja Devotee'))
                        <li class="nav-item">
                            <small class="nav-subtitle" title="{{ translate('leads') }}">
                                {{ translate('puja_records_management') }}
                            </small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- Devotee records -->
                        @if (Helpers::modules_permission_check('Pooja Devotee', 'Pooja Devotee', 'view'))
                        <li class="navbar-vertical-aside-has-menu 
                            {{ request()->is('admin/pujadevotee/puja-devotee-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.pujarecords.puja-devotee-list') }}"
                                title="{{ translate('puja_devotee_list') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('puja_devotee') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Devotee::count() }}
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        <!-- Start Pooja Records-->
                        @if (Helpers::modules_permission_check('Pooja Records', 'Pooja Records', 'view'))
                        <li class="navbar-vertical-aside-has-menu 
                            {{ request()->is('admin/pujarecords/puja-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.pujarecords.puja-list') }}"
                                title="{{ translate('pujarecords') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('puja_records') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\PoojaRecords::count() }}
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Pooja Schedule', 'Pooja Schedule', 'view'))
                        <li class="navbar-vertical-aside-has-menu 
                            {{ request()->is('admin/pujaschedule/pujaschedule-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.pujaschedule.pujaschedule-list') }}"
                                title="{{ translate('pujaschedule') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('puja_schedule') }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Pooja Review', 'Pooja Review', 'view'))
                        <li class="navbar-vertical-aside-has-menu 
                            {{ request()->is('admin/pujarecords/pujareview-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.pujarecords.pujareview-list') }}"
                                title="{{ translate('puja_review') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('puja_review') }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif

                        <!-- Start Pooja Transaction by Kanika-->
                        @if (Helpers::modules_permission_check('Pooja Records', 'Pooja Records', 'view'))
                            <li class="navbar-vertical-aside-has-menu 
                                {{ request()->is('admin/pujarecords/pooja-transaction-list') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.pujarecords.pooja-transaction-list') }}"
                                    title="Puja Transaction">
                                    <i class="tio-star nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        <span class="position-relative">
                                        {{ translate('Puja Transaction') }}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{ \App\Models\PanditTransectionPooja::count() }}
                                            </span>
                                        </span>
                                    </span>
                                </a>
                            </li>
                        @endif
                        @endif
                        <!-- End Pooja Records-->


                        <!-- start video-->
                        @if (Helpers::modules_check('Youtube'))
                        <li class="nav-item {{ Request::is('admin/video*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ translate('video_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/video*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('video') }}">
                                <i class="tio-youtube nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('youtube_video') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/video*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Youtube', 'Video Category', 'view'))
                                <li class="nav-item {{ Request::is('admin/videocategory*') ? 'active' : '' }}"
                                    title="{{ translate('video_Category_setup') }}">
                                    <a class="nav-link" href="{{ route('admin.videocategory.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('Video_category') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Youtube', 'Video Sub Category', 'view'))
                                <li class="nav-item {{ Request::is('admin/videosubcategory*') ? 'active' : '' }}"
                                    title="{{ translate('video_Category_setup') }}">
                                    <a class="nav-link" href="{{ route('admin.videosubcategory.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('Video_subcategory') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Youtube', 'Video List Type', 'view'))
                                <li class="nav-item {{ Request::is('admin/videolisttype*') ? 'active' : '' }}"
                                    title="{{ translate('video_Playlist_setup') }}">
                                    <a class="nav-link" href="{{ route('admin.videolisttype.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('Video_list_type') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Youtube', 'Youtube', 'add'))
                                <li class="nav-item {{ Request::is('admin/video/add-new') ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link" href="{{ route('admin.video.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Youtube', 'Youtube', 'view'))
                                <li class="nav-item {{ Request::is('admin/video/list') ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link" href="{{ route('admin.video.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        <!-- end video-->
                        <!-- start sangeet-->
                        @if (Helpers::modules_check('Sangeet'))
                        <li class="nav-item {{ Request::is('admin/sangeet*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle"
                                title="">{{ translate('sangeet_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/sangeet*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('sangeet') }}">
                                <i class="tio-music nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('sangeet') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/sangeet*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Sangeet', 'Category', 'view'))
                                <li class="nav-item {{ Request::is('admin/sangeetcategory*') ? 'active' : '' }}"
                                    title="{{ translate('sangeet_Category_setup') }}">
                                    <a class="nav-link" href="{{ route('admin.sangeetcategory.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('Sangeet_category') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Sangeet', 'Sub Category', 'view'))
                                <li class="nav-item {{ Request::is('admin/sangeetsubcategory*') ? 'active' : '' }}"
                                    title="{{ translate('sangeet_Subcategory_setup') }}">
                                    <a class="nav-link" href="{{ route('admin.sangeetsubcategory.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ translate('Sangeet_subcategory') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Sangeet', 'Language', 'view'))
                                <li class="nav-item {{ Request::is('admin/sangeetlanguage*') ? 'active' : '' }}"
                                    title="{{ translate('sangeet_Language_setup') }}">
                                    <a class="nav-link" href="{{ route('admin.sangeetlanguage.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('language') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'add'))
                                <li class="nav-item {{ Request::is('admin/sangeet/add-new') ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link" href="{{ route('admin.sangeet.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Sangeet', 'Sangeet', 'view'))
                                <li class="nav-item {{ Request::is('admin/sangeet/list') ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link" href="{{ route('admin.sangeet.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                                <!-- <li class="nav-item {{ Request::is('admin/sangeet/details') ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link" href="{{ route('admin.sangeet.all') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('all') }}</span>
                                    </a>
                                </li>   -->
                            </ul>
                        </li>
                        @endif
                        <!-- end sangeet-->
                        <!--start panchang moon image -->
                        @if (Helpers::modules_check('Panchnage Moon'))
                        <li class="nav-item {{ Request::is('admin/panchangmoonimage*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ translate('Panchang_moon') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/panchangmoonimage*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('Panchang_moon') }}">
                                <i class="tio-moon nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Panchang_moon') }}</span>
                            </a>
                            @if (Helpers::modules_permission_check('Panchnage Moon', 'Panchnage Moon Image', 'view'))
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/panchangmoonimage*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/panchangmoonimage*') ? 'active' : '' }}"
                                    title="{{ translate('Panchang_moon_image') }}">
                                    <a class="nav-link" href="{{ route('admin.panchangmoonimage.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ translate('Panchang_moon_image') }}</span>
                                    </a>
                                </li>
                            </ul>
                            @endif
                        </li>
                        @endif
                        <!--end panchang moon image -->
                        <!--start app section -->
                        @if (Helpers::modules_check('App Section'))
                        <li class="nav-item {{ Request::is('admin/appsection*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle"
                                title="">{{ translate('app_section_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/appsection*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('appsection') }}">
                                <i class="tio-app nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('app_section') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/appsection*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('App Section', 'App Section', 'view'))
                                <li class="nav-item {{ Request::is('admin/appsection*') ? 'active' : '' }}"
                                    title="{{ translate('app_section_setup') }}">
                                    <a class="nav-link" href="{{ route('admin.appsection.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('App_section') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        <!--end app section -->
                        <!--start sahitya section -->
                        {{-- @if (Helpers::module_permission_check('sahitya_management')) --}}
                        @if (Helpers::modules_check('Sahitya'))
                        <li class="nav-item {{ Request::is('admin/sahitya*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle"
                                title="">{{ translate('sahitya_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/sahitya*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('sahitya') }}">
                                <img width="20px" height="24px"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/sahitya.png') }}"
                                    alt="">
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate ml-3">{{ translate('sahitya') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/sahitya*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'view'))
                                <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('admin/sahitya*') ? 'active' : '' }}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                        href="javascript:" title="{{ translate('sahitya') }}">
                                        <img width="20px" height="24px"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/sahitya.png') }}"
                                            alt="">
                                        <span
                                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate ml-3">{{ translate('sahitya') }}</span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                        style="display: {{ Request::is('admin/sahitya*') ? 'block' : 'none' }}">
                                        @if (Helpers::modules_permission_check('Sahitya', 'Sahitya', 'view'))
                                        <li class="nav-item {{ Request::is('admin/sahitya/' . Sahitya::LIST[URI]) ? 'active' : '' }}"
                                            title="{{ translate('view') }}">
                                            <a class="nav-link "
                                                href="{{ route('admin.sahitya.view') }}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span
                                                    class="text-truncate">{{ translate('sahitya') }}</span>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'add') ||
                                Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'view'))
                                <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('admin/bhagavadgita*') ? 'active' : '' }}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                        href="javascript:" title="{{ translate('bhagavadgita') }}">
                                        <img width="18px" height="22px"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/bhagavad-gita.png') }}"
                                            alt="">
                                        <span
                                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate ml-3">{{ translate('bhagavad gita') }}</span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                        style="display: {{ Request::is('admin/bhagavadgita*') ? 'block' : 'none' }}">
                                        @if (Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'add'))
                                        <li class="nav-item {{ Request::is('admin/bhagavadgita/' . BhagavadGita::ADD[URI]) ? 'active' : '' }}"
                                            title="{{ translate('add_new') }}">
                                            <a class="nav-link "
                                                href="{{ route('admin.bhagavadgita.add-new') }}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span
                                                    class="text-truncate">{{ translate('add_new') }}</span>
                                            </a>
                                        </li>
                                        @endif
                                        @if (Helpers::modules_permission_check('Sahitya', 'Bhagavad Geeta', 'view'))
                                        <li class="nav-item {{ Request::is('admin/bhagavadgita/' . BhagavadGita::LIST[URI]) ? 'active' : '' }}"
                                            title="{{ translate('list') }}">
                                            <a class="nav-link "
                                                href="{{ route('admin.bhagavadgita.list') }}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span
                                                    class="text-truncate">{{ translate('list') }}</span>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                                @endif
                                @if (Helpers::modules_check('Valmiki Ramayan'))
                                <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('admin/valmikiramayan*') ? 'active' : '' }}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                        href="javascript:" title="{{ translate('valmiki ramayan') }}">
                                        <img width="20px" height="24px"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/valmiki-ramayan.png') }}"
                                            alt="">
                                        <span
                                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate ml-3">{{ translate('valmiki_ramayan') }}</span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                        style="display: {{ Request::is('admin/valmikiramayan*') ? 'block' : 'none' }}">
                                        <li class="nav-item {{ Request::is('admin/valmikiramayan/' . ValmikiRamayan::LIST[URI]) ? 'active' : '' }}"
                                            title="{{ translate('view') }}">
                                            <a class="nav-link "
                                                href="{{ route('admin.valmikiramayan.view') }}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span
                                                    class="text-truncate">{{ translate('valmiki_ramayan') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endif
                                @if (Helpers::modules_check('Tulsidas Ramayan'))
                                <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('admin/tulsidasramayan*') ? 'active' : '' }}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                        href="javascript:" title="{{ translate('tulsidas ramayan') }}">
                                        <img width="20px" height="24px"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/ramayan.png') }}"
                                            alt="">
                                        <span
                                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate ml-3">{{ translate('tulsidas ramayan') }}</span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                        style="display: {{ Request::is('admin/tulsidasramayan*') ? 'block' : 'none' }}">
                                        <li class="nav-item {{ Request::is('admin/Tulsidasramayan/' . TulsidasRamayan::LIST[URI]) ? 'active' : '' }}"
                                            title="{{ translate('view') }}">
                                            <a class="nav-link "
                                                href="{{ route('admin.tulsidasramayan.view') }}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span
                                                    class="text-truncate">{{ translate('tulsidas_ramayan') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endif
                                @if (Helpers::modules_check('Ram Shalaka'))
                                <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('admin/ramshalaka*') ? 'active' : '' }}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                        href="javascript:" title="{{ translate('ram shalaka') }}">
                                        <img width="20px" height="24px"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/ramayan.png') }}"
                                            alt="">
                                        <span
                                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate ml-3">{{ translate('ram shalaka') }}</span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                        style="display: {{ Request::is('admin/ramshalaka*') ? 'block' : 'none' }}">
                                        <li class="nav-item {{ Request::is('admin/RamShalaka/' . RamShalaka::LIST[URI]) ? 'active' : '' }}"
                                            title="{{ translate('view') }}">
                                            <a class="nav-link "
                                                href="{{ route('admin.ramshalaka.view') }}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span
                                                    class="text-truncate">{{ translate('ram_shalaka') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        {{-- @endif --}}
                        <!--end sahitya section -->
                        <!--start bhagwan-->
                        {{-- @if (Helpers::module_permission_check('bhagwan_images_management')) --}}
                        @if (Helpers::modules_check('Bhagwan'))
                        <li class="nav-item {{ Request::is('admin/bhagwan*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle"
                                title="">{{ translate('bhagwan_images_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/bhagwan*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('bhagwan') }}">
                                <img width="24px" height="24px"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/om.png') }}"
                                    alt="">
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate ml-3">{{ translate('bhagwan') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/bhagwan*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/bhagwan/' . Bhagwan::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.bhagwan.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/bhagwan/' . Bhagwan::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.bhagwan.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/bhagwan/' . Bhagwan::BHAGWANLOGS[URI]) ? 'active' : '' }}"
                                    title="{{ translate('logs') }}">
                                    <a class="nav-link " href="{{ route('admin.bhagwan.bhagwan-logs-list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('logs') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        {{-- @endif --}}
                        <!--end bhagwan-->

                        <!--start jaap-->
                        @if (Helpers::module_permission_check('jaap_management'))
                        <li class="nav-item {{ Request::is('admin/jaap*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle"
                                title="">{{ translate('jaap_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/jaap*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('jaap') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('jaap') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/jaap*') ? 'block' : 'none' }}">

                                <li class="nav-item {{ Request::is('admin/jaap*') ? 'active' : '' }}"
                                    title="{{ translate('jaap') }}">
                                    <a class="nav-link" href="{{ route('admin.jaap.view') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('jaap') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/jaapuser*') ? 'active' : '' }}"
                                    title="{{ translate('jaapuser_setup') }}">
                                    <a class="nav-link" href="{{ route('admin.jaap.jaap-user-list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('jaap_user') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        <!--end jaap-->

                        <!--start visitor-->
                        @if (Helpers::modules_check('Visitor'))

                        <li class="nav-item">
                            <small class="nav-subtitle" title="{{ translate('visitor') }}">
                                {{ translate('visitor') }}
                            </small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu 
                                {{ request()->is('admin/visitor/visitor') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.visitor.visitor-list') }}"
                                title="{{ translate('visitor_list') }}">
                                <i class="tio-person nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('visitor_list') }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        <!--end visitor-->

                        <!-- start astrology -->
                        {{-- @if (Helpers::module_permission_check('astrology_management')) --}}
                        @if (Helpers::modules_check('Rashi'))
                        <li class="nav-item {{ Request::is('admin/rashi*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle"
                                title="">{{ translate('astrology_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/rashi*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('rashi') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('rashi') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/rashi*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Rashi', 'Rashi', 'add'))
                                <li class="nav-item {{ Request::is('admin/rashi/' . Rashi::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.rashi.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Rashi', 'Rashi', 'view'))
                                <li class="nav-item {{ Request::is('admin/rashi/' . Rashi::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.rashi.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Masik Rashi'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/masikrashi*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('masikrashi') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('masikrashi') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/masikrashi*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Masik Rashi', 'Masik Rashi', 'add'))
                                <li class="nav-item {{ Request::is('admin/masikrashi/' . MasikRashi::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.masikrashi.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Masik Rashi', 'Masik Rashi', 'view'))
                                <li class="nav-item {{ Request::is('admin/masikrashi/' . MasikRashi::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.masikrashi.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Varshik Rashi'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/varshikrashi*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('varshikrashi') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('varshikrashi') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/varshikrashi*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Varshik Rashi', 'Varshik Rashi', 'add'))
                                <li class="nav-item {{ Request::is('admin/varshikrashi/' . VarshikRashi::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.varshikrashi.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Varshik Rashi', 'Varshik Rashi', 'view'))
                                <li class="nav-item {{ Request::is('admin/varshikrashi/' . VarshikRashi::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.varshikrashi.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Katha'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/katha*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('katha') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('katha') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/katha*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Katha', 'Katha', 'add'))
                                <li class="nav-item {{ Request::is('admin/katha/' . Katha::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.katha.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Katha', 'Katha', 'view'))
                                <li class="nav-item {{ Request::is('admin/katha/' . Katha::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.katha.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Saptahik Katha'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/saptahikvratkatha*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('saptahikvratkatha') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('saptahikvratkatha') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/saptahikvratkatha*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Saptahik Katha', 'Saptahik Katha', 'add'))
                                <li class="nav-item {{ Request::is('admin/saptahikvratkatha/' . SaptahikVratKatha::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.saptahikvratkatha.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Saptahik Katha', 'Saptahik Katha', 'view'))
                                <li class="nav-item {{ Request::is('admin/saptahikvratkatha/' . SaptahikVratKatha::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.saptahikvratkatha.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Pradosh Katha'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/pradoshkatha*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('pradoshkatha') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('pradoshkatha') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/pradoshkatha*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Pradosh Katha', 'Pradosh Katha', 'add'))
                                <li class="nav-item {{ Request::is('admin/pradoshkatha/' . PradoshKatha::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.pradoshkatha.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Pradosh Katha', 'Pradosh Katha', 'view'))
                                <li class="nav-item {{ Request::is('admin/pradoshkatha/' . PradoshKatha::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.pradoshkatha.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Calendar Day'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/calendarday*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('calendarday') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('calendarday') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/calendarday*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Calendar Day', 'Calendar Day', 'add'))
                                <li class="nav-item {{ Request::is('admin/calendarday/' . CalendarDay::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.calendarday.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Calendar Day', 'Calendar Day', 'view'))
                                <li class="nav-item {{ Request::is('admin/calendarday/' . CalendarDay::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.calendarday.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Calendar Nakshatra'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/calendarnakshatra*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('calendarnakshatra') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('calendarnakshatra') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/calendarnakshatra*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Calendar Nakshatra', 'Calendar Nakshatra', 'add'))
                                <li class="nav-item {{ Request::is('admin/calendarnakshatra/' . CalendarNakshatra::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.calendarnakshatra.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Calendar Nakshatra', 'Calendar Nakshatra', 'view'))
                                <li class="nav-item {{ Request::is('admin/calendarnakshatra/' . CalendarNakshatra::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.calendarnakshatra.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Calendar Vikram Samvat'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/calendarvikramsamvat*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('calendarvikramsamvat') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('calendarvikramsamvat') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/calendarvikramsamvat*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Calendar Vikram Samvat', 'Calendar Vikram Samvat', 'view'))
                                <li class="nav-item {{ Request::is('admin/calendarvikramsamvat/' . CalendarVikramSamvat::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.calendarvikramsamvat.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Calendar Vikram Samvat', 'Calendar Vikram Samvat', 'view'))
                                <li class="nav-item {{ Request::is('admin/calendarvikramsamvat/' . CalendarVikramSamvat::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.calendarvikramsamvat.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if (Helpers::modules_check('Calendar Hindi Month'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/calendarhindimonth*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('calendarhindimonth') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('calendarhindimonth') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/calendarhindimonth*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Calendar Hindi Month', 'Calendar Hindi Month', 'add'))
                                <li class="nav-item {{ Request::is('admin/calendarhindimonth/' . CalendarHindiMonth::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.calendarhindimonth.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Calendar Hindi Month', 'Calendar Hindi Month', 'view'))
                                <li class="nav-item {{ Request::is('admin/calendarhindimonth/' . CalendarHindiMonth::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.calendarhindimonth.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        <!-- <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/festivalhindimonth*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('festivalhindimonth') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('festivalhindimonth') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/festivalhindimonth*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/festivalhindimonth/' . FestivalHindiMonth::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.festivalhindimonth.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/festivalhindimonth/' . FestivalHindiMonth::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.festivalhindimonth.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/festivaladd*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('festivaladd') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('festival_add') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/festivaladd*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/festivaladd/' . FestivalAdd::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.festivaladd.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/festivaladd/' . FestivalAdd::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.festivaladd.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li> -->
                        @if (Helpers::modules_check('Fast Festival'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/festival*') || Request::is('admin/fastfestival*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('fast_festival') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('fast_festival') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/festival*') || Request::is('admin/fastfestival*') ? 'block' : 'none' }}">
                                <!-- <li class="nav-item {{ Request::is('admin/festival/' . Festival::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.festival.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li> -->
                                <!-- <li class="nav-item {{ Request::is('admin/festival/' . Festival::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.festival.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li> -->
                                @if (Helpers::modules_permission_check('Fast Festival', 'Fast Festival List', 'view'))
                                <li class="nav-item {{ Request::is('admin/fastfestival/' . FastFestival::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('fastfestivallist') }}">
                                    <a class="nav-link" href="{{ route('admin.fastfestival.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ translate('fastfestivallist') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        {{-- calculator --}}
                        @if (Helpers::modules_check('Calculator'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/calculator*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('calculator') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('calculator') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/calculator*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Calculator', 'Calculator', 'add'))
                                <li class="nav-item {{ Request::is('admin/calculator/' . Calculator::ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_new') }}">
                                    <a class="nav-link " href="{{ route('admin.calculator.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Calculator', 'Calculator', 'view'))
                                <li class="nav-item {{ Request::is('admin/calculator/' . Calculator::LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('list') }}">
                                    <a class="nav-link " href="{{ route('admin.calculator.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('list') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        <li class="navbar-vertical-aside-has-menu 
                            {{ request()->is('admin/muhurat') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.muhurat.muhurat-list') }}"
                                title="{{ translate('muhurat') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{ translate('muhurat') }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        {{-- @endif --}}
                        {{-- pandit --}}
                        {{-- <li class="navbar-vertical-aside-has-menu {{Request::is('admin/pandit*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{translate('pandits')}}">
                            <i class="tio-star nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('pandits')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('admin/pandit*')?'block':'none'}}">

                            <li class="nav-item {{Request::is('admin/pandit/list')?'active':''}}"
                                title="{{translate('manage_Pandit')}}">
                                <a class="nav-link " href="{{route('admin.pandit.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('manage_Pandit')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/pandit/pending/list')?'active':''}}"
                                title="{{translate('pending_request')}}">
                                <a class="nav-link " href="{{route('admin.pandit.pending.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('pending_request')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/pandit/experties/list')?'active':''}}"
                                title="{{translate('experties')}}">
                                <a class="nav-link " href="{{route('admin.pandit.experties.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('experties')}}</span>
                                </a>
                            </li>
                        </ul>
                        </li> --}}
                        {{-- astrologer --}}
                        @if (Helpers::modules_check('Astrologer & Pandit'))
                        {{-- astrologer --}}
                        <li class="nav-item {{ Request::is('admin/astrologers*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle"
                                title="">{{ translate('astro_/_pandit_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/astrologers*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{ translate('astrologer_/_Pandit') }}">
                                <i class="tio-star nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('astrologer_/_Pandit') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/astrologers*') ? 'block' : 'none' }}">
                                @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Astrologer & Pandit', 'add'))
                                <li class="nav-item {{ Request::is('admin/astrologers/' . Astrologer::MANAGE_ADD[URI]) ? 'active' : '' }}"
                                    title="{{ translate('add_New') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.astrologers.manage.add-new') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_New') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Pending', 'view'))
                                <li class="nav-item {{ Request::is('admin/astrologers/' . Astrologer::PENDING_LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('pending_requests') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.astrologers.pending.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ translate('pending_requests') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Manage', 'view'))
                                <li class="nav-item {{ Request::is('admin/astrologers/' . Astrologer::MANAGE_LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('manage_astrologers') }}">
                                    <a class="nav-link "
                                        href="{{ route('admin.astrologers.manage.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ translate('manage_astrologers') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Block', 'view'))
                                <li class="nav-item {{ Request::is('admin/astrologers/' . Astrologer::BLOCK_LIST[URI]) ? 'active' : '' }}"
                                    title="{{ translate('block_astrologers') }}">
                                    <a class="nav-link " href="{{ route('admin.astrologers.block.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ translate('block_astrologers') }}</span>
                                    </a>
                                </li>
                                @endif
                                {{-- <li class="nav-item {{Request::is('admin/astrologers/'.Astrologer::REVIEW_LIST[URI])?'active':''}}"
                                title="{{translate('reviews')}}">
                                <a class="nav-link " href="{{route('admin.astrologers.review.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('reviews')}}</span>
                                </a>
                        </li> --}}
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Gift', 'view'))
                        <li class="nav-item {{ Request::is('admin/astrologers/' . Astrologer::GIFT_LIST[URI]) ? 'active' : '' }}"
                            title="{{ translate('gifts') }}">
                            <a class="nav-link " href="{{ route('admin.astrologers.gift.list') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{ translate('gifts') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Skill', 'view'))
                        <li class="nav-item {{ Request::is('admin/astrologers/' . Astrologer::SKILL_LIST[URI]) ? 'active' : '' }}"
                            title="{{ translate('skills') }}">
                            <a class="nav-link " href="{{ route('admin.astrologers.skill.list') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{ translate('skills') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::modules_permission_check('Astrologer & Pandit', 'Category', 'view'))
                        <li class="nav-item {{ Request::is('admin/astrologers/' . Astrologer::CATEGORY_LIST[URI]) ? 'active' : '' }}"
                            title="{{ translate('categories') }}">
                            <a class="nav-link "
                                href="{{ route('admin.astrologers.category.list') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{ translate('categories') }}</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item {{ Request::is('admin/astrologers/astro-transection') ? 'active' : '' }}" title="{{translate('pandit_transection')}}">
                            <a class="nav-link " href="{{route('admin.astrologers.manage.astro-transection')}}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{translate('pandit_transection')}}</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::is('admin/astrologers/astro-talk') ? 'active' : '' }}" title="{{translate('astrologer_talk')}}">
                            <a class="nav-link " href="{{route('admin.astrologers.manage.astro-talk')}}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{translate('astrologer_talk')}}</span>
                            </a>
                        </li>
                    </ul>
                    </li>
                    {{-- <li class="navbar-vertical-aside-has-menu {{Request::is('admin/service/tax/*')?'active':''}}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link"
                        href="{{route('admin.service.tax.list')}}"
                        title="{{translate('service_Tax')}}">
                        <i class="tio-category-outlined nav-icon"></i>
                        <span
                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('service_Tax')}}</span>
                    </a>
                    </li> --}}
                    </li>
                    @endif
                    @if (Helpers::modules_check('Customers'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/astrologers/withdraw*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('astro_withdraw') }}">
                            <i class="tio-wallet nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('astro_withdraw') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/astrologers/withdraw/*') ? 'block' : 'none' }}">
                            {{-- @if (Helpers::modules_permission_check('Customers', 'Customer List', 'view')) --}}
                            <li
                                class="nav-item {{ Request::is('admin/astrologers/withdraw/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.astrologers.withdraw.list',0) }}"
                                    title="{{ translate('pending') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{App\Models\Astrologer\AstrologerWithdraw::where('status',0)->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Customers', 'Customer List', 'view')) --}}
                            <li
                                class="nav-item {{ Request::is('admin/astrologers/withdraw/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.astrologers.withdraw.list',1) }}"
                                    title="{{ translate('approved') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('approved') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{App\Models\Astrologer\AstrologerWithdraw::where('status',1)->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Customers', 'Customer List', 'view')) --}}
                            <li
                                class="nav-item {{ Request::is('admin/astrologers/withdraw/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.astrologers.withdraw.list',2) }}"
                                    title="{{ translate('complete') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('complete') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{App\Models\Astrologer\AstrologerWithdraw::where('status',2)->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Customers'))
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/astrologers/withdraw*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Astro/Pandit Puja Order') }}">
                            <i class="tio-wallet nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Astro_/_Pandit_Puja_Order') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/pandit*') ? 'block' : 'none' }}">
                            <li class="nav-item {{ Request::is('admin/pandit/orders/list') ? 'active' : '' }}"
                                title="{{ translate('pandit_puja_orders') }}">
                                <a class="nav-link" href="{{ route('admin.pandit.orders.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditpooja')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                           
                            <li class="nav-item {{ Request::is('admin/pandit/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link "    href="{{ route('admin.pandit.orders.list', 0) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditpooja')->where('status', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                           
                            <li class="nav-item {{ Request::is('admin/pandit/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pandit.orders.list', 1) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditpooja')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                           
                            <li class="nav-item {{ Request::is('admin/pandit/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pandit.orders.list', 2) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditpooja')->where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                           
                            <li class="nav-item {{ Request::is('admin/pandit/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link " href="{{ route('admin.pandit.orders.list', 6) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Rejected') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditpooja')->where('status', 6)->where('order_status', 6)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                          
                            <li class="nav-item {{ Request::is('admin/pandit/orders/lead/list') ? 'active' : '' }}"
                                title="{{ translate('pandit_pooja_leads') }}">
                                <a class="nav-link " href="{{ route('admin.pandit.orders.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'panditpooja')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                           
                          
                            <li class="nav-item {{ Request::is('admin/pooja/orderbycompleted*') ? 'active' : '' }}"
                                title="{{ translate('order_by_completed') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pooja.orders.orderbycompleted') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('order_by_completed') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditpooja')->where('status', 1)->where('is_completed', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    {{-- @if (Helpers::modules_check('Consultation Order')) --}}
                    {{-- counselling order --}}
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/pandit/counselling/order*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('astro/Pandit_Consultancy_Orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('astro_/_Pandit_Consultancy_Orders') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/pandit/counselling/order*') ? 'block' : 'none' }}">
                            {{-- @if (Helpers::modules_permission_check('Consultation Order', 'All', 'view')) --}}
                            <li class="nav-item {{ Request::is('admin/counselling/order/list') ? 'active' : '' }}"
                                title="{{ translate('pandit_Consultancy_Orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pandit.counselling.order.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditcounselling')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Consultation Order', 'Pending', 'view')) --}}
                            <li class="nav-item {{ Request::is('admin/pandit/counselling/order/list') ? 'active' : '' }}"
                                title="{{ translate('pandit_Consultancy_Orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pandit.counselling.order.list', 0) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditcounselling')->where('status', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Consultation Order', 'Completed', 'view')) --}}
                            <li class="nav-item {{ Request::is('admin/pandit/counselling/order/list') ? 'active' : '' }}"
                                title="{{ translate('pandit_Consultancy_Orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pandit.counselling.order.list', 1) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'panditcounselling')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Consultation Order', 'Canceled', 'view')) --}}
                            <li class="nav-item {{ Request::is('admin/pandit/counselling/order/list') ? 'active' : '' }}"
                                title="{{ translate('pandit_Consultancy_Orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pandit.counselling.order.list', 2) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'pandit')->where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Consultation Order', 'Lead', 'view')) --}}
                            <li class="nav-item {{ Request::is('admin/pandit/counselling/order/lead/list') ? 'active' : '' }}"
                                title="{{ translate('pandit_Consultancy_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pandit.counselling.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'panditcounselling')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                        </ul>
                    </li>
                    {{-- @endif --}}

                    {{-- pandit transaction --}}
                    {{-- @if (Helpers::modules_permission_check('Service Tax', 'Service Tax', 'view')) --}}
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/astrologers/manage/guruji-transaction/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.astrologers.manage.guruji.transaction') }}"
                            title="{{ translate('guruji_transaction') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('astro_transaction') }}</span>
                        </a>
                    </li>
                    {{-- @endif --}}

                    <!-- end astrology -->
                    {{-- service tax --}}
                    @if (Helpers::modules_check('Service Tax'))
                    <li class="nav-item {{ Request::is('admin/service/tax*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">{{ translate('service_Tax') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Service Tax', 'Service Tax', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/service/tax/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.service.tax.list') }}"
                            title="{{ translate('service_Tax') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('service_Tax') }}</span>
                        </a>
                    </li>
                    @endif
                    @endif
                    {{-- remote access --}}
                    @if (Helpers::modules_check('Remote Access'))
                    <li class="nav-item {{ Request::is('admin/remote/access*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">{{ translate('Remote Access') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Remote Access', 'Remote Access', 'view'))
                    {{-- <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/remote/access/list/*') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link"
                        href="{{ route('admin.remote.access.list') }}" title="{{ translate('Remote Access') }}">
                        <i class="tio-category-outlined nav-icon"></i>
                        <span
                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Remote Access') }}</span>
                    </a>
                    </li> --}}
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/remote/access/list/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="javascript:0"
                            title="{{ translate('Remote Access') }}" onclick="RemoteAccessModal()">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Remote Access') }}</span>
                        </a>
                    </li>
                    @endif
                    @endif
                    {{-- logs --}}
                    @if (Helpers::modules_check('Logs'))
                    <li class="nav-item {{ Request::is('admin/logs*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">{{ translate('logs') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Logs', 'Auth Logs', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/auth/logs*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.auth.logs') }}"
                            title="{{ translate('auth_Logs') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('auth_Logs') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Logs', 'Logs', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/logs*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.logs') }}" title="{{ translate('logs') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('logs') }}</span>
                        </a>
                    </li>
                    @endif
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/pwd-change-logs*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.pwd-change-logs') }}"
                            title="{{ translate('pwd_Change_Logs') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('pwd_Change_Logs') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Temple'))
                    <li
                        class="nav-item {{ Request::is('admin/cities*') || Request::is('admin/temple/category*') || Request::is('admin/temple/hotel*') || Request::is('admin/temple/restaurants*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('temple_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Temple', 'City', 'add') ||
                    Helpers::modules_permission_check('Temple', 'City', 'view') ||
                    Helpers::modules_permission_check('Temple', 'City', 'review'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/cities*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('citis') }}">
                            <i class="tio-star nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('cities') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/cities*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Temple', 'City', 'add'))
                            <li class="nav-item {{ Request::is('admin/cities/view') ? 'active' : '' }}"
                                title="{{ translate('Cities') }}">
                                <a class="nav-link" href="{{ route('admin.cities.view') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add new') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'City', 'view'))
                            <li class="nav-item {{ Request::is('admin/cities/list') ? 'active' : '' }}"
                                title="{{ translate('list') }}">
                                <a class="nav-link" href="{{ route('admin.cities.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'City', 'review'))
                            <li class="nav-item {{ Request::is('admin/cities/review') ? 'active' : '' }}"
                                title="{{ translate('review') }}">
                                <a class="nav-link" href="{{ route('admin.cities.review') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('review') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- category -->
                    @if (Helpers::modules_permission_check('Temple', 'Category', 'add') ||
                    Helpers::modules_permission_check('Temple', 'Category', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/temple/category*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Temple_Category') }}">
                            <i class="tio-star nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Temple_Category') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/temple/category*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Temple', 'Category', 'add'))
                            <li class="nav-item {{ Request::is('admin/temple/category/add_category') ? 'active' : '' }}"
                                title="{{ translate('temple_category') }}">
                                <a class="nav-link" href="{{ route('admin.temple.category.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_Category') }}</span>
                                </a>
                            </li>
                            @endif

                            @if (Helpers::modules_permission_check('Temple', 'Category', 'view'))
                            <li class="nav-item {{ Request::is('admin/temple/category/list') ? 'active' : '' }}"
                                title="{{ translate('list') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.temple.category.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('list') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!--  -->
                    @if (Helpers::modules_permission_check('Temple', 'Temple', 'add') ||
                    Helpers::modules_permission_check('Temple', 'Temple', 'view') ||
                    Helpers::modules_permission_check('Temple', 'Temple', 'review'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/temple*') && !Request::is('admin/temple/category*') && !Request::is('admin/temple/hotel*') && !Request::is('admin/temple/restaurants*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('temple') }}">
                            <i class="tio-star nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('temple') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/temple*') && !Request::is('admin/temple/category*') && !Request::is('admin/temple/hotel*') && !Request::is('admin/temple/restaurants*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Temple', 'Temple', 'add'))
                            <li class="nav-item {{ Request::is('admin/temple/add_temple') ? 'active' : '' }}"
                                title="{{ translate('temple') }}">
                                <a class="nav-link" href="{{ route('admin.temple.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add new') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'Temple', 'view'))
                            <li class="nav-item {{ Request::is('admin/temple/templepackage') ? 'active' : '' }}"
                                title="{{ translate('temple_package') }}">
                                <a class="nav-link" href="{{ route('admin.temple.templepackage') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('temple_package') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'Temple', 'view'))
                            <li class="nav-item {{ Request::is('admin/temple/templepackageprice') ? 'active' : '' }}"
                                title="{{ translate('temple_package_price') }}">
                                <a class="nav-link" href="{{ route('admin.temple.templepackageprice') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('temple_package_price') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'Temple', 'view'))
                            <li class="nav-item {{ Request::is('admin/temple/list') ? 'active' : '' }}"
                                title="{{ translate('list') }}">
                                <a class="nav-link" href="{{ route('admin.temple.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'Temple', 'review'))
                            <li class="nav-item {{ Request::is('admin/temple/review') ? 'active' : '' }}"
                                title="{{ translate('review') }}">
                                <a class="nav-link" href="{{ route('admin.temple.review') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('review') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    {{-- collector --}}
                    {{-- @if (Helpers::modules_check('Service Tax')) --}}
                    <li class="nav-item {{ Request::is('admin/collector*') || Request::is('admin/sdm*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">{{ translate('collector') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    {{-- @if (Helpers::modules_permission_check('Service Tax', 'Service Tax', 'view')) --}}

                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/collector/*') || Request::is('admin/sdm/*') || Request::is('admin/sdm/employee/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('collector') }}">
                            <i class="tio-star nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('collector') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{  Request::is('admin/collector/*') || Request::is('admin/sdm/*') || Request::is('admin/sdm/employee/*') ? 'block' : 'none' }}">
                            {{-- @if (Helpers::modules_permission_check('Temple', 'Temple', 'add')) --}}
                            <li class="nav-item {{ Request::is('admin/collector/*') ? 'active' : '' }}"
                                title="{{ translate('add_collector') }}">
                                <a class="nav-link" href="{{ route('admin.collector.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_collector') }}</span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Temple', 'Temple', 'add')) --}}
                            <li class="nav-item {{ Request::is('admin/sdm/list') || Request::is('admin/sdm/edit') || Request::is('admin/sdm/view') ? 'active' : '' }}"
                                title="{{ translate('add_sdm') }}">
                                <a class="nav-link" href="{{ route('admin.sdm.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_sdm') }}</span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Temple', 'Temple', 'add')) --}}
                            <li class="nav-item {{ Request::is('admin/sdm/employee/list') || Request::is('admin/sdm/employee/edit') || Request::is('admin/sdm/employee/view') ? 'active' : '' }}"
                                title="{{ translate('add_sdm_employee') }}">
                                <a class="nav-link" href="{{ route('admin.sdm.employee.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_sdm_employee') }}</span>
                                </a>
                            </li>
                            {{-- @endif --}}
                        </ul>
                    </li>
                    {{-- @endif --}}

                    <!-- Hotel -->
                    @if (Helpers::modules_permission_check('Temple', 'Hotel', 'add') ||
                    Helpers::modules_permission_check('Temple', 'Hotel', 'view') ||
                    Helpers::modules_permission_check('Temple', 'Hotel', 'review'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/temple/hotel*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('temple') }}">
                            <i class="tio-hotel nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Hotel') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/temple/hotel*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Temple', 'Hotel', 'add'))
                            <li class="nav-item {{ Request::is('admin/temple/hotel/add-hotel') ? 'active' : '' }}"
                                title="{{ translate('Add_hotel') }}">
                                <a class="nav-link" href="{{ route('admin.temple.hotel.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_Hotel') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'Hotel', 'view'))
                            <li class="nav-item {{ Request::is('admin/temple/hotel/list') ? 'active' : '' }}"
                                title="{{ translate('hotel_list') }}">
                                <a class="nav-link" href="{{ route('admin.temple.hotel.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('hotel_list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'Hotel', 'review'))
                            <li class="nav-item {{ Request::is('admin/temple/hotel/review') ? 'active' : '' }}"
                                title="{{ translate('review') }}">
                                <a class="nav-link" href="{{ route('admin.temple.hotel.review') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('review') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- restaurants -->
                    @if (Helpers::modules_permission_check('Temple', 'Restaurant', 'add') ||
                    Helpers::modules_permission_check('Temple', 'Restaurant', 'view') ||
                    Helpers::modules_permission_check('Temple', 'Restaurant', 'review'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/temple/restaurants*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('temple') }}">
                            <i class="tio-restaurant nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Restaurants') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/temple/restaurants*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Temple', 'Restaurant', 'add'))
                            <li class="nav-item {{ Request::is('admin/temple/restaurants/add_restaurant') ? 'active' : '' }}"
                                title="{{ translate('Add_restaurant') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.temple.restaurants.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_Restaurants') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'Restaurant', 'view'))
                            <li class="nav-item {{ Request::is('admin/temple/restaurants/list') ? 'active' : '' }}"
                                title="{{ translate('restaurants_list') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.temple.restaurants.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('restaurants_list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Temple', 'Restaurant', 'review'))
                            <li class="nav-item {{ Request::is('admin/temple/restaurants/review') ? 'active' : '' }}"
                                title="{{ translate('review') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.temple.restaurants.review') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('review') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @endif
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/temple/darshan-bookings*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.temple.darshan-bookings.booking-list') }}" title="{{ translate('vip_darshan_booking') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('vip_darshan_booking') }}
                                <span class="badge badge-soft-info badge-pill ml-1">
                                    {{ \App\Models\DarshanOrder::where('status',1)->count() }}
                                </span>
                            </span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/temple/darshan-leads*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.temple.darshan-leads.leads-list') }}" title="{{ translate('darshan_Leads') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('darshan_Leads') }}
                                <span class="badge badge-soft-info badge-pill ml-1">
                                    {{ \App\Models\TempleDarshanLead::where('status',0)->count() }}
                                </span>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <small class="nav-subtitle"
                            title="">{{ translate('States') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/state/list*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.state.list') }}" title="{{ translate('state_list') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('state_list') }}                               
                            </span>
                        </a>
                    </li>
                    <!-- start event_managment  -->
                    @if (Helpers::modules_check('Event'))
                    <li class="nav-item {{ Request::is('admin/event-managment*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('event_managment') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Event', 'Category', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/category*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.event-managment.category.add') }}"
                            title="{{ translate('event_category') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('event_category') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Event', 'Package', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/event_package*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.event-managment.event_package.add') }}"
                            title="{{ translate('package') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('packages') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Event', 'Organizer', 'add') ||
                    Helpers::modules_permission_check('Event', 'Organizer', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/organizers*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('event_Organizer') }}">
                            <i class="tio-flare nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('event_Organizers') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/event-managment/organizers*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Event', 'Organizer', 'add'))
                            <li class="nav-item {{ Request::is('admin/event-managment/organizers/add') ? 'active' : '' }}"
                                title="{{ translate('add_Organizer') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.event-managment.organizers.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_Organizer') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Event', 'Organizer', 'view'))
                            <li class="nav-item {{ Request::is('admin/event-managment/organizers/list') ? 'active' : '' }}"
                                title="{{ translate('Organizers_list') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.event-managment.organizers.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('Organizers_list') }}
                                        <?php
                                        $gettransactionRequest = \App\Models\EventApproTransaction::where(['types' => 'withdrawal', 'status' => 0])->first();
                                        ?>
                                        @if (!empty($gettransactionRequest))
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            <i
                                                class='tio-notifications_on_outlined'>notifications_on_outlined</i>
                                        </span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Event', 'Artist', 'view'))
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/event/artist') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.event-managment.event.artist') }}" title="{{ translate('artists_Management') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('artists_Management') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Event', 'Event', 'add') ||
                    Helpers::modules_permission_check('Event', 'List', 'view') ||
                    Helpers::modules_permission_check('Event', 'Pending', 'view') ||
                    Helpers::modules_permission_check('Event', 'Upcoming', 'view') ||
                    Helpers::modules_permission_check('Event', 'Booking', 'view') ||
                    Helpers::modules_permission_check('Event', 'Completed', 'view') ||
                    Helpers::modules_permission_check('Event', 'Canceled', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/event*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Event_Management') }}">
                            <i class="tio-flare nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Event_Management') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/event-managment/event*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Event', 'Event', 'add'))
                            <li class="nav-item {{ Request::is('admin/event-managment/event/add') ? 'active' : '' }}"
                                title="{{ translate('add_event') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.event-managment.event.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_event') }}</span>
                                </a>
                            </li>
                            @endif
                            <!--  -->
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/event/list*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:" title="{{ translate('Event_List') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Event_List') }}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/event-managment/event*') ? 'block' : 'none' }}">
                                    @if (Helpers::modules_permission_check('Event', 'List', 'view'))
                                    <li class="nav-item {{ Request::is('admin/event-managment/event/list') ? 'active' : '' }}"
                                        title="{{ translate('event_list') }}">
                                        <a class="nav-link"
                                            href="{{ route('admin.event-managment.event.list') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('list') }}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                    {{ \App\Models\Events::count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (Helpers::modules_permission_check('Event', 'Pending', 'view'))
                                    <li class="nav-item {{ Request::is('admin/event-managment/event/pending') ? 'active' : '' }}"
                                        title="{{ translate('pending') }}">
                                        <a class="nav-link"
                                            href="{{ route('admin.event-managment.event.pending') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('Pending') }}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                    {{ (\App\Models\Events::whereIn('status', [0, 1])->whereIn('is_approve', [0, 2, 3, 4])->count() ??0) +(\App\Models\Events::where('status', 0)->where('is_approve', 1)->count() ?? 0) }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (Helpers::modules_permission_check('Event', 'Upcoming', 'view'))
                                    <li class="nav-item {{ Request::is('admin/event-managment/event/upcomming') ? 'active' : '' }}"
                                        title="{{ translate('upcomming') }}">
                                        <a class="nav-link"
                                            href="{{ route('admin.event-managment.event.upcomming') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('Upcomming') }}
                                                <span class="badge badge-soft-primary badge-pill ml-1">
                                                    {{ \App\Models\Events::where('is_approve', 1)->where('status', 1)->whereRaw(
                                                                            "
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        DATE(?) < STR_TO_DATE(
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            IF(INSTR(start_to_end_date, ' - ') > 0, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            SUBSTRING_INDEX(start_to_end_date, ' - ', 1), 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            start_to_end_date
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ), '%Y-%m-%d')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ",
                                                                            [now()->format('Y-m-d')],
                                                                        )->count() }}

                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (Helpers::modules_permission_check('Event', 'Booking', 'view'))
                                    <li class="nav-item {{ Request::is('admin/event-managment/event/booking') ? 'active' : '' }}"
                                        title="{{ translate('booking') }}">
                                        <a class="nav-link"
                                            href="{{ route('admin.event-managment.event.booking') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('booking') }}
                                                <span
                                                    class="badge badge-soft-secondary badge-pill ml-1">
                                                    {{ \App\Models\Events::where('is_approve', 1)->where('status', 1)->where(function ($query) {
                                                                            $query->whereRaw(
                                                                                    "
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            DATE(?) BETWEEN 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', 1), '%Y-%m-%d') 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            AND 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', -1), '%Y-%m-%d')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ",
                                                                                    [now()->format('Y-m-d')],
                                                                                )->orWhereRaw(
                                                                                    "
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            DATE(?) = 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            STR_TO_DATE(start_to_end_date, '%Y-%m-%d')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ",
                                                                                    [now()->format('Y-m-d')],
                                                                                );
                                                                        })->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (Helpers::modules_permission_check('Event', 'Completed', 'view'))
                                    <li class="nav-item {{ Request::is('admin/event-managment/event/completed') ? 'active' : '' }}"
                                        title="{{ translate('completed') }}">
                                        <a class="nav-link"
                                            href="{{ route('admin.event-managment.event.completed') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('completed') }}
                                                <span class="badge badge-soft-primary badge-pill ml-1">
                                                    {{ \App\Models\Events::where('is_approve', 1)->where('status', 1)->whereRaw(
                                                                            "DATE(?) > STR_TO_DATE(
                                                                            IF(INSTR(start_to_end_date, ' - ') > 0, 
                                                                            SUBSTRING_INDEX(start_to_end_date, ' - ', -1),start_to_end_date), '%Y-%m-%d')",
                                                                            [now()->format('Y-m-d')],
                                                                        )->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (Helpers::modules_permission_check('Event', 'Canceled', 'view'))
                                    <li class="nav-item {{ Request::is('admin/event-managment/event/canceled') ? 'active' : '' }}"
                                        title="{{ translate('cancel') }}">
                                        <a class="nav-link"
                                            href="{{ route('admin.event-managment.event.canceled') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('canceled') }}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                    {{ \App\Models\Events::where('status', 2)->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Event', 'Booking', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/event-booking/list') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.event-managment.event-booking.list') }}"
                            title="{{ translate('event_Bookings_list') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('event_Bookings_list') }}

                            </span>
                        </a>
                    </li>
                    @endif

                    @if (Helpers::modules_permission_check('Event', 'Lead', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/leads*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.event-managment.leads.list') }}"
                            title="{{ translate('Leads') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Event_Leads') }}
                                <span class="badge badge-soft-info badge-pill ml-1">
                                    {{ \App\Models\EventLeads::whereIn('status',[0,2])->where('test',1)->count() }}
                                </span>
                            </span>
                        </a>
                    </li>
                    @endif

                    @if (Helpers::modules_permission_check('Event', 'Withdraw', 'view'))
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/event-withdrawal*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.event-managment.event-withdrawal.list') }}"
                            title="{{ translate('Event_Withdrawal') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Event_Withdrawal') }}</span>
                        </a>
                    </li>
                    @endif
                    @endif
                    <!-- end event_managment -->
                    <!-- start Kundali -->
                    @if (Helpers::modules_check('Birth Journal'))
                    <li class="nav-item {{ Request::is('admin/birth_journal*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">{{ translate('Birth_Journal') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'add') ||
                    Helpers::modules_permission_check('Birth Journal', 'Kundali', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/birth_journal/add') || Request::is('admin/birth_journal/list') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('kundali') }}">
                            <i class="tio-book_bookmarked nav-icon">book_bookmarked</i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('kundali') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/birth_journal/add') || Request::is('admin/birth_journal/list') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'add'))
                            <li class="nav-item {{ Request::is('admin/birth_journal/add') ? 'active' : '' }}"
                                title="{{ translate('add_kundali') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.birth_journal.add_kundali') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_kundali') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Birth Journal', 'Kundali', 'view'))
                            <li class="nav-item {{ Request::is('admin/birth_journal/list') ? 'active' : '' }}"
                                title="{{ translate('kundali_list') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.birth_journal.kundali_list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('kundali_list') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Birth Journal', 'Lead', 'view'))
                    <li class="nav-item {{  Request::is('admin/birth_journal/kundli_leads') || Request::is('admin/birth_journal/kundli_leads/view') ? 'active' : '' }}"
                        title="{{ translate('kundali_leads') }}">
                        <a class="nav-link"
                            href="{{ route('admin.birth_journal.kundli_leads') }}">
                            <span class="tio-circle nav-indicator-icon"></span>
                            <span class="text-truncate">
                                {{ translate('kundali_leads') }}
                                <span class="badge badge-soft-info badge-pill ml-1">
                                    {{ \App\Models\KundaliLeads::whereIn('status', [0, 2])->where('payment_status', '!=', '1')->count() }}
                                </span>
                            </span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Birth Journal', 'Paid Kundali', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/birth_journal/paid-kundali') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.birth_journal.paid_kundli') }}"
                            title="{{ translate('paid_kundali') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('Paid_Kundali') }}
                                <span class="badge badge-soft-success badge-pill ml-1">
                                    {{ \App\Models\BirthJournalKundali::where(['payment_status' => '1'])->whereHas('birthJournal_kundali', function ($query) {
                                                        $query->where('name', 'kundali');
                                                    })->count() }}
                                </span>
                            </span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'view') ||
                    Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'view') ||
                    Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/birth_journal/orders*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('order') }}">
                            <i class="tio-shopping_cart_add nav-icon">shopping_cart_add</i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Paid_Kundali_milan') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/birth_journal/orders/order-list') || Request::is('admin/birth_journal/orders/order-pending') || Request::is('admin/birth_journal/orders/order-completed') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan All', 'view'))
                            <li class="nav-item {{ Request::is('admin/birth_journal/orders/order-list') ? 'active' : '' }}"
                                title="{{ translate('all_list') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.birth_journal.orders.all_list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('all_list') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\BirthJournalKundali::where('payment_status', 1)->whereHas('birthJournal_kundalimilan', function ($query) {
                                                                    $query->where('name', 'kundali_milan');
                                                                })->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Pending', 'view'))
                            <li class="nav-item {{ Request::is('admin/birth_journal/orders/order-pending*') ? 'active' : '' }}"
                                title="{{ translate('pending') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.birth_journal.orders.pending') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('pending') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\BirthJournalKundali::where('payment_status', 1)->where('milan_verify', 0)->whereHas('birthJournal_kundalimilan', function ($query) {
                                                                    $query->where('name', 'kundali_milan');
                                                                })->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Birth Journal', 'Kundali Milan Completed', 'view'))
                            <li class="nav-item {{ Request::is('admin/birth_journal/orders/order-completed*') ? 'active' : '' }}"
                                title="{{ translate('completed') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.birth_journal.orders.completed') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\BirthJournalKundali::where('payment_status', 1)->where('milan_verify', 1)->whereHas('birthJournal_kundalimilan', function ($query) {
                                                                    $query->where('name', 'kundali_milan');
                                                                })->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @endif
                    <!-- end Kundali -->
                    <!-- start Donate donate_management -->
                    @if (Helpers::modules_check('Donate'))
                    <li class="nav-item {{ Request::is('admin/donate_management*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">{{ translate('Donation') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Donate', 'Category', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/donate_management/category*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.donate_management.category.add') }}"
                            title="{{ translate('Donation_category') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Donation_category') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Donate', 'Purpose', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/donate_management/purpose*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.donate_management.purpose.add') }}"
                            title="{{ translate('Donation_Purpose') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Donation_Purpose') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Donate', 'Trust', 'add') ||
                    Helpers::modules_permission_check('Donate', 'Trust All', 'view') ||
                    Helpers::modules_permission_check('Donate', 'Trust Approved', 'view') ||
                    Helpers::modules_permission_check('Donate', 'Trust Pending', 'view') ||
                    Helpers::modules_permission_check('Donate', 'Trust Canceled', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/donate_management/trust*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Trust') }}">
                            <span class="tio-circle nav-indicator-icon"></span>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Trust') }}
                                @if (\App\Models\DonateAllTransaction::where('amount_status', 0)->where('type', 'withdrawal')->count() > 0)
                                <span class="badge badge-soft-danger badge-pill ml-1">
                                    <i class="tio-saving_outlined">saving_outlined</i>
                                </span>
                                @endif
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/donate_management/trust*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Donate', 'Trust', 'add'))
                            <li class="nav-item {{ Request::is('admin/donate_management/trust/add-trust') ? 'active' : '' }}"
                                title="{{ translate('Add_Trust') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.donate_management.trust.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('add_Trust') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Donate', 'Trust All', 'view'))
                            <li class="nav-item {{ Request::is('admin/donate_management/trust/trust-list') ? 'active' : '' }}"
                                title="{{ translate('Trust_list') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.donate_management.trust.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('Trust_list') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\DonateTrust::count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Donate', 'Trust Approved', 'view'))
                            <li class="nav-item {{ Request::is('admin/donate_management/trust/trust-approved') ? 'active' : '' }}"
                                title="{{ translate('approved') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.donate_management.trust.approved') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('Approved') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\DonateTrust::where('is_approve', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Donate', 'Trust Pending', 'view'))
                            <li class="nav-item {{ Request::is('admin/donate_management/trust/trust-pending') ? 'active' : '' }}"
                                title="{{ translate('pending') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.donate_management.trust.pending') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('Pending') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\DonateTrust::where('is_approve', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Donate', 'Trust Canceled', 'view'))
                            <li class="nav-item {{ Request::is('admin/donate_management/trust/trust-canceled') ? 'active' : '' }}"
                                title="{{ translate('cancel') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.donate_management.trust.canceled') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\DonateTrust::where('is_approve', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Donate', 'Ads', 'add') ||
                    Helpers::modules_permission_check('Donate', 'Ads', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/donate_management/ad_trust*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('ads_Trust') }}">
                            <span class="tio-circle nav-indicator-icon"></span>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Ads_Trust') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/donate_management/ad_trust*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Donate', 'Ads', 'add'))
                            <li class="nav-item {{ Request::is('admin/donate_management/ad_trust/create-ads') ? 'active' : '' }}"
                                title="{{ translate('Ad_Trust') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.donate_management.ad_trust.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('add_ad_Trust') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Donate', 'Ads', 'view'))
                            <li class="nav-item {{ Request::is('admin/donate_management/ad_trust/ads-list') ? 'active' : '' }}"
                                title="{{ translate('Ad_Trust_List') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.donate_management.ad_trust.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('ads_List') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/donate_management/donated/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.donate_management.donated.list') }}"
                            title="{{ translate('all_Donations') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('all_Donations') }}
                                <span class="badge badge-soft-info badge-pill ml-1">
                                    {{ \App\Models\DonateAllTransaction::whereIn('type',['donate_trust','donate_ads'])->where('amount_status',1)->count() }}
                                </span></span>
                        </a>
                    </li>


                    @if (Helpers::modules_permission_check('Donate', 'Lead', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/donate_management/donate_lead*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.donate_management.donate_lead.list') }}" title="{{ translate('Donation_leads') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('Donation_leads') }}
                                <span class="badge badge-soft-info badge-pill ml-1">
                                    {{ \App\Models\DonateLeads::whereIn('status', [0, 2])->count() }}
                                </span>
                            </span>
                        </a>
                    </li>
                    @endif

                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/donate_management/trustees-puja-booking*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.donate_management.trustees-puja-booking.index') }}"
                            title="{{ translate('trust_puja_Booking') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('trust_puja_Booking') }}
                            </span>
                        </a>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/donate_management/trustees-withdrawal*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.donate_management.trustees-withdrawal.index') }}"
                            title="{{ translate('trustees_withdrawal') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('trustees_withdrawal') }}
                                @if(\App\Models\WithdrawalAmountHistory::where(['type' => "trust"])->where('status',0)->exists())
                                <span class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                @endif
                            </span>
                        </a>
                    </li>
                    @endif
                    <!-- end Donate donate_management -->

                    <!--start tour & travel -->
                    {{-- @if (Helpers::module_permission_check('tour_and_travels_management')) --}}
                    @if (Helpers::modules_check('Tour'))
                    <li class="nav-item {{ Request::is('admin/tour_and_travels*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('Tour_&_Travels') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Tour', 'Tour Package', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_package*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.tour_package.view') }}"
                            title="{{ translate('package') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Tour_Package') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Tour', 'Tour Vehicle Type', 'view'))
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_vehicle_setting*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.tour_vehicle_setting.view') }}"
                            title="{{ translate('tour_vehicle_type') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Tour_vehicle_type') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Tour', 'Tour Vehicle Setting', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_cab_service*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.tour_cab_service.view') }}"
                            title="{{ translate('Tour_vehicle_setting') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Tour_vehicle_setting') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Tour', 'Tour Category', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_type*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.tour_type.view') }}"
                            title="{{ translate('tour_Category') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('tour_Category') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Tour', 'Tour Refund Policy', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour-refund-policy*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.tour-refund-policy.list') }}"
                            title="{{ translate('tour_refund_policy') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('tour_refund_policy') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'add') || Helpers::modules_permission_check('Tour', 'Travel Agent', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_and_travels*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('travel_Agents') }}">
                            <i class="tio-moon nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('travel_Agents') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/tour_and_travels*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'add'))
                            <li class="nav-item {{ Request::is('admin/tour_and_travels/add-traveller') ? 'active' : '' }}"
                                title="{{ translate('Add') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.tour_and_travels.add-traveller') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Add') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'view'))
                            <li class="nav-item {{ Request::is('admin/tour_and_travels/traveller-list') ? 'active' : '' }}"
                                title="{{ translate('list') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.tour_and_travels.traveller-list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('list') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if (Helpers::modules_permission_check('Tour', 'Tour Manage', 'add') || Helpers::modules_permission_check('Tour', 'Tour Manage', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_visits*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('tours_management') }}">
                            <i class="tio-moon nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('tours_management') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/tour_visits*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Tour', 'Tour Manage', 'add'))
                            <li class="nav-item {{ Request::is('admin/tour_visits/add-tour') ? 'active' : '' }}"
                                title="{{ translate('Add') }}">
                                <a class="nav-link" href="{{ route('admin.tour_visits.add-tour') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Add') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Tour', 'Tour Manage', 'view'))
                            <li class="nav-item {{ Request::is('admin/tour_visits/tour-list') ? 'active' : '' }}"
                                title="{{ translate('list') }}">
                                <a class="nav-link" href="{{ route('admin.tour_visits.tour-list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('list') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if (Helpers::modules_permission_check('Tour', 'Travel Booking', 'all') || Helpers::modules_permission_check('Tour', 'Travel Booking', 'pending') || Helpers::modules_permission_check('Tour', 'Travel Booking', 'confirm') || Helpers::modules_permission_check('Tour', 'Travel Booking', 'complete') || Helpers::modules_permission_check('Tour', 'Travel Booking', 'cancel'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour-visits-booking*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('tours Booking') }}">
                            <i class="tio-moon nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('tours Booking') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/tour-visits-booking*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Tour', 'Travel Booking', 'all'))
                            <li class="nav-item {{ Request::is('admin/tour-visits-booking/all-list') ? 'active' : '' }}"
                                title="{{ translate('all_booking') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.tour-visits-booking.all-list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all_booking') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\TourOrder::where('amount_status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif

                            @if (Helpers::modules_permission_check('Tour', 'Travel Booking', 'pending'))
                            <li class="nav-item {{ Request::is('admin/tour-visits-booking/pending-booking') ? 'active' : '' }}"
                                title="{{ translate('pending_booking') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.tour-visits-booking.pending-booking') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending_booking') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\TourOrder::where('amount_status', 1)->whereIn('status', [1, 0])->where('cab_assign', '==', '0')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Tour', 'Travel Booking', 'confirm'))
                            <li class="nav-item {{ Request::is('admin/tour-visits-booking/confirm-booking') ? 'active' : '' }}"
                                title="{{ translate('confirm_booking') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.tour-visits-booking.confirm-booking') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('confirm_booking') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\TourOrder::where('amount_status', 1)->where('status', 1)->where('drop_status',0)->where('cab_assign', '!=', '0')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Tour', 'Travel Booking', 'complete'))
                            <li class="nav-item {{ Request::is('admin/tour-visits-booking/complete-booking') ? 'active' : '' }}"
                                title="{{ translate('complete_booking') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.tour-visits-booking.complete-booking') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('complete_booking') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\TourOrder::where('amount_status', 1)->where('status', 1)->where(['pickup_status' => 1, 'drop_status' => 1])->where('cab_assign', '!=', '0')->count() }}
                                        </span>
                                    </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Tour', 'Travel Booking', 'cancel'))
                            <li class="nav-item {{ Request::is('admin/tour-visits-booking/cancel-booking') ? 'active' : '' }}"
                                title="{{ translate('cancel_booking') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.tour-visits-booking.cancel-booking') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('cancel_booking') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\TourOrder::where('amount_status', 1)->where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if (Helpers::modules_permission_check('Tour', 'Tour Lead', 'view'))
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_visits/leads*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.tour_visits.leads') }}"
                            title="{{ translate('Tour_leads') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Tour_leads') }}
                                <span class="badge badge-soft-info badge-pill ml-1">
                                    {{ \App\Models\TourLeads::count() }}
                                </span>
                            </span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Tour', 'Tour Withdraw', 'view'))
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_withdrawal*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.tour_withdrawal.index') }}"
                            title="{{ translate('tour_withdrawal') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('tour_withdrawal') }}</span>
                        </a>
                    </li>
                    @endif
                    @endif

                    <li class="nav-item {{ (Request::is('admin/self-driving-management*') || Request::is('admin/driving-policy*') || Request::is('admin/driving-cancellation-policy*')) ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('self_Driving_Management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/driving-policy/driving-policy') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.driving-policy.driving-policy') }}"
                            title="{{ translate('driving_policy') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('driving_policy') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/driving-cancellation-policy/cancellation-policy') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.driving-cancellation-policy.driving-cancellation-policy') }}"
                            title="{{ translate('cancellation_policy') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('cancellation_policy') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/self-driving-management/self-driving-add') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.self-driving-management.self-driving-add') }}"
                            title="{{ translate('self_driving_add') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('self_driving_add') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/self-driving-management/self-driving-list') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.self-driving-management.self-driving-list') }}"
                            title="{{ translate('self_driving_list') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('self_driving_list') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/self-driving-management/self-vehicle-pending-order') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.self-driving-management.self-vehicle-pending-order') }}"
                            title="{{ translate('self_vehicle_pending_order') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('self_vehicle_pending_order') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/self-driving-management/self-vehicle-confirm-order') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.self-driving-management.self-vehicle-confirm-order') }}"
                            title="{{ translate('self_vehicle_confirm_order') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('self_vehicle_confirm_order') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/self-driving-management/self-vehicle-pickup-order') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.self-driving-management.self-vehicle-pickup-order') }}"
                            title="{{ translate('self_vehicle_pickup_order') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('self_vehicle_pickup_order') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/self-driving-management/self-vehicle-droup-order') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.self-driving-management.self-vehicle-droup-order') }}"
                            title="{{ translate('self_vehicle_droup_order') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('self_vehicle_droup_order') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/self-driving-management/self-driving-lead') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.self-driving-management.self-driving-lead') }}"
                            title="{{ translate('self_driving_lead') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('self_driving_lead') }}
                                <span class="badge badge-soft-primary badge-pill ml-1">
                                    {{ \App\Models\SelfVehicleLeads::count() }}
                                </span>
                            </span>
                        </a>
                    </li>
                    <!--end tour & travel -->

                    @if (Helpers::modules_check('Product Order') ||
                    Helpers::modules_check('Pooja Order') ||
                    Helpers::modules_check('Vip Order') ||
                    Helpers::modules_check('Anushthan Order') ||
                    Helpers::modules_check('Chadhava Order') ||
                    Helpers::modules_check('Consultation Order') ||
                    Helpers::modules_check('Prashad Order') ||
                    Helpers::modules_check('Refund Request') ||
                    Helpers::modules_check('FAQ'))
                    <li class="nav-item {{ Request::is('admin/orders*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('order_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_check('Product Order'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/orders*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('product_orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('product_orders') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/order*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Product Order', 'All', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/all') ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.orders.list', ['all']) }}"
                                    title="{{ translate('all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Order::count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Pending', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/pending') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['pending']) }}"
                                    title="{{ translate('pending') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('pending') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Order::where(['order_status' => 'pending'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Confirmed', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/confirmed') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['confirmed']) }}"
                                    title="{{ translate('confirmed') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('confirmed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Order::where(['order_status' => 'confirmed'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Packaging', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/processing') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['processing']) }}"
                                    title="{{ translate('packaging') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('packaging') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Order::where(['order_status' => 'processing'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Packaging', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/pickup') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['pickup']) }}"
                                    title="{{ translate('packaging') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('pickup') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Order::where(['order_status' => 'pickup'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Out for Delivery', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/out_for_delivery') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['out_for_delivery']) }}"
                                    title="{{ translate('out_for_delivery') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('out_for_delivery') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Order::where(['order_status' => 'out_for_delivery'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Delivered', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/delivered') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['delivered']) }}"
                                    title="{{ translate('delivered') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('delivered') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Order::where(['order_status' => 'delivered'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Returned', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/returned') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['returned']) }}"
                                    title="{{ translate('returned') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('returned') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Order::where('order_status', 'returned')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Failed to Delivered', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/failed') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['failed']) }}"
                                    title="{{ translate('failed') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('failed_to_Deliver') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Order::where(['order_status' => 'failed'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Product Order', 'Canceled', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/orders/' . Order::LIST[URI] . '/canceled') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.orders.list', ['canceled']) }}"
                                    title="{{ translate('canceled') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Order::where(['order_status' => 'canceled'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Pooja Order'))
                    {{-- pooja order --}}
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/pooja*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('puja_orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('puja_orders') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/pooja*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Pooja Order', 'All', 'view'))
                            <li class="nav-item {{ Request::is('admin/pooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pooja.orders.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'pooja')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Order', 'Pending', 'view'))
                            <li class="nav-item {{ Request::is('admin/pooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pooja.orders.list', 0) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'pooja')->where('status', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Order', 'Completed', 'view'))
                            <li class="nav-item {{ Request::is('admin/pooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pooja.orders.list', 1) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'pooja')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Order', 'Canceled', 'view'))
                            <li class="nav-item {{ Request::is('admin/pooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pooja.orders.list', 2) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'pooja')->where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Order', 'Rejected', 'view'))
                            <li class="nav-item {{ Request::is('admin/pooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('puja_orders') }}">
                                <a class="nav-link " href="{{ route('admin.pooja.orders.list', 6) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Rejected') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'pooja')->where('status', 6)->where('order_status', 6)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/pooja/lead/list') ? 'active' : '' }}"
                                title="{{ translate('pooja_leads') }}">
                                <a class="nav-link " href="{{ route('admin.pooja.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'pooja')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Order', 'Order By Pooja', 'view'))
                            {{-- Order By Pooja --}}
                            <li class="nav-item {{ Request::is('admin/pooja/orderbypooja*') ? 'active' : '' }}"
                                title="{{ translate('order_by_pooja') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pooja.orders.orderbypooja') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('order_by_Pooja') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'pooja')->where('status', 0)->where('is_completed', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'view'))
                            <li class="nav-item {{ Request::is('admin/pooja/orderbycompleted*') ? 'active' : '' }}"
                                title="{{ translate('order_by_completed') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.pooja.orders.orderbycompleted') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('order_by_completed') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'pooja')->where('status', 1)->where('is_completed', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    {{-- VIP POOJA ORDER MENU --}}
                    @if (Helpers::modules_check('Vip Order'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/vippooja/order*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('VIP_puja_orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('VIP_puja_orders') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/vippooja/order*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Vip Order', 'All', 'view'))
                            <li class="nav-item {{ Request::is('admin/vippooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('VIP_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'vip')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vip Order', 'Pending', 'view'))
                            <li class="nav-item {{ Request::is('admin/vippooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('VIP_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.list', 0) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('status', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vip Order', 'Completed', 'view'))
                            <li class="nav-item {{ Request::is('admin/vippooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('VIP_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.list', 1) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vip Order', 'Canceled', 'view'))
                            <li class="nav-item {{ Request::is('admin/vippooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('VIP_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.list', 2) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vip Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/vippooja/lead/list') ? 'active' : '' }}"
                                title="{{ translate('VIP_puja_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('VIP Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'vip')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vip Order', 'Rejected', 'view'))
                            <li class="nav-item {{ Request::is('admin/vippooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Vip_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.list', 6) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Rejected') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('status', 6)->where('order_status', 6)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vip Order', 'Order By Vip', 'view'))
                            {{-- Order By VIPPooja --}}
                            <li class="nav-item {{ Request::is('admin/vippooja/orderbyvippooja*') ? 'active' : '' }}"
                                title="{{ translate('order_by_VIPpooja') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.orderbyvippooja') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('order_by_VIPPooja') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('status', 0)->where('is_completed', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            {{-- Order By Instance --}}
                            @if (Helpers::modules_permission_check('Vip Order', 'Instance Order', 'view'))
                            <li class="nav-item {{ Request::is('admin/vippooja/instanceorder*') ? 'active' : '' }}"
                                title="{{ translate('Instance_Order') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.instanceorder') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Instance_Order') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('package_id', 6)->where('status', 0)->where('is_completed', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    {{-- ANUSHTHAN POOJA ORDER MENU --}}
                    @if (Helpers::modules_check('Anushthan Order'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/anushthan/order*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Anushthan_puja_orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Anushthan_puja_orders') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/anushthan/order*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Anushthan Order', 'All', 'view'))
                            <li class="nav-item {{ Request::is('admin/anushthan/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Anushthan_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'anushthan')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Anushthan Order', 'Pending', 'view'))
                            <li class="nav-item {{ Request::is('admin/anushthan/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Anushthan_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.list', 0) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'anushthan')->where('status', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Anushthan Order', 'Completed', 'view'))
                            <li class="nav-item {{ Request::is('admin/anushthan/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Anushthan_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.list', 1) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'anushthan')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Anushthan Order', 'Rejected', 'view'))
                            <li class="nav-item {{ Request::is('admin/anushthan/orders/list') ? 'active' : '' }}"
                                title="{{ translate('anushthan_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.list', 6) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Rejected') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'anushthan')->where('status', 6)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Anushthan Order', 'Canceled', 'view'))
                            <li class="nav-item {{ Request::is('admin/anushthan/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Anushthan_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.list', 2) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'anushthan')->where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Anushthan Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/anushthan/lead/list') ? 'active' : '' }}"
                                title="{{ translate('Anushthan_puja_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Anushthan Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'anushthan')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Anushthan Order', 'Order By Anushthan', 'view'))
                            {{-- Order By Anushthanooja --}}
                            <li class="nav-item {{ Request::is('admin/anushthan/orderbyanushthan*') ? 'active' : '' }}"
                                title="{{ translate('order_by_Anushthanpooja') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.orderbyanushthan') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('order_by_Anushthan') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'anushthan')->where('status', 0)->where('is_completed', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            {{-- Order By Instance --}}
                            @if (Helpers::modules_permission_check('Anushthan Order', 'Instance Order', 'view'))
                            <li class="nav-item {{ Request::is('admin/anushthan/instanceorder*') ? 'active' : '' }}"
                                title="{{ translate('Instance_Order') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.instanceorder') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Instance_Order') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'anushthan')->where('package_id', 8)->where('status', 0)->where('is_completed', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    {{-- CHADHAVA ORDER MENU --}}
                    @if (Helpers::modules_check('Chadhava Order'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/chadhava/order*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Chadhava_orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Chadhava_orders') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/chadhava/order*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Chadhava Order', 'All', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Chadhava_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Chadhava Order', 'Pending', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Chadhava_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.list', 0) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('status', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Chadhava Order', 'Completed', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Chadhava_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.list', 1) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Chadhava Order', 'Canceled', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Chadhava_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.list', 2) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Chadhava Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/lead/list') ? 'active' : '' }}"
                                title="{{ translate('Chadhava_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('chadhava Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'chadhava')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Chadhva Order', 'Rejected', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/orders/list') ? 'active' : '' }}"
                                title="{{ translate('Vip_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.list', 6) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Rejected') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('status', 6)->where('order_status', 6)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            {{-- Order By chadhawa --}}
                            @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/orderbychadhava*') ? 'active' : '' }}"
                                title="{{ translate('Order_by_Chadhava') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.orderbychadhava') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('order_by_chadhava') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('status', 0)->where('is_completed', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Complete', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/orderbycompleted*') ? 'active' : '' }}"
                                title="{{ translate('Order_by_Chadhava') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.orderbycompleted') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('order_by_completed') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('status', 1)->where('is_completed', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            
                        </ul>
                    </li>
                    @endif
                    {{-- OFFLINE POOJA ORDER MENU --}}
                    @if (Helpers::modules_check('OfflinePooja Order'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/offlinepooja/order*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('offline_puja_orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('offline_puja_orders') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/offlinepooja/order*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('OfflinePooja Order', 'All', 'view'))
                            <li class="nav-item {{ Request::is('admin/offlinepooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('offline_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.offlinepooja.order.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\OfflinePoojaOrder::count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('OfflinePooja Order', 'Pending', 'view'))
                            <li class="nav-item {{ Request::is('admin/offlinepooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('offline_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.offlinepooja.order.list', 0) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\OfflinePoojaOrder::where('status', 0)->whereNull('pandit_assign')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('OfflinePooja Order', 'Pandit Assigned', 'view'))
                            <li class="nav-item {{ Request::is('admin/offlinepooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('offline_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.offlinepooja.order.list', 'pandit-assigned') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pandit_Assgined') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\OfflinePoojaOrder::where('status', 0)->whereNotNull('pandit_assign')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('OfflinePooja Order', 'Completed', 'view'))
                            <li class="nav-item {{ Request::is('admin/offlinepooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('offline_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.offlinepooja.order.list', 1) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\OfflinePoojaOrder::where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('OfflinePooja Order', 'Canceled', 'view'))
                            <li class="nav-item {{ Request::is('admin/offlinepooja/orders/list') ? 'active' : '' }}"
                                title="{{ translate('offline_puja_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.offlinepooja.order.list', 2) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\OfflinePoojaOrder::where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Offline Pooja', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/offlinepooja/lead/list') ? 'active' : '' }}"
                                title="{{ translate('offline_puja_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.offlinepooja.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('offline_Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\OfflineLead::where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Consultation Order'))
                    {{-- counselling order --}}
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/counselling/order*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('consultancy_Orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('consultancy_Orders') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/counselling/order*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Consultation Order', 'All', 'view'))
                            <li class="nav-item {{ Request::is('admin/counselling/order/list') ? 'active' : '' }}"
                                title="{{ translate('consultancy_Orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.counselling.order.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'counselling')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Consultation Order', 'Pending', 'view'))
                            <li class="nav-item {{ Request::is('admin/counselling/order/list') ? 'active' : '' }}"
                                title="{{ translate('consultancy_Orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.counselling.order.list', 0) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'counselling')->where('status', 0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Consultation Order', 'Completed', 'view'))
                            <li class="nav-item {{ Request::is('admin/counselling/order/list') ? 'active' : '' }}"
                                title="{{ translate('consultancy_Orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.counselling.order.list', 1) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('completed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'counselling')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Consultation Order', 'Canceled', 'view'))
                            <li class="nav-item {{ Request::is('admin/counselling/order/list') ? 'active' : '' }}"
                                title="{{ translate('consultancy_Orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.counselling.order.list', 2) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Service_order::where('type', 'counselling')->where('status', 2)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Consultation Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/counselling/order/lead/list') ? 'active' : '' }}"
                                title="{{ translate('consultancy_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.counselling.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'counselling')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Prasaad Order'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/prashad/order*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Prashad_Orders') }}">
                            <i class="tio-shopping-cart-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('Prasaad_Orders') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/prashad/order*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Prashad Order', 'All', 'view'))
                            <li class="nav-item {{ Request::is('admin/prashad/order/list') ? 'active' : '' }}"
                                title="{{ translate('All_Prashad') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.prashad.order.list', 'all') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('all') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\Prashad_deliverys::where('pooja_status',1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Prashad Order', 'Confirmed', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/prashad/order/list') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.prashad.order.list', 'confirmed') }}"
                                    title="{{ translate('confirmed') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('confirmed') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Prashad_deliverys::where('status',1)->where(['order_status' => 'confirmed'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Prashad Order', 'Processing', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/prashad/order/list') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.prashad.order.list','processing') }}"
                                    title="{{ translate('processing') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('processing') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Prashad_deliverys::where(['order_status' => 'processing'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Prashad Order', 'Out for Pickup', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/prashad/order/list') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.prashad.order.list','out_for_pickup') }}"
                                    title="{{ translate('packaging') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('out_for_pickup') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{ \App\Models\Prashad_deliverys::where(['order_status' => 'out_for_pickup'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif

                            @if (Helpers::modules_permission_check('Prashad Order', 'Delivered', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/prashad/order/list') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.prashad.order.list', 'delivered') }}"
                                    title="{{ translate('delivered') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('delivered') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\Prashad_deliverys::where(['order_status' => 'delivered'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif

                            @if (Helpers::modules_permission_check('Prashad Order', 'Canceled', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/prashad/order/list') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.prashad.order.list', 'canceled') }}"
                                    title="{{ translate('canceled') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('canceled') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\Prashad_deliverys::where(['order_status' => 'canceled'])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Prashad Order', 'Order By Prashad', 'view'))
                            <li class="nav-item {{ Request::is('admin/prashad/orderbyprashad*') ? 'active' : '' }}"
                                title="{{ translate('order_by_prashad') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.prashad.order.orderbyprashad') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('order_by_Prasaad') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Prashad_deliverys::where('pooja_status', 1)->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Refund Request'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/refund-section/refund/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('return_product') }}">
                            <i class="tio-receipt-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('return_product') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/refund-section/refund*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Refund Request', 'Pending', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/refund-section/refund/' . RefundRequest::LIST[URI] . '/pending') ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.refund-section.refund.list', ['pending']) }}"
                                    title="{{ translate('pending') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('pending') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\RefundRequest::where('status', 'pending')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Refund Request', 'Approved', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/refund-section/refund/' . RefundRequest::LIST[URI] . '/approved') ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.refund-section.refund.list', ['approved']) }}"
                                    title="{{ translate('approved') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('approved') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\RefundRequest::where('status', 'approved')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            {{-- @if (Helpers::modules_permission_check('Refund Request', 'Received', 'view')) --}}
                            <li class="nav-item {{ Request::is('admin/refund-section/refund/' . RefundRequest::LIST[URI] . '/received') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.refund-section.refund.list', ['received']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('received') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\RefundRequest::where('status', 'received')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            @if (Helpers::modules_permission_check('Refund Request', 'Refunded', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/refund-section/refund/' . RefundRequest::LIST[URI] . '/refunded') ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.refund-section.refund.list', ['refunded']) }}"
                                    title="{{ translate('refunded') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('refunded') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ \App\Models\RefundRequest::where('status', 'refunded')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Refund Request', 'Rejected', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/refund-section/refund/' . RefundRequest::LIST[URI] . '/rejected') ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.refund-section.refund.list', ['rejected']) }}"
                                    title="{{ translate('rejected') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('rejected') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ \App\Models\RefundRequest::where('status', 'rejected')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    {{-- @endif
                    @if (Helpers::module_permission_check('product_management')) --}}
                    @if (Helpers::modules_check('FAQ'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/faq*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('faq_manage') }}">
                            <i class="tio-help nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('faq_manage') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub">
                            <li
                                class="nav-item  {{ Request::is('admin/faq/' . FAQPath::CATEGORY[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.faq.category') }}"
                                    title="{{ translate('faq_category') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('faq_category') }}</span>
                                </a>
                            </li>
                            @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/faq/' . FAQPath::LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.faq.list') }}"
                                    title="{{ translate('faq_list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('faq_list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('FAQ', 'FAQ', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/faq/' . FAQPath::ADD[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.faq.add-new') }}"
                                    title="{{ translate('add_new_faq') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_new_faq') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @endif
                    @if (Helpers::modules_check('Category Setup') ||
                    Helpers::modules_check('Brand') ||
                    Helpers::modules_check('Attribute Setup') ||
                    Helpers::modules_check('In-House Product') ||
                    Helpers::modules_check('Pooja Managment') ||
                    Helpers::modules_check('Chadhava Managment') ||
                    Helpers::modules_check('Vendor Product'))
                    <li
                        class="nav-item {{ Request::is('admin/brand*') || Request::is('admin/category*') || Request::is('admin/sub*') || Request::is('admin/attribute*') || Request::is('admin/products*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('product_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_check('Category Setup'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/category*') || Request::is('admin/sub-category*') || Request::is('admin/sub-sub-category*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('category_Setup') }}">
                            <i class="tio-category nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('category_Setup') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/category*') || Request::is('admin/sub*') ? 'block' : '' }}">
                            @if (Helpers::modules_permission_check('Category Setup', 'Category', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/category/' . Category::LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.category.view') }}"
                                    title="{{ translate('categories') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('categories') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Category Setup', 'Sub Category', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/sub-category/' . SubCategory::LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.sub-category.view') }}"
                                    title="{{ translate('sub_Categories') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('sub_Categories') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Category Setup', 'Sub Sub Category', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/sub-sub-category/' . SubSubCategory::LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.sub-sub-category.view') }}"
                                    title="{{ translate('sub_Sub_Categories') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('sub_Sub_Categories') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Brand'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/brand*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('brands') }}">
                            <i class="tio-star nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('brands') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/brand*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Brand', 'Brand', 'add'))
                            <li class="nav-item {{ Request::is('admin/brand/' . Brand::ADD[URI]) ? 'active' : '' }}"
                                title="{{ translate('add_new') }}">
                                <a class="nav-link " href="{{ route('admin.brand.add-new') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_new') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Brand', 'Brand', 'view'))
                            <li class="nav-item {{ Request::is('admin/brand/' . Brand::LIST[URI]) ? 'active' : '' }}"
                                title="{{ translate('list') }}">
                                <a class="nav-link " href="{{ route('admin.brand.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('list') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Attribute Setup'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/attribute*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.attribute.view') }}"
                            title="{{ translate('product_Attribute_Setup') }}">
                            <i class="tio-category-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('product_Attribute_Setup') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_check('In-House Product'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/products/' . Product::LIST[URI] . '/in_house') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::BULK_IMPORT[URI]) || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::ADD[URI]) || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::VIEW[URI] . '/admin/*') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::BARCODE_GENERATE[URI] . '/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('in-House_Products') }}">
                            <i class="tio-shop nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                <span class="text-truncate">{{ translate('in-house_Products') }}</span>
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/products/' . Product::ADD[URI] . '/in_house') || Request::is('admin/products/' . Product::LIST[URI] . '/in_house') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::STOCK_LIMIT[URI] . '/in_house') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::BULK_IMPORT[URI]) || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::ADD[URI]) || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::VIEW[URI] . '/admin/*') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::BARCODE_GENERATE[URI] . '/*') ? 'block' : '' }}">
                            @if (Helpers::modules_permission_check('In-House Product', 'Product', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/products/' . Product::LIST[URI] . '/in_house') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::VIEW[URI] . '/admin/*') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::STOCK_LIMIT[URI] . '/in_house') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::BARCODE_GENERATE[URI] . '/*') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.products.list', ['in_house', '']) }}"
                                    title="{{ translate('Product_List') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Product_List') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ getAdminProductsCount('all') }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('In-House Product', 'Product', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/products/' . Product::ADD[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.products.add') }}"
                                    title="{{ translate('add_New_Product') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_New_Product') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('In-House Product', 'Product', 'bulk-import'))
                            <li
                                class="nav-item {{ Request::is('admin/products/' . Product::BULK_IMPORT[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.products.bulk-import') }}"
                                    title="{{ translate('bulk_import') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('bulk_import') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- Service Management -->
                    @if (Helpers::modules_check('Pooja Managment'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/service*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('pooja_management') }}">
                            <i class="tio-filter-list nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('pooja_management') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub">
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/service/' . ServiceDetails::LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.service.list') }}"
                                    title="{{ translate('pooja_list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('pooja_list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/service/' . ServiceDetails::ADD[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.service.add-new') }}"
                                    title="{{ translate('add_new_pooja') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_new_pooja') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/package*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.package.list') }}"
                                    title="{{ translate('pooja_package') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('pooja_package') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Consultation', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/service/' . ServiceDetails::COUNSELLING_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.counselling.list') }}"
                                    title="{{ translate('counselling_list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('counselling_list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Consultation', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/service/' . ServiceDetails::COUNSELLING_ADD[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.counselling.add-new') }}"
                                    title="{{ translate('add_new_counselling') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_new_counselling') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'VIP Pooja', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/service/' . ServiceDetails::VIP_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.service.vip.list') }}"
                                    title="{{ translate('VIP_list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('VIP_list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'VIP Pooja', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/service/' . ServiceDetails::VIP_ADD[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.vip.add-new') }}"
                                    title="{{ translate('add_VIP_Pooja') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_VIP_Pooja') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/service/' . ServiceDetails::ADD_OFFLINE_POOJA[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.offline.pooja.add-new') }}"
                                    title="{{ translate('add_Offline_Pooja') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('add_Offline_Pooja') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/service/' . ServiceDetails::OFFLINE_POOJA_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.offline.pooja.list') }}"
                                    title="{{ translate('Offline_Pooja_List') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('Offline_Pooja_List') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Category', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/service/' . ServiceDetails::OFFLINE_POOJA_CATEGORY_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.offline.pooja.category.list') }}"
                                    title="{{ translate('Offline_Pooja_Category_List') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('Offline_Pooja_Category_List') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Refund', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/service/' . ServiceDetails::OFFLINE_POOJA_REFUND_POLICY_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.offline.pooja.refund.policy.list') }}"
                                    title="{{ translate('Offline_Pooja_Refund_Policy_List') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('Offline_Pooja_Refund_Policy_List') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja Schedule', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/service/' . ServiceDetails::OFFLINE_POOJA_SCHEDULE_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.offline.pooja.schedule.list') }}"
                                    title="{{ translate('Offline_Pooja_Schedule_List') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('Offline_Pooja_Schedule_List') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Managment', 'Offline Pooja City', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/service/' . ServiceDetails::OFFLINE_POOJA_CITY_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.service.offline.pooja.city.list') }}"
                                    title="{{ translate('City_Detail') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('City_Detail') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if (Helpers::modules_check('Chadhava Managment'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/chadhava*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Chadhava_Managment') }}">
                            <i class="tio-protection nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('Chadhava_Managment') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub">
                            @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'view'))
                            <li
                                class="nav-item  {{ Request::is('admin/chadhava/' . ChadhavaPath::LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.chadhava.list') }}"
                                    title="{{ translate('Chadhava_list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('Chadhava_list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Chadhava Managment', 'Chadhava', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/chadhava/' . ChadhavaPath::ADD[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.chadhava.add-new') }}"
                                    title="{{ translate('Chadhava_Add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('Chadhava_Add') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Vendor Product'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/products/' . Product::LIST[URI] . '/seller*') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::VIEW[URI] . '/seller/*') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::UPDATED_PRODUCT_LIST[URI]) ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('vendor_Products') }}">
                            <i class="tio-airdrop nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('vendor_Products') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::LIST[URI] . '/seller*') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::VIEW[URI] . '/seller/*') || Request::is('admin/products/' . \App\Enums\ViewPaths\Admin\Product::UPDATED_PRODUCT_LIST[URI]) ? 'block' : '' }}">
                            @if (Helpers::modules_permission_check('Vendor Product', 'New Product', 'view'))
                            <li
                                class="nav-item {{ str_contains(url()->current() . '?status=' . request()->get('status'), 'admin/products/' . \App\Enums\ViewPaths\Admin\Product::LIST[URI] . '/seller?status=0') == 1 ? 'active' : '' }}">
                                <a class="nav-link"
                                    title="{{ translate('new_Products_Requests') }}"
                                    href="{{ route('admin.products.list', ['seller', 'status' => '0']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('new_Products_Requests') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ getVendorProductsCount('new-product') }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (getWebConfig(name: 'product_wise_shipping_cost_approval') == 1)
                            <li
                                class="nav-item {{ Request::is('admin/products/' . Product::UPDATED_PRODUCT_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link text-capitalize"
                                    title="{{ translate('product_update_requests') }}"
                                    href="{{ route('admin.products.updated-product-list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate text-capitalize">{{ Str::limit(translate('product_update_requests'), 18, '...') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ getVendorProductsCount('product-updated-request') }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vendor Product', 'Approved Product', 'view'))
                            <li
                                class="nav-item {{ str_contains(url()->current() . '?status=' . request()->get('status'), '/admin/products/' . Product::LIST[URI] . '/seller?status=1') == 1 ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('approved_Products') }}"
                                    href="{{ route('admin.products.list', ['seller', 'status' => '1']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('approved_Products') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{ getVendorProductsCount('approved') }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vendor Product', 'Denied Product', 'view'))
                            <li
                                class="nav-item {{ str_contains(url()->current() . '?status=' . request()->get('status'), '/admin/products/' . Product::LIST[URI] . '/seller?status=2') == 1 ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('denied_Products') }}"
                                    href="{{ route('admin.products.list', ['seller', 'status' => '2']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('denied_Products') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{ getVendorProductsCount('denied') }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @endif
                    @if (Helpers::modules_check('Banner Setup') ||
                    Helpers::modules_check('Offers & Setup') ||
                    Helpers::modules_check('Notifications') ||
                    Helpers::modules_check('Announcement Setup'))
                    <li
                        class="nav-item {{ Request::is('admin/banner*') || Request::is('admin/coupon*') || Request::is('admin/notification*') || Request::is('admin/deal*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('promotion_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Banner Setup', 'Banner Setup', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/banner*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.banner.list') }}"
                            title="{{ translate('banner_Setup') }}">
                            <i class="tio-photo-square-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('banner_Setup') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Offers & Setup'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/coupon*') || Request::is('admin/deal*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('offers_&_Deals') }}">
                            <i class="tio-users-switch nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('offers_&_Deals') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/coupon*') || Request::is('admin/deal*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Offers & Setup', 'Coupon Setup', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/coupon*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.coupon.add') }}"
                                    title="{{ translate('coupon') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('coupon') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Offers & Setup', 'Flash Deal', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/deal/' . FlashDeal::LIST[URI]) || Request::is('admin/deal/' . FlashDeal::UPDATE[URI] . '*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.deal.flash') }}"
                                    title="{{ translate('flash_Deals') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('flash_Deals') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Offers & Setup', 'Deal Of The Day', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/deal/' . DealOfTheDay::LIST[URI]) || Request::is('admin/deal/' . DealOfTheDay::UPDATE[URI] . '*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.deal.day') }}"
                                    title="{{ translate('deal_of_the_day') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('deal_of_the_day') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Offers & Setup', 'Feature Deal', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/deal/' . FeatureDeal::LIST[URI]) || Request::is('admin/deal/' . FeatureDeal::UPDATE[URI] . '*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.deal.feature') }}"
                                    title="{{ translate('featured_Deal') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('featured_Deal') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Notifications'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/notification*') || Request::is('admin/push-notification/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('notifications') }}">
                            <i class="tio-users-switch nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('notifications') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/notification*') || Request::is('admin/push-notification/*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Notifications', 'Send Notification', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ !Request::is('admin/notification/push') && Request::is('admin/notification*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.notification.index') }}"
                                    title="{{ translate('send_notification') }}">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/send-notification.svg') }}"
                                        alt="{{ translate('send_notification_svg') }}"
                                        width="15" class="mr-2">
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                        {{ translate('send_notification') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Notifications', 'Push Notification Setup', 'view') ||
                            Helpers::modules_permission_check('Notifications', 'Push Notification Setup', 'firebase-view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/' . PushNotification::INDEX[URI]) || Request::is('admin/push-notification/' . PushNotification::FIREBASE_CONFIGURATION[URI]) || Request::is('admin/push-notification/' . PushNotification::INDEX[URI]) ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link text-capitalize"
                                    href="{{ route('admin.push-notification.index') }}"
                                    title="{{ translate('push_notifications_setup') }}">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/push-notification.svg') }}"
                                        alt="{{ translate('push_notification_svg') }}"
                                        width="15" class="mr-2">
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                        {{ translate('push_notifications_setup') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Announcement Setup') ||
                    Helpers::modules_permission_check('Announcement Setup', 'Announcement', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/' . BusinessSettings::ANNOUNCEMENT[URI]) ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.business-settings.announcement') }}"
                            title="{{ translate('announcement') }}">
                            <i class="tio-mic-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('announcement') }}
                            </span>
                        </a>
                    </li>
                    @endif
                    @endif
                    @if (Helpers::modules_check('system_settings'))
                    @if (count(config('get_theme_routes')) > 0)
                    <li
                        class="nav-item {{ Request::is('admin/banner*') || Request::is('admin/coupon*') || Request::is('admin/notification*') || Request::is('admin/deal*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ config('get_theme_routes')['name'] }}
                            {{ translate('Menu') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @foreach (config('get_theme_routes')['route_list'] as $route)
                    @if (isset($route['module_permission']) && Helpers::module_permission_check($route['module_permission']))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is($route['path']) || Request::is($route['path'] . '*') ? 'active' : '' }} @foreach ($route['route_list'] as $sub_route){{ Request::is($sub_route['path']) || Request::is($sub_route['path'] . '*') ? 'active' : '' }} @endforeach">
                        <a class="js-navbar-vertical-aside-menu-link nav-link {{ count($route['route_list']) > 0 ? 'nav-link-toggle' : '' }}"
                            href="{{ count($route['route_list']) > 0 ? 'javascript:' : $route['url'] }}"
                            title="{{ translate('offers_&_Deals') }}">
                            {!! $route['icon'] !!}
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate($route['name']) }}</span>
                        </a>
                        @if (count($route['route_list']) > 0)
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: @foreach ($route['route_list'] as $sub_route){{ Request::is($sub_route['path']) || Request::is($sub_route['path'] . '*') ? 'block' : 'none' }} @endforeach">
                            @foreach ($route['route_list'] as $sub_route)
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is($sub_route['path']) || Request::is($sub_route['path'] . '*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ $sub_route['url'] }}"
                                    title="{{ translate($sub_route['name']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate($sub_route['name']) }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </li>
                    @endif
                    @endforeach
                    @endif
                    @endif
                    @if (Helpers::modules_check('Help & Support'))
                    <li
                        class="nav-item {{ Request::is('admin/support-ticket*') || Request::is('admin/contact*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('help_&_support') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_permission_check('Help & Support', 'Inbox', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/messages*') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ route('admin.messages.index', ['type' => 'customer']) }}">
                            <i class="tio-chat nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('inbox') }}
                            </span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Help & Support', 'Message', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/contact*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.contact.list') }}"
                            title="{{ translate('messages') }}">
                            <i class="tio-messages nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                <span class="position-relative">
                                    {{ translate('messages') }}
                                    @php($message = \App\Models\Contact::where('seen', 0)->count())
                                    @if ($message != 0)
                                    <span
                                        class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                    @endif
                                </span>
                            </span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Help & Support', 'Support Ticket', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/support-ticket*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('support_Ticket') }}">
                            <i class="tio-chart-bar-4 nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('support_Ticket') }}
                                @if (\App\Models\SupportTicket::where('status', 'open')->count() > 0)
                                <span
                                    class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                @endif
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/support-ticket*') ? 'block' : 'none' }}">
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/support-ticket/view') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.support-ticket.view') }}"
                                    title="{{ translate('support_Ticket') }}">
                                    <i class="tio-support nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        <span class="position-relative">
                                            {{ translate('support_Ticket') }}
                                            @if (\App\Models\SupportTicket::where('status', 'open')->count() > 0)
                                            <span
                                                class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <!--  -->
                            <?php
                            $getTypes = \App\Models\SupportType::where('status', 1)->get();
                            ?>
                            @if ($getTypes)
                            @foreach ($getTypes as $v_type)
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/support-ticket/view-ticket') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.support-ticket.view-ticket', ['names' => $v_type['name'] ?? '']) }}"
                                    title="{{ ucwords($v_type['name'] ?? '') }}">
                                    <i class="tio-support nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        <span class="position-relative">
                                            {{ ucwords($v_type['name'] ?? '') }}
                                            @if (\App\Models\SupportTicket::where('ticket_type_id', $v_type['id'])->where('status', 'open')->count() > 0)
                                            <span
                                                class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endforeach
                            @endif
                            <!--  -->
                        </ul>
                    </li>
                    @endif
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/vendor-support-ticket*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('vendor_support') }}">
                            <i class="tio-chart-bar-4 nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('vendor_support') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('admin/vendor-support-ticket/vendor-inbox*') || Request::is('admin/vendor-support-ticket/admin-inbox*') ? 'block' : 'none' }}">
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/vendor-support-ticket/vendor/view') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.vendor-support-ticket.view') }}" title="{{ translate('vendor_Ticket') }}">
                                    <i class="tio-support nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        <span class="position-relative">
                                            {{ translate('vendor_Ticket') }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/vendor-support-ticket/vendor-inbox/view') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.vendor-support-ticket.vendor-inbox.view') }}" title="{{ translate('vendor_inbox') }}">
                                    <i class="tio-support nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        <span class="position-relative">
                                            {{ translate('from_vendor') }}
                                            @if (\App\Models\VendorSupportTicketConv::where('created_by','vendor')->where('status', 'open')->count() > 0)
                                            <span class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/vendor-support-ticket/admin-inbox/view') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.vendor-support-ticket.admin-inbox.view') }}" title="{{ translate('admin_inbox') }}">
                                    <i class="tio-support nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        <span class="position-relative">
                                            {{ translate('from_admin') }}
                                            @if (\App\Models\VendorSupportTicketConv::where('created_by','admin')->where('status', 'open')->count() > 0)
                                            <span class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <!--  -->

                        </ul>
                    </li>

                    @endif
                    @if (Helpers::modules_check('Sales & Transaction Report') ||
                    Helpers::modules_check('Product Report') ||
                    Helpers::modules_check('Order Report'))
                    <li
                        class="nav-item {{ Request::is('admin/report/earning') || Request::is('admin/report/' . InhouseProductSale::VIEW[URI]) || Request::is('admin/report/seller-report') || Request::is('admin/report/earning') || Request::is('admin/transaction/list') || Request::is('admin/refund-section/refund-list') || Request::is('admin/stock/product-in-wishlist') || Request::is('admin/reviews*') || Request::is('admin/stock/product-stock') || Request::is('admin/transaction/wallet-bonus') || Request::is('admin/report/order') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">
                            {{ translate('reports_&_Analysis') }}
                        </small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_check('Sales & Transaction Report'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/admin-earning') || Request::is('admin/report/seller-earning') || Request::is('admin/report/' . InhouseProductSale::VIEW[URI]) || Request::is('admin/report/seller-report') || Request::is('admin/report/earning') || Request::is('admin/transaction/order-transaction-list') || Request::is('admin/transaction/expense-transaction-list') || Request::is('admin/report/transaction/' . App\Enums\ViewPaths\Admin\RefundTransaction::INDEX[URI]) || Request::is('admin/transaction/wallet-bonus') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('sales_&_Transaction_Report') }}">
                            <i class="tio-chart-bar-4 nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('sales_&_Transaction_Report') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/report/admin-earning') || Request::is('admin/report/seller-earning') || Request::is('admin/report/' . InhouseProductSale::VIEW[URI]) || Request::is('admin/report/seller-report') || Request::is('admin/report/earning') || Request::is('admin/transaction/order-transaction-list') || Request::is('admin/transaction/expense-transaction-list') || Request::is('admin/report/transaction/' . App\Enums\ViewPaths\Admin\RefundTransaction::INDEX[URI]) || Request::is('admin/transaction/wallet-bonus') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Earning Reports', 'admin-earnings') ||
                            Helpers::modules_permission_check('Sales & Transaction Report', 'Earning Reports', 'vendor-earnings'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/admin-earning') || Request::is('admin/report/seller-earning') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.report.admin-earning') }}"
                                    title="{{ translate('Earning_Reports') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('Earning_Reports') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Inhouse sale', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/report/' . InhouseProductSale::VIEW[URI]) ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.report.inhouse-product-sale') }}"
                                    title="{{ translate('inhouse_Sales') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('inhouse_Sales') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Vendor Sale', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/report/seller-report') ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.report.seller-report') }}"
                                    title="{{ translate('vendor_Sales') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate text-capitalize">
                                        {{ translate('vendor_Sales') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Transaction Report', 'order-view') ||
                            Helpers::modules_permission_check('Sales & Transaction Report', 'Transaction Report', 'expense-view') ||
                            Helpers::modules_permission_check('Sales & Transaction Report', 'Transaction Report Setup', 'refund-view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/transaction/order-transaction-list') || Request::is('admin/transaction/expense-transaction-list') || Request::is('admin/transaction/refund-transaction-list') || Request::is('admin/report/transaction/' . App\Enums\ViewPaths\Admin\RefundTransaction::INDEX[URI]) || Request::is('admin/transaction/wallet-bonus') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.transaction.order-transaction-list') }}"
                                    title="{{ translate('transaction_Report') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('transaction_Report') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Product Report'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/all-product') || Request::is('admin/stock/product-in-wishlist') || Request::is('admin/stock/product-stock') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.report.all-product') }}"
                            title="{{ translate('product_Report') }}">
                            <i class="tio-chart-bar-4 nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                <span class="position-relative">
                                    {{ translate('product_Report') }}
                                </span>
                            </span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Order Report', 'Order Report', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/order') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.report.order') }}"
                            title="{{ translate('order_Report') }}">
                            <i class="tio-chart-bar-1 nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('order_Report') }}
                            </span>
                        </a>
                    </li>
                    @endif
                    @endif
                    @if (Helpers::modules_check('Customers') ||
                    Helpers::modules_check('Vendor') ||
                    Helpers::modules_check('Delivery Men') ||
                    Helpers::modules_check('Employee') ||
                    Helpers::modules_check('Subscriber'))
                    <li
                        class="nav-item {{ Request::is('admin/customer/' . Customer::LIST[URI]) || Request::is('admin/customer/' . Customer::VIEW[URI] . '*') || Request::is('admin/customer/' . Customer::SUBSCRIBER_LIST[URI]) || Request::is('admin/sellers/' . Vendor::ADD[URI]) || Request::is('admin/sellers/' . Vendor::LIST[URI]) || Request::is('admin/delivery-man*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('user_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @if (Helpers::modules_check('Customers'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/customer/wallet*') || Request::is('admin/customer/' . Customer::LIST[URI]) || Request::is('admin/customer/' . Customer::VIEW[URI] . '*') || Request::is('admin/reviews*') || Request::is('admin/customer/loyalty/' . Customer::LOYALTY_REPORT[URI]) ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('customers') }}">
                            <i class="tio-wallet nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('customers') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/customer/wallet*') || Request::is('admin/customer/' . Customer::LIST[URI]) || Request::is('admin/customer/' . Customer::VIEW[URI] . '*') || Request::is('admin/reviews*') || Request::is('admin/customer/loyalty/' . Customer::LOYALTY_REPORT[URI]) ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Customers', 'Customer List', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/customer/' . Customer::LIST[URI]) || Request::is('admin/customer/' . Customer::VIEW[URI] . '*') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.customer.list') }}"
                                    title="{{ translate('Customer_List') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('customer_List') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Customers', 'Customer Review', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/reviews*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.reviews.list') }}"
                                    title="{{ translate('customer_Reviews') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('customer_Reviews') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Customers', 'Customer Wallet', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/customer/wallet/' . CustomerWallet::REPORT[URI]) ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('wallet') }}"
                                    href="{{ route('admin.customer.wallet.report') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('wallet') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Customers', 'Customer Wallet Bonus', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/customer/wallet/' . CustomerWallet::BONUS_SETUP[URI]) ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('wallet_Bonus_Setup') }}"
                                    href="{{ route('admin.customer.wallet.bonus-setup') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('wallet_Bonus_Setup') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Customers', 'Customer Loyalty Point Report', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/customer/loyalty/' . Customer::LOYALTY_REPORT[URI]) ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('loyalty_Points') }}"
                                    href="{{ route('admin.customer.loyalty.report') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('loyalty_Points') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            <li
                                class="nav-item {{ Request::is('admin/customer/app-download/') ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('app_Downloads') }}"
                                    href="{{ route('admin.customer.app.download') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('app_Downloads') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ Request::is('admin/customer/feedback-list/') ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('feedback') }}"
                                    href="{{ route('admin.customer.feedback-list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('feedback_list') }}
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Customers'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/customer/withdraw*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('customer_withdraw') }}">
                            <i class="tio-wallet nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('customer_withdraw') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/customer/withdraw/*') ? 'block' : 'none' }}">
                            {{-- @if (Helpers::modules_permission_check('Customers', 'Customer List', 'view')) --}}
                            <li
                                class="nav-item {{ Request::is('admin/customer/withdraw/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.customer.withdraw.list','pending') }}"
                                    title="{{ translate('pending') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pending') }}
                                        <span class="badge badge-soft-danger badge-pill ml-1">
                                            {{App\Models\UserWithdrawBalance::where('status',0)->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Customers', 'Customer List', 'view')) --}}
                            <li
                                class="nav-item {{ Request::is('admin/customer/withdraw/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.customer.withdraw.list','approve') }}"
                                    title="{{ translate('approved') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('approved') }}
                                        <span class="badge badge-soft-warning badge-pill ml-1">
                                            {{App\Models\UserWithdrawBalance::where('status',1)->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Customers', 'Customer List', 'view')) --}}
                            <li
                                class="nav-item {{ Request::is('admin/customer/withdraw/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.customer.withdraw.list','complete') }}"
                                    title="{{ translate('complete') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('complete') }}
                                        <span class="badge badge-soft-success badge-pill ml-1">
                                            {{App\Models\UserWithdrawBalance::where('status',2)->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- @endif --}}
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Customers'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/seller*') || Request::is('admin/sellers/withdraw-method/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('vendors') }}">
                            <i class="tio-users-switch nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('vendors') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/seller*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/sellers/' . Vendor::ADD[URI]) ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('add_New_Vendor') }}"
                                    href="{{ route('admin.sellers.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('add_New_Vendor') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vendor', 'Vendor', 'view'))
                            <li class="nav-item {{ (!Request::has('status') && (Request::is('admin/sellers/' . Vendor::LIST[URI]) || Request::is('admin/sellers/' . Vendor::VIEW[URI] . '*'))) ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('vendor_List') }}" href="{{ route('admin.sellers.seller-list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('vendor_List') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            <li class="nav-item {{ Request::query('status') === 'approved' ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('live_vendor') }}" href="{{ route('admin.sellers.seller-list', ['status' => 'approved']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('live_vendor') }}
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::query('status') === 'pending' ? 'active' : '' }}">
                                <a class="nav-link" title="{{ translate('pending_vendor') }}" href="{{ route('admin.sellers.seller-list', ['status' => 'pending']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('pending_vendor') }}
                                    </span>
                                </a>
                            </li>
                            @if (Helpers::modules_permission_check('Vendor', 'Withdraws', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/sellers/' . Vendor::WITHDRAW_LIST[URI]) ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.sellers.withdraw_list') }}"
                                    title="{{ translate('withdraws') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('withdraws') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vendor', 'Withdraw Method', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/sellers/withdraw-method/*') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.sellers.withdraw-method.list') }}"
                                    title="{{ translate('withdrawal_Methods') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('withdrawal_Methods') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Delivery Men'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-man*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle text-capitalize"
                            href="javascript:" title="{{ translate('delivery_men') }}">
                            <i class="tio-user nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                {{ translate('delivery_men') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/delivery-man*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Delivery Men', 'Delivery Men', 'add'))
                            <li
                                class="nav-item {{ Request::is('admin/delivery-man/' . DeliveryMan::ADD[URI]) ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.delivery-man.add') }}"
                                    title="{{ translate('add_new') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_new') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Delivery Men', 'Delivery Men', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/delivery-man/' . DeliveryMan::LIST[URI]) || Request::is('admin/delivery-man/' . DeliveryMan::UPDATE[URI] . '*') || Request::is('admin/delivery-man/' . DeliveryMan::EARNING_STATEMENT_OVERVIEW[URI] . '*') || Request::is('admin/delivery-man/' . DeliveryMan::ORDER_HISTORY_LOG[URI] . '*') || Request::is('admin/delivery-man/' . DeliveryMan::EARNING_OVERVIEW[URI] . '*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.delivery-man.list') }}"
                                    title="{{ translate('list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('list') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Delivery Men', 'Chat', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/messages/' . Chatting::INDEX[URI] . '/delivery-man') ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.messages.index', ['type' => 'delivery-man']) }}"
                                    title="{{ translate('chat') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('chat') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Delivery Men', 'Withdraw', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/delivery-man/' . DeliverymanWithdraw::LIST[URI]) || Request::is('admin/delivery-man/' . DeliverymanWithdraw::VIEW[URI] . '*') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.delivery-man.withdraw-list') }}"
                                    title="{{ translate('withdraws') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('withdraws') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Delivery Men', 'Emergency Contact', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/delivery-man/emergency-contact') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.delivery-man.emergency-contact.index') }}"
                                    title="{{ translate('emergency_contact') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('Emergency_Contact') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    {{-- @if (auth('admin')->user()->admin_role_id == 1) --}}
                    @if (Helpers::modules_check('Employee'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/employee*') || Request::is('admin/custom-role*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('employees') }}">
                            <i class="tio-user nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('employees') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/employee*') || Request::is('admin/custom-role*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'view'))
                            {{-- <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/custom-role*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.custom-role.create') }}"
                                title="{{ translate('employee_Role_Setup') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('employee_Role_Setup') }}</span>
                            </a>
                    </li> --}}
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/custom-role*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="javascript:0"
                            title="{{ translate('employee_Role_Setup') }}"
                            onclick="permissionModal('store')">
                            <span class="tio-circle nav-indicator-icon"></span>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('employee_Role_Setup') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Employee', 'Employee Role List', 'view'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/custom-role*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="javascript:0"
                            title="{{ translate('employee_Role_List') }}"
                            onclick="permissionModal('list')">
                            <span class="tio-circle nav-indicator-icon"></span>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('employee_Role_List') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (Helpers::modules_permission_check('Employee', 'Employee', 'view'))
                    <li
                        class="nav-item {{ Request::is('admin/employee/' . Employee::LIST[URI]) || Request::is('admin/employee/' . Employee::ADD[URI]) || Request::is('admin/employee/' . Employee::UPDATE[URI] . '*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.employee.list') }}"
                            title="{{ translate('employees') }}">
                            <span class="tio-circle nav-indicator-icon"></span>
                            <span class="text-truncate">{{ translate('employees') }}</span>
                        </a>
                    </li>
                    @endif
                    </ul>
                    </li>
                    @endif
                    {{-- @endif --}}
                    @if (Helpers::modules_check('Subscriber'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/customer/' . Customer::SUBSCRIBER_LIST[URI]) ? 'active' : '' }}">
                        <a class="nav-link " href="{{ route('admin.customer.subscriber-list') }}"
                            title="{{ translate('subscribers') }}">
                            <span class="tio-user nav-icon"></span>
                            <span class="text-truncate">{{ translate('subscribers') }} </span>
                        </a>
                    </li>
                    @endif
                    {{-- @endif --}}
                    @if (Helpers::modules_check('Business Setup') ||
                    Helpers::modules_check('System Setup') ||
                    Helpers::modules_check('3rd Party') ||
                    Helpers::modules_check('Page & Media') ||
                    Helpers::modules_check('React Website Configuration'))
                    <li
                        class="nav-item {{ Request::is('admin/business-settings/web-config') ||
                                    Request::is('admin/product-settings') ||
                                    Request::is('admin/business-settings/' . SocialMedia::VIEW[URI]) ||
                                    Request::is('admin/business-settings/web-config/' . BusinessSettings::APP_SETTINGS[URI]) ||
                                    Request::is('admin/business-settings/' . Pages::TERMS_CONDITION[URI]) ||
                                    Request::is('admin/business-settings/' . Pages::VIEW[URI] . '*') ||
                                    Request::is('admin/business-settings/' . Pages::PRIVACY_POLICY[URI]) ||
                                    Request::is('admin/business-settings/' . Pages::ABOUT_US[URI]) ||
                                    Request::is('admin/helpTopic/' . HelpTopic::LIST[URI]) ||
                                    Request::is('admin/business-settings/' . PushNotification::INDEX[URI]) ||
                                    Request::is('admin/business-settings/' . Mail::VIEW[URI]) ||
                                    Request::is('admin/business-settings/web-config/' . BusinessSettings::LOGIN_URL_SETUP[URI]) ||
                                    Request::is('admin/business-settings/web-config/' . DatabaseSetting::VIEW[URI]) ||
                                    Request::is('admin/business-settings/web-config/' . EnvironmentSettings::VIEW[URI]) ||
                                    Request::is('admin/business-settings/' . BusinessSettings::INDEX[URI]) ||
                                    Request::is('admin/business-settings/' . BusinessSettings::COOKIE_SETTINGS[URI]) ||
                                    Request::is('admin/business-settings/' . BusinessSettings::OTP_SETUP[URI]) ||
                                    Request::is('admin/system-settings/' . SoftwareUpdate::VIEW[URI]) ||
                                    Request::is('admin/business-settings/web-config/theme/' . ThemeSetup::VIEW[URI]) ||
                                    Request::is('admin/business-settings/shipping-method/' . ShippingMethod::UPDATE[URI] . '*') ||
                                    Request::is('admin/business-settings/shipping-method/' . ShippingMethod::INDEX[URI]) ||
                                    Request::is('admin/business-settings/delivery-restriction') ||
                                    Request::is('admin/addon')
                                        ? 'scroll-here'
                                        : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('system_Settings') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Business Setup'))
                    <li class="navbar-vertical-aside-has-menu">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('business_Setup') }}">
                            <i class="tio-pages-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('business_Setup') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/business-settings/web-config') ||
                                        Request::is('admin/product-settings') ||
                                        Request::is('admin/product-settings/' . InhouseShop::VIEW[URI]) ||
                                        Request::is('admin/business-settings/payment-method/' . PaymentMethod::PAYMENT_OPTION[URI]) ||
                                        Request::is('admin/business-settings/seller-settings') ||
                                        Request::is('admin/customer/' . Customer::SETTINGS[URI]) ||
                                        Request::is('admin/business-settings/delivery-man-settings') ||
                                        Request::is('admin/business-settings/shipping-method/' . ShippingMethod::UPDATE[URI] . '*') ||
                                        Request::is('admin/business-settings/shipping-method/' . ShippingMethod::INDEX[URI]) ||
                                        Request::is('admin/business-settings/order-settings/index') ||
                                        Request::is('admin/' . BusinessSettings::PRODUCT_SETTINGS[URI]) ||
                                        Request::is('admin/business-settings/delivery-restriction')
                                            ? 'block'
                                            : 'none' }}">
                            @if (Helpers::modules_permission_check('Business Setup', 'Business Setting', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/business-settings/web-config') ||
                                                Request::is('admin/product-settings') ||
                                                Request::is('admin/business-settings/payment-method/' . PaymentMethod::PAYMENT_OPTION[URI]) ||
                                                Request::is('admin/business-settings/seller-settings') ||
                                                Request::is('admin/customer/' . Customer::SETTINGS[URI]) ||
                                                Request::is('admin/business-settings/delivery-man-settings') ||
                                                Request::is('admin/business-settings/shipping-method/' . ShippingMethod::UPDATE[URI] . '*') ||
                                                Request::is('admin/business-settings/shipping-method/' . ShippingMethod::INDEX[URI]) ||
                                                Request::is('admin/business-settings/order-settings/index') ||
                                                Request::is('admin/' . BusinessSettings::PRODUCT_SETTINGS[URI]) ||
                                                Request::is('admin/business-settings/delivery-restriction')
                                                    ? 'active'
                                                    : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.business-settings.web-config.index') }}"
                                    title="{{ translate('business_Settings') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('business_Settings') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Business Setup', 'In-House Shop', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/product-settings/' . InhouseShop::VIEW[URI]) ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.product-settings.inhouse-shop') }}"
                                    title="{{ translate('in-house_Shop') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('in-house_Shop') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('System Setup'))
                    <li class="navbar-vertical-aside-has-menu ">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('system_Setup') }}">
                            <i class="tio-pages-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('system_Setup') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/business-settings/web-config/' . EnvironmentSettings::VIEW[URI]) ||
                                        Request::is('admin/business-settings/web-config/' . SiteMap::VIEW[URI]) ||
                                        Request::is('admin/currency/' . Currency::LIST[URI]) ||
                                        Request::is('admin/currency/' . Currency::UPDATE[URI] . '*') ||
                                        Request::is('admin/business-settings/web-config/' . DatabaseSetting::VIEW[URI]) ||
                                        Request::is('admin/business-settings/language*') ||
                                        Request::is('admin/business-settings/web-config/theme/' . ThemeSetup::VIEW[URI]) ||
                                        Request::is('admin/business-settings/web-config/' . BusinessSettings::LOGIN_URL_SETUP[URI]) ||
                                        Request::is('admin/system-settings/' . SoftwareUpdate::VIEW[URI]) ||
                                        Request::is('admin/business-settings/' . BusinessSettings::COOKIE_SETTINGS[URI]) ||
                                        Request::is('admin/business-settings/' . BusinessSettings::OTP_SETUP[URI]) ||
                                        Request::is('admin/business-settings/web-config/' . BusinessSettings::APP_SETTINGS[URI]) ||
                                        Request::is('admin/addon')
                                            ? 'block'
                                            : 'none' }}">
                            @if (Helpers::modules_permission_check('System Setup', 'System Setting', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/business-settings/web-config/' . EnvironmentSettings::VIEW[URI]) ||
                                                Request::is('admin/business-settings/web-config/' . SiteMap::VIEW[URI]) ||
                                                Request::is('admin/currency/' . Currency::LIST[URI]) ||
                                                Request::is('admin/currency/' . Currency::UPDATE[URI] . '*') ||
                                                Request::is('admin/business-settings/web-config/' . DatabaseSetting::VIEW[URI]) ||
                                                Request::is('admin/business-settings/language*') ||
                                                Request::is('admin/system-settings/' . SoftwareUpdate::VIEW[URI]) ||
                                                Request::is('admin/business-settings/' . BusinessSettings::COOKIE_SETTINGS[URI]) ||
                                                Request::is('admin/business-settings/web-config/' . BusinessSettings::APP_SETTINGS[URI]) ||
                                                Request::is('admin/business-settings/delivery-restriction')
                                                    ? 'active'
                                                    : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.business-settings.web-config.app-settings') }}"
                                    title="{{ translate('system_Settings') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('system_Settings') }}
                                    </span>
                                </a>
                                {{-- <a class="nav-link"
                                    href="{{ route('admin.business-settings.web-config.environment-setup') }}"
                                    title="{{ translate('system_Settings') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('system_Settings') }}
                                    </span>
                                </a> --}}
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('System Setup', 'Logging Setting', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/business-settings/web-config/' . BusinessSettings::LOGIN_URL_SETUP[URI]) ||
                                                Request::is('admin/business-settings/' . BusinessSettings::OTP_SETUP[URI])
                                                    ? 'active'
                                                    : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.business-settings.otp-setup') }}"
                                    title="{{ translate('login_Settings') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('login_Settings') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('System Setup', 'Themes & Addons', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/addon') ||
                                                Request::is('admin/business-settings/web-config/theme/' . ThemeSetup::VIEW[URI])
                                                    ? 'active'
                                                    : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.business-settings.web-config.theme.setup') }}"
                                    title="{{ translate('themes_&_Addons') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('themes_&_Addons') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('3rd Party'))
                    <li class="navbar-vertical-aside-has-menu">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('3rd_Party') }}">
                            <span class="tio-key nav-icon"></span>
                            <span class="text-truncate">{{ translate('3rd_Party') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/business-settings/mail' . Mail::VIEW[URI]) ||
                                        Request::is('admin/business-settings/offline-payment-method/' . OfflinePaymentMethod::INDEX[URI]) ||
                                        Request::is('admin/business-settings/offline-payment-method/' . OfflinePaymentMethod::ADD[URI]) ||
                                        Request::is('admin/business-settings/offline-payment-method/' . OfflinePaymentMethod::UPDATE[URI] . '/*') ||
                                        Request::is('admin/business-settings/' . SMSModule::VIEW[URI]) ||
                                        Request::is('admin/business-settings/' . Recaptcha::VIEW[URI]) ||
                                        Request::is('admin/social-login/' . SocialLoginSettings::VIEW[URI]) ||
                                        Request::is('admin/social-media-chat/' . SocialMediaChat::VIEW[URI]) ||
                                        Request::is('admin/business-settings/' . GoogleMapAPI::VIEW[URI]) ||
                                        Request::is('admin/business-settings/payment-method') ||
                                        Request::is('admin/business-settings/' . BusinessSettings::ANALYTICS_INDEX[URI]) ||
                                        Request::is('admin/business-settings/payment-method/offline-payment*')
                                            ? 'block'
                                            : 'none' }}">
                            @if (Helpers::modules_permission_check('3rd Party', 'Payment Method', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/business-settings/payment-method') ||
                                                Request::is('admin/business-settings/payment-method/offline-payment*') ||
                                                Request::is('admin/business-settings/offline-payment-method/' . OfflinePaymentMethod::INDEX[URI]) ||
                                                Request::is('admin/business-settings/offline-payment-method/' . OfflinePaymentMethod::ADD[URI]) ||
                                                Request::is('admin/business-settings/offline-payment-method/' . OfflinePaymentMethod::UPDATE[URI] . '/*')
                                                    ? 'active'
                                                    : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.business-settings.payment-method.index') }}"
                                    title="{{ translate('payment_methods') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('payment_methods') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('3rd Party', 'Other Configuration', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu
                                {{ Request::is('admin/business-settings/mail' . Mail::VIEW[URI]) ||
                                Request::is('admin/business-settings/' . SMSModule::VIEW[URI]) ||
                                Request::is('admin/business-settings/' . Recaptcha::VIEW[URI]) ||
                                Request::is('admin/social-login/' . SocialLoginSettings::VIEW[URI]) ||
                                Request::is('admin/social-media-chat/' . SocialMediaChat::VIEW[URI]) ||
                                Request::is('admin/business-settings/' . BusinessSettings::ANALYTICS_INDEX[URI]) ||
                                Request::is('admin/business-settings/' . GoogleMapAPI::VIEW[URI])
                                    ? 'active'
                                    : '' }}
                                ">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.social-media-chat.view') }}"
                                    title="{{ translate('other_Configurations') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('other_Configurations') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('Page & Media'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/' . Pages::TERMS_CONDITION[URI]) ||
                                    Request::is('admin/business-settings/' . Pages::VIEW[URI] . '*') ||
                                    Request::is('admin/business-settings/' . Pages::PRIVACY_POLICY[URI]) ||
                                    Request::is('admin/business-settings/' . Pages::ABOUT_US[URI]) ||
                                    Request::is('admin/helpTopic/' . HelpTopic::LIST[URI]) ||
                                    Request::is('admin/business-settings/' . FeaturesSection::VIEW[URI]) ||
                                    Request::is('admin/business-settings/' . FeaturesSection::COMPANY_RELIABILITY[URI])
                                        ? 'active'
                                        : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Pages_&_Media') }}">
                            <i class="tio-pages-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('Pages_&_Media') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/page*') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') || Request::is('admin/business-settings/social-media') || Request::is('admin/file-manager*') || Request::is('admin/business-settings/features-section') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'view'))
                            <li
                                class="nav-item {{ Request::is('admin/business-settings/' . Pages::TERMS_CONDITION[URI]) ||
                                                Request::is('admin/business-settings/' . Pages::VIEW[URI] . '*') ||
                                                Request::is('admin/business-settings/' . Pages::PRIVACY_POLICY[URI]) ||
                                                Request::is('admin/business-settings/' . Pages::ABOUT_US[URI]) ||
                                                Request::is('admin/helpTopic/' . HelpTopic::LIST[URI]) ||
                                                Request::is('admin/business-settings/' . FeaturesSection::VIEW[URI]) ||
                                                Request::is('admin/business-settings/' . FeaturesSection::COMPANY_RELIABILITY[URI])
                                                    ? 'active'
                                                    : '' }}">
                                <a class="nav-link"
                                    href="{{ route('admin.business-settings.terms-condition') }}"
                                    title="{{ translate('business_Pages') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('business_Pages') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Page & Media', 'Social Media', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/' . SocialMedia::VIEW[URI]) ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.business-settings.social-media') }}"
                                    title="{{ translate('social_Media_Links') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('social_Media_Links') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Page & Media', 'Gallery', 'view'))
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/file-manager*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.file-manager.index') }}"
                                    title="{{ translate('gallery') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('gallery') }}
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Helpers::modules_check('React Website Configuration'))
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/react') ? 'active' : '' }}">
                        <a class="nav-link text-capitalize" href="{{ route('admin.react.index') }}"
                            title="{{ translate('React_website_configuration') }}">
                            <span class="tio-rear-window-defrost nav-icon"></span>
                            <span
                                class="text-truncate text-capitalize">{{ Str::limit(translate('React_website_configuration'), 22, '...') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (count(config('addon_admin_routes')) > 0)
                    <li
                        class="navbar-vertical-aside-has-menu
                        @foreach (config('addon_admin_routes') as $routes)
                        @foreach ($routes as $route)
                        {{ strstr(Request::url(), $route['path']) ? 'active' : '' }} @endforeach
                        @endforeach
                        ">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('Pages_&_Media') }}">
                            <i class="tio-puzzle nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('addon_Menus') }}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display:
                            @foreach (config('addon_admin_routes') as $routes)
                            @foreach ($routes as $route)
                            {{ strstr(Request::url(), $route['path']) ? 'block' : '' }} @endforeach
                            @endforeach
                            ">
                            @foreach (config('addon_admin_routes') as $routes)
                            @foreach ($routes as $route)
                            <li
                                class="navbar-vertical-aside-has-menu {{ strstr(Request::url(), $route['path']) ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ $route['url'] }}"
                                    title="{{ translate($route['name']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate($route['name']) }}
                                    </span>
                                </a>
                            </li>
                            @endforeach
                            @endforeach
                        </ul>
                    </li>
                    @endif
                    @endif
                    <li
                        class="nav-item {{ Request::is('admin/whatsapp*') || Request::is('admin/temple/category*') || Request::is('admin/temple/hotel*') || Request::is('admin/temple/restaurants*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('Whatsapp_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/whatsapp*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('citis') }}">
                            <i class="tio-whatsapp-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('What`s_App_Template') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/whatsapp*') ? 'block' : 'none' }}">
                            {{-- @if (Helpers::modules_permission_check('Temple', 'City', 'add')) --}}
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('Ecommerce_template') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.ecom-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('E-commerce_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('Pooja_template') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.pooja-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('pooja_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('Offline_Pooja_template') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.whatsapp.offline-pooja-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('offline_Pooja_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('VIP_Anushthan_template') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.whatsapp.vip-anushthan-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('VIP_Anushthan_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('Chadhava_template') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.chadhava-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Chadhava_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('Counsultation_template') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.whatsapp.counsltancy-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Consultation_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('Events_template') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.event-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Events_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('Tours_template') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.tours-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Tours_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('donation_template') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.donation-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Donations_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('kundali_template') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.kundali-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Kundali_Template') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/temple-darshan-template') ? 'active' : '' }}"
                                title="{{ translate('temple_darshan_template') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.temple-darshan-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('temple_darshan_Template') }}</span>
                                </a>
                            </li>
                            {{-- @endif --}}
                            {{-- @if (Helpers::modules_permission_check('Temple', 'City', 'add')) --}}
                            <li class="nav-item {{ Request::is('admin/whatsapppanel/') ? 'active' : '' }}"
                                title="{{ translate('whatsapppanel') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.whatsapp-panel') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('whatsappanel') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsa/') ? 'active' : '' }}"
                                title="{{ translate('send_whatsapp_message') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.send-whatsapp-message') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('send_whatsapp_message') }}</span>
                                </a>
                            </li>
                            {{-- @endif --}}

                        </ul>
                    </li>
                    
                    @if (Helpers::modules_check('Email'))
                    <li
                        class="nav-item {{ Request::is('admin/email*') || Request::is('admin/temple/category*') || Request::is('admin/temple/hotel*') || Request::is('admin/temple/restaurants*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('Email_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/email*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('citis') }}">
                            <i class="tio-email-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Email_Template') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/email*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Email', 'Set Email', 'view'))
                            <li class="nav-item {{ Request::is('admin/email/') ? 'active' : '' }}"
                                title="{{ translate('Set_Email') }}">
                                <a class="nav-link" href="{{ route('admin.email.email-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Set_Email') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Email', 'Create Email', 'view'))
                            <li class="nav-item {{ Request::is('admin/email/create-template') ? 'active' : '' }}" title="{{ translate('Create_Email') }}">
                                <a class="nav-link" href="{{ route('admin.email.create-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Create_Email') }}</span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Email', 'Template List', 'view'))
                            <li class="nav-item {{ Request::is('admin/email/email-template-list') ? 'active' : '' }}" title="{{ translate('Template_Email_list') }}">
                                <a class="nav-link" href="{{ route('admin.email.email-template-list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Template_list') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- lead managent system -->
                    <li
                        class="nav-item {{ Request::is('admin/lead*') || Request::is('admin/temple/category*') || Request::is('admin/temple/hotel*') || Request::is('admin/temple/restaurants*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('daily_lead_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/whatsapp*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('daily_lead') }}">
                            <i class="tio-whatsapp-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('daily_lead_management') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/whatsapp*') ? 'block' : 'none' }}">
                            @if (Helpers::modules_permission_check('Pooja Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/pooja/lead/list') ? 'active' : '' }}"
                                title="{{ translate('pooja_leads') }}">
                                <a class="nav-link " href="{{ route('admin.pooja.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Pooja_Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'pooja')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Vip Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/vippooja/lead/list') ? 'active' : '' }}"
                                title="{{ translate('VIP_puja_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.vippooja.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('VIP Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'vip')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Anushthan Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/anushthan/lead/list') ? 'active' : '' }}"
                                title="{{ translate('Anushthan_puja_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.anushthan.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('Anushthan Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'anushthan')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Chadhava Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/chadhava/lead/list') ? 'active' : '' }}"
                                title="{{ translate('Chadhava_orders') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.chadhava.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('chadhava Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'chadhava')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if (Helpers::modules_permission_check('Consultation Order', 'Lead', 'view'))
                            <li class="nav-item {{ Request::is('admin/counselling/order/lead/list') ? 'active' : '' }}"
                                title="{{ translate('consultancy_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.counselling.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('consultancy_leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\Leads::where('type', 'counselling')->where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            <li class="nav-item {{ Request::is('admin/offlinepooja/lead/list') ? 'active' : '' }}"
                                title="{{ translate('offline_puja_leads') }}">
                                <a class="nav-link "
                                    href="{{ route('admin.offlinepooja.order.lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('offline_Leads') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\OfflineLead::where('status', 1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/whatsapp/') ? 'active' : '' }}"
                                title="{{ translate('offline_pooja_lead') }}">
                                <a class="nav-link" href="{{ route('admin.whatsapp.event-template') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('offline_pooja_lead') }}</span>
                                </a>
                            </li>
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/tour_visits/leads*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.tour_visits.leads') }}"
                                    title="{{ translate('Tour_leads') }}">
                                    <i class="tio-circle nav-indicator-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Tour_leads') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\TourLeads::count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/self-driving-management/self-driving-lead') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.self-driving-management.self-driving-lead') }}"
                                    title="{{ translate('self_driving_lead') }}">
                                    <i class="tio-circle nav-indicator-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('self_driving_lead') }}
                                        <span class="badge badge-soft-primary badge-pill ml-1">
                                            {{ \App\Models\SelfVehicleLeads::count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @if (Helpers::modules_permission_check('Birth Journal', 'Lead', 'view'))
                            <li class="nav-item {{  Request::is('admin/birth_journal/kundli_leads') || Request::is('admin/birth_journal/kundli_leads/view') ? 'active' : '' }}"
                                title="{{ translate('kundali_leads') }}">
                                <a class="nav-link"
                                    href="{{ route('admin.birth_journal.kundli_leads') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('kundali_leads') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\KundaliLeads::whereIn('status', [0, 2])->where('payment_status', '!=', '1')->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endif
                            <li class="nav-item {{ Request::is('admin/donate_management/donate_lead/trust-lead') ? 'active' : '' }}"
                                title="{{ translate('donate_leads') }}">
                                <a class="nav-link" href="{{ route('admin.donate_management.donate_lead.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">
                                        {{ translate('donate_lead') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\DonateLeads::whereIn('status', [0, 2])->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/temple/darshan-leads*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.temple.darshan-leads.leads-list') }}" title="{{ translate('darshan_Leads') }}">
                                    <i class="tio-circle nav-indicator-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('darshan_Leads') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\TempleDarshanLead::where('status',0)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/event-managment/leads*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.event-managment.leads.list') }}"
                                    title="{{ translate('Leads') }}">
                                    <i class="tio-circle nav-indicator-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Event_Leads') }}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{ \App\Models\EventLeads::whereIn('status',[0,2])->where('test',1)->count() }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ Request::is('admin/permission-module*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">{{ translate('permission_module') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/permission-module/module/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            title="{{ translate('add_Module') }}" onclick="permissionModule('module')">
                            <span class="tio-incognito"></span>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> {{ translate('add_Module') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/permission-module/role/*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            title="{{ translate('add_Role') }}" onclick="permissionModule('add')">
                            <span class="tio-incognito"></span>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> {{ translate('add_Role') }}</span>
                        </a>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/permission-module/role-list*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            title="{{ translate('Role-list') }}" onclick="permissionModule('list')">
                            <span class="tio-incognito"></span>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> {{ translate('Role-list') }}</span>
                        </a>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/permission-module/user-list*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{ route('admin.permission-module.user-list') }}"
                            title="{{ translate('user-list') }}">
                            <span class="tio-incognito"></span>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> {{ translate('user-list') }}</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('admin/book*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle" title="">{{ translate('Service_Booking') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/booking/service') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.book.type') }}"
                            title="{{ translate('Book') }}">
                            <span class="tio-incognito"></span>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> {{ translate('book') }}</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('admin/general/review*') ? 'scroll-here' : '' }}">
                        <small class="nav-subtitle"
                            title="">{{ translate('general_review') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <li
                        class="navbar-vertical-aside-has-menu {{ Request::is('admin/general/review*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{ translate('general/review') }}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate ml-3">{{ translate('general/review') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{ Request::is('admin/general/review*') ? 'block' : 'none' }}">
                            <li class="nav-item {{ Request::is('admin/general/review/add') ? 'active' : '' }}"
                                title="{{ translate('add_new') }}">
                                <a class="nav-link " href="{{ route('admin.general.review.add') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('add_new') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/general/review/list') ? 'active' : '' }}"
                                title="{{ translate('list') }}">
                                <a class="nav-link " href="{{ route('admin.general.review.list') }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('list') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>