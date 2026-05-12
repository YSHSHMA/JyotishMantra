<div class="card row border-0 shadow-sm rounded-3 p-4" id="puja-section">
    <!-- Header -->
    <div class="mb-3 pb-2 border-bottom">
        <h4 class="mb-1 text-warning d-flex align-items-center">
            <i class="fa-solid fa-hands-praying me-2 text-warning"></i> Pooja Booking
        </h4>
        <p class="text-muted small mb-0">
            Book your desired pooja by selecting the date and preferred package below. 
            Complete your booking to receive blessings and confirmation instantly.
        </p>
    </div>

    <!-- Booking Fields -->
    <div class="row g-3">
        @php
            $today = date('Y-m-d');
            $nextMonth = date('Y-m-d', strtotime('+1 month'));
        @endphp

        <div class="col-md-6">
            <label class="form-label fw-semibold">Select Pooja Date</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="fa-solid fa-calendar-days text-warning"></i>
                </span>
                <input type="date"  class="form-control"   name="puja_date"  id="pujaDate"  min="{{ $today }}" 
                       max="{{ $nextMonth }}" value="{{ $today }}">
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Choose Pooja Package</label>
            <select class="form-select puja-package-select" name="puja_package">
                @foreach($packages[1] as $pkg)
                    <option value="{{ $pkg->id }}" 
                            data-price="{{ $pkg->base_price }}" 
                            data-gst="{{ $pkg->gst_rate ?? 0 }}" 
                            data-platform="{{ $pkg->platform_fee_percentage ?? 0 }}" 
                            data-reception="{{ $pkg->receipt_fee_percentage ?? 0 }}">
                        {{ $pkg->varient_name }} - ₹{{ $pkg->base_price }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Saved Customers -->
    <div class="row g-3 mt-3" id="pujaSavedCustomers"></div>

    <!-- Total Section -->
    <div class="mt-4 p-3 bg-light rounded-2">
        <div class="d-flex justify-content-between align-items-center">
            <strong>Total Price:</strong>
            <span class="fs-5 fw-bold text-success">₹<span id="pujaTotal">0</span></span>
        </div>
        <div id="pujaPriceBreakdown" class="small text-muted mt-1"></div>
    </div>
</div>
