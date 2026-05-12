@extends('layouts.front-end.app')
@section('title', translate('tour'))
@push('css_or_js')
<link rel="stylesheet"
    href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<style>
    /* Prograss */
    @media (min-width: 768px) {
        .md\:top-\[68px\] {
            top: 68px;
        }
    }

    .w-full {
        width: 100%;
    }

    .z-20 {
        z-index: 20;
    }

    .top-0 {
        top: 0;
    }

    .sticky {
        position: sticky;
    }

    .bg-bar {
        --tw-bg-opacity: 1;
        background-color: #f3f4f6;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .overflow-x-scroll {
        overflow-x: scroll;
    }

    .max-w-screen-xl {
        max-width: 1280px;
    }

    .justify-center {
        justify-content: center;
    }

    .items-center {
        align-items: center;
    }

    .px-2 {
        padding-left: .5rem;
        padding-right: .5rem;
    }

    .shrink-0 {
        flex-shrink: 0;
    }

    .text-next {
        --tw-text-opacity: 1;
        color: #1573DF;
    }

    .text-disable {
        --tw-text-opacity: 1;
        color: #5f6672;
    }

    .border-bar {
        --tw-border-opacity: 1;
        border-color: #5f6672 !important;
    }

    .border {
        border-width: 1px;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    .circle-img-container:hover .circle-img {
        top: -8px;
        left: 0px;
        width: 40px;
        height: 43px;
        z-index: 10;
        max-height: 146px;
    }

    .circle-img-container .circle-img {
        width: 40px;
        height: 43px;
        overflow: hidden;
        position: absolute;
        left: 0;
        top: 0;
        transition: all 0.12s;
        margin-left: -20px;
        background-color: white;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    .bg-center {
        background-position: center;
    }

    .bg-cover {
        background-size: cover;
    }

    .w-full {
        width: 100%;
    }

    .circle-img-container {
        width: 33px;
        height: 40px;
        position: relative;
    }

    .tray {
        text-align: center;
        display: flex;
        flex-wrap: none;
        align-items: center;
        justify-content: center;
        margin-right: 20rem;
        justify-content: center;
        margin-top: 12px;
    }


    .otp-input-fields {
        margin: auto;
        max-width: 400px;
        width: auto;
        display: flex;
        justify-content: center;
        gap: 5px;
        padding: 10px;
    }

    .otp-input-fields input {
        height: 50px;
        width: 50px;
        background-color: transparent;
        border-radius: 4px;
        border: 1px solid #2f8f1f;
        text-align: center;
        outline: none;
        font-size: 18px;
        /* Firefox */
    }

    .otp-input-fields input::-webkit-outer-spin-button,
    .otp-input-fields input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .otp-input-fields input[type=number] {
        -moz-appearance: textfield;
    }

    .otp-input-fields input:focus {
        border-width: 2px;
        border-color: #287a1a;
        font-size: 20px;
    }

    .countdown {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-right: 13rem;
    }

    .countdown>div {
        display: flex;
        flex-wrap: nowrap;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        margin-top: 4px;
        box-shadow: 2px 2px 3px #fe9802;
        width: 62px;
        height: 45px;
        padding: 4px;
        font-size: 12px;
        border-radius: 5px;
    }

    .number {
        font-weight: 500;
        font-size: 25px;
        color: var(--web-primary);
    }

    @media (max-width: 768px) {
        .otp-input-fields input {
            height: 40px;
            width: 40px;
        }

        .otp-input-fields {
            gap: 9px;
        }

        #breadcrum-container {
            font-size: 11px;
            padding-left: 5px !important;
        }
    }
</style>
@endpush
@section('content')
<div class="w-full h-full sticky md:top-[68px] top-0 z-20">
    <div class="bg-bar w-full">
        <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
            id="breadcrum-container-outer">
            <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                <div class="bg-bar w-full">
                    <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                        id="breadcrum-container-outer">
                        <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                            <div class="d-flex justify-center items-center pt-3 pb-3">
                                <div class="d-flex justify-center items-center">
                                    <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                                        <path
                                            d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                                            fill="white"></path>
                                    </svg>
                                    <div
                                        class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium  ">
                                        {{ translate('Details') }}
                                    </div>
                                </div>
                                <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14"
                                        viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                                            fill="#9CA3AF"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                                            fill="#9CA3AF"></path>
                                    </svg>
                                </div>
                                <div class="d-flex justify-center items-center">
                                    <div
                                        class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">
                                        2</div>
                                    <div
                                        class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
                                        {{ translate('tour_booking') }}
                                    </div>
                                </div>
                                <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14"
                                        viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                                            fill="#9CA3AF"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                                            fill="#9CA3AF"></path>
                                    </svg>
                                </div>
                                <div class="d-flex justify-center items-center">
                                    <div
                                        class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">
                                        3</div>
                                    <div
                                        class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
                                        {{ translate('Make Payment') }}
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
<div class="container mt-3 rtl px-0 px-md-3 text-align-direction mb-2" id="cart-summary">
    <div class="__inline-23">
        <div class="container mt-4 rtl text-align-direction">
            <div class="row ">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-4 col-12">
                            <div class="cz-product-gallery">
                                <div class="cz-preview">
                                    <div id="sync1"
                                        class="owl-carousel owl-theme product-thumbnail-slider owl-loaded owl-drag">
                                        <div class="owl-stage-outer">
                                            <div class="owl-stage" style="transform: translate3d(0px, 0px, 0px); transition: all; width: 613px;">
                                                <div class="owl-item active" style="width: 613px;">
                                                    <div class="d-flex align-items-center justify-content-center active">
                                                        <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($getfirst['tour_image'] ?? ''), type: 'backend-product') }}" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="owl-nav disabled"><button type="button" role="presentation"
                                                class="owl-prev"><span aria-label="Previous">‹</span></button><button
                                                type="button" role="presentation" class="owl-next"><span
                                                    aria-label="Next">›</span></button></div>
                                        <div class="owl-dots disabled"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12 mt-md-0 mt-sm-3 web-direction">
                            <div class="details my-2 __h-100">
                                <span class="__inline-24">{{ $getfirst['tour_name'] }} </span>
                                <div class="mb-2">
                                    <span class=" __text-18">{{ $getfirst['cities_name'] }},
                                        {{ $getfirst['state_name'] }}, {{ $getfirst['country_name'] }} </span>
                                </div>
                                @if ($getfirst['use_date'] == 1 || $getfirst['use_date'] == 2 || $getfirst['use_date'] == 4)
                                <div class="mb-2">
                                    <span
                                        class=" __text-18 font-weight-bold">{{ translate('Pickup Location') }}:</span><span
                                        class="__text-16"> {{ $getfirst['pickup_location'] }} </span>
                                </div>
                                @endif
                                @if ($getfirst['use_date'] == 1)
                                <?php
                                $dateRange = explode(' - ', $getfirst['startandend_date']);
                                $startDate =  isset($dateRange[0]) ? $dateRange[0] : '';
                                $endDate = isset($dateRange[1]) ? $dateRange[1] : '';
                                $times = $getfirst['pickup_time'];

                                if ($startDate && $endDate) {
                                    $start = new DateTime($startDate);
                                    $end = new DateTime($endDate);
                                    $difference = $start->diff($end)->days;
                                    $dateDisplay = "";
                                    if (isset($getfirst['customized_type']) && isset($getfirst['customized_dates'])) {
                                        $customizedType = $getfirst['customized_type'];
                                        $customizedDates = json_decode($getfirst['customized_dates'] ?? "[]", true);
                                        switch ($customizedType) {
                                            case 1:
                                                $today = new DateTime();
                                                $nextDates = [];
                                                foreach ($customizedDates as $day) {
                                                    $next = new DateTime("next " . $day);
                                                    if (strtolower($today->format('l')) == strtolower($day)) {
                                                        $next = clone $today;
                                                    }
                                                    $nextDates[] = $next;
                                                }
                                                usort($nextDates, function ($a, $b) {
                                                    return $a <=> $b;
                                                });
                                                $nextDay = $nextDates[0]->format("Y-m-d");
                                                $dates = $nextDay;
                                                $start_time = strtotime($nextDay . ' ' . $times);
                                                break;

                                            case 2:
                                                $dayNumbers = array_map(function ($date) {
                                                    return (int)date('d', strtotime($date));
                                                }, $customizedDates);
                                                $today = new DateTime();
                                                $nextDates = [];
                                                foreach ($dayNumbers as $dayNumber) {
                                                    $next = new DateTime($today->format('Y-m-' . sprintf('%02d', $dayNumber)));
                                                    if ($next < $today) {
                                                        $next->modify('+1 month');
                                                    }
                                                    $nextDates[] = $next;
                                                }
                                                usort($nextDates, function ($a, $b) {
                                                    return $a <=> $b;
                                                });

                                                $nextDay = $nextDates[0]->format("Y-m-d");
                                                $dates = $nextDay;
                                                $start_time = strtotime($nextDay . ' ' . $times);
                                                break;

                                            case 3:
                                                $today = new DateTime();
                                                $nextDates = [];
                                                foreach ($customizedDates as $dateStr) {
                                                    $date = DateTime::createFromFormat('Y-m-d', $dateStr);
                                                    $monthDay = $date->format('m-d');
                                                    $currentYearDate = DateTime::createFromFormat('Y-m-d', $today->format('Y') . '-' . $monthDay);
                                                    if ($currentYearDate < $today) {
                                                        $currentYearDate->modify('+1 year');
                                                    }
                                                    $nextDates[] = $currentYearDate;
                                                }
                                                usort($nextDates, function ($a, $b) {
                                                    return $a <=> $b;
                                                });
                                                $nextDay = $nextDates[0]->format("Y-m-d");
                                                $dates = $nextDay;
                                                $start_time = strtotime($nextDay . ' ' . $times);
                                                break;
                                            default:
                                                $dates = $startDate;
                                                $start_time = strtotime($dates . ' ' . $times);
                                                break;
                                        }
                                    }
                                }
                                $current_time = time();
                                ?>
                                <div class="mb-2">
                                    <span
                                        class=" __text-18 font-weight-bold">{{ translate('pickup Date') }}:</span><span
                                        class="__text-16">
                                        {{ date('d M,Y h:i A', strtotime($dates . ' ' . $times)) }} </span>
                                </div>

                                @if ($start_time > $current_time)
                                <div class="mb-2">
                                    <input type="hidden" name="date" id="fullDate"
                                        value="{{ $dates }}">
                                    <input type="hidden" name="dates" id="fullDates"
                                        value="{{ $dates }}">
                                    <input type="hidden" name="time" id="fullTime"
                                        value="{{ $times }}">
                                    <div class="flex relative w-full">
                                        <div class="row flex w-full justify-between flex-1">
                                            <div class="float-start countdown">
                                                <div>
                                                    <span class="number days"></span>
                                                    <span>{{ translate('Days') }}</span>
                                                </div>
                                                <div>
                                                    <span class="number hours"></span>
                                                    <span>{{ translate('Hour') }}</span>
                                                </div>
                                                <div>
                                                    <span class="number minutes"></span>
                                                    <span>{{ translate('Mins') }}</span>
                                                </div>
                                                <div>
                                                    <span class="number seconds"></span>
                                                    <span>{{ translate('Secs') }}</span>
                                                </div>
                                            </div>
                                            <span
                                                class='countdown_message text-success font-weight-bold'></span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endif
                                <!-- Profile Icon -->
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap justify-center items-center">
                                        <div class="w-full">
                                            <div class="w-full tray mb-3">
                                                <?php $totals_booking_user = ($getfirst['tour_order_review_count'] ?? 0);
                                                if ($totals_booking_user <= 5) {
                                                    $show_user = 1;
                                                } else {
                                                    $show_user = 2;
                                                }
                                                ?>
                                                @if ($show_user == 1)
                                                @for ($ip = 0; $ip < $totals_booking_user; $ip++)
                                                    <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $ip . '.jpg') }}')">
                                                    </div>
                                            </div>
                                            @endfor
                                            @else
                                            @php
                                            $uniqueUsers = range(0, 13);
                                            shuffle($uniqueUsers);
                                            $selectedUsers = array_slice($uniqueUsers, 0, 5);
                                            @endphp
                                            @foreach ($selectedUsers as $random_user)
                                            <div class="relative circle-img-container">
                                                <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                    style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $random_user . '.jpg') }}')">
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Count number of People -->
                            <div class="mt-[1px] mb-3 md:mb-0 flex flex-col">
                                <div class="flex flex-row mt-2 flex-nowrap break-normal leading-normal">
                                    <div class="flex">
                                        <div class="">
                                            <span class=" font-bold text-#F18912 ml-1 break-normal">
                                                <span class="mr-1 inline-flex break-normal">
                                                    {{ translate('Till now') }},
                                                    {{ 10000 + $getfirst['tour_order_review_count'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span
                                                class="text-">{{ translate('Customers have already booked their tours through Mahakal.com, trusting us for a hassle-free and spiritually enriching travel experience') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Button -->
                            <div id="" role="button">
                                @if (auth('customer')->check())
                                <script>
                                    window.addEventListener('load', function() {
                                        document.getElementById('lead-store-form').submit();
                                    });
                                </script>
                                <a href="javascript:void(0);" id="auth-book-now"
                                    class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('explore') }}</a>
                                @else
                                <a href="javascript:void(0);" id="participate-btn"
                                    class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('explore') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="modal fade rtl text-align-direction" id="participateModal" tabindex="-1" role="dialog"
        aria-labelledby="participateModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="flex justify-center items-center my-3">
                        <span class="text-18 font-bold ml-2">{{ translate('Fill in your details') }}</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr class="bg-[#E6E4EB] w-full">
                <div class="modal-body flex justify-content-center">
                    <div id="recaptcha-container"></div>
                    <div class="w-full mt-1 px-2">
                        <span
                            class="block text-[16px] font-bold text-gray-900 dark:text-white">{{ translate('Enter Your Whatsapp Mobile Number') }}</span>
                        <br>
                        <span
                            class="text-[12px] font-normal text-[#707070]">{{ translate('Your booking details and confirmation will be sent to the WhatsApp number provided below') }}...</span>
                        <!-- Model Form -->
                        <div class="w-full mr-9 px-0 pt-3">
                            <form class="needs-validation_" id="lead-store-form" action="{{ route('tour.visit-leads', [$getfirst['id']]) }}" method="post">
                                @csrf
                                @php
                                if (auth('customer')->check()) {
                                $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                }
                                @endphp
                                <input type="hidden" name="tour_id" value="{{ $getfirst['id'] ?? '' }}">
                                <input type="hidden" name="verify_otp" id="phone-number-valid">
                                <div class="row">
                                    <div class="col-md-12" id="phone-div">
                                        <div class="form-group">
                                            <label
                                                class="form-label font-semibold">{{ translate('phone_number') }}
                                                <small class="text-primary">(
                                                    *{{ translate('country_code_is_must_like_for_IND') }} 91
                                                    )</small>
                                            </label>
                                            <input
                                                class="form-control text-align-direction phone-input-with-country-picker"
                                                type="tel"
                                                value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                                name="person_phone" id="person-number"
                                                placeholder="{{ translate('enter_phone_number') }}" required
                                                {{ isset($customer['phone']) ? 'readonly' : '' }}
                                                oninput="this.value=this.value.slice(0,10)">

                                            <input type="hidden" class="country-picker-phone-number w-50" name="person_phone" readonly>

                                            <p id="number-validation" class="text-danger" style="display: none">Enter Your Valid Mobile Number</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="name-div">
                                        <div class="form-group">
                                            <label
                                                class="form-label font-semibold">{{ translate('your_name') }}</label>
                                            <input class="form-control text-align-direction"
                                                value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? ' ' . $customer['l_name'] : '' }}"
                                                type="text" name="person_name" id="person-name"
                                                placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                                required {{ isset($customer['f_name']) ? 'readonly' : '' }} onkeyup="this.value = this.value.replace(/[^a-zA-Z\s]/g, '').replace(/\b\w/g, l => l.toUpperCase());">
                                            <p id="name-validation" class="text-danger" style="display: none">
                                                Enter Your Name</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="otp-input-div" style="display: none;">
                                        <div class="form-group text-center">
                                            <label
                                                class="form-label font-semibold ">{{ translate('enter_OTP') }}</label>
                                            <div class="otp-input-fields">
                                                <input type="number" id="otp1"
                                                    class="otp__digit otp__field__1" inputmode="number">
                                                <input type="number" id="otp2"
                                                    class="otp__digit otp__field__2" inputmode="number">
                                                <input type="number" id="otp3"
                                                    class="otp__digit otp__field__3" inputmode="number">
                                                <input type="number" id="otp4"
                                                    class="otp__digit otp__field__4" inputmode="number">
                                                <input type="number" id="otp5"
                                                    class="otp__digit otp__field__5" inputmode="number">
                                                <input type="number" id="otp6"
                                                    class="otp__digit otp__field__6" inputmode="number">
                                            </div>
                                            <p id="otpValidation" class="text-danger"></p>

                                        </div>
                                    </div>
                                    <div class="mx-auto mt-1 __max-w-356" id="send-otp-btn-div">
                                        <button type="button"
                                            class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold one-time-otp-sends"
                                            id="send-otp-btn"> {{ translate('send_OTP') }} </button>
                                        {{-- <p id="failedOtpValidation" class="text-danger mt-2"></p> --}}
                                    </div>

                                    <div class="mx-auto mt-1 __max-w-356" id="verify-otp-btn-div"
                                        style="display: none">
                                        <div class="d-flex">
                                            <button type="button"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold me-2"
                                                id="otp-back-btn">
                                                {{ translate('back') }} </button>
                                            <button type="submit"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                id="verify-otp-btn">
                                                {{ translate('verify_OTP') }} </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3" id="resend-div" style="display: none;">
                                    <p id="resend-otp-timer-text" style="display: none"> Resend OTP in <span
                                            id="resend-otp-timer"></span></p>
                                    <p id="resend-otp-btn-text" style="display: none">Didn't get the code? <a
                                            href="javascript:0" id="resend-otp-btn" style="color: blue;">Resend
                                            Otp</a></p>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection
@push('script')
</script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
</script>

<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>

<!-- Firbase CDN -->
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>

<script>
    $('#participate-btn').click(function(e) {
        e.preventDefault();
        $('#participateModal').modal('show');
    });
    var dateGet = $('#fullDate').val();
    var timeGet = $('#fullTime').val();
    const newDate = new Date(dateGet + ' ' + timeGet).getTime();
    const countdown = setInterval(() => {
        const date = new Date().getTime()
        const diff = newDate - date
        const days = Math.floor(diff % (1000 * 60 * 60 * 24 * (365.25 / 12)) / (1000 * 60 * 60 * 24))
        const hours = Math.floor(diff % (1000 * 60 * 60 * 24) / (1000 * 60 * 60))
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
        const seconds = Math.floor((diff % (1000 * 60)) / 1000)
        document.querySelector(".seconds").innerHTML = seconds < 10 ? '0' + seconds : seconds
        document.querySelector(".minutes").innerHTML = minutes < 10 ? '0' + minutes : minutes
        document.querySelector(".hours").innerHTML = hours < 10 ? '0' + hours : hours
        document.querySelector(".days").innerHTML = days < 10 ? '0' + days : days
        console.log(diff)
        if (1 > diff) {
            $(".countdown_message").text("Event Starts Live");
            $(".countdown").addClass("d-none");
        } else if (diff <= 0) {
            // clearInterval(countdown)
        } else {
            if (diff > 1) {
                // clearInterval(countdown)
            } else {
                $(".countdown_message").text("Close");
                $(".countdown").addClass("d-none");
            }
        }

    }, 1000)
</script>

<script>
    const firebaseConfig = {
        apiKey: "{{ env('FIREBASE_APIKEY') }}",
        authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
        projectId: "{{ env('FIREBASE_PRODJECTID') }}",
        storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
        appId: "{{ env('FIREBASE_APPID') }}",
        measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
    };
    firebase.initializeApp(firebaseConfig);
</script>

<script>
    $('#participate-btn').click(function(e) {
        e.preventDefault();
        $('#participateModal').modal('show');
    });

    var confirmationResult;
    var appVerifier = "";
    var sendOtpCount = 1;
    $('#send-otp-btn').click(function(e) {
        e.preventDefault();
        var name = $('#person-name').val();
        var number = $('#person-number').val();

        var phoneNumber = $('.country-picker-phone-number')
            .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
        sendotp();
    });


    function sendotp() {
        var name = $('#person-name').val();
        var number = $('#person-number').val();
        if (number == "" || number.length != 10) {
            $('#number-validation').show();
        } else if (name == "") {
            $('#number-validation').hide();
            $('#name-validation').show();
        } else {
            toastr.success('please wait...');
            $('#send-otp-btn').text('Please Wait ...');
            $('#send-otp-btn').prop('disabled', true);
            var phoneNumber = $('.country-picker-phone-number')
                .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
            if (appVerifier == "") {
                appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    size: 'invisible'
                });
            }

            firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                    $('#name-validation').hide();
                    $('#number-validation').hide();
                    $('#send-otp-btn-div').css('display', 'none');
                    $('#phone-div').css('display', 'none');
                    $('#name-div').css('display', 'none');
                    $('#otp-input-div').css('display', 'block');
                    $('#verify-otp-btn-div').css('display', 'block');
                    if (sendOtpCount == 1) {
                        sendOtpCount = 2;
                        otpTimer();
                    }
                    confirmationResult = confirmation;
                    toastr.success('otp sent successfully');
                    $('#resend-div').show();
                })
                .catch(function(error) {
                    toastr.error('Failed to send OTP. Please try again');
                    $('#send-otp-btn').text('Send OTP');
                    $('#send-otp-btn').prop('disabled', false);
                    console.error('OTP sending error:', error);
                });
        }
    }

    // otp timer
    function otpTimer() {
        $('#resend-otp-timer-text').css('display', 'block');
        $('#resend-otp-btn-text').css('display', 'none');
        var resendOtpTimer = 30;
        var interval = setInterval(() => {
            resendOtpTimer--;
            $('#resend-otp-timer').text(resendOtpTimer);
            if (resendOtpTimer <= 0) {
                $('#resend-otp-timer-text').css('display', 'none');
                $('#resend-otp-btn-text').css('display', 'block');
                clearInterval(interval);
            }
        }, 1000);
    }

    // resend otp
    $('#resend-otp-btn').click(function(e) {
        e.preventDefault();

        var phoneNumber = $('.country-picker-phone-number')
            .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
        if (!appVerifier) {
            appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                size: 'invisible'
            });
        }
        firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                confirmationResult = confirmation;
                otpTimer();
                toastr.success('OTP resent successfully');
            })
            .catch(function(error) {
                toastr.error('Failed to send OTP. Please try again');
            });
    });

    $('#verify-otp-btn').click(function(e) {
        e.preventDefault();
        toastr.success('please wait...');
        var name = $('#person-name').val();
        var number = $('.country-picker-phone-number').val(); //$('#person-number').val();
        var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
            $('#otp6').val();
        if (confirmationResult) {
            confirmationResult.confirm(otp).then(function(result) {
                    $('#participateModal').modal('hide');
                    $(this).text('Please Wait ...');
                    $(this).prop('disabled', true);
                    $('#lead-store-form').submit();
                })
                .catch(function(error) {
                    $('#otpValidation').text('Incorrect OTP');
                    $('.otp-input-fields input').val('');
                    $('.otp-input-fields input:first').focus();
                });
        }


    });

    $('#otp-back-btn').click(function(e) {
        e.preventDefault();

        $('#send-otp-btn-div').css('display', 'block');
        $('#phone-div').css('display', 'block');
        $('#name-div').css('display', 'block');
        $('#otp-input-div').css('display', 'none');
        $('#verify-otp-btn-div').css('display', 'none');
        $('#send-otp-btn').prop('disabled', false);
        $('#send-otp-btn').text('Send OTP');
        $('#resend-div').hide();
    });
