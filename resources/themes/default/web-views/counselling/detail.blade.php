@extends('layouts.front-end.app')
@section('title', $counsellingDetails['meta_title'])
@push('css_or_js')
<meta name="description" content="{{ $counsellingDetails->meta_description }}">
<meta name="keywords"
  content="@foreach (explode(' ', $counsellingDetails['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
@if ($counsellingDetails['meta_image'] != null)
<meta property="og:image"
  content="{{ dynamicStorage(path: 'storage/app/public/product/meta') }}/{{ $counsellingDetails->meta_image }}" />
<meta property="twitter:card"
  content="{{ dynamicStorage(path: 'storage/app/public/product/meta') }}/{{ $counsellingDetails->meta_image }}" />
@else
<meta property="og:image"
  content="{{ dynamicStorage(path: 'storage/app/public/product/thumbnail') }}/{{ $counsellingDetails->thumbnail }}" />
<meta property="twitter:card"
  content="{{ dynamicStorage(path: 'storage/app/public/product/thumbnail/') }}/{{ $counsellingDetails->thumbnail }}" />
@endif
@if ($counsellingDetails['meta_title'] != null)
<meta property="og:title" content="{{ $counsellingDetails->meta_title }}" />
<meta property="twitter:title" content="{{ $counsellingDetails->meta_title }}" />
@else
<meta property="og:title" content="{{ $counsellingDetails->name }}" />
<meta property="twitter:title" content="{{ $counsellingDetails->name }}" />
@endif
<meta property="og:url" content="{{ route('product', [$counsellingDetails->slug]) }}">
@if ($counsellingDetails['meta_description'] != null)
<meta property="twitter:description" content="{!! Str::limit($counsellingDetails['meta_description'], 55) !!}">
<meta property="og:description" content="{!! Str::limit($counsellingDetails['meta_description'], 55) !!}">
@else
<meta property="og:description"
  content="@foreach (explode(' ', $counsellingDetails['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
<meta property="twitter:description"
  content="@foreach (explode(' ', $counsellingDetails['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
@endif
<meta property="twitter:url" content="{{ route('product', [$counsellingDetails->slug]) }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/poojadetails.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/product-details.css') }}" />
<link rel="stylesheet"
  href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<script type="text/javascript">
  function preventBack() {
      window.history.forward();
  }
  setTimeout("preventBack()", 0);
  window.onunload = function() {
      null
  };
