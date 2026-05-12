@extends('layouts.front-end.app')

@section('title', translate('sign_in'))
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <style>
        .otp-input-fields {
            margin: auto;
            max-width: 400px;
            width: auto;
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 10px;
        }

        .otp-input-fields input {
            height: 42px;
            width: 42px;
            background-color: transparent;
            border-radius: 4px;
            border: 1px solid #2f8f1f;
            text-align: center;
            outline: none;
            font-size: 18px;
            /* Firefox */
        }

        #timer {
            font-size: 18px;
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
    </style>
@endpush
@section('content')
    <div class="container py-4 py-lg-5 my-4 text-align-direction">
        <div class="login-card">
            <div class="mx-auto __max-w-360">
                <h2 class="text-center h4 mb-4 font-bold text-capitalize fs-18-mobile">{{ translate('sign_in') }}</h2>

                <ul class="nav nav-pills nav-justified" id="linxea-avenir" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold text-dark active" id="withotp-tab" data-toggle="tab" href="#withotp" role="tab" aria-controls="second" aria-selected="false" style="color: #222 !important"> {{ translate('Sign_With_OTP') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold " id="withPassword-tab" data-toggle="tab" href="#withPassword" role="tab" aria-controls="first" aria-selected="true" style="color: #222 !important">{{ translate('sign_password') }}</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                     {{-- With OTP --}}
                     <div class="tab-pane fade show active" id="withotp" role="tabpanel" aria-labelledby="withotp-tab">
                        <div id="recaptcha-container"></div>
                        <form class="needs-validation mt-2" autocomplete="off"  action="{{ route('customer.auth.loginotp') }}" method="post" id="customer-login-OTP">
                            @csrf
                            <div class="form-group" id="phone-div">
                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                    <small class="text-primary">( *
                                        {{ translate('country_code_is_must_like_for_IND') }} 91
                                        )</small>
                                </label>
                                <input class="form-control text-align-direction phone-input-with-country-picker"
                                    type="tel" name="person_phone" id="person-number"
                                    placeholder="{{ translate('phone_number') }}" inputmode="number" required
                                    maxlength="10" minlength="10" input-mode="number">
                                <input type="hidden" class="country-picker-phone-number w-50" name="person_phone"
                                    id="phone-code" readonly>
                                <p id="number-validation" class="text-danger" style="display: none">
                                    {{ translate('enter_your_valid_mobile_number') }}</p>
                            </div>
                            <div class="col-md-12" id="otp-input-div" style="display: none;">
                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                    <small class="text-primary">{{ translate('An_OTP_has_been_sent_to') }}</small>
                                </label>
                                <div class="form-group text-center">
                                 
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
                            <div class="" id="with-otp-div">
                                <button class="btn btn--primary btn-block btn-shadow" type="button" id="with-otp">{{ translate('Send_OTP') }}</button>
                            </div>
                            <div class="" id="verify-otp-btn-div" style="display: none">
                                <div class="d-flex">
                                    <button type="button" class="btn btn--primary btn-block btn-shadow mt-0 m-1" id="otp-back-btn">  {{ translate('back') }} </button>
                                    <button type="submit" class="btn btn--primary btn-block btn-shadow mt-0 m-1" id="verify-otp-btn" disabled>{{ translate('verify_OTP') }} </button>
                                </div>
                            </div>
                            <div class="text-center mt-3" id="resend-div" style="display: none;">
                                <p id="resend-otp-timer-text" style="display: none">
                                    {{ translate('Resend_OTP_in') }} <span id="resend-otp-timer"></span></p>
                                <p id="resend-otp-btn-text" style="display: none">
                                    {{ translate('Did_not_get_the_code?') }}
                                    <a href="javascript:0" id="resend-otp-btn"
                                        style="color: blue;">{{ translate('Resend_OTP') }}</a>
                                </p>
                                <br>
                            </div>
                        </form>
                    </div>
                    {{-- With Password --}}
                    <div class="tab-pane fade" id="withPassword" role="tabpanel"
                        aria-labelledby="withPassword-tab">
                        <form class="needs-validation mt-2" autocomplete="off" action="{{ route('customer.auth.login') }}"
                            method="post" id="customer-login-form">
                            @csrf
                            <div class="form-group">
                                <label class="form-label font-semibold">
                                    {{ translate('email') }} 
                                </label>
                                <input class="form-control text-align-direction" type="text" name="user_id"
                                    id="si-email" value="{{ old('user_id') }}"
                                    placeholder="{{ translate('enter_email_address') }}" required>
                                <input type="hidden" class="form-control" name="fcm_token" id="fcm_token_id">
                                <div class="invalid-feedback">{{ translate('please_provide_valid_email_or_phone_number') }}
                                    .</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label font-semibold">{{ translate('password') }}</label>
                                <div class="password-toggle rtl">
                                    <input class="form-control text-align-direction" name="password" type="password"
                                        id="si-password" placeholder="{{ translate('password_must_be_7+_Character') }}"
                                        required>
                                    <label class="password-toggle-btn">
                                        <input class="custom-control-input" type="checkbox">
                                        <i class="tio-hidden password-toggle-indicator"></i>
                                        <span class="sr-only">{{ translate('show_password') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group d-flex flex-wrap justify-content-between">
                                <div class="rtl">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="custom-control-label text-primary"
                                            for="remember">{{ translate('remember_me') }}</label>
                                    </div>
                                </div>
                                <a class="font-size-sm text-primary text-underline"
                                    href="{{ route('customer.auth.recover-password') }}">
                                    {{ translate('forgot_password') }}?
                                </a>
                            </div>
                            @php($recaptcha = getWebConfig(name: 'recaptcha'))
                            @if (isset($recaptcha) && $recaptcha['status'] == 1)
                                <div id="recaptcha_element" class="w-100" data-type="image"></div>
                                <br />
                            @else
                                <div class="row py-2">
                                    <div class="col-6 pr-2">
                                        <input type="text" class="form-control border __h-40"
                                            name="default_recaptcha_id_customer_login" value=""
                                            placeholder="{{ translate('enter_captcha_value') }}" autocomplete="off">
                                    </div>
                                    <div class="col-6 input-icons mb-2 w-100 rounded bg-white">
                                        <a href="javascript:"
                                            class="d-flex align-items-center align-items-center get-login-recaptcha-verify"
                                            data-link="{{ URL('/customer/auth/code/captcha') }}">
                                            <img src="{{ URL('/customer/auth/code/captcha/1?captcha_session_id=default_recaptcha_id_customer_login') }}"
                                                class="input-field rounded __h-40" id="customer_login_recaptcha_id"
                                                alt="">
                                            <i class="tio-refresh icon cursor-pointer p-2"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                            <button class="btn btn--primary btn-block btn-shadow"
                                type="submit">{{ translate('sign_in') }}</button>
                        </form>
                    </div>
                   
                </div>
                @if ($web_config['social_login_text'])
                    <div class="text-center m-3 text-black-50">
                        <small>{{ translate('or_continue_with') }}</small>
                    </div>
                @endif
                <div class="d-flex justify-content-center my-3 gap-2">
                    @foreach (getWebConfig(name: 'social_login') as $socialLoginService)
                        @if (isset($socialLoginService) && $socialLoginService['status'])
                            <div>
                                <a class="d-block"
                                    href="{{ route('customer.auth.service-login', $socialLoginService['login_medium']) }}">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/icons/' . $socialLoginService['login_medium'] . '.png') }}"
                                        alt="">
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="text-black-50 text-center">
                    <small>
                        {{ translate('Enjoy_New_experience') }}
                        <a class="text-primary text-underline" href="{{ route('customer.auth.sign-up') }}">
                            {{ translate('sign_up') }}
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    @if (isset($recaptcha) && $recaptcha['status'] == 1)
        <script type="text/javascript">
            "use strict";
            var onloadCallback = function() {
                grecaptcha.render('recaptcha_element', {
                    'sitekey': '{{ getWebConfig(name: 'recaptcha')['site_key'] }}'
                });
            };
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
    @endif
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    {{-- <script src="https://www.gstatic.com/firebasejs/9.1.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.1.2/firebase-messaging.js"></script> --}}
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>

    <script type="module">
        // Import Firebase functions
        // import {
        //     initializeApp
        // } from 'https://www.gstatic.com/firebasejs/9.1.2/firebase-app.js';
        // import {
        //     getMessaging,
        //     getToken
        // } from 'https://www.gstatic.com/firebasejs/9.1.2/firebase-messaging.js';

        // Firebase configuration
       
        // const app = initializeApp(firebaseConfig);

        // // Get Firebase Messaging instance
        // const messaging = getMessaging(app);

        // // Request permission and get token
        // messaging.requestPermission()
        //     .then(() => {
        //         return getToken(messaging, {
        //             vapidKey: "{{ env('VAPID_KEY') }}"
        //         });
        //     })
        //     .then((token) => {
        //         console.log("FCM Token:", token);
        //         $('#fcm_token_id').val(token);
        //     })
        //     .catch((error) => {
        //         console.error("Unable to get permission or fetch token", error);
        //     });
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
        var confirmationResult;
        var appVerifier = "";
        var sendOtpCount = 1;
        $('#with-otp').click(function(e) {
            e.preventDefault();
            var phoneNumber = $('#phone-code').val();
            checkmobile();
           
        });

        function sendotp() {
            var number = $('#person-number').val();
            if (number == "" || number.length != 10) {
                $('#number-validation').show();
            } else {
                $('#with-otp').text('Please Wait ...');
                $('#with-otp').prop('disabled', true);
                var phoneNumber = '+91' + $('#person-number').val();
                if (appVerifier == "") {
                    appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                        size: 'invisible'
                    });
                }
                firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                        $('#number-validation').hide();
                        $('#with-otp-div').css('display', 'none');
                        $('#phone-div').css('display', 'none');
                        $('#otp-input-div').css('display', 'block');
                        $('#verify-otp-btn-div').css('display', 'block');
                        if (sendOtpCount == 1) {
                            sendOtpCount = 2;
                            otpTimer();
                        }
                        confirmationResult = confirmation;
                        toastr.success('OTP sent successfully');
                        $('#with-otp').prop('disabled', true);
                        $('#resend-div').show();
                    })
                    .catch(function(error) {
                        toastr.error('Failed to send OTP. Please try again.');
                        $('#with-otp').text('Send OTP');
                        $('#with-otp').prop('disabled', false);
                        console.error('OTP sending error:', error);
                    });
            }
        }
        $('#verify-otp-btn').click(function(e) {
            e.preventDefault();
            toastr.success('Please Wait...');
            var number = $('#person-number').val();
            var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
                $('#otp6').val();
            if (confirmationResult) {
                confirmationResult.confirm(otp).then(function(result) {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        $('#customer-login-OTP').submit();
                    })
                    .catch(function(error) {
                        $('#otpValidation').text('Incorrect OTP');
                    });
            }


        });
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

            var phoneNumber = '+91 ' + $('#person-number').val();
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
                    toastr.success('Failed to send OTP. Please try again.');
                });
        });
        $('#otp-back-btn').click(function(e) {
            e.preventDefault();
            $('#with-otp-div').css('display', 'block');
            $('#phone-div').css('display', 'block');
            $('#otp-input-div').css('display', 'none');
            $('#verify-otp-btn-div').css('display', 'none');
            $('#with-otp').prop('disabled', false);
            $('#resend-div').hide();
            $('#with-otp').text('Send OTP');
        });
        // check the number exist
        function checkmobile() {
            var mobile = $('#person-number').val(); 
            var code = $('.iti__selected-dial-code').text();
                var no = code + mobile; 
                $.ajax({
                    type: "get",
                    url: "{{ url('account-service-order-user-name') }}" + "/" + no,
                    success: function(response) {
                        if (response.status === 200) {                        
                            $('#with-otp').prop('disabled', false);
                            sendotp();
                        } else {                         
                            toastr.error('The entered number does not exist in our records.');
                            $('#with-otp').prop('disabled', false); 
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('An error occurred while checking the number. Please try again.');
                        $('#with-otp').prop('disabled', true);
                    }
                }); 
        }

    </script>
    {{-- show pooja venue --}}
    <!-- OTP SECTION -->
    <script type="text/javascript">
        var otp_inputs = document.querySelectorAll(".otp__digit");
        const verifyOtpBtn = document.getElementById('verify-otp-btn');

        function checkOtpFields() {
            let allFilled = true;
            otp_inputs.forEach(field => {
                if (field.value === '') {
                    allFilled = false;
                }
            });
            verifyOtpBtn.disabled = !allFilled;
        }
        otp_inputs.forEach(field => {
            field.addEventListener('input', checkOtpFields);
        });
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
@endpush
