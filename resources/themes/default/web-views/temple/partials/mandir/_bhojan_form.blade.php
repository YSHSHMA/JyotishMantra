<div class="row" id="bhojan-section">
    <input type="hidden" name="service_id" value="{{ $package->id }}" id="bhojan-package-id">
    <input type="hidden" name="payment_mode" value="online">
    <div class="col-lg-6">
        <div class="row g-3">
            <div class="col-6">
                <label class="f-label">First
                    Name</label>
                <input type="text" class="form-control" placeholder="Your Name"
                    value="{{ $user->f_name . ' ' . $user->l_name ?? null }}" readonly>
            </div>
            <div class="col-6">
                <label class="f-label">Mobile
                    Number</label>
                <input type="text" class="form-control" placeholder="Mobile Number" value="{{ $user->phone }}" readonly>
            </div>
            <div class="col-12">
                <label class="f-label">{{ translate('Select_Bhojan_Date') }}</label>
                <input type="date" class="form-control bhojan-date-select" name="booking_date"
                    min="{{ date('Y-m-d') }}"
                    max="{{ date('Y-m-d', strtotime('+1 month')) }}"
                    value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-12">
                <label class="f-label">{{ translate('Available_Time_Slots') }}</label>
                <div class="bhojan-timeslot-container"></div>
                <input type="hidden" name="timeslot_id" class="bhojan-timeslot-id" id="timeSlotSelect">
            </div>
            <div class="col-12">
                <label class="f-label">Total Customer</label>
                <div class="input-group">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeCustomer(-1)">−</button>
                    <input type="number"
                           class="form-control text-center"
                           name="total_customer"
                           id="total-customer"
                           value="1"
                           min="1"
                           max="5"
                           readonly required>
                    <button type="button" class="btn btn-outline-secondary" onclick="changeCustomer(1)">+</button>
                </div>
            </div>
            {{-- <div class="col-12">
                <label class="f-label">Total Customer</label>
                <select class="form-select" name="total_customer" id="total-customer" required>
                    <option value=""> Select Customer </option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                </select>
            </div> --}}
        </div>
    </div>
    <div class="col-lg-5 offset-lg-1">
        <div class="col-12 mt-3">
            <ul class="list-unstyled mb-0" id="pay-detail">

            </ul>
        </div>
        {{-- <div class="col-12 mb-3 d-flex gap-3 justify-content-center">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_mode"
                    id="cashPayment" value="cash" checked>
                <label class="form-check-label fw-medium" for="cashPayment">
                    {{ translate('Cash') }}
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_mode"
                    id="onlinePayment" value="online">
                <label class="form-check-label fw-medium" for="onlinePayment">
                    {{ translate('Online') }}
                </label>
            </div>
        </div> --}}
    </div>
    <div class="col-12 mt-3 mb-3 text-center mobile-submit-wrapper">
        <button class="btn btn-submit sub-btn-req w-50">
            Submit
        </button>
    </div>
</div>