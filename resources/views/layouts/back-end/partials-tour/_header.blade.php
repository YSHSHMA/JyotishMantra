@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
@endphp
@php($direction = Session::get('direction'))
<div id="headerMain" class="d-none">
    <header id="header"
        class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper d-none d-sm-block d-xl-none">
                @php($shop=[])
                <a class="navbar-brand" href="{{route('vendor.dashboard.index')}}" aria-label="">
                    @if (isset($shop))
                    <img class="navbar-brand-logo"
                        src="{{getValidImage('storage/app/public/shop/',type:'backend-logo')}}" alt="{{translate('logo')}}"
                        height="40">
                    <img class="navbar-brand-logo-mini"
                        src="{{getValidImage('storage/app/public/shop/',type:'backend-logo')}}"
                        alt="{{translate('logo')}}" height="40">

                    @else
                    <img class="navbar-brand-logo-mini"
                        src="{{dynamicAsset(path: 'public/assets/back-end/img/160x160/img1.jpg')}}"
                        alt="{{translate('logo')}}" height="40">
                    @endif
                </a>
            </div>
            <div class="navbar-nav-wrap-content-left">
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-sm-3 d-xl-none">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                        data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        data-toggle="tooltip" data-placement="right" title="Expand"></i>
                </button>
                <div class="d-none">
                    <form class="position-relative">
                    </form>
                </div>
            </div>
            <div class="navbar-nav-wrap-content-right"
                style="{{$direction === "rtl" ? 'margin-left:unset; margin-right: auto' : 'margin-right:unset; margin-left: auto'}}">
                <ul class="navbar-nav align-items-center flex-row gap-xl-16px">

                    <li class="nav-item">
                        <div class="hs-unfold">
                            <span class="font-weight-bold">Current Status :</span>
                            @if(auth('tour')->user()->status == 'pending')
                            <span class="badge badge-soft-warning badge-pill ml-1" style="animation: blink 1s infinite;">Pending</span>
                            @elseif(auth('tour')->user()->status == 'hold')
                            <span class="badge badge-soft-warning badge-pill ml-1" style="animation: blink 1s infinite;">Hold</span>
                            @elseif(auth('tour')->user()->status == 'approved')
                            <span class="badge badge-soft-access badge-pill ml-1" style="animation: blink 1s infinite;">Active</span>
                            @elseif(auth('tour')->user()->status == 'rejected')
                            <span class="badge badge-soft-danger badge-pill ml-1" style="animation: blink 1s infinite;">Rejected Profile</span>
                            @elseif(auth('tour')->user()->status == 'suspended')
                            <span class="badge badge-soft-danger badge-pill ml-1" style="animation: blink 1s infinite;">suspended Profile</span>
                            @endif
                        </div>
                    </li>

                    <li class="nav-item">
                        <div class="hs-unfold">
                            <div>
                                @php( $local = session()->has('local')?session('local'):'en')
                                @php($lang = \App\Models\BusinessSetting::where('type', 'language')->first())
                                <div
                                    class="topbar-text dropdown disable-autohide {{$direction === "rtl" ? 'ml-3' : 'm-1'}} text-capitalize">
                                    <a class="topbar-link dropdown-toggle text-black d-flex align-items-center title-color"
                                        href="javascript:" data-toggle="dropdown">
                                        @foreach(json_decode($lang['value'],true) as $data)
                                        @if($data['code']==$local)
                                        <img class="{{$direction === "rtl" ? 'ml-2' : 'mr-2'}}" width="20"
                                            src="{{dynamicAsset(path: 'public/assets/front-end/img/flags/'.$data['code'].'.png')}}"
                                            alt="{{$data['name']}}">
                                        <span class="d-none d-sm-block">{{$data['name']}}</span>
                                        <span class="d-sm-none">{{$data['code']}}</span>
                                        @endif
                                        @endforeach
                                    </a>
                                    <ul class="dropdown-menu position-absolute">
                                        @foreach(json_decode($lang['value'],true) as $key =>$data)
                                        @if($data['status']==1)
                                        <li class="change-language" data-action="{{route('change-language')}}" data-language-code="{{$data['code']}}">
                                            <a class="dropdown-item pb-1" href="javascript:">
                                                <img class="{{$direction === "rtl" ? 'ml-2' : 'mr-2'}}"
                                                    width="20"
                                                    src="{{dynamicAsset(path: 'public/assets/front-end/img/flags/'.$data['code'].'.png')}}"
                                                    alt="{{$data['name']}}" />
                                                <span class="text-capitalize">{{$data['name']}}</span>
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
                            <a href="{{ route('tour.index') }}" title="{{translate('website_Tour_view')}}"
                                class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                                target="_blank">
                                <i class="tio-globe"></i>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a
                                class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle media align-items-center gap-3 navbar-dropdown-account-wrapper dropdown-toggle-left-arrow dropdown-toggle-empty"
                                href="javascript:"
                                data-hs-unfold-options='{
                                     "target": "#notificationDropdown",
                                     "type": "css-animation"
                                   }'>
                                <i class="tio-notifications-on-outlined"></i>
                                @php($notification= 0)
                                @if($notification!=0)
                                <span
                                    class="btn-status btn-sm-status btn-status-danger notification_data_new_count">{{ $notification }}</span>
                                @endif
                            </a>
                            <div id="notificationDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account py-0 overflow-hidden width--20rem">
                                @php($notification_data= [])
                                @foreach ($notification_data as $item)
                                <button class="dropdown-item position-relative notification-data-view"
                                    data-id="{{ $item->id }}">
                                    <span class="text-truncate pr-2 d-block"
                                        title="Settings">{{translate($item->title)}}</span>
                                    <span class="fs-10">{{ $item->created_at->diffforHumans() }}</span>
                                    @if($item->notification_seen_by == null)
                                    <span class="badge-soft-danger float-right small py-1 px-2 rounded notification_data_new_badge{{ $item->id }}">{{translate('new')}}</span>
                                    @endif
                                </button>
                                <div class="dropdown-divider"></div>
                                @endforeach

                            </div>
                        </div>
                    </li>

                    
                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                                href="{{route('tour-vendor.order.pending')}}">
                                <i class="tio-shopping-cart-outlined"></i>
                                @php($order= 0)
                                @if($order!=0)
                                <span class="btn-status btn-sm-status btn-status-danger">{{ $order }}</span>
                                @endif
                            </a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker media align-items-center gap-3 navbar-dropdown-account-wrapper dropdown-toggle dropdown-toggle-left-arrow"
                                href="javascript:"
                                data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                <div class="d-none d-md-block media-body text-right">
                                    <h5 class="profile-name mb-0">{{auth('tour')->user()->name}}</h5>
                                    <span class="fz-12">{{ Str::limit(($shop->name??""), 20) }}</span>
                                </div>
                                @php($shop = \App\Models\TourAndTravel::where('id', auth('tour')->user()->relation_id ?? 0)->first())
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                        src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($shop['image'] ?? ''), type: 'backend-logo') }}"
                                        alt="{{translate('image_description')}}">
                                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                            </a>
                            <div id="accountNavbarDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account __w-16rem">
                                <div class="dropdown-item-text">
                                    <div class="media align-items-center text-break">
                                        <div class="avatar avatar-sm avatar-circle mr-2">
                                            <img class="avatar-img"
                                                src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($shop['image'] ?? ''), type: 'backend-logo') }}"
                                                alt="{{translate('image_description')}}">
                                        </div>
                                        <div class="media-body">
                                            <span class="card-title h5">{{auth('tour')->user()->f_name}}</span>

                                            <span class="card-text">{{auth('tour')->user()->email}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('tour-vendor.profile.update',[auth('tour')->user()->relation_id])}}">
                                    <span class="text-truncate pr-2" title="Settings">{{translate('settings')}}</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:" data-toggle="modal" data-target="#sign-out-modal">
                                    <span class="text-truncate pr-2" title="{{translate('sign_out')}}">{{translate('sign_out')}}</span>
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
                    <div class="topbar-text dropdown disable-autohide {{$direction === "rtl" ? 'ml-3' : 'm-1'}} text-capitalize">
                        <a class="topbar-link dropdown-toggle title-color d-flex align-items-center" href="#"
                            data-toggle="dropdown">
                            @foreach(json_decode($lang['value'],true) as $data)
                            @if($data['code']==$local)
                            <img class="{{$direction === "rtl" ? 'ml-2' : 'mr-2'}}" width="20"
                                src="{{dynamicAsset(path: 'public/assets/front-end').'/img/flags/'.$data['code']}}.png"
                                alt="{{$data['name']}}">
                            {{$data['name']}}
                            @endif
                            @endforeach
                        </a>
                        <ul class="dropdown-menu">
                            @foreach(json_decode($lang['value'],true) as $key =>$data)
                            @if($data['status']==1)
                            <li class="change-language" data-action="{{route('change-language')}}" data-language-code="{{$data['code']}}">
                                <a class="dropdown-item pb-1" href="javascript:">
                                    <img class="{{$direction === "rtl" ? 'ml-2' : 'mr-2'}}" width="20"
                                        src="{{dynamicAsset(path: 'public/assets/front-end').'/img/flags/'.$data['code']}}.png"
                                        alt="{{$data['name']}}" />
                                    <span class="text-capitalize">{{$data['name']}}</span>
                                </a>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="bg-white p-1 rounded mt-2">
                    <a title="{{('website_shop_view')}}" class="p-2 title-color"
                        target="_blank">
                        <i class="tio-globe"></i>
                        {{translate('view_website')}}
                    </a>
                </div>
                <div class="bg-white p-1 rounded mt-2">
                    <a class="p-2  title-color">
                        <i class="tio-email"></i>

                    </a>
                </div>
                <div class="bg-white p-1 rounded mt-2">
                    <a class="p-2 title-color"
                        href="{{route('vendor.orders.list',['pending'])}}">
                        <i class="tio-shopping-cart-outlined"></i>
                        {{translate('order_list')}}
                    </a>
                </div>
            </div>
        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>