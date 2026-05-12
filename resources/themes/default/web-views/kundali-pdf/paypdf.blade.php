@extends('layouts.front-end.app')

@section('title', translate('kundali'))

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
    .btn-primary {
        color: #fff;
        background-color: #f7a708 !important;
        border-color: #f7a708 !important;
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

    #signUpForm input.invalid {
        border: 1px solid #ffaba5;
    }

    .button-title img {
        width: 80px;
        margin-bottom: 10px;
    }

    #signUpForm {
        max-width: 100%;
        background-color: #ffffff;
        margin: 40px auto;
        padding: 40px;
        box-shadow: 0px 6px 18px rgb(0 0 0 / 9%);
        border-radius: 12px;
    }

    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    .button-title .text {
        color: #fff;
        font-size: 20px;
        font-weight: 600;
    }

    /* tab */
    #signUpForm .form-header .stepIndicator.active {
        font-weight: 600;
    }

    #signUpForm .form-header .stepIndicator {
        position: relative;
        flex: 1;
        padding-bottom: 30px;
    }

    #signUpForm .form-header .stepIndicator.finish::before {
        /* background-color: #009688 !important; */
        background-color: #fe9802;
        border: 3px solid #f4c664;
    }

    #signUpForm .form-header .stepIndicator.finish {
        font-weight: 600;
        /* color: #009688; */
    }

    #signUpForm .form-header .stepIndicator:last-child:after {
        display: none;
    }

    #signUpForm .form-header .stepIndicator.finish::after {

        background-color: #fe9802;

    }


    #signUpForm .form-header .stepIndicator.active::before {
        /* background-color: #fe9802; */
        background-color: #ffb951b0;
        border: 3px solid #ffebce94;
    }

    #signUpForm .form-header .stepIndicator::before {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        transform: translateX(-50%);
        z-index: 9;
        width: 20px;
        height: 20px;
        background-color: #ffd392;
        border-radius: 50%;
        border: 3px solid #ecf5f4;
    }

    #signUpForm .form-header .stepIndicator.active::after {
        background-color: #edc487ad;
    }

    #signUpForm .form-header .stepIndicator::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 8px;
        width: 100%;
        height: 3px;
        background-color: #f3f3f3;
    }

    #signUpForm .step {

        display: none;

    }

    #signUpForm .form-footer button:hover {

        opacity: 0.8;

    }



    #signUpForm .form-footer #prevBtn {

        background-color: #fff;

        color: #009688;

    }
