@extends('layouts.front-end.app')

@section('title', translate('kundali'))

@push('css_or_js')
<meta property="og:image" content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta property="twitter:card" content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<style>
    .img-style {
        width: 97%;
        background: #fff;
        padding: 10px;
    }

    .feature-product-title {
        text-align: center;
        font-size: 21px;
        margin-top: 15px;
        font-style: normal;
        font-weight: 700;
    }

    .button-title {
        background: #1d0100;
        padding: 20px;
        text-align: center;
        border-radius: 7px;
        height: 182px;
        border: 3px solid #ffaa00;
    }

    .button-title img {
        width: 80px;
        margin-bottom: 10px;
    }

    .button-title .text {
        color: #fff;
        font-size: 20px;
        font-weight: 600;
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
    }
    @media (max-width: 768px) {
      .otp-input-fields input {
         height: 40px;
         width: 40px;
      }
      .otp-input-fields{
         gap:9px;
      }
   }
</style>
<style>
    .responsive-bg {
        padding-top: 4rem !important;
        padding-bottom: 4rem !important;
        background:url("{{ asset('public/assets/front-end/img/slider/kundali-pdf.jpg') }}") no-repeat;
        background-size:cover;
        background-position:center center;
    }

    @media (max-width: 768px) {
        .responsive-bg {
            padding-top: 1rem !important;
            padding-bottom: 2rem !important;
            background:url("{{ asset('public/assets/front-end/img/slider/kundali-pdf1.jpg') }}") no-repeat;
        background-size:cover;
        background-position:center center;
        }
    }
</style>
@endpush

@section('content')
<div class="inner-page-bg center bg-bla-7 responsive-bg">
    <div class="container">
        <div class="row all-text-white">
            <div class="col-md-12 align-self-center">
                <h1 class="innerpage-title">{{ translate('kundali') }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white">
                            <i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">{{ translate('kundali') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container py-3 rtl text-align-direction">
    <!--<h2 class="text-center mb-3 headerTitle">{{ translate('return_policy') }}</h2>-->
    <div class="">
        <div class="card-body text-justify">
            <div class="row">
                <div class="col-md-12">
                    <div class="feature-product-title mt-0 mb-2">
                        जन्म पत्रिका {{ ((($kundali_info['name']??"") =='kundali_milan')?"मिलान":"")}} PDF {{ ($kundali_info['pages']??"")}}pages
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>
                </div>
                <div class="col-md-5 pl-0 pr-0">
                    <img
                        src="{{asset('public/assets/front-end/img/janm-kundali.jpg')}}"
                        class="img-fluid rounded-top img-style"
                        alt="" />

                </div>
                <div class="col-md-7 pl-0 pr-0">
                    <div class="bg-white p-4 rounded-lg">
                        {!! ($kundali_info['description']??"") !!}

                        <div id="" role="button">
                            @if (auth('customer')->check())
                            <a href="javascript:void(0);" id="auth-book-now"
                                class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">Get Your Kundali</a>
                            @else
                            <a href="javascript:void(0);" id="participate-btn"
                                class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">Get Your Kundali</a>
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
                    <span class="text-18 font-bold ml-2">Fill your details for {{ ((($kundali_info['name']??"") =='kundali_milan')?"Kundali Milan":"Kundali")}} Pdf</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr class="bg-[#E6E4EB] w-full">
            <div class="modal-body flex justify-content-center">
                <div id="recaptcha-container"></div>
                <div class="w-full mt-1 px-2">
                    <span class="block text-[16px] font-bold text-gray-900 dark:text-white">Enter Your Whatsapp
                        Mobile Number</span>
                    <span class="text-[12px] font-normal text-[#707070]">Your {{ ((($kundali_info['name']??"") =='kundali_milan')?"Kundali Milan":"Kundali")}} PDF update will be sent to the WhatsApp number given below..</span>
                    <!-- Model Form -->
                    <div class="w-full mr-9 px-0 pt-3">
                        <form class="needs-validation_" id="lead-store-form" action="{{ route('kundali-pdf.create-kundli-leads')}}" method="post">
                            @csrf
                            @php
                            if (auth('customer')->check()) {
                            $customer = App\Models\User::where('id', auth('customer')->id())->first();
                            }
                            @endphp
                            <input type="hidden" name="kundali_id" value="{{ ($kundali_info['id']??'') }}">
                            <input type="hidden" name="amount" value="{{ ($kundali_info['selling_price']??'') }}">
                            <div class="row">
                                <div class="col-md-12" id="phone-div">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('phone_number') }}
                                            <small class="text-primary">( *{{ translate('country_code_is_must_like_for_IND') }} 91 )</small>
                                        </label>
                                        <input
                                            class="form-control text-align-direction phone-input-with-country-picker"
                                            type="tel"
                                            value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                            name="person_phone" id="person-number"
                                            placeholder="{{ translate('enter_phone_number') }}" required
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
                                        <p id="name-validation" class="text-danger" style="display: none">Enter
                                            Your Name</p>
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
                                    <button type="button"
                                        class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
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


@endsection

@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>
<script type="text/javascript" src="{{ theme_asset(path: 'public/assets/front-end/js/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ theme_asset(path: 'public/assets/front-end/js/kundaliChart.js') }}"></script>
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

        var phoneNumber = $('.iti__selected-flag').text() + ' ' + $('#person-number').val();
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
            var phoneNumber = $('.iti__selected-flag').text() + ' ' + $('#person-number').val();
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

        var phoneNumber = $('.iti__selected-flag').text() + ' ' + $('#person-number').val();
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
        var number = $('#person-number').val();
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
        var code = $('.iti__selected-dial-code').text();
        var mobile = $(this).val();
        var no = code + '' + mobile;
        console.log(code);
        console.log(mobile);
        $.ajax({
            type: "get",
            url: "{{url('account-counselling-order-user-name')}}" + "/" + no,
            success: function(response) {
                if (response.status == 200) {
                    var name = response.user.f_name + ' ' + response.user.l_name;
                    $('#person-name').val(name);
                    $('#person-name').prop('readonly', true);
                } else {
                    $('#person-name').val('');
                    $('#person-name').prop('readonly', false);
                }
            }
        });
    });
</script>

{{-- auth book now btn click --}}
<script>
    $('#auth-book-now').click(function(e) {
        e.preventDefault();
        $('#lead-store-form').submit();
    });
</script>

@endpush