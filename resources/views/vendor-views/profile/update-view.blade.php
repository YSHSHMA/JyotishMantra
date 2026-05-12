@extends('layouts.back-end.app-seller')

@section('title', translate('profile_Settings'))
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places">
    </script>
    <style>
        .form-control.is-invalid,
        .form-control.is-invalid:focus {
            box-shadow: 0 0 10px rgba(237, 76, 120, .1);
            border-bottom: 1px solid red !important;
        }

        .form-control.is-valid,
        .form-control.is-valid:focus {
            border-bottom: 1px solid #00c9a7 !important;
        }

        #msform {
            text-align: center;
            position: relative;
            margin-top: 20px;
        }

        #msform fieldset .form-card {
            background: white;
            border: 0 none;
            border-radius: 0px;
            box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
            padding: 20px 40px 30px 40px;
            box-sizing: border-box;
            width: 94%;
            margin: 0 3% 20px 3%;

            /*stacking fieldsets above each other*/
            position: relative;
        }

        #msform fieldset {
            background: white;
            border: 0 none;
            border-radius: 0.5rem;
            box-sizing: border-box;
            width: 100%;
            margin: 0;
            padding-bottom: 20px;

            /*stacking fieldsets above each other*/
            position: relative;
        }

        /*Hide all except first fieldset*/
        #msform fieldset:not(:first-of-type) {
            display: none;
        }

        #msform fieldset .form-card {
            text-align: left;
            color: #9E9E9E;
        }

        #msform input,
        #msform textarea {
            padding: 0px 8px 4px 8px;
            border: none;
            border-bottom: 1px solid #ccc;
            border-radius: 0px;
            /* margin-bottom: 25px; */
            margin-top: 2px;
            width: 100%;
            box-sizing: border-box;
            font-family: montserrat;
            color: #2C3E50;
            font-size: 16px;
            letter-spacing: 1px;
        }

        #msform input:focus,
        #msform textarea:focus {
            -moz-box-shadow: none !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: none;
            font-weight: bold;
            border-bottom: 2px solid skyblue;
            outline-width: 0;
        }

        /*Blue Buttons*/
        #msform .action-button {
            width: 100px;
            /* background: skyblue; */
            font-weight: bold;
            color: white;
            border: 0 none;
            border-radius: 0px;
            cursor: pointer;
            padding: 10px 5px;
            margin: 10px 5px;
        }

        #msform .action-button:hover,
        #msform .action-button:focus {
            box-shadow: 0 0 0 2px white, 0 0 0 3px skyblue;
        }

        /*Previous Buttons*/
        #msform .action-button-previous {
            width: 100px;
            background: #616161;
            font-weight: bold;
            color: white;
            border: 0 none;
            border-radius: 0px;
            cursor: pointer;
            padding: 10px 5px;
            margin: 10px 5px;
        }

        #msform .action-button-previous:hover,
        #msform .action-button-previous:focus {
            box-shadow: 0 0 0 2px white, 0 0 0 3px #616161;
        }

        /*Dropdown List Exp Date*/
        select.list-dt {
            border: none;
            outline: 0;
            border-bottom: 1px solid #ccc;
            padding: 2px 5px 3px 5px;
            margin: 2px;
        }

        select.list-dt:focus {
            border-bottom: 2px solid skyblue;
        }

        /*The background card*/
        .card {
            z-index: 0;
            border: none;
            border-radius: 0.5rem;
            position: relative;
        }

        /*FieldSet headings*/
        .fs-title {
            font-size: 25px;
            color: #2C3E50;
            margin-bottom: 10px;
            font-weight: bold;
            text-align: left;
        }

        /*progressbar*/
        #progressbar {
            margin-bottom: 30px;
            overflow: hidden;
            color: lightgrey;
        }

        #progressbar .active {
            color: #000000;
        }

        #progressbar li {
            list-style-type: none;
            font-size: 12px;
            width: 19%;
            float: left;
            position: relative;
        }

        /*Icons in the ProgressBar*/

        #progressbar #personal:before {
            font-family: FontAwesome;
            content: "\f007";
        }

        #progressbar #shops:before {
            font-family: "The-Icon-of";
            content: "\eb2f";
        }

        #progressbar #document:before {
            font-family: "The-Icon-of";
            content: "\ea8f";
        }

        #progressbar #bank_information:before {
            font-family: FontAwesome;
            content: "\f023";
        }

        #progressbar #confirm:before {
            font-family: FontAwesome;
            content: "\f00c";
        }

        /*ProgressBar before any progress*/
        #progressbar li:before {
            width: 50px;
            height: 50px;
            line-height: 45px;
            display: block;
            font-size: 18px;
            color: #ffffff;
            background: lightgray;
            border-radius: 50%;
            margin: 0 auto 10px auto;
            padding: 2px;
        }

        /*ProgressBar connectors*/
        #progressbar li:after {
            content: '';
            width: 100%;
            height: 2px;
            background: lightgray;
            position: absolute;
            left: 0;
            top: 25px;
            z-index: -1;
        }

        /*Color number of the step and the connector before it*/
        #progressbar li.active:before,
        #progressbar li.active:after {
            background: skyblue;
        }

        /*Imaged Radio Buttons*/
        .radio-group {
            position: relative;
            margin-bottom: 25px;
        }

        .radio {
            display: inline-block;
            width: 204;
            height: 104;
            border-radius: 0;
            background: lightblue;
            box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
            cursor: pointer;
            margin: 8px 2px;
        }

        .radio:hover {
            box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.3);
        }

        .radio.selected {
            box-shadow: 1px 1px 2px 2px rgba(0, 0, 0, 0.1);
        }

        /*Fit image in bootstrap div*/
        .fit-image {
            width: 100%;
            object-fit: cover;
        }
    </style>
