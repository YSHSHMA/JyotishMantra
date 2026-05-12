@extends('layouts.front-end.app')
@section('title', translate('darshan'))
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

    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/product-details.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/poojadetails.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />
    <style>
        .owl-nav .owl-prev,
        .owl-nav .owl-next {
            font-size: 24px;
            color: #fff;
            background: #333;
            border-radius: 50%;
            padding: 10px;
            position: absolute;
            top: 40%;
            cursor: pointer;
            z-index: 1000;
        }

        .owl-nav .owl-prev {
            left: -6px;
        }

        .owl-nav .owl-next {
            right: -6px;
        }

        a.section-link.active {
            color: #ffffff !important;
            background: var(--base) !important;
            font-weight: bold;
        }

        a.section-link {
            border-radius: 100px !important;
            padding: 9px 17px;
            /* font-size: 13px; */
            text-decoration: none;
        }

        .user-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .review-comment {
            display: inline-block;
            word-wrap: break-word;
            width: 100%;
        }

        .read-more-btn {
            color: #007bff;
            cursor: pointer;
            font-size: 14px;
            display: block;
            margin-top: 10px;
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 13px;
            margin-right: 13rem;
        }

        .countdown>div {
            display: flex;
            flex-wrap: nowrap;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
            box-shadow: 2px 2px 3px #fe9802;
            width: 62px;
            height: 45px;
            padding: 4px;
            font-size: 12px;
            border-radius: 5px;
        }

        .number {
            font-weight: 500;
            font-size: 25px;
            color: var(--web-primary);
        }

        /* Prograss */
        @media (min-width: 768px) {
            .md\:top-\[68px\] {
                top: 68px;
            }
        }

        .w-full {
            width: 100%;
        }

        .z-20 {
            z-index: 20;
        }

        .top-0 {
            top: 0;
        }

        .sticky {
            position: sticky;
        }

        .bg-bar {
            --tw-bg-opacity: 1;
            background-color: #f3f4f6;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .overflow-x-scroll {
            overflow-x: scroll;
        }

        .max-w-screen-xl {
            max-width: 1280px;
        }

        .justify-center {
            justify-content: center;
        }

        .items-center {
            align-items: center;
        }

        .px-2 {
            padding-left: .5rem;
            padding-right: .5rem;
        }

        .shrink-0 {
            flex-shrink: 0;
        }

        .text-next {
            --tw-text-opacity: 1;
            color: #1573DF;
        }

        .text-disable {
            --tw-text-opacity: 1;
            color: #5f6672;
        }

        .border-bar {
            --tw-border-opacity: 1;
            border-color: #5f6672 !important;
        }

        .border {
            border-width: 1px;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .circle-img-container:hover .circle-img {
            top: -8px;
            left: 0px;
            width: 40px;
            height: 43px;
            z-index: 10;
            max-height: 146px;
        }

        .circle-img-container .circle-img {
            width: 40px;
            height: 43px;
            overflow: hidden;
            position: absolute;
            left: 0;
            top: 0;
            transition: all 0.12s;
            margin-left: -20px;
            background-color: white;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .bg-center {
            background-position: center;
        }

        .bg-cover {
            background-size: cover;
        }

        .w-full {
            width: 100%;
        }

        .circle-img-container {
            width: 28px;
            height: 40px;
            position: relative;
        }

        .tray {
            text-align: center;
            display: flex;
            flex-wrap: none;
            align-items: center;
            justify-content: center;
            margin-right: 20rem;
            justify-content: center;
            margin-top: 12px;
        }


        .otp-input-fields {
            margin: auto;
            max-width: 400px;
            width: auto;
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 10px;
        }

        .otp-input-fields input {
            height: 42px;
            width: 41px;
            background-color: transparent;
            border-radius: 4px;
            border: 1px solid #2f8f1f;
            text-align: center;
            outline: none;
            font-size: 18px;
            /* Firefox */
        }

        .otp-input-fields input::-webkit-outer-spin-button,
        .otp-input-fields input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .otp-input-fields input[type=number] {
            -moz-appearance: textfield;
        }

        .otp-input-fields input:focus {
            border-width: 2px;
            border-color: #287a1a;
            font-size: 20px;
        }

        .product-preview-item {
            /* height: 60% !important; */
            aspect-ratio: unset;
        }

        .section-content {
            padding-bottom: 25px;
        }

        /* Make scrollbar very thin */
        ::-webkit-scrollbar {
            width: 2px;
            /* Change to 2px if 1px is too small to be visible */
            height: 2px;
        }

        /* Change scrollbar track */
        ::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Change scrollbar thumb */
        ::-webkit-scrollbar-thumb {
            background: #888;
            /* Change color */
            border-radius: 10px;
        }

        /* Hide scrollbar when not scrolling */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .inclu::before {
            content: '';
            position: absolute;
            left: 0;
            background: #e74c3c;
            height: 30px;
            width: 5px;
        }

        @media (max-width: 768px) {
            .navbar_section1 {
                font-size: 9px;
                top: 0px !important;
            }

            #breadcrum-container {
                font-size: 11px;
            }

            .venue-font-size {
                font-size: 8px;
            }

            .navbar_section1 a.section-link {
                padding: 6px 6px;
            }
        }

        .button-sticky {
            border-radius: 5px 5px 0 0;
            border: 1px solid rgba(20, 85, 172, 0.05);
            box-shadow: 0 -7px 30px 0 rgba(0, 113, 220, 0.1);
            position: sticky;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: all 150ms ease-in-out;
        }

        .booking-now {
            animation: shimmer 3s;
            animation-iteration-count: infinite;
            background: linear-gradient(to right, #FF641D 5%, #FFB357 25%, #FF7300 35%);
            background-size: 1000px 100%;
            padding: 13px 37px 13px 37px;
            border-radius: 13px;
            color: white !important;
            cursor: pointer;
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        @media (max-width: 320px) {
            .otp-input-fields input {
                height: 35px;
                width: 35px;
            }

            .otp-input-fields {
                gap: 9px;
            }

            .font-10 {
                font-size: 10px;
            }
        }



        .mat-button-wrapper {
            background-color: #2f1b12;
            color: #fff !important;
            border-radius: 13px;
            line-height: normal;
        }

        #vip-darshan-box {
            min-width: 30rem;
            border-radius: 18px 18px 0 0;
            background-color: #fefeed;
            padding-top: 1.5rem;
            display: none;
            position: fixed;
            bottom: 0;
            left: 50%;
            border-top-left: 3px solid #ff9200;
            border-top-right: 3px solid #ff9200;
            transform: translateX(-50%);
            width: 50%;
            z-index: 1000;
            box-shadow: rgb(145 88 127 / 54%) 0px 2px 10px !important;
            max-height: 80vh;
            overflow: auto;
        }

        #vip-darshan-wrapper {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .booking-right-side-show {
            position: fixed;
            top: 40%;
            right: 0;
            z-index: 9999;
        }

        .booking-right-side-show a {
            position: absolute;
            right: 0;
            display: flex;
            align-items: center;
            background: #ff9200;
            color: white;
            text-decoration: none;
            width: 45px;
            /* collapsed */
            height: 45px;
            overflow: hidden;
            white-space: nowrap;
            transition: width 0.5s ease;
            border-radius: 25px 0 0 25px;
            padding: 0 10px;
        }

        .booking-right-side-show a:nth-child(1) {
            top: 0;
        }

        .booking-right-side-show a:nth-child(2) {
            top: 55px;
        }

        .booking-right-side-show a:hover {
            width: 200px;
        }

        .booking-right-side-show i {
            font-size: 18px;
            margin-left: 10px;
            flex-shrink: 0;
        }

        .booking-right-side-show a span {
            opacity: 0;
        }

        .booking-right-side-show a:hover span {
            opacity: 1;
        }

        @media (max-width: 768px) {
            #vip-darshan-box {
                min-width: 24rem;
                font-size: 12px;
            }

            .booking-now {
                padding: 9px 20px 9px 20px;
                font-size: 14px;
            }
        }

        .partial-pooja {
            background: white;
            box-shadow: 0px 3px 6px rgb(0 0 0 / 29%);
            border-radius: 5px;
            border-top: 2px solid #fe9802;
        }

        .review-content {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 14px;
            height: 154px;
            text-align: center;
        }

        .owl-dots {
            top: 25px;
            position: relative !important;
        }
    </style>
