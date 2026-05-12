@php
    use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Add_Traveller'))

@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('Add_Traveller') }}
        </h2>
    </div>

    <form class="product-form text-start" action="{{ route('admin.tour_and_travels.insert-traveller') }}" method="POST" enctype="multipart/form-data" id="services_form">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="">
                    <div class="card-header pt-0">
                        <div class="d-flex gap-2">
                            <i class="tio-company"></i>
                            <h4 class="mb-0">{{ translate('General_Information') }}</h4>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="owner_name">{{ translate('Owner_name') }} </label>
                                <input type="text" required name="owner_name" class="form-control @error('owner_name') is-invalid @enderror" value="{{ old('owner_name') }}" placeholder="{{ translate('Owner_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="company_name">{{ translate('Company_Name') }} </label>
                                <input type="text" required name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="{{ translate('company_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="phone-group">
                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                    <small class="text-primary">( *{{ translate('country_code_is_must_like_for_IND') }} 91 )</small>
                                </label>
                                <input class="form-control text-align-direction phone-input-with-country-picker  @error('phone_no') is-invalid @enderror onfillup" type="tel" required id="person-number" placeholder="{{ translate('enter_phone_number') }}" value="{{ old('phone_no') }}" oninput="this.value=this.value.slice(0,10)" data-point='1'>
                                <input type="hidden" name="phone_no" class="country-picker-phone-number w-50" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('Email_id') }} </label>
                                <input type="email" required name="email" class="form-control @error('email') is-invalid @enderror onfillup" value="{{ old('email') }}" placeholder="{{ translate('email_id') }}" data-point="2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('city') }} </label>
                                <input type="text" required name="city" autocomplete="off" class="form-control @error('city') is-invalid @enderror getAddress_google city_event" value="{{ old('city') }}" placeholder="{{ translate('city') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('state') }} </label>
                                <input type="text" required name="state" class="form-control @error('state') is-invalid @enderror state_event" value="{{ old('state') }}" autocomplete="off" placeholder="{{ translate('state') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('Full_Address') }} </label>
                                <input type="text" required name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="{{ translate('Full_address') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('WebSite_link') }} </label>
                                <input type="text" name="web_site_link" class="form-control @error('web_site_link') is-invalid @enderror onfillup" value="{{ old('web_site_link') }}" placeholder="{{ translate('Web_site_link') }}" data-point="3">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('Number_of_Experience') }} </label>
                                <input type="number" name="experience" class="form-control @error('experience') is-invalid @enderror onfillup" value="{{ old('experience') }}" placeholder="{{ translate('Enter_Number_of_Experience') }}" data-point="15">
                            </div>
                        </div>
                    </div>
                    <div class="card-header pt-0">
                        <div class="d-flex gap-2">
                            <i class="tio-briefcase"></i>
                            <h4 class="mb-0">{{ translate('Business_Details') }}</h4>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="services">{{ translate('Services') }} </label>
                                <textarea required name="services" id="services" class="form-control ckeditor @error('services') is-invalid @enderror">{{ old('services') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="expect_details">{{ translate('Area_of_Operation') }}</label>
                                <textarea required name="area_of_operation" id="area_of_operation " class="form-control ckeditor @error('area_of_operation') is-invalid @enderror">{{ old('area_of_operation') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-header pt-0">
                        <div class="d-flex gap-2">
                            <i class="tio-user"></i>
                            <h4 class="mb-0">{{ translate('Contact_Person_Details') }}</h4>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('person_name') }}</label>
                                <input type="text" name="person_name" class="form-control @error('person_name') is-invalid @enderror" value="{{ old('person_name') }}" placeholder="{{ translate('person_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('person_phone') }} </label>
                                <input type="text" name="person_phone" class="form-control  @error('person_phone') is-invalid @enderror onfillup" value="{{ old('person_phone') }}" placeholder="{{ translate('person_phone') }}" data-point="4">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('person_email') }} </label>
                                <input type="text" name="person_email" class="form-control  @error('person_email') is-invalid @enderror onfillup" value="{{ old('person_email') }}" placeholder="{{ translate('person_email') }}" data-point="5">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('person_address') }}</label>
                                <input type="text" name="person_address" class="form-control  @error('person_address') is-invalid @enderror" value="{{ old('person_address') }}" placeholder="{{ translate('person_address') }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-header pt-0">
                        <div class="d-flex gap-2">
                            <i class="tio-saving_outlined">saving_outlined</i>
                            <h4 class="mb-0">{{ translate('Bank_Details') }}</h4>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('Holder’s Name') }} </label>
                                <input type="text" name="bank_holder_name" class="form-control  @error('bank_holder_name') is-invalid @enderror onfillup" value="{{ old('bank_holder_name') }}" placeholder="{{ translate('Holder’s Name') }}" data-point="6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('Bank_Name') }} </label>
                                <input type="text" name="bank_name" class="form-control  @error('bank_name') is-invalid @enderror onfillup" value="{{ old('bank_name') }}" placeholder="{{ translate('Bank_Name') }}" data-point="7">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('Branch_name') }} </label>
                                <input type="text" name="bank_branch" class="form-control  @error('bank_branch') is-invalid @enderror onfillup" value="{{ old('bank_branch') }}" placeholder="{{ translate('Branch_name') }}" data-point="8">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('IFSC_code') }} </label>
                                <input type="text" name="ifsc_code" id="ifsc-code-bank-account" class="form-control  @error('ifsc_code') is-invalid @enderror onfillup" value="{{ old('ifsc_code') }}" placeholder="{{ translate('IFSC_code') }}" data-point="9">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('Account_number') }} </label>
                                <input type="text" id="account_number" name="account_number" class="form-control  @error('account_number') is-invalid @enderror onfillup" value="{{ old('account_number') }}" required placeholder="{{ translate('Account_number') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );" data-point="10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="">{{ translate('Confirm_Account_number') }} </label>
                                <input type="text" id="confirm_account_number" class="form-control" placeholder="{{ translate('Account_number') }}" value="{{ old('account_number') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );" onblur="validateAccountNumber();" required>
                                <small id="account_match_error" style="color: red; display: none;">Account numbers do not match</small>
                                <input type="hidden" name="bankverify" class="bank-verified-status" value="{{ old('bankverify',0) }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'add'))
                            <button type="button" class="btn btn-primary float-end bank-account-verify-button" onclick="bankVerified()">Bank Verify</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3 rest-part">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('GST_certificate') }}</label>
                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_GST_certificate') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group my-2">
                                    <input type="text" class="form-control gst-input-box" name="gst_number" autocomplete="off" value="{{ old('gst_number') }}" placeholder="Enter GST Number">
                                    <input type="hidden" name="getverify" class="gstno_verify_status_check" value="{{ old('getverify',0) }}">
                                    @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'add'))
                                    <button class="btn btn-primary gstnumber-verify-check" type="button" onclick="verifyGstNumber()">Verify</button>
                                    @endif
                                </div>
                                <div>
                                    <div class="custom_upload_input">
                                        <input type="file" name="gst_image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_gst_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>
                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_gst_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                        </div>
                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-2">
                                        {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp,' }}
                                        <br>
                                        {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 2 second -->
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Pan_card') }}</label>
                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group my-2">
                                    <input type="text" class="form-control " name="pan_card_number" id="pan_card"  value="{{ old('pan_card_number') }}" autocomplete="off" placeholder="Enter PanCard Number" onkeyup="formatPAN(this)">
                                    <input type="hidden" name="panverify" class="pancard_verify_status_check" value="{{ old('panverify') }}">
                                    @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'add'))
                                    <button class="btn btn-primary pancard-verify-check" type="button" onclick="verifyPanCard()">Verify</button>
                                    @endif
                                </div>
                                <small id="pan_error" style="color: red; display: none;">❌Invalid PAN Number(Format: ABCDE1234F)</small>
                                <div>
                                    <div class="custom_upload_input">
                                        <input type="file" name="pan_card_image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_pan_card_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_pan_card_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                        </div>
                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-muted mt-2">
                                        {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp,' }}
                                        <br>
                                        {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  3 three -->
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('aadhaar_card') }}</label>
                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_aadhaar_card') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group my-2 aadhar_number_form">
                                    <input type="text" class="form-control" name="aadhar_card_number" autocomplete="off" value="{{ old('account_number') }}" maxlength="12" placeholder="Enter Aadhar Number">
                                    <input type="hidden" name="aadharveriy" class="aadhar_verify_status_check" value="{{ old('aadharveriy',0) }}">
                                    @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'add'))
                                    <button class="btn btn-primary aadhar-send-buttons" type="button" onclick="aadharSendOtp()">Verify</button>
                                    @endif
                                </div>
                                <div class="input-group my-2 aadhar_otp_form d-none">
                                    <input type="text" class="form-control aadhar_otp" pattern="\d{6}" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 6)" placeholder="{{ translate('Enter Aadhaar OTP') }}">
                                    <input type="hidden" class="aadhar_request_id">
                                    <button type="button" class="btn btn-warning text-white" onclick="aadharverifyOtp()">{{translate('OTP_verify')}}</button>
                                </div>
                                <div>
                                    <div class="custom_upload_input">
                                        <input type="file" name="aadhaar_card_image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_aadhaar_card_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_aadhaar_card_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                        </div>
                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-2">
                                        {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp,' }}
                                        <br>
                                        {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4 forth -->
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Address_Proof') }}</label>
                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="custom_upload_input">
                                        <input type="file" name="address_proof_image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_address_proof_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>
                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_address_proof_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                        </div>
                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-2">
                                        {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp,' }}
                                        <br>
                                        {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 5 -->

                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Company_image') }}</label>
                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_Company_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="custom_upload_input">
                                        <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_Company_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>
                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_Company_img_viewer" class="h-auto aspect-1 bg-white d-none" src="" alt="">
                                        </div>
                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-2">
                                        {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp,' }}
                                        <br>
                                        {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Company_banner') }}</label> <br>
                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Meta Thumbnail'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_Company_banner_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="custom_upload_input">
                                        <input type="file" name="banner" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_Company_banner_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                            <i class="tio-delete"></i>
                                        </span>
                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_Company_banner_viewer" class="h-auto aspect-1 bg-white d-none" src="" alt="">
                                        </div>
                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-2">
                                        {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp,' }}
                                        <br>
                                        {{ translate('image_size') }} : {{ '290 x 73 px' }} {{ translate('max') }} {{ '2 MB' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="row justify-content-end gap-3 mt-3 mx-1">
            @if (Helpers::modules_permission_check('Tour', 'Travel Agent', 'add'))
            <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
            <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
            @endif
        </div>
    </form>
</div>

<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
{{-- ck editor --}}
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

<script>
    // time picker
    $('#opentime').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });
    $('#closetime').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });

    initSample();
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.ckeditor').ckeditor();
    });
</script>
<script type="text/javascript">
    $('.onfillup').on('input', function() {
        let val = $(this).val();
        let point = $(this).data('point');
        $(`.onfillup[data-point="${point}"]`).val(val);
    });
</script>

<script>
    function validateAccountNumber() {
        var accountNumber = document.getElementById("account_number").value;
        var confirmAccountNumber = document.getElementById("confirm_account_number").value;
        var errorMsg = document.getElementById("account_match_error");

        if (accountNumber !== confirmAccountNumber && confirmAccountNumber !== "") {
            errorMsg.style.display = "block";
            document.getElementById("confirm_account_number").value = "";
        } else {
            errorMsg.style.display = "none";
        }
    }

    function aadharSendOtp() {
        let aadhaarNumber = $('input[name="aadhar_card_number"]').val().trim();
        let phoneN = $('.country-picker-phone-number').val().trim();
        if (phoneN.length < 8) {
            phoneN = '';
        }
        let aadhaarRegex = /^\d{12}$/;
        if (!aadhaarRegex.test(aadhaarNumber)) {
            toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
            return;
        }
        $.ajax({
            url: "{{ url('api/v1/darshan/aadhar-send-otp') }}",
            data: {
                "aadhaar_number": aadhaarNumber,
                "_token": "{{ csrf_token() }}"
            },
            dataType: "json",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('.aadhar-send-buttons').attr('disabled', true);
            },
            success: function(data) {
                if (data.status == 1) {
                    toastr.success(data.message);
                    $('.aadhar_otp_form').removeClass('d-none');
                    $('.aadhar_number_form').addClass('d-none');
                    $('.aadhar_request_id').val(data.request_id);
                    $('.aadhar_verify_status_check').val(0);
                } else if (data.status == 2) {
                    toastr.error(data.message);
                    $('.aadhar_verify_status_check').val(1);
                    $('input[name="aadhar_card_number"]').attr('readOnly', true);
                    $('.aadhar-send-buttons').attr('disabled', true);
                    $('.aadhar-send-buttons').html('<i class="tio-done"></i>Verified');
                    $('.aadhar-send-buttons').removeClass('btn-primary');
                    $('.aadhar-send-buttons').addClass('btn-success');
                } else {
                    toastr.error(data.message);
                    $('.aadhar_request_id').val(data.request_id);
                    $('.aadhar_otp_form').addClass('d-none');
                    $('.aadhar_number_form').removeClass('d-none');
                    $('.aadhar_verify_status_check').val(0);
                }
            },
            complete: function() {
                $('.aadhar-send-buttons').attr('disabled', false);
            }
        });
    }

    function aadharverifyOtp() {
        let aadhaarNumber = $('input[name="aadhar_card_number"]').val().trim();
        let aadhaarRegex = /^\d{12}$/;
        if (!aadhaarRegex.test(aadhaarNumber)) {
            toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
            return;
        }
        let otp = $('.aadhar_otp').val().trim();
        let otpRegex = /^\d{6}$/;
        if (!otpRegex.test(otp)) {
            toastr.error('Please Enter a valid 6-digit OTP.');
            return;
        }
        $.ajax({
            url: "{{ url('api/v1/darshan/aadhar-otp-verify') }}",
            data: {
                "otp": otp,
                'request_id': $('.aadhar_request_id').val(),
                'phone_no': $('.country-picker-phone-number').val(),
                "user_id": "0",
                "_token": "{{ csrf_token() }}"
            },
            dataType: "json",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.status == 1) {
                    $('.aadhar_verify_status_check').val(1);
                    toastr.success(data.message);
                    $('.aadhar_otp_form').addClass('d-none');
                    $('.aadhar_number_form').removeClass('d-none');
                    $('input[name="aadhar_card_number"]').attr('readOnly', true);
                    $('.aadhar-send-buttons').attr('disabled', true);
                    $('.aadhar-send-buttons').html('<i class="tio-done"></i>Verified');
                    $('.aadhar-send-buttons').removeClass('btn-primary');
                    $('.aadhar-send-buttons').addClass('btn-success');
                } else {
                    toastr.error(data.message);
                    $('.aadhar_verify_status_check').val(0);
                }
            }
        });
    }

    function verifyPanCard() {
        const panInput = document.getElementById("pan_card").value.toUpperCase();
        const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        const errorElement = document.getElementById("pan_error");
        if (panInput === "") {
            errorElement.style.display = "none";
        } else if (panRegex.test(panInput)) {
            errorElement.style.display = "none";
            $('.pancard-verify-check').attr('disabled', true);
            $.ajax({
                url: "{{ url('api/v1/donate/pan-card-verified-check') }}",
                data: {
                    pancard: panInput,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('#loading').removeClass('d--none');
                    $('#loading').css('index', 1000);
                    $('.pancard_verify_status_check').val(0);
                },
                success: function(data) {
                    $('#loading').addClass('d--none');
                    if (data.status == 1) {
                        toastr.success(data.message);
                        $('.pancard-verify-check').attr('disabled', true);
                        $('.pancard-verify-check').html('<i class="tio-done"></i>Verified');
                        $('.pancard-verify-check').removeClass('btn-primary');
                        $('.pancard-verify-check').addClass('btn-success');
                        $('#pan_card').attr('readonly', true);
                        $('.pancard_verify_status_check').val(1);
                    } else {
                        toastr.error(data.message);
                        $('.pancard-verify-check').attr('disabled', false);
                        $('#pan_card').attr('readonly', false);
                        $('.pancard_verify_status_check').val(0);
                    }
                }
            });
        } else {
            errorElement.style.display = "block";
            $('.pancard_verify_status_check').val(0);
        }
    }

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

    function verifyGstNumber() {
        const gstInput = $('input[name="gst_number"]').val().trim();
        $('.gstnumber-verify-check').attr('disabled', true);
        $.ajax({
            url: "{{ url('api/v1/document-verify/gst-number') }}",
            data: {
                gst_number: gstInput,
                "_token": "{{ csrf_token() }}"
            },
            dataType: "json",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#loading').removeClass('d--none');
                $('#loading').css('index', 1000);
                $('.gstno_verify_status_check').val(0);
            },
            success: function(data) {
                $('#loading').addClass('d--none');
                if (data.status == 1) {
                    toastr.success(data.message);
                    $('.gstnumber-verify-check').attr('disabled', true);
                    $('.gstnumber-verify-check').html('<i class="tio-done"></i>Verified');
                    $('.gstnumber-verify-check').removeClass('btn-primary');
                    $('.gstnumber-verify-check').addClass('btn-success');
                    $('.gst-input-box').attr('readonly', true);
                    $('.gstno_verify_status_check').val(1);
                } else {
                    toastr.error(data.message);
                    $('.gstnumber-verify-check').attr('disabled', false);
                    $('.gst-input-box').attr('readonly', false);
                    $('.gstno_verify_status_check').val(0);
                }
            },
            error: function(xhr, status, error) {
                $('#loading').addClass('d--none');
                console.log("❌ Error:", xhr.responseText);
                toastr.error("Something went wrong: " + error);
                try {
                    let res = JSON.parse(xhr.responseText);
                    console.log(res.message);
                } catch (e) {
                    console.log("Raw error:", xhr.responseText);
                }
            }
        });
    }

    function bankVerified() {
        const gstInput = $('#confirm_account_number').val().trim();
        if (gstInput == '') {
            toastr.error("⚠️ Please enter your account number");
            return false;
        }
        const ifsc = $('input[name="ifsc_code"]').val().trim();
        $('.bank-account-verify-button').attr('disabled', true);
        $.ajax({
            url: "{{ url('api/v1/document-verify/bank-account') }}",
            data: {
                account_number: gstInput,
                ifsc: ifsc,
                "_token": "{{ csrf_token() }}"
            },
            dataType: "json",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#loading').removeClass('d--none');
                $('#loading').css('index', 1000);
                $('.bank-verified-status').val(0);
            },
            success: function(data) {
                $('#loading').addClass('d--none');
                if (data.status == 1) {
                    toastr.success(data.message);
                    $('.bank-account-verify-button').attr('disabled', true);
                    $('.bank-account-verify-button').html('<i class="tio-done"></i>Bank Verified');
                    $('.bank-account-verify-button').removeClass('btn-primary');
                    $('.bank-account-verify-button').addClass('btn-success');
                    $('#ifsc-code-bank-account').attr('readonly', true);
                    $('#account_number').attr('readonly', true);
                    $('#confirm_account_number').attr('readonly', true);
                    $('.bank-verified-status').val(1);
                } else {
                    toastr.error(data.message);
                    $('.bank-account-verify-button').attr('disabled', false);
                    $('#ifsc-code-bank-account').attr('readonly', false);
                    $('#account_number').attr('readonly', false);
                    $('#confirm_account_number').attr('readonly', false);
                    $('.bank-verified-status').val(0);
                }
            },
            error: function(xhr, status, error) {
                $('#loading').addClass('d--none');
                console.log("❌ Error:", xhr.responseText);
                toastr.error("Something went wrong: " + error);
                try {
                    let res = JSON.parse(xhr.responseText);
                    console.log(res.message);
                } catch (e) {
                    console.log("Raw error:", xhr.responseText);
                }
            }
        });
    }

    $(document).ready(function() {
        $('#services_form').on('submit', function(e) {
            var status = $('.aadhar_verify_status_check').val();
            var panstatus = $('.pancard_verify_status_check').val();


            let gstNo = $('input[name="gst_number"]').val().trim();
            var gststatus = $('.gstno_verify_status_check').val();
            var bankaccountstatus = $('.bank-verified-status').val();

            if (status == 0) {
                e.preventDefault();
                toastr.error('Please verify Aadhaar before submitting!');
                return false;
            } else if (bankaccountstatus == 0) {
                e.preventDefault();
                toastr.error('Please verify Bank Account Number before submitting!');
                return false;
            } else if (panstatus == 0) {
                e.preventDefault();
                toastr.error('Please verify Pan Card before submitting!');
                return false;
            } else if (gstNo.length > 1 && gststatus == 0) {
                e.preventDefault();
                toastr.error('Please verify GST Number before submitting!');
                return false;
            }
        });
    });

    $(".getAddress_google").each(function() {
    let inputElement = this;
    let autocomplete = new google.maps.places.Autocomplete(inputElement, {
        types: ['establishment'],
    });

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            $(inputElement).val('');
            return;
        }
        let city = '';
        let state = '';
        if (place.address_components) {
            place.address_components.forEach(component => {
                const types = component.types;

                if (types.includes("locality")) {
                    city = component.long_name;
                }
                if (types.includes("administrative_area_level_1")) {
                    state = component.long_name;
                }
            });
        }
        $(".city_event").val(city);
        $(".state_event").val(state);

        console.log("City:", city);
        console.log("State:", state);
    });
});

</script>
@endpush