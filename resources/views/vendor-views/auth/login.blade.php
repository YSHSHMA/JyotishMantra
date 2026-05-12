@php
    use App\Enums\DemoConstant;
    $host = request()->getHost();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ translate('vendor_Login') }}</title>
    <link rel="shortcut icon"
        href="{{ dynamicStorage(path: 'storage/app/public/company/' . getWebConfig(name: 'company_fav_icon')) }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/google-fonts.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/vendor/icon-set/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/theme.minc619.css?v=1.0') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/custom.css') }}">
</head>

<body>

    <main id="content" role="main" class="main">
        <div class="row">
            <div class="col-12 position-fixed z-9999 mt-10rem">
                <div id="loading" class="d--none">
                    <div id="loader"></div>
                </div>
            </div>
        </div>
        <div class="position-fixed top-0 right-0 left-0 bg-img-hero __h-32rem">
            <figure class="position-absolute right-0 bottom-0 left-0">
                <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                    viewBox="0 0 1921 273">
                    <polygon fill="#fff" points="0,273 1921,273 1921,0 " />
                </svg>
            </figure>
        </div>
        <div id="recaptcha-container"></div>
        <div class="container py-5 py-sm-7">
            @php($companyWebLogo = getWebConfig(name: 'company_web_logo'))
            <a class="d-flex justify-content-center mb-5" href="javascript:">
                <img class="z-index-2" height="40"
                    src="{{ getValidImage(path: 'storage/app/public/company/' . $companyWebLogo, type: 'backend-logo') }}"
                    alt="{{ translate('logo') }}">
            </a>
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="card card-lg mb-5">
                        <div class="card-body">
                            <form action="{{ route('vendor.auth.login') }}" method="post" id="vendor-login-form">
                                @csrf
                                <input type="hidden" name="" id="mobile">
                                <div class="text-center">
                                    <div class="mb-5">
                                        <h1 class="display-4">{{ translate('sign_in') }}</h1>
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">
                                                {{ translate('welcome_back_to_vendor_login') }}</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="js-form-message form-group">
                                    <label class="input-label"
                                        for="signingVendorEmail">{{ translate('your_email') }}</label>
                                    <input type="email" class="form-control form-control-lg" name="email"
                                        id="signingVendorEmail" tabindex="1" placeholder="email@address.com"
                                        aria-label="email@address.com" required
                                        data-msg="Please enter a valid email address.">
                                </div>
                                <div class="js-form-message form-group">
                                    <label class="input-label" for="signingVendorPassword" tabindex="0">
                                        <span class="d-flex justify-content-between align-items-center">
                                            {{ translate('password') }}
                                            <a href="{{ route('vendor.auth.forgot-password.index') }}">
                                                {{ translate('forgot_password') }}
                                            </a>
                                        </span>
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" class="js-toggle-password form-control form-control-lg"
                                            name="password" id="signingVendorPassword"
                                            placeholder="8+ characters required" aria-label="8+ characters required"
                                            required data-msg="Your password is invalid. Please try again."
                                            data-hs-toggle-password-options='{
                                                         "target": "#changePassTarget",
                                                "defaultClass": "tio-hidden-outlined",
                                                "showClass": "tio-visible-outlined",
                                                "classChangeTarget": "#changePassIcon"
                                                }'>
                                        <div id="changePassTarget" class="input-group-append">
                                            <a class="input-group-text" href="javascript:">
                                                <i id="changePassIcon" class="tio-visible-outlined"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="termsCheckbox"
                                            name="remember">
                                        <label class="custom-control-label text-muted user-select-none"
                                            for="termsCheckbox">
                                            {{ translate('remember_me') }}
                                        </label>
                                    </div>
                                </div>
                                @if (isset($recaptcha) && $recaptcha['status'] == 1)
                                    <div id="recaptcha_element" class="w-100" data-type="image"></div>
                                    <br />
                                @else
                                    <div class="row py-2">
                                        <div class="col-6 pr-0">
                                            <input type="text" class="form-control __h-40 border-0"
                                                name="vendorRecaptchaKey" value=""
                                                placeholder="{{ translate('enter_captcha_value') }}"
                                                autocomplete="off">
                                        </div>
                                        <div class="col-6 input-icons mb-2 w-100 rounded bg-white">
                                            <a class="d-flex align-items-center align-items-center get-login-recaptcha-verify"
                                                data-link="{{ URL('/vendor/auth/recaptcha') }}">
                                                <img src="{{ URL('/vendor/auth/recaptcha/1?captcha_session_id=vendorRecaptchaSessionKey') }}"
                                                    alt="" class="rounded __h-40" id="default_recaptcha_id">
                                                <i class="tio-refresh position-relative cursor-pointer p-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if (in_array($host, ['mahakal.com', 'www.mahakal.com']))
                                    <div class="d-none" id="auth-div">
                                        <div class="mb-3">
                                            <label for="auth-select" class="form-label">Auth</label>
                                            <select name="" id="auth-select" class="form-control">
                                                <option value="">Select Auth</option>
                                                <option value="google">Google Auth</option>
                                                <option value="firebase">Login with OTP</option>
                                            </select>
                                            <small class="text-danger d-none" id="auth-error">Please Select
                                                Auth</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="otp" class="form-label">OTP</label>
                                            <input type="text" class="form-control" id="otp" name="otp"
                                                placeholder="Enter OTP" autofocus>
                                            <small class="text-danger d-none" id="otp-error">Please Enter OTP</small>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-lg btn-block btn--primary"
                                        id="submit-login-check">
                                        {{ translate('login') }}
                                    </button>

                                    <button type="button" class="btn btn-lg btn-block btn--primary d-none"
                                        id="submit-vendor-button">
                                        {{ translate('login') }}
                                    </button>
                                @else
                                    <div class="mb-3">
                                        <label for="otp" class="form-label">OTP</label>
                                        <input type="number" class="form-control" id="otp" name="otp"
                                            placeholder="Enter OTP" autofocus>
                                        <small class="text-danger d-none" id="otp-error"></small>
                                    </div>
                                    <button type="button" class="btn btn-lg btn-block btn--primary"
                                        id="submit-prod-vendor-button">
                                        {{ translate('login') }}
                                    </button>
                                @endif
                                <button type="button"
                                    class="btn btn-lg btn-block btn--primary submit-login-form d-none">{{ translate('login') }}</button>
                            </form>
                            <div class="mt-2">Don't have account yet? <a
                                    href="{{ route('vendor.auth.registration.index') }}">{{ translate('Sign up') }}</a>
                            </div>
                        </div>
                        @if (env('APP_MODE') == 'demo')
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-10">
                                        <span id="vendor-email"
                                            data-email="{{ DemoConstant::VENDOR['email'] }}">{{ translate('email') }}
                                            : {{ DemoConstant::VENDOR['email'] }}</span><br>
                                        <span id="vendor-password"
                                            data-password="{{ DemoConstant::VENDOR['password'] }}">{{ translate('password') }}
                                            : {{ DemoConstant::VENDOR['password'] }}</span>
                                    </div>
                                    <div class="col-2">
                                        <button class="btn btn--primary" id="copyLoginInfo"><i class="tio-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="vendor-suspend suspended-message d-none">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/warning.png') }}" alt="">
            <div class="cont">
                <h6>{{ translate('warning') }}</h6>
                <div>
                    {{ translate('your_account_has_been_suspended') . ', ' . translate('please_contact_with') }} <a
                        href="{{ route('contacts') }}">{{ translate('help_and_support') }}</a>
                </div>
            </div>
            <button class="p-2 m-0 border-0 outlie-0 shadow-none bg-transparent clear-alter-message">
                <i class="tio-clear"></i>
            </button>
        </div>
        <div class="vendor-suspend pending-message d-none">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/warning.png') }}" alt="">
            <div class="cont">
                <h6>{{ translate('warning') }}</h6>
                <div>
                    {{ translate('your_account_is_not_approved_yet') . ', ' . translate('please_wait_or_contact_with') }}
                    <a href="{{ route('contacts') }}">{{ translate('help_and_support') }}</a>
                </div>
            </div>
            <button class="p-2 m-0 border-0 outlie-0 shadow-none bg-transparent clear-alter-message">
                <i class="tio-clear"></i>
            </button>
        </div>
    </main>
    <span id="message-please-check-recaptcha" data-text="{{ translate('please_check_the_recaptcha') }}"></span>
    <span id="message-copied_success" data-text="{{ translate('copied_successfully') }}"></span>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/theme.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/toastr.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor/login.js') }}"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>
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

    {!! Toastr::message() !!}
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
</body>

