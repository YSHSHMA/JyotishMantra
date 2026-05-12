@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('VIP_Pooja_Instance_Orders_List'))
@push('css_or_js')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
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
            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/vip.png') }}" alt="">
                {{ translate('VIP_Pooja_Order_List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($orders) }}</span>
            </h2>
        </div>
        <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="row g-2" id="order_stats">
                    <div class="col-lg-3">
                        <div class="card card-body h-100 justify-content-center">
                            @php
                                $totalPaymentAmount = \App\Models\Service_order::where('type', 'vip')->where('package_id',6)->sum(
                                    'pay_amount',
                                );
                            @endphp

                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-info">
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentAmount), currencyCode: getCurrencyCode()) }}
                                    </h3>
                                    <div class="text-capitalize mb-0">Pooja earning</div>
                                </div>
                                <div>
                                    <img width="48" class="mb-2"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                        alt="">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-info">
                                        {{ \App\Models\Service_order::where('type', 'vip')->where('package_id', 6)->count() }}
                                    </h3>
                                    <div class="text-capitalize mb-0">TOTAL ORDER</div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-primary">
                                        {{ \App\Models\Service_order::where('type', 'vip')->where('package_id', 6)->where('status', 0)->where('order_status', 0)->where('is_completed', 0)->count() }}
                                    </h3>
                                    <div class="text-capitalize mb-0">PENDING ORDER</div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-success">
                                        {{ \App\Models\Service_order::where('type', 'vip')->where('package_id', 6)->where('is_completed', 1)->where('status', 1)->where('order_status',1)->count() }}
                                    </h3>
                                    <div class="text-capitalize mb-0">COMPLETED ORDER</div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-12">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-info">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('order_status', 3)->where('package_id', 6)->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">TOTAL SHEDULE</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-danger">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('order_status', 4)->where('package_id', 6)->where('is_completed', 1)->where('status', 1)->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">TOTAL LIVE</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24 text-primary">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('order_status', 5)->where('package_id', 6)->where('status', 0)->where('is_completed', 0)->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">SHARE VIDEO</div>
                                    </div>

                                </div>
                            </div>
                        </div>                      
                        <div class="col-md-3">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">

                                        <h3 class="mb-1 fz-24 text-danger">
                                            {{ \App\Models\Service_order::where('type', 'vip')->where('status', 2)->count() }}
                                        </h3>
                                        <div class="text-capitalize mb-0">CANCEL ORDER</div>
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
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="myTable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('order_info') }}</th>
                                        <th>{{ translate('Customer') }}</th>
                                        <th>{{ translate('VIP Pooja Name') }}</th>
                                        <th>{{ translate('Pandit') }}</th>
                                        <th>{{ translate('Amount') }}</th>
                                        <th>{{ translate('Status') }}</th>
                                        @if (Helpers::modules_permission_check('Vip Order', 'Instance Order', 'detail') ||
                                                Helpers::modules_permission_check('Vip Order', 'Instance Order', 'download') ||
                                                Helpers::modules_permission_check('Vip Order', 'Instance Order', 'assign-pandit') ||
                                                Helpers::modules_permission_check('Vip Order', 'Instance Order', 'schedule'))
                                            <th class="text-center"> {{ translate('action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><a href="#" data-id="{{ $order['id'] }}"
                                                    class="order-link">{{ $order->order_id }}</a>
                                                @if ($order['is_rejected'] == 1)
                                                    <span class="badge badge-warning">R</span>
                                                @endif
                                                @if ($order['package_id'] == 6)
                                                    <span class="badge badge-danger">I</span>
                                                @endif
                                                <br>
                                                {{ date('d M, Y h:i A', strtotime($order->created_at)) }}
                                            </td>
                                            <td><b><a href="{{ route('admin.customer.view', [$order['customers']['id']??0]) }}"
                                                        class="title-color hover-c1 d-flex align-items-center gap-10">{{ @ucwords($order['customers']['f_name']??null) }}
                                                        {{ $order['customers']['l_name']??null }}</b>
                                                <p>{{ $order['customers']['phone']??null }}</p>
                                                </a>
                                            </td>
                                            <td>
                                                <b>
                                                    @if (isset($order['vippoojas']))
                                                        <a href="{{ route('admin.service.vip.view', ['addedBy' => $order['vippoojas']['added_by'], 'id' => $order['vippoojas']['id']]) }}"
                                                            class="title-color hover-c1 d-flex align-items-center gap-10"
                                                            target="_blank">
                                                            {{ Str::limit($order['vippoojas']['name'], 30) }}
                                                        </a>
                                                        <span class="dateBooking">
                                                            {{ $order->booking_date ? date('d ,F , l', strtotime($order->booking_date)) : 'No Date Available' }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">No VIP Pooja details available</span>
                                                    @endif

                                                </b>
                                            </td>

                                            <td>
                                                @if ($order['pandit'] != null)
                                                    <b>{{ @ucwords($order['pandit']['name']) }}</b><br>
                                                    <b>{{ @ucwords($order['pandit']['is_pandit_primary_mandir_location']) }}</b>
                                                @else
                                                    <span class="badge badge-soft-danger">Not Assigned</span>
                                                @endif
                                            </td>
                                            <td>{{ webCurrencyConverter(amount: $order->pay_amount - $order->coupon_amount) }}
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

                                            @if (Helpers::modules_permission_check('Vip Order', 'Instance Order', 'detail') ||
                                                Helpers::modules_permission_check('Vip Order', 'Instance Order', 'download') ||
                                                Helpers::modules_permission_check('Vip Order', 'Instance Order', 'assign-pandit') ||
                                                Helpers::modules_permission_check('Vip Order', 'Instance Order', 'schedule'))
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        @if (Helpers::modules_permission_check('Vip Order', 'Instance Order', 'assign-pandit'))
                                                        @if (empty($order) || $order['order_status'] != 1 || $order['status'] != 1)
                                                        <a class="btn btn-outline-warning btn-sm square-btn"
                                                            title="{{ translate('pandit') }}" href="javascript:void(0);"
                                                            data-id="{{ $order->id }}"
                                                            data-tableid="{{ $order->id }}"
                                                            data-serviceid="{{ $order->service_id }}"
                                                            data-bookingdate="{{ $order->booking_date }}"
                                                            data-packageid="{{ $order->package_id }}"
                                                            onclick="pandit_model(this)">
                                                            <img src="{{ asset('public/assets/back-end/img/pooja/pandit.png') }}"
                                                                alt="" width="20px" height="20px">
                                                        </a>
                                                        @endif
                                                        @endif
                                                        @if ($order->order_status == 6 && $order->status == 6)
                                                            @if (Helpers::modules_permission_check('Vip Order', 'Instance Order', 'schedule'))
                                                                <button class="btn btn-outline-primary btn-sm square-btn"
                                                                    data-toggle="modal" data-target="#rejected-modal"
                                                                    data-servicename="{{ $order['vippoojas']['name'] }}"
                                                                    data-orderid="{{ $order->order_id }}"
                                                                    data-id="{{ $order->id }}"
                                                                    data-customer="{{ $order['customers']['f_name']??null }} {{ $order['customers']['l_name']??null }}"
                                                                    data-customerMobile="{{ $order['customers']['phone']??null }}"
                                                                    data-poojaType="{{ $order['vippoojas']['is_anushthan'] }}"
                                                                    onclick="RejctedModel(this)">
                                                                    <i class="tio-message"></i>
                                                                </button>
                                                            @endif
                                                            {{-- @endif --}}
                                                        @else
                                                           

                                                            @if (Helpers::modules_permission_check('Vip Order', 'Instance Order', 'detail'))
                                                                <a class="btn btn-outline-primary btn-sm square-btn"
                                                                    title="{{ translate('view') }}"
                                                                    href="{{ route('admin.vippooja.order.details', [$order['id']]) }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                        height="12" viewBox="0 0 14 12" fill="none"
                                                                        class="svg replaceds-svg">
                                                                        <path
                                                                            d="M6.79584 3.75937C6.86389 3.75234 6.93195 3.75 7 3.75C8.2882 3.75 9.33333 4.73672 9.33333 6C9.33333 7.24219 8.2882 8.25 7 8.25C5.68993 8.25 4.66667 7.24219 4.66667 6C4.66667 5.93437 4.6691 5.86875 4.67639 5.80313C4.90243 5.90859 5.16493 6 5.44445 6C6.30243 6 7 5.32734 7 4.5C7 4.23047 6.90521 3.97734 6.79584 3.75937ZM11.6813 2.63906C12.8188 3.65625 13.5795 4.85391 13.9392 5.71172C14.0194 5.89687 14.0194 6.10312 13.9392 6.28828C13.5795 7.125 12.8188 8.32266 11.6813 9.36094C10.5365 10.3875 8.96389 11.25 7 11.25C5.03611 11.25 3.46354 10.3875 2.31924 9.36094C1.18174 8.32266 0.42146 7.125 0.059818 6.28828C0.0203307 6.19694 0 6.09896 0 6C0 5.90104 0.0203307 5.80306 0.059818 5.71172C0.42146 4.85391 1.18174 3.65625 2.31924 2.63906C3.46354 1.61344 5.03611 0.75 7 0.75C8.96389 0.75 10.5365 1.61344 11.6813 2.63906ZM7 2.625C5.06771 2.625 3.5 4.13672 3.5 6C3.5 7.86328 5.06771 9.375 7 9.375C8.93229 9.375 10.5 7.86328 10.5 6C10.5 4.13672 8.93229 2.625 7 2.625Z"
                                                                            fill="#0177CD"></path>
                                                                    </svg>
                                                                </a>
                                                            @endif

                                                            @if (Helpers::modules_permission_check('Vip Order', 'Instance Order', 'download'))
                                                                <a class="btn btn-outline-info btn-sm square-btn"
                                                                    target="_blank"
                                                                    href="{{ route('admin.vippooja.order.generate.invoice', $order['id']) }}">
                                                                    <i class="tio-download-to"></i>
                                                                </a>
                                                            @endif
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
                            {{ $orders->links() }}
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
    {{-- Model --}}
    <!-- Booking Model -->
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
                            <form action="{{ route('admin.vippooja.order.updatedOrder') }}" method="post"
                                id="Rejcted_model">
                                @csrf
                                <input type="hidden" name="order_id" id="OrderId">
                                <input type="hidden" name="service_id" id="ServiceId">
                                <input type="text" name="booking_date" class="form-control mb-2" required
                                    id="BookingDateSelected" placeholder="Select Booking Date">
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
                                        <th><strong>Customer Name / Mobile</strong></th>
                                        <td colspan="2"><span id="customerName"></span><br>
                                            <span id="MobileCustomer"></span>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            <h3>Note:</h3>
                            <p>Please select a date and type it in the text area below in the format
                                <strong>yyyy-mm-dd</strong>:
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- Assign Pandit  Model --}}
    <div class="modal fade" id="Assgine-the-pandit" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Change Pandit</h5>
                        <p class="text-muted small mt-1">
                            You are about to change the assigned Pandit (Purohit) for this VIP Pooja order. Please ensure
                            the new selection matches the customer's requirements and schedule.
                        </p>
                    </div>
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
                        <form action="{{ route('admin.vippooja.order.pandit') }}"
                            method="post" id="assign-pandit-form">
                            @csrf
                            <input type="hidden" name="id" id="table-id">
                            <input type="hidden" name="booking_date" id="booking-date">
                            <input type="hidden" name="service_id" id="service-id">
                            <input type="hidden" name="package_id" id="package-id">
                            <input type="hidden" name="pandit_id" id="pandit-id-val">
                        </form>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        // datepicker
        var today = new Date();
        var tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        //Start Date
        $('#BookingDateSelected').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy/mm/dd',
            modal: true,
            footer: true,
            minDate: today,
            todayHighlight: true
        });
    </script>
    <script type="text/javascript">
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
                    url: "{{ url('admin/vippooja/order/get-order-details') }}",
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
                        let membersArray;
                        let membersArray = JSON.parse(data.members);
                        let name = membersArray[0];


                        $('#order-details').html(`
                        <tr><th><b>Booking Id</b></th><td>${data.order_id}</td><th><b>Booking Date</b></th><td>${formatDate(data.created_at)}</td><th><b>TXN ID </b></th><td>${data.payment_id}</td></tr>
                        <tr><th><b>Pooja Details</b></th><td colspan=3><b>Pooja Name:</b>${data.vippoojas.name},<br><b>Pooja Venue:</b></td><th><b>Prashad(YES/NO)</b></th><td><span class="badge badge-soft-${data.prashad_status == 0 ? 'primary' : (data.prashad_status == 1 ? 'success' : 'danger')}">
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
                                <span class="badge badge-soft-${data.status == 0 ? 'primary' : (data.status == 1 ? 'success' : 'danger')}">
                                    ${data.status == 0 ? 'Pending' : (data.status == 1 ? 'Completed' : 'Canceled')}
                                </span>
                            </td>
                            
                        </tr> 
                        <tr>
                            <th><b>Number of Members Name</b></th>
                                <td>${name}</td>
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
                                     `).join('')}
                        </tr>
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
        function RejctedModel(that) {
            var orderid = $(that).data('orderid');
            var id = $(that).data('id');
            var servicename = $(that).data('servicename');
            var customer = $(that).data('customer');
            var bookingDate = $(that).data('bookingdate');
            var poojaType = $(that).data('poojaType');
            var customerMobile = $(that).data('customerMobile');

            // Populate the modal fields
            $('#OrderId').val(orderid);
            $('#ServiceId').val(id);
            $('#ServiceName').val(servicename);
            $('#NextBookingDate').val(bookingDate);
            // Populate the table with dynamic content
            $('#NameofService').text(servicename);
            $('#OrderIdVAl').text(orderid);
            $('#customerName').text(customer);
            $('#NameCustomer').text(customer);
            $('#MobileCustomer').text(customerMobile);
            $('#rejectedModal').modal('show');

            var message = `<p>Dear ${customer},</p>
                        <p>We regret to inform you that your booking for the pooja <strong>${servicename}</strong> has been rejected.</p>
                        <p>The next available date for this pooja is on <strong>yyyy-mm-dd</strong>.</p>
                        <p>We apologize for the inconvenience. Please feel free to contact us if you have any questions.</p>
                        <p>Best regards,</p>
                        <p>Mahakal.com</p>`;
            CKEDITOR.instances.editor.setData(message);

        }
    </script>
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
                title: 'Are you sure you want to assign this Anushthan? This action will change the current Anushthan assignment',
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
            var packageId = $(that).data('packageid');
            var tableId = $(that).data('tableid');

            $('#service-id').val(serviceId);
            $('#package-id').val(packageId);
            $('#booking-date').val(bookdate);
            $('#table-id').val(tableId);

            var inhouseList = "";
            var freelancerList = "";

            $.ajax({
                type: "get",
                url: "{{ url('admin/vippooja/order/getpandit') }}" + '/' + serviceId + '/' + bookdate,
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
