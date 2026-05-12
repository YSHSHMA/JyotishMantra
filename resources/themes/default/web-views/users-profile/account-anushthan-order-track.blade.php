@extends('layouts.front-end.app')
@section('title', translate('order_Track'))
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.vipanushthan-details.anushthan-order-partial')
                <div class="card border-0">
                    <div class="card-body">
                        <div>
                            <h4 style="font-size: 14px;">Anushthan Tracking Status</h4>
                            <p style="font-size: 12px; color: #555;">Track the status of your Anushthan seamlessly and stay
                                updated with real-time notifications.</p>
                        </div>
                        <ul class="nav nav-tabs media-tabs nav-justified order-track-info">

                            <li class="nav-item">
                                <div class="nav-link active-status">
                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                        <div class="media-tab-media mx-sm-auto mb-3">
                                            <img src="{{ asset('/public/assets/front-end/img/track-order/poojaconformed.gif') }}"
                                                style="width:50px;height:50px;" alt="">
                                        </div>
                                        <div class="media-body">
                                            <div class="text-sm-center">
                                                <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">Anushthan
                                                    confirmed</h6>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                    width="14" alt="">
                                                <span
                                                    class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['created_at'])) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </li>
                            @if ($order['status'] == 6)
                                <li class="nav-item">
                                    <div class="nav-link active-status">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mb-3 mx-sm-auto">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/reject.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">Order Rejected</h6>
                                                    <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['created_at'])) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @else
                                @if ($order['status'] == 2)
                                    <li class="nav-item">
                                        <div class="nav-link active-status">
                                            <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                <div class="media-tab-media mb-3 mx-sm-auto">
                                                    <img src="{{ asset('public/assets/front-end/img/track-order/reject.gif') }}"
                                                        style="width:50px;height:50px;" alt="">
                                                </div>
                                                <div class="media-body">
                                                    <div class="text-sm-center">
                                                        <h6 class="media-tab-title text-nowrap mb-0 fs-14">Order Canceled
                                                        </h6>
                                                        <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                            width="14" alt="">
                                                        <span
                                                            class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['created_at'])) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @else
                                    {{-- Schedule time --}}
                                    @if ($order['schedule_time'] == null)
                                        <li class="nav-item ">
                                            <div class="nav-link ">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img src="{{ asset('public/assets/front-end/img/track-order/shadulpuja.gif') }}"
                                                            style="width:50px;height:50px;" alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6
                                                                class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">
                                                                Anushthan Time</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <div
                                                class="nav-link {{ $order['order_status'] == 1 || $order['order_status'] == 3 || $order['order_status'] == 4 || $order['order_status'] == 5 ? 'active-status' : '' }}">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img src="{{ asset('public/assets/front-end/img/track-order/shadulpuja.gif') }}"
                                                            style="width:50px;height:50px;" alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6
                                                                class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">
                                                                Schedule Time</h6>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                            <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                                width="14" alt="">
                                                            <span
                                                                class="text-muted fs-12">{{ date('h:i A', strtotime($order['schedule_time'])) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                    {{-- Live Time --}}
                                    @if ($order['live_stream'] == null)
                                        <li class="nav-item ">
                                            <div class="nav-link ">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img src="{{ asset('public/assets/front-end/img/track-order/livestreem.gif') }}"
                                                            style="width:50px;height:50px;" alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6
                                                                class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">
                                                                Live Stream </h6>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <div
                                                class="nav-link {{ $order['order_status'] == 1 || $order['order_status'] == 4 || $order['order_status'] == 5 ? 'active-status' : '' }}">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img alt=""
                                                            src="{{ asset('public/assets/front-end/img/track-order/livestreem.gif') }}"
                                                            style="width:50px;height:50px;">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6
                                                                class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">
                                                                Live Stream</h6>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                            <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                                width="14" alt="">
                                                            <span
                                                                class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['live_created_stream'])) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                    {{-- Pooja Video --}}
                                    @if ($order['pooja_video'] == null)
                                        <li class="nav-item">
                                            <div class="nav-link">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img src="{{ asset('public/assets/front-end/img/track-order/video.gif') }}"
                                                            style="width:50px;height:50px;" alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6 class="media-tab-title text-nowrap mb-0 fs-14">Preparing
                                                                Video</h6>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <div
                                                class="nav-link {{ $order['order_status'] == 1 || $order['pooja_video'] == 5 ? 'active-status' : '' }}">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img src="{{ asset('public/assets/front-end/img/track-order/video.gif') }}"
                                                            style="width:50px;height:50px;" alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6 class="media-tab-title text-nowrap mb-0 fs-14">Anushthan
                                                                Video Complete</h6>
                                                            <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                                width="14" alt="">
                                                            <span
                                                                class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['video_created_sharing'])) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                    {{-- Certificate --}}
                                    @if ($order['pooja_certificate'] == null)
                                        <li class="nav-item">
                                            <div class="nav-link">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img alt=""
                                                            src="{{ asset('public/assets/front-end/img/track-order/certificate.gif') }}"
                                                            style="width:50px;height:50px;">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6 class="media-tab-title text-nowrap mb-0 fs-14">Certificate
                                                                Generating</h6>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <div
                                                class="nav-link {{ $order['pooja_certificate'] != null ? 'active-status' : '' }}">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img alt=""
                                                            src="{{ asset('public/assets/front-end/img/track-order/certificate.gif') }}"
                                                            style="width:50px;height:50px;">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6 class="media-tab-title text-nowrap mb-0 fs-14">Certificate
                                                                Complete</h6>
                                                            @if (!empty($order['order_completed']))
                                                                <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                                    width="14" alt="">
                                                                <span
                                                                    class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['order_completed'])) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif


                                    @if ($order['status'] == 2)
                                        <li class="nav-item">
                                            <div class="nav-link active-status">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img src="{{ asset('public/assets/front-end/img/track-order/canceled.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6 class="media-tab-title text-nowrap mb-0 fs-14">Order
                                                                Canceled</h6>
                                                            @if (!empty($order['order_canceled']))
                                                                <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                                    width="14" alt="">
                                                                <span
                                                                    class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['order_canceled'])) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <div class="nav-link {{ $order['status'] == 1 ? 'active-status' : '' }}">
                                                <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                    <div class="media-tab-media mb-3 mx-sm-auto">
                                                        <img src="{{ asset('public/assets/front-end/img/track-order/completed.gif') }}"
                                                            style="width:50px;height:50px;" alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="text-sm-center">
                                                            <h6 class="media-tab-title text-nowrap mb-0 fs-14">Order
                                                                Completed</h6>
                                                            @if (!empty($order['order_completed']))
                                                                <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                                    width="14" alt="">
                                                                <span
                                                                    class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['order_completed'])) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endif
                            @endif

                        </ul>

                        <hr>

                    </div>
                </div>
                <div class="card border-0">
                    @if (!empty($prashad) && isset($prashad['pooja_status']) && $prashad['pooja_status'] == 1)
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 style="font-size: 14px;">Anushthan Prashad Tracking Status</h4>
                                    <p style="font-size: 12px; color: #666;">Track the status of your Anushthan Prashad
                                        order and stay updated with real-time information.</p>
                                </div>
                                <div class="col-md-6">
                                    <h4 style="font-size: 14px;">Order Details</h4>
                                    <div style="display: flex; justify-content: space-between;">
                                        <p style="font-size: 12px; color: #666;">AWB No.
                                            {{ $prashad['awb'] ?? 'waiting...' }}, </p>
                                        @if (!empty($prashad['awb']))
                                            <a href="https://mahakal.shipway.com/t/{{ $prashad['awb'] ?? 'track' }}"
                                                class="btn btn-outline-primary" target="_blank"> Track</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                    @endif

                    @if (!empty($prashad) && isset($prashad['pooja_status']) && $prashad['pooja_status'] == 1)
                        <ul class="nav nav-tabs media-tabs nav-justified order-track-info pb-5">
                            @if ($prashad['order_status'] == 'confirmed')
                                <li class="nav-item">
                                    <div class="nav-link active-status">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/confirmed.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('Order Confirmed') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                    <img src="{{ asset('public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($prashad->updated_at)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @else
                                <li class="nav-item">
                                    <div class="nav-link {{ $prashad['status'] == 1 ? 'active-status' : '' }}">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/confirmed.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('Order Confirmed') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                    <img src="{{ asset('public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($prashad->updated_at)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if ($prashad['order_status'] == 'processing')
                                <li class="nav-item">
                                    <div class="nav-link active-status">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/in-transit.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('Processing') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                    <img src="{{ asset('public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($prashad->updated_at)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @else
                                <li class="nav-item">
                                    <div class="nav-link {{ $prashad['status'] == 1 ? 'active-status' : '' }}">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/in-transit.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('Processing') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                    <img src="{{ asset('public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($prashad->updated_at)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if ($prashad['order_status'] == 'in-transit')
                                <li class="nav-item">
                                    <div class="nav-link active-status">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/in-transit.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('In Transit') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                    <img src="{{ asset('public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($prashad->updated_at)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @else
                                <li class="nav-item">
                                    <div
                                        class="nav-link {{ $prashad['order_status'] == 'in-transit' ? 'active-status' : '' }}">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/in-transit.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('In Transit') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if ($prashad['order_status'] == 'out_for_pickup')
                                <li class="nav-item">
                                    <div class="nav-link active-status">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/pickup.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('Out for Pickup') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                    <img src="{{ asset('public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($prashad->updated_at)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @else
                                <li class="nav-item">
                                    <div
                                        class="nav-link {{ $prashad['order_status'] == 'out_for_pickup' ? 'active-status' : '' }}">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/pickup.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('Out for Pickup') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if ($prashad['order_status'] == 'delivered')
                                <li class="nav-item">
                                    <div class="nav-link active-status">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/delivered.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('Order Delivered') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                    <img src="{{ asset('public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($prashad->updated_at)) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @else
                                <li class="nav-item">
                                    <div
                                        class="nav-link {{ $prashad['order_status'] == 'delivered' ? 'active-status' : '' }}">
                                        <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                            <div class="media-tab-media mx-sm-auto mb-3">
                                                <img src="{{ asset('public/assets/front-end/img/track-order/delivered.gif') }}"
                                                    style="width:50px;height:50px;" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">
                                                        {{ translate('Order Delivered') }}</h6>
                                                </div>
                                                <div
                                                    class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                        </ul>
                    @else
                        <div class="card-body" style="text-align: center;">
                            <div>
                                <h4 style="font-size: 14px;">No Selected Prashad</h4>
                                <p style="font-size: 12px; color: #555;">Track the status of your Anushthan seamlessly and
                                    stay updated with real-time notifications.</p>
                            </div>
                        </div>
                    @endif
                </div>
        </div>
        </section>
    </div>
    </div>
    <span id="message-ratingContent" data-poor="{{ translate('poor') }}" data-average="{{ translate('average') }}"
        data-good="{{ translate('good') }}" data-good-message="{{ translate('the_delivery_service_is_good') }}"
        data-good2="{{ translate('very_Good') }}"
        data-good2-message="{{ translate('this_delivery_service_is_very_good_I_am_highly_impressed') }}"
        data-excellent="{{ translate('excellent') }}"
        data-excellent-message="{{ translate('best_delivery_service_highly_recommended') }}"></span>
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>
@endpush
