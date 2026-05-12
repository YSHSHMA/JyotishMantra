<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('VIP_Darshan_booking') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .toast-error {
            background-color: #d32f2f !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .max-w-md {
            max-width: 40rem !important;
        }

        .toast-success {
            background-color: #28a745 !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .toast {
            opacity: 1 !important;
            /* ensure it's not semi-transparent */
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6B21A8',
                        secondary: '#FACC15',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-b from-orange-400 via-orange-600 to-orange-800 min-h-screen font-sans">


    <!-- Header -->
    <header class="py-2 bg-white shadow-md">
        <div class="flex justify-center">
            <img src="https://mahakal.com/storage/app/public/company/2025-02-07-67a5b1849142e.gif" alt="Mahakal Logo"
                class="h-14">
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 md:grid-cols-4 gap-6">

        <!-- Left Side Form (75%) -->
        <section class="md:col-span-3 bg-white rounded-2xl shadow-lg p-6">
            <h2
                class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-500 via-orange-600 to-red-700 mb-6 text-center tracking-wide drop-shadow-md">
                {{ translate('Mandir_Darshan_Ticket_Booking') }}
            </h2>

            <div style="padding: 0px 30px 0px 8px;">
                <div class="mb-4">
                    <div class="flex gap-2">
                        <div class="w-full mx-auto mt-4">
                            <!-- Label -->
                            <label class="block font-semibold mb-1">{{ translate('Choose_Booking_Day') }}</label>

                            <!-- Buttons container -->
                            <div id="date-buttons" class="flex flex-wrap gap-3">
                                <button type="button" onclick="setDate(0, this)"
                                    class="flex-1 md:flex-none border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow transition text-center bg-orange-600 text-white">
                                    Today <span class="ml-1 font-semibold text-gray-700">{{ date('d-m-Y') }}</span>
                                </button>
                                <button type="button" onclick="setDate(1, this)"
                                    class="flex-1 md:flex-none border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow transition text-center">
                                    Tomorrow <span
                                        class="ml-1 font-semibold text-gray-700">{{ date('d-m-Y', strtotime('+1 day')) }}</span>
                                </button>
                                <button type="button" onclick="setDate(2, this)"
                                    class="flex-1 md:flex-none border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow transition text-center">
                                    Next Day <span
                                        class="ml-1 font-semibold text-gray-700">{{ date('d-m-Y', strtotime('+2 day')) }}</span>
                                </button>
                            </div>

                            <!-- Hidden input -->
                            <input type="hidden" name="date" id="hiddenDate" value="{{ date('d-m-Y') }}">
                        </div>

                        <div id="date-buttons" class="flex gap-2 mt-2"></div>
                        <div style="display:none;">
                            <label class="block font-semibold mb-1">Select Time Slot</label>
                            <select id="timeSlot" name="time_slot" class="w-full p-2 border rounded">
                                <option value="">-- Select Time Slot --</option>
                                <option value="08:00 AM - 10:00 PM" selected>Full day slot</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">{{ translate('Select_Package')}}</label>
                            <select id="packageSelect" name="package_id" class="w-full p-2 border rounded">
                                <option value="">{{ translate('Choose_Package')}}</option>
                                @foreach ($plans as $plan)
                                    @foreach ($plan['package'] as $package)
                                        <option value="{{ $plan['id'] }}" data-dates='@json($package['date'])'
                                            data-name='{{ $package['name'] }}' data-price='{{ $package['price'] }}'
                                            data-receiptprice='{{ $package['receipt_price'] }}'
                                            data-platformfee='{{ $package['platform_fee'] }}'
                                            data-platformGst='{{ $package['platform_gst'] }}'
                                            data-platformBasePrice='{{ $package['platform_base_price'] }}'>
                                            ({{ $plan['name'] }})
                                            {{ $package['name'] }} (₹{{ $package['price'] }})
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="block font-semibold mb-1">{{ translate('Select_Purohit')}}</label>
                            <select id="purohitSelect" name="purohit_id" class="w-full p-2 border rounded">
                                <option value="">{{ translate('Choose_Purohit')}}</option>
                                @foreach ($purohits as $purohit)
                                    <option value="{{ $purohit->id }}">{{ $purohit->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>



                <div class="my-4">
                    <hr>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-{{ ($temple['aadhaar_verify_status'] ?? 0) == 1 ? '2' : '1' }} gap-4 mb-4">

                    <div class="form-group">
                        <label class="form-label font-semibold">{{ translate('phone_number') }} <span
                                style="color: red;">*</span>
                            <small class="text-primary" style="font-size: 10px;">(
                                *{{ translate('country_code_is_must_like_for_IND') }} 91 )</small>
                        </label>
                        <input
                            class="flex-1 p-2 border rounded-l text-align-direction phone-input-with-country-picker w-full"
                            type="tel" name="person_phone" id="person-number"
                            placeholder="{{ translate('enter_phone_number') }}" maxlength="15" required
                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 12);">
                        <input type="hidden" class="country-picker-phone-number w-50" name="person_phone" readonly>
                    </div>
                    @if (($temple['aadhaar_verify_status'] ?? 0) == 1)
                        <div id="aadharSection">
                            <label class="block font-semibold mb-1">Aadhar Number<span
                                    style="color: red;">*</span></label>
                            <div class="flex">
                                <input type="text" name="aadhar"
                                    class="flex-1 p-2 border rounded-l aadhar_number_1"
                                    placeholder="Enter 12 Digit Aadhar Number"
                                    onkeyup="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="12" required>
                                <button type="button" onclick="sendOtps(1)"
                                    class="bg-primary text-white px-2 rounded-r hover:bg-primary-500 whitespace-nowrap aadhar_button_1 ml-2">
                                    Send OTP
                                </button>
                            </div>
                        </div>
                        <div id="otpSection" class="hidden mb-4">
                            <label class="block font-semibold mb-1">Enter OTP</label>
                            <div class="flex">
                                <input type="text" name="otp"
                                    class="w-full p-2 border rounded mb-2 aadhar_otp_1" placeholder="Enter Aadhar OTP"
                                    onkeyup="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="6" required>
                                <input type="hidden" class="aadhar_request_id_1" value="3671640">
                                <button type="button" onclick="Otpvarifyed(1)"
                                    class="bg-green-400 text-white px-2 rounded-r hover:bg-green-500 whitespace-nowrap ml-2"
                                    style="margin: 0px -2px 8px 0px;">
                                    Verify OTP
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                @if (($temple['aadhaar_verify_status'] ?? 0) == 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block font-semibold mb-1">Name<span style="color: red;">*</span></label>
                            <input type="text" class="w-full p-2 border rounded user_number_not_1"
                                placeholder="Enter Deveotes Name">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Address<span style="color: red;">*</span></label>
                            <input type="text" class="w-full p-2 border rounded user_address_not_1"
                                placeholder="Enter Deveotes Address">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">{{ translate('Aadhar_Number_(Optional)') }}</label>
                            <div style="display: flex;">
                                <input type="text" class="w-full p-2 border rounded aadhar_number_not_1"
                                    maxlength="12" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')"
                                    placeholder="Enter 12 Digit Aadhar Number ">
                                <button type="button"
                                    class="float-end bg-primary text-white px-4 py-2 rounded shadow text-without-aadhar ml-3"
                                    onclick="AddDevoteewithout()">ADD</button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="my-4">
                    <hr>
                </div>
                <div class="my-4">
                    <h5 class="mb-3 font-bold text-lg">{{ translate('Visitor Details') }}</h5>
                    <div id="devotee-container" class="space-y-2">
                        <p id="no-devotee-msg" class="text-gray-500 italic text-sm">
                            🙏 No visitors added yet. Please add devotee details to continue.
                        </p>
                    </div>

                </div>

                <div class="my-4">
                    <hr>
                </div>
                <div id="total-price" class="mt-4 hidden">
                    <div class="flex justify-between items-center bg-purple-50 rounded-xl px-4 py-3 shadow-sm">

                        <!-- Left: Label -->
                        <div class="text-gray-700 font-semibold">Total</div>

                        <!-- Middle: Member Count -->
                        <div id="total-members" class="text-sm text-gray-600"></div>

                        <!-- Right: Amount -->
                        <div id="total-amount" class="text-purple-900 font-bold text-lg"></div>
                    </div>
                </div>

                <div>
                    <button type="submit" class="bg-green-700 w-full text-white px-4 py-2 rounded font-semibold mt-4"
                        onclick="checkdarshanlimit()">Proceed to Payment</button>
                </div>
            </div>
        </section>

        <!-- Right Side Card (25%) -->
        <aside
            class="md:col-span-1 bg-white rounded-2xl shadow-lg p-4 flex flex-col justify-between h-fit sticky top-4">
            <!-- Top Section: Carousel -->
            <div class="w-full mb-4">
                <div class="owl-carousel">
                    @if (!empty($temple['galleries2']['images']) && json_decode($temple['galleries2']['images'], true))
                        @foreach (json_decode($temple['galleries2']['images'] ?? '[]', true) as $key => $photo)
                            <div class="item carousel-image">
                                <img src="{{ getValidImage(path: 'storage/app/public/temple/gallery/' . $photo, type: 'product') }}"
                                    class="w-full h-40 object-cover rounded-lg shadow-sm"
                                    alt="slide {{ $key + 1 }}">
                            </div>
                        @endforeach

                    @endif
                </div>
            </div>

            <!-- Middle Section: Temple Info -->
            <div class="text-center mb-4">
                <h3 class="text-xl font-bold text-purple-900 mb-2">{{ $temple->name }}</h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-2">{!! $temple->short_description ?? '' !!}</p>

                <!-- Time & Place Info -->
                <div class="flex flex-col items-start text-left text-gray-700 space-y-2 pt-2">
                    @if (isset($temple['cities']['city']))
                        <div class="flex items-center space-x-2">
                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/temple.png') }}"
                                alt="Location" class="w-6 h-6">
                            <span>
                                <strong> {{ $temple['cities']['city'] }},
                                    {{ ucwords(strtolower($temple['states']['name'] ?? '')) }},
                                    {{ $temple['country']['name'] ?? '' }} <strong>
                            </span>
                        </div>
                    @endif

                    @if (isset($temple['opening_time']) && isset($temple['closeing_time']))
                        <div class="flex items-center space-x-2">
                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/date.png') }}"
                                alt="Opening Hours" class="w-6 h-6">
                            <span>
                                <strong>{{ translate('opening_hours') }} :
                                    {{ date('h:i A', strtotime($temple['opening_time'])) }} -
                                    {{ date('h:i A', strtotime($temple['closeing_time'])) }} </strong>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bottom Section: Powered by -->
            <div class="mt-auto text-center">
                <div
                    class="bg-gradient-to-r from-orange-500 to-orange-700 text-white px-3 py-1 rounded-full text-xs font-semibold inline-block shadow-sm">
                    Powered by <span class="font-bold">Mahakal.com</span>
                </div>
            </div>


        </aside>



    </main>

    <!-- Popup -->
    <div id="popup"
        class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center overflow-auto z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md relative">
            <button onclick="closePopup()" class="absolute top-2 right-4 text-gray-600 text-xl">&times;</button>
            <h3 class="text-xl font-bold mb-4">Add Devotee</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 modal_addhar_view">

            </div>

            <div class="mt-4">
                <button onclick="addDevotee()" class="bg-purple-600 text-white px-4 py-2 rounded">Add</button>
            </div>
        </div>
    </div>
    {{-- USer confirm --}}
    <!-- Payment Confirmation Modal -->
    <div id="paymentModal"
        class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-100 p-6 relative">
            <button onclick="closePaymentModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
            <h2 class="text-xl font-bold mb-4">Confirm Your Booking</h2>
            <div id="bookingDetails" class="mb-4 text-sm text-gray-700"> </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2 text-gray-700">Payment Mode</label>
                <div class="flex gap-6 text-sm text-gray-800">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="payment_mode" value="cash" checked>
                        Cash Payment
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="payment_mode" value="online">
                        Online Payment
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button onclick="proceedPayment()"
                    class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    Proceed
                </button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".owl-carousel").owlCarousel({
                loop: true,
                autoplay: true,
                autoplayTimeout: 3000,
                dots: true,
                nav: false,
                margin: 15,
                responsive: {
                    0: {
                        items: 1 // mobile
                    },
                    600: {
                        items: 1 // tablet portrait
                    },
                    992: {
                        items: 1 // tablet landscape / small laptop
                    },
                    1200: {
                        items: 1 // large desktop
                    }
                }
            });
        });
    </script>
    <script>
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "closeButton": true,
            "progressBar": true
        };
        const UserArray = [];
        const packageSelect = document.getElementById('packageSelect');
        const purohitSelect = document.getElementById('purohitSelect');
        const hiddenDate = document.getElementById('hiddenDate');
        const timeSlot = document.getElementById('timeSlot');
        const otpSection = document.getElementById('otpSection');
        const aadharSection = document.getElementById('aadharSection');

        async function sendOtps(index) {
            const aadhaarNumber = $(`.aadhar_number_${index}`).val();
            const mobile = $('.country-picker-phone-number').val();
            if (mobile.length < 9) {
                toastr.error('Please Enter a valid Phone Number.');
                return;
            }
            let aadhaarRegex = /^\d{12}$/;
            if (!aadhaarRegex.test(aadhaarNumber)) {
                toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
                return;
            }
            let isDuplicate = UserArray.some((devotee, index) => {
                return devotee.aadhar == aadhaarNumber;
            });
            if (isDuplicate) {
                toastr.error('This Aadhaar number already exists.');
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
                    $(`.aadhar_button_${index}`).attr('disabled', true);
                },
                success: function(data) {
                    let isDuplicate = UserArray.some((devotee, index) => {
                        return devotee.aadhar == aadhaarNumber;
                    });
                    if (isDuplicate) {
                        toastr.error('This Aadhaar number already exists.');
                        return;
                    }
                    if (data.status == 1) {
                        toastr.success(data.message);
                        if (index > 1) {
                            $('#otpSection1').removeClass('hidden');
                            $('#aadharSection1').addClass('hidden');
                        } else {
                            otpSection.classList.remove('hidden');
                            aadharSection.classList.add('hidden');
                        }
                        $(`.aadhar_request_id_${index}`).val(data.request_id);
                    } else if (data.status == 2) {
                        toastr.success(data.message);
                        $(`.aadhar_number_${index}`).val('');
                        $(`.aadhar_button_${index}`).text('More Add');
                        if (index > 1) {
                            $('#otpSection1').addClass('hidden');
                            $('#aadharSection1').removeClass('hidden');
                        } else {
                            otpSection.classList.add('hidden');
                            aadharSection.classList.remove('hidden');
                        }
                        let nextId = 1;
                        if (UserArray.length > 0) {
                            nextId = UserArray[UserArray.length - 1].id + 1;
                        }
                        UserArray.push({
                            "id": nextId,
                            "fullName": data.data.name,
                            "address": data.data.address,
                            "aadhar": data.data.aadhar,
                            "image": data.data.image,
                            'phone': mobile,
                            'status': 1,
                        });
                        appendUserInfo()
                        closePopup()
                    } else {
                        toastr.error(data.message);
                        $(`.aadhar_request_id_${index}`).val(data.request_id);
                        if (index > 1) {
                            $('#otpSection1').addClass('hidden');
                            $('#aadharSection1').removeClass('hidden');
                        } else {
                            otpSection.classList.add('hidden');
                            aadharSection.classList.remove('hidden');
                        }
                    }
                },
                complete: function() {
                    $(`.aadhar_button_${index}`).attr('disabled', false);
                }
            });
        }

        async function Otpvarifyed(index) {
            let aadhaarNumber = $(`.aadhar_number_${index}`).val().trim();
            // let mobile = document.querySelector('input[name="mobile"]').value;
            let mobile = $('.country-picker-phone-number').val();
            if (mobile.length < 9) {
                toastr.error('Please Enter a valid Phone Number.');
            }
            let aadhaarRegex = /^\d{12}$/;
            if (!aadhaarRegex.test(aadhaarNumber)) {
                toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
                return;
            }
            let otp = $(`.aadhar_otp_${index}`).val().trim();
            let otpRegex = /^\d{6}$/;
            if (!otpRegex.test(otp)) {
                toastr.error('Please Enter a valid 6-digit OTP.');
                return;
            }
            $.ajax({
                url: "{{ url('api/v1/darshan/aadhar-otp-verify') }}",
                data: {
                    "otp": otp,
                    'request_id': $(`.aadhar_request_id_${index}`).val(),
                    'phone_no': mobile,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status == 1) {
                        toastr.success(data.message);
                        // $(`.aadhar_number_${index}`).attr('readonly', true);
                        // $(`.aadhar_button_${index}`).attr('disabled', true);
                        $(`.aadhar_number_${index}`).val('');
                        $(`.aadhar_button_${index}`).text('More Add');
                        if (index > 1) {
                            $('#otpSection1').addClass('hidden');
                            $('#aadharSection1').removeClass('hidden');
                        } else {
                            otpSection.classList.add('hidden');
                            aadharSection.classList.remove('hidden');
                        }
                        let nextId = 1;
                        if (UserArray.length > 0) {
                            nextId = UserArray[UserArray.length - 1].id + 1;
                        }
                        UserArray.push({
                            "id": nextId,
                            "fullName": data.data1.name,
                            "address": data.data1.address,
                            "aadhar": data.data1.aadhar,
                            "image": data.data1.image,
                            'phone': mobile,
                            'status': 1,
                        });
                        appendUserInfo()
                        closePopup()
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, messages) {
                            messages.forEach(function(msg) {
                                toastr.error(msg); // Show each message
                            });
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message); // Fallback to general message
                    } else {
                        toastr.error('Unexpected error occurred.');
                    }
                }
            });
        }


        function openPopup() {
            let count = 2;
            let html = `<div>
                    <label class="block font-semibold mb-1">Name</label>
                    <input type="text" name="popup_name" class="w-full p-2 border rounded  user_name_${count}">
                </div>
                <div id="aadharSection1">
                        <label class="block font-semibold mb-1">Aadhar Number ` + ((
                "{{ $temple['aadhaar_verify_status'] ?? 0 }}" == 1) ? '(required verify)' : '(optional verify)') + `</label>
                        <div class="flex">
                            <input type="text" name="aadhar" class="flex-1 p-2 border rounded-l aadhar_number_${count}" placeholder="Enter Aadhar" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="12" required>
                            <button type="button" onclick="sendOtps(${count})" class="bg-primary text-white px-4 rounded-r hover:bg-primary-500 whitespace-nowrap aadhar_button_${count}">
                                Send OTP
                            </button>
                        </div>
                    </div>
                    <div id="otpSection1" class="hidden mb-4">
                        <label class="block font-semibold mb-1">Enter OTP</label>
                        <div class="flex">
                            <input type="text" name="otp" class="w-full p-2 border rounded mb-2 aadhar_otp_${count}" placeholder="Enter Aadhar OTP" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="6" required>
                            <input type="hidden" class="aadhar_request_id_${count}" value="">
                            <button type="button" onclick="Otpvarifyed(${count})" class="bg-green-400 text-white px-4 rounded-r hover:bg-green-500 whitespace-nowrap ml-2" style="margin: 0px -2px 8px 0px;">
                                Verify OTP
                            </button>
                        </div>
                    </div>
                              <input type="hidden" class="indexget" value="${count}">`;
                    $(".modal_addhar_view").html(html);
                    document.getElementById('popup').classList.remove('hidden');
        }

        function closePopup() {
            appendUserInfo();
            document.getElementById('popup').classList.add('hidden');
        }

        function addDevotee() {
            let index = $('.indexget').val();
            let aadhaarNumber = $(`.aadhar_number_${index}`).val().trim();
            let userName = $(`.user_name_${index}`).val().trim();
            // let mobile = document.querySelector('input[name="mobile"]').value;
            let mobile = $('.country-picker-phone-number').val();
            let aadhaarRegex = /^\d{12}$/;
            if (mobile.length < 9) {
                toastr.error('Please Enter a valid Phone Number.');
                return;
            } else if (userName.length < 2) {
                toastr.error('Please Enter Devotee Name.');
                return;
            } else if (!aadhaarRegex.test(aadhaarNumber)) {
                toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
                return;
            }
            if ("{{ $temple['aadhaar_verify_status'] ?? 0 }}" == 1) {
                toastr.error('Please verify Aadhaar before proceeding.');
                return;
            } else {
                let isDuplicate = UserArray.some((devotee, index) => {
                    return devotee.aadhar == aadhaarNumber;
                });
                if (isDuplicate) {
                    toastr.error('This Aadhaar number already exists.');
                    return;
                }
                let nextId = 1;
                if (UserArray.length > 0) {
                    nextId = UserArray[UserArray.length - 1].id + 1;
                }
                UserArray.push({
                    "id": nextId,
                    "fullName": userName,
                    "address": data.data.address,
                    "aadhar": aadhaarNumber,
                    'phone': mobile,
                    'status': 0,
                });
            }
            closePopup();
            console.log(UserArray);
            toastr.success('Devotee added successfully.');
        }

        function AddDevoteewithout() {
            let aadhaarNumber = $(`.aadhar_number_not_1`).val().trim();
            let userName = $(`.user_number_not_1`).val().trim();
            let userAddress = $(`.user_address_not_1`).val().trim();
            let mobile = $('.country-picker-phone-number').val();
            let aadhaarRegex = /^\d{12}$/;
            if (mobile.length < 9) {
                toastr.error('Please Enter a valid Phone Number.');
                return;
            } else if (userName.length < 2) {
                toastr.error('Please Enter Devotee Name.');
                return;
            } else if (aadhaarNumber.length >= 1 && !aadhaarRegex.test(aadhaarNumber)) {
                toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
                return;
            }
            if ("{{ $temple['aadhaar_verify_status'] ?? 0 }}" == 1) {
                toastr.error('Please verify Aadhaar before proceeding.');
                return;
            } else {
                let isDuplicate = UserArray.some((devotee, index) => {
                    return devotee.aadhar == aadhaarNumber;
                });
                if ("{{ $temple['aadhaar_verify_status'] ?? 0 }}" == 1) {
                    if (isDuplicate) {
                        toastr.error('This Aadhaar number already exists.');
                        return;
                    }
                }
                let nextId = 1;
                if (UserArray.length > 0) {
                    nextId = UserArray[UserArray.length - 1].id + 1;
                }
                UserArray.push({
                    "id": nextId,
                    "fullName": userName,
                    "address": userAddress,
                    "aadhar": aadhaarNumber,
                    'phone': mobile,
                    'status': 0,
                });

            }
            appendUserInfo();
            $(`.aadhar_number_not_1`).val('');
            $(`.user_number_not_1`).val('');
            $(`.user_address_not_1`).val('');
            $('.text-without-aadhar').text('More Add');
            $('.text-without-aadhar').css({
                "white-space": 'nowrap',
                "display": "inline-block"
            });
            toastr.success('Devotee added successfully.');
        }


        function appendUserInfo() {
            const container = $('#devotee-container');
            container.empty();

            UserArray.forEach((d, index) => {
                const initial = d.name.charAt(0).toUpperCase();
                container.append(`
            <div class="flex items-center justify-between bg-white border border-gray-200 rounded-2xl p-4 mb-3 shadow-sm hover:shadow-md transition">
    
                    <!-- Left: Initial + Details -->
                    <div class="flex items-center gap-4">
                        
                        <!-- Circle Initial -->
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold text-lg shadow">
                            ${initial}
                        </div>

                        <!-- Devotee Info -->
                        <div>
                            <div class="font-semibold text-gray-900 capitalize text-base">
                                ${d.name}
                            </div>
                            <div class="text-sm text-gray-600 leading-relaxed">
                                ${index === 0 && d.phone ? `<span class="block"> ${d.phone}</span>` : ''}
                                <span class="block"> ${d.aadhar}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Remove Button -->
                    <button 
                        class="text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full p-2 transition-colors remove-devotee" 
                        data-index="${index}" 
                        title="Remove">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            `);
            });
            //  Calculate price
            calculateTotalPrice();
        }

        function calculateTotalPrice() {
            const selectedOption = $('#packageSelect option:selected');
            const basePrice = parseFloat(selectedOption.data('price')) || 0;
            const platformFee = parseFloat(selectedOption.data('platformfee')) || 0;
            const receiptPrice = parseFloat(selectedOption.data('receiptprice')) || 0;
            const pricePerPerson = basePrice + platformFee + receiptPrice;
            const memberCount = UserArray.length;
            const total = pricePerPerson * memberCount;
            if (memberCount > 0 && pricePerPerson > 0) {
                $('#total-price').removeClass('hidden');
                $('#total-members').text(`${memberCount} Member${memberCount > 1 ? 's' : ''}`);
                $('#total-amount').text(`₹${total.toLocaleString('en-IN')}`);
            } else {
                $('#total-price').addClass('hidden');
            }
        }



        //  Recalculate whenever package changes
        $('#packageSelect').on('change', function() {
            calculateTotalPrice();
        });


        $(document).on('click', '.remove-devotee', function() {
            const index = $(this).data('index');
            UserArray.splice(index, 1);
            appendUserInfo();
            if (UserArray.length <= 0) {
                $('.text-without-aadhar').text('Add');
            }
        });



        function checkdarshanlimit() {
            $.ajax({
                url: "{{ url('api/v1/darshan/temple-darshan-booking-limit-check') }}",
                data: {
                    "temple_id": "{{ $temple->id }}",
                    "package": $('#packageSelect').val(),
                    "purohit_id": $('#purohitSelect').val(),
                    "date": $('#hiddenDate').val(),
                    "time": $('#timeSlot').val(),
                    'count': (UserArray.length),
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status == 1) {
                        toastr.success(data.message);
                        const selectedOption = $('#packageSelect option:selected');
                        const basePrice = parseFloat(selectedOption.data('price')) || 0;
                        const receiptPrice = parseFloat(selectedOption.data('receiptprice')) || 0;
                        const platformFee = parseFloat(selectedOption.data('platformfee')) || 0;
                        const platformGst = parseFloat(selectedOption.data('platformgst')) || 0;
                        const platformBasePrice = parseFloat(selectedOption.data('platformbaseprice')) || 0;
                        let manAmounts = basePrice + receiptPrice + platformFee;
                        let totalAmount = (UserArray?.length || 0) * manAmounts;

                        $('#bookingDetails').html(`
                            <div id="bookingDetails" class="mb-4 text-sm text-gray-700">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <!-- Header -->
                                    <div class="text-center border-b border-dashed border-gray-300 pb-3 mb-3">
                                        <h3 class="text-lg font-bold text-gray-900">
                                            ${$('#packageSelect option:selected').text()}
                                        </h3>
                                        <p class="text-gray-600 text-sm">
                                            Date: <span class="font-semibold">{{ date('d-m-y') }}</span> | 
                                            Time: <span class="font-semibold">${$('#timeSlot').val()}</span>
                                        </p>
                                    </div>

                                    <!-- Details -->
                                    <div class="space-y-1 text-sm">
                                        <div class="flex justify-between">
                                            <span>Members</span>
                                            <span class="font-semibold">${UserArray.length}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Package Price</span>
                                            <span>₹${basePrice.toFixed(2)}</span>
                                        </div>
                                      <div class="space-y-1">
                                        <!-- Platform Base -->
                                        <div class="flex justify-between">
                                            <span>Platform Base Price</span>
                                            <span>₹${platformBasePrice.toFixed(2)}</span>
                                        </div>

                                        <!-- Platform GST -->
                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span>Integrated GST (IGST) @ 18%</span>
                                            <span>₹${platformGst.toFixed(2)}</span>
                                        </div>

                                        <!-- Platform Total -->
                                        <div class="flex justify-between font-semibold">
                                            <span>Platform Fee (incl. GST)</span>
                                            <span>₹${platformFee.toFixed(2)}</span>
                                        </div>
                                    </div>

                                        <div class="flex justify-between">
                                            <span>Receipt Fee</span>
                                            <span>₹${receiptPrice.toFixed(2)}</span>
                                        </div>
                                    </div>

                                    <!-- Divider -->
                                    <hr class="my-3 border-dashed">

                                    <!-- Total -->
                                    <div class="flex justify-between items-center text-lg font-bold text-green-700">
                                        <span>Total Amount</span>
                                        <span>₹${totalAmount.toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                        `);
                        // Save data in global variables for later
                        window.bookingPayload = {
                            "people": JSON.stringify(UserArray),
                            "temple_id": "{{ $temple->id }}",
                            "package": $('#packageSelect').val(),
                            "purohit_id": $('#purohitSelect').val(),
                            "date": $('#hiddenDate').val(),
                            "time": $('#timeSlot').val(),
                            "count": UserArray.length,
                            "price": basePrice,
                            "platform_fee": platformFee,
                            "receipt_price": receiptPrice,
                            "platform_gst": platformGst,
                            "platform_base_price": platformBasePrice,
                            "final_amount": totalAmount,
                            "payment_mode": "cash", // important
                            "_token": "{{ csrf_token() }}"
                        };

                        // Show modal
                        $('#paymentModal').removeClass('hidden');

                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    let message = "Something went wrong!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message, 'Error');
                    console.log('XHR:', xhr);
                    console.log('Status:', status);
                    console.log('Error:', error);
                }
            });
        }

        function proceedCash() {
            $.ajax({
                url: "{{ route('vip.darshan.cashsubmit') }}",
                data: window.bookingPayload,
                dataType: "json",
                type: "post",
                success: function(data) {
                    if (data.status == 1) {
                        toastr.success("Cash booking successful!");
                        window.location.href = data.url;
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }

        function proceedOnline() {
            const selectedOption = $('#packageSelect option:selected');
            const basePrice = parseFloat(selectedOption.data('price')) || 0;
            const platformFee = parseFloat(selectedOption.data('platformfee')) || 0;
            const receiptPrice = parseFloat(selectedOption.data('receiptprice')) || 0;
            const platformGst = parseFloat(selectedOption.data('platformgst')) || 0;
            const platformBasePrice = parseFloat(selectedOption.data('platformbaseprice')) || 0;
            let manAmounts = basePrice + platformFee + receiptPrice;
            let totalAmount = UserArray.length * manAmounts;
            $.ajax({
                url: "{{ route('vip.darshan.submit') }}",
                data: {
                    "people": JSON.stringify(UserArray),
                    "temple_id": "{{ $temple->id }}",
                    "package": $('#packageSelect').val(),
                    "purohit_id": $('#purohitSelect').val(),
                    "date": $('#hiddenDate').val(),
                    "time": $('#timeSlot').val(),
                    "count": UserArray.length,
                    "price": basePrice,
                    "platform_fee": platformFee,
                    "receipt_price": receiptPrice,
                    "platform_gst": platformGst,
                    "platform_base_price": platformBasePrice,
                    "final_amount": totalAmount,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status == 1) {
                        toastr.success(data.message);
                        window.location.href = data.url;
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    let message = "Something went wrong!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    toastr.error(message, 'Error');
                    console.log('XHR:', xhr);
                    console.log('Status:', status);
                    console.log('Error:', error);
                }
            });

        }
        const phoneInputField = document.querySelector(".phone-input-with-country-picker");
        const iti = window.intlTelInput(phoneInputField, {
            initialCountry: "in",
            separateDialCode: true,
            preferredCountries: ["in", "us", "gb"],
            utilsScript: "{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/utils.js') }}"
        });
        phoneInputField.addEventListener("input", updateHiddenField);
        phoneInputField.addEventListener("countrychange", updateHiddenField);

        function updateHiddenField() {
            const fullNumber = iti.getNumber();
            document.querySelector(".country-picker-phone-number").value = fullNumber;
        }
    </script>

    <script>
       function setDate(offset, btn) {
    document.querySelectorAll('#date-buttons button').forEach(b => {
        b.classList.remove('bg-orange-600', 'text-white');
        b.classList.add('border', 'border-gray-300', 'text-gray-700');
    });

    btn.classList.add('bg-orange-600', 'text-white');
    btn.classList.remove('border', 'border-gray-300', 'text-gray-700');

    const selectedDate = new Date();
    selectedDate.setDate(selectedDate.getDate() + offset);

    // format d-m-Y
    const day = String(selectedDate.getDate()).padStart(2, '0');
    const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
    const year = selectedDate.getFullYear();
    const formattedDate = `${day}-${month}-${year}`;

    const dateEl = document.getElementById('selectedDate');
    if (dateEl) dateEl.textContent = formattedDate;

    const hiddenInput = document.getElementById('hiddenDate');
    if (hiddenInput) hiddenInput.value = formattedDate;
}

document.addEventListener("DOMContentLoaded", function () {
    const todayBtn = document.querySelector('#date-buttons button');
    if (todayBtn) {
        setDate(0, todayBtn);
    }
});



        function proceedPayment() {
            let mode = document.querySelector('input[name="payment_mode"]:checked').value;
            if (mode === 'cash') {
                proceedCash();
            } else {
                proceedOnline();
            }
        }

        function openPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        // Background click se modal close
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });
    </script>
</body>

</html>