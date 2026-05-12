@php
    $poojaPendingCount = $poojaData['serviceOrder']->where('type', 'pooja')->where('order_status', 0)->count();
    $consultancyPendingCount = $poojaData['serviceOrder']
        ->where('type', 'counselling')
        ->where('order_status', 0)
        ->count();
    $anushthanPendingCount = $poojaData['serviceOrder']->where('type', 'anushthan')->where('order_status', 0)->count();
    $vipPendingCount = $poojaData['serviceOrder']->where('type', 'vip')->where('order_status', 0)->count();
    $chadhavaPendingCount = $poojaData['chadhavaOrder']->where('order_status', 0)->count();
    $poojaCompletedCount = $poojaData['serviceOrder']->where('type', 'pooja')->where('order_status', 1)->count();
    $consultancyCompletedCount = $poojaData['serviceOrder']
        ->where('type', 'counselling')
        ->where('order_status', 1)
        ->count();
    $anushthanCompletedCount = $poojaData['serviceOrder']
        ->where('type', 'anushthan')
        ->where('order_status', 1)
        ->count();
    $vipCompletedCount = $poojaData['serviceOrder']->where('type', 'vip')->where('order_status', 1)->count();
    $chadhavaCompletedCount = $poojaData['chadhavaOrder']->where('order_status', 1)->count();
    $poojaCanceledCount = $poojaData['serviceOrder']->where('type', 'pooja')->where('order_status', 2)->count();
    $consultancyCanceledCount = $poojaData['serviceOrder']
        ->where('type', 'counselling')
        ->where('order_status', 2)
        ->count();
    $anushthanCanceledCount = $poojaData['serviceOrder']->where('type', 'anushthan')->where('order_status', 2)->count();
    $vipCanceledCount = $poojaData['serviceOrder']->where('type', 'vip')->where('order_status', 2)->count();
    $chadhavaCanceledCount = $poojaData['chadhavaOrder']->where('order_status', 2)->count();
@endphp

{{-- pending modal --}}
<div class="modal fade" id="pendingModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pending Orders</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Pooja</td>
                            <td>{{ $poojaPendingCount }}</td>
                        </tr>
                        <tr>
                            <td>Consultancy</td>
                            <td>{{ $consultancyPendingCount }}</td>
                        </tr>
                        <tr>
                            <td>VIP</td>
                            <td>{{ $anushthanPendingCount }}</td>
                        </tr>
                        <tr>
                            <td>Anushthan</td>
                            <td>{{ $vipPendingCount }}</td>
                        </tr>
                        <tr>
                            <td>Chadhava</td>
                            <td>{{ $chadhavaPendingCount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- completed modal --}}
<div class="modal fade" id="completedModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Completed Orders</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Pooja</td>
                            <td>{{ $poojaCompletedCount }}</td>
                        </tr>
                        <tr>
                            <td>Consultancy</td>
                            <td>{{ $consultancyCompletedCount }}</td>
                        </tr>
                        <tr>
                            <td>VIP</td>
                            <td>{{ $anushthanCompletedCount }}</td>
                        </tr>
                        <tr>
                            <td>Anushthan</td>
                            <td>{{ $vipCompletedCount }}</td>
                        </tr>
                        <tr>
                            <td>Chadhava</td>
                            <td>{{ $chadhavaCompletedCount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- canceled modal --}}
