@extends('layouts.front-end.app')
@section('title', translate('my_Order_List'))
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/social-icon.css') }}">
@endpush
@section('content')
<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
    <div class="row">
        @include('web-views.partials._profile-aside')
        <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
            <div class="card __card d-none d-lg-flex web-direction customer-profile-orders">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                        <h5 class="font-bold mb-0 fs-16">{{ translate('my_Order') }}</h5>
                    </div>
                    <ul class="nav nav-tabs nav--tabs d-flex justify-content-start mt-3 border-top border-bottom py-2"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link __inline-27 active" href="#all_order" data-toggle="tab" role="tab">
                                {{ translate('All Order') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#product_order" data-toggle="tab" role="tab">
                                {{ translate('Product Order') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#service_order" data-toggle="tab" role="tab">
                                {{ translate('Puja Order') }}
                                ({{ \App\Models\Service_order::where(['customer_id' => auth('customer')->id(), 'type' => 'pooja'])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#vip_order" data-toggle="tab" role="tab">
                                {{ translate('VIP Order') }}
                                ({{ \App\Models\Service_order::where(['customer_id' => auth('customer')->id(), 'type' => 'vip'])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#anushthan_order" data-toggle="tab" role="tab">
                                {{ translate('Anushthan Order') }}
                                ({{ \App\Models\Service_order::where(['customer_id' => auth('customer')->id(), 'type' => 'anushthan'])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#counselling_order" data-toggle="tab" role="tab">
                                {{ translate('Counselling Order') }}
                                ({{ \App\Models\Service_order::where(['customer_id' => auth('customer')->id(), 'type' => 'counselling'])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#chadhava_order" data-toggle="tab" role="tab">
                                {{ translate('Chadhava Order') }}
                                ({{ \App\Models\Chadhava_orders::where(['customer_id' => auth('customer')->id(), 'type' => 'chadhava'])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#offlinepooja_order" data-toggle="tab" role="tab">
                                {{ translate('pandit_orders') }}
                                ({{ \App\Models\OfflinepoojaOrder::where(['customer_id' => auth('customer')->id()])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#Event_order" data-toggle="tab" role="tab">
                                {{ translate('Event Order') }}
                                ({{ \App\Models\EventOrder::where(['user_id' => auth('customer')->id()])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#Donate_order" data-toggle="tab" role="tab">
                                {{ translate('Donate') }}
                                ({{ \App\Models\DonateAllTransaction::where(['user_id' => auth('customer')->id()])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#tour_order" data-toggle="tab" role="tab">
                                {{ translate('Tour_Order') }}
                                ({{ \App\Models\TourOrder::where(['user_id' => auth('customer')->id()])->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#paid_kundli_order" data-toggle="tab" role="tab">
                                {{ translate('kundli_order') }}
                                ({{ \App\Models\BirthJournalKundali::withWhereHas('birthJournal', function ($query) {
                                            $query->where('name', 'kundali');
                                        })
                                        ->where('user_id', auth('customer')->id())
                                        ->where('payment_status', 1)
                                        ->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link __inline-27" href="#paid_kundlimilan_order" data-toggle="tab" role="tab">
                                {{ translate('kundli_milan_order') }}
                                ({{ \App\Models\BirthJournalKundali::withWhereHas('birthJournal', function ($query) {
                                    $query->where('name', 'kundali_milan');
                                })
                                ->where('user_id', auth('customer')->id())
                                ->where('payment_status', 1)
                                ->count() }})
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content px-lg-3">
                        {{-- All Order  --}}
                        <div class="tab-pane fade show active text-justify" id="all_order" role="tabpanel">
                            @if ($paginatedOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_type') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('total') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paginatedOrders as $use_oo)
                                        <tr>
                                            <td>
                                                <div class="media-order">
                                                    @if ($use_oo['type'] == 'pooja')
                                                    <a href="{{ route('account-service-order-details', ['order_id' => $use_oo->order_id]) }}" class="d-block position-relative">
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $use_oo['services']['thumbnail'], type: 'pooja') }}">
                                                    </a>
                                                    @elseif($use_oo['type'] == 'counselling')
                                                    <a href="{{ route('account-counselling-order-details', ['order_id' => $use_oo->order_id]) }}" class="d-block position-relative">
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $use_oo['services']['thumbnail'], type: 'pooja') }}">
                                                    </a>
                                                    @elseif($use_oo['type'] == 'offlinepooja')
                                                    <a href="{{ route('account-offlinepooja-order-details', ['order_id' => $use_oo->order_id]) }}" class="d-block position-relative">
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/offlinepooja/thumbnail/' . $use_oo['offlinepooja']['thumbnail'], type: 'pooja') }}">
                                                    </a>
                                                    @elseif($use_oo['type'] == 'vip')
                                                    <a href="{{ route('account-vip-order-details', ['order_id' => $use_oo->order_id]) }}" class="d-block position-relative">
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $use_oo['vippoojas']['thumbnail'], type: 'vip') }}">
                                                    </a>
                                                    @elseif($use_oo['type'] == 'anushthan')
                                                    <a href="{{ route('account-anushthan-order-details', ['order_id' => $use_oo->order_id]) }}" class="d-block position-relative">
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $use_oo['vippoojas']['thumbnail'], type: 'anushthan') }}">
                                                    </a>
                                                    @elseif ($use_oo['type'] == 'chadhava')
                                                    <a href="{{ route('account-chadhava-order-details', ['order_id' => $use_oo->order_id]) }}" class="d-block position-relative">
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $use_oo['chadhava']['thumbnail'], type: 'chadhava') }}">
                                                    </a>
                                                    @elseif ($use_oo['type'] == 'shop')
                                                    <a href="{{ route('account-order-details', ['id' => $use_oo->id]) }}" class="d-block position-relative">
                                                        @if ($use_oo->seller_is == 'seller')
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/shop/' . (isset($use_oo['order']->seller->shop) ? $use_oo['order']->seller->shop->image : 'shop'), type: 'shop') }}">
                                                        @elseif($use_oo->seller_is == 'admin')
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/company/' . $web_config['fav_icon']->value, type: 'logo') }}">
                                                        @endif
                                                    </a>
                                                    @elseif($use_oo['type'] == 'event')
                                                    <a class="d-block position-relative">
                                                        <img alt="{{ translate('shop') }}" src="{{ getValidImage(path: 'storage/app/public/event/events/' . ($eventval['eventid']['event_image'] ?? ''), type: 'counselling') }}">
                                                    </a>
                                                    @elseif($use_oo['type'] == 'donate')
                                                    @if ($use_oo['type'] == 'donate_trust')
                                                    <img src="{{ getValidImage(path: 'storage/app/public/donate/trust/' . ($use_oo['getTrust']['theme_image'] ?? ''), type: 'counselling') }}" alt="{{ translate('donate') }}">
                                                    @else
                                                    <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/' . ($use_oo['adsTrust']['image'] ?? ''), type: 'counselling') }}" alt="{{ translate('donate') }}">
                                                    @endif
                                                    @elseif($use_oo['type'] == 'tour')
                                                    <a class="d-block position-relative">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($use_oo['Tour']['tour_image'] ?? ''), type: 'logo') }}" alt="{{ translate('tour') }}">
                                                    </a>
                                                    @elseif($use_oo['type'] == 'kundli' || $use_oo['type'] == 'kundli milan' )
                                                    <p class="d-block position-relative">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/birthjournal/image/' . ($use_oo['birthJournal']['image'] ?? ''), type: 'logo') }}" alt="{{ translate('tour') }}">
                                                    </p>
                                                    @endif
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            @if ($use_oo['type'] == 'pooja')
                                                            <a href="{{ route('account-service-order-details', ['order_id' => $use_oo->order_id]) }}" class="fs-14 font-semibold">
                                                                {{ translate('order') }} #{{ $use_oo['order_id'] }}
                                                            </a>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $use_oo['services']['name'] }} {{ translate('items') }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                            </div>
                                                            @elseif($use_oo['type'] == 'counselling')
                                                            <a href="{{ route('account-counselling-order-details', ['order_id' => $use_oo->order_id]) }}" class="fs-14 font-semibold">
                                                                {{ translate('order') }} #{{ $use_oo['order_id'] }}
                                                            </a>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $use_oo['services']['name'] }} {{ translate('items') }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                            </div>
                                                            @elseif($use_oo['type'] == 'offlinepooja')
                                                            <a href="{{ route('account-offlinepooja-order-details', ['order_id' => $use_oo->order_id]) }}" class="fs-14 font-semibold">
                                                                {{ translate('order') }} #{{ $use_oo['order_id'] }}
                                                            </a>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $use_oo['offlinepooja']['name'] }} {{ translate('items') }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                            </div>
                                                            @elseif($use_oo['type'] == 'vip')
                                                            <a href="{{ route('account-vip-order-details', ['order_id' => $use_oo->order_id]) }}" class="fs-14 font-semibold">
                                                                {{ translate('order') }} #{{ $use_oo['order_id'] }}
                                                            </a>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $use_oo['vippoojas']['name'] }} {{ translate('items') }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                            </div>
                                                            @elseif($use_oo['type'] == 'anushthan')
                                                            <a href="{{ route('account-anushthan-order-details', ['order_id' => $use_oo->order_id]) }}" class="fs-14 font-semibold">
                                                                {{ translate('order') }} #{{ $use_oo['order_id'] }}
                                                            </a>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $use_oo['vippoojas']['name'] }} {{ translate('items') }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                            </div>
                                                            @elseif ($use_oo['type'] == 'chadhava')
                                                            <a href="{{ route('account-chadhava-order-details', ['order_id' => $use_oo->order_id]) }}" class="fs-14 font-semibold">
                                                                {{ translate('order') }} #{{ $use_oo['order_id'] }}
                                                            </a>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $use_oo['chadhava']['name'] }} {{ translate('items') }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                            </div>
                                                            @elseif ($use_oo['type'] == 'shop')
                                                            <a href="{{ route('account-order-details', ['id' => $use_oo['id']]) }}" class="fs-14 font-semibold">
                                                                {{ translate('order') }} #{{ $use_oo['id'] }}
                                                            </a>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $use_oo['order_details_sum_qty'] }} {{ translate('items') }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                            </div>
                                                            @elseif($use_oo['type'] == 'event')
                                                            <span class="fs-14 font-semibold">
                                                                {{ translate('order') }} #{{ $use_oo['order_no'] }}
                                                            </span>
                                                            <span class="fs-12 font-weight-medium">
                                                                {{ $use_oo['eventid']['event_name'] ?? '' }}
                                                            </span>
                                                            <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                                {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                            </div>
                                                    </div>
                                                    @elseif($use_oo['type'] == 'donate')
                                                    <span class="fs-12 font-weight-medium">
                                                        @if ($use_oo['type'] == 'donate_trust')
                                                        {{ $use_oo['getTrust']['trust_name'] ?? '' }}
                                                        @else
                                                        {{ $use_oo['adsTrust']['name'] ?? '' }}
                                                        @endif
                                                    </span>
                                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                        {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                    </div>
                                                    @elseif($use_oo['type'] == 'tour')
                                                    <span class="fs-12 font-weight-medium">
                                                        {{ $use_oo['Tour']['tour_name'] ?? '' }}
                                                    </span>
                                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                        {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                    </div>

                                                    <div class="small text-start mt-2">
                                                        <span class="font-weight-medium" role="tooltip" data-title="{{ $use_oo['pickup_address'] ?? '' }}" data-toggle="tooltip">
                                                            {{ Str::limit($use_oo['pickup_address'] ?? '', 25) }}
                                                        </span>
                                                    </div>
                                                    <div class="small">
                                                        <span class="font-weight-medium">
                                                            {{ $use_oo['pickup_date'] ?? '' }}
                                                            {{ $use_oo['pickup_time'] ?? '' }}
                                                        </span>
                                                    </div>
                                                    @elseif($use_oo['type'] == 'kundli' || $use_oo['type'] == 'kundli milan')
                                                    <span class="fs-12 font-weight-medium">
                                                        {{ str_replace('_',' ',$use_oo['birthJournal']['name']?? '') }}
                                                    </span>
                                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                        {{ $use_oo['birthJournal']['type'] ?? '' }} (P. {{ $use_oo['birthJournal']['pages'] ?? '' }})
                                                    </div>
                                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                        {{ translate('language') }} : @if($use_oo['language'] == 'hi')
                                                        {{ translate('Hindi') }}
                                                        @elseif($use_oo['language'] == 'en')
                                                        {{ translate('English') }}
                                                        @elseif($use_oo['language'] == 'bn')
                                                        {{ translate('Bengali') }}
                                                        @elseif($use_oo['language'] == 'ma')
                                                        {{ translate('Marathi') }}
                                                        @elseif($use_oo['language'] == 'ml')
                                                        {{ translate('Malayalam') }}
                                                        @elseif($use_oo['language'] == 'kn')
                                                        {{ translate('Kannada') }}
                                                        @elseif($use_oo['language'] == 'te')
                                                        {{ translate('Telogu') }}
                                                        @elseif($use_oo['language'] == 'ta')
                                                        {{ translate('Tamil') }}
                                                        @endif
                                                    </div>
                                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                        {{ date('d M, Y h:i A', strtotime($use_oo['created_at'])) }}
                                                    </div>
                                                    @endif
                                                    </h6>
                                                </div>
                                            </td>
                                            <td><span class="status-badge rounded-pill __badge badge-soft-badge-soft-primary fs-12 font-semibold text-capitalize ">{{$use_oo['type']}}</span></td>
                                            <td>
                                                @if ($use_oo['type'] == 'pooja')
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $use_oo->status == 0 ? 'primary' : ($use_oo->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $use_oo->status == 0 ? 'Pending' : ($use_oo->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                                @elseif($use_oo['type'] == 'counselling')
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $use_oo->status == 0 ? 'primary' : ($use_oo->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $use_oo->status == 0 ? 'Pending' : ($use_oo->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                                @elseif($use_oo['type'] == 'offlinepooja')
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $use_oo->status == 0 ? 'primary' : ($use_oo->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $use_oo->status == 0 ? 'Pending' : ($use_oo->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                                @elseif($use_oo['type'] == 'vip')
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $use_oo->status == 0 ? 'primary' : ($use_oo->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $use_oo->status == 0 ? 'Pending' : ($use_oo->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                                @elseif($use_oo['type'] == 'anushthan')
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $use_oo->status == 0 ? 'primary' : ($use_oo->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $use_oo->status == 0 ? 'Pending' : ($use_oo->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                                @elseif($use_oo['type'] == 'chadhava')
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $use_oo->status == 0 ? 'primary' : ($use_oo->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $use_oo->status == 0 ? 'Pending' : ($use_oo->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                                @elseif($use_oo['type'] == 'shop')
                                                @if ($use_oo['order_status'] == 'failed' || $use_oo['order_status'] == 'canceled')
                                                <span class="status-badge rounded-pill __badge badge-soft-danger fs-12 font-semibold text-capitalize">
                                                    {{ translate($use_oo['order_status'] == 'failed' ? 'failed_to_deliver' : $use_oo['order_status']) }}
                                                </span>
                                                @elseif($use_oo['order_status'] == 'confirmed' || $use_oo['order_status'] == 'processing' || $use_oo['order_status'] == 'delivered')
                                                <span class="status-badge rounded-pill __badge badge-soft-success fs-12 font-semibold text-capitalize">
                                                    {{ translate($use_oo['order_status'] == 'processing' ? 'packaging' : $use_oo['order_status']) }}
                                                </span>
                                                @else
                                                <span class="status-badge rounded-pill __badge badge-soft-primary fs-12 font-semibold text-capitalize">
                                                    {{ translate($use_oo['order_status']) }}
                                                </span>
                                                @endif

                                                @elseif($use_oo['type'] == 'event')

                                                @if ($use_oo['transaction_status'] == 1 && $use_oo['status'] == 1)
                                                @php($showClass = 'badge-soft-badge-soft-primary')
                                                @php($message = 'Completed')
                                                @elseif($use_oo['transaction_status'] == 0 && $use_oo['status'] == 1)
                                                @php($showClass = 'badge-soft badge-warning')
                                                @php($message = 'Pending')
                                                @elseif($use_oo['transaction_status'] == 1 && $use_oo['status'] == 3)
                                                @php($showClass = 'badge-soft-badge-soft-danger')
                                                @php($message = 'Refund')
                                                @else
                                                @php($showClass = 'badge-soft-badge-soft-danger')
                                                @php($message = 'Canceled')
                                                @endif
                                                <span class="status-badge rounded-pill __badge {{ $showClass }} fs-12 font-semibold text-capitalize ">{{ $message }}</span>


                                                @elseif($use_oo['type'] == 'donate')
                                                <span class="status-badge rounded-pill __badge fs-12 font-semibold text-capitalize badge-soft-badge-soft-{{ $use_oo['amount_status'] == 1 ? 'success' : 'danger' }}">{{ $use_oo['amount_status'] == 1 ? 'Success' : 'Pending' }}</span>
                                                @elseif($use_oo['type'] == 'tour')
                                                <?php
                                                if (($use_oo['status'] == 0 || $use_oo['status'] == 1) && $use_oo['cab_assign'] == 0 && $use_oo['pickup_status'] == 0) {
                                                    $showClass = "primary";
                                                    $showName = "Pending";
                                                } elseif (($use_oo['status'] == 0 || $use_oo['status'] == 1) && $use_oo['cab_assign'] != 0 && $use_oo['pickup_status'] == 0) {
                                                    $showClass = "primary";
                                                    $showName = "Processing";
                                                } elseif (($use_oo['status'] == 0 || $use_oo['status'] == 1) && $use_oo['cab_assign'] != 0 && $use_oo['pickup_status'] == 1 && $use_oo['drop_status'] == 0) {
                                                    $showClass = "success";
                                                    $showName = "Pickup";
                                                } elseif (($use_oo['status'] == 0 || $use_oo['status'] == 1) && $use_oo['cab_assign'] != 0 && $use_oo['drop_status'] == 1) {
                                                    $showClass = "success";
                                                    $showName = "Completed";
                                                } else {
                                                    $showClass = "danger";
                                                    $showName = "Refund";
                                                }
                                                ?>
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $showClass }} fs-12 font-semibold text-capitalize">
                                                    {{ $showName }}
                                                </span>
                                                @elseif(($use_oo['type'] == 'kundli' || $use_oo['type'] == 'kundli milan') && $use_oo['milan_verify'] == 0)
                                                <span class="status-badge rounded-pill __badge badge-soft-success fs-12 font-semibold text-capitalize">
                                                    {{ translate('processing') }}
                                                </span>
                                                @elseif(($use_oo['type'] == 'kundli' || $use_oo['type'] == 'kundli milan') && $use_oo['milan_verify'] == 1)
                                                <span class="status-badge rounded-pill __badge badge-soft-success fs-12 font-semibold text-capitalize">
                                                    {{ translate('completed') }}
                                                </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-dark fs-13 font-bold">
                                                    @if($use_oo['type'] == 'pooja')
                                                    {{ webCurrencyConverter(amount: $use_oo['pay_amount']) }}
                                                    @elseif($use_oo['type'] == 'counselling')
                                                    {{ webCurrencyConverter(amount: $use_oo['pay_amount']) }}
                                                    @elseif($use_oo['type'] == 'offlinepooja')
                                                    {{ webCurrencyConverter(amount: $use_oo['pay_amount']) }}
                                                    @elseif($use_oo['type'] == 'vip')
                                                    {{ webCurrencyConverter(amount: $use_oo['pay_amount']) }}
                                                    @elseif($use_oo['type'] == 'anushthan')
                                                    {{ webCurrencyConverter(amount: $use_oo['pay_amount']) }}
                                                    @elseif($use_oo['type'] == 'chadhava')
                                                    {{ webCurrencyConverter(amount: $use_oo['pay_amount']) }}
                                                    @elseif($use_oo['type'] == 'shop')
                                                    {{ webCurrencyConverter(amount: $use_oo['order_amount']) }}
                                                    @elseif($use_oo['type'] == 'event')
                                                    {{ webCurrencyConverter(amount: $use_oo['amount']) }}
                                                    @elseif($use_oo['type'] == 'donate')
                                                    {{ webCurrencyConverter(amount: $use_oo['amount'] ?? 0) }}
                                                    @elseif($use_oo['type'] == 'tour')
                                                    <span>{{ webCurrencyConverter(amount: $use_oo['amount'] ?? 0) }}</span>
                                                    @if ($use_oo['part_payment'] == 'part')
                                                    <br><span class="status-badge rounded-pill __badge f-12 fot-semibold text-capitalize badge-warning"> partially </span>
                                                    @endif
                                                    @elseif($use_oo['type'] == 'kundli' || $use_oo['type'] == 'kundli milan')
                                                    {{ webCurrencyConverter(amount: $use_oo['amount'] ?? 0) }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($use_oo['type'] == 'chadhava' || $use_oo['type'] == 'pooja' || $use_oo['type'] == 'counselling' || $use_oo['type'] == 'vip' || $use_oo['type'] == 'anushthan'|| $use_oo['type'] == 'offlinepooja')
                                                <a href="{{ 
                                                    $use_oo['type'] == 'counselling' ? route('account-counselling-order-user-detail', ['order_id' => $use_oo->order_id]) :
                                                    ($use_oo['type'] == 'chadhava' ? route('account-chadhava-sankalp', ['order_id' => $use_oo->order_id]) :
                                                    ($use_oo['type'] == 'vip' ? route('account-vip-sankalp', ['order_id' => $use_oo->order_id]) :
                                                    ($use_oo['type'] == 'anushthan' ? route('account-anushthan-sankalp', ['order_id' => $use_oo->order_id]) :
                                                    ($use_oo['type'] == 'offlinepooja' ? route('account-offlinepooja-sankalp', ['order_id' => $use_oo->order_id]) :
                                                    route('account-service-sankalp', ['order_id' => $use_oo->order_id]))))) 
                                                }}"
                                                class="badge badge-primary"
                                                title="{{ translate('view_order_details') }}">
                                                    View
                                                </a>
                                                @endif
                                                @if($use_oo['type'] == 'pooja')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-service-order-details', ['order_id' => $use_oo->order_id]) }}" class="btn-outline--info text-base __action-btn btn-shadow rounded-full" title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-service', ['id' => $use_oo->id]) }}" title="{{ translate('download_invoice') }}" class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @elseif($use_oo['type'] == 'counselling')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-counselling-order-details', ['order_id' => $use_oo->order_id]) }}" class="btn-outline--info text-base __action-btn btn-shadow rounded-full" title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('consultation-generate-invoice-service', ['id' => $use_oo->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @elseif($use_oo['type'] == 'offlinepooja')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-offlinepooja-order-details', ['order_id' => $use_oo->order_id]) }}" class="btn-outline--info text-base __action-btn btn-shadow rounded-full" title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-offlinepooja', ['id' => $use_oo->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @elseif($use_oo['type'] == 'vip')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-vip-order-details', ['order_id' => $use_oo->order_id]) }}" class="btn-outline--info text-base __action-btn btn-shadow rounded-full" title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-vip', ['id' => $use_oo->id]) }}" title="{{ translate('download_invoice') }}" class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @elseif($use_oo['type'] == 'anushthan')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-anushthan-order-details', ['order_id' => $use_oo->order_id]) }}" class="btn-outline--info text-base __action-btn btn-shadow rounded-full" title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-anushthan', ['id' => $use_oo->id]) }}" title="{{ translate('download_invoice') }}" class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @elseif($use_oo['type'] == 'chadhava')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-chadhava-order-details', ['order_id' => $use_oo->order_id]) }}" class="btn-outline--info text-base __action-btn btn-shadow rounded-full" title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-chadhava', ['id' => $use_oo->id]) }}" title="{{ translate('download_invoice') }}" class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @elseif($use_oo['type'] == 'shop')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-order-details', ['id' => $use_oo->id]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice', [$use_oo->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @elseif($use_oo['type'] == 'event')
                                                @if ($use_oo['transaction_status'] == 1 && $use_oo['status'] == 1)
                                                <a href="{{ route('event-order-details', [$use_oo['id']]) }}"
                                                    class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                    title="{{ translate('view_Event_Order_details') }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @endif
                                                <a href="{{ route('event-create-pdf-invoice', [$use_oo['id']]) }}"
                                                    title="{{ translate('download_invoice') }}"
                                                    class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="tio-download-to"></i>
                                                </a>
                                                @elseif($use_oo['type'] == 'donate')
                                                <a href="{{ route('donate-create-pdf-invoice', [$use_oo['id']]) }}"
                                                    title="{{ translate('download_donate_invoice') }}"
                                                    class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="tio-download-to"></i>
                                                </a>
                                                @elseif($use_oo['type'] == 'tour')
                                                <a href="{{ route('tour.view-details', [$use_oo['id']]) }}" title="view" class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('tour.tour-pdf-invoice', [$use_oo['id']]) }}" title="Download Tour invoice" class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="tio-download-to"></i>
                                                </a>
                                                @elseif($use_oo['type'] == 'kundli' || $use_oo['type'] == 'kundli milan')
                                                 @if ($use_oo['kundali_pdf'] && $use_oo['milan_verify'] == 1 && $use_oo['type'] == 'kundli')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali/' . $use_oo['kundali_pdf']) }}"
                                                        target="_blank" title="{{ translate('download_PDF') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @endif
                                                @if ($use_oo['kundali_pdf'] && $use_oo['milan_verify'] == 1 && $use_oo['type'] == 'kundli milan')
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/' . $use_oo['kundali_pdf']) }}"
                                                        target="_blank" title="{{ translate('download_PDF') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @endif

                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('saved.paid-kundali-milan.show', [$use_oo['id']]) }}"
                                                        title="{{ translate('View_details') }}"
                                                        class="btn-outline-info text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_order_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $paginatedOrders->appends(['all_order' => request('all_order')])->links() }}
                            </div>
                        </div>
                        {{-- Product Order --}}
                        <div class="tab-pane fade  text-justify" id="product_order" role="tabpanel">
                            @if ($orders->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('total') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                        <tr>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <a href="{{ route('account-order-details', ['id' => $order->id]) }}"
                                                        class="d-block position-relative">
                                                        @if ($order->seller_is == 'seller')
                                                        <img alt="{{ translate('shop') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/shop/' . (isset($order->seller->shop) ? $order->seller->shop->image : 'shop'), type: 'shop') }}">
                                                        @elseif($order->seller_is == 'admin')
                                                        <img alt="{{ translate('shop') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/company/' . $web_config['fav_icon']->value, type: 'logo') }}">
                                                        @endif
                                                    </a>
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            <a href="{{ route('account-order-details', ['id' => $order->id]) }}"
                                                                class="fs-14 font-semibold">
                                                                {{ translate('order') }}
                                                                #{{ $order['id'] }}
                                                            </a>
                                                        </h6>
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $order->order_details_sum_qty }}
                                                            {{ translate('items') }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($order['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                @if ($order['order_status'] == 'failed' || $order['order_status'] == 'canceled')
                                                <span
                                                    class="status-badge rounded-pill __badge badge-soft-danger fs-12 font-semibold text-capitalize">
                                                    {{ translate($order['order_status'] == 'failed' ? 'failed_to_deliver' : $order['order_status']) }}
                                                </span>
                                                @elseif(
                                                $order['order_status'] == 'confirmed' ||
                                                $order['order_status'] == 'processing' ||
                                                $order['order_status'] == 'delivered')
                                                <span
                                                    class="status-badge rounded-pill __badge badge-soft-success fs-12 font-semibold text-capitalize">
                                                    {{ translate($order['order_status'] == 'processing' ? 'packaging' : $order['order_status']) }}
                                                </span>
                                                @else
                                                <span
                                                    class="status-badge rounded-pill __badge badge-soft-primary fs-12 font-semibold text-capitalize">
                                                    {{ translate($order['order_status']) }}
                                                </span>
                                                @endif
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $order['order_amount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-order-details', ['id' => $order->id]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice', [$order->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_product_order_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $orders->links() }}
                            </div>
                        </div>
                        <!-- Service Order -->
                        <div class="tab-pane fade  text-justify" id="service_order" role="tabpanel">
                            @if ($serviceOrder->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-center text-capitalize">
                                                        {{ translate('user_detail') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('total') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($serviceOrder as $serviceorder)
                                        <tr>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <a href="{{ route('account-service-order-details', ['order_id' => $serviceorder->order_id]) }}"
                                                        class="d-block position-relative"><img
                                                            alt="{{ translate('shop') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $serviceorder['services']['thumbnail'], type: 'pooja') }}"></a>
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            <a href="{{ route('account-service-order-details', ['order_id' => $serviceorder->order_id]) }}"
                                                                class="fs-14 font-semibold">
                                                                {{ translate('order') }}
                                                                #{{ $serviceorder['order_id'] }}
                                                            </a>
                                                        </h6>
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $serviceorder['services']['name'] }}
                                                            {{ translate('items') }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($serviceorder['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                @if ($serviceorder->is_edited == 1)
                                                <a href="{{ route('account-service-sankalp', ['order_id' => $serviceorder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    View Details
                                                </a>
                                                @else
                                                <a href="{{ route('account-service-sankalp', ['order_id' => $serviceorder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    Edit Details
                                                </a>
                                                @endif
                                            </td>
                                            <td class="bodytr">
                                                <span  class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $serviceorder->order_status == 0
                                                            ? 'primary'
                                                            : ($serviceorder->order_status == 1
                                                                ? 'success'
                                                                : ($serviceorder->order_status == 12
                                                                ? 'warning'
                                                                : ($serviceorder->order_status == 3
                                                                    ? 'danger'
                                                                    : ($serviceorder->order_status == 6
                                                                        ? 'warning'
                                                                        : ($serviceorder->order_status == 4
                                                                            ? 'info'
                                                                            : ($serviceorder->order_status == 5
                                                                                ? 'secondary'
                                                                                : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                        {{ $serviceorder->order_status == 0
                                                            ? 'Pending'
                                                            : ($serviceorder->order_status == 1
                                                                ? 'Completed'
                                                                : ($serviceorder->order_status == 2
                                                                    ? 'Canceled'
                                                                    : ($serviceorder->order_status == 3
                                                                        ? 'Scheduled'
                                                                        : ($serviceorder->order_status == 4
                                                                            ? 'Live Stream'
                                                                            : ($serviceorder->order_status == 5
                                                                                ? 'Video Share'
                                                                                : ($serviceorder->order_status == 6
                                                                                    ? 'Rejected'
                                                                                    : 'Unknown')))))) }}</span>
                                            
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $serviceorder['pay_amount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    @if ($serviceorder->is_edited == 0)
                                                    <a href="{{ route('account-service-sankalp', ['order_id' => $serviceorder->order_id]) }}"
                                                        class="btn btn-outline--danger text-danger __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @else
                                                    <a href="{{ route('account-service-sankalp', ['order_id' => $serviceorder->order_id]) }}"
                                                        class="btn btn-outline--success text-success __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-list"></i>
                                                    </a>
                                                    @endif
                                                    <a href="{{ route('account-service-order-details', ['order_id' => $serviceorder->order_id]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-service', ['id' => $serviceorder->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_pooja_order_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $serviceOrder->appends(['pooja-page' => request('pooja-page')])->links() }}
                            </div>
                        </div>
                        <!-- VIP POOJA Order -->
                        <div class="tab-pane fade  text-justify" id="vip_order" role="tabpanel">
                            @if ($vipOrder->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-center text-capitalize">
                                                        {{ translate('user_detail') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('total') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vipOrder as $viporder)
                                        <tr>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <a href="{{ route('account-vip-order-details', ['order_id' => $viporder->order_id]) }}"
                                                        class="d-block position-relative">
                                                        <img alt="{{ translate('vippoojas') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $viporder['vippoojas']['thumbnail']) }}"></a>
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            <a href="{{ route('account-vip-order-details', ['order_id' => $viporder->order_id]) }}"
                                                                class="fs-14 font-semibold">
                                                                {{ translate('order') }}
                                                                #{{ $viporder['order_id'] }}
                                                            </a>
                                                        </h6>
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $viporder['vippoojas']['name'] }}
                                                            {{ translate('items') }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($viporder['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                @if ($viporder->is_edited == 1)
                                                <a href="{{ route('account-vip-sankalp', ['order_id' => $viporder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    View Details
                                                </a>
                                                @else
                                                <a href="{{ route('account-vip-sankalp', ['order_id' => $viporder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    Edit Details
                                                </a>
                                                @endif
                                            </td>
                                            <td class="bodytr">
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $viporder->order_status == 0
                                                            ? 'primary'
                                                            : ($viporder->order_status == 1
                                                                ? 'success'
                                                                : ($viporder->order_status == 12
                                                                ? 'warning'
                                                                : ($viporder->order_status == 3
                                                                    ? 'danger'
                                                                    : ($viporder->order_status == 6
                                                                        ? 'warning'
                                                                        : ($viporder->order_status == 4
                                                                            ? 'info'
                                                                            : ($viporder->order_status == 5
                                                                                ? 'secondary'
                                                                                : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                        {{ $viporder->order_status == 0
                                                            ? 'Pending'
                                                            : ($viporder->order_status == 1
                                                                ? 'Completed'
                                                                : ($viporder->order_status == 2
                                                                    ? 'Canceled'
                                                                    : ($viporder->order_status == 3
                                                                        ? 'Scheduled'
                                                                        : ($viporder->order_status == 4
                                                                            ? 'Live Stream'
                                                                            : ($viporder->order_status == 5
                                                                                ? 'Video Share'
                                                                                : ($viporder->order_status == 6
                                                                                    ? 'Rejected'
                                                                                    : 'Unknown')))))) }}</span>
                                               
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $viporder['pay_amount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-vip-order-details', ['order_id' => $viporder->order_id]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-vip', ['id' => $viporder->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                   
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_pooja_order_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $vipOrder->appends(['vip-page' => request('vip-page')])->links() }}
                            </div>
                        </div>
                        <div class="tab-pane fade  text-justify" id="offlinepooja_order" role="tabpanel">
                            @if ($offlinepoojaOrder->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-center text-capitalize">
                                                        {{ translate('user_detail') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('pooja_Amount') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('paid_Amount') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($offlinepoojaOrder as $poojaOrder)
                                        <tr>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <a href="{{ route('account-offlinepooja-order-details', ['order_id' => $poojaOrder->order_id]) }}"
                                                        class="d-block position-relative">
                                                        <img alt="{{ translate('pandit_orders') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/offlinepooja/thumbnail/' . $poojaOrder['offlinePooja']['thumbnail']) }}"></a>
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            <a href="{{ route('account-offlinepooja-order-details', ['order_id' => $poojaOrder->order_id]) }}"
                                                                class="fs-14 font-semibold">
                                                                {{ translate('order') }}
                                                                #{{ $poojaOrder['order_id'] }}
                                                            </a>
                                                        </h6>
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $poojaOrder['offlinePooja']['name'] }}
                                                            {{ translate('items') }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($poojaOrder['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                @if ($poojaOrder->is_edited == 1)
                                                <a href="{{ route('account-offlinepooja-sankalp', ['order_id' => $poojaOrder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    View Details
                                                </a>
                                                @else
                                                <a href="{{ route('account-offlinepooja-sankalp', ['order_id' => $poojaOrder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    Edit Details
                                                </a>
                                                @endif
                                            </td>
                                            <td class="bodytr">
                                                <span
                                                    class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $poojaOrder->status == 0 ? 'primary' : ($poojaOrder->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $poojaOrder->status == 0 ? 'Pending' : ($poojaOrder->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                                {{-- <span class="badge badge-{{ $viporder['status'] == 0 ? 'primary' : ($viporder['status'] == 1 ? 'success' : 'danger') }} font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{ $viporder['status'] == 0 ? 'Pending' : ($viporder['status'] == 1 ? 'Completed' : 'Canceled') }}</span> --}}
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $poojaOrder['package_main_price']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $poojaOrder['pay_amount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-offlinepooja-order-details', ['order_id' => $poojaOrder->order_id]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-offlinepooja', ['id' => $poojaOrder->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                    {{-- <a href="#"
                                                            id="shareButton-{{ $poojaOrder['id'] }}"
                                                    title="{{ translate('share_options') }}"
                                                    class="btn btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="fa fa-share"></i>
                                                    </a> --}}
                                                    {{-- <a href="{{ route('generate-invoice-service', ['id' =>$viporder->id]) }}" title="{{ translate('download_invoice') }}"
                                                    class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="tio-download-to"></i>
                                                    </a> --}}

                                                </div>

                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_pandit_orders_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $offlinepoojaOrder->links() }}
                            </div>
                        </div>
                        <!-- ANUSHTHAN POOJA Order -->
                        <div class="tab-pane fade  text-justify" id="anushthan_order" role="tabpanel">
                            @if ($anushthanOrder->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-center text-capitalize">
                                                        {{ translate('user_detail') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('total') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($anushthanOrder as $anushthanorder)
                                        <tr>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <a href="{{ route('account-service-order-details', ['order_id' => $anushthanorder->order_id]) }}"
                                                        class="d-block position-relative">
                                                        <img alt="{{ translate('shop') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $anushthanorder['vippoojas']['thumbnail']) }}"></a>
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            <a href="{{ route('account-service-order-details', ['order_id' => $anushthanorder->order_id]) }}"
                                                                class="fs-14 font-semibold">
                                                                {{ translate('order') }}
                                                                #{{ $anushthanorder['order_id'] }}
                                                            </a>
                                                        </h6>
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $anushthanorder['vippoojas']['name'] }}
                                                            {{ translate('items') }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($anushthanorder['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                @if ($anushthanorder->is_edited == 1)
                                                <a href="{{ route('account-anushthan-sankalp', ['order_id' => $anushthanorder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    View Details
                                                </a>
                                                @else
                                                <a href="{{ route('account-anushthan-sankalp', ['order_id' => $anushthanorder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    Edit Details
                                                </a>
                                                @endif
                                            </td>
                                            <td class="bodytr">
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $anushthanorder->order_status == 0
                                                            ? 'primary'
                                                            : ($anushthanorder->order_status == 1
                                                                ? 'success'
                                                                : ($anushthanorder->order_status == 2
                                                                ? 'warning'
                                                                : ($anushthanorder->order_status == 3
                                                                    ? 'danger'
                                                                    : ($anushthanorder->order_status == 6
                                                                        ? 'warning'
                                                                        : ($anushthanorder->order_status == 4
                                                                            ? 'info'
                                                                            : ($anushthanorder->order_status == 5
                                                                                ? 'secondary'
                                                                                : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                        {{ $anushthanorder->order_status == 0
                                                            ? 'Pending'
                                                            : ($anushthanorder->order_status == 1
                                                                ? 'Completed'
                                                                : ($anushthanorder->order_status == 2
                                                                    ? 'Canceled'
                                                                    : ($anushthanorder->order_status == 3
                                                                        ? 'Scheduled'
                                                                        : ($anushthanorder->order_status == 4
                                                                            ? 'Live Stream'
                                                                            : ($anushthanorder->order_status == 5
                                                                                ? 'Video Share'
                                                                                : ($anushthanorder->order_status == 6
                                                                                    ? 'Rejected'
                                                                                    : 'Unknown')))))) }}</span>
                                                
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $anushthanorder['pay_amount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-anushthan-order-details', ['order_id' => $anushthanorder->order_id]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-anushthan', ['id' => $anushthanorder->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                   
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_pooja_order_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $anushthanOrder->appends(['anushthan-page' => request('anushthan-page')])->links() }}
                            </div>
                        </div>
                        <!-- Counselling Order -->
                        <div class="tab-pane fade  text-justify" id="counselling_order" role="tabpanel">
                            @if ($counsellingOrder->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('total') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($counsellingOrder as $counsellingorder)
                                        @if (Str::contains($counsellingorder['order_id'], 'CL'))
                                        <tr>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <p class="d-block position-relative"><img
                                                        alt="{{ translate('shop') }}"
                                                        src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $counsellingorder['services']['thumbnail'], type: 'counselling') }}">
                                                </p>
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            <span class="fs-14 font-semibold">
                                                                {{ translate('order') }}
                                                                #{{ $counsellingorder['order_id'] }}
                                                        </h6>
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $counsellingorder['services']['name'] }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($counsellingorder['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <span
                                                    class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $counsellingorder->status == 0 ? 'primary' : ($counsellingorder->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $counsellingorder->status == 0 ? 'Pending' : ($counsellingorder->status == 1 ? 'Completed' : 'Canceled') }}</span>
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $counsellingorder['pay_amount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <a href="{{ route('account-counselling-order-user-detail', ['order_id' => $counsellingorder->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('edit') }}">
                                                    View
                                                </a>
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('account-counselling-order-details', ['order_id' => $counsellingorder->order_id]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('consultation-generate-invoice-service', ['id' => $counsellingorder->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_counselling_order_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $counsellingOrder->links() }}
                            </div>
                        </div>
                        {{-- Chadhava Order --}}
                        <div class="tab-pane fade  text-justify" id="chadhava_order" role="tabpanel">
                            @if ($ChadhavaOrder->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('order_type') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('total') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ChadhavaOrder as $chadhava)
                                        <tr>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <a href="{{ route('account-chadhava-order-details', ['order_id' => $chadhava->order_id]) }}"
                                                        class="d-block position-relative"><img
                                                            alt="{{ translate('shop') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $chadhava['chadhava']['thumbnail'], type: 'pooja') }}"></a>
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            <a href="{{ route('account-service-order-details', ['order_id' => $chadhava->order_id]) }}"
                                                                class="fs-14 font-semibold">
                                                                {{ translate('order') }}
                                                                #{{ $chadhava['order_id'] }}
                                                            </a>
                                                        </h6>
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $chadhava['chadhava']['name'] }}
                                                            {{ translate('items') }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($chadhava['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                @if ($chadhava->is_edited == 1)
                                                <a href="{{ route('account-chadhava-sankalp', ['order_id' => $chadhava->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    View Details
                                                </a>
                                                @else
                                                <a href="{{ route('account-chadhava-sankalp', ['order_id' => $chadhava->order_id]) }}"
                                                    class="badge badge-primary"
                                                    title="{{ translate('view_order_details') }}">
                                                    Edit Details
                                                </a>
                                                @endif
                                            </td>
                                            <td class="bodytr">
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $chadhava->order_status == 0
                                                            ? 'primary'
                                                            : ($chadhava->order_status == 1
                                                                ? 'success'
                                                                : ($chadhava->order_status == 2
                                                                ? 'warning'
                                                                : ($chadhava->order_status == 3
                                                                    ? 'danger'
                                                                    : ($chadhava->order_status == 6
                                                                        ? 'warning'
                                                                        : ($chadhava->order_status == 4
                                                                            ? 'info'
                                                                            : ($chadhava->order_status == 5
                                                                                ? 'secondary'
                                                                                : 'dark')))))) }} fs-12 font-semibold text-capitalize">
                                                        {{ $chadhava->order_status == 0
                                                            ? 'Pending'
                                                            : ($chadhava->order_status == 1
                                                                ? 'Completed'
                                                                : ($chadhava->order_status == 2
                                                                    ? 'Canceled'
                                                                    : ($chadhava->order_status == 3
                                                                        ? 'Scheduled'
                                                                        : ($chadhava->order_status == 4
                                                                            ? 'Live Stream'
                                                                            : ($chadhava->order_status == 5
                                                                                ? 'Video Share'
                                                                                : ($chadhava->order_status == 6
                                                                                    ? 'Rejected'
                                                                                    : 'Unknown')))))) }}</span>
                                            
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $chadhava['pay_amount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    @if ($chadhava->is_edited == 0)
                                                    <a href="{{ route('account-chadhava-sankalp', ['order_id' => $chadhava->order_id]) }}"
                                                        class="btn btn-outline--danger text-danger __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @else
                                                    <a href="{{ route('account-chadhava-sankalp', ['order_id' => $chadhava->order_id]) }}"
                                                        class="btn btn-outline--success text-success __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-list"></i>
                                                    </a>
                                                    @endif
                                                    <a href="{{ route('account-chadhava-order-details', ['order_id' => $chadhava->order_id]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('generate-invoice-chadhava', ['id' => $chadhava->id]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                   
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_history_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $ChadhavaOrder->appends(['chadhava-page' => request('chadhava-page')])->links() }}
                            </div>
                        </div>
                        <!-- event Order -->
                        <div class="tab-pane fade  text-justify" id="Event_order" role="tabpanel">
                            @if ($eventOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('Event_order_list') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('total') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($eventOrders as $eventval)
                                        <tr>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <p class="d-block position-relative"><img
                                                            alt="{{ translate('shop') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/event/events/' . ($eventval['eventid']['event_image'] ?? ''), type: 'counselling') }}">
                                                    </p>
                                                    <div class="cont text-start">
                                                        <h6 class="font-weight-bold m-0 mb-1">
                                                            <span class="fs-14 font-semibold">
                                                                {{ translate('order') }}
                                                                #{{ $eventval['order_no'] }}
                                                        </h6>
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $eventval['eventid']['event_name'] ?? '' }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($eventval['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                @if ($eventval['transaction_status'] == 1 && $eventval['status'] == 1)
                                                @php($showClass = 'badge-soft-badge-soft-primary')
                                                @php($message = 'Completed')
                                                @elseif($eventval['transaction_status'] == 0 && $eventval['status'] == 1)
                                                @php($showClass = 'badge-soft badge-warning')
                                                @php($message = 'Pending')
                                                @elseif($eventval['transaction_status'] == 1 && $eventval['status'] == 3)
                                                @php($showClass = 'badge-soft-badge-soft-danger')
                                                @php($message = 'Refund')
                                                @else
                                                @php($showClass = 'badge-soft-badge-soft-danger')
                                                @php($message = 'Canceled')
                                                @endif
                                                <span
                                                    class="status-badge rounded-pill __badge {{ $showClass }} fs-12 font-semibold text-capitalize ">{{ $message }}</span>
                                            </td>
                                            <td class="bodytr">
                                                <div class="text-dark fs-13 font-bold">
                                                    {{ webCurrencyConverter(amount: $eventval['amount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    @if ($eventval['transaction_status'] == 1 && $eventval['status'] == 1)
                                                    <a href="{{ route('event-order-details', [$eventval['id']]) }}"
                                                        class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                                        title="{{ translate('view_Event_Order_details') }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    @endif
                                                    <a href="{{ route('event-create-pdf-invoice', [$eventval['id']]) }}"
                                                        title="{{ translate('download_invoice') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_event_order_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $eventOrders->appends(['event-page' => request('event-page')])->links() }}
                            </div>
                        </div>
                        <div class="tab-pane fade  text-justify" id="Donate_order" role="tabpanel">
                            @if ($donateOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('Sno') }} </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('name') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('amount') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($donateOrders as $donate)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <p class="d-block position-relative">
                                                        @if ($donate['type'] == 'donate_trust')
                                                        <img src="{{ getValidImage(path: 'storage/app/public/donate/trust/' . ($donate['getTrust']['theme_image'] ?? ''), type: 'counselling') }}"
                                                            alt="{{ translate('donate') }}">
                                                        @else
                                                        <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/' . ($donate['adsTrust']['image'] ?? ''), type: 'counselling') }}"
                                                            alt="{{ translate('donate') }}">
                                                        @endif
                                                    </p>
                                                    <div class="cont text-start">
                                                        <span class="fs-12 font-weight-medium">
                                                            @if ($donate['type'] == 'donate_trust')
                                                            {{ $donate['getTrust']['trust_name'] ?? '' }}
                                                            @else
                                                            {{ $donate['adsTrust']['name'] ?? '' }}
                                                            @endif
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($donate['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td> {{ webCurrencyConverter(amount: $donate['amount'] ?? 0) }}
                                            </td>
                                            <td> <span
                                                    class="status-badge rounded-pill __badge fs-12 font-semibold text-capitalize badge-soft-badge-soft-{{ $donate['amount_status'] == 1 ? 'success' : 'danger' }}">{{ $donate['amount_status'] == 1 ? 'Success' : 'Pending' }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('donate-create-pdf-invoice', [$donate['id']]) }}"
                                                    title="{{ translate('download_donate_invoice') }}"
                                                    class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="tio-download-to"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('no_donate_order_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $donateOrders->appends(['donate-page' => request('donate-page')])->links() }}
                            </div>
                        </div>
                        <div class="tab-pane fade  text-justify" id="tour_order" role="tabpanel">
                            @if ($tourOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('Sno') }} </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('tour_name') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('pickup_info') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('amount') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('status') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tourOrders as $donate)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <p class="d-block position-relative">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($donate['Tour']['tour_image'] ?? ''), type: 'logo') }}"
                                                            alt="{{ translate('tour') }}">
                                                    </p>
                                                    <div class="cont text-start">
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ $donate['Tour']['tour_name'] ?? '' }}
                                                        </span>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ $donate['qty'] }} {{ translate('pepole') }}
                                                        </div>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($donate['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cont text-start">
                                                    <span class="font-weight-medium" role="tooltip"
                                                        data-title="{{ $donate['pickup_address'] ?? '' }}"
                                                        data-toggle="tooltip">
                                                        {{ Str::limit($donate['pickup_address'] ?? '', 25) }}
                                                    </span>
                                                </div>
                                                <div class="cont">
                                                    <span class="font-weight-medium">
                                                        {{ $donate['pickup_date'] ?? '' }}
                                                        {{ $donate['pickup_time'] ?? '' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td> <span>{{ webCurrencyConverter(amount: $donate['amount'] ?? 0) }}</span>
                                                @if ($donate['part_payment'] == 'part')
                                                <br><span
                                                    class="status-badge rounded-pill __badge f-12 fot-semibold text-capitalize badge-warning">
                                                    partially </span>
                                                @endif
                                            </td>
                                            <td>
                                                <?php
                                                if (($donate['status'] == 0 || $donate['status'] == 1) && $donate['cab_assign'] == 0 && $donate['pickup_status'] == 0) {
                                                    $showClass = "primary";
                                                    $showName = "Pending";
                                                } elseif (($donate['status'] == 0 || $donate['status'] == 1) && $donate['cab_assign'] != 0 && $donate['pickup_status'] == 0) {
                                                    $showClass = "primary";
                                                    $showName = "Processing";
                                                } elseif (($donate['status'] == 0 || $donate['status'] == 1) && $donate['cab_assign'] != 0 && $donate['pickup_status'] == 1 && $donate['drop_status'] == 0) {
                                                    $showClass = "success";
                                                    $showName = "Pickup";
                                                } elseif (($donate['status'] == 0 || $donate['status'] == 1) && $donate['cab_assign'] != 0 && $donate['drop_status'] == 1) {
                                                    $showClass = "success";
                                                    $showName = "Completed";
                                                } else {
                                                    $showClass = "danger";
                                                    $showName = "Refund";
                                                }
                                                ?>
                                                <span class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $showClass }} fs-12 font-semibold text-capitalize">
                                                    {{ $showName }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('tour.view-details', [$donate['id']]) }}"
                                                    title="view"
                                                    class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('tour.tour-pdf-invoice', [$donate['id']]) }}"
                                                    title="Download Tour invoice"
                                                    class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                    <i class="tio-download-to"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('Recode_not_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $tourOrders->appends(['tour-page' => request('tour-page')])->links() }}
                            </div>
                        </div>
                        <div class="tab-pane fade  text-justify" id="paid_kundli_order" role="tabpanel">
                            @if ($kundalis_order->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('Sno') }} </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('kundli_Info') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('user_info') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('amount') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kundalis_order as $donate)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <p class="d-block position-relative">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/birthjournal/image/' . ($donate['birthJournal']['image'] ?? ''), type: 'logo') }}" alt="{{ translate('tour') }}">
                                                    </p>
                                                    <div class="cont text-start">
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ str_replace('_',' ',$donate['birthJournal']['name']?? '') }}
                                                        </span>
                                                        <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ $donate['birthJournal']['type'] ?? '' }} (P. {{ $donate['birthJournal']['pages'] ?? '' }})
                                                        </div>
                                                        <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ translate('language') }} : @if($donate['language'] == 'hi')
                                                            {{ translate('Hindi') }}
                                                            @elseif($donate['language'] == 'en')
                                                            {{ translate('English') }}
                                                            @elseif($donate['language'] == 'bn')
                                                            {{ translate('Bengali') }}
                                                            @elseif($donate['language'] == 'ma')
                                                            {{ translate('Marathi') }}
                                                            @elseif($donate['language'] == 'ml')
                                                            {{ translate('Malayalam') }}
                                                            @elseif($donate['language'] == 'kn')
                                                            {{ translate('Kannada') }}
                                                            @elseif($donate['language'] == 'te')
                                                            {{ translate('Telogu') }}
                                                            @elseif($donate['language'] == 'ta')
                                                            {{ translate('Tamil') }}
                                                            @endif
                                                        </div>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($donate['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cont text-start">
                                                    <span class="fs-12 font-weight-medium">
                                                        {{ ($donate['name']?? '') }}
                                                    </span>
                                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                        {{ date('d M,Y',strtotime($donate['bod']?? '')) }} {{ date('h:i A',strtotime($donate['time']?? '')) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td> <span>{{ webCurrencyConverter(amount: $donate['amount'] ?? 0) }}</span>
                                            </td>
                                            <td>
                                                @if ($donate['kundali_pdf'] && $donate['milan_verify'] == 1)
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali/' . $donate['kundali_pdf']) }}"
                                                        target="_blank" title="{{ translate('download_PDF') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @endif

                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('saved.paid-kundali-milan.show', [$donate['id']]) }}"
                                                        title="{{ translate('View_details') }}"
                                                        class="btn-outline-info text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('Recode_not_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $kundalis_order->appends(['paid-kundli-page' => request('paid-kundli-page')])->links() }}
                            </div>
                        </div>
                        <div class="tab-pane fade  text-justify" id="paid_kundlimilan_order" role="tabpanel">
                            @if ($kundali_milan_order->count() > 0)
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO text-start text-capitalize">
                                                        {{ translate('Sno') }} </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('kundli_Info') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('male_info') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('Female_info') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('amount') }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="tdBorder">
                                                <div>
                                                    <span class="d-block spandHeadO">
                                                        {{ translate('action') }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kundali_milan_order as $donate)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="bodytr">
                                                <div class="media-order">
                                                    <p class="d-block position-relative">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/birthjournal/image/' . ($donate['birthJournal']['image'] ?? ''), type: 'logo') }}" alt="{{ translate('tour') }}">
                                                    </p>
                                                    <div class="cont text-start">
                                                        <span class="fs-12 font-weight-medium">
                                                            {{ str_replace('_',' ',$donate['birthJournal']['name']?? '') }}
                                                        </span>
                                                        <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ $donate['birthJournal']['type'] ?? '' }} (P. {{ $donate['birthJournal']['pages'] ?? '' }})
                                                        </div>
                                                        <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ translate('language') }} : @if($donate['language'] == 'hi')
                                                            {{ translate('Hindi') }}
                                                            @elseif($donate['language'] == 'en')
                                                            {{ translate('English') }}
                                                            @elseif($donate['language'] == 'bn')
                                                            {{ translate('Bengali') }}
                                                            @elseif($donate['language'] == 'ma')
                                                            {{ translate('Marathi') }}
                                                            @elseif($donate['language'] == 'ml')
                                                            {{ translate('Malayalam') }}
                                                            @elseif($donate['language'] == 'kn')
                                                            {{ translate('Kannada') }}
                                                            @elseif($donate['language'] == 'te')
                                                            {{ translate('Telogu') }}
                                                            @elseif($donate['language'] == 'ta')
                                                            {{ translate('Tamil') }}
                                                            @endif
                                                        </div>
                                                        <div
                                                            class="text-secondary-50 fs-12 font-semibold mt-1">
                                                            {{ date('d M, Y h:i A', strtotime($donate['created_at'])) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cont text-start">
                                                    <span class="fs-12 font-weight-medium">
                                                        {{ ($donate['name']?? '') }}
                                                    </span>
                                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                        {{ date('d M,Y',strtotime($donate['bod']?? '')) }} {{ date('h:i A',strtotime($donate['time']?? '')) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cont text-start">
                                                    <span class="fs-12 font-weight-medium">
                                                        {{ ($donate['female_name']?? '') }}
                                                    </span>
                                                    <div class="text-secondary-50 fs-12 font-semibold mt-1">
                                                        {{ date('d M,Y',strtotime($donate['female_dob']?? '')) }} {{ date('h:i A',strtotime($donate['female_time']?? '')) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td> <span>{{ webCurrencyConverter(amount: $donate['amount'] ?? 0) }}</span>
                                            </td>
                                            <td>
                                                @if ($donate['kundali_pdf'] && $donate['milan_verify'] == 1)
                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/' . $donate['kundali_pdf']) }}"
                                                        target="_blank" title="{{ translate('download_PDF') }}"
                                                        class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-download-to"></i>
                                                    </a>
                                                </div>
                                                @else
                                                <span class="status-badge rounded-pill __badge badge-soft badge-warning fs-12 font-semibold text-capitalize " style="padding: 1px 9px;">progress</span>
                                                @endif

                                                <div class="__btn-grp-sm flex-nowrap">
                                                    <a href="{{ route('saved.paid-kundali-milan.show', [$donate['id']]) }}"
                                                        title="{{ translate('View_details') }}"
                                                        class="btn-outline-info text-success __action-btn btn-shadow rounded-full">
                                                        <i class="tio-invisible"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center pt-5 text-capitalize">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                                    alt="" width="70">
                                <h5 class="mt-1 fs-14">{{ translate('Recode_not_found') }}!</h5>
                            </div>
                            @endif
                            <div class="card-footer border-0">
                                {{ $kundali_milan_order->appends(['paid-kundlimilan-page' => request('paid-kundlimilan-page')])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white d-lg-none web-direction">
                <div class="card-body d-flex flex-column gap-3 customer-profile-orders py-0">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                        <h5 class="font-bold mb-0 fs-16">{{ translate('my_Order') }}</h5>
                        <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                viewBox="0 0 15 15" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z"
                                    fill="white" />
                            </svg>
                        </button>
                    </div>
                    @foreach ($orders as $order)
                    <div class="d-flex border-lighter rounded p-2 justify-content-between gap-2">
                        <div class="">
                            <div class="media-order">
                                <a href="{{ route('account-order-details', ['id' => $order->id]) }}"
                                    class="d-block position-relative">
                                    @if ($order->seller_is == 'seller')
                                    <img class="border-lighter" alt="{{ translate('shop') }}"
                                        src="{{ getValidImage(path: 'storage/app/public/shop/' . (isset($order->seller->shop) ? $order->seller->shop->image : 'shop'), type: 'shop') }}">
                                    @elseif($order->seller_is == 'admin')
                                    <img alt="{{ translate('shop') }}"
                                        src="{{ getValidImage(path: 'storage/app/public/company/' . $web_config['fav_icon']->value, type: 'logo') }}">
                                    @endif
                                </a>
                                <div class="cont text-start">
                                    <h6 class="font-weight-bold mb-1 fs-14">
                                        <a class="fs-12 font-semibold"
                                            href="{{ route('account-order-details', ['id' => $order->id]) }}">
                                            {{ translate('order') }} #{{ $order['id'] }}
                                        </a>
                                    </h6>
                                    <div class="d-flex flex-column gap-1 fs-12">
                                        <span
                                            class="fs-12 font-weight-normal">{{ $order->order_details_sum_qty }}
                                            {{ translate('items') }}</span>
                                        <div class="fs-11 font-semibold text-secondary-50">
                                            {{ date('d M, Y h:i A', strtotime($order['created_at'])) }}
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="text-nowrap fs-11 font-semibold text-secondary-50">
                                                {{ translate('total') }} :
                                            </div>
                                            <div class="text-dark fs-13 font-weight-bold">
                                                {{ webCurrencyConverter(amount: $order['order_amount']) }}
                                            </div>
                                        </div>
                                        <div class="my-2">
                                            @if ($order['order_status'] == 'failed' || $order['order_status'] == 'canceled')
                                            <span
                                                class="status-badge __badge badge-soft-danger border-soft-danger text-capitalize">
                                                {{ translate($order['order_status'] == 'failed' ? 'failed_to_deliver' : $order['order_status']) }}
                                            </span>
                                            @elseif(
                                            $order['order_status'] == 'confirmed' ||
                                            $order['order_status'] == 'processing' ||
                                            $order['order_status'] == 'delivered')
                                            <span
                                                class="status-badge __badge badge-soft-success border-soft-success text-capitalize">
                                                {{ translate($order['order_status'] == 'processing' ? 'packaging' : $order['order_status']) }}
                                            </span>
                                            @else
                                            <span
                                                class="status-badge __badge badge-soft-primary border-soft-primary text-capitalize">
                                                {{ translate($order['order_status']) }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="__btn-grp-sm ">
                            <a href="{{ route('account-order-details', ['id' => $order->id]) }}"
                                class="btn-outline--info text-base __action-btn btn-shadow rounded-full"
                                title="{{ translate('view_order_details') }}">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('generate-invoice', [$order->id]) }}"
                                title="{{ translate('download_invoice') }}"
                                class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                <i class="tio-download-to"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @if ($orders->count() == 0)
                    <div class="text-center pt-5 text-capitalize">
                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/sorry.svg') }}"
                            alt="" width="70">
                        <h5 class="fs-14 mt-1">{{ translate('no_order_found') }}!</h5>
                    </div>
                    @endif
                    <div class="card-footer border-0">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
<!-- jQuery Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
            @foreach($serviceOrder as $serviceorder)
            $('#shareButton-{{ $serviceorder["id"]}}').click(function(e) {
            e.preventDefault();
            $('#shareModal-{{ $serviceorder["id"]}}').modal('show');
    });
    @endforeach
    });
</script>
<script>
    $(document).ready(function() {
            @foreach($vipOrder as $viporder)
            $('#shareButton-{{ $viporder["id"]}}').click(function(e) {
            e.preventDefault();
            $('#shareModal-{{ $viporder["id"]}}').modal('show');
    });
    @endforeach
    });
</script>
<script>
    $(document).ready(function() {
            @foreach($anushthanOrder as $anushthanorder)
            $('#shareButton-{{ $anushthanorder["id"]}}').click(function(e) {
            e.preventDefault();
            $('#shareModal-{{ $anushthanorder["id"]}}').modal('show');
    });
    @endforeach
    });
</script>
<script>
    $(document).ready(function() {
            @foreach($ChadhavaOrder as $chadhava)
            $('#ChadhavashareModal-{{ $chadhava["id"]}}').click(function(e) {
            e.preventDefault();
            $('#ChadhavashareModal-{{ $chadhava["id"]}}').modal('show');
    });
    @endforeach
    });

    $(document).ready(function() {
        let urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('event-page')) {
            $(`.__inline-27[href="#Event_order"]`).click();
        } else if (urlParams.has('donate-page')) {
            $(`.__inline-27[href="#Donate_order"]`).click();
        } else if (urlParams.has('tour-page')) {
            $(`.__inline-27[href="#tour_order"]`).click();
        } else if (urlParams.has('paid-kundli-page')) {
            $(`.__inline-27[href="#paid_kundli_order"]`).click();
        } else if (urlParams.has('paid-kundlimilan-page')) {
            $(`.__inline-27[href="#paid_kundlimilan_order"]`).click();
        } else if (urlParams.has('pooja-page')) {
            $(`.__inline-27[href="#service_order"]`).click();
        } else if (urlParams.has('vip-page')) {
            $(`.__inline-27[href="#vip_order"]`).click();
        } else if (urlParams.has('anushthan-page')) {
            $(`.__inline-27[href="#anushthan_order"]`).click();
        } else if (urlParams.has('chadhava-page')) {
            $(`.__inline-27[href="#chadhava_order"]`).click();
        }
    });
</script>