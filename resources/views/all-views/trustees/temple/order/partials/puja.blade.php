<form id="poojaForm" method="POST" action="{{ route('trustees-vendor.order-management.store-pooja') }}">
    @csrf
    <input type="hidden" name="temple_id" value="{{ $temple->id }}">
    <input type="hidden" name="type" value="{{ $plan['name'] }}">

    <div class="row">
        {{-- Date Selection --}}
        <div class="col-md-12">
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
                    class="btn puja-date-btn {{ $index === 0 ? 'btn-primary active' : 'btn-outline-primary' }}"
                    data-value="{{ $date['value'] }}">
                    {{ $date['label'] }} ({{ $date['display'] }})
                </button>
                @endforeach
            </div>
            <input type="hidden" name="date" id="selected_puja_date" value="{{ $today->format('Y-m-d') }}">
        </div>
        <div class="col-md-6">
            <label>Pooja Type</label>
            <select name="package_id" id="package_id" class="form-control firstoption puja-tab" required>
                @foreach($packages->where('package_id', $plan['id']) as $index => $pkg)
                <option
                    value="{{ $pkg->id }}"
                    data-base="{{ $pkg->base_price }}"
                    data-platform="{{ $pkg->platform_fee_percentage }}"
                    data-receipt="{{ $pkg->receipt_fee_percentage }}"
                    data-gst="{{ $pkg->gst_rate }}"
                    @if($index===0 ) selected @endif>
                    {{ $pkg->varient_name ?? 'Package' }} - ₹{{ $pkg->base_price }}
                </option>
                @endforeach
            </select>
        </div>
        <?php $purohits = \App\Models\Purohit::where('temple_id', $temple->id)->where('status', 1)
            ->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                $q->where('id', auth('trust_employee')->user()->purohit_id);
            })->when((auth('purohit')->check()), function ($q) {
                $q->where('id', auth('purohit')->user()->id);
            })->orderBy('id', 'desc')->get(['id', 'name']);

        if (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) {
            $purohit_id = auth('trust_employee')->user()->purohit_id;
        } elseif (auth('purohit')->check()) {
            $purohit_id = auth('purohit')->user()->id;
        } else {
            $purohit_id = '';
        }
        ?>
        <div class="col-md-6 {{ (($purohit_id)?'d-none':'')}} ">
            <label>Already Assigned by Temple</label>
            <select name="purohit_id" class="form-control">
                <option value="">Already Assigned by Temple</option>
                @if($purohits)
                @foreach($purohits as $val)
                <option value="{{ $val['id'] }}" {{ (($purohit_id == $val['id'] )? 'selected' : '' ) }}>{{ $val['name'] }}</option>
                @endforeach
                @endif
            </select>
        </div>
        {{-- Devotee Section --}}
        <div class="col-md-12">
            <div class="row mt-4">
                <!-- LEFT SIDE: Devotee Inputs -->
                <div class="col-md-8">
                    <!-- <label class="fw-bold">Devotee Details</label>
                    <hr> -->
                    <div class="row">
                        <div class="col-6 form-goup">
                            <label class="fw-bold">Devotee Name</label>
                            <input type="text" class="form-control w-70 my-2 customerNames" placeholder="Enter Devotees Name" autocomplete="off" onkeyup="$('#main_name').val(this.value)" onblur="setTimeout(() => $('#main_name').val(this.value), 1000);">
                            <ul class="list-group suggestion_lists" style="display:none; position:absolute; z-index:1000; width:100%;">
                            </ul>
                        </div>
                        <div class="col-6 form-goup">
                            <label class="fw-bold">Devotee Count</label>
                            <input type="number" id="countDevoteepuja" class="form-control w-70 my-2" placeholder="Enter Number of Devotees" value="1" onkeyup="this.value = this.value.replace(/\D/g, '').slice(0, 2); this.value = parseInt(this.value || 0);">
                        </div>
                    </div>
                    <div id="pujadevoteeWrapper" class="border rounded p-2" style="max-height: 350px; overflow-y: auto;">
                        <div class="puja-devotee-item border rounded p-3 mb-2">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" id="main_name" name="customers[0][name]" placeholder="Name" class="form-control" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Mobile</label>
                                    <input class="form-control text-align-direction phone-input-with-country-picker-0" type="tel" placeholder="Enter User Phone Number" oninput="this.value=this.value.replace(/\D/g,'').slice(0,13)">
                                    <input type="hidden" class="country-picker-phone-number-0 w-50" name="customers[0][mobile]" readonly="" value="">
                                </div>
                                <div class="col-md-3">
                                    <label>Aadhaar</label>
                                    <input type="number" id="main_aadhaar" name="customers[0][aadhaar]" class="form-control" value="000000000000" placeholder="Aadhaar" maxlength="12" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)">
                                </div>
                                <div class="col-md-3">
                                    <label>Address</label>
                                    <input type="text" id="main_address" name="customers[0][address]" class="form-control puja-main-address" placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <button type="button" id="pujaaddDevotee" class="btn btn-sm btn-success mt-2">+ Add More</button> -->
                </div>
                <!-- RIGHT SIDE: Amount Summary -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-80">
                        <div class="card-body">
                            <h5 class="fw-bold text-center mb-3 text-primary">💰 Amount Summary</h5>

                            <div class="mb-2">
                                <strong>Base Price:</strong> ₹<span id="show_base_puja" class="h5">0.00</span>
                            </div>
                            <div class="mb-2">
                                <strong>Platform Fee (per person):</strong> ₹<span id="show_platform_puja" class="h5">0.00</span>
                            </div>
                            <div class="mb-2">
                                <strong>Receipt Fee (per person):</strong> ₹<span id="show_receipt_puja" class="h5">0.00</span>
                            </div>
                            <div class="mb-2">
                                <strong>GST (%):</strong> <span id="show_gst_puja" class="h5">0</span>%
                            </div>
                            <hr>
                            <div class="mb-2">
                                <strong>Total Devotees:</strong> <span id="show_devotees_puja" class="h3">1</span>
                            </div>
                            <div class="mb-2">
                                <strong>Amount per Devotee:</strong> ₹<span id="show_per_person_puja" class="h3">0.00</span>
                            </div>
                            <hr>
                            <div class="text-end fw-bold fs-5">
                                Total Payable: ₹<span id="show_total_puja" class="h1">0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="payment-method-flex-add puja-tab">
                            <div class="col-md-12">
                                <label>Payment Mode</label>
                                <div class="d-flex gap-3">
                                    <label class="payment-option-puja-free">
                                        <input type="radio" name="payment_mode" value="free" required class="puja-payment-mode" checked tabindex="-1">
                                        <span> Free</span>
                                    </label>
                                    <label class="payment-option-puja-cash">
                                        <input type="radio" name="payment_mode" value="cash" required class="puja-payment-mode" checked tabindex="-1">
                                        <span> Cash</span>
                                    </label>
                                    <label class="payment-option-puja-online">
                                        <input type="radio" name="payment_mode" value="online" required class="puja-payment-mode" tabindex="-1">
                                        <span> Online</span>
                                    </label>
                                    <label class="payment-option-puja-online1">
                                        <input type="radio" name="payment_mode" value="qr_code" required class="puja-payment-mode" tabindex="-1">
                                        <span> Qr Code</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 form-group d-none" id="paymentDetailsSuccess">
                                <div class="card border-success shadow-sm">
                                    <div class="card-header bg-success text-white justify-content-between px-2">
                                        <span>Payment Success Details</span>
                                        <button type="button" class="close text-white rounded-circle bg-danger px-2 py-1" aria-label="Close" onclick="$('#paymentDetailsSuccess').addClass('d-none')">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <p><strong>Transaction ID:</strong> <span id="paymentId"></span></p>
                                        <p><strong>Amount:</strong> <span id="paymentAmount"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 form-group online-qr-code-show"></div>
                            <!-- Hidden Fields for Backend -->
                            <input type="hidden" id="base_price_puja" name="base_price">
                            <input type="hidden" id="receipt_amount_puja" name="receipt_amount">
                            <input type="hidden" id="total_amount_puja" name="amount">
                            <input type="hidden" id="show_devotees_puja" name="customer_qty">
                            {{-- Submit --}}
                            <div class="col-md-12 mt-4 text-end">
                                <button type="button" class="btn btn-primary puja-form-submit submit-button-class-puja" onclick="paymantNow()" tabindex="-1">Submit Pooja Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>
</form>