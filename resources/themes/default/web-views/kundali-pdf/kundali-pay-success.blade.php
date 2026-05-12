@extends('layouts.front-end.app')
@section('title', translate("kundali"))
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/payment.css') }}">
<script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
<script src="https://js.stripe.com/v3/"></script>
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    #productList {
        background-color: white;
        border-radius: 6px;
        box-shadow: 2px 2px 2px 2px #f3f3f3;
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
        width: 33px;
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
</style>
@endpush
@section('content')
@php
$final_price_val = 0;
@endphp
<div class="w-full h-full sticky md:top-[68px] top-0 z-20">
    <div class="bg-bar w-full">
        <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
            <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                <div class="bg-bar w-full">
                    <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
                        <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                            <div class="d-flex justify-center items-center pt-3 pb-3">
                                <div class="d-flex justify-center items-center">
                                    <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                                        <path d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z" fill="white"></path>
                                    </svg>
                                    <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium  ">{{translate('Add Details')}}</div>
                                </div>
                                <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z" fill="#9CA3AF"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z" fill="#9CA3AF"></path>
                                    </svg>
                                </div>
                                <div class="d-flex justify-center items-center">
                                    <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                                        <path d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z" fill="white"></path>
                                    </svg>
                                    <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">{{ translate('Make Payment')}}</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container mt-3 rtl px-0 px-md-3 text-align-direction" id="cart-summary">
    <div class="row d-flex justify-content-center align-items-center">
        <section class="col-md-4 mb-4">
            <div class="card">
            <div class="card-body">
                    <div class="mb-3 text-center">
                        <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                    </div>
                    @if($data['type'] == 'kundali_milan')
                    <h6 class="font-black fw-bold text-center">
                        {{ translate('Your kundali Milan  has been generating successfully')}} !
                    </h6>
                    <p class="text-center fs-12">
                        {{ translate('It will be updated in your whatsapp number also in your order history shortly.') }}
                    </p>
                    @else  
                    <h6 class="font-black fw-bold text-center">
                        {{ translate('Your kundali has been generated successfully')}} !
                    </h6>
                    <p class="text-center fs-12">
                        {{ translate('If you want to open your kundali please click on the below button.') }}
                    </p>                    
                    @endif
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            @if($data['type'] == 'kundali_milan')
                            <a href="{{ route('saved.paid-kundali-milan.show',[$findData['id']])}}" class="btn btn--primary mb-3 text-center"> {{ translate('track_order')}} </a>
                            @else
                            <a href="{{ route('saved.paid.kundali')}}" class="btn btn--primary mb-3 text-center"> {{ translate('view_order')}} </a>
                            @endif
                        </div>
                        <div class="col-12 text-center">
                            <a href="{{ url('/')}}" class=" text-center">
                                {{ translate('continue')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>

@endpush