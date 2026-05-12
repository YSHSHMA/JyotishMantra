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
          .dateBooking {
            animation: blink 1s infinite;
            color: rgb(0, 255, 51);
        }
    </style>
@endpush
@section('content')

    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/anushthan.png') }}"
                    alt="">
                {{ translate('Chadhava | Order List') }}
                <span class="badge badge-soft-dark radius-50 fz-14"></span>
            </h2>
        </div>
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
                            <table id="ChadhavaTable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('Chadava_id') }}</th>
                                        <th>{{ translate('Chandhava_name') }}</th>
                                        <th>{{ translate('Chadhava_date') }}</th>
                                        <th>{{ translate('orders') }}</th>
                                        <th>{{ translate('members') }}</th>
                                        <th>{{ translate('total_amount') }}</th>
                                        <th>{{ translate('pandit_name') }}</th>
                                        @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Complete', 'list') ||
                                                Helpers::modules_permission_check('Chadhava Order', 'Order By Complete', 'detail'))
                                            <th>{{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>#CHDH {{ $order->service_id }}</td>
                                            <td><b>
                                                    <a href="#"
                                                        data-addedby="{{ $order['chadhava']['added_by'] }}"
                                                        data-id="{{ $order['chadhava']['id'] }}"
                                                        class="media align-items-center gap-2 view-service">
                                                        @if (isset($order['chadhava']) && isset($order['chadhava']['chadhava_type']))
                                                            @if ($order['chadhava']['chadhava_type'] == '1')
                                                                <span class="badge badge-danger">S</span>
                                                            @else
                                                                <span class="badge badge-danger">W</span>
                                                            @endif
                                                        @endif
                                                        <span class="media-body title-color hover-c1">
                                                            <strong>{{ Str::limit($order['chadhava']['name'], 40) }}
                                                            </strong>
                                                        </span>
                                                    </a>
                                                </b><br>
                                                <span
                                                    title="Pooja Venue:{{ $order['chadhava']['chadhava_venue'] }}">{{ Str::limit($order['chadhava']['chadhava_venue'], 30) }}
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
                                                @php
                                                    $members = explode('|', $order->members);
                                                    $totalCount = count($members);
                                                    $uniqueCount = count(array_unique($members));
                                                @endphp
                                                {{ $totalCount }}

                                            </td>
                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->total_amount), currencyCode: getCurrencyCode()) }}
                                            </td>


                                            <td>
                                                @if ($order->pandit_assign != null)
                                                    <b>{{ @ucwords($order['pandit']['name']) }}</b>
                                                @else
                                                    <span class="badge badge-soft-danger">Not Assigned</span>
                                                @endif
                                            </td>
                                       
                                            @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Complete', 'list') ||
                                                    Helpers::modules_permission_check('Chadhava Order', 'Order By Complete', 'detail'))
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Complete', 'list'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                target="_blank" title="All order list"
                                                                href="{{ route('admin.chadhava.order.SingleOrder', ['service_id' => $order->service_id, 'booking_date' => $order->booking_date, 'status' => $order->order_status]) }}">
                                                                <i class="tio-format-points nav-icon"></i>
                                                            </a>
                                                        @endif
                                                        @if (Helpers::modules_permission_check('Chadhava Order', 'Order By Complete', 'detail'))
                                                            <a class="btn btn-outline-info btn-sm square-btn"
                                                                title="Order Details" target="_blank"
                                                                href="{{ route('admin.chadhava.order.completechadhava', [
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

    {{-- Anushthan Model PAndit Assing --}}
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

                        <div class="" id="in-house">
                            <select name="assign_pandit" id="assign-inhouse-pandit" class="assign-pandit form-control">
                            </select>
                        </div>

                        <form action="{{ route('admin.chadhava.order.assign.allpandit') }}" method="post"
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
            let table = $('#ChadhavaTable').DataTable({
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
        </script>
        <script>
            $('.assign-pandit').on('change', function() {
                var panditId = $(this).val();
                $('#pandit-id-val').val(panditId);
                Swal.fire({
                    title: 'Are You Sure To Assign  Anushthan Pandit.',
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
                            $('.box').css('display', 'none');
                            $('#Assgine-the-pandit').modal('show');
                        } else {
                            alert('an error occured');
                        }
                    }
                });
            }
        </script>
    @endpush