@endpush
@section('content')
    <div class="container mt-3 rtl text-align-direction" id="cart-summary">
        <div class="__inline-23">
            <div class="container mt-4 rtl text-align-direction p-1">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-lg-6 col-md-4 col-12" style="height: fit-content;">
                                <div class="cz-product-gallery">
                                    <div class="cz-preview">
                                        <div id="sync1" class="owl-carousel owl-theme product-thumbnail-slider">
                                            @if (!empty($templeList['galleries2']['images']) && json_decode($templeList['galleries2']['images'], true))
                                                @foreach (json_decode($templeList['galleries2']['images'] ?? '[]', true) as $key => $photo)
                                                    <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                                        id="image{{ $key }}">
                                                        <img class="cz-image-zoom img-responsive w-100"
                                                            src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'product') }}"
                                                            data-zoom="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'product') }}"
                                                            alt="{{ translate('product') }}" width="">
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    <div class="cz">
                                        <div class="table-responsive __max-h-515px" data-simplebar>
                                            <div class="d-flex">
                                                <div id="sync2" class="owl-carousel owl-theme product-thumb-slider">
                                                    @if (!empty($templeList['galleries2']['images']) && json_decode($templeList['galleries2']['images'], true))
                                                        @foreach (json_decode($templeList['galleries2']['images'] ?? '[]', true) as $key => $photo)
                                                            <div class="">
                                                                <a class="product-preview-thumb {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                                                                    id="preview-img{{ $key }}"
                                                                    href="#image{{ $key }}">
                                                                    <img alt="{{ translate('product') }}"
                                                                        src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'product') }}">
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-8 col-12 mt-md-0 mt-sm-3 web-direction">
                                <div class="details __h-100">


                                    <span class="mb-2 __inline-24"
                                        style="color:#fe9802;">{{ $templeList['name'] }}</span><br />
                                    <div class="w-bar h-bar bg-gradient mt-2"></div>
                                    <div class="flex flex-col">
                                        <div class="flex items-center space-x-1 pt-2">
                                            <div class="d-flex">
                                                <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}"
                                                    alt="" style="width:24px;height:24px;">
                                                {{ $templeList['cities']['city'] }},
                                                {{ ucwords(strtolower($templeList['states']['name'] ?? '')) }},
                                                {{ $templeList['country']['name'] ?? '' }}
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1 pt-2">
                                            <div class="d-flex">
                                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/date.png') }}"
                                                    alt="Booking Date" style="width:24px;height:24px;">
                                                <p class="pooja-calendar">
                                                    {{ translate('opening_hours') }} :
                                                    {{ date('h:i A', strtotime($templeList['opening_time'])) }} -
                                                    {{ date('h:i A', strtotime($templeList['closeing_time'])) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="flex flex-wrap justify-center items-center">
                                            <div class="w-70 d-flex">
                                                <div class="tray mb-3 ml-3 mr-0">
                                                    @php
                                                        $uniqueUsers = range(0, 13);
                                                        shuffle($uniqueUsers);
                                                        $selectedUsers = array_slice($uniqueUsers, 0, 5);
                                                    @endphp
                                                    @foreach ($selectedUsers as $random_user)
                                                        <div class="relative circle-img-container">
                                                            <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                                style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $random_user . '.jpg') }}')">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <!-- Ratings Display -->
                                                @php
                                                    $base_reviews = 1000; // starting base
                                                    $total_count = $base_reviews;

                                                    if (!empty($ratings['alluser']) && count($ratings['alluser']) > 0) {
                                                        $total_count += count($ratings['alluser']); // add actual reviews
                                                    }

                                                    if ($total_count >= 1000000) {
                                                        $total_user_rating = round($total_count / 1000000, 1) . 'M+';
                                                    } elseif ($total_count >= 1000) {
                                                        $total_user_rating = round($total_count / 1000, 1) . 'K+';
                                                    } else {
                                                        $total_user_rating = $total_count;
                                                    }
                                                @endphp
                                                <div class="font-10">
                                                    <p
                                                        class="text-sm mt-4 font-medium border-b border-dashed border-primary font-weight-bold">
                                                        <i class="fas fa-star text-primary"></i>
                                                        @php
                                                            $number = round($ratings['total'], 1);
                                                            $reatings = $number;
                                                            if ($number >= 1000000) {
                                                                $reatings = round($number / 1000000, 1) . 'M' . '+';
                                                            } elseif ($number >= 1000) {
                                                                $reatings = round($number / 1000, 1) . 'K' . '+';
                                                            }
                                                        @endphp
                                                        {{ number_format($reatings, 1) }}/5 ({{ $total_user_rating }}
                                                        ratings)
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="ck-rendered-content">
                                            {!! $templeList['short_description'] ?? '' !!}
                                        </div>
                                    </div>
                                    <div class="mt-2 ck-rendered-content">
                                        {!! $templeList['expect_details'] !!}
                                    </div>
                                    <div class="mt-2 ck-rendered-content">
                                        {!! $templeList['tips_details'] !!}
                                    </div>
                                    <?php
                                    $matchingTrust = \App\Models\DonateTrust::all()->first(function ($items) use ($templeList) {
                                        $ids = json_decode($items->trust_temple_id, true);
                                        return in_array($templeList['id'], (array) $ids);
                                    });
                                    ?>
                                    @if ($matchingTrust && $matchingTrust['id'])
                                        <div class="my-2 d-md-block d-none">
                                            <a class="booking-now font-weight-bolder h5"
                                                onclick="$('#vip-darshan-wrapper').show(); $('#vip-darshan-box').slideDown('slow');">{{ translate('Book VIP Darshan') }}</a>
                                            <a class="booking-now font-weight-bolder h5"
                                                href="{{ route('all-donate_trust', ['slug' => $matchingTrust['slug']]) }}">{{ translate('donate to Temple') }}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid" style="padding-left: 0 !important;padding-right:0 !important;">
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="navbar_section1 section-links w-100 mt-3 border-top border-bottom py-2 mb-4"
                    style="overflow: auto;">
                    <div class="d-flex justify-content-around">
                        <a class="section-link ml-2 {{ request()->comment == 'success' || request()->comment == 'error' ? '' : 'active' }}"
                            href="#religious_Places">{{ translate('religious_Places') }}</a>
                        @if ($nearbyHotels->count() > 0)
                            <a class="section-link" href="#near-hotel-places">{{ translate('near_hotel') }}</a>
                        @endif
                        @if ($nearbyRestaurant->count() > 0)
                            <a class="section-link" href="#near-restaurant-places">{{ translate('near_Restaurant') }}</a>
                        @endif
                        @if ($nearbyCities->count() > 0)
                            <a class="section-link" href="#near-cities-places">{{ translate('near_Cities') }}</a>
                        @endif
                        <a class="section-link" href="#more_info">{{ translate('more_info') }}</a>
                        <a class="section-link mr-2 {{ request()->comment == 'success' || request()->comment == 'error' ? 'active' : '' }}"
                            href="#review_user">{{ translate('Reviews') }}</a>

                    </div>
                </div>
                <div class="px-4 pb-3 mb-3 __rounded-10 pt-3">
                    <div class="content-sections px-lg-3">
                        <div class="section-content {{ request()->comment == 'success' || request()->comment == 'error' ? '' : 'active' }}"
                            id="religious_Places">
                            @include('web-views.darshan.partial.near-temple')
                        </div>
                        @if ($nearbyHotels->count() > 0)
                            <div class="section-content" id="near-hotel-places">
                                @include('web-views.darshan.partial.near-hotel')
                            </div>
                        @endif
                        @if ($nearbyRestaurant->count() > 0)
                            <div class="section-content" id="near-restaurant-places">
                                @include('web-views.darshan.partial.near-restaurant')
                            </div>
                        @endif
                        @if ($nearbyCities->count() > 0)
                            <div class="section-content" id="near-cities-places">
                                @include('web-views.darshan.partial.near-cities')
                            </div>
                        @endif
                        <div class="section-content" id="more_info">
                            <div class="row mt-2 p-2 partial-pooja pb-3 px-4">
                                <div class="col-12"><span class="h6 font-weight-bold"></span></div>
                                <div class="col-12 feature-product-title mt-0 text-center">
                                    {{ translate('more_Info') }}
                                    <h4 class="mt-2 height-10">
                                        <span class="divider">&nbsp;</span>
                                    </h4>
                                </div>
                                <div class="col-12 mt-2 ck-rendered-content">
                                    {!! $templeList['details'] !!}
                                </div>
                            </div>
                            <div class="row mt-2 p-2 partial-pooja pb-3 px-4">
                                <div class="col-12 mt-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-4 mt-1 font-weight-bold"><img
                                                    src="{{ theme_asset(path: 'public/assets/front-end/img/temple.png') }}"
                                                    style="width: 34px; padding-bottom: 14px; position: relative;">{{ translate('temple_known') }}
                                            </div>
                                            <div class="col-8 mt-1">{{ $templeList['temple_known'] }}</div>
                                            <div class="col-12 mt-1">
                                                <hr style="border: 1px solid #d0c6c624;">
                                            </div>
                                            <div class="col-4 mt-1 font-weight-bold"><img
                                                    src="{{ theme_asset(path: 'public/assets/front-end/img/time.png') }}"
                                                    style="width: 34px;padding: 0px 2px 10px 0px; position: relative;">{{ translate('timings') }}
                                            </div>
                                            <div class="col-8 mt-1">{{ translate('open') }} :
                                                {{ date('h:i A', strtotime($templeList['opening_time'])) }}
                                                {{ translate('close') }} :
                                                {{ date('h:i A', strtotime($templeList['closeing_time'])) }}
                                            </div>
                                            <div class="col-12 mt-1">
                                                <hr style="border: 1px solid #d0c6c624;">
                                            </div>
                                            <div class="col-4 mt-1 font-weight-bold"><img
                                                    src="{{ theme_asset(path: 'public/assets/front-end/img/entry_free.jpg') }}"
                                                    style="width: 34px;padding: 0px 2px 10px 0px; position: relative;">{{ translate('Entry fee') }}
                                            </div>
                                            <div class="col-8 mt-1">{{ $templeList['entry_fee'] }}</div>
                                            <div class="col-12 mt-1">
                                                <hr style="border: 1px solid #d0c6c624;">
                                            </div>
                                            <div class="col-4 mt-1 font-weight-bold"><img
                                                    src="{{ theme_asset(path: 'public/assets/front-end/img/Tips_and_restrictions.png') }}"
                                                    style="width: 34px;padding: 0px 2px 10px 0px; position: relative;">
                                                {{ translate('tips_and_restrictions') }}
                                            </div>
                                            <div class="col-8 mt-1">{{ $templeList['tips_restrictions'] }}</div>
                                            <div class="col-12 mt-1">
                                                <hr style="border: 1px solid #d0c6c624;">
                                            </div>
                                            <div class="col-4 mt-1 font-weight-bold"><img
                                                    src="{{ theme_asset(path: 'public/assets/front-end/img/facilities.png') }}"
                                                    style="width: 34px; padding: 0px 2px 10px 0px; position: relative;">{{ translate('facilities') }}
                                            </div>
                                            <div class="col-8 mt-1">{{ $templeList['facilities'] }}</div>
                                            <div class="col-12 mt-1">
                                                <hr style="border: 1px solid #d0c6c624;">
                                            </div>

                                            <div class="col-4 mt-1 font-weight-bold"><img
                                                    src="{{ theme_asset(path: 'public/assets/front-end/img/require_time.png') }}"
                                                    style="width: 34px; padding-bottom: 14px; position: relative;">{{ translate('Require time') }}
                                            </div>
                                            <div class="col-8 mt-1">{{ $templeList['require_time'] }}</div>
                                            <div class="col-12 mt-1">
                                                <hr style="border: 1px solid #d0c6c624;">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2 p-2 partial-pooja pb-3 px-4">
                                <div class="col-12 mt-2 ck-rendered-content">
                                    {!! $templeList['more_details'] !!}
                                </div>
                            </div>
                            <div class="row mt-2 p-2 partial-pooja pb-3 px-4">
                                <div class="col-12 mt-2 ck-rendered-content">
                                    {!! $templeList['temple_services'] !!}
                                </div>
                            </div>
                            <div class="row mt-2 p-2 partial-pooja pb-3 px-4">
                                <div class="col-12 mt-2 ck-rendered-content">
                                    {!! $templeList['temple_aarti'] !!}
                                </div>
                            </div>
                            <div class="row mt-2 p-2 partial-pooja pb-3 px-4">
                                <div class="col-12 mt-2 ck-rendered-content">
                                    {!! $templeList['tourist_place'] !!}
                                </div>
                            </div>
                            <div class="row mt-2 p-2 partial-pooja pb-3 px-4">
                                <div class="col-12 mt-2 ck-rendered-content">
                                    {!! $templeList['temple_local_food'] !!}
                                </div>
                            </div>
                        </div>
                        <div class="section-content {{ request()->comment == 'success' || request()->comment == 'error' ? 'active' : '' }}"
                            id="review_user">
                            <div class="row mt-2 p-2 partial-pooja pb-3 px-4">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="mx-auto max-w-screen-sm text-center mb-4">
                                                <h2 class="text-2xl font-bold tracking-tight mt-2">Reviews & Ratings</h2>
                                                <span class="text-base font-normal">Read what our beloved devotees have to
                                                    say about Mahakal.com.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">

                                            @if (!empty($ratings['list']) && count($ratings['list']) > 0)
                                                <div class="owl-theme owl-carousel review-slider">
                                                    {{-- Video --}}
                                                    @foreach ($ratings['list'] as $darshanR)
                                                        @if (isset($videoId) && $videoId)
                                                            <div class="card product-single-hover shadow-none rtl">
                                                                <div class="card-body position-relative">
                                                                    <div class="ratio ratio-16x9">
                                                                        <iframe width="100%" height="100%"
                                                                            src="https://www.youtube.com/embed/{{ $videoId ?? '' }}"
                                                                            frameborder="0"
                                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                                            allowfullscreen
                                                                            style="border-radius: 10px;"></iframe>
                                                                    </div>
                                                                    <div class="d-flex align-items-center mt-2">
                                                                        <img src="{{ asset('public/images/default.png') }}"
                                                                            alt="User Icon" class="user-icon"
                                                                            style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                                                        <div>
                                                                            <p class="fw-bold m-0"
                                                                                style="font-size:14px;">
                                                                                {{ $darshanR['userData']['name'] ?? 'User Name' }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- --}}
                                                        @else
                                                            <div class="card product-single-hover shadow-none rtl">
                                                                <div class="card-body position-relative">
                                                                    <div class="single-review-details">
                                                                        <div class="review-content"
                                                                            id="content-{{ $darshanR['id'] ?? '0' }}">
                                                                            {{ $darshanR['comment'] }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center mt-2">
                                                                        <img src="{{ getValidImage(path: 'storage/app/public/profile/' . ($darshanR['userData']['image'] ?? ''), type: 'backend-profile') }}"
                                                                            alt="User Icon" class="user-icon"
                                                                            style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                                                        <div>
                                                                            <p class="fw-bold m-0"
                                                                                style="font-size:14px;">
                                                                                {{ $darshanR['userData']['name'] ?? 'User Name' }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    {{-- --}}
                                                </div>
                                            @else
                                                <div class="text-center text-capitalize">
                                                    <p class="text-capitalize">
                                                        <small>{{ translate('No_comment_given_yet') }}!</small></p>
                                                </div>
                                            @endif
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

    <!-- <div class="button-sticky bg-white d-sm-none bottom-package-show">
            <div class="d-flex flex-column gap-1 py-2">
                <div class="d-flex gap-3 justify-content-center" role="button">
                    <a href="#packages" onclick="PackageOpens()" class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center package_view" data-toggle="tab" role="tab">
                        <span class="font-bold">{{ translate('Select Packages') }}</span>
                    </a>
                </div>
            </div>
        </div> -->
    <div class="row">
        @if ($templeList['video_url'] != null && str_contains($templeList['video_url'], 'youtube.com/embed/'))
            <div class="col-12 rtl text-align-direction">
                <style>
                    .resp-iframe__container {
                        position: relative;
                        overflow: hidden;
                        border-radius: 1rem;
                    }

                    .resp-iframe__embed {
                        position: absolute;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        border: 0;
                    }
                </style>
                <div class="resp-iframe">
                    <div class="resp-iframe__container">
                        <iframe width="420" height="315" src="{{ $templeList['video_url'] }}" frameborder="0"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen="">
                        </iframe>
                    </div>
                </div>
            </div>
        @endif
    </div>
    </div>

    </div>
    </div>


    <div class="modal fade rtl text-align-direction" id="participateModal" tabindex="-1" role="dialog"
        aria-labelledby="participateModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="flex justify-center items-center my-3">
                        <span class="text-18 font-bold ml-2">{{ translate('Fill_your_details_for_VIP_Darshan') }} </span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr class="bg-[#E6E4EB] w-full">
                <div class="modal-body flex justify-content-center">
                    <div id="recaptcha-container"></div>
                    <div class="w-full mt-1 px-2">
                        <span
                            class="block text-[16px] font-bold text-gray-900 dark:text-white">{{ translate('enter_Your_Whatsapp_Mobile_Number') }}</span>
                        <span
                            class="text-[12px] font-normal text-[#707070]">{{ translate('All information related to VIP Darshan will be sent to you on the WhatsApp number given below') }}..</span>
                        <!-- Model Form -->
                        <div class="w-full mr-9 px-0 pt-3">
                            <form class="needs-validation_" id="lead-store-form"
                                action="{{ route('vip-darshan-lead') }}" method="GET">
                                @csrf
                                @php
                                    if (auth('customer')->check()) {
                                        $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                    }
                                @endphp
                                <input type="hidden" name="id" class="model-id">
                                <input type="hidden" name="price" class="model-price">
                                <input type="hidden" name="receipt_price" class="model-receipt-price">
                                <input type="hidden" name="platform_fee" class="model-platform-fee">
                                <input type="hidden" name="platform_base" class="model-platform-base">
                                <input type="hidden" name="platform_gst" class="model-platform-gst">
                                <input type="hidden" name="package_name" class="model-package-name">
                                <input type="hidden" name="name" class="model-name">
                                <input type="hidden" name="temple_id" value="{{ $templeList['id'] }}">
                                <input type="hidden" name="verify_otp" id="phone-number-valid">
                                <div class="row">
                                    <div class="col-md-12" id="phone-div">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                <small class="text-primary">(
                                                    *{{ translate('country_code_is_must_like_for_IND') }} 91 )</small>
                                            </label>
                                            <input
                                                class="form-control text-align-direction phone-input-with-country-picker"
                                                type="tel"
                                                value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                                name="person_phone" id="person-number"
                                                placeholder="{{ translate('enter_phone_number') }}" required
                                                {{ isset($customer['phone']) ? 'readonly' : '' }}
                                                oninput="this.value=this.value.slice(0,10)">

                                            <input type="hidden" class="country-picker-phone-number w-50"
                                                name="person_phone" readonly>

                                            <p id="number-validation" class="text-danger" style="display: none">Enter
                                                Your Valid Mobile Number</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="name-div">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('your_name') }}</label>
                                            <input class="form-control text-align-direction"
                                                value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? ' ' . $customer['l_name'] : '' }}"
                                                type="text" name="person_name" id="person-name"
                                                placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                                required {{ isset($customer['f_name']) ? 'readonly' : '' }}
                                                onkeyup="this.value = this.value.replace(/[^a-zA-Z\s]/g, '').replace(/\b\w/g, l => l.toUpperCase());">
                                            <p id="name-validation" class="text-danger" style="display: none">Enter Your
                                                Name</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="otp-input-div" style="display: none;">
                                        <div class="form-group text-center">
                                            <label class="form-label font-semibold ">{{ translate('enter_OTP') }}</label>
                                            <div class="otp-input-fields">
                                                <input type="number" id="otp1" class="otp__digit otp__field__1"
                                                    inputmode="number">
                                                <input type="number" id="otp2" class="otp__digit otp__field__2"
                                                    inputmode="number">
                                                <input type="number" id="otp3" class="otp__digit otp__field__3"
                                                    inputmode="number">
                                                <input type="number" id="otp4" class="otp__digit otp__field__4"
                                                    inputmode="number">
                                                <input type="number" id="otp5" class="otp__digit otp__field__5"
                                                    inputmode="number">
                                                <input type="number" id="otp6" class="otp__digit otp__field__6"
                                                    inputmode="number">
                                            </div>
                                            <p id="otpValidation" class="text-danger"></p>
                                        </div>
                                    </div>
                                    <div class="mx-auto mt-1 __max-w-356" id="send-otp-btn-div">
                                        <button type="button"
                                            class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold one-time-otp-sends"
                                            id="send-otp-btn">
                                            {{ translate('send_OTP') }}
                                        </button>
                                    </div>

                                    <div class="mx-auto mt-1 __max-w-356" id="verify-otp-btn-div" style="display: none">
                                        <div class="d-flex">
                                            <button type="button"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold me-2"
                                                id="otp-back-btn">
                                                {{ translate('back') }}
                                            </button>
                                            <button type="submit"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                id="verify-otp-btn">
                                                {{ translate('verify_OTP') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3" id="resend-div" style="display: none;">
                                    <p id="resend-otp-timer-text" style="display: none"> Resend OTP in
                                        <span id="resend-otp-timer"></span>
                                    </p>
                                    <p id="resend-otp-btn-text" style="display: none">Didn't get the code?
                                        <a href="javascript:0" id="resend-otp-btn" style="color: blue;">Resend Otp</a>
                                    </p>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="vip-darshan-wrapper">
        <div id="vip-darshan-box" class="vip-box p-3 shadow">
            <div class="text-center">
                <div class="row">
                    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold m-0">{{ translate('Select an Option') }}</h5>
                        <button type="button" class="btn btn-sm shadow-sm mat-button-wrapper"
                            onclick="$('#vip-darshan-box').slideUp(200, function() { $('#vip-darshan-wrapper').hide(); });"
                            aria-label="Close">
                            <i class="fa fa-remove" style="font-size: 18px;"></i>
                        </button>
                    </div>
                    @if ($templeList['vip_plans'] && json_decode($templeList['vip_plans'], true))
                        @foreach (json_decode($templeList['vip_plans'], true) as $pass)
                            <div class="col-md-12">
                                <div class="darshan-item d-flex align-items-center p-3 rounded shadow-sm mb-3"
                                    role="button" onclick="showVipDarshanBox(this)"
                                    style="background-color: #fcf1d7; border-radius: 16px; margin-bottom: 1rem;"
                                    data-id="{{ $pass['id'] }}" data-price="{{ $pass['package'][0]['price'] ?? '' }}"
                                    data-receiptprice="{{ $pass['package'][0]['receipt_price'] ?? '' }}"
                                    data-platformfee="{{ $pass['package'][0]['platform_fee'] ?? '' }}"
                                    data-platformgst="{{ $pass['package'][0]['platform_gst'] ?? '' }}"
                                    data-platformbaseprice="{{ $pass['package'][0]['platform_base_price'] ?? '' }}"
                                    data-package="{{ $pass['package'][0]['name'] ?? '' }}"
                                    data-name="{{ $pass['name'] ?? '' }}">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 font-weight-bold">{{ $pass['name'] }}</h6>
                                        <p class="mb-0 text-muted">{{ $pass['description'] }}</p>
                                    </div>
                                    <div class="ml-auto d-flex align-items-center">
                                        <svg width="35" height="35" viewBox="0 0 24 24" fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.9 17.2c-.6 0-1-.4-1-1 0-.3.1-.5.3-.7l3.5-3.5-3.5-3.5c-.4-.4-.4-1 0-1.4s1-.4 1.4 0l4.2 4.2c.4.4.4 1 0 1.4l-4.2 4.2c-.2.2-.5.3-.7.3z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="add_comments" tabindex="-1" aria-labelledby="addCommentsLabel" aria-hidden="true">
        <style>
            .star-rating {
                white-space: nowrap;
            }

            .star-rating [type="radio"] {
                appearance: none;
            }

            .star-rating i {
                font-size: 2.2em;
                transition: 0.3s;
            }

            .star-rating label:is(:hover, :has(~ :hover)) i {
                transform: scale(1.35);
                color: #fffdba;
                animation: jump 0.5s calc(0.3s + (var(--i) - 1) * 0.15s) alternate infinite;
            }

            .star-rating label:has(~ :checked) i {
                color: #faec1b;
                text-shadow: 0 0 2px #ffffff, 0 0 10px #ffee58;
            }

            @keyframes jump {

                0%,
                50% {
                    transform: translatey(0) scale(1.35);
                }

                100% {
                    transform: translatey(-15%) scale(1.35);
                }
            }
        </style>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCommentsLabel">{{ $templeList['name'] }}</h5>
                    <button type="button" class="btn btn-close" data-dismiss="modal" aria-label="Close"><i
                            class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <form method="post" action="{{ route('temple-add-comment') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-12 text-center h3">
                                <span class="star-rating">
                                    <label for="rate-1" style="--i:1"><i class="fa fa-solid fa-star"></i></label>
                                    <input type="radio" name="rating" id="rate-1" value="1">
                                    <label for="rate-2" style="--i:2"><i class="fa fa-solid fa-star"></i></label>
                                    <input type="radio" name="rating" id="rate-2" value="2" checked>
                                    <label for="rate-3" style="--i:3"><i class="fa fa-solid fa-star"></i></label>
                                    <input type="radio" name="rating" id="rate-3" value="3">
                                    <label for="rate-4" style="--i:4"><i class="fa fa-solid fa-star"></i></label>
                                    <input type="radio" name="rating" id="rate-4" value="4">
                                    <label for="rate-5" style="--i:5"><i class="fa fa-solid fa-star"></i></label>
                                    <input type="radio" name="rating" id="rate-5" value="5">
                                </span>
                            </div>
                            <input type="hidden" name="temple_id" value="{{ $templeList['id'] }}">
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Add Comment</label>
                            <textarea class="form-control" name="comment" rows="4" placeholder="{{ translate('Share your thoughts') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Submit Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($matchingTrust && $matchingTrust['id'])
        <div class="booking-right-side-show d-md-block d-none">
            <a class="booking-now" onclick="$('#vip-darshan-wrapper').show(); $('#vip-darshan-box').slideDown('slow');">
                <i class="fa-solid fa-gopuram"></i> <span>{{ translate('Book VIP Darshan') }}</span>

            </a>
            <a href="{{ route('all-donate_trust', ['slug' => $matchingTrust['slug']]) }}" class="booking-now">
                <i class="fa-solid fa-circle-dollar-to-slot"></i> <span>{{ translate('donate to Temple') }}</span>

            </a>
        </div>

        <div class="my-2 d-block d-md-none fixed-bottom text-center py-2">
            <a class="booking-now font-weight-bolder text-white mx-2"
                onclick="$('#vip-darshan-wrapper').show(); $('#vip-darshan-box').slideDown('slow');">
                {{ translate('Book VIP Darshan') }}
            </a>
            <a class="booking-now font-weight-bolder text-white mx-2"
                href="{{ route('all-donate_trust', ['slug' => $matchingTrust['slug']]) }}">
                {{ translate('Donate to Temple') }}
            </a>
        </div>
    @endif

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
    <!-- Firbase CDN -->
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>
    <script>
        $('.nearby-slider-1').owlCarousel({
            loop: false,
            autoplay: true,
            margin: 20,
            nav: true,
            navText: directionFromSession === 'rtl' ? ["<i class='czi-arrow-right'></i>",
                "<i class='czi-arrow-left'></i>"
            ] : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
            dots: false,
            autoplayHoverPause: true,
            rtl: directionFromSession === 'rtl',
            ltr: directionFromSession === 'ltr',
            responsive: {
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                375: {
                    items: 1
                },
                540: {
                    items: 2
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                },
                1200: {
                    items: 6
                },
            },
        });
    </script>
    <script>
        $('.participate-btn').click(function(e) {
            e.preventDefault();
            $('#participateModal').modal('show');
            $(".package_id_model").val($(this).data('package_id'));
            $(".venue_id_model").val($(this).data('venue_id'));
            $("#lead-store-form").attr('action', $(this).data('link'));
        });

        $('#pujaPackageButton').on('click', function(event) {
            event.preventDefault();
            PackageOpens()
        });

        function PackageOpens() {
            if (!$('.packagesTabLink').hasClass('active')) {
                $('.nav-link').removeClass('active');
                $('.tab-pane.fade').removeClass('active show');
                $('.tab-pane.fade').removeClass('show');
                $('.packagesTabLink').addClass('active');
                $('.packagesTabLink').tab('show');
                $('.tab-pane.fade#packages').addClass('active');
                $('.tab-pane.fade#packages').addClass('show');
            }
            $('html, body').animate({
                scrollTop: $('.packagesTabLink').offset().top
            }, 50);
        }
    </script>
    <script>
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_APIKEY') }}",
            authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
            projectId: "{{ env('FIREBASE_PRODJECTID') }}",
            storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
            appId: "{{ env('FIREBASE_APPID') }}",
            measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
        };
        firebase.initializeApp(firebaseConfig);
    </script>
    <script>
        $('.participate-btn').click(function(e) {
            e.preventDefault();
            $('#participateModal').modal('show');
            $(".package_id_model").val($(this).data('package_id'));
            $(".venue_id_model").val($(this).data('venue_id'));
            $("#lead-store-form").attr('action', $(this).data('link'));
        });

        var confirmationResult;
        var appVerifier = "";
        var sendOtpCount = 1;
        $('#send-otp-btn').click(function(e) {
            e.preventDefault();
            var name = $('#person-name').val();
            var number = $('#person-number').val();

            var phoneNumber = $('.country-picker-phone-number')
                .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
            sendotp();
        });


        function sendotp() {
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            if (number == "" || number.length != 10) {
                $('#number-validation').show();
            } else if (name == "") {
                $('#number-validation').hide();
                $('#name-validation').show();
            } else {
                toastr.success('please wait...');
                $('#send-otp-btn').text('Please Wait ...');
                $('#send-otp-btn').prop('disabled', true);
                var phoneNumber = $('.country-picker-phone-number')
                    .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
                if (appVerifier == "") {
                    appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                        size: 'invisible'
                    });
                }

                firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                        $('#name-validation').hide();
                        $('#number-validation').hide();
                        $('#send-otp-btn-div').css('display', 'none');
                        $('#phone-div').css('display', 'none');
                        $('#name-div').css('display', 'none');
                        $('#otp-input-div').css('display', 'block');
                        $('#verify-otp-btn-div').css('display', 'block');
                        if (sendOtpCount == 1) {
                            sendOtpCount = 2;
                            otpTimer();
                        }
                        confirmationResult = confirmation;
                        toastr.success('otp sent successfully');
                        $('#resend-div').show();
                    })
                    .catch(function(error) {
                        toastr.error('Failed to send OTP. Please try again');
                        $('#send-otp-btn').text('Send OTP');
                        $('#send-otp-btn').prop('disabled', false);
                        console.error('OTP sending error:', error);
                    });
            }
        }

        // otp timer
        function otpTimer() {
            $('#resend-otp-timer-text').css('display', 'block');
            $('#resend-otp-btn-text').css('display', 'none');
            var resendOtpTimer = 30;
            var interval = setInterval(() => {
                resendOtpTimer--;
                $('#resend-otp-timer').text(resendOtpTimer);
                if (resendOtpTimer <= 0) {
                    $('#resend-otp-timer-text').css('display', 'none');
                    $('#resend-otp-btn-text').css('display', 'block');
                    clearInterval(interval);
                }
            }, 1000);
        }

        // resend otp
        $('#resend-otp-btn').click(function(e) {
            e.preventDefault();

            var phoneNumber = $('.country-picker-phone-number')
                .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
            if (!appVerifier) {
                appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    size: 'invisible'
                });
            }
            firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                    confirmationResult = confirmation;
                    otpTimer();
                    toastr.success('OTP resent successfully');
                })
                .catch(function(error) {
                    toastr.error('Failed to send OTP. Please try again');
                });
        });

        $('#verify-otp-btn').click(function(e) {
            e.preventDefault();
            toastr.success('please wait...');
            var name = $('#person-name').val();
            var number = $('.country-picker-phone-number').val(); //$('#person-number').val();
            var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
                $('#otp6').val();
            if (confirmationResult) {
                confirmationResult.confirm(otp).then(function(result) {
                        $('#participateModal').modal('hide');
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        // $(".package_id_model").val($(".package_id").val());
                        var check = $(".interested_model").val();
                        $('#lead-store-form').submit();
                    })
                    .catch(function(error) {
                        $('#otpValidation').text('Incorrect OTP');
                        $('.otp-input-fields input').val('');
                        $('.otp-input-fields input:first').focus();
                        // $('#submit').text('Submit');
                        // $('#submit').prop('disabled', false);
                    });
            }


        });

        $('#otp-back-btn').click(function(e) {
            e.preventDefault();

            $('#send-otp-btn-div').css('display', 'block');
            $('#phone-div').css('display', 'block');
            $('#name-div').css('display', 'block');
            $('#otp-input-div').css('display', 'none');
            $('#verify-otp-btn-div').css('display', 'none');
            $('#send-otp-btn').prop('disabled', false);
            $('#send-otp-btn').text('Send OTP');
            $('#resend-div').hide();
        });
    </script>
    <!-- OTP SECTION -->
    <script type="text/javascript">
        var otp_inputs = document.querySelectorAll(".otp__digit")
        var mykey = "0123456789".split("")
        otp_inputs.forEach((_) => {
            _.addEventListener("keyup", handle_next_input)
        })

        function handle_next_input(event) {
            let current = event.target
            let index = parseInt(current.classList[1].split("__")[2])
            current.value = event.key

            if (event.keyCode == 8 && index > 1) {
                current.previousElementSibling.focus()
            }
            if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
                var next = current.nextElementSibling;
                next.focus()
            }
            var _finalKey = ""
            for (let {
                    value
                }
                of otp_inputs) {
                _finalKey += value
            }
        }
    </script>
    {{-- mobile no blur --}}
    <script>
        $('#person-number').blur(function(e) {
            e.preventDefault();
            var code1 = $('.country-picker-phone-number').val();
            var mobile = $(this).val();
            if (mobile.length <= 9) {
                toastr.error("Invalid phone number.");
                $('.one-time-otp-sends').prop('disabled', true);
                return;
            } else {
                $('.one-time-otp-sends').prop('disabled', false);
            }
            $.ajax({
                type: "get",
                url: "{{ url('account-counselling-order-user-name') }}" + "/" + code1,
                success: function(response) {
                    if (response.status == 200) {
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);
                        $('#person-name').prop('readonly', true);
                        $('#phone-number-valid').val(1);
                        $('.one-time-otp-sends').attr('id', 'send-otp-btn');
                        $('.one-time-otp-sends').text("{{ translate('send_OTP') }}");
                    } else {
                        $('#person-name').val('');
                        $('#person-name').prop('readonly', false);
                        $('#phone-number-valid').val(0);
                        $('.one-time-otp-sends').attr('id', 'invalid-user-login-booking');
                        $('.one-time-otp-sends').text("{{ translate('book_now') }}");
                    }
                }
            });
        });

        $(document).on('click', '#invalid-user-login-booking', function(e) {
            e.preventDefault();
            $('#vip-darshan-box').slideUp(200, function() {
                $('#vip-darshan-wrapper').hide();
            });
            $('#participateModal').modal('hide');
            $(this).text('Please Wait ...');
            $(this).prop('disabled', true);
            $('#lead-store-form').submit();
        });
    </script>
    {{-- auth book now btn click --}}
    <script>
        $('.auth-book-now').click(function(e) {
            e.preventDefault();
            $("#lead-store-form").attr('action', $(this).data('link'));
            $(".package_id_model").val($(this).data('package_id'));
            $(".venue_id_model").val($(this).data('venue_id'));
            $('#lead-store-form').submit();
        });
    </script>
    <script type="text/javascript"></script>
    <script>
        $(document).on('click', '#vip-darshan-wrapper', function(e) {
            if (!$(e.target).closest('#vip-darshan-box').length) {
                $('#vip-darshan-box').slideUp(200, function() {
                    $('#vip-darshan-wrapper').hide();
                });
            }
        });

        function showVipDarshanBox(that) {
            $('.model-id').val($(that).data('id'));
            $('.model-name').val($(that).data('name'));
            $('.model-price').val($(that).data('price'));
            $('.model-receipt-price').val($(that).data('receiptprice'));
            $('.model-platform-fee').val($(that).data('platformfee'));
            $('.model-platform-gst').val($(that).data('platformgst'));
            $('.model-platform-base').val($(that).data('platformbaseprice'));
            $('.model-package-name').val($(that).data('package'));
            if ("{{ auth('customer')->check() }}") {
                $('#vip-darshan-box').slideUp(200, function() {
                    $('#vip-darshan-wrapper').hide();
                });
                $('#lead-store-form').submit();
            } else {
                $('#participateModal').modal('show');
            }
        }
    </script>
    <script>
        function read(el) {
            var parentDiv = $(el).closest('.single-product-details');
            var commentDiv = parentDiv.find('.review-comment');
            if (parentDiv.css('height') === '100px') {
                parentDiv.css('height', 'auto'); // Expand
                commentDiv.css('-webkit-line-clamp', '10');
                $(el).text("{{ translate('Read less') }}");
            } else {
                parentDiv.css('height', '100px'); // Collapse
                commentDiv.css('-webkit-line-clamp', '1');
                $(el).text("{{ translate('Read more') }}");
            }
        }

        $(document).ready(function() {
            $('.section-link').on('click', function(e) {
                e.preventDefault();

                const targetId = $(this).attr('href');
                $('html, body').animate({
                    scrollTop: $(targetId).offset().top - $('.navbar_section1').outerHeight() - 100
                }, 200);

            });

            $(window).on('scroll', function() {
                const scrollTop = $(window).scrollTop() + $('.navbar_section1').outerHeight() + 210;
                if (scrollTop > 900) {
                    $('.navbar-stuck-toggler').removeClass('show');
                    $('.navbar-stuck-menu').removeClass('show');
                    $(".navbar_section1").css({
                        'position': 'sticky',
                        'top': '83px',
                        'right': '3px',
                        'left': '3px',
                        'background-color': '#fff',
                        'z-index': '1000',
                        'box-shadow': '0 2px 10px rgba(0, 0, 0, 0.1)',
                        'overflow': 'auto',
                        'text-wrap': 'nowrap',
                    });
                    $('#breadcrum-container').removeClass('d-flex');
                    $('#breadcrum-container').addClass('d-none');
                } else {
                    $('#breadcrum-container').removeClass('d-none');
                    $('#breadcrum-container').addClass('d-flex');
                    $(".navbar_section1").css({
                        'position': 'static',
                        'box-shadow': 'none',
                        'text-wrap': 'nowrap',
                    });
                }
                $('.section-content').each(function() {
                    const sectionTop = $(this).offset().top;
                    const sectionBottom = sectionTop + $(this).outerHeight();
                    const sectionId = $(this).attr('id');
                    const navLink = $(`.section-link[href="#${sectionId}"]`);

                    if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                        $('.section-link').removeClass('active');
                        navLink.addClass('active');
                        console.log(sectionId);
                        if (sectionId == 'packages') {
                            $(".bottom-package-show").addClass("d-none");
                        } else {
                            $(".bottom-package-show").removeClass("d-none");
                        }
                    }
                });

            });
        });
        $(document).ready(function() {
            const $stickyElement = $('.button-sticky');
            const $offsetElement = $('.partial-pooja');

            $(window).on('scroll', function() {
                const elementOffset = $offsetElement.offset().top;
                const scrollTop = $(window).scrollTop();

                if (scrollTop >= elementOffset) {
                    $stickyElement.addClass('stick');
                } else {
                    $stickyElement.removeClass('stick');
                }
            });
        });
    </script>
    <script>
        function renderOwlCarouselSlider() {
            var sync1 = $(".product-thumbnail-slider");
            var thumbnailItemClass = ".owl-item";
            var slides = sync1.owlCarousel({
                startPosition: 12,
                items: 1,
                loop: true,
                autoplay: true,
                margin: 30, // Updated margin
                mouseDrag: true,
                touchDrag: true,
                pullDrag: false,
                stagePadding: 30, // Added stagePadding
                scrollPerPage: true,
                autoplayHoverPause: false,
                nav: false,
                dots: false,
                smartSpeed: 450, // Added smartSpeed for smooth transitions
                animateOut: "slideOutDown", // Added custom animation
                animateIn: "flipInX", // Added custom animation
                navText: [
                    '<i class="fa fa-chevron-left"></i>', // Left arrow
                    '<i class="fa fa-chevron-right"></i>' // Right arrow
                ],
                rtl: themeDirection && themeDirection.toString() === "rtl",
            }).on("changed.owl.carousel", syncPosition);
        }
        // renderOwlCarouselSlider();
    </script>
    <script>
        $('#mobilePackageSlider').owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            dots: true,
            items: 1
        });
    </script>
@endpush