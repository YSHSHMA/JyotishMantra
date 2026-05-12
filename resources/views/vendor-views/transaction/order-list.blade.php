@extends('layouts.back-end.app-seller')
@section('title', translate('order_Transactions'))
@section('content')
    <div class="content container-fluid ">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('public/assets/back-end/img/order_report.png') }}" alt="">
                {{ translate('transaction_report') }}
            </h2>
        </div>
        @include('vendor-views.transaction.transaction-report-inline-menu')

        <div class="card mb-2">
            <div class="card-body">
                <h4 class="mb-3">{{ translate('filter_Data') }}</h4>
                <form action="#" id="form-data" method="GET" class="w-100">
                    <div
                        class="row  gx-2 gy-3 align-items-center text-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}">
                        <div class="col-sm-6 col-md-3">
                            <div class="">
                                <select class="form-control __form-control" name="status">
                                    <option class="text-center" value="0" disabled>
                                        ---{{ translate('select_status') }}---
                                    </option>
                                    <option class="text-capitalize" value="all" {{ $status == 'all' ? 'selected' : '' }}>
                                        {{ translate('all') }} </option>
                                    <option class="text-capitalize" value="disburse"
                                        {{ $status == 'disburse' ? 'selected' : '' }}>{{ translate('disburse') }} </option>
                                    <option class="text-capitalize" value="hold"
                                        {{ $status == 'hold' ? 'selected' : '' }}>
                                        {{ translate('hold') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="">
                                <select class="js-select2-custom form-control __form-control" name="customer_id">
                                    <option class="text-center" value="all"
                                        {{ $customer_id == 'all' ? 'selected' : '' }}>
                                        {{ translate('all_customer') }}
                                    </option>
                                    @foreach ($customers as $customer)
                                        <option class="text-left text-capitalize" value="{{ $customer->id }}"
                                            {{ $customer->id == $customer_id ? 'selected' : '' }}>
                                            {{ $customer->f_name . ' ' . $customer->l_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <select class="form-control __form-control" name="date_type" id="date_type">
                                <option value="this_year" {{ $date_type == 'this_year' ? 'selected' : '' }}>
                                    {{ translate('this_Year') }}</option>
                                <option value="this_month" {{ $date_type == 'this_month' ? 'selected' : '' }}>
                                    {{ translate('this_Month') }}</option>
                                <option value="this_week" {{ $date_type == 'this_week' ? 'selected' : '' }}>
                                    {{ translate('this_Week') }}</option>
                                <option value="today" {{ $date_type == 'today' ? 'selected' : '' }}>
                                    {{ translate('today') }}</option>
                                <option value="custom_date" {{ $date_type == 'custom_date' ? 'selected' : '' }}>
                                    {{ translate('custom_Date') }}</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3" id="from_div">
                            <div class="form-floating">
                                <input type="date" name="from" value="{{ $from }}" id="from_date"
                                    class="form-control __form-control">
                                <label>{{ translate('start_date') }}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3" id="to_div">
                            <div class="form-floating">
                                <input type="date" value="{{ $to }}" name="to" id="to_date"
                                    class="form-control __form-control">
                                <label>{{ translate('end_date') }}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn--primary px-4 min-w-120 __h-45px" id="formUrlChange"
                                data-action="{{ url()->current() }}">
                                {{ translate('filter') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="store-report-content mb-2">
            <div class="left-content">
                <div class="left-content-card">
                    <img src="{{ dynamicAsset(path: '/public/assets/back-end/img/cart.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">{{ $product_data['total_products'] }}</h4>
                        <h6 class="subtext">{{ translate('total_Products') }}</h6>
                    </div>
                </div>
                <div class="left-content-card">
                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/products.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">{{ $product_data['active_products'] }}</h4>
                        <h6 class="subtext">{{ translate('active_Products') }}</h6>
                    </div>
                </div>
                <div class="left-content-card">
                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inactive-product.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">{{ $product_data['inactive_products'] }}</h4>
                        <h6 class="subtext">{{ translate('inactive_Products') }}</h6>
                    </div>
                </div>
                <div class="left-content-card">
                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/pending_products.svg') }}" alt="">
                    <div class="info">
                        <h4 class="subtitle">{{ $product_data['pending_products'] }}</h4>
                        <h6 class="subtext">{{ translate('pending_Products') }}</h6>
                    </div>
                </div>
            </div>
            <div class="center-chart-area">
                <div class="center-chart-header">
                    <h3 class="title">{{ translate('order_Statistics') }}</h3>
                </div>
                <canvas id="updatingData" class="store-center-chart"
                    data-hs-chartjs-options='{
                "type": "bar",
                "data": {
                  "labels": [{{ '"' . implode('","', array_keys($order_transaction_chart['order_amount'])) . '"' }}],
                  "datasets": [{
                    "label": "{{ translate('total_order_amount') }}",
                    "data": [{{ '"' . implode('","', array_values($order_transaction_chart['order_amount'])) . '"' }}],
                    "backgroundColor": "#a2ceee",
                    "hoverBackgroundColor": "#0177cd",
                    "borderColor": "#a2ceee"
                  }]
                },
                "options": {
                  "scales": {
                    "yAxes": [{
                      "gridLines": {
                        "color": "#e7eaf3",
                        "drawBorder": false,
                        "zeroLineColor": "#e7eaf3"
                      },
                      "ticks": {
                        "beginAtZero": true,
                        "fontSize": 12,
                        "fontColor": "#97a4af",
                        "fontFamily": "Open Sans, sans-serif",
                        "padding": 5,
                        "postfix": " {{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}"
                      }
                    }],
                    "xAxes": [{
                      "gridLines": {
                        "display": false,
                        "drawBorder": false
                      },
                      "ticks": {
                        "fontSize": 12,
                        "fontColor": "#97a4af",
                        "fontFamily": "Open Sans, sans-serif",
                        "padding": 5
                      },
                      "categoryPercentage": 0.3,
                      "maxBarThickness": "10"
                    }]
                  },
                  "cornerRadius": 5,
                  "tooltips": {
                    "prefix": " ",
                    "hasIndicator": true,
                    "mode": "index",
                    "intersect": false
                  },
                  "hover": {
                    "mode": "nearest",
                    "intersect": true
                  }
                }
              }'>
                </canvas>
            </div>
            <div class="right-content">
                <div class="card h-100 bg-white payment-statistics-shadow">
                    <div class="card-header border-0 ">
                        <h5 class="card-title">
                            <span>{{ translate('payment_Statistics') }}</span>
                        </h5>
                    </div>
                    <div class="card-body px-0 pt-0">
                        <div class="position-relative pie-chart">
                            <div id="dognut-pie" class="label-hide"></div>
                            <div class="total--orders">
                                <h3>{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}{{ getFormatCurrency(amount: usdToDefaultCurrency(amount: $payment_data['total_payment'])) }}
                                </h3>
                                <span>{{ translate('completed_payments') }}</span>
                            </div>
                        </div>
                        <div class="apex-legends">
                            <div class="before-bg-004188">
                                <span>{{ translate('cash_payments') }}
                                    ({{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $payment_data['cash_payment']), currencyCode: getCurrencyCode()) }})</span>
                            </div>
                            <div class="before-bg-0177CD">
                                <span>{{ translate('digital_payments') }}
                                    ({{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $payment_data['digital_payment']), currencyCode: getCurrencyCode()) }})
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            </div>
                            <div class="before-bg-A2CEEE">
                                <span>{{ translate('wallet') }}
                                    ({{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $payment_data['wallet_payment']), currencyCode: getCurrencyCode()) }})</span>
                            </div>
                            <div class="before-bg-CDE6F5">
                                <span>{{ translate('offline_payments') }}
                                    ({{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $payment_data['offline_payment']), currencyCode: getCurrencyCode()) }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="px-3 py-4">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <h4 class="mb-0 mr-auto">
                        {{ translate('total_Transactions') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $transactions->total() }}</span>
                    </h4>
                    <form action="{{ url()->full() }}" method="GET" class="mb-0">
                        <div class="input-group input-group-merge input-group-custom">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch_" type="search" name="search" class="form-control"
                                placeholder="{{ translate('search_by_orders_id_or_transaction_ID') }}"
                                aria-label="Search orders" value="{{ $search }}" required>
                            <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                        </div>
                    </form>
                    <div>
                        <a href="{{ route('vendor.transaction.order-transaction-summary-pdf', ['date_type' => request('date_type'), 'customer_id' => request('customer_id'), 'status' => request('status'), 'from' => request('from'), 'to' => request('to'), 'search' => request('search')]) }}"
                            class="btn btn-outline--primary text-nowrap btn-block">
                            <i class="tio-file-text"></i>
                            {{ translate('download_PDF') }}
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline--primary text-nowrap btn-block"
                            data-toggle="dropdown">
                            <i class="tio-download-to"></i>
                            {{ translate('export') }}
                            <i class="tio-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('vendor.transaction.order-transaction-export-excel', ['date_type' => request('date_type'), 'customer_id' => request('customer_id'), 'search' => request('search'), 'status' => request('status'), 'from' => request('from'), 'to' => request('to')]) }}">{{ translate('excel') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="datatable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 __table">
                    <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('order_id') }}</th>
                            <th>{{ translate('customer_name') }}</th>
                            <th>{{ translate('total_product_amount') }}</th>
                            <th>{{ translate('product_discount') }}</th>
                            <th>{{ translate('coupon_discount') }}</th>
                            <th>{{ translate('discounted_amount') }}</th>
                            <th>{{ translate('VAT/TAX') }}</th>
                            <th>{{ translate('INC/EXCU') }}</th>
                            <th>{{ translate('vendor_shipping_charge') }}</th>
                            <th>{{ translate('actual_delivery_charge') }}</th>
                            <th>{{ translate('remaining_delivery_balance') }}</th>
                            <th>{{ translate('vendor_remaining_shipping_amount') }}</th>
                            <th>{{ translate('admin_add_shipping_charge_amount') }}</th>
                            <th>{{ translate('order_amount') }}</th>
                            <th>{{ translate('delivered_by') }}</th>
                            <th>{{ translate('deliveryman_incentive') }}</th>
                            <th>{{ translate('admin_discount') }}</th>
                            <th>{{ translate('vendor_discount') }}</th>
                            <th>{{ translate('admin_commission') }}</th>
                            <th>{{ translate('admin_net_income') }}</th>
                            <th>{{ translate('Customer_total_paid_amount') }}</th>
                            <th>{{ translate('customer_refund_amount') }}</th>
                            <th>{{ translate('customer_refund_amount_full_or_partially_show') }}</th>
                            <th>{{ translate('reverse_shipping_charges') }}</th>
                            <th>{{ translate('seller_income') }}</th>
                            <th>{{ translate('seller_net_income') }}</th>
                            <th>{{ translate('payment_method') }}</th>
                            <th>{{ translate('payment_Status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $key => $transaction)
                            @php
                                $deliveredCount = \App\Models\OrderDetail::where('order_id', $transaction->order_id)
                                    ->get()
                                    ->count();
                                $refund = \App\Models\RefundRequest::where('order_id', $transaction->order_id)->get();

                                $refundAmountSum = $refund->sum(function ($refunds) {
                                    return $refunds->amount - $refunds->admin_tax_given ?? 0;
                                });

                                $refundDeliveryChargeSum = $refund->sum(function ($refunds) {
                                    return $refunds->delivery_charge ?? 0;
                                });
                            @endphp
                            @if ($transaction->order)
                                <tr>
                                    <td>{{ $transactions->firstItem() + $key }}</td>

                                    <td>
                                        <a class="title-color hover-c1"
                                            href="{{ route('vendor.orders.details', $transaction['order_id']) }}">{{ $transaction['order_id'] }}</a>
                                    </td>

                                    <td>
                                        @if (!$transaction->order->is_guest && isset($transaction->customer))
                                            {{ $transaction->customer->f_name }} {{ $transaction->customer->l_name }}
                                        @elseif($transaction->order->is_guest)
                                            {{ translate('guest_customer') }}
                                        @else
                                            {{ translate('not_found') }}
                                        @endif
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->orderDetails[0]->order_details_sum_price), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->orderDetails[0]->order_details_sum_discount), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->order->discount_amount), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->orderDetails[0]->order_details_sum_price - $transaction->orderDetails[0]->order_details_sum_discount - (isset($transaction->order->coupon) && $transaction->order->coupon->coupon_type != 'free_delivery' ? $transaction->order->discount_amount : 0)), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction['tax']), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ \App\Models\OrderDetail::where('order_id', $transaction['order_id'])->value('tax_model') }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->order->shipping_cost), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    @php
                                        $remainingAmt = 0;
                                        if (!empty($transaction->order->delivery_charge)) {
                                            $remainingAmt =
                                                $transaction->order->shipping_cost -
                                                $transaction->order->delivery_charge;
                                        }
                                    @endphp

                                    <td>
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: !empty($transaction->order->delivery_charge) ? $transaction->order->delivery_charge : 0), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $remainingAmt), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $remainingAmt >= 0 ? $remainingAmt : 0), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $remainingAmt < 0 ? abs($remainingAmt) : 0), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->order->order_amount), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ $transaction['delivered_by'] }}</td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->order->delivery_type == 'self_delivery' && $transaction->order->shipping_responsibility == 'sellerwise_shipping' ? $transaction->order->deliveryman_charge : 0), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>
                                        @php($admin_coupon_discount = $transaction->order->coupon_discount_bearer == 'inhouse' && $transaction->order->discount_type == 'coupon_discount' ? $transaction->order->discount_amount : 0)
                                        @php($admin_shipping_discount = $transaction->order->free_delivery_bearer == 'admin' && $transaction->order->is_shipping_free ? $transaction->order->extra_discount : 0)
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $admin_coupon_discount + $admin_shipping_discount), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>
                                        @php($seller_coupon_discount = $transaction->order->coupon_discount_bearer == 'seller' && $transaction->order->discount_type == 'coupon_discount' ? $transaction->order->discount_amount : 0)
                                        @php($seller_shipping_discount = $transaction->order->free_delivery_bearer == 'seller' && $transaction->order->is_shipping_free ? $transaction->order->extra_discount : 0)
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $seller_coupon_discount + $seller_shipping_discount), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction['admin_commission'] + ($transaction->order->delivery_charge ?? 0)), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction['admin_commission'] + $transaction['tax'] + ($transaction->order->delivery_charge ?? 0)), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->order->order_amount), currencyCode: getCurrencyCode()) }}
                                    </td>


                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->order->order_amount - $transaction['tax']), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $refundAmountSum), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $refundDeliveryChargeSum), currencyCode: getCurrencyCode()) }}
                                    </td>

                                    <td>{{'₹'}}
                                        {{$refundAmountSum == 0 || $deliveredCount != count($refund) ? $transaction->order->order_amount - ($transaction['admin_commission'] + $transaction['tax'] + ($transaction->order->delivery_charge ?? 0)) : '-' . $transaction['admin_commission'] + ($transaction->order->delivery_charge ?? 0) + $refundDeliveryChargeSum }}
                                    </td>

                                    <td>{{'₹'}}
                                        {{$refundAmountSum == 0 ? $transaction->order->order_amount - ($transaction['admin_commission'] + $transaction['tax'] + ($transaction->order->delivery_charge ?? 0)) : ($deliveredCount == count($refund) ? '-' . $transaction['admin_commission'] + ($transaction->order->delivery_charge ?? 0) + $refundDeliveryChargeSum : $transaction->order->order_amount - ($transaction['admin_commission'] + $transaction['tax'] + ($transaction->order->delivery_charge ?? 0))-$refundAmountSum-$refundDeliveryChargeSum) }}
                                    </td>

                                    <td>{{ str_replace('_', ' ', $transaction['payment_method']) }}</td>

                                    <td>
                                        <div class="text-center">
                                            <span
                                                class="badge {{ $transaction['status'] == 'disburse' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                                                {{ translate(str_replace('_', ' ', $transaction['status'])) }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('vendor.transaction.pdf-order-wise-transaction', ['order_id' => $transaction->order_id]) }}"
                                                class="btn btn-outline-success square-btn btn-sm">
                                                <i class="tio-download-to"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>
                @if (count($transactions) == 0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-160"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                            alt="{{ translate('image_description') }}">
                        <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                    </div>
                @endif
            </div>

            <div class="table-responsive mt-4">
                <div class="px-4 d-flex justify-content-lg-end">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>

    <span id="currency_symbol" data-text="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}"></span>

    <span id="digital_payment" data-text="{{ usdToDefaultCurrency(amount: $payment_data['digital_payment']) }}"></span>
    <span id="cash_payment" data-text="{{ usdToDefaultCurrency(amount: $payment_data['cash_payment']) }}"></span>
    <span id="wallet_payment" data-text="{{ usdToDefaultCurrency(amount: $payment_data['wallet_payment']) }}"></span>
    <span id="offline_payment" data-text="{{ usdToDefaultCurrency(amount: $payment_data['offline_payment']) }}"></span>

    <span id="digital_payment_text" data-text="{{ translate('digital_payment') }}"></span>
    <span id="cash_payment_text" data-text="{{ translate('cash_payment') }}"></span>
    <span id="wallet_payment_text" data-text="{{ translate('wallet_payment') }}"></span>
    <span id="offline_payment_text" data-text="{{ translate('offline_payments') }}"></span>

    <span id="digital_payment_format"
        data-text="{{ getFormatCurrency(amount: usdToDefaultCurrency(amount: $payment_data['digital_payment'])) }}"></span>
    <span id="cash_payment_format"
        data-text="{{ getFormatCurrency(amount: usdToDefaultCurrency(amount: $payment_data['cash_payment'])) }}"></span>
    <span id="wallet_payment_format"
        data-text="{{ getFormatCurrency(amount: usdToDefaultCurrency(amount: $payment_data['wallet_payment'])) }}"></span>
    <span id="offline_payment_format"
        data-text="{{ getFormatCurrency(amount: usdToDefaultCurrency(amount: $payment_data['offline_payment'])) }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/chart.js.extensions/chartjs-extensions.js') }}">
    </script>
    <script
        src="{{ dynamicAsset(path: 'public/assets/back-end/js/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js') }}">
    </script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/apexcharts.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor/transaction-report.js') }}"></script>
@endpush