</style>
<style>
    .responsive-bg {
        padding-top: 4rem !important;
        padding-bottom: 4rem !important;
        background:url("{{ asset('public/assets/front-end/img/slider/kundali-pdf.jpg') }}") no-repeat;
        background-size: cover;
        background-position: center center;
    }

    @media (max-width: 768px) {
        .responsive-bg {
            padding-top: 1rem !important;
            padding-bottom: 2rem !important;
            background:url("{{ asset('public/assets/front-end/img/slider/kundali-pdf1.jpg') }}") no-repeat;
            background-size: cover;
            background-position: center center;
        }

        .card-remove-padding {
            padding: 3px !important;
        }

        #signUpForm .form-header .stepIndicator {
            font-size: 13px;
        }
        .three-step-font-size{
            font-size: 13px;
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
        <div class="card-body text-justify card-remove-padding">
            <div class="row">
                <div class="col-md-12">
                    <div class="feature-product-title mt-0 mb-2">
                        {{ translate('birth_Journal') }}
                        {{ ($kundali_info['name'] ?? '') == 'kundali_milan' ? translate('match') : '' }} PDF
                        {{ $kundali_info['pages'] ?? '' }} {{ translate('pages') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>

                </div>
            </div>
            <div class="row px-5 card-remove-padding">
                <form id="signUpForm" class="col-12 mt-2" method="get" action="#!" name="payuForm">
                    <!-- start step indicators -->
                    <div class="form-header d-flex mb-4 text-center">
                        <span class="stepIndicator active">{{ translate('birth_Details') }}</span>
                        <span class="stepIndicator"> {{ translate('confirm') }}</span>
                        <span class="stepIndicator"> {{ translate('download_PDF') }}</span>
                    </div>
                    <!-- end step indicators -->
                    <!-- step one -->
                    <div class="step" style="display: block;">
                        @if ($kundali_info['name'] == 'kundali')
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    
                                    <input class="form-control" id="username" type="text"
                                        oninput="this.className = 'form-control'" autocomplete="off"
                                        placeholder="{{ translate('Enter_Full_Name') }}">
                                </div>
                            </div>
                            <div class="col-sm-6 d-none">
                                <div class="form-group">
                                    <!-- <input class="form-control" id="useremail" type="email"  autocomplete="off" placeholder="{{ translate('enter_Email') }}"> -->
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select class="form-control" id="gender">
                                        <option value="male">{{ translate('male') }} </option>
                                        <option value="female">{{ translate('female') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 d-none">
                                <div class="form-group">
                                    <!-- oninput="this.className = 'form-control';this.value = this.value.replace(/\D/g, '').slice(0, 10);" -->
                                    <!-- <input class="form-control" id="usermobile" type="number" autocomplete="off" placeholder="{{ translate('Please_Enter_the_Mobile-Number') }}"> -->
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control hasDatepicker" id="dob" type="text"
                                        oninput="this.className = 'form-control'" autocomplete="off"
                                        placeholder="{{ translate('enter_date_of_Birth') }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control select_time" readonly id="time" type="text"
                                        oninput="this.className = 'form-control'" autocomplete="off"
                                        placeholder="{{ translate('Enter_time_of_birth') }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select id="country" class="form-control">
                                        @if ($country)
                                        @foreach ($country as $val)
                                        <option value="{{ $val['name'] }}"
                                            data-id="{{ $val['id'] }}">{{ $val['name'] }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control pac-target-input" type="text" id="places"
                                        oninput="this.className = 'form-control pac-target-input'"
                                        autocomplete="off" placeholder="{{ translate('Enter_place_of_Birth') }}">
                                    <ul id="citylist" class="list-group" style="position: absolute;z-index:9">
                                    </ul>
                                    <input type='hidden' id='latitude' value=''>
                                    <input type='hidden' id='longitude' value=''>
                                    <input type='hidden' id='timezone' value=''>
                                </div>
                            </div>
                        </div>
                        @elseif($kundali_info['name'] == 'kundali_milan')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-12">
                                    <div class="form-group text-center mb-0">
                                        <label class="form-label font-weight-bold h4">
                                            {{ translate('male') }}</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        
                                        <input class="form-control" id="username" type="text"
                                            oninput="this.className = 'form-control'" autocomplete="off"
                                            placeholder="{{ translate('Enter_Full_Name') }}">
                                    </div>
                                </div>
                                <div class="col-12 d-none">
                                    <div class="form-group">
                                        <!-- <input class="form-control" id="useremail" type="email" autocomplete="off" placeholder="{{ translate('enter_Email') }}"> -->
                                    </div>
                                </div>
                                <div class="col-12 d-none">
                                    <div class="form-group">
                                        <select class="form-control" id="gender">
                                            <option value="male" selected>{{ translate('male') }}</option>
                                            <option value="female">{{ translate('female') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 d-none">
                                    <div class="form-group">
                                        <!-- <input class="form-control" id="usermobile" type="number" autocomplete="off" placeholder="{{ translate('Please_Enter_the_Mobile-Number') }}"> -->
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control hasDatepicker" id="dob" type="text"
                                            onblur="this.className = 'form-control'"
                                            oninput="this.className = 'form-control'" autocomplete="off"
                                            placeholder="{{ translate('enter_date_of_Birth') }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control select_time" readonly id="time"
                                            type="text" onblur="this.className = 'form-control'"
                                            oninput="this.className = 'form-control'" autocomplete="off"
                                            placeholder="{{ translate('Enter_time_of_birth') }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <select id="country" class="form-control">
                                            @if ($country)
                                            @foreach ($country as $val)
                                            <option value="{{ $val['name'] }}"
                                                data-id="{{ $val['id'] }}">{{ $val['name'] }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control pac-target-input" type="text"
                                            id="places"
                                            oninput="this.className = 'form-control pac-target-input'"
                                            autocomplete="off"
                                            placeholder="{{ translate('Enter_place_of_Birth') }}">
                                        <ul id="citylist" class="list-group" style="position: absolute;z-index:9">
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class="col-12">
                                    <div class="form-group text-center mb-0">
                                        <label class="form-label font-weight-bold h4">
                                            {{ translate('female') }}</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control" id="username_female" type="text"
                                            oninput="this.className = 'form-control'" autocomplete="off"
                                            placeholder="{{ translate('Enter_Full_Name') }}">
                                    </div>
                                </div>
                                <div class="col-12 d-none">
                                    <div class="form-group">
                                        <!-- <input class="form-control" id="useremail_female" type="email" autocomplete="off" placeholder="{{ translate('enter_Email') }}"> -->
                                    </div>
                                </div>
                                <div class="col-12 d-none">
                                    <div class="form-group">
                                        <select class="form-control" id="gender_female">
                                            <option value="male">{{ translate('male') }} </option>
                                            <option value="female" selected>{{ translate('female') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 d-none">
                                    <div class="form-group">
                                        <!-- <input class="form-control" id="usermobile_female" type="number" autocomplete="off" placeholder="{{ translate('Please_Enter_the_Mobile-Number') }}"> -->
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control" id="dob_female" type="text"
                                            onblur="this.className = 'form-control'"
                                            oninput="this.className = 'form-control'" autocomplete="off"
                                            placeholder="{{ translate('enter_date_of_Birth') }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control" readonly id="time_female" type="text"
                                            onblur="this.className = 'form-control'"
                                            oninput="this.className = 'form-control'" autocomplete="off"
                                            placeholder="{{ translate('Enter_time_of_birth') }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <select id="country_female" class="form-control">
                                            @if ($country)
                                            @foreach ($country as $val)
                                            <option value="{{ $val['name'] }}"
                                                data-id="{{ $val['id'] }}">{{ $val['name'] }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control pac-target-input" type="text"
                                            id="places_female"
                                            oninput="this.className = 'form-control pac-target-input'"
                                            autocomplete="off"
                                            placeholder="{{ translate('Enter_place_of_Birth') }}">
                                        <ul id="citylist_female" class="list-group" style="position: absolute;z-index:9">
                                        </ul>
                                        <input type='hidden' id='latitude_female' value=''>
                                        <input type='hidden' id='longitude_female' value=''>
                                        <input type='hidden' id='timezone_female' value=''>
                                        <input type='hidden' id='latitude' value=''>
                                        <input type='hidden' id='longitude' value=''>
                                        <input type='hidden' id='timezone' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <!-- step two -->
                    <div class="step">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">{{ translate('Confirm_Birth_details') }}</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-1">
                                            <div class="col-md-12">
                                                <i class="fa fa-user-o"></i>
                                                <span id="getname">Name</span>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <div class="col-md-12">
                                                <i class="fa fa-calendar-o"></i>
                                                <span id="getdob">Date</span>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <div class="col-md-12">
                                                <i class="fa fa-clock-o"></i>
                                                <span id="gettime">समय</span>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <div class="col-md-12">
                                                <i class="fa fa-globe"></i>
                                                <span id="getplaces">Address</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($kundali_info['name'] == 'kundali_milan') { ?>
                                            <div class="mb-1">
                                                <hr>
                                                <div class="col-md-12">
                                                    <i class="fa fa-user-o"></i>
                                                    <span id="getname_female">Name</span>
                                                </div>
                                            </div>
                                            <div class="mb-1">
                                                <div class="col-md-12">
                                                    <i class="fa fa-calendar-o"></i>
                                                    <span id="getdob_female">Date</span>
                                                </div>
                                            </div>
                                            <div class="mb-1">
                                                <div class="col-md-12">
                                                    <i class="fa fa-clock-o"></i>
                                                    <span id="gettime_female">समय</span>
                                                </div>
                                            </div>
                                            <div class="mb-1">
                                                <div class="col-md-12">
                                                    <i class="fa fa-globe"></i>
                                                    <span id="getplaces_female">Address</span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="pb-3"></h5>
                                <label class="font-weight-bold">{{ translate('select_chart') }}</label>
                                <div class="form-group">
                                    <select class="form-control" id="chartstyle">
                                        <option value="NORTH_INDIAN"> {{ translate('northern_chart') }}</option>
                                        <option value="SOUTH_INDIAN"> {{ translate('south_chart') }}</option>
                                    </select>
                                </div>
                                <label class="font-weight-bold"> {{ translate('select_Language') }}</label>
                                <div class="form-group">
                                    <select class="form-control" id="language">
                                        <option value="hi"> {{ translate('Hindi') }}</option>
                                        <option value="en"> {{ translate('English') }}</option>
                                        @if ($kundali_info['type'] == 'basic')
                                        <option value="bn"> {{ translate('Bengali') }}</option>
                                        <option value="ma"> {{ translate('Marathi') }}</option>
                                        <option value="kn"> {{ translate('Kannada') }}</option>
                                        <option value="ml"> {{ translate('Malayalam') }}</option>
                                        <option value="te"> {{ translate('Telogu') }}</option>
                                        <option value="ta"> {{ translate('Tamil') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- step three -->
                    <div class="step three-step-font-size">
                        <div class="mt-3 mb-3">
                            <!-- <div class="card">
                                <div class="card-body"> -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div>
                                                <img class="img-thumbnail rounded"
                                                    src="{{ getValidImage(path: 'storage/app/public/birthjournal/image/' . $kundali_info['image'], type: 'backend-product') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="cart_total p-0">
                                                        @if ((\App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0) > 0)
                                                        <div class="row">
                                                            <div class="col-12 text-end">
                                                                <input type="checkbox"
                                                                    onclick="calculator_wallet()"
                                                                    class="wallet_checked" value="1"
                                                                    data-amount="{{ \App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0 }}"
                                                                    checked>&nbsp;{{ translate('apply_Wallet') }}
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <hr class="my-2">
                                                        <div class="d-flex justify-content-between">
                                                            <span
                                                                class="cart_title font-weight-bold">{{ translate('Kundali Amount') }}</span>
                                                            <span
                                                                class="cart_value cart-amount-show kundali_amounts font-weight-bold"
                                                                data-amount="{{ $kundali_info['selling_price'] }}">{{ webCurrencyConverter(amount: $kundali_info['selling_price']) }}</span>
                                                        </div>

                                                        <div class="d-none show_user_wallet_amount">
                                                            <hr class="my-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span class="cart_title text-success font-weight-bold">
                                                                    <img width="20"
                                                                        src="{{ theme_asset(path: 'public/assets/back-end/img/admin-wallet.png') }}"
                                                                        style="margin-top: -9px;">{{ translate('Wallet Amount') }}
                                                                    <small>({{ webCurrencyConverter(amount: \App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0) }})</small>
                                                                </span>
                                                                <span
                                                                    class="cart_value text-success user_wallet_amount font-weight-bold">
                                                                    {{ webCurrencyConverter(amount: \App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0) }}
                                                                </span>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="d-flex justify-content-between mt-2">
                                                                <span
                                                                    class="cart_title text-success font-weight-bold user_wallet_am_remaining_text font-weight-bold"
                                                                    style="color: darkred !important;">{{ translate('Remaining Amount') }}</span>
                                                                <span
                                                                    class="cart_value text-success user_wallet_amount_remaining font-weight-bold"
                                                                    style="color: darkred !important;"> </span>
                                                            </div>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="justify-content-between d-flex">
                                                            <span
                                                                class="cart_title text-primary font-weight-bold">{{ translate('Final Amount') }}</span>
                                                            <span class="cart_value font-weight-bold"
                                                                id="mainProductPrice"></span>
                                                        </div>
                                                        <input type="hidden" class="user-wallet-adds" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <!-- </div>
                            </div> -->


                            <!-- <input type="hidden" name="key" id="key">
                                    <input type="hidden" name="txnid" id="txnid">
                                    <input type="hidden" name="amount" id="amount">
                                    <input type="hidden" name="productinfo" id="productinfo">
                                    <input type="hidden" name="firstname" id="firstname">
                                    <input type="hidden" name="email" id="email">
                                    <input type="hidden" name="phone" id="phone">
                                    <input type="hidden" name="surl" id="surl">
                                    <input type="hidden" name="furl" id="furl">
                                    <input type="hidden" name="hash" id="hash">
                                    <input type="hidden" name="service_provider" value="payu_paisa">
                                    <input type="hidden" name="udf1" id="udf1"> -->

                            <input type="hidden" id="kundli_lead_id" value="">
                        </div>
                    </div>
                    <!-- start previous / next buttons -->
                    <div class="form-footer d-block text-right">
                        <button class="btn btn-primary float-left" type="button" id="prevBtn" onclick="nextPrev(-1)"
                            style="display: none;">{{ translate('go back') }}</button>
                        <button class="btn btn-primary" type="button" id="nextBtn"
                            onclick="nextPrev(1)">{{ translate('next') }}</button>
                        <button class="btn btn-primary text_name_chnage px-2" type="button" id="submitBtn" onclick="janam_patrika()" style="display: none;">{{ translate('download_now') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
</script>

<script>
    var today = new Date();
    var tomorrow = new Date(today);
    tomorrow.setDate(today.getDate());
    $('.hasDatepicker').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd',
        modal: true,
        footer: true,
        // minDate: tomorrow,
        maxDate: today,
        todayHighlight: true
    });
    $('.select_time').timepicker({
        uiLibrary: 'bootstrap4',
        format: 'HH:MM:ss', // Correct format for time display (12-hour with AM/PM)
        modal: true,
        footer: true
    });
</script>
<script>
    // $(function() {
    //     $("#dob").datetimepicker({
    //         format: 'YYYY-MM-DD',
    //         maxDate: new Date(),
    //         icons: {
    //             previous: 'fa fa-chevron-left',
    //             next: 'fa fa-chevron-right',
    //         }
    //     });
    // });
    // $('#time').datetimepicker({
    //     format: 'HH:mm:ss',
    //     icons: {
    //         up: 'fa fa-chevron-up',
    //         down: 'fa fa-chevron-down'
    //     }
    // });


    var currentTab = 0;

    showTab(currentTab);


    function fixStepIndicator(n) {
        var i, x = document.getElementsByClassName("stepIndicator");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        x[n].className += " active";
    }

    function showTab(n) {
        $("#submitBtn").hide();
        var x = document.getElementsByClassName("step");
        x[n].style.display = "block";
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
            $("#nextBtn").show();
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        if (n == (x.length - 1)) {
            $("#nextBtn").hide();
            $("#submitBtn").show();
        } else {
            document.getElementById("nextBtn").innerHTML = "Next";
            $("#nextBtn").show();
        }
        fixStepIndicator(n)

    }


    function nextPrev(n) {
        var x = document.getElementsByClassName("step");

        if (n == 1 && !validateForm()) return false;

        x[currentTab].style.display = "none";

        currentTab = currentTab + n;

        if (currentTab >= x.length) {

            document.getElementById("signUpForm").submit();

            return false;

        }
        showTab(currentTab);

        let sendusername = $("#username").val();
        let senddob = $("#dob").val();
        let sendtime = $("#time").val();
        let sendplaces = $("#places").val();
        // let sendemail = $("#useremail").val();
        // let sendmobile = $("#usermobile").val();
        // let sendamount = $("#useramount").val();

        $('#getname').text(sendusername);
        $('#getdob').text(senddob);
        $('#gettime').text(sendtime);
        $('#getplaces').text(sendplaces);
        <?php if ($kundali_info['name'] == 'kundali_milan') { ?>
            $('#getname_female').text($("#username_female").val());
            $('#getdob_female').text($("#dob_female").val());
            $('#gettime_female').text($("#time_female").val());
            $("#getplaces_female").text($("#places_female").val());
        <?php } ?>
    }


    function validateForm() {
        var x, y, i, valid = true;
        x = document.getElementsByClassName("step");
        y = x[currentTab].getElementsByTagName("input");
        for (i = 0; i < y.length; i++) {
            if (y[i].value == "") {
                y[i].className += " invalid";
                valid = false;
            }
        }
        if (valid) {
            document.getElementsByClassName("stepIndicator")[currentTab].className += " finish";
        }
        return valid;
    }


    $("#places").keyup(function() {
        $('#citylist').html("");
        let countryName = $("#country").val();
        let cityName = $("#places").val();
        let city = "";
        var data = {
            country: countryName,
            name: cityName,
        }
        $.ajax({
            type: "post",
            url: "https://geo.vedicrishi.in/places/",
            data: JSON.stringify(data),
            dataType: "json",
            headers: {
                "Content-Type": 'application/json'
            },
            success: function(response) {
                $.each(response, function(key, value) {
                    console.log(key);
                    city +=
                        `<li class="list-group-item p-0"><button type='button' class="btn btn-transparent" onclick="citydata(${value.latitude},${value.longitude},'${value.place}')">${value.place}</button></li>`;
                });
                $('#citylist').append(city);
            }
        });
    });

    function citydata(latitude, longitude, place) {
        $('#places').val(place);
        $('#longitude').val(longitude);
        $('#latitude').val(latitude);
        $('#citylist').html("");
        let timestamp = Math.floor(Date.now() / 1000);
        var data = {
            latitude: latitude,
            longitude: longitude,
            date: new Date(),
        };
        var userId = "{{ $user_id }}";
        var apiKey = '{{ $astrologyapiKey }}';
        var auth = "Basic " + ethereumjs.Buffer.Buffer.from(userId + ":" + apiKey).toString("base64");
        $.ajax({
            type: "post",
            url: "https://json.astrologyapi.com/v1/timezone_with_dst",
            data: JSON.stringify(data),
            dataType: "json",
            headers: {
                "authorization": auth,
                "Content-Type": 'application/json'
            },
            success: function(response) {
                $('#timezone').val(response.timezone);
            }
        });
    }



    function janam_patrika() {
        // $('#loader').show();
        let username = $('#username').val();
        let useremail = $('#useremail').val();
        let usermobile = $('#usermobile').val();
        let useramount = "{{ $kundali_info['selling_price'] }}";
        let wallet_type = $(".user-wallet-adds").val();
        let payuForm = document.forms.payuForm;
        var formData = {
            _token: '{{ csrf_token() }}',
            user_id: "{{ $user_id }}",
            username: username,
            useremail: useremail,
            usergender: $('#gender').val(),
            usermobile: usermobile,
            userdob: $('#dob').val(),
            usertime: $('#time').val(),
            usercountry: $('#country option:selected').data('id'),
            userlat: $('#latitude').val(),
            userlon: $('#longitude').val(),
            usertzone: $('#timezone').val(),
            userplaces: $('#places').val(),
            userchartstyle: $('#chartstyle').val(),
            userlanguage: $('#language').val(),
            useramount: useramount,
            kundali_id: "{{ $kundali_info['id'] }}",
            leads: "{{ $leads_id }}",
            wallet_type: wallet_type,
        };
        <?php if ($kundali_info['name'] == 'kundali_milan') { ?>
            formData.username_female = $('#username_female').val();
            formData.useremail_female = $('#useremail_female').val();
            formData.usergender_female = $('#gender_female').val();
            formData.usermobile_female = $('#usermobile_female').val();
            formData.userdob_female = $('#dob_female').val();
            formData.usertime_female = $('#time_female').val();
            formData.usercountry_female = $('#country_female option:selected').data('id');
            formData.userlat_female = $('#latitude_female').val();
            formData.userlon_female = $('#longitude_female').val();
            formData.usertzone_female = $('#timezone_female').val();
            formData.userplaces_female = $('#places_female').val();
        <?php } ?>
        $('.text_name_chnage').addClass('disabled');
        $('.text_name_chnage').text(`{{ translate('Please_wait') }}`);
        $.ajax({
            type: "POST",
            url: "{{ route('kundali-pdf.pdfkundalipaid') }}",
            data: formData,
            success: function(response) {
                if (response.code == "200") {
                    window.location.href = response.link;
                } else {
                    toastr.error('You have entered wrong information, Plz try again');
                    // $('#loader').hide();
                }
            }
        });
    }
</script>
@if ($kundali_info['name'] == 'kundali_milan')
<script>
    // female Data

    $(function() {
        $("#dob_female").datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            modal: true,
            footer: true,
            maxDate: new Date(),
            todayHighlight: true
        });
    });
    $('#time_female').timepicker({
        uiLibrary: 'bootstrap4',
        format: 'HH:mm:ss',
        modal: true,
        footer: true,
        // icons: {
        //     up: 'fa fa-chevron-up',
        //     down: 'fa fa-chevron-down'
        // }
    });

    $("#places_female").keyup(function() {
        $('#citylist_female').html("");
        let countryName = $("#country_female").val();
        let cityName = $("#places_female").val();
        let city = "";
        var data = {
            country: countryName,
            name: cityName,
        }
        $.ajax({
            type: "post",
            url: "https://geo.vedicrishi.in/places/",
            data: JSON.stringify(data),
            dataType: "json",
            headers: {
                "Content-Type": 'application/json'
            },
            success: function(response) {
                $.each(response, function(key, value) {
                    city +=
                        `<li class="list-group-item p-0"><button type='button' class="btn btn-transparent" onclick="citydata_female(${value.latitude},${value.longitude},'${value.place}')">${value.place}</button></li>`;
                });
                $('#citylist_female').append(city);
            }
        });
    });

    function citydata_female(latitude, longitude, place) {
        $('#places_female').val(place);
        $('#longitude_female').val(longitude);
        $('#latitude_female').val(latitude);
        $('#citylist_female').html("");
        let timestamp = Math.floor(Date.now() / 1000);
        var data = {
            latitude: latitude,
            longitude: longitude,
            date: new Date(),
        };
        var userId = "{{ $user_id }}";
        var apiKey = '{{ $astrologyapiKey }}';
        var auth = "Basic " + ethereumjs.Buffer.Buffer.from(userId + ":" + apiKey).toString("base64");
        $.ajax({
            type: "post",
            url: "https://json.astrologyapi.com/v1/timezone_with_dst",
            data: JSON.stringify(data),
            dataType: "json",
            headers: {
                "authorization": auth,
                "Content-Type": 'application/json'
            },
            success: function(response) {
                $('#timezone_female').val(response.timezone);
            }
        });
    }
</script>
@endif

<script>
    calculator_wallet();

    function calculator_wallet() {
        var wallet_amount = $(".wallet_checked").data('amount');
        var isChecked = $('.wallet_checked').prop('checked');
        var old_amount = $('.kundali_amounts').data('amount');
        if (isChecked) {
            $(".show_user_wallet_amount").removeClass('d-none');
            $(".user-wallet-adds").val(1);
            if (wallet_amount >= old_amount) {
                $(".user_wallet_amount_remaining").text(
                    `${(0 - 0).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                );
                $(".text_name_chnage").text(`{{ translate('download_now') }}`);
                $(".user_wallet_amount").text(
                    `${(old_amount - 0).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                );
                $('#mainProductPrice').text(
                    `${(0 - 0).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                );
            } else {
                $(".user_wallet_amount").text(
                    `${(wallet_amount - 0).toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`
                );
                $(".text_name_chnage").text(`{{ translate('make_payment') }}`);
                let remainingAmount = old_amount - wallet_amount;
                let formattedAmount = remainingAmount.toLocaleString("en-US", {
                    style: "currency",
                    currency: "{{ getCurrencyCode() }}"
                });
                $(".user_wallet_amount_remaining").text(`-${formattedAmount}`);
                $('#mainProductPrice').text(`${formattedAmount}`);
            }
        } else {
            $(".show_user_wallet_amount").addClass('d-none');
            $(".user-wallet-adds").val(0);
            $(".text_name_chnage").text(`{{ translate('make_payment') }}`);
            let formattedAmount1 = (old_amount - 0).toLocaleString("en-US", {
                style: "currency",
                currency: "{{ getCurrencyCode() }}"
            });
            $('#mainProductPrice').text(`${formattedAmount1}`);
        }
    }
</script>
@endpush