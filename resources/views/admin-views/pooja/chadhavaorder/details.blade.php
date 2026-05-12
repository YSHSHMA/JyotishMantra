@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('Chadhava|Order Details'))

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
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20px" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/anushthan.png') }}"
                    alt="">
                {{ translate('Chadhava|Order Details') }}
            </h2>
        </div>

        <div class="row gy-3" id="printableArea">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">

                            <div class="invoice-header mb-4">
                                <h6 class="dateBooking mb-1 text-capitalize text-danger">
                                    Booking Date: {{ date('d M, Y', strtotime($details['booking_date'])) }}</h6>
                                <h2 class="fw-bold text-uppercase mb-2"> {{ $details['chadhava']['name'] }}</h2>
                                <h5 class="mb-1">OrderId :{{ $details['order_id'] }} + {{ \App\Models\Chadhava_orders::where('type', 'chadhava')->where('booking_date',$details['booking_date'])->where('status',$details['status'])->where('order_status',$details['order_status'])->where('service_id',$details['service_id'])->count() }}</h5>
                                @if (!empty($details['members']))
                                 
                                        <h6 class="mb-1 text-capitalize">Member Name: <strong>{{ $details['members'] }}</strong></h6>
                                   
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
                                @if (Helpers::modules_permission_check('Chadhava Order', 'Detail', 'print-invoice'))
                                <div class="d-flex flex-wrap gap-10 justify-content-end">
                                    <a class="btn btn--primary px-4" target="_blank"
                                        href="{{ route('admin.chadhava.order.generate.invoice', $details['id']) }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/uil_invoice.svg') }}"
                                            alt="" class="mr-1">
                                    
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
                                        <span class="payment-status-span font-weight-bold
                                            @if($details['payment_status'] == 1) text-success
                                            @elseif($details['payment_status'] == 0) text-warning
                                            @elseif($details['payment_status'] == 2) text-danger
                                            @endif">
                                            
                                            @if($details['payment_status'] == 1)
                                                {{ translate('paid') }}
                                            @elseif($details['payment_status'] == 0)
                                                {{ translate('pending') }}
                                            @elseif($details['payment_status'] == 2)
                                                {{ translate('failed') }}
                                            @endif
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
                                            <th>{{ translate('price') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($details['product_leads'] as $productLeads)
                                            <tr>
                                                <td>
                                                    <h6 class="title-color">
                                                        {{ substr($productLeads['productsData']['name'], 0, 30) }}{{ strlen($productLeads['productsData']['name']) > 10 ? '...' : '' }}
                                                    </h6>
                                                </td>
                                                <td>
                                                    {{ $productLeads['qty'] }}
                                                </td>
                                                <td>
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $productLeads['final_price']), currencyCode: getCurrencyCode()) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="border-top border-dark">
                                        <tr>
                                       
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
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
              
               
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center"><i class="tio-imac nav-icon"></i>
                                {{ translate('Chadhava_Schedule_details') }}</h4>
                        </div>
                        <div class="media flex-wrap gap-3">
                            <span class="title-color"><i class="tio-time"></i> {{ translate('Schedule_time') }}:<strong
                                    class="bg-danger text-light">{{ ($details['schedule_time']) ? date('h:i A', strtotime($details['schedule_time'])) : ''; }}</strong></span>
                            <span class=""><i class="tio-tv-new"></i>
                                {{ translate('Live_stream') }}:{{ $details['live_stream'] ?? '' }}</span>
                            <iframe width="280" height="110"
                                src="{{ isset($details['live_stream']) && !empty($details['live_stream']) ? $details['live_stream'] : 'https://www.youtube.com/' }}"
                                frameborder="0" allowfullscreen>
                            </iframe>


                            <span class="title-color"><i class="tio-share"></i>
                                {{ translate('Share_video_link') }}:<strong>{{ $details['pooja_video'] ?? '' }}</strong></span>

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
                            You are about to change the assigned Pandit (Purohit) for this Chadhava order. Please ensure the new selection matches the customer's requirements and schedule.
                        </p>
                        </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select id="change-pandit" class="change-pandit form-control">
                        @if (count($inHouseAstrologers) > 0)
                            @foreach ($inHouseAstrologers as $inhouse)
                            @php 
                            $checkastro= \App\Models\Chadhava_orders::where('pandit_assign',$inhouse->id)->where('booking_date',$details->booking_date)->count();
                            @endphp
                                @if ($inhouse['is_pandit_pooja_per_day'] > $checkastro)
                                    <option value="{{ $inhouse['id'] }}">{{ $inhouse['name'] }}</option>
                                @endif
                            @endforeach
                        @else
                            <option disabled>No Astrologer Found</option>
                        @endif
                    </select>
                    <form action="{{ route('admin.chadhava.order.assign.pandit', [$details['id']]) }}" method="post"
                        id="assign-pandit-form">
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
        aria-hidden="true" data-keyboard="false" data-backdrop="static"s>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.chadhava.order.cancel_poojas', [$details->id]) }}" method="post" class="modal-form"
                    id="pooja-cancel-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="booking_date" id="booking_id" value="{{  $details->booking_date }}">
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
                        <h5 class="modal-title">Chadhava Time Schedule</h5>
                        <p class="text-muted small mt-1">
                            Below is the schedule for the Chadhava timings. Please ensure to arrive at least 15 minutes
                            before the scheduled time.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.chadhava.order.status_times', [$details->id]) }}" method="post"
                    id="pooja-time-form"  class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="booking_date" id="booking_id" value="{{  $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-time-status">
                        <input type="text" name="schedule_time" id="schedule_time"
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
                        <p class="text-muted small mt-1">
                            Below is the live stream video link. Admin can use this link to manage and monitor the live Chadhava session.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.chadhava.order.live_streams', [$details->id]) }}" method="post"
                    id="pooja-live-form"  class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="booking_date" id="booking_id" value="{{  $details->booking_date }}">
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
                        <p class="text-muted small mt-1">
                            As an admin, you can share recorded or live Chadhava videos with devotees. Choose a platform
                            and ensure the video link is accessible.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.chadhava.order.pooja_videos', [$details->id]) }}" method="post"
                    id="pooja-video-form" class="modal-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="booking_date" id="booking_id" value="{{  $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order-video-status">
                        <input name="pooja_video" id="pooja_video" placehoder="{{ translate('Video URL') }}"
                            class="pooja-video form-control" data-id="{{ $details['id'] }}"
                            data-service="{{ $details['service_id'] }}" value="{{ $details['live_stream'] ?? '' }}" readonly>
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


    <script>
        $('#order_status').on('change', function () {
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
        $(document).ready(function() {
            $('#toggle-card').click(function() {
                $('#order-tracking-card').toggle();
                $('#toggle-icon').toggleClass('ti-caret-up');
            });
        });
    </script>
    {{-- <script>
        document.getElementById('assign-pandit').addEventListener('change', function() {
            if (this.value === 'add_pandit') {
                window.location.href = "{{ route('admin.astrologers.manage.list') }}";
            }
        });
    </script> --}}
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
