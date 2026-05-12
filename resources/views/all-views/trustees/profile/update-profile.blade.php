@extends('layouts.back-end.app-trustees')

@section('title', translate('profile_Settings'))
@push('css_or_js')
@php
use App\Utils\Helpers;
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
$logintype = 'trust';
$PurohitsId = 0;
$purohitsEmpId = 0;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
$logintype = 'employee';
$PurohitsId = auth('trust_employee')->user()->purohit_id;
$purohitsEmpId = auth('trust_employee')->user()->id;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
$logintype = 'purohit';
$PurohitsId = auth('purohit')->user()->id;
$purohitsEmpId = 0;
}
@endphp
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
<div class="content container-fluid">
    <div class="mb-3">
        <div class="row gy-2 align-items-center">
            <div class="col-sm">
                @if(auth('trust')->check())
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/support-ticket.png') }}" alt="">
                    {{ translate('settings') }}
                </h2>
                @endif
            </div>
            @if(auth('trust')->check())
            <div class="col-sm-auto">
                <a class="btn btn--primary" href="{{ route('trustees-vendor.dashboard.index') }}">
                    <i class="tio-home mr-1"></i> {{ translate('dashboard') }}
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="px-4 pt-3">
                    @if(auth('trust')->check())
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
                    @if(auth('trust')->check())
                    <div class="form-system-language-form" id="general-section-form">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                $getUniqueArray = [
                                    'name' => 0,
                                    'trust_name' => 0,
                                    "trust_category" => 0,
                                    'trust_email' => 0,
                                    'full_address' => 0,
                                    'description' => 0,
                                    "members" => 0,
                                    "website_link" => 0,
                                    'user_image' => 0,
                                    "gallery_image" => 0,
                                    'pan_card' => 0,
                                    'pan_card_image' => 0,
                                    'trust_pan_card' => 0,
                                    'trust_pan_card_image' => 0,
                                    'twelve_a_certificate' => 0,
                                    'eighty_g_certificate' => 0,
                                    'niti_aayog_certificate' => 0,
                                    'csr_certificate' => 0,
                                    'e_anudhan_certificate' => 0,
                                    'frc_certificate' => 0,
                                    'bank_name' => 0,
                                    'beneficiary_name' => 0,
                                    'ifsc_code' => 0,
                                    'account_type' => 0,
                                    'account_no' => 0,
                                    'cancelled_cheque_image' => 0,
                                    'twelve_a_number' => 0,
                                    'eighty_g_number' => 0,
                                    'niti_aayog_number' => 0,
                                    'csr_number' => 0,
                                    'e_anudhan_number' => 0,
                                    'frc_number' => 0,
                                    'gst_number' => 0,
                                ];

                                if (auth('trust')->user()->update_seller_status == 2) {
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


                                @if(auth('trust')->user()->update_seller_status == 1 || auth('trust')->user()->update_seller_status == 2)
                                <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                                    <h2 class="text-center"><strong>Trustees Information</strong></h2>
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
                                                                <i class="tio-help-outlined text-body ml-1" data-toggle="tooltip" data-placement="right" title="{{ ucwords($getData['name']??'') }}"></i></label>
                                                            <div class="col-sm-9">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <input type="hidden" name="id" value="{{ ($getData['id']??'') }}">
                                                                        <input type="text" name="name" value="{{ ($getData['name']??'') }}" class="form-control {{ (( $getUniqueArray['name'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['name'] == 2)?'is-invalid':'' ) }} " required {{ (( $getUniqueArray['name'] == 1)?'readOnly':"" ) }} placeholder="{{ translate('Enter_Full_name') }}" onkeyup="return this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                                                        <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['name'] == 2)?'Please Enter Correct Full Name':'' ) }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label for="phoneLabel" class="col-sm-3 col-form-label input-label">{{ translate('trust_name') }} </label>
                                                            <div class="col-sm-9 mb-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['trust_name'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['trust_name'] == 2)?'is-invalid':'' ) }}" onkeyup="return this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" value="{{ old('trust_name',($getData['trust_name']??'')) }}" name="trust_name" placeholder="{{ translate('Enter_trust_name') }}" required {{ (( $getUniqueArray['trust_name'] == 1)?'readOnly':"" ) }}>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['trust_name'] == 2)?'Please Enter Correct Trust Name':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <label for="phoneLabel" class="col-sm-3 col-form-label input-label">{{ translate('category') }} </label>
                                                            <div class="col-sm-9 mb-3">
                                                                <select class="form-control {{ (( $getUniqueArray['trust_category'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['trust_category'] == 2)?'is-invalid':'' ) }}" name="trust_category" placeholder="{{ translate('select_Trust_category') }}" required {{ (( $getUniqueArray['trust_category'] == 1)?'readOnly':"" ) }}>
                                                                    <option value="">{{ translate('select_Trust_category') }}</option>
                                                                    @if($categoryList)
                                                                    @foreach($categoryList as $cate)
                                                                    <option value="{{ $cate['id']}}" {{ (( old('trust_category',($getData['category_id']??'')) == $cate['id'])?'selected':'')  }}>{{ $cate['name']}}</option>
                                                                    @endforeach
                                                                    @endif
                                                                </select>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['trust_category'] == 2)?'Please Enter Correct Trust Category':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('Trust_email') }}</label>
                                                            <div class="col-sm-9">
                                                                <input type="email" class="form-control {{ (( $getUniqueArray['trust_email'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['trust_email'] == 2)?'is-invalid':'' ) }}" name="trust_email" value="{{ ($getData['trust_email']??'') }}" placeholder="{{ translate('Enter_Email_Id') }}" {{ (( $getUniqueArray['trust_email'] == 1 || $getUniqueArray['trust_email'] == 0)?'readOnly':"" ) }} required>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['trust_email'] == 2)?'Please Enter Correct Email Id':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('trust_full_address') }}</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['full_address'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['full_address'] == 2)?'is-invalid':'' ) }}" name="full_address" value="{{ ($getData['full_address']??'') }}" placeholder="{{ translate('enter_full_address') }}" {{ (( $getUniqueArray['full_address'] == 1)?'readOnly':"" ) }} required>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['full_address'] == 2)?'Please Enter Correct Trust full Address':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('description') }}</label>

                                                            <div class="col-sm-9">
                                                                <textarea name="description" class="form-control ckeditor {{ (( $getUniqueArray['description'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['description'] == 2)?'is-invalid':'' ) }}" {{ (( $getUniqueArray['description'] == 1)?'readOnly':"" ) }} required placeholder="{{ translate('Please_Enter_a_description') }}">{{ ($getData['description']??'') }}</textarea>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['description'] == 2)?'Please Enter Correct description':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3 form-group">
                                                            <div class="col-md-12">
                                                                <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Trust_members') }}</label>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="row mb-3">
                                                                    <div class="col-3"><b>Name</b></div>
                                                                    <div class="col-3"><b>Phone</b></div>
                                                                    <div class="col-3"><b>Position</b></div>
                                                                    <div class="col-3">
                                                                        <a id="addMember" class="btn btn-outline-primary btn-sm"><i class='tio-add'></i>Add</a>
                                                                    </div>
                                                                </div>

                                                                <div id="memberContainer">
                                                                    @php
                                                                    $members = old('member_name.*', $getData['memberlist'] ? json_decode($getData['memberlist'], true) : []);
                                                                    @endphp

                                                                    @if($members)
                                                                    @foreach($members as $index => $member)
                                                                    <div class="row mt-2">
                                                                        <div class="col-3">
                                                                            <input type="text" name="member_name[]" class="form-control" value="{{ old('member_name.'.$index, ($member['member_name']??'')) }}" required placeholder="{{ translate('please Enter a Member Name') }}">
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input type="text" name="member_phone_no[]" class="form-control" value="{{ old('member_phone_no.' . $index, ($member['member_phone_no']??'')) }}" required placeholder="{{ translate('please Enter a Member Phone Number') }}">
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input type="text" name="member_position[]" class="form-control" value="{{ old('member_position.' . $index, ($member['member_position']??'')) }}" required placeholder="{{ translate('please Enter a Member position') }}">
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <a class="btn btn-outline-danger btn-sm removeMember"><i class='tio-remove'></i></a>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
                                                                    @else
                                                                    <div class="row mt-2">
                                                                        <div class="col-3">
                                                                            <input type="text" name="member_name[]" class="form-control" required placeholder="{{ translate('please Enter a Member Name') }}">
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input type="text" name="member_phone_no[]" class="form-control" required placeholder="{{ translate('please Enter a Member Phone Number') }}">
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <input type="text" name="member_position[]" class="form-control" required placeholder="{{ translate('please Enter a Member position') }}">
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <a class="btn btn-outline-danger btn-sm removeMember"><i class='tio-remove'></i></a>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <span class="text-danger font-weight-bolder mt-2">{{ (( $getUniqueArray['members'] == 2)?'Please Enter Correct member Information':'' ) }}</span>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('website_link') }}</label>

                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['website_link'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['website_link'] == 2)?'is-invalid':'' ) }}" value="{{ $getData['website']??'' }}" name="website_link" {{ (( $getUniqueArray['website_link'] == 1)?'readOnly':"" ) }}>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['website_link'] == 2)?'Please Enter Correct website Link':'' ) }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="name" class="title-color text-capitalize">{{translate('upload_image')}}</label>
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <div class="custom-file text-left">
                                                                    <input type="file" name="image" id="custom-file-upload" class="custom-file-input image-input {{ (( $getUniqueArray['user_image'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['user_image'] == 2)?'is-invalid':'' ) }}" data-image-id="user_image_viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" {{ (( $getUniqueArray['user_image'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['user_image'] == 2 ? 'onchange=$(\'.user_image-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['user_image'] == 2)?'required':'' ) }} placeholder="{{ translate('please Choose a profile Image') }}">
                                                                    <label class="custom-file-label text-capitalize" for="custom-file-upload">{{translate('choose_file')}}</label>
                                                                </div>
                                                                <div class="text-center">
                                                                    @if($getUniqueArray['user_image'] == 2)
                                                                    <img class="upload-img-view user_image-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                    @endif
                                                                    <img class="upload-img-view" id="user_image_viewer" src="{{getValidImage(path: 'storage/app/public/donate/trust/'.($getData['theme_image']??''),type: 'backend-basic')}}" alt="{{translate('user_image')}}" />
                                                                </div>
                                                                <span class="text-danger font-weight-bolder mt-2">{{ (( $getUniqueArray['user_image'] == 2)?'Please Choose Correct Profile Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="additional_image_column col-md-12">
                                                                <div class="card h-100">
                                                                    <div class="card-body">
                                                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                                            <div>
                                                                                <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                                                                <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                                                    title="{{ translate('upload_any_additional_images_for_this_trust_from_here') }}.">
                                                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                                        alt="">
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                        <span class="text-danger font-weight-bolder mt-2">{{ (( $getUniqueArray['gallery_image'] == 2)?'Please Choose Correct Upload Additional Image':'' ) }}</span>
                                                                        <p class="text-muted">{{ translate('upload_additional_images') }}</p>
                                                                        <div class="coba-area">

                                                                            <div class="row g-2" id="additional_Image_Section">

                                                                                @if(!empty($getData['gallery_image']) && json_decode($getData['gallery_image'],true))
                                                                                @foreach (json_decode($getData['gallery_image'],true) as $key => $photo)
                                                                                <?php $unique_id = rand(1111, 9999) ?>

                                                                                <div class="col-sm-12 col-md-4">
                                                                                    <div
                                                                                        class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                                                            href="{{ route('trustees-vendor.profile.delete-image', ['id' => $getData['id'], 'photo' => $photo]) }}">
                                                                                            <i class="tio-delete"></i>
                                                                                        </a>
                                                                                        <div
                                                                                            class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                                            <img id="additional_Image_{{ $unique_id }}"
                                                                                                alt=""
                                                                                                class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                                                src="{{ getValidImage(path: 'storage/app/public/donate/trust/' . $photo, type: 'backend-product') }}">
                                                                                        </div>
                                                                                        <div
                                                                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                                            <div
                                                                                                class="d-flex flex-column justify-content-center align-items-center">
                                                                                                <img alt=""
                                                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                                                    class="w-75">
                                                                                                <h3 class="text-muted">{{ translate('Upload_Image') }}
                                                                                                </h3>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @endforeach
                                                                                @endif

                                                                                <div class="col-sm-12 col-md-4">
                                                                                    <div class="custom_upload_input position-relative border-dashed-2">
                                                                                        <input type="file" name="images[]"
                                                                                            class="custom-upload-input-file action-add-more-image" data-index="1"
                                                                                            data-imgpreview="additional_Image_1"
                                                                                            accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                                            data-target-section="#additional_Image_Section">

                                                                                        <span
                                                                                            class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                                                            <i class="tio-delete"></i>
                                                                                        </span>

                                                                                        <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                                            <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none"
                                                                                                alt="" src="">
                                                                                        </div>
                                                                                        <div
                                                                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                                            <div
                                                                                                class="d-flex flex-column justify-content-center align-items-center">
                                                                                                <img alt=""
                                                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                                                    class="w-75">
                                                                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
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

                                                    </div>
                                                    <input type="button" class="next stepn action-button btn-info" value="Next Step" />
                                                </fieldset>

                                                <fieldset>
                                                    <div class="form-card">
                                                        <h2 class="fs-title">Doc Information</h2>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('PAN Card Number') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" maxlength="10" class="form-control {{ (( $getUniqueArray['pan_card'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['pan_card'] == 2)?'is-invalid':'' ) }}" id="pan_number" name="pan_number" value="{{ ($getData['pan_card']??'') }}" placeholder="{{ translate('enter_your_PAN_number') }}" {{ (( $getUniqueArray['pan_card'] == 1)?'readOnly':"" ) }} onkeyup="formatPAN(this)">
                                                                <small id="panError" class="text-danger font-weight-bolder">{{ (( $getUniqueArray['pan_card'] == 2)?'Please Enter Correct Pan Number':'' ) }}</small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="">Pancard Image</label>
                                                                @php
                                                                $PanCardImg = $getData['pan_card_image']??'';
                                                                $PanCardImgextension = strtolower(pathinfo($PanCardImg, PATHINFO_EXTENSION));
                                                                @endphp
                                                                @if($PanCardImgextension === 'pdf')
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $PanCardImg, type: 'backend-basic') }}" target="_blank">View Pdf</a>
                                                                @elseif(in_array($PanCardImgextension, ['doc', 'docx']))
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $PanCardImg, type: 'backend-basic') }}" target="_blank">View Doc</a>
                                                                @endif
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="pan_card_image" class="custom-upload-input-file action-upload-color-image-pdf {{ (( $getUniqueArray['pan_card_image'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['pan_card_image'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_viewer3" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" {{ (( $getUniqueArray['pan_card_image'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['pan_card_image'] == 2 ? 'onchange=$(\'.pancard_image-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['pan_card_image'] == 2)?'required':'' ) }} placeholder="Please Choose PanCard Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['pan_card_image'] == 2)
                                                                        <img class="h-auto aspect-1 pancard_image-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif

                                                                        @if(in_array($PanCardImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img id="pre_img_viewer3" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $PanCardImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($PanCardImgextension === 'pdf')
                                                                        <img id="pre_img_viewer3" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($PanCardImgextension, ['doc', 'docx']))
                                                                        <img id="pre_img_viewer3" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img id="pre_img_viewer3" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}">
                                                                        @endif
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
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('Trust PAN Card Number') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" maxlength="10" class="form-control {{ (( $getUniqueArray['trust_pan_card'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['trust_pan_card'] == 2)?'is-invalid':'' ) }}" id="trust_pan_card" name="trust_pan_card" value="{{ ($getData['trust_pan_card']??'') }}" placeholder="{{ translate('enter_your_PAN_number') }}" {{ (( $getUniqueArray['trust_pan_card'] == 1)?'readOnly':"" ) }} onkeyup="formatPAN(this)">
                                                                <small id="panError" class="text-danger font-weight-bolder">{{ (( $getUniqueArray['trust_pan_card'] == 2)?'Please Enter Correct Pan Number':'' ) }}</small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="">Pancard Image</label>
                                                                @php
                                                                $TrustPanCardImg = $getData['trust_pan_card_image']??'';
                                                                $TrustPanCardImgextension = strtolower(pathinfo($TrustPanCardImg, PATHINFO_EXTENSION));
                                                                @endphp
                                                                @if($TrustPanCardImgextension === 'pdf')
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TrustPanCardImg, type: 'backend-basic') }}" target="_blank">View Pdf</a>
                                                                @elseif(in_array($TrustPanCardImgextension, ['doc', 'docx']))
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TrustPanCardImg, type: 'backend-basic') }}" target="_blank">View Doc</a>
                                                                @endif
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="trust_pan_card_image" class="custom-upload-input-file action-upload-color-image-pdf {{ (( $getUniqueArray['trust_pan_card_image'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['trust_pan_card_image'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_trust_pan_card" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" {{ (( $getUniqueArray['trust_pan_card_image'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['trust_pan_card_image'] == 2 ? 'onchange=$(\'.pancard_trust-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['trust_pan_card_image'] == 2)?'required':'' ) }} placeholder="Please Choose Trust Pan Card Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['trust_pan_card_image'] == 2)
                                                                        <img class="h-auto aspect-1 pancard_trust-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        @if(in_array($TrustPanCardImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img id="pre_img_trust_pan_card" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TrustPanCardImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($TrustPanCardImgextension === 'pdf')
                                                                        <img id="pre_img_trust_pan_card" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($TrustPanCardImgextension, ['doc', 'docx']))
                                                                        <img id="pre_img_trust_pan_card" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img id="pre_img_trust_pan_card" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['trust_pan_card_image'] == 2)?'Please Choose Correct PanCard Image':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-3"></div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('12A certificate') }}</label>

                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['twelve_a_number'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['twelve_a_number'] == 2)?'is-invalid':'' ) }}" id="twelve_a_number" name="twelve_a_number" value="{{ ($getData['twelve_a_number']??'') }}" placeholder="{{ translate('enter_your_12A_number') }}" {{ (( $getUniqueArray['twelve_a_number'] == 1)?'readOnly':"" ) }}>
                                                                <small class="text-danger font-weight-bolder">{{ (( $getUniqueArray['twelve_a_number'] == 2)?'Please Enter Correct 12A Number':'' ) }}</small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="">{{ translate('12A certificate') }}</label>
                                                                @php
                                                                $TwelveCertificateImg = $getData['twelve_a_certificate']??'';
                                                                $TwelveCertificateImgextension = strtolower(pathinfo($TwelveCertificateImg, PATHINFO_EXTENSION));
                                                                @endphp
                                                                @if($TwelveCertificateImgextension === 'pdf')
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TwelveCertificateImg, type: 'backend-basic') }}" target="_blank">View Pdf</a>
                                                                @elseif(in_array($TwelveCertificateImgextension, ['doc', 'docx']))
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TwelveCertificateImg, type: 'backend-basic') }}" target="_blank">View Doc</a>
                                                                @endif
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="twelve_a_certificate" class="custom-upload-input-file action-upload-color-image-pdf {{ (( $getUniqueArray['twelve_a_certificate'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['twelve_a_certificate'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_twelve_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" {{ (( $getUniqueArray['twelve_a_certificate'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['twelve_a_certificate'] == 2 ? 'onchange=$(\'.twelve_a_certificate-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['twelve_a_certificate'] == 2)?'required':'' ) }} placeholder="Please Choose 12A certificate Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['twelve_a_certificate'] == 2)
                                                                        <img class="h-auto aspect-1 twelve_a_certificate-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        @if(in_array($TwelveCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img id="pre_img_twelve_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TwelveCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($TwelveCertificateImgextension === 'pdf')
                                                                        <img id="pre_img_twelve_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($TwelveCertificateImgextension, ['doc', 'docx']))
                                                                        <img id="pre_img_twelve_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img id="pre_img_twelve_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['twelve_a_certificate'] == 2)?'Please Choose Correct 12A certificate Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('Eighty G certificate') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['eighty_g_number'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['eighty_g_number'] == 2)?'is-invalid':'' ) }}" id="eighty_g_number" name="eighty_g_number" value="{{ ($getData['eighty_g_number']??'') }}" placeholder="{{ translate('enter_your_80G_number') }}" {{ (( $getUniqueArray['eighty_g_number'] == 1)?'readOnly':"" ) }}>
                                                                <small class="text-danger font-weight-bolder">{{ (( $getUniqueArray['eighty_g_number'] == 2)?'Please Enter Correct 80G Number':'' ) }}</small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="">{{ translate('Eighty G certificate') }}</label>
                                                                @php
                                                                $EightygCertificateImg = $getData['eighty_g_certificate']??'';
                                                                $EightygCertificateImgextension = strtolower(pathinfo($EightygCertificateImg, PATHINFO_EXTENSION));
                                                                @endphp
                                                                @if($EightygCertificateImgextension === 'pdf')
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EightygCertificateImg, type: 'backend-basic') }}" target="_blank">View Pdf</a>
                                                                @elseif(in_array($EightygCertificateImgextension, ['doc', 'docx']))
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EightygCertificateImg, type: 'backend-basic') }}" target="_blank">View Doc</a>
                                                                @endif
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="eighty_g_certificate" class="custom-upload-input-file action-upload-color-image-pdf {{ (( $getUniqueArray['eighty_g_certificate'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['eighty_g_certificate'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_eighty_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" {{ (( $getUniqueArray['eighty_g_certificate'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['eighty_g_certificate'] == 2 ? 'onchange=$(\'.eighty_g_certificate-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['eighty_g_certificate'] == 2)?'required':'' ) }} placeholder="Please Choose Eighty G certificate Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['eighty_g_certificate'] == 2)
                                                                        <img class="h-auto aspect-1 eighty_g_certificate-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        @if(in_array($EightygCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img id="pre_img_eighty_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EightygCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($EightygCertificateImgextension === 'pdf')
                                                                        <img id="pre_img_eighty_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($EightygCertificateImgextension, ['doc', 'docx']))
                                                                        <img id="pre_img_eighty_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img id="pre_img_eighty_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['eighty_g_certificate'] == 2)?'Please Choose Correct Eighty G certificate Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <!-- satish -->
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('Niti aayog certificate') }}</label>

                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['niti_aayog_number'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['niti_aayog_number'] == 2)?'is-invalid':'' ) }}" id="niti_aayog_number" name="niti_aayog_number" value="{{ ($getData['niti_aayog_number']??'') }}" placeholder="{{ translate('enter_your_Niti_Aayog_number') }}" {{ (( $getUniqueArray['niti_aayog_number'] == 1)?'readOnly':"" ) }}>
                                                                <small class="text-danger font-weight-bolder">{{ (( $getUniqueArray['niti_aayog_number'] == 2)?'Please Enter Correct Niti Aayog Number':'' ) }}</small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="">{{ translate('Niti aayog certificate') }}</label>
                                                                @php
                                                                $NitiaayogCertificateImg = $getData['niti_aayog_certificate']??'';
                                                                $NitiaayogCertificateImgextension = strtolower(pathinfo($NitiaayogCertificateImg, PATHINFO_EXTENSION));
                                                                @endphp
                                                                @if($NitiaayogCertificateImgextension === 'pdf')
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $NitiaayogCertificateImg, type: 'backend-basic') }}" target="_blank">View Pdf</a>
                                                                @elseif(in_array($NitiaayogCertificateImgextension, ['doc', 'docx']))
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $NitiaayogCertificateImg, type: 'backend-basic') }}" target="_blank">View Doc</a>
                                                                @endif
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="niti_aayog_certificate" class="custom-upload-input-file action-upload-color-image-pdf {{ (( $getUniqueArray['niti_aayog_certificate'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['niti_aayog_certificate'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_niti_aayog_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" {{ (( $getUniqueArray['niti_aayog_certificate'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['niti_aayog_certificate'] == 2 ? 'onchange=$(\'.niti_aayog_certificate-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['niti_aayog_certificate'] == 2)?'required':'' ) }} placeholder="Please Choose Niti aayog certificate Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['niti_aayog_certificate'] == 2)
                                                                        <img class="h-auto aspect-1 niti_aayog_certificate-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        @if(in_array($NitiaayogCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img id="pre_img_niti_aayog_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $NitiaayogCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($NitiaayogCertificateImgextension === 'pdf')
                                                                        <img id="pre_img_niti_aayog_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($NitiaayogCertificateImgextension, ['doc', 'docx']))
                                                                        <img id="pre_img_niti_aayog_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img id="pre_img_niti_aayog_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['niti_aayog_certificate'] == 2)?'Please Choose Correct Niti aayog certificate Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('CSR certificate') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['csr_number'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['csr_number'] == 2)?'is-invalid':'' ) }}" id="csr_number" name="csr_number" value="{{ ($getData['csr_number']??'') }}" placeholder="{{ translate('enter_your_csr_number') }}" {{ (( $getUniqueArray['csr_number'] == 1)?'readOnly':"" ) }}>
                                                                <small class="text-danger font-weight-bolder">{{ (( $getUniqueArray['csr_number'] == 2)?'Please Enter Correct CSR Number':'' ) }}</small>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label for="">{{ translate('CSR certificate') }}</label>
                                                                @php
                                                                $CSRCertificateImg = $getData['csr_certificate']??'';
                                                                $CSRCertificateImgextension = strtolower(pathinfo($CSRCertificateImg, PATHINFO_EXTENSION));
                                                                @endphp
                                                                @if($CSRCertificateImgextension === 'pdf')
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $CSRCertificateImg, type: 'backend-basic') }}" target="_blank">View Pdf</a>
                                                                @elseif(in_array($CSRCertificateImgextension, ['doc', 'docx']))
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $CSRCertificateImg, type: 'backend-basic') }}" target="_blank">View Doc</a>
                                                                @endif
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="csr_certificate" class="custom-upload-input-file action-upload-color-image-pdf {{ (( $getUniqueArray['csr_certificate'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['csr_certificate'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_csr_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" {{ (( $getUniqueArray['csr_certificate'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['csr_certificate'] == 2 ? 'onchange=$(\'.csr_certificate-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['csr_certificate'] == 2)?'required':'' ) }} placeholder="Please Choose CSR certificate Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['csr_certificate'] == 2)
                                                                        <img class="h-auto aspect-1 csr_certificate-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        @if(in_array($CSRCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img id="pre_img_csr_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $CSRCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($CSRCertificateImgextension === 'pdf')
                                                                        <img id="pre_img_csr_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($CSRCertificateImgextension, ['doc', 'docx']))
                                                                        <img id="pre_img_csr_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img id="pre_img_csr_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['csr_certificate'] == 2)?'Please Choose Correct CSR certificate Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('E anudhan certificate') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['e_anudhan_number'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['e_anudhan_number'] == 2)?'is-invalid':'' ) }}" id="e_anudhan_number" name="e_anudhan_number" value="{{ ($getData['e_anudhan_number']??'') }}" placeholder="{{ translate('enter_your_e_anudhan_number') }}" {{ (( $getUniqueArray['e_anudhan_number'] == 1)?'readOnly':"" ) }}>
                                                                <small class="text-danger font-weight-bolder">{{ (( $getUniqueArray['e_anudhan_number'] == 2)?'Please Enter Correct E-anudhan Number':'' ) }}</small>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label for="">{{ translate('E anudhan certificate') }}</label>
                                                                @php
                                                                $EAnudhanCertificateImg = $getData['e_anudhan_certificate']??'';
                                                                $EAnudhanCertificateImgextension = strtolower(pathinfo($EAnudhanCertificateImg, PATHINFO_EXTENSION));
                                                                @endphp
                                                                @if($EAnudhanCertificateImgextension === 'pdf')
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EAnudhanCertificateImg, type: 'backend-basic') }}" target="_blank">View Pdf</a>
                                                                @elseif(in_array($EAnudhanCertificateImgextension, ['doc', 'docx']))
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EAnudhanCertificateImg, type: 'backend-basic') }}" target="_blank">View Doc</a>
                                                                @endif
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="e_anudhan_certificate" class="custom-upload-input-file action-upload-color-image-pdf {{ (( $getUniqueArray['e_anudhan_certificate'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['e_anudhan_certificate'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_e_anudhan_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" {{ (( $getUniqueArray['e_anudhan_certificate'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['e_anudhan_certificate'] == 2 ? 'onchange=$(\'.e_anudhan_certificate-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['e_anudhan_certificate'] == 2)?'required':'' ) }} placeholder="Please Choose E anudhan certificate Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['e_anudhan_certificate'] == 2)
                                                                        <img class="h-auto aspect-1 e_anudhan_certificate-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        @if(in_array($EAnudhanCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img id="pre_img_e_anudhan_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EAnudhanCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($EAnudhanCertificateImgextension === 'pdf')
                                                                        <img id="pre_img_e_anudhan_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($EAnudhanCertificateImgextension, ['doc', 'docx']))
                                                                        <img id="pre_img_e_anudhan_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img id="pre_img_e_anudhan_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['e_anudhan_certificate'] == 2)?'Please Choose Correct E anudhan certificate Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('FRC certificate') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['frc_number'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['frc_number'] == 2)?'is-invalid':'' ) }}" id="frc_number" name="frc_number" value="{{ ($getData['frc_number']??'') }}" placeholder="{{ translate('enter_your_frc_number') }}" {{ (( $getUniqueArray['frc_number'] == 1)?'readOnly':"" ) }}>
                                                                <small class="text-danger font-weight-bolder">{{ (( $getUniqueArray['frc_number'] == 2)?'Please Enter Correct FRC Number':'' ) }}</small>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label for="">{{ translate('FRC certificate') }}</label>
                                                                @php
                                                                $FRCCertificateImg = $getData['frc_certificate']??'';
                                                                $FRCCertificateImgextension = strtolower(pathinfo($FRCCertificateImg, PATHINFO_EXTENSION));
                                                                @endphp
                                                                @if($FRCCertificateImgextension === 'pdf')
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $FRCCertificateImg, type: 'backend-basic') }}" target="_blank">View Pdf</a>
                                                                @elseif(in_array($FRCCertificateImgextension, ['doc', 'docx']))
                                                                <a href="{{ getValidImage(path: 'storage/app/public/donate/document/' . $FRCCertificateImg, type: 'backend-basic') }}" target="_blank">View Doc</a>
                                                                @endif
                                                                <div class="custom_upload_input">
                                                                    <input type="file" name="frc_certificate" class="custom-upload-input-file action-upload-color-image-pdf {{ (( $getUniqueArray['frc_certificate'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['frc_certificate'] == 2)?'is-invalid':'' ) }} " id="" data-imgpreview="pre_img_frc_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" {{ (( $getUniqueArray['frc_certificate'] == 1)?'disabled':"" ) }} {{ $getUniqueArray['frc_certificate'] == 2 ? 'onchange=$(\'.frc_certificate-image\').addClass(\'d-none\')' : '' }} {{ (( $getUniqueArray['frc_certificate'] == 2)?'required':'' ) }} placeholder="Please Choose FRC certificate Image">
                                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                                        @if($getUniqueArray['frc_certificate'] == 2)
                                                                        <img class="h-auto aspect-1 frc_certificate-image" src="{{ dynamicAsset(path: 'public/assets/front-end/img/rejected-icon.png') }}" style="position: absolute;" />
                                                                        @endif
                                                                        @if(in_array($FRCCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img id="pre_img_frc_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $FRCCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($FRCCertificateImgextension === 'pdf')
                                                                        <img id="pre_img_frc_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($FRCCertificateImgextension, ['doc', 'docx']))
                                                                        <img id="pre_img_frc_certificate" class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img id="pre_img_frc_certificate" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                            <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['frc_certificate'] == 2)?'Please Choose Correct FRC certificate Image':'' ) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <label class="col-sm-6 col-form-label input-label">{{ translate('gst_number') }}</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="form-control {{ (( $getUniqueArray['gst_number'] == 1)?'is-valid':'' ) }}  {{ (( $getUniqueArray['gst_number'] == 2)?'is-invalid':'' ) }}" id="gst_number" name="gst_number" value="{{ ($getData['gst_number']??'') }}" placeholder="{{ translate('enter_your_gst_number') }}" {{ (( $getUniqueArray['gst_number'] == 1)?'readOnly':"" ) }}>
                                                                <small class="text-danger font-weight-bolder">{{ (( $getUniqueArray['gst_number'] == 2)?'Please Enter Correct GST Number':'' ) }}</small>
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
                                                                <label for="account_no" class="title-color">{{translate('holder_Name')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <input type="text" name="holder_name" value="{{$getData['beneficiary_name']}}" class="form-control {{ (( $getUniqueArray['beneficiary_name'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['beneficiary_name'] == 2)?'is-invalid':'' ) }}" placeholder="Enter Holder Name" required {{ (( $getUniqueArray['beneficiary_name'] == 1)?'readOnly':"" ) }} onkeyup="return this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['beneficiary_name'] == 2)?'Please Enter Correct Holder Name':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="ifsc" class="title-color">{{translate('IFSC_code')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <input type="text" name="ifsc" value="{{$getData['ifsc_code']}}" class="form-control {{ (( $getUniqueArray['ifsc_code'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['ifsc_code'] == 2)?'is-invalid':'' ) }}" placeholder="Enter Bank IFSC Code" required {{ (( $getUniqueArray['ifsc_code'] == 1)?'readOnly':"" ) }}>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['ifsc_code'] == 2)?'Please Enter Correct Bank Ifsc code':'' ) }}</span>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="name" class="title-color">{{translate('account_type')}} <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <select name="account_type" class="form-control {{ (( $getUniqueArray['account_type'] == 1)?'is-valid':'' ) }} {{ (( $getUniqueArray['account_type'] == 2)?'is-invalid':'' ) }}" required {{ (( $getUniqueArray['account_type'] == 1)?'readOnly':"" ) }} placeholder="Enter Bank Type">
                                                                    <option value="saving account" {{ (($getData['account_type'] == 'saving account' )?'selected':'' ) }}>saving account</option>
                                                                    <option value="current account" {{ (($getData['account_type'] == 'current account' )?'selected':'' ) }}>current account</option>
                                                                </select>
                                                                <span class="text-danger font-weight-bolder">{{ (( $getUniqueArray['account_type'] == 2)?'Please Enter Correct Bank Name':'' ) }}</span>
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
                                                                    <img class="upload-img-view upload-img-view__banner upload-img-view" id="cancel_check-preview" src="{{getValidImage(path: 'storage/app/public/donate/document/'.($getData['cancelled_cheque_image']??''), type: 'backend-basic')}}" alt="{{translate('cancel_check_image')}}" />
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
                                                            <lable class="col-form-label font-weight-bold">{{ translate('Name') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['name']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('trust_Name') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['trust_name']??'') }}</div>

                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('trust_email') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['trust_email']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('category') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['category']['name']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('full_address') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['full_address']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('website_url') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ($getData['website']??'') }}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('Members') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            @php
                                                            $members = old('member_name.*', $getData['memberlist'] ? json_decode($getData['memberlist'], true) : []);
                                                            @endphp

                                                            @if($members)
                                                            @foreach($members as $index => $member)
                                                            <div class="row mt-2">
                                                                <div class="col-4">
                                                                    <span>{{ old('member_name.'.$index, ($member['member_name']??'')) }}</span>
                                                                </div>
                                                                <div class="col-4">
                                                                    <span>{{ old('member_phone_no.' . $index, ($member['member_phone_no']??'')) }}</span>
                                                                </div>
                                                                <div class="col-4">
                                                                    <span>{{ old('member_position.' . $index, ($member['member_position']??'')) }}</span>
                                                                </div>
                                                                <div class="col-12">
                                                                    <hr>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('description') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            {!! ($getData['description']??'') !!}

                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('image')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <a href="{{getValidImage(path: 'storage/app/public/donate/trust/'.($getData['theme_image']??''), type: 'backend-basic')}}" target="_blank">
                                                                        <img id="fassai-preview" src="{{getValidImage(path: 'storage/app/public/donate/trust/'.($getData['theme_image']??''), type: 'backend-basic')}}" alt="{{translate('fassai_image')}}" />
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-12 my-2">
                                                                    <lable class="col-form-label font-weight-bold">{{translate('additional_image')}}</lable>
                                                                </div>
                                                                @if(!empty($getData['gallery_image']) && json_decode($getData['gallery_image'],true))
                                                                @foreach (json_decode($getData['gallery_image'],true) as $key => $photo)
                                                                <?php $unique_id = rand(1111, 9999) ?>

                                                                <div class="col-sm-12 col-md-4">
                                                                    <div class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                                        <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                            <a href="{{ getValidImage(path: 'storage/app/public/donate/trust/' . $photo, type: 'backend-product') }}" target="_blank">
                                                                                <img id="additional_Image_{{ $unique_id }}" alt="" class="h-auto aspect-1 bg-white onerror-add-class-d-none" src="{{ getValidImage(path: 'storage/app/public/donate/trust/' . $photo, type: 'backend-product') }}">
                                                                            </a>
                                                                        </div>
                                                                        <div
                                                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                            <div
                                                                                class="d-flex flex-column justify-content-center align-items-center">
                                                                                <img alt="" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-75">
                                                                                <h3 class="text-muted">{{ translate('Upload_Image') }}
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card mb-3 mb-lg-5" id="document-div" style="display: none;">
                                                    <div class="row p-4 ">
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('pan_number') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['pan_card']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                    $PanCardImg = $getData['pan_card_image']??'';
                                                                    $PanCardImgextension = strtolower(pathinfo($PanCardImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$PanCardImg,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($PanCardImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $PanCardImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($PanCardImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($PanCardImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('trust_pan_number') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['trust_pan_card']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                    $TrustPanCardImg = $getData['trust_pan_card_image']??'';
                                                                    $TrustPanCardImgextension = strtolower(pathinfo($TrustPanCardImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$TrustPanCardImg ,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($TrustPanCardImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TrustPanCardImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($TrustPanCardImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($TrustPanCardImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- /////////////////////////////////////////////// -->
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('12A certificate') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['twelve_a_number']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                    $TwelveCertificateImg = $getData['twelve_a_certificate']??'';
                                                                    $TwelveCertificateImgextension = strtolower(pathinfo($TwelveCertificateImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$TwelveCertificateImg,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($TwelveCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $TwelveCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($TwelveCertificateImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($TwelveCertificateImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('Eighty G certificate') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['eighty_g_number']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                    $EightygCertificateImg = $getData['eighty_g_certificate']??'';
                                                                    $EightygCertificateImgextension = strtolower(pathinfo($EightygCertificateImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$EightygCertificateImg,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($EightygCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EightygCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($EightygCertificateImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($EightygCertificateImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('Niti aayog certificate') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['niti_aayog_number']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                    $NitiaayogCertificateImg = $getData['niti_aayog_certificate']??'';
                                                                    $NitiaayogCertificateImgextension = strtolower(pathinfo($NitiaayogCertificateImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$NitiaayogCertificateImg,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($NitiaayogCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $NitiaayogCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($NitiaayogCertificateImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($NitiaayogCertificateImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('CSR certificate') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['csr_number']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                    $CSRCertificateImg = $getData['csr_certificate']??'';
                                                                    $CSRCertificateImgextension = strtolower(pathinfo($CSRCertificateImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$CSRCertificateImg,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($CSRCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $CSRCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($CSRCertificateImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($CSRCertificateImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('E anudhan certificate') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    {{ ($getData['e_anudhan_number']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                    $EAnudhanCertificateImg = $getData['e_anudhan_certificate']??'';
                                                                    $EAnudhanCertificateImgextension = strtolower(pathinfo($EAnudhanCertificateImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$EAnudhanCertificateImg,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($EAnudhanCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $EAnudhanCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($EAnudhanCertificateImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($EAnudhanCertificateImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('FRC certificate') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">

                                                                <div class="col-12">
                                                                    {{ ($getData['frc_number']??'') }}
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                    $FRCCertificateImg = $getData['frc_certificate']??'';
                                                                    $FRCCertificateImgextension = strtolower(pathinfo($FRCCertificateImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$FRCCertificateImg,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($FRCCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $FRCCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($FRCCertificateImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($FRCCertificateImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/', type: 'backend-basic') }}" alt="">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('verified_access_certificate') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    @php
                                                                    $VerifiedCertificateImg = $getData['verified_access_certificate']??'';
                                                                    $VerifiedCertificateImgextension = strtolower(pathinfo($VerifiedCertificateImg, PATHINFO_EXTENSION));
                                                                    @endphp
                                                                    <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.$VerifiedCertificateImg,type: 'backend-basic') }}" target="_blank">
                                                                        @if(in_array($VerifiedCertificateImgextension, ['jpg', 'jpeg', 'png', 'webp']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/' . $VerifiedCertificateImg, type: 'backend-basic') }}" alt="">
                                                                        @elseif($VerifiedCertificateImgextension === 'pdf')
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}" alt="PDF" width="50">
                                                                        @elseif(in_array($VerifiedCertificateImgextension, ['doc', 'docx']))
                                                                        <img class="h-auto aspect-1 bg-white" src="{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}" alt="DOC/DOCX" width="50">
                                                                        @else
                                                                        <span>File not uploaded</span>
                                                                        @endif

                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{ translate('gst_number') }}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <span>{{$getData['gst_number']??''}}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- /////////////////////////////////////////////// -->
                                                    </div>
                                                </div>
                                                <div class="card mb-3 mb-lg-5" id="banks-div" style="display: none;">
                                                    <div class="row p-4 ">
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('bank_Name')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{$getData['bank_name']??""}}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('holder_Name')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{$getData['beneficiary_name']}}</div>
                                                        <div class="col-md-6 mt-2">
                                                            <lable class="col-form-label font-weight-bold">{{translate('account_type')}}</lable>
                                                        </div>
                                                        <div class="col-md-6 mt-2">{{ ucwords($getData['account_type']??'')}}</div>
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
                                                                        <a href="{{ getValidImage(path: 'storage/app/public/donate/document/'.($getData['cancelled_cheque_image']??''),type: 'backend-basic') }}" target="_blank">
                                                                            <img class="upload-img-view upload-img-view__banner bg-white" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.($getData['cancelled_cheque_image']??''),type: 'backend-basic') }}" alt="">
                                                                        </a>
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
                    <div class="form-system-language-form {{ ((auth('trust')->check())?'d-none':'')}}" id="password-section-form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-3 mb-lg-5">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ translate('change_your_password') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="update-password-form" action="{{ route('trustees-vendor.profile.profile-edit', [$relationEmployees]) }}" method="POST" enctype="multipart/form-data">
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
                                            <div class="d-flex justify-content-end">
                                                @if (Helpers::Employee_modules_permission('Profile', 'Password', 'Update'))
                                                <button type="button" data-form-id="update-password-form"
                                                    data-message="{{ translate('want_to_update_vendor_password') . '?' }}"
                                                    class="btn btn--primary {{ env('APP_MODE') != 'demo' ? 'form-submit' : 'call-demo' }}">{{ translate('save_changes') }}</button>
                                                @endif
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
    </div>


</div>
<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>
<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js')}}"></script>
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
        for (var instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        var formData = new FormData($('form.form_seller_info')[0]);
        $.ajax({
            url: "{{ route('trustees-vendor.profile.update2') }}",
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
        let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        let formatted = '';

        for (let i = 0; i < value.length && i < 10; i++) {
            if (i < 5) {
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i < 9) {
                if (/[0-9]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i === 9) {
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            }
        }
        input.value = formatted;
    }
</script>
<script>
    document.getElementById('addMember').addEventListener('click', function(event) {
        event.preventDefault();

        // Create a new row for the member inputs
        const newRow = document.createElement('div');
        newRow.className = 'row mt-2';
        newRow.innerHTML = `
        <div class="col-3"><input type='text' name='member_name[]' class='form-control'  required placeholder="{{ translate('please Enter a Member Name') }}"></div>
        <div class="col-3"><input type='text' name='member_phone_no[]' class='form-control'  required placeholder="{{ translate('please Enter a Member Phone Number') }}"></div>
        <div class="col-3"><input type='text' name='member_position[]' class='form-control'  required placeholder="{{ translate('please Enter a Member position') }}"></div>
        <div class="col-3">
            <a class="btn btn-outline-danger btn-sm removeMember"><i class='tio-remove'></i></a>
        </div>`;
        document.getElementById('memberContainer').appendChild(newRow);

        newRow.querySelector('.removeMember').addEventListener('click', function() {
            newRow.remove();
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const removeButton = document.querySelector('.removeMember');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                this.closest('.row').remove();
            });
        }
    });
</script>
<script>
    function uploadColorImagePDF(thisData = null) {
        if (!thisData) return;

        let file = thisData.files[0];
        let imageId = $(thisData).data('imgpreview');
        let img = document.getElementById(imageId);

        if (!file || !img) return;

        let fileName = file.name;
        let fileExt = fileName.split('.').pop().toLowerCase();

        let reader = new FileReader();

        if (['jpg', 'jpeg', 'png', 'webp'].includes(fileExt)) {
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else if (fileExt === 'pdf') {
            img.src = "{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/pdf.png') }}";
        } else if (['doc', 'docx'].includes(fileExt)) {
            img.src = "{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/word.png') }}";
        } else {
            img.src = "{{ dynamicAsset(path: 'public/assets/back-end/img/doc-icon/file.png') }}";
        }
        $('#' + imageId).removeClass('d-none');

        // Optional: update data-title or a label
        let container = thisData.closest('[data-title]');
        if (container) {
            container.setAttribute("data-title", fileName);
        }
    }

    // Bind event to input
    $('.action-upload-color-image-pdf').on('change', function() {
        uploadColorImagePDF(this);
    });
</script>
@endpush