</script>
<style>
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
  @media only screen and (max-width: 600px) {
  a.section-link {
  padding: 5px 8px;
  font-size: 10px;
  }
  .w-70 {
  width: 100% !important;
  }
  .font-10 {
  font-size: 14px;
  font-weight: 700;
  }
  .circle-img-container {
  width: 23px !important;
  }
  }
  .w-70 {
  width: 70%;
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
  .product-preview-item {
  height: 60% !important;
  }
  .button-sticky {
  border-radius: 5px 5px 0 0;
  border: 1px solid rgba(20, 85, 172, 0.05);
  box-shadow: 0 -7px 30px 0 rgba(0, 113, 220, 0.1);
  position: sticky;
  bottom: 0;
  left: 0;
  z-index: 1000;
  transition: all 150ms ease-in-out;
  }
  @media (max-width: 768px) {
  .otp-input-fields input {
  height: 40px;
  width: 40px;
  }
  .otp-input-fields{
  gap:9px;
  }
  }
  /* .review-content {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 14px;
  height: 154px;
  text-align: center;
  } */
  .owl-dots {
  top: 25px;
  position: relative !important;
  }
  .partial-pooja {
  background: white;
  box-shadow: 0px 3px 6px rgb(0 0 0 / 29%);
  border-radius: 5px;
  border-top: 2px solid #fe9802;
  }
</style>
@endpush
@section('content')
<!-- Couupon Modal -->
<div class="modal fade" id="coupon-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
  aria-hidden="true">
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
<div class="d-none d-md-block w-full h-full sticky md:top-[68px] top-0 z-20">
  <div class="bg-bar w-full">
    <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
      id="breadcrum-container-outer">
      <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
        @include('web-views.counselling.partials.statusbar')
      </div>
    </div>
  </div>
</div>
<div class="__inline-23">
<div class="container mt-4 rtl text-align-direction">
  <div class="row {{ Session::get('direction') === 'rtl' ? '__dir-rtl' : '' }}">
    <div class="col-12">
      <div class="row">
        <div class="col-lg-6 col-md-4 col-12">
          <div class="cz-product-gallery">
            <div class="cz-preview">
              <div id="sync1" class="owl-carousel owl-theme product-thumbnail-slider">
                @if ($counsellingDetails['images'] && json_decode($counsellingDetails['images']))
                @foreach (json_decode($counsellingDetails['images']) as $pphoto)
                <div class="d-flex align-items-center justify-content-center active">
                  <img src="{{ getValidImage(path: 'storage/app/public/pooja/' . $pphoto, type: 'product') }}"
                    alt="">
                </div>
                @endforeach
                @endif
              </div>
            </div>
            <div class="d-flex flex-column gap-3">
              <button type="button" data-product-id="{{ $counsellingDetails['id'] }}"
                class="btn __text-18px border wishList-pos-btn d-sm-none product-action-add-wishlist">
              <i class="fa fa-heart wishlist_icon_{{ $counsellingDetails['id'] }} web-text-primary"
                aria-hidden="true"></i>
              </button>
              <div class="sharethis-inline-share-buttons share--icons text-align-direction">
              </div>
            </div>
            <div class="cz">
              <div class="table-responsive __max-h-515px" data-simplebar>
                <div class="d-flex">
                  <div id="sync2" class="owl-carousel owl-theme product-thumb-slider">
                    @if ($counsellingDetails['images'] && json_decode($counsellingDetails['images']))
                    @foreach (json_decode($counsellingDetails['images']) as $key => $pphoto)
                    <div class="">
                      <a class="product-preview-thumb color-variants-preview-box-{{ $pphoto }} {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                        id="preview-img{{ $pphoto }}"
                        href="#image{{ $pphoto }}">
                      <img alt="{{ translate('product') }}"
                        src="{{ getValidImage(path: 'storage/app/public/pooja/' . $pphoto, type: 'product') }}">
                      </a>
                    </div>
                    @endforeach
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-8 col-12 mt-md-0 mt-sm-3 web-direction">
          <div class="details __h-100">
            <h5 class="card-title font-weight-700">{{ $counsellingDetails['name'] }}</h5>
            <!-- Profile Icon -->
            <div class="flex flex-col">
              <div class="flex flex-wrap justify-center items-center">
                <div class="w-70 d-flex justify-content-between">
                  <div class="tray mb-3 ml-3 mr-0">
                    @php
                    $uniqueUsers = range(0, 13);
                    shuffle($uniqueUsers);
                    $selectedUsers = array_slice($uniqueUsers, 0, 6);
                    @endphp
                    @foreach ($selectedUsers as $random_user)
                    <div class="relative circle-img-container">
                      <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                        style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $random_user . '.jpg') }}')">
                      </div>
                    </div>
                    @endforeach
                  </div>
                  <!-- Ratings Display -->
                  @foreach ($counsellingData as $service)
                  @php
                  $avgRating = (!empty($service->review_avg_rating) && $service->review_avg_rating > 0) ? $service->review_avg_rating : 5.5;
                  $fullStars = floor($avgRating);
                  $halfStar = $avgRating - $fullStars >= 0.5 ? 1 : 0;
                  @endphp
                  <div class="font-10">
                    <p
                      class="text-sm mt-4 font-medium border-b border-dashed border-primary font-weight-bold">
                      <i class="fas fa-star text-primary"></i>
                      {{ (!empty($reviewCount) && $reviewCount > 0)  ? number_format($reviewSum / $reviewCount, 1)    : '0.0' }}/5

                    </p>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
            <!-- Count number of People -->
            <div class="mt-[1px] mb-3 md:mb-0 flex flex-col">
              <div class="flex flex-row mt-2 flex-nowrap break-normal leading-normal">
                <div class="flex">
                  <div class=""><span
                    class=" inline-flex break-normal">{{ translate('over') }}</span><span
                    class=" font-bold text-#F18912 ml-1 break-normal">{{10000+$reviewCount}} +<span
                    class=" ml-1 mr-1 inline-flex break-normal">{{ translate('customers') }}</span></span><span
                    class="text-">{{ translate('satisfied_customers_have_successfully_received_their_consultancy_reports_through_Mahakal.com,_gaining_valuable_insights_and_guidance.') }}</span>
                  </div>
                </div>
              </div>
            </div>
            <!-- Button -->
            <div id="" role="button">
              @if (auth('customer')->check())
              <aside class="col-lg-12 pt-2 pt-lg-2 px-max-md-0 order-summery-aside">
                <div class="__cart-total __cart-total_sticky">
                  <div class="cart_total p-0">
                    @php
                    $coupon_dis = 0;
                    @endphp
                    @if (session()->has('coupon_discount_counselling'))
                    @php
                    $couponDiscount = session()->has(
                    'coupon_discount_counselling',
                    )
                    ? session('coupon_discount_counselling')
                    : 0;
                    @endphp
                    <div class="pt-2">
                      <div
                        class="d-flex align-items-center form-control rounded-pill pl-3 p-1">
                        <img width="24"
                          src="{{ asset('public/assets/front-end/img/icons/coupon.svg') }}"
                          alt="">
                        <div class="px-2 d-flex justify-content-between w-100">
                          <div>
                            {{ session('coupon_code_counselling') }}
                            <span class="text-primary small">(
                            -
                            {{ webCurrencyConverter(amount: $couponDiscount) }}
                            )</span>
                          </div>
                          <div class="bg-transparent text-danger cursor-pointer px-2 get-view-by-onclick-remove"
                            data-link="{{ route('coupon.poojaremovecoupon', ['code' => session('coupon_code_counselling')]) }}">
                            x
                          </div>
                        </div>
                      </div>
                    </div>
                    @else
                    {{-- 
                    <div class="row">
                      --}}
                      <div class="pt-2">
                        <form class="needs-validation" action="javascript:"
                          method="post" novalidate id="coupon-code-pooja-ajax">
                          <div class="d-flex form-control rounded-pill ps-3 p-1">
                            <img width="24"
                              src="{{ theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg') }}"
                              alt="" onclick="couponList()">
                            <input type="hidden" name="service_id"
                              value="{{ $counsellingDetails['id'] }}">
                            <input
                              class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100"
                              type="text" name="code"
                              placeholder="{{ translate('click_here_to_view') }}"
                              required>
                            <button
                              class="btn btn--primary rounded-pill text-uppercase py-1 fs-12"
                              type="button" id="pooja-coupon-code">
                            {{ translate('apply') }}
                            </button>
                          </div>
                          <div class="invalid-feedback">
                            {{ translate('please_provide_coupon_code') }}
                          </div>
                        </form>
                      </div>
                      @php($coupon_dis = 0)@endphp
                      @endif
                      <hr class="my-2">
                      <div class="d-flex justify-content-between">
                        <span class="cart_title">{{ $counsellingDetails['name'] }}</span>
                        <span
                          class="cart_value">{{ webCurrencyConverter(amount: $counsellingDetails['counselling_selling_price']) }}
                        </span>
                      </div>
                      @if (session()->has('coupon_discount_counselling'))
                      <div class="d-flex justify-content-between">
                        <span
                          class="cart_title">{{ translate('coupon_discount') }}</span>
                        <span class="cart_value"> -
                        {{ webCurrencyConverter(amount: $couponDiscount) }} </span>
                      </div>
                      @endif
                      @php
                      if (auth('customer')->check()) {
                      $customer = App\Models\User::where(
                      'id',
                      auth('customer')->id(),
                      )->first();
                      }
                      $couponDiscount = session()->has('coupon_discount_counselling')
                      ? session('coupon_discount_counselling')
                      : 0;
                      $productTotalAmount =
                      $counsellingDetails['counselling_selling_price'] -
                      $couponDiscount;
                      $totalAmount = $productTotalAmount - $customer->wallet_balance;
                      @endphp
                      @if ($customer->wallet_balance > 0)
                      <div id="productCounts">
                        <div class="finalProducts">
                          <div class="d-flex">
                            <span
                              class="cart_title">{{ translate('wallet_balance') }}
                            </span>
                            <span class="cart_value text-success">
                            ({{ webCurrencyConverter(amount: $customer->wallet_balance) }})</span>
                          </div>
                          <div class="d-flex justify-content-between">
                            <span
                              class="cart_title">{{ translate('Amount_Paid_(via_Wallet)') }}</span>
                            @if ($customer->wallet_balance < $productTotalAmount)
                            <span class="cart_value text-danger"> -
                            {{ webCurrencyConverter(amount: $customer->wallet_balance) }}</span>
                            @else
                            <span class="cart_value text-danger"> -
                            {{ webCurrencyConverter(amount: $productTotalAmount) }}</span>
                            @endif
                          </div>
                          @if ($customer->wallet_balance < $productTotalAmount)
                          <div class="d-flex justify-content-between">
                            <span
                              class="cart_title">{{ translate('Remaining_Amount_to_Pay') }}</span>
                            <span
                              class="cart_value text-danger">{{ webCurrencyConverter($totalAmount) }}</span>
                          </div>
                          @endif
                        </div>
                      </div>
                      @endif
                      <span id="route-coupon-pooja"
                        data-url="{{ route('coupon.couponapply') }}"></span>
                      <hr class="my-2">
                      @if (session()->has('coupon_discount_counselling'))
                      <div class="d-flex justify-content-between">
                        <span
                          class="cart_title text-primary font-weight-bold">{{ translate('total') }}</span>
                        <span class="cart_value" id="mainProductPrice">
                        @php
                        $couponTotalAmount =
                        $counsellingDetails[
                        'counselling_selling_price'
                        ] -
                        $couponDiscount -
                        $customer->wallet_balance;
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
                        <span
                          class="cart_title text-primary font-weight-bold">{{ translate('Total') }}</span>
                        <span class="cart_value" id="mainProductPrice">
                        @if ($customer->wallet_balance < $productTotalAmount)
                        {{ webCurrencyConverter($counsellingDetails['counselling_selling_price'] - $customer->wallet_balance) }}
                        @else
                        {{ webCurrencyConverter(0.0) }}
                        @endif
                        </span>
                      </div>
                      @endif
                    </div>
                    <hr class="my-2">
                    @if ($digital_payment['status'] == 1)
                    @foreach ($payment_gateways_list as $payment_gateway)
                    <form method="post" class="digital_payment paynow"
                      id="{{ $payment_gateway->key_name }}_form"
                      action="{{ route('customer.counselling-payment-request') }}">
                      @csrf
                      <div class="Details">
                        {{-- <input type="hidden" name="user_id"
                          value="{{ auth('customer')->check() ? auth('customer')->user()->id : $user['id'] }}">
                        <input type="hidden" name="customer_id"
                          value="{{ auth('customer')->check() ? auth('customer')->user()->id : $user['id'] }}"> --}}
                        <input type="hidden" name="payment_method"
                          value="{{ $payment_gateway->key_name }}">
                        <input type="hidden" name="payment_platform"
                          value="web">
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
                        <label
                          class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                        <input type="radio"
                          id="{{ $payment_gateway->key_name }}"
                          name="online_payment"
                          class="form-check-input custom-radio"
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
                        {{-- <input type="hidden" name="wallet_balance"
                          value="{{ $customer->wallet_balance < $productTotalAmount ? $customer->wallet_balance : $productTotalAmount }}">
                        <input type="hidden" name="final_amount" id="Amount"
                          value="{{ $counsellingDetails['counselling_selling_price'] }}"> --}}
                        <input type="hidden" name="service_id"
                          value="{{ $counsellingDetails['id'] }}">
                        @if (session()->has('coupon_discount_counselling'))
                        @php
                        $couponTotalAmount =
                        $counsellingDetails[
                        'counselling_selling_price'
                        ] -
                        $couponDiscount -
                        $customer->wallet_balance;
                        @endphp
                        @if ($customer->wallet_balance < $productTotalAmount)
                        {{-- <input type="hidden" name="payment_amount"
                          id="mainProductPriceInput"
                          value="{{ $couponTotalAmount }}"> --}}
                        @else
                        {{-- <input type="hidden" name="payment_amount"
                          id="mainProductPriceInput"
                          value="{{ 0.0 }}"> --}}
                        @endif
                        @else
                        @if ($customer->wallet_balance < $productTotalAmount)
                        {{-- <input type="hidden" name="payment_amount"
                          value="{{ $counsellingDetails['counselling_selling_price'] - $customer->wallet_balance }}"> --}}
                        @else
                        {{-- <input type="hidden" name="payment_amount"
                          value="{{ 0.0 }}"> --}}
                        @endif
                        @endif
                        {{-- <input type="hidden" name="person_name"
                          value="{{ auth('customer')->user()->f_name }}">
                        <input type="hidden" name="person_phone"
                          value="{{ auth('customer')->user()->phone }}"> --}}
                      </div>
                      <div class="mt-4 d-none d-sm-block">
                        <button type="submit"
                          class="btn btn--primary btn-block">{{ translate('Proceed to Checkout') }}</button>
                      </div>
                    </form>
                    @endforeach
                    @endif
                  </div>
              </aside>
              @else
              <a href="javascript:void(0);" id="participate-btn"
                class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold d-none d-sm-inline-block">{{ translate('book_now') }}</a>
              @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid" style="padding-left: 0 !important;padding-right:0 !important;">
    <div class="row mt-2">
      <div class="col-12">
        <div class="navbar_section1 section-links w-100 mt-3 border-top border-bottom py-2 mb-4"
          style="overflow: auto;">
          <div class="d-flex justify-content-around">
            <a class="section-link ml-2 active" href="#about_pooja"> {{ translate('about') }}</a>
            <a class="section-link" href="#process">{{ translate('process') }}</a>
            <a class="section-link" href="#reviews">{{ translate('review') }}</a>
            <a class="section-link" href="#faqs">{{ translate('faqs') }}</a>
          </div>
        </div>
        <div class="px-4 pb-3 mb-3 __rounded-10 pt-3">
          <div class="content-sections px-lg-3">
            <!-- Inclusion Section -->
            <div class="section-content active" id="about_pooja" style="padding-bottom: 25px;">
              <div class="row mt-2 p-2 partial-pooja">
                <!-- About Me -->
                <div class="ck-rendered-content">
                  @include('web-views.counselling.partials.about')
                </div>
              </div>
            </div>
            <div class="section-content" id="process" style="padding-bottom: 25px;">
              <div class="row mt-2 p-2 partial-pooja">
                <!-- Process -->
                <div class="ck-rendered-content">
                  @include('web-views.counselling.partials.process')
                </div>
              </div>
            </div>
            <div class="section-content reviewsTab" id="reviews" style="padding-bottom: 25px;">
              <div class="row mt-2 p-2 partial-pooja pb-4">
                <!-- Review -->
                <div class="col-12">
                  @include('web-views.counselling.partials.review')
                </div>
              </div>
            </div>
            <div class="section-content" id="faqs" style="padding-bottom: 25px;">
              <div class="row mt-2 p-2 partial-pooja">
                <div class="col-12">
                  @if ($faqs)
                  @foreach ($faqs as $faq)
                  <div class="row pt-2 specification">
                    <div class="col-12 col-md-12 col-lg-12">
                      <div class="accordion" id="accordionExample">
                        <div class="cards">
                          <div class="card-header" id="heading{{ $faq->id }}">
                            <h2 class="mb-0">
                              <button
                                class="btn btn-link btn-block  text-left btnClr"
                                type="button" data-toggle="collapse"
                                data-target="#collapse{{ $faq->id }}"
                                aria-expanded="true"
                                aria-controls="collapseOne"
                                style="white-space: normal;">
                              {{ $faq->question }}
                              </button>
                            </h2>
                          </div>
                          <div id="collapse{{ $faq->id }}" class="collapse"
                            aria-labelledby="heading{{ $faq->id }}"
                            data-parent="#accordionExample">
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
  <div class="modal fade rtl text-align-direction" id="participateModal" tabindex="-1" role="dialog"
    aria-labelledby="participateModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div class="flex justify-center items-center my-3">
            <span
              class="text-18 font-bold ml-2">{{ translate('fill_your_details_for_counselling') }}</span>
          </div>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <hr class="bg-[#E6E4EB] w-full">
        <div class="modal-body flex justify-content-center">
          <div id="recaptcha-container"></div>
          <div class="w-full mt-1 px-2">
            <span
              class="block text-[16px] font-bold text-gray-900 dark:text-white">{{ translate('enter_Your_Whatsapp_Mobile_Number') }}</span>
            <span
              class="text-[12px] font-normal text-[#707070]">{{ translate('Your_Counselling_booking_updates_will_be_sent_on_below_WhatsApp_number') }}</span>
            <!-- Model Form -->
            <div class="w-full mr-9 px-0 pt-3">
              <form class="needs-validation_" id="customer-store-form"
                action="{{ route('counselling.store.customer') }}" method="post">
                @csrf
                @php
                if (auth('customer')->check()) {
                $customer = App\Models\User::where(
                'id',
                auth('customer')->id(),
                )->first();
                }
                @endphp
                <div class="row">
                  <div class="col-md-12" id="phone-div">
                    <div class="form-group">
                      <label
                        class="form-label font-semibold">{{ translate('phone_number') }}
                      <small class="text-primary">( *
                      {{ translate('country_code_is_must_like_for_IND') }} 91
                      )</small>
                      </label>
                      <input
                        class="form-control text-align-direction phone-input-with-country-picker"
                        type="tel" name="person_phone" id="person-number"  inputmode="number"
                        required maxlength="10" minlength="10"
                        placeholder="{{ translate('enter_phone_number') }}" required>
                      <input type="hidden" class="country-picker-phone-number w-50"
                        name="person_phone" readonly>
                      <input type="hidden" name="verify_otp" id="verifyOTP" value="0">
                      <p id="number-validation" class="text-danger" style="display: none">
                        {{ translate('Enter_Your_Valid_Mobile_Number') }}
                      </p>
                    </div>
                  </div>
                  <div class="col-md-12" id="name-div">
                    <div class="form-group">
                      <label
                        class="form-label font-semibold">{{ translate('your_name') }}</label>
                      <input class="form-control text-align-direction" type="text"
                        name="person_name" id="person-name"
                        placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                        required>
                      <p id="name-validation" class="text-danger" style="display: none">
                        {{ translate('Enter_Your_Name') }}
                      </p>
                    </div>
                  </div>
                  <div class="col-md-12" id="otp-input-div" style="display: none;">
                    <div class="form-group text-center">
                      <label
                        class="form-label font-semibold ">{{ translate('enter_OTP') }}</label>
                      <div class="otp-input-fields">
                        <input type="number" id="otp1"
                          class="otp__digit otp__field__1" inputmode="number">
                        <input type="number" id="otp2"
                          class="otp__digit otp__field__2" inputmode="number">
                        <input type="number" id="otp3"
                          class="otp__digit otp__field__3" inputmode="number">
                        <input type="number" id="otp4"
                          class="otp__digit otp__field__4" inputmode="number">
                        <input type="number" id="otp5"
                          class="otp__digit otp__field__5" inputmode="number">
                        <input type="number" id="otp6"
                          class="otp__digit otp__field__6" inputmode="number">
                      </div>
                      <p id="otpValidation" class="text-danger"></p>
                    </div>
                  </div>
                  <div class="mx-auto mt-1 __max-w-356" id="send-otp-btn-div">
                    <button type="button" class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold" id="send-otp-btn"> {{ translate('send_OTP') }}</button>
                    <button type="button" class="d-none btn btn--primary btn-block btn-shadow mt-1 font-weight-bold" id="withoutOTP"> {{ translate('book_now') }}
                    </button>
                    {{-- 
                    <p id="failedOtpValidation" class="text-danger mt-2"></p>
                    --}}
                  </div>
                  <div class="mx-auto mt-1 __max-w-356" id="verify-otp-btn-div"
                    style="display: none">
                    <div class="d-flex">
                      <button type="button"
                        class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold me-2"
                        id="otp-back-btn">
                      {{ translate('back') }} </button>
                      <button type="submit"
                        class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                        id="verify-otp-btn">
                      {{ translate('verify_OTP') }} </button>
                    </div>
                  </div>
                </div>
                <div class="text-center mt-3" id="resend-div" style="display: none;">
                  <p id="resend-otp-timer-text" style="display: none">
                    {{ translate('Resend_OTP_in') }} <span id="resend-otp-timer"></span>
                  </p>
                  <p id="resend-otp-btn-text" style="display: none">
                    {{ translate('Did_not_get_the_code') }}? <a href="javascript:0"
                      id="resend-otp-btn"
                      style="color: blue;">{{ translate('Resend_Otp') }}</a>
                  </p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="button-sticky bg-white d-sm-none">
  <div class="d-flex flex-column gap-1 py-2">
    <div class="d-flex gap-3 justify-content-center" role="button">
      @if (auth('customer')->check())
      <button type="button"
        class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"
        onclick="submitCheckoutForm(this)">{{ translate('Proceed to Checkout') }}</button>
      @else
      <button type="button"
        class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center" onclick="responsiveLoginModal()">{{ translate('Proceed to Checkout') }}</button>
      @endif
    </div>
  </div>
