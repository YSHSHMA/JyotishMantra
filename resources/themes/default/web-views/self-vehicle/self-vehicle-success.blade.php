@extends('layouts.front-end.app')

@section('title', translate('vehicle_booking'))

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
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
    rel="stylesheet" type="text/css" />
<link rel="stylesheet"
    href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">


<style>
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

    @media (max-width: 768px) {
        .font-size-ten {
            font-size: 9px;
        }

        .step-name {
            font-size: 10px !important;
            font-weight: 400 !important;
        }
    }

    .stepper-wrapper {
        display: flex;
        justify-content: center;
        margin: 10px 0;
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
</style>
@endpush

@section('content')
<div class="w-full h-full sticky md:top-[68px] top-0 z-20">
    <div class="bg-bar w-full">
        <!-- <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer"> -->
        <div class="d-flex w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
        <div class="container">
            <div class="stepper-wrapper">
                <div class="stepper-item completed">
                    <div class="step-counter"></div>
                    <div class="step-name">{{ translate('details')}}</div>
                </div>
                <div class="stepper-item completed">
                    <div class="step-counter"></div>
                    <div class="step-name">{{ translate('Choose package')}}</div>
                </div>
                <div class="stepper-item completed">
                    <div class="step-counter"></div>
                    <div class="step-name">{{ translate('Enter driver`s info')}}</div>
                </div>
                <div class="stepper-item completed">
                    <div class="step-counter"></div>
                    <div class="step-name">{{ translate('Pay now')}}</div>
                </div>
                <div class="stepper-item active">
                    <div class="step-counter"></div>
                    <div class="step-name">{{ translate('Payment Success')}}</div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<div class="container mt-5 mb-5 rtl __inline-53 text-align-direction">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10 col-lg-10">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                    </div>
                    <h6 class="font-black fw-bold text-center">
                        {{ translate('booked_successfully') }} !
                    </h6>
                    <p class="text-center fs-12">
                        {{ translate('I_have_received_your_booking_,_thank_you_very_much_for_this.') }}
                    </p>
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('account-vehicle-booking-order') }}" class="btn btn--primary mb-3 text-center">
                                {{ translate('view_booking') }}
                            </a>
                        </div>
                        <div class="col-12 text-center">
                            <a href="{{ url('/') }}" class=" text-center">
                                {{ translate('continue') }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js"></script>
@endpush