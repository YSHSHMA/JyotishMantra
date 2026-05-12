@extends('layouts.front-end.app')
@section('title', translate('tour_Booking'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/payment.css') }}">
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places,geometry"></script>

    <style type="text/css">
        .owl-next {
            float: right;
            margin-right: 6% !important;
        }

        .owl-prev {
            float: left;
        }

        #map {
            height: 400px;
            width: 100%;
        }

        /* Search input styling */
        #search-box {
            width: 60%;
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
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    <div class="bg-bar w-full">
                        <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                            id="breadcrum-container-outer">
                            <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                                <div class="d-flex justify-center items-center pt-3 pb-3">
                                    <div class="d-flex justify-center items-center">
                                        <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                                            <path
                                                d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                                                fill="white"></path>
                                        </svg>
                                        <div
                                            class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium  ">
                                            {{ translate('Add Details') }}</div>
                                    </div>
                                    <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14"
                                            viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                                                fill="#9CA3AF"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                                                fill="#9CA3AF"></path>
                                        </svg>
                                    </div>
                                    <div class="d-flex justify-center items-center">
                                        <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                                            <path
                                                d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                                                fill="white"></path>
                                        </svg>
                                        <div
                                            class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
                                            {{ translate('tour_Booking') }}</div>
                                    </div>
                                    <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14"
                                            viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                                                fill="#9CA3AF"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                                                fill="#9CA3AF"></path>
                                        </svg>
                                    </div>
                                    <div class="d-flex justify-center items-center">
                                        <div
                                            class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">
                                            3</div>
                                        <div
                                            class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
                                            {{ translate('Make Payment') }}</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ getValidImage(path: 'storage/app/public/tour/video/2018033136.jpg', type: 'backend-product') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">

                </div>
            </div>
        </div>
    </div>
    @php
        $langs = str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale());
    @endphp
    <div class="container mt-3 rtl px-0 px-md-3 text-align-direction" id="cart-summary">
        <div class="row g-3 mx-max-md-0">
            <section class="col-lg-6 px-max-md-0">
                <div class="cards">
                    <div class="card-header" id="">
                        <div class="details __h-100">
                            <span class="mb-2 __inline-24"></span>
                            <div class="d-flex justify-content-between">
                                <span class=""><b> </b></span>
                            </div>
                            <div class="flex flex-col">
                                <span class='font-weight-bold'>{{ ucwords($getfirst['tour_name'] ?? '') }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="flex flex-col">
                                <div class="flex items-center space-x-1 pt-[16px] md:pt-2">
                                    @php
                                        $date_upcommining = '';
                                        $time_upcommining = '';
                                        $Venue = '';
                                    @endphp
                                    <span class="">
                                        <i class="fa fa-map-marker" aria-hidden="true"
                                            style="color: var(--primary-clr);"></i>
                                        {{ $getfirst['cities_name'] }} ,{{ $getfirst['state_name'] }}
                                        ,{{ $getfirst['country_name'] }}
                                    </span><br>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <aside class="col-lg-12">
                    <div class="table-responsive" id="productList">
                        <table class='table table-borderless table-thead-bordered table-nowrap table-align-middle'>
                            @php
                                $cab_id = '';
                                $cab_package = [];
                                $cab_people = 0;
                                $cab_price = 0;

                                if (!empty($getfirst['package_list']) && json_decode($getfirst['package_list'], true)) {
                                    foreach (json_decode($getfirst['package_list'], true) as $va) {
                                        if ($va['id'] == $getleads['package_id']) {
                                            $cab_id = $va['cab_id'];
                                            $cab_package = $va['package_id'];
                                            $cab_people = $va['people'];
                                            $cab_price = $va['price'];
                                            break;
                                        }
                                    }
                                }
                                $getcabs = \App\Models\TourCab::where('id', $cab_id)->first();
                            @endphp
                            <tbody>
                                <tr>
                                    <td class='__w-45'>
                                        <div class='d-flex gap-3'>
                                            <div class=''>
                                                <a class='position-relative overflow-hidden'>
                                                    <img class='rounded __img-62 '
                                                        src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getcabs['image'] ?? ''), type: 'product') }}"
                                                        id="Productimage" alt='Product'>
                                                </a>
                                            </div>
                                            <div class='d-flex flex-column gap-1'>
                                                {{ ucwords($getcabs['name']) }}
                                                <div class='text-break __line-2 __w-18rem small font-weight-bold'>
                                                    (Max {{ $cab_people }} People)
                                                </div>
                                                <div class='d-flex flex-wrap gap-2'>
                                                    <div class='text-center'>
                                                        {{ webCurrencyConverter(amount: $cab_price) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class='__w-15p text-center'>
                                        <label for="" class="small">{{ translate('Number of people') }}</label>
                                        <div class='qty d-flex justify-content-center align-items-center gap-3'>
                                            <span class="qty_plus DeleteIcon"
                                                onclick="QuantityUpdate(`{{ $getleads['id'] }}`, `{{ $cab_people }}`, `{{ $cab_price }}`,'de')"><i
                                                    class='tio-remove'></i> </span>
                                            <input type='text' class="qty_input cartQuantity"
                                                value="{{ $getleads['qty'] ?? '1' }}" name="quantity" readonly>
                                            <span class="qty_plus"
                                                onclick="QuantityUpdate(`{{ $getleads['id'] }}`, `{{ $cab_people }}`, `{{ $cab_price }}`, 'in')"><i
                                                    class='tio-add'></i> </span>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <span>{{ translate('Choose other cabs and service') }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="owl-carousel owl-theme">
                                            @if (!empty($getfirst['package_list']) && json_decode($getfirst['package_list'], true))
                                                @foreach (json_decode($getfirst['package_list'], true) as $va)
                                                    <div class="item">
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <a class="position-relative overflow-hidden">
                                                                    @php
                                                                        $getcabs = \App\Models\TourCab::where(
                                                                            'id',
                                                                            $va['cab_id'],
                                                                        )->first();
                                                                    @endphp
                                                                    <img class="rounded __img-62"
                                                                        src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getcabs['image'] ?? ''), type: 'product') }}"
                                                                        id="Productimage" alt="Product">
                                                                </a>
                                                            </div>
                                                            <div class="col-8">
                                                                <div> <span
                                                                        class="small">{{ ucwords(\App\Models\TourCab::where('id', $va['cab_id'])->first()['name'] ?? '') }}</span>
                                                                </div>
                                                                <div> <span class="small">(Max {{ $va['people'] }}
                                                                        People)</span> </div>
                                                                <div> <span
                                                                        class="font-weight-bold">{{ webCurrencyConverter(amount: $va['price'] ?? 0) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-2">
                                                            </div>
                                                            <div class="col-5 text-center mt-1">
                                                                @if (!empty($va['package_id']) && count($va['package_id']) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-success"
                                                                        onclick="$('.view-plan-services').modal('show');$('.plan-service-html').html($(this).data('html'))"
                                                                        data-html="
                                                              <div class='row'>
                                                                @foreach ($va['package_id'] as $pi)
<div class='col-12'>
                                                                    <span class='item font-weight-bold pt-1'>
                                                                        <i class='tio-record'></i> {{ ucwords(\App\Models\TourPackage::where('id', $pi)->first()['name'] ?? '') }}
                                                                    </span>
                                                                    {!! htmlentities(\App\Models\TourPackage::where('id', $pi ?? '')->first()['description'] ?? '') !!}                                     
                                                                         
                                                                </div>
@endforeach
                                                            </div>
                                                            ">{{ translate('packages') }}</button>
                                                                @endif
                                                            </div>
                                                            <div class="col-4 text-center mt-1">
                                                                <a href="{{ route('tour.change-tour-plane', [$getleads['id'], $va['id']]) }}"
                                                                    class="btn btn-sm btn-outline-warning">{{ translate('add') }}</a>
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
                </aside>
                <aside class="col-lg-12 pt-2 pt-lg-2 px-max-md-0 order-summery-aside">
                    <div class="__cart-total __cart-total_sticky">
                        <div class="cart_total p-0">
                            @if ((\App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0) > 0)
                                <div class="row">
                                    <div class="col-12 text-end">
                                        <input type="checkbox" onclick="updateProductPrice(`{{ $getleads['id'] }}`)"
                                            class="wallet_checked" value="1"
                                            data-amount="{{ \App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0 }}"
                                            checked>&nbsp;{{ translate('apply_Wallet') }}
                                    </div>
                                </div>
                            @endif
                            <div class="pt-2 d-flex justify-content-between">
                                <span class="cart_value">{{ translate('Item') }}</span>
                                <!-- <span class="cart_value">Qty</span> -->
                                <span class="cart_value">{{ translate('Price') }}</span>

                            </div>
                            <hr class="my-2">
                            <div id="productCount">
                                <div class="finalProduct">
                                    <input type="hidden" name="final_price" id="productCountFinal" value="00">
                                    <div class="d-flex justify-content-between">
                                        <span class="cart_title">Tour Amount</span>
                                        <span class="cart_value totalProduct">
                                            {{ webCurrencyConverter(amount: $cab_price) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                <form class="needs-validation" action="javascript:" method="post" novalidate
                                    id="coupon-code-events-ajax">
                                    <div class="d-flex form-control rounded-pill ps-3 p-1">
                                        <img width="24"
                                            src="{{ theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg') }}"
                                            alt="">
                                        <input type="hidden" name="user_id"
                                            value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                                        <input type="hidden" name="amount" value="{{ $cab_price }}"
                                            class="coupan_amount_min">
                                        <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100"
                                            type="text" name="coupon_code"
                                            placeholder="{{ translate('coupon_code') }}">
                                        <button
                                            class="btn btn--primary rounded-pill text-uppercase py-1 fs-12 coupan_apply_text"
                                            type="button" id="events-coupon-code">
                                            {{ translate('apply') }}
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">{{ translate('please_provide_coupon_code') }}</div>
                                </form>
                                <span id="route-coupon-events"
                                    data-url="{{ url('api/v1/tour/tour-coupon-apply') }}"></span>
                                <div class="justify-content-between  mt-3 mb-2 Coupon_apply_discount_css d-none">
                                    <span class="cart_title">{{ translate('coupon_Discount ') }}</span>
                                    <span class="cart_value Coupon_apply_discount"> -
                                        {{ webCurrencyConverter(amount: 0) }} </span>
                                </div>
                            </div>

                            <div class="justify-content-between d-none">
                                <span class="cart_title text-primary font-weight-bold">Tour Amount</span>
                                <span class="cart_value" id="mainProductPrice"
                                    data-price="{{ $cab_price }}">{{ webCurrencyConverter(amount: $cab_price) }}
                                </span>
                            </div>
                        </div>
                        <div class=" d-none show_user_wallet_amount">
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="cart_title text-success font-weight-bold"><img width="20"
                                        src="{{ theme_asset(path: 'public/assets/back-end/img/admin-wallet.png') }}"
                                        style="margin-top: -9px;"> User Wallet
                                    <small>({{ webCurrencyConverter(amount: \App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0) }})</small></span>
                                <span class="cart_value text-success user_wallet_amount"> </span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span
                                    class="cart_title text-success font-weight-bold user_wallet_am_remaining_text font-weight-bold"
                                    style="    color: darkred !important;"></span>
                                <span class="cart_value text-success user_wallet_amount_remaining"
                                    style="color: darkred !important;"> </span>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mt-2">
                            <span
                                class="cart_title font-weight-bold font-weight-bold">{{ translate('final_Amount') }}</span>
                            <span class="cart_value final_amount_pay"> {{ webCurrencyConverter(amount: $cab_price) }}
                            </span>
                        </div>
                        <hr class="my-2">
                        @if (1 == 1)
                            @foreach ($payment_gateways_list as $payment_gateway)
                                <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form" action="{{ route('tour-payment-request', [$id]) }}" onsubmit="return formcheck()">
                                    @csrf
                                    <div class="Details">
                                        <input type="hidden" name="user_id" value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                                        <input type="hidden" name="customer_id" value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                                        <input type="hidden" name="payment_method" value="{{ $payment_gateway->key_name }}">
                                        <input type="hidden" name="payment_platform" value="web">
                                        @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                            <input type="hidden" name="callback"  value="{{ $payment_gateway->live_values['callback_url'] }}">
                                        @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                            <input type="hidden" name="callback" value="{{ $payment_gateway->test_values['callback_url'] }}">
                                        @else
                                            <input type="hidden" name="callback" value="">
                                        @endif
                                        <input type="hidden" name="external_redirect_link" value="{{ route('tour.tour-pay-success', [$id]) }}">
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
                                        <input type="hidden" name="package_id" value="{{ $getleads['package_id'] }}">
                                        <input type="hidden" name="leads_id" value="{{ $getleads['id'] }}">
                                        <input type="hidden" name="coupon_amount" value="" class='Coupon_apply_discount discount_show' data-discouponamount="0">
                                        <input type="hidden" name="coupon_id" value="" class='Coupon_apply_id'>
                                        <input type="hidden" name="payment_amount" class="mainProductPriceInput"  value="{{ $cab_price }}">
                                        <input type="hidden" name="use_date" value="{{ $getfirst['use_date'] }}">
                                        <input type="hidden" name='pickup_date' class="pickup_date" value="{{ (explode(' - ', $getfirst['startandend_date'])[0]??'')}}">
                                        <input type="hidden" name='pickup_time' class="pickup_time"  value="{{ $getfirst['pickup_time']??''}}">
                                        <input type="hidden" name='pickup_address' class="pickup_address"  value="{{ $getfirst['pickup_location']??''}}">
                                        <input type="hidden" name='pickup_lat' class="pickup_lat"  value="{{ $getfirst['pickup_lat']??''}}">
                                        <input type="hidden" name='pickup_long' class="pickup_long"  value="{{ $getfirst['pickup_long'] ?? '' }}">
                                        <input type="hidden" name='qty' class="qty_order" value='1'>
                                        <input type="hidden" name='type' class="user-wallet-adds">
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn--primary btn-block name_change_continues">{{ translate('Proceed_To_Checkout') }}</button>
                                    </div>
                                </form>
                            @endforeach
                        @endif
                    </div>
                </aside>

            </section>
            <section class="col-lg-6 px-max-md-0">
                <div class="cards">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                @php
                                    $dateRange = explode(' - ', $getfirst['startandend_date']);
                                    $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
                                    $endDate = isset($dateRange[1]) ? $dateRange[1] : '';
                                @endphp
                                <div class="row mt-2 table-responsive">
                                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle">
                                        <tbody>
                                            <tr>
                                                <td><i class="fa fa-calendar" aria-hidden="true"
                                                        style="color: var(--primary-clr);"></i></td>
                                                <td>
                                                    @if ($getfirst['use_date'] == 0)
                                                        <input class="form-control hasDatepicker text-align-direction"
                                                            type="text" name="booking_date" id="bookingdate"
                                                            placeholder="Booking Slot Date"
                                                            onchange="$('.pickup_date').val(this.value)"
                                                            onclick="datePicker(this)" input-mode="text"
                                                            autocomplete="off" required>
                                                    @else
                                                        {{ date('d M, Y', strtotime($startDate)) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($getfirst['use_date'] == 0)
                                                        <input type="text" name='date'
                                                            class="form-control w-50 pickupopen_time" id="opentime"
                                                            onkeyup="$('.pickup_time').val(this.value)"
                                                            onchange="$('.pickup_time').val(this.value)">
                                                    @else
                                                        @if ($startDate != $endDate)
                                                            {{ date('d M, Y', strtotime($endDate)) }}
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                            @if ($getfirst['use_date'] == 1)
                                                <tr>
                                                    <td><i class="fa fa-clock-o" aria-hidden="true"
                                                            style="color: var(--primary-clr);"></i></td>
                                                    <td colspan='2'>{{ $getfirst['pickup_time'] ?? '' }}</td>
                                                </tr>

                                                <tr>
                                                    <td><i class="fa fa-map-marker" aria-hidden="true"
                                                            style="color: var(--primary-clr);"></i></td>
                                                    <td colspan='2'>{{ $getfirst['pickup_location'] ?? '' }}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="3">
                                                        <div class="row">
                                                            <div id="map"></div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                @if ($getfirst['use_date'] == 0)
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-1">
                                                    <i class="fa fa-map-marker" aria-hidden="true"
                                                        style="color: var(--primary-clr);font-size: 27px; margin: 22px 0px 0px 5px;"></i>
                                                </div>
                                                <div class="col-11">
                                                    <input type="hidden" id="city"
                                                        value="{{ $getfirst['cities_name'] }}"
                                                        placeholder="Enter city name" />
                                                    <label for="">{{ translate('Pickup Location') }}</label>
                                                    <input id="search-box" type="text"
                                                        class="pick_up-input form-control mb-2"
                                                        placeholder="{{ translate('Search Pickup locations') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div id="map"></div>
                                            <div id="message"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>

    <div class="modal view-plan-services" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('plan_information') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="plan-service-html">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        // Total Payment
        function addEventProduct(that) {
            var lead_id = $(that).data('lead_id');
            var venue_id = $(that).data('venue_id');
            var package_id = $(that).data('package_id');
            $.ajax({
                url: "{{ route('event-booking-leads-update') }}",
                method: 'POST',
                data: {
                    lead_id: lead_id,
                    venue_id: venue_id,
                    package_id: package_id,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {}
            });

        }
        let qtys = 1;

        function updateProductPrice(lead_id = null, qty_in = 'de') {

            var amount = $('#mainProductPrice').data('price');
            var amount_old = "{{ $cab_price }}";
            var qty = parseInt($(".cartQuantity").val());
            $(".qty_order").val(qty);
            var oldqty = parseInt("{{ $cab_people }}");

            let totalPrice = 0;
            totalPrice = amount_old * Math.ceil(qty / oldqty);
            ///////////////////////////////////////////////////////////
            var isChecked = $('.wallet_checked').prop('checked');
            let walletAmount = $('.wallet_checked').data('amount');
            $('.totalProduct').text(`₹${(parseFloat(totalPrice)).toFixed(2)}`);
            $(".coupan_amount_min").val(totalPrice);
            var amountdis = $(".discount_show").data('discouponamount');
            if (amountdis < 0) {
                totalPrice = Number(totalPrice);
            } else {
                if (qty_in === 'in' && amountdis != 0) {
                    $(".Coupon_apply_discount").val(0);
                    $(".Coupon_apply_id").val('');
                    $(".Coupon_apply_discount").text('');
                    $(".discount_show").data('discouponamount', 0);
                    $(".Coupon_apply_discount_css").addClass('d-none');
                    $(".Coupon_apply_discount_css").removeClass('d-flex');
                    toastr.error('Remove Coupon', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    $(".coupan_apply_text").text("{{ translate('apply') }}");
                }
                totalPrice = Number(totalPrice) - Number(amountdis);
            }
            //////////////////////


            ///////////////////////////
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
                    $('.final_amount_pay').text(
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
                    $(".user_wallet_amount_remaining").text(`-${formattedAmount}`);
                    $(".user_wallet_am_remaining_text").text("{{ translate('remaining_amount') }}");
                    $('.final_amount_pay').text(
                        `${formattedAmount.toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                        );
                }
            } else {
                $(".user-wallet-adds").val(0);
                $(".show_user_wallet_amount").addClass('d-none');
                $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout') }}`);
                $(".user_wallet_amount_remaining").text('');
                $(".user_wallet_am_remaining_text").text('');
                $('.final_amount_pay').text(
                    `${totalPrice.toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`);
            }
            ///////////////////////////////////////////////////////////////
            $('#mainProductPrice').text(`₹${(parseFloat(totalPrice)).toFixed(2)}`);
            $('#mainProductPrice').data('price', totalPrice);
            $(".mainProductPriceInput").val(totalPrice);
            if (qty_in === 'in' && amountdis != 0) {
                updateProductPrice(lead_id, 'de');
            }
        }
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>
    <script>
        function QuantityUpdate(lead_id, people, amount, type) {
            var inputBox = $('.cartQuantity').val();
            if (inputBox == 1 && type == 'de') {
                toastr.warning('Minimum 1 people Applicable.');
                $('.DeleteIcon').addClass('d-none');
            } else {
                if (type == 'de') {
                    var newQuantity = parseInt(inputBox) - 1;
                } else {
                    var newQuantity = parseInt(inputBox) + 1;
                }
                ProductQuantity(lead_id, amount, newQuantity, type);
                $('.DeleteIcon').removeClass('d-none');
                $('.cartQuantity').val(newQuantity)
                updateProductPrice(lead_id, 'in');
            }
        }

        function ProductQuantity(lead_id, amount, quantity, type) {
            // $.ajax({
            //     url: "",
            //     method: 'POST',
            //     data: {
            //         lead_id,
            //         amount,
            //         quantity,
            //         type,
            //         _token: '{{ csrf_token() }}',
            //         coupon_amount: $('.Coupon_apply_discount').val(),
            //         coupon_id: $('.Coupon_apply_id').val(),
            //     },
            //     headers: {
            //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //     },
            //     success: function(response) {
            //         toastr.success(response.message);
            //         location.reload();
            //     },
            //     error: function(xhr, status, error) {
            //         console.error(xhr.responseText);
            //     }
            // });
        }



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
                        $(".coupan_apply_text").text("{{ translate('applyed') }}");
                        $(".Coupon_apply_discount").val(data.data['coupon_amount']);
                        $(".discount_show").data('discouponamount', data.data['coupon_amount']);
                        $(".Coupon_apply_id").val(data.data['coupon_id']);
                        $("#mainProductPriceInput").val(data.data['final_amount']);
                        $('#mainProductPrice').text(`₹${(data.data['final_amount']).toFixed(2)}`);
                        $(".Coupon_apply_discount").text(`- ₹${data.data['coupon_amount']}`);
                        $(".Coupon_apply_discount_css").addClass('d-flex');
                        $(".Coupon_apply_discount_css").removeClass('d-none');
                        toastr.success(messages, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else {
                        $(".coupan_apply_text").text("{{ translate('apply') }}");
                        $(".Coupon_apply_discount").val(0);
                        $(".Coupon_apply_id").val('');
                        $("#mainProductPriceInput").val("{{ $final_price_val }}");
                        $('#mainProductPrice').text(`{{ webCurrencyConverter(amount: $final_price_val) }}`);
                        $(".Coupon_apply_discount").text('');
                        $(".discount_show").data('discouponamount', 0);

                        $(".Coupon_apply_discount_css").addClass('d-none');
                        $(".Coupon_apply_discount_css").removeClass('d-flex');
                        toastr.error(messages, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                    updateProductPrice(`{{ $getleads['id'] }}`);
                    // var lead_id = "getLeads";
                    // $.ajax({
                    //     url: "{{ route('event-booking-leads-qty-update') }}",
                    //     method: 'POST',
                    //     data: {
                    //         lead_id: "getLeads",
                    //         amount: "getLeads_amount",
                    //         quantity: parseInt($('#cart_quantity_web' + lead_id).val()),
                    //         type: '',
                    //         _token: '{{ csrf_token() }}',
                    //         coupon_amount: $('.Coupon_apply_discount').val(),
                    //         coupon_id: $('.Coupon_apply_id').val(),
                    //     },
                    //     headers: {
                    //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    //     },
                    //     success: function(response) {},
                    //     error: function(xhr, status, error) {
                    //         console.error(xhr.responseText);
                    //     }
                    // });
                }
            });
        }
    </script>

    <script>
        toastr.options = {
            "closeButton": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        var forms = document.querySelectorAll('.digital_payment');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                var amountInput = form.querySelector('input[name="payment_amount"]');
                var amount = parseFloat(amountInput.value);
                if (amount <= 0 || isNaN(amount) || amount === "") {
                    event.preventDefault();
                    toastr.error('{{ translate('The payment amount must be greater than 0') }}.');
                } else {
                    return false;
                }
            });
        });

        document.getElementById('bookingForm').addEventListener('submit', function(event) {
            var qty = document.getElementById('free_booking_qty').value;
            if (qty <= 0 || isNaN(qty) || qty === "") {
                event.preventDefault();
                toastr.error('{{ translate('Select Venue') }}');
            } else {
                event.preventDefault();
                Swal.fire({
                    title: '{{ translate('Are you sure?') }}',
                    text: '{{ translate('Do you want to proceed with the booking?') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ translate('Yes, book now!') }}',
                    cancelButtonText: '{{ translate('Cancel') }}'
                }).then((result) => {
                    if (result.value == true) {
                        document.getElementById('bookingForm').submit();
                    }
                });
            }
        });

        function datePicker() {
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(today.getDate());
            $('#bookingdate').datepicker({
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
        $(document).ready(function() {
            datePicker();
            $('#opentime').timepicker({
                uiLibrary: 'bootstrap4',
                format: 'hh:MM TT', // Correct format for time display (12-hour with AM/PM)
                modal: true,
                footer: true
            });
        });
    </script>
    <script>
        if ("{{ $getfirst['use_date'] }}" == 0) {
            window.onload = function() {
                searchCity();
            }
            var map, marker, circle, autocomplete, messageBox;

            function initMap() {
                var defaultLocation = {
                    lat: 23.1765,
                    lng: 75.7885
                };
                map = new google.maps.Map(document.getElementById('map'), {
                    center: defaultLocation,
                    zoom: 13
                });
                marker = new google.maps.Marker({
                    map: map,
                    draggable: true,
                    visible: false
                });
                var input = document.getElementById('search-box');
                autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);
                autocomplete.setComponentRestrictions({
                    country: []
                });

                messageBox = document.getElementById('message');

                autocomplete.addListener('place_changed', function() {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        toastr.error("Place details not available.");
                        return;
                    }

                    var placeLocation = place.geometry.location;

                    if (circle && google.maps.geometry.spherical.computeDistanceBetween(placeLocation, circle
                            .getCenter()) <= circle.getRadius()) {
                        map.setCenter(placeLocation);
                        marker.setPosition(placeLocation);
                        marker.setVisible(true);
                        toastr.success("Authenticated location!");
                        var latitude = placeLocation.lat();
                        var longitude = placeLocation.lng();

                        $('.pickup_lat').val(latitude);
                        $('.pickup_long').val(longitude);
                        $('.pickup_address').val($('.pick_up-input').val());
                    } else {
                        toastr.error("Un-authenticated location!");
                        marker.setVisible(false);
                        $('.pickup_lat').val('');
                        $('.pickup_long').val('');
                        $('.pickup_address').val('');
                    }
                });
            }

            function searchCity() {
                var cityName = document.getElementById('city').value;

                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({
                    address: cityName
                }, function(results, status) {
                    if (status === 'OK') {
                        var cityLocation = results[0].geometry.location;
                        map.setCenter(cityLocation);
                        marker.setPosition(cityLocation);
                        marker.setVisible(true);

                        if (circle) {
                            circle.setMap(null);
                        }

                        circle = new google.maps.Circle({
                            map: map,
                            center: cityLocation,
                            radius: 10000,
                            fillColor: '#5aaf548a',
                            fillOpacity: 0.3,
                            strokeColor: '#5aaf548a',
                            strokeOpacity: 0.8,
                            strokeWeight: 2
                        });

                        map.addListener('click', function(event) {
                            var distance = google.maps.geometry.spherical.computeDistanceBetween(event
                                .latLng, circle.getCenter());
                            if (distance <= circle.getRadius()) {
                                marker.setPosition(event.latLng);
                                marker.setVisible(true);

                            } else {
                                toastr.error('Please click within the circle boundary.');
                            }
                        });
                    } else {
                        alert('City not found: ' + status);
                    }
                });
            }

            google.maps.event.addDomListener(window, 'load', initMap);

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
    </script>
    <script>
        function formcheck() {
            var pickup_address = $('.pickup_address').val().trim();
            var pickup_date = $('.pickup_date').val();
            var pickup_time = $('.pickup_time').val();
            $('.pick_up-input').removeClass('is-invalid');
            $('.hasDatepicker').removeClass('is-invalid');
            $('.pickupopen_time').removeClass('is-invalid');
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
            return checkvalid;
        }

        updateProductPrice(`{{ $getleads['id'] }}`);
        $(".owl-carousel").owlCarousel({
            items: 3,
            margin: 10,
            loop: true,
            autoplay: true,
            nav: true,
            navText: directionFromSession === 'rtl' ? ["<i class='czi-arrow-right'></i>",
                "<i class='czi-arrow-left'></i>"
            ] : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
            dots: false,
            autoplayTimeout: 9000,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        });
    </script>
@endpush
