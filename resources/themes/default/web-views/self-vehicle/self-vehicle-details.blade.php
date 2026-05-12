@extends('layouts.front-end.app')

@section('title', translate('self_vehicle_booking'))

@push('css_or_js')
<meta property="og:image" content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta property="twitter:card" content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<style>

</style>

@endpush
@section('content')
<div class="container mt-3 rtl px-0 px-md-3 text-align-direction mb-2" id="cart-summary">
    <div class="__inline-23">
        <div class="container mt-4 rtl text-align-direction">
            <div class="row">
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
                                                        <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/self_driving/' . ($SelfVehicles['thumbnail'] ?? ''), type: 'backend-product') }}" alt="">
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
                                <span class="__inline-24">{{ $SelfVehicles['getCabId']['name']??'' }} {{ $SelfVehicles['getCategory']['brand_name']??'' }}</span>

                                <!-- Profile Icon -->
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap justify-center items-center">
                                        <div class="w-full">
                                            <div class="w-full tray mb-3">
                                                <?php $totals_booking_user = ($SelfVehicles['tour_order_review_count'] ?? 0);
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
                                                    {{ 10000 + $SelfVehicles['tour_order_review_count'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span
                                                class="text-">{{ translate('satisfied travelers who have already booked through Mahakal.com. Experience the freedom of self-drive with our trusted vehicles—ensuring a smooth, spiritual, and hassle-free journey.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Button -->
                            <div id="" role="button">
                                @if (auth('customer')->check())
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
                            <form class="needs-validation_" id="lead-store-form" action="{{ route('self-driving-lead',['id'=>($SelfVehicles['id'] ?? '')])}}" method="post">
                                @csrf
                                @php
                                if (auth('customer')->check()) {
                                $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                }
                                @endphp
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
                                                name="person_phone" id="person-number" placeholder="{{ translate('enter_phone_number') }}" required {{ isset($customer['phone']) ? 'readonly' : '' }} oninput="this.value=this.value.slice(0,10)">

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
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
</script>

<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>
<script>
    $('#auth-book-now').click(function(e) {
        e.preventDefault();
        $('#lead-store-form').submit();
    });
</script>

<script>
    // const firebaseConfig = {
    //     apiKey: "{{ env('FIREBASE_APIKEY') }}",
    //     authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
    //     projectId: "{{ env('FIREBASE_PRODJECTID') }}",
    //     storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
    //     messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
    //     appId: "{{ env('FIREBASE_APPID') }}",
    //     measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
    // };
    const firebaseConfig = {
            apiKey: "AIzaSyBrrMSAtiASPJGKt0aAQqpIYXoFUG4QGv8",
            authDomain: "rizrv-65a76.firebaseapp.com",
            projectId: "rizrv-65a76",
            storageBucket: "rizrv-65a76.appspot.com",
            messagingSenderId: "832249240595",
            appId: "1:832249240595:web:083a86aed6f3f50fa133e6",
            measurementId: "G-GXM9FXY2WM"
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
@endpush