<!DOCTYPE html>
<html lang="hi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahakal • Booking Form</title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/roboto-font.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/bootstrapnew.min.css') }}" rel="stylesheet" />
    <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/puja-single.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>

<body>
    @php
    $ecommerceLogo = getWebConfig('company_web_logo');
    @endphp

    <header class="container-fluid py-3 bg-white shadow-sm fixed-top">
        <div class="row align-items-center justify-content-between">
            <div class="col-6">
                <div class="guru-title">
                    <span class="small-text">Sacred Rituals by</span>
                    <h3>{{ $gurujiname->name }}</h3>
                </div>
            </div>
            <div class="col-6 d-flex justify-content-end">
                <div class="powered-logo text-end">
                    <span class="powered-text">Powered by</span>
                    <a href="{{ route('guruji.individual', ['name' => Str::slug($gurujiname->name)]) }}">
                        <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}" alt="Mahakal.com" class="site-logo">
                    </a>
                </div>
            </div>
        </div>

        <div class="collapse bg-light p-2 mt-2 d-md-none" id="mobileSteps">
            <h6 class="text-warning mb-1">{{ translate('Online Booking Form') }}</h6>
            <p class="text-secondary small mb-0">
                <i class="fas fa-box"></i> {{ translate('Package Selection') }}
                <span class="mx-1">→</span>
                <i class="fas fa-user"></i> {{ translate('Details') }}
                <span class="mx-1">→</span>
                <i class="fas fa-check-circle"></i> {{ translate('Confirmation') }}
                <span class="mx-1">→</span>
                <i class="fas fa-credit-card"></i> {{ translate('Payment') }}
            </p>
        </div>
    </header>

    <main style="margin-top:100px;">
        <!-- Image Slider -->
        @php
        if ($puja->product_type == 'pooja') {
        $folder = 'pooja/';
        $bookingdate = $puja->booking_date ?? null;
        $pujavenue = $puja->pooja_venue ?? null;
        }
        $pujaCount = 10000;
        @endphp

        <section class="container mb-2">
            <div class="owl-carousel">
                @if(!empty($imagePaths))
                @foreach ($imagePaths as $key => $photo)
                <div class="item carousel-image {{ $key === 0 ? 'active' : '' }}" id="image{{ $key }}">
                    <img class="w-100 shadow-sm" src="{{ $photo }}" alt="Puja Image {{ $key + 1 }}">
                </div>
                @endforeach
                @endif
            </div>
        </section>

        <!-- Hero Card -->
        <section class="container mb-3">
            <div class="card-hero">
                <div class="row align-items-center">
                    <div class="col-md-6 text-start">
                        <div class="flex flex-row mt-2 flex-nowrap leading-normal">
                            <div>
                            <span class="inline-flex">{{ translate('Till_now') }}</span>

                            <span style="color:#000;">
                                {{ translate('have_experienced_divine_blessings') }}.
                                {{ \Illuminate\Support\Str::of($gurujiname->name)->replace('-', ' ')->title() }}
                                {{ translate('has_successfully_completed') }} {{ $pujaCount }}+ {{ translate('counsellings') }}.
                            </span>

                                <div class="tray mb-3 ml-3 mt-2">
                                    @php
                                    $uniqueUsers = range(0, 13);
                                    shuffle($uniqueUsers);
                                    $selectedUsers = array_slice($uniqueUsers, 0, 5);
                                    @endphp
                                    @foreach ($selectedUsers as $random_user)
                                    <div class="relative circle-img-container">
                                        <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                            style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $random_user . '.jpg') }}')">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 text-md-end text-start">
                        <div class="font-10">
                            <h3 class="text-sm mt-2 mb-2 font-medium border-b border-dashed border-primary font-weight-bold">
                                <i class="fas fa-star"></i>
                                5/5 (1K +ratings)
                            </h3>
                        </div>
                    </div>
                </div>
                
                <h3 class="h3 mt-2 text-dark font-bold" id="displayName">{{ $puja->name ?? 'ServiceName' }}</h3>
                
            </div>
        </section>
    </main>

    <!-- Sticky Footer -->
    <div class="sticky-bar py-2">
        <div class="container">
            <div class="row align-items-center">

                <!-- LEFT -->
                <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0" id="footerInfo">
                    <span style="color:#FF6F00; font-weight:700; font-size:25px; margin-left:4px;">
                        {{ translate('Your_service_charge') }}
                        ₹
                        @if($puja->counsellingPackage)
                            {{ number_format($puja->counsellingPackage->price) }}
                        @else
                            {{ $puja['counselling_selling_price'] }}
                        @endif
                        /-
                    </span>
                </div>

                <!-- RIGHT -->
                <div class="col-12 col-md-6 d-flex gap-2 justify-content-center justify-content-md-end">
                    <a href="{{ route('guruji.individual', ['name' => Str::slug($gurujiname->name)]) }}"
                    class="btn w-100 w-md-auto px-3">
                        ← {{ translate('Back') }}
                    </a>

                    <button class="btn w-100 w-md-auto px-3"
                            id="btnEditInfo"
                            style="background-color:#FF6F00; border-color:#FF6F00;">
                        {{ translate('Proceed') }} →
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="fullPageLoader"
        style="position: fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(1px);     
            -webkit-backdrop-filter: blur(1px);
            z-index: 999999;
            display:none;
            align-items:center;
            justify-content:center;
            flex-direction: column;">

        <div class="spinner-border text-light" style="width: 4rem; height: 4rem;"></div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="modal-header">
                    <span class="text-18 font-bold mr-2">
                        {{ translate('Fill_your_details_for_Consultancy') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="detailsForm" method="POST" novalidate action="{{ route('guruji.gurujicounsellingLead', $puja->slug) }}">
                    @csrf
                    @php
                    if (auth('customer')->check()) {
                        $customer = App\Models\User::where('id', auth('customer')->id())->first();
                    }
                    @endphp

                    <input type="hidden" name="service_id" value="{{ $puja->id }}">
                    <input type="hidden" name="type" id="package_type" value="{{ $puja->product_type }}">
                    <input type="hidden"  name="package_price"  id="package_price" 
                    value="@if($puja->counsellingPackage){{ $puja->counsellingPackage->price }}@else
                            {{ $puja->counselling_selling_price }}@endif">
                    <input type="hidden" name="final_amount" id="total_amount" value="@if($puja->counsellingPackage){{ $puja->counsellingPackage->price }}@else
                            {{ $puja->counselling_selling_price }}@endif">
                    <input type="hidden" name="pandit_id" id="pandit-id" value="{{ $gurujiname->id }}">
                    <div class="modal-body">
                        <span class="block text-16 font-bold text-gray-900 text-dark">
                        {{ translate('enter_Your_Whatsapp_Mobile_Number') }}
                        </span>
                        <span class="text-[12px] font-normal text-[#707070]">
                        {{ translate('Your_Counselling_booking_updates_will_be_sent_on_below_WhatsApp_number') }}
                        </span>

                        <!-- Phone -->
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label font-semibold">
                                    {{ translate('Phone Number') }}
                                    <small class="text-primary">( *
                                        {{ translate('Country code is must like for IND') }} 91 )</small>
                                </label>
                                <input class="form-control phone-input-with-country-picker" type="tel"
                                    value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                    name="person_phone" id="person-number"
                                    placeholder="{{ translate('Phone Number') }}" inputmode="numeric" required
                                    maxlength="10" minlength="10" {{ isset($customer['phone']) ? 'readonly' : '' }}>
                                <p id="number-validation" class="text-danger d-none">
                                    Enter a valid 10-digit Mobile Number
                                </p>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label font-semibold">{{ translate('Your Name') }}</label>
                                <input class="form-control"
                                    value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? $customer['l_name'] : '' }}"
                                    type="text" name="person_name" id="person-name"
                                    placeholder="{{ translate('Ex') }}: {{ translate('Your Name') }}" required
                                    {{ isset($customer['f_name']) ? 'readonly' : '' }}>

                                <p id="name-validation" class="text-danger d-none">
                                    Enter Your Name
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="bookNowBtn"
                            class="btn btn-primary btn-block btn-shadow mt-1 font-weight-bold w-100">
                            {{ translate('Book Now') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Confirm Your Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($digital_payment['status'] == 1)
                    @foreach ($payment_gateways_list as $payment_gateway)
                    <form method="post" class="digital_payment counselling-pending-form"
                        id="{{ $payment_gateway->key_name }}_form" action="{{ route('GurujipaymentRequestCounseling') }}">
                        @csrf

                        <div class="Details">
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
                            <input type="hidden" name="external_redirect_link" value="{{ route('guruji-counselling-pending-web-payment') }}">
                            <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                <input type="radio" id="{{ $payment_gateway->key_name }}" name="online_payment" class="form-check-input custom-radio"
                                    value="{{ $payment_gateway->key_name }}" hidden>
                                <img width="30" src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}"
                                    alt="" hidden>
                                <span class="text-capitalize form-check-label" hidden>
                                    @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                    {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                    @else
                                    {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                    @endif
                                </span>
                            </label>
                            <input type="hidden" name="order_id" id="pending-order-id" class="orderId" value="">
                            <input type="hidden" name="leads_id" id="pending-lead-id" class="orderId" value="">
                        </div>
                    </form>
                    @endforeach
                    @endif
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="text-center mb-3"><i class="fas fa-receipt"></i>
                                {{ translate('Booking Receipt') }}
                            </h5>
                            <div class="row">

                                <!-- LEFT: BOOKING INFORMATION -->
                                <div class="col-md-6 mb-3" style="border-right: 1px solid #ddd;">
                                    <h6 class="mb-3"><i class="fas fa-book"></i> {{ translate('Booking Information') }}</h6>

                                    <div class="d-flex flex-column gap-2 pe-3">
                                        <div class="d-flex justify-content-between">
                                            <p class="mb-0"><span id="cService">—</span></p>
                                            <p class="mb-0"><strong>{{ translate('Order ID') }}:</strong>
                                                <span id="cOrderId">—</span>
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <p class="mb-0"><strong>{{ translate('Booking Date') }}:</strong>
                                                <span id="cDate">—</span>
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <p class="mb-0"><strong>{{ translate('Guruji Name') }}:</strong><span id="cGurujiName">—</span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT: CUSTOMER INFORMATION -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="mb-3"><i class="fas fa-user"></i> {{ translate('Customer Details') }}</h6>

                                    <div class="d-flex flex-column gap-2 ps-3">
                                        <div class="d-flex justify-content-between">
                                            <p class="mb-0"><strong>{{ translate('Name') }}:</strong>
                                                <span id="cName">—</span>
                                            </p>

                                            <p class="mb-0"><strong>{{ translate('Mobile') }}:</strong>
                                                <span id="cMobile">—</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
    
                                <!-- Amount -->
                                <div class="text-end mt-2">
                                    <h6><strong>{{ translate('Total Amount') }}:</strong><span id="cAmount">—</span>
                                    </h6>
                                </div>

                            </div>

                            <hr>

                            <!-- Footer Message -->
                            <p class="text-center text-danger fw-bold">
                                {{ translate('Note Confirmed & Payment Pending') }}
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button class="btn btn-primary" type="button"
                            id="finalSubmit">{{ translate('Confirm & Submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="display-6 mb-3">✅</div>
                <h5>पेमेंट सफल!</h5>
                <p>आपकी बुकिंग कन्फर्म हो गई है।</p>
                <button class="btn btn-warning" data-bs-dismiss="modal">ठीक है</button>
            </div>
        </div>
    </div>
  
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

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
                        items: 2 // large desktop
                    }
                }
            });
        });

        // =========================
        // Global Variables
        // =========================
        let chosen = {
            id: null,
            name: null,
            type: null,
            pandit: null,
            amount: 0
        };
        let selectedProducts = {};

        const detailsModal = new bootstrap.Modal('#detailsModal');
        const confirmModal = new bootstrap.Modal('#confirmModal');
        const successModal = new bootstrap.Modal('#successModal');
        const $bookingDateContainer = $('#booking-date-container');
        const $bookingDateSelect = $('#bookingDateSelect');
    
        // =========================
        // Package Selection
        // =========================
     
        document.addEventListener("DOMContentLoaded", function () {
            let footerElement = document.getElementById('footerInfo');
            let proceedBtn = document.getElementById('btnEditInfo');
            proceedBtn.disabled = false;
            window.counsellingPrice = parseFloat(
                footerElement.innerText.replace(/[^0-9.]/g, '')
            );
        });


        // =========================
        // Footer Proceed Btn
        // =========================
        document.getElementById('btnEditInfo').addEventListener('click', () => detailsModal.show());

        // =========================
        // Save Details Form
        // =========================
        
        const detailsForm = document.getElementById('detailsForm');
        detailsForm.addEventListener('submit', function(e) {
            e.preventDefault();

            let name = document.getElementById('person-name').value.trim();
            let mobile = document.getElementById('person-number').value.trim();
            let packageType = chosen.type;

            let valid = true;
            if (!/^\d{10}$/.test(mobile)) {
                document.getElementById('number-validation').classList.remove('d-none');
                valid = false;
            } else {
                document.getElementById('number-validation').classList.add('d-none');
            }
            if (name.length < 2) {
                document.getElementById('name-validation').classList.remove('d-none');
                valid = false;
            } else {
                document.getElementById('name-validation').classList.add('d-none');
            }

            if (!valid) return;

            $('#bookNowBtn').text('Please Wait ...');
            $('#bookNowBtn').prop('disabled', true);
            $('#detailsModal').on('shown.bs.modal', function() {
                $('#bookNowBtn').text('Book Now');
                $('#bookNowBtn').prop('disabled', false);
            });

            let formData = new FormData(detailsForm);
            fetch(detailsForm.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log(data);
                    let d = data.data;

                    document.getElementById('cService').textContent = d.service_name || "—";
                    document.getElementById('cName').textContent = d.name || "—";
                    document.getElementById('cMobile').textContent = d.mobile || "—";
                    document.getElementById('cDate').textContent = d.booking_date || "—";
                    document.getElementById('cGurujiName').textContent = d.guruji_name || "—";
                    document.getElementById('cOrderId').textContent = d.order_id || "—";

                    // Amount inside modal → update only after modal is shown
                    $('#confirmModal').on('shown.bs.modal', function () {
                        document.getElementById('cAmount').textContent = "₹" + (d.total_amount || 0);
                    });

                    console.log("Amount:", d.total_amount);

                    $("#pending-order-id").val(d.order_id);
                    $("#pending-lead-id").val(d.lead_id);

                    const finalBtn = document.getElementById('finalSubmit');
                    finalBtn.setAttribute("data-orderid", d.order_id);
                    finalBtn.setAttribute("data-amount", d.total_amount);
                    finalBtn.setAttribute("data-leadid", d.lead_id);
                    
                    confirmModal.show();
                    detailsModal.hide();
                } else {
                        alert(data.message);
                        $("#fullPageLoader").fadeOut(10);
                        $('#bookNowBtn').text('Book Now');
                        $('#bookNowBtn').prop('disabled', false);
                    }
                
            })
            .catch(err => console.error(err));

        });

        // =========================
        // Final Submit
        // =========================
        document.getElementById('finalSubmit').addEventListener('click', function() {
            const orderId = this.getAttribute("data-orderid");
            const amount = this.getAttribute("data-amount");
            const leadId = this.getAttribute("data-leadid");
            if (!orderId || !amount) return;
            $('#pending-order-id').val(orderId);
            $("#pending-lead-id").val(leadId);
            $('.counselling-pending-form').submit();
        });


    </script>

   


    {{-- mobile no blur --}}
    <script>
        $('#person-number').blur(function(e) {
            e.preventDefault();
            var code = $('.iti__selected-dial-code').text();
            var mobile = $(this).val();
            var no = code + '' + mobile;

            $.ajax({
                type: "get",
                url: "{{ url('account-service-order-user-name') }}" + "/" + no,
                success: function(response) {
                    if (response.status == 200) {
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);
                    } else {
                        $('#send-otp-btn').addClass('d-none');
                        $('#withoutOTP').removeClass('d-none');
                    }
                }
            });
        });
    </script>

    <script>
        function fullReset() {
            selectedProducts = {};
            $('#bookNowBtn').html("Book Now").prop("disabled", false);
        }
        // reset when details modal closes
            $('#detailsModal').on('hidden.bs.modal', function() {
                fullReset();
            });

            $('#confirmModal').on('hidden.bs.modal', function() {
                fullReset();
            });

    </script>

    <script>
      $('#confirmModal').on('show.bs.modal', function () {

        // verify elements exist safely
        let nameInput = document.getElementById('person-name');
        let mobileInput = document.getElementById('person-number');
        let cName = document.getElementById('cName');
        let cMobile = document.getElementById('cMobile');
        let cAmount = document.getElementById('cAmount');
        
        // Name fill
        if (nameInput && cName) {
            cName.textContent = (nameInput.value || "").trim() || "—";
        }

        // Mobile fill
        if (mobileInput && cMobile) {
            cMobile.textContent = (mobileInput.value || "").trim() || "—";
        }

        // Amount — ONLY chosen.amount (NO products)
        if (cAmount) {
            cAmount.textContent = "₹" + Number(chosen.amount).toLocaleString();
        }
        });


    </script>

    <script>
        $(document).ready(function() {

            $("#bookNowBtn").on("click", function(e) {
                let phone = $("#person-number").val().trim();
                let name = $("#person-name").val().trim();
                let valid = true;

                // PHONE 
                if (phone.length !== 10 || isNaN(phone)) {
                    $("#number-validation").removeClass("d-none");
                    valid = false;
                } else {
                    $("#number-validation").addClass("d-none");
                }

                // NAME 
                if (name === "") {
                    $("#name-validation").removeClass("d-none");
                    valid = false;
                } else {
                    $("#name-validation").addClass("d-none");
                }

                if (!valid) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }

                $("#fullPageLoader").fadeIn(10).css("display", "flex");
            });

            $('#confirmModal').on('shown.bs.modal', function() {
                $("#fullPageLoader").fadeOut(10);
            });

        });
    </script>
    <script>
        // Disable CTRL + Plus/Minus zoom
        document.addEventListener('keydown', function(e) {
            if (
                (e.ctrlKey && (e.key === '+' || e.key === '-' || e.key === '=')) ||
                e.key === 'Meta' // Mac pinch zoom
            ) {
                e.preventDefault();
            }
        });

        // Disable mouse wheel zoom (CTRL + scroll)
        window.addEventListener('wheel', function(e) {
            if (e.ctrlKey) {
                e.preventDefault();
            }
        }, {
            passive: false
        });

        // Disable double-tap zoom (mobile)
        let lastTouch = 0;
        document.addEventListener('touchend', function(e) {
            let now = new Date().getTime();
            if (now - lastTouch <= 300) {
                e.preventDefault();
            }
            lastTouch = now;
        }, false);
        // gride
        
    </script>

</body>

</html>