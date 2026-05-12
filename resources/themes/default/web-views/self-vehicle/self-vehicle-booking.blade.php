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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.1/css/swiper.min.css">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .tooltip-inner {
        background-color: #ffffff !important;
        color: #333 !important;
        border: 1px solid #ccc;
        padding: 10px;
        max-width: 250px;
    }

    .tooltip.bs-tooltip-left .arrow::before {
        border-left-color: #ffffff !important;
    }

    .tooltip.bs-tooltip-right .arrow::before {
        border-right-color: #ffffff !important;
    }

    .tooltip.bs-tooltip-top .arrow::before {
        border-top-color: #ffffff !important;
    }

    .tooltip.bs-tooltip-bottom .arrow::before {
        border-bottom-color: #ffffff !important;
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
        top: 0px;
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

        .font-size-13 {
            font-size: 9px;
        }

        .button-boarder-set {
            padding: 3px 6px 3px 2px;
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

    .stepper-wrapper {
        display: flex;
        justify-content: center;
        margin: 30px 0;
        position: relative;
    }

    .stepper-item {
        text-align: center;
        position: relative;
        flex: 1;
    }

    .step-counter {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #ccc;
        margin: 0 auto 10px;
        position: relative;
        z-index: 1;
    }

    .step-name {
        font-size: 14px;
        font-weight: 500;
    }

    .stepper-item.completed .step-counter {
        background: #28a745;
        /* green */
    }

    .stepper-item.active .step-counter {
        background: #f39c12;
        /* orange */
    }

    .stepper-item::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 2px;
        background: #ccc;
        top: 7px;
        left: 50%;
        z-index: 0;
    }

    .stepper-item:last-child::after {
        content: none;
    }

    .stepper-item.completed::after {
        background: #28a745;
    }

    /* //////////////////////////////////// */
    .card-custom {
        border-radius: 10px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .car-info {
        flex: 1;
    }

    .car-category {
        font-size: 14px;
        color: #999;
    }

    .car-title {
        font-size: 20px;
        font-weight: bold;
    }

    .car-title small {
        font-size: 14px;
        color: #28a745;
        font-weight: normal;
    }

    .car-features {
        font-size: 14px;
        color: #555;
    }

    .car-features i {
        margin-right: 5px;
    }

    .service-provider {
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .service-provider img {
        width: 50px;
        margin-right: 8px;
    }

    .badge-custom {
        background: #f8f9fa;
        color: #333;
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 6px;
        margin-right: 5px;
    }

    .rating {
        color: #f39c12;
        font-size: 14px;
    }

    .car-image img {
        border-radius: 10px;
        width: 120px;
    }

    .car-image .badge-photo {
        position: absolute;
        background: #000;
        color: #fff;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 6px;
    }

    .driver_age_details {
        border: 1px solid #eee;
        border-radius: 16px;
        cursor: pointer;
        line-height: 1.5;
        padding: 6px 12px;
    }

    .driver_age_details.active {
        background-color: #fff0e5;
        border-color: #ff5b00;
        color: #ff5b00;
    }

    .date-container {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
    }

    .days-diff {
        border: 1px solid #007bff;
        color: #007bff;
        background: #fff;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 13px;
    }

    .no-hover:hover {
        background-color: transparent !important;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.1/css/swiper.min.css">

@endpush
@section('content')
<?php $getfirst = [];
$faqs = [];
$getfirst['use_date'] = 1;
$getfirst['is_person_use'] = 1;
$getfirst['cab_list_price'] = []; ?>
<div class="container">
    <div class="stepper-wrapper">
        <div class="stepper-item header_one_tab active">
            <div class="step-counter"></div>
            <div class="step-name">{{ translate('details')}}</div>
        </div>
        <div class="stepper-item header_two_tab">
            <div class="step-counter"></div>
            <div class="step-name">{{ translate('Choose package')}}</div>
        </div>
        <div class="stepper-item header_three_tab">
            <div class="step-counter"></div>
            <div class="step-name">{{ translate('Enter driver`s info')}}</div>
        </div>
        <div class="stepper-item header_four_tab">
            <div class="step-counter"></div>
            <div class="step-name">{{ translate('Pay now')}}</div>
        </div>
    </div>
</div>
<?php $langs = str_replace('_', '-', app()->getLocale()); ?>
<div class="container mt-3 rtl text-align-direction" id="step-one-user-side">
    <div class="row">
        <div class="col-md-8">
            <div class="container mt-2">
                @if (!empty($SelfVehicles['images']) && json_decode($SelfVehicles['images'], true))
                <div class="slider-92911">
                    <div class="owl-carousel slide-one-item">
                        @foreach (json_decode($SelfVehicles['images'], true) as $val)
                        <div class="testimony-29101 align-items-stretch">
                            <div class="image"
                                style="height: 300px;border-radius: 12px;background-image: url('{{ getValidImage(path: 'storage/app/public/tour_and_travels/self_driving/' . ($val ?? ''), type: 'backend-product') }}');">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="my-5 text-center">
                        <ul class="thumbnail">
                            @foreach (json_decode($SelfVehicles['images'], true) as $val)
                            <li class="{{ $loop->index == 0 ? 'active' : '' }}"><a><img
                                        src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/self_driving/' . ($val ?? ''), type: 'backend-product') }}"
                                        alt="Image" class="img-fluid"
                                        style="width: 33px !important; height: 33px !important;"></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row mt-2">
                        <div class="col-md-12 p-0">
                            <div class="navbar_section1 section-links d-flex justify-content-between mt-3 border-top border-bottom py-2 mb-4 small" style="overflow: auto;">
                                <a class="section-link ml-2" href="#about_details">{{ translate('details')}}</a>
                                <a class="section-link" href="#important_info">{{ translate('important_Info')}}</a>
                                <a class="section-link mr-2" href="#review_user">{{ translate('Reviews') }}</a>
                                <a class="section-link" href="#tourfaq">{{ translate('faqs') }}</a>
                            </div>
                            <div class="content-sections px-lg-3">
                                <div class="section-content" id="about_details">
                                    <div class="container">
                                        <div class="card-custom d-flex">
                                            <div class="car-info">
                                                <div class="car-category">{{ $SelfVehicles['getCategory']['brand_name']}}</div>
                                                <div class="car-title">{{ $SelfVehicles['getCabId']['name']}}</div>
                                                <div class="car-features">
                                                    <span>{{ $SelfVehicles['getCabId']['seats']}} seats</span>| {{ (($SelfVehicles['air_conditioning_status'] == 1)?'A/C':'NoN A/C')}} | {{ ucwords($SelfVehicles['car_type'])}}
                                                </div>
                                                <div class="service-provider">
                                                    <span>Service provided by <strong>Paradise</strong>
                                                        {{-- <!-- | <span class="rating">★ 3.2</span> (126 reviews)</span> --> --}}
                                                </div>
                                                <div class="mt-2">
                                                    <?php
                                                    $drivingInfo = json_decode($SelfVehicles['cab_about'] ?? "[]", true);
                                                    ?>
                                                    @if (isset($drivingInfo[$langs]))
                                                    @foreach ($drivingInfo[$langs] as $item)
                                                    <span class="badge badge-secondary badge-soft-secondary border-soft-secondary">{{ $item['name'] }}</span>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="car-image position-relative">
                                                <img src="https://via.placeholder.com/120" alt="Car Image">
                                                <div class="badge-photo">Real photos</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-content" id="important_info">
                                    <div class="row p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                        <div class="col-12">
                                            <h4 class="exclu font-weight-bolder">&nbsp;{{ translate('important_Info')}}</h4>
                                            <hr class="mb-3">
                                            {!! $SelfVehicles['drivers_age_details'] !!}
                                        </div>
                                        <hr>
                                        <div class="col-md-12">
                                            <h4>{{ translate('Required documents for pick up')}}</h4>
                                        </div>
                                        <div class="col-md-12">
                                            <a onclick="$('.driver_age_details1').addClass('active');$('.driver_age_details2').removeClass('active');$('.driver_age_details4').addClass('d-none');$('.driver_age_details3').removeClass('d-none');" class="driver_age_details driver_age_details1 active">{{ translate('I`m not a local resident')}}</a>
                                            <a onclick="$('.driver_age_details2').addClass('active');$('.driver_age_details1').removeClass('active');$('.driver_age_details3').addClass('d-none');$('.driver_age_details4').removeClass('d-none');" class="driver_age_details driver_age_details2">{{ translate('I`m a local resident')}}</a>
                                        </div>
                                        <div class="col-md-12 driver_age_details3">
                                            {!! $SelfVehicles['not_local_resident'] !!}
                                        </div>
                                        <div class="col-md-12 driver_age_details4 d-none">
                                            {!! $SelfVehicles['local_resident'] !!}
                                        </div>
                                        <div class="col-12">
                                            <h4 class="exclu font-weight-bolder">&nbsp;{{ translate('tip_for_driving')}}</h4>
                                            <hr class="mb-3">
                                            {!! $SelfVehicles['tip_for_driving'] !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="section-content" id="review_user">
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
                                                                    {{ round($ratings['total']??0, 1) }}&nbsp;
                                                                </a>
                                                                <big>
                                                                    @for ($inc = 1; $inc <= 5; $inc++)
                                                                        @if ($inc <=(int) ($ratings['total']??0))
                                                                        <i class="tio-star text-warning"></i>
                                                                        @elseif (($ratings['total']??0) != 0 && $inc <= (int) $ratings['total'] + 1.1 && $ratings['total']> ((int) $ratings['total']))
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
                                                            <img src="{{ getValidImage(path: 'storage/app/public/profile/' . ($counselling['userData']['image'] ?? ''), type: 'product') }}" alt="User Icon" class="user-icon" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
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
                <div class="card payment-card">
                    <div class="card-body p-0">
                        <div class="pricecolm">
                            <div class="row px-3 py-3">
                                <div class="col-md-12">
                                    @php $pick_point = json_decode($SelfVehicles['pick_point']??"[]",true);@endphp
                                    @if (isset($pick_point[$langs]))
                                    <?php $kk = 0 ?>
                                    @foreach ($pick_point[$langs] as $key=>$item)
                                    <div class="custom-control custom-radio mt-2">
                                        <input type="radio" id="location{{$kk}}" name="location" value="{{ $item['id'] }}" class="custom-control-input check_location_data" {{ (($kk == 0)?'checked':'') }} {{ (($lead_data['pickup_address'] == $item['id'])?'checked':'')}}>
                                        <label class="custom-control-label" for="location{{$kk}}">{{ $item['point'] }}</label>
                                    </div>
                                    <?php $kk++; ?>
                                    @endforeach
                                    @endif
                                </div>
                                <div class="col-md-12 mt-3">
                                    <hr>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="container mt-4">
                                        <div class="date-container">
                                            <input id="dateRange" class="form-control check_date_time_data" placeholder="Select pick-up & drop-off" style="width: 72%;">
                                            <span class="days-diff" id="daysDiff">0 day(s)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <p class="label">{{ translate('basic_price') }}</p>
                                </div>
                                <div class="col-6 text-right">
                                    <p class="amount">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $SelfVehicles['basic_price']??0), currencyCode: getCurrencyCode()) }}</p>
                                </div>
                            </div>
                            <div class="clr"></div>
                            <div class="row">
                                <div class="col-12 py-3 px-3">
                                    <button class="btn btn-sm btn--primary form-control next-step-btn" onclick="storeSelfCabs(1)">Next step</button>
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
<?php $getDataLocation = \App\Models\SelfVehicleLeads::with(['SelfCabData'])->find($lead_data['id']); ?>
<div class="container mt-3 rtl text-align-direction d-none" id="step-two-user-side">
    <div class="row">
        <div class="col-md-8 card shadow-sm p-3 mb-3">
            <div class="row">
                <h4 class="inclu font-weight-bolder"> {{ translate('Select a featured package') }}</h4>
            </div>
            @php
            $getPolicy = json_decode($SelfVehicles['policy_info']??"[]",true);
            @endphp
            @if (isset($getPolicy[$langs]))
            <?php $policeKey = 0;
            $getFistId = 0; ?>
            @foreach ($getPolicy[$langs] as $polikey=>$item)
            <?php if ($policeKey == 0) {
                $getFistId = $item['id'];
            } ?>

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="font-weight-bold">{{ $item['name']}}</h5>
                    <ul class="list-unstyled mb-2">
                        @if($item['policy_info'])
                        @foreach($item['policy_info'] as $deitem)
                        <li class="text-muted">
                            <i class="fas fa-shield-alt">&nbsp;{{ $deitem['name']}}</i>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                <div class="text-right">
                    <div class="font-weight-bold" style="font-size: 1.2rem;">
                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['price']??0), currencyCode: getCurrencyCode()) }}
                        <small class="text-muted">/day</small>
                    </div>
                    <div class="text-muted">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item['price']??0), currencyCode: getCurrencyCode()) }}</div>
                    <button type="button" class="btn btn-warning btn-block mt-2 font-weight-bold seleced_PackageKey seleced_PackageKey{{$item['id']}}" data-key="{{$item['id']}}">
                        @if($lead_data['package_id'] == 0 && $policeKey == 0)
                        {{ translate('selected') }}
                        @elseif($item['id'] == $lead_data['package_id'])
                        {{ translate('selected') }}
                        @else
                        {{ translate('select') }}
                        @endif
                    </button>
                </div>
            </div>
            <hr class="my-3">
            <?php $policeKey++; ?>
            @endforeach
            @endif
            <input type="hidden" value="{{ (($lead_data['package_id'] == 0)?$getFistId: $lead_data['package_id']) }}" class="package_id_gets">
            <div class="container mt-4">
                @if($PolicysCondition)
                @foreach($PolicysCondition as $val)
                <div class="d-flex align-items-center mb-3">
                    <div class="font-weight-bold" style="width: 150px;">{{ $val['title'] }} <i class="tio tio-info_outined ml-1" data-toggle="tooltip" data-placement="left" title="<h6 class='text-success'>{{ $val['policy_name'] }}</h6><span>{{ $val['message'] }}</span>" data-html="true">info_outined</i></div>
                    <a class="btn btn-outline-primary btn-sm no-hover">{{ $val['policy_name'] }}</a>
                </div>
                @endforeach
                @endif
            </div>
            <div class="card p-3 upcancellation_div_reloads mt-3">
                <h6 class="font-weight-bold mb-3">Cancellation Policy</h6>
                <div class="ml-2">
                    <!-- Free Cancellation -->
                    @if($cancellationPolicy)
                    @foreach($cancellationPolicy as $can)
                    <div class="d-flex">
                        <div class="d-flex flex-column align-items-center" style="width: 20px;">
                            <span style="width: 10px; height: 10px; background: green; border-radius: 50%; display: block;"></span>
                            @if($loop->last)
                            @else
                            <span style="width: 2px; background: #ccc; flex-grow: 1;"></span>
                            @endif
                        </div>
                        <div class="ml-3">
                            <div class="font-weight-bold">{{ $can['title']}}</div>
                            <small class="text-muted">{!! preg_replace('/\{\{\s*\$date\s*\}\}/','<strong>' . date('d-m-Y h:i A', strtotime($getDataLocation['pickup_date']. ' -' . $can['day'] . ' hours')) . '</strong>',$can['message']) !!}</small>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="update_div_reloads">
                <?php
                $pick_point = json_decode($getDataLocation['SelfCabData']['pick_point'] ?? "[]", true);
                $filtered = collect($pick_point[$langs] ?? [])->firstWhere('id', $getDataLocation['pickup_address']);
                ?>
                <div class="card p-3 shadow-sm">
                    <h6 class="font-weight-bold mb-3">Pick up & drop off details</h6>
                    <!-- Pickup -->
                    <div class="d-flex mb-1">
                        <div class="d-flex flex-column align-items-center" style="width: 20px;">
                            <span style="width: 10px; height: 10px; background: green; border-radius: 50%; display: block;"></span>
                            <span style="width: 2px; background: #ccc; flex-grow: 1;"></span>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <div class="font-weight-bold">{{ date('d M,Y H:i A',strtotime($getDataLocation['pickup_date'])) }}</div>
                            <small class="text-muted">{{ $filtered['point']??"" }}</small>
                        </div>
                    </div>
                    <!-- Drop off -->
                    <div class="d-flex">
                        <div class="d-flex flex-column align-items-center" style="width: 20px;">
                            <span style="width: 10px; height: 10px; background: blue; border-radius: 50%; display: block;"></span>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <div class="font-weight-bold">{{ date('d M,Y H:i A',strtotime($getDataLocation['droup_date'])) }}</div>
                            <small class="text-muted">{{ $filtered['point']??'' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card p-3 shadow-sm mt-2 paymant_div_reloads">
                <h6 class="font-weight-bold mb-3">Payment details</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>package Price</span>
                    <span class="font-weight-bold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $lead_data['price']??0), currencyCode: getCurrencyCode()) }}</span>
                </div>
                <div class="dropdown mb-3">
                    <?php $pick_point12 = json_decode($getDataLocation['SelfCabData']['policy_info'] ?? "[]", true);
                    $filtered12 = collect($pick_point12[$langs] ?? [])->firstWhere('id', $getDataLocation['package_id']);
                    ?>
                    <button class="btn btn-light d-flex justify-content-between align-items-center w-100" type="button" id="basicFeeDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #ddd; border-radius: 8px;">
                        <span>{{$filtered12['name']??''}} fee</span>
                        <span class="ml-auto">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $lead_data['price']??0), currencyCode: getCurrencyCode()) }} <i class="ml-2 fas fa-chevron-down"></i></span>
                    </button>
                    <div class="dropdown-menu w-100" aria-labelledby="basicFeeDropdown">
                        @if($filtered12 && $filtered12['policy_info'])
                        @foreach($filtered12['policy_info'] as $valp)
                        <a class="dropdown-item">{{ $valp['name'] }}</a>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total</strong>
                    <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $lead_data['price']??0), currencyCode: getCurrencyCode()) }}</strong>
                </div>
                <button class="btn btn-block font-weight-bold" style="background-color: #ff4d00; color: white; border-radius: 8px;" onclick="storeSelfCabs(2)">
                    Next step
                </button>
            </div>
        </div>
    </div>
