@extends('layouts.front-end.app')
@section('title', translate('offline_Pooja_Certificate'))
@section('content')
<style>
    .pac-container{
        z-index: 10000 !important;
    }
        
</style>
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.offlinepooja-details.offlinepooja-order-partial')
                <div class="card border-0">
                    <div class="card-body ">
                        <div class="card text-center">
                            @if (!$order['pooja_certificate'] == null)
                                <img src="{{ asset('public/'.$order['pooja_certificate']) }}" alt=""  class="live-darshan-image-height pb-3">
                                <a href="{{ asset('public/'.$order['pooja_certificate']) }}" download="{{ $order['pooja_certificate'] }}" class="btn btn-success pb-2 mb-2" title="{{ translate('download-certificate') }}">
                                    {{ translate('Download Certificate') }}
                                </a>
                                @else
                                <div class="text-center pt-5 text-capitalize">
                                    <img src="{{ asset('public/assets/front-end/img/track-order/certificatec.png') }}"
                                        alt="" width="70">
                                    <h5 class="mt-1 fs-14">{{ translate('no_certificate_found') }}!</h5>
                                </div>
                                
                                @endif
                            </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

@endsection
@push('script')
<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initAutocomplete" async></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>
@endpush
