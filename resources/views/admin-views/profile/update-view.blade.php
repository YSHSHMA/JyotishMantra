@extends('layouts.back-end.app')

@section('title', translate('profile_Settings'))
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    {{-- modal --}}
    <div class="modal fade" id="qr_code_modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
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
                            <input type="hidden" name="id" class="form-control" value="{{$admin['id']}}"
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

    {{-- main page --}}
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <h2 class="col-sm mb-2 mb-sm-0 h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/profile_setting.png') }}"
                        alt="">
                    {{ translate('settings') }}
                </h2>
                <div class="col-sm-auto">
                    <a class="btn btn--primary" href="{{ route('admin.dashboard.index') }}">
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
                @if (auth('admin')->user()->admin_role_id == 1)
                    <div id="2fa-div" class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h4 class="card-title">{{ translate('2FA Authentication') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="custom-control custom-switch mr-2">
                                <input type="hidden" id="id" value="{{ $admin['id'] }}">
                                <input type="hidden" id="email" value="{{ $admin['email'] }}">
                                <input type="checkbox" class="custom-control-input consultation-charge-checkbox"
                                    id="consultationChargeCustomSwitch" onchange="enable_2fa(this)" {{$admin['enable_2fa']==1?'checked':''}}>
                                <label class="custom-control-label" for="consultationChargeCustomSwitch"></label>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.profile.update', [$admin->id]) }}" method="post"
                    enctype="multipart/form-data" id="admin-profile-form">
                    @csrf
                    <div class="card mb-3 mb-lg-5" id="general-div">
                        <div class="profile-cover">
                            @php($banner = !empty($shopBanner) ? dynamicStorage(path: 'storage/app/public/shop/' . $shopBanner) : dynamicAsset(path: 'public/assets/back-end/img/1920x400/img2.jpg'))
                            <div class="profile-cover-img-wrapper profile-bg"
                                style="background-image: url({{ $banner }})"></div>
                        </div>
                        <div class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar">
                            <img id="viewer" class="avatar-img"
                                src="{{ getValidImage(path: 'storage/app/public/admin/' . $admin->image, type: 'backend-profile') }}"
                                alt="{{ translate('image') }}">
                            <label class="change-profile-image-icon" for="custom-file-upload">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/add-photo.png') }}"
                                    alt="">
                            </label>
                        </div>
                    </div>
                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h2 class="card-title h4 text-capitalize">{{ translate('basic_information') }}</h2>
                        </div>
                        <div class="card-body">

                            <div class="row form-group">
                                <label for="firstNameLabel" class="col-sm-3 col-form-label input-label">
                                    {{ translate('full_name') }}
                                    <i class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                        data-placement="right" title="{{ $admin->name }}">
                                    </i>
                                </label>

                                <div class="col-sm-9">
                                    <div class="input-group input-group-sm-down-break">
                                        <input type="text" class="form-control" name="name" id="firstNameLabel"
                                            placeholder="{{ translate('your_first_name') }}" aria-label="Your first name"
                                            value="{{ $admin->name }}">

                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="phoneLabel"
                                    class="col-sm-3 col-form-label input-label">{{ translate('phone') }} <span
                                        class="input-label-secondary">({{ translate('optional') }})</span></label>
                                <div class="col-sm-9 mb-3">
                                    <input class="form-control form-control-user phone-input-with-country-picker"
                                        type="tel" id="exampleInputPhone"
                                        value="{{ $admin->phone ?? old('phone') }}"
                                        placeholder="{{ translate('enter_phone_number') }}" required>
                                    <div class="">
                                        <input type="text" class="country-picker-phone-number w-50"
                                            value="{{ $admin->phone }}" name="phone" hidden readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="newEmailLabel"
                                    class="col-sm-3 col-form-label input-label">{{ translate('email') }}</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="newEmailLabel"
                                        value="{{ $admin->email }}"
                                        placeholder="{{ translate('enter_new_email_address') }}">
                                </div>
                            </div>
                            <div class="d-none" id="select-img">
                                <input type="file" name="image" id="custom-file-upload" data-image-id="viewer"
                                    class="custom-file-input  image-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" data-id="admin-profile-form"
                                    data-message="{{ translate('want_to_update_admin_info') . '?' }}"
                                    class="btn btn--primary {{ env('APP_MODE') != 'demo' ? 'form-alert' : 'call-demo' }}">{{ translate('save_changes') }}</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="password-div" class="card mb-3 mb-lg-5">
                    <div class="card-header">
                        <h4 class="card-title">{{ translate('change_your_password') }}</h4>
                    </div>
                    <div class="card-body">
                        <form id="change-password-form" action="{{ route('admin.profile.update', [$admin->id]) }}"
                            method="post" enctype="multipart/form-data">
                            @csrf @method('patch')
                            <div class="row form-group">
                                <label for="newPassword"
                                    class="col-sm-3 col-form-label input-label d-flex align-items-center">
                                    {{ translate('new_password') }}
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
                                <label for="confirmNewPasswordLabel" class="col-sm-3 col-form-label input-label">
                                    {{ translate('confirm_password') }} </label>
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
                                <button type="button" data-id="change-password-form"
                                    data-message="{{ translate('want_to_update_admin_password') . '?' }}"
                                    class="btn btn--primary {{ env('APP_MODE') != 'demo' ? 'form-alert' : 'call-demo' }}">{{ translate('save_changes') }}</button>
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
                url: "{{ route('admin.auth.2fa.show.qr') }}",
                data: data,
                success: function(response) {
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
                url: "{{ route('admin.auth.2fa.active') }}",
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
