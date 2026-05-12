@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('puja_full_details'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .section-card {
            display: none;
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
        .vip-container {
            position: relative;
        }

        .vip-edit-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 5;
            padding: 6px 8px;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }

        .vip-edit-badge i {
            font-size: 14px;
        }
    </style>
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/all-orders.png') }}" alt="">
                {{ translate('puja_full_details') }}
            </h2>
        </div>
          

        <div class="row gy-3" id="printableArea">
            {{-- Section for 8 By  --}}
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">

                            <div class="d-flex flex-column gap-10">
                                <h4 class="text-capitalize">{{ translate('Pooja_details') }} </h4>
                                <span class="text-capitalize">
                                    <i class="tio-bookmark"></i> {{ $service['name'] }}
                                </span>
                                <span class="text-capitalize">
                                    <i class="tio-poi"></i> {{ $service['pooja_venue'] }}
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
                                                    $member_count += count(json_decode($memb));
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
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $details->total_amount), currencyCode: getCurrencyCode()) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive datatable-custom mt-4">
                            <table id="order-details-table"
                                class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('order_details') }}</th>
                                        <th>{{ translate('prasaad') }}</th>
                                        <th>{{ translate('Amount') }}</th>
                                        <th>{{ translate('certificate_details') }}</th>
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

                                            $orderdata = \App\Models\Service_order::whereIn('order_id', $orderIds)
                                                ->with('customer')
                                                ->get()
                                                ->keyBy('order_id');
                                        @endphp

                                        @foreach ($orderIds as $index => $orderId)
                                            @php
                                                $order = $orderdata[$orderId] ?? null;
                                            @endphp

                                            <tr>
                                                <!-- Order ID + Member + Gotra + Products -->
                                                <td>
                                                    {{ $orderId }}
                                                    <br>
                                                    {{-- Members --}}
                                                    <b> Customer Name :
                                                    </b>{{ $order && $order->customer ? ucwords($order->customer->f_name . ' ' . $order->customer->l_name) : 'Customer Not Found' }}
                                                    @if (!empty($memberParts[$index]))
                                                        @php
                                                            $decodedMember = json_decode($memberParts[$index], true);
                                                        @endphp
                                                        @if (is_array($decodedMember) && count($decodedMember) > 0)
                                                            <br><strong>Members:</strong>
                                                            {{ implode(', ', $decodedMember) }}
                                                        @endif
                                                    @endif

                                                    {{-- Gotra --}}
                                                    @if (!empty($gotraParts[$index]))
                                                        <br><strong>Gotra:</strong> {{ $gotraParts[$index] }}
                                                    @endif

                                                    {{-- Product Info --}}
                                                    @if (!empty($leadIds) && isset($leadIds[$index]) && isset($products[$leadIds[$index]]))
                                                        <br><strong>Products:</strong>
                                                        @foreach ($products[$leadIds[$index]] as $product)
                                                            <span>{{ $product->product_name }} (Qty:
                                                                {{ $product->qty }})</span><br>
                                                        @endforeach
                                                    @else
                                                        <br><span>No Products</span>
                                                    @endif
                                                </td>



                                                <!-- Prashad Status -->
                                                <td>
                                                    @if ($order && $order->is_prashad == 1)
                                                        <span class="">Yes Prashad</span><br>
                                                        <span class="">
                                                            {!! collect([$order->house_no, $order->area, $order->city, $order->state, $order->pincode])
                                                                ->filter() // remove null/empty values
                                                                ->map(fn($item) => e(trim($item))) // safely trim and escape
                                                                ->implode('<br>') !!}
                                                        </span>

                                                    @else
                                                        <span class="">No Prashad</span>
                                                    @endif
                                                </td>

                                                <!-- Pay Amount -->
                                                <td>
                                                    @if ($order)
                                                        <span class="">
                                                            ₹{{ $order->pay_amount - ($order->coupon_amount ?? 0) }}
                                                        </span>
                                                    @endif
                                                </td>
                                               <!-- Certificate Image -->
                                                <td>
                                                    @if ($order && $order->pooja_certificate)
                                                        <div class="text-center position-relative d-inline-block vip-container">
                                                            <!-- EDIT BADGE -->
                                                            @if (Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'detail-edit'))
                                                                <button
                                                                    class="btn btn-primary btn-sm vip-edit-badge"
                                                                    data-toggle="modal"
                                                                    data-target="#change-certificate-model"
                                                                    title="Edit Certificate">
                                                                    <i class="tio-edit"></i>
                                                                </button>
                                                            @endif

                                                            <!-- CERTIFICATE IMAGE -->
                                                            <img src="{{ asset('public/' . $order->pooja_certificate) }}"
                                                                alt="VIP Certificate Image"
                                                                width="300px"
                                                                class="img-fluid vip-image"
                                                                data-toggle="modal"
                                                                data-target="#viewCertificate{{ $loop->index }}"
                                                                style="cursor: pointer;">
                                                        </div>

                                                        <!-- Modal -->
                                                        <div class="modal fade" id="viewCertificate{{ $loop->index }}"
                                                            tabindex="-1" role="dialog"
                                                            aria-labelledby="viewCertificateLabel{{ $loop->index }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-body p-0">
                                                                        <img src="{{ asset('public/' . $order->pooja_certificate) }}"
                                                                            alt="VIP Certificate Full Image"
                                                                            class="img-fluid w-100">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No Certificate</span>
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
            {{-- Section for 8 By  --}}
            {{-- Section for 4 By  --}}
            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0">
                                {{ translate('pandit_information') }}
                            </h4>
                        </div>

                        <div>
                            {{-- {{dd($details['pandit'])}} --}}
                            @if (!empty($details['pandit']))
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
                                        <span class="title-color break-all" style="text-transform: lowercase !important;"><i
                                                class="tio-email"></i>:
                                            {{ $details['pandit']['email'] }}</span>
                                    </div>
                                </div>
                            @else
                                <p>Pandit Detail Not Available</p>
                            @endif
                        </div>

                    </div>
                </div>
                {{-- Shedule  time --}}
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 text-center"><i class="tio-sidelight-information nav-icon"></i>
                                {{ translate('pooja_completed_details') }}
                            </h4>
                            @if (Helpers::modules_permission_check('Pooja Order', 'Order By Completed', 'detail-edit'))
                            <button class="btn btn-outline-primary btn-sm square-btn" data-toggle="modal"
                                data-target="#change-video-model" title="click the ">
                                <i class="tio-edit"></i>
                            </button>
                            @endif
                        </div>
                        <div class="media flex-wrap gap-3">
                            <span class="title-color"><i class="tio-time"></i> {{ translate('Schedule_time') }}:<strong
                                    class="bg-danger text-light">{{ $details['schedule_time'] ? date('h:i A', strtotime($details['schedule_time'])) : '' }}</strong></span>
                            <span class="title-color"><i class="tio-tv-new"></i>
                                {{ translate('completed_pooja_video_udpate_link') }}:</span>
                            @php
                                $youtubeLink =
                                    isset($details['live_stream']) && !empty($details['live_stream'])
                                        ? $details['live_stream']
                                        : 'https://www.youtube.com/';
                                if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $youtubeLink, $matches)) {
                                    $youtubeLink = 'https://www.youtube.com/embed/' . $matches[1];
                                } elseif (
                                    preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $youtubeLink, $matches)
                                ) {
                                    $youtubeLink = 'https://www.youtube.com/embed/' . $matches[1];
                                } elseif (preg_match('/playlist\?list=([a-zA-Z0-9_-]+)/', $youtubeLink, $matches)) {
                                    $youtubeLink = 'https://www.youtube.com/embed/videoseries?list=' . $matches[1];
                                }
                            @endphp

                            <iframe width="280" height="110" src="{{ $youtubeLink }}" frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                            @php
                                $shareLink =
                                    isset($details['pooja_video']) && !empty($details['pooja_video'])
                                        ? $details['pooja_video']
                                        : 'https://www.youtube.com/';
                                if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $shareLink, $matches)) {
                                    $shareLink = 'https://www.youtube.com/embed/' . $matches[1];
                                } elseif (
                                    preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $shareLink, $matches)
                                ) {
                                    $shareLink = 'https://www.youtube.com/embed/' . $matches[1];
                                } elseif (preg_match('/playlist\?list=([a-zA-Z0-9_-]+)/', $shareLink, $matches)) {
                                    $shareLink = 'https://www.youtube.com/embed/videoseries?list=' . $matches[1];
                                }
                            @endphp

                            <iframe width="280" height="110" src="{{ $shareLink }}" frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>

                            <div class="alert alert-warning mt-3">
                                <strong>📌 Note:</strong> To update the Pooja video link, click on the
                                <strong>"Edit icon (<i class="tio-edit"></i>)"</strong> button below.
                                <br>
                                In the order edit modal, paste the new video link (YouTube or other) in the appropriate
                                field and save. The link will be updated successfully.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            {{-- Section for 4 By  --}}
        </div>
    </div>
    {{-- Vedio Status Change --}}
    <div class="modal fade" id="change-video-model" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Video Status Change</h5>
                        <p class="modal-subtitle" style="font-size: 14px; color: #555; margin: 5px 0 0;">You can update
                            the
                            status of the pooja video from here. Please confirm your action before proceeding.</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.pooja.orders.update_pooja_video') }}" method="post"
                        id="pooja-video-form" class="modal-form">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="booking_date" id="booking_id"
                                value="{{ $details->booking_date }}">
                            <input type="hidden" name="service_id" id="service-id" value="{{ $details->service_id }}">
                            <input type="hidden" name="order_status" id="order-video-status"
                                value="{{ $details->order_status }}">
                            <input name="pooja_video" id="pooja_video" placehoder="{{ translate('Video URL') }}"
                                class="pooja-video form-control" data-id="{{ $details['id'] }}"
                                data-service="{{ $details['service_id'] }}"
                                value="{{ $details['live_stream'] ?? $details['pooja_video'] }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    </div>

       {{-- certificate update --}}
    <div class="modal fade" id="change-certificate-model" tabindex="-1" role="dialog"
        aria-labelledby="modelTitleId" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Certificate Update</h5>
                        <p style="font-size: 14px; color: #555; margin: 5px 0 0;">
                            You can update the certificate image here.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('admin.pooja.orders.update_certificate') }}"
                        method="post" enctype="multipart/form-data" id="certificate-form">
                        @csrf

                        <input type="hidden" name="booking_date" id="booking_date" value="{{ $details->booking_date }}">
                        <input type="hidden" name="service_id" id="service_id" value="{{ $details->service_id }}">
                        <input type="hidden" name="order_status" id="order_status" value="{{ $details->order_status }}">
                        <input type="hidden" name="order_id" id="order_id" value="{{ $details->id }}">

                        <!-- DYNAMIC OLD IMAGE -->
                        <div class="mb-3 text-center">
                            <label class="font-weight-bold">Current Certificate Image</label><br>
                            <img id="certificate_preview"
                                src="{{ asset('public/' . $details->chadhava_certificate) }}"
                                alt="Certificate"
                                class="img-fluid rounded shadow"
                                style="max-height: 300px;">
                        </div>

                        <!-- NEW IMAGE UPLOAD -->
                        <div class="form-group">
                            <label class="font-weight-bold">Upload New Certificate Image</label>
                            <input type="file" name="pooja_certificate" id="certificate_input" class="form-control">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
@push('script_2')
    <!-- jQuery (required by DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        var $timepicker = $('#pooja_time').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#order-details-table').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "responsive": true,
                "columnDefs": [{
                        orderable: false,
                        targets: [3]
                    } // Disable sorting on certificate column if needed
                ]
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
