@extends('layouts.front-end.app')
@section('title', translate('order_Details'))
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
      <div class="bg-white border-lg rounded mobile-full">
        <div class="p-lg-3 p-0">
        <div class="card border-sm">
            <div class="p-lg-3">
              <div class="border-lg rounded payment mb-lg-3 table-responsive">
                <table class="table table-borderless mb-0">
                  <thead>
                    <tr class="order_table_tr">
                      <td class="order_table_td">
                        <div class="">
                          <div class="_1 py-2 d-flex justify-content-between align-items-center">
                            <h6 class="fs-13 font-bold text-capitalize">{{translate('pandit_info')}}</h6>
                          </div>
                          @if(!empty($order['pandit']))
                              <div class="fs-12">
                                  <span class="text-muted text-capitalize">{{ translate('pandit_name') }}</span>:<span class="text-primary text-capitalize">{{ $order['pandit']['name'] }}</span>
                              </div>
                              <div class="fs-12">
                                  <span class="text-muted text-capitalize">{{ translate('pandit_email') }}</span>:<span class="text-primary ">{{ $order['pandit']['email'] }}</span>
                              </div>
                              <div class="fs-12">
                                  <span class="text-muted text-capitalize">{{ translate('Pandit_mobile') }}</span> :<span class="text-primary text-capitalize">{{ $order['pandit']['mobile_no'] }}</span>
                              </div>
                          @else
                              <div class="fs-12">
                                  <span class="text-muted text-capitalize">{{ translate('waiting_for_pandit') }}</span>
                              </div>
                          @endif

                        </div>
                      </td>
                    </tr>
                  </thead>
                </table>
              </div>
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