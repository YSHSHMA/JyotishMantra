@extends('layouts.front-end.app')
@section('title', translate('order_Details'))
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.chadhava-details.chadhava-order-partial')
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
                                                        <span>{{ translate('payment_status') }}:</span>
                                                        @php
                                                            $status = $order['payment_status'];
                                                        @endphp

                                                        @if ($status == 1)
                                                            <span
                                                                class="badge bg-success text-white px-2">{{ translate('paid') }}</span>
                                                        @elseif ($status == 0)
                                                            <span
                                                                class="badge bg-danger text-white px-2">{{ translate('pending') }}</span>
                                                        @elseif ($status == 2)
                                                            <span
                                                                class="badge bg-danger text-white px-2">{{ translate('failed') }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-info text-dark px-2">{{ translate('failed') }}</span>
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
                                                                    : &nbsp;{{ $order['customers']['f_name'] }}
                                                                    {{ $order['customers']['l_name'] }}
                                                                </span>
                                                                <br>
                                                                <span class="text-capitalize">
                                                                    <span
                                                                        class="min-w-60px">{{ translate('phone') }}</span>
                                                                    : &nbsp;{{ $order['customers']['phone'] }}
                                                                </span>
                                                                @if (str_contains($order['customers']['email'], '.com'))
                                                                    <br>
                                                                    <span class="" style="text-transform: lowercase;">
                                                                        <span
                                                                            class="min-w-60px">{{ translate('email') }}</span>
                                                                        : &nbsp;{{ $order['customers']['email'] }}
                                                                    </span>
                                                                @endif

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
                                                <th>{{ translate('Chadhava_name') }}</th>
                                                <th>{{ translate('Address') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="media align-items-center gap-5">
                                                        <img class="d-block get-view-by-onclick rounded" 
                                                                src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $order['chadhava']['thumbnail'], type: 'backend-product') }}" 
                                                                alt="{{ translate('image_Description') }}" 
                                                                style="width:50px; height:auto;">
                                                        <div class="pl-3">
                                                            <h6 class="title-color font-weight-bold text-uppercase">
                                                                {{ Str::words($order['chadhava']['name'], 40, '...') }}
                                                            </h6>
                                                            
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                     {{ $order['chadhava']['chadhava_venue'] }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="payment mb-3 table-responsive d-none d-lg-block">
                                    <table class="table table-borderless min-width-600px">
                                        <thead class="thead-light text-capitalize">
                                            <tr class="fs-13 font-semibold">
                                                <th>{{ translate('Product_name') }}</th>
                                                <th>{{ translate('Qty') }}</th>
                                                <th>{{ translate('price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                        </tbody>
                                    </table>
                                </div>
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
                                                            <span class="product-qty">{{ translate('subtotal') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <span
                                                                class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['pay_amount']), currencyCode: getCurrencyCode()) }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="text-start">
                                                            <span  class="product-qty">
                                                                {{ translate('Amount_Paid_(via_Wallet)') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <span class="fs-15 font-semibold text-danger">- {{  webCurrencyConverter(amount: $order->wallet_amount) }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="border-top">
                                                    <td>
                                                        <div class="text-start">
                                                            <span class="font-weight-bold">
                                                                <strong>{{ translate('total') }}</strong>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <span class="font-weight-bold amount">
                                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:$order->transection_amount), currencyCode: getCurrencyCode()) }}
                                                               
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                        {{-- @if ($order['order_status'] == 'pending')
                                            <button
                                                class="btn btn-soft-danger btn-soft-border w-100 btn-sm text-danger font-semibold text-capitalize mt-3 call-route-alert"
                                                data-route="{{ route('order-cancel', [$order->id]) }}"
                                                data-message="{{ translate('want_to_cancel_this_order?') }}">
                                                {{ translate('cancel_order') }}
                                            </button>
                                        @endif --}}
                                    </div>
                                </div>
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
