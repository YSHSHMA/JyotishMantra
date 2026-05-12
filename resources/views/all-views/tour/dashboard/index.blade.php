@extends('layouts.back-end.app-tour')
@section('title', translate('dashboard'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="content container-fluid">
    <div class="page-header pb-0 border-0 mb-3">
        <div class="flex-between row align-items-center mx-1">
            <div>
                <h1 class="page-header-title text-capitalize">{{translate('welcome').' '.auth('tour')->user()->f_name.' '.auth('tour')->user()->l_name}}</h1>
                <p>{{ translate('monitor_your_business_analytics_and_statistics').'.'}}</p>
            </div>
            <div>
                @if(session()->has('device_fcm'))
                <a href="{{ route('tour-vendor.fcm-update.delete')}}" class="btn btn-primary font-weight-bolder">Reset</a>
                @else
                <form action="{{ route('tour-vendor.fcm-update.owners')}}" method="post">
                    @csrf
                    <a class="btn btn-primary font-weight-bolder" onclick="$('.fcm_type').val('owner');$('.fcm_sumbmits').click();">Owner</a>
                    <a class="btn btn-secondary font-weight-bolder" onclick="$('.fcm_type').val('driver');$('.fcm_sumbmits').click();">Driver</a>
                    <input type="hidden" name="type" value="driver" class="fcm_type">
                    <input type="hidden" name="fcm" value="" class="fcm_tokens">
                    <input type="submit" hidden class="fcm_sumbmits">
                </form>
                @endif
            </div>

        </div>
    </div>
    <div class="card mb-3 remove-card-shadow">
        <div class="card-body">
            <div class="row justify-content-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img src="{{dynamicAsset(path: 'public/assets/back-end/img/business_analytics.png')}}" alt="">
                        {{translate('order_analytics')}}
                    </h4>
                </div>

            </div>
            <div class="row g-2" id="order_stats">
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_pending" href="{{route('tour-vendor.order.pending')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/pending.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('pending')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['pending']??0}}</span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_confirmed" href="{{route('tour-vendor.order.confirm')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/tdce.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('confirmed')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['confirmed']??0}}</span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_confirmed" href="{{route('tour-vendor.order.assigned')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/tdce.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('Assigned')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['assigned']??0}}</span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_out-for-delivery" href="{{route('tour-vendor.order.pickup')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/out-of-delivery.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('pickup')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['pickup']??0}}</span>
                    </a>
                </div>


                <div class="ol-sm-6 col-lg-3">
                    <a class="order-stats order-stats_delivered" href="{{route('tour-vendor.order.complete')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/tdce.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('complete')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['complete']??0}}</span>
                    </a>
                </div>
                <div class="ol-sm-6 col-lg-3">
                    <a class="order-stats order-stats_canceled">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/canceled.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('canceled')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['canceled']??0}}</span>
                    </a>
                </div>


            </div>
        </div>
    </div>
    <div class="card mb-3 remove-card-shadow">
        <div class="card-body">
            <div class="row justify-content-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img width="20" class="mb-1" src="{{dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png')}}" alt="">
                        {{translate('tour_Wallet')}}
                    </h4>
                </div>
            </div>
            <div class="row g-2" id="order_stats">
                <div class="col-lg-4">
                    <!-- Card -->
                    <div class="card h-100 d-flex justify-content-center align-items-center">
                        <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                            <img width="48" class="mb-2" src="{{dynamicAsset(path: 'public/assets/back-end/img/withdraw.png')}}" alt="">
                            <h3 class="for-card-count mb-0 fz-24">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['totalEarning']??0), currencyCode: getCurrencyCode(type: 'default'))}}</h3>
                            <div class="font-weight-bold text-capitalize mb-30">
                                {{translate('withdrawable_balance')}}
                            </div>
                            {{--<a href="javascript:"
                                class="btn btn--primary px-4"
                                data-toggle="modal" data-target="#balance-modal">
                                {{translate('withdraw')}}
                            </a>--}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['pendingWithdraw']??0), currencyCode: getCurrencyCode(type: 'default'))}}</h3>
                                        <div class="text-capitalize mb-0">{{translate('pending_Withdraw')}}</div>
                                    </div>
                                    <div>
                                        <img width="40" class="mb-2" src="{{dynamicAsset(path: 'public/assets/back-end/img/pw.png')}}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['adminCommission']??0), currencyCode: getCurrencyCode(type: 'default'))}}</h3>
                                        <div class="text-capitalize mb-0">{{translate('total_Commission_given')}}</div>
                                    </div>
                                    <div>
                                        <img width="40" src="{{dynamicAsset(path: 'public/assets/back-end/img/tcg.png')}}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['withdrawn']??0), currencyCode: getCurrencyCode(type: 'default'))}}</h3>
                                        <div class="text-capitalize mb-0">{{translate('already_Withdrawn')}}</div>
                                    </div>
                                    <div>
                                        <img width="40" src="{{dynamicAsset(path: 'public/assets/back-end/img/aw.png')}}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body h-100 justify-content-center">
                                <div class="d-flex gap-2 justify-content-between align-items-center">
                                    <div class="d-flex flex-column align-items-start">
                                        <h3 class="mb-1 fz-24">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['collectedTotalTax']??0), currencyCode: getCurrencyCode(type: 'default'))}}</h3>
                                        <div class="text-capitalize mb-0">{{translate('total_tax_given')}}</div>
                                    </div>
                                    <div>
                                        <img width="40" src="{{dynamicAsset(path: 'public/assets/back-end/img/ttg.png')}}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="modal fade" id="balance-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{translate('withdraw_Request')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('tour-vendor.dashboard.withdraw-request')}}" method="post">
                    <div class="modal-body">
                        @csrf
                        <div class="">
                            <select class="form-control" id="withdraw_method" name="withdraw_method" required>

                                @foreach($withdrawalMethods as $method)
                                <option value="{{$method['id']}}" {{ $method['is_default'] ? 'selected':'' }}>{{$method['method_name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="" id="method-filed__div">

                        </div>

                        <div class="mt-1">
                            <label for="recipient-name" class="col-form-label fz-16">{{translate('amount')}}
                                :</label>
                            <input type="number" name="amount" step=".01"
                                value="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $dashboardData['totalEarning']??0), currencyCode: getCurrencyCode(type: 'default'))}}"
                                class="form-control" id="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{translate('close')}}</button>
                        <button type="submit"
                            class="btn btn--primary">{{translate('request')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row g-2">
        <div class="col-lg-12">
            <div class="card h-100 remove-card-shadow">
                <div class="card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-12" id="order-statistics-div">
                            @include('all-views.tour.dashboard.chart')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<span id="earning-statistics-url" data-url="{{ route('vendor.dashboard.earning-statistics') }}"></span>
<span id="withdraw-method-url" data-url="{{ route('vendor.dashboard.method-list') }}"></span>
<span id="order-status-url" data-url="{{ route('vendor.dashboard.order-status', ['type' => ':type']) }}"></span>
<span id="seller-text" data-text="{{ translate('vendor')}}"></span>
<span id="in-house-text" data-text="{{ translate('In-house')}}"></span>
<span id="customer-text" data-text="{{ translate('customer')}}"></span>
<span id="store-text" data-text="{{ translate('store')}}"></span>
<span id="product-text" data-text="{{ translate('product')}}"></span>
<span id="order-text" data-text="{{ translate('order')}}"></span>
<span id="brand-text" data-text="{{ translate('brand')}}"></span>
<span id="business-text" data-text="{{ translate('business')}}"></span>


@endsection

@push('script')
<script src="{{dynamicAsset(path: 'public/assets/back-end/vendor/chart.js/dist/Chart.min.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/apexcharts.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/vendor/chart.js.extensions/chartjs-extensions.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js')}}"></script>
@endpush

@push('script_2')
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/vendor/dashboard.js')}}"></script>
<script>
    orderStatisticsApexChart();

    function orderStatisticsApexChart() {
        let orderStatisticsData = $('#order-statistics-data');
        const inHouseOrderEarn = orderStatisticsData.data('inhouse-order-earn');
        const vendorOrderEarn = orderStatisticsData.data('vendor-order-earn');
        const label = orderStatisticsData.data('label');
        var options = {
            chart: {
                type: 'bar', // You can change to 'line' or 'area'
                height: 350
            },
            title: {
                text: 'Order Booking Per Date'
            },
            xaxis: {
                categories: vendorOrderEarn,
                title: {
                    text: 'Date'
                }
            },
            yaxis: {
                title: {
                    text: 'Amount ({{ getCurrencyCode() }})'
                }
            },
            series: [{
                name: 'Total Amount ({{ getCurrencyCode() }})',
                data: inHouseOrderEarn // Order amount per date
            }]
        };

        var chart = new ApexCharts(document.querySelector("#apex-line-chart"), options);
        chart.render();



        // var options = {
        //     series: [{
        //         name: orderStatisticsData.data('inhouse-text'),
        //         data: Object.values(inHouseOrderEarn)
        //     }],
        //     chart: {
        //         type: 'area',
        //         height: 350,
        //         zoom: {
        //             enabled: false
        //         }
        //     },
        //     dataLabels: {
        //         enabled: false
        //     },
        //     stroke: {
        //         curve: 'straight'
        //     },
        //     title: {
        //         text: '',
        //         align: 'left'
        //     },
        //     subtitle: {
        //         text: '',
        //         align: 'left'
        //     },
        //     xaxis: {
        //         type: 'datetime',
        //         labels: {
        //             format: 'yyyy-MM-dd HH:mm',
        //         }
        //     },
        //     yaxis: {
        //         opposite: true,
        //         labels: {
        //             formatter: function(value) {
        //                 return "{{ getCurrencyCode() }}" + value.toFixed(2);
        //             }
        //         }
        //     },
        //     tooltip: {
        //         y: {
        //             formatter: function(value) {
        //                 return "{{ getCurrencyCode() }}" + value.toFixed(2);
        //             }
        //         }
        //     },
        //     legend: {
        //         horizontalAlign: 'left'
        //     }
        // };

        // var chart = new ApexCharts(document.querySelector("#apex-line-chart"), options);
        // chart.render();
    }


    function orderStatistics(that) {
        let value = $(that).attr('data-date-type');
        let url = $('#order-statistics').data('action');
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                type: value
            },
            beforeSend: function() {
                $('#loading').fadeIn();
            },
            success: function(data) {
                console.log(data.view);
                $('#order-statistics-div').empty().html(data.view);
                orderStatisticsApexChart();
                // orderStatistics();
            },
            complete: function() {
                $('#loading').fadeOut();
            }
        });
    }

    $('.order-statistics').on('click', function() {
        orderStatistics();
    });






    $(".earning-statistics").on("click", function() {
        earningStatisticsUpdate(this);
    });

    function earningStatisticsUpdate(t) {
        let value = $(t).attr('data-earn-type');
        let url = $('#earning-statistics-url').data('url');

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                type: value
            },
            beforeSend: function() {
                $('#loading').fadeIn();
            },
            success: function(response_data) {
                document.getElementById("updatingData").remove();
                let graph = document.createElement('canvas');
                graph.setAttribute("id", "updatingData");
                document.getElementById("set-new-graph").appendChild(graph);

                var ctx = document.getElementById("updatingData").getContext("2d");
                var options = {
                    responsive: true,
                    bezierCurve: false,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            gridLines: {
                                color: "rgba(180, 208, 224, 0.5)",
                                zeroLineColor: "rgba(180, 208, 224, 0.5)",
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                color: "rgba(180, 208, 224, 0.5)",
                                zeroLineColor: "rgba(180, 208, 224, 0.5)",
                                borderDash: [8, 4],
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            usePointStyle: true,
                            boxWidth: 6,
                            fontColor: "#758590",
                            fontSize: 14
                        }
                    },
                    plugins: {
                        datalabels: {
                            display: false
                        }
                    },
                };
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                                label: $('#in-house-text').data('text'),
                                data: [],
                                backgroundColor: "#ACDBAB",
                                hoverBackgroundColor: "#ACDBAB",
                                borderColor: "#ACDBAB",
                                fill: false,
                                lineTension: 0.3,
                                radius: 0
                            },
                            {
                                label: $('#seller-text').data('text'),
                                data: [],
                                backgroundColor: "#0177CD",
                                hoverBackgroundColor: "#0177CD",
                                borderColor: "#0177CD",
                                fill: false,
                                lineTension: 0.3,
                                radius: 0
                            },
                            {
                                label: $('#message-commission-text').data('text'),
                                data: [],
                                backgroundColor: "#FFB36D",
                                hoverBackgroundColor: "FFB36D",
                                borderColor: "#FFB36D",
                                fill: false,
                                lineTension: 0.3,
                                radius: 0
                            }
                        ]
                    },
                    options: options
                });

                myChart.data.labels = response_data.inhouse_label;
                myChart.data.datasets[0].data = response_data.inhouse_earn;
                myChart.data.datasets[1].data = response_data.seller_earn;
                myChart.data.datasets[2].data = response_data.commission_earn;

                myChart.update();
            },
            complete: function() {
                $('#loading').fadeOut();
            }
        });
    }
