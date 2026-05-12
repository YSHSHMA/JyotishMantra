@php
    $coupon_dis = 0;
@endphp
@if (session()->has('coupon_discount_offlinepooja'))
    @php
        $couponDiscount = session()->has('coupon_discount_offlinepooja') ? session('coupon_discount_offlinepooja') : 0;
    @endphp
    <div class="d-flex justify-content-between">
        <span class="cart_title">{{ translate('coupon_discount_offlinepooja') }}</span>
        <span class="cart_value"> - {{ webCurrencyConverter(amount: $couponDiscount) }} </span>
    </div>
    <div class="pt-2">
        <div class="d-flex align-items-center form-control rounded-pill pl-3 p-1">
            <img width="24" src="{{ asset('public/assets/front-end/img/icons/coupon.svg') }}" alt="">
            <div class="px-2 d-flex justify-content-between w-100">
                <div>
                    {{ session('coupon_code_offlinepooja') }}
                    <span class="text-primary small">( - {{ webCurrencyConverter(amount: $couponDiscount) }} )</span>
                </div>
                <div class="bg-transparent text-danger cursor-pointer px-2 get-view-by-onclick"
                    data-link="{{ route('coupon.offlinepoojaremovecoupon', ['code' => session('coupon_discount_offlinepooja')]) }}">
                    x</div>
            </div>
        </div>
    </div>
@else
    <div class="pt-2">
        <form class="needs-validation" action="javascript:" method="post" novalidate id="coupon-code-pooja-ajax">
            <div class="d-flex form-control rounded-pill ps-3 p-1">
                <img width="24" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg') }}"
                    alt="" onclick="offlinepoojaCouponList()">
                <input type="hidden" name="leads_id" value="{{ $leadsDetails->id }}">
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
    @php($coupon_dis = 0)@endphp
@endif
<span id="route-coupon-pooja" data-url="{{ route('coupon.offlinepoojacouponapply') }}"></span>
<hr class="my-2">
@if (session()->has('coupon_discount_offlinepooja'))
    <div class="row" id="with-coupon-full-div">
        <div class="col-10 text-end">
            <span class="cart_value text--primary">Pay Now</span>
        </div>
        <div class="col-2 text-end">
            <span class="cart_value" id="mainProductPrice">
                @php
                    $couponTotalAmount =
                        $leadsDetails['package_main_price'] +
                        $final_price_val -
                        $couponDiscount -
                        $customer->wallet_balance;
                @endphp
                @if ($customer->wallet_balance < $productFullAmount)
                    {{ webCurrencyConverter(amount: $couponTotalAmount) }}
                    {{-- <input type="hidden" id="full-payment-amount" value="{{ $couponTotalAmount }}"> --}}
                @else
                    {{ webCurrencyConverter(0.0) }}
                    {{-- <input type="hidden" id="full-payment-amount" value="0"> --}}
                @endif
            </span>
        </div>
    </div>
    <div class="row d-none" id="with-coupon-partial-div">
        <div class="col-10 text-end">
            <span class="cart_value text--primary">Pay Now</span>
        </div>
        <div class="col-2 text-end">
            <span class="cart_value" id="mainProductPrice">
                @php
                    $couponTotalAmount =
                        $leadsDetails['package_price'] + $final_price_val - $couponDiscount - $customer->wallet_balance;
                @endphp
                @if ($customer->wallet_balance < $productPartialAmount)
                    {{ webCurrencyConverter(amount: $couponTotalAmount) }}
                    {{-- <input type="hidden" id="partial-payment-amount" value="{{ $couponTotalAmount }}"> --}}
                @else
                    {{ webCurrencyConverter(0.0) }}
                    {{-- <input type="hidden" id="partial-payment-amount" value="0"> --}}
                @endif
            </span>
        </div>
        <div class="col-10 text-end">
            <span class="cart_value text--primary">Remaining</span>
        </div>
        <div class="col-2 text-end">
            <span
                class="cart_value">{{ webCurrencyConverter($leadsDetails['package_main_price'] - $leadsDetails['package_price']) }}</span>
        </div>
        <div class="col-10 text-end">
            <span class="cart_value text--primary">Total</span>
        </div>
        <div class="col-2 text-end">
            <span class="cart_value">{{ webCurrencyConverter($leadsDetails['package_main_price']) }}</span>
        </div>
    </div>
