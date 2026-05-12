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
  .always-disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
  }
  .sticky-tabs {
    position: sticky;
    top: 60px;
    z-index: 1020;
    background: #fff;
    padding-top: 8px;
    border-bottom: 1px solid #dee2e6;
  }
  
  .arrow-icon.rotate {
    transform: rotate(90deg);
  }

  .toggle-slab {
    padding: 0 8px;
    line-height: 1;
  }

</style>
@endpush
@section('title', translate('service'))
@section('content')
<div id="pageLoader" style=" display:none; position:fixed;   top:0; left:0;  width:100%; height:100%;
  background:rgba(255,255,255,0.8);   z-index:9999; align-items:center;justify-content:center;">
  <div class="text-center">
    <div class="spinner-border text-primary" role="status"></div>
    <div class="mt-2 fw-bold">Please wait...</div>
  </div>
</div>
<div class="content container-fluid">
  <div class="mb-3">
    <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
      <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/my-bank-info.png')}}" alt="">
      {{translate('service')}}
    </h2>
  </div>
  <div class="row mt-3">
    <div class="col-md-12">
      <div class="card card-top-bg-element mb-4">
        {{-- Card Header --}}
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0">{{ translate('service_charges') }}</h5>
          <a href="{{ route('guruji.services.puja.view', $vendorId) }}" class="btn btn-md btn-primary">
          <i class="fa fa-plus"></i> {{ translate('view_service') }}
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="card mb-4">
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
          
          <div class="tab-pane fade {{ $i == 0 ? 'show active' : '' }}" id="cat-{{ $category['id'] }}">
            <div class="row">
              @foreach($category['services'] as $service)
              @php
                $saved = $panditPrices[$service['id']] ?? [];
                $checked = !empty($saved);
              @endphp
              <div class="col-md-6 mb-3">
                <form method="POST" action="{{ route('guruji.services.save', $vendor->id) }}" class="service-form">
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
                          <input type="number"
                          name="single_price"
                          class="form-control {{ $vendor->type == 'in house' ? 'always-disabled' : '' }}"
                          value="{{ old('single_price', $vendor->single_price ?? '0.00') }}"
                          {{ $vendor->type == 'in house' ? 'readonly' : '' }}>

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
                                <label class="form-label mb-1 fw-semibold">
                                        Min Qty
                                    </label>
                                    <input type="number"
                                        class="form-control form-control-sm always-disabled"
                                        value="{{ $slab->min_qty }}"
                                        readonly>
                                    <input type="hidden"
                                        name="slabs[{{ $slab->id }}][min_qty]"
                                        value="{{ $slab->min_qty }}">
                                </div>

                                <div class="col-4">
                                <label class="form-label mb-1 fw-semibold">
                                Max Qty
                                    </label>
                                    <input type="number"
                                        class="form-control form-control-sm always-disabled"
                                        value="{{ $slab->max_qty }}"
                                        readonly>
                                    <input type="hidden"
                                        name="slabs[{{ $slab->id }}][max_qty]"
                                        value="{{ $slab->max_qty }}">
                                </div>

                                <div class="col-4">
                               
                                  <div class="position-relative">
                                  
                                  <label class="form-label mb-1 fw-semibold">
                                      {{ $vendor->type === 'in house' ? 'Incentive' : 'Price' }}
                                  </label>
                                      <input type="number"
                                          class="form-control form-control-sm"
                                          name="slabs[{{ $slab->id }}][price]"
                                          value="{{ $saved['slabs'][$slab->id]['price'] ?? $slab->price }}"
                                          placeholder="Enter price">
                                  </div>
                              

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
          
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.service-form').forEach(form => {
      const checkbox = form.querySelector('.service-check');
      // sirf wahi inputs jo checkbox se control hone chahiye
      const editableInputs = form.querySelectorAll(
          'input:not(.service-check):not(.always-disabled)'
      );

      function toggle() {
          editableInputs.forEach(input => {
              input.disabled = !checkbox.checked;
          });
      }

      toggle();
      checkbox.addEventListener('change', toggle);

  });
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
  
      const tabs = document.querySelectorAll('.nav-link');
      const sections = document.querySelectorAll('.tab-pane');
  
      function onScroll() {
          let scrollPos = window.scrollY + 150;
  
          sections.forEach(section => {
              if (
                  section.offsetTop <= scrollPos &&
                  (section.offsetTop + section.offsetHeight) > scrollPos
              ) {
                  let id = section.getAttribute('id');
  
                  tabs.forEach(tab => {
                      tab.classList.remove('active');
                      tab.setAttribute('aria-selected', 'false');
  
                      if (tab.getAttribute('data-target') === '#' + id) {
                          tab.classList.add('active');
                          tab.setAttribute('aria-selected', 'true');
                      }
                  });
              }
          });
      }
  
      window.addEventListener('scroll', onScroll);
  });
</script>
<script>
  $(document).ready(function () {
      $('form').on('submit', function () {
          $('#saveId').prop('disabled', true);
          $('#pageLoader').css('display', 'flex');
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