</div>
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<!-- Firbase CDN -->
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>
<!-- Otp Send -->
<script>
  const firebaseConfig = {
      apiKey: "{{ env('FIREBASE_APIKEY') }}",
      authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
      projectId: "{{ env('FIREBASE_PRODJECTID') }}",
      storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
      messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
      appId: "{{ env('FIREBASE_APPID') }}",
      measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
  };
  firebase.initializeApp(firebaseConfig);
</script>
<script>
  $('#participate-btn').click(function(e) {
      e.preventDefault();
      console.log('click');
      $('#participateModal').modal('show');
  });
  
  // OTP SEND THE MODEL
  var confirmationResult;
  var appVerifier = "";
  var sendOtpCount = 1;
  $('#send-otp-btn').click(function(e) {
      e.preventDefault();
      var name = $('#person-name').val();
      var number = $('#person-number').val();
  
      var phoneNumber = '+91 ' + $('#person-number').val();
      sendotp();
  });
  
  
  function sendotp() {
  
      var name = $('#person-name').val();
      var number = $('#person-number').val();
      if (number == "" || number.length != 10) {
          $('#number-validation').show();
      } else if (name == "") {
          $('#number-validation').hide();
          $('#name-validation').show();
      } else {
          toastr.success('please wait...');
          $('#send-otp-btn').text('Please Wait ...');
          $('#send-otp-btn').prop('disabled', true);
          var phoneNumber = '+91 ' + $('#person-number').val();
          if (appVerifier == "") {
              appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                  size: 'invisible'
              });
          }
  
          firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                  $('#name-validation').hide();
                  $('#number-validation').hide();
                  $('#send-otp-btn-div').css('display', 'none');
                  $('#phone-div').css('display', 'none');
                  $('#name-div').css('display', 'none');
                  $('#otp-input-div').css('display', 'block');
                  $('#verify-otp-btn-div').css('display', 'block');
                  if (sendOtpCount == 1) {
                      sendOtpCount = 2;
                      otpTimer();
                  }
                  confirmationResult = confirmation;
                  toastr.success('otp sent successfully');
                  $('#resend-div').show();
              })
              .catch(function(error) {
                  toastr.error('Failed to send OTP. Please try again');
                  $('#send-otp-btn').text('Send OTP');
                  $('#send-otp-btn').prop('disabled', false);
                  console.error('OTP sending error:', error);
              });
      }
  }
  
  // otp timer
  function otpTimer() {
      $('#resend-otp-timer-text').css('display', 'block');
      $('#resend-otp-btn-text').css('display', 'none');
      var resendOtpTimer = 30;
      var interval = setInterval(() => {
          resendOtpTimer--;
          $('#resend-otp-timer').text(resendOtpTimer);
          if (resendOtpTimer <= 0) {
              $('#resend-otp-timer-text').css('display', 'none');
              $('#resend-otp-btn-text').css('display', 'block');
              clearInterval(interval);
          }
      }, 1000);
  }
  
  // resend otp
  $('#resend-otp-btn').click(function(e) {
      e.preventDefault();
  
      var phoneNumber = '+91 ' + $('#person-number').val();
      if (!appVerifier) {
          appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
              size: 'invisible'
          });
      }
      firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
              confirmationResult = confirmation;
              otpTimer();
              toastr.success('OTP resent successfully');
          })
          .catch(function(error) {
              toastr.error('Failed to send OTP. Please try again');
          });
  });
  
  $('#verify-otp-btn').click(function(e) {
      e.preventDefault();
      toastr.success('please wait...');
      var name = $('#person-name').val();
      var number = $('#person-number').val();
      var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
          $('#otp6').val();
      if (confirmationResult) {
          
          confirmationResult.confirm(otp).then(function(result) {
                  $(this).text('Please Wait ...');
                  $(this).prop('disabled', true);
                  $('#participateModal').modal('hide');
                  $('#customer-store-form').submit();
              })
              .catch(function(error) {
                  $('#otpValidation').text('Incorrect OTP');
              });
      }
  
  
  });
  
  $('#otp-back-btn').click(function(e) {
      e.preventDefault();
  
      $('#send-otp-btn-div').css('display', 'block');
      $('#phone-div').css('display', 'block');
      $('#name-div').css('display', 'block');
      $('#otp-input-div').css('display', 'none');
      $('#verify-otp-btn-div').css('display', 'none');
      $('#send-otp-btn').prop('disabled', false);
      $('#send-otp-btn').text('Send OTP');
      $('#resend-div').hide();
  });
