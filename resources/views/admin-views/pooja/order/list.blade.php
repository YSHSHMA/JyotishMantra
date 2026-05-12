@extends('layouts.back-end.app')
@section('title', optional($orders->first())->status == 6 ? 'REJECTED ORDER | LIST' : 'POOJA ORDER | LIST')
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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

        .rotate-icon {
            animation: spin 2s linear infinite, moveX 3s ease-in-out infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes moveX {
            0% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(-20px);
            }

            100% {
                transform: translateX(0);
            }
        }
        #orderCheckboxes {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 10px;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        #selectAllOrders {
            margin-right: 5px;
        }

        label {
            font-size: 14px;
            font-weight: normal;
        }
    </style>
@endpush
@php
    use Carbon\Carbon;
    use App\Utils\Helpers;
    use function App\Utils\getNextPoojaDay;
@endphp
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('pooja_order_list') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($orders) }}</span>
            </h2>
        </div>
        <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="row g-2" id="order_stats">
                    <div class="col-lg-12">
                        @php
                            $totalPaymentSuccess = \App\Models\Service_order::where('type', 'pooja')
                                ->where('status', 0)->where('is_block', '!=', 9)
                                ->where('payment_status', 1)
                                ->sum('pay_amount');
                            $totalPaymentPending = \App\Models\Service_order::where('type', 'pooja')
                                ->where('status', 0)->where('is_block', '!=', 9)
                                ->where('status', 0)
                                ->where('payment_status', 0)
                                ->sum('pay_amount');
                            $totalPaymentFaild = \App\Models\Service_order::where('type', 'pooja')
                                ->where('status', 0)
                                ->where('status', 0)->where('is_block', '!=', 9)
                                ->where('payment_status', 2)
                                ->sum('pay_amount');
                        @endphp
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="card card-body h-100 justify-content-center">
                                    <div class="d-flex gap-2 justify-content-between align-items-center">
                                        <div class="d-flex flex-column align-items-start">
                                            <h3 class="mb-1 fz-24 text-success">
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentSuccess), currencyCode: getCurrencyCode()) }}
                                            </h3>
                                            <div class="text-capitalize mb-0 text-success">Pooja Success Earning</div>
                                        </div>
                                        <div>
                                            <img width="40" class="mb-2 rotate-icon"
                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.pooja.orders.list', 'all') }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-warning">
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentPending), currencyCode: getCurrencyCode()) }}
                                                </h3>
                                                <div class="text-capitalize mb-0 text-warning">Pooja Pending Earning</div>
                                            </div>
                                            <div>
                                                <img width="40" class="mb-2 rotate-icon"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.pooja.orders.list', 'all') }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-danger">
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentFaild), currencyCode: getCurrencyCode()) }}
                                                </h3>
                                                <div class="text-capitalize mb-0 text-danger">Pooja Faild Earning</div>
                                            </div>
                                            <div>
                                                <img width="40" class="mb-2 rotate-icon"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.pooja.orders.list', 'all') }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-info">
                                                    {{ \App\Models\Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->count() }}
                                                </h3>
                                                <div class="text-capitalize mb-0">TOTAL ORDER</div>
                                            </div>
                                            <div>
                                                <img width="40" class="mb-2"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/order.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.pooja.orders.list', 1) }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-success">
                                                    {{ \App\Models\Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->where('is_completed', 1)->where('status', 1)->where('payment_status', 1)->count() }}
                                                </h3>
                                                <div class="text-capitalize mb-0">COMPLETED ORDER</div>
                                            </div>
                                            <div>
                                                <img width="40"
                                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.pooja.orders.list', 0) }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-primary">
                                                    {{ \App\Models\Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->where('status', 0)->where('is_completed', 0)->where('payment_status', 1)->count() }}
                                                </h3>
                                                <div class="text-capitalize mb-0">PENDING ORDER</div>
                                            </div>
                                            <div>
                                                <img width="40"
                                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/panding.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.pooja.orders.list', 6) }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-danger">
                                                    {{ \App\Models\Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->where('status', 6)->where('order_status', 6)->count() }}
                                                </h3>
                                                <div class="text-capitalize mb-0">REJECTED ORDER</div>
                                            </div>
                                            <div>
                                                <img width="70"
                                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/reject.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="px-3 py-4">
                        <!-- Heading + Button Row -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Filter the Puja Service Orders</h4>
                            @if (Helpers::modules_permission_check('Pooja Order', 'All', 'block') || Helpers::modules_permission_check('Pooja Order', 'Pending', 'block') || Helpers::modules_permission_check('Pooja Order', 'Completed', 'block') || Helpers::modules_permission_check('Pooja Order', 'Canceled', 'block') || Helpers::modules_permission_check('Pooja Order', 'Rejected', 'block'))
                            <a href="javascript:void(0);" onclick="block_order(this)" class="btn btn-danger">
                                Block User Orders
                            </a>
                            @endif
                        </div>
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <form method="GET" action="{{ url()->current() }}" id="filterForm" class="row mb-3">
                                    {{-- 🔹 Payment Status --}}
                                    <div class="col-md-3">
                                        <label for="payment_status" class="font-weight-bold">Payment Status</label>
                                        @php
                                            $statusOptions = [0 => 'Pending', 1 => 'Success', 2 => 'Failed'];
                                        @endphp
                                        <select name="payment_status" id="payment_status" class="form-control"
                                            onchange="this.form.submit()">
                                            <option value="">All Payment Status</option>
                                            @foreach ($statusOptions as $statusValue => $statusLabel)
                                                <option value="{{ $statusValue }}"
                                                    {{ request('payment_status') == (string) $statusValue ? 'selected' : '' }}>
                                                    {{ $statusLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- 🔹 Service Name --}}
                                    <div class="col-md-3">
                                        <label for="service_id" class="font-weight-bold">Service Name</label>
                                        @php
                                            $uniqueServices = $orders->pluck('services')->filter()->unique('id');
                                        @endphp
                                        <select name="service_id" id="service_id" class="form-control"
                                            onchange="this.form.submit()">
                                            <option value="">All Services</option>
                                            @foreach ($uniqueServices as $service)
                                                <option value="{{ $service['id'] }}"
                                                    {{ request('service_id') == $service['id'] ? 'selected' : '' }}>
                                                    {{ Str::limit($service['name'], 40) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- 🔹 Pooja Status --}}
                                    <div class="col-md-3">
                                        <label for="order_status" class="font-weight-bold">Pooja Status</label>
                                        @php
                                            $orderStatusOptions = [
                                                3 => 'Scheduled',
                                                4 => 'Live/Shared Video',
                                                1 => 'Complete',
                                                6 => 'Rejected',
                                                2 => 'Cancelled',
                                            ];
                                        @endphp
                                        <select name="order_status" id="order_status" class="form-control"
                                            onchange="this.form.submit()">
                                            <option value="">All Order Status</option>
                                            @foreach ($orderStatusOptions as $statusValue => $statusLabel)
                                                <option value="{{ $statusValue }}"
                                                    {{ request('order_status') == (string) $statusValue ? 'selected' : '' }}>
                                                    {{ $statusLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Clear Filters Button --}}
                                    <div class="col-md-3 d-flex align-items-end">
                                        <a href="{{ url()->current() }}" class="btn btn-secondary w-100">
                                            Clear All Filters
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-md-12">
                                <div style="overflow: auto;">
                                    @include('admin-views.pooja.order.partial.payment')
                                    <table id="table" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('Service Name & Performing Date') }}</th>
                                                <th>{{ translate('order_Id') }}</th>
                                                <th>{{ translate('create_order_Date') }}</th>
                                                <th>{{ translate('customer') }}</th>
                                                <th>{{ translate('is_prashad') }}</th>
                                                <th>{{ translate('purohit') }}</th>
                                                <th>{{ translate('amount') }}</th>
                                                <th>{{ translate('status') }}</th>
                                                <th class="text-center">{{ translate('action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $key => $order)
                                                <tr>
                                                    <td style="position: relative; padding-left: 40px;">
                                                        <div
                                                            style="position: absolute;top: 0;left: 0;  height: 100%;width: 28px;  background-color: {{ $order->payment_status == 1 ? '#28a745' : ($order->payment_status == 2 ? '#dc3545' : '#ffc107') }}; display: flex; align-items: center;justify-content: center;writing-mode: vertical-rl;text-orientation: mixed;color: white;font-weight: bold;font-size: 12px;text-transform: uppercase; border-radius: 0 4px 4px 0;">
                                                            {{ $order->payment_status == 1 ? 'Success' : ($order->payment_status == 2 ? 'Failed' : 'Pending') }}
                                                        </div>
                                                        {{ $key + 1 }}
                                                    </td>
                                                    <td><b>
                                                        <a href="{{ route('admin.service.views', [$order['services']['added_by'], $order['services']['id']]) }}"
                                                            data-addedby="{{ $order['services']['added_by'] }}"
                                                            data-id="{{ $order['services']['id'] }}"
                                                            class="media align-items-center gap-2 view-service">
                                                        <span class="media-body title-color hover-c1">
                                                        <strong>{{ Str::limit($order['services']['name'], 40) }}
                                                        </strong>
                                                        </span>
                                                        </a>
                                                        <span class="dateBooking">
                                                        {{ $order->booking_date ? date('d ,F , l', strtotime($order->booking_date)) : 'No Date Available' }}
                                                        </span>
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <a href="#" data-id="{{ $order->id }}"
                                                            class="order-link">{{ $order->order_id }}</a>
                                                        @if ($order->is_new)
                                                            <span class="badge badge-success">New</span>
                                                        @endif
                                                        @if (isset($order['services']) && isset($order['services']['pooja_type']))
                                                            @if ($order['services']['pooja_type'] == '1')
                                                                <span class="badge badge-danger">S</span>
                                                            @else
                                                                <span class="badge badge-danger">W</span>
                                                            @endif
                                                        @endif
                                                        @if ($order['is_rejected'] == 1)
                                                            <span class="badge badge-warning">R</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ date('d M, Y h:i A', strtotime($order->created_at)) }}</td>
                                                    <td>
                                                        <div>
                                                            @if (isset($order['customers']) && !empty($order['customers']))
                                                                <b>
                                                                    <a href="{{ route('admin.customer.view', [$order['customers']['id'] ?? '']) }}"
                                                                        class="title-color hover-c1 d-flex align-items-center gap-10">
                                                                        {{ ucwords($order['customers']['f_name'] ?? '') }}
                                                                        {{ $order['customers']['l_name'] ?? '' }}
                                                                    </a>
                                                                </b>
                                                                <p>{{ $order['customers']['phone'] ?? 'N/A' }}</p>
                                                            @else
                                                                <p>Customer information is not available.</p>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($order->is_prashad == '1')
                                                            <span
                                                                class="badge badge-soft-success">{{ translate('Yes') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-soft-danger">{{ translate('No') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($order['pandit'] != null)
                                                            <b>{{ @ucwords($order['pandit']['name']) }}</b>
                                                        @else
                                                            <span class="badge badge-soft-danger">Not Assigned</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div>
                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->pay_amount - $order->coupon_amount), currencyCode: getCurrencyCode()) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-soft-{{ $order->status == 0
                                                                ? 'primary'
                                                                : ($order->status == 1
                                                                    ? 'success'
                                                                    : ($order->order_status == 3
                                                                        ? 'danger'
                                                                        : ($order->status == 6
                                                                            ? 'warning'
                                                                            : ($order->order_status == 4
                                                                                ? 'info'
                                                                                : ($order->order_status == 5
                                                                                    ? 'secondary'
                                                                                    : 'dark'))))) }}">
                                                            {{ $order->status == 0
                                                                ? 'Pending'
                                                                : ($order->status == 1
                                                                    ? 'Completed'
                                                                    : ($order->order_status == 3
                                                                        ? 'Scheduled'
                                                                        : ($order->status == 6
                                                                            ? 'Rejected'
                                                                            : ($order->order_status == 4
                                                                                ? 'Live'
                                                                                : ($order->order_status == 5
                                                                                    ? 'Video Share'
                                                                                    : 'Cancel'))))) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            @php
                                                                if ($order['services']['pooja_type'] == 0) {
                                                                    $nextBookingDate = '';
                                                                    $poojaw = json_decode(
                                                                        $order['services']['week_days'],
                                                                    );
                                                                    $timedadat = date(
                                                                        'H:i:s',
                                                                        strtotime($order['services']['pooja_time']),
                                                                    );
                                                                    $nextPoojaDay = getNextPoojaDay(
                                                                        $poojaw,
                                                                        $timedadat,
                                                                    );
                                                                    if ($nextPoojaDay) {
                                                                        $nextBookingDate = $nextPoojaDay->format(
                                                                            'Y-m-d H:i:s',
                                                                        );
                                                                    }
                                                                } else {
                                                                    //  pooja
                                                                    $current_date = date('Y-m-d');
                                                                    $earliestDate = null;
                                                                    $earliestTime = null;
                                                                    if (
                                                                        isset($order['services']['schedule']) &&
                                                                        !empty($order['services']['schedule'])
                                                                    ) {
                                                                        $event_date = json_decode(
                                                                            $order['services']['schedule'],
                                                                        );
                                                                        usort($event_date, function ($a, $b) {
                                                                            return strtotime($a->schedule) -
                                                                                strtotime($b->schedule);
                                                                        });
                                                                        foreach ($event_date as $entry) {
                                                                            $dt = date(
                                                                                'Y-m-d',
                                                                                strtotime($entry->schedule),
                                                                            );
                                                                            if (
                                                                                strtotime($dt) >
                                                                                strtotime($current_date)
                                                                            ) {
                                                                                $earliestDate = $dt;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                    $schedules = json_decode(
                                                                        $order['services']['schedule'],
                                                                    );
                                                                    if (is_array($schedules) || is_object($schedules)) {
                                                                        foreach ($schedules as $schedule) {
                                                                            $Schedule = date(
                                                                                'Y-m-d',
                                                                                strtotime($schedule->schedule),
                                                                            );
                                                                            $Time = \Carbon\Carbon::parse(
                                                                                $schedule->schedule_time,
                                                                            )->format('H:i:s');
                                                                            $dateList = $Schedule;
                                                                            $timeList = $Time;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            @if ($order->order_status == 6 && $order->status == 6)
                                                            @if (Helpers::modules_permission_check('Pooja Order', 'Rejected', 'schedule'))
                                                                <button class="btn btn-outline-primary btn-sm square-btn"
                                                                    data-toggle="modal" data-target="#rejected-modal"
                                                                    data-servicename="{{ $order['services']['name'] }}"
                                                                    data-orderid="{{ $order->order_id }}"
                                                                    data-id="{{ $order->id }}"
                                                                    data-customer="{{ $order['customers']['f_name'] ?? '' }} {{ $order['customers']['l_name'] ?? '' }}"
                                                                    data-poojaType="{{ $order['services']['pooja_type'] }}"
                                                                    @if ($order['services']['pooja_type'] == 0) data-poojatime="{{ \Carbon\Carbon::parse($order['services']['pooja_time'])->format('H:i:s') }}" 
                                                                    data-weeked="{{ $order['services']['week_days'] }}" 
                                                                    data-bookingdate="{{ date('Y-m-d', strtotime($nextBookingDate)) }}"
                                                                    @elseif($order['services']['pooja_type'] == 1)
                                                                    data-special="{{ $dateList }} :: {{ $timeList }}"
                                                                    data-bookingdate="{{ $earliestDate ? date('Y-m-d', strtotime($earliestDate)) : '' }}" @endif
                                                                    onclick="RejctedModel(this)">
                                                                    <i class="tio-message"></i>
                                                                </button>
                                                                @endif
                                                            @else
                                                            @if (Helpers::modules_permission_check('Pooja Order', 'All', 'detail') || Helpers::modules_permission_check('Pooja Order', 'Pending', 'detail') || Helpers::modules_permission_check('Pooja Order', 'Completed', 'detail') || Helpers::modules_permission_check('Pooja Order', 'Canceled', 'detail'))
                                                                <a class="btn btn-outline-primary btn-sm square-btn"
                                                                    title="{{ translate('view') }}"
                                                                    href="{{ route('admin.pooja.orders.details', [$order['id']]) }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                        height="12" viewBox="0 0 14 12" fill="none"
                                                                        class="svg replaceds-svg">
                                                                        <path
                                                                            d="M6.79584 3.75937C6.86389 3.75234 6.93195 3.75 7 3.75C8.2882 3.75 9.33333 4.73672 9.33333 6C9.33333 7.24219 8.2882 8.25 7 8.25C5.68993 8.25 4.66667 7.24219 4.66667 6C4.66667 5.93437 4.6691 5.86875 4.67639 5.80313C4.90243 5.90859 5.16493 6 5.44445 6C6.30243 6 7 5.32734 7 4.5C7 4.23047 6.90521 3.97734 6.79584 3.75937ZM11.6813 2.63906C12.8188 3.65625 13.5795 4.85391 13.9392 5.71172C14.0194 5.89687 14.0194 6.10312 13.9392 6.28828C13.5795 7.125 12.8188 8.32266 11.6813 9.36094C10.5365 10.3875 8.96389 11.25 7 11.25C5.03611 11.25 3.46354 10.3875 2.31924 9.36094C1.18174 8.32266 0.42146 7.125 0.059818 6.28828C0.0203307 6.19694 0 6.09896 0 6C0 5.90104 0.0203307 5.80306 0.059818 5.71172C0.42146 4.85391 1.18174 3.65625 2.31924 2.63906C3.46354 1.61344 5.03611 0.75 7 0.75C8.96389 0.75 10.5365 1.61344 11.6813 2.63906ZM7 2.625C5.06771 2.625 3.5 4.13672 3.5 6C3.5 7.86328 5.06771 9.375 7 9.375C8.93229 9.375 10.5 7.86328 10.5 6C10.5 4.13672 8.93229 2.625 7 2.625Z"
                                                                            fill="#0177CD"></path>
                                                                    </svg>
                                                                </a>
                                                                @endif
                                                                @if (Helpers::modules_permission_check('Pooja Order', 'All', 'download-invoice') || Helpers::modules_permission_check('Pooja Order', 'Pending', 'download-invoice') || Helpers::modules_permission_check('Pooja Order', 'Completed', 'download-invoice') || Helpers::modules_permission_check('Pooja Order', 'Canceled', 'download-invoice'))
                                                                <a class="btn btn-outline-info btn-sm square-btn"
                                                                    target="_blank"
                                                                    href="{{ route('admin.pooja.orders.generate.invoice', $order['id']) }}">
                                                                    <i class="tio-download-to"></i>
                                                                </a>
                                                                @endif
                                                            @endif
                                                            
                                                        </div>
                                                        @if (Helpers::modules_permission_check('Pooja Order', 'All', 'pay') || Helpers::modules_permission_check('Pooja Order', 'Pending', 'pay') || Helpers::modules_permission_check('Pooja Order', 'Completed', 'pay') || Helpers::modules_permission_check('Pooja Order', 'Canceled', 'pay') || Helpers::modules_permission_check('Pooja Order', 'Rejected', 'pay'))
                                                        @if ($order->payment_status==0)
                                                            <button class="btn btn-primary" onclick="pendingOrder('{{$order->order_id}}')">Pay</button>
                                                        @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('Service Name & Performing Date') }}</th>
                                                <th>{{ translate('order_Id') }}</th>
                                                <th>{{ translate('create_order_Date') }}</th>
                                                <th>{{ translate('customer') }}</th>
                                                <th>{{ translate('is_prashad') }}</th>
                                                <th>{{ translate('purohit') }}</th>
                                                <th>{{ translate('amount') }}</th>
                                                <th>{{ translate('status') }}</th>
                                                <th class="text-center">{{ translate('action') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Model --}}
    <!-- Modal Structure -->
    <div id="orderModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Order details will be populated here -->
                    <table class="table table-bordered">
                        <tbody id="order-details">
                            <!-- Table rows will be populated by jQuery -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Rejected Model --}}
    <div class="modal fade" id="rejectedModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Rejected ::<span id="OrderIdVAl"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('admin.pooja.orders.updatedOrder') }}" method="post"
                                id="Rejcted_model">
                                @csrf
                                <input type="hidden" name="order_id" id="OrderId">
                                <input type="hidden" name="service_id" id="ServiceId">
                                <input type="hidden" name="booking_date" id="NextBookingDate">
                                {{-- <input type="hidden" name="service_name" id="ServiceName">
              <input type="hidden" name="week_days" id="WeekDays">
              <input type="hidden" name="specail_pooja" id="SpecialPooja">
              <input type="hidden" name="pooja_time" id="PoojaTime"> --}}
                                <textarea class="ckeditor" id="editor" placeholder="Enter Rejected Reason" name="reject_reason"
                                    style="height:300px"></textarea>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th><strong>Service Name</strong></th>
                                        <td colspan="2"><span id="NameofService"></span></td>
                                    </tr>
                                    <tr>
                                        <th><strong> Week</strong></th>
                                        <td colspan="2"><span id="NextWeek"></span></td>
                                    </tr>
                                    <tr>
                                        <th><strong>Date time</strong></th>
                                        <td colspan="2"><span id="NextDateBooking"></span> <span
                                                id="timePooja"></span></td>
                                    </tr>
                                    <tr>
                                        <th><strong>Special Pooja</strong></th>
                                        <td colspan="2"><span id="NextSpecial"></span></td>
                                    </tr>
                                    <tr>
                                        <th><strong>Customer Name</strong></th>
                                        <td colspan="2"><span id="customerName"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- bLOCK uSER dETAILS--}}
    <div class="modal fade" id="BlockUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="blockUserOrdersLabel">Block User Service Orders</h5>
                        <p class="mb-0 text-muted" style="font-size: 14px;">
                            Select the customer ID below. All service orders for this customer will be blocked based on your
                            selection.
                        </p>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="customer_id">Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" style="width:100%;">
                            <option value="">Search by name or mobile...</option>
                            @foreach ($users as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->f_name }} {{ $customer->l_name }} ({{ $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Orders List -->
                    <div id="ordersList" class="mt-3" style="display:none;">
                        <div class="form-check mb-2">
                            <input type="checkbox" id="selectAllOrders" class="form-check-input">
                            <label for="selectAllOrders" class="form-check-label">Select All Orders</label>
                        </div>
                        <div id="orderCheckboxes"></div>
                    </div>

                    <button id="blockOrdersBtn" class="btn btn-danger mt-3 w-100" disabled>Block Selected
                        Orders</button>


                </div>

            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let table = $('#table').DataTable({
            pageLength: 20,
            scrollY: '500px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            fixedFooter: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });
        $('#payment_status').on('change', function() {
            $('#table').DataTable().ajax.reload();
        });
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script>
        $(document).ready(function() {
            // Capture click event on order links
            $('.order-link').click(function(e) {
                e.preventDefault();
                var orderId = $(this).data('id');

                // Make Ajax call to fetch order details
                $.ajax({
                    url: "{{ url('admin/pooja/get-order-details') }}",
                    type: 'GET',
                    data: {
                        id: orderId
                    },
                    success: function(data) {
                        function formatDate(dateString) {
                            const date = new Date(dateString);
                            const options = {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            };
                            return date.toLocaleString('en-US', options).replace(',', '');
                        }
                        console.log(data);
                        var baseUrl = "{{ url('/') }}";
                        let membersArray = JSON.parse(data.members);
                        let address = data.services.pooja_venue;
                        $('#order-details').html(`
                  <tr><th><b>Booking Id</b></th><td>${data.order_id}</td><th><b>Booking Date</b></th><td>${formatDate(data.created_at)}</td> <th><b>Payment Mode</b></th><td>${data.payment_id && data.wallet_translation_id ? 'Online/Wallet' :data.payment_id ? 'Online' :data.wallet_translation_id ? 'Wallet' :'Online/Wallet'}</td></tr>
                  <tr><th><b>Pooja Details</b></th><td colspan=3><b>Pooja Name:</b>${data.services.name},<br><b>Pooja Venue:</b>${address}</td><th><b>Prashad(YES/NO)</b></th><td><span class="badge badge-soft-${data.prashad_status == 0 ? 'primary' : (data.prashad_status == 1 ? 'success' : 'danger')}">
                              ${data.prashad_status == 0 ? 'No' : (data.prashad_status == 1 ? 'Yes' : 'Canceled')}
                          </span></td></tr>
                  <tr>
                      <th><b>Pandit Name/Email</b></th>
                      <td colspan=2>${data.pandit_assign ? `${data.astrologer.name}<br>${data.astrologer.email}` : 'Not Assigned'}</td>
                      <th colspan=2><b>Pandit Ji Mobile Number</b></th>
                      <td>${data.pandit_assign ? data.astrologer.mobile_no : 'Not Assigned'}</td>
                  </tr>
                  <tr><th><b>Customer Name/Email</b></th><td colspan=2>${data.customers.name}<br>${data.customers.email}</td><th   colspan=2><b>Mobile Number</b></th><td>${data.customers.phone}</td></tr>
                  <tr>
                      <th><b>Order Status</b></th>
                      <td  colspan=2>
                          <span class="badge badge-soft-${data.status == 0 ? 'primary' : (data.status == 1 ? 'success' : (data.status == 6 ? 'warning' : 'danger'))}">
                              ${data.status == 0 ? 'Pending' : (data.status == 1 ? 'Completed' : (data.status == 6 ? 'Rejected' : 'Canceled'))}
                          </span>
  
                      </td>
                      <th><b>Pooja Type</b></th>
                      <td  colspan=2>
                          <span class="badge badge-soft-${data.services.pooja_type == 0 ? 'primary' : (data.services.pooja_type == 1 ? 'success' : 'info')}">
                              ${data.services.pooja_type == 0 ? 'Weekly Pooja' : (data.services.pooja_type == 1 ? 'Special Pooja' : 'Weekly Pooja')}
                          </span>
                      </td>
                      
                  </tr> 
                  <tr>
                      <th><b>Number of Members Name</b></th>
                          <td>${membersArray}</td>
                      <th><b>Pooja Video</b></th>
                          <td>
                              ${data.pooja_video ? `<a href="${data.pooja_video}" target="_blank">View Video</a>` : 'No video available'}
                          </td>
                      <th><b>Pooja Certificate</b></th>
                          <td>
                              ${data.pooja_certificate ? `<img src="${baseUrl}/public/assets/back-end/img/certificate/pooja/${data.pooja_certificate}" alt="Pooja Certificate" style="max-width:100px;">` : 'Certificate pending'}
                          </td>
                  </tr>
                  <tr>
                      <th><b>Package</b></th>
                      <td  colspan=2>
                          ${data.packages.title}
                      </td>
                      <th><b>Package Price Pay</b></th><td  colspan=2>₹  ${data.package_price}</td>
                  </tr> 
                  <tr>
                      <th colspan="3"><b>Charity</b></th>
                      <th colspan="3"><b>Charity Product Price</b></th>
                  </tr>
                  ${data.product_leads.map(lead => `
                    <tr>
                        <td colspan=3>${lead.product_name}</td>
                        <td colspan=3>₹ ${lead.product_price}</td>
                    </tr>
                        `).join('')}
                  <th colspan=3><b>Total Amount Pay</b></th><td  colspan=3>₹  ${data.pay_amount}</td>
                  `);
                        // Show the modal
                        $('#orderModal').modal('show');
                    },
                    error: function() {
                        alert('Failed to fetch order details.');
                    }
                });
            });
        });
    </script>
    <script>
        function RejctedModel(that) {
            var orderid = $(that).data('orderid');
            var id = $(that).data('id');
            var servicename = $(that).data('servicename');
            var weeked = $(that).data('weeked');
            var special = $(that).data('special');
            var customer = $(that).data('customer');
            var poojatime = $(that).data('poojatime');
            var bookingDate = $(that).data('bookingdate');
            var poojaType = $(that).data('poojaType');

            // Populate the modal fields
            $('#OrderId').val(orderid);
            $('#ServiceId').val(id);
            $('#ServiceName').val(servicename);
            $('#PoojaTime').val(poojatime);
            $('#WeekDays').val(weeked);
            $('#SpecialPooja').val(special);
            $('#NextBookingDate').val(bookingDate);
            // Populate the table with dynamic content
            $('#NameofService').text(servicename);
            $('#NextWeek').text(weeked);
            $('#timePooja').text(poojatime);
            $('#NextDateBooking').text(bookingDate);
            $('#NextSpecial').text(special);
            $('#OrderIdVAl').text(orderid);
            $('#customerName').text(customer);
            $('#NameCustomer').text(customer);
            $('#rejectedModal').modal('show');

            var message = `<p>Dear ${customer},</p>
             <p>We regret to inform you that your booking for the pooja <strong>${servicename}</strong> has been rejected.</p>
             <p>The next available date for this pooja is on <strong>${bookingDate}</strong>.</p>
             <p>We apologize for the inconvenience. Please feel free to contact us if you have any questions.</p>
             <p>Best regards,</p>
             <p>Mahakal.com</p>`;
            CKEDITOR.instances.editor.setData(message);
        }
        let searchDelay;
        $('#datatableSearch_').on('keyup', function() {
            clearTimeout(searchDelay);
            searchDelay = setTimeout(function() {
                table.ajax.reload();
            }, 500);
        });
    </script>
    {{-- pending order pay --}}
    <script>
        function pendingOrder(orderId) {
            if (!orderId) {
                alert('Order ID not found');
            } else {
                $('#pending-order-id').val(orderId);
                $('.pooja-pending-form').submit();
            }
        }

        function block_order(el) {
            $('#BlockUserModal').modal('show'); // Show modal
        }
        $(document).ready(function() {

            //  Enable search in select
            $('#customer_id').select2({
                placeholder: 'Search by name or mobile...',
                allowClear: true,
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') return data;
                    let term = params.term.toLowerCase();
                    let text = data.text.toLowerCase();
                    if (text.includes(term)) return data;
                    return null;
                }
            });

            //  Fetch orders when customer selected
            $('#customer_id').on('change', function() {
                let customerId = $(this).val();
                if (!customerId) {
                    $('#ordersList').hide();
                    $('#orderCheckboxes').html('');
                    $('#blockOrdersBtn').prop('disabled', true);
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.pooja.get-customer-orders') }}",
                    type: "GET",
                    data: {
                        customer_id: customerId
                    },
                    success: function(response) {
                        if (response.orders.length > 0) {
                            let html = '';
                            response.orders.forEach(order => {
                                html += `
                            <div class="form-check">
                                <input type="checkbox" name="order_ids[]" value="${order.order_id}" class="form-check-input orderCheckbox">
                                <label class="form-check-label">Order ID: ${order.order_id}</label>
                            </div>
                        `;
                            });
                            $('#orderCheckboxes').html(html);
                            $('#ordersList').show();
                        } else {
                            $('#orderCheckboxes').html(
                                '<p class="text-muted">No orders found for this customer.</p>'
                            );
                            $('#ordersList').show();
                        }
                    }
                });
            });

            //  Select all orders
            $(document).on('change', '#selectAllOrders', function() {
                $('.orderCheckbox').prop('checked', $(this).prop('checked'));
                toggleBlockButton();
            });

            //  Enable/Disable Block button
            $(document).on('change', '.orderCheckbox', function() {
                toggleBlockButton();
            });

            function toggleBlockButton() {
                let checked = $('.orderCheckbox:checked').length > 0;
                $('#blockOrdersBtn').prop('disabled', !checked);
            }

            //  Block selected orders
            $('#blockOrdersBtn').click(function() {
                let selectedOrders = $('.orderCheckbox:checked').map(function() {
                    return $(this).val();
                }).get();

                $.ajax({
                    url: "{{ route('admin.pooja.block-orders') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order_ids: selectedOrders
                    },
                    success: function(response) {
                        toastr.success(response.message);

                        //  Modal hide
                        $('#BlockUserModal').modal('hide');
                        $('#ordersList').hide();
                        $('#orderCheckboxes').html('');
                        $('#blockOrdersBtn').prop('disabled', true);
                        $('#customer_id').val('').trigger('change');
                    }
                });
            });

        });
    </script>
@endpush