@extends('layouts.front-end.app')
@section('title', $leadsDetails->service_name)
@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/theme.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/payment.css') }}">
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />


    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <style type="text/css">
        #productList {
            background-color: white;
            border-radius: 6px;
            box-shadow: 2px 2px 2px 2px #f3f3f3;
        }

        .flash_deal_product {
            scroll-behavior: smooth;
            height: 150px;
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
            background: #ffffff;
            padding: 6px 8px;
            font-size: 11px;
            border: 1px solid #e4eef9;
            font-weight: 500;
            top: -14px;
            border-radius: 2px;
        }
        .widget-meta p {
            color: #b7b5b5;
            font-size: 4.125rem;
            margin: 1px 0 2px;
        }
    </style>
@endpush
@section('content')
<!-- Couupon Modal -->
<div class="modal fade" id="coupon-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
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
    @php
        $final_price_val = 0;
    @endphp
    <div class="w-full h-full sticky md:top-[68px] top-0 z-20">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.epooja.partials.statusbar')
                </div>
            </div>
        </div>
    </div>

    <div class="container rtl mb-3 py-3" id="cart-summary">
        @php
            $selected_product_array = [];
        @endphp
        <div class="row g-3 mx-max-md-0">
            <section class="col-lg-6">
                <div class="cards" style="border-radius: 10px;border: 1px solid #e4eef9;">
                    <div class="card-header" id="">
                        <div class="details __h-100">
                            <span class="mb-2 __inline-24">{{ $leadsDetails['service']['name'] }}</span>
                            <div class="d-flex justify-content-between"> {{ $leadsGet->package_name }}
                                <span class=""><b>{{ webCurrencyConverter(amount: $leadsGet['package_price']) }}</b></span>
                            </div>
                            <div class="stm">
                                <hr style="position: relative; margin-top: 18px !important;">
                                 <span class="lay">{{translate('Date_and_Place_of_Puja')}}</span>
                            </div>
                            <div class="flex flex-col">
                                <!-- Pooja Venue -->
                                <div class="flex items-center space-x-2">
                                    <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}" alt="" style="width:24px;height:24px;">
                                    <span>{{ $leadsGet->service->pooja_venue }}</span>
                                </div>
                            
                                <!-- Booking Date -->
                                <div class="flex items-center space-x-2 mt-2">
                                    <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}" alt="" style="width:24px;height:24px;">
                                    <span>
                                        {{ date('d', strtotime($leadsGet->booking_date)) }},
                                        {{ translate(date('F', strtotime($leadsGet->booking_date))) }},
                                        {{ translate(date('l', strtotime($leadsGet->booking_date))) }}
                                    </span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div id="collapse{{ $leadsGet->id }}" class="collapse" aria-labelledby=""
                        data-parent="#accordionExample">
                        <div class="card-body">
                            {!! $leadsGet->detail !!}
                        </div>
                    </div>
                </div>
                <div class="mt-3" id="productList">
                    <table class='table table-borderless table-thead-bordered table-nowrap table-align-middle'>
                        <tbody>
                            @if (!empty($leadsGet->productleads))
                                @foreach ($leadsGet->productleads as $key => $pval)
                                    @php
                                        array_push($selected_product_array, $pval->product_id);
                                    @endphp

                                    <tr>
                                        <td class='__w-45'>
                                            <div class='d-flex gap-3'>
                                                <div class=''>
                                                    <a href='http://localhost/mahakal/product/crystal-bracelet-Z7HdvN'
                                                        class='position-relative overflow-hidden'>
                                                        <img class='rounded __img-62 '
                                                            src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $pval->productsData->thumbnail, type: 'product') }}"
                                                            id="Productimage" alt='Product'>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="text-break __line-2 __w-14rem">
                                                        <a href='#' id='productName'>{{ $pval->product_name }}</a>
                                                    </div>
                                                    <div class='d-flex flex-wrap gap-2 '>
                                                        <div class='text-center'>
                                                            <div class='fw-semibold' id='productPrice'>
                                                                {{ $pval->qty }} X
                                                                {{ webCurrencyConverter(amount: $pval->product_price * $pval->qty) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class='__w-15p text-center'>
                                            <div class='qty d-flex justify-content-center align-items-center gap-3'>
                                                <span class="qty_minus" data-cart-id="{{ $pval->product_id }}"
                                                    onclick='QuantityUpdate("{{ $pval->product_id }}", -1, "{{ $pval->id }}", "{{ $pval->product_price }}","{{ $pval->leads_id }}","{{ $leadsGet->package_price }}")'
                                                    data-increment='{{ -1 }}'
                                                    data-event="{{ $pval->qty == 1 ? 'delete' : 'minus' }}"><i
                                                        class="{{ $pval->qty > 1 ? 'tio-remove' : 'tio-delete text-danger' }}"
                                                        id="DeleteIcon{{ $pval->product_id }}"></i>
                                                </span>

                                                <input type='text' class='qty_input cartQuantity{{ $pval->product_id }}'
                                                    value="{{ $pval->qty }}" name='quantity{{ $pval->product_id }}'
                                                    id='cart_quantity_web{{ $pval->product_id }}' data-minimum-order='1'
                                                    data-cart-id='{{ $pval->product_id }}'
                                                    data-increment="{{ '0' }}" readonly
                                                    oninput='QuantityUpdate("{{ $pval->product_id }}","{{ $pval->id }}" this.value)'>

                                                <span class="qty_plus" data-cart-id="{{ $pval->product_id }}"
                                                    data-increment="{{ '1' }}"
                                                    onclick='QuantityUpdate("{{ $pval->product_id }}",1,"{{ $pval->id }}","{{ $pval->product_price }}","{{ $pval->leads_id }}","{{ $leadsGet->package_price }}")'><i
                                                        class='tio-add'></i> </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <aside class="col-lg-12 pt-2 pt-lg-2 px-max-md-0 order-summery-aside" id="price-load">
                    <div class="__cart-total __cart-total_sticky">
                        <div class="cart_total p-0">
                         
                            @if (session()->has('coupon_discount_pooja'))
                                @php
                                    $couponDiscount = session()->has('coupon_discount_pooja')
                                        ? session('coupon_discount_pooja')
                                        : 0;
                                @endphp
                                <div class="pt-2">
                                    <div class="d-flex align-items-center form-control rounded-pill pl-3 p-1">
                                        <img width="24"
                                            src="{{ asset('public/assets/front-end/img/icons/coupon.svg') }}"
                                            alt="">
                                        <div class="px-2 d-flex justify-content-between w-100">
                                            <div>
                                                {{ session('coupon_code_pooja') }}
                                                <span class="text-primary small">(
                                                    -{{ webCurrencyConverter(amount: $couponDiscount) }} )</span>
                                            </div>
                                            <div class="bg-transparent text-danger cursor-pointer px-2 get-view-by-onclick-remove"
                                                data-link="{{ route('coupon.poojaremovecoupon', ['code' => session('coupon_code_pooja')]) }}">
                                                x</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                            {{-- <div class="row"> --}}
                                <div class="pt-2">
                                    <form class="needs-validation" action="javascript:" method="post" novalidate id="coupon-code-pooja-ajax">
                                        <div class="d-flex form-control rounded-pill ps-3 p-1">
                                            <img width="24" src="{{theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg')}}" alt="" onclick="couponList()">
                                            <input type="hidden" name="leads_id" value="{{ $leadsGet->id }}">
                                            <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100"
                                                type="text" name="code" placeholder="{{ translate('click_here_to_view') }}" required>
                                            <button class="btn btn--primary rounded-pill text-uppercase py-1 fs-12" type="button" id="pooja-coupon-code">
                                                    {{translate('apply')}}
                                                </button>
                                        </div>
                                        <div class="invalid-feedback">{{translate('please_provide_coupon_code')}}</div>
                                    </form>
                                </div>
                                {{-- <div class="col-md-1 pt-2">
                                    <button class="btn btn--primary btn-sm rounded-pill" onclick="couponList()"><i
                                            class="fa fa-eye fa-lg"></i></button>
                                </div>
                            </div> --}}
                                @php($coupon_dis = 0)@endphp
                            @endif
                            <span id="route-coupon-pooja" data-url="{{ route('coupon.couponapply') }}"></span>

                            <hr class="my-2">
                            <div class="pt-2 d-flex justify-content-between">
                                <span class="cart_value">{{ translate('Item') }}</span>
                                <!-- <span class="cart_value">Qty</span> -->
                                <span class="cart_value">{{ translate('Price') }}</span>

                            </div>
                            <hr class="my-2">

                            <div class="d-flex justify-content-between">
                                <span class="cart_title">{{ $leadsGet->package_name }}</span>
                                <!-- <span class="cart_value" style="margin-right: 10rem;">X 1 </span> -->
                                <span class="cart_value">{{ webCurrencyConverter(amount: $leadsGet['package_price']) }}
                                </span>
                            </div>
                            <div id="productCount">
                                <div class="finalProduct">
                                    @if (!empty($leadsGet->productleads))
                                        @foreach ($leadsGet->productleads as $pval)
                                            @php
                                                $final_price_val += $pval->product_price * $pval->qty;
                                            @endphp
                                            <input type="hidden" name="final_price"
                                                id="productCountFinal{{ $pval->product_id }}"
                                                value="{{ $pval->final_price }}.00">
                                            <div class="d-flex justify-content-between">
                                                <span class="cart_title">{{ $pval->product_name }}</span>
                                                <!-- <span class="cart_value" style="margin-right: 11rem;">X {{ $pval->qty }}</span> -->
                                                <span class="cart_value totalProduct{{ $pval->product_id }}">
                                                    {{ webCurrencyConverter(amount: $pval->product_price * $pval->qty) }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @if (session()->has('coupon_discount_pooja'))
                                <div class="d-flex justify-content-between">
                                    <span class="cart_title">{{ translate('coupon_discount_pooja') }}</span>
                                    <span class="cart_value"> - {{ webCurrencyConverter(amount: $couponDiscount) }}
                                    </span>
                                </div>
                            @endif
                            <hr class="my-2">

                            @php
                                if (auth('customer')->check()) {
                                    $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                }
                                $couponDiscount = session()->has('coupon_discount_pooja') ? session('coupon_discount_pooja') : 0;
                                $productTotalAmount = $leadsGet['package_price'] + $final_price_val - $couponDiscount;
                                $totalAmount = $productTotalAmount - $customer->wallet_balance;
                                
                            @endphp
                            @if($customer->wallet_balance > 0)
                            <div id="productCounts">
                                <div class="finalProducts">
                                    <div class="d-flex">
                                        <span class="cart_title">{{ translate('wallet_balance') }} </span>
                                        <span class="cart_value text-success">  ({{ webCurrencyConverter(amount: $customer->wallet_balance) }})</span>
                                    </div>                            
                                    <div class="d-flex justify-content-between">
                                        <span class="cart_title">{{ translate('Amount_Paid_(via_Wallet)') }}</span>
                                        @if($customer->wallet_balance < $productTotalAmount)
                                        <span class="cart_value text-danger"> - {{ webCurrencyConverter(amount: $customer->wallet_balance) }}</span>
                                        @else
                                        <span class="cart_value text-danger" id="mainProductPrice"> - {{ webCurrencyConverter(amount: $productTotalAmount) }} </span>
                                        
                                        @endif
                                    </div>
                                    @if($customer->wallet_balance < $productTotalAmount)
                                        <div class="d-flex justify-content-between">
                                            <span class="cart_title">{{ translate('Remaining_Amount_to_Pay') }}</span>
                                            <span class="cart_value text-danger">{{ webCurrencyConverter($totalAmount) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            <hr class="my-2">

                            @if (session()->has('coupon_discount_pooja'))
                                <div class="d-flex justify-content-between">
                                    <span class="cart_title text-primary font-weight-bold">{{ translate('Total') }}</span>
                                    <span class="cart_value" id="mainProductPrice">
                                        @php
                                            $couponTotalAmount=$leadsGet['package_price'] + $final_price_val - $couponDiscount - $customer->wallet_balance;                                        
                                        @endphp
                                        @if($customer->wallet_balance < $productTotalAmount)
                                        {{ webCurrencyConverter(amount: $couponTotalAmount) }}
                                        @else
                                        {{ webCurrencyConverter(0.00) }}
                                        @endif
                                    </span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between">
                                    <span class="cart_title text-primary font-weight-bold">{{ translate('Total') }}</span>
                                    <span class="cart_value"  id="mainProductPrice">
                                    @if($customer->wallet_balance < $productTotalAmount)
                                        {{ webCurrencyConverter($leadsGet['package_price'] + $final_price_val - $customer->wallet_balance) }}
                                    @else
                                        {{ webCurrencyConverter(0.00) }}
                                      
                                    @endif
                                </span>
                                </div>
                            @endif
                        </div>
                        <hr class="my-2">

                        @if ($digital_payment['status'] == 1)
                            @foreach ($payment_gateways_list as $payment_gateway)
                                <form method="post" class="digital_payment" id="{{ $payment_gateway->key_name }}_form"
                                    action="{{ route('customer.services-payment-request') }}">
                                    @csrf
                                    <!-- <form action="{{ route('sankalp', $leadsGet['id']) }}" method="get"> -->
                                    <div class="Details">
                                        <input type="hidden" name="user_id"
                                            value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                                        <input type="hidden" name="customer_id"
                                            value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
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
                                            value="{{ url('/') . '/service-web-payment' }}">
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

                                        <input type="hidden" name="booking_date"
                                            value="{{ $leadsDetails->booking_date }}">
                                        <input type="hidden" name="service_id"
                                            value="{{ $leadsDetails['service']['id'] }}">
                                        <input type="hidden" name="pandit_assign"
                                            value="{{ $leadsDetails->pandit_assign }}">
                                        <input type="hidden" name="leads_id" value="{{ $leadsGet->id }}">

                                     

                                        <input type="hidden" name="wallet_balance"                                            value="{{ $customer->wallet_balance < $productTotalAmount ? $customer->wallet_balance : $productTotalAmount}}">
                                        <input type="hidden" name="package_id" id="packagesId"
                                            value="{{ $leadsGet['package_id'] }}">
                                        <input type="hidden" name="package_name" id="packagesName"
                                            value="{{ $leadsGet['package_name'] }}">
                                        <input type="hidden" name="package_price" id="packagesPrice"
                                            value="{{ $leadsGet['package_price'] }}">
                                        <input type="hidden" name="noperson" id="packagesPerson"
                                            value="{{ $leadsGet['noperson'] }}">
                                        <input type="hidden" name="person_phone" id="PersonPhobne"
                                            value="{{ $leadsGet['person_phone'] }}">
                                        <input type="hidden" name="person_name" id="PersonName"
                                            value="{{ $leadsGet['person_name'] }}">
                                        <input type="hidden" name="final_amount" id="Amount"
                                            value="{{ $leadsGet['package_price'] + $final_price_val }}">
                                        @if (session()->has('coupon_discount_pooja'))
                                            @php
                                                $couponTotalAmount=$leadsGet['package_price'] + $final_price_val - $couponDiscount - $customer->wallet_balance;                                        
                                            @endphp
                                            @if($customer->wallet_balance < $productTotalAmount)
                                                <input type="hidden" name="payment_amount" id="mainProductPriceInput"
                                                value="{{$couponTotalAmount }}">
                                            @else
                                                <input type="hidden" name="payment_amount" id="mainProductPriceInput"
                                                value="{{ 0.00 }}"> 
                                            @endif
                                        @else
                                            @if($customer->wallet_balance < $productTotalAmount)
                                            <input type="hidden" name="payment_amount" id="mainProductPriceInput"
                                                value="{{ $leadsGet['package_price'] + $final_price_val - $customer->wallet_balance }}">
                                                @else
                                            <input type="hidden" name="payment_amount" id="mainProductPriceInput"
                                                value="{{ 0.00 }}">
                                            @endif
                                        @endif
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn--primary btn-block"
                                            data-id="{{ $leadsGet->id }}" data-name="{{ $leadsGet->service_name }}"
                                            data-price="{{ $leadsGet['package_name'] }}">{{ translate('Proceed_to_Checkout') }}</button>
                                    </div>
                                </form>
                            @endforeach
                        @endif
                    </div>
                </aside>
            </section>
            @php
                $productIds = json_decode($leadsGet->product_id);
                $products_data = is_array($productIds)
                    ? \App\Models\Product::whereIn('id', $productIds)->get()
                    : collect();
            @endphp
            <section class="col-lg-6 px-max-md-0">
                <div class="pt-3 pb-3">
                    <span class=" __text-16px font-bold text-capitalize">
                        {{ translate('Add_more_offering_items') }}
                    </span>
                </div>
                <div style="height: 560px; overflow-y: auto;">
                    @if (!empty($products_data))
                        @foreach ($products_data as $product)
                            @if (!in_array($product->id, $selected_product_array))
                                @php($overallRating = getOverallRating($product->reviews))
                                <div class="flash_deal_product rtl cursor-pointer mb-2"
                                    id="get-view-by-onclick{{ $product->id }}"
                                    data-link="{{ route('product', $product->slug) }}" data-Pid="{{ $product->id }}"
                                    data-qtyMin="{{ $product->minimum_order_qty }}"
                                    data-Pname=" {{ $product['name'] }}"
                                    data-Pprice="{{ webCurrencyConverter(amount: $product->unit_price) }}">
                                    <div class="d-flex">
                                        <div class="d-flex align-items-center justify-content-center p-3">
                                            <div class="flash-deals-background-image image-default-bg-color">
                                                <img class="__img-125px" alt=""
                                                    src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}">
                                            </div>
                                        </div>
                                        <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex align-items-center">
                                            <div>
                                                <div>
                                                    <h1 class="flash-product-title"
                                                        style="font-size: 18px;font-weight: 600;line-height: 14px;margin-bottom: 5px;">
                                                        {{ $product['name'] }}
                                                    </h1>
                                                </div>
                                                <div
                                                    class="widget-meta d-flex flex-wrap gap-8 align-items-center row-gap-0">
                                                    <p>{!! Str::limit($product->details, 200) !!}</p>
                                                </div>
                                                @if ($overallRating[0] != 0)
                                                    <div class="flash-product-review">
                                                        @for ($inc = 1; $inc <= 5; $inc++)
                                                            @if ($inc <= (int) $overallRating[0])
                                                                <i class="tio-star text-warning">
                                                                </i>
                                                            @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                                                                <i class="tio-star-half text-warning"></i>
                                                            @else
                                                                <i class="tio-star-outlined text-warning"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                @endif
                                                <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0">
                                                    <span class="flash-product-price fw-semibold text-dark">
                                                        &#8377; {{ $product->unit_price }}
                                                    </span>
                                                    <button class="btn btn--primary rounded-pill text-uppercase py-1 fs-12"
                                                        type="button" onclick="addPoojaProduct(this)"
                                                        data-productid="{{ $product->id }}"
                                                        data-name=" {{ $product['name'] }}"
                                                        data-price="{{ $product->unit_price }}"
                                                        data-image="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}"
                                                        data-qtymin="{{ $product->minimum_order_qty }}"
                                                        data-event="{{ $product['quantity'] == $product->minimum_order_qty ? 'delete' : 'minus' }}"
                                                        data-poojaprice="{{ $leadsGet['package_price'] }}"
                                                        data-leadid="{{ $leadsGet['leadId'] }}"
                                                        data-serviceid="{{ $leadsGet['service_id'] }}"> <i
                                                            class="tio-add"></i>{{ translate('add') }} </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
    {{-- Vishesh Prashadam --}}
    <section class="new-arrival-section">
        <div class="container rtl mt-4">
            @if ($prashadamList->count() > 0)
                <div class="section-header">
                    <div class="arrival-title d-block">
                        <div class="text-capitalize">
                            {{ translate('Special_Prasadam_for_the_temple') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="container rtl mb-3 overflow-hidden">
            <div class="py-2">
                <div class="new_arrival_product">
                    <div class="carousel-wrap">
                        <div class="owl-carousel owl-theme new-arrivals-product">
                            @foreach ($prashadamList as $key => $prashad)
                                @include('web-views.partials._prashadam', ['prashad' => $prashad])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Vishesh Prashadam --}}
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/custom.js') }}"></script>
    <!-- jQuery (Required for Toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script type="text/javascript">
        // Total Payment
        function addPoojaProduct(that) {
            var productid = $(that).data('productid');
            var name = $(that).data('name');
            var price = $(that).data('price');
            var image = $(that).data('image');
            var qtyMin = $(that).data('qtymin');
            var leadid = $(that).data('leadid');
            var serviceid = $(that).data('serviceid');
            var poojaprice = $(that).data('poojaprice');
            $.ajax({
                url: '{{ route('poojaproduct', $leadsGet->id) }}',
                method: 'POST',
                data: {
                    productid: productid,
                    name: name,
                    price: price,
                    image: image,
                    qtyMin: qtyMin,
                    lead: leadid,
                    serviceid: serviceid,
                    poojaprice: poojaprice,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $("#productList").load(location.href + " #productList", function() {
                        $(this).addClass("mt-2 pt-lg-2 mb-1 card");
                    });
                    // Check if there are no products

                    var ProductCountList = `<div class="d-flex justify-content-between">
                                        <span class="cart_title">${name}</span>
                                        <span class="cart_value totalProduct${productid}">&#8377; ${price}.00</span>
                                      </div>`;
                    $('#productCount').find('div.finalProduct').append(ProductCountList);
                    $('#get-view-by-onclick' + productid).remove();
                    // var oldprice = parseInt($('#mainProductPrice').text());
                    // var newprice = parseInt(price) + oldprice;
                    // $('#mainProductPrice').text(newprice);
                    // $('#mainProductPriceInput').val(newprice);
                    setTimeout(() => {
                        $("#productList").load(location.href + " #productList");
                        $("#price-load").load(location.href + " #price-load");
                        $("#cart-summary").load(location.href + " #cart-summary");
                    }, 500);


                },
                error: function(xhr, status, error) {}
            });

        }
        // localStorage.clear(); // Assuming your data is stored under the key "products"
        // console.log("Item 'products' removed from localStorage!");
        // function updateTotalPayment() {
        //     totalPayment = 0;
        //     products.forEach(function(product) {
        //         totalPayment += parseFloat(product.price);
        //     });
        //     $('#ProductPrice').text('Total Price: ' + totalPayment.toFixed(2));
        // }

        // function updateProductPrice() {
        //     $('#ProductPrice').val(totalPayment.toFixed(2));
        // }
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>

    <script>
        function QuantityUpdate(cartId, quantity, updateid, pprice, leadid, poojaprice) {
            var inputBox = $('#cart_quantity_web' + cartId);
            console.log(inputBox.val());

            if (quantity == -1) {
                if (inputBox.val() == 1) {
                    deleteQuantity(updateid, cartId, pprice);
                }
                if (inputBox.val() == 2) {
                    $('#DeleteIcon' + cartId).addClass('tio-delete text-danger');
                    $('#DeleteIcon' + cartId).removeClass('tio-remove');
                    $('#get-view-by-onclick' + cartId).append();
                    toastr.error('Quantity not Applicable.');
                    var newQuantity = parseInt(inputBox.val()) + quantity;
                    inputBox.val(newQuantity);
                    ProductQuantity(cartId, quantity, updateid, pprice, leadid, poojaprice, newQuantity);
                } else {
                    var newQuantity = parseInt(inputBox.val()) + quantity;
                    inputBox.val(newQuantity);
                    ProductQuantity(cartId, quantity, updateid, pprice, leadid, poojaprice, newQuantity);
                }
            } else {
                $('#DeleteIcon' + cartId).removeClass('tio-delete text-danger');
                $('#DeleteIcon' + cartId).addClass('tio-remove');
                var newQuantity = parseInt(inputBox.val()) + quantity;
                inputBox.val(newQuantity);
                ProductQuantity(cartId, quantity, updateid, pprice, leadid, poojaprice, newQuantity);
            }

            //
        }

        function ProductQuantity(cartId, quantity, updateid, pprice, leadid, poojaprice, newQuantity) {
            $.ajax({
                url: '{{ route('updateCartQuantity') }}',
                method: 'POST',
                data: {
                    updateid: updateid,
                    price: pprice,
                    cartId: cartId,
                    quantity: newQuantity,
                    leadid: leadid,
                    poojaprice: poojaprice,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('.totalProduct' + cartId).text(response.data.final_price.final_price + '.00');
                    $('#productCountFinal' + cartId).val(response.data.final_price);
                    $('#mainProductPriceInput').val(parseInt(poojaprice) + parseInt(response.data
                        .total_amount));
                    var localPrice = parseInt(response.data.total_amount) + parseInt(poojaprice) + '.00';
                    $('#mainProductPrice').text(localPrice) + '.00';
                    $('#mainProductPriceInput').text(localPrice) + '.00';
                    setTimeout(() => {
                        $("#productList").load(location.href + " #productList");
                        $("#productPrice").load(location.href + " #productPrice");
                        $("#price-load").load(location.href + " #price-load");
                        $("#cart-summary").load(location.href + " #cart-summary");
                    }, 500);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
        // Delete Quantity
        function deleteQuantity(updateid, cartId, pprice) {
            $.ajax({
                url: '{{ route('deleteQuantity') }}',
                method: 'POST',
                data: {
                    pprice: pprice,
                    updateid: updateid,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000); // 1-second delay before reloading
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Debugging: Check error message
                    toastr.error("Something went wrong!");
                }
            });
        }
    </script>
    {{-- coupon list --}}
    <script>
        function couponList() {
            let expireDate = "";
            let formattedDate = "";
            let body = "";
            $.ajax({
                type: "get",
                url: "{{ route('pooja-coupons') }}",
                success: function(response) {
                    $('#modal-body').html('');
                    if(response.status==200){
                        if(response.coupons.length > 0){
                            $.each(response.coupons, function (key, value) { 
                                expireDate = new Date(value.expire_date);
                                formattedDate = expireDate.toLocaleString('en-GB', {
                                    day: 'numeric',
                                    month: 'short',
                                    year: 'numeric'
                                }).replace(" ", ", ");
    
                                body += `<div class="col-lg-6">
                                            <div class="ticket-box">
                                            <div class="ticket-start">
                                                <img width="30"
                                                    src="{{asset('public/assets/front-end/img/icons/dollar.png')}}" alt="">
    
                                                <h2 class="ticket-amount">${value.discount_type === 'percentage' ? value.discount + '%' : '₹' + value.discount}</h2>
                                               <p>${value.title}</p>
                                            </div>
                                            <div class="ticket-border"></div>
                                            <div class="ticket-end">
                                                <button class="ticket-welcome-btn couponid click-to-copy-coupon couponid-${value.code}"
                                                    data-value="${value.code}" onclick="copyToClipboard(this)">${value.code}</button>
                                                <button
                                                    class="ticket-welcome-btn couponid-hide d-none couponhideid-${value.code}">Copied</button>
                                                <h6>Valid till ${formattedDate}</h6>
                                                <p class="m-0">Available from minimum purchase ₹${value.min_purchase}</p>
                                            </div>
                                            </div>
                                        </div>`;
                            });
                        } else{
                            body = 'Coupons not available';
                            $('#modal-body').css({'display': 'flex','justify-content': 'center','padding': '50px 0px','color': 'red'});
                        }
                        $('#modal-body').append(body);
                        $('#coupon-modal').modal('show');
                    }
                    else{
                        toaster.error('Coupon not available');
                    }
                }
            });
        }
    </script>

<script>
    function copyToClipboard(button) {
        const value = button.getAttribute("data-value");
        navigator.clipboard.writeText(value)
            .then(() => {
                toastr.success("Copied to clipboard");
            })
            .catch(err => {
                toast.error("Failed to copy");
            });
    }
</script>
@endpush
