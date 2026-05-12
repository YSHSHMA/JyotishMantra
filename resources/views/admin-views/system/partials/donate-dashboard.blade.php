<div class="content container-fluid">
    <div class="page-header pb-0 mb-0 border-0">
        <div class="flex-between align-items-center">
            <div class="mb-2">
                <h1 class="page-header-title">{{ translate('welcome_to_Donation_Dashboard') }}</h1>
            </div>
        </div>
    </div>

    <div class="card mb-2 remove-card-shadow">
        <div class="card-body">
            <div class="row flex-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/business_analytics.png') }}"
                            alt="">{{ translate('business_analytics') }}
                    </h4>
                </div>
            </div>
            <div class="row g-2" id="order_stats">
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Trust') }}</h5>
                        <h2 class="business-analytics__title">{{ $doneteData['totalTrust'] }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/total-trust.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('trust_Ads') }}</h5>
                        <h2 class="business-analytics__title">{{ $doneteData['totalTurstAds'] }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/trust-ads.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('our_Ads') }}</h5>
                        <h2 class="business-analytics__title">{{ $doneteData['totalOurAds'] }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/our-ads.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Donaters') }}</h5>
                        <h2 class="business-analytics__title">{{ $doneteData['totalDonets'] }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/total-donaters.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('trust_Amount') }}</h5>
                        <h2 class="business-analytics__title">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $doneteData['outsideAmount']), currencyCode: getCurrencyCode()) }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/trust-amount.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('our_Amount') }}</h5>
                        <h2 class="business-analytics__title">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $doneteData['inhouseAmount']), currencyCode: getCurrencyCode()) }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/our-amount.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>


                <div class="col-sm-4">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: '/public/assets/back-end/img/delivered.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('total_Ads') }}</h6>
                        </div>
                        <span class="order-stats__title">
                           {{ $doneteData['allAds'] }}
                        </span>
                    </a>
                </div>

                <div class="col-sm-4">
                    <a class="order-stats order-stats_confirmed" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/pending.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('pending_Ads') }}</h6>
                        </div>
                        <span class="order-stats__title">
                           {{ $doneteData['pendingAds'] }}
                        </span>
                    </a>
                </div>
                <div class="col-sm-4">
                    <a class="order-stats order-stats_confirmed" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/confirmed.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('running_Ads') }}</h6>
                        </div>
                        <span class="order-stats__title">
                           {{ $doneteData['runningAds'] }}
                        </span>
                    </a>
                </div>
               
                
            </div>
        </div>
    </div>
</div>
