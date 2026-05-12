@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('pooja_completed_order_list'))
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
            color: rgb(0, 255, 51);
        }
    </style>
@endpush
@section('content')

    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <h2 class="h1 mb-0 d-flex gap-2">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}"
                            alt="">
                        {{ translate('pooja_completed_order_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-14">{{ count($orders) }}</span>
                    </h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-10 d-flex justify-content-center align-items-center">
                    @php
                        $totalPaymentAmount = \App\Models\Service_order::where('type', 'pooja')
                            ->where('status', 1)
                            ->where('order_status', 1)
                            ->sum('pay_amount');
                    @endphp
                    <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                        <h3 class="for-card-count mb-0 fz-24 text-success">
                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentAmount), currencyCode: getCurrencyCode()) }}
                        </h3>
                        <div class="text-capitalize mb-30">
                            Pooja earning
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="card">
            <div class="card-body">
                <form action="{{url()->current()}}" id="form-data" method="GET">
                    <div class="row gx-2">
                        <div class="col-12">
                            <h4 class="mb-3 text-capitalize">{{translate('filter_order')}}</h4>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color" for="customer_filter">{{translate('customer')}}</label>
                                <select  id="" name="customer_filter" class="js-data-example-ajax form-control form-ellipsis">
                                    <option value="all">{{translate('all_customer')}}</option>
                                    <option value="guest">Guest</option>
                                    @foreach ($users as $user)
                                        <option value="{{$user['id']}}">{{$user['f_name'].' '.$user['l_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <label class="title-color" for="date_type_filter">{{translate('date_type')}}</label>
                            <div class="form-group">
                                <select class="form-control __form-control"s name="date_type_filter" id="daste_type">
                                    <option value="" selected disabled>{{stranslate('select_Date_Type')}}s</option>
                                    <option value="this_year">{{translate('this_Year')}}</soption>
                       s             <option value="this_month">{{translate('this_Month')}}</option>
                                    <option value="this_week">{{translate('this_Week')}}</option>
                                    <option value="custom_date">{{translate('custom_Date')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12" id="from-to-div" style="display: none;">
                            <div class="row">
                                <div class="col-sm-6 col-lg-4 col-xl-3" id="from_div">
                                    <label class="title-color" for="customer">{{translate('start_date')}}</label>
                                    <div class="form-group">
                                        <input type="date" name="from" id="from_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3" id="to_div">
                                    <label class="title-color" for="customer">{{translate('end_date')}}</label>
                                    <div class="form-group">
                                        <input type="date" name="to" id="to_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div classs="d-flex gap-3 justify-content-send">
                                <button type="submit" class="btn btn--primary px-5" id="formUrlChange">
                                    {{translate('show_data')}}
                                </button>
                            </div>
                        </div>
                    </divs>
                </form>
     s       </div>
        </div> --}}


        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            {{-- <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_name') }}"
                                            aria-label="{{ translate('search_by_name') }}"
                                            value="{{ request('searchValue') }}" required>
                                        <button type="submit"
                                            class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div> --}}
                            <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                <!-- <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                                                                                <i class="tio-download-to"></i>
                                                                                                {{ translate('export') }}
                                                                                                <i class="tio-chevron-down"></i>
                                                                                            </button> -->
                                {{-- <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.calculator.export', ['searchValue' => request('searchValue')]) }}">
                                            <img width="14"
                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/excel.png') }}"
                                                alt="">
                                            {{ translate('excel') }}
                                        </a>
                                    </li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="myTable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('Pooja_Id') }}</th>
                                        <th>{{ translate('pooja_name_cateogry_venue') }}</th>
                                        <th>{{ translate('pooja_completed_date') }}</th>
                                        <th>{{ translate('orders') }}</th>
                                        <th>{{ translate('members') }}</th>
                                        <th>{{ translate('total_amount') }}</th>
                                        <th>{{ translate('pandit_name') }}</th>
                                        @if (Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'order-list') || Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'order-details'))
                                        <th>{{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($orders as $key => $order)
                                        @php
                                            $member_count = 0;
                                            $all_members = [];

                                            if (!empty($order->members)) {
                                                $groups = explode('|', $order->members);
                                                foreach ($groups as $group) {
                                                    $decoded = json_decode($group, true);
                                                    if (is_array($decoded)) {
                                                        $member_count += count($decoded);
                                                        $all_members = array_merge($all_members, $decoded);
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>## {{ $order->service_id }}</td>
                                            <td><b>
                                                    <a href="{{ route('admin.service.views', [$order['services']['added_by'], $order['services']['id']]) }}"
                                                        data-addedby="{{ $order['services']['added_by'] }}"
                                                        data-id="{{ $order['services']['id'] }}"
                                                        class="media align-items-center gap-2 view-service">
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
                                                        <span class="media-body title-color hover-c1">
                                                            <strong>{{ Str::limit($order['services']['name'], 40) }}
                                                            </strong>
                                                        </span>

                                                    </a>
                                                </b>

                                                {{ Str::limit($order['services']['category']['name'], 30) }}<br>
                                                <span
                                                    title="Pooja Venue:{{ $order['services']['pooja_venue'] }}">{{ Str::limit($order['services']['pooja_venue'], 30) }}
                                                </span><br>
                                                <span
                                                    class="dateBooking">{{ $order->booking_date ? date('d,F,l', strtotime($order->booking_date)) : 'No Date Available' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ date('d M Y', strtotime($order->order_completed)) }}

                                            </td>


                                            <td>{{ $order->total_orders }}</td>
                                            <td>

                                                <span class="tio-user nav-icon" title="{!! implode('<br>', array_map('htmlspecialchars', $all_members)) !!}">
                                                </span>
                                                {{ $member_count }}
                                            </td>

                                            <td>₹{{ $order->total_amount }}</td>
                                            <td>
                                                @if ($order->pandit_assign != null)
                                                    <b>{{ @ucwords($order['pandit']['name']) }}</b>
                                                @else
                                                    <span class="badge badge-soft-danger">Not Assigned</span>
                                                @endif
                                            </td>

                                            @if (Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'order-list') || Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'order-details'))
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if (Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'order-list'))
                                                    <a class="btn btn-outline-info btn-sm square-btn" target="_blank"
                                                        title="All order list"
                                                        href="{{ route('admin.pooja.orders.AllSingleOrder', [
                                                            'service_id' => $order->service_id,
                                                            'booking_date' => $order->booking_date,
                                                            'status' => $order->order_status,
                                                        ]) }}">
                                                        <i class="tio-format-points nav-icon"></i>
                                                    </a>
                                                    @endif
                                                    @if (Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'order-details'))
                                                    <a class="btn btn-outline-primary btn-sm square-btn"
                                                        title="Order Details" target="_blank"
                                                        href="{{ route('admin.pooja.orders.completepuja', [
                                                            'service_id' => $order->service_id,
                                                            'booking_date' => $order->booking_date,
                                                            'status' => $order->order_status,
                                                        ]) }}">
                                                        <i class="tio-visible nav-icon"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
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

    <div class="modal fade" id="Assgine-the-pandit" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
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
                        <form action="{{ route('admin.pooja.orders.assign.allpandit') }}" method="post"
                            id="assign-pandit-form">
                            @csrf
                            <input type="hidden" name="booking_date" id="booking_date">
                            <input type="hidden" name="service_id" id="service_id">
                            <input type="hidden" name="pandit_id" id="pandit-id-val">
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
        let table = new DataTable('#myTable');
    </script>
    <script>
        new DataTable('#example', {
            layout: {
                topStart: {
                    buttons: ['print']
                }
            }
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

            $('#service_id').val(serviceId);
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
