@php
use App\Models\RefundRequest;
use App\Enums\ViewPaths\Vendor\Order as OrderEnum;
use App\Utils\Helpers;
if (auth('guruji')->check()) {
    $vendorId = auth('guruji')->user()->id;
} 
@endphp

<div id="sidebarMain" class="d-none">
    <aside style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    
                    <a class="navbar-brand" href="{{ route('guruji.dashboard.index') }}" aria-label="Front">
                        <img class="navbar-brand-logo-mini for-seller-logo"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/900x400/img1.jpg') }}"
                            alt="{{ translate('logo') }}">
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
                       
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('guruji/dashboard*') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('guruji.dashboard.index') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('dashboard') }}
                                </span>
                            </a>
                        </li>
                        <!-- Package CRUD START -->
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('package_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('guruji/packages*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-folder-add nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('all_packages') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('guruji/packages*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('guruji/packages/create') ? 'active' : '' }}
                                   ">
                                    <a class="nav-link " href="{{ route('guruji.packages.create', $vendorId) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('packages') }}
                                            <span class="badge badge-soft-info badge-pill {{ Session::get('direction') === 'rtl' ? 'mr-1' : 'ml-1' }}"></span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Package CRUD END -->
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('service_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('guruji/services*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-files nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('all_services') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('guruji/services*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('guruji/services/puja/view') ? 'active' : '' }}
                                   ">
                                    <a class="nav-link " href="{{ route('guruji.services.puja.view', $vendorId) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('puja') }}
                                            <span class="badge badge-soft-info badge-pill {{ Session::get('direction') === 'rtl' ? 'mr-1' : 'ml-1' }}"></span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('guruji/services/chadhava/view') ? 'active' : '' }}
                                   ">
                                    <a class="nav-link " href="{{ route('guruji.services.chadhava.view', $vendorId) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('chadhava') }}
                                            <span class="badge badge-soft-info badge-pill {{ Session::get('direction') === 'rtl' ? 'mr-1' : 'ml-1' }}"></span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('guruji/services/counselling/view') ? 'active' : '' }}
                                   ">
                                    <a class="nav-link " href="{{ route('guruji.services.counselling.view', $vendorId) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('counselling') }}
                                            <span class="badge badge-soft-info badge-pill {{ Session::get('direction') === 'rtl' ? 'mr-1' : 'ml-1' }}"></span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('guruji/services/puja/individual/view') ? 'active' : '' }}
                                   ">
                                    <a class="nav-link " href="{{ route('guruji.services.puja.individual.view', $vendorId) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('Indivisual_services') }}
                                            <span class="badge badge-soft-info badge-pill {{ Session::get('direction') === 'rtl' ? 'mr-1' : 'ml-1' }}"></span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- ORder DEtails -->
                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('orders_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('guruji/orders*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('all_orders') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('guruji/orders*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('guruji/orders/puja/list') ? 'active' : '' }}
                                   ">
                                    <a class="nav-link " href="{{ route('guruji.orders.puja.list', $vendorId) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('puja_orders') }}
                                            <span class="badge badge-soft-info badge-pill {{ Session::get('direction') === 'rtl' ? 'mr-1' : 'ml-1' }}"></span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('guruji/orders/chadhava/list') ? 'active' : '' }}
                                   ">
                                    <a class="nav-link " href="{{ route('guruji.orders.chadhava.list', $vendorId) }}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('chadhava_orders') }}
                                            <span class="badge badge-soft-info badge-pill {{ Session::get('direction') === 'rtl' ? 'mr-1' : 'ml-1' }}"></span>
                                        </span>
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