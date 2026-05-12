@extends('layouts.front-end.app')

@section('title', translate('astro_register'))

@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush

@section('content')
    <div class="container py-4 __inline-7 text-align-direction">
        <div class="login-card p-4 shadow-lg rounded bg-white">
            <div class="mx-auto __max-w-760">
                <h2 class="text-center h4 mb-4 font-bold text-capitalize fs-18-mobile">
                    {{ translate('astro_sign_up') }}
                </h2>

                <!-- Mobile Number -->
                <div class="mb-4">
                    <label class="form-label font-semibold">{{ translate('mobile_number') }}</label>
                    <input class="form-control text-align-direction" type="number" id="mobile-no"
                        placeholder="{{ translate('enter_mobile_number') }}" maxlength="10" required
                        oninput="this.value = this.value.slice(0, 10)">
                    <small class="text-danger" id="mobile-error"></small>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button class="btn btn--primary px-5" type="button" id="submit-btn">
                        {{ translate('submit') }}
                    </button>
                    <div class="spinner-border text--primary d-none" role="status" id="submit-spinner">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- Verification Sections -->
            <div class="row my-3 g-4 d-none" id="verification-div">
                <!-- Aadhaar -->
                {{-- <input type="hidden" name="" id="method" value="create">
                <input type="hidden" name="" id="name">
                <input type="hidden" name="" id="dob">
                <input type="hidden" name="" id="gender">
                <input type="hidden" name="" id="pincode">
                <input type="hidden" name="" id="state">
                <input type="hidden" name="" id="city">
                <input type="hidden" name="" id="address">
                <input type="hidden" name="" id="aadhaar-mobile">
                <input type="hidden" name="" id="aadhaar-image"> --}}
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 shadow-sm d-flex flex-column">
                        <h6 class="mb-3 d-flex align-items-center">
                            {{ translate('verify_aadhaar_no') }}
                        </h6>
                        <div class="mb-3">
                            <input type="hidden" name="" id="aadhaar-request-id">
                            <label class="form-label font-semibold">{{ translate('aadhaar_no.') }}</label>
                            <input class="form-control text-align-direction" type="number" id="aadhaar-no"
                                placeholder="{{ translate('enter_aadhaar_no') }}"
                                oninput="this.value = this.value.slice(0, 12)">
                        </div>

                        <div class="mb-3 d-none" id="aadhaar-otp-div">
                            <label class="form-label font-semibold">{{ translate('OTP') }}</label>
                            <input class="form-control text-align-direction" type="number" id="aadhaar-otp"
                                placeholder="{{ translate('enter_OTP') }}" oninput="this.value = this.value.slice(0, 6)">
                            {{-- <small class="" id="aadhaar-otp-error"></small> --}}
                        </div>
                        <small class="" id="aadhaar-no-error"></small>

                        <div class="mt-auto text-center">
                            <button class="btn btn--primary w-100" type="button" id="aadhaar-send-otp-btn">
                                {{ translate('verify') }}
                            </button>
                            <button class="btn btn--primary w-100 d-none" type="button" id="aadhaar-verify-btn">
                                {{ translate('verify_otp') }}
                            </button>
                            <div class="spinner-border text--primary d-none" role="status" id="aadhaar-spinner">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pancard -->
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 shadow-sm d-flex flex-column">
                        <h6 class="mb-3 d-flex align-items-center">
                            <i class="bi bi-credit-card-2-front-fill text-primary me-2 fs-5"></i>
                            {{ translate('verify_pancard_no') }}
                        </h6>
                        <div class="mb-3">
                            <label class="form-label font-semibold">{{ translate('pancard_no.') }}</label>
                            <input class="form-control text-align-direction" type="text" id="pancard-no"
                                placeholder="{{ translate('enter_pancard_no') }}">
                            <small class="" id="pancard-no-error"></small>
                        </div>
                        <div class="mt-auto text-center">
                            <button class="btn btn--primary w-100" type="button" id="pancard-verify-btn">
                                {{ translate('verify') }}
                            </button>
                            <div class="spinner-border text--primary d-none" role="status" id="pancard-spinner">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 shadow-sm d-flex flex-column">
                        <h6 class="mb-3 d-flex align-items-center">
                            <i class="bi bi-bank2 text-primary me-2 fs-5"></i>
                            {{ translate('verify_bank_details') }}
                        </h6>
                        <div class="mb-3">
                            <label class="form-label font-semibold">{{ translate('account_no.') }}</label>
                            <input class="form-control text-align-direction" type="number" id="account-no"
                                placeholder="{{ translate('enter_account_no.') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-semibold">{{ translate('bank_ifsc') }}</label>
                            <input class="form-control text-align-direction" type="text" id="bank-ifsc"
                                placeholder="{{ translate('enter_bank_ifsc') }}">
                            <small class="" id="bank-details-error"></small>
                        </div>
                        <div class="mt-auto text-center">
                            <button class="btn btn--primary w-100" type="button" id="bank-verify-btn">
                                {{ translate('verify') }}
                            </button>
                            <div class="spinner-border text--primary d-none" role="status" id="bank-spinner">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="text-success text-center d-none" id="success-message">
                <b>{{ translate('you_are_our_verified_astrologer/pandit._Please_login_to_continue.') }}</b>
            </h6>
        </div>

    </div>
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>

    <script>
        // submit button
        let aadhaarcardVerify = false;
        let pancardVerify = false;
        let bankdetailVerify = false;

        $('#submit-btn').click(function(e) {
            e.preventDefault();

            // var method = "";
            const mobile = $('#mobile-no').val();
            $('#mobile-error').text('');
            if (mobile.length !== 10) {
                $('#mobile-error').text('Mobile number must be 10 digits');
            } else {
                $('#submit-btn').addClass('d-none');
                $('#submit-spinner').removeClass('d-none');
                $.ajax({
                    url: "{{ route('astro.check-exist') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        mobile_no: mobile
                    },
                    success: function(res) {
                        $('#mobile-no').prop('readonly', true);
                        $('#submit-btn').addClass('d-none');
                        $('#submit-spinner').addClass('d-none');
                        $('#verification-div').removeClass('d-none');
                        if (res.status) {
                            // $('#method').val('update');
                            if (res.astro_exists.adharcard && res.astro_exists.adharcard.trim() !==
                                "") {
                                $('#aadhaar-no').val(res.astro_exists.adharcard);
                                $('#aadhaar-no').prop('disabled', true);
                                $('#aadhaar-no-error').addClass('text-success').text(
                                    'Aadhaar number already exists');
                                $('#aadhaar-send-otp-btn').prop('disabled', true);
                                aadhaarcardVerify = true;
                            }
                            if (res.astro_exists.pancard && res.astro_exists.pancard.trim() !== "") {
                                $('#pancard-no').val(res.astro_exists.pancard);
                                $('#pancard-no').prop('disabled', true);
                                $('#pancard-no-error').addClass('text-success').text(
                                    'Pancard number already exists');
                                $('#pancard-verify-btn').prop('disabled', true);
                                pancardVerify = true;
                            }
                            if (res.astro_exists.account_no && res.astro_exists.account_no.trim() !==
                                "") {
                                $('#account-no').val(res.astro_exists.account_no);
                                $('#bank-ifsc').val(res.astro_exists.bank_ifsc);
                                $('#account-no').prop('disabled', true);
                                $('#bank-ifsc').prop('disabled', true);
                                $('#bank-details-error').addClass('text-success').text(
                                    'Bank details already exists');
                                $('#bank-verify-btn').prop('disabled', true);
                                bankdetailVerify = true;
                            }
                            if (aadhaarcardVerify && pancardVerify && bankdetailVerify) {
                                $('#success-message').removeClass('d-none');
                            }
                        } else {
                            $('#submit-spinner').addClass('d-none');
                        }
                    }
                });
            }
        });

        // aadhar verification
        $('#aadhaar-send-otp-btn').click(function(e) {
            e.preventDefault();

            const aadhaarNumber = $('#aadhaar-no').val();
            $('#aadhaar-no-error').text('');
            if (aadhaarNumber.length !== 12) {
                $('#aadhaar-no-error').addClass('text-danger').text('Aadhaar no. number must be 12 digits');
            } else {
                $(this).addClass('d-none');
                $('#aadhaar-spinner').removeClass('d-none');
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
                    success: function(data) {
                        if (data.status == 1) {
                            $('#aadhaar-spinner').addClass('d-none');
                            $('#aadhaar-request-id').val(data.request_id);
                            $('#aadhaar-no').prop('disabled', true);
                            $('#aadhaar-otp-div').removeClass('d-none');
                            $('#aadhaar-verify-btn').removeClass('d-none');
                        } else if (data.status == 2) {
                            $('#aadhaar-request-id').val('');
                            astroProcess('aadhaarcard', aadhaarNumber);
                        } else {
                            $('#aadhaar-no-error').addClass('text-danger').text(data.message);
                            $('#aadhaar-request-id').val('');
                            $('#aadhaar-send-otp-btn').removeClass('d-none');
                            $('#aadhaar-spinner').addClass('d-none');
                        }
                    }
                });
            }
        });

        $('#aadhaar-verify-btn').click(function(e) {
            e.preventDefault();

            const aadhaarNumber = $('#aadhaar-no').val();
            const aadhaarOtp = $('#aadhaar-otp').val();
            const aadhaarRequestId = $('#aadhaar-request-id').val();
            $('#aadhaar-no-error').text('');
            if (aadhaarOtp.length !== 6) {
                $('#aadhaar-no-error').addClass('text-danger').text('Aadhaar OTP must be 6 digits');
            } else {
                $(this).addClass('d-none');
                $('#aadhaar-spinner').removeClass('d-none');
                $.ajax({
                    url: "{{ url('api/v1/darshan/aadhar-otp-verify') }}",
                    data: {
                        "otp": aadhaarOtp,
                        'request_id': aadhaarRequestId,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            astroProcess('aadhaarcard', aadhaarNumber);
                        } else {
                            $('#aadhaar-no-error').addClass('text-danger').text(data.message);
                            $('#aadhaar-verify-btn').removeClass('d-none');
                            $('#aadhaar-spinner').addClass('d-none');
                        }
                    }
                });
            }
        });

        // pancard verification
        $('#pancard-verify-btn').click(function(e) {
            e.preventDefault();

            const pancardNumber = $('#pancard-no').val().toUpperCase();
            $('#pancard-no-error').text('');

            const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

            if (pancardNumber.length === 0 || !panRegex.test(pancardNumber)) {
                $('#pancard-no-error')
                    .addClass('text-danger')
                    .text(pancardNumber.length === 0 ?
                        'Pancard cannot be empty' :
                        'Enter a valid Pancard number (e.g. ABCDE1234F)');
            } else {
                $(this).addClass('d-none');
                $('#pancard-spinner').removeClass('d-none');
                $.ajax({
                    url: "{{ url('api/v1/donate/pan-card-verified-check') }}",
                    data: {
                        pancard: pancardNumber,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            astroProcess('pancard', pancardNumber);
                        } else {
                            $('#pancard-no-error').addClass('text-danger').text(data.message);
                            $('#pancard-verify-btn').removeClass('d-none');
                            $('#pancard-spinner').addClass('d-none');
                        }
                    }
                });
            }
        });

        // bank verification
        $('#bank-verify-btn').click(function(e) {
            e.preventDefault();

            const accNumber = $('#account-no').val();
            const ifscNumber = $('#bank-ifsc').val();
            $('#bank-details-error').text('');
            if (accNumber.length === 0 || ifscNumber.length === 0) {
                $('#bank-details-error').addClass('text-danger').text('bank detail can not be empty');
            } else {
                $(this).addClass('d-none');
                $('#bank-spinner').removeClass('d-none');
                $.ajax({
                    url: "{{ url('api/v1/document-verify/bank-account') }}",
                    data: {
                        account_number: accNumber,
                        ifsc: ifscNumber,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            astroProcess('bank', accNumber);
                        } else {
                            $('#bank-details-error').addClass('text-danger').text(data.message);
                            $('#bank-verify-btn').removeClass('d-none');
                            $('#bank-spinner').addClass('d-none');
                        }
                    }
                });
            }
        });

        // astro create or update
        function astroProcess(type, idNo) {
            const mobileNo = $('#mobile-no').val();

            $.ajax({
                url: "{{ route('astro.process') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    mobile_no: mobileNo,
                    type: type,
                    id_no: idNo,
                },
                success: function(res) {
                    console.log(type);
                    console.log('stat ' + res.status);
                    if (res.status) {
                        if (type === 'aadhaarcard') {
                            console.log('aa yes');
                            $('#aadhaar-no').prop('disabled', true);
                            $('#aadhaar-otp').prop('disabled', true);
                            $('#aadhaar-no-error').removeClass('text-danger').addClass('text-success').text(res
                                .message);
                            $("#aadhaar-send-otp-btn").removeClass('d-none').prop('disabled', true);
                            $('#aadhaar-spinner').addClass('d-none');
                        } else if (type === 'pancard') {
                            console.log('pan yes');
                            $('#pancard-no').prop('disabled', true);
                            $('#pancard-no-error').removeClass('text-danger').addClass('text-success').text(res
                                .message);
                            $("#pancard-verify-btn").removeClass('d-none').prop('disabled', true);
                            $('#pancard-spinner').addClass('d-none');
                        } else if (type === 'bank') {
                            console.log('bnk yes');
                            $('#account-no').prop('disabled', true);
                            $('#bank-ifsc').prop('disabled', true);
                            $('#bank-details-error').removeClass('text-danger').addClass('text-success').text(
                                res.message);
                            $("#bank-verify-btn").removeClass('d-none').prop('disabled', true);
                            $('#bank-spinner').addClass('d-none');
                        }
                    } else {
                        if (type === 'aadhaarcard') {
                            console.log('aa no');
                            $('#aadhaar-no-error').addClass('text-danger').text(res.message);
                            $("#aadhaar-send-otp-btn").removeClass('d-none');
                            $("#aadhaar-verify-btn").addClass('d-none');
                            $('#aadhaar-spinner').addClass('d-none');
                        } else if (type === 'pancard') {
                            console.log('pan no');
                            $('#pancard-no-error').addClass('text-danger').text(res.message);
                            $("#pancard-verify-btn").removeClass('d-none');
                            $('#pancard-spinner').addClass('d-none');
                        } else if (type === 'bank') {
                            console.log('bank no');
                            $('#bank-details-error').addClass('text-danger').text(res.message);
                            $("#bank-verify-btn").removeClass('d-none');
                            $('#bank-spinner').addClass('d-none');
                        }
                    }
                }
            });
        }
    </script>
@endpush