@else
    <div class="row" id="without-coupon-full-div">
        <div class="col-10 text-end">
            <span class="cart_value text--primary">Pay Now</span>
        </div>
        <div class="col-2 text-end">
            <span class="cart_value" id="mainProductPrice">
                @if ($customer->wallet_balance < $productFullAmount)
                    {{ webCurrencyConverter($leadsDetails['package_main_price'] + $final_price_val - $customer->wallet_balance) }}
                    {{-- <input type="hidden" id="full-payment-amount"
                        value="{{ $leadsDetails['package_main_price'] + $final_price_val - $customer->wallet_balance }}"> --}}
                @else
                    {{ webCurrencyConverter(0.0) }}
                    {{-- <input type="hidden" id="full-payment-amount" value="0"> --}}
                @endif
            </span>
        </div>
    </div>
    <div class="row d-none" id="without-coupon-partial-div">
        <div class="col-10 text-end">
            <span class="cart_value text--primary">Pay Now</span>
        </div>
        <div class="col-2 text-end">
            <span class="cart_value" id="mainProductPrice">
                @if ($customer->wallet_balance < $productPartialAmount)
                    {{ webCurrencyConverter($leadsDetails['package_price'] + $final_price_val - $customer->wallet_balance) }}
                    {{-- <input type="hidden" id="partial-payment-amount"
                        value="{{ $leadsDetails['package_price'] + $final_price_val - $customer->wallet_balance }}"> --}}
                @else
                    {{ webCurrencyConverter(0.0) }}
                    {{-- <input type="hidden" id="partial-payment-amount" value="0"> --}}
                @endif
            </span>
        </div>
        <div class="col-10 text-end">
            <span class="cart_value text--primary">Remaining</span>
        </div>
        <div class="col-2 text-end">
            <span
                class="cart_value">{{ webCurrencyConverter($leadsDetails['package_main_price'] - $leadsDetails['package_price']) }}</span>
        </div>
        <div class="col-10 text-end">
            <span class="cart_value text--primary">Total</span>
        </div>
        <div class="col-2 text-end">
            <span class="cart_value">{{ webCurrencyConverter($leadsDetails['package_main_price']) }}</span>
        </div>
    </div>
@endif
@if ($digital_payment['status'] == 1)
    @foreach ($payment_gateways_list as $payment_gateway)
        <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form"
            action="{{ route('customer.offlinepooja-payment-request') }}">
            @csrf

            <div class="Details">
                {{-- <input type="hidden" name="user_id"
                    value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                <input type="hidden" name="customer_id"
                    value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}"> --}}
                <input type="hidden" name="payment_method" value="{{ $payment_gateway->key_name }}">
                <input type="hidden" name="payment_platform" value="web">
                @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                    <input type="hidden" name="callback" value="{{ $payment_gateway->live_values['callback_url'] }}">
                @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                    <input type="hidden" name="callback" value="{{ $payment_gateway->test_values['callback_url'] }}">
                @else
                    <input type="hidden" name="callback" value="">
                @endif
                <input type="hidden" name="external_redirect_link"
                    value="{{ url('/') . '/offlinepooja-web-payment' }}">
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
                <input type="hidden" name="leads_id" value="{{ $leadsDetails->id }}">
                {{-- <input type="hidden" id="wallet-balance" name="wallet_balance"
                    value="{{ $customer->wallet_balance < $productFullAmount ? $customer->wallet_balance : $productFullAmount }}"> --}}
                {{-- <input type="hidden" name="final_amount" id="Amount"
                    value="{{ $leadsDetails['package_main_price'] }}"> --}}
                {{-- <input type="hidden" name="payment_amount" id="mainProductPriceInput"> --}}
                {{-- <input type="hidden" name="service_id" value="{{ $leadsDetails['offlinepooja']['id'] }}">
                <input type="hidden" name="package_id" id="packagesId" value="{{ $leadsDetails['package_id'] }}">
                <input type="hidden" name="package_name" id="packagesName"
                    value="{{ $leadsDetails['package_name'] }}">
                <input type="hidden" name="package_main_price" id="packagesMainPrice"
                    value="{{ $leadsDetails['package_main_price'] }}">
                <input type="hidden" name="package_price" id="packagesPrice"
                    value="{{ $leadsDetails['package_price'] }}">
                <input type="hidden" name="noperson" id="packagesPerson" value="{{ $leadsDetails['noperson'] }}">
                <input type="hidden" name="person_phone" id="PersonPhobne"
                    value="{{ $leadsDetails['person_phone'] }}">
                <input type="hidden" name="person_name" id="PersonName"
                    value="{{ $leadsDetails['person_name'] }}"> --}}
                {{-- <input type="hidden" name="remain_amount" id="remain-amount" value="0"> --}}
                {{-- <input type="hidden" name="remain_amount_status" id="remain-amount-status" value="1"> --}}

            </div>

            <div class="form-group">
                <label for="city" class="form-label">City</label>
                <select name="city" id="city" class="form-control" required>
                    @if ($cities->count() > 0)
                        <option value="">Select city</option>
                        @foreach ($cities as $item)
                            <option value="{{ $item->name }}">{{ $item->name }}</option>
                        @endforeach
                    @else
                        <option value="">City not available</option>
                    @endif
                    <option value="other">Other</option>
                </select>
                <small class="text-danger d-none"
                    id="city-error">{{ translate('new_cities_will_be_available_soon') }}</small>
            </div>

            <div class="mt-4">
                <button type="submit" id="proceed-btn" class="btn btn--primary btn-block"
                    data-id="{{ $leadsDetails->id }}" data-name="{{ $leadsDetails->service_name }}"
                    data-price="{{ $leadsDetails['package_price'] }}" disabled>Proceed
                    to Checkout</button>
            </div>

        </form>
    @endforeach
@endif