{{-- login check --}}

<script type="text/javascript">
    $('#submit-login-check').on('click', function() {
        var email = $('#signingVendorEmail').val();
        if (email === "") {
            toastr.error('please enter email', {
                CloseButton: true,
                ProgressBar: true
            });
            return;
        }

        let data = {
            _token: '{{ csrf_token() }}',
            email: email,
        };

        $.ajax({
            type: "post",
            url: "{{ route('vendor.auth.2fa.login.check') }}",
            data: data,
            success: function(response) {
                if (response.status == 200) {
                    if (response.vendor.enable_2fa != 1) {
                        $("#auth-select option[value='google']").prop("disabled", true);
                    }
                    $('#mobile').val(response.vendor.phone);
                    $('#auth-div').removeClass('d-none');
                    $('#submit-login-check').addClass('d-none');
                    $('#submit-vendor-button').removeClass('d-none');
                } else {
                    toastr.error(response.message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });
    });
</script>


<script type="text/javascript">
    // auth select on change
    var confirmationResult = "";
    var appVerifier = "";

    $('#auth-select').change(function(e) {
        e.preventDefault();

        var auth = $(this).val();
        if (auth === "") {
            $('#auth-error').removeClass('d-none');
            return;
        } else {
            $('#auth-error').addClass('d-none');

            if (auth === 'firebase') {
                var number = $('#mobile').val();
                if (number === "") {
                    toastr.error('vendor mobile no is not valid', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    return;
                } else {
                    toastr.success('sending otp to mobile no');
                    if (appVerifier == "") {
                        appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                            size: 'invisible'
                        });
                    }

                    firebase.auth().signInWithPhoneNumber(number, appVerifier).then(function(
                            confirmation) {
                            confirmationResult = confirmation;
                            toastr.success('otp sent successfully');
                        })
                        .catch(function(error) {
                            toastr.error('Failed to send OTP. Please try again');
                            console.error('OTP sending error:', error);
                        });
                }
            }
        }

    });

    // login vendor
    $('#submit-vendor-button').on('click', function() {
        var email = $('#signingVendorEmail').val();
        var auth = $('#auth-select').val();
        var otp = $('#otp').val().trim();

        if (auth === "") {
            $('#auth-error').removeClass('d-none');
            return;
        } else {
            $('#auth-error').addClass('d-none');
        }

        if (otp === "") {
            $('#otp-error').removeClass('d-none');
            return;
        } else {
            $('#otp-error').addClass('d-none');
        }

        let data = {
            _token: '{{ csrf_token() }}',
            email: email,
            otp: otp,
        };

        if (auth === 'google') {
            $.ajax({
                type: "post",
                url: "{{ route('vendor.auth.2fa.login.submit') }}",
                data: data,
                success: function(response) {
                    if (response.status == 200) {
                        $('.submit-login-form').click();
                    } else {
                        toastr.error(response.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                }
            });
        } else {
            toastr.success('verifying OTP');
            if (confirmationResult) {
                // confirmationResult.confirm(otp).then(function(result) {
                //         $('.submit-login-form').click();
                //     })
                //     .catch(function(error) {
                //         toastr.error('Incorrect OTP');
                //         return;
                //     });
                confirmationResult.confirm(otp)
                    .then(function(result) {
                        toastr.success('OTP verified successfully');
                        $('.submit-login-form').click();
                    })
                    .catch(function(error) {
                        console.error("OTP Verification Failed:", error.code, error.message);
                        toastr.error('OTP verification failed');
                    });
            }
        }
    });

    //prod login vendor
    $('#submit-prod-vendor-button').on('click', function() {
        var otp = $('#otp').val().trim();

        if (otp === "") {
            $('#otp-error').removeClass('d-none').text('Please Enter OTP');
            return;
        } else if (otp != "123456") {
            $('#otp-error').removeClass('d-none').text('Incorrect OTP');
            return;
        } else {
            $('#otp-error').addClass('d-none');
            $('.submit-login-form').click();
        }
    });
</script>

</html>
