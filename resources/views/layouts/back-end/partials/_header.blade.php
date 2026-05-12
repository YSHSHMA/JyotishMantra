@php
    use Illuminate\Support\Facades\Session;
@endphp
@php($direction = Session::get('direction'))
<div id="headerMain" class="d-none">
    <header id="header"
        class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container shadow">

        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper d-none d-sm-block d-xl-none">
                @php($ecommerceLogo = getWebConfig('company_web_logo'))
                <a class="navbar-brand" href="{{ route('admin.dashboard.index') }}" aria-label="">
                    <img class="navbar-brand-logo"
                        src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                        alt="{{ translate('logo') }}">
                    <img class="navbar-brand-logo-mini"
                        src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                        alt="{{ translate('logo') }}">
                </a>
            </div>
            <div class="navbar-nav-wrap-content-left">
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-3 d-xl-none">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                        data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        data-toggle="tooltip" data-placement="right" title="Expand"></i>
                </button>
            </div>
            <div class="navbar-nav-wrap-content-right"
                style="{{ $direction == 'rtl' ? 'margin-left:unset; margin-right: auto' : 'margin-right:unset; margin-left: auto' }}">
                <ul class="navbar-nav align-items-center flex-row gap-xl-16px">
                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                                href="{{ route('admin.auth.logs') }}">
                                <i class="tio-notification-add"></i>
                                <span
                                    class="btn-status btn-sm-status btn-status-danger">{{ \App\Models\LoginLogs::where('notification_status', 1)->count() }}</span>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <div class="hs-unfold">
                            <div>
                                @php($local = session()->has('local') ? session('local') : 'en')
                                @php($lang = \App\Models\BusinessSetting::where('type', 'language')->first())
                                <div
                                    class="topbar-text dropdown disable-autohide {{ $direction == 'rtl' ? 'ml-3' : 'm-1' }} text-capitalize">
                                    <a class="topbar-link dropdown-toggle d-flex align-items-center title-color"
                                        href="javascript:" data-toggle="dropdown">
                                        @foreach (json_decode($lang['value'], true) as $data)
                                            @if ($data['code'] == $local)
                                                <img class="{{ $direction == 'rtl' ? 'ml-2' : 'mr-2' }}" width="20"
                                                    src="{{ dynamicAsset(path: 'public/assets/front-end/img/flags/' . $data['code'] . '.png') }}"
                                                    alt="{{ $data['name'] }}">
                                                <span class="d-none d-sm-block">{{ $data['name'] }}</span>
                                                <span class="d-sm-none">{{ $data['code'] }}</span>
                                            @endif
                                        @endforeach
                                    </a>
                                    <ul class="dropdown-menu position-absolute">
                                        @foreach (json_decode($lang['value'], true) as $key => $data)
                                            @if ($data['status'] == 1)
                                                <li class="change-language"
                                                    data-action="{{ route('change-language') }}"
                                                    data-language-code="{{ $data['code'] }}">
                                                    <a class="dropdown-item py-1" href="javascript:">
                                                        <img class="{{ $direction == 'rtl' ? 'ml-2' : 'mr-2' }}"
                                                            width="20"
                                                            src="{{ dynamicAsset(path: 'public/assets/front-end/img/flags/' . $data['code'] . '.png') }}"
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
                    </li>

                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a title="Website home"
                                class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                                href="{{ route('home') }}" target="_blank">
                                <i class="tio-globe"></i>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a title="QR HOME"
                                class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                                href="javascript:void(0);" onclick="qr_model(this)">
                                <i class="tio-qr-code"></i>
                            </a>
                        </div>
                    </li>
                    @if (\App\Utils\Helpers::module_permission_check('support_section'))
                        <li class="nav-item">
                            <!-- Notification -->
                            <div class="hs-unfold">
                                <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                                    href="{{ route('admin.contact.list') }}">
                                    <i class="tio-email"></i>
                                    @php($message = \App\Models\Contact::where('seen', 0)->count())
                                    @if ($message != 0)
                                        <span
                                            class="btn-status btn-sm-status btn-status-danger">{{ $message }}</span>
                                    @endif
                                </a>
                            </div>
                        </li>
                    @endif

                    @if (\App\Utils\Helpers::module_permission_check('order_management'))
                        <li class="nav-item">
                            <div class="hs-unfold">
                                <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                                    href="{{ route('admin.orders.list', ['status' => 'pending']) }}">
                                    <i class="tio-shopping-cart-outlined"></i>
                                    <span
                                        class="btn-status btn-sm-status btn-status-danger">{{ \App\Models\Order::where('order_status', 'pending')->count() }}</span>
                                </a>
                            </div>
                        </li>
                    @endif

                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker media align-items-center gap-3 navbar-dropdown-account-wrapper dropdown-toggle dropdown-toggle-left-arrow"
                                href="javascript:"
                                data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                <div class="d-none d-md-block media-body text-right">
                                    <h5 class="profile-name mb-0">{{ auth('admin')->user()->name }}</h5>
                                    <span class="fz-12">{{ auth('admin')->user()->role->name ?? '' }}</span>
                                </div>
                                <div class="avatar border avatar-circle">
                                    <img class="avatar-img"
                                        src="{{ getValidImage('storage/app/public/admin/' . auth('admin')->user()->image, type: 'backend-profile') }}"
                                        alt="{{ translate('image_description') }}">
                                    <span class="d-none avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                            </a>
                            <div id="accountNavbarDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account">
                                <div class="dropdown-item-text">
                                    <div class="media align-items-center text-break">
                                        <div class="avatar avatar-sm avatar-circle mr-2">
                                            <img class="avatar-img"
                                                src="{{ getValidImage('storage/app/public/admin/' . auth('admin')->user()->image, type: 'backend-profile') }}"
                                                alt="{{ translate('image_description') }}">
                                        </div>
                                        <div class="media-body">
                                            <span class="card-title h5">{{ auth('admin')->user()->name }}</span>
                                            <span class="card-text">{{ auth('admin')->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                @if (auth('admin')->user()->admin_role_id == 1)
                                    <a class="dropdown-item" href="javascript:0" onclick="SettingAccessModal()">
                                        <span class="text-truncate pr-2"
                                            title="Settings">{{ translate('settings') }}</span>
                                    </a>
                                @else
                                    <a class="dropdown-item" href="{{ route('admin.profile.update', auth('admin')->user()->id) }}">
                                        <span class="text-truncate pr-2"
                                            title="Settings">{{ translate('settings') }}</span>
                                    </a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:" data-toggle="modal"
                                    data-target="#sign-out-modal">
                                    <span class="text-truncate pr-2"
                                        title="{{ translate('sign_out') }}">{{ translate('sign_out') }}</span>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div id="website_info" class="bg-secondary w-100 d-none">
            <div class="p-3">
                <div class="bg-white p-1 rounded">
                    @php($local = session()->has('local') ? session('local') : 'en')
                    <div
                        class="topbar-text dropdown disable-autohide {{ $direction == 'rtl' ? 'ml-3' : 'm-1' }} text-capitalize">
                        <a class="topbar-link dropdown-toggle title-color d-flex align-items-center" href="#"
                            data-toggle="dropdown">
                            @foreach (json_decode($lang['value'], true) as $data)
                                @if ($data['code'] == $local)
                                    <img class="{{ $direction == 'rtl' ? 'ml-2' : 'mr-2' }}" width="20"
                                        src="{{ dynamicAsset(path: 'public/assets/front-end/img/flags/' . $data['code'] . '.png') }}"
                                        alt="{{ $data['name'] }}">
                                    {{ $data['name'] }}
                                @endif
                            @endforeach
                        </a>
                        <ul class="dropdown-menu">
                            @foreach (json_decode($lang['value'], true) as $key => $data)
                                @if ($data['status'] == 1)
                                    <li class="change-language" data-action="{{ route('change-language') }}"
                                        data-language-code="{{ $data['code'] }}">
                                        <a class="dropdown-item pb-1" href="javascript:">
                                            <img class="{{ $direction == 'rtl' ? 'ml-2' : 'mr-2' }}" width="20"
                                                src="{{ dynamicAsset(path: 'public/assets/front-end/img/flags/' . $data['code'] . '.png') }}"
                                                alt="{{ $data['name'] }}" />
                                            <span class="text-capitalize">{{ $data['name'] }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="bg-white p-1 rounded mt-2">
                    <a title="Website home" class="p-2 title-color" href="{{ route('home') }}" target="_blank">
                        <i class="tio-globe"></i>
                        {{ translate('view_website') }}
                    </a>
                </div>
                @if (\App\Utils\Helpers::module_permission_check('support_section'))
                    <div class="bg-white p-1 rounded mt-2">
                        <a class="p-2  title-color" href="{{ route('admin.contact.list') }}">
                            <i class="tio-email"></i>
                            {{ translate('message') }}
                            @php($message = \App\Models\Contact::where('seen', 0)->count())
                            @if ($message != 0)
                                <span>({{ $message }})</span>
                            @endif
                        </a>
                    </div>
                @endif
                @if (\App\Utils\Helpers::module_permission_check('order_management'))
                    <div class="bg-white p-1 rounded mt-2">
                        <a class="p-2  title-color" href="{{ route('admin.orders.list', ['status' => 'pending']) }}">
                            <i class="tio-shopping-cart-outlined"></i>
                            {{ translate('order_list') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>
