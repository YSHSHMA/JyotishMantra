<div class="content container-fluid">
    <div class="page-header pb-0 mb-0 border-0">
        <div class="flex-between align-items-center">
            <div class="mb-2">
                <h1 class="page-header-title">{{ translate('welcome_to_Event_Dashboard') }}</h1>
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
                <div class="col-sm-6 col-lg-3">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Organizers') }}</h5>
                        <h2 class="business-analytics__title">{{$eventData['organizers']}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/total-organizer.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('organizer_Events') }}</h5>
                        <h2 class="business-analytics__title">{{$eventData['organizerEvents']}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/organizer-event.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('our_Events') }}</h5>
                        <h2 class="business-analytics__title">{{$eventData['outEvents']}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/our-event.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Bookings') }}</h5>
                        <h2 class="business-analytics__title">{{$eventData['orders']}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/total-booking.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('organizer_Event_Amt') }}</h5>
                        <h2 class="business-analytics__title">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $eventData['organizerAmount']), currencyCode: getCurrencyCode()) }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/organizer-event-amount.png') }}" class="business-analytics__img" alt=""  width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('our_Event_Amount') }}</h5>
                        <h2 class="business-analytics__title">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $eventData['ourAmount']), currencyCode: getCurrencyCode()) }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/our-event-amount.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Commission') }}</h5>
                        <h2 class="business-analytics__title">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $eventData['commission']), currencyCode: getCurrencyCode()) }}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/dashboard/total-commission.png') }}"
                            class="business-analytics__img" alt="" width="30px">
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: '/public/assets/back-end/img/pending.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('running_Events') }}</h6>
                        </div>
                        <span class="order-stats__title">
                            {{$eventData['runningEvents']}}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_confirmed" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/confirmed.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('completed_events') }}</h6>
                        </div>
                        <span class="order-stats__title">
                            {{$eventData['completedEvents']}}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="order-stats order-stats_failed cursor-pointer" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/out-of-delivery.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('upcomming_events') }}</h6>
                        </div>
                        <span class="order-stats__title h3">{{$eventData['upcommingEvents']}}</span>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_packaging" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/failed-to-deliver.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('canceled_events') }}</h6>
                        </div>
                        <span class="order-stats__title">
                            {{$eventData['canceledEvents']}}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
