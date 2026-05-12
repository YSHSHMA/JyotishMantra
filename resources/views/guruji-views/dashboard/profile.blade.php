@extends('layouts.back-end.app-guruji')
@push('css_or_js')
    <link rel="stylesheet"  href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@section('title', translate('update_profile'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/my-bank-info.png')}}" alt="">
                {{translate('update_profile')}}
            </h2>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card text-start">
                    <div class="border-bottom d-flex gap-3 flex-wrap justify-content-between align-items-center px-4 py-3">
                        <div class="d-flex gap-2 align-items-center">
                            <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/bank.png')}}" alt="" />
                            <h3 class="mb-0">{{translate('update_profile')}} <span data-toggle="tooltip" data-placement="right" data-title="{{translate('update_your_bank_details_with_correct_information').'.'.translate('it_will_be_used_for_your_withdraw_request_transactions by admin').'.'}}"> <img src="{{ dynamicAsset(path: 'public/assets/installation/assets/img/svg-icons/info.svg') }}" alt="" class="svg ml-1"> </span></h3>
                        </div>
                    </div>
                    <div class="card-body p-30">
                        <div class="row">
                            <!-- left -->
                            <div class="col-md-3">
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
                                            <li class="nav-item">
                                                <a class="nav-link" href="javascript:" id="account-section">
                                                    <i class="tio-lock-outlined nav-icon"></i> {{ translate('account') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- right -->
                            <div class="col-lg-9">
                                <div id="general-div" class="p-3">
                                    <form action="{{ route('guruji.profile.basicinfo-update', [$vendor->id]) }}" method="post"
                                    enctype="multipart/form-data" id="update-profile-form">
                                    @csrf
                                        <div class="card mb-3 mb-lg-5">
                                            <div class="profile-cover">
                                                @php
                                                    $banner = !empty($vendor->banner)
                                                        ? dynamicStorage(path: 'storage/app/public/astrologers/banner/' . $vendor->banner)
                                                        : dynamicAsset(path: 'public/assets/back-end/img/1920x400/img2.jpg');
                                                
                                                @endphp

                                                <div class="profile-cover-img-wrapper profile-bg"
                                                    style="background-image: url('{{ $banner }}')">
                                                </div>
                                            </div>

                                            <div class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar">

                                                <!-- IMAGE -->
                                                <img id="viewer" class="avatar-img cursor-pointer"
                                                    src="{{ dynamicStorage(path: 'astrologers/'.$vendor->image) }}" onclick="document.getElementById('custom-file-upload').click();"
                                                >

                                                <!-- HIDDEN FILE INPUT -->
                                                <input type="file" id="custom-file-upload"  name="image"
                                                    accept="image/*"     hidden   onchange="previewImage(event)"
                                                >

                                                <!-- CAMERA ICON -->
                                                <label class="change-profile-image-icon" for="custom-file-upload">
                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/add-photo.png') }}"
                                                        alt="{{ translate('change_image') }}">
                                                </label>
                                            </div>

                                        </div>
                                        <div class="row g-3">

                                            <div class="col-md-4">
                                                <label class="title-color">{{ translate('name') }}</label>
                                                <input type="text" name="name"
                                                    value="{{ $vendor->name }}"
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="title-color">{{ translate('email') }}</label>
                                                <input type="email" name="email"
                                                    value="{{ $vendor->email }}"
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="title-color">{{ translate('mobile_no') }}</label>
                                                <input type="tel"
                                                    value="{{ $vendor->mobile_no }}"
                                                    class="form-control" readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="title-color">{{ translate('gender') }}</label>
                                                <select name="gender" class="form-control">
                                                    <option value="male" {{ old('gender', $vendor->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                                    <option value="female" {{ old('gender', $vendor->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                                    <option value="other" {{ old('gender', $vendor->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="title-color">{{ translate('date_of_birth') }}</label>
                                                <input type="date"
                                                    name="dob"
                                                    value="{{ $vendor->dob }}"
                                                    class="form-control"
                                                    max="{{ now()->subYears(18)->format('Y-m-d') }}">
                                            </div>

                                            {{-- Address --}}
                                            <div class="col-md-12">
                                                <label class="title-color">{{ translate('address') }}</label>
                                                <textarea name="address"
                                                        class="form-control"
                                                        rows="3"
                                                        placeholder="{{ translate('enter_address') }}">{{ old('address', $vendor->address) }}</textarea>
                                            </div>

                                            {{-- Update Button Right Aligned --}}
                                            <div class="col-md-12 d-flex justify-content-end mt-2">
                                                <button class="btn btn-primary px-4" type="submit">
                                                    {{ translate('update') }}
                                                </button>
                                            </div>

                                        </div>

                                    </form>
                                </div>
                                <div id="password-div" class="card mb-3 mb-lg-5 p-2">
                                    <form action="{{ route('guruji.profile.password-update', [$vendor->id]) }}" method="post" enctype="multipart/form-data" id="update-profile-form">
                                    @csrf
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="title-color">{{ translate('old_password') }}</label>

                                                <div class="input-group">
                                                <input type="password" name="old_password" class="form-control" placeholder="Old Password">

                                                    <span class="input-group-text" style="cursor:pointer"
                                                        onclick="togglePassword()">
                                                        <i class="fa fa-eye" id="eyeIcon"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="title-color">{{ translate('new_password') }}</label>
                                                <input type="password" name="password" class="form-control" placeholder="New Password">

                                            </div>
                                            {{-- Update Button Right Aligned --}}
                                            <div class="col-md-12 d-flex justify-content-end mt-2">
                                                <button class="btn btn-primary px-4" type="submit">
                                                    {{ translate('update') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div id="account-div" class="card mb-3 mb-lg-5 p-2">
                                <form action="{{ route('guruji.profile.account-update', [$vendor->id]) }}"
                                    method="post" enctype="multipart/form-data" id="update-profile-form">
                                    @csrf

                                    <div class="row g-3">

                                        <!-- Holder Name -->
                                        <div class="col-md-6">
                                            <label class="title-color">{{ translate('holder_name') }}</label>
                                            <input type="text"  name="holder_name" class="form-control" value="{{ $vendor->holder_name }}" placeholder="{{ translate('holder_name') }}"
                                                required>
                                        </div>

                                        <!-- Bank Name -->
                                        <div class="col-md-6">
                                            <label class="title-color">{{ translate('bank_name') }}</label>
                                            <input type="text" name="bank_name"  class="form-control" value="{{ $vendor->bank_name }}"
                                                placeholder="{{ translate('bank_name') }}"
                                                required>
                                        </div>

                                        <!-- Account Number -->
                                        <div class="col-md-6">
                                            <label class="title-color">{{ translate('account_no') }}</label>
                                            <input type="text"
                                                name="account_no"
                                                class="form-control"
                                                value="{{ $vendor->account_no }}"
                                                placeholder="{{ translate('account_no') }}"
                                                pattern="\d{9,18}"
                                                inputmode="numeric"
                                                title="Account number must be 9 to 18 digits"
                                                required>
                                        </div>

                                        <!-- Confirm Account Number -->
                                        <div class="col-md-6">
                                            <label class="title-color">{{ translate('confirm_account_no') }}</label>
                                            <input type="text"
                                                name="confirm_account_no"
                                                class="form-control"
                                                value="{{ $vendor->account_no }}"
                                                placeholder="{{ translate('confirm_account_no') }}"
                                                pattern="\d{9,18}"
                                                inputmode="numeric"
                                                title="Account number must match"
                                                required>
                                        </div>

                                        <!-- IFSC Code -->
                                        <div class="col-md-6">
                                            <label class="title-color">{{ translate('bank_ifsc') }}</label>
                                            <input type="text"
                                                name="bank_ifsc"
                                                class="form-control text-uppercase"
                                                value="{{ $vendor->bank_ifsc }}"
                                                placeholder="{{ translate('bank_ifsc') }}"
                                                pattern="^[A-Z]{4}0[A-Z0-9]{6}$"
                                                title="Enter valid IFSC code (e.g. SBIN0001234)"
                                                required>
                                        </div>

                                        <!-- Passbook Image -->
                                        <div class="col-md-6">
                                            <label class="title-color">{{ translate('bank_passbook_image') }}</label>
                                            <!-- Image Preview -->
                                            <div class="mb-2">
                                                <img id="passbookPreview" src="{{ dynamicStorage(path: 'storage/app/public/astrologers/bankpassbook/' . $vendor->bank_passbook_image) }}"
                                                    alt="Passbook Image"  style="max-width: 200px; border:1px solid #ddd; padding:4px;">
                                            </div>
                                            <!-- File Input -->
                                            <input type="file"  name="bank_passbook_image"  class="form-control"
                                                accept=".jpg,.jpeg,.png,.webp"  onchange="previewPassbookImage(event)">
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-md-12 d-flex justify-content-end mt-2">
                                            <button class="btn btn-primary px-4" type="submit">
                                                {{ translate('update') }}
                                            </button>
                                        </div>

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
    <script>
    $(document).ready(function () {

        // default state
        $('#general-div').show();
        $('#password-div').hide();
        $('#account-div').hide();

        $('#general-section').on('click', function () {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('#general-div').show();
            $('#password-div').hide();
            $('#account-div').hide();
        });

        $('#password-section').on('click', function () {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');

            $('#general-div').hide();
            $('#account-div').hide();
            $('#password-div').show();
        });

        $('#account-section').on('click', function () {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');

            $('#general-div').hide();
            $('#password-div').hide();
            $('#account-div').show();
        });
    });
</script>
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        document.getElementById('viewer').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
<script>
function togglePassword() {
    const passwordField = document.getElementById('oldPassword');
    const eyeIcon = document.getElementById('eyeIcon');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>
<script>
function previewPassbookImage(event) {
    const input = event.target;
    const preview = document.getElementById('passbookPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endpush

