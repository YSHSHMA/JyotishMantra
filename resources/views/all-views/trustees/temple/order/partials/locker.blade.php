<form id="lockerForm" method="POST">
    @csrf
    <input type="hidden" name="temple_id" value="{{ $temple->id }}">
    <input type="hidden" name="type" value="{{ $plan['name'] }}">

    <div class="row">
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
                    class="btn locker-date-btn {{ $index === 0 ? 'btn-primary active' : 'btn-outline-primary' }}"
                    data-value="{{ $date['value'] }}">
                    {{ $date['label'] }} ({{ $date['display'] }})
                </button>
                @endforeach
            </div>
            <input type="hidden" name="date" id="locker-date" value="{{ $today->format('Y-m-d') }}">
        </div>

        <div class="col-md-6">
            <label>Locker Type</label>
            <select name="package_id" class="form-control firstoption locker-tab" id="locker_package_id" required>
                <option selected>Select Package</option>
                @foreach($packages->where('package_id', $plan['id']) as $pkg)
                <option value="{{ $pkg->id }}"
                    data-base="{{ $pkg->base_price }}"
                    data-platform="{{ $pkg->platform_fee_percentage }}"
                    data-gst="{{ $pkg->gst_rate }}"
                    data-receipt="{{ $pkg->receipt_fee ?? 0 }}"
                    data-available="{{ $pkg->is_available }}">
                    {{ $pkg->varient_name ?? 'Package' }} - ₹ {{ $pkg->base_price }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Number Of Phone</label>
            <input name="locker_items[mobile]" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Number Of luggage</label>
            <input name="locker_items[luggage]" class="form-control" required>
        </div>

        <div class="col-md-12">
            <div class="row mt-4">
                <!-- LEFT SIDE: Devotee Inputs -->
                <div class="col-md-8">
                    <label class="fw-bold">Devotee Details</label>
                    <div id="devoteeLockerAcddsWrapper" class="border rounded p-2" style="max-height: 350px; overflow-y: auto;">
                        <div class="devotee-lockertems border rounded p-3 mb-2">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" id="customer_locker_name" name="customers[0][name]" placeholder="Name" class="form-control  customerNames" autocomplete="off" required>
                                    <ul class="list-group suggestion_lists" style="display:none; position:absolute; z-index:1000; width:100%;"> </ul>
                                </div>
                                <div class="col-md-3">
                                    <label>Mobile</label>
                                    <input class="form-control text-align-direction phone-input-with-country-picker-locker-0" type="tel" placeholder="Enter User Phone Number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13)">
                                    <input type="hidden" class="country-picker-phone-number-locker-0 w-50" name="customers[0][mobile]" id="customer_locker_mobile" readonly="" value="">
                                </div>
                                <div class="col-md-3">
                                    <label>Aadhaar</label>
                                    <input type="text" id="customer_locker_aadhaar" name="customers[0][aadhaar]" class="form-control" placeholder="Aadhaar" value="000000000000" maxlength="12" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)">
                                </div>
                                <div class="col-md-3">
                                    <label>Address</label>
                                    <input type="text" id="customer_locker_address" name="customers[0][address]" class="form-control" placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-80">
                        <div class="card-body">
                            <h5 class="fw-bold text-center mb-2 text-primary">💰 Amount Summary</h5>
                            <div class="mb-2"><strong>Base Price:</strong> ₹<span id="show_locker_base_price" class="h5">0.00</span></div>
                            <div class="mb-2"><strong>Platform Fee (per person):</strong> ₹<span id="show_locker_platform_price" class="h5">0.00</span></div>
                            <div class="mb-2"><strong>Receipt Fee (per person):</strong> ₹<span id="show_locker_receipt_price" class="h5">0.00</span></div>
                            <div class="mb-2"><strong>GST (%):</strong> <span id="show_locker_gst_per" class="h5">0</span>%</div>
                            <hr>
                            <div class="mb-2"><strong>Total Devotees:</strong> <span id="show_locker_devotees_qty" class="h3">1</span></div>
                            <div class="mb-2"><strong>Amount per Devotee:</strong> ₹<span id="show_locker_per_person_qty" class="h3">0.00</span></div>
                            <hr>
                            <div class="text-end fw-bold fs-5">Total Payable: ₹<span id="show_locker_total_amount" class="h1">0.00</span></div>
                        </div>
                    </div>
                    <div class="payment-method-flex-add locker-tab">
                        <div class="mt-3">
                            <label class="form-label fw-bold text-secondary d-block mb-2">Payment Mode</label>
                            <div class="d-flex gap-3">
                                <label class="payment-option-locker-free">
                                    <input type="radio" name="payment_mode" value="free" required class="puja-payment-locker-mode" checked tabindex="-1">
                                    <span>Free</span>
                                </label>
                                <label class="payment-option-locker-cash">
                                    <input type="radio" name="payment_mode" value="cash" required class="puja-payment-locker-mode" checked tabindex="-1">
                                    <span> Cash</span>
                                </label>

                                <label class="payment-option-locker-online">
                                    <input type="radio" name="payment_mode" value="online" required class="puja-payment-locker-mode" tabindex="-1">
                                    <span> Online</span>
                                </label>
                                <label class="payment-option-locker-online1">
                                    <input type="radio" name="payment_mode" value="qr_code" required class="puja-payment-locker-mode" tabindex="-1">
                                    <span> Qr Code</span>
                                </label>
                            </div>
                        </div>
                        <div class="my-2">
                            <div class="form-group d-none" id="payment-locker-details-Success">
                                <div class="card border-success shadow-sm">
                                    <div class="card-header bg-success text-white justify-content-between px-2">
                                        <span>Payment Success Details</span>
                                        <button type="button" class="close text-white rounded-circle bg-danger px-2 py-1" aria-label="Close" onclick="$('#payment-locker-details-Success').addClass('d-none')">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Transaction ID:</strong> <span id="paymant-locker-id"></span></p>
                                        <p><strong>Amount:</strong> <span id="paymant-locker-amount"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group online-qr-code-locker-show"></div>
                        </div>
                        <input type="hidden" id="base_locker_price" name="base_price">
                        <input type="hidden" id="receipt_locker_amount" name="receipt_amount">
                        <input type="hidden" id="total_locker_amount" name="amount">
                        <input type="hidden" id="customer_locker_qty" name="customer_qty">
                        <div class="col-md-12 mt-2">
                            <button type="button" class="btn btn-primary w-100  float-end locker-form-submit submit-button-class-locker" onclick="paymantLockerNow()" tabindex="-1">Submit Locker Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
