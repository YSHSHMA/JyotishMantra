<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>{{ translate('Temple_Services') }}</title>
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/roboto-font.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/bootstrapnew.min.css') }}"
        rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/puja-single.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.5.1/css/intlTelInput.css">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/temple-service.css') }}">
  
</head>

<body class="bg-gradient-to-b from-orange-400 via-orange-600 to-orange-800 min-h-screen font-sans">
    @php
        $ecommerceLogo = getWebConfig('company_web_logo');
        $verify = $temple->aadhaar_verify_status ?? 0;
        $images = !empty($temple['galleries2']['images']) ? json_decode($temple['galleries2']['images'], true) : [];
        $businessMode = getWebConfig(name: 'business_mode');
     @endphp

    <div class="header mb-5 d-flex justify-content-between align-items-center">
        <!-- Left Logo -->
        <a href="{{ url('/') }}">
            <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                alt="Logo">
        </a>
        <!-- Right Language Selector -->
        <div style="max-width: 950px; width: 100%;">
            <select class="form-select changeLanguageSelect" data-action="{{ route('change-language') }}"  style="padding: 4px 7px; border-radius: 5px;">
                @php
                    $currentLang = session('local');
                @endphp
                @foreach (json_decode($language['value'], true) as $key => $data)
                    @if ($data['status'] == 1)
                        <option value="{{ $data['code'] }}"
                            {{ $currentLang == $data['code'] ? 'selected' : '' }}>
                            {{ $data['name'] }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    {{-- temple info --}}
    <div class="container-fluid slider-temple">
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="row align-items-stretch">
                <div class="col-md-7">
                    @if (count($images) > 0)
                        <div class="slider h-100">
                            <div class="owl-carousel owl-theme h-100">
                                @foreach ($images as $photo)
                                    <div class="item h-100 d-flex align-items-center justify-content-center">
                                        <img src="{{ getValidImage('storage/app/public/temple/gallery/' . $photo, type: 'product') }}"
                                            alt="Temple Image" class="img-fluid rounded-start w-100"
                                            style="object-fit: cover; height: 100%;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-5 d-flex flex-column justify-content-center">
                    <div class="temple-info p-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div class="temple-left">
                                <h4 class="mb-2">
                                    {{ $temple['name'] }}
                                    @if ($verify == 1)
                                        <span class="verified">
                                            <i class="fa-solid fa-circle-check text-success"></i> {{ translate('Verified')}}
                                        </span>
                                    @endif
                                </h4>

                                <p class="mb-2 d-flex align-items-center gap-2">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/date.png') }}"
                                        alt="Opening Hours" style="width:20px;height:20px;">
                                    {{ \Carbon\Carbon::parse($temple['opening_time'])->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($temple['closeing_time'])->format('h:i A') }}
                                </p>

                                <p class="mb-2 d-flex align-items-center gap-2">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/temple.png') }}"
                                        alt="Location" style="width:20px;height:20px;">
                                    {{ $temple['cities']['city'] }},
                                    {{ ucwords(strtolower($temple['states']['name'] ?? '')) }},
                                    {{ $temple['country']['name'] ?? '' }}
                                </p>

                                <p class="mb-0 gap-2">
                                    <i class="fa-solid fa-align-left"></i>
                                    {!! Str::limit(strip_tags($temple['details']), 120, '...') !!}
                                </p>
                            </div>

                            <div class="temple-right text-end mt-3 mt-md-0">
                                <div class="rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= 3)
                                            <i class="fa-solid fa-star text-warning"></i>
                                        @else
                                            <i class="fa-regular fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-1">(3.0 / 5)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">

            <div class="col-12 col-md-12 col-sm-6 mt-2 mb-5">
               
                    @if (isset($templeServices) && !empty($templeServices))
                        <div class="sticky-top bg-white shadow-sm" style="z-index: 1050; top: 75px;">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-customer-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-customer" type="button" role="tab"
                                    data-name="customer" aria-controls="nav-customer" aria-selected="true">{{ translate('Yajman_Info') }}</button>
                                    @foreach ($templeServices as $service)
                                    <button class="nav-link" id="nav-{{ strtolower($service['name']) }}-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-{{ strtolower($service['name']) }}" type="button"
                                    role="tab" data-name="{{ $service['name'] }}"
                                    aria-controls="nav-{{ strtolower($service['name']) }}"
                                    aria-selected="false">{{ ucfirst($service['name']) }}</button>
                                    @endforeach
                                </div>
                            </nav>
                        </div>
                        <div class="customer-section p-4 shadow-sm rounded bg-white mb-5">
                            <div class="tab-content" id="nav-tabContent">
                            
                                <div class="tab-pane fade show active" id="nav-customer" role="tabpanel"
                                    aria-labelledby="nav-customer-tab">
                                    <div class="mb-3 pb-2 border-bottom">
                                        <h4 class="mb-2 text-warning d-flex align-items-center">
                                            <i class="fa-solid fa-user me-2 text-warning"></i>{{ translate('Your_Information') }}
                                        </h4>
                                        <p class="text-muted small mb-3">
                                            {{ translate('Enter_your_details_for_temple_service_booking.') }}
                                        </p>
                                    </div>
                                    <div class="row g-2" id="inputRow">
                                        <div class="col-md-12 d-flex justify-content-between p-2 nriYajman">
                                            <label class="form-label font-semibold d-block">{{ translate('NRI_Yajman') }} ({{ translate('If_you_are_an_NRI') }})                                            </label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="nriSwitch">
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <label class="form-label font-semibold">
                                            {{ translate('phone_number') }}<small class="text-primary">( *
                                            {{ translate('country_code_is_must_like_for_IND') }} 91)</small>
                                            </label>
                                            <input class="form-control phone-input-with-country-picker" type="text"
                                                id="mobile" placeholder="{{ translate('phone_number') }}" required>

                                            <input type="hidden" id="full_phone_number" name="full_phone_number">
                                        </div>
                                        <div class="system-default-country-code" data-value="IN" hidden></div>

                                        <ul id="savedCustomers" class="list-group small overflow-auto"
                                            style="max-height: 300px;">
                                        </ul>

                                        <div id="aadhaar-section" class="col-12 d-flex">
                                            <input type="number" id="aadhaar"
                                                class="form-control {{ $temple->aadhaar_verify_status == 1 ? 'me-2' : '' }}"
                                                placeholder="{{ translate('12_-_digit_Aadhaar') }}" maxlength="12" pattern="\d{12}"
                                                title="Enter 12-digit Aadhaar number"
                                                oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,12)">
                                            <input type="hidden" id="aadhaar-request-id" inputmode="numeric">
                                            @if ($temple->aadhaar_verify_status == 1)
                                                <button type="button" id="sendOtpBtn"
                                                    class="btn btn-primary send-otp-btn">
                                                    {{ translate('Send_OTP') }}
                                                </button>
                                            @endif
                                        </div>

                                        <div id="passport-section" class="col-12 d-none">
                                            <input type="text" id="passport" class="form-control"
                                                placeholder="{{ translate('Enter_Passport_Number') }}" maxlength="15">
                                        </div>

                                        <div class="col-12 mt-2 d-none d-flex" id="otpSection">
                                            <input type="text" id="aadhaar-otp" class="form-control me-2"
                                                maxlength="6" placeholder="{{ translate('Enter_OTP') }}" pattern="\d{6}"
                                                inputmode="numeric">
                                            <button type="button" id="verifyOtpBtn"  class="btn btn-success">{{ translate('Verify') }}</button>
                                        </div>
                                        <div id="aadhaar-no-error" class="small mt-1 text-danger"></div>

                                        <div class="mt-2 {{ $temple->aadhaar_verify_status == 1 ? 'd-none' : '' }}"
                                            id="UserInfoHide">
                                            <div class="col-12 mt-2">
                                                <input type="text" id="name" class="form-control"
                                                    placeholder="{{ translate('Yajman_Name') }}">
                                            </div>

                                            <div class="col-12 text-center mt-2">
                                                <button type="button" id="addBtn" class="btn btn-success w-100">{{ translate('Save') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @foreach ($templeServices as $service)
                                    <div class="tab-pane fade" id="nav-{{ strtolower($service['name']) }}"
                                        role="tabpanel" aria-labelledby="nav-{{ strtolower($service['name']) }}-tab}}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info mb-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ translate('No_temple_services_are_currently_available_._Please_check_back_later.') }}
                        </div>
                    @endif
               
            </div>


        </div>
    </div>

    <input type="hidden" id="templeID" value="{{ $temple['id'] }}">
    <input type="hidden" id="userID">
    <input type="hidden" id="customerCount">
    <input type="hidden" id="darshanCustomerCount">
    <input type="hidden" id="bhojanCustomerCount">

    {{-- footer  --}}
    @if (!empty($temple['package_service']))
        <div class="footer bg-light text-center row" id="footerInfo">
            <div class="row" id="actionButtons">
                <div class="offset-3 col-6 text-center">
                    <button class="btn btn-primary btn-lg" id="saveContinueBtn">{{ translate('Save_&_Next') }}</button>
                </div>
                <div class="col-3 text-end">
                    <button class="btn btn-secondary btn-lg" id="skipBtn">{{ translate('Skip') }}</button>
                </div>
            </div>
            <div class="d-none" id="pay-section">
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                    data-bs-target="#confirmModal">
                    {{ translate('Confirm_Order') }}
                </button>
            </div>

            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel"> {{ translate('Order_Details') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row w-100" id="pay-sectionsss">
                                <div class="col-12 text-center">
                                    <div id="summaryPopup" class="" aria-hidden="true"></div>
                                    {{-- <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <div id="toggleSummaryWrap" style="position:relative;">
                                        <i class="fas fa-info-circle text-primary fs-3 me-2" id="toggleSummary"
                                            style="cursor:pointer;"></i>
                                        <div id="summaryPopup" class="summary-popup d-none" aria-hidden="true"></div>
                                    </div>
            
                                    <span class="fw-bold text-success fs-4" id="totalAmount"></span>
                                </div>
                            </div> --}}
                                </div>
                                <div class="col-12">
                                    @if ($digital_payment['status'] == 1)
                                        @foreach ($payment_gateways_list as $payment_gateway)
                                            <form method="POST" class="digital_payment"
                                                id="{{ $payment_gateway->key_name }}_form"
                                                action="{{ route('temple.paymentRequestTemple') }}">
                                                @csrf
                                                <div class="row">
                                                    {{-- <div class="col-md-4"> --}}
                                                    <input type="hidden" name="payment_method"
                                                        value="{{ $payment_gateway->key_name }}">
                                                    <input type="hidden" name="payment_platform" value="web">
                                                    @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                                        <input type="hidden" name="callback"
                                                            value="{{ $payment_gateway->live_values['callback_url'] }}">
                                                    @elseif($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                                        <input type="hidden" name="callback"
                                                            value="{{ $payment_gateway->test_values['callback_url'] }}">
                                                    @else
                                                        <input type="hidden" name="callback" value="">
                                                    @endif
                                                    <input type="hidden" name="external_redirect_link"
                                                        value="{{ route('temple.templepaymentRequest') }}">
                                                    <label
                                                        class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                                        <input type="radio" id="{{ $payment_gateway->key_name }}"
                                                            name="online_payment"
                                                            class="form-check-input custom-radio"
                                                            value="{{ $payment_gateway->key_name }}" hidden>
                                                        <img width="30"
                                                            src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}"
                                                            alt="" hidden>
                                                        <span class="text-capitalize form-check-label" hidden>
                                                            @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                                                {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                                            @else
                                                                {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                                            @endif
                                                        </span>
                                                    </label>
                                                    <input type="hidden" id="serviceID" name="temple_id"
                                                        value="{{ $temple['id'] }}">
                                                    <input type="hidden" id="customerID" name="customer_id">
                                                    {{-- </div> --}}

                                                    <div class="col-md-8 d-flex justify-content-center">
                                                        <div id="paymentOptions"
                                                            class="payment-options d-none d-flex gap-2 justify-content-around align-items-center">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="payment_mode" id="cashPayment"
                                                                    value="cash" checked>
                                                                <label class="form-check-label fw-medium"
                                                                    for="cashPayment">
                                                                    {{ translate('Cash') }}
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="payment_mode" id="onlinePayment"
                                                                    value="online">
                                                                <label class="form-check-label fw-medium"
                                                                    for="onlinePayment">
                                                                    {{ translate('Online') }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div id="paymentFreeOptions"
                                                            class="d-flex d-none align-items-center">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    value="cash" checked>
                                                                <label class="form-check-label fw-medium"
                                                                    for="freePayment">
                                                                    {{ translate('Free') }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="text-md-end pt-2">
                                                            <button class="btn btn-primary btn-lg px-4" type="submit"
                                                                id="proceedPaymentBtn">
                                                                <i class="fas fa-check-circle me-1"></i> {{ translate('Confirm_Booking') }} 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--<div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> {{ translate('Close') }}</button>
                                <button type="button" class="btn btn-primary">{{ translate('Save_Changes') }}</button>
                            </div> --}}
                    </div>
                </div>
            </div>


        </div>

    @endif
 
    




    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    {{-- <script src="{{ theme_asset(path: 'public/assets/front-end/js/temple-service.js') }}"></script> --}}
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.5.1/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.5.1/js/utils.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>


    {{-- owl carousel --}}
    <script>
        $('.owl-carousel').owlCarousel({
            loop: true,
            autoplay: true,
            autoplayTimeout: 3000,
            dots: false,
            nav: false,
            margin: 15,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                900: {
                    items: 2
                },
                1200: {
                    items: 1
                }
            }
        });
    </script>

    <script>
        let iti = "";
        $(document).ready(function() {
            // country code
            const mobileInput = document.querySelector("#mobile");
            iti = window.intlTelInput(mobileInput, {
                initialCountry: "in",
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.7.0/build/js/utils.js"
            });

            mobileInput.addEventListener('input', function() {
                const number = iti.getNumber();
                $('#full_phone_number').val(number);
            });

            setTimeout(() => {
                document.querySelectorAll('.iti__arrow').forEach(el => {
                    el.style.display = 'none';
                });
            }, 200);


        });
    </script>


    {{-- get service data --}}
    @if (!empty($temple['package_service']))
        <script>
            $(document).ready(function() {
                getServiceDetail();
            });

            function getServiceDetail() {
                const data = localStorage.getItem('templecustomer');
                const customers = JSON.parse(data);
                const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
                const mobile = firstCustomer?.mobile;

                if (!mobile) {
                    loadAllForms();
                    return;
                }

                $.ajax({
                    url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.details && response.details.length >
                            0) {
                            const leadDataArray = response.details;
                            const foundTypes = [];
                            let allLeadDataForSummary = [];

                            $(`#nav-customer-tab`).addClass('bg-success text-white');
                            leadDataArray.forEach(leadData => {
                                const type = leadData.type?.toLowerCase();
                                foundTypes.push(type);
                                const html = buildLeadDisplayHtml(leadData);
                                $(`#nav-${type}`).html(html);
                                $(`#nav-${type}-tab`).addClass('bg-success text-white');
                                allLeadDataForSummary.push(leadData);
                            });

                            // Load form for types not found
                            renderSummaryPopup(allLeadDataForSummary);

                            // const allTypes = ['puja', 'darshan', 'bhojan', 'locker'];
                            const packageService = JSON.parse(@json($temple->package_service));

                            // Extract only the "name" field where status == 1
                            const allTypes = packageService.filter(service => service.status == 1).map(service => service.name);
                            const missingTypes = allTypes.filter(t => !foundTypes.includes(t));
                            missingTypes.forEach(t => appendForm(t));

                        } else {
                            loadAllForms();
                        }
                    },
                    error: function() {
                        toastr.error('Error fetching temple lead data');
                        loadAllForms();
                    }
                });

                // ----- Helper: Load all forms if no data -----
                function loadAllForms() {
                    // ['puja', 'darshan', 'bhojan', 'locker'].forEach(t => appendForm(t));
                    const formPackageServices = JSON.parse(@json($temple->package_service));
                    const formAllType = formPackageServices.filter(service => service.status == 1).map(service => service.name);
                    formAllType.forEach(t => appendForm(t));

                }

                function renderSummaryPopup(leadDataArray) {
                    let totalAmount = 0;
                    let summaryHtml = `
                    <div>
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-clipboard-list me-1"></i> Selected Services
                        </h6>
                        <ul class="list-group small mb-2">
                `;

                    leadDataArray.forEach((l, i) => {
                        const amount = parseFloat(l.amount || 0);
                        totalAmount += amount;
                        summaryHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${i + 1}. ${l.type || '-'}
                            <span class="fw-semibold text-success">₹${amount.toFixed(2)}</span>
                        </li>
                    `;
                    });

                    summaryHtml += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total <span class="fw-semibold text-success">₹${totalAmount.toFixed(2)}</span>
                        </li>
                    </ul></div>`;

                    $('#summaryPopup').html(summaryHtml);
                    // $('#totalAmount').text('₹' + totalAmount.toFixed(2));
                    if (totalAmount > 0) {
                        $('#paymentOptions').removeClass('d-none');
                        $('#paymentFreeOptions').addClass('d-none');
                    } else {
                        $('#paymentOptions').addClass('d-none');
                        $('#paymentFreeOptions').removeClass('d-none');
                    }
                }

                // ----- Helper: Append form -----
                function appendForm(type) {
                    // return false;
                    let formHtml = '';
                    switch (type) {
                        case 'puja':
                            formHtml = `@include('web-views.temple.partials._puja_form')`;
                            break;
                        case 'darshan':
                            formHtml = `@include('web-views.temple.partials._darshan_form')`;
                            break;
                        case 'bhojan':
                            formHtml = `@include('web-views.temple.partials._bhojan_form')`;
                            break;
                        case 'locker':
                            formHtml = `@include('web-views.temple.partials._locker_form')`;
                            break;
                    }
                    $(`#nav-${type}`).html(formHtml);
                }

                // ----- Helper: Build display HTML for lead data -----
                function buildLeadDisplayHtml(leadData) {
                    const templeName = "{{ $temple['name'] ?? 'Temple Name' }}";
                    let customersList = '-';
                    let lockerList = '-';
                    const customerQty = parseInt(leadData.customer_qty || 1);

                    // Parse customers
                    if (leadData.customers) {
                        try {
                            const customersArr = Array.isArray(leadData.customers) ?
                                leadData.customers : JSON.parse(leadData.customers);
                            customersList = customersArr.map(c => c.name || c.mobile).join(', ');
                        } catch (e) {
                            toastr.error('Invalid customers data');
                        }
                    }

                    // Parse locker
                    if (leadData.locker_items) {
                        try {
                            const lockerData = typeof leadData.locker_items === 'object' ?
                                leadData.locker_items : JSON.parse(leadData.locker_items);
                            lockerList = Object.entries(lockerData)
                                .map(([key, val]) => `${key} (${val})`).join(', ');
                        } catch (e) {
                            toastr.error('Invalid locker data');
                        }
                    }

                    const basePrice = (parseFloat(leadData.package?.base_price) || 0) * customerQty;
                    const platformFee = (parseFloat(leadData.package?.platform_fee_percentage) || 0) * customerQty;
                    const receiptFee = (parseFloat(leadData.package?.receipt_fee_percentage) || 0) * customerQty;

                    return `
                <div class="lead-data-view bg-white shadow-md rounded-2xl border border-gray-200 p-4 w-full max-w-md mx-auto">
                    <h2 class="text-center text-xl font-semibold text-purple-700 mb-3 border-b pb-2">${templeName}</h2>
                    <p class="text-center"> ${leadData.package?.varient_name || '-'}</p>
                    <p class="text-center"><strong>Registration No:</strong> ${leadData.order_id || '-'}</p>
                    <div style="display:flex; justify-content:space-between;"><span><strong>Booking Date:</strong></span><span>${leadData.booking_date || '-'}</span></div>
                    <div style="display:flex; justify-content:space-between;"><span><strong>Customers:</strong></span><span>${customersList}</span></div>
                    ${leadData.type?.toLowerCase() === 'locker' ? `<div style="display:flex; justify-content:space-between;">
                                                                                                                                                                            <span><strong>Locker Items:</strong></span><span>${lockerList}</span></div>` : ''}
                    <div style="display:flex; justify-content:space-between;"><span><strong>Service Price:</strong></span><span>₹${basePrice.toFixed(2)}</span></div>
                    ${['puja', 'darshan'].includes(leadData.type?.toLowerCase()) ? 
                        `<div style="display:flex; justify-content:space-between;"><span><strong>GST Rate:</strong></span><span>${leadData.package?.gst_rate || '-'}%</span></div>` : ''}
                    <div style="display:flex; justify-content:space-between;"><span><strong>Platform Fee:</strong></span><span>₹${platformFee.toFixed(2)}</span></div>
                    <div style="display:flex; justify-content:space-between;"><span><strong>Receipt Fee:</strong></span><span>₹${receiptFee.toFixed(2)}</span></div>
                    <div style="display:flex; justify-content:space-between;"><span><strong>Amount:</strong></span><span>₹${leadData.amount || '-'}</span></div>
                    ${['darshan', 'bhojan'].includes(leadData.type?.toLowerCase()) && leadData.timeslot ?
                        `<div style="display:flex; justify-content:space-between;"><span><strong>Time Slot:</strong></span><span>${leadData.timeslot.start_time || '-'} - ${leadData.timeslot.end_time || '-'}</span></div>` : ''}
                    <div class="mt-4 text-center">
                        <div class="inline-block bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                            <strong>Status:</strong> ${leadData.payment_status || 'Pending'}
                        </div>
                    </div>
                </div>`;
                }
            }
        </script>
    @endif

    {{-- skip and save button --}}
    <script>
        $(document).ready(function() {
            const $skipBtn = $('#skipBtn');
            const $saveBtn = $('#saveContinueBtn');
            const $actionButtons = $('#actionButtons');
            const $tabs = $('#nav-tab .nav-link');

            // Helper: move to next tab safely
            function moveToNextTab(currentIndex) {
                const nextIndex = currentIndex + 1;
                if (nextIndex < $tabs.length) {
                    const $nextTab = $tabs.eq(nextIndex);
                    const nextTabId = $nextTab.attr('id');
                    const nextTabType = $nextTab.data('name'); // ✅ get data-name

                    // Existing load logic
                    // if (nextTabId === 'nav-darshan-tab') {
                    //     loadTimeSlotsGeneric('darshan');
                    //     showDarshanSavedCustomers();
                    // } else if (nextTabId === 'nav-bhojan-tab') {
                    //     loadTimeSlotsGeneric('bhojan');
                    //     showBhojanSavedCustomers();
                    // }

                    const tabTrigger = new bootstrap.Tab($nextTab[0]);
                    tabTrigger.show();

                    // ✅ Run the AJAX type check
                    checkTypeExists(nextTabType);

                    if (nextIndex === $tabs.length - 1) {
                        // Handle last tab (payment)
                    } else {
                        $('#actionButtons').removeClass('d-none');
                    }
                } else {
                    checkServicesTaken().then(servicesTaken => {
                        if (servicesTaken) {
                            $('#actionButtons').addClass('d-none');
                            $('#pay-section').removeClass('d-none');
                            // $('#proceedPaymentBtn').removeClass('d-none');
                        } else {
                            toastr.error('You need to choose at least one service');
                        }
                    });
                }
            }


            // Main tab handler
            function goToNextTab(isSaveAction = false) {
                const $active = $tabs.filter('.active');
                const currentIndex = $tabs.index($active);
                const activeTabId = $active.attr('id');

                // If SAVE clicked → validate + save before switching tab
                if (isSaveAction) {
                    let savePromise;

                    switch (activeTabId) {
                        case 'nav-customer-tab':
                            savePromise = customerSaveCheck();
                            break;
                        case 'nav-puja-tab':
                            savePromise = poojaSave();
                            break;
                        case 'nav-darshan-tab':
                            savePromise = darshanSave();
                            break;
                        case 'nav-locker-tab':
                            console.log('lock run');
                            savePromise = lockerSave();
                            break;
                        case 'nav-bhojan-tab':
                            savePromise = bhojanSave();
                            break;
                        default:
                            savePromise = Promise.resolve(true);
                    }

                    // ✅ Only move when data is saved successfully
                    savePromise
                        .then((res) => {
                            if (res) {
                                console.log('response');
                                moveToNextTab(currentIndex);
                                console.log('moved');
                            } else {
                                console.warn('Save failed or returned false');
                            }
                        })
                        .catch(() => {
                            console.warn('Save failed — staying on current tab');
                        });

                } else {
                    // If SKIP clicked → move directly
                    console.log('move to next');
                    moveToNextTab(currentIndex);
                }
            }

            // Toggle skip visibility and button alignment
            function toggleSkipButton() {
                const firstTabActive = $('#nav-customer-tab').hasClass('active');
                if (firstTabActive) {
                    $skipBtn.addClass('d-none');
                    // $actionButtons.removeClass('justify-content-between').addClass('justify-content-center');
                } else {
                    $skipBtn.removeClass('d-none');
                    // $actionButtons.removeClass('justify-content-center').addClass('justify-content-between');
                }
            }

            // Disable manual tab clicks
            $tabs.on('click', function(e) {
                e.preventDefault();
                return false;
            });

            $tabs.on('shown.bs.tab', function(e) {
                toggleSkipButton();

                // ✅ Hide pay section when switching tabs
                if (!$('#pay-section').hasClass('d-none')) {
                    $('#pay-section').addClass('d-none');
                    $('#actionButtons').removeClass('d-none');
                }

                // ✅ Check type for this tab
                const currentTab = $(e.target);
                const typeName = currentTab.data('name');
                checkTypeExists(typeName);
            });

            toggleSkipButton();

            // Button actions
            $skipBtn.on('click', function(e) {
                e.preventDefault();
                console.log('skip btn');
                goToNextTab(false);
            });

            $saveBtn.on('click', async function(e) {
                e.preventDefault();
                console.log('save btn');
                const $active = $tabs.filter('.active');
                const activeTabId = $active.attr('id');

                try {
                    console.log(activeTabId);
                    if (activeTabId === 'nav-customer-tab') {
                        await customerSaveCheck();
                        $('#' + activeTabId).addClass('bg-success text-white');
                    } else if (activeTabId === 'nav-puja-tab') {
                        await poojaSave();
                        $('#' + activeTabId).addClass('bg-success text-white');
                    } else if (activeTabId === 'nav-darshan-tab') {
                        await darshanSave(); // ← will display darshan data then move next
                        $('#' + activeTabId).addClass('bg-success text-white');
                    } else if (activeTabId === 'nav-locker-tab') {
                        await lockerSave();
                        $('#' + activeTabId).addClass('bg-success text-white');
                    } else if (activeTabId === 'nav-bhojan-tab') {
                        await bhojanSave();
                        $('#' + activeTabId).addClass('bg-success text-white');
                    }

                    // Move to next tab only when save + display complete
                    goToNextTab(false);

                } catch (err) {
                    console.warn("Save failed or validation error – staying on current tab.");
                }
            });


            // Toggle summary visibility on icon click
            // $('#toggleSummary').on('click', function(e) {
            //     e.stopPropagation(); // prevent the document click close handler from immediately hiding it
            //     $('#summaryPopup').toggleClass('d-none');
            // });

            // 🔸 Function to check type existence from server
            function checkTypeExists(typeName) {
                const data = localStorage.getItem('templecustomer');
                const customers = JSON.parse(data);
                const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
                const mobile = firstCustomer?.mobile;

                if (!mobile) {
                    console.warn("No mobile found in localStorage");
                    return;
                }

                $.ajax({
                    url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.details && response.details
                            .length > 0) {
                            const leadDataArray = response.details;
                            const found = leadDataArray.some(item => item.type === typeName);

                            if (found) {
                                // Type found → hide Save, show Skip
                                $('#saveContinueBtn').addClass('d-none');
                                $('#skipBtn').removeClass('d-none');
                            } else {
                                // Type not found → show both
                                $('#saveContinueBtn').removeClass('d-none');
                                $('#skipBtn').removeClass('d-none');
                            }
                        } else {
                            // No details found → show both
                            $('#saveContinueBtn').removeClass('d-none');
                            $('#skipBtn').removeClass('d-none');
                        }
                    },
                    error: function() {
                        toastr.error('Error fetching temple lead data');
                    }
                });
            }

        });
    </script>

    <!--customer-->
    <script>
        $(document).ready(function() {
            readyData();
        });

        const aadhaarVerifyStatus = {{ $verify }};
        let aadhaarVerified = aadhaarVerifyStatus == 0 ? true : false;

        // nri check
        $('#nriSwitch').on('change', function() {
            if ($(this).is(':checked')) {
                $('#aadhaar').val('');
                $('#aadhaar-request-id').val('');
                $('#aadhaar-section').addClass('d-none');
                $('#passport-section').removeClass('d-none');
                $('#UserInfoHide').removeClass('d-none');
                $('#addBtn').text('Save & Add More');
            } else {
                $('#passport').val('');
                $('#passport-section').addClass('d-none');
                $('#sendOtpBtn').removeClass('d-none');
                $('#aadhaar').prop('disabled', false).addClass('me-2');
                $('#aadhaar-section').removeClass('d-none');
                if (aadhaarVerifyStatus == 1) {
                    $('#UserInfoHide').addClass('d-none');
                }
            }
        });

        // ---- SEND OTP ----
        $(document).on('click', '#sendOtpBtn', function() {
            const aadhaarNumber = $('#aadhaar').val().trim();

            if (aadhaarNumber.length !== 12) {
                toastr.error('Please enter a valid 12-digit Aadhaar number!');
                return;
            }

            $.ajax({
                url: "{{ url('api/v1/darshan/aadhar-send-otp') }}",
                type: "POST",
                data: {
                    aadhaar_number: aadhaarNumber,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status == 1) {
                        $('#aadhaar-request-id').val(data.request_id);
                        $('#otpSection').removeClass('d-none');
                        toastr.success(
                            'OTP sent successfully to your registered mobile number.');
                    } else if (data.status == 2) {
                        // Aadhaar already verified
                        $('#aadhaar-request-id').val('');
                        aadhaarVerified = true;
                        $('#otpSection').addClass('d-none');
                        $('#sendOtpBtn').addClass('d-none');
                        $('#aadhaar').prop('disabled', true).removeClass('me-2');
                        $('#UserInfoHide').removeClass('d-none');
                        $('#name').val(data.data.name);
                        $('#addBtn').text('Save & Add More');
                        toastr.success('This Aadhaar is already verified.');

                    } else {
                        $('#aadhaar-request-id').val('');
                        $('#otpSection').addClass('d-none');
                        alert(data.message);
                    }
                },
                error: function() {
                    toastr.success('Error sending OTP. Please try again.');
                }
            });
        });

        // ---- VERIFY OTP ----
        $('#verifyOtpBtn').on('click', function() {
            const aadhaarOtp = $('#aadhaar-otp').val().trim();
            const aadhaarRequestId = $('#aadhaar-request-id').val();

            $('#aadhaar-no-error').text('');

            if (aadhaarOtp.length !== 6) {
                $('#aadhaar-no-error').addClass('text-danger').text('Aadhaar OTP must be 6 digits');
                return;
            }

            $.ajax({
                url: "{{ url('api/v1/darshan/aadhar-otp-verify') }}",
                type: "POST",
                data: {
                    otp: aadhaarOtp,
                    request_id: aadhaarRequestId,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status == 1) {
                        aadhaarVerified = true;
                        $('#otpSection').addClass('d-none');
                        $('#UserInfoHide').removeClass('d-none');
                        $('#name').val(data.data1.name);
                        // $('#mobile').val(data.data1.phone);
                        $('#sendOtpBtn').addClass('d-none');
                        $('#aadhaar').prop('disabled', true).removeClass('me-2');
                        toastr.success('Aadhaar verified successfully.');
                        $('#addBtn').text('Save & Add More');
                    } else {
                        $('#aadhaar-no-error').addClass('text-danger').text(data.message);
                    }
                },
                error: function() {
                    $('#aadhaar-no-error').addClass('text-danger').text(
                        'Error verifying OTP.');
                }
            });
        });

        // ---- ADD CUSTOMER ----
        // $(document).ready(function() {
        $(document).on('click', '#addBtn', function() {
            // $('#addBtn').click(function() {
            const isNRI = $('#nriSwitch').is(':checked');
            const mobile = iti.getNumber();
            const name = $('#name').val().trim();
            let aadhaar = $('#aadhaar').val().trim();
            const passport = $('#passport').val().trim();
            let customerData = {};

            if (!mobile) {
                toastr.error('Please fill valid mobile no.');
                return;
            }

            if (!isNRI) {
                if (aadhaarVerifyStatus == 1) {
                    if (aadhaar.length !== 12) {
                        toastr.error('Please fill valid Aadhaar details.');
                        return;
                    }

                    if (!aadhaarVerified) {
                        toastr.error('Please verify Aadhaar before adding.');
                        return;
                    }
                } else {
                    if (aadhaar.length <= 0) {
                        aadhaar = "000000000000";
                    }
                }
            } else {
                if (passport.length === 0) {
                    toastr.error('Please fill valid passport details.');
                    return;
                }
            }

            console.log(aadhaarVerifyStatus);
            console.log(aadhaarVerified);
            console.log(aadhaar);
            console.log(aadhaar.length);

            if (!name) {
                toastr.error('Please fill valid name.');
                return;
            }

            let customers = JSON.parse(localStorage.getItem('templecustomer')) || [];
            if (aadhaar && aadhaar !== "000000000000") {
                const exists = customers.some(c => c.aadhaar && c.aadhaar === aadhaar);
                if (exists) {
                    toastr.error('This Aadhaar number is already added.');
                    $("#nriSwitch").prop("checked", false).trigger("change");
                    return;
                }
            }

            // Check Passport duplication
            if (passport) {
                const exists = customers.some(c => c.passport && c.passport === passport);
                if (exists) {
                    toastr.error('This Passport number is already added.');
                    $("#nriSwitch").prop("checked", false).trigger("change");
                    return;
                }
            }

            if (customers.length === 0) {
                $.ajax({
                    url: '{{ route('temple.customer.checkOrCreate') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer: {
                            name,
                            mobile,
                        }
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#userID').val(res.user.id);
                            $('#customerID').val(res.user.id);

                            customerData.name = name;
                            customerData.mobile = mobile;
                            if (isNRI) {
                                customerData.passport = passport;
                            } else {
                                customerData.aadhaar = aadhaar;
                            }

                            customers.push(customerData);

                            saveAndShow(customers);
                            // getServiceData();
                            toastr.success('Yajman added successfully!');
                        } else {
                            toastr.error('Error saving yajman.');
                        }
                    },
                    error: function() {
                        toastr.error('Server error while saving yajman.');
                    }
                });
            } else {
                customerData.name = name;
                customerData.mobile = mobile;
                if (isNRI) {
                    customerData.passport = passport;
                } else {
                    customerData.aadhaar = aadhaar;
                }

                customers.push(customerData);
                saveAndShow(customers);
                toastr.success('Yajman added successfully!');
            }
            $('#addBtn').text('Save & Add More');
            $('#aadhaar').prop('disabled', false).addClass('me-2');
            $('#sendOtpBtn').removeClass('d-none');
            // iti.setNumber("");
            $("#nriSwitch").prop("checked", false).trigger("change");
        });
        // });

        function saveAndShow(customers) {
            console.log(customers);
            localStorage.setItem('templecustomer', JSON.stringify(customers));
            showSavedCustomers();
            showPujaSavedCustomers();
            loadTimeSlotsGeneric('darshan');
            showDarshanSavedCustomers();
            loadTimeSlotsGeneric('bhojan');
            showBhojanSavedCustomers();
            clearInputs();
            // toastr.success('Yajman added successfully!');
        }

        let mobileInitialized = false;

        function showSavedCustomers() {
            const saved = JSON.parse(localStorage.getItem('templecustomer')) || [];
            const list = $('#savedCustomers');
            list.empty();

            if (saved.length === 0) {
                return;
            }

            const firstCustomer = saved[0];

            // ✅ Run this block only once
            if (!mobileInitialized && firstCustomer.mobile) {
                mobileInitialized = true; // mark as done

                const mobileInput = document.querySelector("#mobile");
                const iti = window.intlTelInputGlobals.getInstance(mobileInput);
                let cleanNumber = firstCustomer.mobile.replace(/^\+91|^0+/, '');
                mobileInput.value = cleanNumber;
                iti.setCountry('in');
                mobileInput.disabled = true;
                // $("#mobile").prop('disabled',true);

                const flagContainer = document.querySelector(".iti__flag-container");
                if (flagContainer) {
                    flagContainer.style.pointerEvents = "none";
                    flagContainer.style.opacity = "0.6";
                }
            }

            // Display saved customers
            $.each(saved, function(i, c) {
                let idInfo = "";

                if (c.aadhaar && c.aadhaar !== "000000000000") {
                    idInfo = `<small>Aadhaar: ${c.aadhaar}</small>`;
                } else if (c.passport) {
                    idInfo = `<small>Passport: ${c.passport}</small>`;
                }

                // Add bar only if idInfo exists
                const idDisplay = idInfo ? ` | ${idInfo}` : "";

                list.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${i + 1}) </strong>
                            <strong>${c.name}</strong><br>
                            <small>Mobile: ${c.mobile}</small>${idDisplay}
                        </div>
                        ${i === 0 ? '' : `<button class="btn btn-sm btn-outline-danger deleteBtn" data-index="${i}">×</button>`}
                    </li>
                `);
            });
        }

        // ---- SHOW PUJA SAVED CUSTOMERS ----
        function showPujaSavedCustomers() {
            const saved = JSON.parse(localStorage.getItem('templecustomer')) || [];
            const list = $('#pujaSavedCustomers');
            list.empty();

            if (saved.length === 0) {
                list.append('<li class="list-group-item text-muted">No customers added yet.</li>');
                return;
            }

            $.each(saved, function(i, c) {
                let idInfo = "";

                if (c.aadhaar && c.aadhaar !== "000000000000") {
                    idInfo = `<small>Aadhaar: ${c.aadhaar}</small>`;
                } else if (c.passport) {
                    idInfo = `<small>Passport: ${c.passport}</small>`;
                }

                // Add bar only if idInfo exists
                const idDisplay = idInfo ? ` | ${idInfo}` : "";

                list.append(`
                   
                        <div class="col-md-4 col-sm-6">
                            <div class="card p-2 shadow-sm h-100">
                                <div class="form-check">
                                    <input class="form-check-input pujaCustomerCheckbox" type="checkbox" value="${i}" id="pujaCustomer${i}" checked>
                                    <label class="form-check-label" for="pujaCustomer${i}">
                                        <strong>Name: ${c.name}</strong><br>
                                        Mobile: ${c.mobile}<br>
                                        ${idDisplay}
                                    </label>
                                </div>
                            </div>
                        </div>
                   
                `);
            });
            calculatePujaTotal();
        }

        // ---- SHOW DARSHAN SAVED CUSTOMERS
        function showDarshanSavedCustomers() {
            const saved = JSON.parse(localStorage.getItem('templecustomer')) || [];
            const list = $('#darshanSavedCustomers');
            list.empty();

            if (saved.length === 0) {
                list.append('<li class="list-group-item text-muted">No customers added yet.</li>');
                return;
            }

            $.each(saved, function(i, c) {
                let idInfo = "";

                if (c.aadhaar && c.aadhaar !== "000000000000") {
                    idInfo = `<small>Aadhaar: ${c.aadhaar}</small>`;
                } else if (c.passport) {
                    idInfo = `<small>Passport: ${c.passport}</small>`;
                }

                // Add bar only if idInfo exists
                const idDisplay = idInfo ? ` | ${idInfo}` : "";

                const checkboxId = `darshanCustomer${i}`;
                list.append(`
                    
                        <div class="col-md-4 col-sm-6">
                            <div class="card p-2 shadow-sm h-100">
                                <div class="form-check">
                                    <input class="form-check-input darshanCustomerCheckbox" type="checkbox" value="${i}" id="${checkboxId}">
                                    <label class="form-check-label" for="${checkboxId}">
                                        <strong>Name: ${c.name}</strong><br>
                                        Mobile: ${c.mobile}<br>
                                        ${idDisplay}
                                    </label>
                                </div>
                            </div>
                        </div>
                    
                `);
            });
            calculateDarshanTotal();
        }

        // ---- SHOW BHOJAN SAVED CUSTOMERS
        function showBhojanSavedCustomers() {
            const saved = JSON.parse(localStorage.getItem('templecustomer')) || [];
            const list = $('#bhojanSavedCustomers');
            list.empty();

            if (saved.length === 0) {
                list.append('<li class="list-group-item text-muted">No customers added yet.</li>');
                return;
            }

            $.each(saved, function(i, c) {
                let idInfo = "";

                if (c.aadhaar && c.aadhaar !== "000000000000") {
                    idInfo = `<small>Aadhaar: ${c.aadhaar}</small>`;
                } else if (c.passport) {
                    idInfo = `<small>Passport: ${c.passport}</small>`;
                }

                // Add bar only if idInfo exists
                const idDisplay = idInfo ? ` | ${idInfo}` : "";

                const checkboxId = `bhojanCustomer${i}`;
                list.append(`
                    
                        <div class="col-md-4 col-sm-6">
                            <div class="card p-2 shadow-sm h-100">
                                <div class="form-check">
                                    <input class="form-check-input bhojanCustomerCheckbox" type="checkbox" value="${i}" id="${checkboxId}">
                                    <label class="form-check-label" for="${checkboxId}">
                                        <strong>Name: ${c.name}</strong><br>
                                        Mobile: ${c.mobile}<br>
                                        ${idDisplay}
                                    </label>
                                </div>
                            </div>
                        </div>
                    
                `);
            });
            calculateBhojanTotal();
        }

        // ---- DELETE CUSTOMER ----
        $(document).on('click', '.deleteBtn', function() {
            const index = $(this).data('index');
            let customers = JSON.parse(localStorage.getItem('templecustomer')) || [];
            customers.splice(index, 1);
            localStorage.setItem('templecustomer', JSON.stringify(customers));
            showSavedCustomers();
        });

        // ---- CLEAR INPUTS ----
        function clearInputs() {
            $('#name, #aadhaar, #aadhaar-otp, #passport').val('');
            $('#aadhaar-request-id').val('');
            if (aadhaarVerifyStatus == 1) {
                aadhaarVerified = false;
                $('#otpSection').addClass('d-none');
                $('#sendOtpBtn')
                    .text('Send OTP')
                    .prop('disabled', false)
                    .removeClass('btn-success')
                    .addClass('btn-outline-primary');
            }
        }

        setTimeout(() => {
            showPujaSavedCustomers();
            loadTimeSlotsGeneric('darshan');
            showDarshanSavedCustomers();
            loadTimeSlotsGeneric('bhojan');
            showBhojanSavedCustomers();
        }, 7000);

        function readyData() {
            showSavedCustomers();
            getCustomerData();
        }
    </script>
    <script>
        function getCustomerData() {
            const data = localStorage.getItem('templecustomer');
            if (!data) {
                return;
            }
            const customers = JSON.parse(data);
            const firstCustomer = Array.isArray(customers) ? customers[0] : customers;

            if (!firstCustomer || !firstCustomer.mobile) {
                return;
            }

            const mobile = firstCustomer.mobile;
            $.ajax({
                url: "{{ url('account-service-order-user-name') }}/" + mobile,
                type: "GET",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 200) {
                        $('#userID').val(response.user.id);
                        $('#customerID').val(response.user.id);
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr.responseText);
                }
            });

        }
    </script>

    <script>
        // 🔹 Generic function to load slots for both darshan & bhojan
        function loadTimeSlotsGeneric(section) {
            const $section = $('#' + section + '-section');
            const $package = $section.find('.' + section + '-package-select');
            const $date = $section.find('.' + section + '-date-select');
            const $slotContainer = $section.find('.' + section + '-timeslot-container');
            const $hiddenInput = $section.find('.' + section + '-timeslot-id');

            const packageId = $package.val();
            const dateVal = $date.val();

            $slotContainer.html('<div class="text-muted small">Loading...</div>');

            if (!packageId || !dateVal) {
                $slotContainer.html('<div class="text-muted small">Select package and date first</div>');
                return;
            }

            const dayName = new Date(dateVal).toLocaleDateString('en-US', {
                weekday: 'long'
            });

            $.ajax({
                url: '{{ url('temple/package-timeslots') }}/' + packageId,
                method: 'GET',
                data: {
                    day: dayName,
                    date: dateVal,
                    type: section
                },
                dataType: 'json',
                success: function(data) {
                    $slotContainer.empty();

                    if (!data.length) {
                        $slotContainer.html('<div class="text-danger small">No slots available</div>');
                        return;
                    }

                    const today = new Date().toISOString().split('T')[0];
                    const now = new Date();

                    $.each(data, function(i, slot) {
                        const slotTime = new Date(`${dateVal} ${slot.time}`);
                        const isPast = (dateVal === today && slotTime <= now);
                        if (isPast) return;

                        const btn = $('<button>')
                            .addClass('btn btn-outline-primary btn-sm m-1 time-slot-btn')
                            .attr('data-id', slot.id)
                            .attr('data-available', slot.available)
                            .text(slot.time + ' (Available: ' + slot.available + ')');

                        $slotContainer.append(btn);
                    });

                    if ($slotContainer.children().length === 0) {
                        $slotContainer.html(
                            '<div class="text-muted small">No future slots available today</div>');
                    }
                },
                error: function() {
                    $slotContainer.html('<div class="text-danger small">Failed to fetch slots</div>');
                }
            });
        }

        // 🧭 Handle slot selection
        $(document).on('click', '.time-slot-btn', function() {
            const $btn = $(this);
            const $container = $btn.closest('[id$="-section"]');
            const section = $container.attr('id').replace('-section', '');

            $container.find('.time-slot-btn')
                .removeClass('active btn-primary')
                .addClass('btn-outline-primary');
            $btn.removeClass('btn-outline-primary').addClass('btn-primary active');

            const slotId = $btn.data('id');
            const available = parseInt($btn.data('available')) || 0;

            $container.find('input[type="hidden"].' + section + '-timeslot-id').val(slotId);
            $container.data('available', available);

            // ✅ Clear previous customer selection if slot changes
            $container.find('.' + section + 'CustomerCheckbox').prop('checked', false);

            toastr.success('Slot selected. Available slots: ' + available);
        });

        // 🧮 Shared function for validating customer selection
        function validateCustomerSelection($checkbox, section) {
            const $container = $('#' + section + '-section');
            const $slotBtn = $container.find('.time-slot-btn.active');

            if (!$slotBtn.length) {
                $checkbox.prop('checked', false);
                toastr.error('Please select a time slot first.');
                return false;
            }

            const available = parseInt($slotBtn.data('available')) || 0;
            const checkedCount = $container.find('.' + section + 'CustomerCheckbox:checked').length;

            if (checkedCount > available) {
                $checkbox.prop('checked', false);
                toastr.error('You cannot select more customers than available slots (' + available + ').');
                return false;
            }

            return true;
        }

        // ✅ Validation for Darshan customers
        $(document).on('change', '.darshanCustomerCheckbox', function() {
            validateCustomerSelection($(this), 'darshan');
        });

        // ✅ Validation for Bhojan customers
        $(document).on('change', '.bhojanCustomerCheckbox', function() {
            validateCustomerSelection($(this), 'bhojan');
        });

        // 🔄 Auto-load slots on package or date change for both types
        $(document).on('change',
            '.darshan-package-select, .darshan-date-select, .bhojan-package-select, .bhojan-date-select',
            function() {
                const className = $(this).attr('class');
                let section = null;
                if (className.includes('darshan')) section = 'darshan';
                else if (className.includes('bhojan')) section = 'bhojan';
                if (section) loadTimeSlotsGeneric(section);
            });
    </script>



    <script>
        function calculatePujaTotal() {
            const selectedPackage = $('.puja-package-select').find(':selected');

            if (!selectedPackage.val()) {
                $('#pujaTotal').html('0');
                $('#pujaPriceBreakdown').html('');
                return;
            }

            const basePrice = parseFloat(selectedPackage.data('price')) || 0;
            const gst = parseFloat(selectedPackage.data('gst')) || 0;
            const platform = parseFloat(selectedPackage.data('platform')) || 0;
            const reception = parseFloat(selectedPackage.data('reception')) || 0;

            const selectedCustomers = $('.pujaCustomerCheckbox:checked');
            const customerCount = selectedCustomers.length;

            if (customerCount === 0) {
                $('#pujaTotal').html('0');
                $('#pujaPriceBreakdown').html('');
                return;
            }

            // Base + Platform + Receipt per customer
            const baseWithExtras = basePrice;
            const gstAmount = (baseWithExtras * gst) / 100;
            const pricePerCustomer = baseWithExtras + gstAmount + platform + reception;
            const total = pricePerCustomer * customerCount;
            $('#pujaTotal').html(total.toFixed(2));
            let footerTotal = parseFloat($('#totalPaymentAmount').html()) || 0;
            $('#totalPaymentAmount').html((footerTotal + total).toFixed(2));

            const breakdownHtml = `
                <ul class="list-unstyled mb-0">
                    <li>Base Price (per customer): ₹${basePrice}</li>
                    <li>Platform Fee (per customer): ₹${platform}</li>
                    <li>Receipt Fee (per customer): ₹${reception}</li>
                    <li>GST (${gst}%): ₹${gstAmount.toFixed(2)} per customer</li>
                    <li><strong>Price per Customer: ₹${pricePerCustomer.toFixed(2)}</strong></li>
                    <li><strong>Total Customers: ${customerCount}</strong></li>
                </ul>
            `;
            $('#pujaPriceBreakdown').html(breakdownHtml);
            $('#customerCount').val(customerCount);

        }

        $(document).on('change', '.pujaCustomerCheckbox, .puja-package-select', function() {
            calculatePujaTotal();
        });

        // Initial calculation
        calculatePujaTotal();

        // DARSHAN TOTAL CALCULATION
        function calculateDarshanTotal() {
            const selectedPackage = $('.darshan-package-select').find(':selected');

            if (!selectedPackage.val()) {
                $('#darshanTotal').html('0');
                $('#darshanPriceBreakdown').html('');
                return;
            }

            const basePrice = parseFloat(selectedPackage.data('price')) || 0;
            const gst = parseFloat(selectedPackage.data('gst')) || 0;
            const platform = parseFloat(selectedPackage.data('platform')) || 0;
            const reception = parseFloat(selectedPackage.data('reception')) || 0;

            // Count selected customers
            const selectedCustomers = $('.darshanCustomerCheckbox:checked');
            const customerCount = selectedCustomers.length;

            if (customerCount === 0) {
                $('#darshanTotal').html('0');
                $('#darshanPriceBreakdown').html('');
                return;
            }

            // Base + Platform + Receipt per customer
            const baseWithExtras = basePrice;
            const gstAmount = (baseWithExtras * gst) / 100;
            const pricePerCustomer = baseWithExtras + gstAmount + platform + reception;
            const total = pricePerCustomer * customerCount;

            $('#darshanTotal').html(total.toFixed(2));
            let footerTotal = parseFloat($('#totalPaymentAmount').html()) || 0;
            $('#totalPaymentAmount').html((pricePerCustomer + footerTotal).toFixed(2));
            const breakdownHtml = `
            <ul class="list-unstyled mb-0">
                        <li>Base Price (per customer): ₹${basePrice}</li>
                        <li>Platform Fee (per customer): ₹${platform}</li>
                        <li>Receipt Fee (per customer): ₹${reception}</li>
                        <li>GST (${gst}%): ₹${gstAmount.toFixed(2)} per customer</li>
                        <li><strong>Price per Customer: ₹${pricePerCustomer.toFixed(2)}</strong></li>
                        <li><strong>Total Customers: ${customerCount}</strong></li>
                    </ul>
            `;
            $('#darshanPriceBreakdown').html(breakdownHtml);
            $('#darshanCustomerCount').val(customerCount);
        }

        $(document).on('change', '.darshanCustomerCheckbox, .darshan-package-select', function() {
            calculateDarshanTotal();
        });

        calculateDarshanTotal();

        // BOJAN CALCULATION
        function calculateBhojanTotal() {
            const selectedPackage = $('.bhojan-package-select').find(':selected');

            if (!selectedPackage.val()) {
                $('#bhojanTotal').html('0');
                $('#bhojanPriceBreakdown').html('');
                return;
            }

            const basePrice = parseFloat(selectedPackage.data('price')) || 0;
            // const gst = parseFloat(selectedPackage.data('gst')) || 0;
            const platform = parseFloat(selectedPackage.data('platform')) || 0;
            const reception = parseFloat(selectedPackage.data('reception')) || 0;

            // Count selected customers
            const selectedCustomers = $('.bhojanCustomerCheckbox:checked');
            const customerCount = selectedCustomers.length;

            if (customerCount === 0) {
                $('#bhojanTotal').html('0');
                $('#bhojanPriceBreakdown').html('');
                return;
            }

            // Base + Platform + Receipt per customer
            const baseWithExtras = basePrice + platform + reception;
            // const gstAmount = (baseWithExtras * gst) / 100;
            const pricePerCustomer = baseWithExtras;
            const total = pricePerCustomer * customerCount;

            $('#bhojanTotal').html(total.toFixed(2));
            let footerTotal = parseFloat($('#totalPaymentAmount').html()) || 0;
            $('#totalPaymentAmount').html((pricePerCustomer + footerTotal).toFixed(2));
            const breakdownHtml = `
            <ul class="list-unstyled mb-0">
                        <li>Base Price (per customer): ₹${basePrice}</li>
                        <li>Platform Fee (per customer): ₹${platform}</li>
                        <li>Receipt Fee (per customer): ₹${reception}</li>
                        <li><strong>Price per Customer: ₹${pricePerCustomer.toFixed(2)}</strong></li>
                        <li><strong>Total Customers: ${customerCount}</strong></li>
                    </ul>
            `;
            $('#bhojanPriceBreakdown').html(breakdownHtml);
            $('#bhojanCustomerCount').val(customerCount);
        }

        $(document).on('change', '.bhojanCustomerCheckbox, .bhojan-package-select', function() {
            calculateBhojanTotal();
        });

        calculateBhojanTotal();
    </script>

    <script>
        function customerSaveCheck() {
            return new Promise((resolve, reject) => {
                const storedCustomers = JSON.parse(localStorage.getItem('templecustomer')) || [];

                if (storedCustomers.length === 0) {
                    toastr.error('Please add at least one customer to proceed.');
                    reject(false); // ❌ No customers, stay on tab
                } else {
                    resolve(true); // ✅ Customers exist, move to next tab
                }
            });
        }
    </script>

    <script>
        // $(document).on('click', '#pooja-save', function(e) {
        //     e.preventDefault()
        function poojaSave() {
            return new Promise((resolve, reject) => {
                let date = $('input[name="puja_date"]').val();
                let packageId = $('.puja-package-select').val();
                let customers = [];
                let templeid = $('#templeID').val();
                let userid = $('#userID').val();
                let panditId = $('#pandit-id').val();
                let customerCount = $('#customerCount').val();

                $('.pujaCustomerCheckbox:checked').each(function(index) {
                    let label = $(this).siblings('label').text().trim();
                    let nameMatch = label.match(/Name:\s*(.*)/);
                    let mobileMatch = label.match(/Mobile:\s*([\+\d]+)/);
                    let aadharMatch = label.match(/Aadhaar:\s*([0-9]+)/);
                    let passportMatch = label.match(/Passport:\s*([A-Za-z0-9]+)/);

                    let name = nameMatch ? nameMatch[1].trim() : '';
                    let mobile = mobileMatch ? mobileMatch[1] : '';
                    let idValue = '';
                    if (aadharMatch) {
                        idValue = aadharMatch[1];
                    } else if (passportMatch) {
                        idValue = passportMatch[1];
                    } else {
                        idValue = "000000000000";
                    }

                    let serial = (index + 1).toString().padStart(2, '0');
                    name = `${name} (${serial})`;

                    let customerData = {
                        name: name,
                        mobile: mobile
                    };
                    if (aadharMatch) {
                        customerData.aadhaar = idValue;
                    } else if (passportMatch) {
                        customerData.passport = idValue;
                    } else{
                        customerData.aadhaar = idValue;
                    }

                    customers.push(customerData);
                });

                // Validation
                if (!date) {
                    toastr.error('Please select a date.');
                    return reject(false);
                }
                if (customers.length === 0) {
                    toastr.error('Please select at least one customer.');
                    return reject(false);
                }

                // AJAX save
                $.ajax({
                    url: "{{ route('temple.service.save') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer_qty: customerCount,
                        temple_id: templeid,
                        user_id: userid,
                        pandit_id: panditId,
                        package_id: packageId,
                        date: date,
                        customers: customers,
                        type: 'puja'
                    },
                    success: function(response) {
                        if (response.status === 'success' || response.success) {
                            toastr.success('Puja booking saved successfully!');

                            // ✅ After saving, load lead data and show it
                            loadSavedPoojaData()
                                .then(() => resolve(true))
                                .catch(() => reject(false));

                        } else {
                            toastr.error(response.message || 'Error saving Puja booking!');
                            reject(false);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        toastr.error('Server error while saving Puja booking!');
                        reject(false);
                    }
                });
            });
        }

        function loadSavedPoojaData() {
            return new Promise((resolve, reject) => {
                const data = localStorage.getItem('templecustomer');
                const customers = JSON.parse(data);
                const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
                const mobile = firstCustomer?.mobile;

                if (!mobile) {
                    toastr.error('No customer mobile found.');
                    return reject();
                }

                $.ajax({
                    url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.details && response.details
                            .length > 0) {
                            const poojaData = response.details.find(d => d.type?.toLowerCase() ===
                                'puja');

                            if (poojaData) {
                                // Hide form and inject saved data
                                $('#puja-section').html(renderLeadData(poojaData));
                                loadSummaryPopup();
                                resolve(true);
                            } else {
                                toastr.error('No Pooja booking found in response.');
                                reject();
                            }
                        } else {
                            toastr.error('No saved booking found for this customer.');
                            reject();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Error fetching saved Pooja data.');
                        reject();
                    }
                });
            });
        }

        function renderLeadData(leadData) {
            const templeName = "{{ $temple['name'] ?? 'Temple Name' }}";
            let customersList = '-';
            let lockerList = '-';
            const customerQty = parseInt(leadData.customer_qty || 1);

            if (leadData.customers) {
                try {
                    const customersArr = Array.isArray(leadData.customers) ?
                        leadData.customers :
                        JSON.parse(leadData.customers);
                    customersList = customersArr.map(c => c.name || c.mobile).join(', ');
                } catch (e) {
                    toastr.error('Invalid customers data');
                }
            }

            if (leadData.locker_items) {
                try {
                    const lockerData = typeof leadData.locker_items === 'object' ?
                        leadData.locker_items :
                        JSON.parse(leadData.locker_items);
                    lockerList = Object.entries(lockerData)
                        .map(([key, val]) => `${key} (${val})`).join(', ');
                } catch (e) {
                    toastr.error('Invalid locker data');
                }
            }

            const basePrice = (parseFloat(leadData.package?.base_price) || 0) * customerQty;
            const platformFee = (parseFloat(leadData.package?.platform_fee_percentage) || 0) * customerQty;
            const receiptFee = (parseFloat(leadData.package?.receipt_fee_percentage) || 0) * customerQty;

            return `
                <div class="lead-data-view bg-white shadow-md rounded-2xl border border-gray-200 p-4 w-full max-w-md mx-auto">
                    <h2 class="text-center text-xl font-semibold text-purple-700 mb-3 border-b pb-2">${templeName}</h2>
                    <p class="text-center">${leadData.package?.varient_name || '-'}</p>
                    <p class="text-center"><strong>Registration No:</strong> ${leadData.order_id || '-'}</p>
                    <div style="display:flex; justify-content:space-between;"><span><strong>Booking Date:</strong></span><span>${leadData.booking_date || '-'}</span></div>
                    <div style="display:flex; justify-content:space-between;"><span><strong>Customers:</strong></span><span>${customersList}</span></div>
                    ${leadData.type?.toLowerCase() === 'locker' ? `
                                                                                                                                <div style="display:flex; justify-content:space-between;">
                                                                                                                                    <span><strong>Locker Items:</strong></span><span>${lockerList}</span></div>` : ''}
                    <div style="display:flex; justify-content:space-between;"><span><strong>Service Price:</strong></span><span>₹${basePrice.toFixed(2)}</span></div>
                    ${['puja', 'darshan'].includes(leadData.type?.toLowerCase()) ? 
                        `<div style="display:flex; justify-content:space-between;"><span><strong>GST Rate:</strong></span><span>${leadData.package?.gst_rate || '-'}%</span></div>` : ''}
                    <div style="display:flex; justify-content:space-between;"><span><strong>Platform Fee:</strong></span><span>₹${platformFee.toFixed(2)}</span></div>
                    <div style="display:flex; justify-content:space-between;"><span><strong>Receipt Fee:</strong></span><span>₹${receiptFee.toFixed(2)}</span></div>
                    <div style="display:flex; justify-content:space-between;"><span><strong>Amount:</strong></span><span>₹${leadData.amount || '-'}</span></div>
                    ${['darshan', 'bhojan'].includes(leadData.type?.toLowerCase()) && leadData.timeslot ?
                        `<div style="display:flex; justify-content:space-between;"><span><strong>Time Slot:</strong></span><span>${leadData.timeslot.start_time || '-'} - ${leadData.timeslot.end_time || '-'}</span></div>` : ''}
                    <div class="mt-4 text-center">
                        <div class="inline-block bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                            <strong>Status:</strong> ${leadData.payment_status || 'Pending'}
                        </div>
                    </div>
                </div>`;
        }

        function loadSummaryPopup() {
            const data = localStorage.getItem('templecustomer');
            const customers = JSON.parse(data);
            const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
            const mobile = firstCustomer?.mobile;

            if (!mobile) {
                toastr.error('No customer mobile found.');
                return;
            }

            $.ajax({
                url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                type: "GET",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success' && response.details?.length > 0) {
                        const leadData = response.details;

                        let totalAmount = 0;
                        const dataArray = Array.isArray(leadData) ? leadData : [leadData];
                        let summaryHtml = `
                            <div>
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-clipboard-list me-1"></i> Selected Services
                                </h6>
                                <ul class="list-group small mb-2">
                        `;

                        dataArray.forEach((l, i) => {
                            totalAmount += parseFloat(l.amount || 0);
                            summaryHtml += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${i + 1}. ${l.type || '-'}
                                    <span class="fw-semibold text-success">₹${l.amount || 0}</span>
                                </li>
                            `;
                        });

                        summaryHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total
                            <span class="fw-semibold text-success">₹${totalAmount.toFixed(2)}</span>
                        </li>
                        </ul></div>`;

                        $('#summaryPopup').html(summaryHtml);
                        // $('#totalAmount').text('₹' + totalAmount.toFixed(2));
                        if (totalAmount > 0) {
                            $('#paymentOptions').removeClass('d-none');
                            $('#paymentFreeOptions').addClass('d-none');
                        } else {
                            $('#paymentOptions').addClass('d-none');
                            $('#paymentFreeOptions').removeClass('d-none');
                        }
                        // $('#summaryModal').modal('show');
                    } else {
                        toastr.warning('No booking summary found.');
                    }
                },
                error: function() {
                    toastr.error('Error loading summary data.');
                }
            });
        }


        // });
    </script>

    <script>
        // $(document).on('click', '#darshan-save', function(e) {
        // e.preventDefault();
        function darshanSave() {
            return new Promise((resolve, reject) => {
                let date = $('input[name="vip_date"]').val();
                let packageId = $('.darshan-package-select').val();
                let customers = [];
                let templeid = $('#templeID').val();
                let userid = $('#userID').val();
                let customerCount = $('#darshanCustomerCount').val();
                let timeSlotId = $('.darshan-timeslot-id').val();

                $('.darshanCustomerCheckbox:checked').each(function(index) {
                    let label = $(this).siblings('label').text().trim();
                    let nameMatch = label.match(/Name:\s*(.*)/);
                    let mobileMatch = label.match(/Mobile:\s*([\+\d]+)/);
                    let aadharMatch = label.match(/Aadhaar:\s*([0-9]+)/);
                    let passportMatch = label.match(/Passport:\s*([A-Za-z0-9]+)/);

                    let name = nameMatch ? nameMatch[1].trim() : '';
                    let mobile = mobileMatch ? mobileMatch[1] : '';
                    let idValue = '';
                    if (aadharMatch) {
                        idValue = aadharMatch[1];
                    } else if (passportMatch) {
                        idValue = passportMatch[1];
                    } else {
                        idValue = "000000000000";
                    }

                    let serial = (index + 1).toString().padStart(2, '0');
                    name = `${name} (${serial})`;

                    let customerData = {
                        name: name,
                        mobile: mobile
                    };
                    if (aadharMatch) {
                        customerData.aadhaar = idValue;
                    } else if (passportMatch) {
                        customerData.passport = idValue;
                    } else{
                        customerData.aadhaar = idValue;
                    }

                    customers.push(customerData);
                });

                // Validation
                if (!timeSlotId) {
                    toastr.error('Please select a Time Slot.');
                    return reject();
                }
                if (!date) {
                    toastr.error('Please select a date.');
                    return reject();
                }
                if (customers.length === 0) {
                    toastr.error('Please select at least one customer.');
                    return reject();
                }

                // AJAX Save
                $.ajax({
                    url: "{{ route('temple.service.save') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer_qty: customerCount,
                        temple_id: templeid,
                        user_id: userid,
                        package_id: packageId,
                        date: date,
                        customers: customers,
                        type: 'darshan',
                        time_slot_id: timeSlotId,
                    },
                    success: async function(response) {
                        toastr.success('Darshan booking saved successfully!');

                        // ✅ Load the saved Darshan data and show it
                        await loadSavedDarshanData();

                        // ✅ Resolve after rendering
                        resolve();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Error saving Darshan booking!');
                        reject();
                    }
                });
            });
        }

        function loadSavedDarshanData() {
            return new Promise((resolve, reject) => {
                const data = localStorage.getItem('templecustomer');
                const customers = JSON.parse(data);
                const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
                const mobile = firstCustomer?.mobile;

                if (!mobile) {
                    toastr.error('No customer mobile found.');
                    return reject();
                }

                $.ajax({
                    url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.details && response.details
                            .length > 0) {
                            const darshanData = response.details.find(d => d.type?.toLowerCase() ===
                                'darshan');

                            if (darshanData) {
                                // Hide form and inject saved data
                                $('#darshan-section').html(renderLeadData(darshanData));
                                loadSummaryPopup();
                                resolve(true);
                            } else {
                                toastr.error('No Darshan booking found in response.');
                                reject();
                            }
                        } else {
                            toastr.error('No saved booking found for this customer.');
                            reject();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Error fetching saved Darshan data.');
                        reject();
                    }
                });
            });
        }

        // });
    </script>
    <script>
        // $(document).on('click', '#bhojan-save', function(e) {
        //     e.preventDefault();
        function bhojanSave() {
            return new Promise((resolve, reject) => {
                let date = $('input[name="bhojan_date"]').val();
                let packageId = $('.bhojan-package-select').val();
                let customers = [];
                let templeid = $('#templeID').val();
                let userid = $('#userID').val();
                let customerCount = $('#bhojanCustomerCount').val();
                let timeSlotId = $('.bhojan-timeslot-id').val();

                // Collect checked customers
                $('.bhojanCustomerCheckbox:checked').each(function(index) {
                    let label = $(this).siblings('label').text().trim();
                    let nameMatch = label.match(/Name:\s*(.*)/);
                    let mobileMatch = label.match(/Mobile:\s*([\+\d]+)/);
                    let aadharMatch = label.match(/Aadhaar:\s*([0-9]+)/);
                    let passportMatch = label.match(/Passport:\s*([A-Za-z0-9]+)/);

                    let name = nameMatch ? nameMatch[1].trim() : '';
                    let mobile = mobileMatch ? mobileMatch[1] : '';
                    let idValue = '';
                    if (aadharMatch) {
                        idValue = aadharMatch[1];
                    } else if (passportMatch) {
                        idValue = passportMatch[1];
                    } else {
                        idValue = "000000000000";
                    }

                    let serial = (index + 1).toString().padStart(2, '0');
                    name = `${name} (${serial})`;

                    let customerData = {
                        name: name,
                        mobile: mobile
                    };
                    if (aadharMatch){
                        customerData.aadhaar = idValue;  
                    } else if (passportMatch){
                        customerData.passport = idValue;
                    } else{
                        customerData.aadhaar = idValue;
                    }

                    customers.push(customerData);
                });

                // ✅ Validation (return reject if fails)
                if (!packageId) {
                    toastr.error('Please select a Bhojan package.');
                    return reject();
                }
                if (!timeSlotId) {
                    toastr.error('Please select a Time Slot.');
                    return reject();
                }
                if (!date) {
                    toastr.error('Please select a Bhojan date.');
                    return reject();
                }
                if (customers.length === 0) {
                    toastr.error('Please select at least one customer.');
                    return reject();
                }

                // ✅ Save via AJAX
                $.ajax({
                    url: "{{ route('temple.service.save') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer_qty: customerCount,
                        temple_id: templeid,
                        user_id: userid,
                        package_id: packageId,
                        date: date,
                        customers: customers,
                        type: 'bhojan',
                        time_slot_id: timeSlotId,
                    },
                    success: function(response) {
                        toastr.success('Bhojan booking saved successfully!');

                        // ✅ Load and display saved data
                        loadSavedBhojanData()
                            .then(() => {
                                toastr.success('Bhojan data displayed successfully.');
                                resolve(true); // tell main click handler it's OK to move
                            })
                            .catch(() => {
                                toastr.error('Unable to load Bhojan data after saving.');
                                reject();
                            });
                    },
                    error: function(xhr) {
                        toastr.error('Error saving Bhojan booking!');
                        reject();
                    }
                });
            });
        }
        // });

        function loadSavedBhojanData() {
            return new Promise((resolve, reject) => {
                const data = localStorage.getItem('templecustomer');
                const customers = JSON.parse(data);
                const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
                const mobile = firstCustomer?.mobile;

                if (!mobile) {
                    toastr.error('No customer mobile found.');
                    return reject();
                }

                $.ajax({
                    url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.details?.length > 0) {
                            const bhojanData = response.details.find(
                                d => d.type?.toLowerCase() === 'bhojan'
                            );

                            if (bhojanData) {
                                $('#bhojan-section').html(renderLeadData(bhojanData));
                                loadSummaryPopup();
                                resolve(true);
                            } else {
                                toastr.error('No Bhojan booking found.');
                                reject();
                            }
                        } else {
                            toastr.error('No saved booking found for this customer.');
                            reject();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Error fetching saved Bhojan data.');
                        reject();
                    }
                });
            });
        }
    </script>

    <script>
        // $(document).on('click', '#locker-save', function(e) {
        //     e.preventDefault()
        function lockerSave() {
            console.log('locker running');
            return new Promise((resolve, reject) => {
                let date = $('input[name="locker_date"]').val();
                let packageId = $('.locker-package-select').val();
                let templeid = $('#templeID').val();
                let userid = $('#userID').val();
                let mobileQty = parseInt($('#locker-mobile-qty').val()) || 0;
                let luggageQty = parseInt($('#locker-luggage-qty').val()) || 0;
                let customers = [];

                // 🧩 Retrieve stored customer
                const storedCustomers = localStorage.getItem('templecustomer');
                if (storedCustomers) {
                    let parsedData = JSON.parse(storedCustomers);
                    const customersArray = Array.isArray(parsedData) ? parsedData : [parsedData];
                    if (customersArray.length > 0) {
                        const firstCustomer = customersArray[0];
                        let customerData = {
                            name: firstCustomer.name || '',
                            mobile: firstCustomer.mobile || ''
                        };
                        if (firstCustomer.aadhaar) customerData.aadhaar = firstCustomer.aadhaar;
                        else if (firstCustomer.passport) customerData.passport = firstCustomer.passport;
                        customers.push(customerData);
                    } else {
                        toastr.error('Please add a customer.');
                        return reject();
                    }
                } else {
                    toastr.error('Please add a customer.');
                    return reject();
                }

                // 🛑 Validation checks
                if (!date) {
                    toastr.error('Please select a date.');
                    return reject();
                }

                if ((mobileQty <= 0 && luggageQty <= 0) || mobileQty < 0 || luggageQty < 0) {
                    console.log('mobile if');
                    toastr.error('Please select valid locker quantities.');
                    return reject();
                }

                // ✅ Prepare locker data
                let locker_items = {
                    mobile: mobileQty,
                    luggage: luggageQty
                };
                console.log('validation fails');

                // ✅ AJAX Save
                $.ajax({
                    url: "{{ route('temple.service.save') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer_qty: 1,
                        temple_id: templeid,
                        user_id: userid,
                        package_id: packageId,
                        date: date,
                        customers: customers,
                        type: 'locker',
                        locker_items: locker_items,
                    },
                    success: async function(response) {
                        console.log('locer save');
                        toastr.success('Locker booking saved successfully!');
                        try {
                            // Display saved locker data
                            await loadSavedLockerData();
                            resolve(); // ✅ Proceed to next tab only after display
                        } catch (err) {
                            console.log('errpr');
                            toastr.error('Error displaying locker data.');
                            reject();
                        }
                    },
                    error: function(xhr) {
                        console.log('last error');
                        console.error(xhr.responseText);
                        toastr.error('Error saving locker booking!');
                        reject();
                    }
                });
            });
        }

        function loadSavedLockerData() {
            console.log('display data');
            return new Promise((resolve, reject) => {
                const data = localStorage.getItem('templecustomer');
                const customers = JSON.parse(data);
                const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
                const mobile = firstCustomer?.mobile;

                if (!mobile) {
                    toastr.error('No customer mobile found.');
                    return reject();
                }

                $.ajax({
                    url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.details?.length > 0) {
                            const lockerData = response.details.find(
                                d => d.type?.toLowerCase() === 'locker'
                            );

                            if (lockerData) {
                                $('#locker-section').html(renderLeadData(lockerData));
                                loadSummaryPopup();
                                resolve(true);
                            } else {
                                toastr.error('No locker booking found.');
                                reject();
                            }
                        } else {
                            toastr.error('No saved booking found for this customer.');
                            reject();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Error fetching saved locker data.');
                        reject();
                    }
                });
            });
        }


        // });
    </script>

    {{-- check service taken --}}
    <script>
        function checkServicesTaken() {
            return new Promise((resolve, reject) => {
                const data = localStorage.getItem('templecustomer');
                const customers = JSON.parse(data);
                const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
                const mobile = firstCustomer?.mobile;

                if (!mobile) {
                    toastr.error('No customer mobile found.');
                    return resolve(false);
                }

                $.ajax({
                    url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.details && response.details
                            .length > 0) {
                            resolve(true);
                        } else {
                            resolve(false);
                        }
                    },
                    error: function() {
                        resolve(false);
                    }
                });
            });
        }
    </script>

    {{-- previous button click --}}
    {{-- <script>
        function prevBtn(){
            $('#actionButtons').removeClass('d-none');
            $('#pay-section').addClass('d-none');
            $('#proceedPaymentBtn').addClass('d-none');            
        }
    </script> --}}
    <script>
        $(".change-language").on("click", function () {
            $.ajaxSetup({
                headers: {
                "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
                },
            });
            $.ajax({
                type: "POST",
                url: $(this).data("action"),
                data: {
                language_code: $(this).data("language-code"),
                },
                success: function (data) {
                toastr.success(data.message);
                location.reload();
                },
            });
        });
    </script>
    <script>
    $(document).on('change', '.changeLanguageSelect', function () {
        let lang = $(this).val();
        let action = $(this).data('action');

        if (lang) {
            $.post(action, {language_code: lang, _token: "{{ csrf_token() }}"}, function () {
                location.reload();
            });
        }
    });
</script>


</body>

</html>
