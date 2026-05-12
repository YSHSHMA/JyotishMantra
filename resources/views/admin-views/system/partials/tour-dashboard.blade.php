<div class="content container-fluid">
    <div class="page-header pb-0 mb-0 border-0">
        <div class="flex-between align-items-center">
            <div class="mb-2">
                <h1 class="page-header-title">{{ translate('welcome_to_Tour_&_Travel_Dashboard') }}</h1>
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
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Active_Agents') }}</h5>
                        <h2 class="business-analytics__title">{{ \App\Models\seller::where('type', "tour")->where('status','approved') ->count()}}
                            <small class="float-end font-weight-bolder" style="font-size: 12px; padding-top: 13px;">{{ translate('total') }} : {{ \App\Models\seller::where('type', "tour")->count()}}</small>
                        </h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-sale.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Customers') }}</h5>
                        <h2 class="business-analytics__title">{{ \App\Models\TourOrder::where('amount_status', 1)->where('refund_status', 0) ->distinct('user_id') ->count('user_id')}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-product.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Active_Tour') }}</h5>
                        <h2 class="business-analytics__title">{{ \App\Models\TourVisits::where('status', 1) ->count()}}
                            <small class="float-end font-weight-bolder" style="font-size: 12px; padding-top: 13px;">{{ translate('total') }} : {{ \App\Models\TourVisits::count()}}</small>
                        </h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-customer.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Agent_Amount') }}</h5>
                        <h2 class="business-analytics__title">
                            <?php
                            $sumAmountsAgent = \App\Models\TourAndTravel::selectRaw('SUM(wallet_amount) as total_wallet, SUM(withdrawal_amount) as total_withdrawal')
                                ->first();
                            ?>
                            {{ (($sumAmountsAgent->total_wallet ?? 0) + ($sumAmountsAgent->total_withdrawal ?? 0))}}

                        </h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-customer.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Commission') }}</h5>
                        <h2 class="business-analytics__title">{{ \App\Models\TourAndTravel::sum('admin_commission')}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-customer.png') }}" class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Goverment_Tax') }}</h5>
                        <h2 class="business-analytics__title">{{ \App\Models\TourAndTravel::sum('gst_amount')}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-customer.png') }}" class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('advance_payment') }}</h5>
                        <h2 class="business-analytics__title">{{ \App\Models\TourOrder::where('part_payment','part')->whereIn('status',[0,1])->where('amount_status',1)->sum('amount')}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-customer.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="business-analytics">
                        <h5 class="business-analytics__subtitle">{{ translate('total_Bookings') }}</h5>
                        <h2 class="business-analytics__title">{{ \App\Models\TourOrder::whereIn('status',[0,1])->where('amount_status',1)->count()}}</h2>
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/total-customer.png') }}"
                            class="business-analytics__img" alt="">
                    </div>
                </div>
            </div>
            <div class="row g-2" id="order_stats">
                <div class="col-sm-6 col-lg-4">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: '/public/assets/back-end/img/confirmed.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('city_Tour') }} <span class="text-danger">({{ \App\Models\TourVisits::where('status', 1)->where('use_date',0)->count()}})</span></h6>
                        </div>
                        <span class="order-stats__title">
                            {{ \App\Models\TourOrder::whereIn('status',[0,1])->whereHas('Tour', function ($query) {
                                $query->where('use_date', 0);
                            })->where('amount_status',1)->count()}}
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: '/public/assets/back-end/img/confirmed.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('Special_Tour(With_Date)') }} <span class="text-danger">({{ \App\Models\TourVisits::where('status', 1)->where('use_date',1)->count()}})</span></h6>
                        </div>
                        <span class="order-stats__title">
                            
                            {{ \App\Models\TourOrder::whereIn('status',[0,1])->whereHas('Tour', function ($query) {
                                $query->where('use_date', 1);
                            })->where('amount_status',1)->count()}}
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: '/public/assets/back-end/img/confirmed.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('Special_Tour(Without_Date)') }} <span class="text-danger">({{ \App\Models\TourVisits::where('status', 1)->where('use_date',4)->count()}})</span></h6>
                        </div>
                        <span class="order-stats__title">                           
                            {{ \App\Models\TourOrder::whereIn('status',[0,1])->whereHas('Tour', function ($query) {
                                $query->where('use_date', 4);
                            })->where('amount_status',1)->count()}}
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: '/public/assets/back-end/img/confirmed.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('Daily_Tour(With_Address)') }} <span class="text-danger">({{ \App\Models\TourVisits::where('status', 1)->where('use_date',2)->count()}})</span></h6>
                        </div>
                        <span class="order-stats__title">
                        {{ \App\Models\TourOrder::whereIn('status',[0,1])->whereHas('Tour', function ($query) {
                                $query->where('use_date', 2);
                            })->where('amount_status',1)->count()}}
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order-stats order-stats_pending" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: '/public/assets/back-end/img/confirmed.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('Daily_Tour(WithOut_Address)') }} 
                               <span class="text-danger"> ({{ \App\Models\TourVisits::where('status', 1)->where('use_date',3)->count()}})</span>
                            </h6>
                        </div>
                        <span class="order-stats__title">
                        {{ \App\Models\TourOrder::whereIn('status',[0,1])->whereHas('Tour', function ($query) {
                                $query->where('use_date', 3);
                            })->where('amount_status',1)->count()}}
                        </span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <a class="order-stats order-stats_packaging" href="javascript:0">
                        <div class="order-stats__content">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/packaging.png') }}"
                                alt="">
                            <h6 class="order-stats__subtitle">{{ translate('cancel_Tour') }}</h6>
                        </div>
                        <span class="order-stats__title">
                            {{ \App\Models\TourOrder::where('status',2)->count()}}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>