</div>
<div class="container mt-3 rtl text-align-direction d-none" id="step-three-user-side">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <h4 class="section-title exclu">&nbsp;Driver's info</h4>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="firstName" class="required-field">First name (as on passport)</label>
                                <input type="text" class="form-control" id="firstName" value="{{ (($lead_data['f_name'])? $lead_data['f_name'] : ($userfind['f_name']??'')) }}" placeholder="e.g. User First Name" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="lastName" class="required-field">Last name (as on passport)</label>
                                <input type="text" class="form-control" id="lastName" value="{{ (($lead_data['l_name'])? $lead_data['l_name'] : ($userfind['l_name']??'')) }}" placeholder="e.g.  User Last Name" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="user_age" class="required-field">Driver's age</label>
                                <select class="form-control" id="user_age" required>
                                    <option value="">Please Select Age(18-99)</option>
                                    @for($i=18;$i <= 99;$i++)
                                        <option value="{{$i}}" {{ (($lead_data['age'] == $i)? 'selected' :'' ) }}>{{$i}}</option>
                                        @endfor
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <h4 class="section-title exclu">&nbsp;Contact info</h4>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="phone" class="required-field">Phone number</label>
                                <input
                                    class="form-control text-align-direction phone-input-with-country-picker"
                                    type="tel"
                                    value="{{ $lead_data['phone_number']??($userfind['phone']??'') }}"
                                    name="person_phone" id="person-number" placeholder="{{ translate('enter_phone_number') }}" required {{ isset($customer['phone']) ? 'readonly' : '' }} oninput="this.value=this.value.slice(0,10)">

                                <input type="hidden" class="country-picker-phone-number w-50" name="person_phone" value="{{ $lead_data['phone_number']??($userfind['phone']??'') }}" readonly>

                                <p id="number-validation" class="text-danger" style="display: none">Enter Your Valid Mobile Number</p>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="email_id" class="required-field">Email address</label>
                                <input type="email" class="form-control" id="email_id" value="{{ $lead_data['email']??($userfind['email']??'') }}" placeholder="e.g abc@gmail.com" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <h4 class="section-title exclu">&nbsp;Document info</h4>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="aadhar_number" class="required-field">Aadhaar Number</label>
                                <input type="text" class="form-control" id="aadhar_number" value="{{ $lead_data['aadhaar_number'] }}" placeholder="e.g. Aadhaar Number" required maxlength="12" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="pan_cards" class="required-field">PanCard Number</label>
                                <input type="text" class="form-control" id="pan_cards" value="{{ $lead_data['pancard'] }}" placeholder="e.g. PanCard Number" required onkeyup="formatPAN(this)" maxlength="10" style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="driving_licence" class="required-field">Driving licence</label>
                                <input type="text" class="form-control" id="driving_licence" value="{{ $lead_data['driving_licence'] }}" placeholder="e.g. Driving Licence Number" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm mt-2 paymant_driver_info_div_reloads">
                <h6 class="font-weight-bold mb-3">Payment details</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>package Price</span>
                    <span class="font-weight-bold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $lead_data['price']??0), currencyCode: getCurrencyCode()) }}</span>
                </div>
                <div class="dropdown mb-3">
                    <?php $pick_point12 = json_decode($getDataLocation['SelfCabData']['policy_info'] ?? "[]", true);
                    $filtered12 = collect($pick_point12[$langs] ?? [])->firstWhere('id', $getDataLocation['package_id']);
                    ?>
                    <button class="btn btn-light d-flex justify-content-between align-items-center w-100" type="button" id="basicFeeDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #ddd; border-radius: 8px;">
                        <span>{{$filtered12['name']??''}} fee</span>
                        <span class="ml-auto">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $lead_data['price']??0), currencyCode: getCurrencyCode()) }} <i class="ml-2 fas fa-chevron-down"></i></span>
                    </button>
                    <div class="dropdown-menu w-100" aria-labelledby="basicFeeDropdown">
                        @if($filtered12 && $filtered12['policy_info'])
                        @foreach($filtered12['policy_info'] as $valp)
                        <a class="dropdown-item">{{ $valp['name'] }}</a>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total</strong>
                    <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $lead_data['price']??0), currencyCode: getCurrencyCode()) }}</strong>
                </div>
                <button class="btn btn-block font-weight-bold" style="background-color: #ff4d00; color: white; border-radius: 8px;" onclick="storeSelfCabs(3)">
                    Next step
                </button>
            </div>
        </div>
    </div>
