@extends('layouts.front-end.app')
@section('title', translate('Donate') )
@push('css_or_js')

<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
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

    @media (max-width: 768px) {
        .tabs-header {
            position: sticky;
            top: 0px;
            background-color: white;
            padding: 4px;
            z-index: 9;
        }

        .overflow-x-scroll {
            display: none !important;
        }
    }

    .inclu::before {
        content: '';
        position: absolute;
        left: 0;
        top: 12px;
        background: #DA1515;
        height: 30px;
        width: 5px;
        top: -1px;
    }

    a.section-link.active {
        color: #ffffff !important;
        background: var(--base) !important;
        font-weight: bold;

    }

    a.section-link {
        border-radius: 100px !important;
        padding: 9px 17px;
        /* font-size: 13px; */
        text-decoration: none;
    }

    .navbar_section1 {
        text-wrap: nowrap;
    }

    @media (max-width: 768px) {
        .otp-input-fields input {
            height: 40px;
            width: 40px;
        }

        .otp-input-fields {
            gap: 9px;
        }
    }
</style>
@endpush
@section('content')
<div class="w-full h-full sticky md:top-[68px] top-0 z-20">
    <div class="bg-bar w-full">
        <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
            <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                <div class="d-flex justify-center items-center pt-3 pb-3">
                    <div class="d-flex justify-center items-center">
                        <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                            <path d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z" fill="white"></path>
                        </svg>
                        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium  ">{{ translate('Add Details')}}</div>
                    </div>
                    <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z" fill="#9CA3AF"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z" fill="#9CA3AF"></path>
                        </svg>
                    </div>
                    <div class="d-flex justify-center items-center">
                        <div class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">2</div>
                        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">{{ translate('Donate')}}</div>
                    </div>
                    <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z" fill="#9CA3AF"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z" fill="#9CA3AF"></path>
                        </svg>
                    </div>
                    <div class="d-flex justify-center items-center">
                        <div class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">3</div>
                        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">{{ translate('Make Payment')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-3 rtl px-0 px-md-3 text-align-direction" id="cart-summary">
    <div class="__inline-23">
        <div class="container mt-4 rtl text-align-direction">
            <div class="row ">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-4 col-12">
                            <div class="cz-product-gallery">
                                <div class="cz-preview">
                                    <div class="product-thumbnail-single">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <img src="{{ $images }}" alt="" style="max-width: 100%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12 mt-md-0 mt-sm-3 web-direction">
                            <div class="details __h-100">
                                <span class="mb-2 __inline-24">{{ $donateList[str_replace('_', '-', app()->getLocale()).'_trust_name'] }} </span><br>
                                <span class="mb-2 __inline-24">{{ $donateList['name'] }} </span>


                                <!-- Profile Icon -->
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap justify-center items-center">
                                        <div class="w-full">
                                            <div class="w-full tray mb-3">
                                                <?php $totals_booking_user = $countdonate;
                                                if ($totals_booking_user <= 5) {
                                                    $show_user = 1;
                                                } else {
                                                    $show_user = 2;
                                                }
                                                ?>
                                                @if($show_user == 1)
                                                @for($ip=0;$ip < $totals_booking_user;$ip++)
                                                    <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img" style="background-image:url('{{  theme_asset(path: 'public/assets/user_list/user'.$ip.'.jpg')}}')">
                                                    </div>
                                            </div>
                                            @endfor
                                            @else
                                            @php
                                            $uniqueUsers = range(0, 13);
                                            shuffle($uniqueUsers);
                                            $selectedUsers = array_slice($uniqueUsers, 0, 5);
                                            @endphp
                                            @foreach($selectedUsers as $random_user)
                                            <div class="relative circle-img-container">
                                                <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                    style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user'.$random_user.'.jpg') }}')">
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
                                        <div class=""><span class=" inline-flex break-normal">{{ translate('Till now')}}</span><span class=" font-bold text-#F18912 ml-1 break-normal">
                                                {{ 10000 + ($totals_booking_user??0) }}+
                                            </span>
                                            <span class="mr-1 inline-flex break-normal">{{ translate("devotees are getting blessings by donating on Mahakal.com. You too join this pious work and get the blessings of Mahakal")}}!</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Button -->
                            <div id="" role="button">
                                @if (auth('customer')->check())
                                <a href="javascript:void(0);" id="auth-book-now"
                                    class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('Donate Now')}}</a>
                                @else
                                <a href="javascript:void(0);" id="participate-btn"
                                    class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('Donate Now')}}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <div class="card card-body px-4 pb-3 mb-3 __rounded-10 pt-3">
                    <div class="navbar_section1 section-links justify-content-between mt-3 border-top border-bottom py-2 mb-4 small" style="    overflow: auto;">
                        <a class="section-link ml-2 active" href="#about_details">{{ translate('about')}}</a>
                        <a class="section-link" href="#donatefaq">{{ translate('faqs') }}</a>
                    </div>
                    <div class="content-sections px-lg-3">
                        <!-- Inclusion Section -->
                        <div class="section-content active" id="about_details">
                            <div class="row p-3 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922);border-radius: 5px; border-bottom: 3px solid transparent;">
                                <div class="col-12 p-2">
                                    {!! $donateList['description'] !!}
                                </div>
                            </div>
                        </div>
                        <div class="section-content active" id="donatefaq">
                            <div class="row p-2 mt-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                                <div class="col-md-12">
                                    <h5 class="inclu font-weight-bolder" style="color:#e74c3c">&nbsp;{{ translate('faqs') }}</h5>
                                </div>
                                @if($faqs)
                                <div class="col-12">
                                    @foreach($faqs as $faq)
                                    <div class="row pt-2 specification">
                                        <div class="col-12 col-md-12 col-lg-12">
                                            <div class="accordion" id="accordionExample">
                                                <div class="cards">
                                                    <div class="card-header" id="heading{{$faq->id}}">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link btn-block  text-left btnClr" type="button" data-toggle="collapse" data-target="#collapse{{$faq->id}}" aria-expanded="true" aria-controls="collapseOne" style="white-space: normal;">
                                                                {{$faq->question}}
                                                            </button>
                                                        </h2>
                                                    </div>
                                                    <div id="collapse{{$faq->id}}" class="collapse" aria-labelledby="heading{{$faq->id}}" data-parent="#accordionExample">
                                                        <div class="card-body">
                                                            {!! $faq->detail !!}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
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
                        <span class="text-18 font-bold ml-2">{{ translate('Fill in your details')}}</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr class="bg-[#E6E4EB] w-full">
                <div class="modal-body flex justify-content-center">
                    <div id="recaptcha-container"></div>
                    <div class="w-full mt-1 px-2">
                        <span class="block text-[16px] font-bold text-gray-900 dark:text-white">{{ translate('Enter Your Whatsapp Mobile Number')}}</span>
                        <span class="text-[12px] font-normal text-[#707070]">{{ translate('Your donation information will be sent to the WhatsApp number given below')}}...</span>
                        <!-- Model Form -->
                        <div class="w-full mr-9 px-0 pt-3">
                            <form class="needs-validation_" id="lead-store-form" action="{{ route('donate-leads',[$ids])}}" method="post">
                                @csrf
                                @php
                                if (auth('customer')->check()) {
                                $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                }
                                @endphp
                                <input type="hidden" name="trust_id" value="{{ ($donateList['trust_id']??'') }}">
                                <input type="hidden" name="ads_id" value="{{ ($donateList['id']??'') }}">
                                <input type="hidden" name="verify_otp" id="phone-number-valid">
                                <div class="row">
                                    <div class="col-md-12" id="phone-div">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                <small class="text-primary">( *{{ translate('country_code_is_must_like_for_IND') }} 91 )</small>
                                            </label>
                                            <input
                                                class="form-control text-align-direction phone-input-with-country-picker"
                                                type="tel"
                                                value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}" name="person_phone" id="person-number" placeholder="{{ translate('enter_phone_number') }}" required
                                                {{ isset($customer['phone']) ? 'readonly' : '' }} oninput="this.value=this.value.slice(0,10)">

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
                                                required {{ isset($customer['f_name']) ? 'readonly' : '' }}>
                                            <p id="name-validation" class="text-danger" style="display: none">Enter Your Name</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="otp-input-div" style="display: none;">
                                        <div class="form-group text-center">
                                            <label
                                                class="form-label font-semibold ">{{ translate('enter_OTP') }}</label>
                                            <div class="otp-input-fields">
                                                <input type="number" id="otp1" class="otp__digit otp__field__1"
                                                    inputmode="number">
                                                <input type="number" id="otp2" class="otp__digit otp__field__2"
                                                    inputmode="number">
                                                <input type="number" id="otp3" class="otp__digit otp__field__3"
                                                    inputmode="number">
                                                <input type="number" id="otp4" class="otp__digit otp__field__4"
                                                    inputmode="number">
                                                <input type="number" id="otp5" class="otp__digit otp__field__5"
                                                    inputmode="number">
                                                <input type="number" id="otp6" class="otp__digit otp__field__6"
                                                    inputmode="number">
                                            </div>
                                            <p id="otpValidation" class="text-danger"></p>

                                        </div>
                                    </div>
                                    <div class="mx-auto mt-1 __max-w-356" id="send-otp-btn-div">
                                        <button type="button" class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold one-time-otp-sends" id="send-otp-btn"> {{ translate('send_OTP') }} </button>
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
                                    <p id="resend-otp-timer-text" style="display: none"> Resend OTP in <span id="resend-otp-timer"></span></p>
                                    <p id="resend-otp-btn-text" style="display: none">Didn't get the code? <a href="javascript:0" id="resend-otp-btn" style="color: blue;">Resend Otp</a></p>
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

