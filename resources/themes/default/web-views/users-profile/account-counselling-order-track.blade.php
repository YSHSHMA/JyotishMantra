@extends('layouts.front-end.app')
@section('title', translate('order_Track'))
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.service-details.counselling-order-partial')
                <div class="card border-0">
                    <div class="card-body">
                        <div>
                        </div>

                        <ul class="nav nav-tabs media-tabs nav-justified order-track-info">

                            <li class="nav-item">
                                <div class="nav-link active-status">
                                    <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                        <div class="media-tab-media mx-sm-auto mb-3">
                                            <img src="{{ asset('/public/assets/front-end/img/track-order/order-placed.png') }}"
                                                alt="">
                                        </div>
                                        <div class="media-body">
                                            <div class="text-sm-center">
                                                <h6 class="media-tab-title text-nowrap mb-0 text-capitalize fs-14">Order
                                                    placed</h6>
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
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">Order Canceled</h6>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                    <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                        width="14" alt="">
                                                    <span
                                                        class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['order_canceled'])) }}</span>
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
                                                <img src="{{ asset('public/assets/front-end/img/track-order/delivered.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class="text-sm-center">
                                                    <h6 class="media-tab-title text-nowrap mb-0 fs-14">Order Completed</h6>
                                                </div>
                                                @if ($order['status'] == 1)
                                                    <div
                                                        class="d-flex align-items-center justify-content-sm-center gap-1 mt-2">
                                                        <img src="{{ asset('/public/assets/front-end/img/track-order/clock.png') }}"
                                                            width="14" alt="">
                                                        <span
                                                            class="text-muted fs-12">{{ date('h:i A, d M Y', strtotime($order['order_completed'])) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @if ($order['status'] == 1 && !empty($order['counselling_report']) && $order['counselling_report_verified'] == 1)
                                    <li class="nav-item">
                                        <div class="nav-link active-status">
                                            <div class="d-flex flex-sm-column gap-3 gap-sm-0">
                                                <div class="media-tab-media mb-3 mx-sm-auto">
                                                    <a href="{{ asset('storage/app/public/consultation-order-report/' . $order['counselling_report']) }}"
                                                        download="">
                                                        <img src="{{ asset('public/assets/front-end/img/download-report.gif') }}"
                                                            alt=""></a>
                                                </div>
                                                <div class="media-body">
                                                    <div class="text-sm-center">
                                                        <h6 class="media-tab-title text-nowrap mb-0 fs-14">Download Report
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endif

                        </ul>

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
