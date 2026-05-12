<!-- Header -->
@php
    $packageId = json_decode($temple['package_service'], true);
    $pujaId = collect($packageId)->firstWhere('name', 'puja')['id'] ?? null;
@endphp
@if (!empty($pujaId))
<div class="mb-3 pb-2 border-bottom">
    <h4 class="mb-2 text-warning d-flex align-items-center">
        <i class="fa-solid fa-hands-praying me-2 text-warning"></i>{{ translate('Puja_Booking_form') }}
    </h4>
    <p class="text-muted small mb-3">
        {{ translate('Select your puja date and package to book instantly.') }}
    </p>
</div>
<div class="card row border-0 shadow-sm rounded-3" id="puja-section">
    <!-- Booking Fields -->
    <div class="row">
        @php
            $today = date('Y-m-d');
            $nextMonth = date('Y-m-d', strtotime('+1 month'));
        @endphp

        <div class="col-md-4">
            <label class="form-label fw-semibold">{{ translate('Select_Puja_Date') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="fa-solid fa-calendar-days text-warning"></i>
                </span>
                <input type="date"  class="form-control"  name="puja_date"  id="pujaDate"  min="{{ $today }}"  max="{{ $nextMonth }}" value="{{ $today }}">
                <button class="btn btn-primary" onclick="pujaNextDateBtn()">Next ></button>
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">{{ translate('Choose_the_Best_Puja_Package_for_You') }}
            </label>
            <select class="form-select puja-package-select" name="puja_package">
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

        <div class="col-md-4">
            <label class="form-label fw-semibold">{{ translate('Already_Assigned_by_the_Temple') }} 
            </label>
            <select class="form-select" name="pandit_id" id="pandit-id">
                <option value="">{{ translate('Already_Assigned_by_the_Temple') }}</option>
                @foreach($purohits as $purohit)
                    <option value="{{ $purohit->id }}">
                        {{ $purohit->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Saved Customers -->
    <div class="row g-2 mt-3" id="pujaSavedCustomers">
    <span class="fw-semibold text-secondary"> {{ translate('Confirm_Selected_Customers') }}</span>
    </div>

    <!-- Total Section -->
    <div class="mt-4 p-3 bg-light rounded-2">
        <div class="d-flex justify-content-between align-items-center">
            <strong> {{ translate('Total_Price') }}</strong>
            <span class="fs-5 fw-bold text-success">₹<span id="pujaTotal">0</span></span>
        </div>
        <div id="pujaPriceBreakdown" class="small text-muted mt-1"></div>
    </div>
</div>
@endif