</script>
<!-- OTP SECTION -->
<script type="text/javascript">
  var otp_inputs = document.querySelectorAll(".otp__digit")
  var mykey = "0123456789".split("")
  otp_inputs.forEach((_) => {
      _.addEventListener("keyup", handle_next_input)
  })
  
  function handle_next_input(event) {
      let current = event.target
      let index = parseInt(current.classList[1].split("__")[2])
      current.value = event.key
  
      if (event.keyCode == 8 && index > 1) {
          current.previousElementSibling.focus()
      }
      if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
          var next = current.nextElementSibling;
          next.focus()
      }
      var _finalKey = ""
      for (let {
              value
          }
          of otp_inputs) {
          _finalKey += value
      }
  }
  
  // Without OTP LOGIN
      $('#withoutOTP').click(function(e) {
          e.preventDefault();
          $('#customer-store-form').submit();
  
  
      });
  // Without OTP LOGIN
</script>
{{-- mobile no blur --}}
<script>
  $('#person-number').blur(function(e) {
      e.preventDefault();
      var code = $('.iti__selected-dial-code').text();
      var mobile = $(this).val();
      var no = code + '' + mobile;
      console.log(code);
      console.log(mobile);
      $.ajax({
          type: "get",
          url: "{{ url('account-counselling-order-user-name') }}" + "/" + no,
          success: function(response) {
              // if (response.status == 200) {
              //     var name = response.user.f_name + ' ' + response.user.l_name;
              //     $('#person-name').val(name);
              //     $('#person-name').prop('readonly', true);
              // } else {
              //     $('#person-name').val('');
              //     $('#person-name').prop('readonly', false);
              // }
              if (response.status == 200) {
                  var name = response.user.f_name + ' ' + response.user.l_name;
                  $('#person-name').val(name);
                  $('#verifyOTP').val(1);
  
              } else {
                  $('#send-otp-btn').addClass('d-none');
                  $('#withoutOTP').removeClass('d-none');
              }
              
          }
      });
  });
