// const aadhaarVerifyStatus = {{ $verify }};
//         let aadhaarVerified = aadhaarVerifyStatus == 0 ? true : false;

//         // nri check
//         $('#nriSwitch').on('change', function() {
//             if ($(this).is(':checked')) {
//                 $('#aadhaar').val('');
//                 $('#aadhaar-request-id').val('');
//                 $('#aadhaar-section').addClass('d-none');
//                 $('#passport-section').removeClass('d-none');
//                 $('#UserInfoHide').removeClass('d-none');
//             } else {
//                 $('#passport').val('');
//                 $('#passport-section').addClass('d-none');
//                 $('#aadhaar-section').removeClass('d-none');
//                 if (aadhaarVerifyStatus == 1) {
//                     $('#UserInfoHide').addClass('d-none');
//                 }
//             }
//         });

//         // ---- SEND OTP ----
//         $(document).on('click', '#sendOtpBtn', function() {
//             const aadhaarNumber = $('#aadhaar').val().trim();

//             if (aadhaarNumber.length !== 12) {
//                 toastr.error('Please enter a valid 12-digit Aadhaar number!');
//                 return;
//             }

//             $.ajax({
//                 url: "{{ url('api/v1/darshan/aadhar-send-otp') }}",
//                 type: "POST",
//                 data: {
//                     aadhaar_number: aadhaarNumber,
//                     _token: "{{ csrf_token() }}"
//                 },
//                 dataType: "json",
//                 headers: {
//                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
//                 },
//                 success: function(data) {
//                     if (data.status == 1) {
//                         $('#aadhaar-request-id').val(data.request_id);
//                         // $('#otpSection').slideDown();
//                         $('#otpSection').removeClass('d-none');
//                         // $('#sendOtpBtn').prop('disabled', true);
//                         toastr.success(
//                             'OTP sent successfully to your registered mobile number.');

//                     } else if (data.status == 2) {
//                         // Aadhaar already verified
//                         $('#aadhaar-request-id').val('');
//                         aadhaarVerified = true;
//                         $('#otpSection').addClass('d-none');
//                         $('#sendOtpBtn').addClass('d-none');
//                         $('#aadhaar').prop('disabled', true).removeClass('me-2');
//                         $('#UserInfoHide').removeClass('d-none');
//                         $('#name').val(data.data.name);
//                         // $('#mobile').val(data.data.phone);
//                         toastr.success('This Aadhaar is already verified.');

//                     } else {
//                         // Error from API
//                         $('#aadhaar-request-id').val('');
//                         $('#otpSection').addClass('d-none');
//                         alert(data.message);
//                     }
//                 },
//                 error: function() {
//                     toastr.success('Error sending OTP. Please try again.');
//                 }
//             });
//         });

//         // ---- VERIFY OTP ----
//         $('#verifyOtpBtn').on('click', function() {
//             const aadhaarOtp = $('#aadhaar-otp').val().trim();
//             const aadhaarRequestId = $('#aadhaar-request-id').val();

//             $('#aadhaar-no-error').text('');

//             if (aadhaarOtp.length !== 6) {
//                 $('#aadhaar-no-error').addClass('text-danger').text('Aadhaar OTP must be 6 digits');
//                 return;
//             }

//             $.ajax({
//                 url: "{{ url('api/v1/darshan/aadhar-otp-verify') }}",
//                 type: "POST",
//                 data: {
//                     otp: aadhaarOtp,
//                     request_id: aadhaarRequestId,
//                     _token: "{{ csrf_token() }}"
//                 },
//                 dataType: "json",
//                 headers: {
//                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
//                 },
//                 success: function(data) {
//                     if (data.status == 1) {
//                         aadhaarVerified = true;
//                         $('#otpSection').addClass('d-none');
//                         $('#UserInfoHide').removeClass('d-none');
//                         $('#name').val(data.data1.name);
//                         // $('#mobile').val(data.data1.phone);
//                         $('#sendOtpBtn').addClass('d-none');
//                         $('#aadhaar').prop('disabled', true).removeClass('me-2');
//                         toastr.success('Aadhaar verified successfully.');
//                     } else {
//                         $('#aadhaar-no-error').addClass('text-danger').text(data.message);
//                     }
//                 },
//                 error: function() {
//                     $('#aadhaar-no-error').addClass('text-danger').text(
//                         'Error verifying OTP.');
//                 }
//             });
//         });