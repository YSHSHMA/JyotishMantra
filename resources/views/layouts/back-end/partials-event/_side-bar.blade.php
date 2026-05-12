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
use App\Utils\Helpers;
use App\Enums\ViewPaths\Vendor\Order as OrderEnum;

if (auth('event')->check()) {
$relationEmployees = auth('event')->user()->relation_id;
} elseif (auth('event_employee')->check()) {
$relationEmployees = auth('event_employee')->user()->relation_id;
}

@endphp
<div id="sidebarMain" class="d-none">
    <aside style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <a class="navbar-brand" href="{{ route('event-vendor.dashboard.index') }}" aria-label="Front">
                        @if(\App\Models\EventOrganizer::where('id',$relationEmployees)->exists())
                        <?php $org_data_get = \App\Models\EventOrganizer::where('id', $relationEmployees)->first(); ?>
                        <img class="navbar-brand-logo-mini for-seller-logo"
                            src="{{getValidImage('storage/app/public/event/organizer/'.$org_data_get['image'],type:'backend-logo')}}"
                            alt="{{ translate('logo') }}">
                        @else
                        <img class="navbar-brand-logo-mini for-seller-logo"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/900x400/img1.jpg') }}"
                            alt="{{ translate('logo') }}">
                        @endif
                    </a>


                    <button type="button"
                        class="d-none js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
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
                            <input type="text" class="js-form-search form-control form--control"
                                id="search-bar-input" placeholder="{{ translate('search_menu') . '...' }}">
                        </div>
                    </div>
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/dashboard*') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.dashboard.index') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('dashboard') }}
                                </span>
                            </a>
                        </li>
                        @if (Helpers::Employee_modules_permission('Employee', 'Add Employee', 'View')
                        || Helpers::Employee_modules_permission('Employee', 'Employee List', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Employee_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Employee', 'Add Employee', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/employee/add-employee') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.employee.add-employee') }}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_Employee') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Employee', 'Employee List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/employee/employee-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.employee.employee-list') }}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Employee_List') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if (Helpers::Employee_modules_permission('Artist Management', 'Add Artist', 'View') || Helpers::Employee_modules_permission('Artist Management', 'Artist List', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('artist_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Artist Management', 'Add Artist', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/artist/add-artist') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.artist.add-artist') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_artist') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Artist Management', 'Artist List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/artist/artist-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.artist.list') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('artist_list') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif

                        @if (Helpers::Employee_modules_permission('Coupon Management', 'Add Coupon', 'View') || Helpers::Employee_modules_permission('Coupon Management', 'Coupon List', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('coupon_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Coupon Management', 'Add Coupon', 'View') || Helpers::Employee_modules_permission('Coupon Management', 'Coupon List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/coupon/add-coupon') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.coupon.add-coupon') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_coupon') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        
                        @if (Helpers::Employee_modules_permission('Sponsor Management', 'Add Sponsor', 'View') || Helpers::Employee_modules_permission('Sponsor Management', 'Sponsor List', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('Sponsor_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Sponsor Management', 'Add Sponsor', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/sponsor/add-sponsor') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.sponsor.add-sponsor') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_sponsor') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Sponsor Management', 'Sponsor List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/sponsor/sponsor-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.sponsor.sponsor-list') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('sponsor_list') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif

                        <!-- //////////////////////////// -->
                        @if (Helpers::Employee_modules_permission('POS Management', 'Add POS', 'View') || Helpers::Employee_modules_permission('Sponsor Management', 'POS List', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('POS_Management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('POS Management', 'Add POS', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/pos/add-pos') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.pos.add-pos') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_POS') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('POS Management', 'POS List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/pos/pos-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.pos.pos-list') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('POS_list') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        <!-- //////////////////////////// -->


                        @if (Helpers::Employee_modules_permission('Event Management', 'Add Event', 'View')
                        || Helpers::Employee_modules_permission('Event Management', 'Event List', 'View')
                        || Helpers::Employee_modules_permission('Event Management', 'Pending List', 'View')
                        || Helpers::Employee_modules_permission('Event Management', 'Upcomming List', 'View')
                        || Helpers::Employee_modules_permission('Event Management', 'Running List', 'View')
                        || Helpers::Employee_modules_permission('Event Management', 'Complete List', 'View')
                        || Helpers::Employee_modules_permission('Event Management', 'Cancel List', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('event_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Event Management', 'Add Event', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-management/add-event') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-management.add-event') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_event') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Event Management', 'Event List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-management/event-list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-management.event-list') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('event_list') }}
                                    <span class="badge badge-soft-success badge-pill ml-1">
                                        @php
                                        echo \App\Models\Events::where('event_organizer_id',$relationEmployees)->count();
                                        @endphp
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Event Management', 'Pending List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-management/event-pending') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-management.event-pending') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('pending_list') }}
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        {{ \App\Models\Events::where('event_organizer_id', $relationEmployees)
                                        ->where(function ($q) {
                                        $q->where(function ($q2) {
                                        $q2->where('status', 0)
                                        ->whereIn('is_approve', [0, 1, 2, 3, 4]);
                                        })
                                        ->orWhere(function ($q2) {
                                        $q2->where('status', 1)
                                        ->whereIn('is_approve', [0, 2, 3, 4]);
                                        });
                                        })
                                        ->count();
                                    }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Event Management', 'Upcomming List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-management/event-upcomming') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-management.event-upcomming') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('upcomming_list') }}
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        @php
                                        echo \App\Models\Events::where('event_organizer_id',$relationEmployees)->where('is_approve', 1)->where('status', 1)->whereRaw(" DATE(?) < STR_TO_DATE(
                                            IF(INSTR(start_to_end_date, ' - ' )> 0,
                                            SUBSTRING_INDEX(start_to_end_date, ' - ', 1),
                                            start_to_end_date
                                            ), '%Y-%m-%d') ", [now()->format('Y-m-d')])->count();
                                            @endphp
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Event Management', 'Running List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-management/event-running') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-management.event-running') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('running_list') }}
                                    <span class="badge badge-soft-warning badge-pill ml-1">
                                        @php
                                        echo \App\Models\Events::where('event_organizer_id',$relationEmployees)->where('is_approve', 1)->where('status', 1)->where(function ($query) {
                                        $query->whereRaw("
                                        DATE(?) BETWEEN
                                        STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', 1), '%Y-%m-%d')
                                        AND
                                        STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', -1), '%Y-%m-%d')
                                        ", [now()->format('Y-m-d')])
                                        ->orWhereRaw("
                                        DATE(?) =
                                        STR_TO_DATE(start_to_end_date, '%Y-%m-%d')
                                        ", [now()->format('Y-m-d')]);
                                        })->count();
                                        @endphp
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Event Management', 'Complete List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-management/event-complate') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-management.event-complate') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('complete_list') }}
                                    <span class="badge badge-soft-success badge-pill ml-1">
                                        @php
                                        echo \App\Models\Events::where('event_organizer_id',$relationEmployees)->where('is_approve', 1)->where('status', 1)->whereRaw("DATE(?) > STR_TO_DATE(
                                        IF(INSTR(start_to_end_date, ' - ') > 0,
                                        SUBSTRING_INDEX(start_to_end_date, ' - ', -1),
                                        start_to_end_date
                                        ), '%Y-%m-%d')", [now()->format('Y-m-d')])->count();
                                        @endphp
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Event Management', 'Cancel List', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-management/event-cancel') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-management.event-cancel') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('cancel_list') }}
                                    <span class="badge badge-soft-danger badge-pill ml-1">
                                        @php
                                        echo \App\Models\Events::where('event_organizer_id',$relationEmployees)->where('status',2)->count();
                                        @endphp
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if (Helpers::Employee_modules_permission('Order Management', 'Running Event Order', 'View')
                        || Helpers::Employee_modules_permission('Order Management', 'Complete Event Order', 'View')
                        || Helpers::Employee_modules_permission('Order Management', 'Event Order Refund', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('order_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Order Management', 'Running Event Order', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-order/running') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-order.running') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('running_Event_order') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Order Management', 'Complete Event Order', 'View'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-order/complate') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-order.complate') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('complete_Event_order') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Order Management', 'Event Order Refund', 'View'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/event-order/refund') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.event-order.refund') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('event_order_refund') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if (Helpers::Employee_modules_permission('Support Management', 'From Vendor', 'View')
                        || Helpers::Employee_modules_permission('Support Management', 'From Admin', 'View')
                        || Helpers::Employee_modules_permission('Qr Management', 'Qr Verify', 'View'))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('help_&_support') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Support Management', 'From Vendor', 'View')
                        || Helpers::Employee_modules_permission('Support Management', 'From Admin', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/support-ticket*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="{{ translate('support_Ticket') }}">
                                <i class="tio-chat nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('support_Ticket') }}
                                    @if (\App\Models\VendorSupportTicketConv::where('type', 'tour')->where('created_by', 'vendor')->where('vendor_id', $relationEmployees)->where('status', 'open')->count() > 0)
                                    <span
                                        class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                    @endif
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('event-vendor/messages*') || Request::is('event-vendor/message*') ? 'block' : 'none' }}">
                                @if (Helpers::Employee_modules_permission('Support Management', 'From Vendor', 'View'))
                                <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/messages*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('event-vendor.messages.index') }}">
                                        <i class="tio-support nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{ translate('from_vendor') }}
                                            @if (\App\Models\VendorSupportTicketConv::where('type', 'tour')->where('created_by', 'vendor')->where('vendor_id', $relationEmployees)->where('status', 'open')->count() > 0)
                                            <span
                                                class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                                @endif
                                @if (Helpers::Employee_modules_permission('Support Management', 'From Admin', 'View'))
                                <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/message/*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('event-vendor.message.index') }}">
                                        <i class="tio-support nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{ translate('from_admin') }}
                                            @if (\App\Models\VendorSupportTicketConv::where('type', 'tour')->where('created_by', 'admin')->where('vendor_id', $relationEmployees)->where('status', 'open')->count() > 0)
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
                        @if ( Helpers::Employee_modules_permission('Qr Management', 'Qr Verify', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/qr-code-verify*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.qr-code-verify.index') }}">
                                <i class="tio-qr_code nav-icon">qr_code</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('Qr_verify') }}
                                    <span class="badge badge-soft-info badge-pill ml-1">
                                        {{ \App\Models\Events::where('is_approve', 1) ->where('status', 1) ->where('event_organizer_id', $relationEmployees)
                                            ->whereRaw("
                                            JSON_CONTAINS(
                                                JSON_EXTRACT(all_venue_data, '$[*].date'),
                                                JSON_QUOTE(?)
                                            )
                                        ", [date('Y-m-d')])->count() }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if (Helpers::Employee_modules_permission('Transaction Management', 'Transaction', 'View')
                        || Helpers::Employee_modules_permission('Transaction Management', 'Withdrawal', 'View'))
                        <li class="nav-item {{ Request::is('event-vendor/withdraw') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ translate('transaction_section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if (Helpers::Employee_modules_permission('Transaction Management', 'Transaction', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/transaction') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.transaction.index') }}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('transaction') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if (Helpers::Employee_modules_permission('Transaction Management', 'Withdrawal', 'View'))
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('event-vendor/withdraw') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('event-vendor.withdraw.index') }}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('withdraws') }}
                                </span>
                            </a>
                        </li>
                        @endif
                        @endif

                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>