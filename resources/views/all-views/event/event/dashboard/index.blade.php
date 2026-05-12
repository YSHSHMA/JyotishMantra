@extends('layouts.back-end.app-event')
@section('title', translate('dashboard'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@php 
use App\Utils\Helpers;
@endphp
<div class="content container-fluid">
    <div class="page-header pb-0 border-0 mb-3">
        <div class="flex-between row align-items-center mx-1">
            <div>
                @if(auth('event')->check())
                <h1 class="page-header-title text-capitalize">{{translate('welcome').' '.auth('event')->user()->f_name.' '.auth('event')->user()->l_name}}</h1>
                @elseif(auth('event_employee')->check())
                <h1 class="page-header-title text-capitalize">{{translate('welcome').' '.auth('event_employee')->user()->name }}</h1>
                @endif
                <p>{{ translate('monitor_your_business_analytics_and_statistics').'.'}}</p>
            </div>
            <div>
                @if(auth('event')->check())
                @if(session()->has('device_fcm'))

                @else
                <form action="{{ route('event-vendor.fcm-update.owners')}}" method="post" onload="$('.fcm_type').val('owner')">
                    @csrf
                    <input type="hidden" name="type" value="owner" class="fcm_type">
                    <input type="hidden" name="fcm" value="" class="fcm_tokens">
                    <input type="submit" hidden class="fcm_sumbmits">
                </form>
                @endif
                @endif
            </div>

        </div>
    </div>
    @if(Helpers::Employee_modules_permission('Dashboard', 'Analytics', 'View'))
    <div class="card mb-3 remove-card-shadow">
        <div class="card-body">
            <div class="row justify-content-between align-items-center g-2 mb-3">
                <div class="col-sm-6">
                    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                        <img src="{{dynamicAsset(path: 'public/assets/back-end/img/business_analytics.png')}}" alt="">
                        {{translate('event_analytics')}}
                    </h4>
                </div>
            </div>
            <div class="row g-2" id="order_stats">
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_pending" href="{{route('event-vendor.event-management.event-pending')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/pending.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('pending_event')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['pending']??0}}</span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_confirmed" href="{{route('event-vendor.event-management.event-upcomming')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/tdce.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('upcomming')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['upcomming']??0}}</span>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <a class="order-stats order-stats_out-for-delivery" href="{{route('event-vendor.event-management.event-running')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/out-of-delivery.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('running')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['running']??0}}</span>
                    </a>
                </div>


                <div class="ol-sm-6 col-lg-3">
                    <a class="order-stats order-stats_delivered" href="{{route('event-vendor.event-management.event-complate')}}">
                        <div class="order-stats__content">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/tdce.png')}}" alt="">
                            <h6 class="order-stats__subtitle">{{translate('complete')}}</h6>
                        </div>
                        <span class="order-stats__title">{{$orderStatus['complete']??0}}</span>
                    </a>
                </div>
                <div class="ol-sm-6 col-lg-3">
                    <a class="order-stats order-stats_canceled" href="{{route('event-vendor.event-management.event-cancel')}}">
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
    @endif
    @if(Helpers::Employee_modules_permission('Dashboard', 'Wallet', 'View'))
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
    @endif
</div>
<span id="earning-statistics-url" data-url=""></span>
<span id="withdraw-method-url" data-url=""></span>
<span id="order-status-url" data-url=""></span>
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
<script type="module">
    import {
        initializeApp
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js';
    import {
        getMessaging,
        getToken
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging.js';

    // const firebaseConfig = {
    //     apiKey: "AIzaSyBNsNd1OSPgjTm9NxX38MZq_pdE5cpUy3A",
    //     authDomain: "manalsoftech-6807e.firebaseapp.com",
    //     projectId: "manalsoftech-6807e",
    //     storageBucket: "manalsoftech-6807e.appspot.com",
    //     messagingSenderId: "1023155540439",
    //     appId: "1:1023155540439:web:8f7f2f268931822bbffb92",
    //     measurementId: "G-EVNBKN5FVB"
    // };

    const firebaseConfig = {
        apiKey: "{{ env('FIREBASE_APIKEY') }}",
        authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
        projectId: "{{ env('FIREBASE_PRODJECTID') }}",
        storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
        appId: "{{ env('FIREBASE_APPID') }}",
        measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    // Register Service Worker
    navigator.serviceWorker.register("{{ asset('firebase/sw.js') }}")
        .then((registration) => {
            console.log("Service Worker registered successfully:", registration);
            return getToken(messaging, {
                serviceWorkerRegistration: registration,
                // vapidKey: "BAtngqANTm4hqsfJirncDxyHeS6ghRBFvgXYXi7iJ_I4mQ1bMhGHe20mUWV0YZIcUGjQN8-upn8udKlVdL_FNWU" 
                vapidKey: "{{ env('VAPID_KEY') }}"
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