</div>
<div class="container mt-3 rtl text-align-direction d-none" id="step-four-user-side">
    <div class="mt-3 mb-3">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <img class="img-thumbnail rounded" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/self_driving/' . $SelfVehicles['thumbnail'], type: 'backend-product') }}" alt="">
                </div>
            </div>
            <div class="col-md-6 payment_last_screen_model">
                <div class="card">
                    <div class="card-body">
                        <div class="cart_total p-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="needs-validation" action="javascript:" method="post" novalidate id="coupon-code-events-ajax">
                                        <div class="d-flex form-control rounded-pill ps-3 p-1">
                                            <img width="24" src="{{theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg')}}" alt="" onclick="couponList()">
                                            <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100 input_coupon_code" type="text" value="{{ \App\Models\Coupon::where('id', $lead_data['coupan_id'])->first()['code']??'' }}" name="coupon_code" placeholder="{{translate('coupon_code')}}">
                                            <button class="btn btn--primary rounded-pill text-uppercase py-1 fs-12 coupan_apply_text" type="button" id="events-coupon-code" onclick="apply_coupan()">
                                                {{translate('apply')}}
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">{{translate('please_provide_coupon_code')}}</div>
                                    </form>
                                </div>
                            </div>
                            @if (($userfind['wallet_balance']?? 0) > 0)
                            <div class="row">
                                <div class="col-12 text-end">
                                    <input type="checkbox" onclick="calculator_wallet()" class="wallet_checked" value="1" checked>&nbsp;{{ translate('apply_Wallet') }}
                                </div>
                            </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="cart_title font-weight-bold">{{ translate('package_price') }}</span>
                                <span class="cart_value font-weight-bold">{{ webCurrencyConverter(amount: ($lead_data['price']??0)) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <span class="cart_title font-weight-bold">{{ translate('security_amount') }}</span>
                                <span class="cart_value font-weight-bold">{{ webCurrencyConverter(amount: ($lead_data['security_amount']??0)) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <span class="cart_title font-weight-bold">{{ translate('total_Tax') }}({{($lead_data['tax']??0)}}%)</span>
                                <span class="cart_value font-weight-bold">{{ webCurrencyConverter(amount: ($lead_data['tax_amount']??0)) }}</span>
                            </div>
                            <div class="justify-content-between mt-2 {{ (($lead_data['coupan_amount'] > 0)?'d-flex':'d-none')}} Coupon_apply_discount_css font-weight-bold">
                                <span class="cart_title">{{translate('coupon_discount')}}</span>
                                <span class="cart_value Coupon_apply_discount"> - {{ webCurrencyConverter(amount: ($lead_data['coupan_amount']??0)) }} </span>
                            </div>
                            <div class="d-none show_user_wallet_amount">
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="cart_title text-success font-weight-bold">
                                        <img width="20"
                                            src="{{ theme_asset(path: 'public/assets/back-end/img/admin-wallet.png') }}"
                                            style="margin-top: -9px;">{{ translate('Wallet Amount') }}
                                        <small>({{ webCurrencyConverter(amount: ($userfind['wallet_balance']??0)) }})</small>
                                    </span>
                                    <span
                                        class="cart_value text-success user_wallet_amount font-weight-bold">
                                        {{ webCurrencyConverter(amount: ($userfind['wallet_balance']??0)) }}
                                    </span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between mt-2">
                                    <span
                                        class="cart_title text-success font-weight-bold user_wallet_am_remaining_text font-weight-bold"
                                        style="color: darkred !important;">{{ translate('Remaining Amount') }}</span>
                                    <span
                                        class="cart_value text-success user_wallet_amount_remaining font-weight-bold"
                                        style="color: darkred !important;"> </span>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="justify-content-between d-flex">
                                <span class="cart_title text-primary font-weight-bold">{{ translate('Final Amount') }}</span>
                                <span class="cart_value font-weight-bold" id="mainProductPrice"></span>
                            </div>
                            <input type="hidden" class="user-wallet-adds" value="1">
                        </div>
                        <hr class="my-2">
                        <div class="mt-4">
                            <button type="button" class="btn btn--primary btn-block name_change_continues" onclick="paymantNow()">{{ translate('Proceed_To_Checkout')}}</button>
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
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}" type="text/javascript"></script>
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

    flatpickr("#dateRange", {
        mode: "range",
        enableTime: true,
        dateFormat: "d M Y H:i",
        minDate: new Date().fp_incr(1),
        defaultDate: [
            "{{ \Carbon\Carbon::parse($lead_data['pickup_date'])->format('d M Y H:i') }}",
            "{{ \Carbon\Carbon::parse($lead_data['droup_date'])->format('d M Y H:i') }}"
        ],
        onChange: function(selectedDates) {
            updateDaysDiff(selectedDates);
        },
        onReady: function(selectedDates) {
            updateDaysDiff(selectedDates);
        }
    });

    function updateDaysDiff(selectedDates) {
        if (selectedDates.length === 2) {
            let start = selectedDates[0];
            let end = selectedDates[1];
            let diff = end - start;
            let days = Math.ceil(diff / (1000 * 60 * 60 * 24));
            document.getElementById('daysDiff').innerText = days + " day(s)";
        }
    }

    function storeSelfCabs(step) {
        let selectedLocation = document.querySelector('.check_location_data:checked');
        let package_id = $('.package_id_gets').val();
        let dateRange = document.querySelector('.check_date_time_data').value.trim();
        let firstName = $('#firstName').val();
        let lastName = $('#lastName').val();
        let user_age = $('#user_age').val();
        let user_phone = $('.country-picker-phone-number').val();
        let email_id = $('#email_id').val();
        let aadhar_number = $('#aadhar_number').val();
        let pan_cards = $('#pan_cards').val();
        let driving_licence = $('#driving_licence').val();
        if (step == 1) {
            if (!selectedLocation) {
                toastr.error('Please select a location.');
                return false;
            }
            if (dateRange === '') {
                toastr.error('Please select a pick-up & drop-off date.');
                document.querySelector('.check_date_time_data').focus();
                return false;
            }
        } else if (step == 2.1 || step == 2) {

        } else if (step == 3) {
            const fields = [{
                    value: firstName,
                    message: 'Please Enter First Name.'
                },
                {
                    value: lastName,
                    message: 'Please Enter Last Name.'
                },
                {
                    value: user_age,
                    message: 'Please Choose Age.'
                },
                {
                    value: user_phone,
                    message: 'Please Enter Phone Number.'
                },
                {
                    value: email_id,
                    message: 'Please Enter Email Id.'
                },
                {
                    value: aadhar_number,
                    message: 'Please Enter Aadhar Number.'
                },
                {
                    value: pan_cards,
                    message: 'Please Enter Pan Card.'
                },
                {
                    value: driving_licence,
                    message: 'Please Enter Driving Licence.'
                },
            ];

            for (let field of fields) {
                if (!field.value || field.value.trim() === "") {
                    toastr.error(field.message);
                    return false;
                }
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_id)) {
                toastr.error('Invalid Email Id.');
                return false;
            }
            if (!/^\d{12}$/.test(aadhar_number)) {
                toastr.error('Invalid Aadhaar Number. It must be 12 digits.');
                return false;
            }
            if (!/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(pan_cards)) {
                toastr.error('Invalid PAN Card Number. Format should be ABCDE1234F.');
                return false;
            }
            // if (!/^[A-Z]{2}\d{2}\s?\d{11}$/.test(driving_licence)) {
            //     toastr.error('Invalid Driving Licence Number. Format should be MH14 20110012345.');
            //     return false;
            // }

        } else {
            toastr.error('not step.');
            return false;
        }
        $('#loading').removeClass('d--none');
        $.ajax({
            url: "{{ route('self-vehicle-lead-update',['lead'=>$lead_data['id']]) }}",
            type: 'POST',
            data: {
                step,
                location: selectedLocation.value,
                date: dateRange,
                package_id,
                firstName,
                lastName,
                user_age,
                user_phone,
                email_id,
                aadhar_number,
                pan_cards,
                driving_licence,
                _token: '{{ csrf_token() }}',
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loading').addClass('d--none');
                if (step == 1) {
                    $('.header_one_tab').addClass('completed');
                    $('.header_one_tab').removeClass('active');
                    $('.header_two_tab').addClass('active');
                    $('.update_div_reloads').load(location.href + ' .update_div_reloads > *');
                    $('.upcancellation_div_reloads').load(location.href + ' .upcancellation_div_reloads > *');
                    $('#step-one-user-side').addClass('d-none');
                    $('#step-two-user-side').removeClass('d-none');
                    $('#step-three-user-side').addClass('d-none');
                    $('.upcancellation_div_reloads').load(location.href + ' .upcancellation_div_reloads > *');
                    $('.paymant_div_reloads').load(location.href + ' .paymant_div_reloads > *');
                    $('.paymant_driver_info_div_reloads').load(location.href + ' .paymant_driver_info_div_reloads > *');
                } else if (step == 2.1) {
                    $('.upcancellation_div_reloads').load(location.href + ' .upcancellation_div_reloads > *');
                    $('.paymant_div_reloads').load(location.href + ' .paymant_div_reloads > *');
                    $('.paymant_driver_info_div_reloads').load(location.href + ' .paymant_driver_info_div_reloads > *');
                } else if (step == 2) {
                    $('.header_two_tab').addClass('completed');
                    $('.header_two_tab').removeClass('active');
                    $('.header_three_tab').addClass('active');
                    $('#step-one-user-side').addClass('d-none');
                    $('#step-two-user-side').addClass('d-none');
                    $('#step-three-user-side').removeClass('d-none');
                } else if (step == 3) {
                    // $('.payment_last_screen_model').load(location.href + ' .payment_last_screen_model > *');
                    $('.header_three_tab').addClass('completed');
                    $('.header_three_tab').removeClass('active');
                    $('.header_four_tab').addClass('active');
                    calculator_wallet();
                    $('.payment_last_screen_model').load(location.href + ' .payment_last_screen_model > *', function() {
                        calculator_wallet();
                    });
                    $('#step-one-user-side').addClass('d-none');
                    $('#step-two-user-side').addClass('d-none');
                    $('#step-three-user-side').addClass('d-none');
                    $('#step-four-user-side').removeClass('d-none');
                }
            },
            error: function(xhr, status, error) {
                $('#loading').addClass('d--none');
                console.error(xhr.responseText);
            }
        });
    }

    $(document).on('click', '.seleced_PackageKey', function() {
        $('.seleced_PackageKey').text('{{ translate("select") }}');
        $(this).text('{{ translate("selected") }}');
        $('.package_id_gets').val($(this).data('key'));
        storeSelfCabs(2.1)
    });


    function formatPAN(input) {
        let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); // Allow only A-Z, 0-9
        let formatted = '';

        for (let i = 0; i < value.length && i < 10; i++) {
            if (i < 5) {
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i < 9) {
                if (/[0-9]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i === 9) {
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            }
        }

        input.value = formatted;
    }

    function calculator_wallet() {
        var wallet_amount = "{{ $userfind['wallet_balance']?? 0 }}";
        var isChecked = $('.wallet_checked').prop('checked');
        $.ajax({
            url: "{{ route('self-vehicle-lead-update',['lead'=>$lead_data['id']]) }}",
            type: 'POST',
            data: {
                step: 4,
                wallet_type: $('.wallet_checked').is(':checked') ? $('.wallet_checked').val() : '0',
                _token: '{{ csrf_token() }}',
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loading').addClass('d--none');
                var old_amount = response.data;
                if (isChecked) {
                    $(".show_user_wallet_amount").removeClass('d-none');
                    $(".user-wallet-adds").val(1);
                    if (wallet_amount >= old_amount) {
                        $(".user_wallet_amount_remaining").text(
                            `${(0 - 0).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                        );
                        $(".name_change_continues").text(`{{ translate('book_now') }}`);
                        $(".user_wallet_amount").text(
                            `${(old_amount - 0).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                        );
                        $('#mainProductPrice').text(
                            `${(0 - 0).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                        );
                    } else {
                        $(".user_wallet_amount").text(
                            `${(wallet_amount - 0).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                        );
                        $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout') }}`);
                        let remainingAmount = old_amount - wallet_amount;
                        let formattedAmount = remainingAmount.toLocaleString("en-US", {
                            style: "currency",
                            currency: "{{ getCurrencyCode() }}"
                        });
                        $(".user_wallet_amount_remaining").text(`-${formattedAmount}`);
                        $('#mainProductPrice').text(`${formattedAmount}`);
                    }
                } else {
                    $(".show_user_wallet_amount").addClass('d-none');
                    $(".user-wallet-adds").val(0);
                    $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout') }}`);
                    let formattedAmount1 = (old_amount - 0).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{ getCurrencyCode() }}"
                    });
                    $('#mainProductPrice').text(`${formattedAmount1}`);
                }
            }
        });
    }

    function couponList() {
        let expireDate = "";
        let formattedDate = "";
        let body = "";
        $.ajax({
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                type: "self-driving",
            },
            url: "{{ route('coupon.coupon-list-type') }}",
            success: function(response) {
                $('#modal-body').html('');
                let body = '';
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
                                           <p>On All Self Vehicle</p>
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
                        $('#modal-body').append(body);
                        $('#modal-body').css({
                            'display': 'flex',
                            'justify-content': 'center',
                            'padding': '50px 0px',
                            'color': 'red'
                        });
                        $('#coupon-modal').modal('show');
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

    function apply_coupan() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{ url('/api/v1/self-vehicle/coupon-apply') }}",
            data: {
                'user_id': "{{ auth('customer')->user()->id }}",
                "lead_id": "{{ $lead_data['id']}}",
                'coupon_code': $('.input_coupon_code').val()
            },
            success: function(data) {
                let messages = data.message;
                if (data.status == 1) {
                    $(".coupan_apply_text").text("{{translate('applyed')}}");
                    toastr.success(messages, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                } else {
                    $(".coupan_apply_text").text("{{translate('apply')}}");
                    toastr.error(messages, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
                $('.payment_last_screen_model').load(location.href + ' .payment_last_screen_model > *', function() {
                    calculator_wallet();
                });
            }
        });
    }
    function paymantNow(){
        apply_coupan();
        window.location.href = "{{ route('self-vehicle-paymant-request',['lead'=>$lead_data['id']]) }}";
    }
</script>
@endpush