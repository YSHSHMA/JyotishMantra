@extends('layouts.front-end.app')

@section('title', translate('vehicle_order_details'))
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/social-icon.css') }}">
<style>
    @media (max-width: 767px) {
        .chat-container {
            height: 400px;
        }

        .chat-header,
        .chat-box,
        .chat-input {
            padding: 8px;
        }

        .user-message,
        .admin-message {
            font-size: 14px;
            padding: 8px;
        }

        .order_table_td {
            display: block;
            width: 100%;
        }

        .order_table_tr {
            display: block;
            margin-bottom: 20px;
        }

        .payment .table {
            min-width: 100%;
        }

        .mobile-full {
            width: 100% !important;
        }

        .customer-profile-orders .card-body {
            padding: 15px;
        }

        .payment .min-width-600px {
            min-width: auto !important;
        }
    }

    @media (max-width: 991px) {
        .customer-profile-wishlist {
            margin-top: 20px;
        }

        .d-lg-flex {
            display: block !important;
        }
    }

    .cancellation-policy-table td,
    .cancellation-policy-table th {
        font-size: 16px;
    }

    @media (max-width: 991px) {

        .cancellation-policy-table td,
        .cancellation-policy-table th {
            font-size: 14px;
        }
    }

    @media (max-width: 767px) {

        .cancellation-policy-table td,
        .cancellation-policy-table th {
            font-size: 13px;
        }
    }

    @media (max-width: 575px) {

        .cancellation-policy-table td,
        .cancellation-policy-table th {
            font-size: 12px;
        }
    }

    .star-rating1 {
        display: block;
        gap: 5px;
        font-size: 30px;
        cursor: pointer;
    }

    .star-rating1 i {
        color: #fe9802;
        transition: color 0.2s;
    }

    .star-rating1 i.filled {
        color: #fe9802;
    }

    .star-rating-display-contents {
        display: contents;
    }
</style>


<style>
    .chat-container {
        margin: 0 auto;
        border: 1px solid #ccc;
        border-radius: 10px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 500px;
    }

    .chat-header {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #fff;
        border-bottom: 1px solid #ccc;
    }


    .chat-box {
        padding: 10px;
        flex-grow: 1;
        overflow-y: auto;
        background-color: #f1f1f1;
    }

    .chat-input {
        display: flex;
        border-top: 1px solid #ccc;
        padding: 10px;
    }

    .chat-input input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 20px;
        outline: none;
    }

    .chat-input button {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        padding: 10px;
        margin-left: 10px;
        cursor: pointer;
    }

    .chat-input button i {
        font-size: 16px;
    }

    .chat-message {
        margin-bottom: 10px;
        padding: 10px;
        /* border-radius: 10px;
        max-width: 60%; */
        word-wrap: break-word;
    }

    .user-message {
        background-color: #ff9200;
        color: white;
        align-self: flex-end;
        text-align: right;
        border-radius: 8px;
    }

    .admin-message {
        background-color: #f1f1f1;
        color: black;
        align-self: flex-start;
        text-align: left;
    }
</style>
@endpush
@section('content')