@endpush
@section('content')
    {{-- 2fa modal --}}
    <div class="modal fade" id="qr_code_modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">2FA Authentication</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-2">
                        <h3 class="mb-0">Add Authenticator App</h3>
                    </div>
                    <h5 class="mb-2 pt-1 text-break">Authenticator Apps</h5>
                    <p class="mb-4">Using an authenticator app like Google Authenticator, Microsoft Authenticator, Authy,
                        or 1Password, scan the QR code. It will generate a 4-digit code for you to enter below to Activate 2
                        FACTOR Authentication.</p>
                    <div class="text-center" id="svg_code">
                        {{-- <img src="" id="qr_code" alt="QR Code" width="140"> --}}
                    </div>
                    <div class="mb-4">
                        <form id="OtpAPI" method="POST">
                            @csrf
                            <label class="form-label" for="fname">Enter Your OTP</label>
                            <input type="hidden" name="id" class="form-control" value="{{ $vendor['id'] }}"
                                required />
                            <input type="text" id="otp" name="otp" class="form-control" placeholder="Enter OTP"
                                required />
                            <div class="text-end pt-3">
                                <button type="submit" class="btn btn-primary waves-effect waves-light"><span
                                        class="align-middle d-none d-sm-inline-block" value="Submit">Submit</span><i
                                        class="ti ti-arrow-right ti-xs ms-1 scaleX-n1-rtl"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- main --}}
    <div class="content container-fluid">
        <div class="mb-3">
            <div class="row gy-2 align-items-center">
                <div class="col-sm">
                    <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/support-ticket.png') }}" alt="">
                        {{ translate('settings') }}
                    </h2>
                </div>
                <div class="col-sm-auto">
                    <a class="btn btn--primary" href="{{ route('vendor.dashboard.index') }}">
                        <i class="tio-home mr-1"></i> {{ translate('dashboard') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?php
                    $minimumOrderAmountStatus = getWebConfig(name: 'minimum_order_amount_status');
                    $minimumOrderAmountByVendor = getWebConfig(name: 'minimum_order_amount_by_seller');
                    $freeDeliveryStatus = getWebConfig(name: 'free_delivery_status');
                    $freeDeliveryResponsibility = getWebConfig(name: 'free_delivery_responsibility');
                    ?>
                    <div class="px-4 pt-3">
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            <li class="nav-item" onclick="handleTabSwitch('general1')">
                                <span class="nav-link text-capitalize form-system-language-tab active cursor-pointer"
                                    id="general-section-link"><i class="tio-user-outlined nav-icon"></i>
                                    {{ translate('basic_Information') }}</span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link text-capitalize form-system-language-tab cursor-pointer"
                                    id="password-section-link"><i class="tio-lock-outlined nav-icon"></i>
                                    {{ translate('password') }}</span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link text-capitalize form-system-language-tab cursor-pointer"
                                    id="general-shops-section-link"><i
                                        class="tio-settings_outlined nav-icon">settings_outlined</i>
                                    {{ translate('General') }}</span>
                            </li>
                            @if (
                                ($minimumOrderAmountStatus && $minimumOrderAmountByVendor) ||
                                    ($freeDeliveryStatus && $freeDeliveryResponsibility == 'seller'))
                                <li class="nav-item">
                                    <span class="nav-link text-capitalize form-system-language-tab cursor-pointer"
                                        id="Order-settings-section-link"><i
                                            class="tio-shopping_cart_add nav-icon">shopping_cart_add</i>
                                        {{ translate('Order_settings') }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="form-system-language-form" id="general-section-form">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    $getUniqueArray = [
                                        'f_name' => 0,
                                        'phone' => 0,
                                        'email' => 0,
                                        'user_image' => 0,
                                        'image' => 0,
                                        'banner' => 0,
                                        'bank_name' => 0,
                                        'branch' => 0,
                                        'ifsc' => 0,
                                        'account_no' => 0,
                                        'holder_name' => 0,
                                        'cancel_check' => 0,
                                        'gst' => 0,
                                        'aadhar_number' => 0,
                                        'aadhar_front_image' => 0,
                                        'aadhar_back_image' => 0,
                                        'pan_number' => 0,
                                        'pancard_image' => 0,
                                        'name' => 0,
                                        'building_no' => 0,
                                        'address' => 0,
                                        'pincode' => 0,
                                        'gumasta' => 0,
                                        'fassai_no' => 0,
                                        'fassai_image' => 0,
                                        'contact' => 0,
                                    ];
                                    if (auth('seller')->user()->update_seller_status == 2) {
                                        if ($vendor->all_doc_info && json_decode($vendor->all_doc_info, true)) {
                                            foreach (json_decode($vendor->all_doc_info, true) as $key => $value) {
                                                if (array_key_exists($key, $getUniqueArray) && $value == 2) {
                                                    $getUniqueArray[$key] = 2;
                                                } elseif (array_key_exists($key, $getUniqueArray) && $value == 1) {
                                                    $getUniqueArray[$key] = 1;
                                                }
                                            }
                                        }
                                    }
                                    
                                    ?>


                                    @if (auth('seller')->user()->update_seller_status == 1 || auth('seller')->user()->update_seller_status == 2)
                                        <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                                            <h2 class="text-center"><strong>Seller Information</strong></h2>
                                            <div class="row">
                                                <div class="col-md-12 mx-0">
                                                    <form id="msform" class="form_seller_info"
                                                        enctype="multipart/form-data">
                                                        <!-- progressbar -->
                                                        <ul id="progressbar">
                                                            <li class="active" id="personal"><strong>Personal</strong></li>
                                                            <li id="shops"><strong>Shop</strong></li>
                                                            <li id="document"><strong>Doc</strong></li>
                                                            <li id="bank_information"><strong>Bank Information</strong></li>
                                                            <li id="confirm" class="finish_points"><strong>Finish</strong>
                                                            </li>
                                                        </ul>
                                                        <!-- fieldsets -->
                                                        <fieldset>
                                                            <div class="form-card">
                                                                <h2 class="fs-title">Personal Information</h2>
                                                                <div class="row">
                                                                    <label for="firstNameLabel"
                                                                        class="col-sm-3 col-form-label input-label">{{ translate('full_Name') }}
                                                                        <i class="tio-help-outlined text-body ml-1"
                                                                            data-toggle="tooltip" data-placement="right"
                                                                            title="{{ ucwords($vendor->f_name . ' ' . $vendor->l_name) }}"></i></label>
                                                                    <div class="col-sm-9">
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-3">
                                                                                <input type="hidden" name="id"
                                                                                    value="{{ $vendor->id ?? '' }}">
                                                                                <label for="name"
                                                                                    class="title-color">{{ translate('first_Name') }}
                                                                                    <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text" name="f_name"
                                                                                    value="{{ $vendor->f_name ?? '' }}"
                                                                                    class="form-control {{ $getUniqueArray['f_name'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['f_name'] == 2 ? 'is-invalid' : '' }} "
                                                                                    required
                                                                                    {{ $getUniqueArray['f_name'] == 1 ? 'readOnly' : '' }}>
                                                                                <span
                                                                                    class="text-danger font-weight-bolder">{{ $getUniqueArray['f_name'] == 2 ? 'Please Enter Correct First Name' : '' }}</span>
                                                                            </div>
                                                                            <div class="col-md-6 mb-3">
                                                                                <label for="name"
                                                                                    class="title-color">{{ translate('last_Name') }}
                                                                                    <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text" name="l_name"
                                                                                    value="{{ $vendor->l_name ?? '' }}"
                                                                                    class="form-control {{ $getUniqueArray['f_name'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['f_name'] == 2 ? 'is-invalid' : '' }}"
                                                                                    required
                                                                                    {{ $getUniqueArray['f_name'] == 1 ? 'readOnly' : '' }}>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <label for="phoneLabel"
                                                                        class="col-sm-3 col-form-label input-label">{{ translate('phone') }}
                                                                    </label>
                                                                    <div class="col-sm-9 mb-3">
                                                                        <input
                                                                            class="form-control form-control-user phone-input-with-country-picker {{ $getUniqueArray['phone'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['phone'] == 2 ? 'is-invalid' : '' }}"
                                                                            type="tel" id="exampleInputPhone"
                                                                            value="{{ old('phone', $vendor->phone ?? '') }}"
                                                                            placeholder="{{ translate('enter_phone_number') }}"
                                                                            required
                                                                            {{ $getUniqueArray['phone'] == 1 ? 'readOnly' : '' }}>
                                                                        <div class="">
                                                                            <input type="text"
                                                                                class="country-picker-phone-number w-50"
                                                                                value="{{ $vendor->phone ?? '' }}"
                                                                                name="phone" hidden readonly>
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['phone'] == 2 ? 'Please Enter Correct Phone Number' : '' }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="row form-group">
                                                                    <label for="newEmailLabel"
                                                                        class="col-sm-3 col-form-label input-label">{{ translate('email') }}</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="email"
                                                                            class="form-control {{ $getUniqueArray['email'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['email'] == 2 ? 'is-invalid' : '' }}"
                                                                            name="email" id="newEmailLabel"
                                                                            value="{{ $vendor->email ?? '' }}"
                                                                            placeholder="{{ translate('enter_new_email_address') }}"
                                                                            readonly
                                                                            {{ $getUniqueArray['email'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['email'] == 2 ? 'Please Enter Correct Email Id' : '' }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="name"
                                                                            class="title-color text-capitalize">{{ translate('upload_image') }}</label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <div class="custom-file text-left">
                                                                            <input type="file" name="user_image"
                                                                                id="custom-file-upload"
                                                                                class="custom-file-input image-input {{ $getUniqueArray['user_image'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['user_image'] == 2 ? 'is-invalid' : '' }}"
                                                                                data-image-id="user_image_viewer"
                                                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['user_image'] == 1 ? 'disabled' : '' }}
                                                                                {{ $getUniqueArray['user_image'] == 2 ? 'onchange=$(\'.user_image-image\').addClass(\'d-none\')' : '' }}>
                                                                            <label
                                                                                class="custom-file-label text-capitalize"
                                                                                for="custom-file-upload">{{ translate('choose_file') }}</label>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            @if ($getUniqueArray['user_image'] == 2)
                                                                                <img class="upload-img-view user_image-image"
                                                                                    src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                    style="position: absolute;" />
                                                                            @endif
                                                                            <img class="upload-img-view"
                                                                                id="user_image_viewer"
                                                                                src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->image ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('user_image') }}" />
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['user_image'] == 2 ? 'Please Choose Correct Profile Image' : '' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="button"
                                                                class="next stepn action-button btn-info"
                                                                value="Next Step" />
                                                        </fieldset>
                                                        <fieldset>
                                                            <div class="form-card">
                                                                <h2 class="fs-title">shop Information</h2>
                                                                <div class="row">
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="name"
                                                                            class="title-color text-capitalize">{{ translate('shop_name') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <input type="text" name="name"
                                                                            value="{{ $shop->name ?? '' }}"
                                                                            class="form-control {{ $getUniqueArray['name'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['name'] == 2 ? 'is-invalid' : '' }}"
                                                                            placeholder="Enter Shop Name" required
                                                                            {{ $getUniqueArray['name'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['name'] == 2 ? 'Please Enter Correct Shop Name' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="name"
                                                                            class="title-color">{{ translate('contact') }}</label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <input
                                                                            class="form-control form-control-user phone-input-with-country-picker {{ $getUniqueArray['contact'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['contact'] == 2 ? 'is-invalid' : '' }}"
                                                                            type="tel" id="exampleInputPhone"
                                                                            value="{{ old('phone', $shop->contact ?? '') }}"
                                                                            placeholder="{{ translate('enter_phone_number') }}"
                                                                            required
                                                                            {{ $getUniqueArray['contact'] == 1 ? 'readOnly' : '' }}>
                                                                        <div class="">
                                                                            <input type="text"
                                                                                class="country-picker-phone-number w-50"
                                                                                value="{{ $shop->contact ?? '' }}"
                                                                                name="contact" hidden readonly>
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['contact'] == 2 ? 'Please Enter Correct shop Phone Number' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="name"
                                                                            class="title-color">{{ translate('pincode') }}</label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <input type="number"
                                                                            class="form-control {{ $getUniqueArray['pincode'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['pincode'] == 2 ? 'is-invalid' : '' }}"
                                                                            name="pincode"
                                                                            value="{{ $shop->pincode ?? '' }}"
                                                                            placeholder="Enter Pin-code"
                                                                            {{ $getUniqueArray['pincode'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['pincode'] == 2 ? 'Please Enter Correct pincode' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="building_no"
                                                                            class="title-color text-capitalize">{{ translate('shop_building_no') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <input type="text" name="building_no"
                                                                            value="{{ $shop->building_no ?? '' }}"
                                                                            class="form-control {{ $getUniqueArray['building_no'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['building_no'] == 2 ? 'is-invalid' : '' }}"
                                                                            placeholder="Enter Shop Building No" required
                                                                            {{ $getUniqueArray['building_no'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['building_no'] == 2 ? 'Please Enter Correct Shop building No' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="address"
                                                                            class="title-color">{{ translate('address') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <textarea type="text" rows="2" name="address"
                                                                            class="form-control getAddress_google {{ $getUniqueArray['address'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['address'] == 2 ? 'is-invalid' : '' }}"
                                                                            id="address" required placeholder="Enter Shop Full Address"
                                                                            {{ $getUniqueArray['address'] == 1 ? 'readOnly' : '' }}>{{ $shop->address ?? '' }}</textarea>
                                                                        <input type="hidden" name="latitude"
                                                                            value="{{ $shop->latitude ?? '' }}">
                                                                        <input type="hidden" name="longitude"
                                                                            value="{{ $shop->longitude ?? '' }}">
                                                                        <input type="hidden" name="state_name"
                                                                            class="state_select_geolocation"
                                                                            value="{{ $shop->state_name ?? '' }}">
                                                                        <input type="hidden" name="city_name"
                                                                            class="City_select_geolocation"
                                                                            value="{{ $shop->city_name ?? '' }}">
                                                                        <input type="hidden" name="country_name"
                                                                            class="Country_select_geolocation"
                                                                            value="{{ $shop->country_name ?? '' }}">
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['address'] == 2 ? 'Please choose Correct address' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="name"
                                                                            class="title-color">{{ translate('Fassai_number') }}</label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <input type="number"
                                                                            class="form-control {{ $getUniqueArray['fassai_no'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['fassai_no'] == 2 ? 'is-invalid' : '' }}"
                                                                            name="fassai_no"
                                                                            value="{{ $shop->fassai_no ?? '' }}"
                                                                            placeholder="Enter Shop Fassai Number"
                                                                            {{ $getUniqueArray['fassai_no'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['fassai_no'] == 2 ? 'Please Enter Correct shop Fassai Number' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="name"
                                                                            class="title-color text-capitalize">{{ translate('upload_image') }}</label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <div class="custom-file text-left">
                                                                            <input type="file" name="image"
                                                                                id="custom-file-upload"
                                                                                class="custom-file-input image-input {{ $getUniqueArray['image'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['image'] == 2 ? 'is-invalid' : '' }}"
                                                                                data-image-id="viewer"
                                                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['image'] == 1 ? 'disabled' : '' }}
                                                                                {{ $getUniqueArray['image'] == 2 ? 'onchange=$(\'.image-image\').addClass(\'d-none\')' : '' }}>
                                                                            <label
                                                                                class="custom-file-label text-capitalize"
                                                                                for="custom-file-upload">{{ translate('choose_file') }}</label>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            @if ($getUniqueArray['image'] == 2)
                                                                                <img class="upload-img-view image-image"
                                                                                    src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                    style="position: absolute;" />
                                                                            @endif
                                                                            <img class="upload-img-view" id="viewer"
                                                                                src="{{ getValidImage(path: 'storage/app/public/shop/' . ($shop->image ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('image') }}" />
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['image'] == 2 ? 'Please Choose Correct shop Image' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <label for="name"
                                                                            class="title-color text-capitalize">{{ translate('upload_banner') }}
                                                                        </label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <div class="flex-start">
                                                                            <div class="mx-1">
                                                                                <span
                                                                                    class="text-info">{{ THEME_RATIO[theme_root_path()]['Store cover Image'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="custom-file text-left">
                                                                            <input type="file" name="banner"
                                                                                id="banner-upload"
                                                                                class="custom-file-input image-input {{ $getUniqueArray['banner'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['banner'] == 2 ? 'is-invalid' : '' }}"
                                                                                {{ $getUniqueArray['banner'] == 1 ? 'disabled' : '' }}
                                                                                data-image-id="viewer-banner"
                                                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['banner'] == 2 ? 'onchange=$(\'.banner-image\').addClass(\'d-none\')' : '' }}>
                                                                            <label
                                                                                class="custom-file-label text-capitalize"
                                                                                for="banner-upload">{{ translate('choose_file') }}</label>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            <div class="d-flex justify-content-center">
                                                                                @if ($getUniqueArray['banner'] == 2)
                                                                                    <img class="upload-img-view banner-image"
                                                                                        src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                        style="position: absolute;" />
                                                                                @endif
                                                                                <img class="upload-img-view upload-img-view__banner"
                                                                                    id="viewer-banner"
                                                                                    src="{{ getValidImage(path: 'storage/app/public/shop/banner/' . ($shop->banner ?? ''), type: 'backend-basic') }}"
                                                                                    alt="{{ translate('banner_image') }}" />
                                                                            </div>
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['banner'] == 2 ? 'Please Choose Correct shop banner' : '' }}</span>
                                                                    </div>

                                                                    <div class="col-md-6 form-group">
                                                                        <label for="fassai-upload"
                                                                            class="title-color text-capitalize">{{ translate('upload_fassai_image') }}</label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">

                                                                        <div class="flex-start">
                                                                            <div class="mx-1">
                                                                                <span
                                                                                    class="text-info">{{ THEME_RATIO[theme_root_path()]['Store cover Image'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="custom-file text-left">
                                                                            <input type="file" name="fassai_image"
                                                                                id="fassai-upload"
                                                                                class="custom-file-input image-input {{ $getUniqueArray['fassai_image'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['fassai_image'] == 2 ? 'is-invalid' : '' }}"
                                                                                {{ $getUniqueArray['fassai_image'] == 1 ? 'disabled' : '' }}
                                                                                data-image-id="fassai-preview"
                                                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['fassai_image'] == 2 ? 'onchange=$(\'.fassai_image-image\').addClass(\'d-none\')' : '' }}>
                                                                            <label
                                                                                class="custom-file-label text-capitalize"
                                                                                for="fassai-upload">{{ translate('choose_file') }}</label>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            @if ($getUniqueArray['fassai_image'] == 2)
                                                                                <img class="upload-img-view fassai_image-image"
                                                                                    src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                    style="position: absolute;" />
                                                                            @endif
                                                                            <img class="upload-img-view"
                                                                                id="fassai-preview"
                                                                                src="{{ getValidImage(path: 'storage/app/public/shop/fassai/' . ($shop->fassai_image ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('fassai_image') }}" />
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['fassai_image'] == 2 ? 'Please Choose Correct Fassai Image' : '' }}</span>
                                                                    </div>

                                                                    <div class="col-md-6 form-group">
                                                                        <label for="gumasta-upload"
                                                                            class="title-color text-capitalize">{{ translate('upload_gumasta') }}</label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <div class="flex-start">
                                                                            <div class="mx-1">
                                                                                <span
                                                                                    class="text-info">{{ THEME_RATIO[theme_root_path()]['Store cover Image'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="custom-file text-left">
                                                                            <input type="file" name="gumasta"
                                                                                id="gumasta-upload"
                                                                                class="custom-file-input image-input {{ $getUniqueArray['gumasta'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['gumasta'] == 2 ? 'is-invalid' : '' }}"
                                                                                {{ $getUniqueArray['gumasta'] == 1 ? 'readOnly' : '' }}
                                                                                data-image-id="gumasta-preview"
                                                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['gumasta'] == 2 ? 'onchange=$(\'.gumasta-image\').addClass(\'d-none\')' : '' }}>
                                                                            <label
                                                                                class="custom-file-label text-capitalize"
                                                                                for="gumasta-upload">{{ translate('choose_file') }}</label>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            @if ($getUniqueArray['gumasta'] == 2)
                                                                                <img class="upload-img-view gumasta-image"
                                                                                    src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                    style="position: absolute;" />
                                                                            @endif
                                                                            <img class="upload-img-view"
                                                                                id="gumasta-preview"
                                                                                src="{{ getValidImage(path: 'storage/app/public/shop/gumasta/' . ($shop->gumasta ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('gumasta_image') }}" />
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['gumasta'] == 2 ? 'Please Choose Correct shop Gumasta' : '' }}</span>
                                                                    </div>


                                                                </div>
                                                            </div>
                                                            <input type="button" class="previous action-button-previous"
                                                                value="Previous" />
                                                            <input type="button"
                                                                class="next stepn action-button btn-info"
                                                                value="Next Step" />
                                                        </fieldset>
                                                        <fieldset>
                                                            <div class="form-card">
                                                                <h2 class="fs-title">Doc Information</h2>
                                                                <div class="row form-group">
                                                                    <label
                                                                        class="col-sm-3 col-form-label input-label">{{ translate('aadhar_Number') }}</label>
                                                                    <div class="col-sm-3">
                                                                        <input type="text"
                                                                            class="form-control {{ $getUniqueArray['aadhar_number'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['aadhar_number'] == 2 ? 'is-invalid' : '' }}"
                                                                            id="aadhar_number" name="aadhar_number"
                                                                            value="{{ $vendor->aadhar_number }}"
                                                                            placeholder="Enter your Aadhar number"
                                                                            {{ $getUniqueArray['aadhar_number'] == 1 ? 'readOnly' : '' }}>
                                                                        <small id="aadharError"
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['aadhar_number'] == 2 ? 'Please Enter Correct Aadhar Number' : '' }}</small>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Aadhar Front Image</label>
                                                                        <div class="custom_upload_input">
                                                                            <input type="file"
                                                                                name="aadhar_front_image"
                                                                                class="custom-upload-input-file action-upload-color-image {{ $getUniqueArray['aadhar_front_image'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['aadhar_front_image'] == 2 ? 'is-invalid' : '' }}"
                                                                                id=""
                                                                                data-imgpreview="pre_img_viewer"
                                                                                accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['aadhar_front_image'] == 1 ? 'disabled' : '' }}
                                                                                {{ $getUniqueArray['aadhar_front_image'] == 2 ? 'onchange=$(\'.aadhar_front_image-image\').addClass(\'d-none\')' : '' }}>

                                                                            <div
                                                                                class="img_area_with_preview position-absolute z-index-2">
                                                                                @if ($getUniqueArray['aadhar_front_image'] == 2)
                                                                                    <img class="h-auto aspect-1 aadhar_front_image-image"
                                                                                        src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                        style="position: absolute;" />
                                                                                @endif
                                                                                <img id="pre_img_viewer"
                                                                                    class="h-auto aspect-1 bg-white"
                                                                                    src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->aadhar_front_image ?? ''), type: 'backend-basic') }}"
                                                                                    alt="">
                                                                            </div>
                                                                            <div
                                                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center align-items-center">
                                                                                    <img alt="" class="w-75"
                                                                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                                    <h3 class="text-muted">
                                                                                        {{ translate('Upload_Image') }}
                                                                                    </h3>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['aadhar_front_image'] == 2 ? 'Please Choose Correct Aadhar Image' : '' }}</span>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <label for="">Aadhar Back Image</label>
                                                                        <div class="custom_upload_input">
                                                                            <input type="file" name="aadhar_back_image"
                                                                                class="custom-upload-input-file action-upload-color-image {{ $getUniqueArray['aadhar_front_image'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['aadhar_front_image'] == 2 ? 'is-invalid' : '' }}"
                                                                                data-imgpreview="pre_img_viewer1"
                                                                                accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['aadhar_front_image'] == 1 ? 'disabled' : '' }}
                                                                                {{ $getUniqueArray['aadhar_front_image'] == 2 ? 'onchange=$(\'.aadhar_front_image-image\').addClass(\'d-none\')' : '' }}>

                                                                            <div
                                                                                class="img_area_with_preview position-absolute z-index-2">
                                                                                @if ($getUniqueArray['aadhar_front_image'] == 2)
                                                                                    <img class="h-auto aspect-1 aadhar_front_image-image"
                                                                                        src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                        style="position: absolute;" />
                                                                                @endif
                                                                                <img id="pre_img_viewer1"
                                                                                    class="h-auto aspect-1 bg-white"
                                                                                    src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->aadhar_back_image ?? ''), type: 'backend-basic') }}"
                                                                                    alt="">
                                                                            </div>
                                                                            <div
                                                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center align-items-center">
                                                                                    <img alt="" class="w-75"
                                                                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                                    <h3 class="text-muted">
                                                                                        {{ translate('Upload_Image') }}
                                                                                    </h3>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row form-group">
                                                                    <label
                                                                        class="col-sm-3 col-form-label input-label">{{ translate('pan_Number') }}</label>
                                                                    <div class="col-sm-3">
                                                                        <input type="text"
                                                                            class="form-control {{ $getUniqueArray['pan_number'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['pan_number'] == 2 ? 'is-invalid' : '' }}"
                                                                            id="pan_number" name="pan_number"
                                                                            value="{{ $vendor->pan_number ?? '' }}"
                                                                            placeholder="{{ translate('enter_your_PAN_number') }}"
                                                                            {{ $getUniqueArray['pan_number'] == 1 ? 'readOnly' : '' }}>
                                                                        <small id="panError"
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['pan_number'] == 2 ? 'Please Enter Correct Pan Number' : '' }}</small>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="">Pancard Image</label>
                                                                        <div class="custom_upload_input">
                                                                            <input type="file" name="pancard_image"
                                                                                class="custom-upload-input-file action-upload-color-image{{ $getUniqueArray['pancard_image'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['pancard_image'] == 2 ? 'is-invalid' : '' }} "
                                                                                id=""
                                                                                data-imgpreview="pre_img_viewer3"
                                                                                accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['pancard_image'] == 1 ? 'disabled' : '' }}
                                                                                {{ $getUniqueArray['pancard_image'] == 2 ? 'onchange=$(\'.pancard_image-image\').addClass(\'d-none\')' : '' }}>

                                                                            <div
                                                                                class="img_area_with_preview position-absolute z-index-2">
                                                                                @if ($getUniqueArray['pancard_image'] == 2)
                                                                                    <img class="h-auto aspect-1 pancard_image-image"
                                                                                        src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                        style="position: absolute;" />
                                                                                @endif
                                                                                <img id="pre_img_viewer3"
                                                                                    class="h-auto aspect-1 bg-white"
                                                                                    src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->pancard_image ?? ''), type: 'backend-basic') }}"
                                                                                    alt="">
                                                                            </div>
                                                                            <div
                                                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center align-items-center">
                                                                                    <img alt="" class="w-75"
                                                                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                                    <h3 class="text-muted">
                                                                                        {{ translate('Upload_Image') }}
                                                                                    </h3>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['pancard_image'] == 2 ? 'Please Choose Correct PanCard Image' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-3"></div>
                                                                </div>
                                                                <div class="row form-group">
                                                                    <label
                                                                        class="col-sm-3 col-form-label input-label">{{ translate('GST_Number') }}</label>

                                                                    <div class="col-sm-9">
                                                                        <input type="text"
                                                                            class="form-control {{ $getUniqueArray['gst'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['gst'] == 2 ? 'is-invalid' : '' }}"
                                                                            name="gst"
                                                                            {{ $getUniqueArray['gst'] == 1 ? 'readOnly' : '' }}
                                                                            value="{{ $vendor->gst ?? '' }}"
                                                                            placeholder="{{ translate('enter_your_gst_number') }}">
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['gst'] == 2 ? 'Please Enter Correct Gst Number' : '' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="button" class="previous action-button-previous"
                                                                value="Previous" />
                                                            <input type="button"
                                                                class="next stepn action-button btn-info"
                                                                value="Next Step" />
                                                        </fieldset>
                                                        <fieldset>
                                                            <div class="form-card">
                                                                <h2 class="fs-title">bank Information</h2>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="name"
                                                                            class="title-color">{{ translate('bank_Name') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="bank_name"
                                                                            value="{{ $vendor->bank_name }}"
                                                                            class="form-control {{ $getUniqueArray['bank_name'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['bank_name'] == 2 ? 'is-invalid' : '' }}"
                                                                            id="name" placeholder="Enter Bank Name"
                                                                            required
                                                                            {{ $getUniqueArray['bank_name'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['bank_name'] == 2 ? 'Please Enter Correct Bank Name' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="name"
                                                                            class="title-color">{{ translate('branch_Name') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="branch"
                                                                            value="{{ $vendor->branch }}"
                                                                            class="form-control {{ $getUniqueArray['branch'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['branch'] == 2 ? 'is-invalid' : '' }}"
                                                                            placeholder="Enter Bank Branch Name" required
                                                                            {{ $getUniqueArray['branch'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['bank_name'] == 2 ? 'Please Enter Correct Branch Name' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="account_no"
                                                                            class="title-color">{{ translate('holder_Name') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="holder_name"
                                                                            value="{{ $vendor->holder_name }}"
                                                                            class="form-control {{ $getUniqueArray['holder_name'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['holder_name'] == 2 ? 'is-invalid' : '' }}"
                                                                            placeholder="Enter Holder Name" required
                                                                            {{ $getUniqueArray['holder_name'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['holder_name'] == 2 ? 'Please Enter Correct Holder Name' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="account_no"
                                                                            class="title-color">{{ translate('account_No') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="password" name="account_no"
                                                                            value="{{ $vendor->account_no }}"
                                                                            class="form-control {{ $getUniqueArray['account_no'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['account_no'] == 2 ? 'is-invalid' : '' }}"
                                                                            placeholder="Enter Bank Account Number"
                                                                            required
                                                                            {{ $getUniqueArray['account_no'] == 1 ? 'readOnly' : '' }}
                                                                            onclick="$('.confirm_account_no').val('')">
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['account_no'] == 2 ? 'Please Enter Correct Account Number' : '' }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="con_account_no"
                                                                            class="title-color">{{ translate('confirm_account_No') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text"
                                                                            value="{{ $getUniqueArray['account_no'] == 2 ? '' : $vendor->account_no }}"
                                                                            class="form-control confirm_account_no"
                                                                            placeholder="Enter Bank Confirm Account Number"
                                                                            required
                                                                            {{ $getUniqueArray['account_no'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="font-weight-bold confirm_account_no-error text-danger"></span>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="ifsc"
                                                                            class="title-color">{{ translate('IFSC_code') }}
                                                                            <span class="text-danger">*</span></label>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="text" name="ifsc"
                                                                            value="{{ $vendor->ifsc }}"
                                                                            class="form-control {{ $getUniqueArray['ifsc'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['ifsc'] == 2 ? 'is-invalid' : '' }}"
                                                                            placeholder="Enter Bank IFSC Code" required
                                                                            {{ $getUniqueArray['ifsc'] == 1 ? 'readOnly' : '' }}>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['ifsc'] == 2 ? 'Please Enter Correct Bank Ifsc code' : '' }}</span>
                                                                    </div>

                                                                    <div class="col-md-6 form-group">
                                                                        <label for="gumasta-upload"
                                                                            class="title-color text-capitalize">{{ translate('upload_cancel_check') }}</label>
                                                                    </div>
                                                                    <div class="col-md-6 form-group">
                                                                        <div class="flex-start">
                                                                            <div class="mx-1">
                                                                                <span
                                                                                    class="text-info">{{ THEME_RATIO[theme_root_path()]['Store cover Image'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="custom-file text-left">
                                                                            <input type="file" name="cancel_check"
                                                                                id="cancel_check-upload"
                                                                                class="custom-file-input image-input {{ $getUniqueArray['cancel_check'] == 1 ? 'is-valid' : '' }} {{ $getUniqueArray['cancel_check'] == 2 ? 'is-invalid' : '' }}"
                                                                                data-image-id="cancel_check-preview"
                                                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                {{ $getUniqueArray['cancel_check'] == 1 ? 'readOnly' : '' }}
                                                                                {{ $getUniqueArray['cancel_check'] == 2 ? 'onchange=$(\'.cancel_check-image\').addClass(\'d-none\')' : '' }}>
                                                                            <label
                                                                                class="custom-file-label text-capitalize"
                                                                                for="cancel_check-upload">{{ translate('choose_file') }}</label>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            @if ($getUniqueArray['cancel_check'] == 2)
                                                                                <img class="upload-img-view upload-img-view__banner cancel_check-image"
                                                                                    src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}"
                                                                                    style="position: absolute;" />
                                                                            @endif
                                                                            <img class="upload-img-view upload-img-view__banner upload-img-view"
                                                                                id="cancel_check-preview"
                                                                                src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->cancel_check ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('cancel_check_image') }}" />
                                                                        </div>
                                                                        <span
                                                                            class="text-danger font-weight-bolder">{{ $getUniqueArray['cancel_check'] == 2 ? 'Please Choose Correct Bank Cancel Check' : '' }}</span>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <input type="button" class="previous action-button-previous"
                                                                value="Previous" />
                                                            <input type="button" class="next last action-button btn-info"
                                                                value="Confirm" />
                                                        </fieldset>
                                                        <fieldset class="last_message_success">
                                                            <div class="form-card">
                                                                <h2 class="fs-title text-center">Success !</h2>
                                                                <br><br>
                                                                <div class="row justify-content-center">
                                                                    <div class="col-3">
                                                                        <img src="https://img.icons8.com/color/96/000000/ok--v2.png"
                                                                            class="fit-image">
                                                                    </div>
                                                                </div>
                                                                <br><br>
                                                                <div class="row justify-content-center">
                                                                    <div class="col-7 text-center">
                                                                        <h5>You Have Successfully Update Informtion</h5>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="card px-0 pb-0 mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                                                            <button type="button"
                                                                class="navbar-toggler btn btn-block btn-white mb-3"
                                                                aria-label="Toggle navigation" aria-expanded="false"
                                                                aria-controls="navbarVerticalNavMenu"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#navbarVerticalNavMenu">
                                                                <span
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <span
                                                                        class="h5 mb-0">{{ translate('nav_menu') }}</span>
                                                                    <span class="navbar-toggle-default">
                                                                        <i class="tio-menu-hamburger"></i>
                                                                    </span>
                                                                    <span class="navbar-toggle-toggled">
                                                                        <i class="tio-clear"></i>
                                                                    </span>
                                                                </span>
                                                            </button>

                                                            <div id="navbarVerticalNavMenu"
                                                                class="collapse navbar-collapse">
                                                                <ul id="navbarSettings"
                                                                    class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active"
                                                                            href="javascript:void(0);"
                                                                            data-target="general1">
                                                                            <i
                                                                                class="tio-user-outlined nav-icon"></i>{{ translate('basic_Information') }}
                                                                        </a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" href="javascript:void(0);"
                                                                            data-target="shops1">
                                                                            <i
                                                                                class="tio-shop nav-icon"></i>{{ translate('Shop') }}
                                                                        </a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" href="javascript:void(0);"
                                                                            data-target="document">
                                                                            <i class="tio-documents nav-icon"></i>
                                                                            {{ translate('Document') }}
                                                                        </a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" href="javascript:void(0);"
                                                                            data-target="banks">
                                                                            <i class="tio-museum nav-icon"></i>
                                                                            {{ translate('Bank') }}
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-9">
                                                        <div class="card mb-3 mb-lg-5" id="general1-div">
                                                            <div class="row p-4 ">
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('first_Name') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->f_name ?? '' }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('last_Name') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->l_name ?? '' }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('phone') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->phone ?? '' }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('email') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->email ?? '' }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('image') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <img id="fassai-preview"
                                                                                src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->image ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('fassai_image') }}" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('2FA') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="custom-control custom-switch mr-2">
                                                                        <input type="hidden" id="id"
                                                                            value="{{ $vendor['id'] }}">
                                                                        <input type="hidden" id="email"
                                                                            value="{{ $vendor['email'] }}">
                                                                        <input type="checkbox"
                                                                            class="custom-control-input consultation-charge-checkbox"
                                                                            id="consultationChargeCustomSwitch"
                                                                            onchange="enable_2fa(this)"
                                                                            {{ $vendor['enable_2fa'] == 1 ? 'checked' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="consultationChargeCustomSwitch"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3 mb-lg-5" id="shops1-div"
                                                            style="display: none;">
                                                            <div class="row p-4 ">
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('shop_name') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $shop->name ?? '' }}</div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('contact') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $shop->contact ?? '' }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('pincode') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $shop->pincode ?? '' }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('building_no') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $shop->building_no ?? '' }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('address') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $shop->address ?? '' }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('Fassai_number') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    {{ $shop->fassai_no ?? '- -' }}</div>

                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('fassai_image') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <img id="fassai-preview"
                                                                                src="{{ getValidImage(path: 'storage/app/public/shop/fassai/' . ($shop->fassai_image ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('fassai_image') }}" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('gumasta_image') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <img src="{{ getValidImage(path: 'storage/app/public/shop/gumasta/' . ($shop->gumasta ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('fassai_image') }}" />
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('shop_image') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <img id="viewer"
                                                                                src="{{ getValidImage(path: 'storage/app/public/shop/' . ($shop->image ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('image') }}" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('shop_banner') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <img src="{{ getValidImage(path: 'storage/app/public/shop/banner/' . ($shop->banner ?? ''), type: 'backend-basic') }}"
                                                                                alt="{{ translate('image') }}" />
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="card mb-3 mb-lg-5" id="document-div"
                                                            style="display: none;">
                                                            <div class="row p-4 ">
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('aadhar_Number') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->aadhar_number }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('aadhar_image') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <img class="h-auto aspect-1 bg-white"
                                                                                src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->aadhar_front_image ?? ''), type: 'backend-basic') }}"
                                                                                alt="">
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <img class="h-auto aspect-1 bg-white"
                                                                                src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->aadhar_back_image ?? ''), type: 'backend-basic') }}"
                                                                                alt="">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('pancard_Number') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    {{ $vendor->pan_number ?? '' }}</div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('pancard_image') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <img class="h-auto aspect-1 bg-white"
                                                                                src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->pancard_image ?? ''), type: 'backend-basic') }}"
                                                                                alt="">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('GST_Number') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->gst ?? '' }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3 mb-lg-5" id="banks-div"
                                                            style="display: none;">
                                                            <div class="row p-4 ">
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('bank_Name') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->bank_name }}</div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('branch_Name') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->branch }}</div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('holder_Name') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->holder_name }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('account_No') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->account_no }}
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('IFSC_code') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">{{ $vendor->ifsc }}</div>
                                                                <div class="col-md-6 mt-2">
                                                                    <lable class="col-form-label font-weight-bold">
                                                                        {{ translate('cancel_check') }}</lable>
                                                                </div>
                                                                <div class="col-md-6 mt-2">
                                                                    <div class="row">
                                                                        <div class="col-md-12 form-group">
                                                                            <div class="text-center">
                                                                                <img class="upload-img-view upload-img-view__banner bg-white"
                                                                                    src="{{ getValidImage(path: 'storage/app/public/seller/' . ($vendor->cancel_check ?? ''), type: 'backend-basic') }}"
                                                                                    alt="">
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
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-system-language-form d-none" id="password-section-form">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-3 mb-lg-5">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ translate('change_your_password') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <form id="update-password-form"
                                                action="{{ route('vendor.profile.update', [auth('seller')->id()]) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @method('PATCH')
                                                @csrf
                                                <div class="row form-group">
                                                    <label for="newPassword"
                                                        class="col-sm-3 col-form-label input-label d-flex align-items-center">
                                                        {{ translate('new_Password') }}
                                                        <span class="input-label-secondary cursor-pointer"
                                                            data-toggle="tooltip" data-placement="right" title=""
                                                            data-original-title="{{ translate('The_password_must_be_at_least_8_characters_long_and_contain_at_least_one_uppercase_letter') . ',' . translate('_one_lowercase_letter') . ',' . translate('_one_digit_') . ',' . translate('_one_special_character') . ',' . translate('_and_no_spaces') . '.' }}">
                                                            <img alt="" width="16"
                                                                src={{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}
                                                                alt="" class="m-1">
                                                        </span>
                                                    </label>

                                                    <div class="col-sm-9">
                                                        <div class="input-group input-group-merge">
                                                            <input type="password"
                                                                class="js-toggle-password form-control password-check"
                                                                id="newPassword" autocomplete="off" name="password"
                                                                required minlength="8"
                                                                placeholder="{{ translate('password_minimum_8_characters') }}"
                                                                data-hs-toggle-password-options='{
                                                             "target": "#changePassTarget",
                                                            "defaultClass": "tio-hidden-outlined",
                                                            "showClass": "tio-visible-outlined",
                                                            "classChangeTarget": "#changePassIcon"
                                                    }'>
                                                            <div id="changePassTarget" class="input-group-append">
                                                                <a class="input-group-text" href="javascript:">
                                                                    <i id="changePassIcon"
                                                                        class="tio-visible-outlined"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <span class="text-danger mx-1 password-error"></span>
                                                    </div>

                                                </div>
                                                <div class="row form-group">
                                                    <label for="confirmNewPasswordLabel"
                                                        class="col-sm-3 col-form-label input-label pt-0">
                                                        {{ translate('confirm_Password') }} </label>
                                                    <div class="col-sm-9">
                                                        <div class="mb-3">
                                                            <div class="input-group input-group-merge">
                                                                <input type="password"
                                                                    class="js-toggle-password form-control"
                                                                    name="confirm_password" required
                                                                    id="confirmNewPasswordLabel"
                                                                    placeholder="{{ translate('confirm_password') }}"
                                                                    autocomplete="off"
                                                                    data-hs-toggle-password-options='{
                                                             "target": "#changeConfirmPassTarget",
                                                            "defaultClass": "tio-hidden-outlined",
                                                            "showClass": "tio-visible-outlined",
                                                            "classChangeTarget": "#changeConfirmPassIcon"
                                                    }'>
                                                                <div id="changeConfirmPassTarget"
                                                                    class="input-group-append">
                                                                    <a class="input-group-text" href="javascript:">
                                                                        <i id="changeConfirmPassIcon"
                                                                            class="tio-visible-outlined"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="button" data-form-id="update-password-form"
                                                        data-message="{{ translate('want_to_update_vendor_password') . '?' }}"
                                                        class="btn btn--primary {{ env('APP_MODE') != 'demo' ? 'form-submit' : 'call-demo' }}">{{ translate('save_changes') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-system-language-form d-none" id="general-shops-section-form">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-3 mb-lg-5">
                                        <div class="card-body">
                                            <form action="{{ route('vendor.shop.close-shop-temporary', [$shop['id']]) }}"
                                                method="POST" id="temporary-close-form" data-from="shop">
                                                @csrf
                                                <div
                                                    class="border rounded border-color-c1 px-4 py-3 d-flex justify-content-between mb-1">
                                                    <h5 class="mb-0 d-flex gap-1 c1">
                                                        {{ translate('temporary_close') }}
                                                    </h5>
                                                    <input type="hidden" name="id" value="{{ $shop->id }}">
                                                    <div class="position-relative">
                                                        <label class="switcher">
                                                            <input type="checkbox"
                                                                class="switcher_input toggle-switch-message"
                                                                name="status" value="1" id="temporary-close"
                                                                {{ isset($shop->temporary_close) && $shop->temporary_close == 1 ? 'checked' : '' }}
                                                                data-modal-id="toggle-status-modal"
                                                                data-toggle-id="temporary-close"
                                                                data-on-image="maintenance_mode-on.png"
                                                                data-off-image="maintenance_mode-off.png"
                                                                data-on-title="{{ translate('want_to_enable_the_Temporary_Close') . '?' }}"
                                                                data-off-title="{{ translate('want_to_disable_the_Temporary_Close') . '?' }}"
                                                                data-on-message="<p>{{ translate('if_you_enable_this_option_your_shop_will_be_shown_as_temporarily_closed_in_the_user_app_and_website_and_customers_cannot_add_products_from_your_shop') }}</p>"
                                                                data-off-message="<p>{{ translate('if_you_disable_this_option_your_shop_will_be_open_in_the_user_app_and_website_and_customers_can_add_products_from_your_shop') }}</p>">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </form>
                                            <p>{{ '*' . translate('by_turning_on_temporary_close_mode_your_shop_will_be_shown_as_temporary_off_in_the_website_and_app_for_the_customers._they_cannot_purchase_or_place_order_from_your_shop') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <form action="{{ route('vendor.shop.update-vacation', [$shop['id']]) }}"
                                        method="post">
                                        @csrf
                                        <div class="modal-header border-bottom pb-2">
                                            <div>
                                                <h5 class="modal-title text-capitalize" id="exampleModalLabel">
                                                    {{ translate('vacation_mode') }}</h5>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="switcher">
                                                        <input type="checkbox" name="vacation_status"
                                                            class="switcher_input" id="vacation_close"
                                                            {{ $shop->vacation_status == 1 ? 'checked' : '' }}>
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-5">
                                                *{{ translate('set_vacation_mode_for_shop_means_you_will_be_not_available_receive_order_and_provider_products_for_placed_order_at_that_time') }}
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label
                                                        class="text-capitalize">{{ translate('vacation_start') }}</label>
                                                    <input type="date" name="vacation_start_date"
                                                        value="{{ $shop->vacation_start_date }}" id="start-date-time"
                                                        class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label
                                                        class="text-capitalize">{{ translate('vacation_end') }}</label>
                                                    <input type="date" name="vacation_end_date"
                                                        value="{{ $shop->vacation_end_date }}" id="end-date-time"
                                                        class="form-control" required>
                                                </div>
                                                <div class="col-md-12 mt-2 ">
                                                    <label
                                                        class="text-capitalize">{{ translate('vacation_note') }}</label>
                                                    <textarea class="form-control" name="vacation_note" id="vacation_note">{{ $shop->vacation_note }}</textarea>
                                                </div>
                                            </div>
                                            <div class="text-end gap-5 mt-2">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ translate('close') }}</button>
                                                <button type="submit"
                                                    class="btn btn--primary">{{ translate('update') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="form-system-language-form d-none" id="Order-settings-section-form">
                            <div class="row my-3 gy-3">
                                @if ($minimumOrderAmountStatus && $minimumOrderAmountByVendor)
                                    <form action="{{ route('vendor.shop.update-order-settings', [$vendor['id']]) }}"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="card h-100">
                                            <div class="card-header">
                                                <h5 class="text-capitalize mb-0">
                                                    <i class="tio-dollar-outlined"></i>
                                                    {{ translate('minimum_order_amount') }}
                                                </h5>
                                            </div>
                                            <div class="card-body text-start">
                                                <div class="mb-3">
                                                    <label class="title-color" for="minimum_order_amount">
                                                        {{ translate('amount') }} ({{ getCurrencySymbol() }})
                                                    </label>
                                                    <span class="input-label-secondary cursor-pointer"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="{{ translate('set_the_minimum_order_amount_a_customer_must_order_from_this_vendor_shop') }}">
                                                        <img width="16"
                                                            src="{{ dynamicAsset(path: '/public/assets/back-end/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span>
                                                    <input type="number" step="0.01" class="form-control w-100"
                                                        id="minimum_order_amount" name="minimum_order_amount"
                                                        min="1"
                                                        value="{{ usdToDefaultCurrency(amount: $vendor->minimum_order_amount) ?? 0 }}"
                                                        placeholder="{{ translate('0.00') }}">
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" id="submit"
                                                        class="btn btn--primary px-4">{{ translate('submit') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                                @if ($freeDeliveryStatus && $freeDeliveryResponsibility == 'seller')
                                    <div class="col-sm-12 col-md-6">
                                        <form action="{{ route('vendor.shop.update-order-settings', [$vendor['id']]) }}"
                                            method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <h5 class="text-capitalize mb-0">
                                                        <i class="tio-dollar-outlined"></i>
                                                        {{ translate('free_delivery_over_amount') }}
                                                    </h5>
                                                </div>
                                                <div class="card-body text-start">
                                                    <div class="row align-items-end">
                                                        <div class="col-xl-6 col-md-6">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center gap-10 form-control form-group">
                                                                <span class="title-color d-flex align-items-center gap-1">
                                                                    {{ translate('free_Delivery') }}
                                                                    <span class="input-label-secondary cursor-pointer"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="{{ translate('if_enabled_free_delivery_will_be_available_when_customers_order_over_a_certain_amount') }}">
                                                                        <img width="16"
                                                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                            alt="">
                                                                    </span>
                                                                </span>

                                                                <label class="switcher" for="free-delivery-status">
                                                                    <input type="checkbox"
                                                                        class="switcher_input toggle-switch-message"
                                                                        name="free_delivery_status"
                                                                        id="free-delivery-status"
                                                                        {{ $vendor['free_delivery_status'] == 1 ? 'checked' : '' }}
                                                                        data-modal-id="toggle-modal"
                                                                        data-toggle-id="free-delivery-status"
                                                                        data-on-image="free-delivery-on.png"
                                                                        data-off-image="free-delivery-on.png"
                                                                        data-on-title="{{ translate('want_to_Turn_ON_Free_Delivery') }}"
                                                                        data-off-title="{{ translate('want_to_Turn_OFF_Free_Delivery') }}"
                                                                        data-on-message="<p>{{ translate('if_enabled_the_free_delivery_feature_will_be_shown_from_the_system') }}</p>"
                                                                        data-off-message="<p>{{ translate('if_disabled_the_free_delivery_feature_will_be_hidden_from_the_system') }}</p>">
                                                                    <span class="switcher_control"></span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 col-md-6">
                                                            <div class="form-group">
                                                                <label class="title-color d-flex align-items-center gap-2"
                                                                    for="free-delivery-over-amount">
                                                                    {{ translate('free_Delivery_Over') }}
                                                                    ({{ getCurrencySymbol() }})
                                                                    <span class="input-label-secondary cursor-pointer"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="{{ translate('customers_will_get_free_delivery_if_the_order_amount_exceeds_the_given_amount_and_the_given_amount_will_be_added_as_vendor_expenses') }}">
                                                                        <img width="16"
                                                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                            alt="">
                                                                    </span>
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                    name="free_delivery_over_amount"
                                                                    id="free-delivery-over-amount" min="0"
                                                                    placeholder="{{ translate('ex') . ':' . translate('10') }}"
                                                                    value="{{ usdToDefaultCurrency($vendor['free_delivery_over_amount']) ?? 0 }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" id="submit"
                                                            class="btn btn--primary px-4">{{ translate('submit') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>


                </div>


            </div>
        </div>


    </div>
@endsection
@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>

    <script>
        // Function to validate PAN number
        function validatePAN() {
            const panInput = $('#pan_number').val().toUpperCase();
            const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

            if (panRegex.test(panInput)) {
                $('#panError').text('');
                return true;
            } else {
                if (panInput.trim() !== '') {
                    $('#panError').text('Invalid PAN format');
                }
                return false;
            }
        }

        // Function to validate Aadhar number
        function validateAadhar() {
            const aadharInput = $('#aadhar_number').val();
            const aadharRegex = /^[2-9][0-9]{11}$/;

            if (aadharRegex.test(aadharInput)) {
                $('#aadharError').text('');
                return true;
            } else {
                if (aadharInput.trim() !== '') {
                    $('#aadharError').text('Invalid Aadhar format');
                }
                return false;
            }
        }

        // Validate both PAN and Aadhar on keyup
        $('#pan_number, #aadhar_number').on('keyup', function() {
            const isPanValid = validatePAN();
            const isAadharValid = validateAadhar();

            // Enable submit button only if both PAN and Aadhar are valid
            if (isPanValid && isAadharValid) {
                $('#submit-button').prop('disabled', false); // Enable button
            } else {
                $('#submit-button').prop('disabled', true); // Disable button
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll(".form-system-language-tab");
            const forms = document.querySelectorAll(".form-system-language-form");

            tabs.forEach((tab, index) => {
                tab.addEventListener("click", () => {
                    // Remove active class and hide all forms
                    tabs.forEach(t => t.classList.remove("active"));
                    forms.forEach(f => f.classList.add("d-none"));

                    // Add active class to clicked tab and show corresponding form
                    tab.classList.add("active");
                    forms[index].classList.remove("d-none");
                });
            });
        });

        $(document).ready(function() {

            var current_fs, next_fs, previous_fs; //fieldsets
            var opacity;
            const accountNo = document.querySelector('input[name="account_no"]');
            const confirmAccountNo = document.querySelector('.confirm_account_no');
            confirmAccountNo.addEventListener('input', function() {
                if (accountNo.value !== confirmAccountNo.value) {
                    $('.confirm_account_no-error').text("Account numbers do not match*.");
                    return false;
                } else {
                    $('.confirm_account_no-error').text("");
                }
            });
            $(".next.stepn").click(function() {
                current_fs = $(this).parent();
                next_fs = $(this).parent().next();
                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
                next_fs.show();
                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now) {
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        next_fs.css({
                            'opacity': opacity
                        });
                    },
                    duration: 600
                });
            });
            $(".next.last").click(function(event) {
                current_fs = $(this).parent();
                next_fs = $(this).parent().next();

                if (accountNo.value !== confirmAccountNo.value) {
                    $('.confirm_account_no-error').text("Account numbers do not match*.");
                    toastr.error('Account numbers do not match*', '', {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 5000,
                        positionClass: 'toast-top-right'
                    });
                } else {
                    $('.confirm_account_no-error').text("");
                    var userConfirmed = confirm("Are you sure you want to Update Profile");
                    if (userConfirmed) {
                        form_submit();
                        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
                        next_fs.show();
                        current_fs.animate({
                            opacity: 0
                        }, {
                            step: function(now) {
                                opacity = 1 - now;
                                current_fs.css({
                                    'display': 'none',
                                    'position': 'relative'
                                });
                                next_fs.css({
                                    'opacity': opacity
                                });
                            },
                            duration: 600
                        });

                    }
                }


            });

            $(".previous").click(function() {

                current_fs = $(this).parent();
                previous_fs = $(this).parent().prev();
                $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

                previous_fs.show();

                current_fs.animate({
                    opacity: 0
                }, {
                    step: function(now) {
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        previous_fs.css({
                            'opacity': opacity
                        });
                    },
                    duration: 600
                });
            });

            $('.radio-group .radio').click(function() {
                $(this).parent().find('.radio').removeClass('selected');
                $(this).addClass('selected');
            });

            $(".submit").click(function() {
                alert('asdsa');
                return false;
            })

        });
    </script>
    <script>
        // function confirmAndSubmit(that) {
        //     var userConfirmed = confirm("Are you sure you want to Update Profile");
        //     if (userConfirmed) {
        //         form_submit();
        //     } else {
        //         current_fs = $(that).closest("fieldset");
        //         previous_fs = current_fs.prev();
        //         $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
        //         previous_fs.show();
        //         current_fs.animate({
        //             opacity: 0
        //         }, {
        //             step: function(now) {
        //                 opacity = 1 - now;

        //                 current_fs.css({
        //                     'display': 'none',
        //                     'position': 'relative'
        //                 });
        //                 previous_fs.css({
        //                     'opacity': opacity
        //                 });
        //             },
        //             duration: 600
        //         });
        //         setTimeout(function() {
        //             $('.last_message_success').css({
        //                 'display': 'none',
        //                 "opacity": 0,
        //                 'position': 'relative'
        //             });
        //             $('.finish_points').removeClass('active');
        //         }, 1000);
        //     }
        // }

        function form_submit() {
            var formData = new FormData($('form.form_seller_info')[0]);

            $.ajax({
                url: "{{ route('vendor.profile.update2') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log('Form submitted successfully');
                    console.log(res);
                },
                error: function(error) {
                    console.error('Error in form submission:', error);
                }
            });
        }
    </script>


    <script>
        function handleTabSwitch(target) {
            const sections = ['general1', 'shops1', 'document', 'banks'];

            // Hide all sections
            sections.forEach(section => {
                document.getElementById(section + '-div').style.display = 'none';
                document.querySelector('[data-target="' + section + '"]').classList.remove('active');
            });

            // Show the selected section
            document.getElementById(target + '-div').style.display = 'block';
            document.querySelector('[data-target="' + target + '"]').classList.add('active');
        }

        // Add event listeners to the tabs
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                handleTabSwitch(target);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const inputElement = $('textarea[name="address"].form-control.getAddress_google')[0];
            const autocomplete = new google.maps.places.Autocomplete(inputElement, {
                types: ['establishment']
            });

            $(".getAddress_google").on('input', function() {
                if ($(this).val().length < 2) {
                    $('input[type="hidden"][name="latitude"]').val('');
                    $('input[type="hidden"][name="longitude"]').val('');


                    $('.state_select_geolocation').val('');
                    $('.City_select_geolocation').val('');
                    $('.Country_select_geolocation').val('');
                    $('input[type="hidden"][name="zipcode"].form-control.zipcode').val('');
                }

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        $(this).val('');
                        return;
                    }

                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();

                    $('input[type="hidden"][name="latitude"]').val(lat);
                    $('input[type="hidden"][name="longitude"]').val(lng);

                    // Initialize variables for the address components
                    let zipcode = '',
                        city = '',
                        state = '',
                        country = '';

                    // Extract address components
                    for (const component of place.address_components) {
                        const componentType = component.types[0];
                        switch (componentType) {
                            case 'postal_code':
                                zipcode = component.long_name;
                                break;
                            case 'locality':
                                city = component.long_name;
                                break;
                            case 'administrative_area_level_1':
                                state = component.short_name;
                                break;
                            case 'country':
                                country = component.short_name;
                                break;
                        }
                    }
                    $('input[type="number"][name="pincode"].form-control').val(zipcode);
                    $('.Country_select_geolocation').val(country);
                    $('.City_select_geolocation').val(city);
                    $('.state_select_geolocation').val(state);
                });
            });
        });
    </script>

    <script>
        function enable_2fa(e) {
            var enable;
            if ($(e).is(':checked')) {
                var enable = 1;
            } else {
                var enable = 0;
            }
            var id = $('#id').val();
            var email = $('#email').val();

            let data = {
                _token: '{{ csrf_token() }}',
                id: id,
                email: email,
                enable: enable
            };
            $.ajax({
                type: "post",
                url: "{{ route('vendor.auth.2fa.show.qr') }}",
                data: data,
                success: function(response) {
                    console.log(response);
                    if (response.status == 200 && response.qr_code != null) {
                        // var base64Svg = window.btoa(response.qr_code);
                        // var imgSrc = 'data:image/svg+xml;base64,' + base64Svg;
                        // $('#qr_code').attr('src', imgSrc);

                        $('#svg_code').html('<img src="' + response.qr_code + '" alt="QR Code">');
                        $('#qr_code_modal').modal('show');

                        // const svgBlob = new Blob([response.qr_code], { 
                        //     type: 'image/svg+xml'
                        // });

                        // // Convert Blob to base64
                        // const reader = new FileReader();
                        // reader.onloadend = function() {
                        //     const base64data = reader.result;
                        //     $('#qr_code').prop('src', base64data);
                        //     $('#qr_code_modal').modal('show');
                        // };
                        // reader.readAsDataURL(svgBlob);
                    } else if (response.status == 200 && response.qr_code == null) {
                        // alert('2FA successfully disabled');
                        toastr.success('2FA successfully disabled', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else {
                        // alert('something went wrong');
                        toastr.error('something went wrong', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                }
            });

        }

        // active otp
        $('#OtpAPI').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('vendor.auth.2fa.active') }}",
                method: 'post',
                data: formData,
                success: function(response) {
                    if (response.status == 200) {
                        // alert('2FA successfully Updated');
                        toastr.success('2FA successfully Updated', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        $('#otp').val('');
                        $('#qr_code_modal').modal('hide');
                    } else {
                        toastr.error(response.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        // alert(response.message);
                    }
                }
            });
        });
    </script>
@endpush
