@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('offlinepooja_order_Details'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
    <style>
        .section-card {
            display: none;
        }

        #order-tracking-card {
            display: none;
        }

        .history-tl-container {
            font-family: "Roboto", sans-serif;
            width: 135%;
            margin: auto;
            display: block;
            position: relative;
        }

        .history-tl-container ul.tl {
            margin: 20px 0;
            padding: 0;
            display: inline-block;

        }

        .history-tl-container ul.tl li {
            list-style: none;
            margin: auto;
            margin-left: 120px;
            min-height: 50px;
            /*background: rgba(255,255,0,0.1);*/
            border-left: 1px dashed #0976ed;
            padding: 0 0 50px 45px;
            position: relative;
        }

        .history-tl-container ul.tl li:last-child {
            border-left: 0;
        }

        .history-tl-container ul.tl li::before {
            position: absolute;
            left: -10px;
            top: -5px;
            content: " ";
            border: 8px solid rgba(255, 255, 255, 0.74);
            border-radius: 500%;
            background: #1e4e82;
            height: 20px;
            width: 20px;
            transition: all 500ms ease-in-out;

        }

        .history-tl-container ul.tl li:hover::before {
            border-color: #0378cd;
            transition: all 1000ms ease-in-out;
        }

        ul.tl li .item-title {}

        ul.tl li .item-detail {
            color: rgba(0, 0, 0, 0.5);
            font-size: 12px;
        }

        ul.tl li .timestamp {
            color: #8D8D8D;
            position: absolute;
            width: 100px;
            left: -80%;
            text-align: right;
            font-size: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/vippooja.png') }}"
                    alt="">
                {{ translate('offlinepooja_order_Details') }}
            </h2>
        </div>

        <div class="row gy-3" id="printableArea">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                            <div class="d-flex flex-column gap-10">
                                <h4 class="text-capitalize">{{ translate('Order_ID') }} #{{ $details['order_id'] }}</h4>
                                <div class=""><strong>Order Date:</strong>
                                    {{ date('d F Y', strtotime($details['created_at'])) }}
                                </div>
                            </div>
                            <div class="text-sm-right flex-grow-1">
                                @if ($details['payment_status']==1)
                                <div class="d-flex flex-wrap gap-10 justify-content-end">
                                    <a class="btn btn--primary px-4" target="_blank"
                                        href="{{ route('admin.offlinepooja.order.generate.invoice', $details['order_id']) }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}"
                                            alt="" class="mr-1">
                                        {{ translate('print_Invoice') }}
                                    </a>
                                </div>
                                @endif
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{ translate('order_Status') }}: </span>
                                        <span
                                            class="badge badge-{{ $details['status'] == 0 ? 'primary' : ($details['status'] == 1 ? 'success' : ($details['status'] == 2 ? 'danger' : ($details['status'] == 3 ? 'warning' : 'info'))) }} font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{ $details['status'] == 0 ? 'Pending' : ($details['status'] == 1 ? 'Completed' : ($details['status'] == 2 ? 'Canceled' : ($details['status'] == 3 ? 'Scheduled' : 'Live URL'))) }}</span>
                                    </div>

                                    <div class="payment-method d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{ translate('payment_Method') }} :</span>
                                        <strong>{{ translate('online') }}</strong>
                                    </div>

                                    <div class="d-flex justify-content-sm-end gap-10">
                                        <span class="title-color">{{ translate('payment_Status') }}:</span>
                                        @if ($details->status != 2)
                                            <span class="{{$details->payment_status==1?'text-success':'text-danger'}} payment-status-span font-weight-bold">
                                                {{ translate($details->payment_status==1?($details->remain_amount_status == 1 ? 'full_Paid' : 'partially_Paid'):'Unpaid') }}
                                            </span>
                                        @else
                                            <span class="text-danger payment-status-span font-weight-bold">
                                                {{ translate($details->refund_status == 0 ? 'not_refunded' : 'refunded') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span
                                class="text-danger">{{ $details->is_edited == 0 ? '(Note:- Venue Detail is not available)' : '' }}</span>
                        </div>

                        <div class="table-responsive datatable-custom">
                            <table
                                class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('pooja_name') }}</th>
                                        <th>{{ translate('package_name') }}</th>
                                        <th>{{ translate('price') }}</th>
                                        <th>{{ translate('paid_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="media align-items-center gap-10">
                                                <img class="avatar avatar-60 rounded"
                                                    src="{{ getValidImage(path: 'storage/app/public/offlinepooja/thumbnail/' . $details['offlinePooja']['thumbnail'], type: 'backend-product') }}"
                                                    alt="{{ translate('image_Description') }}">
                                                <div>
                                                    <h6 class="title-color">
                                                        {{ substr($details['offlinePooja']['name'], 0, 40) }}{{ strlen($details['offlinePooja']['name']) > 10 ? '...' : '' }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-capitalize">{{ $details['package']['title'] }}</span>
                                        </td>
                                        <td>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['package_main_price']), currencyCode: getCurrencyCode()) }}
                                        </td>
                                        <td>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['pay_amount']), currencyCode: getCurrencyCode()) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="offset-md-6 col-md-6 mt-3">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td>{{ translate('pooja_Price') }}</td>
                                        <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['package_main_price']), currencyCode: getCurrencyCode()) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('amount_Paid') }}</td>
                                        <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['pay_amount']), currencyCode: getCurrencyCode()) }}
                                        </td>
                                    </tr>
                                    @if ($details->status != 2)
                                        <tr>
                                            <td>{{ translate('remaining_Amount') }}</td>
                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['remain_amount']), currencyCode: getCurrencyCode()) }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ translate('refund_Amount') }}</td>
                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['refund_amount']), currencyCode: getCurrencyCode()) }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if ($details['schedule_status'] == 1 && $details['status'] == 0)
                            <div class="mt-3 text-end">
                                <p class="text-success">Pooja has been schedule to
                                    <strong>{{ date('d F Y', strtotime($details['booking_date'])) }}</strong> with amount
                                    <strong>₹{{ $details->schedule_amount }}</strong>
                                </p>
                            </div>
                        @endif
                        @if ($details['status'] == 2 && $details['refund_status'] == 0)
                            <p class="text-danger m-2">Pooja has been canceled and amount
                                <strong>₹{{ $details['refund_amount'] }}</strong> pending for refunded.
                            </p>
                        @elseif ($details['status'] == 2 && $details['refund_status'] == 1)
                            <p class="text-success m-2">Pooja has been canceled and amount
                                <strong>₹{{ $details['refund_amount'] }}</strong> has been refunded to customer wallet
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        @if (Helpers::modules_permission_check('OfflinePooja Order', 'Detail', 'assign-pandit'))
                        <div class="d-flex align-items-center justify-content-between gap-2">

                            <h4 class="mb-0">
                                <i class="tio-group-equal"></i>
                                {{ translate(empty($details['pandit_assign']) ? 'assign_Pandit' : 'pandit_information') }}
                            </h4>
                            @if (!empty($details['pandit_assign']) && $details['status'] == 0)
                                <button class="btn btn-outline-primary btn-sm square-btn" data-toggle="modal"
                                    data-target="#change-pandit-modal">
                                    <i class="tio-edit"></i>
                                </button>
                            @endif
                        </div>
                        {{-- @endif --}}
                        @if (empty($details['pandit_assign']))
                            {{-- @if (Helpers::modules_permission_check('Vip Order', 'Detail', 'assign-pandit')) --}}
                            @if ($details->status != 2)
                                <div class="">
                                    <label
                                        class="font-weight-bold title-color fz-14">{{ translate('assign_Pandit_ji') }}</label>

                                    <select name="astrologer_type" id="astrologer-type"
                                        class="astrologer-type form-control" {{ translate($details->payment_status==0?'disabled':'') }}>
                                        <option value="in house">In house</option>
                                        <option value="freelancer">Freelancer</option>
                                    </select>

                                    <br>
                                    <div class="" id="in-house">
                                        <label
                                            class="font-weight-bold title-color fz-14">{{ translate('assign_Pandit') }}</label>
                                        <select name="assign_pandit" id="assign-pandit" class="assign-pandit form-control" {{ translate($details->payment_status==0?'disabled':'') }}>
                                            <option value="" selected disabled>Select Pandit</option>
                                            @if (count($inHouseAstrologers) > 0)

                                                @foreach ($inHouseAstrologers as $inhouse)
                                                    @php
                                                        $checkastro = \App\Models\OfflinePoojaOrder::where(
                                                            'pandit_assign',
                                                            $inhouse->id,
                                                        )
                                                            ->where('booking_date', $details->booking_date)
                                                            ->count();
                                                    @endphp
                                                    @if ($inhouse['is_pandit_pooja_per_day'] > $checkastro)
                                                        <option value="{{ $inhouse['id'] }}">{{ $inhouse['name'] }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @else
                                                <option disabled>No Pandit Found</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="" id="freelancer" style="display: none;">
                                        <label
                                            class="font-weight-bold title-color fz-14">{{ translate('freelancer_Astrologer') }}</label>
                                        <select name="assign_pandit" id="assign-pandit" class="assign-pandit form-control">
                                            <option value="" selected disabled>Select Pandit</option>
                                            @if (count($freelancerAstrologers) > 0)
                                                @foreach ($freelancerAstrologers as $freelancer)
                                                    {{-- pandit price for pooja --}}
                                                    @php
                                                        $price =
                                                            json_decode($freelancer['is_pandit_offlinepooja'], true)[
                                                                $details->service_id
                                                            ] ?? 0;
                                                        $checkastro = \App\Models\OfflinePoojaOrder::where(
                                                            'pandit_assign',
                                                            $freelancer->id,
                                                        )
                                                            ->where('booking_date', $details->booking_date)
                                                            ->count();
                                                    @endphp

                                                    {{-- pandit distance from user address --}}
                                                    @php
                                                        $distance = 'unknown km';
                                                        if (
                                                            $freelancer['latitude'] &&
                                                            $freelancer['longitude'] &&
                                                            $details['latitude'] &&
                                                            $details['longitude']
                                                        ) {
                                                            $earthRadius = 6371;

                                                            // Convert degrees to radians
                                                            $lat1 = deg2rad($details['latitude']);
                                                            $lon1 = deg2rad($details['longitude']);
                                                            $lat2 = deg2rad($freelancer['latitude']);
                                                            $lon2 = deg2rad($freelancer['longitude']);

                                                            // Differences in coordinates
                                                            $deltaLat = $lat2 - $lat1;
                                                            $deltaLon = $lon2 - $lon1;

                                                            // Haversine formula
                                                            $a =
                                                                sin($deltaLat / 2) * sin($deltaLat / 2) +
                                                                cos($lat1) *
                                                                    cos($lat2) *
                                                                    sin($deltaLon / 2) *
                                                                    sin($deltaLon / 2);
                                                            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                                                            $distance = round($earthRadius * $c, 2);
                                                        }
                                                    @endphp
                                                    @if ($freelancer['is_pandit_pooja_per_day'] > $checkastro)
                                                        <option value="{{ $freelancer['id'] }}">
                                                            {{ $freelancer['name'] . ' (₹' . $price . ')' . ' (' . $distance . ' km )' }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @else
                                                <option disabled>No Pandit Found</option>
                                            @endif
                                        </select>
                                    </div>
                                    <form
                                        action="{{ route('admin.offlinepooja.order.assign.pandit', [$details['order_id']]) }}"
                                        method="post" id="assign-pandit-form">
                                        @csrf
                                        <input type="hidden" name="booking_date" id="booking_id"
                                            value="{{ $details->booking_date }}">
                                        <input type="hidden" name="service_id" id="service_id"
                                            value="{{ $details->service_id }}">
                                        <input type="hidden" name="pandit_id" id="pandit-id-val">
                                    </form>

                                    {{-- <select name="assign_pandit" id="assign-pandit" class="assign-pandit form-control">
                                    <option value="" selected disabled>Select Pandit Ji</option>
                                    @if (count($inHouseAstrologers) > 0)
                                        @foreach ($inHouseAstrologers as $inhouse)
                                            @php
                                                $checkastro = \App\Models\OfflinePoojaOrder::where(
                                                    'pandit_assign',
                                                    $inhouse->id,
                                                )
                                                    ->where('booking_date', $details->booking_date)
                                                    ->count();
                                            @endphp
                                            @if ($inhouse['is_pandit_pooja_per_day'] > $checkastro)
                                                <option value="{{ $inhouse['id'] }}">{{ $inhouse['name'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option disabled>No Astrologer Found</option>
                                    @endif
                                </select>
                                <form action="{{ route('admin.offlinepooja.order.assign.pandit', [$details['id']]) }}"
                                    method="post" id="assign-pandit-form">
                                    @csrf
                                    <input type="hidden" name="booking_date" id="booking_id"
                                        value="{{ $details->booking_date }}">
                                    <input type="hidden" name="service_id" id="service_id"
                                        value="{{ $details->service_id }}">
                                    <input type="hidden" name="pandit_id" id="pandit-id-val">
                                </form> --}}
                                </div>
                            @else
                                <strong class="text-danger text-center">No pandit selected</strong>
                            @endif
                            {{-- @endif --}}
                        @else
                            <div>
                                @if (!empty($details['pandit']))
                                    <div class="media flex-wrap gap-3">
                                        <div class="">
                                            <img class="avatar rounded-circle avatar-70"
                                                src="{{ getValidImage(path: 'storage/app/public/astrologers/' . $details['pandit']['image'], type: 'backend-basic') }}"
                                                alt="{{ translate('Image') }}">
                                        </div>
                                        <div class="media-body d-flex flex-column gap-1">
                                            <span class="title-color"><i class="tio-user"></i>
                                                :<strong>{{ $details['pandit']['name'] }}
                                                </strong></span>
                                            <span class="title-color break-all"><i class="tio-call"></i>
                                                :<strong>{{ $details['pandit']['mobile_no'] }}</strong></span>
                                            <span class="title-color break-all"
                                                style="text-transform: lowercase !important;"><i class="tio-email"></i> :
                                                <strong>
                                                    {{ $details['pandit']['email'] }}</strong></span>
                                        </div>
                                    </div>
                                @else
                                    <p>Pandit Detail Not Available</p>
                                @endif
                            </div>
                        @endif
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center"><i class="tio-shopping-cart-outlined nav-icon"></i>
                                {{ translate('Pooja_order_status') }}</h4>
                        </div>

                        @if ($details['status'] == 0 || $details['status'] == 3 || $details['status'] == 4)
                            @if (Helpers::modules_permission_check('OfflinePooja Order', 'Detail', 'order-status'))
                            <div class="">
                                <label
                                    class="font-weight-bold title-color fz-14">{{ translate('change_order_status') }}</label>
                                <select name="order_status" id="order_status" class="order-status form-control"
                                    data-id="{{ $details['id'] }}">
                                    @if ($details['status'] == 0)
                                        <option value="0" {{ $details['status'] == 0 ? 'selected' : '' }}>
                                            {{ translate('pending') }}</option>
                                    @endif
                                    @if ($details['remain_amount_status'] == 1)
                                        @if ($details['pooja_method'] == 'online' && $details['pooja_venue_type'] == 'temple')
                                            @if ($details['status'] == 0 || $details['status'] == 3)
                                                <option value="3" {{ $details['status'] == 3 ? 'selected' : '' }}>
                                                    {{ translate('schedule') }}</option>
                                            @endif
                                            @if ($details['status'] == 3 || $details['status'] == 4)
                                                <option value="4" {{ $details['status'] == 4 ? 'selected' : '' }}>
                                                    {{ translate('live_url') }}</option>
                                            @endif
                                        @endif
                                        {{-- @if ($details['booking_date'] <= date('Y-m-d')) --}}
                                            <option value="1" {{ $details['status'] == 1 ? 'selected' : '' }}>
                                                {{ translate('complete') }}</option>
                                        {{-- @endif --}}
                                    @endif
                                    <option value="2" {{ $details['status'] == 2 ? 'selected' : '' }}>
                                        {{ translate('cancel') }} </option>
                                </select>
                                <form action="{{ route('admin.offlinepooja.order.status', [$details['order_id']]) }}"
                                    method="post" id="order-status-form">
                                    @csrf
                                    <input type="hidden" name="booking_date" id="booking_id"
                                        value="{{ $details->booking_date }}">
                                    <input type="hidden" name="service_id" id="service-id"
                                        value="{{ $details->service_id }}">
                                    <input type="hidden" name="package_id" id="package_id"
                                        value="{{ $details->package_id }}">
                                    <input type="hidden" name="order_status" id="order-status-val">
                                </form>
                            </div>
                            @endif
                        @else
                            <div class="text-center">
                                <span
                                    class="badge badge-{{ $details['status'] == 1 ? 'success' : ($details['status'] == 2 ? 'danger' : 'warning') }}"
                                    style="font-size: 18px;">
                                    {{ $details['status'] == 1 ? 'Completed' : ($details['status'] == 2 ? 'Canceled' : 'Pending') }}
                                </span>


                            </div>

                            <div class="text-center">
                                <img src="{{ !empty($details['pooja_certificate']) ? asset('public/assets/back-end/img/certificate/offlinepooja/' . $details['pooja_certificate']) : '' }}"
                                    alt="" width="150">
                            </div>
                        @endif
                    </div>
                </div>

                @if (!empty($details['time_schedule']))
                    <div class="card">
                        <div class="card-body text-capitalize d-flex flex-column gap-4">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h4 class="mb-0 text-center"><i class="tio-shopping-cart-outlined nav-icon"></i>
                                    {{ translate('time_schedule') }}</h4>
                            </div>

                            <div class="text-center">
                                <h4 class="font-weight-bold text-dark">{{ translate('time : ') }}
                                    {{ $details['time_schedule'] }}</h4> <br>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!empty($details['live_url']) )
                    <div class="card">
                        <div class="card-body text-capitalize d-flex flex-column gap-4">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h4 class="mb-0 text-center"><i class="tio-shopping-cart-outlined nav-icon"></i>
                                    {{ translate('live_URL') }}</h4>
                            </div>

                            <div class="text-center">
                                <h4 class="font-weight-bold text-dark">{{ translate('URL : ') }}
                                    <span style="text-transform: lowercase !important" >{{ $details['live_url'] }}</span></h4> <br>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($details['status'] == 2)
                    <div class="card">
                        <div class="card-body text-capitalize d-flex flex-column gap-4">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h4 class="mb-0 text-center"><i class="tio-shopping-cart-outlined nav-icon"></i>
                                    {{ translate('refund_status') }}</h4>
                            </div>

                            @if ($details['refund_status'] == 0)
                                @if (Helpers::modules_permission_check('OfflinePooja Order', 'Detail', 'refund'))
                                <div class="text-center">
                                    <h4 class="font-weight-bold title-color m-0">{{ translate('refund_amount') }}
                                        ₹{{ $details['refund_amount'] }}</h4> <br>
                                    <a href="{{ route('admin.offlinepooja.order.refund.amount', [$details['order_id']]) }}"
                                        class="btn btn-danger">Refund</a>
                                </div>
                                @endif
                            @else
                                <div class="text-center">
                                    <h4 class="font-weight-bold text-success">{{ translate('amount_refunded') }}
                                        ₹{{ $details['refund_amount'] }}</h4> <br>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (!empty($details['customers']))
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                                <h4 class="d-flex gap-2">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}"
                                        alt="">
                                    {{ translate('customer_information') }}
                                </h4>
                            </div>
                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img class="avatar rounded-circle avatar-70"
                                        src="{{ getValidImage(path: 'storage/app/public/profile/' . $details['customers']['image'], type: 'backend-basic') }}"
                                        alt="{{ translate('Image') }}">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span class="title-color text-capitalize">Name:<strong>{{ $details['customers']['f_name'] . ' ' . $details['customers']['l_name'] }}
                                        </strong></span>
                                    <span
                                        class="title-color break-all">Contact:<strong>{{ $details['customers']['phone'] }}</strong></span>
                                    @if (str_contains($details['customers']['email'], '.com'))
                                        <span
                                            class="title-color break-all">Email:<strong>{{ $details['customers']['email'] }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                            <h4 class="d-flex gap-2">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}"
                                    alt="">
                                {{ translate('pooja_detail') }}
                            </h4>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class=""><span>{{ translate('method-') }}</span></td>
                                        <td><strong>{{ $details['pooja_method'] ? $details['pooja_method'] : 'NA' }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class=""><span>{{ translate('date-') }}</span></td>
                                        <td><strong>{{ $details['booking_date'] ? date('d F Y', strtotime($details['booking_date'])) : 'NA' }}</strong>
                                        </td>
                                    </tr>
                                    @if ($details->pooja_venue_type == 'temple')
                                        <tr>
                                            <td class=""><span>{{ translate('temple-') }}</span></td>
                                            <td><strong>{{ $details->temple ? $details->temple->name : 'NA' }}</strong>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class=""><span>{{ translate('state-') }}</span></td>
                                            <td><strong>{{ $details['state'] ?? 'NA' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class=""><span>{{ translate('city-') }}</span></td>
                                            <td><strong class="text-capitalize">{{ $details['city'] ?? 'NA' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><span>{{ translate('address-') }}</span></td>
                                            <td><strong
                                                    class="text-capitalize">{{ $details['venue_address'] ?? 'NA' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><span>{{ translate('landmark-') }}</span></td>
                                            <td><strong
                                                    class="text-capitalize">{{ $details['landmark'] ?? 'NA' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><span>{{ translate('pincode-') }}</span></td>
                                            <td><strong>{{ $details['pincode'] ?? 'NA' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class=""><span>{{ translate('latitude-') }}</span></td>
                                            <td><strong
                                                    class="text-capitalize">{{ $details['latitude'] ?? 'NA' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><span>{{ translate('longitude-') }}</span></td>
                                            <td><strong
                                                    class="text-capitalize">{{ $details['longitude'] ?? 'NA' }}</strong>
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

        {{-- change-pandit-modal --}}
        <div class="modal fade" id="change-pandit-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Pandit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <label class="font-weight-bold title-color fz-14">{{ translate('type') }}</label>
                            <select id="astrologer-type-change" class="form-control">
                                <option value="in house">In house</option>
                                <option value="freelancer">Freelancer</option>
                            </select>
                            <br>
                            <div class="" id="in-house-change">
                                <label
                                    class="font-weight-bold title-color fz-14">{{ translate('inhouse_Pandit_ji') }}</label>
                                <select id="assign-astrologer-change" class="change-pandit form-control">
                                    <option value="" selected disabled>Select Pandit Ji</option>
                                    @if (count($inHouseAstrologers) > 0)
                                        @foreach ($inHouseAstrologers as $inhouse)
                                            @if ($inhouse['id'] != $details['pandit_assign'])
                                                @if ($inhouse['is_pandit_pooja_per_day'] > $inhouse['ordercount'])
                                                    <option value="{{ $inhouse['id'] }}">
                                                        {{ $inhouse['name'] }}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @else
                                        <option disabled>No Pandit Found</option>
                                    @endif
                                </select>
                            </div>
                            <div class="" id="freelancer-change" style="display: none;">
                                <label
                                    class="font-weight-bold title-color fz-14">{{ translate('freelancer_Pandit_ji') }}</label>
                                <select id="assign-astrologer-change" class="change-pandit form-control">
                                    <option value="" selected disabled>Select Pandit Ji</option>
                                    @if (count($freelancerAstrologers) > 0)
                                        @foreach ($freelancerAstrologers as $freelancer)
                                            @if ($freelancer['id'] != $details['pandit_assign'])
                                                @if ($freelancer['is_pandit_pooja_per_day'] > $freelancer['ordercount'])
                                                    <option value="{{ $freelancer['id'] }}">{{ $freelancer['name'] }}
                                                    </option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @else
                                        <option disabled>No Pandit Found</option>
                                    @endif
                                </select>
                            </div>
                            <form action="{{ route('admin.offlinepooja.order.assign.pandit', [$details['order_id']]) }}"
                                method="post" id="change-pandit-form">
                                @csrf
                                <input type="hidden" name="booking_date" id="booking_id"
                                    value="{{ $details->booking_date }}">
                                <input type="hidden" name="service_id" id="service_id"
                                    value="{{ $details->service_id }}">
                                <input type="hidden" name="pandit_id" id="change-pandit-id-val">
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        {{-- <button type="submit" class="btn btn-primary">Change</button> --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- order-schedule-modal --}}
        <div class="modal fade" id="order-schedule-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Schedule Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.offlinepooja.order.status_times', [$details->order_id]) }}"
                        method="post" id="pooja-schedule-form">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="package_id" value="{{ $details->package_id }}">
                            <input type="hidden" name="booking_date" value="{{ $details->booking_date }}">
                            <input type="hidden" name="service_id" value="{{ $details->service_id }}">
                            <input type="hidden" name="customer_id" value="{{ $details->customer_id }}">
                            <div class="mb-2">
                                <label for="time-schedule" class="form-label">Time Schedule</label>
                                <input type="time" name="time_schedule" id="time-schedule" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- order-live-url-modal --}}
        <div class="modal fade" id="order-live-url-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Live Url Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.offlinepooja.order.live_streams', [$details->order_id]) }}"
                        method="post" id="pooja-live-url-form">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="package_id" value="{{ $details->package_id }}">
                            <input type="hidden" name="booking_date" value="{{ $details->booking_date }}">
                            <input type="hidden" name="service_id" value="{{ $details->service_id }}">
                            <input type="hidden" name="customer_id" value="{{ $details->customer_id }}">
                            <div class="mb-2">
                                <label for="live-url" class="form-label">Live Url</label>
                                <input type="url" name="live_url" id="live-url" class="form-control"
                                    placeholder="Enter url" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- order-cancel-modal --}}
        <div class="modal fade" id="order-cancel-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.offlinepooja.order.cancel_poojas', [$details->order_id]) }}"
                        method="post" id="pooja-cancel-form">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="package_id" id="package_id"
                                value="{{ $details->package_id }}">
                            <input type="hidden" name="booking_date" id="booking_id"
                                value="{{ $details->booking_date }}">
                            <input type="hidden" name="service_id" id="service-id"
                                value="{{ $details->service_id }}">
                            <input type="hidden" name="customer_id" value="{{ $details->customer_id }}">
                            <input type="hidden" name="order_status" id="order-cancel-status">
                            <input type="hidden" name="refund_amount" value="{{ $details->pay_amount }}">
                            {{-- <input type="hidden" name="schedule_amount" value="{{ $details->schedule_amount }}"> --}}
                            <textarea name="cancel_reason" cols="5" class="form-control" placeholder="Enter cancel reason" required></textarea>
                            <div class="row">
                                <div class="offset-md-7 col-md-5">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td colspan="2">Refund Payment</td>
                                            </tr>
                                            <tr>
                                                <td>Pooja Price</td>
                                                <td>{{ '₹' . $details->package_main_price }}</td>
                                            </tr>
                                            <tr>
                                                <td>User Paid</td>
                                                <td>{{ '₹' . $details->pay_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td>Refund Amount</td>
                                                <td>₹{{ $details->pay_amount }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endsection

    @push('script')
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ getWebConfig('map_api_key') }}&callback=map_callback_fucntion&libraries=places&v=3.49"
            defer></script>
        {{-- <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/order.js') }}"></script> --}}

        {{-- Change Astrologer --}}
        <script>
            $('#astrologer-type').change(function(e) {
                e.preventDefault();
                var type = $(this).val();
                console.log(type);
                if (type == 'in house') {
                    $('#in-house').show();
                    $('#freelancer').hide();
                } else if (type == 'freelancer') {
                    $('#in-house').hide();
                    $('#freelancer').show();
                }
            });

            $('#astrologer-type-change').change(function(e) {
                e.preventDefault();

                var type = $(this).val();
                if (type == 'in house') {
                    $('#in-house-change').show();
                    $('#freelancer-change').hide();
                } else if (type == 'freelancer') {
                    $('#in-house-change').hide();
                    $('#freelancer-change').show();
                }
            });
        </script>

        {{-- status change --}}
        <script>
            $('.order-status').on('change', function() {
                var orderStatus = $(this).val();
                $('#order-status-val').val(orderStatus);
                if (orderStatus == 1) {
                    Swal.fire({
                        title: 'Are You Sure To change status',
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            $('#order-status-form').submit();
                        }
                    });
                } else if (orderStatus == 2) {
                    $('#order-cancel-status').val(orderStatus);
                    $('#order-cancel-modal').modal('show');
                } else if (orderStatus == 3) {
                    $('#order-schedule-modal').modal('show');
                } else if (orderStatus == 4) {
                    $('#order-live-url-modal').modal('show');
                }
            });
        </script>

        {{-- pandit assign --}}
        <script>
            $('.assign-pandit').on('change', function() {
                var panditId = $(this).val();
                $('#pandit-id-val').val(panditId);
                Swal.fire({
                    title: 'Are You Sure To Assign Pandit',
                    type: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $('#assign-pandit-form').submit();
                    }
                });
            });
        </script>

        {{-- change pandit modal --}}
        <script>
            $('.change-pandit').on('change', function() {
                var panditId = $(this).val();
                $('#change-pandit-id-val').val(panditId);
                Swal.fire({
                    title: 'Are You Sure To Change Pandit',
                    type: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $('#change-pandit-form').submit();
                    }
                });
            });
        </script>
        {{-- Schedual Time Assing --}}

        <script>
            $(document).ready(function() {
                $('#toggle-card').click(function() {
                    $('#order-tracking-card').toggle();
                    $('#toggle-icon').toggleClass('ti-caret-up');
                });
            });
        </script>
    @endpush
