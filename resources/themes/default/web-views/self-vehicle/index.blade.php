@extends('layouts.front-end.app')

@section('title', translate('self_vehicle_booking'))

@push('css_or_js')
<meta property="og:image" content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta property="twitter:card" content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/animationbutton.css') }}">
<style>
    .one-lines-only {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .two-lines-only {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.5em;
        min-height: 3em;
    }

    .responsive-bg {
        padding-top: 3rem !important;
        padding-bottom: 4rem !important;
        /* background:url("{{ asset('assets/front-end/img/slider/self-driving.png') }}") no-repeat; */
        background: url("{{ asset('public/assets/front-end/img/slider/self-driving.png') }}") no-repeat;
        background-size: cover;
        background-position: center center;
    }

    @media (max-width: 768px) {
        .responsive-bg {
            padding-top: 1.91rem !important;
            padding-bottom: 2rem !important;
            /* background:url("{{ asset('assets/front-end/img/slider/self-driving1.png') }}") no-repeat; */
            background: url("{{ asset('public/assets/front-end/img/slider/self-driving.png') }}") no-repeat;
            background-size: cover;
            background-position: center center;
        }

        .font-size-set {
            font-size: 12px;
        }
    }
</style>

@endpush
@section('content')
<div class="inner-page-bg center bg-bla-7 responsive-bg">
    <div class="container">
        <div class="row all-text-white">
            <div class="col-md-12 align-self-center text-center">
                <h1 class="innerpage-title mb-1">{{ translate('vehicles_available') }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">{{ ucwords(translate('vehicle')) }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<div class="container py-3" dir="{{Session::get('direction')}}">
    <div class="search-page-header">
        <div>

        </div>
        <form id="search-form" class="d-none d-lg-block" action="" method="GET">
            <div class="sorting-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                    <path d="M11.6667 7.80078L14.1667 5.30078L16.6667 7.80078" stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7.91675 4.46875H4.58341C4.3533 4.46875 4.16675 4.6553 4.16675 4.88542V8.21875C4.16675 8.44887 4.3533 8.63542 4.58341 8.63542H7.91675C8.14687 8.63542 8.33341 8.44887 8.33341 8.21875V4.88542C8.33341 4.6553 8.14687 4.46875 7.91675 4.46875Z"
                        stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7.91675 11.9688H4.58341C4.3533 11.9688 4.16675 12.1553 4.16675 12.3854V15.7188C4.16675 15.9489 4.3533 16.1354 4.58341 16.1354H7.91675C8.14687 16.1354 8.33341 15.9489 8.33341 15.7188V12.3854C8.33341 12.1553 8.14687 11.9688 7.91675 11.9688Z"
                        stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M14.1667 5.30078V15.3008" stroke="#D9D9D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <label class="for-sorting" for="sorting">
                    <span>{{translate('sort_by')}}</span>
                </label>
                <select class="selfvehicle-list-filter-on-viewpage">
                    <option value="latest" {{ request('sort_by') == 'latest' ? 'selected':'' }}>{{translate('latest')}}</option>
                    <option
                        value="low-high" {{ request('sort_by') == 'low-high' ? 'selected':'' }}>{{translate('low_to_High_Price')}} </option>
                    <option
                        value="high-low" {{ request('sort_by') == 'high-low' ? 'selected':'' }}>{{translate('High_to_Low_Price')}}</option>
                    <option
                        value="a-z" {{ request('sort_by') == 'a-z' ? 'selected':'' }}>{{translate('A_to_Z_Order')}}</option>
                    <option
                        value="z-a" {{ request('sort_by') == 'z-a' ? 'selected':'' }}>{{translate('Z_to_A_Order')}}</option>
                </select>
            </div>
        </form>
        <div class="d-lg-none">
            <div class="filter-show-btn btn btn--primary py-1 px-2 m-0">
                <i class="tio-filter"></i>
            </div>
        </div>
    </div>
</div>
<div class="container pb-5 mb-2 mb-md-4 rtl __inline-35" dir="{{Session::get('direction')}}">
    <div class="row">
        <aside class="col-lg-3 hidden-xs col-md-3 col-sm-4 SearchParameters __search-sidebar {{Session::get('direction') === "rtl" ? 'pl-2' : 'pr-2'}}" id="SearchParameters">
            <div class="cz-sidebar __inline-35" id="shop-sidebar">
                <div class="cz-sidebar-header bg-light">
                    <button class="close ms-auto" type="button" data-dismiss="sidebar" aria-label="Close">
                        <i class="tio-clear"></i>
                    </button>
                </div>
                <div>
                    <div class="text-center">
                        <div class="__cate-side-title pt-0">
                            <span class="widget-title font-semibold">{{translate('price')}} </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center __cate-side-price">
                            <div class="__w-35p">
                                <input class="bg-white cz-filter-search form-control form-control-sm appended-form-control" type="number" value="0" min="0" max="1000000" id="min_price" placeholder="{{ translate('min')}}">
                            </div>
                            <div class="__w-10p">
                                <p class="m-0">{{translate('to')}}</p>
                            </div>
                            <div class="__w-35p">
                                <input value="100" min="100" max="1000000"
                                    class="bg-white cz-filter-search form-control form-control-sm appended-form-control"
                                    type="number" id="max_price" placeholder="{{ translate('max')}}">

                            </div>

                            <div class="d-flex justify-content-center align-items-center __number-filter-btn">

                                <a class="action-search-products-by-price">
                                    <i class="__inline-37 czi-arrow-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}"></i>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 __cate-side-arrordion">
                    <div>
                        <div class="text-center __cate-side-title">
                            <span class="widget-title font-semibold">{{translate('vehicle_category')}}</span>
                        </div>
                        <div class="accordion mt-n1 __cate-side-price" id="shop-categories">
                            @foreach($vehicleList as $category)
                            @if ($category['type'])
                            <div class="menu--caret-accordion">
                                <div class="card-header flex-between">
                                    <div>
                                        <label class="for-hover-label cursor-pointer get-view-by-onclick" data-link="{{ route('products-slug',['slug'=> $category['slug'],'data_from'=>'category','page'=>1]) }}">
                                            {{$category['type']}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <section class="col-lg-9">
            <div class="row" id="ajax-products">
                @if($SelfVehicles)
                @foreach($SelfVehicles as $val)
                <div class="col-lg-4 col-md-4 col-sm-6 col-12  p-2">
                    <div class="card h-100 shadow-sm">
                        <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/self_driving/'.($val['thumbnail']??''), type: 'backend-product'); }}" class="card-img-top" alt="KLIA Ekspres">
                        <div class="card-body">
                            <span class="text-muted mb-1 one-lines-only">{{ $val['getCabId']['name']??""}}</span>
                            <h6 class="card-title mb-2 two-lines-only">{{ $val['getTraveller']['company_name']??""}}</h6>
                            <div class="rating mb-2"> ★ 4.9 • 200K+ booked</div>
                            <div>
                                <a class="text-capitalize fw-semibold ml-2">{{ translate('minimum_price')}} : {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($val['basic_price']??0)), currencyCode: getCurrencyCode()) }} </a>
                            </div>
                            <a href="{{ route('self-vehicle-choose',[($val['getCabId']['slug']??'')])}}" class="text-white animated-button mt-2">
                                <span class="text-wrapper d-inline mb-4">
                                    <span class="text-slide">{{ translate('book_now')}}</span>
                                    <span class="text-slide">{{ translate('limited_slots')}}!</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>

        </section>
    </div>
</div>
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
</script>

<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
@endpush