</script>


<script type="module">
    import {
        initializeApp
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js';
    import {
        getMessaging,
        getToken
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging.js';

    const firebaseConfig = {
        apiKey: "AIzaSyBNsNd1OSPgjTm9NxX38MZq_pdE5cpUy3A",
        authDomain: "manalsoftech-6807e.firebaseapp.com",
        projectId: "manalsoftech-6807e",
        storageBucket: "manalsoftech-6807e.appspot.com",
        messagingSenderId: "1023155540439",
        appId: "1:1023155540439:web:8f7f2f268931822bbffb92",
        measurementId: "G-EVNBKN5FVB"
    };

    // const firebaseConfig = {
    //     apiKey: "{{ env('FIREBASE_APIKEY') }}",
    //     authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
    //     projectId: "{{ env('FIREBASE_PRODJECTID') }}",
    //     storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
    //     messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
    //     appId: "{{ env('FIREBASE_APPID') }}",
    //     measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
    // };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    // Register Service Worker
    navigator.serviceWorker.register("{{ asset('firebase/sw.js') }}")
        .then((registration) => {
            console.log("Service Worker registered successfully:", registration);
            return getToken(messaging, {
                serviceWorkerRegistration: registration,
                vapidKey: "BAtngqANTm4hqsfJirncDxyHeS6ghRBFvgXYXi7iJ_I4mQ1bMhGHe20mUWV0YZIcUGjQN8-upn8udKlVdL_FNWU" //"{{ env('VAPID_KEY') }}"
            });
        })
        .then((token) => {
            if (token) {
                console.log("FCM Token:", token);
                $('.fcm_tokens').val(token);
            } else {
                console.warn("No FCM token available. Request notification permission.");
            }
        })
        .catch((error) => {
            console.error("Error while retrieving FCM token:", error);
        });
</script>

@endpush