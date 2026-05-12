@extends('layouts.front-end.app')

@section('title', translate('Donate'))

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
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
    rel="stylesheet" type="text/css" />
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
                                        {{ translate('Add Details') }}
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
                                    <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                                        <path
                                            d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                                            fill="white"></path>
                                    </svg>
                                    <div
                                        class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
                                        {{ translate('Donate') }}
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
                                    <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                                        <path
                                            d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                                            fill="white"></path>
                                    </svg>
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
@if ($type == 'success')
<div class="container mt-5 mb-5 rtl __inline-53 text-align-direction">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10 col-lg-10">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                    </div>
                    <h6 class="font-black fw-bold text-center">
                        {{ translate('Your donation has been successfully received') }}!
                    </h6>
                    <p class="text-center fs-12">
                        {{ translate('Your support will help the trust continue its noble initiatives more effectively') }}.
                    </p>
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('account-order-donate') }}" class="btn btn--primary mb-3 text-center">
                                {{ translate('view_details') }}
                            </a>
                        </div>
                        <div class="col-12 text-center">
                            <a href="{{ route('all-donate') }}" class="text-center">
                                {{ translate('Continue Donate')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="container mt-3 rtl my-3 text-align-direction" id="cart-summary">
    <div class="row">
        <div class="col-md-8">
            <div class="login-card">
                <div class="mx-auto __max-w-760">
                    <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">
                        {{ translate('Enter Details to Contribute') }}
                    </h2>

                    <form class="needs-validation" action="{{ route('donor-submit') }} " method="post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label font-semibold">{{ translate('Name') }}</label>
                                    <input class="form-control" type="text" name="user_name"
                                        value="{{ $customer['name'] }}" placeholder="Enter Donor Name">
                                    <input class="form-control" type="hidden" name="user_id"
                                        value="{{ $customer['id'] }}">
                                    <input class="form-control" type="hidden" name="id"
                                        value="{{ $id }}">
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="col-md-12" id="phone-div">
                                <div class="form-group">
                                    <label class="form-label font-semibold">{{ translate('phone_number') }}
                                        <small class="text-primary">(
                                            *{{ translate('country_code_is_must_like_for_IND') }} 91 )</small>
                                    </label>
                                    <input
                                        class="form-control text-align-direction phone-input-with-country-picker"
                                        type="tel"
                                        value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                        name="person_phone" id="person-number"
                                        placeholder="{{ translate('enter_phone_number') }}" required
                                        {{ isset($customer['phone']) ? 'readonly' : '' }}
                                        oninput="this.value=this.value.slice(0,10)">
                                    <input type="hidden" class="country-picker-phone-number w-50"
                                        name="person_phone" readonly>
                                    <p id="number-validation" class="text-danger" style="display: none">Enter
                                        Your Valid Mobile Number</p>
                                </div>
                            </div>
                            <hr class="my-2">

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label font-semibold">{{ translate('Pan Card') }}</label>
                                    <div class="form-group d-flex">
                                        <input class="form-control" type="text" id="pan_card" name="pan_card" placeholder="Pan Card (Optional)" onkeyup="formatPAN(this);validatePAN()" maxlength="10" style="text-transform: uppercase;">
                                        <button type="button" class="btn btn-sm btn-success pancard-verify-check" onclick="verifyPanCard()">verify</button>
                                    </div>
                                    <small id="pan_error" style="color: red; display: none;">❌ Invalid PAN Number (Format: ABCDE1234F)</small>
                                </div>
                            </div>

                        </div>

                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="web-direction">
                            <div class="mx-auto mt-4 __max-w-356">
                                <button class="w-100 btn btn--primary" id="submit_pan" type="submit">{{ translate('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
</script>

<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script>
    function validatePAN() {
        const panInput = document.getElementById("pan_card").value.toUpperCase();
        const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        const errorElement = document.getElementById("pan_error");

        if (panInput === "") {
            errorElement.style.display = "none";
            $("#submit_pan").attr('disabled', false);
        }else {
            errorElement.style.display = "block";
            $("#submit_pan").attr('disabled', true);
        }
    }

    function verifyPanCard() {
        const panInput = document.getElementById("pan_card").value.toUpperCase();
        const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        const errorElement = document.getElementById("pan_error");
        if (panInput === "") {
            errorElement.style.display = "none";
            $("#submit_pan").attr('disabled', false);
        } else if (panRegex.test(panInput)) {
            errorElement.style.display = "none";
            $("#submit_pan").attr('disabled', false);
            $('.pancard-verify-check').attr('disabled', true);
            $.ajax({
                url: "{{ url('api/v1/donate/pan-card-verified-check') }}",
                data: {
                    pancard: panInput,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('#loading').removeClass('d--none');
                    $('#loading').css('index', 1000);
                    $("#submit_pan").attr('disabled', true);
                },
                success: function(data) {
                    $('#loading').addClass('d--none');
                    if (data.status == 1) {
                        toastr.success(data.message);
                        $("#submit_pan").attr('disabled', false);
                        $('.pancard-verify-check').attr('disabled', true);
                        $('#pan_card').attr('readonly', true);
                    } else {
                        toastr.error(data.message);
                        $("#submit_pan").attr('disabled', true);
                        $('.pancard-verify-check').attr('disabled', false);
                        $('#pan_card').attr('readonly', false);
                    }
                }
            });
        } else {
            errorElement.style.display = "block";
            $("#submit_pan").attr('disabled', true);
        }
    }

    function formatPAN(input) {
        let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        let formatted = '';

        for (let i = 0; i < value.length && i < 10; i++) {
            if (i < 5) {
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i < 9) {
                if (/[0-9]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i === 9) {
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            }
        }
        input.value = formatted;
    }
</script>
@endpush