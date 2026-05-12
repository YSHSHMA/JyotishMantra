@extends('layouts.front-end.app')
@section('title', translate('order_Details'))
@section('content')
<div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
  <div class="row g-3">
    @include('web-views.partials._profile-aside')
    <section class="col-lg-9">
      @include('web-views.users-profile.vipanushthan-details.anushthan-order-partial')
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
                                  <span class="text-muted text-capitalize">{{ translate('temple') }}</span>:<span class="text-primary text-capitalize">{{ $order['pandit']['is_pandit_primary_mandir'] }}</span>
                              </div>
                              <div class="fs-12">
                                  <span class="text-muted text-capitalize">{{ translate('location') }}</span> :<span class="text-primary text-capitalize">{{ $order['pandit']['is_pandit_primary_mandir_location'] }}</span>
                              </div>
                          @else
                              <div class="fs-12">
                                  <span class="text-muted text-capitalize">{{ translate('waiting_for_pandit') }}</span>
                              </div>
                          @endif

                        </div>
                      </td>
                      <td class="order_table_td">
                        <div class="">
                          <div class="py-2">
                            <h6 class="fs-13 font-bold text-capitalize">
                              {{translate('attened_pooja_member_name')}}:
                            </h6>
                          </div>
                          @php
                              $members = json_decode($order->members);
                          @endphp
                             @if(!empty($members))
                             <div class="fs-12">
                                 <span class="text-muted text-capitalize">{{ translate('Member Name') }}</span>: <span class="text-primary text-capitalize">@foreach ($members as $item)
                                   {{$item}}
                               @endforeach </span>
                             </div>
                           @else
                             <div class="fs-12">
                                 <span class="text-muted text-capitalize">{{ translate('No Members Found') }}</span>
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
<script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>
@endpush