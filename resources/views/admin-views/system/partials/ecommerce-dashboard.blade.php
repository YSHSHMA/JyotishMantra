@php use App\Utils\Helpers; @endphp
@if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_check('Dashboard'))
        <div class="content container-fluid">
            <div class="page-header pb-0 mb-0 border-0">
                <div class="flex-between align-items-center">
                    <div class="mb-2">
                        {{-- <h1 class="page-header-title">{{translate('welcome').' '.auth('admin')->user()->name}}</h1> --}}
                        {{-- <p>{{ translate('monitor_your_business_analytics_and_statistics').'.'}}</p> --}}
                        <h1 class="page-header-title">{{translate('welcome_to_Ecommerce_Dashboard')}}</h1>
                    </div>
                </div>
            </div>
            
            @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Bussiness Analytics','view'))
            <div class="card mb-2 remove-card-shadow">
                <div class="card-body">
                    <div class="row flex-between align-items-center g-2 mb-3">
                        <div class="col-sm-6">
                            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/business_analytics.png')}}"
                                     alt="">{{translate('business_analytics')}}</h4>
                        </div>
                        <div class="col-sm-6 d-flex justify-content-sm-end">
                            <select class="custom-select w-auto" name="statistics_type" id="statistics_type">
                                <option
                                    value="overall" {{session()->has('statistics_type') && session('statistics_type') == 'overall'?'selected':''}}>
                                    {{ translate('overall_statistics')}}
                                </option>
                                <option
                                    value="today" {{session()->has('statistics_type') && session('statistics_type') == 'today'?'selected':''}}>
                                    {{ translate("todays_Statistics")}}
                                </option>
                                <option
                                    value="this_month" {{session()->has('statistics_type') && session('statistics_type') == 'this_month'?'selected':''}}>
                                    {{ translate("this_Months_Statistics")}}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2" id="order_stats">
                        @include('admin-views.partials._dashboard-order-stats',['data'=>$data])
                    </div>
                </div>
            </div>
            @endif

            @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Admin Wallet','view'))
            <div class="card mb-3 remove-card-shadow">
                <div class="card-body">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-3">
                        <img width="20" class="mb-1" src="{{dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png')}}"
                             alt="">
                        {{translate('admin_wallet')}}
                    </h4>

                    <div class="row g-2" id="order_stats">
                        @include('admin-views.partials._dashboard-wallet-stats',['data'=>$data])
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-1">
                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Order Statistics','view'))
                <div class="col-lg-8" id="order-statistics-div">
                    @include('admin-views.system.partials.order-statistics')
                </div>
                @endif

                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','User Overview','view'))
                <div class="col-lg-4">
                    <div class="card remove-card-shadow h-100">
                        <div class="card-header">
                            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0 ">
                                {{translate('user_overview')}}
                            </h4>
                        </div>
                        <div class="card-body justify-content-center d-flex flex-column">
                            <div>
                                <div class="position-relative">
                                    <div id="chart" class="d-flex justify-content-center"></div>
                                    <div class="total--orders">
                                        <h3>{{$data['getTotalCustomerCount']+$data['getTotalVendorCount']+$data['getTotalDeliveryManCount']}}</h3>
                                        <span>{{translate('user')}}</span>
                                    </div>
                                </div>
                                <div class="apex-legends flex-column">
                                    <div class="before-bg-017EFA">
                                        <span >{{translate('customer').' '.'('.$data['getTotalCustomerCount'].')'}} </span>
                                    </div>
                                    <div class="before-bg-51CBFF">
                                        <span class="text-capitalize">{{translate('vendor').' '.'('.$data['getTotalVendorCount'].')'}}</span>
                                    </div>
                                    <div class="before-bg-56E7E7">
                                        <span class="text-capitalize">{{translate('delivery_man').' '.'('.$data['getTotalDeliveryManCount'].')'}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Earning Statistics','view'))
                <div class="col-12">
                    <div class="card remove-card-shadow">
                        <div class="card-body">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-6">
                                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                                        <img src="{{dynamicAsset(path: 'public/assets/back-end/img/earning_statictics.png')}}"
                                             alt="">
                                        {{translate('earning_statistics')}}
                                    </h4>
                                </div>
                                <div class="col-md-6 d-flex justify-content-md-end">
                                    <ul class="option-select-btn">
                                        <li>
                                            <label class="basic-box-shadow">
                                                <input type="radio" name="statistics2" hidden="" checked="">
                                                <span data-earn-type="yearEarn" class="earning-statistics">{{translate('this_Year')}}</span>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="basic-box-shadow">
                                                <input type="radio" name="statistics2" hidden="">
                                                <span data-earn-type="MonthEarn" class="earning-statistics">{{translate('this_Month')}}</span>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="basic-box-shadow">
                                                <input type="radio" name="statistics2" hidden="">
                                                <span data-earn-type="WeekEarn" class="earning-statistics">{{translate('this_Week')}}</span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chartjs-custom mt-2" id="set-new-graph">
                                <canvas id="updatingData"
                                        data-hs-chartjs-options='{
                                            "type": "bar",
                                            "data": {
                                              "labels": ["Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                                              "datasets": [{
                                                "label": " {{translate('in-house')}} ",
                                                "data": [{{$inhouseEarningStatisticsData[1]}},{{$inhouseEarningStatisticsData[2]}},{{$inhouseEarningStatisticsData[3]}},{{$inhouseEarningStatisticsData[4]}},{{$inhouseEarningStatisticsData[5]}},{{$inhouseEarningStatisticsData[6]}},{{$inhouseEarningStatisticsData[7]}},{{$inhouseEarningStatisticsData[8]}},{{$inhouseEarningStatisticsData[9]}},{{$inhouseEarningStatisticsData[10]}},{{$inhouseEarningStatisticsData[11]}},{{$inhouseEarningStatisticsData[12]}}],
                                                "backgroundColor": "#ACDBAB",
                                                "hoverBackgroundColor": "#ACDBAB",
                                                "borderColor": "#ACDBAB"
                                              },
                                              {
                                                "label": " {{translate('vendor')}} ",
                                                "data": [{{$sellerEarningStatisticsData[1]}},{{$sellerEarningStatisticsData[2]}},{{$sellerEarningStatisticsData[3]}},{{$sellerEarningStatisticsData[4]}},{{$sellerEarningStatisticsData[5]}},{{$sellerEarningStatisticsData[6]}},{{$sellerEarningStatisticsData[7]}},{{$sellerEarningStatisticsData[8]}},{{$sellerEarningStatisticsData[9]}},{{$sellerEarningStatisticsData[10]}},{{$sellerEarningStatisticsData[11]}},{{$sellerEarningStatisticsData[12]}}],
                                                "backgroundColor": "#0177CD",
                                                "borderColor": "#0177CD"
                                              },
                                              {
                                                "label": " {{translate('commission')}} ",
                                                "data": [{{$commissionEarningStatisticsData[1]}},{{$commissionEarningStatisticsData[2]}},{{$commissionEarningStatisticsData[3]}},{{$commissionEarningStatisticsData[4]}},{{$commissionEarningStatisticsData[5]}},{{$commissionEarningStatisticsData[6]}},{{$commissionEarningStatisticsData[7]}},{{$commissionEarningStatisticsData[8]}},{{$commissionEarningStatisticsData[9]}},{{$commissionEarningStatisticsData[10]}},{{$commissionEarningStatisticsData[11]}},{{$commissionEarningStatisticsData[12]}}],
                                                "backgroundColor": "#FFB36D",
                                                "borderColor": "#FFB36D"
                                              }]
                                            },
                                            "options": {
                                                "legend": {
                                                    "display": true,
                                                    "position": "top",
                                                    "align": "center",
                                                    "labels": {
                                                        "usePointStyle": true,
                                                        "boxWidth": 8,
                                                        "fontColor": "#758590",
                                                        "fontSize": 14
                                                    }
                                                },
                                                "scales": {
                                                    "yAxes": [{
                                                    "gridLines": {
                                                        "color": "rgba(180, 208, 224, 0.5)",
                                                        "borderDash": [8, 4],
                                                        "drawBorder": false,
                                                        "zeroLineColor": "rgba(180, 208, 224, 0.5)"
                                                    },
                                                    "ticks": {
                                                        "beginAtZero": true,
                                                        "fontSize": 12,
                                                        "fontColor": "#5B6777",
                                                        "padding": 10,
                                                        "postfix": " {{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}"
                                                    }
                                                    }],
                                                    "xAxes": [{
                                                    "gridLines": {
                                                        "color": "rgba(180, 208, 224, 0.5)",
                                                        "display": true,
                                                        "drawBorder": true,
                                                        "zeroLineColor": "rgba(180, 208, 224, 0.5)"
                                                    },
                                                    "ticks": {
                                                        "fontSize": 12,
                                                        "fontColor": "#5B6777",
                                                        "fontFamily": "Open Sans, sans-serif",
                                                        "padding": 5
                                                    },
                                                    "categoryPercentage": 0.5,
                                                    "maxBarThickness": "7"
                                                    }]
                                                },
                                                "cornerRadius": 3,
                                                "tooltips": {
                                                    "prefix": " ",
                                                    "hasIndicator": true,
                                                    "mode": "index",
                                                    "intersect": false
                                                },
                                                "hover": {
                                                    "mode": "nearest",
                                                    "intersect": true
                                                }
                                            }
                                  }'></canvas>

                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Top Customer','view'))
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-customer',['top_customer'=>$data['top_customer']])
                    </div>
                </div>
                @endif

                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Most Popular Stores','view'))
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-store-by-order',['top_store_by_order_received'=>$data['top_store_by_order_received']])
                    </div>
                </div>
                @endif

                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Top Selling Store','view'))
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-selling-store',['topVendorByEarning'=>$data['topVendorByEarning']])
                    </div>
                </div>
                @endif

                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Top Selling Products','view'))
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._most-rated-products',['mostRatedProducts'=>$data['mostRatedProducts']])
                    </div>
                </div>
                @endif

                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Most Popular Product','view'))
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-selling-products',['topSellProduct'=>$data['topSellProduct']])
                    </div>
                </div>
                @endif

                @if(auth('admin')->user()->admin_role_id==1 || Helpers::modules_permission_check('Dashboard','Top Delivery Man','view'))
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 remove-card-shadow">
                        @include('admin-views.partials._top-delivery-man',['topRatedDeliveryMan'=>$data['topRatedDeliveryMan']])
                    </div>
                </div>
                @endif

            </div>
        </div>
    @else
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-12 mb-2 mb-sm-0">
                        <h3 class="text-center">{{translate('hi')}} {{auth('admin')->user()->name}}
                            , {{translate('welcome_to_dashboard')}}.</h3>
                    </div>
                </div>
            </div>
        </div>
        
    @endif