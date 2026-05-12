@extends('layouts.front-end.app')
@section('title', $leadsDetails['offlinePooja']['name'])
@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/payment.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/theme.css') }}">
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <style>
        #productList {
            background-color: white;
            border-radius: 6px;
            box-shadow: 2px 2px 2px 2px #f3f3f3;
        }

        .product-preview-item {
            /* height: 60% !important; */
            aspect-ratio: unset;
        }
    </style>
    <script type="text/javascript">
        function preventBack() {
            window.history.forward();
        }
        setTimeout("preventBack()", 0);
        window.onunload = function() {
            null
        };
    </script>
@endpush
@section('content')
    <!-- Offline Pooja Couupon Modal -->
    <div class="modal fade" id="offlinepooja-coupon-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Coupons</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row g-3" id="offlinepooja-modal-body">
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
                    @include('web-views.offlinepooja.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-3 rtl px-0 px-md-3 text-align-direction" id="cart-summary">
        <!--  <h3 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">
                                                        <a href="#"><span aria-hidden="true"><i class="fa fa-arrow-left"></i></span></a>
                                                    </h3> -->
        @php
            $selected_product_array = [];

        @endphp
        <div class="row g-3 mx-max-md-0">

            <section class="col-lg-6 px-max-md-0">
                {{-- <div class="col-12"> --}}
                <div class="cz-product-gallery">
                    <div class="cz-preview">
                        <div id="sync1" class="owl-carousel owl-theme product-thumbnail-slider">
                            @if ($leadsDetails['offlinePooja']['images'] != null && json_decode($leadsDetails['offlinePooja']['images']) > 0)
                                @foreach (json_decode($leadsDetails['offlinePooja']['images']) as $key => $photo)
                                    <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                        id="image{{ $key }}">
                                        <img class="cz-image-zoom img-responsive w-100"
                                            src="{{ getValidImage(path: 'storage/app/public/offlinepooja/' . $photo, type: 'product') }}"
                                            data-zoom="{{ getValidImage(path: 'storage/app/public/offlinepooja/' . $photo, type: 'product') }}"
                                            alt="{{ translate('product') }}" width="">
                                    </div>
                                @endforeach

                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <button type="button" data-product-id="{{ $leadsDetails['offlinePooja']['id'] }}"
                            class="btn __text-18px border wishList-pos-btn d-sm-none product-action-add-wishlist">
                            <i class="fa fa-heart wishlist_icon_{{ $leadsDetails['offlinePooja']['id'] }} web-text-primary"
                                aria-hidden="true"></i>
                        </button>
                        <div class="sharethis-inline-share-buttons share--icons text-align-direction">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="col-12 cz">
                        <div class="table-responsive __max-h-515px" data-simplebar>
                            <div class="d-flex">
                                <div id="sync2" class="owl-carousel owl-theme product-thumb-slider">
                                    @if ($leadsDetails['offlinePooja']['images'] != null && json_decode($leadsDetails['offlinePooja']['images']) > 0)
                                        @foreach (json_decode($leadsDetails['offlinePooja']['images']) as $key => $photo)
                                            <div class="">
                                                <a class="product-preview-thumb {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                                                    id="preview-img{{ $key }}" href="#image{{ $key }}">
                                                    <img alt="{{ translate('product') }}"
                                                        src="{{ getValidImage(path: 'storage/app/public/offlinepooja/' . $photo, type: 'product') }}">
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- </div> --}}
            </section>
            <section class="col-lg-6 px-max-md-0">
                <div class="cards">
                    <div class="card-header" id="" style="border: 0px;">
                        <div class="details __h-100">
                            <span class="mb-2 __inline-24">{{ $leadsDetails['offlinePooja']['name'] }}</span>
                            <div class="mt-3 d-flex">
                                {{-- {{ $leadsDetails['package_name'] }} --}}
                                <p class="">{{ translate('total_Amount') }}
                                    <b>{{ webCurrencyConverter(amount: $leadsDetails['package_main_price']) }}</b>
                                </p>
                            </div>
                            <div class="d-flex">
                                <p class="">{{ translate('booking_Amount') }}
                                    <b>{{ webCurrencyConverter(amount: $leadsDetails['package_price']) }}</b>
                                </p>
                            </div>
                            {{ translate('Book_your_pooja_now_and_invite_a_pandit_to_bless_your_auspicious_occasion!') }}
                        </div>
                    </div>
                </div>

                <aside class="col-lg-12 pt-2 pt-lg-2 px-max-md-0 order-summery-aside">
                    <div class="__cart-total __cart-total_sticky">
                        <div class="cart_total p-0">

                            <div class="pt-2 d-flex row gap-1">
                                <div class="bg-success p-2 rounded align-content-center text-center"
                                    style="height: 50px; width: 49.3%">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payment_type" id="full"
                                            value="full" checked style="width: 15px; height: 15px;">
                                        <h6 class="form-check-label text-white pt-1" for="full">Full Payment</h6>
                                    </div>
                                </div>
                                <div class="bg-success p-2 rounded align-content-center text-center"
                                    style="height: 50px; width: 49.3%">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payment_type"
                                            id="partial" value="partial" style="width: 15px; height: 15px;">
                                        <h6 class="form-check-label text-white pt-1" for="partial">Partial Payment
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 d-flex justify-content-between">
                                <span class="cart_value">Package</span>

                                <span class="cart_value">Price</span>

                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="cart_title">{{ $leadsDetails['package_name'] }}</span>
                                <span id="full-pay-span"
                                    class="cart_value">{{ webCurrencyConverter(amount: $leadsDetails['package_main_price']) }}
                                </span>
                                <span id="partial-pay-span"
                                    class="cart_value d-none">{{ webCurrencyConverter(amount: $leadsDetails['package_price']) }}
                            </div>
                            <hr class="my-2">

                            @php
                                if (auth('customer')->check()) {
                                    $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                }
                                $couponDiscount = session()->has('coupon_discount_offlinepooja')
                                    ? session('coupon_discount_offlinepooja')
                                    : 0;
                                $productFullAmount =
                                    $leadsDetails['package_main_price'] + $final_price_val - $couponDiscount;
                                $fullAmount = $productFullAmount - $customer->wallet_balance;
                                $productPartialAmount =
                                    $leadsDetails['package_price'] + $final_price_val - $couponDiscount;
                                $partialAmount = $productPartialAmount - $customer->wallet_balance;

                            @endphp
                            @if ($customer->wallet_balance > 0)
                                <div id="wallet-full-div">
                                    <div class="finalProducts">
                                        <div class="d-flex">
                                            <span class="cart_title">{{ translate('wallet_balance') }} </span>
                                            <span class="cart_value text-success">
                                                ({{ webCurrencyConverter(amount: $customer->wallet_balance) }})</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="cart_title">{{ translate('Amount_Paid_(via_Wallet)') }}</span>
                                            @if ($customer->wallet_balance < $productFullAmount)
                                                <span class="cart_value text-danger"> -
                                                    {{ webCurrencyConverter(amount: $customer->wallet_balance) }}</span>
                                            @else
                                                <span class="cart_value text-danger"> -
                                                    {{ webCurrencyConverter(amount: $productFullAmount) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div id="wallet-partial-div" class="d-none">
                                    <div class="finalProducts">
                                        <div class="d-flex">
                                            <span class="cart_title">{{ translate('wallet_balance') }} </span>
                                            <span class="cart_value text-success">
                                                ({{ webCurrencyConverter(amount: $customer->wallet_balance) }})</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="cart_title">{{ translate('Amount_Paid_(via_Wallet)') }}</span>
                                            @if ($customer->wallet_balance < $productPartialAmount)
                                                <span class="cart_value text-danger"> -
                                                    {{ webCurrencyConverter(amount: $customer->wallet_balance) }}</span>
                                            @else
                                                <span class="cart_value text-danger"> -
                                                    {{ webCurrencyConverter(amount: $productPartialAmount) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2">
                            @endif

                        </div>

                        @include('web-views.offlinepooja.partials._couponofflinepooja')

                    </div>

                </aside>
            </section>

        </div>
    </div>

@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/cart-details.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>

    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>


    <script>
        function offlinepoojaCouponList() {
            let expireDate = "";
            let formattedDate = "";
            let body = "";
            $.ajax({
                type: "get",
                url: "{{ route('offlinepooja-coupons') }}",
                success: function(response) {
                    $('#offlinepooja-modal-body').html('');
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
                                            <p>On All Pooja</p>
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
                            $('#offlinepooja-modal-body').css({
                                'display': 'flex',
                                'justify-content': 'center',
                                'padding': '50px 0px',
                                'color': 'red'
                            });
                        }
                        $('#offlinepooja-modal-body').append(body);
                        $('#offlinepooja-coupon-modal').modal('show');
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
                    toast.error("Failed to copy");
                });
        }
    </script>

    <script>
        $(document).ready(function() {
            // var paymentAmount = $('#full-payment-amount').val();
            // $('#mainProductPriceInput').val(paymentAmount);
            $('input[name="payment_type"]').on('change', function() {
                if ($(this).val() === 'full') {
                    $('#full-pay-span').removeClass('d-none');
                    $('#partial-pay-span').addClass('d-none');
                    $('#with-coupon-full-div').removeClass('d-none');
                    $('#with-coupon-partial-div').addClass('d-none');
                    $('#without-coupon-full-div').removeClass('d-none');
                    $('#without-coupon-partial-div').addClass('d-none');
                    $('#wallet-full-div').removeClass('d-none');
                    $('#wallet-partial-div').addClass('d-none');
                    // var fullPaymentAmount = $('#full-payment-amount').val();
                    // $('#mainProductPriceInput').val(fullPaymentAmount);
                    $('#wallet-balance').val(
                        {{ $customer->wallet_balance < $productFullAmount ? $customer->wallet_balance : $productFullAmount }}
                    );
                    $('#Amount').val({{ $leadsDetails['package_main_price'] }});
                    $('#remain-amount').val(0);
                    $('#remain-amount-status').val(1);
                } else {
                    $('#full-pay-span').addClass('d-none');
                    $('#partial-pay-span').removeClass('d-none');
                    $('#with-coupon-full-div').addClass('d-none');
                    $('#with-coupon-partial-div').removeClass('d-none');
                    $('#without-coupon-full-div').addClass('d-none');
                    $('#without-coupon-partial-div').removeClass('d-none');
                    $('#wallet-full-div').addClass('d-none');
                    $('#wallet-partial-div').removeClass('d-none');
                    // var partialPaymentAmount = $('#partial-payment-amount').val();
                    // $('#mainProductPriceInput').val(partialPaymentAmount);
                    $('#wallet-balance').val(
                        {{ $customer->wallet_balance < $productPartialAmount ? $customer->wallet_balance : $productPartialAmount }}
                    );
                    $('#Amount').val({{ $leadsDetails['package_price'] }});
                    $('#remain-amount').val(
                        {{ $leadsDetails['package_main_price'] - $leadsDetails['package_price'] }});
                    $('#remain-amount-status').val(0);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('input[name="payment_type"]').on('change', function() {
                let selectedPaymentMode = $(this).val();
                let leadId = '{{ $leadsDetails->id }}';

                $.ajax({
                    url: "{{ route('offline.pooja.offlinepooja.updatePaymentMode') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        lead_id: leadId,
                        payment_type: selectedPaymentMode
                    },
                    success: function(response) {
                        console.log("Payment mode updated:", response.message);
                    },
                    error: function(xhr) {
                        console.error("Error updating payment mode");
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#city').on('change', function() {
                var selectedCity = $(this).val();

                if (selectedCity === "") {
                    $('#proceed-btn').prop('disabled', true);
                    $('#city-error').addClass('d-none');
                } else if (selectedCity === "other") {
                    $('#proceed-btn').prop('disabled', true);
                    $('#city-error').removeClass('d-none');
                } else {
                    $('#proceed-btn').prop('disabled', false);
                    $('#city-error').addClass('d-none');
                }
            });
        });
    </script>
@endpush
