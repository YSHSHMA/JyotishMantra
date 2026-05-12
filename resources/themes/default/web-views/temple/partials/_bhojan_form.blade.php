<!-- Header -->
@php
    $packageId = json_decode($temple['package_service'], true);
    $pujaId = collect($packageId)->firstWhere('name', 'bhojan')['id'] ?? null;
    // dd($pujaId);
@endphp
@if (!empty($pujaId))
    
<div class="mb-3 pb-2 border-bottom">
    <h4 class="mb-1 text-warning d-flex align-items-center">
        <i class="fa-solid fa-utensils me-2 text-warning"></i>{{ translate('Bhojan_Booking_form') }}
    </h4>
    <p class="text-muted small mb-0">
        {{ translate('Select your bhojan date and package to book instantly.') }}
    </p>

</div>
<div class="row card border-0 shadow-sm rounded-3" id="bhojan-section">

    <!-- Booking Fields -->
    <div class="row">
        <div class="col-md-6">
            <label for="bhojanPackage" class="form-label fw-semibold">{{ translate('Select_Bhojan_Package') }}</label>
            <select class="form-select bhojan-package-select" name="bhojan_package" id="bhojanPackage" >
                @foreach($packages[$pujaId] as $pkg)
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
        @php
            $today = date('Y-m-d');
            $nextMonth = date('Y-m-d', strtotime('+1 month'));
        @endphp

        <!-- Bhojan Date -->
        <div class="col-md-6">
            <label for="bhojanDate" class="form-label fw-semibold">{{ translate('Select_Date') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="fa-solid fa-calendar-days text-warning"></i>
                </span>
                <input type="date" class="form-control bhojan-date-select" name="bhojan_date"  id="bhojanDate"  min="{{ $today }}"   max="{{ $nextMonth }}"  value="{{ $today }}">
                <button class="btn btn-primary" onclick="bhojanNextDateBtn()">Next ></button>
            </div>
        </div>
        <div class="col-md-12">
            <label class="form-label fw-semibold">{{ translate('Available_Time_Slots') }}</label>
            <div class="bhojan-timeslot-container"></div>
            <input type="hidden" name="bhojan_timeslot_id" class="bhojan-timeslot-id" id="timeSlotSelect">
        </div>        
    </div>
    <!-- Saved Customers -->
    <div class="row g-2 mt-3" id="bhojanSavedCustomers">
        <span class="fw-semibold text-secondary">{{ translate('Confirm_Selected_Customers') }}</span>
    </div>

    <!-- Total Section -->
    <div class="mt-4 p-3 bg-light rounded-2">
        <div class="d-flex justify-content-between align-items-center">
            <strong>{{ translate('Total_Price') }}:</strong>
            <span class="fs-5 fw-bold text-success">₹<span id="bhojanTotal">0</span></span>
        </div>
        <div id="bhojanPriceBreakdown" class="small text-muted mt-1"></div>
    </div>

    <!-- Action Button -->
    {{-- <div class="text-end mt-3">
        <button class="btn btn-primary px-4" id="bhojan-save">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save Booking
        </button>
    </div> --}}
</div>
@endif
