@extends('layouts.front-end.app')

@section('title', $leadsDetails['service']['name'])

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/payment.css') }}">
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
        <script type="text/javascript">
            function preventBack() {
                window.history.forward(); 
            }
            setTimeout("preventBack()", 0);
            window.onunload = function () { null };
        </script>
@endpush

@section('content')
    <div class="w-full h-full sticky md:top-[68px] top-0 z-20">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.counselling.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-3 rtl px-0 px-md-3 mb-4 text-align-direction" id="cart-summary">

        <div class="row g-3 mx-max-md-0">
            <section class="col-lg-6 px-max-md-0" style="display: flex; justify-content: center; height:450px;">
                <img src="{{ asset('public/storage/pooja/thumbnail/' . $leadsDetails['service']['thumbnail']) }}"
                    class="img-fluid m-2" alt="">
            </section>

            <section class="col-lg-6 px-max-md-0">
                <div class="cards">
                    <div class="card-header" id="">
                        <div class="details __h-100 mb-5">
                            <span class="mb-2 __inline-24">{{ $leadsDetails['service']['name'] }}</span>
                        </div>
                        {!! $leadsDetails['service']['details'] !!}
                    </div>
                </div>

                <aside class="col-lg-12 pt-2 pt-lg-2 px-max-md-0 order-summery-aside">
                    <div class="__cart-total __cart-total_sticky">
                        <div class="cart_total p-0">
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="cart_title">{{ $leadsDetails['service']['name'] }}</span>
                                <span class="cart_value">{{ webCurrencyConverter(amount: $leadsDetails['package_price']) }}
                                </span>
                            </div>
                           
                            @php
                            $coupon_dis = 0;
                            @endphp
                            @if (session()->has('coupon_discount_counselling'))
                                @php
                                    $couponDiscount = session()->has('coupon_discount_counselling')  ? session('coupon_discount_counselling') : 0;
                                @endphp
                                <div class="d-flex justify-content-between">
                                    <span class="cart_title">{{ translate('coupon_discount_counselling') }}</span>
                                    <span class="cart_value"> -  {{ webCurrencyConverter(amount: $couponDiscount) }} </span>
                                </div>
                                <div class="pt-2">
                                    <div class="d-flex align-items-center form-control rounded-pill pl-3 p-1">
                                        <img width="24" src="{{ asset('public/assets/front-end/img/icons/coupon.svg') }}"
                                            alt="">
                                        <div class="px-2 d-flex justify-content-between w-100">
                                            <div>
                                                {{ session('coupon_code_counselling') }}
                                                <span class="text-primary small">(
                                                    -  {{ webCurrencyConverter(amount: $couponDiscount) }} )</span>
                                            </div>
                                            <div class="bg-transparent text-danger cursor-pointer px-2 get-view-by-onclick-remove"
                                                data-link="{{ route('coupon.poojaremovecoupon', ['code' => session('coupon_code_counselling')]) }}">
                                                x</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="pt-2">
                                    <form class="needs-validation" action="javascript:" method="post" novalidate
                                        id="coupon-code-pooja-ajax">
                                        <div class="d-flex form-control rounded-pill ps-3 p-1">
                                            <img width="24"
                                                src="{{ theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg') }}"
                                                alt="">
                                            <input type="hidden" name="leads_id" value="{{ $leadsDetails['id'] }}">
                                            <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100"
                                                type="text" name="code" placeholder="{{ translate('coupon_code') }}"
                                                required>
                                            <button class="btn btn--primary rounded-pill text-uppercase py-1 fs-12"
                                                type="button" id="pooja-coupon-code">
                                                {{ translate('apply') }}
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">{{ translate('please_provide_coupon_code') }}</div>
                                    </form>
                                </div>
                                @php($coupon_dis = 0)@endphp
                            @endif
                            <span id="route-coupon-pooja" data-url="{{ route('coupon.couponapply') }}"></span>
                            <hr class="my-2">
                            @if(session()->has('coupon_discount_counselling'))
                            <div class="d-flex justify-content-between">
                                <span class="cart_title text-primary font-weight-bold">{{translate('total')}}</span>
                                <span class="cart_value" id="mainProductPrice"> {{ webCurrencyConverter(amount:  $leadsDetails['package_price'] - $couponDiscount) }}</span>
                            </div>
                            @else
                            <div class="d-flex justify-content-between">
                                <span class="cart_title text-primary font-weight-bold">Total</span>
                                <span class="cart_value" id="mainProductPrice">{{ webCurrencyConverter(amount:  $leadsDetails['package_price']) }}</span>
                            </div>
                            @endif
                        </div>
                        <hr class="my-2">

                        @if ($digital_payment['status'] == 1)
                            @foreach ($payment_gateways_list as $payment_gateway)
                                <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form"
                                    action="{{ route('customer.counselling-payment-request') }}">
                                    @csrf
                                    <div class="Details">
                                        <input type="hidden" name="user_id"
                                            value="{{ auth('customer')->check() ? auth('customer')->user()->id : $user['id'] }}">
                                        <input type="hidden" name="customer_id"
                                            value="{{ auth('customer')->check() ? auth('customer')->user()->id : $user['id'] }}">
                                        <input type="hidden" name="payment_method"
                                            value="{{ $payment_gateway->key_name }}">
                                        <input type="hidden" name="payment_platform" value="web">
                                        @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                            <input type="hidden" name="callback"
                                                value="{{ $payment_gateway->live_values['callback_url'] }}">
                                        @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                            <input type="hidden" name="callback"
                                                value="{{ $payment_gateway->test_values['callback_url'] }}">
                                        @else
                                            <input type="hidden" name="callback" value="">
                                        @endif

                                        <input type="hidden" name="external_redirect_link"
                                            value="{{ url('/') . '/counselling-web-payment' }}">

                                        <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                            <input type="radio" id="{{ $payment_gateway->key_name }}"
                                                name="online_payment" class="form-check-input custom-radio"
                                                value="{{ $payment_gateway->key_name }}" hidden>
                                            <img width="30"
                                                src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}"
                                                alt="" hidden>
                                            <span class="text-capitalize form-check-label" hidden>
                                                @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                                    {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                                @else
                                                    {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                                @endif
                                            </span>
                                        </label>
                                        <input type="hidden" name="leads_id" value="{{ $leadsDetails['id'] }}">
                                        <input type="hidden" name="service_id" value="{{ $leadsDetails['service_id'] }}">
                                        @if(session()->has('coupon_discount_counselling'))
                                        <input type="hidden" name="payment_amount" value="{{ $leadsDetails['package_price'] - $couponDiscount }}">
                                        @else
                                        <input type="hidden" name="payment_amount" value="{{ $leadsDetails['package_price'] }}">
                                        @endif
                                        <input type="hidden" name="person_name"
                                            value="{{ $leadsDetails['person_name'] }}">
                                        <input type="hidden" name="person_phone"
                                            value="{{ $leadsDetails['person_phone'] }}">

                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn--primary btn-block"
                                            data-id="{{ $leadsDetails['id'] }}" data-name="{{ $leadsDetails['name'] }}"
                                            data-price="{{ $leadsDetails['package_price'] }}">{{translate('Proceed to Checkout')}}</button>
                                    </div>
                                </form>
                            @endforeach
                        @endif
                    </div>
                </aside>
            </section>
        </div>
    </div>
@endsection
@push('script')

    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>
@endpush