<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
    <div class="row">
        @include('web-views.partials._profile-aside')
        <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
            <!-- <div class="card __card d-lg-flex web-direction customer-profile-orders"> -->
            <div class="card __card customer-profile-orders shadow-sm rounded">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 text-capitalize">
                                <h4 class="text-capitalize mb-0 mobile-fs-14 fs-18 font-bold">{{ translate('order') }} #{{ $VehicleOrders['order_id'] ?? '' }} </h4>
                                <?php
                                if (($VehicleOrders['status'] == 0 || $VehicleOrders['status'] == 1) && $VehicleOrders['pickup_status'] == 0) {
                                    $showClass = 'primary';
                                    $showName = 'Pending';
                                } elseif (($VehicleOrders['status'] == 0 || $VehicleOrders['status'] == 1) && $VehicleOrders['pickup_status'] == 0) {
                                    $showClass = 'primary';
                                    $showName = 'Processing';
                                } elseif (($VehicleOrders['status'] == 0 || $VehicleOrders['status'] == 1) && $VehicleOrders['pickup_status'] == 1 && $VehicleOrders['drop_status'] == 0) {
                                    $showClass = 'success';
                                    $showName = 'Pickup';
                                } elseif (($VehicleOrders['status'] == 0 || $VehicleOrders['status'] == 1) && $VehicleOrders['drop_status'] == 1) {
                                    $showClass = 'success';
                                    $showName = 'Completed';
                                } else {
                                    $showClass = 'danger';
                                    $showName = 'Refund';
                                }
                                ?>
                                <span
                                    class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $showClass }} fs-12 font-semibold text-capitalize">
                                    {{ $showName }}
                                </span>
                            </div>
                            <div class="date fs-12 font-semibold text-secondary-50 text-body mb-3 mt-2">
                                {{ date('d M, Y h:i A', strtotime($VehicleOrders['created_at'])) }}
                            </div>
                        </div>
                        <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z" fill="white" />
                            </svg>
                        </button>
                    </div>
                    <ul class="nav nav-tabs nav--tabs d-flex justify-content-start mt-3 border-top border-bottom py-2"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link __inline-27 active" href="#all_order" data-toggle="tab" role="tab">
                                {{ translate('order_summary') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#reviews" data-toggle="tab" role="tab">
                                {{ translate('reviews') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content px-lg-3">
                        <div class="tab-pane fade show active text-justify" id="all_order" role="tabpanel">
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
                                                                    <div class="_1 py-2 d-flex justify-content-between align-items-center">
                                                                        <h6 class="fs-13 font-bold text-capitalize">
                                                                            {{ translate('payment_info') }}
                                                                        </h6>
                                                                    </div>
                                                                    <div class="fs-12">
                                                                        <span
                                                                            class="text-muted text-capitalize">{{ translate('payment_status') }}</span>:
                                                                        <?php if ($VehicleOrders['status'] == 1) { ?>
                                                                            <span class="text-success text-capitalize">{{ translate('paid') }}</span>
                                                                        <?php } else { ?>
                                                                            <span class="text-success text-capitalize">{{ translate('unpaid') }}</span>
                                                                        <?php } ?>
                                                                    </div>
                                                                    <div class="mt-2 fs-12">
                                                                        <span
                                                                            class="text-muted text-capitalize">{{ translate('payment_method') }}</span>
                                                                        :<span class="text-primary text-capitalize">
                                                                            @if ($VehicleOrders['transaction_id'] == 'wallet')
                                                                            {{ translate('Wallet') }}
                                                                            @else
                                                                            {{ translate('online') }}
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                    @if ($VehicleOrders['refund_status'] != 0)
                                                                    <div class="mt-2 fs-12">
                                                                        <span
                                                                            class="text-muted text-capitalize">{{ translate('Refound_status') }}</span>
                                                                        :<span
                                                                            class="text-primary text-capitalize">{{ $VehicleOrders['refund_status'] == 1 ? 'Refunded' : ($VehicleOrders['refund_status'] == 3 ? 'refund Cancel' : 'refund proccess') }}</span>
                                                                    </div>
                                                                    @endif
                                                                    <div class="mt-2 fs-12">

                                                                        <small class="fs-13 font-bold text-capitalize">{{ translate('vehicle_info') }}</small>
                                                                        :
                                                                        <span>{{ $VehicleOrders['SelfCabData']['getCategory']['brand_name'] ?? '' }} | {{ $VehicleOrders['SelfCabData']['getCabId']['name'] ?? '' }} | {{ $VehicleOrders['SelfCabData']['getCabId']['seats'] ?? '' }} seats | {{ ucwords($VehicleOrders['SelfCabData']['car_type'] ?? '') }}</span>
                                                                        <br>
                                                                        <small
                                                                            class="fs-13 font-bold text-capitalize">{{ translate('pickup_date') }}</small>
                                                                        : <span>{{ date('d M, Y h:i A', strtotime($VehicleOrders['pickup_date'])) }}</span><br>
                                                                        <small
                                                                            class="fs-13 font-bold text-capitalize">{{ translate('drop_date') }}</small>
                                                                        : <span>{{ date('d M, Y h:i A', strtotime($VehicleOrders['droup_date'])) }}</span><br>
                                                                        @if($VehicleOrders['drop_status'] == 1)
                                                                        <small class="fs-13 font-bold text-capitalize">{{ translate('over_time') }}</small>
                                                                        : <span>{{ $VehicleOrders['ex_time']??"" }}</span><br>
                                                                        @endif
                                                                        <small class="fs-13 font-bold text-capitalize">{{ translate('pickup_location') }}</small>
                                                                        :
                                                                        <span>{{ $VehicleOrders['pickup_address'] ?? '' }}</span><br>
                                                                        <small
                                                                            class="fs-13 font-bold text-capitalize">{{ translate('booking_time') }}</small>
                                                                        :
                                                                        <span>{{ date('d M, Y h:i A', strtotime($VehicleOrders['created_at'])) }}</span><br>
                                                                    </div>
                                                                </div>
                                                                <!--  -->
                                                                @if ($VehicleOrders['traveller_id'] != 0)
                                                                <div
                                                                    class="mt-2 py-2 d-flex justify-content-between align-items-center">
                                                                    <small
                                                                        class="fs-13 font-bold text-capitalize">{{ translate('company_info') }}</small>
                                                                </div>
                                                                <div class="fs-12">
                                                                    <span
                                                                        class="text-muted text-capitalize">{{ translate('traveller_name') }}</span>:
                                                                    <span
                                                                        class="font-weight-bold text-capitalize">{{ $VehicleOrders['SelfCabData']['getTraveller']['company_name'] ?? '' }}</span>
                                                                </div>
                                                                @endif
                                                            </td>
                                                            <td class="order_table_td">
                                                                <div class="">
                                                                    <div class="py-2">
                                                                        <h6 class="fs-13 font-bold text-capitalize">
                                                                            {{ translate('User_info') }}:
                                                                        </h6>
                                                                    </div>
                                                                    <div class="">
                                                                        <span class="text-capitalize fs-12">
                                                                            <span class="text-capitalize">
                                                                                <span
                                                                                    class="min-w-60px">{{ translate('name') }}</span>
                                                                                :
                                                                                &nbsp;{{ $VehicleOrders['userData']['name'] ?? '' }}
                                                                            </span>
                                                                            <br>
                                                                            <span class="text-capitalize">
                                                                                <span
                                                                                    class="min-w-60px">{{ translate('phone') }}</span>
                                                                                :
                                                                                &nbsp;{{ $VehicleOrders['userData']['phone'] ?? '' }},
                                                                            </span>
                                                                            <br>
                                                                            <span style="text-transform: lowercase;">
                                                                                <span class="min-w-60px">{{ translate('Email') }}</span>:
                                                                                &nbsp;<span>{{ $VehicleOrders['userData']['email'] ?? '' }}</span>,
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <!-- <div class="payment mb-3 table-responsive d-none d-lg-block"> -->
                                            <div class="payment mb-3 table-responsive">
                                                <table class="table table-borderless min-width-600px">
                                                    <thead class="thead-light text-capitalize">
                                                        <tr class="fs-13 font-semibold">
                                                            <th class="px-5">{{ translate('vehicle_name') }}</th>
                                                            <th>{{ translate('sub_amount') }}</th>
                                                            <th>{{ translate('tax') }}</th>
                                                            <th>{{ translate('tax_amount') }}</th>
                                                            <th>{{ translate('price') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $VehicleOrders['SelfCabData']['getCabId']['name'] ?? '' }}</td>
                                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$VehicleOrders['price']) ), currencyCode: getCurrencyCode()) }}</td>
                                                            <td>{{ $VehicleOrders['tax'] ?? '' }}%</td>
                                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$VehicleOrders['tax_amount']) ), currencyCode: getCurrencyCode()) }}</td>
                                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($VehicleOrders['price'] + $VehicleOrders['tax_amount'])) ), currencyCode: getCurrencyCode()) }}</td>
                                                        </tr>
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
                                                                        <span
                                                                            class="font-semibold">{{ translate('item') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span
                                                                            class="font-semibold">{{ translate('Price') }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span
                                                                            class="product-qty">{{ translate('subtotal') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>

                                                                    <div class="text-end">
                                                                        <span class="fs-15 font-semibold">
                                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$VehicleOrders['price']) ), currencyCode: getCurrencyCode()) }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span class="product-qty" style="font-size: 13px;">{{ translate('total_tax') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$VehicleOrders['tax_amount']) ), currencyCode: getCurrencyCode()) }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span
                                                                            class="product-qty">{{ translate('coupon_discount') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span
                                                                            class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $VehicleOrders['coupan_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span
                                                                            class="product-qty">{{ translate('security_amount') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span
                                                                            class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $VehicleOrders['security_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span class="font-weight-bold">
                                                                            <strong>{{ translate('Paid_Amount') }}</strong>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span class="font-weight-bold amount">
                                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($VehicleOrders['price']?? 0) + ($VehicleOrders['tax_amount']?? 0) + ($VehicleOrders['security_amount'] ?? 0) - ($VehicleOrders['coupan_amount']?? 0) )), currencyCode: getCurrencyCode()) }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @if($VehicleOrders['drop_status'] == 1)
                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span
                                                                            class="product-qty">{{ translate('over_time_change') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $VehicleOrders['ex_change'] ?? 0), currencyCode: getCurrencyCode()) }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span
                                                                            class="product-qty">{{ translate('security deposit returned') }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span class="fs-15 font-semibold">-{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $VehicleOrders['security_deposit'] ?? 0), currencyCode: getCurrencyCode()) }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="border-top">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span class="font-weight-bold">
                                                                            <strong>{{ translate('final_Amount') }}</strong>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span class="font-weight-bold amount">
                                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)($VehicleOrders['price']?? 0) + ($VehicleOrders['tax_amount']?? 0) + ($VehicleOrders['security_amount'] ?? 0) - ($VehicleOrders['coupan_amount']?? 0) - ($VehicleOrders['security_deposit']?? 0)  )), currencyCode: getCurrencyCode()) }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            @if ($VehicleOrders['refund_status'] == 1)
                                                            <tr class="border-top text-danger">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <span class="font-weight-bold">
                                                                            <strong>{{ translate('refund_Price') }}</strong>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end">
                                                                        <span class="font-weight-bold amount">
                                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $VehicleOrders['refund_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            @if ($VehicleOrders['status'] == 1 && $VehicleOrders['pickup_status'] == 0 && $VehicleOrders['refund_status'] == 0)
                                                            <tr>
                                                                <td colspan="2">
                                                                    <button type="button" onclick="click_inquery()"
                                                                        class="btn btn-soft-danger btn-soft-border w-100 btn-sm text-danger font-semibold text-capitalize mt-3">
                                                                        {{ translate('cancel_order') }}
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade  text-justify" id="reviews" role="tabpanel">
                            <div class="col-12">
                                <div class="card-body bg-white border-lg rounded mobile-full">
                                    @php
                                    $getTourReview = \App\Models\SelfVehicleReview::where([
                                    'order_id' => $VehicleOrders['id'],
                                    'user_id' => $VehicleOrders['user_id']
                                    ])->first();
                                    @endphp
                                    @if (!$getTourReview || $getTourReview['is_edited'] == 0)
                                    <form action="{{ route('self-vehicle-review-update', $VehicleOrders['id']) }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $VehicleOrders['id'] }}">
                                        <input type="hidden" name="user_id" value="{{ $VehicleOrders['user_id'] }}">
                                        <input type="hidden" name="self_vehicle_id" value="{{ $VehicleOrders['vehicle_id'] }}">
                                        <div class="modal-body">
                                            <div class="form-group text-center">
                                                <label>{{ translate('Give_Your_Rating_&_Feedback') }}</label>
                                                <div class="star-rating1" id="starRating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="far fa-star" data-index="{{ $i }}"></i>
                                                        @endfor
                                                </div>
                                                <input type="hidden" name="rating" id="ratingInput" value="0" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">{{ translate('comment') }}</label>
                                                <textarea class="form-control" name="comment" placeholder="Write your comments here" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ URL::previous() }}"
                                                class="btn btn-secondary">{{ translate('back') }}</a>
                                            <button type="submit"
                                                class="btn btn--primary">{{ translate('submit') }}</button>
                                        </div>
                                    </form>
                                    @else
                                    <section class="rating__card text-center">
                                        <blockquote class="rating__card__quote">“{{ $getTourReview['comment'] }}”
                                        </blockquote>
                                        <div class="rating__card__stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <=$getTourReview['star'])
                                                <i class="fa fa-star star-rating text-warning star-rating-display-contents"></i>
                                                @else
                                                <i class="fa fa-star-o star-rating star-rating-display-contents"></i>
                                                @endif
                                                @endfor
                                                <br>
                                                <span
                                                    class="rating__card__stars__name">{{ $VehicleOrders['userData']['name']??"" }}</span>
                                        </div>
                                        <p class="rating__card__bottomText">
                                            {{ date('h:i A, d M Y', strtotime($getTourReview['created_at'])) }}
                                        </p>
                                    </section>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>
    </div>

