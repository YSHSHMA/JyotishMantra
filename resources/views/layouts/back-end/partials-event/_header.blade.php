@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Utils\Helpers;
if (auth('event')->check()) {
$relationEmployees = auth('event')->user()->relation_id;
} elseif (auth('event_employee')->check()) {
$relationEmployees = auth('event_employee')->user()->relation_id;
}
$relationEmployeesData = \App\Models\Seller::where('type','event')->where('relation_id',$relationEmployees)->first();
@endphp
@php($direction = Session::get('direction'))
<div id="headerMain" class="d-none">
    <header id="header"
        class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper d-none d-sm-block d-xl-none">
                <a class="navbar-brand" href="{{route('event-vendor.dashboard.index')}}" aria-label="">
                    <?php $org_data_get = \App\Models\EventOrganizer::where('id', $relationEmployees)->first(); ?>
                    @if(\App\Models\EventOrganizer::where('id',$relationEmployees)->exists())
                    <img class="navbar-brand-logo"
                        src="{{getValidImage('storage/app/public/event/organizer/'.$org_data_get['image'],type:'backend-logo')}}" alt="{{translate('logo')}}" height="40">
                    <img class="navbar-brand-logo-mini"
                        src="{{getValidImage('storage/app/public/event/organizer/'.$org_data_get['image'],type:'backend-logo')}}" alt="{{translate('logo')}}" height="40">
                    @else
                    <img class="navbar-brand-logo-mini" src="{{dynamicAsset(path: 'public/assets/back-end/img/160x160/img1.jpg')}}" alt="{{translate('logo')}}" height="40">
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
                            @if(($relationEmployeesData['status']??'') == 'pending')
                            <span class="badge badge-soft-warning badge-pill ml-1" style="animation: blink 1s infinite;">Pending</span>
                            @elseif(($relationEmployeesData['status']??'') == 'hold')
                            <span class="badge badge-soft-warning badge-pill ml-1" style="animation: blink 1s infinite;">Hold</span>
                            @elseif(($relationEmployeesData['status']??'') == 'approved')
                            <span class="badge badge-soft-access badge-pill ml-1" style="animation: blink 1s infinite;">Active</span>
                            @elseif(($relationEmployeesData['status']??'') == 'rejected')
                            <span class="badge badge-soft-danger badge-pill ml-1" style="animation: blink 1s infinite;">Rejected Profile</span>
                            @elseif(($relationEmployeesData['status']??'') == 'suspended')
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
                            <a title="{{translate('website_view')}}" href="{{ route('event')}}" class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle" target="_blank">
                                <i class="tio-globe"></i>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item">
                        @php($notification_data=\App\Models\Notification::whereBetween('created_at', [$relationEmployeesData['created_at'], Carbon::now()])->where('sent_to', 'event')->where('status', '1')->with('notificationSeenBy')->latest()->get())
                        @php($notification_data2=\App\Models\Notification::whereBetween('created_at', [$relationEmployeesData['created_at'], Carbon::now()])->where('sent_to', 'event')->where('sent_by',auth('event')->id())->where('status', '0')->with('notificationSeenBy')->latest()->get())

                        <div class="hs-unfold">
                            <a
                                class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle media align-items-center gap-3 navbar-dropdown-account-wrapper dropdown-toggle-left-arrow dropdown-toggle-empty"
                                href="javascript:"
                                data-hs-unfold-options='{
                                     "target": "#notificationDropdown",
                                     "type": "css-animation"
                                   }'>
                                <i class="tio-notifications-on-outlined"></i>
                                @php($notification=App\Models\Notification::whereBetween('created_at', [$relationEmployeesData['created_at'], Carbon::now()])->where('sent_to', 'event')->where('status', '1')->whereDoesntHave('notificationSeenBy')->count())
                                @php($notification2=App\Models\Notification::whereBetween('created_at', [$relationEmployeesData['created_at'], Carbon::now()])->where('sent_to', 'event')->where('sent_by',auth('event')->id())->where('status', '0')->whereDoesntHave('notificationSeenBy')->count())
                                @if($notification != 0 || $notification2 != 0 )
                                <span
                                    class="btn-status btn-sm-status btn-status-danger notification_data_new_count">{{ $notification }}</span>
                                @endif
                            </a>
                            <div id="notificationDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account py-0 overflow-hidden width--20rem">
                                @php($notification_data=\App\Models\Notification::whereBetween('created_at', [$relationEmployeesData['created_at'], Carbon::now()])->where('sent_to', 'event')->where('status', '1')->with('notificationSeenBy')->latest()->get())
                                @php($notification_data2=\App\Models\Notification::whereBetween('created_at', [$relationEmployeesData['created_at'], Carbon::now()])->where('sent_to', 'event')->where('sent_by',auth('event')->id())->where('status', '0')->with('notificationSeenBy')->latest()->get())

                                @foreach ($notification_data2 as $item2)
                                <button class="dropdown-item position-relative notification-data-view"
                                    data-id="{{ $item2->id }}">
                                    <span class="text-truncate pr-2 d-block font-weight-bold" title="Settings">
                                        {{translate($item2->title)}}
                                        <span class="fs-10" style="float: inline-end;">{{ $item2->created_at->locale('en')->diffForHumans() }}</span>
                                    </span>
                                    <span class="fs-11">{!! nl2br(e($item2->description)) !!}</span>
                                    @if($item2->notification_seen_by == null)
                                    <span class="badge-soft-danger float-right small py-1 px-2 rounded notification_data_new_badge{{ $item2->id }}">{{translate('new')}}</span>
                                    @endif
                                </button>
                                <div class="dropdown-divider"></div>
                                @endforeach

                                @foreach ($notification_data as $item)
                                <button class="dropdown-item position-relative notification-data-view"
                                    data-id="{{ $item->id }}">
                                    <span class="text-truncate pr-2 d-block font-weight-bold" title="Settings">
                                        {{translate($item->title)}}
                                        <span class="fs-10" style="float: inline-end;">{{ $item->created_at->locale('en')->diffForHumans() }}</span>
                                    </span>
                                    <span class="fs-11">{{ ($item->description) }}</span>
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
                            <a class="js-hs-unfold-invoker media align-items-center gap-3 navbar-dropdown-account-wrapper dropdown-toggle dropdown-toggle-left-arrow"
                                href="javascript:"
                                data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                <div class="d-none d-md-block media-body text-right">
                                    @if(auth('event')->check())
                                    <h5 class="profile-name mb-0">{{auth('event')->user()->f_name}}</h5>
                                    @elseif (auth('event_employee')->check())
                                    <h5 class="profile-name mb-0">{{auth('event_employee')->user()->name}}</h5>
                                    @endif


                                </div>
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                        src="{{getValidImage(path:'storage/app/public/event/organizer/'.$org_data_get['image']??'',type:'backend-profile')}}"
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
                                                src="{{getValidImage(path:'storage/app/public/event/organizer/'.$org_data_get['image']??'',type:'backend-profile')}}"
                                                alt="{{translate('image_description')}}">
                                        </div>
                                        <div class="media-body">
                                            @if(auth('event')->check())
                                            <span class="card-title h5">{{auth('event')->user()->f_name}}</span>
                                            <span class="card-text">{{auth('event')->user()->email}}</span>
                                            @elseif (auth('event_employee')->check())
                                            <span class="card-title h5">{{auth('event_employee')->user()->name}}</span>
                                            <span class="card-text">{{auth('event_employee')->user()->email}}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                @if (Helpers::Employee_modules_permission('Profile', 'Password', 'View'))
                                <a class="dropdown-item" href="{{route('event-vendor.profile.update',[$relationEmployees])}}">
                                    <span class="text-truncate pr-2" title="Settings">{{translate('settings')}}</span>
                                </a>
                                @endif
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