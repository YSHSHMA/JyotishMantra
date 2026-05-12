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
    .hotel-filter {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .hotel-filter::-webkit-scrollbar {
        display: none;

    }

    .hotel-filter .btn {
        flex: 0 0 auto;
    }

    .btn-outline-primary:hover {
        background-color: #ff9200 !important;
        border-color: #ff9200 !important;
    }

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

    .pickdrop-active {
        background-color: #f0f8ff;
        border-top: 1px solid var(--primary-clr) !important;
        border-right: 1px solid var(--primary-clr) !important;
        border-bottom: 1px solid var(--primary-clr) !important;
        border-left: 5px solid var(--primary-clr) !important;
    }

    .button-boarder-set {
        border: 1px solid #fe696a;
        border-radius: 10px;
        padding: 7px 16px 7px 5px;
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
            font-size: 10px;
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

        .font-size-13 {
            font-size: 13px;
        }

        .button-boarder-set {
            padding: 7px 7px 7px 6px;
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

    .pickuplats,
    .droplats {
        border: 1px !important;
        width: 13px !important;
        position: relative !important;
        height: 12px !important;
    }
</style>
<?php if ($getfirst['use_date'] == 1) { ?>
    <style>
        .calendar-container {
            margin-top: 2px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: absolute;
            width: 80%;
            background: white;
            z-index: 100;
            display: none;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 15px;
            background-color: #f9a12b;
            color: white;
        }

        .calendar-header button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .calendar-header button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background-color: #f8f9fa;
        }

        .calendar-day-header {
            padding: 5px;
            text-align: center;
            font-weight: 600;
            background-color: #e9ecef;
        }

        .calendar-day {
            padding: 3px 5px;
            text-align: center;
            cursor: pointer;
            /* border-bottom: 1px solid #dee2e6; */
            /* border-right: 1px solid #dee2e6; */
            transition: all 0.2s;
        }

        .calendar-day:nth-child(7n) {
            border-right: none;
        }

        .calendar-day.other-month {
            color: #adb5bd;
            background-color: white;
        }

        .calendar-day.disabled {
            color: #adb5bd;
            background-color: white;
            cursor: not-allowed;
        }

        .calendar-day.available {
            background-color: white;
            color: #333;
        }

        .calendar-day.available:hover {
            background-color: #ffe9b6;
        }

        .calendar-day.selected {
            background-color: #ff9800;
            color: white;
            border-radius: 8px;
        }

        .calendar-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff9800;
            cursor: pointer;
        }

        .selected-date {
            margin-top: 15px;
            padding: 12px;
            background-color: #e8f4ff;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            color: #2575fc;
            display: none;
        }

        .config-info {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
            font-style: italic;
        }
    </style>
<?php } ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.1/css/swiper.min.css">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initAutocomplete"></script>


@endpush
@section('content')
<?php $IncludedNewArrays = []; ?>
<div class="container-fluid mt-3 rtl text-align-direction" id="cart-summary">
    <div class="row">
        <!-- New -->
        <!-- TailwindCSS & SwiperJS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        <div class="max-w-6xl mx-auto p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white rounded-lg shadow-lg p-4">

                <!-- Left: Slider -->
                <div>
                    <div class="swiper rounded-lg overflow-hidden">
                        <div class="swiper-wrapper">
                            @foreach (json_decode($getfirst['image'], true) as $val)
                            <div class="swiper-slide">
                                <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($val ?? ''), type: 'backend-product') }}"
                                    class="w-full h-100 object-cover" alt="Tour Image">
                            </div>
                            @endforeach
                        </div>

                        <!-- Swiper Controls -->
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>

                <!-- Right: Content -->
                <div class="flex flex-col justify-center space-y-4">

                    <!-- Title -->
                    <h1 class="text-2xl font-bold text-gray-800">{{ $getfirst['tour_name'] ?? '' }}</h1>

                    <!-- Ratings -->
                    <div class="flex items-center gap-1 text-gray-700 text-sm flex-wrap">
                        <i class="tio-star text-yellow-500"></i>
                        @php
                        $number = round($ratings['total'], 1);
                        $formattedNumber = $number >= 1000000 ? round($number / 1000000, 1) . 'M+' :
                        ($number >= 1000 ? round($number / 1000, 1) . 'K+' : ($number ?: 1));
                        $totalReviews = !empty($ratings['list']) ? (
                        count($ratings['list']) >= 1000000 ? round(count($ratings['list']) / 1000000, 1) . 'M+' :
                        (count($ratings['list']) >= 1000 ? round(count($ratings['list']) / 1000, 1) . 'K+' :
                        count($ratings['list']))
                        ) : 1;
                        @endphp
                        <span>{{ $formattedNumber }} ({{ ($totalReviews==0)?1:$totalReviews }} {{ translate('Reviews') }})</span>
                    </div>
                    <?php $difference = 0; ?>
                    @if(($getfirst['use_date'] == 1))
                    <div class="flex items-center gap-1 text-gray-700 text-sm flex-wrap mt-2">
                        @if($getfirst['pickup_location'])
                        <span class="__text-16"> <i class="tio-poi_add text-yellow-500">poi_add</i> {{ $getfirst['pickup_location'] }} </span>
                        @endif
                        @if($getfirst['startandend_date'])
                        <?php $date_s = explode(' - ', $getfirst['startandend_date'])[0] ?? '';
                        $dateRange = explode(' - ', $getfirst['startandend_date']);
                        $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
                        $endDate = isset($dateRange[1]) ? $dateRange[1] : '';

                        if ($startDate && $endDate) {
                            $start = new DateTime($startDate);
                            $end = new DateTime($endDate);
                            $difference = $start->diff($end)->days;
                            if (isset($getfirst['customized_type']) && isset($getfirst['customized_dates'])) {
                                $customizedType = $getfirst['customized_type'];
                                $customizedDates = json_decode($getfirst['customized_dates'] ?? "[]", true);
                                switch ($customizedType) {
                                    case 1:
                                        $today = new DateTime();
                                        $nextDates = [];
                                        foreach ($customizedDates as $day) {
                                            $next = new DateTime("next " . $day);
                                            // if (strtolower($today->format('l')) == strtolower($day)) {
                                            //     $next = clone $today;
                                            // }
                                            $nextDates[] = $next;
                                        }
                                        usort($nextDates, function ($a, $b) {
                                            return $a <=> $b;
                                        });
                                        $nextDay = $date_s = $nextDates[0]->format("Y-m-d");
                                        break;

                                    case 2:
                                        $dayNumbers = array_map(function ($date) {
                                            return (int)date('d', strtotime($date));
                                        }, $customizedDates);
                                        $today = new DateTime();
                                        $nextDates = [];
                                        foreach ($dayNumbers as $dayNumber) {
                                            $next = new DateTime($today->format('Y-m-' . sprintf('%02d', $dayNumber)));
                                            if ($next < $today) {
                                                $next->modify('+1 month');
                                            }
                                            $nextDates[] = $next;
                                        }
                                        usort($nextDates, function ($a, $b) {
                                            return $a <=> $b;
                                        });

                                        $nextDay = $date_s = $nextDates[0]->format("Y-m-d");
                                        break;

                                    case 3:
                                        $today = new DateTime();
                                        $nextDates = [];
                                        foreach ($customizedDates as $dateStr) {
                                            $date = DateTime::createFromFormat('Y-m-d', $dateStr);
                                            $monthDay = $date->format('m-d');
                                            $currentYearDate = DateTime::createFromFormat('Y-m-d', $today->format('Y') . '-' . $monthDay);
                                            if ($currentYearDate < $today) {
                                                $currentYearDate->modify('+1 year');
                                            }
                                            $nextDates[] = $currentYearDate;
                                        }
                                        usort($nextDates, function ($a, $b) {
                                            return $a <=> $b;
                                        });
                                        $nextDay = $date_s = $nextDates[0]->format("Y-m-d");
                                        break;
                                    default:
                                        $date_s = $startDate;
                                        break;
                                }
                            }
                        }
                        $time_s = $getfirst['pickup_time'];
                        ?>
                        <span class="__text-16"> <i class="tio-calendar_note text-yellow-500">calendar_note</i> {{ date('d M,Y h:i A', strtotime($date_s . ' ' . $time_s)) }} </span>
                        @endif
                    </div>
                    @endif
                    <input type="hidden" class="difference-dates" value="{{ $difference }}">
                    <!-- Included Packages -->
                    @php
                    $includePackages = json_decode($getfirst['is_included_package'], true);
                    $packageIcons = [
                    'sightseen' => ['sightseeing.png', 'sightseeing'],
                    'cab' => ['car.png', 'transportion'],
                    'food' => ['foods.png', 'Accomadation'],
                    'hotel' => ['hotel.png', 'hotel']
                    ];
                    @endphp
                    <div class="flex flex-wrap md:flex-nowrap gap-4">
                        @foreach ($packageIcons as $key => [$icon, $label])
                        @if(!empty($includePackages[$key]))
                        <div class="flex flex-col items-center">
                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/' . $icon) }}"
                                class="w-12 h-10 mb-1" alt="{{ $label }}">
                            <span class="text-xs font-semibold text-center">{{ translate($label) }}</span>
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <!-- Tour Badge -->
                    <span class="text-white text-sm font-semibold px-3 py-1 rounded-full w-max"
                        style="background-color:#FF7700;">
                        {{ $getfirst['number_of_day'] }}D / {{ $getfirst['number_of_night'] }}N Tour
                    </span>

                    <!-- Price -->
                    <?php
                    $is_person_check = $getfirst['is_person_use'] ?? 0;
                    $cab_price = 0;
                    $per_head_min_persons = 0;
                    $packages_price = 0;

                    if (!empty($getfirst['cab_list_price']) && is_array(json_decode($getfirst['cab_list_price'], true))) {
                        $cabList = json_decode($getfirst['cab_list_price'], true);
                        usort($cabList, fn($a, $b) => $a['price'] <=> $b['price']);
                        $cab_price = $cabList[0]['price'] ?? 0;
                        // $per_head_min_persons = $cabList[0]['min'] ?? 0;
                    }

                    if (!empty($getfirst['package_list_price']) && is_array(json_decode($getfirst['package_list_price'], true)) && ($is_person_check == 0 && in_array($getfirst['use_date'], [1, 2, 3, 4]))) {
                        foreach (json_decode($getfirst['package_list_price'], true) as $plis) {
                            $packages_price += $plis['pprice'];
                        }
                    }

                    $final_price = $cab_price + $packages_price;
                    ?>
                    <div>
                        <div class="flex items-end gap-2 flex-wrap">
                            <span class="text-3xl font-bold" style="color:#FF7700;">
                                {{ webCurrencyConverter(amount: (double)$final_price) }}
                            </span>
                            <span class="line-through text-gray-400">
                                {{ webCurrencyConverter(amount: (double)($final_price / (1 - (($getfirst['percentage_off'] ?? 0) / 100)))) }}
                            </span>
                            <span class="text-green-600 font-semibold text-sm">{{$getfirst['percentage_off']??0}}% OFF</span>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ $is_person_check ? 'Per Head' : 'per person' }}
                        </p>
                    </div>

                    <!-- Book Button -->
                    <button class="text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-colors duration-200 d-sm-block d-none" style="background-color:#FF7700;"
                        onmouseover="this.style.backgroundColor='#e66600'"
                        onmouseout="this.style.backgroundColor='#FF7700'"
                        onclick="add_all_package()">
                        {{ translate('book_Now') }}
                    </button>
                    <button class="text-white font-semibold py-3 px-6 rounded-0 shadow-lg transition-colors duration-200 d-block d-sm-none w-100 mobile-book-btn"
                        style="background-color:#FF7700; position:fixed; bottom:0; left:0; z-index:9999;"
                        onmouseover="this.style.backgroundColor='#e66600'"
                        onmouseout="this.style.backgroundColor='#FF7700'"
                        onclick="add_all_package()">
                        {{ translate('book_Now') }}
                    </button>
                </div>
            </div>
        </div>

        <script>
            new Swiper('.swiper', {
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                }
            });
        </script>

        <!-- Old -->
        <!-- <div class="col-md-12 ml-4 py-3">
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
        </div> -->
    </div>
    <div class="row">
        <?php /* ?>    
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
        </div>
        <?php */ ?>
        <div class="col-md-4" style="display:none;">
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

                                    @if (!empty($getfirst['package_list_price']) && is_array(json_decode($getfirst['package_list_price'], true)) && $is_person_check == 0 && in_array($getfirst['use_date'], [1, 2, 3, 4]))
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
                                @if(($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && $getfirst['is_person_use'] == 0)
                                <span class="lay">Package Included</span>
                                @elseif($getfirst['is_person_use'] == 1 && (!$getfirst['package_list_price'] || (json_decode($getfirst['package_list_price'],true) <= 0)))
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
        <div class="col-md-12">
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
                                        <div class="modal fade" id="imageModal-{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content" style="    background-color: #3d3d3ed1;    border: 0px;">
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
                                <?php /*
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
                                                            <img src="{{ getValidImage(path: 'storage/app/public/profile/' . ($counselling['userData']['image'] ?? ''), type: 'product') }}" alt="User Icon" class="user-icon" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
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
                                                                                <i class="tio-star-half text-warning"></i>
                                                                                @else
                                                                                <i class="tio-star-outlined text-warning"></i>
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
                                */
                                ?>

                                <div class="section-content {{ ((request()->comment == 'success' || request()->comment == 'error') ? 'active' : '') }}" id="review_user">
                                    <div class="p-6 mt-4 bg-white shadow-md rounded-md border-b-4 border-transparent">

                                        <!-- Section Heading -->
                                        <div class="mb-6 text-center">
                                            <h2 class="text-2xl font-bold text-gray-800">{{ translate('Reviews') }} & {{ translate('Ratings') }}</h2>
                                            <p class="text-gray-500">{{ translate('Read what our beloved devotees have to say about Mahakal.com.') }}</p>
                                        </div>

                                        @if (!empty($ratings['list']) && count($ratings['list']) > 0)
                                        <!-- Swiper Container -->
                                        <div class="relative">
                                            <div class="swiper review-swiper">
                                                <div class="swiper-wrapper">
                                                    @foreach ($ratings['list'] as $counselling)
                                                    <div class="swiper-slide">
                                                        <div class="bg-white border rounded-lg p-4 shadow-sm h-full flex flex-col">

                                                            <!-- User Info -->
                                                            <div class="flex items-center mb-3">
                                                                <img src="{{ getValidImage(path: 'storage/app/public/profile/' . ($counselling['userData']['image'] ?? ''), type: 'product') }}"
                                                                    alt="User Icon"
                                                                    class="w-12 h-12 rounded-full object-cover mr-3">
                                                                <div>
                                                                    <p class="font-semibold">{{ $counselling['userData']['name'] ?? translate('User Name') }}</p>
                                                                    <div class="flex text-yellow-500 text-sm">
                                                                        @for ($inc = 1; $inc <= 5; $inc++)
                                                                            @if ($inc <=(int) $counselling['star'])
                                                                            <i class="tio-star"></i>
                                                                            @elseif ($counselling['star'] != 0 && $inc <= (int) $counselling['star'] + 1.1 && $counselling['star']> ((int) $counselling['star']))
                                                                                <i class="tio-star-half"></i>
                                                                                @else
                                                                                <i class="tio-star-outlined"></i>
                                                                                @endif
                                                                                @endfor
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Comment -->
                                                            <div class="flex-1">
                                                                <p class="text-gray-600 text-sm line-clamp-4 review-comment">
                                                                    {{ $counselling['comment'] ?? '' }}
                                                                </p>
                                                                @php $filePath = 'storage/event/comment/' . ($counselling['image'] ?? ''); @endphp
                                                                @if (!empty($counselling['image']) && file_exists($filePath))
                                                                <img alt="{{ translate('product') }}"
                                                                    src="{{ getValidImage(path: 'storage/app/public/event/comment/' . $counselling['image'], type: 'product') }}"
                                                                    class="mt-2 w-12 h-12 border border-gray-200 rounded">
                                                                @endif
                                                            </div>

                                                            <!-- Read More -->
                                                            <button type="button" class="mt-2 text-orange-500 text-xs cursor-pointer toggle-read">
                                                                {{ translate('Read more') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Navigation Arrows -->
                                            <div class="swiper-button-prev !text-gray-700"></div>
                                            <div class="swiper-button-next !text-gray-700"></div>
                                        </div>
                                        @else
                                        <div class="text-center">
                                            <img class="mx-auto" src="{{ asset('public/assets/front-end/img/icons/empty-review.svg') }}" alt="">
                                            <p class="text-gray-500 text-sm mt-2">{{ translate('No review given yet!') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Swiper Init -->
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        new Swiper('.review-swiper', {
                                            slidesPerView: 1,
                                            spaceBetween: 16,
                                            loop: true,
                                            autoplay: {
                                                delay: 3000,
                                                disableOnInteraction: false,
                                            },
                                            breakpoints: {
                                                640: {
                                                    slidesPerView: 2
                                                },
                                                1024: {
                                                    slidesPerView: 4
                                                }
                                            },
                                            navigation: {
                                                nextEl: '.swiper-button-next',
                                                prevEl: '.swiper-button-prev',
                                            }
                                        });

                                        // Read more toggle
                                        document.querySelectorAll('.toggle-read').forEach(btn => {
                                            btn.addEventListener('click', function() {
                                                let comment = this.parentElement.querySelector('.review-comment');
                                                comment.classList.toggle('line-clamp-4');
                                                this.textContent = comment.classList.contains('line-clamp-4') ?
                                                    "{{ translate('Read more') }}" :
                                                    "{{ translate('Read less') }}";
                                            });
                                        });
                                    });
                                </script>

                                <style>
                                    /* Position arrows on sides, vertically centered */
                                    .review-swiper .swiper-button-prev,
                                    .review-swiper .swiper-button-next {
                                        top: 50%;
                                        transform: translateY(-50%);
                                        z-index: 10;
                                    }

                                    .swiper-button-prev {
                                        left: -2rem;
                                    }

                                    .swiper-button-next {
                                        right: -2rem;
                                    }
                                </style>

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

    </div>
</div>



<div class="row">
    @if (isset($getfirst['video_url']) &&
    $getfirst['video_url'] != null &&
    str_contains($getfirst['video_url'], 'youtube.com/embed/'))

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
            <div class="modal-body px-2">
                <div>
                    <button type="button" class="btn btn-danger btn-sm float-end borer mb-2 text-white addOtherpackages-modal-close" data-dismiss="modal" aria-label="Close" style="margin: -32px -22px 0px 0px;">x</button>
                </div>
                <h3>{{ $getfirst['tour_name']??""}}</h3>
                <div>
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
                        <div class="mt-4 col-12 rtl text-align-direction px-0">
                            <?php $tab_index = 0; ?>
                            <ul class="nav nav-tabs nav--tabs mt-3 border-top border-bottom py-2 mb-0 small" role="tablist" id="tab-navigation" style="text-wrap: nowrap;">
                                @if($is_person_check == 1)
                                <?php $tab_index++; ?>
                                <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link navlinks __inline-27 active disabled" href="#is_person_user" data-toggle="tab" role="tab">{{ $tab_index }}</a>&nbsp;<span>{{ translate('people') }}</span>
                                </li>
                                <li class="nav-item navItems">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                </li>
                                @elseif($is_person_check == 0)
                                <?php $tab_index++; ?>
                                <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link navlinks __inline-27 active disabled" href="#cab_package" data-toggle="tab" role="tab">{{ $tab_index }}</a>&nbsp;<span>{{ translate('choose transportation') }}</span>
                                </li>
                                <li class="nav-item navItems">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                </li>
                                @endif
                                @if($getfirst['use_date'] == 0 || ($getfirst['is_person_use'] == 1))
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
                                <?php $tab_index++; ?>
                                <li class="nav-item navItems" style="width: 150px; text-align: center">
                                    <a class="nav-link navlinks __inline-27 disabled booking_date_point" href="#booking_date" data-toggle="tab" role="tab">
                                        {{ $tab_index }}
                                    </a> &nbsp;<span>{{ translate('booking_info') }}</span>
                                </li>
                                <li class="nav-item navItems">
                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                </li>

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
                                                    <hr style="height: 5px; border: 0px;">
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
                                    @elseif($getfirst['use_date'] == 1)
                                    <div class="tab-pane fade show active text-justify" id="cab_package" role="tabpanel">
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
                                        <div class="row px-3">
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
                                                    <?php
                                                    $dateRange = explode(' - ', $getfirst['startandend_date'] ?? "");
                                                    $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
                                                    $endDate = isset($dateRange[1]) ? $dateRange[1] : '';

                                                    if ($startDate && $endDate) {
                                                        $start = new DateTime($startDate);
                                                        $end = new DateTime($endDate);
                                                        $difference = $start->diff($end)->days;
                                                        if (isset($getfirst['customized_type']) && isset($getfirst['customized_dates'])) {
                                                            $customizedType = $getfirst['customized_type'];
                                                            $customizedDates = json_decode($getfirst['customized_dates'] ?? "[]", true);
                                                            switch ($customizedType) {
                                                                case 1:
                                                                    $today = new DateTime();
                                                                    $nextDates = [];
                                                                    foreach ($customizedDates as $day) {
                                                                        $next = new DateTime("next " . $day);
                                                                        // if (strtolower($today->format('l')) == strtolower($day)) {
                                                                        //     $next = clone $today;
                                                                        // }
                                                                        $nextDates[] = $next;
                                                                    }
                                                                    usort($nextDates, function ($a, $b) {
                                                                        return $a <=> $b;
                                                                    });
                                                                    $startDate = $nextDates[0]->format("Y-m-d");
                                                                    $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
                                                                    break;

                                                                case 2:
                                                                    $dayNumbers = array_map(function ($date) {
                                                                        return (int)date('d', strtotime($date));
                                                                    }, $customizedDates);
                                                                    $today = new DateTime();
                                                                    $nextDates = [];
                                                                    foreach ($dayNumbers as $dayNumber) {
                                                                        $next = new DateTime($today->format('Y-m-' . sprintf('%02d', $dayNumber)));
                                                                        if ($next < $today) {
                                                                            $next->modify('+1 month');
                                                                        }
                                                                        $nextDates[] = $next;
                                                                    }
                                                                    usort($nextDates, function ($a, $b) {
                                                                        return $a <=> $b;
                                                                    });

                                                                    $startDate = $nextDates[0]->format("Y-m-d");
                                                                    $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
                                                                    break;

                                                                case 3:
                                                                    $today = new DateTime();
                                                                    $nextDates = [];
                                                                    foreach ($customizedDates as $dateStr) {
                                                                        $date = DateTime::createFromFormat('Y-m-d', $dateStr);
                                                                        $monthDay = $date->format('m-d');
                                                                        $currentYearDate = DateTime::createFromFormat('Y-m-d', $today->format('Y') . '-' . $monthDay);
                                                                        if ($currentYearDate < $today) {
                                                                            $currentYearDate->modify('+1 year');
                                                                        }
                                                                        $nextDates[] = $currentYearDate;
                                                                    }
                                                                    usort($nextDates, function ($a, $b) {
                                                                        return $a <=> $b;
                                                                    });
                                                                    $startDate = $nextDates[0]->format("Y-m-d");
                                                                    $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("d/m/Y");;
                                                                    break;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    @php
                                                    $getseats = \App\Models\TourOrder::where('tour_id',$getfirst['id'])->where('pickup_date',$startDate)->where('amount_status',1)->where('status',1)->where('available_seat_cab_id',$vapak)->sum('qty');
                                                    @endphp
                                                    <div class="col-4">
                                                        @if(($s_seats[$vapak] - $getseats) > 0)
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
                                                        <div class="small font-weight-bolder spcab_packages_data{{$k}} spcab_packages_data mt-4" style="display: flex;" data-manamount='{{ $getcab_prices }}' data-keys='{{ $k }}' data-seats='{{$s_name[$vapak]}} {{$s_seats[$vapak]}} seats'> {{ webCurrencyConverter(amount: $getcab_prices) }}</div>
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
                                                            <a class="personMessageShow personMessageShow{{$kper}} text-primary small d-sm-block d-none"></a>
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
                                                                <span class="OnepersonMessageShow OnepersonMessageShow{{$kper}} text-danger small"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row py-3 d-none d-md-flex">
                                                    <div class="col-4">
                                                        <div class="font-weight-bold align-self-center">
                                                            <span>Group of {{ $persons['min'] }}{{ (($persons['min'] == $persons['max'])? '' : ' - '.$persons['max']) }} (Per Person) </span>
                                                            <a class="personMessageShow personMessageShow{{$kper}} text-primary small d-sm-block d-none"></a>
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
                                                                <span class="OnepersonMessageShow OnepersonMessageShow{{$kper}} text-danger small"></span>
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
                                    </div>
                                    @elseif(($getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($is_person_check == 0))
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
                                                <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold d-block d-sm-none" style="font-size: 12px;">
                                                    <span class="small"> 1 * {{ webCurrencyConverter(amount: ((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))) }}</span>
                                                    <hr style="height: 5px; border: 0px;">
                                                    <span class="cab_information{{ $kk }}{{((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))}} cab_information text-danger h6">
                                                        {{ webCurrencyConverter(amount: ((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))) }}
                                                    </span>
                                                </div>
                                                <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold d-none d-sm-flex" style="font-size: 12px;">
                                                    <span> 1 * {{ webCurrencyConverter(amount: ((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))) }}</span>&nbsp;
                                                    <span class="cab_information{{ $kk }}{{((double)($s_price[$kk] ?? 0) + ($packages_price ?? 0))}} cab_information text-danger h6">
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
                                    @elseif(($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4) && ($is_person_check == 1))
                                    <div class="tab-pane fade text-justify is_person_user_point1" id="is_person_user" role="tabpanel">
                                        <div class="row mt-4">
                                            <div class="col-12 mt-2 px-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="small">
                                                            <span class="cab-tab-font-header">{{ translate('Group_of_Person')}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small font-weight-bold"><span class="cab-tab-font-header">{{ translate('per_Person')}}</span></div>
                                                    </div>
                                                    <div class="col-4 text-center">
                                                        <div class="cab-tab-font-header">{{ translate('Choose')}}</div>
                                                    </div>
                                                    <div class="col-12">
                                                        <hr>
                                                    </div>
                                                </div>
                                                @if (!empty($getfirst['cab_list_price']) && is_array(json_decode($getfirst['cab_list_price'], true)))
                                                <?php $getCabOrderDesc = json_decode($getfirst['cab_list_price'], true); ?>
                                                @foreach (array_reverse($getCabOrderDesc) as $kper=>$persons)
                                                <div class="row my-2">
                                                    <div class="col-4">
                                                        <div class="font-weight-bold">
                                                            <span>{{ $persons['min'] }}{{ (($persons['min'] == $persons['max'])? '' : ' - '.$persons['max']) }} </span><br>
                                                            <a class="personMessageShow personMessageShow{{$kper}} text-primary small d-sm-block d-none"></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="small d-block d-sm-none">
                                                            <span>{{ webCurrencyConverter(amount: $persons['price']??0) }}</span>
                                                            <br>
                                                            <span class="font-weight-bold person_total_amounts{{$kper}} person_total_amounts text-danger"></span>
                                                        </div>
                                                        <div class="font-weight-bold d-none d-sm-block">
                                                            <span class="small mt-2">{{ webCurrencyConverter(amount: $persons['price']??0) }}</span>&nbsp; &nbsp;&nbsp; &nbsp;
                                                            <span class="person_total_amounts{{$kper}} person_total_amounts h4 text-danger"></span>
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
                                                            <span class="OnepersonMessageShow OnepersonMessageShow{{$kper}} text-danger small"></span>
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
                                    <!-- //////////////////////////////////////////////// -->
                                    @if($getfirst['use_date'] == 0 || $getfirst['is_person_use'] == 1)
                                    @if(!empty($DuplicatePackagepacid))
                                    @foreach($DuplicatePackagepacid as $kkey => $nname)
                                    @if($nname)
                                    <div class="tab-pane fade text-justify" id="other_package_{{$kkey}}" role="tabpanel">
                                        <div class="pt-2 specification" style="height: 287px; overflow: auto;">
                                            @if(!empty($getfirst['package_list_price']) && json_decode($getfirst['package_list_price'], true))
                                            @php
                                            $displayedPackages = [];
                                            $packagesNew = json_decode($getfirst['package_list_price']??'[]', true);
                                            $filteredNew = array_filter($packagesNew, function ($pkg) {
                                            return ((\App\Models\TourPackage::where('id', $pkg['package_id'])->first()['type']??"") === 'hotel' && in_array($pkg['included']??"", [1]));
                                            });
                                            $packageListInclude = json_decode($getfirst['package_list_price'] ?? "[]", true);
                                            usort($packageListInclude, function($a, $b) {
                                            return $b['included'] <=> $a['included'];
                                                });
                                                @endphp
                                                @foreach($packageListInclude as $keyk => $plis)
                                                @foreach($nname as $pp_v => $packages_ar)
                                                @if($packages_ar['id'] == $plis['package_id'] && !in_array($plis['package_id'].$plis['pprice'], $displayedPackages))
                                                @php
                                                $tourPackages = \App\Models\TourPackage::where('id', $plis['package_id'])->first();
                                                $displayedPackages[] = $plis['package_id'].$plis['pprice'];
                                                @endphp
                                                @if($tourPackages)
                                                <div class="row {{ (($tourPackages['type'] == 'hotel' && $filteredNew) ? ((($plis['included'] ?? 0) == 1)?'':'d-none filters-hotel-'.str_replace(' ','__',$tourPackages['hotel_type']??'') ): '' )}}">
                                                    <div class="col-3 text-left custom-class-add_{{$tourPackages['type']}}{{($plis['included']??0)}}">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product') }}" style="width: 125px; height: 77px;margin-bottom: 4px;">
                                                        <br>
                                                        <div class="text-left font-weight-bold d-block d-sm-none custom-class-addsm_{{$tourPackages['type']}}{{($plis['included']??0)}}" style="font-size: 12px;">
                                                            <span>{{ ucwords($tourPackages['name'] ?? '') }}</span><br>
                                                            <span>{{ $tourPackages['title'] ?? '' }}</span> <br>
                                                            <span>{{ webCurrencyConverter(amount: ((double)$plis['pprice'] ?? 0)) }}</span><br>

                                                        </div>
                                                    </div>
                                                    <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold  d-none d-sm-block custom-class-addmd_{{$tourPackages['type']}}{{($plis['included']??0)}}" style="font-size: 12px;">
                                                        <span>{{ ucwords($tourPackages['name'] ?? '') }}</span><br>
                                                        <span>{{ $tourPackages['title'] ?? '' }}</span> <br>
                                                        @if(($plis['included']??0) == 0)
                                                        <span>{{ webCurrencyConverter(amount: ((double)$plis['pprice'] ?? 0)) }}</span><br>
                                                        @endif
                                                    </div>
                                                    <div class="col-4 col-md-3 col-lg-3 text-left font-weight-bold {{ ((($plis['included']??0) == 1)?'pt-3':'')}} custom-class-add_{{$tourPackages['type']}}{{($plis['included']??0)}}" style="font-size: 12px;">
                                                        <span class="{{ ((($plis['included']??0) == 1)?'text-success font-weight-bold h5':'')}}">
                                                            @if((($plis['included'] ?? 0) == 1) && $tourPackages['type'] == 'foods')
                                                            @elseif((($plis['included'] ?? 0) == 1) && $tourPackages['type'] == 'hotel')
                                                            <span class="hotel-title-change-included">Included</span> <br>
                                                            @else
                                                            1 * {{ webCurrencyConverter(amount: ((double)$plis['pprice'] ?? 0)) }}
                                                            <hr style="height: 5px; border: 0px;">
                                                            <span class="other_information{{$plis['package_id']}} other_information{{$plis['package_id']}}{{$plis['pprice']}}"></span>
                                                            @endif
                                                        </span>
                                                    </div>

                                                    <?php
                                                    $newpprices = $plis['pprice'];
                                                    if ((($plis['included'] ?? 0) == 1)) {
                                                        $newpprices = 0;
                                                    } ?>
                                                    @if((($plis['included'] ?? 0) == 0))
                                                    <div class="col-4 col-md-3 col-lg-3 hotels_package_add_{{$tourPackages['type']}}">
                                                        <a style="margin-top: 15px;" class="px-3 py-1 btn--primary rounded-pill cursor-pointer {{ ((($plis['included']??0) == 1)?'d-none':'')}} other_package_add1{{$plis['package_id']}} other_package_add1_{{$plis['package_id']}}{{$keyk}}" onclick="handleAddPackage(this)" data-hotalname="{{ str_replace(' ','_',$tourPackages['hotel_type']) }}" data-typename="{{$tourPackages['type']}}" data-keyname="{{$keyk}}" data-packid="{{$plis['package_id']}}" data-type="other{{$plis['package_id']}}" data-seats="{{$tourPackages['seats']??''}}" data-newclass1="other_package_add_value_{{$plis['package_id']}}{{$keyk}}" data-newprices="{{$newpprices}}" data-buttondiv="other_package_add{{$tourPackages['type']}}{{$plis['package_id']}}">add</a>
                                                        <div class="other_package_add{{$tourPackages['type']}}{{$plis['package_id']}} other_package_add_{{$plis['package_id']}}{{$keyk}} {{ ((($plis['included']??0) == 1)?'':'d-none')}}">
                                                            <div class="small" style="display:inline-flex; margin-top: 15px;">
                                                                <a class="cab-button-plus-minus" onclick="newaddpackages('de',`other_package_add_value_{{$plis['package_id']}}{{$keyk}}`,`{{$newpprices}}`,this);<?php if ($getfirst['is_person_use'] == 1) { ?>updateOldarray(this);<?php } ?>" data-type1="other" data-type="other{{$plis['package_id']}}" data-button="other_package_add1_{{$plis['package_id']}}{{$keyk}}" data-point="other_package_add_{{$plis['package_id']}}{{$keyk}}" data-id="{{$plis['package_id']}}" data-key="{{$keyk}}" data-typename="{{$tourPackages['type']}}" data-seats="{{$tourPackages['seats']??''}}" data-newclass1="other_package_add_value_{{$plis['package_id']}}{{$keyk}}" data-newprices="{{$newpprices}}"><i class="tio-remove" style="font-size: 15px;margin-top:15px"></i></a>
                                                                <input type="number" readonly style="width: 39px; height: 33px; border: 1px solid #80808040;" class="{{ ((($plis['included']??0) == 1)?'included_packages':'')}} included_max_packages other_package_add_value{{$plis['package_id']}} other_package_add_value_{{$plis['package_id']}}{{$keyk}} text-center" value="{{ ((($clprice['id']??0) == ($cab_index ?? '')) ? 1 : ((($plis['included']??0) == 1)?'1':0)) }}" data-hotalname-input="{{ str_replace(' ','_',$tourPackages['hotel_type']) }}" data-hotalname="{{ str_replace(' ','_',$tourPackages['hotel_type']) }}" data-gettype="{{$tourPackages['type']}}">
                                                                <a class="cab-button-plus-minus {{ ((($plis['included']??0) == 1)?'included_packages_plus':'')}}" onclick="newaddpackages('in',`other_package_add_value_{{$plis['package_id']}}{{$keyk}}`,`{{$newpprices}}`,this);<?php if ($getfirst['is_person_use'] == 1) { ?>updateOldarray(this);<?php } ?>" data-type1="other" data-type="other{{$plis['package_id']}}" data-button="other_package_add1_{{$plis['package_id']}}{{$keyk}}" data-point="other_package_add_{{$plis['package_id']}}{{$keyk}}" data-id="{{$plis['package_id']}}" data-key="{{$keyk}}" data-typename="{{$tourPackages['type']}}" data-seats="{{$tourPackages['seats']??''}}" data-newclass1="other_package_add_value_{{$plis['package_id']}}{{$keyk}}" data-newprices="{{$newpprices}}"><i class="tio-add" style="font-size: 15px;margin-top:15px"></i></a>
                                                            </div>
                                                            <span class="OnepersonMessageShow OnepersonMessageShowother{{$plis['package_id']}} text-danger small">&nbsp;</span>
                                                            @if($tourPackages['type'] == 'hotel')
                                                            <br>
                                                            <span>{{ translate('number_of_Room') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @elseif((($plis['included'] ?? 0) == 1) && $tourPackages['type'] == 'foods')
                                                    <span class="text-success font-weight-bold h5">Included</span>
                                                    <?php $IncludedNewArrays[] = ["id" => $plis['package_id'], "price" => $newpprices, "price2" => $newpprices, 'qty' => '', "type" => "other" . $plis['package_id']] ?>
                                                    @elseif((($plis['included'] ?? 0) == 1) && $tourPackages['type'] == 'hotel')
                                                    <div class="col-4 col-md-3 col-lg-3 custom-class-add_{{$tourPackages['type']}}{{($plis['included']??0)}}">
                                                        <a style="margin-top: 15px;" class="px-3 py-1 btn--primary rounded-pill cursor-pointer customBtn">Custom</a>
                                                    </div>
                                                    <?php $IncludedNewArrays[] = ["id" => $plis['package_id'], "price" => $newpprices, "price2" => $newpprices, 'qty' => '', "type" => "other" . $plis['package_id']];
                                                    $newIncludeHotel = "other" . $plis['package_id'];
                                                    ?>
                                                    @endif
                                                    <div class="col-12 mb-2 custom-class-add_{{$tourPackages['type']}}{{($plis['included']??0)}}">
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
                                                    @if((($plis['included'] ?? 0) == 1) && $tourPackages['type'] == 'hotel')
                                                    <div class="col-12 customOptions" style="display:none;">
                                                        <?php $packages = json_decode($getfirst['package_list_price'] ?? "[]", true);
                                                        if (!is_array($packages)) {
                                                            $packages = [];
                                                        }
                                                        $packages = array_filter($packages, function ($p) {
                                                            return !empty($p) && !empty($p['package_id']) && (($p['included'] ?? 0) == 0);
                                                        });
                                                        $packageIds = !empty($packages) ? array_column($packages, 'package_id') : [];
                                                        $tourPackages_query = \App\Models\TourPackage::select('hotel_type')->whereIn('id', $packageIds)->whereNotNull('hotel_type')->groupBy('hotel_type')->get();

                                                        ?>
                                                        @if($tourPackages_query)
                                                        <div class="hotel-filter mb-2 gap-2">
                                                            <button class="btn btn-sm btn-outline-primary filter-hotel rounded-pill px-4 active" data-star="all">All</button>
                                                            @foreach($tourPackages_query as $va)
                                                            <button class="btn btn-sm btn-outline-primary filter-hotel rounded-pill px-4 " data-star="{{ str_replace(' ','__',$va['hotel_type']??'') }}">{{$va['hotel_type']}}</button>
                                                            @endforeach
                                                            <a class="custom-new-class-add_{{$tourPackages['type']}}{{($plis['included']??0)}} px-3 py-1 btn-danger float-end rounded-pill cursor-pointer customBtn"><i class="tio tio-clear"></i></a>
                                                        </div>
                                                        @else
                                                        <a class="custom-new-class-add_{{$tourPackages['type']}}{{($plis['included']??0)}} px-3 py-1 btn-danger float-end rounded-pill cursor-pointer customBtn"><i class="tio tio-clear"></i></a>
                                                        @endif
                                                    </div>
                                                    <div class="col-12 customOptions" style="display:none;">
                                                        <small class="text-danger font-weight-bolder"> {{ translate('Switching hotels will clear your previous selections') }}</small>
                                                        <br>
                                                    </div>
                                                    @endif
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
                                    <div class="tab-pane fade text-justify booking_date_point1" id="booking_date" role="tabpanel">
                                        <div class="row pt-2 specification">
                                            <?php
                                            $dateRange = explode(' - ', $getfirst['startandend_date'] ?? "");
                                            $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
                                            $endDate = isset($dateRange[1]) ? $dateRange[1] : '';

                                            if ($startDate && $endDate) {
                                                $start = new DateTime($startDate);
                                                $end = new DateTime($endDate);
                                                $difference = $start->diff($end)->days;
                                                if (isset($getfirst['customized_type']) && isset($getfirst['customized_dates'])) {
                                                    $customizedType = $getfirst['customized_type'];
                                                    $customizedDates = json_decode($getfirst['customized_dates'] ?? "[]", true);
                                                    switch ($customizedType) {
                                                        case 1:
                                                            $today = new DateTime();
                                                            $nextDates = [];
                                                            foreach ($customizedDates as $day) {
                                                                $next = new DateTime("next " . $day);
                                                                // if (strtolower($today->format('l')) == strtolower($day)) {
                                                                //     $next = clone $today;
                                                                // }
                                                                $nextDates[] = $next;
                                                            }
                                                            usort($nextDates, function ($a, $b) {
                                                                return $a <=> $b;
                                                            });
                                                            $startDate = $nextDates[0]->format("Y-m-d");
                                                            $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
                                                            break;

                                                        case 2:
                                                            $dayNumbers = array_map(function ($date) {
                                                                return (int)date('d', strtotime($date));
                                                            }, $customizedDates);
                                                            $today = new DateTime();
                                                            $nextDates = [];
                                                            foreach ($dayNumbers as $dayNumber) {
                                                                $next = new DateTime($today->format('Y-m-' . sprintf('%02d', $dayNumber)));
                                                                if ($next < $today) {
                                                                    $next->modify('+1 month');
                                                                }
                                                                $nextDates[] = $next;
                                                            }
                                                            usort($nextDates, function ($a, $b) {
                                                                return $a <=> $b;
                                                            });

                                                            $startDate = $nextDates[0]->format("Y-m-d");
                                                            $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
                                                            break;

                                                        case 3:
                                                            $today = new DateTime();
                                                            $nextDates = [];
                                                            foreach ($customizedDates as $dateStr) {
                                                                $date = DateTime::createFromFormat('Y-m-d', $dateStr);
                                                                $monthDay = $date->format('m-d');
                                                                $currentYearDate = DateTime::createFromFormat('Y-m-d', $today->format('Y') . '-' . $monthDay);
                                                                if ($currentYearDate < $today) {
                                                                    $currentYearDate->modify('+1 year');
                                                                }
                                                                $nextDates[] = $currentYearDate;
                                                            }
                                                            usort($nextDates, function ($a, $b) {
                                                                return $a <=> $b;
                                                            });
                                                            $startDate = $nextDates[0]->format("Y-m-d");
                                                            $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("d/m/Y");;
                                                            break;
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="col-12 row mt-2">
                                                <div class="col-12 table-responsive">
                                                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle">
                                                        <tbody>
                                                            @if ($getfirst['use_date'] == 1)
                                                            <tr class="d-none d-sm-block d-block d-sm-none">
                                                                <td>
                                                                    <i class="fa fa-calendar" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                                                </td>
                                                                <td colspan="2">
                                                                    <label for="dateInput">{{ translate('Select your booking date') }}</label>
                                                                    <div class="date-input-container" style="position: relative;">
                                                                        <input type="text" id="dateInput" class="form-control" placeholder="Click to select a date" readonly>
                                                                        <i class="fas fa-calendar-alt calendar-icon" id="calendarIcon"></i>
                                                                    </div>
                                                                    <div class="calendar-container" id="calendarContainer">
                                                                        <div class="calendar-header">
                                                                            <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                                                                            <span id="currentMonth">Month Year</span>
                                                                            <button id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                                                                        </div>
                                                                        <div class="calendar-grid" id="calendarGrid">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            <tr class="d-block d-sm-none">
                                                                <td><i class="fa fa-calendar" aria-hidden="true" style="color: var(--primary-clr);"></i></td>
                                                                <td colspan="2">
                                                                    @if ($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                                                    <span class="font-weight-bold">{{ translate('Arrival Date') }}</span> <br>
                                                                    <input class="form-control hasDatepicker text-align-direction" type="text" name="booking_date" id="bookingdate" placeholder="Booking Slot Date" onchange="$('.pickup_date').val(this.value)" onclick="datePicker(this)" input-mode="text" autocomplete="off" required>
                                                                    @else
                                                                    <span class="font-weight-bold">{{ translate('Arrival Date') }}</span> <br>
                                                                    <span class="start-date-arrival">{{ date('d M, Y', strtotime($startDate)) }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr class="d-block d-sm-none">
                                                                <td>
                                                                    @if ($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                                                    <i class="fa fa-clock-o" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                                                    @else
                                                                    <i class="fa fa-calendar" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                                                    @endif
                                                                </td>
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
                                                                    <span class="start-date-departure">{{ date('d M, Y', strtotime($endDate)) }}</span>
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
                                                                    <span class="start-date-arrival">{{ date('d M, Y', strtotime($startDate)) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($getfirst['use_date'] == 0 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4)
                                                                    @if($getfirst['time_slot'] && json_decode($getfirst['time_slot'],true))
                                                                    <span class="font-weight-bold">{{ translate('Time Slot') }}</span> <br>
                                                                    <select name="date" class="form-control time_slot_pickupopen_time" onchange="$('.pickup_time').val($(this).val())">
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
                                                                    <span class="start-date-departure">{{ date('d M, Y', strtotime($endDate)) }}</span>
                                                                    @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @if ($getfirst['use_date'] == 1)
                                                            <tr class="d-none d-sm-block d-block d-sm-none">
                                                                <td><i class="fa fa-clock-o" aria-hidden="true" style="color: var(--primary-clr);"></i></td>
                                                                <td colspan='2'>
                                                                    <span class="font-weight-bold">{{ translate('Arrival Time') }}</span><br>
                                                                    {{ $getfirst['pickup_time'] ?? '' }}
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            @if (($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 4) && $is_person_check == 0)
                                                            <tr class="d-none d-sm-block d-block d-sm-none">
                                                                <td><i class="fa fa-map-marker" aria-hidden="true" style="color: var(--primary-clr);"></i></td>
                                                                <td colspan='2'>
                                                                    <span class="font-weight-bold">{{ translate('Pickup Location') }} as</span><br>
                                                                    {{ $getfirst['pickup_location'] ?? '' }}
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle">
                                                        <tbody>
                                                            @if(($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 4) && $is_person_check == 1)
                                                            <tr>
                                                                <td colspan="3" class="font-size-13 d-table-cell d-sm-none">
                                                                    <i class="fa fa-map-marker" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                                                    {{ translate('pickup') }}
                                                                    <div class="d-flex flex-column mt-2">
                                                                        <label class="font-weight-bold location-type font-size-13 pickdrop-active button-boarder-set">
                                                                            <input type="radio" name="pickuplats" class="pickuplats"
                                                                                onclick="transportsOption(this)" data-type="free" data-type1="pick" checked>
                                                                            {{ translate('Fixed Location') }} <small class="text-success">({{ translate('free')}})</small>
                                                                        </label>

                                                                        <label class="font-weight-bold place-name font-size-13 button-boarder-set mt-1">
                                                                            <input type="radio" name="pickuplats" class="pickuplats"
                                                                                onclick="transportsOption(this)" data-type="air" data-type1="pick">
                                                                            <i class="fa fa-plane"></i> {{ translate('nearest Airport') }}
                                                                            <small class="text-danger change_paid_text">({{ translate('paid')}})</small>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="pickup-row d-none d-sm-table-row">
                                                                <td class="font-size-13 p-0">
                                                                    <i class="fa fa-map-marker" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                                                    {{ translate('pickup') }}
                                                                </td>
                                                                <td class="p-0">
                                                                    <span class="font-weight-bold location-type font-size-13 pickdrop-active button-boarder-set">
                                                                        <input type="radio" name="pickuplats" class="pickuplats"
                                                                            onclick="transportsOption(this)" data-type="free" data-type1="pick" checked>
                                                                        {{ translate('Fixed Location') }} <small class="text-success">({{ translate('free')}})</small>
                                                                    </span>
                                                                </td>
                                                                <td class="p-0">
                                                                    <span class="font-weight-bold place-name font-size-13 button-boarder-set">
                                                                        <input type="radio" name="pickuplats" class="pickuplats"
                                                                            onclick="transportsOption(this)" data-type="air" data-type1="pick">
                                                                        <i class="fa fa-plane"></i> {{ translate('nearest Airport') }}
                                                                        <small class="text-danger change_paid_text">({{ translate('paid')}})</small>
                                                                    </span>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="px-1 py-2" colspan="3">
                                                                    <span class="font-size-13 pickup-row-location">
                                                                        {{ $getfirst['pickup_location'] ?? '' }}
                                                                    </span>
                                                                </td>

                                                            </tr>
                                                            <!-- Mobile view -->
                                                            <tr>
                                                                <td colspan="3" class="font-size-13 d-table-cell d-sm-none">
                                                                    <i class="fa fa-map-marker" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                                                    {{ translate('drop') }}

                                                                    <div class="d-flex flex-column mt-2">
                                                                        <label class="font-weight-bold location-type font-size-13 pickdrop-active button-boarder-set mb-1">
                                                                            <input type="radio" name="droplats" class="droplats"
                                                                                onclick="transportsOption(this)" data-type="free" data-type1="drop" checked>
                                                                            {{ translate('Fixed Location') }} <small class="text-success">({{ translate('free')}})</small>
                                                                        </label>

                                                                        <label class="font-weight-bold place-name font-size-13 button-boarder-set">
                                                                            <input type="radio" name="droplats" class="droplats"
                                                                                onclick="transportsOption(this)" data-type="air" data-type1="drop">
                                                                            <i class="fa fa-plane"></i> {{ translate('nearest Airport') }}
                                                                            <small class="text-danger change_paid_text">({{ translate('paid')}})</small>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <!-- Desktop view -->
                                                            <tr class="drop-row d-none d-sm-table-row">
                                                                <td class="font-size-13 p-0">
                                                                    <i class="fa fa-map-marker" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                                                    {{ translate('drop') }}
                                                                </td>
                                                                <td class="p-0">
                                                                    <span class="font-weight-bold location-type font-size-13 pickdrop-active button-boarder-set">
                                                                        <input type="radio" name="droplats" class="droplats"
                                                                            onclick="transportsOption(this)" data-type="free" data-type1="drop" checked>
                                                                        {{ translate('Fixed Location') }} <small class="text-success">({{ translate('free')}})</small>
                                                                    </span>
                                                                </td>
                                                                <td class="p-0">
                                                                    <span class="font-weight-bold place-name font-size-13 button-boarder-set">
                                                                        <input type="radio" name="droplats" class="droplats"
                                                                            onclick="transportsOption(this)" data-type="air" data-type1="drop">
                                                                        <i class="fa fa-plane"></i> {{ translate('nearest Airport') }}
                                                                        <small class="text-danger change_paid_text">({{ translate('paid')}})</small>
                                                                    </span>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="px-1 py-2" colspan="3">
                                                                    <span class="font-size-13 drop-row-location">
                                                                        {{ $getfirst['pickup_location'] ?? '' }}
                                                                    </span>
                                                                </td>

                                                            </tr>
                                                            <tr class="">
                                                                <td colspan='3'>
                                                                    <span class="font-weight-bold d-none"><input type="checkbox" class="only-pickup extracharges-transport" data-id="only-pickup" data-type="Pickup" data-type1="pick" onclick="transportOption(this)">&nbsp;Only Pickup</span>
                                                                    <span class="font-weight-bold d-none"><input type="checkbox" class="only-droup extracharges-transport" data-id="only-droup" data-type="Drop" data-type1="drop" onclick="transportOption(this)">&nbsp;Only Droup</span>
                                                                    <span class="font-weight-bold d-none"><input type="checkbox" class="only-both extracharges-transport" data-id="only-both" data-type="Both" data-type1="both" onclick="transportOption(this)">&nbsp;Both</span>
                                                                    <span class="extransportPrice font-wight-bolder text-primary font-size-13"></span>
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

                                    <div class="tab-pane fade text-justify pay_summary_point1" id="pay_summary" role="tabpanel">
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
                                        <div class="row px-3 tab-booking-data"></div>
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

                                        <!--  {{-- @if(($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 3 || $getfirst['use_date'] == 4 ) && ($getfirst['is_person_use'] == 0))
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
                                        @endif --}} -->


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
                                <div class="pt-2">
                                    <button class="btn btn--primary cab-tab-font-header button-padding-five-tan" id="prev-tab" disabled>{{ translate('Previous')}}</button>
                                </div>
                                <div class="pt-2">
                                    <span id="tab-counter" class="align-self-center">
                                        <div style="padding: 2px 0px;">
                                            <span class="food-hotal-lists font-weight-bolder px-2 py-1 rounded small d-flex"></span>
                                        </div>
                                        @if($is_person_check == 1)
                                        <span class="small d-flex hotel-title-message-show-hotel font-weight-bolder px-3 py-1 rounded" style="background-color: #f7d9a2;"></span>
                                        @endif
                                        <span class="small d-flex step-text ps-3">Step 1 of 2</span>
                                    </span>
                                </div>
                                <div class="pt-2">
                                    <button class="btn btn--primary cab-tab-font-header button-padding-five-tan" id="next-tab">{{ translate('Next')}}</button>

                                    <button class="btn btn-success save_allPackage cab-tab-font-header button-padding-five-tan d-none name_change_continues " id="submit-tab" onclick="$('.razer_pay_opens').click()">pay now</button>
                                </div>
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



<div class="pda10 text-center">
    <?php
    $dateRange = explode(' - ', $getfirst['startandend_date'] ?? "");
    $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
    $endDate = isset($dateRange[1]) ? $dateRange[1] : '';

    if ($startDate && $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $difference = $start->diff($end)->days;
        if (isset($getfirst['customized_type']) && isset($getfirst['customized_dates'])) {
            $customizedType = $getfirst['customized_type'];
            $customizedDates = json_decode($getfirst['customized_dates'] ?? "[]", true);
            switch ($customizedType) {
                case 1:
                    $today = new DateTime();
                    $nextDates = [];
                    foreach ($customizedDates as $day) {
                        $next = new DateTime("next " . $day);
                        // if (strtolower($today->format('l')) == strtolower($day)) {
                        //     $next = clone $today;
                        // }
                        $nextDates[] = $next;
                    }
                    usort($nextDates, function ($a, $b) {
                        return $a <=> $b;
                    });
                    $startDate = $nextDates[0]->format("Y-m-d");
                    break;

                case 2:
                    $dayNumbers = array_map(function ($date) {
                        return (int)date('d', strtotime($date));
                    }, $customizedDates);
                    $today = new DateTime();
                    $nextDates = [];
                    foreach ($dayNumbers as $dayNumber) {
                        $next = new DateTime($today->format('Y-m-' . sprintf('%02d', $dayNumber)));
                        if ($next < $today) {
                            $next->modify('+1 month');
                        }
                        $nextDates[] = $next;
                    }
                    usort($nextDates, function ($a, $b) {
                        return $a <=> $b;
                    });

                    $startDate = $nextDates[0]->format("Y-m-d");
                    break;

                case 3:
                    $today = new DateTime();
                    $nextDates = [];
                    foreach ($customizedDates as $dateStr) {
                        $date = DateTime::createFromFormat('Y-m-d', $dateStr);
                        $monthDay = $date->format('m-d');
                        $currentYearDate = DateTime::createFromFormat('Y-m-d', $today->format('Y') . '-' . $monthDay);
                        if ($currentYearDate < $today) {
                            $currentYearDate->modify('+1 year');
                        }
                        $nextDates[] = $currentYearDate;
                    }
                    usort($nextDates, function ($a, $b) {
                        return $a <=> $b;
                    });
                    $startDate = $nextDates[0]->format("Y-m-d");
                    break;
            }
        }
    }
    ?>
    @foreach ($payment_gateways_list as $payment_gateway)
    <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form" action="{{ route('tour-payment-request', [$getfirst['slug']]) }}" onsubmit="return formcheck_check()">
        @csrf
        <div class="Details">
            <input type="hidden" name="booking_date" value="{{ date('Y-m-d H:i:s') }}">
            <input type="hidden" name="tour_id" value="{{ $getfirst['id'] }}">
            <input type="hidden" name="use_date" value="{{ $getfirst['use_date'] }}">
            <input type="hidden" name='pickup_date' class="pickup_date" value="{{ ($startDate)}}">
            <input type="hidden" name='pickup_time' class="pickup_time" value="{{ $getfirst['pickup_time']??''}}">
            <input type="hidden" name='pickup_address' class="pickup_address" value="{{ $getfirst['pickup_location']??''}}">
            <input type="hidden" name='pickup_lat' class="pickup_lat" value="{{ $getfirst['pickup_lat']??''}}">
            <input type="hidden" name='pickup_long' class="pickup_long" value="{{ $getfirst['pickup_long'] ?? '' }}">

            <input type="hidden" name="package_id" value="{{ ($getleads['package_id']??'') }}">
            <input type="hidden" name="leads_id" value="{{ ($getleads['id']??'') }}">
            <input type="hidden" name="coupon_amount" value="" class='coupon_amount Coupon_apply_discount discount_show' data-discouponamount="0">
            <input type="hidden" name="coupon_id" value="" class='coupon_id Coupon_apply_id'>

            @if($getfirst['use_date'] == 1 || $getfirst['use_date'] == 4)
            <input type="hidden" name='available_seat_cab_id' class="available_seat_cab_id" value='0'>
            <input type="hidden" name='totals_seat_cab_id' class="totals_seat_cab_id" value='0'>
            @endif
            <input type="hidden" class="total_pay_amount" value="{{ $cab_price }}">
            <input type="hidden" name='part_payment' class="part_payment_type" value="full">
            <input type="hidden" name='wallet_type' class="user-wallet-adds">
            <input type="hidden" name="payment_amount" class="mainProductPriceInput" value="{{ $cab_price }}">
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn--primary btn-block font-weight-bold name_change_continues d-none razer_pay_opens">{{ translate('Proceed_To_Checkout') }}</button>
        </div>
    </form>
    @endforeach
</div>

@endsection
@push('script')

<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}" type="text/javascript"></script>

<script>
    const MultiArrayPush = [];
</script>
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
                    price2: 0,
                    hotal_remaining: 0,
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
                existingItem.price2 = (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0;
            } else {
                MultiArrayPush.push({
                    type: 'ex_distance',
                    id: '0',
                    qty: (Math.ceil(ex_distance * 100) / 100) || 0,
                    price: (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0,
                    price2: (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0,
                });
            }

            let existingItemRoute = MultiArrayPush.find(item => item.type === 'route');

            let types = $('.extracharges-transport:checked').data('type')
            if (types && "{{ $getfirst['is_person_use']}}" == 1) {
                way_type = types;
            }
            if (existingItemRoute) {
                existingItemRoute.price = way_type ?? "two_way";
                existingItemRoute.price2 = way_type ?? "two_way";
            } else {
                MultiArrayPush.push({
                    type: 'route',
                    id: 0,
                    qty: 0,
                    price: way_type ?? "two_way",
                    price2: way_type ?? "two_way",
                });
            }

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
        leadBackFun()
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

        let useDate = "{{ $getfirst['use_date'] }}";
        let timeSlot = @json(json_decode($getfirst['time_slot'] ?? '[]', true));

        if ((useDate == "0" || useDate == "2" || useDate == "3" || useDate == "4") && timeSlot && timeSlot.length > 0) {
            $('.time_slot_pickupopen_time').removeClass('is-invalid');
        } else {
            $('.pickupopen_time').removeClass('is-invalid');
        }


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
            if ((useDate == "0" || useDate == "2" || useDate == "3" || useDate == "4") && timeSlot && timeSlot.length > 0) {
                $('.time_slot_pickupopen_time').focus();
                $('.time_slot_pickupopen_time').addClass('is-invalid');
            } else {
                $('.pickupopen_time').focus();
                $('.pickupopen_time').addClass('is-invalid');
            }

            checkvalid = false;
        }
        if (checkvalid) {
            // $(".addOtherpackages").modal('hide');
            leadBackFun()
            $.ajax({
                url: "{{ route('tour.booking-tab-amount')}}",
                data: {
                    item: MultiArrayPush,
                    id: "{{ $getfirst['id']}}",
                    _token: "{{ csrf_token() }}"
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

                    let normalItems = [];
                    let gstTransportItems = [];

                    data.data.forEach((key) => {
                        if (["gst", "sgst", "transport"].includes(key.type)) {
                            gstTransportItems.push(key);
                        } else {
                            normalItems.push(key);
                        }
                    });

                    let finalItems = [...normalItems, ...gstTransportItems];
                    $.each(finalItems, function(index, key) {
                        <?php if ($getfirst['is_person_use'] == 0) { ?>
                            // if (key.type != 'ex_distance') {
                            if ("{{$getfirst['use_date']}}" != 1 || ("{{$getfirst['use_date']}}" == 1 && ["ex_distance", "sgst",'gst','cab','route'].includes(key.type))) {
                                if ((!array_use.includes(key.type) && key.type != 'ex_distance') || (key.type == 'ex_distance' && Number(key.price) > 0)) {
                                    html += `<div class="col-12 mt-2">
                                    <div class="row">
                                        <div class="col-4">`;
                                    if (key.type == 'gst' || key.type == 'sgst' || key.type == 'ex_distance') {
                                        html += `<div class="small font-weight-bold"><span>${key.title}</span></div>`;
                                    } else {
                                        html += `<div class="small">                                            
                                                <span>${key.name}</span>
                                                <span>${((key.type == 'cab') ? ` (${key.seats} seats)`:'')} </span>
                                            </div>`;
                                    }
                                    html += `</div>
                                        <div class="col-4">
                                        `;
                                    if (key.type == 'gst' || key.type == 'sgst') {} else if (key.type == 'ex_distance') {
                                        html += `<div class="small font-weight-bold">
                                                <span>${((key.qty > 20)? key.qty+' KM':'')}</span>
                                            </div>`;
                                    } else {
                                        html += `<div class="small font-weight-bold">
                                                <span>${key.qty} ${((key.type == 'cab'  && ("{{$getfirst['use_date']}}" == '0'))?'cab':'People')}</span>
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
                            }
                        <?php } else { ?>
                            if ((!array_use.includes(key.type) && key.type != 'ex_distance') || (key.type == 'ex_distance' && Number(key.price) > 0)) {
                                html += `<div class="col-12 mt-2">
                                    <div class="row">
                                        <div class="col-6">`;
                                if (key.type == 'gst' || key.type == 'sgst' || key.type == 'transport') {
                                    html += `<div class="small font-weight-bold"><span>${key.title}</span></div>`;
                                } else {
                                    html += `<div class="small font-weight-bold"><span>${key.title??key.name} (Qty. ${key.qty})</span></div>`;
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
        leadBackFun()
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
        const tabCounter = document.querySelector('#tab-counter .step-text'); //document.getElementById('tab-counter');
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

            let PerHeads = MultiArrayPush.find(item => item.type === 'per_head');
            if (PerHeads) {
                $(".hotel-title-message-show-hotel").text(`{{ translate('Total Persons')}}: ${PerHeads.qty}`);
            }
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
        leadBackFun();
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

        // $('.spcab_packages_data').each(function() {
        //     var amo1 = $(this).data('manamount');
        //     var keys = $(this).data('keys');
        //     $(`.spcab_packages_data${keys}`).text(amo1.toLocaleString("en-US", {
        //         style: "currency",
        //         currency: "{{getCurrencyCode()}}"
        //     }));
        // });
        // $('.spcab_packages_data').each(function() {
        //     const amountp = parseFloat($(this).data('manamount')) || 0;
        //     const key = $(this).data('keys');
        //     const target = $(`.spcab_packages_data[data-keys="${key}"]`);
        //     if (target.length) {
        //         target.text(
        //             amountp.toLocaleString("en-US", {
        //                 style: "currency",
        //                 currency: "{{ getCurrencyCode() }}" // works only in Blade
        //             })
        //         );
        //     }
        // });
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
    let currentHotel = null;

    function addcustomhotels(id) {
        if ("{{$getfirst['is_person_use']}}" == 1 && $(`.${id}`).data('gettype') == 'hotel') {
            let selectedHotel = $(`.${id}`).data('hotalname');
            if (currentHotel && currentHotel !== selectedHotel) {
                $(`[data-hotalname="${currentHotel}"]`).each(function() {
                    let $container = $(this).closest('[class*="hotels_package_add_hotel"]');
                    $container.find('input[type="number"]').val(1);
                    $container.find('.cab-button-plus-minus').first().trigger('click');
                });

            }
            currentHotel = selectedHotel;
        }
    }


    function newaddpackages(inde, id, amount, that) {
        let input = $(`.${id}`).val();
        let checkmax = $(`.${id}`).attr("max");
        addcustomhotels(id);
        const type1 = $(that).data('type1');
        let type = $(that).data('type');
        let perHeads = MultiArrayPush.findIndex(item => item.type == "per_head");
        let totalSeats = MultiArrayPush.filter(item => item.newkey).reduce((sum, item) => sum + (item.numberofseat || 0), 0);
        let perHeadIndex;
        if ("{{ $getfirst['is_person_use'] }}" == 0) {
            perHeadIndex = MultiArrayPush.findIndex(item => item.type == "cab");
        } else {
            perHeadIndex = MultiArrayPush.findIndex(item => item.type == "per_head");
            let personsNeeded = perHeadIndex !== -1 ? Number(MultiArrayPush[perHeadIndex].qty) : 0;
            if (inde === 'in') {
                if (totalSeats >= personsNeeded) {
                    toastr.error("Hotel rooms already cover the required persons");
                    return false;
                }
            }
        }
        if (inde == 'in' && checkmax < (Number(input) + 1)) {
            $(`.OnepersonMessageShow${type}`).html(`<i class="tio-warning_outlined">warning_outlined</i>Max ${checkmax}`);
            return false;
        } else {
            $(`.OnepersonMessageShow${type}`).html('');
        }
        if (inde == 'in') {
            $(`.${id}`).val((Number(input) + 1))
        } else {
            if (input > 1) {
                $(`.${id}`).val((Number(input) - 1))
            } else if (input == 1 && (type1 == 'hotel' || type1 == 'foods' || type1 == 'other')) {
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


        <?php if (($getfirst['use_date'] == 1 || $getfirst['use_date'] == 4) && $getfirst['is_person_use'] == 0) { ?>
            sub_qtys(type1, ids, qty, amount);
            <?php if (!empty($getfirst['package_list_price']) && is_array(json_decode($getfirst['package_list_price'], true))) {
                foreach (json_decode($getfirst['package_list_price'], true) as $plis) {
                    $tourPackages = \App\Models\TourPackage::where('id', $plis['package_id'])->first();
            ?>
                    sub_qtys("{{$tourPackages['type']}}", "{{ $plis['package_id'] }}", qty, `{{$plis['pprice']}} * ${qty}`);
            <?php  }
            }
        } else { ?>
            sub_qtys(type, ids, qty, amount);
            hotalmessageshow();
        <?php } ?>
        <?php if (($getfirst['use_date'] == 0) && $getfirst['is_person_use'] == 0) { ?>
            if ($(that).data('type1') == 'cab') {
                $('.included_max_packages').attr('max', $(`.${id}`).val());
            }
        <?php } ?>
    }

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
                existingItem.qty = parseInt(qty);

                if (("{{$getfirst['use_date']}}" == 2 || "{{$getfirst['use_date']}}" == 3 || "{{$getfirst['use_date']}}" == 4) && type == 'cab') {
                    existingItem.price = (parseFloat(price));
                    existingItem.price2 = (parseFloat(price));
                } else {
                    existingItem.price = (parseFloat(price) * parseInt(qty));
                    existingItem.price2 = (parseFloat(price) * parseInt(qty));
                }
            }

        } else if (qty > 0) {
            let price1 = (parseFloat(price) * parseInt(qty));
            if (("{{$getfirst['use_date']}}" == 2 || "{{$getfirst['use_date']}}" == 3 || "{{$getfirst['use_date']}}" == 4) && type == 'cab') {
                price1 = (parseFloat(price));
            }
            MultiArrayPush.push({
                type,
                id: parseInt(id),
                qty: parseInt(qty),
                price: price1,
                price2: price1
            });

        }
        calculateDistance();
        driverandCabExCharge();

        if ("{{$getfirst['is_person_use']}}" == 0 && ("{{$getfirst['use_date']}}" == "1" || "{{$getfirst['use_date']}}" == "2" || "{{$getfirst['use_date']}}" == "3" || "{{$getfirst['use_date']}}" == "4")) {
            let indexchecks = MultiArrayPush.findIndex(item => item.type === "cab");
            $('.available_seat_cab_id').val(MultiArrayPush[indexchecks]['id']);
            $('.qty_order').val(MultiArrayPush[indexchecks]['qty']);
            $('.total_pay_amount').val(MultiArrayPush[indexchecks]['price']);
            $('.mainProductPriceInput').val(MultiArrayPush[indexchecks]['price']);
            $('.part_full_pay1').addClass('active');
            $('.part_full_pay2').removeClass('active');
            $('.part_full_pay1,.part_full_pay2').data('amount', MultiArrayPush[indexchecks]['price']);
            $('.part_full_pay1').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cc.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('full')}} ${(MultiArrayPush[indexchecks]['price']).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
            $('.part_full_pay2').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cash-in-hand.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('part')}} ${(MultiArrayPush[indexchecks]['price']/2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);

            $('.coupan_amount_min').val(MultiArrayPush[indexchecks]['price']);
            $(".getallproducts").val(JSON.stringify(MultiArrayPush));
            if (MultiArrayPush[indexchecks]['type'] == 'cab') {
                $(`.spcab_packages_data${MultiArrayPush[indexchecks]['id']}`).text(MultiArrayPush[indexchecks]['price'].toLocaleString("en-US", {
                    style: "currency",
                    currency: "{{getCurrencyCode()}}"
                }));
            }
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
                                            <div class="font-weight-bold product-package-total_amount" style="display: flex;" data-amount="${MultiArrayPush[indexchecks]['price']}"> ${MultiArrayPush[indexchecks]['price'].toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})} </div>
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
            backdrop: 'static',
            keyboard: false
        });
    }
    $('.addOtherpackages').on('shown.bs.modal', function() {
        $('.mobile-book-btn').removeClass('d-block');
        $('.mobile-book-btn').hide();
    });

    // जब modal बंद हो
    $('.addOtherpackages').on('hidden.bs.modal', function() {
        $('.mobile-book-btn').addClass('d-block');
        $('.mobile-book-btn').show(); // show button
    });
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

            return getToken(messaging, {
                serviceWorkerRegistration: registration,
                vapidKey: "{{ env('VAPID_KEY') }}"
            });
        })
        .then((token) => {
            if (token) {

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
                    toastr.error('Coupon not available');
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
        if ("{{ $getfirst['is_person_use']}}" == 0 && "{{ $getfirst['use_date']}}" == 3 && (MultiArrayPush[index_remmm_route]['id'] ?? '')) {
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
        $(`.OnepersonMessageShow`).html('');
        if (type == 'de' && number > $(that).data('min')) {
            $('.person_use_input').val(0);
            $(`.person_use_input${$(that).data('key')}`).val(Number(number) - 1);
        } else if (type == 'in' && number < $(that).data('max')) {
            $('.person_use_input').val(0);
            $(`.person_use_input${$(that).data('key')}`).val(Number(number) + 1);
        } else {
            if (type == 'de') {
                $(`.personMessageShow${$(that).data('key')}`).text(`You must book at least ${$(that).data('min')} for this unit`);
                $(`.OnepersonMessageShow${$(that).data('key')}`).html(`<i class="tio-warning_outlined">warning_outlined</i>Min ${$(that).data('min')}`);
            } else if (type == 'in') {
                $(`.personMessageShow${$(that).data('key')}`).text(`you can only select up to ${$(that).data('max')}`);
                $(`.OnepersonMessageShow${$(that).data('key')}`).html(`<i class="tio-warning_outlined">warning_outlined</i>Max ${$(that).data('max')}`);
            }
        }
        $(`.person_total_amounts`).html('');
        var number = $(`.person_use_input${$(that).data('key')}`).val();
        var message = (number * price).toLocaleString("en-US", {
            style: "currency",
            currency: "{{ getCurrencyCode() }}"
        });
        $(`.person_total_amounts${$(that).data('key')}`).html(message);

        let PerHeads = MultiArrayPush.find(item => item.type === 'per_head');
        if (PerHeads) {
            PerHeads.price = number * price;
            PerHeads.price2 = number * price;
            PerHeads.qty = number;
            PerHeads.id = $(that).data('id');
            PerHeads.hotal_remaining = number;
            hotalmessageshow();
        } else {
            MultiArrayPush.push({
                type: 'per_head',
                id: $(that).data('id'),
                qty: number,
                price: number * price,
                price2: number * price,
                hotal_remaining: number,
            });
        }

        setTimeout(() => {
            include_functions(number);
        }, 2000);

        <?php if ($getfirst['use_date'] == 1) {  ?>
            let PerHeadsUsers = MultiArrayPush.find(item => item.type === 'per_head');
            $('.total_pay_amount').val(PerHeadsUsers.price);
            let gstPercent = "{{ \App\Models\ServiceTax::first()['tour_tax'] ?? 1 }}";
            let priceA = parseFloat(PerHeadsUsers.price);
            let gstAmount = (priceA * gstPercent) / 100;
            // let newPrice = priceA - gstAmount;
            let newPrice = priceA;
            priceA = priceA + gstAmount;
            if (PerHeadsUsers) {
                PerHeadsUsers.title = "person";
                PerHeadsUsers.price = newPrice;
                PerHeadsUsers.price2 = priceA;
            }


            // let cgstItem = MultiArrayPush.find(item => item.type === 'cgst');
            // if (cgstItem) {
            //     cgstItem.price = gstAmount / 2;
            //     cgstItem.title = `CGST (${gstAmount / 2}%)`;
            // } else {
            //     MultiArrayPush.push({
            //         type: 'cgst',
            //         id: 0,
            //         qty: '',
            //         price: gstAmount / 2,
            //         title: `CGST (${gstAmount / 2}%)`
            //     });
            // }

            // // Add/Update SGST
            // let sgstItem = MultiArrayPush.find(item => item.type === 'sgst');
            // if (sgstItem) {
            //     sgstItem.price = gstAmount / 2;
            //     sgstItem.title = `SGST (${gstAmount / 2}%)`;
            // } else {
            //     MultiArrayPush.push({
            //         type: 'sgst',
            //         id: 0,
            //         qty: '',
            //         price: gstAmount / 2,
            //         title: `SGST (${gstAmount / 2}%)`
            //     });
            // }
            // $(".tab-booking-total_amount").html(`<div class="col-12 mt-2">
            //                         <hr>
            //                     </div>
            //                     <div class="col-12 py-2 px-3">
            //                         <div class="row">
            //                             <div class="col-4">
            //                                 <div class="font-weight-bold" style="display: flex;">{{ translate('price') }}</div>
            //                             </div>
            //                             <div class="col-4">
            //                              </div>
            //                             <div class="col-4">
            //                                 <div class="font-weight-bold" style="display: flex;"> ${newPrice.toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}  </div>
            //                             </div>
            //                         </div>
            //                     </div>
            //                     <div class="col-12 py-2 px-3">
            //                         <div class="row">
            //                             <div class="col-4">
            //                                 <div class="font-weight-bold" style="display: flex;">{{ translate('total_tax') }}</div>
            //                             </div>
            //                             <div class="col-4">
            //                              </div>
            //                             <div class="col-4">
            //                                 <div class="font-weight-bold align-self-center" style="display: flex;font-size: 13px;"> (CGST ${(gstAmount /2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"}) }) </div>
            //                                 <div class="font-weight-bold align-self-center" style="display: flex;font-size: 13px;"> (SGST ${(gstAmount /2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"}) }) </div>
            //                             </div>
            //                         </div>
            //                     </div>
            //                     <div class="col-12 py-2 px-3">
            //                         <div class="row">
            //                             <div class="col-4">
            //                                 <div class="font-weight-bold" style="display: flex;">{{ translate('price') }}</div>
            //                             </div>
            //                             <div class="col-4">
            //                              </div>
            //                             <div class="col-4">
            //                                 <div class="font-weight-bold product-package-total_amount" style="display: flex;" data-amount="${(priceA)}"> ${(priceA).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})} </div>
            //                             </div>
            //                         </div>
            //                     </div>`);
            $('.qty_order').val(PerHeadsUsers.qty);

            $('.mainProductPriceInput').val((priceA));
            $('.part_full_pay1').addClass('active');
            $('.part_full_pay2').removeClass('active');
            $('.part_full_pay1,.part_full_pay2').data('amount', (priceA));
            $('.part_full_pay1').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cc.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('full')}} ${((priceA)).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
            $('.part_full_pay2').html(`<img width="40" src="{{ theme_asset(path: 'public/assets/back-end/img/cash-in-hand.png') }}" style="margin-top: -9px;    float: inline-start;">{{ translate('part')}} ${((priceA)/2).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);

            $('.coupan_amount_min').val((number * price));
            $(".getallproducts").val(JSON.stringify(MultiArrayPush));


            var isChecked = $('.wallet_checked').prop('checked');
            let walletAmount = $('.wallet_checked').data('amount');
            let totalPrice = (priceA);
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
        leadBackFun();

        let key = $(that).data('key');
        let input = $(`.person_use_input${key}`);
        let currentVal = parseInt(input.val()) || 0;
        let newVal = currentVal - 1;
        if (newVal < 0) newVal = 0;
        $('.included_packages').val(newVal);
        $('.included_packages').attr('max', $(`.person_use_input${$(that).data('key')}`).val());
        $('.included_max_packages').attr('max', $(`.person_use_input${$(that).data('key')}`).val());
        $('.included_packages_plus').click();
    }

    function transportsOption(that) {
        let $input = $(that);
        let type1 = $input.data('type1');
        if (type1 === 'pick') {
            $('.pickuplats').closest('span').removeClass('pickdrop-active');
        } else if (type1 === 'drop') {
            $('.droplats').closest('span').removeClass('pickdrop-active');
        }
        $input.closest('span').addClass('pickdrop-active');
        let selectedType = $input.data('type');
        if (type1 === 'pick') {
            if (selectedType === 'air') {
                $('.pickup-row-location').hide();
            } else {
                $('.pickup-row-location').show();
            }
        }

        if (type1 === 'drop') {
            if (selectedType === 'air') {
                $('.drop-row-location').hide();
            } else {
                $('.drop-row-location').show();
            }
        }

        let pickupType = $('.pickuplats:checked').data('type') || '';
        let dropType = $('.droplats:checked').data('type') || '';

        $('.extracharges-transport').prop('checked', false);
        if (pickupType === 'air' && dropType === 'air') {
            $('.only-both').prop('checked', true);
        } else if (pickupType === 'air' && dropType === 'free') {
            $('.only-pickup').prop('checked', true);
        } else if (pickupType === 'free' && dropType === 'air') {
            $('.only-droup').prop('checked', true);
        } else {
            $('.extracharges-transport').prop('checked', false);
        }
        transportOption();
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
        let matchedGroup = gstgroupQty.find(item => parseInt(PerHeadsUsers.qty || 0) >= Number(item.min) && parseInt(PerHeadsUsers.qty || 0) <= Number(item.max));
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

        // if ($('.droplats:checked').data('type') == 'air' || $('.pickuplats:checked').data('type') == 'air') {
        if (parseInt(pricetrans) > 1) {
            $('.button-boarder-set small.change_paid_text').text('(Paid)');
            $('.button-boarder-set small.change_paid_text').addClass('text-danger');
            $('.button-boarder-set small.change_paid_text').removeClass('text-success');
        } else {
            $('.button-boarder-set small.change_paid_text').text('(Free)');
            $('.button-boarder-set small.change_paid_text').addClass('text-success');
            $('.button-boarder-set small.change_paid_text').removeClass('text-danger');
        }
        // }
        let transItem = MultiArrayPush.find(item => item.type === 'transport');
        if (transItem) {
            transItem.price = (pricetrans);
            transItem.price2 = (pricetrans);
            transItem.title = `Ex Transport`;
            transItem.qty = PerHeadsUsers.qty;
        } else {
            MultiArrayPush.push({
                type: 'transport',
                id: 0,
                qty: PerHeadsUsers.qty,
                price: (pricetrans),
                price2: (pricetrans),
                title: `Ex Transport`,
            });
        }

        if ((pricetrans) > 0) {
            $(".extransportPrice").html(`{{ translate('Total Additional Transportation Amount')}} : ${pricetrans.toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`);
        } else {
            $(".extransportPrice").html('');
        }
    }
</script>
<?php if ($getfirst['is_person_use'] == 1) {
    if ($getfirst['use_date'] == 1) { ?>
        <script>
            $(document).ready(function() {
                $('.per_specel_use_add{{$cab_index}}').click();
            });
        </script>
    <?php } else { ?>
        <script>
            $(document).ready(function() {
                $('.person_use_add0').click();
            });
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
<script>
    function leadBackFun() {
        $.ajax({
            url: "{{ url('api/v1/tour/booking-tab-tour')}}",
            data: {
                tour_id: "{{ $getfirst['id'] }}",
                lead_id: "{{ ($getleads['id']??'') }}",
                item: MultiArrayPush,
                pickup_address: $('.pickup_address').val().trim(),
                lat: $('.pickup_lat').val(),
                log: $('.pickup_long').val(),
                pickup_date: $('.pickup_date').val(),
                pickup_time: $('.pickup_time').val(),
                part_payment_type: $('.part_payment_type').val(),
                coupan_amount: $('.coupon_amount').val(),
                coupon_id: $('.Coupon_apply_id').val(),
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $('#loading').removeClass('d--none');
                $('#loading').css('index', 1000);
            },
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: "post",
            success: function(data) {
                $('#loading').addClass('d--none');
            }
        });
    }
</script>
<script>
    leadBackFun()
    $(document).ready(function() {
        $(".customBtn").on("click", function() {
            // $(".customOptions").slideToggle();
            $('[class*="filters-hotel-"]').addClass('d-none');
            $('[class*="hotels_package_add_hotel"]').find('input').val(1);
            $('[class*="hotels_package_add_hotel"]').each(function() {
                $(this).find('.cab-button-plus-minus').first().trigger('click');
            });
            $(".customOptions").slideToggle(function() {
                if ($(this).is(":visible")) {
                    $('.custom-class-add_hotel1').addClass('d-none');
                    $('.custom-class-addmd_hotel1').removeClass('d-sm-block');
                    $('.custom-class-addsm_hotel1').removeClass('d-sm-none');
                    let PerHeads = MultiArrayPush.find(item => item.type === 'per_head');
                    $(".hotel-title-message-show-hotel").text(`{{ translate('Total Persons')}}: ${PerHeads.qty}`);
                    $(".hotel-title-change-included").text('Custom booking active');
                    $(".hotel-title-change-included").addClass('small');
                    $('.filter-hotel[data-star="all"]').trigger('click');
                } else {
                    toastr.success("Remove Custom Hotels");
                    $('.custom-class-add_hotel1').removeClass('d-none');
                    $('.custom-class-addmd_hotel1').addClass('d-sm-block');
                    $('.custom-class-addsm_hotel1').addClass('d-sm-none');
                    // $(".hotel-title-message-show-hotel").text("");
                    $(".hotel-title-change-included").text('Included');
                    $(".hotel-title-change-included").removeClass('small');
                }
            });
        });
    });
    $(document).on('click', '.filter-hotel', function() {
        $(".filter-hotel").removeClass("active");
        $(this).addClass("active");
        let star = $(this).data('star');
        if (star === 'all') {
            $('[class*="filters-hotel-"]').removeClass('d-none');
        } else {
            $('[class*="filters-hotel-"]').addClass('d-none');
            $('.filters-hotel-' + star.replace(" ", "__")).removeClass('d-none');
        }
    });
</script>
<script>
    function include_functions(number, type_nu = '') {
        let GetOldRe = '';
        if (type_nu == 'hotel') {
            <?php if ($IncludedNewArrays) {
                foreach ($IncludedNewArrays as $val11) { ?>
                    GetOldRe = MultiArrayPush.find(item => item.type == '{{$val11["type"]}}');
                    if (GetOldRe) {
                        GetOldRe.qty = number;
                    } else {
                        MultiArrayPush.push({
                            type: "{{ $val11['type'] }}",
                            id: Number("{{ $val11['id'] }}"),
                            price: Number("{{ $val11['price'] }}"),
                            price2: Number("{{ $val11['price2'] }}"),
                            qty: number,
                        });
                    }
            <?php }
            } ?>
        } else if (type_nu == 'hotelrem') {
            let index = MultiArrayPush.findIndex(item => item.type == "{{ ($newIncludeHotel??'') }}");
            if (index !== -1) {
                MultiArrayPush.splice(index, 1);
            }
        } else {
            <?php if ($IncludedNewArrays) {
                foreach ($IncludedNewArrays as $val11) { ?>
                    GetOldRe = MultiArrayPush.find(item => item.type == '{{$val11["type"]}}');
                    if (GetOldRe) {
                        GetOldRe.qty = number;
                    } else {
                        MultiArrayPush.push({
                            type: "{{ $val11['type'] }}",
                            id: Number("{{ $val11['id'] }}"),
                            price: Number("{{ $val11['price'] }}"),
                            price2: Number("{{ $val11['price2'] }}"),
                            qty: number,
                        });
                    }
            <?php }
            } ?>
        }
    }
</script>
<script>
    function hotalmessageshow() {
        let PerHeads = MultiArrayPush.find(item => item.type === 'per_head');
        if ($(".hotel-title-message-show-hotel").text().length > 0) {
            $(".hotel-title-message-show-hotel").text(`{{ translate('Total Persons')}}: ${PerHeads.qty}`);
        }
    }

    function updateOldarray(that) {
        let typename2 = $(that).data('typename');
        let type = $(that).data('type');
        let seats = parseInt($(that).data('seats')) || 1;
        let perHeads = MultiArrayPush.findIndex(item => item.type == "per_head");
        if (typename2 === 'hotel') {
            let index_recrrct = MultiArrayPush.findIndex(item => item.type === type);
            if (index_recrrct !== -1) {
                MultiArrayPush[index_recrrct] = {
                    ...MultiArrayPush[index_recrrct],
                    newkey: typename2,
                    numberofseat: MultiArrayPush[index_recrrct].qty * seats,
                };
            }

            let totalSeats = MultiArrayPush.filter(item => item.newkey).reduce((sum, item) => sum + (item.numberofseat || 0), 0);
            var number = document.querySelector(".person_use_input").value;
            if (totalSeats > 0) {
                $(".food-hotal-lists").text(`Total Occupancy: ${totalSeats} person`);
                $('.food-hotal-lists').css({
                    "background-color": "#f7d9a2"
                });
                include_functions(number, 'hotelrem');
            } else {
                include_functions(number, 'hotel');
                $(".food-hotal-lists").text(``);
                $('.food-hotal-lists').removeAttr("style");
            }

            // let requiredPersons = Number(MultiArrayPush[perHeads].qty);  // booking persons
            // let totalRooms = MultiArrayPush[index_recrrct]?.qty || 0;    // selected rooms
            // let requiredRooms = Math.ceil(requiredPersons / seats);      // min rooms needed

            // // 🚨 If selected rooms are less than required
            // if (totalRooms < requiredRooms) {
            //     toastr.error(`You need at least ${requiredRooms} room(s) for ${requiredPersons} persons (capacity ${seats} per room)`);

            //     // auto-fix (optional)
            //     $(`.${$(that).data('newclass1')}`).val(requiredRooms);
            //     MultiArrayPush[index_recrrct].qty = requiredRooms;
            //     MultiArrayPush[index_recrrct].numberofseat = requiredRooms * seats;

            //     return false;
            // }

            // // ✅ Update info display

        }
    }


    function handleAddPackage(that) {
        if (newaddpackages('in', $(that).data('newclass1'), $(that).data('newprices'), that) === false) {
            return;
        }
        if ("{{$getfirst['is_person_use']}}" == 1) {
            updateOldarray(that);
        }
        addcustomhotels(`${$(that).data('newclass1')}`);
        $(`.other_package_add${$(that).data('typename')}${$(that).data('packid')}`).addClass('d-none');
        $(`.other_package_add1${$(that).data('packid')}`).removeClass('d-none');
        $(`.other_package_add1_${$(that).data('packid')}${$(that).data('keyname')}`).addClass('d-none');
        $(`.other_package_add_value${$(that).data('packid')}`).val(0);
        $(`.other_package_add_${$(that).data('packid')}${$(that).data('keyname')}`).removeClass('d-none');
        $(`.other_package_add_value_${$(that).data('packid')}${$(that).data('keyname')}`).val(1);
        sub_qtys(`other${$(that).data('packid')}`, `${$(that).data('packid')}`, 1, `${$(that).data('newprices')}`);
        $(`.other_information${$(that).data('packid')}`).text('');
        $(`.other_information${$(that).data('packid')}${$(that).data('newprices')}`).text(parseFloat($(that).data('newprices')).toLocaleString('en-US', {
            style: 'currency',
            currency: '{{getCurrencyCode()}}'
        }));
    }
</script>
<?php if ($getfirst['use_date'] == 1) { ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('dateInput');
            const calendarContainer = document.getElementById('calendarContainer');
            const calendarGrid = document.getElementById('calendarGrid');
            const currentMonthElement = document.getElementById('currentMonth');
            const prevMonthButton = document.getElementById('prevMonth');
            const nextMonthButton = document.getElementById('nextMonth');
            const calendarIcon = document.getElementById('calendarIcon');

            let currentDate = new Date();
            let selectedDate = null;
            let configType = parseInt("{{ $getfirst['customized_type']??0 }}");
            let newarrays = @json($getfirst['customized_dates'] ?? []);
            if (!Array.isArray(Array.isArray(newarrays))) {
                newarrays = JSON.parse(newarrays);
            }
            const configData = newarrays;

            const processedConfig = {
                1: configData,
                2: Array.isArray(configData) ? configData.map(date => {
                    return parseInt(date.split('-')[2]);
                }) : [],
                3: Array.isArray(configData) ? configData.map(date => {
                    const parts = date.split('-');
                    return `${parts[1]}-${parts[2]}`;
                }) : []
            };
            initializeCalendar();

            dateInput.addEventListener('click', toggleCalendar);
            calendarIcon.addEventListener('click', toggleCalendar);

            prevMonthButton.addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                initializeCalendar();
            });

            nextMonthButton.addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                initializeCalendar();
            });


            configType = parseInt("{{ $getfirst['customized_type'] }}");

            initializeCalendar();


            function toggleCalendar() {
                if (calendarContainer.style.display === 'block') {
                    calendarContainer.style.display = 'none';
                } else {
                    calendarContainer.style.display = 'block';
                }
            }

            function isDateAvailable(date) {

                const today = new Date();
                const todayDateOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                const checkDateOnly = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                if (checkDateOnly <= todayDateOnly) {
                    return false;
                }
                const day = date.getDate();
                const month = String(date.getMonth() + 1).padStart(2, '0'); // 01-12
                const dayName = date.toLocaleDateString('en-US', {
                    weekday: 'long'
                });

                switch (configType) {
                    case 1:
                        return processedConfig[1].includes(dayName);
                    case 2:
                        return processedConfig[2].includes(day);
                    case 3:
                        const monthDay = `${month}-${String(day).padStart(2, '0')}`;
                        return processedConfig[3].includes(monthDay);
                    default:
                        return false;
                }
            }

            function initializeCalendar() {
                calendarGrid.innerHTML = '';
                const monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                currentMonthElement.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

                const dayHeaders = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                dayHeaders.forEach(day => {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day-header';
                    dayElement.textContent = day;
                    calendarGrid.appendChild(dayElement);
                });

                const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = firstDay.getDay();
                for (let i = 0; i < startingDay; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'calendar-day other-month';
                    calendarGrid.appendChild(emptyDay);
                }
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    dayElement.textContent = day;
                    const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);

                    if (isDateAvailable(dayDate)) {
                        dayElement.classList.add('available');
                        if (selectedDate &&
                            selectedDate.getDate() === dayDate.getDate() &&
                            selectedDate.getMonth() === dayDate.getMonth() &&
                            selectedDate.getFullYear() === dayDate.getFullYear()) {
                            dayElement.classList.add('selected');
                        }

                        dayElement.addEventListener('click', function() {
                            selectDate(dayDate);
                        });
                    } else {
                        dayElement.classList.add('disabled');
                    }

                    calendarGrid.appendChild(dayElement);
                }
            }

            function selectDate(date) {
                selectedDate = date;
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                };
                const formattedDate = date.toLocaleDateString('en-GB', options);

                dateInput.value = formattedDate;
                $('.pickup_date').val(`${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`);
                $('.start-date-arrival').text(`${String(date.getDate()).padStart(2, '0')} ${date.toLocaleDateString('en-US', { month: 'short' })}, ${date.getFullYear()}`);

                const differenceDays = parseInt($('.difference-dates').val()) || 0;
                const departureDate = new Date(selectedDate);
                departureDate.setDate(departureDate.getDate() + differenceDays);
                $('.start-date-departure').text(`${String(departureDate.getDate()).padStart(2, '0')} ${departureDate.toLocaleDateString('en-US', { month: 'short' })}, ${departureDate.getFullYear()}`);
                calendarContainer.style.display = 'none';
                initializeCalendar();
            }

            document.addEventListener('click', function(event) {
                if (!calendarContainer.contains(event.target) &&
                    event.target !== dateInput &&
                    event.target !== calendarIcon) {
                    calendarContainer.style.display = 'none';
                }
            });
        });
    </script>
<?php } ?>
@endpush