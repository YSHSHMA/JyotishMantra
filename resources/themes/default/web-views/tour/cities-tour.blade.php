@extends('layouts.front-end.app')

@section('title', translate('special_Tours'))

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
    }

    .fixed-image {
        /* width: 100%; */
        height: 250px;
        object-fit: cover;
    }

    .fixed-image-a {
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, 0.085);
        background-repeat: no-repeat;
        box-sizing: border-box;
        -moz-osx-font-smoothing: grayscale;
        -webkit-font-smoothing: antialiased;
        position: relative;
        text-rendering: optimizeLegibility;
        overflow: hidden;
    }

    .fixed-image-a img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transform: scale(1);
        transition: transform 4s ease-in-out;
    }

    .fixed-image-a:hover img {
        transform: scale(1.2);
    }
</style>
@endpush

@section('content')
{{-- main page --}}
<div class="inner-page-bg center bg-bla-7 py-4"
    style="background:url({{  getValidImage(path: 'storage/app/public/tour/video/2018033136.jpg', type: 'backend-product'); }}) no-repeat;background-size:cover;background-position:center center">
    <div class="container">
        <div class="row all-text-white">
            <div class="col-md-12 align-self-center">

            </div>
        </div>

    </div>
</div>
<div class="container-fluid">
    <div class="row" tyle="position: relative; background:url({{  getValidImage(path: 'storage/app/public/tour/video/75d4f624ef44d259c3f4431b6790d963.jpg', type: 'backend-product'); }}) no-repeat;background-size:cover;background-position:center;background-attachment: fixed;">
        <div tyle="position: absolute;    top: 0;    left: 0;    right: 0;    bottom: 0;    background-color: rgb(52 51 51 / 38%);     z-index: 1;"></div>
        @if(!empty($special_tour) && count($special_tour)>0)
        <div class="col-md-12 mt-4" style="position: relative; z-index: 1;">
            <div>
                <svg style="width: 100%;height: 55px;">
                    <text x="21%" y="77%" text-anchor="middle">
                        {{ translate('special_Tours')}}
                    </text>
                </svg>
            </div>
        </div>
        <div class="col-md-12 mt-2 mb-4" style="position: relative; z-index: 1;">
            <div class="others-store-slider owl-theme owl-carousel">
                @foreach ($special_tour as $st)
                <div class="item">
                    <div class="card">
                        <a>
                            <div class="vertical-activity-card__photo">
                                <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/'.$st['tour_image'], type: 'backend-product'); }}" alt="Honolulu: Glass Bottom Boat Tour along Oahu's South Shore">
                            </div>
                        </a>
                        <div class="mb-1 mt-2 single-product-details min-height-unset">

                            <a class="text-capitalize fw-semibold ml-2 h5">
                                {{$st['tour_name']}}
                            </a>
                            <p class="ml-2 mb-1">
                                @if($st['use_date'] == 1)
                                {{ $st['startandend_date']??"" }}
                                @else
                                &nbsp;
                                @endif
                            </p>
                            @php
                                        $price_minst = 0;
                                        if (!empty($st['package_list']) && json_decode($st['package_list'], true)) {
                                        $prices = array_column(json_decode($st['package_list'], true), 'price');
                                        $price_minst = min($prices);
                                        }
                                        @endphp
                                        <a  class="text-capitalize fw-semibold ml-2">{{ translate('minimum_price')}} : {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($price_minst??0)), currencyCode: getCurrencyCode()) }} </a>

                            <div class='row'>
                                <a href="{{ route('tour.tour-visit-id',[base64_encode($st['id'])])}}" class="btn btn--primary btn-block btn-shadow m-3 font-weight-bold">{{ translate('book_Now')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="row" tyle="background:url({{  getValidImage(path: 'storage/app/public/tour/video/place2.jpg', type: 'backend-product'); }}) no-repeat;background-size:cover;background-position:center;background-attachment: fixed;position: relative;">
        <div tyle="position: absolute;    top: 0;    left: 0;    right: 0;    bottom: 0;    background-color: rgb(45 45 45 / 73%);  z-index: 1;"></div>
        <div class="col-md-12" style="position: relative; z-index: 1;">
            @if(!empty($cities_tour) && count($cities_tour) >0 )
            @foreach($cities_tour as $state=>$cities)
            <div class="row mb-5">
                <div class="col-12 ml-4 mb-3">
                    <span class="font-weight-bold h3 mb-3 ">{{$state}}</span>
                </div>
                <div class="col-12">
                    <div class="row pl-2">
                        @foreach ($cities as $city)
                        <div class="col-md-4 mt-2">
                            <div class="card overflow-hidden position-relative">
                                <div class="inline_product clickable">
                                    <a class="fixed-image-a">
                                        <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/'.$city['tour_image'], type: 'backend-product'); }}" alt="" class="fixed-image">
                                    </a>
                                </div>
                                <div class="single-product-details min-height-unset">
                                    <div>
                                        <a class="text-capitalize fw-semibold ml-2 mt-1 h5"> {{$city['tour_name']}} </a><br>
                                        @php
                                        $price_min = 0;
                                        if (!empty($city['package_list']) && json_decode($city['package_list'], true)) {
                                        $prices = array_column(json_decode($city['package_list'], true), 'price');
                                        $price_min = min($prices);
                                        }
                                        @endphp
                                        <a  class="text-capitalize fw-semibold ml-2">{{ translate('minimum_price')}} : {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($price_min??0)), currencyCode: getCurrencyCode()) }} </a>

                                    </div>
                                </div>
                                <div class='row'>
                                    <a href="{{ route('tour.tour-visit-id',[base64_encode($city['id'])])}}" class="btn btn--primary btn-block btn-shadow m-3 font-weight-bold">{{ translate('book_Now')}}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
            @endif

        </div>
    </div>
</div>
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>

@endpush