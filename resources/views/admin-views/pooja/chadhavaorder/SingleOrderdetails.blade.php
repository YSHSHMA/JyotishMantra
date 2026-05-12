@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('Chadhava| Order Details'))

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

        .tracking-detail {
            padding: 3rem 0;
        }

        #tracking {
            margin-bottom: 1rem;
        }

        [class*="tracking-status-"] p {
            margin: 0;
            font-size: 1.1rem;
            color: #fff;
            text-transform: uppercase;
            text-align: center;
        }

        [class*="tracking-status-"] {
            padding: 1.6rem 0;
        }

        /* .tracking-list {
                                border: 1px solid #e5e5e5;
                            } */

        .tracking-item {
            border-left: 4px solid #00ba0d;
            position: relative;
            padding: 2rem 1.5rem 0.5rem 2.5rem;
            font-size: 0.9rem;
            margin-left: 3rem;
            min-height: 5rem;
        }

        .tracking-item:last-child {
            padding-bottom: 4rem;
        }

        .tracking-item .tracking-date {
            margin-bottom: 0.5rem;
        }

        .tracking-item .tracking-date span {
            color: #888;
            font-size: 85%;
            padding-left: 0.4rem;
        }

        .tracking-item .tracking-content {
            padding: 0.5rem 0.8rem;
            background-color: #f4f4f4;
            border-radius: 0.5rem;
        }

        .tracking-item .tracking-content span {
            display: block;
            color: #767676;
            font-size: 13px;
        }

        .tracking-item .tracking-icon {
            position: absolute;
            left: -0.7rem;
            width: 1.1rem;
            height: 1.1rem;
            text-align: center;
            border-radius: 50%;
            font-size: 1.1rem;
            background-color: #fff;
            color: #fff;
        }

        .tracking-item-pending {
            border-left: 4px solid #d6d6d6;
            position: relative;
            padding: 2rem 1.5rem 0.5rem 2.5rem;
            font-size: 0.9rem;
            margin-left: 3rem;
            min-height: 5rem;
        }

        .tracking-item-pending:last-child {
            padding-bottom: 4rem;
        }

        .tracking-item-pending .tracking-date {
            margin-bottom: 0.5rem;
        }

        .tracking-item-pending .tracking-date span {
            color: #888;
            font-size: 85%;
            padding-left: 0.4rem;
        }

        .tracking-item-pending .tracking-content {
            padding: 0.5rem 0.8rem;
            background-color: #f4f4f4;
            border-radius: 0.5rem;
        }

        .tracking-item-pending .tracking-content span {
            display: block;
            color: #767676;
            font-size: 13px;
        }

        .tracking-item-pending .tracking-icon {
            line-height: 2.6rem;
            position: absolute;
            left: -0.7rem;
            width: 1.1rem;
            height: 1.1rem;
            text-align: center;
            border-radius: 50%;
            font-size: 1.1rem;
            color: #d6d6d6;
        }

        .tracking-item-pending .tracking-content {
            font-weight: 600;
            font-size: 17px;
        }

        .tracking-item .tracking-icon.status-current {
            width: 1.9rem;
            height: 1.9rem;
            left: -1.1rem;
        }

        .tracking-item .tracking-icon.status-intransit {
            color: #00ba0d;
            font-size: 0.6rem;
        }

        .tracking-item .tracking-icon.status-current {
            color: #00ba0d;
            font-size: 0.6rem;
        }

        @media (min-width: 992px) {
            .tracking-item {
                margin-left: 3rem;
            }

            .tracking-item .tracking-date {
                position: absolute;
                left: -3rem;
                width: 2.5rem;
                text-align: right;
            }

            .tracking-item .tracking-date span {
                display: block;
            }

            .tracking-item .tracking-content {
                padding: 0;
                background-color: transparent;
            }

            .tracking-item-pending {
                margin-left: 3rem;
            }

            .tracking-item-pending .tracking-date {
                position: absolute;
                left: -3rem;
                width: 1.5rem;
                text-align: right;
            }

            .tracking-item-pending .tracking-date span {
                display: block;
            }

            .tracking-item-pending .tracking-content {
                padding: 0;
                background-color: transparent;
            }
        }

        .tracking-item .tracking-content {
            font-weight: 600;
            font-size: 17px;
        }

        .blinker {
            border: 7px solid #ff2c00;
            animation: blink 1s;
            animation-iteration-count: infinite;
        }

        @keyframes blink {
            50% {
                border-color: #fff;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/all-orders.png') }}" alt="">
                {{ translate('Chadhava| Order Details') }}
            </h2>
        </div>

        <div class="row gy-3" id="printableArea">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                            <div class="d-flex flex-column gap-10">
                                <h4 class="text-capitalize">{{ translate('Chadhava_Details') }} </h4>
                                <span class="text-capitalize">
                                    <i class="tio-bookmark"></i> {{ $details['chadhava']['name'] }}
                                </span>
                                <span class="text-capitalize">
                                    <i class="tio-poi"></i> {{ $details['chadhava']['chadhava_venue'] }}
                                </span>
                                <span class="text-capitalize">
                                    <i class="tio-calendar-note"></i>
                                    {{ date('d M, Y', strtotime($details['booking_date'])) }}
                                </span>

                            </div>
                            <div class="text-sm-right flex-grow-1">

                                <div class="d-flex flex-column gap-2 mt-3">
                                    <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{ translate('Total Order') }}: </span>
                                        <strong>{{ $details->total_orders }}</strong>
                                    </div>

                                    @php
                                        $member_count = 0;
                                        if (isset($details['members']) && $details['members'] != null) {
                                            $members = explode('|', $details['members']);
                                            foreach ($members as $memb) {
                                                if ($memb != null) {
                                                    $member_count++;
                                                }
                                            }
                                        }
                                    @endphp

                                    <div class="d-flex justify-content-sm-end gap-10">
                                        <span class="title-color">{{ translate('Total Members') }}:</span>
                                        <strong>{{ $member_count }}</strong>
                                    </div>
                                    <div class="payment-method d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{ translate('payment_Method') }} :</span>
                                        <strong>{{ translate('Paid') }}</strong>
                                    </div>

                                    <div class="d-flex justify-content-sm-end gap-10">
                                        <span class="title-color">{{ translate('Total Payment') }}:</span>
                                        <strong>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details->total_amount), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="table-responsive datatable-custom mt-4">
                            <table
                                class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('Order_id') }}</th>
                                        <th>{{ translate('Members Name`s') }}</th>
                                        <th>{{ translate('All Members Gotra') }}</th>
                                        <th>{{ translate('amount') }}</th>
                                        <th>{{ translate('product_name') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($details->order_id))
                                        @php
                                            $orderIds = explode('|', $details->order_id);
                                            $memberParts = !empty($details->members)
                                                ? explode('|', $details->members)
                                                : [];
                                            $gotraParts = !empty($details->gotra) ? explode('|', $details->gotra) : [];
                                            $leadIds = !empty($details->leads)
                                                ? array_map('trim', explode(',', $details->leads))
                                                : [];
                                            $products = \App\Models\ProductLeads::whereIn('leads_id', $leadIds)
                                                ->get()
                                                ->groupBy('leads_id');
                                            $orderdata = \App\Models\Chadhava_orders::whereIn('order_id', $orderIds)
                                                ->with('customer')
                                                ->get()
                                                ->keyBy('order_id');
                                        @endphp
                                        {{-- {{dd($details->members);}} --}}

                                        @foreach ($orderIds as $index => $orderId)
                                            <tr>
                                                <!-- Order ID -->
                                                @php
                                                    $order = $orderdata[$orderId] ?? null;
                                                @endphp
                                                <td> {{ $orderId }}</td>
                                                <!-- Member Name -->
                                                <td>
                                                    @if (isset($memberParts[$index]) && !empty($memberParts[$index]))
                                                        {{ $memberParts[$index] }}
                                                    @else
                                                        <span>No Members</span>
                                                    @endif
                                                </td>

                                                <!-- Gotra -->
                                                <td>
                                                    @if (isset($gotraParts[$index]) && !empty($gotraParts[$index]))
                                                        {{ $gotraParts[$index] }}
                                                    @else
                                                        <span>No Gotra</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($order)
                                                        <span class="">
                                                            ₹{{ $order->pay_amount }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <!-- Product Name & Quantity -->
                                                <td>
                                                    @if (!empty($leadIds) && isset($leadIds[$index]) && isset($products[$leadIds[$index]]))
                                                        @foreach ($products[$leadIds[$index]] as $product)
                                                            <span>{{ $product->product_name }} (Qty:
                                                                {{ $product->qty }})</span><br>
                                                        @endforeach
                                                    @else
                                                        <span>No Products</span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">No Orders Found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0">
                                {{ translate(empty($details['pandit_assign']) ? 'assign_Pandit' : 'pandit_information') }}
                            </h4>
                            @if (!empty($details['pandit_assign']))
                                <button class="btn btn-outline-primary btn-sm square-btn" data-toggle="modal"
                                    data-target="#change-pandit-modal">
                                    <i class="tio-edit"></i>
                                </button>
                            @endif
                        </div>
                        @if (empty($details['pandit_assign']))
                        @if (Helpers::modules_permission_check('Chadhava Order', 'Detail', 'assign-pandit'))
                            <div class="">
                                <select name="astrologer_type" id="astrologer-type" class="astrologer-type form-control">
                                    <option value="in house">In house</option>
                                    <option value="freelancer">Freelancer</option>
                                </select>
                                <br>
                                <div class="" id="in-house">
                                    <label
                                        class="font-weight-bold title-color fz-14">{{ translate('inhouse_Astrologer') }}</label>
                                    <select name="assign_pandit" id="assign-pandit" class="assign-pandit form-control">
                                        <option value="" selected disabled>Select Pandit Ji</option>
                                        @if (count($inHouseAstrologers) > 0)
                                            @foreach ($inHouseAstrologers as $inhouse)
                                                @php
                                                    $checkastro = \App\Models\Chadhava_orders::where(
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
                                                $sortedAstrologers = collect($freelancerAstrologers)->sortBy('price');
                                            @endphp
                                            @foreach ($sortedAstrologers as $freelancer)
                                                @php
                                                    $checkastro = \App\Models\Chadhava_orders::where(
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
                                <form action="{{ route('admin.chadhava.order.assign.allpandit') }}" method="post"
                                    id="assign-pandit-form">
                                    @csrf
                                    <input type="hidden" name="booking_date" id="booking_id"
                                        value="{{ $details->booking_date }}">
                                    <input type="hidden" name="service_id" id="service_id"
                                        value="{{ $details->service_id }}">
                                    <input type="hidden" name="pandit_id" id="pandit-id-val">
                                </form>
                            </div>
                            @endif
                        @else
                            <div>

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
                                            {{ $details['pandit']['email'] }}</span>
                                    </div>
                                </div>

                            </div>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center"><i class="tio-imac nav-icon"></i>
                                {{ translate('Chadhava_Schedule_details') }}</h4>
                        </div>
                        <div class="media flex-wrap gap-3">
                            <span class="title-color"><i class="tio-time"></i> {{ translate('Schedule_time') }}:<strong
                                    class="bg-danger text-light">{{ $details['schedule_time'] ? date('h:i A', strtotime($details['schedule_time'])) : '' }}</strong></span>
                            <span class="title-color"><i class="tio-tv-new"></i>
                                {{ translate('Live_stream') }}:</span>
                            @php
                                // Check if a valid YouTube URL exists
                                $youtubeLink =
                                    isset($details['live_stream']) && !empty($details['live_stream'])
                                        ? $details['live_stream']
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

                            <iframe width="280" height="110" src="{{ $youtubeLink }}" frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>



                            <span class="title-color"><i class="tio-share"></i>
                                {{ translate('Share_video_link') }}:<strong>{{ $details['pooja_video'] ?? '' }}</strong></span>

                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center"><i class="tio-blend-tool nav-icon"></i>
                                {{ translate('Chadhava_order_status') }}</h4>
                        </div>
                        @if ($details['status'] == 0)
                            <div class="">

                                <input type="hidden" id="pandit-assigned"
                                    value="{{ !empty($details['pandit_assign']) ? '1' : '0' }}">
                                    @if (Helpers::modules_permission_check('Chadhava Order', 'Detail', 'order-status'))
                                <select name="order_status" id="order_status" class="order-status form-control"
                                    data-id="{{ $details['id'] }}">
                                    <option value="0" {{ $details['order_status'] == 0 ? 'selected' : '' }}
                                        {{ in_array($details['order_status'], [3, 4, 5, 1, 2]) ? 'disabled' : '' }}>
                                        {{ translate('pending') }}
                                    </option>
                                    <option value="3" {{ $details['order_status'] == 3 ? 'selected' : '' }}
                                        {{ in_array($details['order_status'], [4, 5, 1, 2]) ? 'disabled' : '' }}>
                                        {{ translate('Schedule Time') }}
                                    </option>
                                    @if (
                                        \Illuminate\Support\Carbon::parse($details['booking_date'])->toDateString() <=
                                            \Illuminate\Support\Carbon::now()->addDay()->toDateString())
                                        <option value="4" {{ $details['order_status'] == 4 ? 'selected' : '' }}
                                            {{ in_array($details['order_status'], [5, 1, 2]) ? 'disabled' : '' }}>
                                            {{ translate('Live') }}
                                        </option>
                                        <option value="5" {{ $details['order_status'] == 5 ? 'selected' : '' }}
                                            {{ in_array($details['order_status'], [1, 2]) ? 'disabled' : '' }}>
                                            {{ translate('Video Sharing') }}
                                        </option>
                                        <option value="1" {{ $details['order_status'] == 1 ? 'selected' : '' }}
                                            {{ $details['order_status'] == 2 ? 'disabled' : '' }}>
                                            {{ translate('Complete') }}
                                        </option>
                                    @endif
                                    <option value="2" {{ $details['order_status'] == 2 ? 'selected' : '' }}
                                        {{ $details['order_status'] == 2 ? 'disabled' : '' }}>
                                        {{ translate('Canceled') }}
                                    </option>
                                </select>
                                @endif
                                <form action="{{ route('admin.chadhava.order.allstatus') }}" method="post"
                                    id="order-status-form">
                                    @csrf
                                    <input type="hidden" name="booking_date" id="booking_id"
                                        value="{{ $details->booking_date }}">
                                    <input type="hidden" name="service_id" id="service_id"
                                        value="{{ $details->service_id }}">
                                    <input type="hidden" name="order_status" id="order-status-val">
                                </form>
                            </div>
                        @else
                            <div class="text-center">
                                <span
                                    class="badge badge-{{ $details['order_status'] == 1
                                        ? 'success'
                                        : ($details['order_status'] == 2
                                            ? 'danger'
                                            : ($details['order_status'] == 6
                                                ? 'warning'
                                                : 'secondary')) }}"
                                    style="font-size: 18px;">
                                    {{ $details['order_status'] == 1
                                        ? 'Completed'
                                        : ($details['order_status'] == 2
                                            ? 'Cancel'
                                            : ($details['order_status'] == 6
                                                ? 'Rejected'
                                                : 'Unknown Status')) }}
                                </span>


                            </div>

                            <div class="text-center">
                                <img src="{{ !empty($details['pooja_certificate']) ? asset('public/assets/back-end/img/certificate/pooja/' . $details['pooja_certificate']) : '' }}"
                                    alt="" width="150">
                            </div>


                        @endif
                    </div>
                </div>


                {{-- Traking data --}}
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center"><i class="tio-route nav-icon"></i>
                                {{ translate('Chadhava_traking') }}</h4>
                        </div>
                        <div class="">
                            <div id="tracking-pre"></div>
                            <div id="tracking">
                                <div class="tracking-list">
                                    @php
                                        $statuses = [
                                            0 => [
                                                'label' => 'Pending Chadhava',
                                                'timestamp' => date(
                                                    'h:i A, d M Y',
                                                    strtotime($details['created_at'] ?? now()),
                                                ),
                                            ],
                                            3 => [
                                                'label' => 'Schedule Time',
                                                'timestamp' => $details['schedule_time'] ?? 'Update Soon',
                                            ],
                                            4 => [
                                                'label' => 'Live Stream',
                                                'link' => $details['live_stream'] ?? '#',
                                                'timestamp' => $details['live_created_stream'] ?? 'Update Soon',
                                            ],
                                            5 => [
                                                'label' => 'Video Share',
                                                'link' => $details['pooja_video'] ?? '#',
                                                'timestamp' => $details['video_created_sharing'] ?? 'Update Soon',
                                            ],
                                            2 => [
                                                'label' => 'Cancel Chadhava',
                                                'timestamp' => $details['order_canceled'] ?? 'Update Soon',
                                            ],
                                            1 => [
                                                'label' => 'Completed Chadhava',
                                                'timestamp' => $details['order_completed'] ?? 'Update Soon',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($statuses as $status => $info)
                                        <div
                                            class="{{ $details['order_status'] == $status ? 'tracking-item-pending' : 'tracking-item' }}">
                                            <div
                                                class="tracking-icon {{ $details['order_status'] == $status ? 'blinker status-intransit' : 'status-intransit' }}">
                                                <svg class="svg-inline--fa fa-circle fa-w-16" aria-hidden="true"
                                                    data-prefix="fas" data-icon="circle" role="img"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="currentColor"
                                                        d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="tracking-content">
                                                {{ $info['label'] }}
                                                @if (isset($info['link']))
                                                    <a class="text-danger"
                                                        href="{{ $info['link'] }}">{{ $info['label'] }}</a>
                                                @endif
                                                <span>{{ $info['timestamp'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- change-pandit-modal --}}
    <div class="modal fade" id="change-pandit-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Pandit</h5>
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
                                        $checkastro = \App\Models\Chadhava_orders::where('pandit_assign', $inhouse->id)
                                            ->where('booking_date', $details->booking_date)
                                            ->count();
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
                                        $checkastro = \App\Models\Chadhava_orders::where(
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
                    <form action="{{ route('admin.chadhava.order.assign.allpandit') }}" method="post"
                        id="change-pandit-form">
                        @csrf
                        <input type="hidden" name="booking_date" id="booking_id" value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service_id" value="{{ $details->service_id }}">
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
                <form action="{{ route('admin.chadhava.order.cancel_pooja') }}"method="post" class="modal-form"
                    id="pooja-cancel-form">
                    @csrf
                    <div class="modal-body">

                        <input type="hidden" name="booking_date" id="booking_id" value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-cancel-status">
                        <input type="hidden" name="order_id" id="order_id" value="{{ $details->order_id }}">
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
                        <h5 class="modal-title">Chadhava Time Schedule</h5>
                        <p class="modal-subtitle" style="font-size: 14px; color: #555; margin: 5px 0 0;">
                            Below is the schedule for the Chadhava timings. Please ensure to arrive at least 15 minutes
                            before the scheduled time.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.chadhava.order.status_time') }}" method="post" id="pooja-time-form"
                    class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="booking_date" id="booking_id" value="{{ $details->booking_date }}">
                        <input type="hidden" name="order_id" id="order_id" value="{{ $details->order_id }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-time-status">
                        <input type="text" name="schedule_time" id="pooja_time"
                            placehoder="{{ translate('Schedule Time') }}" class="schedule-time form-control"
                            data-id="{{ $details['id'] }}" data-service="{{ $details['service_id'] }}"
                            value="{{ $details['schedule_time'] ?? '' }}" required>
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
                        <p class="modal-subtitle" style="font-size: 14px; color: #555; margin: 5px 0 0;">
                            Below is the live stream video link. Admin can use this link to manage and monitor the live
                            Chadhava session.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.chadhava.order.live_stream') }}" method="post" id="pooja-live-form"
                    class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="booking_date" id="booking_id"
                            value="{{ $details->booking_date }}">
                            <input type="hidden" name="order_id" id="order_id" value="{{ $details->order_id }}">
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
                        <h5 class="modal-title">Chadhava Video Share</h5>
                        <p class="modal-subtitle" style="font-size: 14px; color: #555; margin: 5px 0 0;">
                            As an admin, you can share recorded or Share Chadhava videos with devotees. Choose a platform
                            and ensure the video link is accessible.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.chadhava.order.pooja_video') }}" class="modal-form"method="post"
                    id="pooja-video-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="booking_date" id="booking_id" value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-video-status">
                        <input type="hidden" name="order_id" id="order_id" value="{{ $details->order_id }}">
                        <input name="pooja_video" id="pooja_video" placehoder="{{ translate('Video URL') }}"
                            class="pooja-video form-control" data-id="{{ $details['id'] }}"
                            data-service="{{ $details['service_id'] }}" value="{{ $details['live_stream'] ?? '' }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
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
                    title: "{{ translate('chadhava_order_status') }}",
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
        // 
            $('.assign-astrologer-change').on('change', function() {
                var panditId = $(this).val();
                $('#change-pandit-id-val').val(panditId);
                Swal.fire({
                    title: 'Are You Sure To Change Pandit chadhava',
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
@endpush
