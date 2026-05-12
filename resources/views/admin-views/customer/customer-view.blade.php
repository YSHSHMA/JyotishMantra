@extends('layouts.back-end.app')
@section('title', translate('customer_Details'))
@section('content')
<div class="content container-fluid">
    <div class="d-print-none pb-2">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <div class="mb-3">
                    <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/customer.png') }}"
                            alt="">
                        {{ translate('customer_details') }}
                    </h2>
                </div>
                <div class="d-sm-flex align-items-sm-center">
                    <h3 class="page-header-title">{{ translate('customer_ID') }} #{{ $customer['id'] }}</h3>
                    <span class="{{ Session::get('direction') === 'rtl' ? 'mr-2 mr-sm-3' : 'ml-2 ml-sm-3' }}">
                        <i class="tio-date-range">
                        </i>
                        {{ translate('joined_At') . ':' . date('d M Y H:i:s', strtotime($customer['created_at'])) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-2 remove-card-shadow">
        <div class="card-body">
            <div class="row flex-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img src="http://localhost/mahakal_final/public/assets/back-end/img/business_analytics.png"
                            alt="">Total Order analytics
                    </h4>
                </div>
            </div>
            <div class="row g-2" id="order_stats">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <a class="order-stats order-stats_pending">
                                <div class="order-stats__content">
                                    <img width="20"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pending.png') }}"
                                        alt="">
                                    <h6 class="order-stats__subtitle">Product Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\Order::where('customer_id', $customer['id'])->count() }}
                                </span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <a class="order-stats order-stats_confirmed">
                                <div class="order-stats__content">
                                    <img width="20"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/poojas.png') }}"
                                        alt="">
                                    <h6 class="order-stats__subtitle">Pooja Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\Service_order::where('customer_id', $customer['id'])->where('type', 'pooja')->count() }}
                                </span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <a class="order-stats order-stats_packaging">
                                <div class="order-stats__content">
                                    <img width="20"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/vippooja.png') }}"
                                        alt="">
                                    <h6 class="order-stats__subtitle">Vip Pooja</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\Service_order::where('customer_id', $customer['id'])->where('type', 'vip')->count() }}
                                </span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <a class="order-stats order-stats_out-for-delivery">
                                <div class="order-stats__content">
                                    <img width="20"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/anushthan.png') }}"
                                        alt="">
                                    <h6 class="order-stats__subtitle">Anushthan Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\Service_order::where('customer_id', $customer['id'])->where('type', 'anushthan')->count() }}
                                </span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <div class="order-stats order-stats_delivered cursor-pointer">
                                <div class="order-stats__content">
                                    <img width="20"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/chadhava.png') }}"
                                        alt="">
                                    <h6 class="order-stats__subtitle">Chadhava Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\Chadhava_orders::where('customer_id', $customer['id'])->where('type', 'chadhava')->count() }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <div class="order-stats order-stats_delivered cursor-pointer">
                                <div class="order-stats__content">
                                    <img width="20"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/chadhava.png') }}"
                                        alt="">
                                    <h6 class="order-stats__subtitle">Counselling Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\Service_order::where('customer_id', $customer['id'])->where('type', 'counselling')->count() }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <div class="order-stats order-stats_delivered cursor-pointer">
                                <div class="order-stats__content">
                                    <img width="20"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/chadhava.png') }}"
                                        alt="">
                                    <h6 class="order-stats__subtitle">Offline Puja Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\OfflinePoojaOrder::where('customer_id',$customer->id)->count() }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <div class="order-stats order-stats_delivered cursor-pointer">
                                <div class="order-stats__content">
                                    <img width="20" src="{{ dynamicAsset(path: 'public/assets/front-end/img/car.png') }}" alt="">
                                    <h6 class="order-stats__subtitle">Tour Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\TourOrder::where('amount_status',1)->where('user_id',$customer->id)->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <div class="order-stats order-stats_delivered cursor-pointer">
                                <div class="order-stats__content">
                                    <img width="20" src="{{ dynamicAsset(path: 'public/assets/front-end/img/track-order/livestreem.png') }}" alt="">
                                    <h6 class="order-stats__subtitle">Event Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\EventOrder::where('transaction_status',1)->where('user_id',$customer->id)->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <div class="order-stats order-stats_delivered cursor-pointer">
                                <div class="order-stats__content">
                                    <img width="20" src="{{ dynamicAsset(path: 'public/assets/front-end/img/reward-card.png') }}" alt="">
                                    <h6 class="order-stats__subtitle">Donate Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\DonateAllTransaction::whereIn('type',['donate_trust','donate_ads'])->where('amount_status',1)->where('user_id',$customer->id)->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 pb-2">
                            <div class="order-stats order-stats_delivered cursor-pointer">
                                <div class="order-stats__content">
                                    <img width="20" src="{{ dynamicAsset(path: 'public/assets/front-end/img/track-order/pooja.png') }}" alt="">
                                    <h6 class="order-stats__subtitle">Paid Kundali Order</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ \App\Models\BirthJournalKundali::where('payment_status',1)->where('user_id',$customer->id)->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        @if ($customer)
                        <div class="card-body">
                            <h4 class="mb-4 d-flex align-items-center gap-2">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}"
                                    alt="">
                                {{ translate('customer') }}
                            </h4>
                            <div class="media">
                                <div class="mr-3">
                                    <img class="avatar rounded-circle avatar-70"
                                        src="{{ getValidImage(path: 'storage/app/public/profile/' . $customer['image'], type: 'backend-profile') }}"
                                        alt="{{ translate('image') }}">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span
                                        class="title-color hover-c1"><strong>{{ $customer['f_name'] . ' ' . $customer['l_name'] }}</strong></span>
                                    <span class="title-color">
                                        @php
                                        $poCount = App\Models\Service_order::where('customer_id',$customer->id)->count();
                                        $coCount = App\Models\Chadhava_orders::where('customer_id',$customer->id)->count();
                                        $opoCount = App\Models\OfflinePoojaOrder::where('customer_id',$customer->id)->count();
                                        $toCount = App\Models\TourOrder::where('amount_status',1)->where('user_id',$customer->id)->count();
                                        $evoCount = App\Models\EventOrder::where('transaction_status',1)->where('user_id',$customer->id)->count();
                                        $doCount = App\Models\DonateAllTransaction::whereIn('type',['donate_trust','donate_ads'])->where('amount_status',1)->where('user_id',$customer->id)->count();
                                        $koCount = App\Models\BirthJournalKundali::where('payment_status',1)->where('user_id',$customer->id)->count();
                                        @endphp
                                        <strong>
                                            {{ count($customer['orders']??[]) + $poCount + $coCount + $opoCount + $toCount + $evoCount + $doCount + $koCount }}
                                        </strong>{{ translate('orders') }}
                                    </span>
                                    <span class="title-color"><strong>{{ $customer['phone'] }}</strong></span>
                                    <span class="title-color">{{ $customer['email'] }}</span>
                                </div>
                                <div class="media-body text-right">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="white-space: nowrap;">
        <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist" style="flex-wrap: nowrap; overflow-x: auto;">
            @if (!empty($paginatedOrders))
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-pooja-tab" data-toggle="pill" data-target="#all-pooja"
                    type="button" role="tab" aria-controls="all-pooja" aria-selected="true">All Order</button>
            </li>
            @endif
            @if (!empty($orders))
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ empty($orders) ? 'active' : '' }}" id="product-tab" data-toggle="pill" data-target="#product" type="button"
                    role="tab" aria-controls="product" aria-selected="true">Product Order</button>
            </li>
            @endif
            @if (!empty($poojaorders))
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ empty($poojaorders) ? 'active' : '' }}" id="pooja-tab" data-toggle="pill" data-target="#pooja" type="button"
                    role="tab" aria-controls="pooja" aria-selected="false">Pooja Order</button>
            </li>
            @endif
            @if (!empty($viporders))
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ empty($viporders) ? 'active' : '' }}" id="vip-tab" data-toggle="pill" data-target="#vip" type="button"
                    role="tab" aria-controls="vip" aria-selected="false">VIP Pooja Order</button>
            </li>
            @endif
            @if (!empty($anushthanOrder))
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ empty($anushthanOrder) ? 'active' : '' }}" id="anushthan-tab" data-toggle="pill" data-target="#anushthan" type="button"
                    role="tab" aria-controls="anushthan" aria-selected="false">Anushthan Order</button>
            </li>
            @endif
            @if (!empty($ChadhavaOrder))
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ empty($ChadhavaOrder) ? 'active' : '' }}" id="chadhava-tab" data-toggle="pill" data-target="#chadhava" type="button"
                    role="tab" aria-controls="chadhava" aria-selected="false">Chadhava Order</button>
            </li>
            @endif
            @if (!empty($counsellingOrder))
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ empty($counsellingOrder) ? 'active' : '' }}" id="counselling-tab" data-toggle="pill" data-target="#counselling"
                    type="button" role="tab" aria-controls="counselling" aria-selected="false">Counselling</button>
            </li>
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="offline-tab" data-toggle="pill" data-target="#offline-orders" type="button" role="tab" aria-controls="offline-orders" aria-selected="false">Offline Order</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link __inline-tab-show" id="Tour-tab" data-toggle="pill" data-target="#tour-orders" type="button" role="tab" aria-controls="tour-orders" aria-selected="false">Tour Order</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link __inline-tab-show" id="Event-tab" data-toggle="pill" data-target="#event-orders" type="button" role="tab" aria-controls="event-orders" aria-selected="false">Event Order</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link __inline-tab-show" id="Donate-tab" data-toggle="pill" data-target="#donate-orders" type="button" role="tab" aria-controls="donate-orders" aria-selected="false">Donate Order</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link __inline-tab-show" id="kundali-tab" data-toggle="pill" data-target="#kundali-orders" type="button" role="tab" aria-controls="kundali-orders" aria-selected="false">Kundali Order</button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            {{-- All Order Details --}}
            @if (!empty($paginatedOrders))
            <div class="tab-pane fade show active" id="all-pooja" role="tabpanel" aria-labelledby="all-pooja-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue"
                                                    class="form-control" placeholder="{{ translate('search_orders') }}"
                                                    aria-label="Search orders" value="{{ request('searchValue') }}"
                                                    required>
                                                <button type="submit"
                                                    class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th>{{ translate('booking_date') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paginatedOrders as $key => $orderall)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($orderall['type'] == 'pooja')
                                                <a href="{{ route('admin.pooja.orders.details', [$orderall['id']]) }}" data-addedby="{{ $orderall['added_by'] }}"
                                                    data-id="{{ $orderall['id'] }}" class="title-color hover-c1">{{ $orderall['order_id'] }}</a>
                                                @elseif($orderall['type'] == 'counselling')
                                                <a href="{{ route('admin.counselling.order.details', [$orderall['id']]) }}" data-addedby="{{ $orderall['added_by'] }}"
                                                    data-id="{{ $orderall['id'] }}" class="title-color hover-c1">{{ $orderall['order_id'] }}</a>
                                                @elseif($orderall['type'] == 'vip')
                                                <a href="{{ route('admin.vippooja.order.details', [$orderall['id']]) }}" data-addedby="{{ $orderall['added_by'] }}"
                                                    data-id="{{ $orderall['id'] }}" class="title-color hover-c1">{{ $orderall['order_id'] }}</a>
                                                @elseif($orderall['type'] == 'anushthan')
                                                <a href="{{ route('admin.anushthan.order.details', [$orderall['id']]) }}" data-addedby="{{ $orderall['added_by'] }}"
                                                    data-id="{{ $orderall['id'] }}" class="title-color hover-c1">{{ $orderall['order_id'] }}</a>
                                                @elseif(($orderall['chadhavaOrders']['type']??'') == 'chadhava')
                                                <a href="{{ route('admin.chadhava.order.details', [$orderall['chadhavaOrders']['id']]) }}" data-addedby="{{ $orderall['added_by'] }}"
                                                    data-id="{{ $orderall['id'] }}" class="title-color hover-c1">{{ $orderall['order_id'] }}</a>
                                                @elseif($orderall['type'] == 'shop')
                                                <a href="{{ route('admin.orders.details', ['id' => $orderall['id']]) }}"
                                                    class="title-color hover-c1">{{ $orderall['id'] }}</a>
                                                @elseif($orderall['type'] == 'offlinepooja')
                                                {{ $orderall['order_id']??"" }}
                                                @elseif($orderall['type'] == 'event')
                                                {{ $orderall['order_no']??"" }}
                                                @elseif($orderall['type'] == 'tour')
                                                {{ $orderall['order_id']??"" }}
                                                @elseif($orderall['type'] == 'donate')
                                                {{ $orderall['trans_id']??"" }}
                                                @elseif($orderall['type'] == 'kundli' || $orderall['type'] == 'kundli milan')
                                                {{ $orderall['order_id']??""}}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ucwords($orderall['type'])}}
                                            </td>
                                            <td>
                                                @if ($orderall['type'] == 'pooja')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['pay_amount']??0)) }}
                                                @elseif($orderall['type'] == 'counselling')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['pay_amount']??0)) }}
                                                @elseif($orderall['type'] == 'vip')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['pay_amount']??0)) }}
                                                @elseif($orderall['type'] == 'anushthan')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['pay_amount']??0)) }}
                                                @elseif(($orderall['chadhavaOrders']['type']??"") == 'chadhava')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['chadhavaOrders']['pay_amount']??0)) }}
                                                @elseif($orderall['type'] == 'shop')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['order_amount'])) }}
                                                @elseif($orderall['type'] == 'offlinepooja')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['pay_amount']??0)) }}
                                                @elseif($orderall['type'] == 'event')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['amount']??0)) }}
                                                @elseif($orderall['type'] == 'tour')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['amount']??0)) }}
                                                @elseif($orderall['type'] == 'donate')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['amount']??0)) }}
                                                @elseif($orderall['type'] == 'kundli' || $orderall['type'] == 'kundli milan')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderall['amount']??0)) }}
                                                @endif
                                            </td>
                                            <td>

                                                @if($orderall['type'] == 'event')
                                                <?php if ($orderall['transaction_status'] == 1 && $orderall['status'] == 1) {
                                                    $showClass = 'badge-soft-badge-soft-primary';
                                                    $message = 'Completed';
                                                } elseif ($orderall['transaction_status'] == 0 && $orderall['status'] == 1) {
                                                    $showClass = 'badge-soft badge-warning';
                                                    $message = 'Pending';
                                                } elseif ($orderall['transaction_status'] == 1 && $orderall['status'] == 3) {
                                                    $showClass = 'badge-soft-badge-soft-danger';
                                                    $message = 'Refund';
                                                } else {
                                                    $showClass = 'badge-soft-badge-soft-danger';
                                                    $message = 'Canceled';
                                                }
                                                ?>
                                                <span class="status-badge rounded-pill __badge {{ $showClass }} fs-12 font-semibold text-capitalize ">{{ $message }}</span>
                                                @elseif($orderall['type'] == 'tour')
                                                <?php
                                                if (($orderall['status'] == 0 || $orderall['status'] == 1) && $orderall['cab_assign'] == 0 && $orderall['pickup_status'] == 0) {
                                                    $showClass = 'primary';
                                                    $showName = 'Pending';
                                                } elseif (($orderall['status'] == 0 || $orderall['status'] == 1) && $orderall['cab_assign'] != 0 && $orderall['pickup_status'] == 0) {
                                                    $showClass = 'primary';
                                                    $showName = 'Processing';
                                                } elseif (($orderall['status'] == 0 || $orderall['status'] == 1) && $orderall['cab_assign'] != 0 && $orderall['pickup_status'] == 1 && $orderall['drop_status'] == 0) {
                                                    $showClass = 'success';
                                                    $showName = 'Pickup';
                                                } elseif (($orderall['status'] == 0 || $orderall['status'] == 1) && $orderall['cab_assign'] != 0 && $orderall['drop_status'] == 1) {
                                                    $showClass = 'success';
                                                    $showName = 'Completed';
                                                } else {
                                                    $showClass = 'danger';
                                                    $showName = 'Refund';
                                                }
                                                ?>
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $showClass }} fs-12 font-semibold text-capitalize">{{ $showName }}</span>
                                                @elseif($orderall['type'] == 'donate')
                                                <span class="status-badge rounded-pill __badge fs-12 font-semibold text-capitalize badge-soft-badge-soft-{{ $orderall['amount_status'] == 1 ? 'success' : 'danger' }}">{{ $orderall['amount_status'] == 1 ? 'Success' : 'Pending' }}</span>
                                                @elseif($orderall['type'] == 'kundli' || $orderall['type'] == 'kundli milan')
                                                <span class="status-badge rounded-pill __badge fs-12 font-semibold text-capitalize badge-soft-badge-soft-{{ $orderall['payment_status'] == 1 ? 'success' : 'danger' }}">{{ $orderall['payment_status'] == 1 ? 'Success' : 'Pending' }}</span>
                                                @elseif($orderall['type'] == 'shop')
                                                @if ($orderall['order_status'] == 'pending')
                                                <span class="badge badge-soft-info fz-12">
                                                    {{ translate($orderall['order_status']) }}
                                                </span>
                                                @elseif($orderall['order_status'] == 'processing' || $orderall['order_status'] == 'out_for_delivery')
                                                <span class="badge badge-soft-warning fz-12">
                                                    {{ str_replace('_', ' ', $orderall['order_status'] == 'processing' ? translate('packaging') : translate($orderall['order_status'])) }}
                                                </span>
                                                @elseif($orderall['order_status'] == 'confirmed')
                                                <span class="badge badge-soft-success fz-12">
                                                    {{ translate($orderall['order_status']) }}
                                                </span>
                                                @elseif($orderall['order_status'] == 'failed')
                                                <span class="badge badge-soft-danger fz-12">
                                                    {{ translate('failed_to_deliver') }}
                                                </span>
                                                @elseif($orderall['order_status'] == 'delivered')
                                                <span class="badge badge-soft-success fz-12">
                                                    {{ translate($orderall['order_status']) }}
                                                </span>
                                                @else
                                                <span class="badge badge-soft-danger fz-12">
                                                    {{ translate($orderall['order_status']) }}
                                                </span>
                                                @endif
                                                @else
                                                <span class="fz-12 badge badge-soft-{{ $orderall->status == 0
                                                    ? 'primary'
                                                    : ($orderall->status == 1
                                                    ? 'success'
                                                    : ($orderall->status == 2
                                                    ? 'danger'
                                                    : ($orderall->status == 3
                                                    ? 'dark'
                                                    : ($orderall->status == 4
                                                    ? 'info'
                                                    : ($orderall->status == 5
                                                    ? 'secondary'
                                                    : ($orderall->status == 6
                                                    ? 'warning'
                                                    : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                    {{ $orderall->status == 0
                                                    ? 'Pending'
                                                    : ($orderall->status == 1
                                                    ? 'Completed'
                                                    : ($orderall->status == 2
                                                    ? 'Canceled'
                                                    : ($orderall->status == 3
                                                    ? 'Schedule Time'
                                                    : ($orderall->status == 4
                                                    ? 'Live Stream'
                                                    : ($orderall->status == 5
                                                    ? 'Video Share'
                                                    : ($orderall->status == 6
                                                    ? 'Rejected'
                                                    : 'Warning')))))) }}
                                                </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ date('d M,Y h:i A',strtotime($orderall['created_at'])) }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    @if ($orderall['type'] == 'pooja')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.anushthan.order.details', [$orderall['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.anushthan.order.generate.invoice', $orderall['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                    @elseif($orderall['type'] == 'counselling')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.counselling.order.details', [$orderall['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.counselling.order.generate.invoice', $orderall['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                    @elseif($orderall['type'] == 'vip')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.vippooja.order.details', [$orderall['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.vippooja.order.generate.invoice', $orderall['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                    @elseif($orderall['type'] == 'anushthan')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.anushthan.order.details', [$orderall['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.anushthan.order.generate.invoice', $orderall['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                    @elseif(($orderall['chadhavaOrders']['type']??'') == 'chadhava')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.chadhava.order.details', [$orderall['chadhavaOrders']['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.chadhava.order.generate.invoice', $orderall['chadhavaOrders']['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                    @elseif($orderall['type'] == 'shop')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.orders.details', ['id' => $orderall['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.orders.generate-invoice', [$orderall['id']]) }}"><i
                                                            class="tio-download"></i> </a>
                                                    @elseif($orderall['type'] == 'event')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn" href="{{ route('admin.event-managment.event-booking.user-booking-details',[$orderall['id']]) }}">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                    <a title="Download Event invoice" class="btn btn-outline-info btn-sm square-btn"  href="{{ route('admin.event-managment.event-booking.booking-invoice',[$orderall['id']]) }}">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    @elseif($orderall['type'] == 'tour')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn" href="{{ route('admin.tour-visits-booking.user-booking-details',[$orderall['id']]) }}">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                    <a href="{{ route('tour.tour-pdf-invoice', [$orderall['id']]) }}" title="Download Tour invoice" class="btn btn-outline-info btn-sm square-btn">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    @elseif($orderall['type'] == 'donate')
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn" title="{{ translate('view') }}" href="{{ route('admin.donate_management.donated.view',['id'=>$orderall['id']])}}">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('invoice') }}" target="_blank" href="{{ route('donate-create-pdf-invoice', [$orderall['id']]) }}">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    @if(!empty($orderall['pan_card']))
                                                    <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('80G') }}" target="_blank" href="{{ url('api/v1/donate/twoal-a-certificate', [$orderall['id']]) }}">
                                                        <i class="tio-file_text">file_text</i>
                                                    </a>
                                                    @endif
                                                    @elseif($orderall['type'] == 'kundli' || $orderall['type'] == 'kundli milan')
                                                    <a href="{{ route('admin.birth_journal.order.generate-invoice', [$orderall['id']]) }}"
                                                        target="_blank" title="{{ translate('invoice') }}"
                                                        class="btn btn-outline-info btn-sm square-btn">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    <a href="{{ route('admin.birth_journal.view-kundali-milan', [$orderall['id']]) }}"
                                                        title="{{ translate('View_details') }}"
                                                        class="btn btn-outline--primary btn-sm edit square-btn">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                    @if ($orderall['kundali_pdf'] && $orderall['milan_verify'] == 1)
                                                    <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/' . $orderall['kundali_pdf']) }}"
                                                        target="_blank" title="{{ translate('download_PDF') }}"
                                                        class="btn btn-outline-success btn-sm square-btn">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    @else
                                                    <span class="status-badge text-white rounded-pill __badge badge-soft badge-warning fs-12 font-semibold text-capitalize " style="padding: 1px 9px;">progress</span>
                                                    @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @if (count($paginatedOrders) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                                <div class="card-footer border-0">
                                    {{ $paginatedOrders->appends(['all_order' => request('all_order')])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($orders))
            {{-- Product Order --}}
            <div class="tab-pane fade" id="product" role="tabpanel" aria-labelledby="product-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue"
                                                    class="form-control" placeholder="{{ translate('search_orders') }}"
                                                    aria-label="Search orders" value="{{ request('searchValue') }}"
                                                    required>
                                                <button type="submit"
                                                    class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_ID') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $orders->firstItem() + $key }}</td>
                                            <td>
                                                <a href="{{ route('admin.orders.details', ['id' => $order['id']]) }}"
                                                    class="title-color hover-c1">{{ $order['id'] }}</a>
                                            </td>
                                            <td> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['order_amount']??0)) }}
                                            </td>
                                            <td>
                                                @if ($order['order_status'] == 'pending')
                                                <span class="badge badge-soft-info fz-12">
                                                    {{ translate($order['order_status']) }}
                                                </span>
                                                @elseif($order['order_status'] == 'processing' || $order['order_status'] == 'out_for_delivery')
                                                <span class="badge badge-soft-warning fz-12">
                                                    {{ str_replace('_', ' ', $order['order_status'] == 'processing' ? translate('packaging') : translate($order['order_status'])) }}
                                                </span>
                                                @elseif($order['order_status'] == 'confirmed')
                                                <span class="badge badge-soft-success fz-12">
                                                    {{ translate($order['order_status']) }}
                                                </span>
                                                @elseif($order['order_status'] == 'failed')
                                                <span class="badge badge-soft-danger fz-12">
                                                    {{ translate('failed_to_deliver') }}
                                                </span>
                                                @elseif($order['order_status'] == 'delivered')
                                                <span class="badge badge-soft-success fz-12">
                                                    {{ translate($order['order_status']) }}
                                                </span>
                                                @else
                                                <span class="badge badge-soft-danger fz-12">
                                                    {{ translate($order['order_status']) }}
                                                </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.orders.details', ['id' => $order['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.orders.generate-invoice', [$order['id']]) }}"><i
                                                            class="tio-download"></i> </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (count($orders) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                                <div class="card-footer">
                                    {!! $orders->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($poojaorders))
            {{-- Pooja  Order --}}
            <div class="tab-pane fade" id="pooja" role="tabpanel" aria-labelledby="pooja-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue"
                                                    class="form-control" placeholder="{{ translate('search_orders') }}"
                                                    aria-label="Search orders" value="{{ request('searchValue') }}"
                                                    required>
                                                <button type="submit"
                                                    class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($poojaorders as $key => $porder)
                                        <tr>
                                            <td>{{ $poojaorders->firstItem() + $key }}</td>
                                            <td>
                                                <a href="{{ route('admin.pooja.orders.details', [$porder['id']]) }}"
                                                    data-addedby="{{ $porder['added_by'] }}"
                                                    data-id="{{ $porder['id'] }}"
                                                    class="title-color hover-c1">{{ $porder['order_id'] }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{$porder['type']}}
                                            </td>
                                            <td> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $porder['pay_amount']??0)) }}
                                            </td>
                                            <td>
                                                <span
                                                    class="fz-12 badge badge-soft-{{ $porder->status == 0
                                                    ? 'primary'
                                                    : ($porder->status == 1
                                                    ? 'success'
                                                    : ($porder->status == 2
                                                    ? 'danger'
                                                    : ($porder->status == 3
                                                    ? 'dark'
                                                    : ($porder->status == 4
                                                    ? 'info'
                                                    : ($porder->status == 5
                                                    ? 'secondary'
                                                    : ($porder->status == 6
                                                    ? 'warning'
                                                    : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                    {{ $porder->status == 0
                                                    ? 'Pending'
                                                    : ($porder->status == 1
                                                    ? 'Completed'
                                                    : ($porder->status == 2
                                                    ? 'Canceled'
                                                    : ($porder->status == 3
                                                    ? 'Schedule Time'
                                                    : ($porder->status == 4
                                                    ? 'Live Stream'
                                                    : ($porder->status == 5
                                                    ? 'Video Share'
                                                    : ($porder->status == 6
                                                    ? 'Rejected'
                                                    : 'Warning')))))) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.pooja.orders.details', [$porder['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.pooja.orders.generate.invoice', $porder['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (count($poojaorders) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                                <div class="card-footer">
                                    {!! $poojaorders->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($viporders))
            {{-- Vip Pooja  Order --}}
            <div class="tab-pane fade" id="vip" role="tabpanel" aria-labelledby="vip-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue"
                                                    class="form-control" placeholder="{{ translate('search_orders') }}"
                                                    aria-label="Search orders" value="{{ request('searchValue') }}"
                                                    required>
                                                <button type="submit"
                                                    class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($viporders as $key => $vorder)
                                        <tr>
                                            <td>{{ $orders->firstItem() + $key }}</td>
                                            <td>
                                                <a href="{{ route('admin.vippooja.order.details', [$vorder['id']]) }}"
                                                    data-addedby="{{ $vorder['added_by'] }}"
                                                    data-id="{{ $vorder['id'] }}"
                                                    class="title-color hover-c1">{{ $vorder['order_id'] }}</a>
                                                @if($vorder->package_id == 6)
                                                <span class="badge badge-danger">I</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{$vorder['type']}}


                                            </td>
                                            <td> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $vorder['pay_amount']??0)) }}
                                            </td>
                                            <td>
                                                <span
                                                    class="fz-12 badge badge-soft-{{ $vorder->status == 0
                                                    ? 'primary'
                                                    : ($vorder->status == 1
                                                    ? 'success'
                                                    : ($vorder->status == 2
                                                    ? 'danger'
                                                    : ($vorder->status == 3
                                                    ? 'dark'
                                                    : ($vorder->status == 4
                                                    ? 'info'
                                                    : ($vorder->status == 5
                                                    ? 'secondary'
                                                    : ($vorder->status == 6
                                                    ? 'warning'
                                                    : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                    {{ $vorder->status == 0
                                                    ? 'Pending'
                                                    : ($vorder->status == 1
                                                    ? 'Completed'
                                                    : ($vorder->status == 2
                                                    ? 'Canceled'
                                                    : ($vorder->status == 3
                                                    ? 'Schedule Time'
                                                    : ($vorder->status == 4
                                                    ? 'Live Stream'
                                                    : ($vorder->status == 5
                                                    ? 'Video Share'
                                                    : ($vorder->status == 6
                                                    ? 'Rejected'
                                                    : 'Warning')))))) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.vippooja.order.details', [$vorder['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.vippooja.order.generate.invoice', $vorder['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (count($viporders) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                                <div class="card-footer">
                                    {!! $viporders->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($anushthanOrder))
            {{-- Anushtah Pooja  Order --}}
            <div class="tab-pane fade" id="anushthan" role="tabpanel" aria-labelledby="anushthan-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue"
                                                    class="form-control" placeholder="{{ translate('search_orders') }}"
                                                    aria-label="Search orders" value="{{ request('searchValue') }}"
                                                    required>
                                                <button type="submit"
                                                    class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($anushthanOrder as $key => $aorder)
                                        <tr>
                                            <td>{{ $orders->firstItem() + $key }}</td>
                                            <td>
                                                <a href="{{ route('admin.anushthan.order.details', [$aorder['order_id']]) }}"
                                                    data-addedby="{{ $aorder['added_by'] }}"
                                                    data-id="{{ $aorder['id'] }}"
                                                    class="title-color hover-c1">{{ $aorder['order_id'] }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{$aorder['type']}}
                                            </td>
                                            <td> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $aorder['pay_amount']??0)) }}
                                            </td>
                                            <td>
                                                <span
                                                    class="fz-12 badge badge-soft-{{ $aorder->status == 0
                                                    ? 'primary'
                                                    : ($aorder->status == 1
                                                    ? 'success'
                                                    : ($aorder->status == 2
                                                    ? 'danger'
                                                    : ($aorder->status == 3
                                                    ? 'dark'
                                                    : ($aorder->status == 4
                                                    ? 'info'
                                                    : ($aorder->status == 5
                                                    ? 'secondary'
                                                    : ($aorder->status == 6
                                                    ? 'warning'
                                                    : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                    {{ $aorder->status == 0
                                                    ? 'Pending'
                                                    : ($aorder->status == 1
                                                    ? 'Completed'
                                                    : ($aorder->status == 2
                                                    ? 'Canceled'
                                                    : ($aorder->status == 3
                                                    ? 'Schedule Time'
                                                    : ($aorder->status == 4
                                                    ? 'Live Stream'
                                                    : ($aorder->status == 5
                                                    ? 'Video Share'
                                                    : ($aorder->status == 6
                                                    ? 'Rejected'
                                                    : 'Warning')))))) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.anushthan.order.details', [$aorder['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.anushthan.order.generate.invoice', $aorder['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (count($anushthanOrder) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                                <div class="card-footer">
                                    {!! $anushthanOrder->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($ChadhavaOrder))
            {{-- Chadahva Order --}}
            <div class="tab-pane fade" id="chadhava" role="tabpanel" aria-labelledby="chadhava-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue"
                                                    class="form-control" placeholder="{{ translate('search_orders') }}"
                                                    aria-label="Search orders" value="{{ request('searchValue') }}"
                                                    required>
                                                <button type="submit"
                                                    class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ChadhavaOrder as $key => $corder)
                                        <tr>
                                            <td>{{ $orders->firstItem() + $key }}</td>
                                            <td>
                                                <a href="{{ route('admin.chadhava.order.details', [$corder['id']]) }}"
                                                    data-addedby="{{ $corder['added_by'] }}"
                                                    data-id="{{ $corder['id'] }}"
                                                    class="title-color hover-c1">{{ $corder['order_id'] }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{$corder['type']}}
                                            </td>
                                            <td> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $corder['pay_amount']??0)) }}
                                            </td>
                                            <td>
                                                <span
                                                    class="fz-12 badge badge-soft-{{ $corder->status == 0
                                                    ? 'primary'
                                                    : ($corder->status == 1
                                                    ? 'success'
                                                    : ($corder->status == 2
                                                    ? 'danger'
                                                    : ($corder->status == 3
                                                    ? 'dark'
                                                    : ($corder->status == 4
                                                    ? 'info'
                                                    : ($corder->status == 5
                                                    ? 'secondary'
                                                    : ($corder->status == 6
                                                    ? 'warning'
                                                    : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                    {{ $corder->status == 0
                                                    ? 'Pending'
                                                    : ($corder->status == 1
                                                    ? 'Completed'
                                                    : ($corder->status == 2
                                                    ? 'Canceled'
                                                    : ($corder->status == 3
                                                    ? 'Schedule Time'
                                                    : ($corder->status == 4
                                                    ? 'Live Stream'
                                                    : ($corder->status == 5
                                                    ? 'Video Share'
                                                    : ($corder->status == 6
                                                    ? 'Rejected'
                                                    : 'Warning')))))) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.chadhava.order.details', [$corder['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.chadhava.order.generate.invoice', $corder['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (count($ChadhavaOrder) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                                <div class="card-footer">
                                    {!! $ChadhavaOrder->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (!empty($counsellingOrder))
            {{-- Chadahva Order --}}
            <div class="tab-pane fade" id="counselling" role="tabpanel" aria-labelledby="counselling-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue"
                                                    class="form-control" placeholder="{{ translate('search_orders') }}"
                                                    aria-label="Search orders" value="{{ request('searchValue') }}"
                                                    required>
                                                <button type="submit"
                                                    class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($counsellingOrder as $key => $cusorder)
                                        <tr>
                                            <td>{{ $orders->firstItem() + $key }}</td>
                                            <td>
                                                <a href="{{ route('admin.counselling.order.details', [$cusorder['id']]) }}"
                                                    data-addedby="{{ $cusorder['added_by'] }}"
                                                    data-id="{{ $cusorder['id'] }}"
                                                    class="title-color hover-c1">{{ $cusorder['order_id'] }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{$cusorder['type']}}
                                            </td>
                                            <td> {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $cusorder['pay_amount']??0)) }}
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-{{ $cusorder->status == 0 ? 'primary' : ($cusorder->status == 1 ? 'success' : 'danger') }}">{{ $cusorder->status == 0 ? 'Pending' : ($cusorder->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn"
                                                        title="{{ translate('view') }}"
                                                        href="{{ route('admin.counselling.order.details', [$cusorder['id']]) }}"><i
                                                            class="tio-invisible"></i> </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ translate('invoice') }}" target="_blank"
                                                        href="{{ route('admin.counselling.order.generate.invoice', $cusorder['id']) }}"><i
                                                            class="tio-download"></i> </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (count($counsellingOrder) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                                <div class="card-footer">
                                    {!! $counsellingOrder->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="tab-pane fade" id="offline-orders" role="tabpanel" aria-labelledby="offline-orders-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_orders') }}" aria-label="Search orders" value="{{ request('searchValue') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th>{{ translate('Created_date') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tour-orders" role="tabpanel" aria-labelledby="tour-orders-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='type' value="tour">
                                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{ translate('search_orders_Id') }}" aria-label="Search orders" value="{{ ((request('type') == 'tour')? request('search') : '') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_ID') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th>{{ translate('Created_date') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($ToutOrders) > 0)
                                        @foreach($ToutOrders as $tval)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $tval['order_id'] }}</td>
                                            <td>
                                                @if($tval['part_payment'] == 'part')
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (($tval['amount']??0) + ($tval['amount']??0)))) }}
                                                @else
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $tval['amount']??0)) }}
                                                @endif
                                            </td>
                                            <td>
                                                <?php
                                                if (($tval['status'] == 0 || $tval['status'] == 1) && $tval['cab_assign'] == 0 && $tval['pickup_status'] == 0) {
                                                    $showClass = 'primary';
                                                    $showName = 'Pending';
                                                } elseif (($tval['status'] == 0 || $tval['status'] == 1) && $tval['cab_assign'] != 0 && $tval['pickup_status'] == 0) {
                                                    $showClass = 'primary';
                                                    $showName = 'Processing';
                                                } elseif (($tval['status'] == 0 || $tval['status'] == 1) && $tval['cab_assign'] != 0 && $tval['pickup_status'] == 1 && $tval['drop_status'] == 0) {
                                                    $showClass = 'success';
                                                    $showName = 'Pickup';
                                                } elseif (($tval['status'] == 0 || $tval['status'] == 1) && $tval['cab_assign'] != 0 && $tval['drop_status'] == 1) {
                                                    $showClass = 'success';
                                                    $showName = 'Completed';
                                                } else {
                                                    $showClass = 'danger';
                                                    $showName = 'Refund';
                                                }
                                                ?>
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $showClass }} fs-12 font-semibold text-capitalize">{{ $showName }}</span>
                                            </td>
                                            <td>{{ date("d M,Y h:i A",strtotime($tval['created_at'])) }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn" href="{{ route('admin.tour-visits-booking.user-booking-details',[$tval['id']]) }}">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                    <a href="{{ route('tour.tour-pdf-invoice', [$tval['id']]) }}" title="Download Tour invoice" class="btn btn-outline-info btn-sm square-btn">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="6">
                                                <div class="text-center p-4">
                                                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image_description') }}">
                                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer border-0">
                                {{ $ToutOrders->appends(['tour-page' => request('tour-page')])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="event-orders" role="tabpanel" aria-labelledby="event-orders-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='type' value="event">
                                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{ translate('search_orders_Id') }}" aria-label="Search orders" value="{{ ((request('type') == 'event')? request('search') : '') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_ID') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th>{{ translate('Created_date') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($eventOrders) > 0)
                                        @foreach($eventOrders as $eval)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $eval['order_no']}}</td>
                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $eval['amount']??0)) }}</td>
                                            <td>
                                                <?php if ($eval['transaction_status'] == 1 && $eval['status'] == 1) {
                                                    $showClass = 'badge-soft-badge-soft-primary';
                                                    $message = 'Completed';
                                                } elseif ($eval['transaction_status'] == 0 && $eval['status'] == 1) {
                                                    $showClass = 'badge-soft badge-warning';
                                                    $message = 'Pending';
                                                } elseif ($eval['transaction_status'] == 1 && $eval['status'] == 3) {
                                                    $showClass = 'badge-soft-badge-soft-danger';
                                                    $message = 'Refund';
                                                } else {
                                                    $showClass = 'badge-soft-badge-soft-danger';
                                                    $message = 'Canceled';
                                                }
                                                ?>
                                                <span class="status-badge rounded-pill __badge {{ $showClass }} fs-12 font-semibold text-capitalize ">{{ $message }}</span>
                                            </td>
                                            <td>{{ date("d M,Y h:i A",strtotime($eval['created_at'])) }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn" href="{{ route('admin.event-managment.event-booking.user-booking-details',[$eval['id']]) }}">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                    <a title="Download Event invoice" class="btn btn-outline-info btn-sm square-btn"  href="{{ route('admin.event-managment.event-booking.booking-invoice',[$eval['id']]) }}">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="6">
                                                <div class="text-center p-4">
                                                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image_description') }}">
                                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer border-0">
                                {{ $eventOrders->appends(['event-page' => request('event-page')])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="donate-orders" role="tabpanel" aria-labelledby="donate-orders-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='type' value="donate">
                                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{ translate('search_orders_Id') }}" aria-label="Search orders" value="{{ ((request('type') == 'donate')? request('search') : '') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th>{{ translate('Created_date') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($donateOrders) > 0)
                                        @foreach($donateOrders as $dval)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $dval['trans_id']}}</td>
                                            <td>{{ (($dval['type'] == 'donate_trust')?'Trust':'Ads')}}</td>
                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dval['amount']??0)) }}</td>
                                            <td><span class="status-badge rounded-pill __badge fs-12 font-semibold text-capitalize badge-soft-badge-soft-{{ $dval['amount_status'] == 1 ? 'success' : 'danger' }}">{{ $dval['amount_status'] == 1 ? 'Success' : 'Pending' }}</span></td>
                                            <td>{{ date("d M,Y h:i A",strtotime($dval['created_at'])) }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-outline--primary btn-sm edit square-btn" title="{{ translate('view') }}" href="{{ route('admin.donate_management.donated.view',['id'=>$dval['id']])}}">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                    <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('invoice') }}" target="_blank" href="{{ route('donate-create-pdf-invoice', [$dval['id']]) }}">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    @if(!empty($dval['pan_card']))
                                                    <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('80G') }}" target="_blank" href="{{ url('api/v1/donate/twoal-a-certificate', [$dval['id']]) }}">
                                                        <i class="tio-file_text">file_text</i>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="6">
                                                <div class="text-center p-4">
                                                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image_description') }}">
                                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer border-0">
                                {{ $donateOrders->appends(['donate-page' => request('donate-page')])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="kundali-orders" role="tabpanel" aria-labelledby="kundali-orders-tab">
                <div class="row" id="printableArea">
                    <div class="col-lg-12 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="p-3">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="input-group input-group-merge input-group-custom">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="tio-search"></i>
                                                    </div>
                                                </div>
                                                <input type="hidden" name='type' value="kundali">
                                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{ translate('search_orders_Id') }}" aria-label="Search orders" value="{{ ((request('type') == 'kundali')? request('search') : '') }}" required>
                                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('order_list') }}</th>
                                            <th>{{ translate('type') }}</th>
                                            <th>{{ translate('total') }}</th>
                                            <th>{{ translate('order_Status') }}</th>
                                            <th>{{ translate('Created_date') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($kundaliOrders) > 0)
                                        @foreach($kundaliOrders as $kval)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kval['order_id']}}</td>
                                            <td>{{ ucwords(str_replace('_'," ",($kval['birthJournal']['name']??''))) }} </td>
                                            <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $kval['amount']??0)) }}</td>
                                            <td><span class="status-badge rounded-pill __badge fs-12 font-semibold text-capitalize badge-soft-badge-soft-{{ $kval['payment_status'] == 1 ? 'success' : 'danger' }}">{{ $kval['payment_status'] == 1 ? 'Success' : 'Pending' }}</span></td>
                                            <td>{{ date("d M,Y h:i A",strtotime($kval['created_at'])) }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('admin.birth_journal.order.generate-invoice', [$kval['id']]) }}"
                                                        target="_blank" title="{{ translate('invoice') }}"
                                                        class="btn btn-outline-info btn-sm square-btn">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    <a href="{{ route('admin.birth_journal.view-kundali-milan', [$kval['id']]) }}"
                                                        title="{{ translate('View_details') }}"
                                                        class="btn btn-outline--primary btn-sm edit square-btn">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                    @if ($kval['kundali_pdf'] && $kval['milan_verify'] == 1)
                                                    @if($kval['birthJournal']['name'] == 'kundali')
                                                    <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali/' . $kval['kundali_pdf']) }}"
                                                        target="_blank" title="{{ translate('download_PDF') }}"
                                                        class="btn btn-outline-success btn-sm square-btn">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    @else
                                                    <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/' . $kval['kundali_pdf']) }}"
                                                        target="_blank" title="{{ translate('download_PDF') }}"
                                                        class="btn btn-outline-success btn-sm square-btn">
                                                        <i class="tio-download"></i>
                                                    </a>
                                                    @endif
                                                    @else
                                                    <span class="status-badge text-white rounded-pill __badge badge-soft badge-warning fs-12 font-semibold text-capitalize " style="padding: 1px 9px;">progress</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="6">
                                                <div class="text-center p-4">
                                                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image_description') }}">
                                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer border-0">
                                {{ $kundaliOrders->appends(['kundali-page' => request('kundali-page')])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        let urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('type') === 'tour' || urlParams.has('tour-page')) {
            $(`.__inline-tab-show[data-target="#tour-orders"]`).click();
        } else if (urlParams.get('type') === 'event' || urlParams.has('event-page')) {
            $(`.__inline-tab-show[data-target="#event-orders"]`).click();
        } else if (urlParams.get('type') === 'donate' || urlParams.has('donate-page')) {
            $(`.__inline-tab-show[data-target="#donate-orders"]`).click();
        } else if (urlParams.get('type') === 'kundali' || urlParams.has('kundali-page')) {
            $(`.__inline-tab-show[data-target="#kundali-orders"]`).click();
        }
    });
</script>
@endpush