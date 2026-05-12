@extends('layouts.front-end.app')
@section('title', translate('booking'))
@push('css_or_js')
    <meta property="og:image"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
    <meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <meta property="twitter:card"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
    <meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <style>
        .right-section .pay-card {
            width: 100%;
            display: inline-flex;
            background: #ffeccc;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }



        .temple-header {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .temple-header img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
        }

        .temple-text h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .temple-text p {
            margin: 2px 0;
            font-size: 14px;
        }

        .info-box {
            background: #ffe4c7;
            border-left: 6px solid orange;
            padding: 1rem 1.2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            color: #6b3e00;
            font-size: 16px;
            font-weight: 600;
        }

        .info-box ul {
            padding-left: 1.2rem;
            margin: 0;
        }

        .info-box ul li {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .right-section h3 {
            margin-top: 0;
            font-size: 16px;
            color: #333;
        }

        .price-tag {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 10px;
        }

        .btn-continue {
            display: inline-block;
            background: linear-gradient(90deg, #ff9900, #ffcc66);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 16px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: 0.3s ease;
        }

        .btn-continue:hover {
            transform: scale(1.03);
        }


        .btn-outline-primary:hover {
            background-color: var(--web-primary) !important;
            border-color: var(--web-primary) !important;
        }
    </style>
@endpush
@section('content')
    <div class="__inline-23">
        <div class="container mt-5 mb-5 rtl __inline-53 text-align-direction">
            <div class="row g-3 mx-max-md-0">
                <!-- Left Section -->
                <div class="col-lg-8 col-md-7 col-12 mb-4">
                    <div class="cards mb-3" style="border-radius: 10px;border: 1px solid #e4eef9;">
                        <div class="card-header" id="">
                            <span class="mb-2 __inline-24">{{ $templeLead['title'] }}</span>
                            <h6 class="m-0 text-uppercase">{{ translate('for') }} {{ $getData['name'] ?? '' }}</h6>
                            <div class="temple-header mt-3 d-flex flex-wrap align-items-center">
                                <img src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/' . ($getData['thumbnail'] ?? ''), type: 'backend-product') }}"
                                    alt="Temple Image" class="img-fluid rounded mb-2 mb-md-0"
                                    style="max-width: 120px; height: auto;">
                                <div class="temple-text ml-md-3 mt-2 mt-md-0">
                                    <h2 class="text-capitalize">{{ $getData['name'] ?? '' }}</h2>
                                    <p class="text-lowercase mb-1">{{ $getData['states']['name'] ?? '' }} |
                                        {{ $getData['cities']['city'] ?? '' }}</p>
                                    <p class="mb-0">{{ date('jS M Y', strtotime($templeLead['date'] ?? '')) }} |
                                        {{ $templeLead['time'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Details -->
                    <div id="devotee-list" class="p-4 bg-light rounded mt-3">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0 font-weight-bold">{{ translate('Devotess_Information') }}</h5>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                    data-target="#devoteeModal" id="addDevoteeBtn"
                                    onclick="$('.aadhar_otp_form').addClass('d-none');
                                    $('.aadhar_number_form').removeClass('d-none');
                                    $('#aadhar_request_id').val('');
                                    $('#aadhar_otp').val('');
                                    $('#aadhar_Number').attr('readonly',false);
                                    phoneNumberSet('{{ optional(auth('customer')->user())->phone ?? '' }}');">
                                    <i class="fa fa-plus"></i> {{ translate('Add New Member') }}
                                </button>
                            </div>
                        </div>
                        <div id="devotee-container"></div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="col-lg-4 col-md-5 col-12 right-section">


                    {{-- Member list --}}
                    <?php
                    $getAddharData = json_decode($templeLead['people_info'] ?? '[]', true);
                    $aadharList = array_column($getAddharData, 'aadhar');
                    ?>
                    @if ($memberList && count($memberList) > 0)
                        <div class="">
                            <div class="card shadow-sm border-0">
                                <div class="card-body add-member-list-div">
                                    <h5 class="font-weight-bold mb-2"
                                        style="border-bottom: 2px solid #000; padding-bottom: 5px;">
                                        {{ translate('Member List') }}
                                    </h5>
                                    <div class="devotee-list-container">
                                        @foreach ($memberList as $key => $mname)
                                            <?php $isAdded = in_array($mname['aadhar'], $aadharList); ?>
                                            <div
                                                class="devotee-item d-flex justify-content-between align-items-center p-3 mb-2 flex-wrap">
                                                <!-- Left Section -->
                                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                                    <div class="devotee-initial bg-primary text-white rounded-circle d-flex justify-content-center align-items-center"
                                                        style="width: 40px; height: 40px; font-weight: bold;">
                                                        {{ strtoupper(substr($mname['name'] ?? '', 0, 1)) }}
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="font-weight-bold text-capitalize">{{ $mname['name'] }}
                                                        </div>
                                                        <div class="small text-muted"> {{ $mname['phone'] }}</div>
                                                        <div class="small text-muted">
                                                            {{ str_repeat('*', 8) . substr($mname['aadhar'], -4) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Right Section -->
                                                <div>
                                                    @if ($isAdded)
                                                        <span class="text-success font-weight-bold">
                                                            <i class="fa fa-check-circle"></i>
                                                        </span>
                                                    @else
                                                        <button
                                                            class="btn btn-sm btn-outline-primary member-list-add{{ $key }}"
                                                            onclick="AddUsers('{{ $key }}')">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                        <span
                                                            class="member-list-added{{ $key }} d-none text-success font-weight-bold">
                                                            <i class="fa fa-check-circle"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- Payment Button --}}
                    <div class="pay-card d-flex justify-content-between align-items-center  bg-white shadow-sm rounded">
                        <span>
                            <?php
                            $vipPlans = json_decode($getData['vip_plans'], true);
                            $vipDarshan = [];
                            if (!empty($vipPlans)) {
                                foreach ($vipPlans as $plan) {
                                    if ($plan['id'] == ($templeLead['package_id'] ?? 0)) {
                                        $vipDarshan = $plan['package'][0] ?? [];
                                        break;
                                    }
                                }
                            }
                            ?>
                            <h3 class="total_person_prices mb-1">
                                
                            @php
                                $totalPrice = ($templeLead['price'] ?? 0) 
                                            + ($templeLead['platform_fee'] ?? 0) 
                                            + ($templeLead['receipt_price'] ?? 0);
                            @endphp

                            @if ($totalPrice > 0)
                                {{ translate('total') }}:-
                                <span class="price-tag">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPrice), currencyCode: getCurrencyCode()) }}/-
                                </span>
                            @else
                                <span class="price-tag">{{ translate('Free Pass') }}</span>
                            @endif

                            </h3>
                            <p class="total_person_counts mb-0">
                                {{ translate('for') }} {{ $templeLead['people_qty'] ?? '' }} {{ translate('people') }}
                            </p>
                        </span>
                        <span class="how_to_show_buttons"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="devoteeModal" tabindex="-1" role="dialog" aria-labelledby="devoteeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header">
                    <h5 class="modal-title">Devotee Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form id="devoteeForm">
                        @if (($templeLead['Temple']['aadhaar_verify_status'] ?? 0) == 1)
                            <div class="form-group aadhar_number_form">
                                <!-- <label for="phone_number_id">{{ translate('phone') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone_number_id" value="{{ optional(auth('customer')->user())->phone ?? '' }}" pattern="\d{10}" oninput="this.value=this.value.slice(0,10)" placeholder="{{ translate('Enter Valid Phone Number') }}"> -->
                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                    <small class="text-primary">( *{{ translate('country_code_is_must_like_for_IND') }} 91
                                        )</small>
                                </label>
                                <input class="form-control text-align-direction phone-input-with-country-picker"
                                    type="tel" placeholder="{{ translate('enter_phone_number') }}"
                                    oninput="this.value=this.value.slice(0,10)">
                                <input type="hidden" class="country-picker-phone-number w-50" readonly>
                            </div>
                            <div class="form-group aadhar_number_form">
                                <label for="aadhar_Number1">{{ translate('Aadhar Number') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="aadhar_Number1" pattern="\d{12}"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 12)"
                                    placeholder="{{ translate('Enter 12-digit Aadhaar number') }}">
                            </div>
                            <div class="form-group aadhar_number_form">
                                <div class="row">
                                    <button type="button" class="btn btn-warning text-white top-send-buttons"
                                        onclick="aadharsendOtp()">{{ translate('verify') }}</button>
                                </div>
                            </div>
                            <div class="form-group aadhar_otp_form d-none">
                                <div>
                                    <label for="aadhar_otp">{{ translate('Aadhar OTP') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="aadhar_otp" pattern="\d{6}"
                                        oninput="this.value = this.value.replace(/\D/g, '').slice(0, 6)"
                                        placeholder="{{ translate('Enter Aadhaar OTP') }}">
                                    <input type="hidden" id="aadhar_request_id">
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-warning text-white"
                                        onclick="aadharverifyOtp()">{{ translate('OTP_verify') }}</button>
                                </div>
                            </div>
                        @endif
                        <input type="hidden" id="editIndex" value="">
                        @if (($templeLead['Temple']['aadhaar_verify_status'] ?? 0) == 0)
                            <div class="form-group valid_aadhar_use">
                                <label for="fullName">{{ translate('Full Name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fullName" required
                                    onkeyup="this.value = this.value.replace(/[^a-zA-Z\s]/g, '').replace(/\b\w/g, l => l.toUpperCase());">
                            </div>

                            <div class="form-group valid_aadhar_use" id="phone-group">
                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                    <small class="text-primary">( *{{ translate('country_code_is_must_like_for_IND') }} 91
                                        )</small>
                                </label>
                                <input class="form-control text-align-direction phone-input-with-country-picker"
                                    type="tel" id="person-number"
                                    placeholder="{{ translate('enter_phone_number') }}"
                                    oninput="this.value=this.value.slice(0,10)">
                                <input type="hidden" class="country-picker-phone-number w-50" readonly>
                            </div>
                            <div class="form-group valid_aadhar_use">
                                <label for="aadhar_Number">{{ translate('Aadhar Number') }}
                                    ({{ translate('optional') }})</label>
                                <input type="text" class="form-control" id="aadhar_Number" pattern="\d{12}"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 12)"
                                    placeholder="{{ translate('Enter 12-digit Aadhaar number') }}">
                            </div>
                        @endif
                    </form>
                </div>
                @if (($templeLead['Temple']['aadhaar_verify_status'] ?? 0) == 0)
                    <div class="modal-footer">
                        <button type="submit" form="devoteeForm"
                            class="btn btn-warning text-white">{{ translate('save') }}</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- wallet Use -->

    <div class="modal fade Wallet_payNow" tabindex="-1" role="dialog" aria-labelledby="WalletModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Use_a_Wallet') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <form action="{{ route('temple-darshan-pay-online-now') }}" class="pay-online-now" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 my-2">{{ translate('title') }}</div>
                            <div class="col-6 my-2">{{ translate('price') }}</div>
                            <div class="col-6 my-1">{{ translate('total_Amount') }}</div>
                            <div class="col-6 my-1"><span class="wallet_billing_amount"></span></div>
                            <div class="col-6 my-1">
                                {{ translate('Wallet_Amount') }}(<small>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: \App\Models\User::where('id', $templeLead['user_id'])->first()['wallet_balance'] ?? 0), currencyCode: getCurrencyCode()) }}</small>)<br>
                                <input type="checkbox" onclick="updateProductPrice()" class="wallet_checked"
                                    name="wallet_type" value="1"
                                    data-amount="{{ \App\Models\User::where('id', $templeLead['user_id'])->first()['wallet_balance'] ?? 0 }}">
                                <input type="hidden" name="lead_id" value="{{ $templeLead['id'] }}">
                            </div>
                            <div class="col-6 my-1"><span class="wallet_billing_cutting_amount"></span></div>
                            <div class="col-6 my-1">{{ translate('Remaining Amount') }}</div>
                            <div class="col-6 my-1"><span class="wallet_billing_remaining_amount"></span></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit"   class="btn btn-warning text-white name_changes">{{ translate('make_payment') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
@push('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script>
        let devotees = @json(json_decode($templeLead['people_info'] ?? '[]', true));
      
        function renderDevotees() {
            const container = $('#devotee-container');
            container.empty();
            $(".total_person_counts").text(`{{ translate('for') }} ${devotees.length} {{ translate('people') }}`);
            $('.how_to_show_buttons').empty();
            if (devotees.length > 0) {
                $('.how_to_show_buttons').append(
                    `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn-continue" onclick="Paymant_models('model')">Continue</button>`
                );
            } else {
                $('.how_to_show_buttons').append(`&nbsp;&nbsp;<button type="button" class="btn btn-outline-primary mt-3" data-toggle="modal" data-target="#devoteeModal" id="addDevoteeBtn" onclick="$('.aadhar_otp_form').addClass('d-none');$('.aadhar_number_form').removeClass('d-none');$('#aadhar_request_id').val('');$('#aadhar_otp').val(''); $('#aadhar_Number').attr('readonly',false);phoneNumberSet('{{ optional(auth('customer')->user())->phone ?? '' }}');">
                 <i class="fa fa-plus"></i>  {{ translate('Add New Member') }}
            </button>`);
            }
            <?php
                $totalBase = ($vipDarshan['price'] ?? 0)  + ($vipDarshan['platform_fee'] ?? 0)  + ($vipDarshan['receipt_price'] ?? 0);
            ?>
            var manAmounts = parseFloat("{{ $totalBase }}");
            <?php if ($totalBase > 0) { ?>
                $(".total_person_prices").html(`{{ translate('total') }}:- 
                    <span class="price-tag"> 
                        ${(devotees.length * manAmounts).toLocaleString("en-US", {
                            style: "currency",
                            currency: "{{ getCurrencyCode() }}"
                        })}
                    </span>`);
            <?php } else { ?>
                $(".total_person_prices").html("<span class='price-tag'>{{ translate('Free Pass') }}</span>");
            <?php } ?>
            devotees.forEach((d, index) => {
                const initial = d.fullName.charAt(0).toUpperCase();
                container.append(`
                    <div class="devotee-item d-flex justify-content-between align-items-center p-2 border rounded mb-2">
                        <!-- Left Section -->
                        <div class="d-flex align-items-center">
                            <div class="devotee-initial bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" 
                                style="width: 40px; height: 40px; font-weight: bold;">
                                ${initial}
                            </div>
                            <div class="ml-3">
                                <div class="font-weight-bold text-capitalize">
                                    ${d.fullName}
                                </div>
                                <div class="small text-muted">
                                    ${index === 0 && d.phone ? `${d.phone}<br>` : ''}
                                    ${'*'.repeat(8) + d.aadhar.slice(-4)}
                                </div>
                            </div>
                        </div>
                        <!-- Right Section -->
                        <div class="d-flex align-items-center">
                            ${index > 0 
                                ? `<button class="btn btn-sm btn-outline-danger ml-2 remove-devotee" data-index="${index}">
                                            <i class="fa fa-minus fa-sm"></i>
                                    </button>` 
                                : ''}

                            <!-- Edit pencil moved here -->
                            <a class="edit-devotee ml-2 text-primary" 
                            data-index="${index}" 
                            style="cursor: pointer;"
                            onclick="$('.aadhar_otp_form').addClass('d-none');
                                        $('.aadhar_number_form').removeClass('d-none');
                                        $('#aadhar_request_id').val('');
                                        $('#aadhar_otp').val('');
                                        $('#user_aadhar_id').val('${d.aadhar}');">
                                <i class="fa fa-pencil fa-sm"></i>
                            </a>
                        </div>
                    </div>
                `);
            });
        }

        $('#addDevoteeBtn').on('click', function() {
            $('#devoteeForm')[0].reset();
            phoneNumberSet("{{ optional(auth('customer')->user())->phone ?? '' }}");
            $('#editIndex').val('');
            $('#devoteeModal .modal-title').text("{{ translate('Add New Member') }}");

            if (devotees.length === 0) {
                $('#phone-group').show();
                $('.phone-input-with-country-picker').prop('required', true);
            } else {
                $('#phone-group').hide();
                $('.phone-input-with-country-picker').prop('required', false);
            }
        });

        $('#devoteeForm').submit(function(e) {
            e.preventDefault();

            const fullName = $('#fullName').val().trim();
            const phone = $('.country-picker-phone-number').val().trim();
            const aadhar = $('#aadhar_Number').val().trim();
            const editIndex = $('#editIndex').val();
            if (!fullName) {
                toastr.error("Name are required.");
                return;
            }
            let existingDevotee = devotees.find((devotee, index) => {
                return devotee.aadhar === aadhar && index != editIndex;
            });
           

            if (existingDevotee && aadhar !== '') {
                toastr.error('This devotee already exists.');
                return false;
            }
            const data = {
                fullName,
                phone: (devotees.length === 0 || editIndex == 0) ? phone : '',
                aadhar,
                verify: 0,
            };

            if (editIndex === '') {
                devotees.push(data);
            } else {
                devotees[editIndex] = data;
            }

            $('#devoteeModal').modal('hide');
            renderDevotees();
            updateLeads();
        });

        // Edit
        $(document).on('click', '.edit-devotee', function() {
            const index = $(this).data('index');
            const d = devotees[index];
            $('#fullName').val(d.fullName);
            $('#aadhar_Number').val(d.aadhar);
            if (d.verify == 1) {
                $('#aadhar_Number').attr('readonly', true);
            }
            $('#aadhar_Number1').val(d.aadhar);
            $('#editIndex').val(index);
            phoneNumberSet(d.phone);
            if (index === 0) {
                $('#phone-group').show();
                $('.country-picker-phone-number').prop('required', true);

            } else {
                $('#phone-group').hide();
                $('.country-picker-phone-number').prop('required', false);

            }

            $('#devoteeModal .modal-title').text("{{ translate('Edit Devotee') }}");
            $('#devoteeModal').modal('show');
        });

        // Remove
        $(document).on('click', '.remove-devotee', function() {
            const index = $(this).data('index');
            devotees.splice(index, 1);
            renderDevotees();
            updateLeads();
        });

        renderDevotees();
    </script>
    <script>
        // document.addEventListener('contextmenu', function(e) {
        //     e.preventDefault();
        // });
        document.onkeydown = function(e) {
            if (e.keyCode == 123) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
                return false;
            }
            if (e.ctrlKey && e.keyCode == 85) {
                return false;
            }
            if (e.ctrlKey && e.shiftKey && e.keyCode == 67) {
                return false;
            }
        };
    </script>
    <script>
        renderDevotees();

        function updateLeads() {
            var manAmounts = parseFloat("{{ $vipDarshan['price'] ?? 0 }}");
            var receiptAmounts = parseFloat("{{ $vipDarshan['receipt_price'] ?? 0 }}");
            var platformBaseAmounts = parseFloat("{{ $vipDarshan['platform_base_price'] ?? 0 }}");
            var platformFeeAmounts = parseFloat("{{ $vipDarshan['platform_fee'] ?? 0 }}");
            var platformAmounts = parseFloat("{{ $vipDarshan['platform_gst'] ?? 0 }}");
            $.ajax({
                url: "{{ route('vip-darshan-lead-person-update') }}",
                data: {
                    "people": JSON.stringify(devotees),
                    'lead_id': "{{ $templeLead['id'] }}",
                    'price': (devotees.length * manAmounts),
                    'receipt_price': (devotees.length * receiptAmounts),
                    'platform_base_price': (devotees.length * platformBaseAmounts),
                    'platform_fee': (devotees.length * platformFeeAmounts),
                    'platform_gst': (devotees.length * platformAmounts),
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.add-member-list-div').load(location.href + ' .add-member-list-div > *');
                }
            });
        }

        function Paymant_models() {
            var walletAmount = parseFloat(
                "{{ \App\Models\User::where('id', $templeLead['user_id'])->first()['wallet_balance'] ?? 0 }}") || 0;
            if (Number("{{ $vipDarshan['price'] ?? 0 }}") <= 0) {
                $(`.wallet_checked`).prop('checked', true);
                updateProductPrice();
                $('.pay-online-now').submit();
            } else if (walletAmount > 0) {
                $(`.wallet_checked`).prop('checked', true);
                updateProductPrice();
                $('.Wallet_payNow').modal('show');
            } else {
                $(`.wallet_checked`).prop('checked', false);
                updateProductPrice();
                $('.pay-online-now').submit();
            }
        }

        function updateProductPrice() {
            <?php
                $manAmount = ($vipDarshan['price'] ?? 0)  + ($vipDarshan['platform_fee'] ?? 0)  + ($vipDarshan['receipt_price'] ?? 0);
            ?>
            var manAmounts = parseFloat("{{ $manAmount }}");
            var walletAmount = parseFloat(
                "{{ \App\Models\User::where('id', $templeLead['user_id'])->first()['wallet_balance'] ?? 0 }}") || 0;
            var billingAmount = (devotees.length * manAmounts);
            let isChecked = $(`.wallet_checked`).is(':checked');
            if (isChecked) {
                $(`.wallet_checked`).prop('checked', true);
                $('.wallet_billing_amount').text((billingAmount).toLocaleString("en-US", {
                    style: "currency",
                    currency: "{{ getCurrencyCode() }}"
                }));
                if (billingAmount > walletAmount) {
                    $('.wallet_billing_remaining_amount').text(((billingAmount) - walletAmount).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{ getCurrencyCode() }}"
                    }));
                    $('.wallet_billing_cutting_amount').text(((walletAmount)).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{ getCurrencyCode() }}"
                    }));
                    $('.name_changes').text("{{ translate('make_payment') }}");
                } else {
                    $('.wallet_billing_remaining_amount').text((0).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{ getCurrencyCode() }}"
                    }));
                    $('.wallet_billing_cutting_amount').text(((billingAmount)).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{ getCurrencyCode() }}"
                    }));
                    $('.name_changes').text("{{ translate('wallet_payment') }}");
                }
            } else {
                $(`.wallet_checked`).prop('checked', false);

                $('.wallet_billing_amount').text((billingAmount).toLocaleString("en-US", {
                    style: "currency",
                    currency: "{{ getCurrencyCode() }}"
                }));
                $('.wallet_billing_remaining_amount').text((billingAmount).toLocaleString("en-US", {
                    style: "currency",
                    currency: "{{ getCurrencyCode() }}"
                }));
                $('.wallet_billing_cutting_amount').text((0).toLocaleString("en-US", {
                    style: "currency",
                    currency: "{{ getCurrencyCode() }}"
                }));
                $('.name_changes').text("{{ translate('make_payment') }}");
            }
        }
    </script>
    <script>
        function aadharsendOtp() {
            let aadhaarNumber = $('#aadhar_Number1').val().trim();
            let phoneN = $('.country-picker-phone-number').val().trim();
            if (phoneN.length < 8) {
                phoneN = '';
            }
            let aadhaarRegex = /^\d{12}$/;
            if (!aadhaarRegex.test(aadhaarNumber)) {
                toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
                return;
            }
            let editIndex1 = $('#editIndex').val();
            let isDuplicate = devotees.some((devotee, index) => {
                return devotee.aadhar == aadhaarNumber && index != editIndex1;
            });
            // --- Check in DOM (already rendered list) ---
            let duplicateInDOM = false;
            $('#devotee-container .devotee-item').each(function () {
                const existingAadhar = $(this).data('aadhar');
                const existingIndex = $(this).data('index'); // assuming you stored index in DOM

                if (existingAadhar === aadhaarNumber && editIndex1 === '') {
                    duplicateInDOM = true;
                    return false; // break loop
                }
            });

            if ((isDuplicate && aadhaarNumber !== '') || duplicateInDOM) {
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
                    $('.top-send-buttons').attr('disabled', true);
                },
                success: function(data) {
                    if (data.status == 1) {
                        toastr.success(data.message);
                        $('.aadhar_otp_form').removeClass('d-none');
                        $('.aadhar_number_form').addClass('d-none');
                        $('#aadhar_request_id').val(data.request_id);
                    } else if (data.status == 2) {
                        let editIndex1 = $('#editIndex').val();
                        $('#devoteeModal').modal('hide');
                        let isDuplicate = devotees.some((devotee, index) => {
                            return devotee.aadhar == data.data.aadhar && index != editIndex1;
                        });
                        if (isDuplicate && data.data.aadhar != '') {
                            toastr.error('This Aadhaar number already exists.');
                            return;
                        }
                        let nextId = 1;
                        if (devotees.length > 0) {
                            const ids = devotees.map(d => d.id || 0);
                            nextId = Math.max(...ids) + 1;
                        }
                        toastr.success(data.message);
                        let data_messgae = {
                            id: nextId,
                            fullName: data.data.name,
                            phone: (devotees.length === 0 || editIndex1 == 0) ? (phoneN ?? data.data.phone) : '',
                            aadhar: data.data.aadhar,
                            address: data.data.address,
                            image: data.data.image,
                            verify: data.data.verify,
                        };
                        if (editIndex1 === '') {
                            devotees.push(data_messgae);
                        } else {
                            devotees[editIndex1] = data_messgae;
                        }
                        renderDevotees();
                        updateLeads();
                    } else {
                        toastr.error(data.message);
                        $('#aadhar_request_id').val(data.request_id);
                        $('.aadhar_otp_form').addClass('d-none');
                        $('.aadhar_number_form').removeClass('d-none');
                    }
                },
                complete: function() {
                    $('.top-send-buttons').attr('disabled', false);
                }
            });
        }

        function aadharverifyOtp() {
            let aadhaarNumber = $('#aadhar_Number1').val().trim();
            let aadhaarRegex = /^\d{12}$/;
            if (!aadhaarRegex.test(aadhaarNumber)) {
                toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
                return;
            }
            let otp = $('#aadhar_otp').val().trim();
            let otpRegex = /^\d{6}$/;
            if (!otpRegex.test(otp)) {
                toastr.error('Please Enter a valid 6-digit OTP.');
                return;
            }
            $.ajax({
                url: "{{ url('api/v1/darshan/aadhar-otp-verify') }}",
                data: {
                    "otp": otp,
                    'request_id': $('#aadhar_request_id').val(),
                    'phone_no': $('.country-picker-phone-number').val(),
                    "user_id": "{{ auth('customer')->id() }}",
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
                        let editIndex1 = $('#editIndex').val();
                        let nextId = 1;
                        if (devotees.length > 0) {
                            nextId = Math.max(...devotees.map(d => d.id)) + 1;
                        }
                        let data_messgae = {
                            id: nextId,
                            fullName: data.data1.name,
                            phone: (devotees.length === 0 || editIndex1 == 0) ? data.data1.phone : '',
                            aadhar: data.data1.aadhar,
                            address: data.data1.address,
                            image: data.data1.image,
                            verify: data.data1.verify,
                        };
                        console.log('data_messgae');
                        if (editIndex1 === '') {
                            devotees.push(data_messgae);
                        } else {
                            devotees[editIndex1] = data_messgae;
                        }
                        $('#devoteeModal').modal('hide');
                        renderDevotees();
                        updateLeads();
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
        let memberList = @json($memberList);

        function AddUsers(id) {
            var userData = memberList[id];
            let name = userData.name;
            let address = userData.address;
            let image = userData.image;
            let phone = userData.phone || "";
            if (phone.length < 9) {
                phone = "{{ optional(auth('customer')->user())->phone ?? '' }}";
            }
            let Aadhar = userData.aadhar;
            let aadhaarRegex = /^\d{12}$/;
            if (!aadhaarRegex.test(Aadhar)) {
                toastr.error('Please Enter a valid 12-digit Aadhaar Number.');
                return false;
            }
            let isDuplicate = devotees.some((devotee, index) => {
                return devotee.aadhar == Aadhar;
            });
            if (isDuplicate && Aadhar != '') {
                toastr.error('This Aadhaar number already exists.');
                $(`.member-list-add${id}`).addClass('d-none');
                $(`.member-list-added${id}`).removeClass('d-none');
                return false;
            }
            let nextId = 1;
            if (devotees.length > 0) {
                nextId = devotees[devotees.length - 1].id + 1;
            }
            let data_messgae = {
                id: nextId,
                fullName: name,
                phone: phone,
                address: address,
                image: image,
                aadhar: Aadhar,
                verify: 1,
            }
            devotees.push(data_messgae);
            $(`.member-list-add${id}`).addClass('d-none');
            $(`.member-list-added${id}`).removeClass('d-none');
            renderDevotees();
            updateLeads();
        }

        function phoneNumberSet(phone) {
            var phoneInput = document.querySelector(".phone-input-with-country-picker");
            var iti = window.intlTelInputGlobals.getInstance(phoneInput);
            iti.setNumber(phone);
            $('.country-picker-phone-number').val(phone);
        }
    </script>
@endpush