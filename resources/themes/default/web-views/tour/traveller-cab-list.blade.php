@extends('layouts.front-end.app')

@section('title', translate('Tours'))

@push('css_or_js')
<meta property="og:image"
    content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta property="twitter:card"
    content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">


<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/style.css') }}">

<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/theme.css') }}">

<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .carousel-tour-visit .item {
        margin: 5px;
    }

    .vertical-activity-card__photo img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }


    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .nav-link.location.active {
        font-weight: bold;
    }

    .nav-link.location {
        color: black !important;
    }

    svg text {
        font-family: Lora;
        letter-spacing: 10px;
        stroke: #fe9802;
        font-size: 50px;
        font-weight: 700;
        stroke-width: 2;
        animation: textAnimate 3s infinite alternate;
    }

    @keyframes textAnimate {
        0% {
            stroke-dasharray: 0 50%;
            stroke-dashoffset: 10%;
            fill: hsl(35.99deg 100% 57.62%)
        }

        50% {
            stroke-dasharray: 20% 0;
            stroke-dashoffstet: -20%;
            fill: hsla(19, 6%, 7%, 0%)
        }

        100% {
            stroke-dasharray: 50% 0;
            stroke-dashoffstet: -20%;
            fill: hsla(189, 68%, 75%, 0%)
        }

    }

    .loader-icon-search {
        display: none;
        font-size: 1.5rem;
        /* Adjust size as needed */
    }
</style>
@endpush

@section('content')
{{-- main page --}}

<div class="container-fluid">
    <div class="row" tyle="position: relative; background:url({{  getValidImage(path: 'storage/app/public/tour/video/75d4f624ef44d259c3f4431b6790d963.jpg', type: 'backend-product'); }}) no-repeat;background-size:cover;background-position:center;background-attachment: fixed;">
        <div class="col-md-12 mt-2">
            <div class="rtl">
                <div class="bg-white __shop-banner-main">
                    <img class="__shop-page-banner" alt="" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.($travellerinfo['image']??''), type: 'backend-product') }}">
                    <div class="position-relative z-index-99 rtl w-100 text-align-direction d-none d-md-block max-width-500px">
                        <div class="__rounded-10 bg-white position-relative">
                            <div class="d-flex flex-wrap justify-content-between seller-details">
                                <div class="d-flex align-items-center p-2 flex-grow-1">
                                    <div class="">
                                        <div class="position-relative">
                                            <img class="__img-60 img-circle" alt="w-90px rounded" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.($travellerinfo['image']??''), type: 'backend-product') }}">
                                        </div>
                                    </div>
                                    <div class="__w-100px flex-grow-1  pl-2 pl-sm-4">
                                        <div class="font-weight-bolder mb-2">
                                            {{ ($travellerinfo['owner_name']??"")}}
                                            
                                        </div>
                                        <div class="d-flex flex-column gap-1">
                                            <div class="fs-12">
                                                {{ ($travellerinfo['company_name']??"")}}
                                            </div>

                                            <div class="d-flex flex-wrap py-1 fs-12 web-text-primary">
                                                <span class="text-nowrap">{{ (\App\Models\TourOrder::where('cab_assign',$travellerinfo['id'])->where('status',1)->count() ) }} {{translate('orders')}}</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="row">
                @if(!empty($tourvisit) && count($tourvisit) > 0)
                @foreach($tourvisit as $use)
                <div class="col-md-4">
                    <div class="card portfolio">
                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                            <span class="direction-ltr blink d-block">{{ translate($use['tour_type'])}}
                            </span>
                        </span>
                        <a href="{{ route('tour.tourvisit',[base64_encode($use['id'])])}}"><img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/'.$use['tour_image'] , type: 'product') }}" class="card-img-top puja-image" alt="..."></a>
                        <div class="card-body newpadding">
                            <p class="pooja-heading underborder two-lines-only">{{$use['tour_name']}}
                            </p>
                            <div class="w-bar h-bar bg-gradient mt-2"></div>
                            <p class="ml-2 mb-1">
                                @if($use['use_date'] == 1)
                                {{ $use['startandend_date']??"" }}
                                @else
                                &nbsp;
                                @endif
                            </p>
                            @php
                            $price_minst = 0;
                            if (!empty($use['cab_list_price']) && json_decode($use['cab_list_price'], true)) {
                            $prices = array_column(json_decode($use['cab_list_price'], true), 'price');
                            $price_minst = min($prices);
                            }
                            @endphp
                            <a class="text-capitalize fw-semibold ml-2">{{ translate('minimum_price')}} : {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($price_minst??0)), currencyCode: getCurrencyCode()) }} </a>

                            <a href="{{ route('tour.tourvisit',[base64_encode($use['id'])])}}" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('book_Now')}} </a>
                        </div>
                    </div>

                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js"></script>
<!-- <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script> -->
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>

@endpush