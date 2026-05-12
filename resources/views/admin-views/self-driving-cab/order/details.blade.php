@extends('layouts.back-end.app')

@section('title', translate('Tour_details'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/refund_transaction.png') }}"
                alt="">
            {{ translate('Tour_details') }}
        </h2>
    </div>
    <div class="refund-details-card--2 p-4">
        <div class="row gy-2">
            <div class="col-lg-4">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <h4 class="mb-3">{{ translate('Payment_summary') }}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{ translate('transaction_id') }} </span> <span>:</span> <span
                                    class="right">{{ $getData['transaction_id'] }}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left text-capitalize">{{ translate('booking_date') }}</span>
                                <span>:</span>
                                <span class="right">{{ date('d M Y, h:s:A', strtotime($getData['created_at'])) }}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left">{{ translate('payment_method') }} </span> <span>:</span> <span
                                    class="right">{{ str_replace('_', ' ', $getData['payment_method']) }}</span>
                            </li>
                        </ul>
                        @if ($getData['refund_status'] != 0)
                        <h4 class="mb-1 mt-3">{{ translate('Refund_summary') }}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{ translate('refund_id') }} </span> <span>:</span> <span
                                    class="right">{{ $getData['refound_id'] }}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left text-capitalize">{{ translate('refund_date') }}</span>
                                <span>:</span>
                                <span class="right">
                                    @if ($getData['refund_date'])
                                    {{ date('d M Y, h:s:A', strtotime($getData['refund_date'])) }}
                                    @endif
                                </span>
                            </li>
                            <li class="align-items-center">
                                <span class="left">{{ translate('refund_status') }} </span> <span>:</span>
                                <span class="right">
                                    @if ($getData['refund_status'] == 1)
                                    {{ translate('success') }}
                                    @elseif($getData['refund_status'] == 2)
                                    {{ translate('progressed') }}
                                    @elseif($getData['refund_status'] == 3)
                                    {{ translate('failed') }}
                                    @else
                                    {{ translate('pending') }}
                                    @endif

                                </span>
                            </li>
                        </ul>
                        @endif


                        @if (($getData['advance_withdrawal_amount'] ?? 0) != 0)
                        <h4 class="mb-1 mt-3">{{ translate('advance_withdrawal_amount') }}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{ translate('amount') }} </span> <span>:</span> <span
                                    class="right">{{ $getData['advance_withdrawal_amount'] }}</span>
                            </li>

                            <li class="align-items-center">
                                <span class="left">{{ translate('status') }} </span> <span>:</span>
                                <span class="right">
                                    <?php
                                    $new_withdrowal = \App\Models\WithdrawalAmountHistory::where('vendor_id', $getData['cab_assign'])->where('ex_id', $getData['id'])->where('type', 'tour')->first();
                                    ?>
                                    @if ($new_withdrowal['status'] == 1)
                                    {{ translate('success') }}
                                    @elseif($new_withdrowal['status'] == 2)
                                    {{ translate('failed') }}
                                    @else
                                    {{ translate('pending') }}
                                    @endif

                                </span>
                            </li>
                        </ul>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <div class="gap-3 mb-4 d-flex justify-content-between flex-wrap align-items-center">
                            <h4 class="">{{ translate('Tour_details') }}</h4>

                        </div>
                        <div class="refund-details">
                            <div class="img">
                                <div class="onerror-image border rounded">
                                    <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($getData['thumbnail'] ?? ''), type: 'backend-product') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="--content flex-grow-1">
                                <h4>
                                    <a href="{{ route('admin.tour_visits.overview', [$getData['SelfCabData']['slug']]) }}">
                                        {{ $getData['SelfCabData']['getCabId']['name'] }}
                                    </a>
                                    <br>
                                    <span class="h6">Pickup Date : {{ date('d M,Y', strtotime($getData['pickup_date'])) }}</span><br>
                                    <span class="h6">Pickup Time : {{ $getData['pickup_time'] }}</span><br>
                                    <span class="h6">Pickup Location : {{ $getData['pickup_address'] }}</span>
                                </h4>


                            </div>
                            <ul class="dm-info p-0 m-0 w-l-115">

                                <li>
                                    <span class="left">{{ translate('total_price') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['amount'] + $getData['coupon_amount']), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('coupon_discount') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['coupon_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>

                                <li>
                                    <span class="left">{{ translate('total_tax') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['gst_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('admin_commission') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['admin_commission'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('subtotal') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['final_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                @if ($getData['refund_status'] != 0)
                                <li>
                                    <span class="left">{{ translate('refundable_amount') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['refund_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                @endif

                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                            <h4 class="d-flex gap-2">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}"
                                    alt=""> Customer information
                            </h4>
                        </div>
                        <div class="media flex-wrap gap-3">
                            <div class="">
                                <img class="avatar rounded-circle avatar-70"
                                    src="{{ getValidImage(path: 'storage/app/public/profile/' . $getData['userData']['image'], type: 'backend-product') }}"
                                    alt="Image">
                            </div>
                            <div class="media-body d-flex flex-column gap-1">
                                <span class="title-color"><strong>{{ $getData['userData']['name'] }}</strong></span>
                                <span
                                    class="title-color break-all"><strong>{{ $getData['userData']['phone'] }}</strong></span>
                                @if ($getData['userData']['phone'] != $getData['userData']['email'])
                                <span class="title-color break-all">{{ $getData['userData']['email'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card h-100 refund-details-card--2">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{ translate('Booking_info') }}</h4>
                        <div class="row">
                            <div class="col-12 table-responsive datatable-custom">
                               
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card h-100 refund-details-card--2">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{ translate('cab_info') }}</h4>
                        <div class="key-val-list d-flex flex-column gap-2 min-width--60px">
                            <div class="key-val-list-item">
                                
                            </div>
                            @if ($getData['TravellerInfo'])
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{ translate('traveller_name') }}</span>:
                                <span>{{ $getData['TravellerInfo']['company_name'] }}</span>
                            </div>
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{ translate('email_address') }}</span>:
                                <span>
                                    <a class="text-dark"
                                        href="mailto:{{ $getData['TravellerInfo']['email'] }}">{{ $getData['TravellerInfo']['email'] }}
                                    </a>
                                </span>
                            </div>
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{ translate('phone_number') }} </span>:
                                <span>
                                    <a class="text-dark"
                                        href="tel:{{ $getData['TravellerInfo']['phone_no'] }}">{{ $getData['TravellerInfo']['phone_no'] }}
                                    </a>
                                </span>
                            </div>
                            @endif

                        </div>
                        
                        <div>
                            <hr>
                        </div>
                       
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</div>




@endsection
@push('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

@endpush