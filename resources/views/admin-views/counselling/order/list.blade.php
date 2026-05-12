@extends('layouts.back-end.app')
@section('title', 'COUNSELLING ORDER | LIST')
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
    // use function App\Utils\getNextPoojaDay;
@endphp
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('counselling_Order_List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($orders) }}</span>
            </h2>
        </div>

        <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="row g-2" id="order_stats">
                    <div class="col-lg-12">
                        @php
                            $totalPaymentSuccess = \App\Models\Service_order::where('type', 'counselling')
                                ->where('status', 0)
                                ->where('is_block', '!=', 9)
                                ->where('payment_status', 1)
                                ->sum('pay_amount');
                            $totalPaymentPending = \App\Models\Service_order::where('type', 'counselling')
                                ->where('status', 0)
                                ->where('is_block', '!=', 9)
                                ->where('payment_status', 0)
                                ->sum('pay_amount');
                            $totalPaymentFaild = \App\Models\Service_order::where('type', 'counselling')
                                ->where('status', 0)
                                ->where('is_block', '!=', 9)
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
                                            <div class="text-capitalize mb-0 text-success">Success Earning</div>
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
                                <a href="{{ route('admin.counselling.order.list', 'all') }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-warning">
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentPending), currencyCode: getCurrencyCode()) }}
                                                </h3>
                                                <div class="text-capitalize mb-0 text-warning">Pending Earning
                                                </div>
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
                                <a href="{{ route('admin.counselling.order.list', 'all') }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-danger">
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentFaild), currencyCode: getCurrencyCode()) }}
                                                </h3>
                                                <div class="text-capitalize mb-0 text-danger">Failed Earning
                                                </div>
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
                                <a href="{{ route('admin.counselling.order.list', 'all') }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-info">
                                                    {{ \App\Models\Service_order::where('type', 'counselling')->where('is_block', '!=', 9)->count() }}
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
                                <a href="{{ route('admin.counselling.order.list', 0) }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-primary">
                                                    {{ \App\Models\Service_order::where('type', 'counselling')->where('is_block', '!=', 9)->where('status', 0)->count() }}
                                                </h3>
                                                <div class="text-capitalize mb-0">PENDING ORDER</div>
                                            </div>
                                            <div>
                                                <img width="40"
                                                    class="mb-2"src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/pending.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.counselling.order.list', 1) }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-success">
                                                    {{ \App\Models\Service_order::where('type', 'counselling')->where('is_block', '!=', 9)->where('status', 1)->count() }}
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
                                <a href="{{ route('admin.counselling.order.list', 2) }}" class="text-decoration-none">
                                    <div class="card card-body h-100 justify-content-center">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fz-24 text-primary">
                                                    {{ \App\Models\Service_order::where('type', 'counselling')->where('is_block', '!=', 9)->where('status', 2)->count() }}
                                                </h3>
                                                <div class="text-capitalize mb-0">CANCEL ORDER</div>
                                            </div>
                                            <div>
                                                <img width="40"
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
                        @if (Helpers::modules_permission_check('Consultation Order', 'All', 'block') || Helpers::modules_permission_check('Consultation Order', 'Pending', 'block') || Helpers::modules_permission_check('Consultation Order', 'Completed', 'block') || Helpers::modules_permission_check('Consultation Order', 'Canceled', 'block'))
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Filter the Counselling Service Orders</h4>
                            <a href="javascript:void(0);" onclick="block_order(this)" class="btn btn-danger">
                                Block User Orders
                            </a>
                        </div>
                        @endif
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <form method="GET" action="{{ url()->current() }}" id="filterForm" class="row mb-3">
                                    {{-- 🔹 Payment Status --}}
                                    <div
                                        class="{{ str_contains(url()->current(), 'list/all') ? 'col-md-3' : 'col-md-4' }}">
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
                                    <div
                                        class="{{ str_contains(url()->current(), 'list/all') ? 'col-md-3' : 'col-md-4' }}">
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
                                    {{-- 🔹 Counselling Status --}}
                                    @if (str_contains(url()->current(), 'list/all'))
                                    <div
                                        class="{{ str_contains(url()->current(), 'list/all') ? 'col-md-3' : 'col-md-4' }}">
                                        <label for="status" class="font-weight-bold">Counselling Status</label>
                                        @php
                                            $orderStatusOptions = [
                                                0 => 'Pending',
                                                // 4 => 'Live/Shared Video',
                                                1 => 'Complete',
                                                // 6 => 'Rejected',
                                                2 => 'Cancelled',
                                            ];
                                        @endphp
                                        <select name="status" id="status" class="form-control"
                                            onchange="this.form.submit()">
                                            <option value="">All Order Status</option>
                                            @foreach ($orderStatusOptions as $statusValue => $statusLabel)
                                                <option value="{{ $statusValue }}"
                                                    {{ request('status') == (string) $statusValue ? 'selected' : '' }}>
                                                    {{ $statusLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>    
                                    @endif
                                    
                                    {{-- Clear Filters Button --}}
                                    <div
                                        class="{{ str_contains(url()->current(), 'list/all') ? 'col-md-3' : 'col-md-4' }} d-flex align-items-end">
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
                                    @include('admin-views.counselling.order.partial.payment')
                                    <table id="table" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('Service Name') }}</th>
                                                <th>{{ translate('order_Id') }}</th>
                                                <th>{{ translate('create_order_Date') }}</th>
                                                <th>{{ translate('customer') }}</th>
                                                <th>{{ translate('purohit') }}</th>
                                                <th>{{ translate('amount') }}</th>
                                                <th>{{ translate('status') }}</th>
                                                @if (Helpers::modules_permission_check('Consultation Order', 'All', 'detail') || Helpers::modules_permission_check('Consultation Order', 'Pending', 'detail') || Helpers::modules_permission_check('Consultation Order', 'Completed', 'detail') || Helpers::modules_permission_check('Consultation Order', 'Canceled', 'detail') || Helpers::modules_permission_check('Consultation Order', 'All', 'pay') || Helpers::modules_permission_check('Consultation Order', 'Pending', 'pay') || Helpers::modules_permission_check('Consultation Order', 'Completed', 'pay') || Helpers::modules_permission_check('Consultation Order', 'Canceled', 'pay') || Helpers::modules_permission_check('Consultation Order', 'All', 'download') || Helpers::modules_permission_check('Consultation Order', 'Pending', 'download') || Helpers::modules_permission_check('Consultation Order', 'Completed', 'download') || Helpers::modules_permission_check('Consultation Order', 'Canceled', 'download'))
                                                <th class="text-center">{{ translate('action') }}</th>
                                                @endif
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
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:0"
                                                            data-id="{{ $order->id }}">{{ $order->order_id }}</a>
                                                        @if ($order->is_new)
                                                            <span class="badge badge-success">New</span>
                                                        @endif
                                                        {{-- @if (isset($order['services']) && isset($order['services']['pooja_type']))
                                                            @if ($order['services']['pooja_type'] == '1')
                                                                <span class="badge badge-danger">S</span>
                                                            @else
                                                                <span class="badge badge-danger">W</span>
                                                            @endif
                                                        @endif --}}
                                                        {{-- @if ($order['is_rejected'] == 1)
                                                            <span class="badge badge-warning">R</span>
                                                        @endif --}}
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
                                                            class="badge badge-soft-{{ $order->status == 0 ? 'primary' : ($order->status == 1 ? 'success' : 'dark') }}">
                                                            {{ $order->status == 0 ? 'Pending' : ($order->status == 1 ? 'Completed' : 'Cancel') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            @if (Helpers::modules_permission_check('Consultation Order', 'All', 'detail') || Helpers::modules_permission_check('Consultation Order', 'Pending', 'detail') || Helpers::modules_permission_check('Consultation Order', 'Completed', 'detail') || Helpers::modules_permission_check('Consultation Order', 'Canceled', 'detail'))
                                                            <a class="btn btn-outline-primary btn-sm square-btn"
                                                                title="{{ translate('view') }}"
                                                                href="{{ route('admin.counselling.order.details', [$order['id']]) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                    height="12" viewBox="0 0 14 12" fill="none"
                                                                    class="svg replaceds-svg">
                                                                    <path
                                                                        d="M6.79584 3.75937C6.86389 3.75234 6.93195 3.75 7 3.75C8.2882 3.75 9.33333 4.73672 9.33333 6C9.33333 7.24219 8.2882 8.25 7 8.25C5.68993 8.25 4.66667 7.24219 4.66667 6C4.66667 5.93437 4.6691 5.86875 4.67639 5.80313C4.90243 5.90859 5.16493 6 5.44445 6C6.30243 6 7 5.32734 7 4.5C7 4.23047 6.90521 3.97734 6.79584 3.75937ZM11.6813 2.63906C12.8188 3.65625 13.5795 4.85391 13.9392 5.71172C14.0194 5.89687 14.0194 6.10312 13.9392 6.28828C13.5795 7.125 12.8188 8.32266 11.6813 9.36094C10.5365 10.3875 8.96389 11.25 7 11.25C5.03611 11.25 3.46354 10.3875 2.31924 9.36094C1.18174 8.32266 0.42146 7.125 0.059818 6.28828C0.0203307 6.19694 0 6.09896 0 6C0 5.90104 0.0203307 5.80306 0.059818 5.71172C0.42146 4.85391 1.18174 3.65625 2.31924 2.63906C3.46354 1.61344 5.03611 0.75 7 0.75C8.96389 0.75 10.5365 1.61344 11.6813 2.63906ZM7 2.625C5.06771 2.625 3.5 4.13672 3.5 6C3.5 7.86328 5.06771 9.375 7 9.375C8.93229 9.375 10.5 7.86328 10.5 6C10.5 4.13672 8.93229 2.625 7 2.625Z"
                                                                        fill="#0177CD"></path>
                                                                </svg>
                                                            </a>
                                                            @endif
                                                            @if ($order->payment_status == 0)
                                                            @if (Helpers::modules_permission_check('Consultation Order', 'All', 'pay') || Helpers::modules_permission_check('Consultation Order', 'Pending', 'pay') || Helpers::modules_permission_check('Consultation Order', 'Completed', 'pay') || Helpers::modules_permission_check('Consultation Order', 'Canceled', 'pay'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                    href="javascript:0" onclick="pendingOrder('{{ $order->order_id }}')">
                                                                    <i class="tio-money-vs"></i>
                                                                </a>
                                                                @endif
                                                            @else
                                                            @if (Helpers::modules_permission_check('Consultation Order', 'All', 'download') || Helpers::modules_permission_check('Consultation Order', 'Pending', 'download') || Helpers::modules_permission_check('Consultation Order', 'Completed', 'download') || Helpers::modules_permission_check('Consultation Order', 'Canceled', 'download'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                target="_blank"
                                                                href="{{ route('admin.counselling.order.generate.invoice', $order['id']) }}">
                                                                <i class="tio-download-to"></i>
                                                            </a>
                                                            @endif
                                                            @endif

                                                        </div>
                                                        {{-- @if ($order->payment_status == 0)
                                                            <button class="btn btn-primary"
                                                                onclick="pendingOrder('{{ $order->order_id }}')">Pay</button>
                                                        @endif --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th>{{ translate('Service Name') }}</th>
                                                <th>{{ translate('order_Id') }}</th>
                                                <th>{{ translate('create_order_Date') }}</th>
                                                <th>{{ translate('customer') }}</th>
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

    {{-- bLOCK uSER dETAILS --}}
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
                $('.offlinepooj-pending-form').submit();
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
                    url: "{{ route('admin.counselling.order.get-customer-orders') }}",
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
                    url: "{{ route('admin.counselling.order.block-orders') }}",
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
