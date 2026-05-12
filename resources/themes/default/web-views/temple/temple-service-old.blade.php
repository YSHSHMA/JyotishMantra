<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Temple Services Dynamic Forms</title>
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

    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial;
            margin: 0;
            background: #f7f8fb;
            color: #111;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 15px;
            background: #fff;
            border-bottom: 2px solid #e67e22;
            z-index: 1000;
        }

        .header img {
            height: 40px;
            object-fit: contain;
        }

        .services {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        label.service {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #e6e9ef;
            border-radius: 8px;
            cursor: pointer;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 12px;
            font-weight: 600;
            z-index: 9999;
            border-top: 2px solid #e67e22;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #eef2ff;
            font-size: 12px;
        }

        .saved {
            color: green;
            font-weight: 600;
        }

        .note {
            font-size: 14px;
            color: #555;
        }

        /* Slider */
        .slider {
            margin: 30px auto;
            border-radius: 10px;
            overflow: hidden;
        }

        .owl-carousel .item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }

        /* Temple Info Container */
        .temple-info {
            background: #fff;
            margin: 35px auto;
            padding: 15px;
            border-radius: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        /* Left and Right Columns */
        .temple-left,
        .temple-right {
            flex: 1 1 50px;
            min-width: 300px;
        }

        .temple-info h2 {
            margin-bottom: 15px;
            color: #333;
            font-size: 24px;
        }

        .temple-info p {
            margin: 10px 0;
            color: #555;
            font-size: 15px;
            line-height: 1.6;
            display: flex;
            align-items: center;
        }

        .temple-info i {
            color: #e67e22;
            width: 18px;
            text-align: center;
        }

        .temple-info strong {
            color: #222;
        }

        .rating {
            display: flex;
            align-items: center;
        }

        .rating i {
            color: #f1c40f;
        }

        .verified {
            display: inline-block;
            margin-left: 8px;
            color: #27ae60;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .temple-info {
                flex-direction: column;
                padding: 20px;
            }
        }

        .card-header h5 {
            font-weight: 600;
        }

        .otp-section {
            background-color: #f9f9f9;
        }

        #savedCustomers .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Optional: nicer scroll for customer list */
        #savedCustomers::-webkit-scrollbar {
            width: 6px;
        }

        #savedCustomers::-webkit-scrollbar-thumb {
            background-color: rgba(0, 123, 255, 0.5);
            border-radius: 3px;
        }

        .customer-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: 0.3s ease;
        }

        .customer-section h5 {
            font-weight: 600;
        }

        #addCustomerSection,
        #savedCustomersSection {
            transition: all 0.4s ease-in-out;
        }

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-radius: 6px !important;
        }

        .list-group-item:hover {
            background-color: #f9f9f9;
        }

        @media (max-width: 768px) {
            .customer-section {
                padding: 20px 15px;
            }
        }

        .fixed-sidebar {
            position: sticky;
            top: 80px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .fixed-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .fixed-sidebar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        .btn-xs {
            font-size: 0.75rem;
            line-height: 1rem;
            border-radius: 6px;
            padding: 4px 8px;
        }

        #showAddCustomerBtn i,
        #showSavedCustomerBtn i {
            font-size: 0.8rem;
        }

        #sendOtpBtn {
            white-space: nowrap;
        }

        .fa-whatsapp {
            font-size: 1.2rem;
        }

        .form-control.ps-5 {
            padding-left: 2.5rem !important;
        }

        /* Container stays sticky (already done) */
        .services {
            position: sticky;
            top: 80px;
            z-index: 1000;
            background: #fff;
            padding: 12px;
            border-bottom: 1px solid #eee;
            transition: box-shadow 0.2s ease;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Each service label styled as a nice clickable button */
        .service-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;

            cursor: pointer;
            font-size: 15px;
            color: #333;
            transition: all 0.2s ease;
            user-select: none;
        }


        /* Hide the native checkbox */
        .service-btn input[type="checkbox"] {
            display: none;
        }

        /* When checked — highlight beautifully */
        .service-btn input[type="checkbox"]:checked+span {
            background: linear-gradient(135deg, #ffb300, #ff8c00);
            color: #fff;
            border-color: #ff9800;
            box-shadow: 0 2px 6px rgba(255, 152, 0, 0.3);
            padding: 8px 20px;
            border-radius: 25px;
        }

        /* Text span adjustment */
        .service-btn span {
            pointer-events: none;
            transition: all 0.2s ease;
        }

        /* amount summary popup */
        .summary-popup {
            position: absolute;
            bottom: calc(100% + 8px);
            /* show above the icon */
            left: 0;
            /* width auto so it fits content */
            min-width: 210px;
            max-width: 360px;
            background: #fff;
            border: 1px solid #e6e6e6;
            border-radius: 6px;
            padding: 10px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
            z-index: 1200;
            white-space: normal;
        }

        /* ensure scroll if many items */
        .summary-popup .list-group {
            max-height: 240px;
            overflow-y: auto;
        }

        /* optional small arrow */
        .summary-popup::after {
            content: "";
            position: absolute;
            bottom: -6px;
            left: 12px;
            /* tweak to position arrow */
            border-width: 6px 6px 0 6px;
            border-style: solid;
            border-color: #fff transparent transparent transparent;
            filter: drop-shadow(0 -1px 1px rgba(0, 0, 0, 0.06));
        }

        /* country code phone input */
        .iti {
            width: 100% !important;
        }

        .phone-input-with-country-picker {
            width: 100% !important;
        }
    </style>
</head>

<body class="bg-gradient-to-b from-orange-400 via-orange-600 to-orange-800 min-h-screen font-sans">
    @php
        $ecommerceLogo = getWebConfig('company_web_logo');
        $verify = $temple->aadhaar_verify_status ?? 0;
        $images = !empty($temple['galleries2']['images']) ? json_decode($temple['galleries2']['images'], true) : [];
    @endphp
    <div class="header mb-5">
        <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
            alt="Logo">
    </div>
    <div class="container-fluid pt-5">
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="row align-items-stretch">
                <!-- Left: Image Slider -->
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

                <!-- Right: Temple Info -->
                <div class="col-md-5 d-flex flex-column justify-content-center">
                    <div class="temple-info p-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <!-- Left Info -->
                            <div class="temple-left">
                                <h4 class="mb-2">
                                    {{ $temple['name'] }}
                                    @if ($verify == 1)
                                        <span class="verified">
                                            <i class="fa-solid fa-circle-check text-success"></i> Verified
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

                            <!-- Right Rating -->
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
            </div><!-- /.row (Temple Image Slider & Info Section) -->
        </div><!-- /.card -->
    </div><!-- /.container-fluid (Temple Header Section) -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-4 my-2">
                <div class="customer-section p-4 shadow-sm rounded bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary d-flex align-items-center mb-0">
                            <i class="fas fa-users me-2"></i> Booking Inforamtion
                        </span>
                        <div class="d-flex gap-2 align-items-center">
                            <button class="btn btn-outline-primary btn-xs px-2 py-1 d-flex align-items-center"
                                id="showAddCustomerBtn">
                                <i class="fas fa-user-plus me-1"></i> Add
                            </button>
                            <button class="btn btn-outline-secondary btn-xs px-2 py-1 d-flex align-items-center"
                                id="showSavedCustomerBtn">
                                <i class="fas fa-list me-1"></i> List
                            </button>
                        </div>
                    </div>
                    <!-- Add Customer Section -->
                    <div id="addCustomerSection">
                        <div class="row g-3 align-items-end" id="inputRow">
                            <div class="col-md-12 d-flex justify-content-between">
                                <label class="form-label font-semibold d-block">NRI User</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="nriSwitch">
                                    {{-- <label class="form-check-label" for="nriSwitch">Toggle if NRI</label> --}}
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12">
                                <label class="form-label font-semibold">
                                    Phone Number <small class="text-primary">(Country code is required, e.g., for India
                                        use +91)</small>
                                </label>
                                <input class="form-control phone-input-with-country-picker" type="tel"
                                    id="mobile" placeholder="Enter phone number" required>

                                <!-- Hidden field to store the full number -->
                                <input type="hidden" id="full_phone_number" name="full_phone_number">
                            </div>
                            <!-- Default country indicator (optional) -->
                            <div class="system-default-country-code" data-value="IN" hidden></div>

                            <div id="aadhaar-section" class="col-12 d-flex">
                                <input type="text" id="aadhaar"
                                    class="form-control {{ $temple->aadhaar_verify_status == 1 ? 'me-2' : '' }}"
                                    placeholder="12-digit Aadhaar" maxlength="12" pattern="\d{12}"
                                    title="Enter 12-digit Aadhaar number"
                                    oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,12)">
                                <input type="hidden" id="aadhaar-request-id"inputmode="numeric">
                                @if ($temple->aadhaar_verify_status == 1)
                                    <button type="button" id="sendOtpBtn" class="btn btn-primary send-otp-btn">
                                        Send OTP
                                    </button>
                                @endif
                            </div>

                            <div id="passport-section" class="col-12 d-none">
                                <input type="text" id="passport" class="form-control"
                                    placeholder="Enter Passport Number" maxlength="15">
                            </div>

                            <div class="col-12 mt-2 d-none d-flex" id="otpSection">
                                {{-- <div class="col-md-9 col-sm-12"> --}}
                                <input type="text" id="aadhaar-otp" class="form-control me-2" maxlength="6"
                                    placeholder="Enter OTP" pattern="\d{6}" inputmode="numeric">
                                {{-- </div> --}}
                                {{-- <div class="col-md-3 col-sm-12"> --}}
                                <button type="button" id="verifyOtpBtn" class="btn btn-success">Verify</button>
                                {{-- </div> --}}
                            </div>
                            <div id="aadhaar-no-error" class="small mt-1 text-danger"></div>

                            <div class="mt-2 {{ $temple->aadhaar_verify_status == 1 ? 'd-none' : '' }}"
                                id="UserInfoHide">
                                <div class="col-12 mt-2">
                                    <input type="text" id="name" class="form-control"
                                        placeholder="Yajman Name">
                                </div>

                                <div class="col-12 text-center mt-2">
                                    <button type="button" id="addBtn" class="btn btn-success w-100">Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Saved Customers Section -->
                    <div id="savedCustomersSection" class="d-none">
                        <h6 class="text-secondary mb-3 d-flex align-items-center">
                            <i class="fas fa-users me-2"></i> Saved Customers
                        </h6>
                        <ul id="savedCustomers" class="list-group small overflow-auto" style="max-height: 300px;">
                            {{-- Dynamically added customer items --}}
                        </ul>
                    </div>
                </div>
                <!-- Product / Service Information Section -->
                {{-- <div class="col-md-12 mt-2  fixed-sidebar">
                    <div class="customer-section p-4 shadow-sm rounded bg-white"> --}}


                <!-- Confirm Booking Button -->
                {{-- <div id="selectedServices"></div> --}}
                {{-- @if ($digital_payment['status'] == 1)
                            @foreach ($payment_gateways_list as $payment_gateway)
                                <form method="POST" class="digital_payment"
                                    id="{{ $payment_gateway->key_name }}_form"
                                    action="{{ route('temple.paymentRequestTemple') }}">
                                    @csrf
                                    <div class="mb-4">
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
                                                name="online_payment" class="form-check-input custom-radio"
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

                                    </div>
                                    <div id="paymentOptions"
                                        class="payment-options d-none d-flex justify-content-between align-items-center mt-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_mode"
                                                id="cashPayment" value="cash" checked>
                                            <label class="form-check-label fw-medium" for="cashPayment">
                                                Cash Payment
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_mode"
                                                id="onlinePayment" value="online">
                                            <label class="form-check-label fw-medium" for="onlinePayment">
                                                Online Payment
                                            </label>
                                        </div>
                                    </div>


                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary d-none px-4" type="submit"
                                            id="proceedPaymentBtn">
                                            <i class="fas fa-check-circle me-1"></i> Confirm Booking
                                        </button>
                                    </div>
                                </form>
                            @endforeach
                        @endif --}}
                {{-- </div>
                </div> --}}

            </div>

            <div class="col-md-8 mt-2 mb-5">
                <div class="customer-section p-4 shadow-sm rounded bg-white">
                    <h4 class="mb-2 text-primary d-flex align-items-center">
                        <i class="fa-solid fa-temple-hindu me-2 text-warning"></i> Choose Temple Services
                    </h4>
                    <p class="text-muted small mb-3">
                        Please select the temple services you wish to avail. You can choose one or multiple services
                        based on your needs before proceeding with the booking.
                    </p>

                    @if (isset($templeServices) && !empty($templeServices))
                        <div class="services mb-3 d-flex flex-wrap gap-2" role="group"
                            aria-label="Temple services selection">
                            @foreach ($templeServices as $service)
                                @if (isset($service['id']) && isset($service['name']))
                                    <label class="service-btn" for="service-{{ $service['id'] }}">
                                        <input type="checkbox" id="service-{{ $service['id'] }}"
                                            class="service-checkbox"
                                            data-name="{{ \Illuminate\Support\Str::slug($service['name']) }}"
                                            value="{{ $service['id'] }}" aria-label="{{ $service['name'] }}">
                                        <span>{{ ucfirst($service['name']) }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            No temple services are currently available. Please check back later.
                        </div>
                    @endif
                    <ul class="nav nav-tabs" id="serviceTabs" role="tablist"></ul>
                    <div class="tab-content mt-2" id="serviceTabContent"></div>
                </div>
            </div>
        </div>
    </div>


    {{-- Hidden partial forms (cloned dynamically) --}}
    <div id="hiddenForms" style="display:none;">
        <div id="form-puja">
            @include('web-views.temple.partials._puja_form')
        </div>
        <div id="form-darshan">
            @include('web-views.temple.partials._darshan_form')
        </div>
        <div id="form-bhojan">
            @include('web-views.temple.partials._bhojan_form')
        </div>
        <div id="form-locker">
            @include('web-views.temple.partials._locker_form')
        </div>
    </div>
    <input type="hidden" id="templeID" value="{{ $temple['id'] }}">
    <input type="hidden" id="userID">
    <input type="hidden" id="customerCount">
    <input type="hidden" id="darshanCustomerCount">
    <input type="hidden" id="bhojanCustomerCount">
    <div class="footer bg-light text-center d-none row" id="footerInfo">
        {{-- <div class="row w-100 d-flex justify-content-between"> --}}
        <div class="col-md-3">
            <div class="d-flex align-items-center">
                <!-- wrapper is relative so popup can be absolutely positioned relative to this -->
                <div id="toggleSummaryWrap" style="position:relative;">
                    <i class="fas fa-info-circle text-primary fs-5 me-2" id="toggleSummary"
                        style="cursor:pointer;"></i>
                    <!-- popup placed here -->
                    <div id="summaryPopup" class="summary-popup d-none" aria-hidden="true"></div>
                </div>

                <span class="fw-bold text-success" id="totalAmount"></span>
            </div>
        </div>
        <div class="col-md-9">
            @if ($digital_payment['status'] == 1)
                @foreach ($payment_gateways_list as $payment_gateway)
                    <form method="POST" class="digital_payment" id="{{ $payment_gateway->key_name }}_form"
                        action="{{ route('temple.paymentRequestTemple') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
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
                                <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                    <input type="radio" id="{{ $payment_gateway->key_name }}"
                                        name="online_payment" class="form-check-input custom-radio"
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
                                <input type="hidden" id="serviceID" name="temple_id" value="{{ $temple['id'] }}">
                                <input type="hidden" id="customerID" name="customer_id">
                            </div>

                            <div class="col-md-8 d-flex justify-content-end gap-4">
                                <div id="paymentOptions"
                                    class="payment-options d-none d-flex gap-2 justify-content-around align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_mode"
                                            id="cashPayment" value="cash" checked>
                                        <label class="form-check-label fw-medium" for="cashPayment">
                                            Cash
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_mode"
                                            id="onlinePayment" value="online">
                                        <label class="form-check-label fw-medium" for="onlinePayment">
                                            Online
                                        </label>
                                    </div>
                                </div>
                            {{-- </div> --}}

                            {{-- <div class="col-md-4"> --}}
                                <div class="text-end">
                                    <button class="btn btn-primary d-none px-4" type="submit"
                                        id="proceedPaymentBtn">
                                        <i class="fas fa-check-circle me-1"></i> Confirm Booking
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endforeach
            @endif
        </div>
        {{-- </div> --}}
    </div>

    {{-- <div id="summaryContainer" class="position-fixed bottom-0 start-0 w-100 bg-white border-top shadow-lg p-3 d-none"
        style="max-height: 300px; overflow-y: auto; z-index: 1050;">
    </div> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    {{-- <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script> --}}
    <!-- Include intl-tel-input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.5.1/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.5.1/js/utils.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            var today = new Date().toISOString().split('T')[0];
            // $('#vipDate-select').attr('min', today);
            $('#pujaDate').attr('min', today);
            loadTimeSlotsGeneric('darshan');
            loadTimeSlotsGeneric('bhojan');
        });
        // Owl Carousel
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
            } else {
                $('#passport').val('');
                $('#passport-section').addClass('d-none');
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
                        // $('#otpSection').slideDown();
                        $('#otpSection').removeClass('d-none');
                        // $('#sendOtpBtn').prop('disabled', true);
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
                        // $('#mobile').val(data.data.phone);
                        toastr.success('This Aadhaar is already verified.');

                    } else {
                        // Error from API
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
        $(document).ready(function() {
            const input = document.querySelector("#mobile");
            const iti = window.intlTelInput(input, {
                initialCountry: "in",
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.7.0/build/js/utils.js"
            });

            input.addEventListener('input', function() {
                const number = iti.getNumber();
                $('#full_phone_number').val(number);
            });

            $(document).on('click', '#addBtn', function() {
                // $('#addBtn').click(function() {
                const isNRI = $('#nriSwitch').is(':checked');
                const mobile = iti.getNumber();
                const name = $('#name').val().trim();
                const mobileNo = $('#mobile').val().trim();
                const aadhaar = $('#aadhaar').val().trim();
                const passport = $('#passport').val().trim();
                let customerData = {};

                if (!name || mobileNo.length === 0) {
                    toastr.error('Please fill valid details.');
                    return;
                }
                console.log(isNRI);
                if (!isNRI) {
                    if (aadhaar.length !== 12) {
                        toastr.error('Please fill valid aadhaar details.');
                        return;
                    }
                    if (aadhaarVerifyStatus == 1 && !aadhaarVerified) {
                        toastr.error('Please verify Aadhaar before adding.');
                        return;
                    }
                } else {
                    console.log(passport.length)
                    if (passport.length === 0) {
                        toastr.error('Please fill valid passport details.');
                        return;
                    }
                }

                let customers = JSON.parse(localStorage.getItem('templecustomer')) || [];

                if (customers.length === 0) {
                    $.ajax({
                        url: '{{ route('temple.customer.checkOrCreate') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer: {
                                name,
                                mobile,
                                // aadhaar
                            }
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                $('#userID').val(res.user.id);
                                $('#customerID').val(res.user.id);

                                // customers.push({
                                //     name,
                                //     mobile,
                                //     aadhaar
                                // });

                                customerData.name = name;
                                customerData.mobile = mobile;
                                if (isNRI) {
                                    customerData.passport = passport;
                                } else {
                                    customerData.aadhaar = aadhaar;
                                }

                                customers.push(customerData);

                                saveAndShow(customers);
                                getServiceData();
                                // $('#addCustomerSection').load(location.href +
                                //     ' #addCustomerSection>*');
                                toastr.success('Yajman added successfully!');
                            } else {
                                toastr.error('Error saving first customer.');
                            }
                        },
                        error: function() {
                            toastr.error('Server error while saving first customer.');
                        }
                    });
                } else {
                    // customers.push({
                    //     name,
                    //     mobile,
                    //     aadhaar
                    // });
                    customerData.name = name;
                    customerData.mobile = mobile;
                    if (isNRI) {
                        customerData.passport = passport;
                    } else {
                        customerData.aadhaar = aadhaar;
                    }

                    customers.push(customerData);
                    saveAndShow(customers);
                    // $('#addCustomerSection').load(location.href + ' #addCustomerSection>*');
                    toastr.success('Yajman added successfully!');
                }
                $('#aadhaar').prop('disabled', false).addClass('me-2');
                $('#sendOtpBtn').removeClass('d-none');
                input.value = ""; // clears the phone input
                iti.setNumber("");
                $("#nriSwitch").prop("checked", false).trigger("change");
            });
        });

        function saveAndShow(customers) {
            localStorage.setItem('templecustomer', JSON.stringify(customers));
            showSavedCustomers();
            showPujaSavedCustomers();
            showDarshanSavedCustomers();
            showBhojanSavedCustomers();
            clearInputs();
            toastr.success('Yajman added successfully!');
        }

        // ---- SHOW SAVED CUSTOMERS (existing list) ----
        // ---- SHOW ALL SAVED CUSTOMERS (LIST) ----
        function showSavedCustomers() {
            const saved = JSON.parse(localStorage.getItem('templecustomer')) || [];
            const list = $('#savedCustomers');
            list.empty();

            if (saved.length === 0) {
                list.append('<li class="list-group-item text-muted">No customers added yet.</li>');
                return;
            }

            $.each(saved, function(i, c) {
                const idInfo = c.aadhaar ?
                    `<small>Aadhaar: ${c.aadhaar}</small>` :
                    c.passport ?
                    `<small>Passport: ${c.passport}</small>` :
                    `<small class="text-muted">No ID provided</small>`;

                list.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${c.name}</strong><br>
                            <small>Mobile: ${c.mobile}</small> |
                            ${idInfo}
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
                const idInfo = c.aadhaar ?
                    `Aadhaar: ${c.aadhaar}` :
                    c.passport ?
                    `Passport: ${c.passport}` :
                    `No ID provided`;

                list.append(`
            <div class="col-md-4">
                <div class="card p-2 shadow-sm">
                    <div class="form-check">
                        <input class="form-check-input pujaCustomerCheckbox" type="checkbox" value="${i}" id="pujaCustomer${i}">
                        <label class="form-check-label" for="pujaCustomer${i}">
                            <strong>Name: ${c.name}</strong><br>
                            Mobile: ${c.mobile}<br>
                            ${idInfo}
                        </label>
                    </div>
                </div>
            </div>
        `);
            });
        }

        // ---- SHOW DARSHAN SAVED CUSTOMERS ----
        function showDarshanSavedCustomers() {
            const saved = JSON.parse(localStorage.getItem('templecustomer')) || [];
            const list = $('#darshanSavedCustomers');
            list.empty();

            if (saved.length === 0) {
                list.append('<li class="list-group-item text-muted">No customers added yet.</li>');
                return;
            }

            $.each(saved, function(i, c) {
                const idInfo = c.aadhaar ?
                    `Aadhaar: ${c.aadhaar}` :
                    c.passport ?
                    `Passport: ${c.passport}` :
                    `No ID provided`;

                const checkboxId = `darshanCustomer${i}`;
                list.append(`
                    <div class="col-md-4">
                        <div class="card p-2 shadow-sm">
                            <div class="form-check">
                                <input class="form-check-input darshanCustomerCheckbox" type="checkbox" value="${i}" id="${checkboxId}">
                                <label class="form-check-label" for="${checkboxId}">
                                    <strong>Name: ${c.name}</strong><br>
                                    Mobile: ${c.mobile}<br>
                                    ${idInfo}
                                </label>
                            </div>
                        </div>
                    </div>
                `);
            });
        }

        // ---- SHOW BHOJAN SAVED CUSTOMERS ----
        function showBhojanSavedCustomers() {
            const saved = JSON.parse(localStorage.getItem('templecustomer')) || [];
            const list = $('#bhojanSavedCustomers');
            list.empty();

            if (saved.length === 0) {
                list.append('<li class="list-group-item text-muted">No customers added yet.</li>');
                return;
            }

            $.each(saved, function(i, c) {
                const idInfo = c.aadhaar ?
                    `Aadhaar: ${c.aadhaar}` :
                    c.passport ?
                    `Passport: ${c.passport}` :
                    `No ID provided`;

                const checkboxId = `bhojanCustomer${i}`;
                list.append(`
            <div class="col-md-4">
                <div class="card p-2 shadow-sm">
                    <div class="form-check">
                        <input class="form-check-input bhojanCustomerCheckbox" type="checkbox" value="${i}" id="${checkboxId}">
                        <label class="form-check-label" for="${checkboxId}">
                            <strong>Name: ${c.name}</strong><br>
                            Mobile: ${c.mobile}<br>
                            ${idInfo}
                        </label>
                    </div>
                </div>
            </div>
        `);
            });
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
            $('#name, #mobile, #aadhaar, #aadhaar-otp, #passport').val('');
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



        function readyData() {
            showSavedCustomers();
            getCustomerData();
            getServiceData();
            // console.log('getservicedata');
            // showPujaSavedCustomers();
            // console.log('pujacustomer');
            // showDarshanSavedCustomers();
            // console.log('darshancustomer');
            // showBhojanSavedCustomers();
            // console.log('bhojancustomer');
        }
    </script>
    <script>
        function getCustomerData() {
            const data = localStorage.getItem('templecustomer');
            if (!data) {
                console.log('No customer data found');
                return;
            }
            const customers = JSON.parse(data);
            const firstCustomer = Array.isArray(customers) ? customers[0] : customers;

            if (!firstCustomer || !firstCustomer.mobile) {
                console.log('First customer or mobile number not found');
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
        // Function to handle checkbox change
        let checkboxes = document.querySelectorAll('.service-checkbox');;

        function handleCheckboxChange(cb, leadData = null) {
            const tabList = document.getElementById('serviceTabs');
            const tabContent = document.getElementById('serviceTabContent');
            const serviceName = cb.dataset.name;
            const formId = `form-${serviceName}`;
            const tabId = `${serviceName}-tab`;
            const paneId = `${serviceName}-pane`;

            if (cb.checked) {
                if (!document.getElementById(tabId)) {
                    const tab = document.createElement('li');
                    tab.classList.add('nav-item');
                    tab.innerHTML = `
                <a class="nav-link" id="${tabId}" data-bs-toggle="tab"
                    href="#${paneId}" role="tab" aria-controls="${paneId}" aria-selected="false">
                    ${serviceName.charAt(0).toUpperCase() + serviceName.slice(1)}
                    </a>
                `;
                    tabList.appendChild(tab);
                }



                // Add Tab Content if not exists
                if (!document.getElementById(paneId) || document.getElementById(paneId)) {
                    $(paneId).remove();
                    const pane = document.createElement('div');
                    pane.classList.add('tab-pane', 'fade');
                    pane.id = paneId;
                    pane.setAttribute('role', 'tabpanel');

                    if (leadData) {
                        let customersList = '-';
                        let lockerList = '-';
                        const templeName = "{{ $temple['name'] ?? 'Temple Name' }}"; //
                        if (leadData.customers) {
                            try {
                                const customersArr = Array.isArray(leadData.customers) ?
                                    leadData.customers :
                                    JSON.parse(leadData.customers);
                                customersList = customersArr.map(c => c.name || c.mobile).join(', ');
                            } catch (e) {
                                toastr.error('Invalid customers data', e);
                            }
                        }
                        const customerQty = parseInt(leadData.customer_qty || 1);

                        if (leadData.locker_items) {
                            try {
                                const lockerData = typeof leadData.locker_items === 'object' ?
                                    leadData.locker_items :
                                    JSON.parse(leadData.locker_items);

                                const lockerLabels = {
                                    mobile: 'Mobile',
                                    luggage: 'Luggage'
                                };

                                lockerList = Object.entries(lockerData)
                                    .map(([key, value]) => `${lockerLabels[key] || key}(${value})`)
                                    .join(', ');
                            } catch (e) {
                                toastr.error('Invalid locker item data', e);
                            }
                        }

                        // Calculate multiplied values safely
                        const basePrice = (parseFloat(leadData.package?.base_price) || 0) * customerQty;
                        const platformFee = (parseFloat(leadData.package?.platform_fee_percentage) || 0) * customerQty;
                        const receiptFee = (parseFloat(leadData.package?.receipt_fee_percentage) || 0) * customerQty;
                        // Build display HTML
                        let displayHtml = `
                            <div class="lead-data-view bg-white shadow-md rounded-2xl border border-gray-200 p-4 w-full max-w-md mx-auto">
                                <h2 class="text-center text-xl font-semibold text-purple-700 mb-3 border-b pb-2">
                                ${templeName}
                                </h2>
                                <p class="text-center"> ${leadData.package?.varient_name || '-'}</p>
                                <p class="text-center"><strong>Registration No:</strong> ${leadData.order_id || '-'}</p>
                                <!-- Booking Details (Left-Right) -->
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span><strong>Booking Date:</strong></span>
                                    <span>${leadData.booking_date || '-'}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span><strong>Customers:</strong></span>
                                    <span>${customersList}</span>
                                </div>
                                ${leadData.type?.toLowerCase() === 'locker'? 
                                `<div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                                                                    <span><strong>Locker Items:</strong></span>
                                                                                    <span>${lockerList}</span>
                                                                                </div>` : ''}
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span><strong>Service Price:</strong></span>
                                    <span>₹${basePrice.toFixed(2)}</span>
                                </div>
                                ${leadData.type?.toLowerCase() === 'puja' || leadData.type?.toLowerCase() === 'darshan'? 
                                `<div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                                                                    <span><strong>GST Rate:</strong></span>
                                                                                    <span>${leadData.package?.gst_rate || '-'}%</span>
                                                                                </div>` : ''}
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span><strong>Platform Fee:</strong></span>
                                    <span>₹${platformFee.toFixed(2)}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span><strong>Receipt Fee:</strong></span>
                                    <span>₹${receiptFee.toFixed(2)}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span><strong>Amount:</strong></span>
                                    <span>₹${leadData.amount || '-'}</span>
                                </div>

                                ${((leadData.type?.toLowerCase() === 'darshan' || leadData.type?.toLowerCase() === 'bhojan') && leadData.timeslot)
                                    ? `
                                                                                        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                                                                            <span><strong>Time Slot:</strong></span>
                                                                                            <span>${leadData.timeslot.start_time || '-'} - ${leadData.timeslot.end_time || '-'}</span>
                                                                                        </div>
                                                                                    `
                                    : ''
                                }

                                <!-- Payment Status -->
                                <div class="mt-4 text-center">
                                    <div class="inline-block bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                                        <strong>Status:</strong> ${leadData.payment_status || 'Pending'}
                                    </div>
                                </div>
                            </div>
                            `;

                        pane.innerHTML = displayHtml;
                    } else {
                        const formHtml = document.querySelector(`#hiddenForms #${formId}`)?.innerHTML || '';
                        pane.innerHTML = formHtml;
                    }
                    tabContent.appendChild(pane);
                    $('#' + paneId).addClass('active show');
                    showPujaSavedCustomers();
                    showDarshanSavedCustomers();
                    showBhojanSavedCustomers();
                }

                const bsTab = new bootstrap.Tab(document.getElementById(tabId));
                bsTab.show();
            } else {
                // Remove tab and content
                document.getElementById(tabId)?.remove();
                document.getElementById(paneId)?.remove();

                // Activate first tab if available
                const firstTab = tabList.querySelector('.nav-link');
                if (firstTab) {
                    const bsTab = new bootstrap.Tab(firstTab);
                    bsTab.show();
                }
            }
        }
        // Attach change event to all checkboxes
        checkboxes.forEach(cb => cb.addEventListener('change', () => handleCheckboxChange(cb)));

        // --- GET SERVICE DATA AND CHECK BOXES ---
        function getServiceData() {
            const data = localStorage.getItem('templecustomer');
            if (!data) {
                $('.service-checkbox').each(function() {
                    const leadForTyp = null;
                    $(this).prop('checked', true);
                    handleCheckboxChange(this, leadForTyp);
                });
                // $('#selectedServices').empty().append('<div class="text-muted">No services found.</div>');
                loadTimeSlotsGeneric('darshan');
                loadTimeSlotsGeneric('bhojan');
                return;
            }
            const customers = JSON.parse(data);
            const firstCustomer = Array.isArray(customers) ? customers[0] : customers;
            const mobile = firstCustomer.mobile;
            if (!firstCustomer || !firstCustomer.mobile) {
                $('.service-checkbox').each(function() {
                    const leadForTyp = null;
                    $(this).prop('checked', true);
                    handleCheckboxChange(this, leadForTyp);
                });
                // $('#selectedServices').empty().append('<div class="text-muted">No services found.</div>');
                loadTimeSlotsGeneric('darshan');
                loadTimeSlotsGeneric('bhojan');
                return;
            }
            $.ajax({
                url: "{{ url('temple/lead-data-temple') }}/" + mobile,
                type: "GET",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const leads = response.details || [];
                        let totalAmount = response.lead?.amount || 0;
                        const types = leads.map(l => l.type.toLowerCase());
                        const btn = $('#proceedPaymentBtn');
                        const paymentOptions = $('#paymentOptions');
                        if (totalAmount == 0) {
                            btn.removeClass('d-none');
                            // paymentOptions.removeClass('d-none');
                        } else {
                            paymentOptions.removeClass('d-none');
                            btn.removeClass('d-none');
                        }
                        $('#serviceTabContent').html('');
                        // === Fill service checkboxes based on lead types ===
                        $('.service-checkbox').each(function() {
                            $(this).prop('checked', true);
                            const name = $(this).data('name');
                            const leadForType = leads.find(l => l.type.toLowerCase() === name) ?? null;
                            if (leadForType) {
                                $(this).prop('disabled', true);
                            }
                            handleCheckboxChange(this, leadForType);
                        });

                        // === Build summary HTML dynamically ===
                        let summaryHtml = '';
                        // let totalAmount = 0;

                        if (leads.length > 0) {
                            $('#footerInfo').removeClass('d-none');
                            summaryHtml += `
                                <div>
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-clipboard-list me-1"></i> Selected Services
                                    </h6>
                                    <ul class="list-group small mb-2">
                            `;

                            leads.forEach((l, i) => {
                                // const amt = parseFloat((l.amount ?? 0).toString().replace(/,/g, '')) ||
                                //     0;
                                // totalAmount += amt;

                                summaryHtml += `
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        ${i + 1}. ${l.type}
                                        <span class="fw-semibold text-success">₹${l.amount}</span>
                                    </li>
                                `;
                                // totalAmount += parseFloat(l.amount);
                            });

                            summaryHtml += `</ul></div>`;

                            $('#summaryPopup').html(summaryHtml);
                            $('#totalAmount').text('₹' + totalAmount);
                        }

                        // Toggle summary visibility on icon click
                        $('#toggleSummary').on('click', function(e) {
                            e
                                .stopPropagation(); // prevent the document click close handler from immediately hiding it
                            $('#summaryPopup').toggleClass('d-none');
                        });

                        // prevent clicks inside popup from closing it
                        $('#summaryPopup').on('click', function(e) {
                            e.stopPropagation();
                        });

                        // clicking anywhere else closes the popup
                        $(document).on('click', function() {
                            $('#summaryPopup').addClass('d-none');
                        });

                        // === Inject or replace in DOM ===
                        // $('#selectedServices').html(summaryHtml);
                    } else {
                        $('.service-checkbox').each(function() {
                            const leadForTyp = null;
                            $(this).prop('checked', true);
                            handleCheckboxChange(this, leadForTyp);
                        });
                        // $('#selectedServices').html(
                        //     '<div class="text-muted">No services found.</div>');
                        
                        loadTimeSlotsGeneric('darshan');
                        loadTimeSlotsGeneric('bhojan');
                    }
                },
                error: function(xhr, status, error) {

                    // $('#selectedServices').empty().append(
                    //     '<div class="text-danger">Failed to load services.</div>');
                }
            });

        }
                            
    </script>
    <script>
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
                    date: dateVal
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

                        if (isPast) return; // skip past time slots for today

                        const btn = $('<button>')
                            .addClass('btn btn-outline-primary btn-sm m-1 time-slot-btn')
                            .attr('data-id', slot.id)
                            .text(slot.time);

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

        // Handle slot button click
        $(document).on('click', '.time-slot-btn', function() {
            const $btn = $(this);
            const $container = $btn.closest('[id$="-section"]');
            $container.find('.time-slot-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
            $btn.removeClass('btn-outline-primary').addClass('btn-primary active');

            const slotId = $btn.data('id');
            $container.find('input[type="hidden"].' + $container.attr('id').replace('-section', '') +
                '-timeslot-id').val(slotId);
        });

        // Auto-load on date or package change
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
        // BOJAN CALCULATION
    </script>

    <script>
        $(document).on('click', '#pooja-save', function(e) {
            e.preventDefault()
            let date = $('input[name="puja_date"]').val();
            let packageId = $('.puja-package-select').val();
            let customers = [];
            let templeid = $('#templeID').val();
            let userid = $('#userID').val();
            let customerCount = $('#customerCount').val();
            // let timeSlotId = $('#timeSlotSelect').val();
            $('.pujaCustomerCheckbox:checked').each(function(index) {
                let label = $(this).siblings('label').text().trim();
                let nameMatch = label.match(/Name:\s*(.*)/);
                let mobileMatch = label.match(/Mobile:\s*([0-9]+)/);
                let aadharMatch = label.match(/Aadhaar:\s*([0-9]+)/);
                let passportMatch = label.match(/Passport:\s*([A-Za-z0-9]+)/);

                let name = nameMatch ? nameMatch[1].trim() : '';
                let mobile = mobileMatch ? mobileMatch[1] : '';
                let idValue = aadharMatch ? aadharMatch[1] : (passportMatch ? passportMatch[1] : '');

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
                }

                customers.push(customerData);
            });

            // Validation
            if (!date) {
                toastr.error('Please select a date.');
                return;
            }
            if (customers.length === 0) {
                toastr.error('Please select at least one customer.');
                return;
            }

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
                    type: 'puja',
                    // time_slot_id: timeSlotId,
                },
                success: function(response) {
                    readyData();
                    toastr.success('Puja booking saved successfully!'); // changed to success
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    toastr.error('Error saving puja booking!');
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '#darshan-save', function(e) {
            e.preventDefault();
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
                let idValue = aadharMatch ? aadharMatch[1] : (passportMatch ? passportMatch[1] : '');
                
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
                }

                customers.push(customerData);
            });

            // Validation
            if (!timeSlotId) {
                toastr.error('Please select a Time Slot.');
                return;
            }
            if (!date) {
                toastr.error('Please select a date.');
                return;
            }
            if (customers.length === 0) {
                toastr.error('Please select at least one customer.');
                return;
            }

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
                success: function(response) {
                    readyData();
                    toastr.success('Darshan booking saved successfully!');
                },
                error: function(xhr) {
                    toastr.error('Error saving darshan booking!');
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '#bhojan-save', function(e) {
            e.preventDefault();
            let date = $('input[name="bhojan_date"]').val();
            let packageId = $('.bhojan-package-select').val();
            let customers = [];
            let templeid = $('#templeID').val();
            let userid = $('#userID').val();
            let customerCount = $('#bhojanCustomerCount').val();
            let timeSlotId = $('.bhojan-timeslot-id').val();

            $('.bhojanCustomerCheckbox:checked').each(function(index) {
                let label = $(this).siblings('label').text().trim();

                let nameMatch = label.match(/Name:\s*(.*)/);
                let mobileMatch = label.match(/Mobile:\s*([0-9]+)/);
                let aadharMatch = label.match(/Aadhaar:\s*([0-9]+)/);
                let passportMatch = label.match(/Passport:\s*([A-Za-z0-9]+)/);

                let name = nameMatch ? nameMatch[1].trim() : '';
                let mobile = mobileMatch ? mobileMatch[1] : '';
                let idValue = aadharMatch ? aadharMatch[1] : (passportMatch ? passportMatch[1] : '');

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
                }

                customers.push(customerData);
            });

            // Validation
            if (!timeSlotId) {
                toastr.error('Please select a Time Slot.');
                return;
            }
            if (!date) {
                toastr.error('Please select a date.');
                return;
            }
            if (customers.length === 0) {
                toastr.error('Please select at least one customer.');
                return;
            }

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
                    readyData();
                    toastr.success('Bhojan booking saved successfully!');
                },
                error: function(xhr) {
                    toastr.error('Error saving Bhojan booking!');
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '#locker-save', function(e) {
            e.preventDefault()
            let date = $('input[name="puja_date"]').val();
            let packageId = $('.locker-package-select').val();
            let templeid = $('#templeID').val();
            let userid = $('#userID').val();
            let mobileQty = parseInt($('#locker-mobile-qty').val()) || 0;
            let luggageQty = parseInt($('#locker-luggage-qty').val()) || 0;
            let customers = [];
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
                    if (firstCustomer.aadhaar) {
                        customerData.aadhaar = firstCustomer.aadhaar;
                    } else if (firstCustomer.passport) {
                        customerData.passport = firstCustomer.passport;
                    }

                    customers.push(customerData);
                } else {
                    toastr.error('Please add a customer.');
                    return;
                }
            }

            // Validation
            if (!date) {
                toastr.error('Please select a date.');
                return;
            }

            // Check conditions
            if ((mobileQty <= 0 && luggageQty <= 0) || mobileQty < 0 || luggageQty < 0) {
                toastr.error('Please select any quantity.');
                return false;
            }

            let locker_items = {
                mobile: mobileQty,
                luggage: luggageQty
            };

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
                success: function(response) {
                    readyData();
                    toastr.success('Locker booking saved successfully!'); // changed to success
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    toastr.error('Error saving locker booking!');
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addSection = document.getElementById('addCustomerSection');
            const savedSection = document.getElementById('savedCustomersSection');
            const addBtn = document.getElementById('showAddCustomerBtn');
            const savedBtn = document.getElementById('showSavedCustomerBtn');

            addBtn.addEventListener('click', () => {
                addSection.classList.remove('d-none');
                savedSection.classList.add('d-none');
                addBtn.classList.add('btn-primary');
                savedBtn.classList.remove('btn-primary');
            });

            savedBtn.addEventListener('click', () => {
                savedSection.classList.remove('d-none');
                addSection.classList.add('d-none');
                savedBtn.classList.add('btn-primary');
                addBtn.classList.remove('btn-primary');
            });
        });
        document.addEventListener('scroll', function() {
            const services = document.querySelector('.services');
            if (!services) return;

            if (window.scrollY > 120) {
                services.classList.add('is-sticky');
            } else {
                services.classList.remove('is-sticky');
            }
        });
    </script>


</body>

</html>
