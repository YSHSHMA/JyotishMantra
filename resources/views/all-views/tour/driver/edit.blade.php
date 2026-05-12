@extends('layouts.back-end.app-tour')

@section('title', translate('driver_Edit'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('driver_Edit') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new tour_cab -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tour-vendor.tour_cab_management.driver-edit') }}" method="post" enctype="multipart/form-data" id="services_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="name">{{ translate('cab_driver_name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{old('name',$getData['name'])}}" class="form-control" placeholder="{{ translate('enter_cab_driver_name') }}" required>
                                <input type="hidden" name="id" value="{{ $getData['id']}}">
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="title-color" for="phone">{{ translate('phone_number') }} </label>
                                    <input class="form-control form-control-user phone-input-with-country-picker  @error('phone') is-invalid @enderror onfillup"  value="{{old('phone',$getData['phone'])}}" type="tel" id="exampleInputPhone" placeholder="{{ translate('enter_phone_number') }}" required oninput="validatePhone(this)">
                                    <div class="">
                                        <input type="hidden" class="country-picker-phone-number w-50" name="phone" readonly>
                                    </div>
                                </div>
                                <span id="phone_error" style="color: red; font-size: 14px;"></span>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="email">{{ translate('enter_email_Id') }}</label>
                                <input type="text" name="email" value="{{old('email',$getData['email']) }}" class="form-control" placeholder="{{ translate('enter_email_Id') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="gender">{{ translate('gender') }}<span class="text-danger">*</span></label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ ((old('gender',$getData['gender']) == 'male' )?"selected":"" )}}>Male</option>
                                    <option value="female" {{ ((old('gender',$getData['gender']) == 'female' )?"selected":"" )}}>FeMale</option>
                                    <option value="other" {{ ((old('gender',$getData['gender']) == 'other' )?"selected":"" )}}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="reg_number">{{ translate('date_of_birth') }}<span class="text-danger">*</span></label>
                                <input type="date" name="dob" value="{{old('dob',$getData['dob'])}}" class="form-control" placeholder="{{ translate('enter_date_of_birth') }}" required onchange="validateDob(this)">
                                <span id="date_of_brith_error" style="color: red; font-size: 14px;"></span>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="year_ex">{{ translate('years_of_driving_experience') }}<span class="text-danger">*</span></label>
                                <input type="number" name="year_ex" value="{{old('year_ex',$getData['year_ex']) }}" class="form-control" placeholder="{{ translate('enter_years_of_driving_experience') }}" required>
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="title-color" for="license_number">{{ translate('driving_license_number') }}<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control " name="license_number" id="license_number_id" value="{{old('license_number',$getData['license_number'])}}" autocomplete="off" placeholder="{{ translate('enter_driving_license_number') }}" onkeyup="$('.license_verify_status_check').val(0);" require>
                                    <input type="hidden" name="licenseverify" class="license_verify_status_check" value="{{ old('licenseverify',(($getData['license_number'])? 1 : 0 ) ) }}">
                                    <button class="btn btn-primary license-verify-check" type="button" onclick="verifylicenseNumber()">Verify</button>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="pan_number">{{ translate('pan_number') }}<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control " name="pan_number" id="pan_card" value="{{old('pan_number',$getData['pan_number']) }}" autocomplete="off" placeholder="Enter PanCard Number" required onkeyup="validatePAN(this);$('.pancard_verify_status_check').val(0);">
                                    <input type="hidden" name="panverify" class="pancard_verify_status_check" value="{{ old('panverify',(($getData['pan_number'])? 1 : 0 ) ) }}">
                                    <button class="btn btn-primary pancard-verify-check" type="button" onclick="verifyPanCard()">Verify</button>
                                </div>
                                <small id="pan_error" style="color: red; display: none;">❌Invalid PAN Number(Format: ABCDE1234F)</small>
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="title-color" for="aadhar_number">{{ translate('aadhar_number') }}<span class="text-danger">*</span></label>
                                <div class="input-group aadhar_number_form">
                                    <input type="text" class="form-control" name="aadhar_number" autocomplete="off" value="{{old('aadhar_number',$getData['aadhar_number'])}}" maxlength="12" placeholder="Enter Aadhar Number" onkeyup="validateAadhar(this);$('.aadhar_verify_status_check').val(0);">
                                    <input type="hidden" name="aadharveriy" class="aadhar_verify_status_check" value="{{ old('aadharveriy',(($getData['aadhar_number']) ? 1 : 0 ) ) }}">
                                    <button class="btn btn-primary aadhar-send-buttons" type="button" onclick="aadharSendOtp()">Verify</button>
                                </div>
                                <div class="input-group aadhar_otp_form d-none">
                                    <input type="text" class="form-control aadhar_otp" pattern="\d{6}" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 6)" placeholder="{{ translate('Enter Aadhaar OTP') }}">
                                    <input type="hidden" class="aadhar_request_id">
                                    <button type="button" class="btn btn-warning text-white" onclick="aadharverifyOtp()">{{translate('OTP_verify')}}</button>
                                </div>
                                 <small id="aadhar_error" style="color: red; display: none;"></small>
                            </div>
                            <!--  -->
                            <div class="col-md-3 mb-4">
                                <div class="text-center">
                                    <img class="upload-img-view" id="driver_user_image" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/'.$getData['image'], type: 'backend-product')  }}" alt="">
                                </div>
                                <div class="form-group">
                                    <label for="detail_image" class="title-color"> {{ translate('driver_image') }}<span class="text-danger">*</span></label>
                                    <span class="ml-1 text-info"> {{ THEME_RATIO[theme_root_path()]['Brand Image'] }} </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#driver_user_image" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="detail-image"> {{ translate('choose_file') }} </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="text-center">
                                    <img class="upload-img-view" id="driving_license_number1" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/'.$getData['license_image'], type: 'backend-product')  }}" alt="">
                                </div>
                                <div class="form-group">
                                    <label for="detail_image" class="title-color"> {{ translate('license') }}<span class="text-danger">*</span></label>
                                    <span class="ml-1 text-info"> {{ THEME_RATIO[theme_root_path()]['Brand Image'] }} </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="license_image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#driving_license_number1" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="detail-image"> {{ translate('choose_file') }} </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="text-center">
                                    <img class="upload-img-view" id="pan_number1" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/'.$getData['pan_image'], type: 'backend-product')  }}" alt="">
                                </div>
                                <div class="form-group">
                                    <label for="detail_image" class="title-color"> {{ translate('pan_card') }}<span class="text-danger">*</span></label>
                                    <span class="ml-1 text-info"> {{ THEME_RATIO[theme_root_path()]['Brand Image'] }} </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="pan_image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#pan_number1" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="detail-image"> {{ translate('choose_file') }} </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="text-center">
                                    <img class="upload-img-view" id="aadhar_number1" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/'.$getData['aadhar_image'], type: 'backend-product')  }}" alt="">
                                </div>
                                <div class="form-group">
                                    <label for="detail_image" class="title-color"> {{ translate('aadhar_card') }}<span class="text-danger">*</span></label>
                                    <span class="ml-1 text-info"> {{ THEME_RATIO[theme_root_path()]['Brand Image'] }} </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="aadhar_image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#aadhar_number1" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="detail-image"> {{ translate('choose_file') }} </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Buttons for form actions -->
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    function validatePAN(input) {
        const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        const panValue = input.value.toUpperCase();

        input.value = panValue; // Ensure uppercase

        if (!panPattern.test(panValue)) {
            $('#pan_error').text('Please enter a valid PAN card number (e.g., ABCDE1234F).');
        } else {
            $('#pan_error').text('');
        }
    }

    function validateAadhar(input) {
        const aadharPattern = /^\d{12}$/;
        if (!aadharPattern.test(input.value)) {
            $("#aadhar_error").text("Aadhar number must be exactly 12 digits.");
        } else {
            $("#aadhar_error").text("");
        }
    }

    function validatePhone(input) {
        input.value = input.value.replace(/\D/g, '');

        if (input.value.length > 10) {
            input.value = input.value.slice(0, 10);
        }

        if (input.value.length < 10) {
            $("#phone_error").text('Phone number must be exactly 10 digits.');
        } else {
            $("#phone_error").text('');
        }
    }

    function validateDob(input) {
        const dobError = document.getElementById('date_of_brith_error');
        const dob = new Date(input.value);
        const today = new Date();
        if (isNaN(dob.getTime())) {
            dobError.textContent = 'Invalid date. Please enter a valid date.';
            return;
        }
        if (dob > today) {
            dobError.textContent = 'Date of birth cannot be in the future.';
            return;
        }
        const age = today.getFullYear() - dob.getFullYear();
        if (age < 18) {
            dobError.textContent = 'You must be at least 18 years old.';
            return;
        }
        dobError.textContent = '';
    }
