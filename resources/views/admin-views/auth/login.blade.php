@php
    $host = request()->getHost();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ translate($role) }} | {{ translate('login') }}</title>

    <link rel="shortcut icon"
        href="{{ dynamicStorage(path: 'storage/app/public/company/' . getWebConfig(name: 'company_fav_icon')) }}">

    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/google-fonts.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/vendor/icon-set/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/theme.minc619.css?v=1.0') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/toastr.css') }}">
</head>

<body>
    <div id="recaptcha-container"></div>
    <main id="content" role="main" class="main">
        <div class="position-fixed top-0 right-0 left-0 bg-img-hero __inline-1">
            <figure class="position-absolute right-0 bottom-0 left-0">
                <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                    viewBox="0 0 1921 273">
                    <polygon fill="#fff" points="0,273 1921,273 1921,0 " />
                </svg>
            </figure>
        </div>
        <div class="container py-5 py-sm-7">
            <label class="badge badge-soft-success float-right __inline-2">{{ translate('software_version') }}
                : {{ env('SOFTWARE_VERSION') }}</label>
            @php($e_commerce_logo = getWebConfig(name: 'company_web_logo'))
            <a class="d-flex justify-content-center mb-5" href="{{ route('home') }}">
                <img class="z-index-2 onerror-logo" height="40"
                    src="{{ getValidImage(path: 'storage/app/public/company/' . $e_commerce_logo, type: 'backend-logo') }}"
                    alt="Logo">
            </a>

            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="card card-lg mb-5">
                        <div class="card-body">
                            <form id="form-id" action="{{ route('login') }}" method="post" id="admin-login-form">
                                @csrf
                                <input type="hidden" name="" id="mobile">
                                <div class="text-center">
                                    <div class="mb-5">
                                        <h1 class="display-4">{{ translate('sign_in') }}</h1><br>
                                        <span>( {{ translate($role) }} {{ translate('Login') }})</span>
                                    </div>
                                </div>

                                <input type="hidden" class="form-control mb-3" name="role" id="role"
                                    value="{{ $role }}">

                                <div class="js-form-message form-group">
                                    <label class="input-label"
                                        for="signingAdminEmail">{{ translate('your_email') }}</label>

                                    <input type="email" class="form-control form-control-lg" name="email"
                                        id="signingAdminEmail" tabindex="1" placeholder="email@address.com"
                                        aria-label="email@address.com" required
                                        data-msg="Please enter a valid email address.">
                                </div>
                                <div class="js-form-message form-group">
                                    <label class="input-label" for="signingAdminPassword" tabindex="0">
                                        <span class="d-flex justify-content-between align-items-center">
                                            {{ translate('password') }}
                                        </span>
                                    </label>

                                    <div class="input-group input-group-merge">
                                        <input type="password" class="js-toggle-password form-control form-control-lg"
                                            name="password" id="signingAdminPassword"
                                            placeholder="{{ translate('8+_characters_required') }}"
                                            aria-label="8+ characters required" required
                                            data-msg="Your password is invalid. Please try again."
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
                                <!-- <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="termsCheckbox"
                                            name="remember">
                                        <label class="custom-control-label text-muted" for="termsCheckbox">
                                            {{ translate('remember_me') }}
                                        </label>
                                    </div>
                                </div> -->
                                @if (isset($recaptcha) && $recaptcha['status'] == 1)
                                    <div id="recaptcha_element" class="w-100;" data-type="image"></div>
                                    <br />
                                @else
                                    <div class="row p-2">
                                        <div class="col-6 pr-0">
                                            <input type="text" class="form-control form-control-lg border-0"
                                                name="default_captcha_value" value="" required
                                                placeholder="{{ translate('enter_captcha_value') }}"
                                                autocomplete="off">
                                        </div>
                                        <div class="col-6 input-icons bg-white rounded">
                                            <a class="get-login-recaptcha-verify"
                                                data-link="{{ URL('login/recaptcha/') }}">
                                                <img src="{{ URL('login/recaptcha/' . rand() . '?captcha_session_id=default_recaptcha_id_' . $role . '_login') }}"
                                                    class="input-field w-90 h-75" id="default_recaptcha_id"
                                                    alt="">
                                                <i class="tio-refresh icon"></i>
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
                                        <small class="text-danger d-none" id="auth-error">Please Select Auth</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="otp" class="form-label">OTP</label>
                                        <input type="text" class="form-control" id="otp" name="otp"
                                            placeholder="Enter OTP" autofocus>
                                        <small class="text-danger d-none" id="otp-error">Please Enter OTP</small>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-lg btn-block btn--primary"
                                    id="submit-employee-button">
                                    {{ translate('login') }}
                                </button>

                                <button type="button" class="btn btn-lg btn-block btn--primary d-none"
                                    id="submit-admin-button">
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
                                        id="submit-prod-employee-button">
                                        {{ translate('login') }}
                                    </button>
                                @endif
                            </form>
                        </div>
                        @if (env('APP_MODE') == 'demo')
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-10">
                                        <span id="admin-email"
                                            data-email="{{ \App\Enums\DemoConstant::ADMIN['email'] }}">{{ translate('email') }}
                                            : {{ \App\Enums\DemoConstant::ADMIN['email'] }}</span><br>
                                        <span id="admin-password"
                                            data-password="{{ \App\Enums\DemoConstant::ADMIN['password'] }}">{{ translate('password') }}
                                            : {{ \App\Enums\DemoConstant::ADMIN['password'] }}</span>
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
    </main>

    <span id="message-please-check-recaptcha" data-text="{{ translate('please_check_the_recaptcha') }}"></span>
    <span id="message-copied_success" data-text="{{ translate('copied_successfully') }}"></span>

    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor.min.js') }}"></script>

    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/theme.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/toastr.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/login.js') }}"></script>

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

    @if ($errors->any())
        <script>
            "use strict";
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
            @endforeach
        </script>
    @endif
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
    $('#submit-employee-button').on('click', function() {
        var email = $('#signingAdminEmail').val();
        if (email === "") {
            return;
        }

        let data = {
            _token: '{{ csrf_token() }}',
            email: email,
        };

        $.ajax({
            type: "post",
            url: "{{ route('admin.auth.2fa.login.check') }}",
            data: data,
            success: function(response) {
                if (response.status == 200) {
                    if (response.admin.admin_role_id == 1) {
                        if (response.admin.enable_2fa != 1) {
                            $("#auth-select option[value='google']").prop("disabled", true);
                        }
                        $('#mobile').val(response.admin.phone);
                        $('#auth-div').removeClass('d-none');
                        $('#submit-employee-button').addClass('d-none');
                        $('#submit-admin-button').removeClass('d-none');
                    } else {
                        $('#form-id').submit();
                    }
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
                    toastr.error('admin mobile no is not valid', {
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


    // login admin
    $('#submit-admin-button').on('click', function() {
        var email = $('#signingAdminEmail').val();
        var auth = $('#auth-select').val();
        var otp = $('#otp').val();

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
                url: "{{ route('admin.auth.2fa.login.submit') }}",
                data: data,
                success: function(response) {
                    if (response.status == 200) {
                        $('#form-id').submit();
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
                confirmationResult.confirm(otp).then(function(result) {
                        $('#form-id').submit();
                    })
                    .catch(function(error) {
                        toastr.error('Incorrect OTP');
                        return;
                    });
            }
        }
    });
</script>

<script>
    //prod login employee
    $('#submit-prod-employee-button').on('click', function() {
        var otp = $('#otp').val().trim();

        if (otp === "") {
            $('#otp-error').removeClass('d-none').text('Please Enter OTP');
            return;
        } else if (otp != "123456") {
            $('#otp-error').removeClass('d-none').text('Incorrect OTP');
            return;
        } else {
            $('#otp-error').addClass('d-none');
            $('#form-id').submit();
        }
    });
</script>

</html>
