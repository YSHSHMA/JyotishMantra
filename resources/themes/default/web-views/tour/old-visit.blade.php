@extends('layouts.front-end.app')
@section('title', translate('tour'))
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
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">


<style>
    .fullscreen-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        text-align: center;
        overflow: auto;
    }

    .fullscreen-modal img {
        max-width: 90%;
        max-height: 90vh;
        margin-top: 5%;
    }

    .close-modal {
        position: absolute;
        top: 20px;
        right: 40px;
        font-size: 40px;
        color: white;
        cursor: pointer;
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

    .read-more-shor-details,
    .read-less-shor-details {
        color: blue;
        cursor: pointer;
        /* text-decoration: underline; */
    }

    .more-text-shor-details {
        display: none;
    }

    .countdown {
        display: flex;
        justify-content: center;
        gap: 20px;
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

    section {
        width: 100%;
        height: 300px;
    }

    .swiper-container {
        width: 100%;
        height: 300px;
    }

    .slide {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        text-align: center;
        font-size: 18px;
        background: #fff;
        overflow: hidden;
        /*  */
        height: 300px;
    }

    .slide-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-position: center;
        background-size: cover;
        object-fit: cover;
    }

    .slide-title {
        font-size: 43px;
        line-height: 1;
        max-width: 50%;
        white-space: normal;
        word-break: break-word;
        color: #FFF;
        z-index: 100;
        font-family: 'Oswald', sans-serif;
        text-transform: uppercase;
        font-weight: normal;
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
    }

    @media (min-width: 45em) {
        .slide-title {
            font-size: 43px;
            max-width: none;
        }
    }

    .slide-title span {
        white-space: pre;
        display: inline-block;
        opacity: 0;
    }

    .slideshow {
        position: relative;
    }

    .slideshow-pagination {
        position: absolute;
        bottom: 5rem;
        left: 0;
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        transition: .3s opacity;
        z-index: 10;
    }

    .slideshow-pagination-item {
        display: flex;
        align-items: center;
    }

    .slideshow-pagination-item .pagination-number {
        opacity: 0.5;
    }

    .slideshow-pagination-item:hover,
    .slideshow-pagination-item:focus {
        cursor: pointer;
    }

    .slideshow-pagination-item:last-of-type .pagination-separator {
        width: 0;
    }

    .slideshow-pagination-item.active .pagination-number {
        opacity: 1;
    }

    .slideshow-pagination-item.active .pagination-separator {
        width: 10vw;
    }

    .slideshow-navigation-button {
        position: absolute;
        top: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 5rem;
        z-index: 1000;
        transition: all .3s ease;
        color: #FFF;
    }

    .slideshow-navigation-button:hover,
    .slideshow-navigation-button:focus {
        cursor: pointer;
        background: rgba(0, 0, 0, 0.5);
    }

    .slideshow-navigation-button.prev {
        left: 0;
    }

    .slideshow-navigation-button.next {
        right: 0;
    }

    .pagination-number {
        font-size: 1.8rem;
        color: #FFF;
        font-family: 'Oswald', sans-serif;
        padding: 0 0.5rem;
    }

    .pagination-separator {
        display: none;
        position: relative;
        width: 40px;
        height: 2px;
        background: rgba(255, 255, 255, 0.25);
        transition: all .3s ease;
    }

    @media (min-width: 45em) {
        .pagination-separator {
            display: block;
        }
    }

    .pagination-separator-loader {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #FFFFFF;
        transform-origin: 0 0;
    }

    .image-container {
        position: relative;
        overflow: hidden;
    }

    .gallery-img {
        transition: transform 0.5s ease, filter 0.5s ease;
        width: 100%;
        display: block;
    }

    .image-container:hover .gallery-img {
        transform: scale(1.2);
    }

    .parent-element {
        overflow: visible;
        /* Ensure parent does not have overflow hidden */
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

    .navbar_section1 {
        text-wrap: nowrap;
        white-space: nowrap;
        /* position: sticky;
    top: 84px; 
    z-index: 1000;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); */
    }

    /* Solid orange background with white text and green image for 'Full' */




    /* Shared styles for both buttons */


    /* Optional: Better alignment on smaller screens */
    @media (max-width: 425px) {

        .part_full_pay1 img,
        .part_full_pay2 img {
            display: none;
        }
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.1/css/swiper.min.css">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> -->
<!-- <link href="https://fonts.googleapis.com/css?family=Oswald:500" rel="stylesheet"> -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initAutocomplete"></script>

<style>
    .inclu::before {
        content: '';
        position: absolute;
        left: 0;
        top: 12px;
        background: #63C266;
        height: 30px;
        width: 5px;
    }

    .exclu::before {
        content: '';
        position: absolute;
        left: 0;
        top: 12px;
        background: #DA1515;
        height: 30px;
        width: 5px;
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

    .container .slider-92911 .testimony-29101 .image {
        background-size: cover;
        background-position: center center;
    }

    .slider-92911 {
        position: relative;
    }

    @media (max-width: 991.98px) {
        .testimony-29101 .image {
            height: 300px;
        }
    }

    .testimony-29101 .text {
        width: 60%;
        padding: 7rem 4rem;
        background: #007bff;
    }

    .testimony-29101 .text blockquote {
        position: relative;
        padding-bottom: 50px;
        font-size: 20px;
    }

    .testimony-29101 .text blockquote p {
        color: #fff;
        line-height: 1.8;
    }

    .testimony-29101 .text blockquote .author {
        font-size: 16px;
        letter-spacing: .1rem;
        position: absolute;
        bottom: 0;
        color: rgba(255, 255, 255, 0.7);
    }

    .slide-one-item {
        -webkit-box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.1);
        box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 991.98px) {
        .slide-one-item .owl-nav {
            display: none;
        }
    }

    .slide-one-item .owl-nav .owl-prev,
    .slide-one-item .owl-nav .owl-next {
        position: absolute;
        top: 50%;
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        color: #000;
    }

    .slide-one-item .owl-nav .owl-prev span,
    .slide-one-item .owl-nav .owl-next span {
        font-size: 30px;
    }

    .slide-one-item .owl-nav .owl-prev:hover,
    .slide-one-item .owl-nav .owl-next:hover {
        color: #000;
    }

    .slide-one-item .owl-nav .owl-prev:active,
    .slide-one-item .owl-nav .owl-prev:focus,
    .slide-one-item .owl-nav .owl-next:active,
    .slide-one-item .owl-nav .owl-next:focus {
        outline: none;
    }

    .slide-one-item .owl-nav .owl-prev {
        left: 20px;
    }

    .slide-one-item .owl-nav .owl-next {
        right: 20px;
    }

    .slide-one-item .owl-dots {
        position: absolute;
        bottom: 20px;
        width: 100%;
        text-align: center;
        z-index: 2;
    }

    .slide-one-item .owl-dots .owl-dot {
        display: inline-block;
    }

    .slide-one-item .owl-dots .owl-dot>span {
        -webkit-transition: 0.3s all cubic-bezier(0.32, 0.71, 0.53, 0.53);
        -o-transition: 0.3s all cubic-bezier(0.32, 0.71, 0.53, 0.53);
        transition: 0.3s all cubic-bezier(0.32, 0.71, 0.53, 0.53);
        display: inline-block;
        width: 15px;
        height: 3px;
        background: rgba(0, 123, 255, 0.4);
        margin: 3px;
    }

    .slide-one-item .owl-dots .owl-dot.active>span {
        width: 15px;
        background: #007bff;
    }

    .thumbnail {
        list-style: none;
        padding: 0;
        margin: 0;
        position: absolute;
        bottom: 0px;
        left: 50%;
        -webkit-transform: translateY(50%) translateX(-50%);
        -ms-transform: translateY(50%) translateX(-50%);
        transform: translateY(50%) translateX(-50%);
        z-index: 99;
    }

    .thumbnail li {
        display: inline-block;
        width: 37px;
    }

    .thumbnail li a {
        display: block;
        margin-left: 2px;
    }

    .thumbnail li a img {
        width: 50px;
        border-radius: 50%;
        -webkit-transform: scale(0.8);
        -ms-transform: scale(0.8);
        transform: scale(0.8);
        -webkit-transition: .3s all ease;
        -o-transition: .3s all ease;
        transition: .3s all ease;
        -webkit-box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.2);
    }

    .thumbnail li.active a img {
        -webkit-transform: scale(1.2);
        -ms-transform: scale(1.2);
        transform: scale(1.2);
        -webkit-box-shadow: 0 10px 20px 0 rgba(0, 0, 0, 0.2);
        box-shadow: 0 10px 20px 0 rgba(0, 0, 0, 0.2);
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
        height: 50px;
        width: 50px;
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

    .days_wise_itiner::before {
        content: '';
        position: absolute;
        background: #0A5B9B;
        height: 23px;
        width: 5px;
    }

    .bdrbtm {
        border-bottom: 1px solid #b8d0e5;
    }

    .grbg {
        background: linear-gradient(90deg, #ff9200 0%, #ff9200 100%);
        border-radius: 10px 10px 0 0;
    }

    .pda10 {
        padding: 10px;
    }


    .tlpricecut {
        font-size: 12px;
        /* color: #515151; */
        margin-top: 5px;
        line-height: 18px;
        display: flex;
        align-items: center;
        /* text-decoration: line-through; */
    }

    .tlprice {
        font-size: 28px;
        font-weight: 700;
        /* color: #000; */
        display: flex;
        align-items: center;
    }



    .stm {
        position: relative;
        margin: 0px 0;
        text-align: center;
        z-index: 1;
        width: 99%;
        float: left;
    }

    .stm .lay {
        position: relative;
        background: #fff;
        padding: 6px 8px;
        font-size: 11px;
        /* font-weight: 500; */
        top: -14px;
    }

    #map {
        height: 400px;
        width: 100%;
    }

    /* Search input styling */
    #search-box {
        width: 100%;
        display: block;
        height: calc(1.5em + 1.25rem + 2px);
        padding: 0.625rem 1rem;
        font-size: 0.9375rem;
        color: #4b566b;
        border: 1px solid #dae1e7;
    }

    /* Styling for the message when location is not authenticated */
    #message {
        margin-top: 10px;
        color: red;
        font-weight: bold;
    }

    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    .pac-container {
        z-index: 1050 !important;
    }

    .boxv1::before {
        content: '';
        position: absolute;
        left: 0;
        top: 12px;
        background: #0A5B9B;
        height: 30px;
        width: 5px;
    }

    .btn-outline--primary:hover {
        background-color: var(--web-primary);
        border-color: var(--web-primary);
        color: white;
    }

    .btn-outline--primary.active {
        background-color: var(--web-primary);
        border-color: var(--web-primary);
        color: white;
    }

    .btn-outline--primary {
        color: var(--web-primary);
        border-color: var(--web-primary);
    }

    @media only screen and (max-width: 600px) {
        a.section-link {
            padding: 5px 8px;
            font-size: 10px;
        }

        li.nav-item.navItems span {
            display: none;
        }

        .cab-tab-font-header {
            font-size: 11px;
        }

        .button-padding-five-tan {
            padding: 5px 10px;
        }

        .cab-button-plus-minus {
            padding: 7px 2px !important;
            color: white !important;
        }

        .align-self-center {
            font-size: 12px;
        }
    }

    /* Main container for the tab navigation */
    .nav--tabs {
        position: relative;
        display: flex;
        justify-content: space-between;
        /* Ensure even spacing */
        align-items: center;
        /* Align vertically */
        padding: 0 5%;
    }

    /* Horizontal line connecting steps */
    /* .nav--tabs::before {
                                            content: "";
                                            position: absolute;
                                            top: 50%;
                                            left: 17%;
                                            right: 0;
                                            height: 2px;
                                            background-color: #ccc;
                                            z-index: 0;
                                            width: 70%;
                                        } */
    .nav--tabs::before {
        background-color: #ff6600;
        top: 60%;
        width: 80%;
        left: 10%;
    }

    /* Individual tab items */
    .navItems {
        position: relative;
        z-index: 1;
        /* Ensure steps are above the line */
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1;
        /* Equal width for each item */
    }

    /* Circular tab links */
    .navlinks {
        border-radius: 50%;
        width: 9px;
        height: 9px;
        line-height: 40px;
        text-align: center;
        background-color: #fff;
        color: #666;
        font-weight: bold;
        border: 2px solid #ccc;
        transition: 0.3s;
        border: 1px solid #fe696a !important;
        background: white !important;
        padding: 0px 16px 23px 9px !important;
    }

    /* Active step */
    .navlinks.active {
        background-color: #ff9800;
        color: #fff;
        border-color: #ff9800;
    }

    /* Disabled step */
    .navlinks.disabled {
        pointer-events: none;
        opacity: 0.6;
    }

    .cab-button-plus-minus {
        padding: 7px 4px;
        background-color: #ff9200;
        color: white !important;
        margin-bottom: 12px;
    }
</style>
@endpush
@section('content')
<div class="container mt-3 rtl text-align-direction" id="cart-summary">
    <div class="row">
        <div class="col-md-12 ml-4 py-3">
            <span class="h4 font-weight-bold">{{ $getfirst['tour_name'] ?? '' }}
            </span>
            <span class="small">
                <i class="tio-star text-warning"></i>
                @php
                $number = round($ratings['total'], 1);
                @endphp
                @if ($number >= 1000000)
                {{ round($number / 1000000, 1) . 'M' . '+' }}
                @elseif ($number >= 1000)
                {{ round($number / 1000, 1) . 'K' . '+' }}
                @else
                {{ $number }}
                @endif
                @php
                $total_user_rating = 0;
                if (!empty($ratings['list']) && count($ratings['list']) > 0) {
                if (count($ratings['list']) >= 1000000) {
                $total_user_rating = round(count($ratings['list']) / 1000000, 1) . 'M' . '+';
                } elseif (count($ratings['list']) >= 1000) {
                $total_user_rating = round(count($ratings['list']) / 1000, 1) . 'K' . '+';
                } else {
                $total_user_rating = count($ratings['list']);
                }
                }
                @endphp
                ({{ $total_user_rating }} {{ translate('Reviews') }})
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="container mt-2">
                @if (!empty($getfirst['image']) && json_decode($getfirst['image'], true))
                <div class="slider-92911">
                    <div class="owl-carousel slide-one-item">
                        @foreach (json_decode($getfirst['image'], true) as $val)
                        <div class="testimony-29101 align-items-stretch">
                            <div class="image"
                                style="height: 300px;border-radius: 12px;background-image: url('{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($val ?? ''), type: 'backend-product') }}');">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="my-5 text-center">
                        <ul class="thumbnail">
                            @foreach (json_decode($getfirst['image'], true) as $val)
                            <li class="{{ $loop->index == 0 ? 'active' : '' }}"><a><img
                                        src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($val ?? ''), type: 'backend-product') }}"
                                        alt="Image" class="img-fluid"
                                        style="width: 33px !important; height: 33px !important;"></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
            <!-- start sticky -->
            <div class="card">
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-md-12 p-0">
                            <div class="navbar_section1 section-links d-flex justify-content-between mt-3 border-top border-bottom py-2 mb-4 small" style="    overflow: auto;">
                                <a class="section-link ml-2 {{ ((request()->comment == 'success' || request()->comment == 'error')?'':'active')}}" href="#about_details">{{ translate('about')}}</a>

                                <a class="section-link" href="#highlights">{{ translate('highlights') }}</a>

                                <a class="section-link" href="#inclusion_exclusion">{{ translate('inclusion') }}/{{ translate('exclusion') }}</a>

                                <a class="section-link" href="#Itinerary">{{ translate('Itinerary') }}</a>

                                <a class="section-link" href="#terms_and_conditions"> {{ translate('terms_and_conditions') }}</a>
                                <a class="section-link" href="#cancellation_policy"> {{ translate('cancellation_policy') }}</a>
                                <a class="section-link" href="#notes">{{ translate('notes') }}</a>
                                <a class="section-link mr-2 {{ ((request()->comment == 'success' || request()->comment == 'error')?'active':'')}}" href="#review_user">{{ translate('Reviews') }}</a>
                                <a class="section-link" href="#tourfaq">{{ translate('faqs') }}</a>
                            </div>
                            <div class="content-sections px-lg-3">
                                <!-- Inclusion Section -->
                                <div class="section-content {{ ((request()->comment == 'success' || request()->comment == 'error')?'':'active')}}" id="about_details">
                                    <div class="row p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                        <div class="col-12">
                                            {!! $getfirst['description'] !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content" id="highlights">
                                    <div class="row p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                        <div class="col-12">
                                            {!! $getfirst['highlights'] !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content" id="inclusion_exclusion">
                                    <div class="row mt-4">
                                        <div class="col-md-6 my-2">
                                            <div class="card">
                                                <div class="card-body" style="background: #4fc33c29;">
                                                    <div class="row mb-2 inclu" style="max-height: 180px;overflow: auto;">
                                                        <span class="font-weight-bold">{{ translate('inclusion') }}</span>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12" style=" height: 218px; overflow: auto;">
                                                            {!! $getfirst['inclusion'] !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6  my-2">
                                            <div class="card">
                                                <div class="card-body" style="background: #f5040414;">
                                                    <div class="row mb-2 exclu" style="max-height: 180px;overflow: auto;">
                                                        <span class="font-weight-bold">{{ translate('exclusion') }}</span>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12" style=" height: 218px; overflow: auto;">
                                                            {!! $getfirst['exclusion'] !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content" id="Itinerary">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5 class="days_wise_itiner">&nbsp;&nbsp;{{ translate('Day Wise Itinerary') }}</h5>
                                        </div>
                                        @if (!empty($getfirst['TourPlane']) && count($getfirst['TourPlane']) > 0)
                                        @foreach ($getfirst['TourPlane'] as $va)
                                        <div class="col-md-12 mt-2">
                                            <div class="card">
                                                <div class="card-body row">
                                                    <div class="col-md-2 small font-weight-bold">{{ translate('days') }} {{ $loop->iteration }} &nbsp;&nbsp;<i class="tio-calendar_note" style="    font-size: 19px;">calendar_note</i>
                                                    </div>
                                                    <div class="col-md-10 p-0">
                                                        <div style="border: 1px solid #b8d0e5;border-radius: 4px;" class="small">
                                                            <div class="font-weight-bold" style="background: linear-gradient(90deg, #c7dffe 0%, #d8f2ff 100%); padding: 6px 10px;">
                                                                {{ $va['name'] }}, {{ $va['time'] }}
                                                            </div>
                                                            <div class="px-2">
                                                                {!! $va['description'] !!}
                                                                <br>
                                                                @if (!empty($va['images']) && json_decode($va['images'], true))
                                                                @php
                                                                $images = json_decode($va['images'], true);
                                                                @endphp
                                                                <div class="image-wrapper" style="position: relative;">
                                                                    <a class="image-count-overlay"
                                                                        style="position: absolute; font-size: 29px; background-color: rgba(0, 0, 0, 0.6); color: white;     padding:54px 65px; border-radius: 5px;"
                                                                        data-toggle="modal"
                                                                        data-target="#imageModal-{{ $loop->index }}">
                                                                        {{ count($images) }}+
                                                                    </a>
                                                                    <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit_place/' . ($images[0] ?? ''), type: 'backend-product') }}"
                                                                        alt="Image" class="img-fluid" data-toggle="modal"
                                                                        data-target="#imageModal-{{ $loop->index }}"
                                                                        style="border-radius: 12px;width: 163px;height: 151px;">
                                                                </div>
                                                                @endif

                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--  -->
                                        @if (!empty($va['images']) && json_decode($va['images'], true))
                                        <div class="modal fade" id="imageModal-{{ $loop->index }}"
                                            tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content"
                                                    style="    background-color: #3d3d3ed1;    border: 0px;">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-white">All Images</h5>
                                                        <button type="button" class="close text-white"
                                                            data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            @foreach (json_decode($va['images'], true) as $kindex=>$image)
                                                            <div class="col-md-4 mb-3">
                                                                <div class="image-container img-container"
                                                                    style="position: relative; overflow: hidden; width: 100%; height: 200px;">
                                                                    <img id="zoomable-img{{ $va['id']}}_{{$kindex}}"
                                                                        src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit_place/' . $image, type: 'backend-product') }}"
                                                                        alt="Image" class="img-fluid"
                                                                        style="border-radius: 12px; width: 100%; height: 100%; object-fit: cover; cursor: zoom-in;">
                                                                </div>
                                                            </div>

                                                            <div id="fullscreen-modal{{ $va['id']}}_{{$kindex}}" onclick="fullviewScreen(`{{ $va['id']}}_{{$kindex}}`)" class="fullscreen-modal" style="z-index: 2000;">
                                                                <span class="close-modal">&times;</span>
                                                                <img id="fullscreen-img" class="fullscreen-img">
                                                            </div>
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        @endif
                                        <!--  -->
                                        @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="section-content" id="terms_and_conditions">
                                    <div class="row p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                        <div class="col-12">
                                            {!! $getfirst['terms_and_conditions'] !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content" id="cancellation_policy">
                                    <div class="row p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                        <div class="col-12">
                                            {!! $getfirst['cancellation_policy'] !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content" id="notes">
                                    <div class="row p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                        <div class="col-12">
                                            {!! $getfirst['notes'] !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content {{ ((request()->comment == 'success' || request()->comment == 'error')?'active':'')}}" id="review_user">
                                    <div class="row  p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                        <div class="col-md-12">
                                            <h4>{{ translate('reviews') }}</h4>
                                        </div>
                                        <div class="col-lg-4 px-max-md-0">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="suggestion-card">
                                                        <div class="text-capitalize">
                                                            <p class="text-capitalize mb-0">
                                                                <a class='h3'>
                                                                    {{ round($ratings['total'], 1) }}&nbsp;
                                                                </a>
                                                                <big>
                                                                    @for ($inc = 1; $inc <= 5; $inc++)
                                                                        @if ($inc <=(int) $ratings['total'])
                                                                        <i class="tio-star text-warning"></i>
                                                                        @elseif ($ratings['total'] != 0 && $inc <= (int) $ratings['total'] + 1.1 && $ratings['total']> ((int) $ratings['total']))
                                                                            <i class="tio-star-half text-warning"></i>
                                                                            @else
                                                                            <i class="tio-star-outlined text-warning"></i>
                                                                            @endif
                                                                            @endfor
                                                                </big>
                                                            </p>
                                                            <a class='small'>
                                                                &nbsp;{{ !empty($ratings['list']) && count($ratings['list']) > 0 ? count($ratings['list']) : 0 }}
                                                                {{ translate('Reviews') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 d-md-block px-max-md-0">
                                            @if (!empty($ratings['list']) && count($ratings['list']) > 0)
                                            <div class="owl-theme owl-carousel review-slider">
                                                @foreach ($ratings['list'] as $counselling)
                                                <div class="card product-single-hover shadow-none rtl">
                                                    <div class="card-body position-relative">
                                                        <div class=" d-flex align-items-center">
                                                            <!-- User Icon -->
                                                            <img src="{{ getValidImage(path: 'storage/app/public/profile/' . ($counselling['userData']['image'] ?? ''), type: 'product') }}"
                                                                alt="User Icon" class="user-icon"
                                                                style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                                            <!-- User Name -->
                                                            <div>
                                                                <p class="fw-bold m-0">
                                                                    {{ $counselling['userData']['name'] ?? 'user name' }}
                                                                </p>
                                                                <p class="m-0">
                                                                    <big class="small">
                                                                        @for ($inc = 1; $inc <= 5; $inc++)
                                                                            @if ($inc <=(int) $counselling['star'])
                                                                            <i class="tio-star text-warning"></i>
                                                                            @elseif (
                                                                            $counselling['star'] != 0 &&
                                                                            $inc <= (int) $counselling['star'] + 1.1 &&
                                                                                $counselling['star']> ((int) $counselling['star']))
                                                                                <i
                                                                                    class="tio-star-half text-warning"></i>
                                                                                @else
                                                                                <i
                                                                                    class="tio-star-outlined text-warning"></i>
                                                                                @endif
                                                                                @endfor
                                                                    </big>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="single-product-details min-height-unset"
                                                            style="height: 100px; overflow: hidden;">
                                                            <div>
                                                                <a class="text-capitalize fw-semibold review-comment">
                                                                    {{ $counselling['comment'] ?? '' }}
                                                                    @php $filePath = 'storage/event/comment/' . ($counselling['image']??''); @endphp
                                                                    @if (!empty($counselling['image']) && file_exists($filePath))
                                                                    <img alt="{{ translate('product') }}"
                                                                        src="{{ getValidImage(path: 'storage/app/public/event/comment/' . $counselling['image'], type: 'product') }}"
                                                                        class='border border-light'
                                                                        style="width:50px">
                                                                    @endif
                                                                </a>
                                                            </div>
                                                            <a onclick="read(this)"
                                                                class="read-more-btn">{{ translate('Read more') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @else
                                            <div class="text-center text-capitalize">
                                                <img class="mw-100"
                                                    src="{{ asset('public/assets/front-end/img/icons/empty-review.svg') }}"
                                                    alt="">
                                                <p class="text-capitalize">
                                                    <small>No review given yet!</small>
                                                </p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content" id="tourfaq">
                                    <div class="row p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                        <div class="col-md-12">
                                            <h4>{{ translate('faqs') }}</h4>
                                        </div>
                                        <div class="col-12">
                                            @if($faqs)
                                            @foreach($faqs as $faq)
                                            <div class="row pt-2 specification">
                                                <div class="col-12 col-md-12 col-lg-12">
                                                    <div class="accordion" id="accordionExample">
                                                        <div class="cards">
                                                            <div class="card-header" id="heading{{$faq->id}}">
                                                                <h2 class="mb-0">
                                                                    <button class="btn btn-link btn-block  text-left btnClr" type="button" data-toggle="collapse" data-target="#collapse{{$faq->id}}" aria-expanded="true" aria-controls="collapseOne" style="white-space: normal;">
                                                                        {{$faq->question}}
                                                                    </button>
                                                                </h2>
                                                            </div>
                                                            <div id="collapse{{$faq->id}}" class="collapse" aria-labelledby="heading{{$faq->id}}" data-parent="#accordionExample">
                                                                <div class="card-body">
                                                                    {!! $faq->detail !!}
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            @endforeach
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
        <div class="col-md-4">
            <div class="paystickyset my-2">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="pricecolm">
                            <div class="pda10 grbg bdrbtm">
                                @php
                                $is_person_check = $getfirst['is_person_use']??0;
                                $cab_ids = '';
                                $cab_index = '';
                                $cab_name = '';
                                $cab_price = 0;
                                $cab_seats = '';
                                $cab_image = '';
                                if (!empty($getfirst['cab_list_price']) && json_decode($getfirst['cab_list_price'], true) || $is_person_check == 1) {
                                $cabList = json_decode($getfirst['cab_list_price'], true);
                                usort($cabList, function ($a, $b) {
                                return $a['price'] <=> $b['price'];
                                    });
                                    $minPriceCab = $cabList[0];
                                    if($is_person_check == 0){
                                    $getCabs = \App\Models\TourCab::where('id', $minPriceCab['cab_id'])->first();
                                    $cab_name = ucwords($getCabs['name'] ?? '');
                                    $cab_ids = ($minPriceCab['cab_id'] ?? '');
                                    }
                                    $cab_index = ($minPriceCab['id'] ?? '');
                                    $cab_price = $minPriceCab['price'];
                                    $cab_seats = $getCabs['seats'] ?? '';
                                    $cab_image = $getCabs['image'] ?? '';

                                    }
                                    @endphp

                                    @php
                                    $s_price = [];
                                    $s_seats = [];
                                    $s_seats_text_show = [];
                                    $s_image = [];
                                    $s_name = [];
                                    $s_packageid = [];
                                    $packages_price =0;
                                    $s_description =[];
                                    @endphp

                                    @if ($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)

                                    @if (!empty($getfirst['cab_list_price']) && is_array(json_decode($getfirst['cab_list_price'], true)))
                                    @foreach (json_decode($getfirst['cab_list_price'], true) as $cabplis)
                                    @php
                                    if($is_person_check == 0){
                                    $cabId = $cabplis['cab_id'];
                                    $getCabs = \App\Models\TourCab::find($cabId);
                                    if (!isset($s_packageid[$cabId])) {
                                    $s_price[$cabId] = $cabplis['price'];
                                    $s_seats[$cabId] = $getCabs->seats;
                                    $s_seats_text_show[$cabId] = 1; //$getCabs->seats;
                                    $s_image[$cabId] = $getCabs->image ?? '';
                                    $s_name[$cabId] = ucwords($getCabs->name ?? '');
                                    $s_description[$cabId] = $getCabs->description??'';
                                    $s_packageid[$cabId] = $cabId;
                                    } else {
                                    $s_seats[$cabId] += $getCabs->seats;
                                    $s_seats_text_show[$cabId] += 1; //($s_seats_text_show[$cabId].' + '.$getCabs->seats);
                                    }
                                    }
                                    @endphp
                                    @endforeach
                                    @endif

                                    @if (!empty($getfirst['package_list_price']) && is_array(json_decode($getfirst['package_list_price'], true)))
                                    @foreach (json_decode($getfirst['package_list_price'], true) as $plis)
                                    @php
                                    $packages_price += $plis['pprice'];
                                    @endphp
                                    @endforeach
                                    @endif

                                    @php
                                    $cab_price += $packages_price;
                                    @endphp
                                    @endif



                                    <div class="row mflex text-white">

                                        <div class="col-6">
                                            <span class="small">{{ translate('minimum_price')}}</span>
                                            @if(($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($is_person_check == 0))
                                            <span class="tlprice"> <span class="header_price_change">{{ webCurrencyConverter(amount: (double)((reset($s_price)??0) + $packages_price??0)) }}</span>
                                                @else
                                                <span class="tlprice"> <span>{{ webCurrencyConverter(amount: (double)$cab_price??0) }}</span>
                                                    @endif
                                                    @if(($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($is_person_check == 0))
                                                    <span style="font-size: 11px; line-height: 11px;margin-left: 2px;">per person</span>
                                                    @elseif($is_person_check == 1)
                                                    <span style="font-size: 11px; line-height: 11px;margin-left: 2px;">Per Head</span>
                                                    @endif
                                                </span>
                                                <span class="tlpricecut font-weight-bold">
                                                    @if($is_person_check == 0)
                                                    @if($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                                    <span class="fin-pri-n header_show_seats">{{ (reset($s_name)) }} :{{ (reset($s_seats)) }} seat</span>
                                                    @else
                                                    <span class="fin-pri-n">{{$cab_name}} {{$cab_seats}} seat</span>
                                                    @endif
                                                    @endif
                                                </span>

                                                <div class="clr"></div>
                                        </div>
                                    </div>
                            </div>

                            <div class="stm">
                                <hr style="position: relative; margin-top: 18px !important;">
                                @if($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                <span class="lay">Package Included</span>
                                @elseif($getfirst['is_person_use'] == 1)
                                <span class="lay">Package Included</span>
                                @else
                                <span class="lay">Add Package</span>
                                @endif
                            </div>
                            <div class="row px-3">
                                <div class="col-12 text-center" style="display: ruby;">
                                    @if(($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($getfirst['is_person_use'] == 0))
                                    <div class="px-2">
                                        <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($cab_image ?? ''), type: 'backend-product') }}" style="width: 59px; height: 47px; margin-bottom: 4px;">
                                        <div class="ico-nem"><span class="small font-weight-bold">{{ $cab_name }}</span></div>
                                    </div>
                                    @if(!empty($getfirst['package_list_price']) && json_decode($getfirst['package_list_price'], true))
                                    @foreach(json_decode($getfirst['package_list_price'], true) as $keyk => $plis)
                                    @php
                                    $tourPackages = \App\Models\TourPackage::where('id', $plis['package_id'])->first();
                                    @endphp
                                    <div class="px-2">
                                        <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product') }}" style="width: 59px; height: 47px; margin-bottom: 4px;">
                                        <div class="ico-nem"><span class="small font-weight-bold">{{ $tourPackages['name'] }}</span></div>
                                    </div>
                                    @endforeach
                                    @endif
                                    @elseif($getfirst['is_person_use'] == 1)
                                    <?php $includePackages = json_decode($getfirst['is_included_package'], true); ?>
                                    @if($includePackages['sightseen'] == 1)
                                    <div class="px-2">
                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/sightseeing.png') }}"
                                            style="width: 49px; height: 42px; margin-bottom: 4px;">
                                        <div class="ico-nem"><span
                                                class="small font-weight-bold">{{ translate('sightseeing') }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    @if($includePackages['cab'] == 1)
                                    <div class="px-2">
                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/car.png') }}"
                                            style="width: 49px; height: 42px; margin-bottom: 4px;">
                                        <div class="ico-nem"><span
                                                class="small font-weight-bold">{{ translate('transportion') }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    @if($includePackages['food'] == 1)
                                    <div class="px-2">
                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/foods.png') }}"
                                            style="width: 49px; height: 42px; margin-bottom: 4px;">
                                        <div class="ico-nem"><span
                                                class="small font-weight-bold">{{ translate('Accomadation') }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    @if($includePackages['hotel'] == 1)
                                    <div class="px-2">
                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/hotel.png') }}"
                                            style="width: 49px; height: 42px; margin-bottom: 4px;">
                                        <div class="ico-nem"><span
                                                class="small font-weight-bold">{{ translate('hotel') }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    @else
                                    <div class="px-2">
                                        <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($cab_image ?? ''), type: 'backend-product') }}" style="width: 59px; height: 47px; margin-bottom: 4px;">
                                    </div>
                                    <div class="px-2">
                                        <div class="ico-nem"><span class="small font-weight-bold">{{ $cab_name }}</span></div>
                                    </div>
                                    <!-- <div class="px-2">
                                        <a class="btn btn-sm btn--primary" onclick="add_all_package()">view package</a>
                                    </div> -->
                                    @endif
                                </div>
                            </div>
                            <div class="clr"></div>
                            <div class="row">
                                <div class="col-12 py-3 px-3">
                                    <a class="btn btn-sm btn--primary form-control" onclick="add_all_package()">{{translate('book_Now')}}</a>
                                </div>
                            </div>
                            <div class="clr"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="row">
    @if (isset($getfirst['video_url']) &&
    $getfirst['video_url'] != null &&
    str_contains($getfirst['video_url'], 'youtube.com/embed/'))
    <!-- <div class="col-12 mb-4">
             <iframe width="420" height="315" src="{{-- $getfirst['video_url'] --}}"> </iframe>
             </div> -->
    <div class="col-12 rtl text-align-direction">
        <div class="resp-iframe">
            <div class="resp-iframe__container">
                <iframe width="420" height="315" src="{{ $getfirst['video_url'] }}" frameborder="0"
                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen="">
                </iframe>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- add_all_package -->

<div class="modal fade addOtherpackages" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-body">
                <div>
                    <button type="button" class="btn btn-danger btn-sm float-end borer mb-2 text-white addOtherpackages-modal-close" data-dismiss="modal" aria-label="Close" style="margin: -32px -22px 0px 0px;">x</button>
                </div>
                <h3>{{ $getfirst['tour_name']??""}}</h3>
                <div>
                    <!--  -->
                    @php
                    $service_name = [];
                    $service_name1 = [];
                    $service_price = [];
                    $service_seats = [];
                    $service_image = [];
                    $package_checkId = [];
                    $package_checkName = [];
                    $getallnamesem = [];
                    $DuplicatePackagepacid = [];

                    if (!empty($getfirst['package_list_price']) && json_decode($getfirst['package_list_price'], true)) {
                    $packageList = json_decode($getfirst['package_list_price'], true);
                    $packageIds = array_column($packageList, 'package_id');

                    $tourPackages = \App\Models\TourPackage::whereIn('id', $packageIds)->get()->keyBy('id');

                    foreach ($packageList as $pp_index=>$val) {
                    $getpackage = $tourPackages[$val['package_id']] ?? null;
                    $packageName = strtolower(trim($getpackage['type'] ?? '')); // Normalize package name
                    $getallnamesem[] = $getpackage['name'] ?? '';
                    $isDuplicate = false;
                    $mainPackageId = strtolower(trim($getpackage['type'] ?? ''));

                    foreach ($package_checkName as $index => $existingName) {
                    if ( str_contains($packageName, $existingName) || str_contains($existingName, $packageName)) {
                    $isDuplicate = true;

                    $mainPackageId = $service_name1[$index];
                    break;
                    }
                    }
                    if ($isDuplicate) {
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['id'] = $val['package_id'];
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['name'] = ucwords($getpackage['name'] ?? '');
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['price'] = $val['pprice'];
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['seat'] = $getpackage['seats'] ?? '';
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['image'] = $getpackage['image'] ?? '';
                    } else {
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['id'] = $val['package_id'];
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['name'] = ucwords($getpackage['name'] ?? '');
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['price'] = $val['pprice'];
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['seat'] = $getpackage['seats'] ?? '';
                    $DuplicatePackagepacid[$mainPackageId][$val['package_id'].'_'.$pp_index]['image'] = $getpackage['image'] ?? '';

                    $package_checkId[] = $val['package_id'];
                    $package_checkName[] = $packageName;
                    $service_name1[] = strtolower(trim($getpackage['type'] ?? ''));
                    $service_name[] = ucwords($getpackage['name'] ?? '');
                    $service_price[] = $val['pprice'];
                    $service_seats[] = $getpackage['seats'] ?? '';
                    $service_image[] = $getpackage['image'] ?? '';
                    }
                    }
                    }
                    @endphp

                    <?php //echo'<pre>';print_r($DuplicatePackagepacid) 
                    ?>




                    <div class="row">
                        <div class="mt-4 col-12 rtl text-align-direction">
                            <?php $tab_index = 0; ?>
                            <ul class="nav nav-tabs nav--tabs mt-3 border-top border-bottom py-2 mb-0 small" role="tablist" id="tab-navigation" style="text-wrap: nowrap;">
                                @if(($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($is_person_check == 1))
                                <?php $tab_index++; ?>
                                <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link navlinks __inline-27 active disabled" href="#is_person_user" data-toggle="tab" role="tab">{{ $tab_index }}</a>&nbsp;<span>{{ translate('people') }}</span>
                                </li>
                                <li class="nav-item navItems">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                </li>
                                @endif

                                @if(($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($is_person_check == 0))
                                <?php $tab_index++; ?>
                                <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link navlinks __inline-27 active disabled" href="#cab_package" data-toggle="tab" role="tab">{{ $tab_index }}</a>&nbsp;<span>{{ translate('choose transportation') }}</span>
                                </li>
                                <li class="nav-item navItems">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                </li>
                                @endif
                                @if($getfirst['use_date'] == 0)
                                @if(!empty($DuplicatePackagepacid))
                                @foreach($DuplicatePackagepacid as $kkey=>$nname)
                                <?php $tab_index++; ?>
                                <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link navlinks __inline-27 disabled" href="#other_package_{{$kkey}}" data-toggle="tab" role="tab">{{ $tab_index }} </a>&nbsp;<span> {{ translate('choose '.$kkey) }}</span>
                                </li>
                                <li class="nav-item navItems">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                </li>
                                @endforeach
                                @endif
                                @endif
                                @if($is_person_check == 1)
                                <!-- <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link navlinks __inline-27 active disabled" href="#cab_package" data-toggle="tab" role="tab">{{ $tab_index }}</a>&nbsp;<span>{{ translate('choose transportation') }}</span>
                                </li>
                                <li class="nav-item navItems">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                </li> -->
                                @endif
                                @if($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                <?php $tab_index++; ?>
                                <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link navlinks __inline-27 disabled booking_date_point" href="#booking_date" data-toggle="tab" role="tab">
                                        {{ $tab_index }}
                                    </a> &nbsp;<span>{{ translate('booking_info') }}</span>
                                </li>
                                <li class="nav-item navItems">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                </li>
                                @endif


                                <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link {{ (($tab_index > 1)?'navlinks':'')}} __inline-27 {{ (($getfirst['use_date'] == 1)?'active':'')}} disabled pay_summary_point booking_date_point" href="#pay_summary" data-toggle="tab" role="tab" style="background: white;">
                                        <?php if ($tab_index > 1) {
                                            $tab_index++; ?>
                                            {{ $tab_index }}
                                        <?php } else { ?>
                                            {{ translate('payment') }}
                                        <?php } ?>
                                    </a>
                                    <?php if ($tab_index > 1) { ?>
                                        &nbsp;<span>{{ translate('payment') }}</span>
                                    <?php } ?>
                                </li>
                            </ul>
                            @if($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)

                            <div class="col-12 text-center my-2">
                                <span class="title_show_names font-weight-bold"></span>
                            </div>
                            @endif
                            <div class=" __review-overview __rounded-10 pt-3">
                                <div class="tab-content px-lg-3">
                                    <!-- Process -->
                                    @if($getfirst['use_date'] == 0 && $is_person_check == 0)
                                    <div class="tab-pane fade show active text-justify" id="cab_package" role="tabpanel">
                                        <div class="pt-2 specification mx-2" style="height: 287px; overflow: auto;">
                                            @if (!empty($getfirst['cab_list_price']) && json_decode($getfirst['cab_list_price'], true))
                                            <?php $v_header = 0; ?>
                                            @foreach(json_decode($getfirst['cab_list_price'], true) as $key=>$clprice)
                                            @if($v_header == 0)
                                            <div class="row mb-1 cab-tab-font-header">
                                                <div class="col-4 col-md-3 col-lg-3">{{translate('Vehicle Name')}}</div>
                                                <div class="col-4 col-md-3 col-lg-3 d-none d-sm-block"></div>
                                                <div class="col-4 col-md-3 col-lg-3">{{translate('Price')}}</div>
                                                <div class="col-4 col-md-3 col-lg-3">{{translate('No. of Vehicle')}}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                            </div>
                                            <?php $v_header = 1; ?>
                                            @endif
                                            @php
                                            $getCabs = \App\Models\TourCab::where('id', $clprice['cab_id'])->first();
                                            @endphp
                                            @if($getCabs)
                                            <div class="row">
                                                <div class="col-4 col-md-3 col-lg-3 text-left">
                                                    <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ''), type: 'backend-product') }}" style="width: 125px; height: 77px;margin-bottom: 4px;">
                                                    <br>
                                                    <div class="text-left font-weight-bold d-block d-sm-none" style="font-size: 12px;">
                                                        <span>{{ ucwords($getCabs['name'] ?? '')}}</span><br>
                                                        <span>{{$getCabs['seats']}} seats</span> <br>
                                                        <span>{{ webCurrencyConverter(amount: ((double)$clprice['price']??0)) }}</span><br>
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold  d-none d-sm-block" style="font-size: 12px;">
                                                    <span>{{ ucwords($getCabs['name'] ?? '')}}</span><br>
                                                    <span>{{$getCabs['seats']}} seats</span> <br>
                                                    <span>{{ webCurrencyConverter(amount: ((double)$clprice['price']??0)) }}</span><br>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold" style="font-size: 12px;">
                                                    <span> 1 * {{ webCurrencyConverter(amount: ((double)$clprice['price']??0)) }}</span>
                                                    <hr style="    height: 5px; border: 0px;">
                                                    <span class="cab_information{{ $clprice['cab_id'] }}{{$clprice['price']}} cab_information">
                                                        @if($clprice['id'] == ($cab_index??''))
                                                        {{ webCurrencyConverter(amount: ((double)$clprice['price']??0)) }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3">
                                                    <a style="margin-top: 15px;" class="px-3 py-1 btn--primary rounded-pill cursor-pointer {{ (($clprice['id'] != ($cab_index??''))?'':'d-none') }} cab_add_package1 cab_add_package1_{{$key}}" onclick="$('.cab_add_package').addClass('d-none');$('.cab_add_package1').removeClass('d-none');$('.cab_add_package1_{{$key}}').addClass('d-none');$('.cab_add_package_value').val(0);$('.cab_add_package_{{$key}}').removeClass('d-none');$('.cab_add_package_value_{{$key}}').val(1);sub_qtys(`cab`,`{{ $clprice['cab_id'] }}`,1,`{{$clprice['price']}}`);$('.cab_information').text('');$(`.cab_information{{ $clprice['cab_id'] }}{{$clprice['price']}}`).text(parseFloat(`{{$clprice['price']}}`).toLocaleString('en-US', { style: 'currency', currency: '{{getCurrencyCode()}}'}))">add</a>
                                                    <div class="cab_add_package cab_add_package_{{$key}}  {{ (($clprice['id'] == ($cab_index??''))?'':'d-none') }}">
                                                        <div class="small" style="display: flex;margin-top: 15px;">
                                                            <a class="cab-button-plus-minus" onclick="newaddpackages('de',`cab_add_package_value_{{$key}}`,`{{$clprice['price']}}`,this)" data-type1="cab" data-type="cab" data-button="cab_add_package1_{{$key}}" data-point="cab_add_package_{{$key}}" data-id="{{ $clprice['cab_id'] }}"><i class="tio-remove" style="font-size: 15px;margin-top:15px"></i> </a>
                                                            <input type="number" readonly style="width: 39px; height: 33px; border: 1px solid #80808040;" class="cab_add_package_value cab_add_package_value_{{$key}} text-center" value="{{ (($clprice['id'] == ($cab_index??''))?1:0) }}">
                                                            <a class="cab-button-plus-minus" onclick="newaddpackages('in',`cab_add_package_value_{{$key}}`,`{{$clprice['price']}}`,this)" data-type1="cab" data-type="cab" data-button="cab_add_package1_{{$key}}" data-point="cab_add_package_{{$key}}" data-id="{{ $clprice['cab_id'] }}"><i class="tio-add" style="font-size: 15px;margin-top:15px"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-12 mb-2">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <a class="w-100 small" style="cursor: pointer;color: var(--web-primary) !important;" data-toggle="collapse" href="#multiCollapseExample{{$key}}" aria-expanded="false" aria-controls="multiCollapseExample{{$key}}"><i class="tio-chevron_down" style="font-size: 23px;">chevron_down</i>{{ translate('view') }} {{ translate('details') }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12">
                                                            <div class="col">
                                                                <div class="collapse" id="multiCollapseExample{{$key}}">
                                                                    <div class="card card-body">
                                                                        {!! $getCabs['description'] !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-12 mb-1">
                                                    <hr>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    <!-- ///////////////satish//////////////// -->
                                    @if(($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($is_person_check == 1))
                                    <div class="tab-pane fade text-justify is_person_user_point1" id="is_person_user" role="tabpanel">
                                        <div class="row mt-4">
                                            <div class="col-12 mt-2 px-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="small"><span>{{ translate('user_Group')}}</span></div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small font-weight-bold"><span>{{ translate('per_Person')}}</span></div>
                                                    </div>
                                                    <div class="col-4 text-center">
                                                        <div class="small">{{ translate('Choose')}}</div>
                                                    </div>
                                                    <div class="col-12">
                                                        <hr>
                                                    </div>
                                                </div>
                                                @if (!empty($getfirst['cab_list_price']) && is_array(json_decode($getfirst['cab_list_price'], true)))
                                                @foreach (json_decode($getfirst['cab_list_price'], true) as $kper=>$persons)
                                                <div class="row my-2">
                                                    <div class="col-4">
                                                        <div class="font-weight-bold">
                                                            <span>Group of {{ $persons['min'] }}{{ (($persons['min'] == $persons['max'])? '' : ' - '.$persons['max']) }} (Per Person) </span><br>
                                                            <a class="personMessageShow personMessageShow{{$kper}} text-primary small"></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small font-weight-bold ">
                                                            <span>{{ webCurrencyConverter(amount: $persons['price']??0) }}</span><br>
                                                            <span class="person_total_amounts{{$kper}} person_total_amounts"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-center">
                                                        <a class="px-3 py-1 btn--primary rounded-pill cursor-pointer person_use_add person_use_add{{$kper}} " onclick="$('.person_use_book').addClass('d-none');$('.person_use_input').val(0);$('.person_use_book{{$kper}}').removeClass('d-none');$('.person_use_input{{$kper}}').val('{{$persons['min']}}');$('.person_use_add').removeClass('d-none');$('.person_use_add{{$kper}}').addClass('d-none');newperPerson_Calculation('',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}">add</a>
                                                        <div>
                                                            <div class="small person_use_book person_use_book{{$kper}} d-none" style="display: inline-flex;">
                                                                <a class="cab-button-plus-minus " onclick="newperPerson_Calculation('de',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}"><i class="tio-remove" style="font-size: 15px;margin-top:15px"></i></a>
                                                                <input type="number" readonly style="width: 39px; height: 33px; border: 1px solid #80808040;" class="text-center person_use_input person_use_input{{ $kper}}" value="1">
                                                                <a class="cab-button-plus-minus " onclick="newperPerson_Calculation('in',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}"><i class="tio-add" style="font-size: 15px;margin-top:15px"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <hr>
                                                    </div>
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if(($getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($is_person_check == 0))
                                    <div class="tab-pane fade show active text-justify" id="cab_package" role="tabpanel">
                                        <div class="specification p-2" style="height: 287px; overflow: auto;">
                                            @if($s_name)
                                            <?php $v_header = 0; ?>
                                            @foreach($s_name as $kk=>$cab_names)
                                            @if($v_header == 0)
                                            <div class="row mb-1 cab-tab-font-header">
                                                <div class="col-4 col-md-3 col-lg-3">{{translate('Vehicle Name')}}</div>
                                                <div class="col-4 col-md-3 col-lg-3 d-none d-sm-block"></div>
                                                <div class="col-4 col-md-3 col-lg-3">{{translate('Price')}}</div>
                                                <div class="col-4 col-md-3 col-lg-3">{{translate('No. of Person')}}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                            </div>
                                            <?php $v_header = 1; ?>
                                            @endif
                                            <div class="row">
                                                <div class="col-4 col-md-3 col-lg-3 text-left">
                                                    <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($s_image[$kk] ?? ''), type: 'backend-product') }}" style="width: 125px; height: 77px;margin-bottom: 4px;">
                                                    <br>
                                                    <div class="text-left font-weight-bold d-block d-sm-none" style="font-size: 12px;">
                                                        <span>{{ ucwords($cab_names ?? '')}}</span><br>
                                                        <span>{{$s_seats[$kk]}} seats</span> <br>
                                                        <span>{{ webCurrencyConverter(amount: ((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))) }}</span><br>
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold  d-none d-sm-block" style="font-size: 12px;">
                                                    <span>{{ ucwords($cab_names ?? '')}}</span><br>
                                                    <span>{{$s_seats[$kk]}} seats</span> <br>
                                                    <span>{{ webCurrencyConverter(amount: ((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))) }}</span><br>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold" style="font-size: 12px;">
                                                    <span> 1 * {{ webCurrencyConverter(amount: ((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))) }}</span>
                                                    <hr style="height: 5px; border: 0px;">
                                                    <span class="cab_information{{ $kk }}{{((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))}} cab_information">
                                                        {{ webCurrencyConverter(amount: ((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))) }}
                                                    </span>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3">
                                                    <a style="margin-top: 15px;" class="px-3 py-1 btn--primary rounded-pill cursor-pointer {{ (($kk != ($cab_ids??''))?'':'d-none') }} cab_add_package1 cab_add_package1_{{$kk}}" onclick="$('.cab_add_package').addClass('d-none');$('.cab_add_package1').removeClass('d-none');$('.cab_add_package1_{{$kk}}').addClass('d-none');$('.cab_add_package_value').val(0);$('.cab_add_package_{{$kk}}').removeClass('d-none');$('.cab_add_package_value_{{$kk}}').val(1);sub_qtys(`cab`,`{{ $kk }}`,1,`{{((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))}}`);$('.cab_information').text('');$(`.cab_information{{ $kk }}{{((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))}}`).text(parseFloat(`{{((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))}}`).toLocaleString('en-US', { style: 'currency', currency: '{{getCurrencyCode()}}'}))">add</a>
                                                    <div class="cab_add_package cab_add_package_{{$kk}}  {{ (($kk == ($cab_ids??''))?'':'d-none') }}">
                                                        <div class="small" style="display: flex;margin-top: 15px;">
                                                            <a class="cab-button-plus-minus" data-error-spans="{{$kk}}" data-cab_amount="{{$s_price[$kk]}}" data-cab_max_seat="{{$s_seats[$kk]}}" data-ex_packages_amount="{{($packages_price??'')}}" onclick="newaddpackages_daliy('de',`cab_add_package_value_{{$kk}}`,`{{ ((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0)) }}`,this)" data-type1="cab" data-type="cab" data-button="cab_add_package1_{{$kk}}" data-point="cab_add_package_{{$kk}}" data-id="{{ $kk }}"><i class="tio-remove" style="font-size: 15px;margin-top:15px"></i> </a>
                                                            <input type="number" readonly style="width: 39px; height: 33px; border: 1px solid #80808040;" class="cab_add_package_value cab_add_package_value_{{$kk}} text-center" value="{{ (($kk == ($cab_ids??''))?1:0) }}">
                                                            <a class="cab-button-plus-minus" data-error-spans="{{$kk}}" data-cab_amount="{{$s_price[$kk]}}" data-cab_max_seat="{{$s_seats[$kk]}}" data-ex_packages_amount="{{($packages_price??'')}}" onclick="newaddpackages_daliy('in',`cab_add_package_value_{{$kk}}`,`{{((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))}}`,this)" data-type1="cab" data-type="cab" data-button="cab_add_package1_{{$kk}}" data-point="cab_add_package_{{$kk}}" data-id="{{ $kk }}"><i class="tio-add" style="font-size: 15px;margin-top:15px"></i></a>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <span class="text-danger small cab-select-error-{{$kk}}" style="font-weight: 300 !important;"></span>
                                                </div>
                                                <!--  -->
                                                <div class="col-12 mb-2">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <a class="w-100 small" style="cursor: pointer;color: var(--web-primary) !important;" data-toggle="collapse" href="#multiCollapseExample{{$kk}}" aria-expanded="false" aria-controls="multiCollapseExample{{$kk}}"><i class="tio-chevron_down" style="font-size: 23px;">chevron_down</i>{{ translate('view') }} {{ translate('details') }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12">
                                                            <div class="col">
                                                                <div class="collapse" id="multiCollapseExample{{$kk}}">
                                                                    <div class="card card-body">
                                                                        {!! $s_description[$kk] !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--  -->
                                                <div class="col-12 mb-1">
                                                    <hr>
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    <!-- //////////////////////////////////////////////// -->
                                    @if($getfirst['use_date'] == 0)
                                    @if(!empty($DuplicatePackagepacid))
                                    @foreach($DuplicatePackagepacid as $kkey => $nname)
                                    @if($nname)
                                    <div class="tab-pane fade text-justify" id="other_package_{{$kkey}}" role="tabpanel">
                                        <div class="pt-2 specification" style="height: 287px; overflow: auto;">
                                            @if(!empty($getfirst['package_list_price']) && json_decode($getfirst['package_list_price'], true))
                                            @php
                                            $displayedPackages = [];
                                            @endphp
                                            @foreach(json_decode($getfirst['package_list_price'], true) as $keyk => $plis)
                                            @foreach($nname as $pp_v => $packages_ar)
                                            @if($packages_ar['id'] == $plis['package_id'] && !in_array($plis['package_id'].$plis['pprice'], $displayedPackages))
                                            @php
                                            $tourPackages = \App\Models\TourPackage::where('id', $plis['package_id'])->first();
                                            $displayedPackages[] = $plis['package_id'].$plis['pprice'];
                                            @endphp
                                            @if($tourPackages)
                                            <div class="row">
                                                <div class="col-3 text-left">
                                                    <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product') }}" style="width: 125px; height: 77px;margin-bottom: 4px;">
                                                    <br>
                                                    <div class="text-left font-weight-bold d-block d-sm-none" style="font-size: 12px;">
                                                        <span>{{ ucwords($tourPackages['name'] ?? '') }}</span><br>
                                                        <span>{{ $tourPackages['title'] ?? '' }}</span> <br>
                                                        <span>{{ webCurrencyConverter(amount: ((double)$plis['pprice'] ?? 0)) }}</span><br>
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold  d-none d-sm-block" style="font-size: 12px;">
                                                    <span>{{ ucwords($tourPackages['name'] ?? '') }}</span><br>
                                                    <span>{{ $tourPackages['title'] ?? '' }}</span> <br>
                                                    <span>{{ webCurrencyConverter(amount: ((double)$plis['pprice'] ?? 0)) }}</span><br>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold" style="font-size: 12px;">
                                                    <span> 1 * {{ webCurrencyConverter(amount: ((double)$plis['pprice'] ?? 0)) }}</span>
                                                    <hr style="height: 5px; border: 0px;">
                                                    <span class="other_information{{$plis['package_id']}} other_information{{$plis['package_id']}}{{$plis['pprice']}}"></span>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3">
                                                    <a style="margin-top: 15px;" class="px-3 py-1 btn--primary rounded-pill cursor-pointer other_package_add1{{$plis['package_id']}} other_package_add1_{{$plis['package_id']}}{{$keyk}}" onclick="$(`.other_package_add{{$plis['package_id']}}`).addClass('d-none');$(`.other_package_add1{{$plis['package_id']}}`).removeClass('d-none');$(`.other_package_add1_{{$plis['package_id']}}{{$keyk}}`).addClass('d-none');$(`.other_package_add_value{{$plis['package_id']}}`).val(0);$(`.other_package_add_{{$plis['package_id']}}{{$keyk}}`).removeClass('d-none');$(`.other_package_add_value_{{$plis['package_id']}}{{$keyk}}`).val(1);sub_qtys(`other{{$plis['package_id']}}`,`{{$plis['package_id']}}`,1,`{{$plis['pprice']}}`);$(`.other_information{{$plis['package_id']}}`).text('');$(`.other_information{{$plis['package_id']}}{{$plis['pprice']}}`).text(parseFloat(`{{$plis['pprice']}}`).toLocaleString('en-US', { style: 'currency', currency: '{{getCurrencyCode()}}'}))">add</a>
                                                    <div class="other_package_add{{$plis['package_id']}} other_package_add_{{$plis['package_id']}}{{$keyk}} d-none">
                                                        <div class="small" style="display: flex; margin-top: 15px;">
                                                            <a class="cab-button-plus-minus" onclick="newaddpackages('de',`other_package_add_value_{{$plis['package_id']}}{{$keyk}}`,`{{$plis['pprice']}}`,this)" data-type1="other" data-type="other{{$plis['package_id']}}" data-button="other_package_add1_{{$plis['package_id']}}{{$keyk}}" data-point="other_package_add_{{$plis['package_id']}}{{$keyk}}" data-id="{{$plis['package_id']}}" data-key="{{$keyk}}"><i class="tio-remove" style="font-size: 15px;margin-top:15px"></i></a>
                                                            <input type="number" readonly style="width: 39px; height: 33px; border: 1px solid #80808040;" class="other_package_add_value{{$plis['package_id']}} other_package_add_value_{{$plis['package_id']}}{{$keyk}} text-center" value="{{ (($clprice['id'] == ($cab_index ?? '')) ? 1 : 0) }}">
                                                            <a class="cab-button-plus-minus" onclick="newaddpackages('in',`other_package_add_value_{{$plis['package_id']}}{{$keyk}}`,`{{$plis['pprice']}}`,this)" data-type1="other" data-type="other{{$plis['package_id']}}" data-button="other_package_add1_{{$plis['package_id']}}{{$keyk}}" data-point="other_package_add_{{$plis['package_id']}}{{$keyk}}" data-id="{{$plis['package_id']}}" data-key="{{$keyk}}"><i class="tio-add" style="font-size: 15px;margin-top:15px"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-2">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <a class="w-100 small" style="cursor: pointer;color: var(--web-primary) !important;" data-toggle="collapse" href="#multiCollapseExample{{$plis['package_id']}}{{$keyk}}" aria-expanded="false" aria-controls="multiCollapseExample{{$plis['package_id']}}{{$keyk}}"><i class="tio-chevron_down" style="font-size: 23px;">chevron_down</i>{{ translate('view') }} {{ translate('details') }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12">
                                                            <div class="col">
                                                                <div class="collapse" id="multiCollapseExample{{$plis['package_id']}}{{$keyk}}">
                                                                    <div class="card card-body">
                                                                        {!! $tourPackages['description'] !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-1">
                                                    <hr>
                                                </div>
                                            </div>
                                            @endif
                                            @endif
                                            @endforeach
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                    @endif
                                    @endif

                                    <!-- About -->
                                    @if($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                    <div class="tab-pane fade text-justify booking_date_point1" id="booking_date" role="tabpanel">
                                        <div class="row pt-2 specification">
                                            @php
                                            $dateRange = explode(' - ', $getfirst['startandend_date']);
                                            $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
                                            $endDate = isset($dateRange[1]) ? $dateRange[1] : '';
                                            @endphp
                                            <div class="col-12 row mt-2">
                                                <div class="col-12 table-responsive">
                                                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle">
                                                        <tbody>
                                                            <tr class="d-block d-sm-none">
                                                                <td><i class="fa fa-calendar" aria-hidden="true" style="color: var(--primary-clr);"></i></td>
                                                                <td colspan="2">
                                                                    @if ($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                                                    <span class="font-weight-bold">{{ translate('Arrival Date') }}</span> <br>
                                                                    <input class="form-control hasDatepicker text-align-direction" type="text" name="booking_date" id="bookingdate" placeholder="Booking Slot Date" onchange="$('.pickup_date').val(this.value)" onclick="datePicker(this)" input-mode="text" autocomplete="off" required>
                                                                    @else
                                                                    <span class="font-weight-bold">{{ translate('Arrival Date') }}</span> <br>
                                                                    {{ date('d M, Y', strtotime($startDate)) }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr class="d-block d-sm-none">
                                                                <td><i class="fa fa-clock-o" aria-hidden="true" style="color: var(--primary-clr);"></i></td>
                                                                <td colspan="2">
                                                                    @if ($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                                                    @if($getfirst['time_slot'] && json_decode($getfirst['time_slot'],true))
                                                                    <span class="font-weight-bold">{{ translate('Time Slot') }}</span> <br>
                                                                    <select name="date" class="form-control" onchange="$('.pickup_time').val($(this).val())">
                                                                        <option value="" selected disabled>Select Time Slot</option>
                                                                        @foreach(json_decode($getfirst['time_slot'],true) as $vva)
                                                                        <option value="{{$vva}}">{{ $vva}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @else
                                                                    <span class="font-weight-bold">{{ translate('Arrival Time') }}</span> <br>
                                                                    <input type="text" name='date' class="form-control w-50 pickupopen_time" id="opentime" onkeyup="$('.pickup_time').val(this.value)" onchange="$('.pickup_time').val(this.value)" onclick="window.$timepicker.open()" autocomplete="off">
                                                                    @endif
                                                                    @else
                                                                    @if ($startDate != $endDate)
                                                                    <span class="font-weight-bold">{{ translate('Departure Date') }}</span> <br>
                                                                    {{ date('d M, Y', strtotime($endDate)) }}
                                                                    @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <!--  -->
                                                            <tr class="d-none d-sm-block">
                                                                <td><i class="fa fa-calendar" aria-hidden="true" style="color: var(--primary-clr);"></i></td>
                                                                <td>
                                                                    @if ($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                                                    <span class="font-weight-bold">{{ translate('Arrival Date') }}</span> <br>
                                                                    <input class="form-control hasDatepicker text-align-direction" type="text" name="booking_date" id="bookingdate" placeholder="Booking Slot Date" onchange="$('.pickup_date').val(this.value)" onclick="datePicker(this)" input-mode="text" autocomplete="off" required>
                                                                    @else
                                                                    <span class="font-weight-bold">{{ translate('Arrival Date') }}</span> <br>
                                                                    {{ date('d M, Y', strtotime($startDate)) }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                                                    @if($getfirst['time_slot'] && json_decode($getfirst['time_slot'],true))
                                                                    <span class="font-weight-bold">{{ translate('Time Slot') }}</span> <br>
                                                                    <select name="date" class="form-control" onchange="$('.pickup_time').val($(this).val())">
                                                                        <option value="" selected disabled>Select Time Slot</option>
                                                                        @foreach(json_decode($getfirst['time_slot'],true) as $vva)
                                                                        <option value="{{$vva}}">{{ $vva}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @else
                                                                    <span class="font-weight-bold">{{ translate('Arrival Time') }}</span> <br>
                                                                    <input type="text" name='date' class="form-control w-50 pickupopen_time" id="opentime" onkeyup="$('.pickup_time').val(this.value)" onchange="$('.pickup_time').val(this.value)" onclick="window.$timepicker.open()" autocomplete="off">
                                                                    @endif
                                                                    @else
                                                                    @if ($startDate != $endDate)
                                                                    <span class="font-weight-bold">{{ translate('Departure Date') }}</span> <br>
                                                                    {{ date('d M, Y', strtotime($endDate)) }}
                                                                    @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @if ($getfirst['use_date'] == 1)
                                                            <tr>
                                                                <td><i class="fa fa-clock-o" aria-hidden="true" style="color: var(--primary-clr);"></i></td>
                                                                <td colspan='2'>
                                                                    <span class="font-weight-bold">{{ translate('Arrival Time') }}</span><br>
                                                                    {{ $getfirst['pickup_time'] ?? '' }}
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            @if ($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 4)
                                                            <tr class="d-none d-sm-block d-block d-sm-none">
                                                                <td><i class="fa fa-map-marker" aria-hidden="true" style="color: var(--primary-clr);"></i></td>
                                                                <td colspan='2'>
                                                                    <span class="font-weight-bold">{{ translate('Pickup Location') }}</span><br>
                                                                    {{ $getfirst['pickup_location'] ?? '' }}
                                                                </td>
                                                            </tr>
                                                            <tr class="d-none d-sm-flex d-flex d-sm-none">
                                                                <td class="p-0"></td>
                                                                <td colspan='2' class="w-100 text-center p-0">
                                                                    <span class="font-weight-bold">{{ translate('OR') }}</span><br>
                                                                </td>
                                                            </tr>
                                                            @if($is_person_check == 1)
                                                            <tr class="d-none d-sm-flex d-flex d-sm-none">
                                                                <td></td>
                                                                <td colspan='2' class="w-100 text-center pt-0">
                                                                    <span class="font-weight-bold small">{{ translate('Nearest Amenities Pickup Points: Airport, Railway Station, Bus Station, Hotels, etc') }}</span><br>
                                                                </td>
                                                            </tr>
                                                            <tr class="d-none d-sm-flex d-flex d-sm-none">
                                                                <td colspan='3' class="w-100 text-center pt-0">
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-pickup extracharges-transport" data-id="only-pickup" data-type="Pickup" data-type1="pick" onclick="transportOption(this)">&nbsp;Only Pickup</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-droup extracharges-transport" data-id="only-droup" data-type="Drop" data-type1="drop" onclick="transportOption(this)">&nbsp;Only Droup</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <span class="font-weight-bold"><input type="checkbox" class="only-both extracharges-transport" data-id="only-both" data-type="Both" data-type1="both" onclick="transportOption(this)">&nbsp;Both</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <span class="extransportPrice font-wight-bolder text-primary"></span>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            <!-- <tr>
                                                                        <td colspan="3">
                                                                            <div class="row">
                                                                                <div id="map"></div>
                                                                            </div>
                                                                        </td>
                                                                    </tr> -->
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            @if ($getfirst['use_date'] == 0 || $getfirst['use_date'] == 3)
                                            <div class="col-12 row">
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-md-1 col-1">
                                                            <i class="fa fa-map-marker" aria-hidden="true" style="color: var(--primary-clr);    font-size: 21px; margin: 13px 0px 0px 2px;"></i>
                                                        </div>
                                                        <div class="col-md-11 col-11">
                                                            <input type="hidden" id="city" value="{{ $getfirst->getRawOriginal('cities_name') }}" placeholder="Enter city name" />
                                                            <span class="font-weight-bold cab-tab-font-header">{{ translate('Pickup Location') }}
                                                                @if($getfirst['use_date'] == 0)
                                                                ( {{translate('Railway Station,Bus Station,Hotels')}} )
                                                                @else
                                                                ( {{translate('Airport,Railway Station,Bus Station,Hotels,etc')}} )
                                                                @endif
                                                            </span>
                                                            @if($getfirst['use_date'] == 0)
                                                            <br>
                                                            <small class="">
                                                                ( {{ ($getfirst['cities_name']??'') }} {{ ($getfirst['state_name']??'')}} {{ ($getfirst['country_name']??'') }} ) <a style="cursor: pointer;" onclick="mapShow()"><i class="fa fa-globe" aria-hidden="true" style="color: #36c136; font-size: 22px;"></i><i class="tio-made_call" style="color: blue;font-size: 18px;">made_call</i></a>
                                                            </small>
                                                            @else
                                                            <br>
                                                            <small class="">
                                                                ( {{ ($getfirst['cities_name']??'') }} {{ ($getfirst['state_name']??'')}} {{ ($getfirst['country_name']??'') }} ) {{ translate('Free up to 20 km, there after charges will be payable as per distance')}} <a style="cursor: pointer;" onclick="mapShow()"><i class="fa fa-globe" aria-hidden="true" style="color: #36c136; font-size: 22px;"></i><i class="tio-made_call" style="color: blue;font-size: 18px;">made_call</i></a>
                                                            </small>
                                                            @endif
                                                            <input id="search-box" type="text" class="pick_up-input mb-2 getAddress_google" placeholder="{{ translate('Search Pickup locations') }}">
                                                            <span class="address_error_message text-danger font-weight-bolder small"></span>
                                                        </div>
                                                        @if($getfirst['cities_tour'] == 0)
                                                        <div class="col-md-1 col-1">
                                                            <i class="fa fa-road" aria-hidden="true" style="color: var(--primary-clr);font-size: 27px; margin: 22px 0px 0px 5px;"></i>
                                                        </div>
                                                        <div class="col-md-11 col-11">
                                                            <div class="row mt-4">
                                                                <div class="col-6">
                                                                    <input type="radio" name="oneusedistance" class="out_side_div" value="one_way" onclick="calculateDistance()" data-ex_distance="{{ $getfirst['ex_distance']??0 }}" checked style="position: relative;width: 21px;height: 17px;">&nbsp;Only Pickup
                                                                </div>
                                                                <div class="col-6">
                                                                    <input type="radio" name="oneusedistance" class="out_side_div" value="two_way" onclick="calculateDistance()" data-ex_distance="{{ $getfirst['ex_distance']??0 }}" style="position: relative;width: 21px;height: 17px;">&nbsp;Pickup & Drop both
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @elseif($getfirst['use_date'] == 2 || $getfirst['use_date'] == 3)
                                                        <input type="radio" name="oneusedistance" class="out_side_div" value="two_way" onclick="calculateDistance()" data-ex_distance="{{ $getfirst['ex_distance']??0 }}">
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- <div class="col-12">
                                                            <div id="map"></div>
                                                            <div id="message"></div>
                                                        </div> -->
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <div class="tab-pane fade text-justify {{ (($getfirst['use_date'] == 1)?'show active':'')}} pay_summary_point1" id="pay_summary" role="tabpanel">
                                        <div class="row mt-4">
                                            <div class="col-12 mt-2 px-3">
                                                @if($getfirst['is_person_use'] == 0)
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="small"><span>{{ translate('name')}}</span></div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small font-weight-bold"><span>{{ translate('No. Of Person')}}</span></div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small" style="display: flex;">{{ translate('total_price')}}</div>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="small"><span>{{ translate('per_head')}}</span></div>
                                                    </div>
                                                    <div class="col-6 text-center">
                                                        <div class="small">{{ translate('total_price')}}</div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-12 mt-2">
                                                <div class="row px-2">
                                                    <hr style="width: 100%">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row px-3 tab-booking-data">
                                            @if($getfirst['use_date'] == 1 && count($s_packageid) > 0 && $getfirst['is_person_use'] == 0)
                                            @foreach($s_packageid as $k=>$vapak)
                                            @php
                                            $getcab_prices = ($s_price[$vapak]??0) + ($packages_price??0)
                                            @endphp
                                            <div class="col-12 mt-2">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($s_image[$vapak] ?? ''), type: 'backend-product') }}" style="width: 59px; height: 47px; margin-bottom: 4px;">
                                                        <div class="small">
                                                            <!-- <span>{{$s_seats[$vapak]}} seats</span><br> -->
                                                            <span class="font-weight-bolder">{{$s_name[$vapak]}}</span>
                                                        </div>
                                                    </div>
                                                    @php
                                                    $getseats = \App\Models\TourOrder::where('tour_id',$getfirst['id'])->where('amount_status',1)->where('status',1)->where('available_seat_cab_id',$vapak)->sum('qty');
                                                    @endphp
                                                    <div class="col-4">
                                                        @if(($s_seats[$vapak] - $getseats) >= 0)
                                                        <a style="margin-top: 15px;" class="px-3 py-1 btn--primary rounded-pill cursor-pointer {{ ((reset($s_packageid) == $vapak )?'d-none':'') }} cab_add_packagesp1 cab_add_packagesp1_{{$k}}" onclick="handleCabPackageClick('{{ $k }}', '{{ $s_packageid[$vapak] }}', '{{ $getcab_prices }}')">add</a>
                                                        <div class="cab_add_packagesp cab_add_packagesp_{{$k}}  {{ ((reset($s_packageid) == $vapak )?'':'d-none') }}">
                                                            <div class="small" style="display: flex;margin-top: 15px;">
                                                                <a class="cab-button-plus-minus" onclick="newaddpackages('de',`cab_add_packagesp_value_{{$k}}`,`{{$getcab_prices}}`,this)" data-type1="cab" data-type="cab" data-button="cab_add_packagesp1_{{$k}}" data-point="cab_add_packagesp_{{$k}}" data-id="{{ $s_packageid[$vapak] }}"><i class="tio-remove" style="font-size: 15px;margin-top:15px"></i> </a>

                                                                <input type="number" readonly style="width: 39px; height: 33px; border: 1px solid #80808040;" class="cab_add_packagesp_value cab_add_packagesp_value_{{$k}} text-center" value="{{ ((reset($s_packageid) == $vapak)?1:0) }}" data-min_value="{{ ($s_seats[$vapak] - $getseats) }}" data-total_seats="{{$s_seats[$vapak]}}">
                                                                <a class="cab-button-plus-minus" onclick="newaddpackages('in',`cab_add_packagesp_value_{{$k}}`,`{{$getcab_prices}}`,this)" data-type1="cab" data-type="cab" data-button="cab_add_packagesp1_{{$k}}" data-point="cab_add_packagesp_{{$k}}" data-id="{{ $s_packageid[$vapak] }}"><i class="tio-add" style="font-size: 15px;margin-top:15px"></i></a>
                                                            </div>
                                                        </div>
                                                        @else
                                                        <a style="margin-top: 15px;" class="px-3 py-1 btn-danger rounded-pill cursor-pointer">sold-out</a>
                                                        @endif
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small font-weight-bolder spcab_packages_data{{$k}} spcab_packages_data mt-4" style="display: flex;" data-manamount='{{$getcab_prices}}' data-seats='{{$s_name[$vapak]}} {{$s_seats[$vapak]}} seats'> {{ webCurrencyConverter(amount: $getcab_prices) }}</div>
                                                    </div>
                                                    <div class="seat-info-container" style='line-height: 14px;'>
                                                        <small>Total Vehicle: <span class="font-weight-bold">{{$s_seats_text_show[$vapak]}}</span></small><br>
                                                        <small>Total Seat: <span class="font-weight-bold">{{$s_seats[$vapak]}}</span></small><br>
                                                        <small>Remaining Seat: <span class="font-weight-bold">{{ ($s_seats[$vapak] - $getseats) }}</span></small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                            @elseif($getfirst['is_person_use'] == 1 && $getfirst['use_date'] == 1)
                                            @if (!empty($getfirst['cab_list_price']) && is_array(json_decode($getfirst['cab_list_price'], true)))
                                            <div class="col-md-12">
                                                @foreach (json_decode($getfirst['cab_list_price'], true) as $kper=>$persons)
                                                <div class="row py-3 d-flex d-md-none">
                                                    <div class="col-6">
                                                        <div class="font-weight-bold align-self-center">
                                                            <span>Group of {{ $persons['min'] }}{{ (($persons['min'] == $persons['max'])? '' : ' - '.$persons['max']) }} (Per Person) </span>
                                                            <a class="personMessageShow personMessageShow{{$kper}} text-primary small"></a>
                                                        </div>
                                                        <div class="small font-weight-bold">
                                                            <span>{{ webCurrencyConverter(amount: $persons['price']??0) }}</span><br>
                                                            <span class="person_total_amounts{{$kper}} person_total_amounts"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 text-center">
                                                        <a class="px-3 py-1 btn--primary rounded-pill cursor-pointer person_use_add person_use_add{{$kper}} per_specel_use_add{{$persons['id']}}" onclick="$('.person_use_book').addClass('d-none');$('.person_use_input').val(0);$('.person_use_book{{$kper}}').removeClass('d-none');$('.person_use_input{{$kper}}').val('{{$persons['min']}}');$('.person_use_add').removeClass('d-none');$('.person_use_add{{$kper}}').addClass('d-none');newperPerson_Calculation('',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}">add</a>
                                                        <div>
                                                            <div class="small person_use_book person_use_book{{$kper}} d-none" style="display: inline-flex;">
                                                                <a class="cab-button-plus-minus " onclick="newperPerson_Calculation('de',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}"><i class="tio-remove" style="font-size: 15px;margin-top:15px"></i></a>
                                                                <input type="number" readonly style="width: 39px; height: 33px; border: 1px solid #80808040;" class="text-center person_use_input person_use_input{{ $kper}}" value="1">
                                                                <a class="cab-button-plus-minus " onclick="newperPerson_Calculation('in',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}"><i class="tio-add" style="font-size: 15px;margin-top:15px"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row py-3 d-none d-md-flex">
                                                    <div class="col-4">
                                                        <div class="font-weight-bold align-self-center">
                                                            <span>Group of {{ $persons['min'] }}{{ (($persons['min'] == $persons['max'])? '' : ' - '.$persons['max']) }} (Per Person) </span>
                                                            <a class="personMessageShow personMessageShow{{$kper}} text-primary small"></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small font-weight-bold ">
                                                            <span>{{ webCurrencyConverter(amount: $persons['price']??0) }}</span><br>
                                                            <span class="person_total_amounts{{$kper}} person_total_amounts"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-center">
                                                        <a class="px-3 py-1 btn--primary rounded-pill cursor-pointer person_use_add person_use_add{{$kper}}  per_specel_use_add{{$persons['id']}}" onclick="$('.person_use_book').addClass('d-none');$('.person_use_input').val(0);$('.person_use_book{{$kper}}').removeClass('d-none');$('.person_use_input{{$kper}}').val('{{$persons['min']}}');$('.person_use_add').removeClass('d-none');$('.person_use_add{{$kper}}').addClass('d-none');newperPerson_Calculation('',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}">add</a>
                                                        <div>
                                                            <div class="small person_use_book person_use_book{{$kper}} d-none" style="display: inline-flex;">
                                                                <a class="cab-button-plus-minus " onclick="newperPerson_Calculation('de',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}"><i class="tio-remove" style="font-size: 15px;margin-top:15px"></i></a>
                                                                <input type="number" readonly style="width: 39px; height: 33px; border: 1px solid #80808040;" class="text-center person_use_input person_use_input{{ $kper}}" value="1">
                                                                <a class="cab-button-plus-minus " onclick="newperPerson_Calculation('in',`perPerson_package_add_value_{{$persons['id']}}{{$kper}}`,`{{$persons['price']}}`,this)" data-type1="person" data-id="{{$persons['id']}}" data-key="{{$kper}}" data-min="{{$persons['min']}}" data-max="{{$persons['max']}}"><i class="tio-add" style="font-size: 15px;margin-top:15px"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                            @else
                                            <div class="col-12 mt-2 {{ (($getfirst['is_person_use'] == 1)?'d-none':'') }}">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($cab_image ?? ''), type: 'backend-product') }}" style="width: 59px; height: 47px; margin-bottom: 4px;">
                                                        <div class="small"><span>{{$cab_name}}</span></div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small font-weight-bold">
                                                            <span>{{$cab_seats}} seat</span><br>
                                                            @if($getfirst['use_date'] == 0)
                                                            <span>1 cab</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small font-weight-bold" style="display: flex;"> {{ webCurrencyConverter(amount: $cab_price) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="row px-3 tab-booking-total_amount">
                                            <div class="col-12 mt-2">
                                                <hr>
                                            </div>
                                            <div class="col-12 py-2 px-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="font-weight-bold" style="display: flex;">{{ translate('price') }}</div>
                                                    </div>
                                                    <div class="col-4">
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="font-weight-bold product-package-total_amount" style="display: flex;" data-amount="{{$cab_price}}"> {{ webCurrencyConverter(amount: $cab_price) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if(($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4 ) && ($getfirst['is_person_use'] == 0))
                                        @if(!empty($getfirst['package_list_price']) && json_decode($getfirst['package_list_price'], true))
                                        <div class="card my-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 text-center"><span>{{ translate('Package Includes')}}</span></div>
                                                    <div class="col-12">
                                                        <hr>
                                                    </div>
                                                    <div class="col-12 text-center" style="display: ruby;background-color: ghostwhite;">
                                                        @foreach(json_decode($getfirst['package_list_price'], true) as $keyk => $plis)
                                                        @php
                                                        $tourPackages = \App\Models\TourPackage::where('id', $plis['package_id'])->first();
                                                        @endphp
                                                        <div class="px-2">
                                                            <img src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product') }}" style="width: 59px; height: 47px; margin-bottom: 4px;">
                                                            <div class="ico-nem"><span class="small font-weight-bold">{{ $tourPackages['name'] }}</span></div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @elseif($getfirst['is_person_use'] == 1)
                                        <div class="card my-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 text-center">
                                                        <span>{{ translate('Package Includes')}}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <hr>
                                                    </div>
                                                    <div class="col-12 text-center py-2" style="display: ruby;background-color: ghostwhite;">
                                                        <?php $includePackages = json_decode($getfirst['is_included_package'], true); ?>
                                                        @if($includePackages['sightseen'] == 1)
                                                        <div class="px-2">
                                                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/sightseeing.png') }}"
                                                                style="width: 49px; height: 42px; margin-bottom: 4px;">
                                                            <div class="ico-nem"><span
                                                                    class="small font-weight-bold">{{ translate('sightseeing') }}</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @if($includePackages['cab'] == 1)
                                                        <div class="px-2">
                                                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/car.png') }}"
                                                                style="width: 49px; height: 42px; margin-bottom: 4px;">
                                                            <div class="ico-nem"><span
                                                                    class="small font-weight-bold">{{ translate('transportion') }}</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @if($includePackages['food'] == 1)
                                                        <div class="px-2">
                                                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/foods.png') }}"
                                                                style="width: 49px; height: 42px; margin-bottom: 4px;">
                                                            <div class="ico-nem"><span
                                                                    class="small font-weight-bold">{{ translate('Accomadation') }}</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @if($includePackages['hotel'] == 1)
                                                        <div class="px-2">
                                                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/hotel.png') }}"
                                                                style="width: 49px; height: 42px; margin-bottom: 4px;">
                                                            <div class="ico-nem"><span
                                                                    class="small font-weight-bold">{{ translate('hotel') }}</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if ((\App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0) > 0)
                                        <div class="row px-3">
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="col-12 text-end py-2">
                                                <input type="checkbox" onclick="updateProductPrice(`12`)"
                                                    class="wallet_checked" value="1"
                                                    data-amount="{{ \App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0 }}"
                                                    checked>&nbsp;{{ translate('apply_Wallet') }}
                                            </div>
                                        </div>
                                        @endif
                                        <div class="row px-3">
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <form class="needs-validation" action="javascript:" method="post" novalidate id="coupon-code-events-ajax">
                                                    <div class="d-flex form-control rounded-pill ps-3 p-1">
                                                        <img width="24" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg') }}" alt="" onclick="couponList()">
                                                        <input type="hidden" name="user_id" value="{{ auth('customer')->check() ? auth('customer')->user()->id : ($userId->id??'') }}">
                                                        <input type="hidden" name="amount" value="{{ $cab_price }}" class="coupan_amount_min">
                                                        <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100" type="text" name="coupon_code" placeholder="{{ translate('coupon_code') }}" onclick="return (($('.input_code').val() == '')?couponList():'')">
                                                        <button
                                                            class="btn btn--primary rounded-pill text-uppercase py-1 fs-12 coupan_apply_text"
                                                            type="button" id="events-coupon-code">
                                                            {{ translate('apply') }}
                                                        </button>
                                                    </div>
                                                    <div class="invalid-feedback">{{ translate('please_provide_coupon_code') }}</div>
                                                </form>
                                                <span id="route-coupon-events" data-url="{{ url('api/v1/tour/tour-coupon-apply') }}"></span>
                                                <!-- <div class="justify-content-between  mt-3 mb-2 Coupon_apply_discount_css d-none">
                                                        <span class="cart_title">{{ translate('coupon_Discount ') }}</span>
                                                        <span class="cart_value Coupon_apply_discount"> - {{ webCurrencyConverter(amount: 0) }} </span>
                                                    </div> -->
                                                <div class="row  mt-3 mb-2 px-2 Coupon_apply_discount_css d-none">
                                                    <div class="col-8">{{ translate('coupon_Discount') }}</div>

                                                    <div class="col-4 Coupon_apply_discount font-weight-bold"> - {{ webCurrencyConverter(amount: 0) }} </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row px-2">
                                            <div class="col-12 d-none show_user_wallet_amount">
                                                <hr class="my-2">
                                                <div class="row justify-content-between px-2">
                                                    <span class="col-8 cart_title text-success font-weight-bold">
                                                        <img width="20" src="{{ theme_asset(path: 'public/assets/back-end/img/admin-wallet.png') }}" style="margin-top: -9px;"> User Wallet
                                                        <small>({{ webCurrencyConverter(amount: \App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0) }})</small></span>
                                                    <span class="col-4 cart_value text-success user_wallet_amount"> </span>
                                                </div>
                                                <div class="row justify-content-between mt-2 px-2">
                                                    <span class="col-8 cart_title text-success font-weight-bold user_wallet_am_remaining_text font-weight-bold"
                                                        style="color: darkred !important;"></span>
                                                    <span class="col-4 cart_value text-success user_wallet_amount_remaining"
                                                        style="color: darkred !important;"> </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row px-3 mt-2">
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="col-12 mt-2 px-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="font-weight-bold" style="display: flex;">{{ translate('total_price') }}</div>
                                                    </div>
                                                    <div class="col-4">
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="font-weight-bold show_view_amounts" style="display: flex;"> {{ webCurrencyConverter(amount: $cab_price) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--  -->
                                        @if($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                        <div class="row px-3 mt-2 part_full_pay_none {{ (((\App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0) > 0)?'d-none':'')}}">
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="col-6 py-3">
                                                <button type="button" class="btn btn-outline--primary form-control active part_full_pay1 cab-tab-font-header button-padding-five-tan" onclick="paypartnow('full')" data-amount="{{ $cab_price}}"><img width="30" src="{{ theme_asset(path: 'public/assets/back-end/img/cc.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('full')}} ({{ webCurrencyConverter(amount: $cab_price) }})</button>
                                            </div>
                                            <div class="col-6 py-3">
                                                <button type="button" class="btn btn-outline--primary form-control part_full_pay2 cab-tab-font-header button-padding-five-tan" onclick="paypartnow('part')" data-amount="{{ $cab_price}}"><img width="30" src="{{ theme_asset(path: 'public/assets/back-end/img/cash-in-hand.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('part')}} ({{ webCurrencyConverter(amount: ($cab_price/2)) }})</button>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>
                                        @endif
                                        <!--  -->
                                        <div class="pda10 text-center">
                                            @foreach ($payment_gateways_list as $payment_gateway)
                                            <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form" action="{{ route('tour-payment-request', [$getfirst['slug']]) }}" onsubmit="return formcheck_check()">
                                                @csrf
                                                <div class="Details">
                                                    <input type="hidden" name="user_id" value="{{ auth('customer')->check() ? auth('customer')->user()->id : ($userId->id??'') }}">
                                                    <input type="hidden" name="customer_id" value="{{ auth('customer')->check() ? auth('customer')->user()->id : ($userId->id??'') }}">
                                                    <input type="hidden" name="payment_method" value="{{ $payment_gateway->key_name }}">
                                                    <input type="hidden" name="payment_platform" value="web">
                                                    @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                                    <input type="hidden" name="callback" value="{{ $payment_gateway->live_values['callback_url'] }}">
                                                    @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                                    <input type="hidden" name="callback" value="{{ $payment_gateway->test_values['callback_url'] }}">
                                                    @else
                                                    <input type="hidden" name="callback" value="">
                                                    @endif
                                                    <input type="hidden" name="external_redirect_link" value="{{ route('tour.tour-pay-success', [$getfirst['slug'],'lead'=>($getleads['id']??'')]) }}">
                                                    <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                                        <input type="radio" id="{{ $payment_gateway->key_name }}" name="online_payment" class="form-check-input custom-radio" value="{{ $payment_gateway->key_name }}" hidden>
                                                        <img width="30" src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}" alt="" hidden>
                                                        <span class="text-capitalize form-check-label" hidden>
                                                            @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                                            {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                                            @else
                                                            {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                                            @endif
                                                        </span>
                                                    </label>

                                                    <input type="hidden" name="booking_date" value="{{ date('Y-m-d H:i:s') }}">
                                                    <input type="hidden" name="tour_id" value="{{ $getfirst['id'] }}">
                                                    <input type="hidden" name="use_date" value="{{ $getfirst['use_date'] }}">
                                                    <input type="hidden" name='pickup_date' class="pickup_date" value="{{ (explode(' - ', $getfirst['startandend_date'])[0]??'')}}">
                                                    <input type="hidden" name='pickup_time' class="pickup_time" value="{{ $getfirst['pickup_time']??''}}">
                                                    <input type="hidden" name='pickup_address' class="pickup_address" value="{{ $getfirst['pickup_location']??''}}">
                                                    <input type="hidden" name='pickup_lat' class="pickup_lat" value="{{ $getfirst['pickup_lat']??''}}">
                                                    <input type="hidden" name='pickup_long' class="pickup_long" value="{{ $getfirst['pickup_long'] ?? '' }}">

                                                    <input type="hidden" name="package_id" value="{{ ($getleads['package_id']??'') }}">
                                                    <input type="hidden" name="leads_id" value="{{ ($getleads['id']??'') }}">
                                                    <input type="hidden" name="coupon_amount" value="" class='coupon_amount Coupon_apply_discount discount_show' data-discouponamount="0">
                                                    <input type="hidden" name="coupon_id" value="" class='coupon_id Coupon_apply_id'>
                                                    <input type="hidden" class="total_pay_amount" value="{{ $cab_price }}">
                                                    <input type="hidden" name="payment_amount" class="mainProductPriceInput" value="{{ $cab_price }}">


                                                    <input type="hidden" name='part_payment' class="part_payment_type" value='full'>
                                                    <input type="hidden" name='qty' class="qty_order" value='1'>
                                                    @if($getfirst['use_date'] == 1 || $getfirst['use_date'] == 4)
                                                    <input type="hidden" name='available_seat_cab_id' class="available_seat_cab_id" value='0'>
                                                    <input type="hidden" name='totals_seat_cab_id' class="totals_seat_cab_id" value='0'>
                                                    @endif
                                                    <input type="hidden" name='traveller_id' value="{{ ($getfirst['created_id']??0) }}">
                                                    <input type="hidden" name='wallet_type' class="user-wallet-adds">
                                                    <input type="hidden" name="bookings_packages" class="getallproducts">
                                                </div>
                                                <div class="mt-4">
                                                    <button type="submit" class="btn btn--primary btn-block font-weight-bold name_change_continues d-none razer_pay_opens">{{ translate('Proceed_To_Checkout') }}</button>
                                                </div>
                                            </form>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <!-- <div class="d-flex justify-content-between mt-3">
                                        <button class="btn btn--primary" id="prev-tab" disabled>Previous</button>
                                        <button class="btn btn--primary" id="next-tab">Next</button>
                                        <button class="btn btn-success save_allPackage d-none" id="submit-tab" onclick="formcheck()">Book</button>
                                    </div> -->

                            <div class="d-flex justify-content-between mt-3">
                                @if($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                <button class="btn btn--primary cab-tab-font-header button-padding-five-tan" id="prev-tab" disabled>{{ translate('Previous')}}</button>
                                <span id="tab-counter" class="align-self-center">Step 1 of 2</span>
                                <button class="btn btn--primary cab-tab-font-header button-padding-five-tan" id="next-tab">{{ translate('Next')}}</button>
                                @endif
                                <button class="btn btn-success save_allPackage cab-tab-font-header button-padding-five-tan{{ (($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)?'d-none':'') }} name_change_continues  {{ (($getfirst['use_date'] == 1)?'form-control':'') }}" id="submit-tab" onclick="$('.razer_pay_opens').click()">pay now</button>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="coupon-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Coupons</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row g-3" id="modal-body">
            </div>
        </div>
    </div>
</div>

<div class="modal fade show-google-map" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Google Map</h5>
                <button type="button" class="btn btn-danger btn-sm float-end borer mb-2 text-white" data-dismiss="modal" aria-label="Close" style="margin: -32px -22px 0px 0px;" onclick="
        $('.show-google-map').css('display', 'none').attr('aria-hidden', 'true');
        setTimeout(function () {
            if ($('.addOtherpackages').is(':visible')) {
                $('body').addClass('modal-open');
            } else {
                $('body').removeClass('modal-open');
            }
        }, 1000);
    ">x</button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <div id="map"></div>
                    <div id="message"></div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@push('script')

<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}" type="text/javascript"></script>


<script>
    function mapShow() {
        $('.show-google-map').modal({
            backdrop: 'static',
            keyboard: false
        });

    }

    function calculateDistance() {
        <?php if ($getfirst['is_person_use'] == 1) { ?>
            let PerHeads = MultiArrayPush.find(item => item.type === 'per_head');
            if (PerHeads) {} else {
                MultiArrayPush.push({
                    type: 'per_head',
                    id: 0,
                    qty: 0,
                    price: 0,
                });
            }
        <?php } ?>


        const lat1 = parseFloat("{{ $getfirst['lat'] }}");
        const lng1 = parseFloat("{{ $getfirst['long'] }}");

        const lat2 = parseFloat($('.pickup_lat').val());
        const lng2 = parseFloat($('.pickup_long').val());

        if (lat2 && lng2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLng = (lng2 - lng1) * (Math.PI / 180);

            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = (Math.ceil((R * c) * 100) / 100) //Math.ceil(R * c);

            let way_type = $('.out_side_div:checked').val();
            const freeDistance = 20; // Free distance in km
            const perKmCharge = parseFloat($('.out_side_div:checked').data('ex_distance')) || 0;

            let ex_distance = 0;
            let additionalCharge = 0;

            let existingCab = MultiArrayPush.find(item => item.type === 'cab');
            if (distance > freeDistance) {
                ex_distance = distance - freeDistance;
                additionalCharge = ex_distance * perKmCharge;
            } else {
                ex_distance = distance;
            }

            if (existingCab && "{{ $getfirst['use_date']}}" == 0) {
                additionalCharge = parseFloat(existingCab['qty']) * parseFloat(additionalCharge);
            }

            if (way_type === 'two_way') {
                additionalCharge *= 2;
                ex_distance *= 2;
            }

            let existingItem = MultiArrayPush.find(item => item.type === 'ex_distance');


            // var getexCharges = $('.getAddress_google').data('ex-charge-driver');
            // if (getexCharges > 0) {
            //     additionalCharge += getexCharges;
            // }


            if (existingItem) {
                existingItem.qty = (Math.ceil(ex_distance * 100) / 100) || 0;
                existingItem.price = (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0;
            } else {
                MultiArrayPush.push({
                    type: 'ex_distance',
                    id: '0',
                    qty: (Math.ceil(ex_distance * 100) / 100) || 0,
                    price: (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0
                });
            }

            let existingItemRoute = MultiArrayPush.find(item => item.type === 'route');

            let types = $('.extracharges-transport:checked').data('type')
            if (types && "{{ $getfirst['is_person_use']}}" == 1) {
                way_type = types;
            }
            if (existingItemRoute) {
                existingItemRoute.price = way_type ?? "two_way";
            } else {
                MultiArrayPush.push({
                    type: 'route',
                    id: 0,
                    qty: 0,
                    price: way_type ?? "two_way",
                });
            }
            console.log(MultiArrayPush);
        }
    }
</script>


<script>
    function paypartnow(type) {
        if (type == 'part') {

        } else {

        }

        let amount = $('.part_full_pay2').data('amount');
        if (type == 'part') {
            $('.part_full_pay1').removeClass('active');
            $('.part_full_pay2').addClass('active');
            $(".mainProductPriceInput").val(amount / 2);
            $('.part_payment_type').val('part');
        } else {
            $('.part_full_pay1').addClass('active');
            $('.part_full_pay2').removeClass('active');
            $(".mainProductPriceInput").val(amount);
            $('.part_payment_type').val('full');
        }
    }

    function formcheck_check() {
        let pickup_lat = $('.pickup_lat').val().trim();
        let pickup_long = $('.pickup_long').val().trim();
        if (!pickup_lat) {
            add_all_package()
            return false
        }
        if (!pickup_long) {
            add_all_package()
            return false
        }

        if ("{{$getfirst['use_date']}}" == "1") {
            let rowdata = $('.getallproducts').val().trim();
            if (!rowdata) {
                toastr.error('please select a valid package');
                return false
            }

        }
        return true;
    }

    function formcheck() {
        var pickup_address = $('.pickup_address').val().trim();
        var pickup_date = $('.pickup_date').val();
        var pickup_time = $('.pickup_time').val();
        $('.pick_up-input').removeClass('is-invalid');
        $('.hasDatepicker').removeClass('is-invalid');
        $('.pickupopen_time').removeClass('is-invalid');
        calculateDistance();
        let checkvalid = true;
        if (!pickup_address) {
            $('.pick_up-input').focus();
            $('.pick_up-input').addClass('is-invalid');
            checkvalid = false;
        }
        if (!pickup_date) {
            $('.hasDatepicker').focus();
            $('.hasDatepicker').addClass('is-invalid');
            checkvalid = false;
        }
        if (!pickup_time) {
            $('.pickupopen_time').focus();
            $('.pickupopen_time').addClass('is-invalid');
            checkvalid = false;
        }
        if (checkvalid) {
            // $(".addOtherpackages").modal('hide');

            $.ajax({
                url: "{{ route('tour.booking-tab-amount')}}",
                data: {
                    item: MultiArrayPush,
                    id: "{{ $getfirst['id']}}"
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "post",
                success: function(data) {
                    let html = ``;
                    let amount = 0;
                    let cab_price = 0;
                    let array_use = ['route'];
                    $.each(data.data, function(index, key) {
                        <?php if ($getfirst['is_person_use'] == 0) { ?>
                            // if (key.type != 'ex_distance') {
                            if (!array_use.includes(key.type)) {
                                html += `<div class="col-12 mt-2">
                                    <div class="row">
                                        <div class="col-4">`;
                                if (key.type == 'cgst' || key.type == 'sgst' || key.type == 'ex_distance') {
                                    html += `<div class="small font-weight-bold"><span>${key.title}</span></div>`;
                                } else {
                                    html +=`<img src="${key.image}" style="width: 59px; height: 47px; margin-bottom: 4px;">
                                            <div class="small">                                            
                                                <span>${key.name}</span>
                                            </div>`;
                                }
                                    html += `</div>
                                        <div class="col-4">
                                        `;
                                if (key.type == 'cgst' || key.type == 'sgst') {
                                }else if(key.type == 'ex_distance'){
                                    html +=`<div class="small font-weight-bold">
                                                <span>${((key.qty > 20)? key.qty+' KM':'')}</span>
                                            </div>`;
                                } else {
                                    html +=`<div class="small font-weight-bold">
                                                <span>${((key.type == 'cab')? key.seats+' seats':'')} </span><br><span>${key.qty} ${((key.type == 'cab'  && ("{{$getfirst['use_date']}}" == '0'))?'cab':'People')}</span>
                                            </div>`;
                                }
                                    html += `</div>
                                        <div class="col-4">
                                            <div class="small font-weight-bold my-2" styles="display: flex;"> 
                                             ${ (Number(key.price)).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} ) }`;

                                if (key.type === 'cab' && key.ExChargeAmount && !isNaN(parseFloat(key.ExChargeAmount))) {
                                    html += `<br><small style="width: -webkit-fill-available;">(Ex. distance charge ${parseFloat(key.ExChargeAmount)})</small>`;
                                }
                                html += `</div>
                                        </div>
                                    </div>
                                </div>`;
                                amount = Number(key.price) + Number(amount);
                                if (key.type == 'cab') {
                                    cab_price = Number(key.price) + Number(cab_price);
                                }
                            }
                        <?php } else { ?>
                            if (!array_use.includes(key.type)) {
                                html += `<div class="col-12 mt-2">
                                    <div class="row">
                                        <div class="col-6">`;
                                if (key.type == 'cgst' || key.type == 'sgst' || key.type == 'transport') {
                                    html += `<div class="small font-weight-bold"><span>${key.title}</span></div>`;
                                } else {
                                    html += `<div class="small font-weight-bold"><span>${key.qty} ${key.title}</span></div>`;
                                }
                                html += `</div>
                                        <div class="col-6 text-center">
                                            <div class="small font-weight-bold my-2"> 
                                             ${ (Number(key.price)).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} ) } ${((key.type == 'tax')? key.title :'')}
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                                amount = Number(key.price) + Number(amount);
                            }
                        <?php } ?>
                    })
                    $(".tab-booking-data").html(html);
                    $('.total_pay_amount').val(amount);
                    $('.mainProductPriceInput').val(amount);
                    $('.coupan_amount_min').val(amount);


                    $(".getallproducts").val(JSON.stringify(data.data));
                    $(".tab-booking-total_amount").html(`<div class="col-12 mt-2">
                                    <hr>
                                </div><div class="col-12 py-2 px-3">
                                    <div class="row">
                                        <div class="col-4">
                                        <div class="font-weight-bold" style="display: flex;">{{ translate('price') }}</div>
                                        </div>
                                        <div class="col-4">
                                         </div>
                                        <div class="col-4">
                                            <div class="font-weight-bold product-package-total_amount" style="display: flex;" data-amount="${amount}"> ${amount.toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})} </div>
                                        </div>
                                    </div>
                                </div>`);

                    if ($(".coupon_id").val() > 0) {
                        apply_coupon()
                    }
                    updateProductPrice()
                }
            });
            return checkvalid;
        } else {
            return checkvalid;
        }
    }
</script>

<script>
    updateProductPrice();

    function updateProductPrice(lead_id = null) {

        var amount = $('.total_pay_amount').val();
        var coupon = $('.coupon_amount').val();

        let totalPrice = 0;
        totalPrice = Number(amount) - Number(coupon);
        $(".mainProductPriceInput").val(totalPrice);
        $('.part_full_pay1').addClass('active');
        $('.part_full_pay1,.part_full_pay2').data('amount', totalPrice);
        $('.part_full_pay2').removeClass('active');
        $('.part_full_pay1').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cc.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('full')}} ${(totalPrice).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
        $('.part_full_pay2').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cash-in-hand.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('part')}} ${(totalPrice/2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
        ///////////////////////////////////////////////////////////
        var isChecked = $('.wallet_checked').prop('checked');
        let walletAmount = $('.wallet_checked').data('amount');

        if (isChecked) {
            var type = $('.wallet_checked').val();
            $(".show_user_wallet_amount").removeClass('d-none');
            $(".user-wallet-adds").val(1);
            if (walletAmount >= totalPrice) {
                $(".name_change_continues").text(`{{ translate('book_now') }}`);
                $(".user_wallet_amount_remaining").text('');
                $(".user_wallet_amount").text(
                    `${(totalPrice).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                );
                $(".user_wallet_am_remaining_text").text('');
                $('.final_amount_pay,.show_view_amounts').text(
                    `${(0.00).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`);
            } else {
                $(".user_wallet_amount").text(
                    `${(walletAmount).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                );
                $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout') }}`);
                let remainingAmount = totalPrice - walletAmount;
                let formattedAmount = remainingAmount.toLocaleString("en-US", {
                    style: "currency",
                    currency: "{{ getCurrencyCode() }}"
                });
                $(".user_wallet_amount_remaining").text(`- ${formattedAmount}`);
                $(".user_wallet_am_remaining_text").text("{{ translate('remaining_amount') }}");
                $('.final_amount_pay,.show_view_amounts').text(
                    `${formattedAmount.toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                );
            }
            $('.part_full_pay_none').addClass('d-none');
            $(".part_payment_type").val('full');
        } else {
            $('.part_full_pay_none').removeClass('d-none');
            $(".part_payment_type").val('full');

            $(".user-wallet-adds").val(0);
            $(".show_user_wallet_amount").addClass('d-none');
            $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout') }}`);
            $(".user_wallet_amount_remaining").text('');
            $(".user_wallet_am_remaining_text").text('');
            $('.final_amount_pay,.show_view_amounts').text(
                `${totalPrice.toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`);
        }
        if ("{{$getfirst['use_date']}}" == 0 || "{{$getfirst['use_date']}}" == 1) {
            calculateDistance();
        }

        ///////////////////////////////////////////////////////////////
        // $('#mainProductPrice').text(`₹${(parseFloat(totalPrice)).toFixed(2)}`);
        // $('#mainProductPrice').data('price', totalPrice);
        // $(".mainProductPriceInput").val(totalPrice);

    }
</script>

<script>
    $('#events-coupon-code').on('click', function() {
        apply_coupon();
    });

    function apply_coupon() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: $('#route-coupon-events').data('url'),
            data: $('#coupon-code-events-ajax').serializeArray(),
            success: function(data) {
                let messages = data.message;
                if (data.status == 1) {
                    $(".coupan_apply_text").text("{{ translate('applied') }}");
                    $(".coupon_amount").val(data.data['coupon_amount']);
                    $(".discount_show").data('discouponamount', data.data['coupon_amount']);
                    $(".coupon_id").val(data.data['coupon_id']);
                    $("#mainProductPriceInput").val(data.data['final_amount']);
                    $('.show_view_amounts').text(`${Number(data.data['final_amount']).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} )}`);
                    $(".Coupon_apply_discount").text(`- ${Number(data.data['coupon_amount']).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} )}`);
                    $(".Coupon_apply_discount_css").addClass('d-flex');
                    $(".Coupon_apply_discount_css").removeClass('d-none');
                    toastr.success(messages, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                } else {
                    $(".coupan_apply_text").text("{{ translate('apply') }}");
                    $(".coupon_amount").val(0);
                    $(".coupon_id").val('');
                    $('.input_code').val('');
                    $("#mainProductPriceInput").val("{{$cab_price}}");
                    $('.show_view_amounts').text(`{{ webCurrencyConverter(amount: ($cab_price??0)) }}`);
                    $(".Coupon_apply_discount").text('');
                    $(".discount_show").data('discouponamount', 0);

                    $(".Coupon_apply_discount_css").addClass('d-none');
                    $(".Coupon_apply_discount_css").removeClass('d-flex');
                    toastr.error(messages, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
                updateProductPrice();
            }
        });
    }
</script>
<script>
    $(document).ready(function() {
        datePicker();
        window.$timepicker = $('.pickupopen_time').timepicker({
            uiLibrary: 'bootstrap4',
            format: 'hh:MM TT',
            modal: true,
            footer: true
        });
    });
</script>
<script>
    function datePicker() {
        var today = new Date();
        var tomorrow = new Date(today);
        tomorrow.setDate(today.getDate());
        $('.hasDatepicker').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            modal: true,
            footer: true,
            minDate: tomorrow,
            todayHighlight: true
        });
    }
</script>

<script>
    if ("{{ $getfirst['use_date'] }}" == 0) {
        if ("{{$getfirst['cities_tour']}}" == 0) {
            const inputElement = $('input[type="text"].getAddress_google')[0];
            const autocomplete = new google.maps.places.Autocomplete(inputElement, {
                componentRestrictions: {
                    country: "IN"
                }
            });
            // const inputElement = $('input[type="text"].getAddress_google')[0];
            // const autocomplete = new google.maps.places.Autocomplete(inputElement, {
            //     types: ['establishment']
            // });
            $(".getAddress_google").on('input', function() {
                if ($(this).val().length < 2) {
                    $('.pickup_address').val('');
                    $('.pickup_lat').val('');
                    $('.pickup_long').val('');
                }

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        $(this).val('');
                        return;
                    }
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();

                    $('.pickup_address').val($('.pick_up-input').val());
                    $('.pickup_lat').val(lat);
                    $('.pickup_long').val(lng);
                    initMap();
                });
            });
        } else {
            const inputElement = document.querySelector(".getAddress_google");
            const autocomplete = new google.maps.places.Autocomplete(inputElement, {
                componentRestrictions: {
                    country: "IN"
                }
            });

            const userLat = parseFloat("{{ $getfirst['lat'] }}");
            const userLng = parseFloat("{{ $getfirst['long'] }}");
            const maxDistance = 20000; // 20 km in meters

            const originalPlaceholder = inputElement.placeholder;

            // Listen for input changes (improved)
            $(".getAddress_google").on('input', function() {
                if ($(this).val().length < 2) {
                    // clearFields();
                }
            });


            autocomplete.addListener("place_changed", function() {
                const place = autocomplete.getPlace();

                if (!place.geometry) {
                    clearFields("Address Not Found");
                    return;
                }

                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                const distance = getDistanceFromLatLonInMeters(userLat, userLng, lat, lng);
                console.log(distance);
                if (isNaN(distance)) { // Check for invalid coordinates
                    clearFields("Invalid Coordinates");
                    return;
                }
                if (distance > maxDistance) {
                    // Revert to the original placeholder and clear the fields
                    inputElement.placeholder = originalPlaceholder; // Restore placeholder
                    clearFields("Address beyond " + (maxDistance / 1000) + " km radius"); // Clear and provide a reason
                    $(".address_error_message").text(`{{ translate("Pickup will be done only from Hotels, Restaurants, Railway stations, Bus stations within The City")}}.`).fadeIn(400).delay(3000).fadeOut(4000);
                    inputElement.value = ""; // Clear the input field as well
                } else {
                    $(".address_error_message").text('');
                    $(".pickup_address").val(place.formatted_address); // No need to add "(Available)"
                    $(".pickup_lat").val(lat);
                    $(".pickup_long").val(lng);
                    inputElement.value = place.formatted_address; // Set the input field value
                    inputElement.placeholder = originalPlaceholder; // Restore placeholder
                    initMap();

                }
            });

            // ... (getDistanceFromLatLonInMeters and degToRad functions remain the same)

            function clearFields(message = '') {
                $(".pickup_address").val(message);
                $(".pickup_lat").val('');
                $(".pickup_long").val('');
                //  Don't clear the input field immediately on short input, let autocomplete suggest
                if (message !== "Address Not Found" && message !== "Invalid Coordinates") {
                    $(".getAddress_google").val(""); // Clear only if an error message is not being displayed
                }
            }

            function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
                const R = 6371000; // Earth's radius in meters
                const dLat = degToRad(lat2 - lat1);
                const dLon = degToRad(lon2 - lon1);
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(degToRad(lat1)) * Math.cos(degToRad(lat2)) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            function degToRad(deg) {
                return deg * (Math.PI / 180);
            }

        }



        /////////////////////////

        // let map, marker, circle, autocomplete, messageBox, polygon;

        // var map, marker, circle;

        // function initMap() {
        //     var defaultLocation = {
        //         lat: parseFloat("{{ $getfirst['lat'] }}"),
        //         lng: parseFloat("{{ $getfirst['long'] }}")
        //     };

        //     // Initialize the map
        //     map = new google.maps.Map(document.getElementById("map"), {
        //         center: defaultLocation,
        //         zoom: 13
        //     });

        //     marker = new google.maps.Marker({
        //         map: map,
        //         draggable: true,
        //         visible: false
        //     });

        //     // Initialize the circle
        //     circle = new google.maps.Circle({
        //         map: map,
        //         radius: 10000,
        //         center: defaultLocation,
        //         fillColor: "#FF0000",
        //         fillOpacity: 0.3,
        //         strokeColor: "#FF0000",
        //         strokeOpacity: 0.8,
        //         strokeWeight: 2
        //     });


        //     updateMapBasedOnCheckbox();


        //     var input = document.getElementById('search-box');
        //     var autocomplete = new google.maps.places.Autocomplete(input);
        //     autocomplete.bindTo('bounds', map);
        //     autocomplete.setComponentRestrictions({
        //         country: []
        //     });

        //     autocomplete.addListener('place_changed', function() {
        //         var place = autocomplete.getPlace();
        //         if (!place.geometry) {
        //             toastr.error("Place details not available.");
        //             return;
        //         }

        //         var placeLocation = place.geometry.location;


        //         // if ($('.check_out_side').is(':checked')) {
        //             map.setCenter(placeLocation);
        //             marker.setPosition(placeLocation);
        //             marker.setVisible(true);
        //             toastr.success("Authenticated location!");
        //             $('.pickup_lat').val(placeLocation.lat());
        //             $('.pickup_long').val(placeLocation.lng());
        //             $('.pickup_address').val($('.pick_up-input').val());
        //             // if ($('.check_out_side').is(':checked')) {

        //                 calculateDistance();
        //             // }

        //             // return;
        //         // }

        //         // if (circle && circle.getMap() && google.maps.geometry.spherical.computeDistanceBetween(placeLocation, circle.getCenter()) <= circle.getRadius()) {
        //         //     map.setCenter(placeLocation);
        //         //     marker.setPosition(placeLocation);
        //         //     marker.setVisible(true);
        //         //     toastr.success("Authenticated location!");
        //         //     $('.pickup_lat').val(placeLocation.lat());
        //         //     $('.pickup_long').val(placeLocation.lng());
        //         //     $('.pickup_address').val($('.pick_up-input').val());
        //         //     calculateDistance();

        //         // } else {
        //         //     toastr.error("Un-authenticated location!");
        //         //     marker.setVisible(false);
        //         //     $('.pickup_lat').val('');
        //         //     $('.pickup_long').val('');
        //         //     $('.pickup_address').val('');
        //         // }
        //     });

        // }

        // function updateMapBasedOnCheckbox() {
        //     // if ($('.check_out_side').is(':checked')) {

        //         if (circle) circle.setMap(null); // Remove circle
        //         map.setZoom(8);
        //         map.setCenter({
        //             lat: parseFloat("{{ $getfirst['lat'] }}"),
        //             lng: parseFloat("{{ $getfirst['long'] }}")
        //         });
        //         marker.setPosition({
        //             lat: parseFloat("{{ $getfirst['lat'] }}"),
        //             lng: parseFloat("{{ $getfirst['long'] }}")
        //         });
        //         marker.setVisible(true);
        //     // } else {
        //     //     if (circle) circle.setMap(map);
        //     //     map.setZoom(13);
        //     //     map.setCenter({
        //     //         lat: parseFloat("{{ $getfirst['lat'] }}"),
        //     //         lng: parseFloat("{{ $getfirst['long'] }}")
        //     //     });
        //     // }
        // }

        // // Event listener for checkbox change to update the map
        // $('.check_out_side').change(function() {
        //     updateMapBasedOnCheckbox();
        // });

        // // Event listener for city search (ensuring correct map handling)
        // function searchCity() {
        //     let cityName;
        //     // if ($('.check_out_side').is(':checked')) {
        //     //     cityName = "{{ $getfirst['state_name'] }}";
        //     // } else {
        //         cityName = document.getElementById('city').value;
        //     // }

        //     var geocoder = new google.maps.Geocoder();
        //     geocoder.geocode({
        //         address: cityName
        //     }, function(results, status) {
        //         if (status === 'OK') {
        //             var cityLocation = results[0].geometry.location;
        //             map.setCenter(cityLocation);
        //             marker.setPosition(cityLocation);
        //             marker.setVisible(true);

        //             // Remove any previous circle and add a new one if needed
        //             if (circle) circle.setMap(null);

        //             // Create a new circle around the city location
        //             circle = new google.maps.Circle({
        //                 map: map,
        //                 center: cityLocation,
        //                 radius: 10000, // Radius in meters
        //                 fillColor: '#5aaf548a',
        //                 fillOpacity: 0.3,
        //                 strokeColor: '#5aaf548a',
        //                 strokeOpacity: 0.8,
        //                 strokeWeight: 2
        //             });

        //             map.addListener('click', function(event) {
        //                 var distance = google.maps.geometry.spherical.computeDistanceBetween(event.latLng, circle.getCenter());
        //                 if (distance <= circle.getRadius()) {
        //                     marker.setPosition(event.latLng);
        //                     marker.setVisible(true);
        //                 } else {
        //                     toastr.error('Please click within the circle boundary.');
        //                 }
        //             });
        //         } else {
        //             alert('City not found: ' + status);
        //         }
        //     });
        // }

        // // Load the map after the window is loaded
        // google.maps.event.addDomListener(window, 'load', initMap);
    } else if ("{{ $getfirst['use_date'] }}" == 2 || "{{ $getfirst['use_date'] }}" == 3 || "{{ $getfirst['use_date'] }}" == 4) {
        // const inputElement = $('input[type="text"].getAddress_google')[0];
        // const autocomplete = new google.maps.places.Autocomplete(inputElement, {
        //     types: ['establishment']
        // });
        // // const inputElement = $('input[type="text"].getAddress_google')[0];
        // // const autocomplete = new google.maps.places.Autocomplete(inputElement, {
        // //     types: ['establishment']
        // // });
        // $(".getAddress_google").on('input', function() {
        //     if ($(this).val().length < 2) {
        //         $('.pickup_address').val('');
        //         $('.pickup_lat').val('');
        //         $('.pickup_long').val('');
        //     }

        //     autocomplete.addListener('place_changed', () => {
        //         const place = autocomplete.getPlace();
        //         if (!place.geometry) {
        //             $(this).val('');
        //             return;
        //         }
        //         const lat = place.geometry.location.lat();
        //         const lng = place.geometry.location.lng();

        //         $('.pickup_address').val($('.pick_up-input').val());
        //         $('.pickup_lat').val(lat);
        //         $('.pickup_long').val(lng);
        //     });
        // });

        $(document).ready(function() {
            const inputElement = $('input[type="text"].getAddress_google')[0];

            const centerLatLng = {
                lat: "{{ $getfirst['lat'] }}",
                lng: "{{ $getfirst['long'] }}"
            }; // Example: New Delhi, India

            const autocomplete = new google.maps.places.Autocomplete(inputElement, {
                types: ['establishment'],
                // componentRestrictions: { country: 'IN' },
            });

            // Set bounds with a 200km radius
            const circle = new google.maps.Circle({
                center: centerLatLng,
                radius: 200000, // 200 km in meters
            });

            autocomplete.setBounds(circle.getBounds());

            $(".getAddress_google").on('input', function() {
                if ($(this).val().length < 2) {
                    $('.pickup_address').val('');
                    $('.pickup_lat').val('');
                    $('.pickup_long').val('');
                }
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    $('.getAddress_google').val('');
                    return;
                }

                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();

                const distance = getDistanceFromLatLonInKm(centerLatLng.lat, centerLatLng.lng, lat, lng);

                if (distance > 200) {
                    $('.address_error_message').text('Please select a location within 200km range.').delay(2000).fadeOut(500).fadeIn(200).fadeOut(300);
                    $('.getAddress_google').val('');
                    $('.pickup_address').val('');
                    $('.pickup_lat').val('');
                    $('.pickup_long').val('');
                } else {
                    $('.pickup_address').val($('.getAddress_google').val());
                    $('.pickup_lat').val(lat);
                    $('.pickup_long').val(lng);
                    calculateDistance();
                    initMap();
                    driverandCabExCharge();
                }
            });

            function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
                const R = 6371; // Radius of Earth in km
                const dLat = deg2rad(lat2 - lat1);
                const dLon = deg2rad(lon2 - lon1);
                const a =
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c; // Distance in km
            }

            function deg2rad(deg) {
                return deg * (Math.PI / 180);
            }
        });


    } else {
        function initMap() {
            var location = {
                lat: parseFloat("{{ $getfirst['pickup_lat'] ?? '0' }}"),
                lng: parseFloat("{{ $getfirst['pickup_long'] ?? '0' }}")
            };
            var map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
                center: location,
            });
            var marker = new google.maps.Marker({
                position: location,
                map: map,
            });
        }
        initMap();
    }
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll('#tab-navigation .nav-link');
        const tabContents = document.querySelectorAll('.tab-pane');
        const nextButton = document.getElementById('next-tab');
        const prevButton = document.getElementById('prev-tab');
        const submitButton = document.getElementById('submit-tab');
        const tabCounter = document.getElementById('tab-counter');
        let currentTab = 0;

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', function(event) {
                event.preventDefault();
            });
        });

        function updateTabs() {
            tabs.forEach((tab) => tab.classList.remove('active'));
            tabContents.forEach((content) => content.classList.remove('show', 'active'));

            tabs[currentTab].classList.add('active');
            tabContents[currentTab].classList.add('show', 'active');

            prevButton.disabled = currentTab === 0;
            if (currentTab === tabs.length - 1) {
                nextButton.classList.add('d-none');
                submitButton.classList.remove('d-none');
            } else {
                nextButton.classList.remove('d-none');
                submitButton.classList.add('d-none');
            }

            // Update the tab counter
            tabCounter.textContent = `Step ${currentTab + 1} of ${tabs.length}`;
            var title_name_view = $('.nav-link.navlinks.active').parent().contents().filter(function() {
                return this.nodeType === 3;
            }).text().trim();
            console.log($('.nav-link.navlinks.active'));
            $('.title_show_names').text(title_name_view);
        }

        nextButton.addEventListener('click', function() {
            if (currentTab < tabs.length - 1) {
                if (currentTab === tabs.length - 2) {
                    if (!formcheck()) {
                        return;
                    }
                }
                currentTab++;
                updateTabs();
            }
        });

        prevButton.addEventListener('click', function() {
            if (currentTab > 0) {
                currentTab--;
                updateTabs();
            }
        });

        updateTabs();
    });
</script>

<script>
    // function calculateDistance() {
    //     const lat1 = parseFloat("{{ $getfirst['lat'] }}");
    //     const lng1 = parseFloat("{{ $getfirst['long'] }}");
    //     const lat2 = $('.pickup_lat').val();
    //     const lng2 = $('.pickup_long').val();
    //     if (lat2 && lng2) {
    //         const R = 6371;
    //         const dLat = (lat2 - lat1) * (Math.PI / 180);
    //         const dLng = (lng2 - lng1) * (Math.PI / 180);
    //         const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    //             Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
    //             Math.sin(dLng / 2) * Math.sin(dLng / 2);
    //         const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    //         const distance = R * c; // Math.ceil()
    //         ///////////////////////////////////////////////////////////////////////////////////////

    //         var way_type = $('.out_side_div:checked').val();
    //         const freeDistance = 10; // Free distance in km
    //         const perKmCharge = $('.out_side_div:checked').data('ex_distance');

    //         let ex_distance = 0;
    //         let additionalCharge = 0;

    //         let existingcab = MultiArrayPush.find(item => item.type === 'cab');

    //         if (distance > freeDistance) {
    //             ex_distance = (distance - freeDistance);
    //             additionalCharge = (distance - freeDistance) * perKmCharge;
    //         } else {
    //             ex_distance = distance;
    //         }
    //         if (existingcab) {
    //             additionalCharge = parseFloat(existingcab['qty']) * parseFloat(additionalCharge);
    //         }
    //         if (way_type == 'two_way') {
    //             additionalCharge = parseFloat(additionalCharge) + parseFloat(additionalCharge);
    //             ex_distance = parseFloat(ex_distance) + parseFloat(ex_distance);
    //         }

    //         let existingItem = MultiArrayPush.find(item => item.type === 'ex_distance');

    //         if (existingItem) {
    //             existingItem.qty = ex_distance || 0;
    //             existingItem.price = parseFloat(additionalCharge) || 0;
    //         }
    //         /////////////////////////////////////////////////////////////////////////////////
    //         console.log(MultiArrayPush);
    //     }
    // }
    function toRad(degrees) {
        return degrees * (Math.PI / 180);
    }
</script>

@if($getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
<script>
    <?php if ($getfirst['is_person_use'] == 0) { ?>
        setTimeout(() => {
            // var pprice = parseFloat("{{ ($cab_price??0) + ($packages_price??0) }}");
            var pprice = parseFloat("{{ ($cab_price??0) }}");
            var pcab_id = parseFloat("{{ $cab_ids }}");
            sub_qtys("cab", pcab_id, 1, pprice);
        }, 2000);

    <?php } ?>

    function newaddpackages_daliy(inde, id, amount, that) {
        let input = $(`.${id}`).val();
        // data-cab_amount="{{-- $s_price[$kk] --}}" 
        // data-="{{-- $s_seats[$kk] --}}"
        // data-="{{-- ($packages_price??'') --}}" 
        let cabAmount = 0;
        let packageAmount = 1;
        if ("{{$getfirst['use_date']}}" == "4") {
            packageAmount = amount;
        } else {
            cabAmount = $(that).data('cab_amount');
            packageAmount = $(that).data('ex_packages_amount');
        }

        let cabMaxSeat = $(that).data('cab_max_seat');
        let errorspan = $(that).data('error-spans');
        if (inde == 'in') {
            if (cabMaxSeat > input) {
                $(`.${id}`).val((Number(input) + 1));
                $(`.cab-select-error-${errorspan}`).text('');
            } else {
                $(`.${id}`).val(cabMaxSeat);
                $(`.cab-select-error-${errorspan}`).text(`Only ${cabMaxSeat} seats can be booked in this cab`).fadeIn(200).delay(2000).fadeOut(1000);
            }
        } else {
            if (input > 1) {
                $(`.${id}`).val((Number(input) - 1))
            }
        }
        let inputSeats = $(`.${id}`).val();


        let requiredCabs = Math.ceil(inputSeats / cabMaxSeat);
        let totalCabCost = requiredCabs * cabAmount;
        let totalPackageCost = inputSeats * packageAmount;

        let totalPrice = totalCabCost + totalPackageCost;

        // $(".cab_information").text('');
        $(`.cab_information${$(that).data('id')}${amount}`).text((parseFloat(totalPrice)).toLocaleString("en-US", {
            style: "currency",
            currency: "{{getCurrencyCode()}}"
        }));

        let type = $(that).data('type');
        var qty = $(`.${id}`).val();
        var ids = $(that).data('id');

        sub_qtys(type, ids, qty, totalPrice);
    }
</script>
@endif

@if($getfirst['use_date'] == 1)
<script>
    <?php if ($getfirst['is_person_use'] == 0) { ?>
        setTimeout(() => {
            sub_qtys('cab', '{{ reset($s_packageid) }}', 1, '{{ ((reset($s_price)??0) + $packages_price??0) }}');

            <?php if (!empty($getfirst['package_list_price']) && is_array(json_decode($getfirst['package_list_price'], true))) {
                foreach (json_decode($getfirst['package_list_price'], true) as $plis) {
                    $tourPackages = \App\Models\TourPackage::where('id', $plis['package_id'])->first();
            ?>
                    sub_qtys("{{$tourPackages['type']}}", "{{ $plis['package_id'] }}", 1, "{{$plis['pprice']}}");
            <?php  }
            }
            ?>
        }, 2000);
    <?php } ?>

    function handleCabPackageClick(k, sPackageId, cabPrices) {
        $('.cab_add_packagesp').addClass('d-none');
        $('.cab_add_packagesp1').removeClass('d-none');
        $(`.cab_add_packagesp1_${k}`).addClass('d-none');
        $('.cab_add_packagesp_value').val(0);
        $(`.cab_add_packagesp_${k}`).removeClass('d-none');
        $(`.cab_add_packagesp_value_${k}`).val(1);

        $('.spcab_packages_data').each(function() {
            var amo = $(this).data('manamount');
            $(this).text(amo.toLocaleString("en-US", {
                style: "currency",
                currency: "{{getCurrencyCode()}}"
            }));
        });
        var amou = $(`.spcab_packages_data${k}`).data('manamount');
        $(".header_show_seats").text($(`.spcab_packages_data${k}`).data('seats'));
        $(".header_price_change").text(amou.toLocaleString("en-US", {
            style: "currency",
            currency: "{{getCurrencyCode()}}"
        }));


        sub_qtys('cab', sPackageId, 1, cabPrices);
    }
</script>
@endif
<script>
    function newaddpackages(inde, id, amount, that) {
        let input = $(`.${id}`).val();
        let type1 = $(that).data('type1');
        let type = $(that).data('type');

        if (inde == 'in') {
            $(`.${id}`).val((Number(input) + 1))
        } else {
            if (input > 1) {
                $(`.${id}`).val((Number(input) - 1))
            } else if (input == 1 && type1 == 'other') {
                var point = $(that).data('point');
                var button = $(that).data('button');
                $(`.${point}`).addClass('d-none');
                $(`.${button}`).removeClass('d-none');
                $(`.${id}`).val(0)
            }
        }

        if ("{{$getfirst['use_date']}}" == "1" || "{{$getfirst['use_date']}}" == "2" || "{{$getfirst['use_date']}}" == "3") {
            var qty = $(`.${id}`).val();
            var min = $(`.${id}`).data('min_value');

            if (min < qty) {
                $(`.${id}`).val(min);
                toastr.error(`Currently ${min} seats are available`);
            }
        }

        if (type1 == 'cab') {
            $(".cab_information").text('');
            $(`.cab_information${$(that).data('id')}${amount}`).text((parseFloat(amount) * parseInt($(`.${id}`).val())).toLocaleString("en-US", {
                style: "currency",
                currency: "{{getCurrencyCode()}}"
            }));
        } else {
            $(`.other_information${$(that).data('id')}`).text('');
            $(`.other_information${$(that).data('id')}${amount}`).text((parseFloat(amount) * parseInt($(`.${id}`).val())).toLocaleString("en-US", {
                style: "currency",
                currency: "{{getCurrencyCode()}}"
            }));
        }


        var qty = $(`.${id}`).val();
        var ids = $(that).data('id');


        <?php if ($getfirst['use_date'] == 1 || $getfirst['use_date'] == 4) { ?>
            sub_qtys(type, ids, qty, amount);
            <?php if (!empty($getfirst['package_list_price']) && is_array(json_decode($getfirst['package_list_price'], true))) {
                foreach (json_decode($getfirst['package_list_price'], true) as $plis) {
                    $tourPackages = \App\Models\TourPackage::where('id', $plis['package_id'])->first();
            ?>
                    sub_qtys("{{$tourPackages['type']}}", "{{ $plis['package_id'] }}", qty, `{{$plis['pprice']}} * ${qty}`);
            <?php  }
            }
        } else { ?>
            sub_qtys(type, ids, qty, amount);
        <?php } ?>
    }

    const MultiArrayPush = [];
    if ("{{$getfirst['use_date']}}" == 0 || "{{$getfirst['use_date']}}" == 1) {
        calculateDistance();
    }

    function sub_qtys(type, id, qty, price) {
        if (type == 'cab') {
            var total_seats = $(`.cab_add_packagesp_value_${id}`).data('total_seats');
            $(".totals_seat_cab_id").val(total_seats);
        }
        if ("{{$getfirst['use_date']}}" == "1" || "{{$getfirst['use_date']}}" == "4") {
            let index_remmm = MultiArrayPush.findIndex(item => item.type === "ex_distance");
            if (index_remmm !== -1) {
                MultiArrayPush.splice(index_remmm, 1);
            }
            let index_remmm_route = MultiArrayPush.findIndex(item => item.type === "route");
            if (index_remmm_route !== -1) {
                MultiArrayPush.splice(index_remmm_route, 1);
            }
        }
        let existingItem = MultiArrayPush.find(item => item.type === type);
        if (existingItem) {
            if (parseInt(qty) === 0) {
                const index = MultiArrayPush.indexOf(existingItem);
                if (index > -1) {
                    MultiArrayPush.splice(index, 1);
                }
            } else {
                existingItem.id = id;
                existingItem.qty = qty;

                if (("{{$getfirst['use_date']}}" == 2 || "{{$getfirst['use_date']}}" == 3 || "{{$getfirst['use_date']}}" == 4) && type == 'cab') {
                    existingItem.price = (parseFloat(price));
                } else {
                    existingItem.price = (parseFloat(price) * parseInt(qty));
                }
            }
        } else if (qty > 0) {
            let price1 = (parseFloat(price) * parseInt(qty));
            if (("{{$getfirst['use_date']}}" == 2 || "{{$getfirst['use_date']}}" == 3 || "{{$getfirst['use_date']}}" == 4) && type == 'cab') {
                price1 = (parseFloat(price));
            }
            MultiArrayPush.push({
                type,
                id,
                qty,
                price: price1
            });
        }
        calculateDistance();
        driverandCabExCharge();

        if ("{{$getfirst['use_date']}}" == "1" || "{{$getfirst['use_date']}}" == "2" || "{{$getfirst['use_date']}}" == "3" || "{{$getfirst['use_date']}}" == "4") {
            $('.available_seat_cab_id').val(MultiArrayPush[0]['id']);
            $('.qty_order').val(MultiArrayPush[0]['qty']);
            $('.total_pay_amount').val(MultiArrayPush[0]['price']);
            $('.mainProductPriceInput').val(MultiArrayPush[0]['price']);
            $('.part_full_pay1').addClass('active');
            $('.part_full_pay2').removeClass('active');
            $('.part_full_pay1,.part_full_pay2').data('amount', MultiArrayPush[0]['price']);
            $('.part_full_pay1').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cc.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('full')}} ${(MultiArrayPush[0]['price']).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
            $('.part_full_pay2').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cash-in-hand.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('part')}} ${(MultiArrayPush[0]['price']/2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);

            $('.coupan_amount_min').val(MultiArrayPush[0]['price']);
            $(".getallproducts").val(JSON.stringify(MultiArrayPush));

            $(`.spcab_packages_data${id}`).text(MultiArrayPush[0]['price'].toLocaleString("en-US", {
                style: "currency",
                currency: "{{getCurrencyCode()}}"
            }));

            $(".tab-booking-total_amount").html(`<div class="col-12 mt-2">
                                    <hr>
                                </div><div class="col-12 py-2 px-3">
                                    <div class="row">
                                        <div class="col-4">
                                        <div class="font-weight-bold" style="display: flex;">{{ translate('price') }}</div>
                                        </div>
                                        <div class="col-4">
                                         </div>
                                        <div class="col-4">
                                            <div class="font-weight-bold product-package-total_amount" style="display: flex;" data-amount="${MultiArrayPush[0]['price']}"> ${MultiArrayPush[0]['price'].toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})} </div>
                                        </div>
                                    </div>
                                </div>`);

            if ($(".coupon_id").val() > 0) {
                apply_coupon()
            }
            updateProductPrice()
        }

    }
</script>
@if($getfirst['use_date'] == 0 && $getfirst['is_person_use'] == 0)
<script>
    var pprice = parseFloat("{{ $cab_price }}");
    var pcab_id = parseFloat("{{ $cab_ids }}");
    sub_qtys("cab", pcab_id, 1, pprice);
</script>
@endif

<script>
    ! function(e) {
        "undefined" == typeof module ? this.charming = e : module.exports = e
    }(function(e, n) {
        "use strict";
        n = n || {};
        var t = n.tagName || "span",
            o = null != n.classPrefix ? n.classPrefix : "char",
            r = 1,
            a = function(e) {
                for (var n = e.parentNode, a = e.nodeValue, c = a.length, l = -1; ++l < c;) {
                    var d = document.createElement(t);
                    o && (d.className = o + r, r++), d.appendChild(document.createTextNode(a[l])), n.insertBefore(d,
                        e)
                }
                n.removeChild(e)
            };
        return function c(e) {
            for (var n = [].slice.call(e.childNodes), t = n.length, o = -1; ++o < t;) c(n[o]);
            e.nodeType === Node.TEXT_NODE && a(e)
        }(e), e
    });
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
</script>
<script>
    $(function() {
        var owl = $('.slide-one-item');
        $('.slide-one-item').owlCarousel({
            center: false,
            items: 1,
            loop: true,
            stagePadding: 0,
            margin: 0,
            smartSpeed: 1500,
            autoplay: false,
            dots: false,
            nav: false,
            navText: ['<span class="icon-keyboard_arrow_left">',
                '<span class="icon-keyboard_arrow_right">'
            ]
        });

        $('.thumbnail li').each(function(slide_index) {
            $(this).click(function(e) {
                owl.trigger('to.owl.carousel', [slide_index, 1500]);
                e.preventDefault();
            })
        })

        owl.on('changed.owl.carousel', function(event) {
            $('.thumbnail li').removeClass('active');
            $('.thumbnail li').eq(event.item.index - 2).addClass('active');
        })
    })
    ////////////////////////////////////////////////
</script>
{{-- mobile no blur --}}
<script>
    $(document).ready(function() {
        // Initialize all tooltips on the page
        $('[data-toggle="tooltip"]').tooltip('dispose').tooltip();
    });

    $(function() {
        $('.section-link').on('click', function(e) {
            e.preventDefault();
            const targetId = $(this).attr('href');
            const targetOffset = $(targetId).offset().top - $('.navbar_section1').outerHeight() - 100;

            $('html, body').animate({
                scrollTop: targetOffset
            }, 200);
        });

        $(window).on('scroll', function() {
            let screenWidth = $(window).width();
            const scrollTop = $(window).scrollTop() + $('.navbar_section1').outerHeight() + 100;

            if (scrollTop > 900) {
                $('.navbar-stuck-toggler').removeClass('show');
                $('.navbar-stuck-menu').removeClass('show');
                if (screenWidth <= 768) {
                    $(".navbar_section1").css({
                        'top': '0px',
                    });
                } else {
                    $(".navbar_section1").css({
                        'top': '84px',
                    });
                }
                $(".navbar_section1").css({
                    "position": "sticky",
                    'background-color': '#fff',
                    'z-index': '1000',
                    'box-shadow': '0 2px 10px rgba(0, 0, 0, 0.1)',
                    'overflow': 'auto',
                    "text-wrap": "nowrap",
                });
            } else {
                $(".navbar_section1").css({
                    'position': 'static',
                    "text-wrap": "nowrap",
                    'box-shadow': 'none'
                });
            }

            $('.section-content').each(function() {
                const sectionTop = $(this).offset().top - 50;
                const sectionBottom = sectionTop + $(this).outerHeight();
                const sectionId = $(this).attr('id');
                const navLink = $(`.section-link[href="#${sectionId}"]`);

                if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                    $('.section-link').removeClass('active');
                    navLink.addClass('active');
                }
            });
        });
    });


    $(document).ready(function() {
        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop(); // Get current scroll position
            const stickyOffset = 200; // Offset for sticky effect

            if (scrollTop > stickyOffset) {
                $('.navbar-stuck-toggler').removeClass('show');
                $('.navbar-stuck-menu').removeClass('show');
                $('.paystickyset').css({
                    'position': 'sticky',
                    'top': '93px',
                    'right': '3px',
                    'left': '3px',
                    'background-color': '#fff',
                    'z-index': '1000',
                    'box-shadow': '0 2px 10px rgba(0, 0, 0, 0.1)',
                    // 'overflow': 'auto',
                });
            } else {
                $('.paystickyset').css({
                    'position': 'static',
                    'box-shadow': 'none'
                });
            }
        });
    });

    function showPackages(id) {
        const element = document.getElementById(id);
        if (element) {
            if (element.classList.contains('show')) {
                element.classList.remove('show');
            } else {
                $('.collapse_packages').removeClass('show');
                element.classList.add('show');
            }
        }
    }

    function add_all_package() {
        $(".addOtherpackages").modal({
            backdrop: 'static', // Prevents closing on outside click
            keyboard: false // Disables closing with the Esc key
        });
    }
</script>


<script type="module">
    // import {
    //     initializeApp
    // } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js';
    // import {
    //     getMessaging,
    //     getToken
    // } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging.js';

    // const firebaseConfig = {
    //     apiKey: "AIzaSyBNsNd1OSPgjTm9NxX38MZq_pdE5cpUy3A",
    //     authDomain: "manalsoftech-6807e.firebaseapp.com",
    //     projectId: "manalsoftech-6807e",
    //     storageBucket: "manalsoftech-6807e.appspot.com",
    //     messagingSenderId: "1023155540439",
    //     appId: "1:1023155540439:web:8f7f2f268931822bbffb92",
    //     measurementId: "G-EVNBKN5FVB"
    // };
    // const app = initializeApp(firebaseConfig);
    // const messaging = getMessaging(app);

    import {
        initializeApp
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js';
    import {
        getMessaging,
        getToken
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging.js';


    const firebaseConfig = {
        apiKey: "{{ env('FIREBASE_APIKEY') }}",
        authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
        projectId: "{{ env('FIREBASE_PRODJECTID') }}",
        storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
        appId: "{{ env('FIREBASE_APPID') }}",
        measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    // Register Service Worker
    navigator.serviceWorker.register("{{ asset('public/firebase/sw.js') }}")
        .then((registration) => {
            console.log("Service Worker registered successfully:", registration);
            return getToken(messaging, {
                serviceWorkerRegistration: registration,
                vapidKey: "{{ env('VAPID_KEY') }}"
            });
        })
        .then((token) => {
            if (token) {
                console.log("FCM Token:", token);
                $.ajax({
                    url: "{{ url('api/v1/fcm_token_Update') }}",
                    data: {
                        'token': token,
                        'user_id': "{{ auth('customer')->id() ?? 0 }}"
                    },
                    dataType: "json",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {

                    }
                });
            } else {
                console.warn("No FCM token available. Request notification permission.");
            }
        })
        .catch((error) => {
            console.error("Error while retrieving FCM token:", error);
        });
</script>

<script>
    function couponList() {
        let expireDate = "";
        let formattedDate = "";
        let body = "";
        $.ajax({
            type: "post",
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                type: "tour",
            },
            url: "{{ route('coupon.coupon-list-type') }}",
            success: function(response) {
                $('#modal-body').html('');
                if (response.status == 200) {
                    if (response.coupons.length > 0) {
                        $.each(response.coupons, function(key, value) {
                            expireDate = new Date(value.expire_date);
                            formattedDate = expireDate.toLocaleString('en-GB', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            }).replace(" ", ", ");

                            body += `<div class="col-lg-6">
                                        <div class="ticket-box">
                                        <div class="ticket-start">
                                            <img width="30" src="{{ asset('public/assets/front-end/img/icons/dollar.png') }}" alt="">
                                            <h2 class="ticket-amount">${((value.discount_type == 'percentage')?'':'₹')}${value.discount} ${((value.discount_type == 'percentage')?'%':'')}</h2>
                                            <p>On All Tours</p>
                                        </div>
                                        <div class="ticket-border"></div>
                                        <div class="ticket-end">
                                            <button class="ticket-welcome-btn couponid click-to-copy-coupon couponid-${value.code}" data-value="${value.code}" onclick="copyToClipboard(this)">${value.code}</button>
                                            <button
                                                class="ticket-welcome-btn couponid-hide d-none couponhideid-${value.code}">Copied</button>
                                            <h6>Valid till ${formattedDate}</h6>
                                            <p class="m-0">Available from minimum purchase ₹${value.min_purchase}</p>
                                        </div>
                                        </div>
                                    </div>`;
                        });
                        $('#modal-body').append(body);
                        $('#coupon-modal').on('hidden.bs.modal', function() {
                            if ($('.modal.show').length) {
                                $('body').addClass('modal-open');
                            }
                        });
                        $('#coupon-modal').modal('show');
                    } else {
                        body = 'Coupons not available';
                        $('#modal-body').css({
                            'display': 'flex',
                            'justify-content': 'center',
                            'padding': '50px 0px',
                            'color': 'red'
                        });
                    }
                } else {
                    toaster.error('Coupon not available');
                }
            }
        });
    }

    function copyToClipboard(button) {
        const value = button.getAttribute("data-value");
        if ($('.input_code').val() == '') {
            $('.input_code').val(value);
            $('#coupon-modal').modal('hide');
        } else {
            navigator.clipboard.writeText(value)
                .then(() => {
                    toastr.success("Copied to clipboard");
                })
                .catch(err => {
                    toast.error("Failed to copy");
                });
        }
    }


    function fullviewScreen(id) {
        const modal = document.getElementById(`fullscreen-modal${id}`);
        const img = document.getElementById(`zoomable-img${id}`);
        const modalImg = modal.querySelector(".fullscreen-img");
        const closeModal = modal.querySelector(".close-modal");

        if (!modal || !img || !modalImg || !closeModal) return;

        // Open modal on image click
        img.onclick = function() {
            modal.style.display = "flex";
            modalImg.src = this.src;
        };

        // Close when clicking X
        closeModal.onclick = function() {
            modal.style.display = "none";
        };

        // Close when clicking outside image
        modal.onclick = function(e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        };
    }

    // Optional: initialize for all modals with unique IDs
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("[id^='fullscreen-modal']").forEach(modal => {
            const id = modal.id.replace("fullscreen-modal", "");
            fullviewScreen(id);
        });
    });




    function calculateFare($distance) {
        $rate_per_km = 10;

        if ($distance <= 50) {
            return 500;
        } else if ($distance <= 99) {
            return 500 + ($distance - 50) * $rate_per_km;
        } else if ($distance <= 149) {
            return 1000;
        } else if ($distance <= 199) {
            return 2000;
        } else {
            return 2000 + ($distance - 150) * $rate_per_km;
        }
    }
</script>

<script>
    let map, marker, circle;
    initMap();

    function initMap() {
        var defaultLocation = {
            lat: parseFloat("{{ $getfirst['lat'] }}"),
            lng: parseFloat("{{ $getfirst['long'] }}")
        };

        var radius = 20000; // Default to 20km
        if ("{{ $getfirst['use_date'] }}" == 3) {
            radius = 200000; // Set to 250km
        }

        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 10
        });


        var checkaddress = $('.pickup_address').val();
        if (checkaddress) {
            var defaultLocation2 = {
                lat: parseFloat($('.pickup_lat').val()),
                lng: parseFloat($('.pickup_long').val())
            };

            marker = new google.maps.Marker({
                position: defaultLocation2,
                map: map,
                icon: {
                    url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                },
                draggable: false,
                visible: true
            });
        } else {
            marker = new google.maps.Marker({
                position: defaultLocation,
                map: map,
                icon: {
                    url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                },
                draggable: false,
                visible: true
            });
        }

        circle = new google.maps.Circle({
            map: map,
            radius: radius, // Set radius dynamically
            center: defaultLocation,
            fillColor: "#50fb79", //"#FF0000",
            fillOpacity: 0.3,
            strokeColor: "#11c43c", //"#FF0000",
            strokeOpacity: 0.8,
            strokeWeight: 2
        });

        // Adjust zoom level based on radius
        if (radius === 250000) {
            map.setZoom(7); // Zoom out for 250km
        } else {
            map.setZoom(11); // Zoom in for 20km
        }
    }

    function driverandCabExCharge() {
        let index_remmm_route = MultiArrayPush.findIndex(item => item.type === "cab");
        if ((MultiArrayPush[index_remmm_route]['id'] ?? '') && "{{ $getfirst['use_date']}}" == 3) {
            $.ajax({
                url: "{{ url('api/v1/tour/tour-get-distance') }}",
                type: "post",
                beforeSend: function() {
                    $('#loading').removeClass('d--none');
                    $('#loading').css('index', 1000);
                },
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    tour_id: "{{$getfirst['id']}}",
                    cab_id: MultiArrayPush[index_remmm_route]['id'],
                    lat: $('.pickup_lat').val(),
                    long: $('.pickup_long').val(),
                    route_way: $('.out_side_div:checked').val()
                },
                success: function(response) {
                    $('#loading').addClass('d--none');
                    let existingItemRoute = MultiArrayPush.findIndex(item => item.type === 'route');
                    let existingRoute = MultiArrayPush.findIndex(item => item.type === "ex_distance");
                    if (existingRoute !== -1) {
                        MultiArrayPush[existingRoute].ExChargeAmount = response.ExChargeAmount;
                        if (MultiArrayPush[existingRoute].price > 0) {
                            MultiArrayPush[existingRoute].price = (MultiArrayPush[existingRoute].price || 0) + response.ExChargeAmount;
                        }
                    }
                    if (MultiArrayPush[existingRoute] && MultiArrayPush[existingRoute].price !== undefined) {
                        $('.getAddress_google').data('ex-charge-driver', MultiArrayPush[existingRoute].price);
                    } else {
                        $('.getAddress_google').data('ex-charge-driver', 0);
                    }

                }
            });
        }
    }

    function newperPerson_Calculation(type, id, price, that) {
        var number = $(`.person_use_input${$(that).data('key')}`).val();
        $(`.personMessageShow`).text('');
        if (type == 'de' && number > $(that).data('min')) {
            $('.person_use_input').val(0);
            $(`.person_use_input${$(that).data('key')}`).val(Number(number) - 1);
        } else if (type == 'in' && number < $(that).data('max')) {
            $('.person_use_input').val(0);
            $(`.person_use_input${$(that).data('key')}`).val(Number(number) + 1);
        } else {
            if (type == 'de') {
                $(`.personMessageShow${$(that).data('key')}`).text(`You must book at least ${$(that).data('min')} for this unit`);
            } else if (type == 'in') {
                $(`.personMessageShow${$(that).data('key')}`).text(`you can only select up to ${$(that).data('max')}`);
            }
        }
        $(`.person_total_amounts`).html('');
        var number = $(`.person_use_input${$(that).data('key')}`).val();
        var message = `${number} * ${number * price}`;
        $(`.person_total_amounts${$(that).data('key')}`).html(message);

        let PerHeads = MultiArrayPush.find(item => item.type === 'per_head');
        if (PerHeads) {
            PerHeads.price = number * price;
            PerHeads.qty = number;
            PerHeads.id = $(that).data('id');
        } else {
            MultiArrayPush.push({
                type: 'per_head',
                id: $(that).data('id'),
                qty: number,
                price: number * price,
            });
        }
        console.log(MultiArrayPush);

        <?php if ($getfirst['use_date'] == 1) {  ?>
            let PerHeadsUsers = MultiArrayPush.find(item => item.type === 'per_head');
            $('.total_pay_amount').val(PerHeadsUsers.price);
            let gstPercent = "{{ \App\Models\ServiceTax::first()['tour_tax'] ?? 1 }}";
            let priceA = parseFloat(PerHeadsUsers.price);
            let gstAmount = (priceA * gstPercent) / 100;
            let newPrice = priceA - gstAmount;
            if (PerHeadsUsers) {
                PerHeadsUsers.title = "person";
                PerHeadsUsers.price = newPrice;
            }

            let cgstItem = MultiArrayPush.find(item => item.type === 'cgst');
            if (cgstItem) {
                cgstItem.price = gstAmount / 2;
                cgstItem.title = `CGST (${gstAmount / 2}%)`;
            } else {
                MultiArrayPush.push({
                    type: 'cgst',
                    id: 0,
                    qty: '',
                    price: gstAmount / 2,
                    title: `CGST (${gstAmount / 2}%)`
                });
            }

            // Add/Update SGST
            let sgstItem = MultiArrayPush.find(item => item.type === 'sgst');
            if (sgstItem) {
                sgstItem.price = gstAmount / 2;
                sgstItem.title = `SGST (${gstAmount / 2}%)`;
            } else {
                MultiArrayPush.push({
                    type: 'sgst',
                    id: 0,
                    qty: '',
                    price: gstAmount / 2,
                    title: `SGST (${gstAmount / 2}%)`
                });
            }
            $(".tab-booking-total_amount").html(`<div class="col-12 mt-2">
                                    <hr>
                                </div>
                                <div class="col-12 py-2 px-3">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="font-weight-bold" style="display: flex;">{{ translate('price') }}</div>
                                        </div>
                                        <div class="col-4">
                                         </div>
                                        <div class="col-4">
                                            <div class="font-weight-bold" style="display: flex;"> ${newPrice.toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}  </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 py-2 px-3">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="font-weight-bold" style="display: flex;">{{ translate('total_tax') }}</div>
                                        </div>
                                        <div class="col-4">
                                         </div>
                                        <div class="col-4">
                                            <div class="font-weight-bold align-self-center" style="display: flex;font-size: 13px;"> (CGST ${(gstAmount /2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"}) }) </div>
                                            <div class="font-weight-bold align-self-center" style="display: flex;font-size: 13px;"> (SGST ${(gstAmount /2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"}) }) </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 py-2 px-3">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="font-weight-bold" style="display: flex;">{{ translate('price') }}</div>
                                        </div>
                                        <div class="col-4">
                                         </div>
                                        <div class="col-4">
                                            <div class="font-weight-bold product-package-total_amount" style="display: flex;" data-amount="${(number * price)}"> ${(number * price).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})} </div>
                                        </div>
                                    </div>
                                </div>`);
            $('.qty_order').val(PerHeadsUsers.qty);

            $('.mainProductPriceInput').val((number * price));
            $('.part_full_pay1').addClass('active');
            $('.part_full_pay2').removeClass('active');
            $('.part_full_pay1,.part_full_pay2').data('amount', (number * price));
            $('.part_full_pay1').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cc.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('full')}} ${((number * price)).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
            $('.part_full_pay2').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cash-in-hand.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('part')}} ${((number * price)/2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);

            $('.coupan_amount_min').val((number * price));
            $(".getallproducts").val(JSON.stringify(MultiArrayPush));


            var isChecked = $('.wallet_checked').prop('checked');
            let walletAmount = $('.wallet_checked').data('amount');
            let totalPrice = (number * price);
            if (isChecked) {
                var type = $('.wallet_checked').val();
                $(".show_user_wallet_amount").removeClass('d-none');
                $(".user-wallet-adds").val(1);
                if (walletAmount >= totalPrice) {
                    $(".name_change_continues").text(`{{ translate('book_now') }}`);
                    $(".user_wallet_amount_remaining").text('');
                    $(".user_wallet_amount").text(
                        `${(totalPrice).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                    );
                    $(".user_wallet_am_remaining_text").text('');
                    $('.final_amount_pay,.show_view_amounts').text(
                        `${(0.00).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`);
                } else {
                    $(".user_wallet_amount").text(
                        `${(walletAmount).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                    );
                    $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout') }}`);
                    let remainingAmount = totalPrice - walletAmount;
                    let formattedAmount = remainingAmount.toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{ getCurrencyCode() }}"
                    });
                    $(".user_wallet_amount_remaining").text(`- ${formattedAmount}`);
                    $(".user_wallet_am_remaining_text").text("{{ translate('remaining_amount') }}");
                    $('.final_amount_pay,.show_view_amounts').text(
                        `${formattedAmount.toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                    );
                }
                $('.part_full_pay_none').addClass('d-none');
                $(".part_payment_type").val('full');
            } else {
                $('.part_full_pay_none').removeClass('d-none');
                $(".part_payment_type").val('full');
                $(".user-wallet-adds").val(0);
                $(".show_user_wallet_amount").addClass('d-none');
                $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout') }}`);
                $(".user_wallet_amount_remaining").text('');
                $(".user_wallet_am_remaining_text").text('');
                $('.final_amount_pay,.show_view_amounts').text(
                    `${totalPrice.toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`);
            }
        <?php } ?>
        transportOption()
    }

    function transportOption(that = null) {
        if (that != null) {
            let id = $(that).data('id');
            let isChecked = $(`.${id}`).is(':checked');
            $('.extracharges-transport').prop('checked', false);
            if (isChecked) {
                $(`.${id}`).prop('checked', true);
            } else {
                $(`.${id}`).prop('checked', false);
            }
        }
        let PerHeadsUsers = MultiArrayPush.find(item => item.type === 'per_head');
        let gstgroupQty = @json(json_decode($getfirst['ex_transport_price'] ?? '[]', true));
        let matchedGroup = gstgroupQty.find(item => PerHeadsUsers.qty >= item.min && PerHeadsUsers.qty <= item.max);
        if (!matchedGroup) {
            gstgroupQty.sort((a, b) => {
                let aDiff = Math.min(Math.abs(PerHeadsUsers.qty - a.min), Math.abs(PerHeadsUsers.qty - a.max));
                let bDiff = Math.min(Math.abs(PerHeadsUsers.qty - b.min), Math.abs(PerHeadsUsers.qty - b.max));
                return aDiff - bDiff;
            });
            matchedGroup = gstgroupQty;
        }
        let types = $('.extracharges-transport:checked').data('type1')

        let pricetrans = types && matchedGroup[types] ? matchedGroup[types] : 0;

        let transItem = MultiArrayPush.find(item => item.type === 'transport');
        if (transItem) {
            transItem.price = (PerHeadsUsers.qty * pricetrans);
            transItem.title = `Ex Transport`;
            transItem.qty = PerHeadsUsers.qty;
        } else {
            MultiArrayPush.push({
                type: 'transport',
                id: 0,
                qty: PerHeadsUsers.qty,
                price: (PerHeadsUsers.qty * pricetrans),
                title: `Ex Transport`
            });
        }

        if ((PerHeadsUsers.qty * pricetrans) > 0) {
            $(".extransportPrice").text((PerHeadsUsers.qty * pricetrans).toLocaleString("en-US", {
                style: "currency",
                currency: "{{ getCurrencyCode() }}"
            }));
        } else {
            $(".extransportPrice").text('');
        }
    }
</script>
<?php if ($getfirst['is_person_use'] == 1) {
    if ($getfirst['use_date'] == 1) { ?>
        <script>
            $('.per_specel_use_add{{$cab_index}}').click();
        </script>
    <?php } else { ?>
        <script>
            $('.person_use_add0').click();
        </script>
<?php }
} ?>
<script>
    // document.addEventListener('contextmenu', function(e) {
    //     e.preventDefault();
    // });
    document.onkeydown = function(e) {
        if (e.keyCode == 123) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
            return false;
        }
        if (e.ctrlKey && e.keyCode == 85) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 67) {
            return false;
        }
    };
</script>

@endpush