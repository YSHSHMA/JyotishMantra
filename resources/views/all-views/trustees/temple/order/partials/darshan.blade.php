<form id="darshanForm" method="POST">
    @csrf
    <input type="hidden" name="temple_id" value="{{ $temple->id }}">
    <input type="hidden" name="type" value="{{ $plan['name'] }}">

    <div class="row">
        {{-- Date Selection --}}
        <div class="col-md-12 pb-2">
            <label>Select Date</label>
            @php
            $today = \Carbon\Carbon::today();
            $dates = [
            ['label' => 'Today', 'value' => $today->format('Y-m-d'), 'display' => $today->format('d/m/Y')],
            ['label' => 'Tomorrow', 'value' => $today->copy()->addDay()->format('Y-m-d'), 'display' => $today->copy()->addDay()->format('d/m/Y')],
            ['label' => 'Next Day', 'value' => $today->copy()->addDays(2)->format('Y-m-d'), 'display' => $today->copy()->addDays(2)->format('d/m/Y')],
            ];
            @endphp
            <div class="btn-group w-100" role="group" aria-label="Date selection">
                @foreach($dates as $index => $date)
                <button type="button"
                    class="btn darshan-date-btn {{ $index === 0 ? 'btn-primary active' : 'btn-outline-primary' }}"
                    data-value="{{ $date['value'] }}">
                    {{ $date['label'] }} ({{ $date['display'] }})
                </button>
                @endforeach
            </div>
            <input type="hidden" name="date" id="selected_darshan_date" value="{{ $today->format('Y-m-d') }}">
        </div>

        <div class="col-md-6">
            <label>Darshan Type</label>
            <select name="package_id" id="darshan_package_id" class="form-control firstoption darshan-tab" required>
                @foreach($packages->where('package_id', $plan['id']) as $pkg)
                <option
                    value="{{ $pkg->id }}"
                    data-base="{{ $pkg->base_price }}"
                    data-platform="{{ $pkg->platform_fee_percentage }}"
                    data-gst="{{ $pkg->gst_rate }}"
                    data-available="{{ $pkg->is_available }}">
                    {{ $pkg->varient_name ?? 'Package' }} - ₹ {{ $pkg->base_price }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label>Available Slots</label>
            <select name="slot_id" id="slot_id_darshan" class="form-control" disabled>
            </select>
        </div>


        {{-- Devotee Section --}}
        <div class="col-md-12">
            <div class="row mt-4">
                <!-- LEFT SIDE: Devotee Inputs -->
                <div class="col-md-8">
                    <!-- <label class="fw-bold">Devotee Details</label>
                    <input type="number" id="countDevoteedarshan" class="form-control w-50 my-2" placeholder="Enter Number of Devotees" value="1" onkeyup="this.value = this.value.replace(/\D/g, '').slice(0, 2); this.value = parseInt(this.value || 0);"> -->
                    <div class="row">
                        <div class="col-6 form-goup">
                            <label class="fw-bold">Devotee Name</label>
                            <input type="text" class="form-control w-70 my-2 customerNames" placeholder="Enter Devotees Name" autocomplete="off" onkeyup="$('#customer_name').val(this.value)" onblur="setTimeout(() => $('#customer_name').val(this.value), 1000);">
                            <ul class="list-group suggestion_lists" style="display:none; position:absolute; z-index:1000; width:100%;">
                            </ul>
                        </div>
                        <div class="col-6 form-goup">
                            <label class="fw-bold">Devotee Count</label>
                            <input type="number" id="countDevoteedarshan" class="form-control w-70 my-2" placeholder="Enter Number of Devotees" value="1" onkeyup="this.value = this.value.replace(/\D/g, '').slice(0, 2); this.value = parseInt(this.value || 0);">
                        </div>
                    </div>
                    <div id="darshandevoteeWrapper" class="border rounded p-2" style="max-height: 350px; overflow-y: auto;">
                        <div class="darshan-devotee-items border rounded p-3 mb-2">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" id="customer_name" name="customers[0][name]" placeholder="Name" class="form-control" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Mobile </label>
                                    <input class="form-control text-align-direction phone-input-with-country-picker-darshan-0" type="tel" placeholder="Enter User Phone Number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13)">
                                    <input type="hidden" class="country-picker-phone-number-darshan-0 w-50" name="customers[0][mobile]" id="customer_mobile" readonly="" value="">
                                </div>
                                <div class="col-md-3">
                                    <label>Aadhaar </label>
                                    <input type="text" id="customer_aadhaar" name="customers[0][aadhaar]" class="form-control" value="000000000000" placeholder="Aadhaar" maxlength="12" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)">
                                </div>
                                <div class="col-md-3">
                                    <label>Address </label>
                                    <input type="text" id="customer_address" name="customers[0][address]" class="form-control darshan-main-address" placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- RIGHT SIDE: Amount Summary -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-80">
                        <div class="card-body">
                            <h5 class="fw-bold text-center mb-3 text-primary">💰 Amount Summary</h5>

                            <div class="mb-2">
                                <strong>Base Price:</strong> ₹<span id="show_base_price_darshan" class="h5">0.00</span>
                            </div>
                            <div class="mb-2">
                                <strong>Platform Fee (per person):</strong> ₹<span id="show_platform_price_darshan" class="h5">0.00</span>
                            </div>
                            <div class="mb-2">
                                <strong>Receipt Fee (per person):</strong> ₹<span id="show_receipt_price_darshan" class="h5">0.00</span>
                            </div>
                            <div class="mb-2">
                                <strong>GST (%):</strong> <span id="show_gst_per_darshan" class="h5">0</span>%
                            </div>
                            <hr>
                            <div class="mb-2">
                                <strong>Total Devotees:</strong> <span id="show_devotees_qty_darshan" class="h3">1</span>
                            </div>
                            <div class="mb-2">
                                <strong>Amount per Devotee:</strong> ₹<span id="show_per_person_qty_darshan" class="h3">0.00</span>
                            </div>
                            <hr>
                            <div class="text-end fw-bold fs-5">
                                Total Payable: ₹<span id="show_total_amount_darshan" class="h1">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="payment-method-flex-add darshan-tab">
                        <div class="col-md-12">
                            <label>Payment Mode</label>
                            <div class="d-flex gap-3">
                                <label class="payment-option-darshan-free">
                                    <input type="radio" name="payment_mode" value="free" required class="puja-payment-darshan-mode" checked tabindex="-1">
                                    <span> Free</span>
                                </label>
                                <label class="payment-option-darshan-cash">
                                    <input type="radio" name="payment_mode" value="cash" required class="puja-payment-darshan-mode" checked tabindex="-1">
                                    <span> Cash</span>
                                </label>

                                <label class="payment-option-darshan-online">
                                    <input type="radio" name="payment_mode" value="online" required class="puja-payment-darshan-mode" tabindex="-1">
                                    <span> Online</span>
                                </label>
                                <label class="payment-option-darshan-online1">
                                    <input type="radio" name="payment_mode" value="qr_code" required class="puja-payment-darshan-mode" tabindex="-1">
                                    <span> Qr Code</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12 form-group d-none" id="payment-darshn-details-Success">
                            <div class="card border-success shadow-sm">
                                <div class="card-header bg-success text-white justify-content-between px-2">
                                    <span>Payment Success Details</span>
                                    <button type="button" class="close text-white rounded-circle bg-danger px-2 py-1" aria-label="Close" onclick="$('#payment-darshn-details-Success').addClass('d-none')">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <p><strong>Transaction ID:</strong> <span id="paymant-darshan-id"></span></p>
                                    <p><strong>Amount:</strong> <span id="paymant-darshan-amount"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 form-group online-qr-code-darshan-show"></div>
                        <!-- Hidden Fields for Backend -->
                        <input type="hidden" id="base_price_darshan" name="base_price">
                        <input type="hidden" id="receipt_amount_darshan" name="receipt_amount">
                        <input type="hidden" id="total_amount_darshan" name="amount">
                        <input type="hidden" id="show_devotees_qty_darshan" name="customer_qty">

                        <div class="col-md-12 mt-4">
                            <button type="button" class="btn btn-primary float-end darshan-form-submit submit-button-class-darshan" onclick="paymantDarshanNow()" tabindex="-1">Submit Darshan Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>