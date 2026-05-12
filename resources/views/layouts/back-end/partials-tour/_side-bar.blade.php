@php
use App\Enums\ViewPaths\AllPaths\TourPath;
use App\Enums\ViewPaths\AllPaths\SelfDrivingPath;
@endphp
<div id="sidebarMain" class="d-none">
    <aside style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    @php($shop = \App\Models\TourAndTravel::where('id', auth('tour')->user()->relation_id ?? 0)->first())
                    <a class="navbar-brand" href="{{ route('tour-vendor.dashboard.index') }}" aria-label="Front">
                        @if (isset($shop))
                        <img class="navbar-brand-logo-mini for-seller-logo"
                            src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($shop['image'] ?? ''), type: 'backend-logo') }}"
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
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/dashboard*') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.dashboard.index') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('dashboard') }}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('tour_and_travels_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/tour_visits/'.TourPath::ADDTOUR[URL]) ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.tour_visits.add-tour') }}">
                                <i class="tio-stability_control_off nav-icon">stability_control_off</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_tour') }}
                                </span>
                            </a>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/tour_visits/'.TourPath::TOURLIST[URL]) ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.tour_visits.tour-list') }}">
                                <i class="tio-stability_control_off nav-icon">stability_control_off</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('tour_list') }}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('cab_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/tour_cab_management/'.TourPath::CABLIST[URL]) ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.tour_cab_management.cab-list') }}">
                                <i class="tio-boot_open nav-icon">boot_open</i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('cab_list') }}
                                </span>
                            </a>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/tour_cab_management/'.TourPath::DRIVERLIST[URL]) ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.tour_cab_management.cab-driver-list') }}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('driver_list') }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('lead_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/lead/'.TourPath::TOURLEADADD[URL]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.lead.add-lead') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('add_lead') }}
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/lead/'.TourPath::TOURLEADLIST[URL]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.lead.lead-list') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('lead_list') }}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('order_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/order/'.TourPath::ORDERPENDING[URL]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.order.pending') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('pending') }}
                                    <span class="badge badge-soft-danger badge-pill ml-1">
                                        {{
                                        \App\Models\TourOrder::where('amount_status', 1)
                                            ->where('refund_status', 0)
                                            ->whereIn('status', [1, 0])
                                            ->where('pickup_status', 0)
                                            ->where('drop_status', 0)
                                            ->where('cab_assign', 0)
                                            ->where('pickup_date', '>', \Carbon\Carbon::today()->toDateString())    
                                            ->whereHas('accept', function ($subQuery) {
                                                $subQuery->where('status', 1)
                                                        ->where('traveller_id', auth('tour')->user()->relation_id);
                                            })
                                            ->where(function ($q) {
                                                    $q->whereNull('cancel_vendor_list')
                                                        ->orWhere('cancel_vendor_list', '[]')
                                                        ->orWhere('cancel_vendor_list', '')
                                                        ->orWhereRaw(
                                                            "NOT JSON_CONTAINS(cancel_vendor_list, ?)",
                                                            [json_encode((string) auth('tour')->user()->relation_id)]
                                                        );
                                                })
                                            ->withCabOrderCheck(auth('tour')->user()->relation_id)
                                            ->count() 
                                        }}


                                    </span>
                                </span>
                            </a>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/order/'.TourPath::ORDERCONFIRM[URL]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.order.confirm') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('confirm') }}
                                    <span class="badge badge-soft-warning badge-pill ml-1">
                                        {{ \App\Models\TourOrder::where('amount_status', 1)->where('refund_status', 0)->whereIn('status', [1, 0])->where('pickup_status', 0)->where('drop_status', 0)->where('cab_assign', auth('tour')->user()->relation_id)->whereRaw("JSON_CONTAINS(traveller_cab_id, '0')")->count() }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/order/'.TourPath::ORDERASSIGNED[URL]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.order.assigned') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('assigned') }}
                                    <span class="badge badge-soft-warning badge-pill ml-1">
                                        {{ \App\Models\TourOrder::where('amount_status', 1)->where('refund_status', 0)->whereIn('status', [1, 0])->where('pickup_status', 0)->where('drop_status', 0)->where('cab_assign', auth('tour')->user()->relation_id)->whereRaw("NOT JSON_CONTAINS(traveller_cab_id, '0')")->count() }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/order/'.TourPath::ORDERPICKUP[URL]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.order.pickup') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('pickup') }}
                                    <span class="badge badge-soft-success badge-pill ml-1">
                                        {{ \App\Models\TourOrder::where('amount_status', 1)->where('refund_status', 0)->whereIn('status', [1, 0])->where('pickup_status', 1)->where('drop_status', 0)->where('cab_assign', auth('tour')->user()->relation_id)->count() }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/order/'.TourPath::ORDERCOMPLETE[URL]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.order.complete') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('complete') }}
                                    <span class="badge badge-soft-success badge-pill ml-1">
                                        {{ \App\Models\TourOrder::where('amount_status', 1)->where('refund_status', 0)->whereIn('status', [1, 0])->where('pickup_status', 1)->where('drop_status', 1)->where('cab_assign', auth('tour')->user()->relation_id)->count() }}
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/order/'.TourPath::ORDERCANCEL[URL]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.order.user-cancel') }}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('cancel') }}
                                    <span class="badge badge-soft-danger badge-pill ml-1">
                                        {{ \App\Models\TourOrder::where('refund_status',1)->where('status', 2)->where('traveller_id',auth('tour')->user()->relation_id)->count() }}
                                    </span>
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('self_driving_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/self-driving/'.SelfDrivingPath::ADDCAB[URI]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.self-driving.add-vehicle') }}">
                                <i class="tio-door_open nav-icon">door_open </i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Add Self Driving Vehicle
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/self-driving/'.SelfDrivingPath::SELFDRIVINGLIST[URI]) ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('tour-vendor.self-driving.self-vehicle-list') }}">
                                <i class="tio-door_open nav-icon">door_open </i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Self Driving List
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('help_&_support') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/messages*') || Request::is('tour-vendor/message*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="{{ translate('support_Ticket') }}">
                                <i class="tio-chat nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('support_Ticket') }}
                                    @if (\App\Models\VendorSupportTicketConv::where('type', 'tour')->where('created_by', 'vendor')->where('vendor_id', auth('tour')->user()->relation_id)->where('status', 'open')->count() > 0)
                                    <span
                                        class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                    @endif
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{ Request::is('tour-vendor/messages*') || Request::is('tour-vendor/message*') ? 'block' : 'none' }}">
                                <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/messages/*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('tour-vendor.messages.index') }}">
                                        <i class="tio-support nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{ translate('from_vendor') }}
                                            @if (\App\Models\VendorSupportTicketConv::where('type', 'tour')->where('created_by', 'vendor')->where('vendor_id', auth('tour')->user()->relation_id)->where('status', 'open')->count() > 0)
                                            <span class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/message/*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('tour-vendor.message.index') }}">
                                        <i class="tio-support nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{ translate('from_admin') }}
                                            @if (\App\Models\VendorSupportTicketConv::where('type', 'tour')->where('created_by', 'admin')->where('vendor_id', auth('tour')->user()->relation_id)->where('status', 'open')->count() > 0)
                                            <span
                                                class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                            @endif
                                        </span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="nav-item {{ Request::is('vendor/withdraw') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ translate('business_section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('tour-vendor/withdraw') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('tour-vendor.withdraw.index') }}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('withdraws') }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>