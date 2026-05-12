<div class="row">
    <input type="hidden" name="service_id" value="{{ $package->id }}">
    <input type="hidden" name="payment_mode" value="online">
    <input type="hidden" name="total_customer" value="1">
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
                <label class="f-label">{{ translate('Select_Locker_Date') }}</label>
                <input type="date" class="form-control" name="booking_date"
                    min="{{ date('Y-m-d') }}"
                    max="{{ date('Y-m-d', strtotime('+1 month')) }}"
                    value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-12">
                <label class="f-label">{{ translate('Mobile') }}</label>
                <div class="input-group">
                    <input type="number"  class="form-control" name="locker_mobile_qty" placeholder="{{ translate('mobile_quantity') }}" value="0" inputmode="numeric" required>
                </div>
            </div>
    
            <div class="col-md-12">
                <label class="f-label">{{ translate('Luggage') }}</label>
                <div class="input-group">
                    <input type="number"  class="form-control" name="locker_luggage_qty" placeholder="{{ translate('luggage_quantity') }}" value="0" inputmode="numeric" required>
                </div>
            </div>
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