@extends('layouts.temple-front-end.app1')
@section('title', 'temple booking')

@push('css_or_js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
         @media (max-width: 767px) {
                  .mobile-submit-wrapper {
                        position: fixed;
                        bottom: -15px;
                        left: 0;
                        width: 100%;
                        background: #fff;
                        padding: 10px;
                        box-shadow: 0 -2px 10px rgba(0,0,0,0.15);
                        z-index: 999;
                  }

                  .mobile-submit-wrapper .btn {
                        width: 90%;
                  }

                  .hide-mobile-submit {
                        display: none !important;
                  }
                  .footer-section{
                        display: none !important;
                  }
                  #contact{
                        margin-top : 0px !important;
                  }
                  .mobile-action-bar{
                    display: none !important;
                  }
            }
            #pay-detail {
                  background: #fff;
                  padding: 14px;
                  border-radius: 12px;
                  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                  font-size: 14px;
            }

            #pay-detail li {
                  padding: 6px 0;
            }

            #pay-detail .total-amount {
                  font-size: 16px;
                  font-weight: 700;
                  color: #e65100;
            }
            .darshan-timeslot-container {
                  display: grid;
                  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                  gap: 10px;
                  margin-top: 10px;
            }

            .time-slot-btn {
                  background: #fff;
                  border: 1.5px solid #ff9800;
                  border-radius: 10px;
                  padding: 10px 8px;
                  font-size: 13px;
                  font-weight: 600;
                  color: #333;
                  text-align: center;
                  cursor: pointer;
                  transition: all 0.25s ease;
            }

            .time-slot-btn small {
                  display: block;
                  margin-top: 4px;
                  font-size: 11px;
                  color: #777;
                  font-weight: 500;
            }

            .time-slot-btn:hover {
                  background: #fff3e0;
                  border-color: #fb8c00;
            }

            .time-slot-btn.active {
                  background: linear-gradient(135deg, #ff9800, #f57c00);
                  color: #fff;
                  border-color: transparent;
            }

            .time-slot-btn.active small {
                  color: #fff;
            }
    </style>
@endpush
@section('content')
   <!-- start get-in-touch section -->
   <section class="get-in-touch" id="contact" style="margin-top: 160px;">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-12 margin-top-135 margin-top-30">
                    <div class="form-card">
                        <h5>Book Your {{ ucfirst($serviceType) }}</h5>
                        <p>Fill the form below and we'll get back to you within 24 hours</p>

                        @if ($digital_payment['status'] == 1)
                            @foreach ($payment_gateways_list as $payment_gateway)
                                <form method="POST" class="digital_payment" id="{{ $payment_gateway->key_name }}_form"
                                    action="{{ route('mandirservice.paymentRequestMandir') }}">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="{{ $payment_gateway->key_name }}">
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
                                        value="{{ route('mandirservice.mandirpaymentRequest') }}">
                                    <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                        <input type="radio" id="{{ $payment_gateway->key_name }}" name="online_payment"
                                            class="form-check-input custom-radio" value="{{ $payment_gateway->key_name }}"
                                            hidden>
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

                                    <input type="hidden" name="temple_id" value="{{ $package->temple->id }}">
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <input type="hidden" name="service_type" value="{{ $serviceType }}">
                                    <input type="hidden" name="aadhar_number" value="{{ $aadharNumber }}">

                                    @if ($serviceType == 'puja')
                                        @include('web-views.temple.partials.mandir._puja_form')
                                    @elseif ($serviceType == 'darshan')
                                        @include('web-views.temple.partials.mandir._darshan_form')
                                    @elseif ($serviceType == 'bhojan')
                                        @include('web-views.temple.partials.mandir._bhojan_form')
                                    @elseif ($serviceType == 'locker')
                                        @include('web-views.temple.partials.mandir._locker_form')
                                    @endif

                                </form>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>
   

 


@endsection
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/mixitup@3/dist/mixitup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    @if ($serviceType == 'puja')
        <script>
            const priceData = {
                basePrice: {{ $package->base_price }},
                platformFee: {{ $package->platform_fee_percentage }},
                receiptFee: {{ $package->receipt_fee_percentage }},
                gstRate: {{ $package->gst_rate }}
            };

            // $(document).ready(function() {

                function calculatePrice() {
                    let customers = parseInt($('#total-customer').val());

                    let basePrice = priceData.basePrice;
                    let platformFee = priceData.platformFee;
                    let receiptFee = priceData.receiptFee;
                    let gst = (basePrice * priceData.gstRate) / 100;

                    let pricePerCustomer = basePrice + platformFee + receiptFee + gst;
                    let totalAmount = pricePerCustomer * customers;

                    $('#pay-detail').html(`
                        <li class="d-flex justify-content-between">
                            <span>Base Price <small>(per customer)</small></span>
                            <span>₹${basePrice}</span>
                        </li>

                        <li class="d-flex justify-content-between">
                            <span>Platform Fee <small>(per customer)</small></span>
                            <span>₹${platformFee}</span>
                        </li>

                        <li class="d-flex justify-content-between">
                            <span>Receipt Fee <small>(per customer)</small></span>
                            <span>₹${receiptFee}</span>
                        </li>

                        <li class="d-flex justify-content-between">
                            <span>GST (${priceData.gstRate}% per customer)</span>
                            <span>₹${gst.toFixed(2)}</span>
                        </li>

                        <li class="border-top pt-2 mt-2 d-flex justify-content-between fw-bold">
                            <span>Price per Customer</span>
                            <span>₹${pricePerCustomer.toFixed(2)}</span>
                        </li>

                        <li class="d-flex justify-content-between">
                            <span>Total Customers</span>
                            <span>${customers}</span>
                        </li>

                        <li class="border-top pt-2 mt-2 d-flex justify-content-between total-amount">
                            <span>Total Amount</span>
                            <span>₹${totalAmount.toFixed(2)}</span>
                        </li>
                    `);

                }

                // On page load
                calculatePrice();

                // On dropdown change
                // $('#total-customer').on('change', function() {
                //     calculatePrice();
                // });
            // });

            // on button click
            function changeCustomer(no){
                no = Number(no);
                
                let prevCustomers = Number($('#total-customer').val()) || 1;
                let updateCustomers = prevCustomers + no;

                // error condition
                if (updateCustomers < 1 || updateCustomers > 5) {
                    toastr.error('Customer limit exhausted');
                    return;
                }

                $('#total-customer').val(updateCustomers);
                calculatePrice();
            }
        </script>
    @elseif ($serviceType == 'darshan')
        <script>
            const priceData = {
                basePrice: {{ $package->base_price }},
                platformFee: {{ $package->platform_fee_percentage }},
                receiptFee: {{ $package->receipt_fee_percentage }},
                gstRate: {{ $package->gst_rate }}
            };

            function calculatePrice() {

                let customers = parseInt($('#total-customer').val());

                // 🛑 Prevent NaN issues
                if (isNaN(customers) || customers === '') {
                    $('#pay-detail').html(`
                        <li class="text-muted">Please select customers</li>
                    `);
                    return;
                }

                let basePrice = parseFloat(priceData.basePrice);
                let platformFee = parseFloat(priceData.platformFee);
                let receiptFee = parseFloat(priceData.receiptFee);
                let gst = (basePrice * priceData.gstRate) / 100;

                let pricePerCustomer = basePrice + platformFee + receiptFee + gst;
                let totalAmount = pricePerCustomer * customers;

                $('#pay-detail').html(`
                    <li class="d-flex justify-content-between">
                        <span>Base Price <small>(per customer)</small></span>
                        <span>₹${basePrice.toFixed(2)}</span>
                    </li>

                    <li class="d-flex justify-content-between">
                        <span>Platform Fee <small>(per customer)</small></span>
                        <span>₹${platformFee.toFixed(2)}</span>
                    </li>

                    <li class="d-flex justify-content-between">
                        <span>Receipt Fee <small>(per customer)</small></span>
                        <span>₹${receiptFee.toFixed(2)}</span>
                    </li>

                    <li class="d-flex justify-content-between">
                        <span>GST (${priceData.gstRate}% per customer)</span>
                        <span>₹${gst.toFixed(2)}</span>
                    </li>

                    <li class="divider"></li>

                    <li class="d-flex justify-content-between fw-bold">
                        <span>Price per Customer</span>
                        <span>₹${pricePerCustomer.toFixed(2)}</span>
                    </li>

                    <li class="d-flex justify-content-between">
                        <span>Total Customers</span>
                        <span>${customers}</span>
                    </li>

                    <li class="d-flex justify-content-between total-amount">
                        <span>Total Amount</span>
                        <span>₹${totalAmount.toFixed(2)}</span>
                    </li>
                `);

            }

            // 🔹 Generic function to load slots for both darshan & bhojan
            function loadTimeSlotsGeneric(section) {
                const $section = $('#' + section + '-section');
                const $package = $section.find('#' + section + '-package-id');
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
                                .addClass('btn button btn-outline-primary btn-sm m-1 time-slot-btn')
                                .attr('data-id', slot.id)
                                .attr('data-available', slot.available)
                                .text(slot.time + ' (Available: ' + slot.available + ')');

                            $slotContainer.append(btn);
                        });

                        if ($slotContainer.children().length === 0) {
                            $slotContainer.html(
                                '<div class="text-muted small">No future slots available today</div>');
                        } else {
                            const $firstSlot = $slotContainer.find('.time-slot-btn').first();
                            if ($firstSlot.length) {
                                $firstSlot.trigger('click');
                            }
                        }
                    },
                    error: function() {
                        $slotContainer.html('<div class="text-danger small">Failed to fetch slots</div>');
                    }
                });
            }

            // 🧭 Handle slot selection
            $(document).on('click', '.time-slot-btn', function(e) {
                e.preventDefault();

                const $btn = $(this);
                const $container = $btn.closest('[id$="-section"]');
                const section = $container.attr('id').replace('-section', '');

                $container.find('.time-slot-btn')
                    .removeClass('active btn-primary')
                    .addClass('btn-outline-primary');

                $btn.removeClass('btn-outline-primary').addClass('btn-primary active');

                const slotId = $btn.data('id');
                const available = parseInt($btn.data('available')) || 0;

                // ✅ store available limit
                $container.attr('data-available', available);

                $container.find('.' + section + '-timeslot-id').val(slotId);

                calculatePrice();

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
            function changeCustomer(no){

                no = Number(no);

                const $darshanSection = $('#darshan-section');

                const available = parseInt($darshanSection.attr('data-available')) || 0;
                const slotId = $('.darshan-timeslot-id').val();

                if (!slotId) {
                    toastr.error('Please select a time slot first.');
                    $(this).val(1);
                    return;
                }
                
                let prevCustomers = Number($('#total-customer').val()) || 1;
                let updateCustomers = prevCustomers + no;

                if (updateCustomers > available) {
                    toastr.error('Only ' + available + ' slots are available for this time.');
                    return;
                }

                // error condition
                if (updateCustomers < 1 || updateCustomers > 5) {
                    toastr.error('Customer limit exhausted');
                    return;
                }

                $('#total-customer').val(updateCustomers);
                calculatePrice();
            }

            // $(document).on('change', '#total-customer', function() {

            //     const totalCustomer = parseInt($(this).val());
            //     const $darshanSection = $('#darshan-section');

            //     const available = parseInt($darshanSection.attr('data-available')) || 0;
            //     const slotId = $('.darshan-timeslot-id').val();

            //     if (!slotId) {
            //         toastr.error('Please select a time slot first.');
            //         $(this).val(1);
            //         return;
            //     }

            //     if (totalCustomer > available) {
            //         toastr.error('Only ' + available + ' slots are available for this time.');
            //         $(this).val(available > 0 ? available : 1);
            //     }
            //     calculatePrice();
            // });


            // 🔄 Auto-load slots on package or date change for both types
            $(document).on('change', '.darshan-date-select', function() {
                let darshanDate = $(this).val();
                if (!darshanDate) return;

                // reset selections
                $('#total-customer').val(1);
                $('.darshan-timeslot-id').val('');
                $('#darshan-section').removeAttr('data-available');

                loadTimeSlotsGeneric('darshan');
                calculatePrice();
            });


            $(document).ready(function() {
                // auto load slots on page load
                if ($('.darshan-date-select').val()) {
                    loadTimeSlotsGeneric('darshan');
                }
            });
        </script>
    @elseif ($serviceType == 'bhojan')
        <script>
            const priceData = {
                basePrice: {{ $package->base_price }},
                platformFee: {{ $package->platform_fee_percentage }},
                receiptFee: {{ $package->receipt_fee_percentage }},
                gstRate: {{ $package->gst_rate }}
            };

            function calculatePrice() {

                let customers = parseInt($('#total-customer').val());

                // 🛑 Prevent NaN issues
                if (isNaN(customers) || customers === '') {
                    $('#pay-detail').html(`
                        <li class="text-muted">Please select customers</li>
                    `);
                    return;
                }

                let basePrice = parseFloat(priceData.basePrice);
                let platformFee = parseFloat(priceData.platformFee);
                let receiptFee = parseFloat(priceData.receiptFee);
                let gst = (basePrice * priceData.gstRate) / 100;

                let pricePerCustomer = basePrice + platformFee + receiptFee + gst;
                let totalAmount = pricePerCustomer * customers;

                $('#pay-detail').html(`
                    <li class="d-flex justify-content-between">
                        <span>Base Price <small>(per customer)</small></span>
                        <span>₹${basePrice.toFixed(2)}</span>
                    </li>

                    <li class="d-flex justify-content-between">
                        <span>Platform Fee <small>(per customer)</small></span>
                        <span>₹${platformFee.toFixed(2)}</span>
                    </li>

                    <li class="d-flex justify-content-between">
                        <span>Receipt Fee <small>(per customer)</small></span>
                        <span>₹${receiptFee.toFixed(2)}</span>
                    </li>

                    <li class="d-flex justify-content-between">
                        <span>GST (${priceData.gstRate}%) <small>(per customer)</small></span>
                        <span>₹${gst.toFixed(2)}</span>
                    </li>

                    <li class="divider"></li>

                    <li class="d-flex justify-content-between fw-bold">
                        <span>Price per Customer</span>
                        <span>₹${pricePerCustomer.toFixed(2)}</span>
                    </li>

                    <li class="d-flex justify-content-between">
                        <span>Total Customers</span>
                        <span>${customers}</span>
                    </li>

                    <li class="d-flex justify-content-between total-amount">
                        <span>Total Amount</span>
                        <span>₹${totalAmount.toFixed(2)}</span>
                    </li>
                `);

            }

            // 🔹 Generic function to load slots for both bhojan & bhojan
            function loadTimeSlotsGeneric(section) {
                const $section = $('#' + section + '-section');
                const $package = $section.find('#' + section + '-package-id');
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
                                .addClass('btn button btn-outline-primary btn-sm m-1 time-slot-btn')
                                .attr('data-id', slot.id)
                                .attr('data-available', slot.available)
                                .text(slot.time + ' (Available: ' + slot.available + ')');

                            $slotContainer.append(btn);
                        });

                        if ($slotContainer.children().length === 0) {
                            $slotContainer.html(
                                '<div class="text-muted small">No future slots available today</div>');
                        } else {
                            const $firstSlot = $slotContainer.find('.time-slot-btn').first();
                            if ($firstSlot.length) {
                                $firstSlot.trigger('click');
                            }
                        }
                    },
                    error: function() {
                        $slotContainer.html('<div class="text-danger small">Failed to fetch slots</div>');
                    }
                });
            }

            // 🧭 Handle slot selection
            $(document).on('click', '.time-slot-btn', function(e) {
                e.preventDefault();

                const $btn = $(this);
                const $container = $btn.closest('[id$="-section"]');
                const section = $container.attr('id').replace('-section', '');

                $container.find('.time-slot-btn')
                    .removeClass('active btn-primary')
                    .addClass('btn-outline-primary');

                $btn.removeClass('btn-outline-primary').addClass('btn-primary active');

                const slotId = $btn.data('id');
                const available = parseInt($btn.data('available')) || 0;

                // ✅ store available limit
                $container.attr('data-available', available);

                $container.find('.' + section + '-timeslot-id').val(slotId);

                calculatePrice();

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

            // ✅ Validation for bhojan customers
            function changeCustomer(no){

                no = Number(no);

                const $bhojanSection = $('#bhojan-section');

                const available = parseInt($bhojanSection.attr('data-available')) || 0;
                const slotId = $('.bhojan-timeslot-id').val();

                if (!slotId) {
                    toastr.error('Please select a time slot first.');
                    $(this).val(1);
                    return;
                }

                let prevCustomers = Number($('#total-customer').val()) || 1;
                let updateCustomers = prevCustomers + no;

                if (updateCustomers > available) {
                    toastr.error('Only ' + available + ' slots are available for this time.');
                    return;
                }

                // error condition
                if (updateCustomers < 1 || updateCustomers > 5) {
                    toastr.error('Customer limit exhausted');
                    return;
                }

                $('#total-customer').val(updateCustomers);
                calculatePrice();

            }
            // $(document).on('change', '#total-customer', function() {

            //     const totalCustomer = parseInt($(this).val());
            //     const $bhojanSection = $('#bhojan-section');

            //     const available = parseInt($bhojanSection.attr('data-available')) || 0;
            //     const slotId = $('.bhojan-timeslot-id').val();

            //     if (!slotId) {
            //         toastr.error('Please select a time slot first.');
            //         $(this).val(1);
            //         return;
            //     }

            //     if (totalCustomer > available) {
            //         toastr.error('Only ' + available + ' slots are available for this time.');
            //         $(this).val(available > 0 ? available : 1);
            //     }
            //     calculatePrice();
            // });


            // 🔄 Auto-load slots on package or date change for both types
            $(document).on('change', '.bhojan-date-select', function() {
                let bhojanDate = $(this).val();
                if (!bhojanDate) return;

                // reset selections
                $('#total-customer').val(1);
                $('.bhojan-timeslot-id').val('');
                $('#bhojan-section').removeAttr('data-available');

                loadTimeSlotsGeneric('bhojan');
                calculatePrice();
            });


            $(document).ready(function() {
                // auto load slots on page load
                if ($('.bhojan-date-select').val()) {
                    loadTimeSlotsGeneric('bhojan');
                }
            });
        </script>
    @elseif ($serviceType == 'locker')
        <script>
            const priceData = {
                basePrice: {{ $package->base_price }},
                platformFee: {{ $package->platform_fee_percentage }},
                receiptFee: {{ $package->receipt_fee_percentage }},
                gstRate: {{ $package->gst_rate }}
            };

            $(document).ready(function() {

                function calculatePrice() {
                    let customers = 1;

                    let basePrice = priceData.basePrice;
                    let platformFee = priceData.platformFee;
                    let receiptFee = priceData.receiptFee;
                    let gst = (basePrice * priceData.gstRate) / 100;

                    let pricePerCustomer = basePrice + platformFee + receiptFee + gst;
                    let totalAmount = pricePerCustomer * customers;

                    $('#pay-detail').html(`
                        <li class="d-flex justify-content-between">
                            <span>Base Price <small>(per customer)</small></span>
                            <span>₹${basePrice}</span>
                        </li>

                        <li class="d-flex justify-content-between">
                            <span>Platform Fee <small>(per customer)</small></span>
                            <span>₹${platformFee}</span>
                        </li>

                        <li class="d-flex justify-content-between">
                            <span>Receipt Fee <small>(per customer)</small></span>
                            <span>₹${receiptFee}</span>
                        </li>

                        <li class="d-flex justify-content-between">
                            <span>GST (${priceData.gstRate}%) <small>(per customer)</small></span>
                            <span>₹${gst.toFixed(2)}</span>
                        </li>

                        <li class="divider"></li>

                        <li class="d-flex justify-content-between fw-bold">
                            <span>Price per Customer</span>
                            <span>₹${pricePerCustomer.toFixed(2)}</span>
                        </li>

                        <li class="d-flex justify-content-between">
                            <span>Total Customers</span>
                            <span>${customers}</span>
                        </li>

                        <li class="d-flex justify-content-between total-amount">
                            <span>Total Amount</span>
                            <span>₹${totalAmount.toFixed(2)}</span>
                        </li>
                    `);

                }

                // On page load
                calculatePrice();
            });
        </script>
        
    @endif
    
@endpush
