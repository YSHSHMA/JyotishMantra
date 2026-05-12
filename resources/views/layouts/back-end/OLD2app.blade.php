@php
    use App\Utils\Helpers;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{Session::get('direction')}}"
      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <meta name="_token" content="{{csrf_token()}}">
    <link rel="shortcut icon" href="{{dynamicStorage(path: 'storage/app/public/company/'.getWebConfig(name: 'company_fav_icon'))}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/google-fonts.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/custom.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/style.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/toastr.css')}}">
    @if(Session::get('direction') === "rtl")
        <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/menurtl.css')}}">
    @endif
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/css/lightbox.css')}}">
    @stack('css_or_js')
    <script
        src="{{dynamicAsset(path: 'public/assets/back-end/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js')}}"></script>
    <style>
        select {
            background-image: url('{{dynamicAsset(path: 'public/assets/back-end/img/arrow-down.png')}}');
            background-size: 7px;
            background-position: 96% center;
        }
    </style>
    @if(Request::is('admin/payment/configuration/addon-payment-get'))
        <style>
            .form-floating > label {
                position: relative;
                display: block;
                margin-bottom: 12px;
                padding: 0;
                inset-inline: 0 !important;
            }
        </style>
    @endif
</head>

<body class="footer-offset">

<div id="recaptcha-container"></div>

{{-- permission modal --}}
<div class="modal fade" id="permission-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permission Access</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="permission-mobile-div" class="col-12">
                        <div class="form-group">
                          <label for="mobile_no"></label>
                          <select name="mobile_no" id="mobile-no-value" class="form-control">
                            <option value="+919713794786">Admin</option>
                            <option value="+918770540672">Varshaa mam</option>
                            <option value="+918871604650">Safal</option>
                          </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-primary" onclick="sendOtp()">Send</button>
                        </div>
                    </div>
                    <div id="permission-otp-div" class="col-12" style="display: none;">
                        <div class="form-group">
                          <label for="otp">Enter OTP</label>
                          <input type="number" name="otp" id="otp-value" class="form-control" placeholder="Enter OTP">
                          <p id="permission-otp-validate" class="text-danger" style="display: none"></p>
                        </div>
                        <div class="col-12 text-end">
                          <button type="button" class="btn btn-danger" onclick="backToMobile()">Back</button>
                          <button type="button" class="btn btn-primary ml-2" onclick="otpVerify()">Verify</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- remote access modal --}}
<div class="modal fade" id="remote-access-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remote Access</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="remote-access-mobile-div" class="col-12">
                        <div class="form-group">
                          <label for="mobile_no"></label>
                          <select name="mobile_no" id="remote-access-mobile-no-value" class="form-control">
                            <option value="+919713794786">Admin</option>
                            <option value="+918871604650">Safal</option>
                            <option value="+918770540672">Varshaa Mam</option>
                          </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-primary" onclick="sendOtpRemoteAccess()">Send</button>
                        </div>
                    </div>
                    <div id="remote-access-otp-div" class="col-12" style="display: none;">
                        <div class="form-group">
                          <label for="otp">Enter OTP</label>
                          <input type="number" name="otp" id="remote-access-otp-value" class="form-control" placeholder="Enter OTP">
                          <p id="remote-access-otp-validate" class="text-danger" style="display: none"></p>
                        </div>
                        <div class="col-12 text-end">
                          <button type="button" class="btn btn-danger" onclick="backToMobileRemoteAccess()">Back</button>
                          <button type="button" class="btn btn-primary ml-2" onclick="otpVerifyRemoteAccess()">Verify</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.back-end.partials._front-settings')
<span class="d-none" id="placeholderImg" data-img="{{dynamicAsset(path: 'public/assets/back-end/img/400x400/img3.png')}}"></span>
<div class="row">
    <div class="col-12 position-fixed z-9999 mt-10rem">
        <div id="loading" class="d--none">
            <div id="loader"></div>
        </div>
    </div>
</div>
@include('layouts.back-end.partials._header')
@include('layouts.back-end.partials._side-bar')
@include('layouts.back-end._translator-for-js')
<span id="get-root-path-for-toggle-modal-image" data-path="{{dynamicAsset(path: 'public/assets/back-end/img/modal')}}"></span>

