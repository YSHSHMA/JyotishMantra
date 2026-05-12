@extends('layouts.back-end.app')
@section('title', translate('order_Details'))
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
        .dateBooking {
            animation: blink 1s infinite;
            color: red;
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
    </style>
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2" title="total order for this puja">
                <img src="{{ dynamicAsset(path: 'public\assets\back-end\img\pooja\poojas.png') }}" alt=""
                    style="width: 24px;height:24px;">
                {{ translate('pooja_order_details') }}
            </h2>
        </div>
        <div class="row gy-3" id="printableArea">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                            <div class="d-flex flex-column gap-10">
                                <div class="invoice-header mb-4">
                                    <h6 class="dateBooking mb-1 text-capitalize text-danger">
                                        Booking Date: {{ date('d M, Y', strtotime($details['booking_date'])) }},{{ $details['schedule_time'] ? date('h:i A', strtotime($details['schedule_time'])) : '' }}</h6>
                                    <h2 class="fw-bold text-uppercase mb-2">{{ $details['services']['name'] }}</h2>
                                    <h5 class="mb-1">OrderId :{{ $details['order_id'] }} + {{ \App\Models\Service_order::where('type', 'pooja')->where('booking_date',$details['booking_date'])->where('status',$details['status'])->where('order_status',$details['order_status'])->where('service_id',$details['service_id'])->count() }}</h5>
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
                            </div>
                            <div class="text-sm-right flex-grow-1">
                                <div class="d-flex flex-wrap gap-10 justify-content-end">
                                    <a class="btn btn--primary px-4" target="_blank" title="print Invoice"
                                        href="{{ route('admin.pooja.orders.generate.invoice', $details['id']) }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}"
                                            alt="" class="mr-1">
                                      
                                    </a>
                                </div>
                                <div class="d-flex flex-column gap-2 mt-3">
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
                                            @if($details['payment_status'] == 1)
                                            <strong>{{ translate('Paid') }}</strong>
                                            @elseif($details['payment_status'] == 0)
                                            <strong>{{ translate('Unpaid') }}</strong>
                                            @elseif($details['payment_status'] == 2)
                                            <strong>{{ translate('Cancel') }}</strong>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-sm-end gap-10 text-capitalize">
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
                                                    <h6 class="title-color">
                                                        {{ substr($productLeads['productsData']['name'], 0, 30) }}{{ strlen($productLeads['productsData']['name']) > 10 ? '...' : '' }}
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
                @if (
                    !empty($details['order_status']) &&
                        $details['order_status'] == 1 &&
                        !empty($details['status']) &&
                        $details['status'] == 1)
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2 align-items-center justify-content-between">
                                <h4 class="d-flex gap-2">
                                    <i class="tio-route-dashed"></i> {{ translate('Prashad_orders_status') }}
                                </h4>
                                <button id="toggle-card" class="btn btn-sm">
                                    <i id="toggle-icon" class="tio-caret-down"></i>
                                </button>
                            </div>
                            @if ($details['is_prashad'] == 1)
                                <div class="">
                                    <label
                                        class="font-weight-bold title-color fz-14">{{ translate('change_order_status') }}</label>
                                    <select name="prashad_status" id="prashad_status"
                                        class="order-prashad-status form-control" data-id="{{ $details['id'] }}">
                                        <option value="0" {{ $details->prashad_status == '0' ? 'selected' : '' }}
                                            {{ in_array($details['prashad_status'], [3, 4, 5, 6, 1, 2]) ? 'disabled' : '' }}>
                                            {{ translate('pending') }}</option>
                                        <option value="1" {{ $details->prashad_status == '1' ? 'selected' : '' }}
                                            {{ in_array($details['prashad_status'], [4, 5, 3, 2, 6]) ? 'disabled' : '' }}>
                                            {{ translate('confirmed') }}</option>
                                        <option value="2" {{ $details->prashad_status == '2' ? 'selected' : '' }}
                                            {{ in_array($details['prashad_status'], [4, 5, 3, 6]) ? 'disabled' : '' }}>
                                            {{ translate('packaging') }} </option>
                                        <option class="text-capitalize" value="3"
                                            {{ $details->prashad_status == '3' ? 'selected' : '' }}
                                            {{ in_array($details['prashad_status'], [4, 5, 6]) ? 'disabled' : '' }}>
                                            {{ translate('out_for_delivery') }} </option>
                                        <option value="4" {{ $details->prashad_status == '4' ? 'selected' : '' }}
                                            {{ in_array($details['prashad_status'], [5, 6]) ? 'disabled' : '' }}>
                                            {{ translate('delivered') }} </option>
                                        <option value="5" {{ $details->prashad_status == '5' ? 'selected' : '' }}
                                            {{ in_array($details['prashad_status'], [6]) ? 'disabled' : '' }}>
                                            {{ translate('failed_to_Deliver') }} </option>
                                        <option value="6" {{ $details->prashad_status == '6' ? 'selected' : '' }}>
                                            {{ translate('canceled') }} </option>
                                    </select>
                                    <form action="{{ route('admin.pooja.orders.prashad-status', [$details['id']]) }}"
                                        method="post" id="order-prashad-status-form">
                                        @csrf
                                        <input type="hidden" name="prashad_status" id="order-prashad-status-val">
                                    </form>
                                </div>
                            @else
                                <div class="text-center">
                                    <span
                                        class="badge badge-{{ $details['order_status'] == 1
                                            ? 'success'
                                            : ($details['order_status'] == 4
                                                ? 'danger'
                                                : ($details['order_status'] == 0
                                                    ? 'warning'
                                                    : 'secondary')) }}"
                                        style="font-size: 18px;">
                                        {{ $details['order_status'] == 4
                                            ? 'Completed'
                                            : ($details['order_status'] == 6
                                                ? 'Cancel'
                                                : ($details['order_status'] == 0
                                                    ? 'Unknown Status.'
                                                    : 'Prashad NO Delivered!.')) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Shedule  time --}}
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center"><i class="tio-imac nav-icon"></i>
                                {{ translate('pooja_performing_details') }}</h4>
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
                                    <span>{{ translate('address') }}:</span>
                                    <strong>{{ $details['house_no'] . ' ' . $details['area'] }}</strong>
                                </div>
                                <div>
                                    <span>{{ translate('landmark') }} :</span>
                                    <strong>{{ $details['landmark'] }}</strong>
                                </div>
                                <div>
                                    <span>{{ translate('prashad_status') }} :</span>
                                    <strong><span  class="badge badge-{{ $details['prashard_status'] == 0 ? 'warning' : 'success' }}">{{ $details['prashard_status'] == 0 ? 'Pending' : 'Delivered' }}</span></strong>
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
                            <select id="assign-astrologer-change" class="assign-astrologer-change form-control">
                                <option value="" selected disabled>Select Pandit Ji</option>
                                @if (count($inHouseAstrologers) > 0)
                                    @foreach ($inHouseAstrologers as $inhouse)
                                        @if ($inhouse['is_pandit_pooja_per_day'] > $inhouse['ordercount'])
                                            <option value="{{ $inhouse['id'] }}">
                                                {{ $inhouse['name'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option disabled>No Astrologer Found</option>
                                @endif
                            </select>
                        </div>
                        <div class="" id="freelancer-change" style="display: none;">
                            <label
                                class="font-weight-bold title-color fz-14">{{ translate('freelancer_Pandit_ji') }}</label>
                            <select id="assign-astrologer-change" class="assign-astrologer-change form-control">
                                <option value="" selected disabled>Select Pandit Ji</option>
                                @if (count($freelancerAstrologers) > 0)
                                    @php
                                        // Sort astrologers by price
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
                        <form action="{{ route('admin.pooja.orders.assign.pandit', [$details['id']]) }}" method="post"
                            id="change-astrologer-form">
                            @csrf
                            <input type="hidden" name="booking_date" id="booking_id"
                                value="{{ $details->booking_date }}">
                            <input type="hidden" name="service_id" id="service_id"
                                value="{{ $details->service_id }}">
                            <input type="hidden" name="pandit_id" id="change-astrologer-id-val">
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
                <form action="{{ route('admin.pooja.orders.cancel_poojas', [$details->id]) }}" method="post"
                    id="pooja-cancel-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="order_status" id="order-cancel-status">
                        <textarea name="cancel_reason" cols="5" class="form-control" placeholder="Enter cancel reason" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
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
                    <h5 class="modal-title">Time Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.pooja.orders.status_times', [$details->id]) }}" method="post"
                    id="pooja-time-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="order_status" id="order-time-status">
                        <input type="time" name="schedule_time" id="schedule_time"
                            placehoder="{{ translate('Schedule Time') }}" class="schedule-time form-control"
                            data-id="{{ $details['id'] }}" data-service="{{ $details['service_id'] }}"
                            value="{{ $details['schedule_time'] ?? '' }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
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
                    <h5 class="modal-title">Live Stream</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.pooja.orders.live_streams', [$details->id]) }}" method="post"
                    id="pooja-live-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="order_status" id="order-live-status">
                        <input type="text" name="live_stream" id="live_stream"
                            placehoder="{{ translate('Live Stream') }}" class="live-stream form-control"
                            data-id="{{ $details['id'] }}" data-service="{{ $details['service_id'] }}"
                            value="{{ $details['live_stream'] ?? '' }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
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
                    <h5 class="modal-title">Pooja Video Share</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.pooja.orders.pooja_videos', [$details->id]) }}" method="post"
                    id="pooja-video-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="order_status" id="order-video-status">
                        <input name="pooja_video" id="pooja_video" placehoder="{{ translate('Video URL') }}"
                            class="pooja-video form-control" data-id="{{ $details['id'] }}"
                            data-service="{{ $details['service_id'] }}">
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
@push('script_2')
    {{-- <script
   src="https://maps.googleapis.com/maps/api/js?key={{ getWebConfig('map_api_key') }}&callback=map_callback_fucntion&libraries=places&v=3.49"
   defer></script> --}}
    {{-- <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script> --}}
    {{-- <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script> --}}
    {{-- <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/order.js') }}"></script> --}}
    <script>
        $('#order_status').on('change', function() {
            var isPanditAssigned = $('#pandit-assigned').val();
            if (isPanditAssigned == '0') {
                Swal.fire({
                    title: "{{ translate('Pooja_order_status') }}",
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
    {{-- Prashad --}}
    <script>
        $('.order-prashad-status').on('change', function() {
            var orderStatus = $(this).val();
            $('#order-prashad-status-val').val(orderStatus);
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
                        $('#order-prashad-status-form').submit();
                    }
                });
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
        $('.assign-astrologer-change').on('change', function() {
            var astrologerId = $(this).val();
            $('#change-astrologer-id-val').val(astrologerId);
            Swal.fire({
                title: 'Are You Sure To Change Pandit Ji',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#change-astrologer-form').submit();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#toggle-card').click(function() {
                $('#order-tracking-card').toggle();
                $('#toggle-icon').toggleClass('ti-caret-up');
            });
        });
    </script>
    {{-- Change Astrologer --}}
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
@endpush
