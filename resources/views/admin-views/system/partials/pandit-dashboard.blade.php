@php
    $panditPendingCount = $panditData['serviceOrder']->where('status', 0)->count();
    $panditCompletedCount = $panditData['serviceOrder']->where('status', 1)->count();
    $panditCanceledCount = $panditData['serviceOrder']->where('status', 2)->count();    
@endphp

{{-- customer modal --}}
<div class="modal fade" id="panditCustomerModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
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
                            @foreach ($panditData['poojaCustomers'] as $customer)
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
                <h1 class="page-header-title">{{ translate('welcome_to_Pandit_Booking_Dashboard') }}</h1>
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
                        <h2 class="business-analytics__title">{{ $panditData['services'] }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-product.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6" data-toggle="modal" data-target="#panditCustomerModal">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Customers') }}</h5>
                        <h2 class="business-analytics__title">{{ count($panditData['poojaCustomers']) }}</h2>
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
                            {{ count($panditData['serviceOrder']) }}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
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
                                    $panditData['serviceOrder']->where('status', 0)->count();
                            @endphp
                            {{ $pendingCount }}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
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
                                    $panditData['serviceOrder']->where('status', 1)->count();
                            @endphp
                            {{ $completedCount }}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
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
                                    $panditData['serviceOrder']->where('status', 2)->count();
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
                                ₹{{ $panditData['poojaWallet'] ?? 0 }}
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
                                            ₹{{ $panditData['poojaCommission'] ?? 0 }}
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
                                            ₹{{ $panditData['poojaTax'] ?? 0 }}
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
                                            ₹{{ $panditData['givenAmount'] ?? 0 }}
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