<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js"></script>

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
</script>

<script>
    const firebaseConfig = {
        apiKey: "{{env('FIREBASE_APIKEY')}}",
        authDomain: "{{env('FIREBASE_AUTHDOMAIN')}}",
        projectId: "{{env('FIREBASE_PRODJECTID')}}",
        storageBucket: "{{env('FIREBASE_STROAGEBUCKET')}}",
        messagingSenderId: "{{env('FIREBASE_MESSAGINGSENDERID')}}",
        appId: "{{env('FIREBASE_APPID')}}",
        measurementId: "{{env('FIREBASE_MEASUREMENTID')}}"
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
        var phoneNumber = $('.country-picker-phone-number').val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
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
            var phoneNumber = $('.country-picker-phone-number').val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
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

        var phoneNumber = $('.country-picker-phone-number').val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
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
                    // $('#submit').text('Submit');
                    // $('#submit').prop('disabled', false);
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
            return;
        }
        console.log(mobile);
        // var notmob = code1 + '' + mobile;
        // console.log(code);
        // console.log(mobile);
        $.ajax({
            type: "get",
            url: "{{url('account-counselling-order-user-name')}}" + "/" + code1,
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
                    $('.one-time-otp-sends').text("{{ translate('donate_now') }}");
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

{{-- auth book now btn click --}}
<script>
    $('#auth-book-now').click(function(e) {
        e.preventDefault();
        $('#lead-store-form').submit();
    });

    $(function() {
        $('.section-link').on('click', function(e) {
            e.preventDefault();
            const targetId = $(this).attr('href');
            const targetOffset = $(targetId).offset().top - $('.navbar_section1').outerHeight() - 100;

            $('html, body').animate({
                scrollTop: targetOffset
            }, 200);
        });

        $(window).on('scroll', function() {
            let screenWidth = $(window).width();
            const scrollTop = $(window).scrollTop() + $('.navbar_section1').outerHeight() + 100;

            if (scrollTop > 900) {
                $('.navbar-stuck-toggler').removeClass('show');
                $('.navbar-stuck-menu').removeClass('show');
                if (screenWidth <= 768) {
                    $(".navbar_section1").css({
                        'top': '0px',
                    });
                } else {
                    $(".navbar_section1").css({
                        'top': '84px',
                    });
                }
                $(".navbar_section1").css({
                    "position": "sticky",
                    'background-color': '#fff',
                    'z-index': '1000',
                    'box-shadow': '0 2px 10px rgba(0, 0, 0, 0.1)',
                    'overflow': 'auto',
                    "text-wrap": "nowrap",
                });
            } else {
                $(".navbar_section1").css({
                    'position': 'static',
                    "text-wrap": "nowrap",
                    'box-shadow': 'none'
                });
            }

            $('.section-content').each(function() {
                const sectionTop = $(this).offset().top - 50;
                const sectionBottom = sectionTop + $(this).outerHeight();
                const sectionId = $(this).attr('id');
                const navLink = $(`.section-link[href="#${sectionId}"]`);

                if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                    $('.section-link').removeClass('active');
                    navLink.addClass('active');
                }
            });
        });
    });
</script>

@endpush