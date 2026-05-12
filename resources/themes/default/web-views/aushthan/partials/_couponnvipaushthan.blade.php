@php
    $coupon_dis = 0;
@endphp
@if (session()->has('coupon_discount_vipanushthan'))
    @php
        $couponDiscount = session()->has('coupon_discount_vipanushthan') ? session('coupon_discount_vipanushthan') : 0;
    @endphp
    <div class="d-flex justify-content-between">
        <span class="cart_title">{{ translate('coupon_discount_vipanushthan') }}</span>
        <span class="cart_value"> - {{ webCurrencyConverter(amount: $couponDiscount) }} </span>
    </div>
    <div class="pt-2">
        <div class="d-flex align-items-center form-control rounded-pill pl-3 p-1">
            <img width="24" src="{{ asset('public/assets/front-end/img/icons/coupon.svg') }}" alt="">
            <div class="px-2 d-flex justify-content-between w-100">
                <div>
                    {{ session('coupon_code_vipanushthan') }}
                    <span class="text-primary small">( - {{ webCurrencyConverter(amount: $couponDiscount) }} )</span>
                </div>
                <div class="bg-transparent text-danger cursor-pointer px-2 get-view-by-onclick"
                    data-link="{{ route('coupon.poojaremovecoupon', ['code' => session('coupon_code_vipanushthan')]) }}">
                    x</div>
            </div>
        </div>
    </div>
@else
{{-- <div class="row"> --}}
    <div class="pt-2">
        <form class="needs-validation" action="javascript:" method="post" novalidate id="coupon-code-pooja-ajax">
            <div class="d-flex form-control rounded-pill ps-3 p-1">
                <img width="24" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg') }}"
                    alt="" onclick="anushthanCouponList()">
                <input type="hidden" name="leads_id" value="{{ $leadsGet->id }}">
                <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100" type="text"
                    name="code" placeholder="{{ translate('click_here_to_view') }}" required>
                <button class="btn btn--primary rounded-pill text-uppercase py-1 fs-12" type="button"
                    id="pooja-coupon-code">
                    {{ translate('apply') }}
                </button>
            </div>
            <div class="invalid-feedback">{{ translate('please_provide_coupon_code') }}</div>
        </form>
    </div>
    {{-- <div class="col-md-1 pt-2">
        <button class="btn btn--primary btn-sm rounded-pill" onclick="anushthanCouponList()"><i
                class="fa fa-eye fa-lg"></i></button>
    </div>
</div> --}}
    @php($coupon_dis = 0)@endphp
@endif
<span id="route-coupon-pooja" data-url="{{ route('coupon.couponapply') }}"></span>
<hr class="my-2">
@if (session()->has('coupon_discount_vipanushthan'))
    <div class="d-flex justify-content-between">
        <span class="cart_title text-primary font-weight-bold">Total</span>
        <span class="cart_value" id="mainProductPrice">
            @php
                $couponTotalAmount =
                    $leadsGet['package_price'] + $final_price_val - $couponDiscount - $customer->wallet_balance;
            @endphp
            @if ($customer->wallet_balance < $productTotalAmount)
                {{ webCurrencyConverter(amount: $couponTotalAmount) }}
            @else
                {{ webCurrencyConverter(0.0) }}
            @endif
        </span>
    </div>
@else
    <div class="d-flex justify-content-between">
        <span class="cart_title text-primary font-weight-bold">Total</span>
        <span class="cart_value" id="mainProductPrice">
            @if ($customer->wallet_balance < $productTotalAmount)
                {{ webCurrencyConverter($leadsGet['package_price'] + $final_price_val - $customer->wallet_balance) }}
            @else
                {{ webCurrencyConverter(0.0) }}
            @endif
        </span>
    </div>
@endif
@if ($digital_payment['status'] == 1)
    @foreach ($payment_gateways_list as $payment_gateway)
        <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form"
            action="{{ route('customer.anushthan-payment-request') }}">
            @csrf
            <!-- <form action="{{ route('sankalp', $leadsGet['id']) }}" method="get"> -->
            <div class="Details">
                <input type="hidden" name="user_id"
                    value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                <input type="hidden" name="customer_id"
                    value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                <input type="hidden" name="payment_method" value="{{ $payment_gateway->key_name }}">
                <input type="hidden" name="payment_platform" value="web">
                @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                    <input type="hidden" name="callback" value="{{ $payment_gateway->live_values['callback_url'] }}">
                @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                    <input type="hidden" name="callback" value="{{ $payment_gateway->test_values['callback_url'] }}">
                @else
                    <input type="hidden" name="callback" value="">
                @endif
                <input type="hidden" name="external_redirect_link" value="{{ url('/') . '/anushthan-web-payment' }}">
                <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                    <input type="radio" id="{{ $payment_gateway->key_name }}" name="online_payment"
                        class="form-check-input custom-radio" value="{{ $payment_gateway->key_name }}" hidden>
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

                <input type="hidden" name="booking_date" value="{{ $leadsDetails->booking_date }}">
                <input type="hidden" name="service_id" value="{{ $leadsDetails['vippooja']['id'] }}">
                <input type="hidden" name="pandit_assign" value="{{ $leadsDetails->pandit_assign }}">
                <input type="hidden" name="leads_id" value="{{ $leadsGet->id }}">
                <input type="hidden" name="package_id" id="packagesId" value="{{ $leadsGet['package_id'] }}">
                <input type="hidden" name="package_name" id="packagesName"
                    value="{{ $leadsGet['package_name'] }}">
                <input type="hidden" name="package_price" id="packagesPrice"
                    value="{{ $leadsGet['package_price'] }}">
                <input type="hidden" name="noperson" id="packagesPerson" value="{{ $leadsGet['noperson'] }}">
                <input type="hidden" name="person_phone" id="PersonPhobne"
                    value="{{ $leadsGet['person_phone'] }}">
                <input type="hidden" name="person_name" id="PersonName" value="{{ $leadsGet['person_name'] }}">
                <input type="hidden" name="wallet_balance"
                    value="{{ $customer->wallet_balance < $productTotalAmount ? $customer->wallet_balance : $productTotalAmount }}">
                <input type="hidden" name="final_amount" id="Amount"
                    value="{{ $leadsGet['package_price'] + $final_price_val }}">
                @if (session()->has('coupon_code_vippooja'))
                    @php
                        $couponTotalAmount =
                            $leadsGet['package_price'] + $final_price_val - $couponDiscount - $customer->wallet_balance;
                    @endphp
                    @if ($customer->wallet_balance < $productTotalAmount)
                        <input type="hidden" name="payment_amount" id="mainProductPriceInput"
                            value="{{ $couponTotalAmount }}">
                    @else
                        <input type="hidden" name="payment_amount" id="mainProductPriceInput"
                            value="{{ 0.0 }}">
                    @endif
                @else
                    @if ($customer->wallet_balance < $productTotalAmount)
                        <input type="hidden" name="payment_amount" id="mainProductPriceInput"
                            value="{{ $leadsGet['package_price'] + $final_price_val - $customer->wallet_balance }}">
                    @else
                        <input type="hidden" name="payment_amount" id="mainProductPriceInput"
                            value="{{ 0.0 }}">
                    @endif
                @endif
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn--primary btn-block" data-id="{{ $leadsGet->id }}"
                    data-name="{{ $leadsGet->service_name }}" data-price="{{ $leadsGet['package_name'] }}">Proceed
                    to Checkout</button>
            </div>
        </form>
    @endforeach
@endif