<main id="content" role="main" class="main pointer-event">
    @yield('content')
    @include('layouts.back-end.partials._footer')
    @include('layouts.back-end.partials._modals')
    @include('layouts.back-end.partials._toggle-modal')
    @include('layouts.back-end.partials._sign-out-modal')
</main>

<span class="please_fill_out_this_field" data-text="{{ translate('please_fill_out_this_field') }}"></span>
<span class="get-application-environment-mode" data-value="{{ env('APP_MODE') == 'demo' ? 'demo':'live' }}"></span>
<span id="get-currency-symbol"
      data-currency-symbol="{{ getCurrencySymbol(currencyCode: getCurrencyCode(type: 'default')) }}"></span>

<span id="message-select-word" data-text="{{ translate('select') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>
<span id="message-cancel-word" data-text="{{ translate('cancel') }}"></span>
<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }} ?"></span>
<span id="message-invalid-date-range" data-text="{{ translate('invalid_date_range') }}"></span>
<span id="message-status-change-successfully" data-text="{{ translate('status_change_successfully') }}"></span>
<span id="message-are-you-sure-delete-this" data-text="{{ translate('are_you_sure_to_delete_this') }} ?"></span>
<span id="message-you-will-not-be-able-to-revert-this"
      data-text="{{ translate('you_will_not_be_able_to_revert_this') }}"></span>

<span id="get-customer-list-route" data-action="{{route('admin.customer.customer-list-search')}}"></span>

<span id="get-search-product-route" data-action="{{route('admin.products.search-product')}}"></span>
<span id="get-orders-list-route" data-action="{{route('admin.orders.list',['status'=>'all'])}}"></span>
<span class="system-default-country-code" data-value="{{ getWebConfig(name: 'country_code') ?? 'us' }}"></span>

<audio id="myAudio">
    <source src="{{ dynamicAsset(path: 'public/assets/back-end/sound/notification.mp3') }}" type="audio/mpeg">
</audio>


<script src="{{dynamicAsset(path: 'public/assets/back-end/js/vendor.min.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/theme.min.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/bootstrap.min.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/sweet_alert.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/toastr.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/js/lightbox.min.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/custom.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/app-script.js')}}"></script>
<!-- Firbase CDN -->
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>

