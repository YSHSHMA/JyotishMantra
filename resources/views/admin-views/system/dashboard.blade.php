@extends('layouts.back-end.app')
@section('title', translate('dashboard'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-12 mt-3">
            <ul class="nav nav-tabs mb-3 justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item col-2" role="presentation">
                    <button class="nav-link w-100 active" id="ecommerce-tab" data-toggle="pill" data-target="#ecommerce"
                        type="button" role="tab" aria-controls="ecommerce" aria-selected="true">Ecommerce</button>
                </li>
                <li class="nav-item col-2" role="presentation">
                    <button class="nav-link w-100" id="pooja-tab" data-toggle="pill" data-target="#pooja" type="button"
                        role="tab" aria-controls="pooja" aria-selected="true">Pooja</button>
                </li>
                <li class="nav-item col-2" role="presentation">
                    <button class="nav-link w-100" id="pandit-tab" data-toggle="pill" data-target="#pandit" type="button"
                        role="tab" aria-controls="pandit" aria-selected="true">Pandit Book</button>
                </li>
                <li class="nav-item col-2" role="presentation">
                    <button class="nav-link w-100" id="event-tab" data-toggle="pill" data-target="#event" type="button"
                        role="tab" aria-controls="event" aria-selected="false">Event</button>
                </li>
                <li class="nav-item col-2" role="presentation">
                    <button class="nav-link w-100" id="donate-tab" data-toggle="pill" data-target="#donate" type="button"
                        role="tab" aria-controls="donate" aria-selected="false">Donation</button>
                </li>
                <li class="nav-item col-2" role="presentation">
                    <button class="nav-link w-100" id="tour-tab" data-toggle="pill" data-target="#tour" type="button"
                        role="tab" aria-controls="tour" aria-selected="false">Tour & Travel</button>
                </li>
            </ul>
        </div>

        <div class="col-12">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="ecommerce" role="tabpanel" aria-labelledby="ecommerce-tab">
                    @include('admin-views.system.partials.ecommerce-dashboard')
                </div>

                <div class="tab-pane fade show" id="pooja" role="tabpanel" aria-labelledby="pooja-tab">
                    @include('admin-views.system.partials.pooja-dashboard')
                </div>
                <div class="tab-pane fade show" id="pandit" role="tabpanel" aria-labelledby="pandit-tab">
                    @include('admin-views.system.partials.pandit-dashboard')
                </div>

                <div class="tab-pane fade show" id="event" role="tabpanel" aria-labelledby="event-tab">
                    @include('admin-views.system.partials.event-dashboard')
                </div>

                <div class="tab-pane fade show" id="donate" role="tabpanel" aria-labelledby="donate-tab">
                    @include('admin-views.system.partials.donate-dashboard')
                </div>

                <div class="tab-pane fade show" id="tour" role="tabpanel" aria-labelledby="tour-tab">
                    @include('admin-views.system.partials.tour-dashboard')
                </div>
            </div>
        </div>
    </div>

    <span id="earning-statistics-url" data-url="{{ route('admin.dashboard.earning-statistics') }}"></span>
    <span id="order-status-url" data-url="{{ route('admin.dashboard.order-status') }}"></span>
    <span id="seller-text" data-text="{{ translate('vendor') }}"></span>
    <span id="message-commission-text" data-text="{{ translate('commission') }}"></span>
    <span id="in-house-text" data-text="{{ translate('In-house') }}"></span>
    <span id="customer-text" data-text="{{ translate('customer') }}"></span>
    <span id="store-text" data-text="{{ translate('store') }}"></span>
    <span id="product-text" data-text="{{ translate('product') }}"></span>
    <span id="order-text" data-text="{{ translate('order') }}"></span>
    <span id="brand-text" data-text="{{ translate('brand') }}"></span>
    <span id="business-text" data-text="{{ translate('business') }}"></span>
    <span id="orders-text" data-text="{{ $data['order'] }}"></span>
    <span id="user-overview-data" data-customer="{{ $data['getTotalCustomerCount'] }}"
        data-customer-title="{{ translate('customer') }}" data-vendor="{{ $data['getTotalVendorCount'] }}"
        data-vendor-title="{{ translate('vendor') }}" data-delivery-man="{{ $data['getTotalDeliveryManCount'] }}"
        data-delivery-man-title="{{ translate('delivery_man') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/chart.js.extensions/chartjs-extensions.js') }}">
    </script>
    <script
        src="{{ dynamicAsset(path: 'public/assets/back-end/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js') }}">
    </script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/apexcharts.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/dashboard.js') }}"></script>
@endpush