</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-faded-info">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ translate('Send_Message_to_traveller') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="tour-view-chat-form">
                    @csrf
                    <input type="hidden" value="" class="tour-view-chat-order_id">
                    <input type="hidden" value="" class="tour-view-chat-user_id">
                    <textarea name="message" class="form-control min-height-100px max-height-200px tour-view-chat-msg" required
                        placeholder="{{ translate('Write_here') }}..."></textarea>
                    <br>
                    @php
                    $getSpecial_tour = \App\Models\SelfCancellationPolicy::where('status', 1)->orderBy('day', 'desc')->get();
                    $refund_amount = 0;
                    $pickupTimestamp = strtotime($VehicleOrders['pickup_date']);
                    @endphp

                    @if (!empty($getSpecial_tour) && count($getSpecial_tour) > 0)
                    @foreach ($getSpecial_tour as $val)
                    @php
                    $calculatedTimestamp = strtotime("-" . $val['day'] . " hours", $pickupTimestamp);
                    $currentTimestamp = strtotime(now());
                    @endphp
                    @if ($currentTimestamp <= $calculatedTimestamp)
                        @php
                        $refund_amount=($VehicleOrders['price'] * $val['percentage']) / 100;
                        break;
                        @endphp
                        @endif
                        @endforeach
                        @endif
                        <br>
                        <span class="font-weight-bold">Refund Amount :
                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $refund_amount ?? 0), currencyCode: getCurrencyCode()) }}
                        </span>
                        <br>
                        <br>
                        <div class="justify-content-end gap-2 d-flex flex-wrap">
                            <button type='button' class="btn btn--primary text-white self-vehicle-view-chat-submit">
                                {{ translate('send') }}
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')
<script>
    function click_inquery() {
        $('#exampleModal').modal('show');
    }

    $('.self-vehicle-view-chat-submit').click(function() {
        var msg = $('.tour-view-chat-msg').val();
        var order_id = "{{ $VehicleOrders['id'] }}";
        var user_id = "{{ $VehicleOrders['user_id'] }}";
        var amount = "{{ $refund_amount ?? 0 }}";
        if (!msg || msg.trim().length === 0) {
            toastr.error("{{ translate('Enter a Tour Cancel Resonance ') }}");
            return false;
        } else {
            $.ajax({
                url: "{{ route('create-ticket-self-vehicle') }}",
                data: {
                    msg,
                    order_id,
                    user_id,
                    amount,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                type: "post",
                success: function(data) {
                    window.location.href = ``;
                    $('#exampleModal').modal('hide');
                }
            })
        }
    })

    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('#starRating i');
        const ratingInput = document.getElementById('ratingInput');

        let currentRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));

                if (index === currentRating) {
                    currentRating = 0;
                } else {
                    currentRating = index;
                }

                ratingInput.value = currentRating;

                stars.forEach((s, i) => {
                    if (i < currentRating) {
                        s.classList.remove('far');
                        s.classList.add('fas', 'filled');
                    } else {
                        s.classList.remove('fas', 'filled');
                        s.classList.add('far');
                    }
                });
            });
        });
    });
</script>
@endpush