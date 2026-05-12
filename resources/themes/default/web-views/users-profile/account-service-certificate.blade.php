@extends('layouts.front-end.app')
@section('title', translate('Pooja_Certificate'))
@section('content')
<div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
<div class="row g-3">
@include('web-views.partials._profile-aside')
<section class="col-lg-9">
  @include('web-views.users-profile.service-details.service-order-partial')
  <div class="card border-0">
    <div class="card-body">
      <div class="card border-0 shadow-sm">
        <div class="card border-0 shadow-sm">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="text-center">
                @if (!empty($order['pooja_certificate']))
                <div class="position-relative d-inline-block" style="max-width: 100%; border: 1px solid #ddd; border-radius: 12px; overflow: hidden;">
                  <!-- Download Button - Top Right -->
                  <a href="{{ asset('public/' . $order['pooja_certificate']) }}"
                    download="{{ $order['pooja_certificate'] }}"
                    class="position-absolute"
                    style="
                    top: 10px;
                    right: 10px;
                    background-color: #f8f9fa;
                    color: #212529;
                    border: none;
                    border-radius: 20px;
                    padding: 6px 16px;
                    font-size: 12px;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
                    text-decoration: none;
                    z-index: 10;
                    ">
                  <i class="fa fa-download me-1"></i>
                  </a>
                  <!-- Certificate Image -->
                  <img src="{{ asset('public/' . $order['pooja_certificate']) }}"
                    alt="Pooja Certificate"
                    class="img-fluid"
                    style="width: 100%; max-width: 500px; height: auto;">
                </div>
                @else
                <div class="text-center pt-5 text-capitalize">
                  <img src="{{ asset('public/assets/front-end/img/track-order/certificatec.png') }}"
                    alt="No Certificate"
                    width="70">
                  <h5 class="mt-2 fs-14">{{ translate('no_certificatec_found') }}!</h5>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
</section>
</div>
</div>
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>
@endpush