</script>
{{-- auth book now btn click --}}
<script>
  $('#auth-book-now').click(function(e) {
      e.preventDefault();
      $('#lead-store-form').submit();
  });
</script>
<script>
  function read(el) {
      var parentDiv = $(el).closest('.single-review-details');
      var commentDiv = parentDiv.find('.review-comment');
      if (parentDiv.css('height') === '100px') {
          parentDiv.css('height', 'auto'); // Expand
          commentDiv.css('-webkit-line-clamp', '10');
          $(el).text('Read less...');
      } else {
          parentDiv.css('height', '100px'); // Collapse
          commentDiv.css('-webkit-line-clamp', '3');
          $(el).text('Read more...');
      }
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
          url: "{{ route('counselling-coupons') }}",
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
                                  <img width="30"
                                      src="{{ asset('public/assets/front-end/img/icons/dollar.png') }}" alt="">
  
                                  <h2 class="ticket-amount">${value.discount} ${value.discount_type == 'percentage' ? '%' : 'Rs.'}</h2>
                                  <h5>${value.title}</h5>
                                  <p>On All Services</p>
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
                  } else {
                      body = 'Coupons not available';
                      $('#modal-body').css({
                          'display': 'flex',
                          'justify-content': 'center',
                          'padding': '50px 0px',
                          'color': 'red'
                      });
                  }
                  $('#modal-body').append(body);
                  $('#coupon-modal').modal('show');
              } else {
                  toastr.error('Coupon not available');
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
              toastr.error("Failed to copy");
          });
  }