</script>

<script>
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

    function verifylicenseNumber() {
        const licenseInput = document.getElementById("license_number_id").value.toUpperCase();
        const panRegex = /^[A-Z]{2}[0-9]{13}$/;
        if (licenseInput === "") {} else if (licenseInput) {
            $('.license-verify-check').attr('disabled', true);
            $.ajax({
                url: "{{ url('api/v1/donate/license-number-verified-check') }}",
                data: {
                    license_number: licenseInput,
                    dob: $('input[name="dob"]').val(),
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
                    $('.license_verify_status_check').val(0);
                },
                success: function(data) {
                    $('#loading').addClass('d--none');
                    if (data.status == 1) {
                        toastr.success(data.message);
                        $('.license-verify-check').attr('disabled', true);
                        $('.license-verify-check').html('<i class="tio-done"></i>Verified');
                        $('.license-verify-check').removeClass('btn-primary');
                        $('.license-verify-check').addClass('btn-success');
                        $('#license_number_id').attr('readonly', true);
                        $('.license_verify_status_check').val(1);
                    } else {
                        toastr.error(data.message);
                        $('.license-verify-check').attr('disabled', false);
                        $('#license_number_id').attr('readonly', false);
                        $('.license_verify_status_check').val(0);
                    }
                }
            });
        } else {
            $('.license_verify_status_check').val(0);
        }
    }

    function aadharSendOtp() {
        let aadhaarNumber = $('input[name="aadhar_number"]').val().trim();
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
                    $('input[name="aadhar_number"]').attr('readOnly', true);
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
        let aadhaarNumber = $('input[name="aadhar_number"]').val().trim();
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
                    $('input[name="aadhar_number"]').attr('readOnly', true);
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

     $(document).ready(function() {
        $('#services_form').on('submit', function(e) {
            var status = $('.aadhar_verify_status_check').val();
            var licensestatus = $('.license_verify_status_check').val();
            var panstatus = $('.pancard_verify_status_check').val();

            if (licensestatus == 0) {
                e.preventDefault();
                toastr.error('Please verify License Number before submitting!');
                return false;
            }else if (status == 0) {
                e.preventDefault();
                toastr.error('Please verify Aadhaar before submitting!');
                return false;
            }else if (panstatus == 0) {
                e.preventDefault();
                toastr.error('Please verify Pan Card before submitting!');
                return false;
            }
        });
    });
</script>
@endpush