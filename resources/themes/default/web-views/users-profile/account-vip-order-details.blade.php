@extends('layouts.front-end.app')
@section('title', translate('order_Details'))
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.vipanushthan-details.vip-order-partial')
                <div class="bg-white border-lg rounded mobile-full">
                    <div class="p-lg-3 p-0">
                        <div class="card border-sm">
                            <div class="p-lg-3">
                                <div class="border-lg rounded payment mb-lg-3 table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <thead>
                                            <tr class="order_table_tr">
                                                <td class="order_table_td">
                                                    <div class="">
                                                        <div
                                                            class="_1 py-2 d-flex justify-content-between align-items-center">
                                                            <h6 class="fs-13 font-bold text-capitalize">
                                                                {{ translate('payment_info') }}</h6>
                                                        </div>
                                                        <div class="fs-12">
                                                            <span
                                                                class="text-muted text-capitalize">{{ translate('payment_status') }}</span>:
                                                            @if ($order['payment_status'] == 1)
                                                                <span
                                                                    class="text-success text-capitalize">{{ translate('paid') }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="mt-2 fs-12">
                                                            <span
                                                                class="text-muted text-capitalize">{{ translate('payment_method') }}</span>
                                                            :<span
                                                                class="text-primary text-capitalize">{{ translate('online') }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                @if ($order->is_prashad == '1')
                                                    <td class="order_table_td">
                                                        <div class="">
                                                            <div class=" py-2">
                                                                <h6 class="fs-13 font-bold text-capitalize">
                                                                    {{ translate('prshard_shipping_address') }}:
                                                                </h6>
                                                            </div>
                                                            <div class="">
                                                                <span class="text-capitalize fs-12">
                                                                    <span class="text-capitalize">
                                                                        <span
                                                                            class="min-w-60px">{{ translate('name') }}</span>
                                                                        :&nbsp; {{ $order['customer']['name'] }},
                                                                    </span>
                                                                    <br>
                                                                    <span class="text-capitalize">
                                                                        <span
                                                                            class="min-w-60px">{{ translate('phone') }}</span>
                                                                        :&nbsp; {{ $order['customer']['phone'] }},
                                                                    </span>
                                                                    <br>
                                                                    <span class="text-capitalize">
                                                                        <span class="min-w-60px">
                                                                            {{ translate('city') }} /
                                                                            {{ translate('zip') }}
                                                                        </span> :&nbsp; {{ $order->city }},
                                                                        {{ $order->pincode }}
                                                                    </span>
                                                                    <br>
                                                                    <span class="text-capitalize">
                                                                        <span class="min-w-60px">
                                                                            {{ translate('address') }}
                                                                        </span> :
                                                                        {{ $order->house_no }},{{ $order->area }}
                                                                    </span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                @endif
                                                <td class="order_table_td">
                                                    <div class="">
                                                        <div class="py-2">
                                                            <h6 class="fs-13 font-bold text-capitalize">
                                                                {{ translate('Bill Address') }}:
                                                            </h6>
                                                        </div>
                                                        <div class="">
                                                            <span class="text-capitalize fs-12">
                                                                <span class="text-capitalize">
                                                                    <span class="min-w-60px">{{ translate('name') }}</span>
                                                                    : &nbsp;{{ $order['customers']['name'] }}
                                                                </span>
                                                                <br>
                                                                <span class="text-capitalize">
                                                                    <span
                                                                        class="min-w-60px">{{ translate('phone') }}</span>
                                                                    : &nbsp;{{ $order['customer']['phone'] }},
                                                                </span>
                                                                <br>
                                                                <span class="text-capitalize">
                                                                    <span
                                                                        class="min-w-60px">{{ translate('email') }}</span>
                                                                    : &nbsp;{{ $order['customer']['email'] }},
                                                                </span>

                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="payment mb-3 table-responsive d-none d-lg-block">
                                    <table class="table table-borderless min-width-600px">
                                        <thead class="thead-light text-capitalize">
                                            <tr class="fs-13 font-semibold">
                                                <th>{{ translate('puja_name') }}</th>
                                                <th>{{ translate('price') }}</th>
                                                <th>{{ translate('Prashad') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="media align-items-center gap-5">
                                                        <img class="d-block get-view-by-onclick rounded"
                                                            src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $order['vippoojas']['thumbnail'], type: 'backend-product') }}"
                                                            alt="{{ translate('image_Description') }}" style="width:50px">
                                                        <div>
                                                            <h6 class="title-color">
                                                                {{ Str::words($order['vippoojas']['name'], 20, '...') }}

                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>


                                                    <div class="">
                                                        <span
                                                            class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['package_price']), currencyCode: getCurrencyCode()) }}</span>
                                                    </div>

                                                <td>
                                                    {{ $order['is_prashad'] == 1 ? 'Yes' : 'No' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if (count($order['product_leads']) > 0)
                                    <div class="table-responsive datatable-custom">
                                        <table class="table table-borderless min-width-600px">
                                            <thead class="thead-light text-capitalize">
                                                <tr class="fs-13 font-semibold">
                                                    <th>{{ translate('Cherity_Details') }}</th>
                                                    <th>{{ translate('qty') }}</th>
                                                    <th>{{ translate('Price') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order['product_leads'] as $productLeads)
                                                    <tr>
                                                        <td>
                                                            <h6 class="title-color">
                                                                {{ Str::words($productLeads['product_name'], 20, '...') }}

                                                        </td>
                                                        <td>
                                                            {{ $productLeads['qty'] }}
                                                        </td>

                                                        <td>
                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $productLeads['final_price']), currencyCode: getCurrencyCode()) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row d-flex justify-content-end mt-2">
                            <div class="col-md-8 col-lg-5">
                                <div class="bg-white border-sm rounded">
                                    <div class="card-body ">
                                        <table class="calculation-table table table-borderless mb-0">
                                            <tbody class="totals">
                                                <tr>
                                                    <td>
                                                        <div class="text-start">
                                                            <span class="font-semibold">{{ translate('item') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <span class="font-semibold">{{ translate('Price') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <span class="fs-15 font-semibold">{{ $order->qty }}</span>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr class="border-top">
                                                    <td>
                                                        <div class="text-start">
                                                            <span class="product-qty">{{ translate('subtotal') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>

                                                        <div class="text-end">
                                                            <span
                                                                class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['package_price']), currencyCode: getCurrencyCode()) }}</span>
                                                        </div>
                                                    </td>
                                                </tr>

                                                @if (count($order['product_leads']) > 0)
                                                    <tr>
                                                        <td>
                                                            <div class="text-start">
                                                                <span class="product-qty">
                                                                    {{ translate('Dann Price') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="text-end">
                                                                <span class="fs-15 font-semibold">
                                                                    @php
                                                                        $totalSum = 0;
                                                                    @endphp

                                                                    @foreach ($order['product_leads'] as $productLeads)
                                                                        @php
                                                                            $convertedPrice = usdToDefaultCurrency(
                                                                                amount: $productLeads['final_price'],
                                                                            );
                                                                            $totalSum += $convertedPrice;
                                                                        @endphp
                                                                    @endforeach
                                                                    @php
                                                                        $formattedTotalSum = setCurrencySymbol(
                                                                            amount: $totalSum,
                                                                            currencyCode: getCurrencyCode(),
                                                                        );
                                                                    @endphp

                                                                    <!-- Display the formatted total sum -->
                                                                    {{ $formattedTotalSum }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td>
                                                        <div class="text-start">
                                                            <span
                                                                class="product-qty">{{ translate('coupon_discount') }}</span>
                                                            <br>
                                                            @if ($order->coupon_code)
                                                                <span class="text-danger">{{ $order->coupon_code }}</span>
                                                            @else
                                                                <span
                                                                    class="text-muted">{{ translate('No Coupon Applied') }}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            @if ($order->coupon_amount)
                                                                <span class="fs-15 font-semibold text-danger">
                                                                    -
                                                                    {{ webCurrencyConverter(amount: $order->coupon_amount) }}
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="fs-15 text-muted">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: 0), currencyCode: getCurrencyCode()) }}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="text-start">
                                                            <span class="product-qty">
                                                                {{ translate('Amount_Paid_(via_Wallet)') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <span class="fs-15 font-semibold text-danger">-
                                                                {{ webCurrencyConverter(amount: $order->wallet_amount) }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="text-start">
                                                            <span class="product-qty">
                                                                {{ translate('Amount_Paid_(Online)') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <span class="fs-15 font-semibold text-danger">-
                                                                {{ webCurrencyConverter(amount: $order->transection_amount) }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="border-top">
                                                    <td>
                                                        <div class="text-start">
                                                            <span class="font-weight-bold">
                                                                <strong>{{ translate('total_amount') }}</strong>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <span class="font-weight-bold amount">
                                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->pay_amount), currencyCode: getCurrencyCode()) }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                        @if ($order['order_status'] == 'pending')
                                            <button
                                                class="btn btn-soft-danger btn-soft-border w-100 btn-sm text-danger font-semibold text-capitalize mt-3 call-route-alert"
                                                data-route="{{ route('order-cancel', [$order->id]) }}"
                                                data-message="{{ translate('want_to_cancel_this_order?') }}">
                                                {{ translate('cancel_order') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @if ($order->transection_amount == 0)
                                <div class="important-note">
                                    <strong>Important Note:</strong> Amount will be deducted via wallet.
                                </div>
                            @else
                                <div class="important-note">
                                    <strong>Important:</strong> Online payment of 
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->transection_amount), currencyCode: getCurrencyCode()) }} 
                                    and wallet balance will be used for the remaining amount.
                                </div>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <span id="message-ratingContent" data-poor="{{ translate('poor') }}" data-average="{{ translate('average') }}"
        data-good="{{ translate('good') }}" data-good-message="{{ translate('the_delivery_service_is_good') }}"
        data-good2="{{ translate('very_Good') }}"
        data-good2-message="{{ translate('this_delivery_service_is_very_good_I_am_highly_impressed') }}"
        data-excellent="{{ translate('excellent') }}"
        data-excellent-message="{{ translate('best_delivery_service_highly_recommended') }}"></span>
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>
@endpush
