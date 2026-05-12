@extends('layouts.back-end.app-seller')

@section('title', translate('profile_Settings'))
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@section('content')
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
            <div class="col-lg-3">
                <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                    <button type="button" class="navbar-toggler btn btn-block btn-white mb-3"
                        aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu"
                        data-toggle="collapse" data-target="#navbarVerticalNavMenu">
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
                        <ul id="navbarSettings"
                            class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active" href="javascript:" id="general-section">
                                    <i class="tio-user-outlined nav-icon"></i>{{ translate('basic_Information') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:" id="password-section">
                                    <i class="tio-lock-outlined nav-icon"></i> {{ translate('password') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <form action="{{ route('vendor.profile.update', [$vendor->id]) }}" method="post"
                    enctype="multipart/form-data" id="update-profile-form">
                    @csrf
                    <div class="card mb-3 mb-lg-5" id="general-div">
                        <div class="profile-cover">
                            @php($banner = !empty($shopBanner) ? dynamicStorage(path: 'storage/app/public/shop/banner/' . $shopBanner) : dynamicAsset(path: 'public/assets/back-end/img/1920x400/img2.jpg'))
                            <div class="profile-cover-img-wrapper profile-bg"
                                style="background-image: url({{ $banner }})"></div>
                        </div>
                        <div class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar">
                            <img id="viewer" class="avatar-img"
                                src="{{ getValidImage(path: 'storage/app/public/seller/' . $vendor->image, type: 'backend-profile') }}"
                                alt="{{ translate('image') }}">
                            <label class="change-profile-image-icon" for="custom-file-upload">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/add-photo.png') }}"
                                    alt="">
                            </label>
                        </div>
                    </div>
                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h5 class="mb-0 text-capitalize">{{ translate('basic_information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label for="firstNameLabel"
                                    class="col-sm-3 col-form-label input-label">{{ translate('full_Name') }}
                                    <i class="tio-help-outlined text-body ml-1" data-toggle="tooltip" data-placement="right"
                                        title="{{ ucwords($vendor->f_name . ' ' . $vendor->l_name) }}"></i></label>

                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="title-color">{{ translate('first_Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="f_name" value="{{ $vendor->f_name }}"
                                                class="form-control" id="name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="title-color">{{ translate('last_Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="l_name" value="{{ $vendor->l_name }}"
                                                class="form-control" id="name" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label for="phoneLabel"
                                    class="col-sm-3 col-form-label input-label">{{ translate('phone') }} </label>
                                <div class="col-sm-9 mb-3">
                                    <input class="form-control form-control-user phone-input-with-country-picker"
                                        type="tel" id="exampleInputPhone" value="{{ $vendor->phone ?? old('phone') }}"
                                        placeholder="{{ translate('enter_phone_number') }}" required>
                                    <div class="">
                                        <input type="text" class="country-picker-phone-number w-50"
                                            value="{{ $vendor->phone }}" name="phone" hidden readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="newEmailLabel"
                                    class="col-sm-3 col-form-label input-label">{{ translate('email') }}</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="newEmailLabel"
                                        value="{{ $vendor->email }}"
                                        placeholder="{{ translate('enter_new_email_address') }}" readonly>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-sm-3 col-form-label input-label">{{ translate('GST_Number') }}</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="gst"
                                        value="{{ $vendor->gst }}"
                                        placeholder="{{ translate('enter_your_gst_number') }}">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label
                                    class="col-sm-3 col-form-label input-label">{{ translate('aadhar_Number') }}</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="aadhar_number" name="aadhar_number"
                                        value="{{ $vendor->aadhar_number }}" placeholder="Enter your Aadhar number">
                                    <small id="aadharError" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-4 offset-md-1 text-center">
                                    <label for="">Aadhar Front Image</label>
                                    <div class="custom_upload_input">
                                        <input type="file" name="aadhar_front_image"
                                            class="custom-upload-input-file action-upload-color-image" id=""
                                            data-imgpreview="pre_img_viewer"
                                            accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_img_viewer" class="h-auto aspect-1 bg-white"
                                            src="{{ getValidImage(path: 'storage/app/public/seller/'.$vendor->aadhar_front_image) }}" alt="">
                                        </div>
                                        <div
                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 offset-md-2 text-center">
                                    <label for="">Aadhar Back Image</label>
                                    <div class="custom_upload_input">
                                        <input type="file" name="aadhar_back_image"
                                            class="custom-upload-input-file action-upload-color-image" id=""
                                            data-imgpreview="pre_img_viewer1"
                                            accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_img_viewer1" class="h-auto aspect-1 bg-white"
                                            src="{{ getValidImage(path: 'storage/app/public/seller/'.$vendor->aadhar_back_image) }}" alt="">
                                        </div>
                                        <div
                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group">
                                <label class="col-sm-3 col-form-label input-label">{{ translate('pan_Number') }}</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="pan_number" name="pan_number"
                                        value="{{ $vendor->pan_number }}"
                                        placeholder="{{ translate('enter_your_PAN_number') }}">
                                    <small id="panError" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-4 offset-md-4">
                                    <label for="">Pancard Image</label>
                                    <div class="custom_upload_input">
                                        <input type="file" name="pancard_image"
                                            class="custom-upload-input-file action-upload-color-image" id=""
                                            data-imgpreview="pre_img_viewer3"
                                            accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_img_viewer3" class="h-auto aspect-1 bg-white"
                                            src="{{ getValidImage(path: 'storage/app/public/seller/'.$vendor->pancard_image) }}" alt="">
                                        </div>
                                        <div
                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-75"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-none" id="select-img">
                                <input type="file" name="image" id="custom-file-upload"
                                    class="custom-file-input image-input" data-image-id="viewer"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button id="submit-button" type="button" data-form-id="update-profile-form"
                                    data-message="{{ translate('want_to_update_vendor_info') . '?' }}"
                                    class="btn btn--primary {{ env('APP_MODE') != 'demo' ? 'form-submit' : 'call-demo' }}">{{ translate('save_changes') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="password-div" class="card mb-3 mb-lg-5">
                    <div class="card-header">
                        <h5 class="mb-0">{{ translate('change_your_password') }}</h5>
                    </div>
                    <div class="card-body">
                        <form id="update-password-form"
                            action="{{ route('vendor.profile.update', [auth('seller')->id()]) }}" method="POST"
                            enctype="multipart/form-data">
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
                if (panInput.trim() !== ''){
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
                if (aadharInput.trim() !== ''){
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
@endpush