</script>
<script>
  $(document).ready(function() {
      $('.section-link').on('click', function(e) {
          e.preventDefault();
  
          const targetId = $(this).attr('href');
          $('html, body').animate({
              scrollTop: $(targetId).offset().top - $('.navbar_section1').outerHeight() - 100
          }, 200);
  
      });
  
      $(window).on('scroll', function() {
          const scrollTop = $(window).scrollTop() + $('.navbar_section1').outerHeight() + 200;
          if (scrollTop > 800) {
              $('.navbar-stuck-toggler').removeClass('show');
              $('.navbar-stuck-menu').removeClass('show');
              $(".navbar_section1").css({
                  'position': 'sticky',
                  'top': window.innerWidth <= 768 ? '0' : '83px',
                  'right': '3px',
                  'left': '3px',
                  'background-color': '#fff',
                  'z-index': '1000',
                  'box-shadow': '0 2px 10px rgba(0, 0, 0, 0.1)',
                  'overflow': 'auto',
              });
          } else {
              $(".navbar_section1").css({
                  'position': 'static',
                  'box-shadow': 'none'
              });
          }
          $('.section-content').each(function() {
              const sectionTop = $(this).offset().top;
              const sectionBottom = sectionTop + $(this).outerHeight();
              const sectionId = $(this).attr('id');
              const navLink = $(`.section-link[href="#${sectionId}"]`);
  
              if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                  $('.section-link').removeClass('active'); // Remove active from all links
                  navLink.addClass('active'); // Add active to the current section link
              }
          });
      });
  });
</script>
<script>
  function toggleComment(element) {
      const container = element.closest('.single-review-details');
      const shortComment = container.querySelector('.short-comment');
      const fullComment = container.querySelector('.full-comment');
  
      if (fullComment.classList.contains('d-none')) {
          shortComment.classList.add('d-none');
          fullComment.classList.remove('d-none');
          element.textContent = 'Read Less...';
      } else {
          shortComment.classList.remove('d-none');
          fullComment.classList.add('d-none');
          element.textContent = 'Read More...';
      }
  }
</script>
<script>
  function submitCheckoutForm(button) {
      $(button).prop('disabled', true);
      $('.paynow').submit();
  }
</script>
<script>
  function responsiveLoginModal(){
      $('#participateModal').modal('show');
  }
</script>
@endpush