<div class="modal fade" id="canceledModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Canceled Orders</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Pooja</td>
                            <td>{{ $poojaCanceledCount }}</td>
                        </tr>
                        <tr>
                            <td>Consultancy</td>
                            <td>{{ $consultancyCanceledCount }}</td>
                        </tr>
                        <tr>
                            <td>VIP</td>
                            <td>{{ $anushthanCanceledCount }}</td>
                        </tr>
                        <tr>
                            <td>Anushthan</td>
                            <td>{{ $vipCanceledCount }}</td>
                        </tr>
                        <tr>
                            <td>Chadhava</td>
                            <td>{{ $chadhavaCanceledCount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- customer modal --}}
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="height: 300px; overflow-y: scroll">
                    <table class="table">
                        <tbody>
                            @foreach ($poojaData['poojaCustomers'] as $customer)
                                <tr>
                                    <td>{{ !empty(\App\Models\User::where('id', $customer)->first()['f_name']) ? \App\Models\User::where('id', $customer)->first()['f_name'] : '' }}
                                    </td>
                                    <td>{{ !empty(\App\Models\User::where('id', $customer)->first()['phone']) ? \App\Models\User::where('id', $customer)->first()['phone'] : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="content container-fluid">
    <div class="page-header pb-0 mb-0 border-0">
        <div class="flex-between align-items-center">
            <div class="mb-2">
                <h1 class="page-header-title">{{ translate('welcome_to_Pooja_Dashboard') }}</h1>
            </div>
        </div>
    </div>

    <div class="card mb-2 remove-card-shadow">
        <div class="card-body">
            <div class="row flex-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/business_analytics.png') }}"
                            alt="">{{ translate('business_analytics') }}
                    </h4>
                </div>
            </div>
            <div class="row g-2" id="order_stats">
                <div class="col-sm-6">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Services') }}</h5>
                        <h2 class="business-analytics__title">{{ $poojaData['services'] }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-product.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6" data-toggle="modal" data-target="#customerModal">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Customers') }}</h5>
                        <h2 class="business-analytics__title">{{ count($poojaData['poojaCustomers']) }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-customer.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/out-of-delivery.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('orders') }}</h6>
                        </div>
                        <span class="order-stats__title">
                            {{ count($poojaData['serviceOrder']) + count($poojaData['chadhavaOrder']) }}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3" data-toggle="modal" data-target="#pendingModal">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: '/public/assets/back-end/img/pending.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('pending') }}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                                $pendingCount =
                                    $poojaData['serviceOrder']->where('order_status', 0)->count() +
                                    $poojaData['chadhavaOrder']->where('order_status', 0)->count();
                            @endphp
                            {{ $pendingCount }}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3" data-toggle="modal" data-target="#completedModal">
                    <a class="order-stats order-stats_confirmed" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/confirmed.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('completed') }}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                                $completedCount =
                                    $poojaData['serviceOrder']->where('order_status', 1)->count() +
                                    $poojaData['chadhavaOrder']->where('order_status', 1)->count();
                            @endphp
                            {{ $completedCount }}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3" data-toggle="modal" data-target="#canceledModal">
                    <div class="order-stats order-stats_canceled cursor-pointer" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/canceled.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('canceled') }}</h6>
                        </div>
                        <span class="order-stats__title h3">
                            @php
                                $canceledCount =
                                    $poojaData['serviceOrder']->where('order_status', 2)->count() +
                                    $poojaData['chadhavaOrder']->where('order_status', 2)->count();
                            @endphp
                            {{ $canceledCount }}
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card mb-3 remove-card-shadow">
        <div class="card-body">
            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-3">
                <img width="20" class="mb-1"
                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}" alt="">
                {{ translate('admin_wallet') }}
            </h4>

            <div class="row g-2" id="order_stats">
                <div class="col-lg-4">
                    <div class="card h-100 d-flex justify-content-center align-items-center">
                        <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                            <img width="48" class="mb-2"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-earning.png') }}"
                                alt="">
                            <h3 class="for-card-count mb-0 fz-24">
                                {{-- {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $poojaData['poojaWallet']), currencyCode: getCurrencyCode()) }} --}}

                                ₹{{ $poojaData['poojaWallet'] ?? 0 }}
                            </h3>
                            <div class="text-capitalize mb-30">
                                {{ translate('earning') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{-- {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $poojaData['poojaCommission']), currencyCode: getCurrencyCode()) }} --}}
                                            ₹{{ $poojaData['poojaCommission'] ?? 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('commission_earned') }}</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/ce.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{-- {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $poojaData['poojaTax']), currencyCode: getCurrencyCode()) }} --}}
                                            ₹{{ $poojaData['poojaTax'] ?? 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">{{ translate('total_tax_collected') }}</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/ttc.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">
                                            {{-- {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $poojaData['poojaPandit']), currencyCode: getCurrencyCode()) }} --}}

                                            ₹{{ $poojaData['givenAmount'] ?? 0 }}
                                        </h3>
                                        <div class="text-capitalize mb-0">
                                            {{ translate('pandit_&_Astrologer_Amount') }}</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/pa.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