{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        'use strict';
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

@stack('script')

@if(Helpers::module_permission_check('order_management') && env('APP_MODE')!='dev')
<script>
    'use strict'
        setInterval(function () {
            $.get({
                url: '{{route('admin.orders.get-order-data')}}',
                dataType: 'json',
                success: function (response) {
                    let data = response.data;
                    if (data.new_order > 0) {
                        playAudio();
                        $('#popup-modal').appendTo("body").modal('show');
                    }
                },
            });
        }, 5000);
</script>
@endif

@stack('script_2')

@if(Helpers::module_permission_check('order_management') && env('APP_MODE')!='dev')
<script>
    'use strict'
        setInterval(function () {
            $.get({
                url: '{{route('admin.orders.get-order-pooja')}}',
                dataType: 'json',
                success: function (response) {
                    let data = response.data;
                    console.log(response.data);
                    if (data.new_pooja > 0) {
                        playAudio();
                        $('#popup-modal-pooja').appendTo("body").modal('show');
                    }
                },
            });
        }, 5000);
</script>
@endif

@stack('script_3')

@if(Helpers::module_permission_check('order_management') && env('APP_MODE')!='dev')
<script>
    'use strict'
        setInterval(function () {
            $.get({
                url: '{{route('admin.orders.get-order-counselling')}}',
                dataType: 'json',
                success: function (response) {
                    let data = response.data;
                    console.log(response.data);
                    if (data.new_counselling > 0) {
                        playAudio();
                        $('#popup-modal-counselling').appendTo("body").modal('show');
                    }
                },
            });
        }, 5000);
</script>
@endif

@stack('script_4')

{{-- firebase config --}}
<script>
    const firebaseConfigg = {
        apiKey: "AIzaSyBrrMSAtiASPJGKt0aAQqpIYXoFUG4QGv8",
        authDomain: "rizrv-65a76.firebaseapp.com",
        projectId: "rizrv-65a76",
        storageBucket: "rizrv-65a76.appspot.com",
        messagingSenderId: "832249240595",
        appId: "1:832249240595:web:083a86aed6f3f50fa133e6",
        measurementId: "G-GXM9FXY2WM"
    };
</script>

{{-- permission check to open view --}}
<script>
    
    function permissionModal(){
        $('#permission-modal').modal('show');
    }
    
    var confirmationResultt = "";
    var appVerifierr;

    function sendOtp() {
        var mobile = $('#mobile-no-value').val();

        // Check if the Firebase app is already initialized
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfigg);
        }

        // Check if appVerifierr has already been created
        if (!appVerifierr) {
            appVerifierr = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                size: 'invisible'
            });
        }

        firebase.auth().signInWithPhoneNumber(mobile, appVerifierr).then(function(confirmation) {
            confirmationResultt = confirmation;
            toastr.success('OTP sent successfully');
            $('#permission-mobile-div').hide();
            $('#permission-otp-div').show();
        }).catch(function(error) {
            toastr.error('Failed to send OTP. Please try again');
            console.error('OTP sending error:', error);
        });
    }

    function backToMobile(){
        $('#otp-value').val('');
        $('#permission-mobile-div').show();
        $('#permission-otp-div').hide();
    }

    function otpVerify() {
        var otp = $('#otp-value').val();
        if (otp.length > 0) {
            toastr.success('Please wait...');
            $('#permission-otp-validate').hide();
            if (confirmationResultt) {
                confirmationResultt.confirm(otp).then(function(result) {
                    window.location.href = "{{ route('admin.custom-role.create') }}";
                }).catch(function(error) {
                    $('#permission-otp-validate').text('Incorrect OTP');
                    $('#permission-otp-validate').show();
                    console.error('OTP verification error:', error);
                });
            }
        } else {
            $('#permission-otp-validate').text('Please Enter OTP');
            $('#permission-otp-validate').show();
        }
    }
    
</script>

{{-- remote access check to open view --}}
<script>
    
    function RemoteAccessModal(){
        $('#remote-access-modal').modal('show');
    }
    
    var confirmationRemoteAccessResult = "";
    var appVerifierrRemoteAccess;

    function sendOtpRemoteAccess() {
        var mobileRemoteAccess = $('#remote-access-mobile-no-value').val();

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfigg);
        }

        if (!appVerifierrRemoteAccess) {
            appVerifierrRemoteAccess = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                size: 'invisible'
            });
        }

        firebase.auth().signInWithPhoneNumber(mobileRemoteAccess, appVerifierrRemoteAccess).then(function(confirmation) {
            confirmationRemoteAccessResult = confirmation;
            toastr.success('OTP sent successfully');
            $('#remote-access-mobile-div').hide();
            $('#remote-access-otp-div').show();
        }).catch(function(error) {
            toastr.error('Failed to send OTP. Please try again');
            console.error('OTP sending error:', error);
        });
    }

    function backToMobileRemoteAccess(){
        $('#remote-access-otp-value').val('');
        $('#remote-access-mobile-div').show();
        $('#remote-access-otp-div').hide();
    }

    function otpVerifyRemoteAccess() {
        var otp = $('#remote-access-otp-value').val();
        if (otp.length > 0) {
            toastr.success('Please wait...');
            $('#remote-access-otp-validate').hide();
            if (confirmationRemoteAccessResult) {
                confirmationRemoteAccessResult.confirm(otp).then(function(result) {
                    window.location.href = "{{ route('admin.remote.access.list') }}";
                }).catch(function(error) {
                    $('#remote-access-otp-validate').text('Incorrect OTP');
                    $('#remote-access-otp-validate').show();
                    console.error('OTP verification error:', error);
                });
            }
        } else {
            $('#remote-access-otp-validate').text('Please Enter OTP');
            $('#remote-access-otp-validate').show();
        }
    }
    
</script>

</body>
</html>
