@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Chadhava| Order List'))
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <style>
        /* Optional: Style for the tooltip */
        .ui-tooltip {
            max-width: 300px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            color: #333;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .box {
            width: 200px;
            height: 200px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            overflow: hidden;
        }

        .box .b {
            border-radius: 50%;
            border-left: 4px solid;
            border-right: 4px solid;
            border-top: 4px solid transparent !important;
            border-bottom: 4px solid transparent !important;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: ro 2s infinite;
        }

        .box .b1 {
            border-color: #4A69BD;
            width: 120px;
            height: 120px;
        }

        .box .b2 {
            border-color: #F6B93B;
            width: 100px;
            height: 100px;
            animation-delay: 0.2s;
        }

        .box .b3 {
            border-color: #2ECC71;
            width: 80px;
            height: 80px;
            animation-delay: 0.4s;
        }

        .box .b4 {
            border-color: #34495E;
            width: 60px;
            height: 60px;
            animation-delay: 0.6s;
        }

        @keyframes ro {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            50% {
                transform: translate(-50%, -50%) rotate(-180deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
        }
    </style>
@endpush
@section('content')

    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('puja_order_progress') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($chadhavaOrder) }}</span>
            </h2>
        </div>
         <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="row g-3" id="order_stats">
                    {{-- Total Earning --}}
                    <div class="col-lg-4">
                        @php
                            $totalPaymentAmount = \App\Models\Chadhava_orders::where('type', 'chadhava')
                                ->where('status', 0)
                                ->where('payment_status', 1)
                                ->sum('pay_amount');
                        @endphp
                        <div class="card h-100 shadow-sm border-success border-1">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="text-success mb-1">
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentAmount), currencyCode: getCurrencyCode()) }}
                                    </h4>
                                    <p class="text-muted mb-0">Chadhava Success Earnings</p>
                                </div>
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                    alt="Earnings" width="50">
                            </div>
                        </div>
                    </div>

                    {{-- Remaining Stats --}}
                    <div class="col-lg-8">
                        <div class="row g-3">
                            {{-- Schedule Chadhava --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-info border-1">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h4 class="text-info mb-1">
                                                {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('order_status', 3)->count() }}
                                            </h4>
                                            <p class="text-muted mb-0">Total Scheduled Chadhava</p>
                                        </div>
                                        <img src="https://cdn-icons-png.flaticon.com/512/7474/7474976.png"
                                            alt="Schedule Icon" width="40">
                                    </div>
                                </div>
                            </div>

                            {{-- Live Chadhava --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-success border-1">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h4 class="text-success mb-1">
                                                {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('order_status', 4)->count() }}
                                            </h4>
                                            <p class="text-muted mb-0">Total Live Chadhava</p>
                                        </div>
                                        <img src="https://cdn-icons-png.flaticon.com/512/942/942748.png" alt="Live Icon"
                                            width="40">
                                    </div>
                                </div>
                            </div>

                            {{-- Video Share --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-primary border-1">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h4 class="text-primary mb-1">
                                                {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('order_status', 5)->count() }}
                                            </h4>
                                            <p class="text-muted mb-0">Total Video Shared</p>
                                        </div>
                                        <img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="Video Icon"
                                            width="40">
                                    </div>
                                </div>
                            </div>

                            {{-- Rejected Orders --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-warning border-1">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h4 class="text-warning mb-1">
                                                {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('status', 6)->where('order_status', 6)->count() }}
                                            </h4>
                                            <p class="text-muted mb-0">Rejected Orders</p>
                                        </div>
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828665.png"
                                            alt="Rejected Icon" width="40">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <form method="GET" action="{{ url()->current() }}" id="filterForm" class="row mb-3">
                                
                                    {{-- 🔹 Service Name --}}
                                    <div class="col-md-3">
                                        <label for="service_id" class="font-weight-bold">Service Name</label>
                                        @php
                                            $uniqueServices = $chadhavaOrder->pluck('chadhava')->filter()->unique('id');
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
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-md-12">
                                <table id="table" class="table table-bordered">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('SL') }}</th>
                                            <th>{{ translate('Chadava_id') }}</th>
                                            <th>{{ translate('chadhava_name_cateogry_venue') }}</th>
                                            <th>{{ translate('order_date_time') }}</th>
                                            <th>{{ translate('orders') }}</th>
                                            <th>{{ translate('members') }}</th>
                                            <th>{{ translate('total_amount') }}</th>
                                            <th>{{ translate('pandit_name') }}</th>
                                            <th>{{ translate('chadhava_status') }}</th>
                                            @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'change-pandit') ||Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'list') ||
                                            Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'detail'))
                                            <th>{{ translate('action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($chadhavaOrder as $key => $order)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>## {{ $order->service_id }}</td>
                                                <td>
                                                    <b>
                                                        <a href="{{ route('admin.chadhava.view', [$order['chadhava']['added_by'], $order['chadhava']['id']]) }}"
                                                            data-addedby="{{ $order['chadhava']['added_by'] }}"
                                                            data-id="{{ $order['chadhava']['id'] }}"
                                                            class="media flex-column align-items-start view-service">

                                                            {{-- Badges --}}
                                                            <div class="mb-1">
                                                                @if (isset($order['chadhava']) && isset($order['chadhava']['pooja_type']))
                                                                    @if ($order['chadhava']['pooja_type'] == '1')
                                                                        <span class="badge badge-danger">S</span>
                                                                    @else
                                                                        <span class="badge badge-danger">W</span>
                                                                    @endif
                                                                @endif

                                                                @if ($order['is_rejected'] == 1)
                                                                    <span class="badge badge-warning">R</span>
                                                                @endif
                                                            </div>

                                                            {{-- Service Name --}}
                                                            <div class="title-color hover-c1 mb-1">
                                                                <strong>
                                                                    {{ Str::limit($order['chadhava']['name'], 40) }}
                                                                </strong>
                                                            </div>

                                                            {{-- Pooja Venue --}}
                                                            <div class="text-muted mb-1">
                                                                {{ $order['chadhava']['chadhava_venue'] ?? 'Venue Not Available' }}
                                                            </div>

                                                            {{-- Booking Date --}}
                                                            <div class="dateBooking">
                                                                {{ $order->booking_date ? date('d ,F , l', strtotime($order->booking_date)) : 'No Date Available' }}
                                                            </div>
                                                        </a>
                                                    </b>
                                                </td>
                                                <td>
                                                    {{ date('d M Y H:i:s', strtotime($order->created_at)) }}
                                                </td>


                                                <td>{{ $order->total_orders }}</td>
                                                <td>
                                                @php
                                                    $members = explode('|', $order->members);
                                                    $totalCount = count($members);
                                                    $uniqueCount = count(array_unique($members));
                                                @endphp
                                                {{ $totalCount }}
                                                </td>
                                                <td>₹{{ $order->total_amount }}</td>
                                                <td>
                                                    @if ($order->pandit_assign != null)
                                                        <b>{{ @ucwords($order['pandit']['name']) }}</b>
                                                    @else
                                                        <span class="badge badge-soft-danger">Not Assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-soft-{{ $order->order_status == 0
                                                            ? 'primary'
                                                            : ($order->order_status == 1
                                                                ? 'success'
                                                                : ($order->order_status == 2
                                                                    ? 'danger'
                                                                    : ($order->order_status == 3
                                                                        ? 'warning'
                                                                        : ($order->order_status == 4
                                                                            ? 'secondary'
                                                                            : ($order->order_status == 5
                                                                                ? 'info'
                                                                                : ($order->order_status == 6
                                                                                    ? 'warning'
                                                                                    : 'light')))))) }}">
                                                        {{ $order->order_status == 0
                                                            ? 'Pending'
                                                            : ($order->order_status == 1
                                                                ? 'Completed'
                                                                : ($order->order_status == 2
                                                                    ? 'Cancel'
                                                                    : ($order->order_status == 3
                                                                        ? 'Schedule Time'
                                                                        : ($order->order_status == 4
                                                                            ? 'Live Pooja'
                                                                            : ($order->order_status == 5
                                                                                ? 'Share Soon'
                                                                                : ($order->order_status == 6
                                                                                    ? 'Rejected'
                                                                                    : 'Unknown Status')))))) }}
                                                    </span>
                                                </td>
                                                @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'change-panidt') ||Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'list') ||
                                                    Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'detail'))
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if (
                                                            \Illuminate\Support\Carbon::parse($order['booking_date'])->toDateString() <=
                                                                \Illuminate\Support\Carbon::now()->addDay()->toDateString())
                                                                @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'change-pandit'))
                                                            <a class="btn btn-outline-warning btn-sm square-btn"
                                                                title="{{ translate('pandit') }}"
                                                                href="javascript:void(0);" data-id="{{ $order->id }}"
                                                                data-serviceid="{{ $order->service_id }}"
                                                                data-bookingdate="{{ $order->booking_date }}"
                                                                onclick="pandit_model(this)">
                                                                <img src="{{ asset('public/assets/back-end/img/pooja/pandit.png') }}"
                                                                    alt="" width="20px" height="20px">
                                                            </a>
                                                            @endif
                                                        @else
                                                            <a class="btn btn-outline-danger btn-sm square-btn"
                                                                title="{{ translate('pandit') }}" disabled>
                                                                <img src="{{ asset('public/assets/back-end/img/pooja/bellboy.png') }}"
                                                                    alt="" width="20px" height="20px">
                                                            </a>
                                                        @endif
                                                        @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'list'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                target="_blank" title="All order list"
                                                                href="{{ route('admin.chadhava.order.SingleOrder', ['service_id' => $order->service_id, 'booking_date' => $order->booking_date, 'status' => $order->order_status]) }}">
                                                                <i class="tio-format-points nav-icon"></i>
                                                            </a>
                                                        @endif
                                                        @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'detail'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="Order Details" target="_blank"
                                                                href="{{ route('admin.chadhava.order.SingleOrderdetails', ['booking_date' => $order->booking_date, 'service_id' => $order->service_id, 'status' => 0]) }}">
                                                                <i class="tio-visible nav-icon"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>{{ translate('SL') }}</th>
                                            <th>{{ translate('Chadava_id') }}</th>
                                            <th>{{ translate('chadhava_name_cateogry_venue') }}</th>
                                            <th>{{ translate('order_date_time') }}</th>
                                            <th>{{ translate('orders') }}</th>
                                            <th>{{ translate('members') }}</th>
                                            <th>{{ translate('total_amount') }}</th>
                                            <th>{{ translate('pandit_name') }}</th>
                                            <th>{{ translate('chadhava_status') }}</th>
                                            @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'list') ||
                                            Helpers::modules_permission_check('Chadhava Order', 'Order By Chadhava', 'detail'))
                                            <th>{{ translate('action') }}</th>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box" style="display: none;">
            <div class="b b1"></div>
            <div class="b b2"></div>
            <div class="b b3"></div>
            <div class="b b4"></div>
        </div>
    </div>

    {{-- Chadhava Model PAndit Assing --}}
    <div class="modal fade" id="Assgine-the-pandit" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Change Pandit</h5>
                        <p class="text-muted small mt-1">
                            You are about to change the assigned Pandit (Purohit) for this Chadhava order. Please ensure
                            the new selection matches the customer's requirements and schedule.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                     <div class="">
                        <label class="font-weight-bold title-color fz-14">{{ translate('type_of_pandit_ji') }}</label>
                        <select name="astrologer_type" id="astrologer-type" class="astrologer-type form-control">
                            <option value="in house">In house</option>
                            <option value="freelancer">Freelancer</option>
                        </select>
                        <br>
                        <div class="" id="in-house">
                            <select name="assign_pandit" id="assign-inhouse-pandit" class="assign-pandit form-control">
                            </select>
                        </div>
                        <div class="" id="freelancer" style="display: none;">
                            <label
                                class="font-weight-bold title-color fz-14">{{ translate('freelancer_Astrologer') }}</label>
                            <select name="assign_pandit" id="assign-freelancer-pandit"
                                class="assign-pandit form-control">
                            </select>
                        </div>

                        <form action="{{ route('admin.chadhava.order.assign.allpandit') }}" method="post"
                            id="assign-pandit-form">
                            @csrf
                            <input type="hidden" name="booking_date" id="booking_date">
                            <input type="hidden" name="service_id" id="serviceID">
                            <input type="hidden" name="pandit_id" id="pandit-id-val">
                        </form>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    @endsection


    @push('script')
        <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
        <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
        <script>
            $('#date_type').change(function(e) {
                e.preventDefault();

                var value = $(this).val();
                if (value == 'custom_date') {
                    $('#from-to-div').show();
                } else {
                    $('#from-to-div').hide();
                }
            });
        </script>
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
            $('#service_id').on('change', function() {
                $('#table').DataTable().ajax.reload();
            });
            $('#order_status').on('change', function() {
                $('#table').DataTable().ajax.reload();
            });
            $(document).ready(function() {
                $('.ckeditor').ckeditor();
            });
        </script>
         <script>
            $('.assign-pandit').on('change', function() {
                var panditId = $(this).val();
                $('#pandit-id-val').val(panditId);
                Swal.fire({
                    title: 'Are You Sure To Assign  Chadhava Pandit.',
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

            function pandit_model(that) {
                $('.box').css('display', 'block');
                var panditId = $(that).data('id');
                var bookdate = $(that).data('bookingdate');
                var serviceId = $(that).data('serviceid');

                $('#serviceID').val(serviceId);
                $('#booking_date').val(bookdate);

                var inhouseList = "";
                var freelancerList = "";

                $.ajax({
                    type: "get",
                    url: "{{ url('admin/chadhava/order/getpandit') }}" + '/' + serviceId + '/' + bookdate,
                    success: function(response) {
                        if (response.status == 200) {
                            $('#assign-inhouse-pandit').html('');
                            if (response.inhouse.length > 0) {
                                inhouseList = `<option value="" selected disabled>Select Pandit Ji</option>`;
                                $.each(response.inhouse, function(key, value) {
                                    if (value.is_pandit_pooja_per_day > value.checkastro) {
                                        inhouseList += `<option value="${value.id}">${value.name}</option>`;
                                    }
                                });
                                $('#assign-inhouse-pandit').append(inhouseList);
                            } else {
                                $('#assign-inhouse-pandit').append(
                                    '<option value="" selected disabled>No Pandit Found</option>');
                            }
                            
                        if (response.freelancer.length > 0) {
                            let freelancerList = `<option value="" selected disabled>Select Pandit Ji</option>`;
                            let addedPanditIds = new Set(); // Track added IDs

                            $.each(response.freelancer, function(key, value) {
                                if (
                                    value.is_pandit_pooja_per_day > value.checkastro &&
                                    !addedPanditIds.has(value.id)
                                ) {
                                    let priceText = value.price ? `₹${parseInt(value.price).toLocaleString('en-IN')}` : 'N/A';
                                    freelancerList += `<option value="${value.id}">${value.name} - Price: ${value.price}</option>`;
                                    addedPanditIds.add(value.id); // Mark this ID as added
                                }
                            });

                            $('#assign-freelancer-pandit').html(freelancerList);
                        } else {
                            $('#assign-freelancer-pandit').html(
                                '<option value="" selected disabled>No Pandit Found</option>'
                            );
                        }
                            $('.box').css('display', 'none');
                            $('#Assgine-the-pandit').modal('show');
                        } else {
                            $('#assign-inhouse-pandit').html('<option value="" selected disabled>No Pandit Found</option>'
                            );
                        }

                    }
                });
            }
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
    @endpush
