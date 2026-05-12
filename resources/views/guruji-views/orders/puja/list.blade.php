@extends('layouts.back-end.app-guruji')
@section('title', optional($orders->first())->status == 6 ? 'REJECTED ORDER | LIST' : 'POOJA ORDER | LIST')
@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Utils\Helpers;
use function App\Utils\getNextPoojaDay;
if (auth('guruji')->check()) {
    $vendorId = auth('guruji')->user()->id;
} 
@endphp
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <style>
        .ui-tooltip {
            max-width: 300px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            color: #333;
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
    </style>
@endpush
@section('content')

    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('puja_order_progress') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($orders) }}</span>
            </h2>
        </div>
        <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3">
                        <a href="#" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-info">
                                        {{ \App\Models\Service_order::where('type', 'pooja')->where('pandit_assign', $vendor->id)->where('is_block', '!=', 9)->count() }}
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
                        <a href="#" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-success">
                                        {{ \App\Models\Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->where('pandit_assign', $vendor->id)->where('is_completed', 1)->where('status', 1)->where('payment_status', 1)->count() }}
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
                        <a href="#" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-primary">
                                        {{ \App\Models\Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->where('pandit_assign', $vendor->id)->where('status', 0)->where('is_completed', 0)->where('payment_status', 1)->count() }}
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
                        <a href="#}" class="text-decoration-none">
                            <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-danger">
                                        {{ \App\Models\Service_order::where('type', 'pooja')->where('pandit_assign', $vendor->id)->where('is_block', '!=', 9)->where('status', 6)->where('order_status', 6)->count() }}
                                    </h3>
                                    <div class="text-capitalize mb-0">REJECTED ORDER</div>
                                </div>
                                <div>
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828665.png"
                                        alt="Rejected Icon" width="40">
                                </div>
                            </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="row g-3" id="order_stats">
                    {{-- Total Earning --}}
                  
                    {{-- Remaining Stats --}}
                    {{-- Schedule Pooja --}}
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-info border-1">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="text-info mb-1">
                                        {{ \App\Models\Service_order::where('type', 'pooja')->where('order_status', 3)->where('pandit_assign',$vendor->id)->count() }}
                                    </h4>
                                    <p class="text-muted mb-0">Total Scheduled Pooja</p>
                                </div>
                                <img src="https://cdn-icons-png.flaticon.com/512/7474/7474976.png"
                                    alt="Schedule Icon" width="40">
                            </div>
                        </div>
                    </div>

                    {{-- Live Pooja --}}
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-success border-1">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="text-success mb-1">
                                        {{ \App\Models\Service_order::where('type', 'pooja')->where('order_status', 4)->where('pandit_assign',$vendor->id)->count() }}
                                    </h4>
                                    <p class="text-muted mb-0">Total Live Pooja</p>
                                </div>
                                <img src="https://cdn-icons-png.flaticon.com/512/942/942748.png" alt="Live Icon"
                                    width="40">
                            </div>
                        </div>
                    </div>

                    {{-- Video Share --}}
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-primary border-1">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="text-primary mb-1">
                                        {{ \App\Models\Service_order::where('type', 'pooja')->where('order_status', 5)->where('pandit_assign',$vendor->id)->count() }}
                                    </h4>
                                    <p class="text-muted mb-0">Total Video Shared</p>
                                </div>
                                <img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="Video Icon"
                                    width="40">
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
                                
                                    {{--  Service Name --}}
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
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-md-12">
                                <table id="table" class="table table-bordered">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('SL') }}</th>
                                            <th>{{ translate('puja_Id') }}</th>
                                            <th>{{ translate('puja_name_cateogry_venue') }}</th>
                                            <th>{{ translate('order_date_time') }}</th>
                                            <th>{{ translate('orders') }}</th>
                                            <th>{{ translate('members') }}</th>
                                            <th>{{ translate('puja_status') }}</th>
                                            <th>{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($orders as $key => $order)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>## {{ $order->service_id }}</td>
                                                <td>
                                                    <b>
                                                        <a href="#" data-addedby="{{ $order['services']['added_by'] }}" data-id="{{ $order['services']['id'] }}" class="media flex-column align-items-start view-service">
                                                            {{-- Service Name --}}
                                                            <div class="title-color hover-c1 mb-1">
                                                                <strong>
                                                                    {{ Str::limit($order['services']['name'], 40) }}
                                                                </strong>
                                                            </div>

                                                            {{-- Pooja Venue --}}
                                                            <div class="text-muted mb-1">
                                                                {{ $order['services']['pooja_venue'] ?? 'Venue Not Available' }}
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
                                                        $memberNames = [];

                                                        if (!empty($order->members)) {
                                                            $members = explode('|', $order->members);

                                                            foreach ($members as $memb) {
                                                                if (!empty($memb)) {
                                                                    $decoded = json_decode($memb, true);

                                                                    if (is_array($decoded)) {
                                                                        foreach ($decoded as $member) {
                                                                            // yahan key name adjust kar sakte ho
                                                                            if (!empty($member['name'])) {
                                                                                $memberNames[] = $member['name'];
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    @endphp

                                                    @if (!empty($memberNames))
                                                        <span class="tio-user nav-icon"
                                                            data-toggle="tooltip"
                                                            data-html="true"
                                                            title="{!! implode('<br>', $memberNames) !!}">
                                                        </span>
                                                        <small>({{ count($memberNames) }})</small>
                                                    @else
                                                        <span class="badge badge-soft-danger">No Members</span>
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
                                                
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                      
                                                      
                                                        <a class="btn btn-outline-info btn-sm square-btn" target="_blank"
                                                            title="All order list"
                                                            href="{{ route('guruji.orders.puja.orders-details-members', ['service_id' => $order->service_id, 'booking_date' => $order->booking_date, 'status' => $order->order_status,'id' => $vendorId]) }}">
                                                            <i class="tio-format-points nav-icon"></i>
                                                        </a>
                                                       
                                                        <a class="btn btn-outline-primary btn-sm square-btn"
                                                            title="Order Details" target="_blank"
                                                            href="{{ route('guruji.orders.puja.orders-details', ['booking_date' => $order->booking_date, 'service_id' => $order->service_id, 'status' => 0, 'id' => $vendorId]) }}">
                                                            <i class="tio-visible nav-icon"></i>
                                                        </a>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{-- {{ $orders->links() }} --}}
                        </div>
                    </div>
                    @if (count($orders) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
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
            lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
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
                url: "{{ url('admin/pooja/orders/getpandit') }}" + '/' + serviceId + '/' + bookdate,
                success: function(response) {
                    if (response.status == 200) {
                        $('#assign-inhouse-pandit').html('');
                        $('#assign-freelancer-pandit').html('');

                        if (response.inhouse.length > 0) {
                            inhouseList = `<option value="" selected disabled>Select Pandit Ji</option>`;
                            $.each(response.inhouse, function(key, value) {
                                // orderCount(value.id, bookdate, function(count) {
                                if (value.is_pandit_pooja_per_day > value.checkastro) {
                                    inhouseList += `<option value="${value.id}">${value.name}</option>`;
                                }
                                $('#assign-inhouse-pandit').html(inhouseList);
                                // });
                            });
                        } else {
                            $('#assign-inhouse-pandit').append(
                                '<option value="" selected disabled>No Pandit Found</option>');
                        }

                        if (response.freelancer.length > 0) {
                            freelancerList = `<option value="" selected disabled>Select Pandit Ji</option>`;
                            $.each(response.freelancer, function(key, value) {
                                if (value.is_pandit_pooja_per_day > value.checkastro) {
                                    freelancerList +=
                                        `<option value="${value.id}">${value.name} Price:${value.price}</option>`;
                                }
                                $('#assign-freelancer-pandit').append(freelancerList);

                            });
                        } else {
                            $('#assign-freelancer-pandit').append(
                                '<option value="" selected disabled>No Pandit Found</option>');
                        }

                        $('.box').css('display', 'none');
                        $('#Assgine-the-pandit').modal('show');
                    } else {
                        alert('An error occurred');
                    }
                }
            });
        }

        function orderCount(panditId, bookdate, callback) {
            $.ajax({
                type: "get",
                url: "{{ url('admin/pooja/orders/get-pandit-order-count') }}" + '/' + panditId + '/' + bookdate,
                success: function(response) {
                    callback(response.ordercount); // Pass the order count to the callback
                },
                error: function() {
                    callback(0); // In case of error, pass 0 as the count
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