</script>

<!-- OTP SECTION -->
<script type="text/javascript">
    var otp_inputs = document.querySelectorAll(".otp__digit")
    var mykey = "0123456789".split("")
    otp_inputs.forEach((_) => {
        _.addEventListener("keyup", handle_next_input)
    })

    function handle_next_input(event) {
        let current = event.target
        let index = parseInt(current.classList[1].split("__")[2])
        current.value = event.key

        if (event.keyCode == 8 && index > 1) {
            current.previousElementSibling.focus()
        }
        if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
            var next = current.nextElementSibling;
            next.focus()
        }
        var _finalKey = ""
        for (let {
                value
            }
            of otp_inputs) {
            _finalKey += value
        }
    }
</script>

{{-- mobile no blur --}}
<script>
    $('#person-number').blur(function(e) {
        e.preventDefault();
        var code1 = $('.country-picker-phone-number').val();
        var mobile = $(this).val();
        if (mobile.length <= 9) {
            toastr.error("Invalid phone number.");
            $('.one-time-otp-sends').prop('disabled', true);
            return;
        } else {
            $('.one-time-otp-sends').prop('disabled', false);
        }
        $.ajax({
            type: "get",
            url: "{{ url('account-counselling-order-user-name') }}" + "/" + code1,
            success: function(response) {
                if (response.status == 200) {
                    var name = response.user.f_name + ' ' + response.user.l_name;
                    $('#person-name').val(name);
                    $('#person-name').prop('readonly', true);
                    $('#phone-number-valid').val(1);
                    $('.one-time-otp-sends').attr('id', 'send-otp-btn');
                    $('.one-time-otp-sends').text("{{ translate('send_OTP') }}");
                } else {
                    $('#person-name').val('');
                    $('#person-name').prop('readonly', false);
                    $('#phone-number-valid').val(0);
                    $('.one-time-otp-sends').attr('id', 'invalid-user-login-booking');
                    $('.one-time-otp-sends').text("{{ translate('book_now') }}");
                }
            }
        });
    });

    $(document).on('click', '#invalid-user-login-booking', function(e) {
        e.preventDefault();
        $('#participateModal').modal('hide');
        $(this).text('Please Wait ...');
        $(this).prop('disabled', true);
        $('#lead-store-form').submit();
    });
</script>

<script>
    $('#auth-book-now').click(function(e) {
        e.preventDefault();
        $('#lead-store-form').submit();
    });
</script>
@endpush