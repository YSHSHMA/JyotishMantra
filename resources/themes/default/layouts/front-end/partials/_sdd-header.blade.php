@php($announcement = getWebConfig(name: 'announcement'))

@if (isset($announcement) && $announcement['status'] == 1)
    <div class="text-center position-relative px-4 py-1" id="announcement"
        style="background-color: {{ $announcement['color'] }};color:{{ $announcement['text_color'] }}">
        <span>{{ $announcement['announcement'] }} </span>
        <span class="__close-announcement web-announcement-slideUp">X</span>
    </div>
@endif


<header class="rtl __inline-10">
    @php($businessMode = getWebConfig(name: 'business_mode'))
    <div class="topbar">
        <div class="container">

            <div>
                <div class="topbar-text dropdown d-md-none ms-auto">
                    <a class="topbar-link direction-ltr" href="tel: {{ $web_config['phone']->value }}">
                        <i class="fa fa-phone"></i> {{ $web_config['phone']->value }}
                    </a>
                </div>
                <div class="d-none d-md-block mr-2 text-nowrap">
                    <a class="topbar-link d-none d-md-inline-block direction-ltr"
                        href="tel:{{ $web_config['phone']->value }}">
                        <i class="fa fa-phone"></i> {{ $web_config['phone']->value }}
                    </a>
                </div>
            </div>

            <div>
                @php($currency_model = getWebConfig(name: 'currency_model'))
                @if ($currency_model == 'multi_currency')
                    <div class="topbar-text dropdown disable-autohide mr-4">
                        <a class="topbar-link dropdown-toggle" href="#" data-toggle="dropdown">
                            <span>{{ session('currency_code') }} {{ session('currency_symbol') }}</span>
                        </a>
                        <ul
                            class="text-align-direction dropdown-menu dropdown-menu-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }} min-width-160px">
                            @foreach (\App\Models\Currency::where('status', 1)->get() as $key => $currency)
                                <li class="dropdown-item cursor-pointer get-currency-change-function"
                                    data-code="{{ $currency['code'] }}">
                                    {{ $currency->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="topbar-text dropdown disable-autohide  __language-bar text-capitalize">
                    <a class="topbar-link dropdown-toggle" href="#" data-toggle="dropdown">
                        @foreach (json_decode($language['value'], true) as $data)
                            @if ($data['code'] == getDefaultLanguage())
                                <img class="mr-2" width="20"
                                    src="{{ theme_asset(path: 'public/assets/front-end/img/flags/' . $data['code'] . '.png') }}"
                                    alt="{{ $data['name'] }}">
                                {{ $data['name'] }}
                            @endif
                        @endforeach
                    </a>
                    <ul
                        class="text-align-direction dropdown-menu dropdown-menu-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}">
                        @foreach (json_decode($language['value'], true) as $key => $data)
                            @if ($data['status'] == 1)
                                <li class="change-language" data-action="{{ route('change-language') }}"
                                    data-language-code="{{ $data['code'] }}">
                                    <a class="dropdown-item pb-1" href="javascript:">
                                        <img class="mr-2" width="20"
                                            src="{{ theme_asset(path: 'public/assets/front-end/img/flags/' . $data['code'] . '.png') }}"
                                            alt="{{ $data['name'] }}" />
                                        <span class="text-capitalize">{{ $data['name'] }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="navbar-sticky bg-light mobile-head">
        <div class="navbar navbar-expand-md navbar-light">
            <div class="container-fluid ">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand d-none d-sm-block mr-3 flex-shrink-0 __min-w-7rem" href="{{ route('home') }}">
                    <img class="__inline-11 "
                        src="{{ getValidImage(path: 'storage/app/public/company/' . $web_config['web_logo']->value, type: 'logo') }}"
                        alt="{{ $web_config['name']->value }}">
                </a>
                <a class="navbar-brand d-sm-none" href="{{ route('home') }}">
                    <img class="mobile-logo-img __inline-12 "
                        src="{{ getValidImage(path: 'storage/app/public/company/' . $web_config['mob_logo']->value, type: 'logo') }}"
                        alt="{{ $web_config['name']->value }}" />
                </a>

                <div class="mx-lg-4 search-form-mobile" style="width: 50%">
                    <a href="javascript:0"
                        {{ request()->is('*same-day-delivery') ? 'data-toggle=modal data-target=#changeAddressModal' : '' }}>
                        <h5 class="font-bold mb-0">{{ translate('same_day_delivery') }}</h5>
                        <div class="d-flex align-items-center gap-2">
                            <small class="font-semibold mb-1">
                                <span id="current-address"></span>
                            </small>
                            <i class="fa fa-location-dot"></i>
                        </div>
                    </a>

                </div>

                <div class="input-group-overlay mx-lg-2 search-form-mobile text-align-direction serBarIcon mr-2">
                    <form action="{{ route('products') }}" class="search_form">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-control appended-form-control search-bar-input" type="search"
                                autocomplete="off" placeholder="{{ translate('search_for_items') }}..." name="name"
                                value="{{ request('name') }}">

                            <button class="input-group-append-overlay search_button d-none d-md-block" type="submit">
                                <span class="input-group-text __text-20px">
                                    <i class="czi-search text-white"></i>
                                </span>
                            </button>

                            <span class="close-search-form-mobile fs-14 font-semibold text-muted d-md-none"
                                type="submit">
                                {{ translate('cancel') }}
                            </span>
                        </div>

                        <input name="data_from" value="search" hidden>
                        <input name="types" class="header-type-set" hidden>
                        <input name="page" value="1" hidden>
                        <diV class="card search-card mobile-search-card">
                            <div class="card-body">
                                <div class="search-result-box __h-400px overflow-x-hidden overflow-y-auto"></div>
                            </div>
                        </diV>
                    </form>
                </div>

                {{-- <a href="{{route('same-day-delivery')}}" class="btn btn--primary">{{translate('same_day_delivery')}}</a> --}}

                <div class="navbar-toolbar d-flex flex-shrink-0 align-items-center">
                    @if (request()->is('*same-day-delivery'))
                        <div
                            class="navbar-tool open-search-form-mobile d-lg-none {{ Session::get('direction') === 'rtl' ? 'mr-md-1' : 'ml-md-1' }}">
                            <a class="navbar-tool-icon-box bg-secondary" href="javascript:" data-toggle="modal"
                                data-target="#changeAddressModal">
                                <i class="fa fa-location-dot"></i>
                            </a>
                        </div>
                    @endif
                    <div
                        class="navbar-tool open-search-form-mobile d-lg-none {{ Session::get('direction') === 'rtl' ? 'mr-md-1' : 'ml-md-1' }}">
                        <a class="navbar-tool-icon-box bg-secondary" href="javascript:">
                            <i class="tio-search"></i>
                        </a>
                    </div>
                    <div class="dropdown">
                        <a class="navbar-tool {{ Session::get('direction') === 'rtl' ? 'mr-md-1' : 'ml-md-1' }}"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="navbar-tool-icon-box bg-secondary">
                                <div class="navbar-tool-icon-box bg-secondary">
                                    <div class="__img" style="width:27px;display: inline-block;">
                                        <img alt=" {{ translate('counselling') }}"
                                            src="{{ theme_asset(path: 'public/assets/front-end/img/menu-icon/allvender.png') }}">
                                    </div>
                                </div>
                            </div>
                        </a>
                        @if ($businessMode == 'multi')
                            @if (getWebConfig(name: 'seller_registration'))
                                <div class="text-align-direction dropdown-menu __auth-dropdown dropdown-menu-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}"
                                    aria-labelledby="dropdownMenuButton" style="max-width: 180px;">
                                    <a class="dropdown-item" href="{{ route('vendor.auth.registration.index') }}"
                                        style="display: block;">
                                        <i class="fa fa-sign-in mr-2"></i> {{ translate('become_a_vendor') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('vendor.auth.login') }}">
                                        <i class="fa fa-user-circle mr-2"></i>{{ translate('vendor_login') }}
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div
                        class="navbar-tool dropdown d-none d-md-block {{ Session::get('direction') === 'rtl' ? 'mr-md-1' : 'ml-md-1' }}">
                        <a class="navbar-tool-icon-box bg-secondary dropdown-toggle" href="{{ route('wishlists') }}">
                            <span class="navbar-tool-label">
                                <span class="countWishlist">
                                    {{ session()->has('wish_list') ? count(session('wish_list')) : 0 }}
                                </span>
                            </span>
                            <i class="navbar-tool-icon czi-heart"></i>
                        </a>
                    </div>
                    @if (auth('customer')->check())
                        <div class="dropdown">
                            <a class="navbar-tool ml-1" type="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <div class="navbar-tool-icon-box bg-secondary">
                                    <div class="navbar-tool-icon-box bg-secondary">
                                        <img class="img-profile rounded-circle __inline-14" alt=""
                                            src="{{ getValidImage(path: 'storage/app/public/profile/' . auth('customer')->user()->image, type: 'avatar') }}">
                                    </div>
                                </div>
                                <div class="navbar-tool-text">
                                    <small>{{ translate('hello') }}, {{ auth('customer')->user()->f_name }}</small>
                                    {{ translate('dashboard') }}
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}"
                                aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('account-order') }}">
                                    {{ translate('my_Order') }} </a>
                                <a class="dropdown-item" href="{{ route('user-account') }}">
                                    {{ translate('my_Profile') }}</a>
                                @php($total_wallet_balance = auth('customer')->user()->wallet_balance)
                                @if ($web_config['wallet_status'] == 1)
                                    <div class="d-flex">
                                        <a class="dropdown-item"
                                            href="{{ route('wallet') }}">{{ translate('my_wallet') }}</a>
                                        <span
                                            style="margin-right: 20px;">{{ webCurrencyConverter(amount: $total_wallet_balance ?? 0) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item"
                                    href="{{ route('customer.auth.logout') }}">{{ translate('logout') }}</a>
                            </div>
                        </div>
                    @else
                        <div class="dropdown">
                            <a class="navbar-tool {{ Session::get('direction') === 'rtl' ? 'mr-md-1' : 'ml-md-1' }}"
                                type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="navbar-tool-icon-box bg-secondary">
                                    <div class="navbar-tool-icon-box bg-secondary">
                                        <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M4.25 4.41675C4.25 6.48425 5.9325 8.16675 8 8.16675C10.0675 8.16675 11.75 6.48425 11.75 4.41675C11.75 2.34925 10.0675 0.666748 8 0.666748C5.9325 0.666748 4.25 2.34925 4.25 4.41675ZM14.6667 16.5001H15.5V15.6667C15.5 12.4509 12.8825 9.83341 9.66667 9.83341H6.33333C3.11667 9.83341 0.5 12.4509 0.5 15.6667V16.5001H14.6667Z"
                                                fill="#fe9802" />
                                        </svg>

                                    </div>
                                </div>
                            </a>
                            <div class="text-align-direction dropdown-menu __auth-dropdown dropdown-menu-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}"
                                aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('customer.auth.login') }}"
                                    style=" display: block;">
                                    <i class="fa fa-sign-in mr-2"></i> {{ translate('sign_in') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('customer.auth.sign-up') }}"
                                    style="display: block;">
                                    <i class="fa fa-user-circle mr-2"></i>{{ translate('sign_up') }}
                                </a>
                            </div>
                        </div>
                    @endif
                    <div id="cart_items">
                        @include('layouts.front-end.partials._cart')
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="navbar navbar-expand-md navbar-stuck-menu {{ request()->is('all-puja') ? '' : 'show' }}">
            <div class="container px-10px">
                <div class="collapse navbar-collapse text-align-direction" id="navbarCollapse">
                    <div class="w-100 d-md-none text-align-direction">
                        <button class="navbar-toggler p-0" type="button" data-toggle="collapse"
                            data-target="#navbarCollapse">
                            <i class="tio-clear __text-26px"></i>
                        </button>
                    </div>
                    <ul class="navbar-nav">

                        @if (auth('customer')->check())
                            <li class="nav-item d-md-none">
                                <a href="{{ route('user-account') }}" class="nav-link text-capitalize">
                                    {{ translate('user_profile') }}
                                </a>
                            </li>
                            <li class="nav-item d-md-none">
                                <a href="{{ route('wishlists') }}" class="nav-link">
                                    {{ translate('Wishlist') }}
                                </a>
                            </li>
                        @else
                            <li class="nav-item d-md-none">
                                <a class="dropdown-item pl-2" href="{{ route('customer.auth.login') }}">
                                    <i class="fa fa-sign-in mr-2"></i> {{ translate('sign_in') }}
                                </a>
                                <div class="dropdown-divider"></div>
                            </li>
                            <li class="nav-item d-md-none">
                                <a class="dropdown-item pl-2" href="{{ route('customer.auth.sign-up') }}">
                                    <i class="fa fa-user-circle mr-2"></i>{{ translate('sign_up') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                    @if (auth('customer')->check())
                        <div class="logout-btn mt-auto d-md-none">
                            <hr>
                            <a href="{{ route('customer.auth.logout') }}" class="nav-link">
                                <strong class="text-base">{{ translate('logout') }}</strong>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div> --}}
    </div>
</header>

@push('script')
    <style>
        @media (prefers-color-scheme: dark) {
            .site-logo-dark-light-option {
                content: url('https://mahakal.com/storage/app/public/company/2025-02-10-67a9f0a535193.webp');
            }
        }
    </style>
    <script>
        "use strict";

        $(".category-menu").find(".mega_menu").parents("li")
            .addClass("has-sub-item").find("> a")
            .append("<i class='czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}'></i>");
    </script>
@endpush
