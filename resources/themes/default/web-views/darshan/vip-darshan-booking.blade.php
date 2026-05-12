@extends('layouts.front-end.app')
@section('title', translate('VIP_darshan'))
@push('css_or_js')
<meta property="og:image"
    content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta property="twitter:card"
    content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }


    .section-header {
        height: 200px;
        background-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .section-header img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .price-box {
        background-color: #fff3e0;
        border: 1px solid #ffcc80;
        padding: 15px;
        border-radius: 8px;
    }

    .badge-custom {
        background-color: #fbe9e7;
        color: #d84315;
        padding: 8px 12px;
        border-radius: 20px;
        margin: 4px;
        display: inline-block;
        font-size: 14px;
    }

    .btn-outline-primary:hover {
        background-color: var(--web-primary) !important;
        border-color: var(--web-primary) !important;
    }

    .slot-btn {
        margin: 4px;
    }

    .custom-continue-btn {
        background: linear-gradient(to right, #ff9900, #ffcc66);
        color: white;
        font-weight: bold;
        border: none;
        padding: 15px 98px;
        font-size: 16px;
        border-radius: 20px;
        box-shadow: 2px 4px 6px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .custom-continue-btn:hover {
        box-shadow: 3px 6px 12px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        button.btn.btn-outline-primary.slot-btn.m-2 {
            font-size: 10px;
            padding: 0.425rem 0.275rem;
        }
    }
</style>
@endpush
@section('content')
<div class="container mt-3 rtl text-align-direction" id="cart-summary">
    <div class="row">
        <div class="col-md-6">
            <!-- <div class="container mt-4">
                <div class="section-header text-center">
                    <img src="" alt="Omkareshwar Darshan">
                </div>
            </div> -->
            <div>
                <img class="img-thumbnail rounded"
                    src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/' . ($getData['thumbnail'] ?? ''), type: 'backend-product') }}"
                    alt="">
            </div>
        </div>
        <div class="col-md-6">
            <h3 class="mt-3 text-left">{{ $getData['name'] ?? '' }}</h3>
            <div class="price-box mt-4">
                <div class="d-flex justify-content-between">
                    <?php
                    $vipPlans = json_decode($getData['vip_plans'], true);
                    $vipDarshan = [];

                    // Find the darshan plan with matching id
                    if (!empty($vipPlans)) {
                        foreach ($vipPlans as $plan) {
                            if ($plan['id'] == ($templeLead['package_id'] ?? 0)) {
                                $vipDarshan = $plan['package'][0] ?? [];
                                break;
                            }
                        }
                    }
                    ?>
                    <strong>
                        <span>{{translate('price')}}:</span><br>
                        <span class="text-muted"> {{ $vipDarshan['name']??'' }}</span>
                    </strong>
                    <strong>
                        <span class="text-danger font-weight-bolder">
                        @php
                            $price = ($vipDarshan['price'] ?? 0) 
                                + ($vipDarshan['platform_fee'] ?? 0) 
                                + ($vipDarshan['receipt_price'] ?? 0);
                        @endphp

                        @if($price > 0)
                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $price), currencyCode: getCurrencyCode()) }}
                        @else
                            {{ translate('Free Pass') }}
                        @endif
                        </span><br>
                        <span> {{ translate('per_Head') }}</span>
                    </strong>
                </div>
            </div>
            @if(!empty($vipDarshan['include']) && count($vipDarshan['include']) > 0)
            <div class="mt-4">
                <h5>{{ translate('what_does_that_involve') }}?</h5>
                <div>
                    @foreach($vipDarshan['include'] as $in_val)
                    <span class="badge-custom">{{ $in_val['name'] }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            <!-- Slot Selection -->
            @if(!empty($vipDarshan['date']) && count($vipDarshan['date']) > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <h5>{{translate('select_time_slot')}}</h5>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3">
                        <input type="text" class="form-control hasDatepicker" placeholder="{{ translate('select_time_slot') }}" onclick="window.$datepicker.open()" readonly />
                    </div>
                </div>
                <div class="col-12 col-md-6"></div>
                <div class="col-md-12 d-flex flex-wrap">
                    @foreach($vipDarshan['date'] as $in_date)
                    <button type="button" class="btn btn-outline-primary slot-btn m-2" data-time="{{ $in_date['time'] }}">
                        <span class="check-icon me-2 d-none">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        {{ $in_date['time'] }}
                    </button>
                    @endforeach
                    <div id="no-slot-msg" class="mt-3 text-danger d-none">{{ translate('Currently_No_slots_available_on_the_given date') }}</div>
                </div>
            </div>
            @endif
        </div>
        <div class="container my-4 text-end">
            <button class="custom-continue-btn font-weight-bolder" onclick="from_check()">{{ translate('continue') }}</button>
        </div>
    </div>
</div>

<div class="row">
    @if (isset($getData['video_url']) &&
    $getData['video_url'] != null &&
    str_contains($getData['video_url'], 'youtube.com/embed/'))
    <div class="col-12 rtl text-align-direction">
        <div class="resp-iframe">
            <div class="resp-iframe__container">
                <iframe width="420" height="315" src="{{ $getData['video_url'] }}" frameborder="0"
                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen="">
                </iframe>
            </div>
        </div>
    </div>
    @endif
</div>
<form action="{{ route('vip-darshan-lead-update') }}" class="submit-forms" method="GET">
    @csrf
    <input type="hidden" name="lead_id" value="{{ $templeLead['id'] }}">
    <input type="hidden" name="information" class="information-array">
</form>
@endsection
@push('script')
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script type="module">
    // const firebaseConfig = {
    //     apiKey: "AIzaSyBNsNd1OSPgjTm9NxX38MZq_pdE5cpUy3A",
    //     authDomain: "manalsoftech-6807e.firebaseapp.com",
    //     projectId: "manalsoftech-6807e",
    //     storageBucket: "manalsoftech-6807e.appspot.com",
    //     messagingSenderId: "1023155540439",
    //     appId: "1:1023155540439:web:8f7f2f268931822bbffb92",
    //     measurementId: "G-EVNBKN5FVB"
    // };
    // const app = initializeApp(firebaseConfig);
    // const messaging = getMessaging(app);

    import {
        initializeApp
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js';
    import {
        getMessaging,
        getToken
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging.js';


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
    navigator.serviceWorker.register("{{ asset('public/firebase/sw.js') }}")
        .then((registration) => {
            console.log("Service Worker registered successfully:", registration);
            return getToken(messaging, {
                serviceWorkerRegistration: registration,
                vapidKey: "{{ env('VAPID_KEY') }}"
            });
        })
        .then((token) => {
            if (token) {
                console.log("FCM Token:", token);
                $.ajax({
                    url: "{{ url('api/v1/fcm_token_Update') }}",
                    data: {
                        'token': token,
                        'user_id': "{{ auth('customer')->id() ?? 0 }}"
                    },
                    dataType: "json",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {

                    }
                });
            } else {
                console.warn("No FCM token available. Request notification permission.");
            }
        })
        .catch((error) => {
            console.error("Error while retrieving FCM token:", error);
        });
</script>


<script>
    // document.addEventListener('contextmenu', function(e) {
    //     e.preventDefault();
    // });
    document.onkeydown = function(e) {
        if (e.keyCode == 123) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
            return false;
        }
        if (e.ctrlKey && e.keyCode == 85) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 67) {
            return false;
        }
    };

    var today = new Date();
    var maxDate = new Date(today);
    maxDate.setMonth(maxDate.getMonth() + 2);

    var formattedToday = today.getDate().toString().padStart(2, '0') + '-' +
        (today.getMonth() + 1).toString().padStart(2, '0') + '-' +
        today.getFullYear();

    window.$datepicker = $('.hasDatepicker').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'dd-mm-yyyy',
        modal: true,
        footer: true,
        minDate: formattedToday,
        maxDate: maxDate,
        todayHighlight: true,
        // value: formattedToday
    });

    $(document).ready(function() {
        var today = new Date();

        // Handle date change
        $('.hasDatepicker').on('change', function() {
            var selectedDate = $(this).val();
            var todayFormatted = today.getDate().toString().padStart(2, '0') + '-' +
                (today.getMonth() + 1).toString().padStart(2, '0') + '-' +
                today.getFullYear();

            // Reset all slots
            $('.slot-btn').removeClass('active d-none');
            $('.slot-btn').find('.check-icon').addClass('d-none');
            MultipleArray.time = "";

            var hiddenCount = 0; // Track how many slots get hidden

            if (selectedDate === todayFormatted) {
                var nowMinutes = today.getHours() * 60 + today.getMinutes();

                $('.slot-btn').each(function() {
                    var timeRange = $(this).data('time');
                    var startTimeStr = timeRange.split('-')[0].trim();
                    var startMinutes = convertToMinutes(startTimeStr);

                    if (startMinutes <= nowMinutes) {
                        $(this).addClass('d-none'); // hide instead of disable
                        hiddenCount++;
                    }
                });
            }

            // If all slots are hidden → show message
            if ($('.slot-btn:visible').length === 0) {
                $('#no-slot-msg').removeClass('d-none');
            } else {
                $('#no-slot-msg').addClass('d-none');
            }
        });


        function convertToMinutes(timeStr) {
            var [time, modifier] = timeStr.split(' ');
            var [hours, minutes] = time.split(':').map(Number);

            if (modifier === 'PM' && hours !== 12) {
                hours += 12;
            }
            if (modifier === 'AM' && hours === 12) {
                hours = 0;
            }
            return hours * 60 + minutes;
        }
    });


    const MultipleArray = {
        "price": "{{$vipDarshan['price']??0}}",
        "date": "",
        "time": ""
    };

    $(document).ready(function() {
        $('.slot-btn').on('click', function() {
            $('.slot-btn').removeClass('active');
            $('.check-icon').addClass('d-none');
            $(this).addClass('active');
            $(this).find('.check-icon').removeClass('d-none');
            MultipleArray.time = $(this).data('time');
            console.log("Selected time:", MultipleArray.time);
            console.log("Updated object:", MultipleArray);
        });
    });

    function from_check() {
        MultipleArray.date = $('.hasDatepicker').val();
        if (!MultipleArray.date) {
            toastr.error("Please select a Date.");
            return false;
        }
        if (!MultipleArray.time) {
            toastr.error("Please select a Time Slot.");
            return false;
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            }
        });
        $('.information-array').val(JSON.stringify(MultipleArray));
        $('.submit-forms').submit();
    }
</script>

@endpush