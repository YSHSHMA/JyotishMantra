@php
    $packageId = json_decode($temple['package_service'], true);
    $pujaId = collect($packageId)->firstWhere('name', 'darshan')['id'] ?? null;
@endphp
@if (!empty($pujaId))
    
<div class="mb-3 pb-2 border-bottom">
    <h4 class="mb-1 text-warning d-flex align-items-center">
        <i class="fa-solid fa-om me-2 text-warning"></i> {{ translate('Darsha_Booking_form') }}
    </h4>
    <p class="text-muted small mb-0">
        {{ translate('Select your package, date, and time slot to book your darshan.') }}
    </p>

</div>

<div class="row card border-0 shadow-sm rounded-3" id="darshan-section">

    @php
        $today = date('Y-m-d');
        $nextMonth = date('Y-m-d', strtotime('+1 month'));
    @endphp

    <!-- Booking Fields -->
    <div class="row">
        <div class="col-md-6">
            <label class="form-label fw-semibold">{{ translate('Select_Darshan_Package') }}</label>
            <select class="form-select darshan-package-select" name="package_id" data-timeslot-target="#timeSlotSelect">
                @foreach($packages[$pujaId] as $pkg)
                    <option value="{{ $pkg->id }}"
                            data-price="{{ $pkg->base_price }}"
                            data-gst="{{ $pkg->gst_rate ?? 0 }}"
                            data-platform="{{ $pkg->platform_fee_percentage ?? 0 }}"
                            data-reception="{{ $pkg->receipt_fee_percentage ?? 0 }}">
                        {{ $pkg->variant_name ?? $pkg->varient_name }} - ₹{{ $pkg->base_price }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">{{ translate('Select_Date') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="fa-solid fa-calendar-days text-warning"></i>
                </span>
                <input type="date" class="form-control darshan-date-select" name="vip_date" id="vipDate-select"
                       min="{{ $today }}" max="{{ $nextMonth }}" value="{{ $today }}">
                <button class="btn btn-primary" onclick="darshanNextDateBtn()">Next ></button>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">{{ translate('Available_Time_Slots') }}</label>
            <div class="darshan-timeslot-container"></div>
            <input type="hidden" name="darshan_timeslot_id" class="darshan-timeslot-id" id="timeSlotSelect">
        </div>
    </div>

    <!-- Saved Customers -->
    <div class="row g-2 mt-3" id="darshanSavedCustomers">
        <span class="fw-semibold text-secondary">{{ translate('Confirm_Selected_Customers') }}</span>
    </div>

    <!-- Total Section -->
    <div class="mt-4 p-3 bg-light rounded-2">
        <div class="d-flex justify-content-between align-items-center">
            <strong>{{ translate('Total_Price') }}:</strong>
            <span class="fs-5 fw-bold text-success">₹<span id="darshanTotal">0</span></span>
        </div>
        <div id="darshanPriceBreakdown" class="small text-muted mt-1"></div>
    </div>

    <!-- Action Button -->
    {{-- <div class="text-end mt-3">
        <button class="btn btn-primary px-4" id="darshan-save">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save Booking
        </button>
    </div> --}}
</div>
@endif
<!-- Header -->
