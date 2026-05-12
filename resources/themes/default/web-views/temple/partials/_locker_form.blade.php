<!-- Header -->
@php
    $packageId = json_decode($temple['package_service'], true);
    $pujaId = collect($packageId)->firstWhere('name', 'locker')['id'] ?? null;
@endphp
@if (!empty($pujaId))
    
<div class="mb-3 pb-2 border-bottom">
    <h4 class="mb-1 text-warning d-flex align-items-center">
        <i class="fa-solid fa-hands-praying me-2 text-warning"></i>{{ translate('Locker_Booking_form') }} 
    </h4>
    <p class="text-muted small mb-0">
        {{ translate('Select a date to book a locker. Jewellery is not allowed.') }}
    </p>
</div>

<div class="row card border-0 shadow-sm rounded-3" id="locker-section">

    <!-- Booking Fields -->
    <div class="row">
        @php
            $today = date('Y-m-d');
            $nextMonth = date('Y-m-d', strtotime('+1 month'));
        @endphp

        <div class="col-md-6">
            <label class="form-label fw-semibold">{{ translate('Choose_Locker_Package') }}</label>
            <select class="form-select locker-package-select" name="locker_package">
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

        <div class="col-md-6">
            <label class="form-label fw-semibold">{{ translate('Select_Locker_Date') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="fa-solid fa-calendar-days text-warning"></i>
                </span>
                <input type="date"  class="form-control"  name="locker_date"  id="lockerDate"  min="{{ $today }}" 
                       max="{{ $nextMonth }}" value="{{ $today }}">
                <button class="btn btn-primary" onclick="lockerNextDateBtn()">Next ></button>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">{{ translate('Mobile') }}</label>
            <div class="input-group">
                <input type="number"  class="form-control" id="locker-mobile-qty"  name="locker_mobile_qty" placeholder="{{ translate('mobile_quantity') }}" value="0" inputmode="numeric">
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">{{ translate('Luggage') }}</label>
            <div class="input-group">
                <input type="number"  class="form-control" id="locker-luggage-qty" name="locker_luggage_qty" placeholder="{{ translate('luggage_quantity') }}" value="0" inputmode="numeric">
            </div>
        </div>
    </div>

    <!-- Saved Customers -->
    {{-- <div class="row g-2 mt-3" id="lockerSavedCustomers"></div> --}}

    <!-- Total Section -->
    <div class="mt-4 p-3 bg-light rounded-2">
        <div class="d-flex justify-content-between align-items-center">
            <strong>Total Price:</strong>
            <span class="fs-5 fw-bold text-success">₹<span id="locakerTotal">{{$packages[4][0]['base_price']+$packages[4][0]['platform_fee_percentage']+$packages[4][0]['receipt_fee_percentage']}}</span></span>
        </div>
        <div id="locakerPriceBreakdown" class="small text-muted mt-1"></div>
    </div>

    <!-- Action Button -->
    {{-- <div class="text-end mt-3">
        <button class="btn btn-primary px-4" id="locker-save">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save Booking
        </button>
    </div> --}}
</div>
@endif
