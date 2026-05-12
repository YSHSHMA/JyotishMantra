@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('Anushthan_order_Details'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .section-card {
            display: none;
        }

        #order-tracking-card {
            display: none;
        }

        #bar-progress {
            width: 100%;
            display: inline-flex;
            justify-content: center;
        }

        #bar-progress .step {
            display: inline-block;
        }

        #bar-progress .step .number-container {
            display: inline-block;
            border: solid 1px #0177cd;
            border-radius: 50%;
            width: 24px;
            height: 24px;
        }

        #bar-progress .step.step-active .number-container {
            background-color: #0177cd;
        }

        #bar-progress .step .number-container .number {
            font-weight: 700;
            font-size: .8em;
            line-height: 1.75em;
            display: block;
            text-align: center;
        }

        #bar-progress .step.step-active .number-container .number {
            color: white;
        }

        #bar-progress .step h5 {
            display: inline;
            font-weight: 100;
            font-size: .8em;
            margin-left: 10px;
            text-transform: uppercase;
        }

        #bar-progress .seperator {
            display: block;
            width: 20px;
            height: 1px;
            background-color: rgba(0, 0, 0, .2);
            margin: auto 20px;
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

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .dateBooking {
            animation: blink 1s infinite;
            color: red;
        }

        /* Ribbon style */
        /* Parent container must be relative */
        .position-relative {
            position: relative;
        }

        /* Ribbon Style */
        .anushthan-container {
            position: relative;
            display: inline-block;
            overflow: hidden;
        }


        .anushthan-image {
            border: 3px solid #ff4b2b;
            border-radius: 8px;
            animation: fadeInUp 0.8s ease;
            z-index: 1;
        }

        .vip-image {
            border: 3px solid #ff4b2b;
            border-radius: 8px;
            animation: fadeInUp 0.8s ease;
            z-index: 1;
        }

        .vip-container {
            position: relative;
        }

        .vip-edit-badge {
            position: absolute;
            top: 8px;  
            right: 8px;  
            z-index: 10;
            padding: 4px 6px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                {{ translate('order_Details') }}
            </h2>
        
            @php
                // Define all possible statuses with their display text
                $baseStatuses = [
                    0 => 'PENDING',
                    3 => 'SCHEDULE', 
                    4 => 'LIVE',
                    5 => 'SHARE',
                    1 => 'COMPLETE',
                    2 => 'CANCEL',
                    6 => 'REJECT',
                ];
        
                // Prasad-specific statuses
                $prasadStatuses = [
                    'confirmed' => 'Confirmed',
                    'processing' => 'Processing',
                    'in-transit' => 'In Transit',
                    'out_for_pickup' => 'Out for Pickup',
                    'delivered' => 'Delivered',
                    'cancel' => 'Cancelled',
                    'rejected' => 'Rejected'
                ];
        
                // Filter statuses based on current order status
                $filteredStatuses = [];
                
                switch ($details->order_status) {
                    case 1: // COMPLETE
                        $filteredStatuses = array_intersect_key($baseStatuses, array_flip([0, 3, 4, 5, 1]));
                        if ($details['is_prashad'] == 1) {
                            $filteredStatuses += array_intersect_key($prasadStatuses, array_flip(['confirmed', 'processing', 'in-transit', 'out_for_pickup', 'delivered']));
                        }
                        break;
                        
                    case 2: // CANCEL
                        $filteredStatuses = array_intersect_key($baseStatuses, array_flip([0, 3, 4, 5, 2]));
                        if ($details['is_prashad'] == 1) {
                            $filteredStatuses += array_intersect_key($prasadStatuses, array_flip(['confirmed', 'processing', 'cancel']));
                        }
                        break;
                        
                    case 6: // REJECT
                        $filteredStatuses = array_intersect_key($baseStatuses, array_flip([0, 6]));
                        if ($details['is_prashad'] == 1) {
                            $filteredStatuses += array_intersect_key($prasadStatuses, array_flip(['confirmed', 'rejected']));
                        }
                        break;
                        
                    default: // PENDING, SCHEDULE, LIVE, SHARE
                        $filteredStatuses = $baseStatuses;
                        if ($details['is_prashad'] == 1) {
                            $filteredStatuses += $prasadStatuses;
                        }
                        break;
                }
            @endphp
        
            {{-- Progress Bar --}}
            <div id="bar-progress" class="d-flex align-items-center gap-3 pt-4 flex-wrap">
                @foreach ($filteredStatuses as $statusKey => $statusText)
                    @php
                        $isActive = ($details->order_status == $statusKey || 
                                    $details->current_status === $statusKey ||
                                    (is_string($statusKey) && $details->prasad_status === $statusKey));
                                    
                    @endphp
                    
                    <div class="step {{ $isActive ? 'step-active' : '' }}">
                        <span class="number-container">
                            <span class="number">{{ $loop->iteration }}</span>
                        </span>
                        <h5 class="mb-0">{{ $statusText }}</h5>
                    </div>
                    
                    @if (!$loop->last)
                        <div class="seperator"></div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="row gy-3" id="printableArea">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                            <div class="invoice-header mb-4">
                                <h6 class="dateBooking mb-1 text-capitalize text-danger">
                                    Booking Date: {{ date('d M, Y', strtotime($details['booking_date'])) }}</h6>
                                <h2 class="fw-bold text-uppercase mb-2">{{ $details['vippoojas']['name'] }}</h2>
                                <h5 class="mb-1">OrderId :{{ $details['order_id'] }} + {{ \App\Models\Service_order::where('type', 'anushthan')->where('package_id',$details->package_id)->where('booking_date',$details['booking_date'])->where('status',$details['status'])->where('order_status',$details['order_status'])->where('service_id',$details['service_id'])->count() }}</h5>
                                <h5 class="mb-1 text-capitalize">Package Details :{{ $details['packages']['title'] }} /
                                    ₹{{ number_format($details['package_price'], 2) }}</h5>
                                @if (!empty($details['members']))
                                    @foreach (json_decode($details['members']) as $key => $item)
                                        <h6 class="mb-1 text-capitalize">Member Name: <strong>{{ $item }}</strong></h6>
                                    @endforeach
                                    @else
                                    <h6 class="mb-1 text-capitalize">Member: <strong>No Members</strong></h6>
                                @endif
                                @if (!empty($details['gotra']))
                                    <h6 class="mb-1 text-capitalize">Gotra: <strong>{{ $details['gotra'] }}</strong></h6>
                                @else
                                    <h6 class="mb-1 text-capitalize">Gotra: <strong>No Gotra</strong></h6>
                                @endif
                            </div>
                            <div class="text-sm-right flex-grow-1">
                                @if (Helpers::modules_permission_check('Anushthan Order', 'Detail', 'print-invoice'))
                                    <div class="d-flex flex-wrap gap-10 justify-content-end">
                                        <a class="btn btn--primary px-4" target="_blank"
                                            href="{{ route('admin.anushthan.order.generate.invoice', $details['id']) }}">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}"
                                                alt="" class="mr-1">
                                            {{ translate('print_Invoice') }}
                                        </a>
                                    </div>
                                @endif
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{ translate('status') }}: </span>
                                        <span
                                            class="badge badge-soft-{{ $details->order_status == 0
                                                ? 'primary'
                                                : ($details->order_status == 1
                                                    ? 'success'
                                                    : ($details->order_status == 2
                                                        ? 'danger'
                                                        : ($details->order_status == 3
                                                            ? 'warning'
                                                            : ($details->order_status == 4
                                                                ? 'secondary'
                                                                : ($details->order_status == 5
                                                                    ? 'info'
                                                                    : ($details->order_status == 6
                                                                        ? 'warning'
                                                                        : 'light')))))) }}">
                                            {{ $details->order_status == 0
                                                ? 'Pending'
                                                : ($details->order_status == 1
                                                    ? 'Completed'
                                                    : ($details->order_status == 2
                                                        ? 'Cancel'
                                                        : ($details->order_status == 3
                                                            ? 'Schedule Time'
                                                            : ($details->order_status == 4
                                                                ? 'Live Pooja'
                                                                : ($details->order_status == 5
                                                                    ? 'Share Soon'
                                                                    : ($details->order_status == 6
                                                                        ? 'Rejected'
                                                                        : 'Unknown Status')))))) }}
                                        </span>
                                    </div>

                                    <div class="payment-method d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{ translate('payment_Method') }} :</span>
                                        <strong>
                                            @if ($details['payment_id'] && $details['wallet_translation_id'])
                                                Razorpay/Wallet
                                            @elseif ($details['payment_id'])
                                                Razorpay
                                            @elseif ($details['wallet_translation_id'])
                                                Wallet
                                            @else
                                                Razorpay/Wallet
                                            @endif
                                        </strong>
                                    </div>

                                    <div class="d-flex justify-content-sm-end gap-10">
                                        <span class="title-color">{{ translate('payment_Status') }}:</span>
                                        <span class="text-success payment-status-span font-weight-bold">
                                            {{ translate('paid') }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-sm-end gap-10">
                                        <span class="title-color">{{ translate('Prasaad') }}:</span>
                                        <span
                                            class="text-{{ $details['is_prashad'] == 1 ? 'success' : 'danger' }} font-weight-bold">
                                            {{ $details['is_prashad'] == 1 ? 'Yes' : 'No' }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>


                        @if (count($details['product_leads']) > 0)
                            <div class="table-responsive datatable-custom mt-4">
                                <table
                                    class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('charity_product') }}</th>
                                            <th>{{ translate('quantity') }}</th>
                                            <th class="text-end">{{ translate('price') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($details['product_leads'] as $productLeads)
                                            <tr>
                                                <td>
                                                    <h6 class="title-color"> {{ $productLeads['productsData']['name'] }}
                                                    </h6>
                                                </td>
                                                <td>
                                                    {{ $productLeads['qty'] }}
                                                </td>
                                                <td class="text-end">
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $productLeads['final_price']), currencyCode: getCurrencyCode()) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="border-top border-dark">
                                        <tr>
                                        <tr>
                                            <td></td>
                                            <td class="text-start">{{ $details['packages']['title'] }}</td>
                                            <td class="text-end">
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['package_price']), currencyCode: getCurrencyCode()) }}
                                            </td>
                                            <td class="text-end">{{ $details->qty }}</td>
                                        </tr>
                                        @if (count($details['product_leads']) > 0)
                                            <tr>
                                                <td></td>
                                                <td class="text-start">{{ translate('Charity_Price') }}</td>
                                                <td class="text-end">
                                                    @php
                                                        $totalSum = 0;
                                                        foreach ($details['product_leads'] as $productLeads) {
                                                            $convertedPrice = usdToDefaultCurrency(
                                                                amount: $productLeads['final_price'],
                                                            );
                                                            $totalSum += $convertedPrice;
                                                        }
                                                        $formattedTotalSum = setCurrencySymbol(
                                                            amount: $totalSum,
                                                            currencyCode: getCurrencyCode(),
                                                        );
                                                    @endphp
                                                    {{ $formattedTotalSum }}
                                                </td>

                                            </tr>
                                        @endif
                                        <tr>
                                            <td></td>
                                            <td class="text-start">
                                                <span>{{ translate('Coupon_Discount') }}</span><br>
                                                @if ($details->coupon_code)
                                                    <small class="text-danger">{{ $details->coupon_code }}</small>
                                                @else
                                                    <small class="text-muted">{{ translate('No Coupon Applied') }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end text-danger">
                                                @if ($details->coupon_amount)
                                                    - {{ webCurrencyConverter(amount: $details->coupon_amount) }}
                                                @else
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: 0.0), currencyCode: getCurrencyCode()) }}
                                                @endif
                                            </td>

                                        </tr>
                                        <tr>
                                                <td></td>
                                                <th class="text-start text-muted">{{ translate('Amount_Paid_(via_Razorpay)') }}
                                                </th>
                                                <td class="text-end text-success">
                                                    {{ webCurrencyConverter(amount: $details->transection_amount) }}</td>
    
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <th class="text-start text-muted">{{ translate('Amount_Paid_(via_Wallet)') }}
                                                </th>
                                                <td class="text-end text-success">
                                                    {{ webCurrencyConverter(amount: $details->wallet_amount) }}</td>
    
                                            </tr>
                                            
                                            <tr class="border-top border-dark">
                                                <td></td>
                                                <td class="text-end fs-5"><strong>{{ translate('Total_Amount') }}</strong>
                                                </td>
                                                <td class="text-end fs-5 text-primary fw-bold">
                                               {{ webCurrencyConverter(amount: $details->wallet_amount + $details->transection_amount)  }}
                                                </td>
    
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="table-responsive datatable-custom mt-4">
                                <table
                                    class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                   
                                    <tbody>
                                        <tr class="border-top border-dark">
                                            <tr>
                                            <tr>
                                                <td></td>
                                                <th class="text-start">{{ $details['packages']['title'] }}</th>
                                                <td class="text-end">
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details['package_price']), currencyCode: getCurrencyCode()) }}
                                                </td>
                                                <td class="text-end">{{ $details->qty }}</td>
                                            </tr>
                                            @if (count($details['product_leads']) > 0)
                                                <tr>
                                                    <td></td>
                                                    <th class="text-start">{{ translate('Charity_Price') }}</th>
                                                    <td class="text-end">
                                                        @php
                                                            $totalSum = 0;
                                                            foreach ($details['product_leads'] as $productLeads) {
                                                                $convertedPrice = usdToDefaultCurrency(
                                                                    amount: $productLeads['final_price'],
                                                                );
                                                                $totalSum += $convertedPrice;
                                                            }
                                                            $formattedTotalSum = setCurrencySymbol(
                                                                amount: $totalSum,
                                                                currencyCode: getCurrencyCode(),
                                                            );
                                                        @endphp
                                                        {{ $formattedTotalSum }}
                                                    </td>
    
                                                </tr>
                                            @endif
                                            <tr>
                                                <td></td>
                                                <th class="text-start">
                                                    <span>{{ translate('Coupon_Discount') }}</span><br>
                                                    @if ($details->coupon_code)
                                                        <small class="text-danger">{{ $details->coupon_code }}</small>
                                                    @else
                                                        <small class="text-muted">{{ translate('No Coupon Applied') }}</small>
                                                    @endif
                                                </th>
                                                <td class="text-end text-danger">
                                                    @if ($details->coupon_amount)
                                                        - {{ webCurrencyConverter(amount: $details->coupon_amount) }}
                                                    @else
                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: 0.0), currencyCode: getCurrencyCode()) }}
                                                    @endif
                                                </td>
    
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <th class="text-start text-muted">{{ translate('Amount_Paid') }}
                                                </th>
                                                <td class="text-end text-success">
                                                    {{ webCurrencyConverter(amount: $details->transection_amount) }}</td>
    
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <th class="text-start text-muted">{{ translate('Amount_Paid_(via_Wallet)') }}
                                                </th>
                                                <td class="text-end text-success">
                                                    {{ webCurrencyConverter(amount: $details->wallet_amount) }}</td>
    
                                            </tr>
                                            
                                            <tr class="border-top border-dark">
                                                <td></td>
                                                <td class="text-end fs-5"><strong>{{ translate('Total_Amount') }}</strong>
                                                </td>
                                                <td class="text-end fs-5 text-primary fw-bold">
                                               {{ webCurrencyConverter(amount: $details->wallet_amount + $details->transection_amount)  }}
                                                </td>
    
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif


                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">

                {{-- Pandit Information --}}
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        @if (Helpers::modules_permission_check('Anushthan Order', 'Detail', 'assign-pandit'))
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0">
                                {{ translate(empty($details['pandit_assign']) ? 'assign_Pandit' : 'pandit_information') }}
                            </h4>
                            @if (!empty($details['pandit_assign']))
                                <button class="btn btn-outline-primary btn-sm square-btn" data-toggle="modal"
                                    data-target="#change-pandit-modal"
                                    {{ $details['order_status'] == 1 ? 'disabled' : '' }}>
                                    <i class="tio-edit"></i>
                                </button>
                            @endif
                        </div>
                        @endif
                        @if (empty($details['pandit_assign']))
                            <div class="">
                                @if (Helpers::modules_permission_check('Anushthan Order', 'Detail', 'assign-pandit'))
                              @if (
                                \Illuminate\Support\Carbon::parse($details->booking_date)->toDateString() <=
                                    \Illuminate\Support\Carbon::now()->addDay()->toDateString())
                            <div class="">
                                <label  class="font-weight-bold title-color fz-14">{{ translate('type_of_pandit_ji') }}</label>
                                <select name="astrologer_type" id="astrologer-type" class="astrologer-type form-control">
                                    <option value="in house">In house</option>
                                    <option value="freelancer">Freelancer</option>
                                </select>
                                <br>
                                <div class="" id="in-house">
                                    <label  class="font-weight-bold title-color fz-14">{{ translate('inhouse_Astrologer') }}</label>
                                        <select name="assign_pandit" id="assign-pandit" class="assign-pandit form-control">
                                            <option value="" selected disabled>Select Pandit Ji</option>
                                            @if (count($inHouseAstrologers) > 0)
                                                @foreach ($inHouseAstrologers as $inhouse)
                                                    @php
                                                        $checkastro = \App\Models\Service_order::where(
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
                                </div>
                                <div class="" id="freelancer" style="display: none;">
                                    <label
                                        class="font-weight-bold title-color fz-14">{{ translate('freelancer_Astrologer') }}</label>
                                    <select name="assign_pandit" id="assign-pandit" class="assign-pandit form-control">
                                        <option value="" selected disabled>Select Pandit Ji</option>
                                        @if (count($freelancerAstrologers) > 0)
                                            @php
                                                // Sort astrologers by price
                                                $sortedAstrologers = collect($freelancerAstrologers)->sortBy(
                                                    'price',
                                                );
                                            @endphp
                                            @foreach ($sortedAstrologers as $freelancer)
                                                @php
                                                    $checkastro = \App\Models\Service_order::where(
                                                        'pandit_assign',
                                                        $freelancer['id'],
                                                    )
                                                        ->where('booking_date', $details->booking_date)
                                                        ->count();
                                                @endphp
                                                @if ($freelancer['is_pandit_pooja_per_day'] > $checkastro)
                                                    <option value="{{ $freelancer['id'] }}">
                                                        {{ $freelancer['name'] }} - Price: {{ $freelancer['price'] }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option disabled>No Astrologer Found</option>
                                        @endif
                                    </select>
                                </div>
                                <form action="{{ route('admin.anushthan.order.pandit') }}" method="post" id="assign-pandit-form">
                                    @csrf
                                    <input type="hidden" name="id" id="table-id" value="{{ $details->id }}">
                                    <input type="hidden" name="package_id" id="package_id" value="{{ $details->package_id }}">
                                    <input type="hidden" name="booking_date" id="booking-id" value="{{ $details->booking_date }}">
                                    <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                                    <input type="hidden" name="pandit_id" id="pandit-id-val">
                                </form>
                            </div>
                            @endif
                             @else
                              <img src="{{ asset('public/assets/back-end/img/pooja/bellboy.png') }}"
                                  alt=""  class="text-center" width="200px" height="200px" style="margin-left: 3rem;">
                            @endif
                        @else
                            <div>
                                @if (!empty($details['astrologer']))
                                    <div class="media flex-wrap gap-3">
                                        <div class="">
                                           
                                            <img class="avatar rounded-circle avatar-70"
                                                src="{{ getValidImage(path: 'storage/app/public/astrologers/' . $details['pandit']['image'], type: 'backend-basic') }}"
                                                alt="{{ translate('Image') }}">
                                        </div>
                                        <div class="media-body d-flex flex-column gap-1">
                                            <span class="title-color"><i
                                                    class="tio-user"></i>:<strong>{{ $details['pandit']['name'] }}
                                                </strong></span>
                                            <span class="title-color break-all"><i
                                                    class="tio-call"></i>:<strong>{{ $details['pandit']['mobile_no'] }}</strong></span>
                                            <span class="title-color break-all"
                                                style="text-transform: lowercase !important;"><i class="tio-email"></i>:
                                                <strong> {{ $details['pandit']['email'] }}</strong></span>
                                            <span class="title-color break-all"
                                                style="text-transform: lowercase !important;"><i class="tio-poi"></i>:
                                                <strong> {{ $details['pandit']['is_pandit_primary_mandir_location'] }}</strong></span>
                                        </div>
                                    </div>
                                @else
                                    <p>Pandit Detail Not Available</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                {{-- ORder Status --}}
                
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center">
                                @if ($details['order_status'] != 1)
                                    <i class="tio-shopping-cart-outlined nav-icon"></i>
                                    {{ translate('Anushthan_order_status') }}
                                @endif
                            </h4>
                        </div>
                        @if ($details['status'] == 0)
                        @if (Helpers::modules_permission_check('Anushthan Order', 'Detail', 'order-status'))
                            <div class="">
                                <label
                                    class="font-weight-bold title-color fz-14">{{ translate('change_order_status') }}</label>
                                <input type="hidden" id="pandit-assigned"
                                    value="{{ !empty($details['pandit_assign']) ? '1' : '0' }}">
                                <select name="order_status" id="order_status" class="order-status form-control">
                                    <option value="0" {{ $details['order_status'] == 0 ? 'selected' : '' }}
                                        {{ in_array($details['order_status'], [3, 4, 5, 1, 2]) ? 'disabled' : '' }}>
                                        {{ translate('pending') }}
                                    </option>
                                    <option value="3" {{ $details['order_status'] == 3 ? 'selected' : '' }}
                                        {{ in_array($details['order_status'], [4, 5, 1, 2]) ? 'disabled' : '' }}>
                                        {{ translate('Schedule Time') }}
                                    </option>

                                    <option value="4" {{ $details['order_status'] == 4 ? 'selected' : '' }}
                                        {{ in_array($details['order_status'], [5, 1, 2]) ? 'disabled' : '' }}>
                                        {{ translate('Live') }}
                                    </option>
                                    <option value="5" {{ $details['order_status'] == 5 ? 'selected' : '' }}
                                        {{ in_array($details['order_status'], [1, 2]) ? 'disabled' : '' }}>
                                        {{ translate('Video Sharing') }}
                                    </option>
                                    @if (!empty($details['order_status'] == 5))
                                        <option value="1" {{ $details['order_status'] == 1 ? 'selected' : '' }}
                                            {{ $details['order_status'] == 2 ? 'disabled' : '' }}>
                                            {{ translate('Complete') }}
                                        </option>
                                    @endif
                                    @if ($details['order_status'] == 3)
                                    <option value="2" {{ $details['order_status'] == 2 ? 'selected' : '' }}
                                        {{ $details['order_status'] == 2 ? 'disabled' : '' }}>
                                        {{ translate('Canceled') }}
                                    </option>
                                    @endif

                                </select>
                                <form action="{{ route('admin.anushthan.order.status', $details->id) }}" method="post"
                                    id="order-status-form">
                                    @csrf
                                    <input type="hidden" name="booking_date" id="booking_id"
                                        value="{{ $details->booking_date }}">
                                    <input type="hidden" name="service_id" id="service-id"
                                        value="{{ $details->service_id }}">
                                    <input type="hidden" name="package_id" id="package-id"
                                        value="{{ $details->package_id }}">
                                    <input type="hidden" name="order_status" id="order-status-val">
                                </form>
                            </div>
                            @endif
                        @else
                         @if (!empty($details['pooja_certificate']))
                                <div class="text-center position-relative d-inline-block vip-container">

                                    {{-- EDIT BADGE --}}
                                    @if (Helpers::modules_permission_check('Anushthan Order', 'Anushthan Order', 'detail-edit'))
                                        <button
                                            class="btn btn-primary btn-sm vip-edit-badge"
                                            data-toggle="modal"
                                            data-target="#change-anushthan-certificate-model"
                                            title="Edit Certificate">
                                            <i class="tio-edit"></i>
                                        </button>
                                    @endif

                                    {{-- CERTIFICATE IMAGE --}}
                                    <img src="{{ asset('public/' . $details['pooja_certificate']) }}"
                                        alt="Anushthan Certificate"
                                        width="300px"
                                        class="img-fluid vip-image"
                                        data-toggle="modal"
                                        data-target="#viewAnushthanCertificate"
                                        style="cursor:pointer;">
                                </div>

                                {{-- VIEW IMAGE MODAL --}}
                                <div class="modal fade" id="viewAnushthanCertificate" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body p-0">
                                                <img src="{{ asset('public/' . $details['pooja_certificate']) }}"
                                                    class="img-fluid w-100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">No Certificate</span>
                            @endif

                        @endif
                    </div>
                </div>

                {{-- Shedule  time --}}
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center"><i class="tio-imac nav-icon"></i>
                                {{ translate('Anushthan_performing_details') }}</h4>
                        </div>
                        <div class="media flex-wrap gap-3">
                            <span class="title-color"><i class="tio-time"></i> {{ translate('Schedule_time') }}:<strong
                                    class="bg-danger text-light">{{ $details['schedule_time'] ? date('h:i A', strtotime($details['schedule_time'])) : '' }}</strong></span>
                            <span class="title-color"><i class="tio-tv-new"></i>
                                {{ translate('Live_link') }}:<strong><a href="{{ $details['live_stream'] ?? '' }}">Live
                                        Now</a></strong></span>
                            <span class="title-color"><i class="tio-share"></i> {{ translate('Share_video') }}:</span>
                            @php
                                // Check if a valid YouTube URL exists
                                $youtubeLink =
                                    isset($details['pooja_video']) && !empty($details['pooja_video'])
                                        ? $details['pooja_video']
                                        : 'https://www.youtube.com/';

                                // Convert YouTube URL to Embed Format
                                if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $youtubeLink, $matches)) {
                                    // Shortened YouTube URL (https://youtu.be/VIDEO_ID)
                                    $youtubeLink = 'https://www.youtube.com/embed/' . $matches[1];
                                } elseif (
                                    preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $youtubeLink, $matches)
                                ) {
                                    // Regular YouTube URL (https://www.youtube.com/watch?v=VIDEO_ID)
                                    $youtubeLink = 'https://www.youtube.com/embed/' . $matches[1];
                                } elseif (preg_match('/playlist\?list=([a-zA-Z0-9_-]+)/', $youtubeLink, $matches)) {
                                    // YouTube Playlist URL (https://www.youtube.com/playlist?list=PLAYLIST_ID)
                                    $youtubeLink = 'https://www.youtube.com/embed/videoseries?list=' . $matches[1];
                                }
                            @endphp

                            <iframe width="280" height="100" src="{{ $youtubeLink }}" frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>



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
                                    <span class="title-color">Name:<strong>{{ $details['customers']['f_name'] . ' ' . $details['customers']['l_name'] }}
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
                {{-- Prashad Details --}}
                @if ($details['is_prashad'] == 1)
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                                <h4 class="d-flex gap-2">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}"
                                        alt="">
                                    {{ translate('prashad_shipping_address') }}
                                </h4>
                                {{-- @if ($details['order_status'] != 'delivered')
                                        <button class="btn btn-outline-primary btn-sm square-btn" title="Edit"
                                                data-toggle="modal" data-target="#shippingAddressUpdateModal">
                                            <i class="tio-edit"></i>
                                        </button>
                                    @endif --}}
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <span>{{ translate('state') }} :</span>
                                    <strong>{{ $details['state'] }}</strong>
                                </div>
                                <div>
                                    <span>{{ translate('city') }} :</span>
                                    <strong>{{ $details['city'] }}</strong>
                                </div>
                                <div>
                                    <span>{{ translate('zip_code') }} :</span>
                                    <strong>{{ $details['pincode'] }}</strong>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <span>{{ translate('address : ') }}</span>
                                    {{ $details['house_no'] . ' ' . $details['area'] }}
                                </div>
                                <div>
                                    <span>{{ translate('landmark') }} :</span>
                                    <strong>{{ $details['landmark'] }}</strong>
                                </div>
                                <div>
                                    <span>{{ translate('prashad_status') }} :</span>
                                    <strong><span
                                            class="badge badge-{{ $details['prashard_status'] == 0 ? 'warning' : 'success' }}">{{ $details['prashard_status'] == 0 ? 'Pending' : 'Delivered' }}</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- change-pandit-modal --}}
    <div class="modal fade" id="change-pandit-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Change Pandit</h5>
                        <p class="text-muted small mt-1">
                            You are about to change the assigned Pandit (Purohit) for this Anushthan order. Please ensure the new selection matches the customer's requirements and schedule.
                        </p>
                        </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select id="astrologer-type-change" class="form-control">
                        <option value="in house">In house</option>
                        <option value="freelancer">Freelancer</option>
                    </select>
                    <br>
                    <div class="" id="in-house-change">
                        <label class="font-weight-bold title-color fz-14">{{ translate('inhouse_Pandit_ji') }}</label>
                        <select id="assign-astrologer-change" class="assign-astrologer-change form-control">
                            <option value="" selected disabled>Select Pandit Ji</option>
                            @if (count($inHouseAstrologers) > 0)
                                @foreach ($inHouseAstrologers as $inhouse)
                                    @php
                                        $checkastro = \App\Models\Service_order::where('pandit_assign', $inhouse->id)->where('booking_date', $details->booking_date)->count();
                                    @endphp
                                    @if ($inhouse['is_pandit_pooja_per_day'] > $checkastro)
                                        <option value="{{ $inhouse['id'] }}">{{ $inhouse['name'] }}</option>
                                    @endif
                                @endforeach
                            @else
                                <option disabled>No Astrologer Found</option>
                            @endif
                        </select>
                    </div>
                    <div class="" id="freelancer-change" style="display: none;">
                        <label class="font-weight-bold title-color fz-14">{{ translate('freelancer_Pandit_ji') }}</label>
                        <select id="assign-astrologer-change" class="assign-astrologer-change form-control">
                            <option value="" selected disabled>Select Pandit Ji</option>
                            @if (count($freelancerAstrologers) > 0)
                                @php
                                    $sortedAstrologers = collect($freelancerAstrologers)->sortBy('price');
                                @endphp
                                @foreach ($sortedAstrologers as $freelancer)
                                    @php
                                        $checkastro = \App\Models\Service_order::where(
                                            'pandit_assign',
                                            $freelancer['id'],
                                        )
                                            ->where('booking_date', $details->booking_date)
                                            ->count();
                                    @endphp
                                    @if ($freelancer['is_pandit_pooja_per_day'] > $checkastro)
                                        <option value="{{ $freelancer['id'] }}">
                                            {{ $freelancer['name'] }} - Price: {{ $freelancer['price'] }}
                                        </option>
                                    @endif
                                @endforeach
                            @else
                                <option disabled>No Astrologer Found</option>
                            @endif
                        </select>
                    </div>
                    <form action="{{ route('admin.anushthan.order.pandit') }}" method="post"
                        id="change-pandit-form">
                        @csrf
                        <input type="hidden" name="id" id="tableid" value="{{ $details->id }}">
                        <input type="hidden" name="package_id" id="package_id" value="{{ $details->package_id }}">
                        <input type="hidden" name="booking_date" id="booking_id" value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }} ">
                        <input type="hidden" name="pandit_id" id="change-pandit-id-val">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{-- <button type="submit" class="btn btn-primary">Change</button> --}}
                </div>
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
                <form action="{{ route('admin.anushthan.order.cancel_poojas', [$details->id]) }}" method="post"
                    id="pooja-cancel-form" class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="package_id" id="package_id" value="{{ $details->package_id }}">
                        <input type="hidden" name="booking_date" id="booking_id"
                            value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-cancel-status">
                        <textarea name="cancel_reason" cols="5" class="form-control" placeholder="Enter cancel reason" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- order-time-modal --}}
    <div class="modal fade" id="order-time-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Anushthan Time Schedule</h5>
                        <p class="text-muted small mt-1">
                            Below is the schedule for the Anushthan timings. Please ensure to arrive at least 15 minutes
                            before the scheduled time.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.anushthan.order.status_times', [$details->id]) }}" method="post"
                    id="pooja-time-form" class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="package_id" id="package_id" value="{{ $details->package_id }}">
                        <input type="hidden" name="booking_date" id="booking_id"
                            value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-time-status">
                        <input type="text" name="schedule_time" id="pooja_time" onclick="$timepicker.open()"
                            placehoder="{{ translate('Schedule Time') }}" class="schedule-time form-control"
                            data-id="{{ $details['id'] }}" data-service="{{ $details['service_id'] }}"
                            value="{{ $details['schedule_time'] ?? '' }}" autocomplete="off" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- order-live-modal --}}
    <div class="modal fade" id="order-live-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Live Stream Video Link</h5>
                        <p class="text-muted small mt-1">
                            Below is the live stream video link. Admin can use this link to manage and monitor the live Anushthan session.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.anushthan.order.live_streams', [$details->id]) }}" method="post"
                    id="pooja-live-form" class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="package_id" id="package_id" value="{{ $details->package_id }}">
                        <input type="hidden" name="booking_date" id="booking_id"
                            value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-live-status">
                        <input type="text" name="live_stream" id="live_stream"
                            placehoder="{{ translate('Live Stream') }}" class="live-stream form-control"
                            data-id="{{ $details['id'] }}" data-service="{{ $details['service_id'] }}"
                            value="{{ $details['live_stream'] ?? '' }}" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- order-Share-modal --}}
    <div class="modal fade" id="order-video-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Anushthan Video Share</h5>
                        <p class="text-muted small mt-1">
                            As an admin, you can share recorded or live Anushthan videos with devotees. Choose a platform
                            and ensure the video link is accessible.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.anushthan.order.pooja_videos', [$details->id]) }}" method="post"
                    id="pooja-video-form" class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="package_id" id="package_id" value="{{ $details->package_id }}">
                        <input type="hidden" name="booking_date" id="booking_id"
                            value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-video-status">
                        <input name="pooja_video" id="pooja_video" placehoder="{{ translate('Video URL') }}"
                            class="pooja-video form-control" data-id="{{ $details['id'] }}"
                            data-service="{{ $details['service_id'] }}" value="{{ $details['live_stream'] }}"
                            readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

      {{--CERTIFICATE UPDATE MODAL --}}
    <div class="modal fade" id="change-anushthan-certificate-model" tabindex="-1"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Anushthan Certificate Update</h5>
                        <p style="font-size:14px;color:#555;">
                            You can update the Anushthan certificate image here.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('admin.anushthan.order.update-certificate') }}"
                        method="post"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="order_id" value="{{ $details['id'] }}">

                        {{-- OLD IMAGE --}}
                        <div class="mb-3 text-center">
                            <label class="font-weight-bold">Current Certificate</label><br>
                            <img src="{{ asset('public/' . $details['pooja_certificate']) }}"
                                class="img-fluid rounded shadow"
                                style="max-height:300px;">
                        </div>

                        {{-- NEW IMAGE --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Upload New Certificate</label>
                            <input type="file"
                                name="pooja_certificate"
                                class="form-control"
                                required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('script_2')
    {{-- <script
        src="https://maps.googleapis.com/maps/api/js?key={{ getWebConfig('map_api_key') }}&callback=map_callback_fucntion&libraries=places&v=3.49"
        defer></script> --}}
    {{-- <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script> --}}
    {{-- <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script> --}}
    {{-- <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/order.js') }}"></script> --}}
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        var $timepicker = $('#pooja_time').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });
    </script>
    <script>
        $('#order_status').on('change', function() {
            var isPanditAssigned = $('#pandit-assigned').val();
            if (isPanditAssigned == '0') {
                Swal.fire({
                    title: "{{ translate('anushthan_order_status') }}",
                    text: "Please select a Pandit first.",
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                });
                $(this).val('');
                return false;
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
                $('#order-time-status').val(orderStatus);
                $('#order-time-modal').modal('show');
            } else if (orderStatus == 4) {
                $('#order-live-status').val(orderStatus);
                $('#order-live-modal').modal('show');
            } else if (orderStatus == 5) {
                $('#order-video-status').val(orderStatus);
                $('#order-video-modal').modal('show');
            }
        });
    </script>

    {{-- pandit assign --}}
    <script>
        $('.assign-pandit').on('change', function() {
            var panditId = $(this).val();
            $('#pandit-id-val').val(panditId);
            Swal.fire({
                title: 'Are you sure you want to reassign this Anushthan? This will replace the current pandit.',
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
                title: 'Are you sure you want to reassign this Anushthan? This will replace the current pandit.',
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
    <script>
        document.querySelectorAll('.modal-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('.submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    'Please wait... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            });
        });
    </script>
     <script>
        $('#astrologer-type').change(function(e) {
            e.preventDefault();
            var type = $(this).val();
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
    <script>
        $('.assign-astrologer-change').on('change', function() {
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
@endpush