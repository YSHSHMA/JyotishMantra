@extends('layouts.back-end.app-event')

@section('title', translate('profile_Settings'))
@push('css_or_js')
<link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
<script src="https://maps.googleapis.com/maps/api/js?key={{config('services.google_maps.api_key')}}&libraries=places"></script>
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
@php 
use App\Utils\Helpers;
@endphp
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
                <a class="btn btn--primary" href="{{ route('event-vendor.dashboard.index') }}">
                    <i class="tio-home mr-1"></i> {{ translate('dashboard') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="px-4 pt-3">
                    @if(auth('event')->check())
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        <li class="nav-item" onclick="handleTabSwitch('general1')">
                            <span class="nav-link text-capitalize form-system-language-tab active cursor-pointer" id="general-section-link"><i class="tio-user-outlined nav-icon"></i> {{ translate('basic_Information') }}</span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link text-capitalize form-system-language-tab cursor-pointer" id="password-section-link"><i class="tio-lock-outlined nav-icon"></i> {{ translate('password') }}</span>
                        </li>
                    </ul>
                    @endif
                </div>

                <div class="card-body">
                     @if(auth('event')->check())
                    <div class="form-system-language-form" id="general-section-form">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                $getUniqueArray = [
                                    'full_name' => 0,
                                    'contact_number' => 0,
                                    'email_address' => 0,
                                    'organizer_name' => 0,
                                    'itr_return' => 0,
                                    'itr_return_image' => 0,
                                    "organizer_address" => 0,
                                    'user_image' => 0,
                                    "aadhar_number" => 0,
                                    'aadhar_image' => 0,
                                    'organizer_pan_no' => 0,
                                    'pan_card_image' => 0,
                                    'gst_no' => 0,
                                    'bank_name' => 0,
                                    'branch_name' => 0,
                                    'beneficiary_name' => 0,
                                    'ifsc_code' => 0,
                                    'account_no' => 0,
                                    'account_type' => 0,
                                    'cancelled_cheque_image' => 0,
                                ];

                                if (auth('event')->user()->update_seller_status == 2) {
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


                                @if(auth('event')->user()->update_seller_status == 1 || auth('event')->user()->update_seller_status == 2)
                                <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                                    <h2 class="text-center"><strong>Organizer Information</strong></h2>
                                    <div class="row">
                                        <div class="col-md-12 mx-0">
                                            <form id="msform" class="form_seller_info" enctype="multipart/form-data">
                                                <!-- progressbar -->
                                                <ul id="progressbar">
                                                    <li class="active" id="personal"><strong>Personal</strong></li>
                                                    <li id="document"><strong>Doc</strong></li>
                                                    <li id="bank_information"><strong>Bank Information</strong></li>
                                                    <li id="confirm" class="finish_points"><strong>Finish</strong></li>
                                                </ul>
                                                <!-- fieldsets -->
                                                <fieldset>
                                                    <div class="form-card">
                                                        <h2 class="fs-title">Personal Information</h2>
                                                        <div class="row">
                                                            <label for="firstNameLabel" class="col-sm-3 col-form-label input-label">{{ translate('full_Name') }}
                                                                <i class="tio-help-outlined text-body ml-1" data-toggle="tooltip" data-placement="right" title="{{ ucwords($getData['full_name']??'') }}"></i></label>
                                                            <div class="col-sm-9">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="hidden" name="id" value="{{ ($getData['id']??'') }}">
                                                                        <input type="text" name="f_name" value="{{ ($getData['full_name']??'') }}" class="form-control {{ (( $getUniqueArray['full_name'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['full_name'] == 2)?'is-invalid':'' ) }} " required {{ (( $getUniqueArray['full_name'] == 1)?'readOnly':"" ) }} placeholder="{{ translate('Enter_Full_name') }}" onkeyup="return this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                                                        <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['full_name'] == 2)?'Please Enter Correct FUll Name':'' ) }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label for="phoneLabel" class="col-sm-3 col-form-label input-label">{{ translate('phone') }} </label>
                                                            <div class="col-sm-9 mb-3">
                                                                <input class="form-control form-control-user phone-input-with-country-picker {{ (( $getUniqueArray['contact_number'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['contact_number'] == 2)?'is-invalid':'' ) }}" type="tel" onkeyup="return this.value = this.value.replace(/[^0-9]/g, '')" id="exampleInputPhone" value="{{ old('contact_number',($getData['contact_number']??'')) }}" placeholder="{{ translate('Enter_phone_number') }}" required {{ (( $getUniqueArray['contact_number'] == 1)?'readOnly':"" ) }} maxlength="10" inputmode="numeric">
                                                                <div class="">
                                                                    <input type="text" class="country-picker-phone-number w-50" value="{{ ($getData['contact_number']??'') }}" name="contact_number" hidden readonly>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['contact_number'] == 2)?'Please Enter Correct Phone Number':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('email') }}</label>
                                                            <div class="col-sm-9">
                                                                <input type="email" class="form-control {{ (( $getUniqueArray['email_address'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['email_address'] == 2)?'is-invalid':'' ) }}" name="email_address" id="newEmailLabel" value="{{ ($getData['email_address']??'') }}" placeholder="{{ translate('Enter_Email_Id') }}" {{ (( $getUniqueArray['email_address'] == 1)?'readOnly':"" ) }} required>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['email_address'] == 2)?'Please Enter Correct Email Id':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('Organization / Individual Name') }}</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['organizer_name'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['organizer_name'] == 2)?'is-invalid':'' ) }}" name="organizer_name" id="newEmailLabel" value="{{ ($getData['organizer_name']??'') }}" placeholder="{{ translate('enter_Organization_Name') }}" {{ (( $getUniqueArray['organizer_name'] == 1)?'readOnly':"" ) }} required onkeyup="return this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['organizer_name'] == 2)?'Please Enter Correct Organization Name':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('Have you filed last 2 years ITR Return') }}</label>
                                                            <div class="col-sm-3">
                                                                <div class="d-flex">
                                                                    <input type="radio" class="form-radio" name="itr_return" style="width: 13px;" value="1">&nbsp;Yes &nbsp;&nbsp;&nbsp;<input type="radio" class="" name="itr_return" value="0" checked="" style="width: 13px;">&nbsp;No
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['itr_return'] == 2)?'Please Enter Correct Only One Option':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <div class="custom-file text-left">
                                                                    <input type="file" name="itr_return_image" id="itr_return-file-upload" class="custom-file-input image-input {{ (( $getUniqueArray['itr_return_image'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['itr_return_image'] == 2)?'is-invalid':'' ) }}" data-image-id="itr_return_image_viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" {{ (( $getUniqueArray['itr_return_image'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['itr_return_image'] == 2 ? 'onchange=$(\'.itr_return_image-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['itr_return_image'] == 2)?'required':'' ) }}>
                                                                    <label class="custom-file-label text-capitalize" for="itr_return-file-upload">{{translate('choose_file')}}</label>
                                                                </div>
                                                                <div class="text-center">
                                                                    @if($getUniqueArray['itr_return_image'] == 2)
                                                                    <img class="upload-img-view itr_return_image-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                    @endif
                                                                    <img class="upload-img-view" id="itr_return_image_viewer" src="{{getValidImage(path: 'storage/app/public/event/organizer/'.($getData['itr_return_image']??''),type: 'backend-basic')}}" alt="{{translate('itr_return_image')}}" />
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['itr_return_image'] == 2)?'Please Choose Correct Profile Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('Organization / Individual Address') }}</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['organizer_address'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['organizer_address'] == 2)?'is-invalid':'' ) }}" name="organizer_address" id="newEmailLabel" value="{!! strip_tags($getData['organizer_address']??'') !!}" placeholder="{{ translate('enter_Organization_Address') }}" placeholder="Please Choose Correct Profile Image" {{ (( $getUniqueArray['organizer_address'] == 1)?'readOnly':"" ) }} required>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['organizer_address'] == 2)?'Please Enter Correct Organization Address':'' ) }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="name" class="title-color text-capitalize">{{translate('upload_image')}}</label>
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <div class="custom-file text-left">
                                                                    <input type="file" name="image" id="custom-file-upload" class="custom-file-input image-input {{ (( $getUniqueArray['user_image'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['user_image'] == 2)?'is-invalid':'' ) }}" data-image-id="user_image_viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" {{ (( $getUniqueArray['user_image'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['user_image'] == 2 ? 'onchange=$(\'.user_image-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['user_image'] == 2)?'required':'' ) }}>
                                                                    <label class="custom-file-label text-capitalize" for="custom-file-upload">{{translate('choose_file')}}</label>
                                                                </div>
                                                                <div class="text-center">
                                                                    @if($getUniqueArray['user_image'] == 2)
                                                                    <img class="upload-img-view user_image-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                    @endif
                                                                    <img class="upload-img-view" id="user_image_viewer" src="{{getValidImage(path: 'storage/app/public/event/organizer/'.($getData['image']??''),type: 'backend-basic')}}" alt="{{translate('user_image')}}" />
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['user_image'] == 2)?'Please Choose Correct Profile Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="button" class="next stepn action-button btn-info" value="Next Step" />
                                                </fieldset>

                                                <fieldset>
                                                    <div class="form-card">
                                                        <h2 class="fs-title">Doc Information</h2>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('aadhar_Number') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['aadhar_number'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['aadhar_number'] == 2)?'is-invalid':'' ) }}" maxlength="12" id="aadhar_number" name="aadhar_number" value="{{ $getData['aadhar_number']??'' }}" placeholder="Enter your Aadhar number" {{ (( $getUniqueArray['aadhar_number'] == 1)?'readOnly':"" ) }} required onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                                                <small id="aadharError" class="text-danger font-weight-bolder">{{ (( $getUniqueArray['aadhar_number'] == 2)?'Please Enter Correct Aadhar Number':'' ) }}</small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="">Aadhar Front Image</label>
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="aadhar_front_image" class="custom-upload-input-file action-upload-color-image {{ (( $getUniqueArray['aadhar_image'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['aadhar_image'] == 2)?'is-invalid':'' ) }}" id="" data-imgpreview="pre_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" {{ (( $getUniqueArray['aadhar_image'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['aadhar_image'] == 2 ? 'onchange=$(\'.aadhar_front_image-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['aadhar_image'] == 2)?'required':'' ) }} placeholder="'Please Choose Correct Aadhar Image'">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['aadhar_image'] == 2)
                                                                        <img class="h-auto aspect-1 aadhar_front_image-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        <img id="pre_img_viewer" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.($getData['aadhar_image']??''), type: 'backend-basic') }}" alt="">
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['aadhar_image'] == 2)?'Please Choose Correct Aadhar Image':'' ) }}</span>
                                                            </div>

                                                        </div>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('Organization / Individual PAN Card Number') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" maxlength="10" class="form-control {{ (( $getUniqueArray['organizer_pan_no'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['organizer_pan_no'] == 2)?'is-invalid':'' ) }}" id="pan_number" name="pan_number" value="{{ ($getData['organizer_pan_no']??'') }}" placeholder="{{ translate('enter_your_PAN_number') }}" {{ (( $getUniqueArray['organizer_pan_no'] == 1)?'readOnly':"" ) }} onkeyup="formatPAN(this)">
                                                                <small id="panError" class="text-danger font-weight-bolder">{{ (( $getUniqueArray['organizer_pan_no'] == 2)?'Please Enter Correct Pan Number':'' ) }}</small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="">Pancard Image</label>
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="pan_card_image" class="custom-upload-input-file action-upload-color-image{{ (( $getUniqueArray['pan_card_image'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['pan_card_image'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_viewer3" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" {{ (( $getUniqueArray['pan_card_image'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['pan_card_image'] == 2 ? 'onchange=$(\'.pancard_image-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['pan_card_image'] == 2)?'required':'' ) }} placeholder="Please Choose Correct PanCard Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['pan_card_image'] == 2)
                                                                        <img class="h-auto aspect-1 pancard_image-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        <img id="pre_img_viewer3" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.($getData['pan_card_image']??''), type: 'backend-basic') }}" alt="">
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['pan_card_image'] == 2)?'Please Choose Correct PanCard Image':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-3"></div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label class="col-sm-3 col-form-label input-label">{{ translate('GST_Number') }}</label>

                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['gst_no'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['gst_no'] == 2)?'is-invalid':'' ) }}" name="gst" {{ (( $getUniqueArray['gst_no'] == 1)?'readOnly':"" ) }} value="{{ ($getData['gst_no']??'') }}" placeholder="{{ translate('enter_your_gst_number') }}">
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['gst_no'] == 2)?'Please Enter Correct Gst Number':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="button" class="previous action-button-previous" value="Previous" />
                                                    <input type="button" class="next stepn action-button btn-info" value="Next Step" />
                                                </fieldset>
                                                <fieldset>
                                                    <div class="form-card">
                                                        <h2 class="fs-title">bank Information</h2>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="name" class="title-color">{{translate('bank_Name')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                {{-- $getData['bank_name'] --}}
                                                                <select name="bank_name" class="form-control {{ (( $getUniqueArray['bank_name'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['bank_name'] == 2)?'is-invalid':'' ) }}" required {{ (( $getUniqueArray['bank_name'] == 1)?'readOnly':"" ) }} placeholder="Enter Bank Name">
                                                                    @if(\App\Models\Bank::where('status',1)->exists())
                                                                    <?php $back_list = \App\Models\Bank::where('status', 1)->get(); ?>
                                                                    @foreach($back_list as $bank)
                                                                    <option value="{{$bank['bank_name']}}" {{ (($getData['bank_name'] == $bank['bank_name'])?'selected':'' ) }}>{{$bank['bank_name']}}</option>
                                                                    @endforeach
                                                                    @endif
                                                                </select>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['bank_name'] == 2)?'Please Enter Correct Bank Name':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="name" class="title-color">{{translate('branch_Name')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <input type="text" name="branch_name" value="{{$getData['branch_name']}}" class="form-control {{ (( $getUniqueArray['branch_name'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['branch_name'] == 2)?'is-invalid':'' ) }}" placeholder="Enter Bank Branch Name" required {{ (( $getUniqueArray['branch_name'] == 1)?'readOnly':"" ) }}>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['branch_name'] == 2)?'Please Enter Correct Branch Name':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="account_no" class="title-color">{{translate('holder_Name')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <input type="text" name="holder_name" value="{{$getData['beneficiary_name']}}" class="form-control {{ (( $getUniqueArray['beneficiary_name'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['beneficiary_name'] == 2)?'is-invalid':'' ) }}" placeholder="Enter Holder Name" required {{ (( $getUniqueArray['beneficiary_name'] == 1)?'readOnly':"" ) }} onkeyup="return this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['beneficiary_name'] == 2)?'Please Enter Correct Holder Name':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="account_no" class="title-color">{{translate('account_No')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="input-group input-group-merge">
                                                                    <input type="password" class="js-toggle-password form-control form-control-lg {{ (( $getUniqueArray['account_no'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['account_no'] == 2)?'is-invalid':'' ) }}"
                                                                        {{ (( $getUniqueArray['account_no'] == 1)?'readOnly':"" ) }} onclick="$('.confirm_account_no').val('')" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')"
                                                                        name="account_no"
                                                                        value="{{$getData['account_no']}}"
                                                                        placeholder="Enter Bank Account Number"
                                                                        aria-label="Enter Bank Account Number" required
                                                                        data-msg="Enter Bank Account Number. Please try again."
                                                                        data-hs-toggle-password-options='{
                                                                                    "target": "#account_show_hideId",
                                                                            "defaultClass": "tio-hidden-outlined",
                                                                            "showClass": "tio-visible-outlined",
                                                                            "classChangeTarget": "#AccountchangePassIcon"
                                                                            }'>
                                                                    <div id="account_show_hideId" class="input-group-append">
                                                                        <a class="input-group-text" href="javascript:">
                                                                            <i id="AccountchangePassIcon" class="tio-visible-outlined"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['account_no'] == 2)?'Please Enter Correct Account Number':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="con_account_no" class="title-color">{{translate('confirm_account_No')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="input-group input-group-merge">
                                                                    <input type="password" class="js-toggle-password form-control form-control-lg confirm_account_no"
                                                                        {{ (( $getUniqueArray['account_no'] == 1)?'readOnly':"" ) }} onkeyup="this.value = this.value.replace(/[^0-9]/g, '')"
                                                                        value="{{ (($getUniqueArray['account_no'] == 2)? '' : $getData['account_no']) }}"
                                                                        placeholder="Enter Bank Confirm Account Number"
                                                                        aria-label="Enter Bank Confirm Account Number" required
                                                                        data-msg="Enter Bank Confirm Account Number. Please try again."
                                                                        data-hs-toggle-password-options='{
                                                                                    "target": "#account_confirm_show_hideId",
                                                                            "defaultClass": "tio-hidden-outlined",
                                                                            "showClass": "tio-visible-outlined",
                                                                            "classChangeTarget": "#AccountConfirmchangePassIcon"
                                                                            }'>
                                                                    <div id="account_confirm_show_hideId" class="input-group-append">
                                                                        <a class="input-group-text" href="javascript:">
                                                                            <i id="AccountConfirmchangePassIcon" class="tio-visible-outlined"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <span class="font-weight-bold confirm_account_no-error text-danger"></span>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="ifsc" class="title-color">{{translate('IFSC_code')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <input type="text" name="ifsc" value="{{$getData['ifsc_code']}}" class="form-control {{ (( $getUniqueArray['ifsc_code'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['ifsc_code'] == 2)?'is-invalid':'' ) }}" placeholder="Enter Bank IFSC Code" required {{ (( $getUniqueArray['ifsc_code'] == 1)?'readOnly':"" ) }}>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['ifsc_code'] == 2)?'Please Enter Correct Bank Ifsc code':'' ) }}</span>
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="gumasta-upload" class="title-color text-capitalize">{{translate('upload_cancel_check')}}</label>
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <div class="flex-start">
                                                                    <div class="mx-1">
                                                                        <span class="text-info">{{ THEME_RATIO[theme_root_path()]['Store cover Image'] }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="custom-file text-left">
                                                                    <input type="file" name="cancelled_cheque_image" id="cancel_check-upload" class="custom-file-input image-input {{ (( $getUniqueArray['cancelled_cheque_image'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['cancelled_cheque_image'] == 2)?'is-invalid':'' ) }}" data-image-id="cancel_check-preview" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" {{ (( $getUniqueArray['cancelled_cheque_image'] == 1)?'readOnly':"" ) }} {{ $getUniqueArray['cancelled_cheque_image'] == 2 ? 'onchange=$(\'.cancel_check-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['cancelled_cheque_image'] == 2)?'required':'' ) }} placeholder="Please Choose Correct Bank Cancel Check">
                                                                    <label class="custom-file-label text-capitalize" for="cancel_check-upload">{{translate('choose_file')}}</label>
                                                                </div>
                                                                <div class="text-center">
                                                                    @if($getUniqueArray['cancelled_cheque_image'] == 2)
                                                                    <img class="upload-img-view upload-img-view__banner cancel_check-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                    @endif
                                                                    <img class="upload-img-view upload-img-view__banner upload-img-view" id="cancel_check-preview" src="{{getValidImage(path: 'storage/app/public/event/organizer/'.($getData['cancelled_cheque_image']??''), type: 'backend-basic')}}" alt="{{translate('cancel_check_image')}}" />
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['cancelled_cheque_image'] == 2)?'Please Choose Correct Bank Cancel Check':'' ) }}</span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <input type="button" class="previous action-button-previous" value="Previous" />
                                                    <input type="button" class="next last action-button btn-info" value="Confirm" />
                                                </fieldset>
                                                <fieldset class="last_message_success">
                                                    <div class="form-card">
                                                        <h2 class="fs-title text-center">Success !</h2>
                                                        <br><br>
                                                        <div class="row justify-content-center">
                                                            <div class="col-3">
                                                                <img src="https://img.icons8.com/color/96/000000/ok--v2.png" class="fit-image">
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
                                                    <button type="button" class="navbar-toggler btn btn-block btn-white mb-3"
                                                        aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu"
                                                        data-bs-toggle="collapse" data-bs-target="#navbarVerticalNavMenu">
                                                        <span class="d-flex justify-content-between align-items-center">
                                                            <span class="h5 mb-0">{{ translate('nav_menu') }}</span>
                                                            <span class="navbar-toggle-default">
                                                                <i class="tio-menu-hamburger"></i>
                                                            </span>
                                                            <span class="navbar-toggle-toggled">
                                                                <i class="tio-clear"></i>
                                                            </span>
                                                        </span>
                                                    </button>

                                                    <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                                                        <ul id="navbarSettings" class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" href="javascript:void(0);" data-target="general1">
                                                                    <i class="tio-user-outlined nav-icon"></i>{{ translate('basic_Information') }}
                                                                </a>
                                                            </li>

                                                            <li class="nav-item">
                                                                <a class="nav-link" href="javascript:void(0);" data-target="document">
                                                                    <i class="tio-documents nav-icon"></i> {{ translate('Document') }}
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" href="javascript:void(0);" data-target="banks">
                                                                    <i class="tio-museum nav-icon"></i> {{ translate('Bank') }}
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
                                                            <lable class="col-form-label font-weight-bold">{{ translate('first_Name') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['full_name']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('phone') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['contact_number']??'') }}</div>

                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('email') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['email_address']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('Organization / Individual Name') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['organizer_name']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('Organization / Individual Address') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['organizer_address']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('Last 2 years ITR Return') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['itr_return']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    <img id="fassai-preview" src="{{getValidImage(path: 'storage/app/public/event/organizer/'.($getData['itr_return_image']??''), type: 'backend-basic')}}" alt="{{translate('itr_return_image')}}" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('image')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <img id="fassai-preview" src="{{getValidImage(path: 'storage/app/public/event/organizer/'.($getData['image']??''), type: 'backend-basic')}}" alt="{{translate('fassai_image')}}" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card mb-3 mb-lg-5" id="document-div" style="display: none;">
                                                    <div class="row p-4 ">

                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('aadhar_image') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['aadhar_number']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.($getData['aadhar_image']??''),type: 'backend-basic') }}" alt="">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('pan_number') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['organizer_pan_no']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('pancard_image') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.($getData['pan_card_image']??''),type: 'backend-basic') }}" alt="">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('GST_Number') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['gst_no']??'') }}</div>
                                                    </div>
                                                </div>
                                                <div class="card mb-3 mb-lg-5" id="banks-div" style="display: none;">
                                                    <div class="row p-4 ">
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('bank_Name')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{$getData['bank_name']??""}}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('branch_Name')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{$getData['branch_name']}}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('holder_Name')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{$getData['beneficiary_name']}}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('account_No')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{$getData['account_no']}}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('IFSC_code')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{$getData['ifsc_code']}}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('cancel_check') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-md-12 form-group">
                                                                    <div class="text-center">
                                                                        <img class="upload-img-view upload-img-view__banner bg-white" src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.($getData['cancelled_cheque_image']??''),type: 'backend-basic') }}" alt="">
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
                    @endif
                    <div class="form-system-language-form {{ ((auth('event_employee')->check())?'':'d-none') }}" id="password-section-form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-3 mb-lg-5">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ translate('change_your_password') }}</h5>
                                    </div>
                                    <?php
                                    if (auth('event')->check()) {
                                        $relationEmployees = auth('event')->user()->relation_id;
                                    } elseif (auth('event_employee')->check()) {
                                        $relationEmployees = auth('event_employee')->user()->relation_id;
                                    }
                                    ?>
                                    <div class="card-body">
                                        <form id="update-password-form" action="{{ route('event-vendor.profile.profile-edit', [$relationEmployees]) }}" method="POST" enctype="multipart/form-data">
                                            @method('PATCH')
                                            @csrf
                                            <div class="row form-group">
                                                <label for="newPassword"
                                                    class="col-sm-3 col-form-label input-label d-flex align-items-center">
                                                    {{ translate('new_Password') }}
                                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                        data-placement="right" title=""
                                                        data-original-title="{{ translate('The_password_must_be_at_least_8_characters_long_and_contain_at_least_one_uppercase_letter') . ',' . translate('_one_lowercase_letter') . ',' . translate('_one_digit_') . ',' . translate('_one_special_character') . ',' . translate('_and_no_spaces') . '.' }}">
                                                        <img alt="" width="16"
                                                            src={{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}
                                                            alt="" class="m-1">
                                                    </span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <div class="input-group input-group-merge">
                                                        <input type="password" class="js-toggle-password form-control password-check"
                                                            id="newPassword" autocomplete="off" name="password" required minlength="8"
                                                            placeholder="{{ translate('password_minimum_8_characters') }}"
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
                                                    <span class="text-danger mx-1 password-error"></span>
                                                </div>

                                            </div>
                                            <div class="row form-group">
                                                <label for="confirmNewPasswordLabel" class="col-sm-3 col-form-label input-label pt-0">
                                                    {{ translate('confirm_Password') }} </label>
                                                <div class="col-sm-9">
                                                    <div class="mb-3">
                                                        <div class="input-group input-group-merge">
                                                            <input type="password" class="js-toggle-password form-control"
                                                                name="confirm_password" required id="confirmNewPasswordLabel"
                                                                placeholder="{{ translate('confirm_password') }}" autocomplete="off"
                                                                data-hs-toggle-password-options='{
                                                             "target": "#changeConfirmPassTarget",
                                                            "defaultClass": "tio-hidden-outlined",
                                                            "showClass": "tio-visible-outlined",
                                                            "classChangeTarget": "#changeConfirmPassIcon"
                                                    }'>
                                                            <div id="changeConfirmPassTarget" class="input-group-append">
                                                                <a class="input-group-text" href="javascript:">
                                                                    <i id="changeConfirmPassIcon" class="tio-visible-outlined"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            @if(Helpers::Employee_modules_permission('Profile', 'Password', 'Update'))
                                            <div class="d-flex justify-content-end">
                                                <button type="button" data-form-id="update-password-form"
                                                    data-message="{{ translate('want_to_update_vendor_password') . '?' }}"
                                                    class="btn btn--primary {{ env('APP_MODE') != 'demo' ? 'form-submit' : 'call-demo' }}">{{ translate('save_changes') }}</button>
                                            </div>
                                            @endif
                                        </form>
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
        var current_fs, next_fs, previous_fs;
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
            if (!validateRequiredFields(current_fs)) {
                return false;
            }

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
            if (!validateRequiredFields(current_fs)) {
                return false;
            }
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
    function validateRequiredFields(container) {
        let isValid = true;
        container.find("input[required]:visible, select[required]:visible, textarea[required]:visible").each(function() {
            if ($(this).val().trim() === '') {
                toastr.error($(this).attr('placeholder'));
                $(this).addClass("is-invalid");
                isValid = false;
            } else {
                $(this).removeClass("is-invalid");
            }
        });
        return isValid;
    }
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
            url: "{{ route('event-vendor.profile.update2') }}",
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
        const sections = ['general1', 'document', 'banks'];

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
    function formatPAN(input) {
        let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); // Allow only A-Z, 0-9
        let formatted = '';

        for (let i = 0; i < value.length && i < 10; i++) {
            if (i < 5) {
                // First 5: Only letters
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i < 9) {
                // Next 4: Only numbers
                if (/[0-9]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i === 9) {
                // Last one: Only letter
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            }
        }

        input.value = formatted;
    }
</script>


@endpush