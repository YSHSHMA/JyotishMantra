@extends('layouts.back-end.app-guruji')
@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Utils\Helpers;
if (auth('guruji')->check()) {
    $vendorId = auth('guruji')->user()->id;
} 
@endphp
@push('css_or_js')
<link rel="stylesheet"  href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
<style>
.floating-save-btn {
    position: fixed;
    right: 30px;
    bottom: 30px;
    z-index: 9999;

    background: #0d6efd;
    color: #fff;
    border: none;
    padding: 12px 18px;

    font-size: 14px;
    font-weight: 600;
    cursor: pointer;

    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    /* animation */
    animation: floatUp 2.5s ease-in-out infinite;

    transition: all 0.3s ease;
}
/* Hover effect */
.floating-save-btn:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 14px 35px rgba(13,110,253,0.6);
}

/* Floating animation */
@keyframes floatUp {
    0%   { transform: translateY(0); }
    50%  { transform: translateY(-6px); }
    100% { transform: translateY(0); }
}

.arrow-icon {
    display: inline-block;
    font-size: 18px;
    transition: transform 0.25s ease;
}

.arrow-icon.rotate {
    transform: rotate(90deg); /* > becomes ˅ */
}

.toggle-slab {
    padding: 0 8px;
    line-height: 1;
}


</style>

@endpush
@section('title', translate('service'))
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
      <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
         <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/my-bank-info.png')}}" alt="">
         {{translate('service')}}
      </h2>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card mb-4">
                {{-- Card Header --}}
                <div class="card-header p-2">
                    <ul class="nav nav-tabs">
                        @foreach($categoryServices as $i => $category)
                        <li class="nav-item">
                        <button class="nav-link {{ $i == 0 ? 'active' : '' }}"
                            data-toggle="tab"
                            data-target="#cat-{{ $category['id'] }}">
                        {{ $category['name'] }}
                        </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="tab-content p-3">
                @foreach($categoryServices as $category)
             
                <div class="tab-pane fade {{ $i == 0 ? 'show active' : '' }}" id="cat-{{ $category['id'] }}">
                    <div class="row">
                        @foreach($category['services'] as $service)
                        @php
                            $saved = $panditPrices[$service['id']] ?? [];
                            $checked = !empty($saved);
                        @endphp
                        <div class="col-md-6 mb-3">
                            <form method="POST" action="{{ route('guruji.services.puja.update', $vendor->id) }}"
                            class="service-form">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service['id'] }}">
                                <input type="hidden" name="pandit_id" value="{{ $vendor->id }}">
                                <input type="hidden" name="type" value="puja">
                                <input type="hidden" name="by_type" value="{{ $vendor->type }}">
                                <div class="card border">
                                <div class="card-body">
                                    {{-- HEADER --}}
                                    <div class="d-flex align-items-center mb-2">
                                    <input type="checkbox" class="form-check-input mr-2 service-check"
                                    {{ $checked ? 'checked' : '' }}>
                                    <img src="{{ getValidImage(path: 'storage/app/public/pooja/'.$service['thumbnail'], type:'product') }}"
                                        width="40" class="rounded mr-2">
                                    <strong>{{ $service['name'] }}</strong>
                                    </div>
                                    {{-- SERVICE LEVEL INPUTS --}}
                                    <div class="row mb-2 service-inputs">
                                    <div class="col-6">
                                        <label class="small text-muted">Pooja Venue</label>
                                        <input type="text" class="form-control"
                                        name="venue"
                                        value="{{ $saved['venue'] ?? $service['pooja_venue'] ?? '' }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted">Single Price</label>
                                        <input type="number" class="form-control"
                                        name="single_price"
                                        value="{{ $saved['single_price'] ?? '' }}">
                                    </div>
                                    </div>
                                    {{-- PRICE SLABS --}}
                                    <div class="border rounded p-2 bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="fw-bold">Quantity Slabs</small>
                                            <button type="button" class="btn btn-sm btn-outline-secondary toggle-slab">                                              <span class="arrow-icon">›</span>
                                            </button>
                                            </div>
                                        @foreach($category['slabs'] as $i => $slab)
                                            <div class="quantity-slab-content mt-2" style="display: none;">
                                                <div class="row g-2 align-items-center mb-1">

                                                    <div class="col-4">
                                                    <label class="small text-muted">Min Qty</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm always-disabled"
                                                            value="{{ $slab->min_qty }}"
                                                            readonly>
                                                        <input type="hidden"
                                                            name="slabs[{{ $slab->id }}][min_qty]"
                                                            value="{{ $slab->min_qty }}">
                                                    </div>

                                                    <div class="col-4">
                                                    <label class="small text-muted">Max Qty</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm always-disabled"
                                                            value="{{ $slab->max_qty }}"
                                                            readonly>
                                                        <input type="hidden"
                                                            name="slabs[{{ $slab->id }}][max_qty]"
                                                            value="{{ $slab->max_qty }}">
                                                    </div>

                                                    <div class="col-4">
                                                        @if($vendor->type === 'in house')
                                                            {{-- SHOW ONLY (ADMIN PRICE) --}}
                                                            <label class="small text-muted">Price Fiexd</label>
                                                            <input type="number"
                                                                class="form-control form-control-sm always-disabled"
                                                                value="{{ $slab->price }}"
                                                                readonly>

                                                            {{-- SUBMIT SAME VALUE --}}
                                                            <input type="hidden"
                                                                name="slabs[{{ $slab->id }}][price]"
                                                                value="{{ $slab->price }}">
                                                        @else
                                                            {{-- FREELANCER CAN EDIT --}}
                                                            <label class="small text-muted">Price</label>
                                                            <input type="number"
                                                                class="form-control form-control-sm"
                                                                name="slabs[{{ $slab->id }}][price]"
                                                                value="{{ $saved['slabs'][$slab->id]['price'] ?? $slab->price }}"
                                                                placeholder="Enter price">
                                                        @endif
                                                    </div>


                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                    {{-- SAVE --}}
                                    <div class="text-end mt-2">
                                    <button class="btn btn-mb btn-primary">
                                    {{ $checked ? 'Update' : 'Save' }}
                                    </button>
                                    </div>
                                </div>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        </div>

        </div>
    </div>
    <a href="{{ route('guruji.services.create', $vendorId) }}" class="btn btn-md btn-primary floating-save-btn">
                        <i class="fa fa-plus"></i> {{ translate('add_new_service') }}
                    </a>

</div>
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/country-picker-init.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#serviceList').DataTable({
            pageLength: 10,
            scrollY: '500px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
        });

        
    });
    $('.toggle-slab').on('click', function () {
        const content = $(this).closest('.bg-light').find('.quantity-slab-content');
        const arrow = $(this).find('.arrow-icon');

        content.slideToggle(200);

        arrow.toggleClass('rotate');
    });


</script>
@endpush