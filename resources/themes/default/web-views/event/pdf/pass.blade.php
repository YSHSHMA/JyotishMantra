@extends('layouts.front-end.app')
@section('title', translate('Event_Information'))
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/social-icon.css') }}">
<style>
   .star-rating {
      display: block;
      gap: 5px;
      font-size: 30px;
      cursor: pointer;
   }

   .star-rating i {
      color: #fe9802;
      transition: color 0.2s;
   }

   .star-rating i.filled {
      color: #fe9802;
   }
.star-rating-display-contents {
        display: contents;
    }
   @media (max-width: 767px) {
      .order_table_td {
         display: block;
         width: 100%;
      }

      .order_table_tr {
         display: block;
         margin-bottom: 20px;
      }

      .calculation-table td {
         font-size: 14px;
      }

      .nav--tabs .nav-link {
         font-size: 14px;
         padding: 5px 10px;
      }

      .rating__card__quote {
         font-size: 14px;
      }
   }
</style>

@endpush
@section('content')
<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
   <div class="row">
      @include('web-views.partials._profile-aside')
      <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
         <div class="card __card web-direction customer-profile-orders">
            <div class="card-body">
               <div class="align-items-center justify-content-between mb-0 mb-md-3">
                  <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none float-end">
                     <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                        fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z" fill="white" />
                     </svg>
                  </button>
                  <span class="font-weight-bold"> {{ translate('order_id')}} </span>: #{{ ($orderData['order_no']??"") }} <br>
                  <span class="font-weight-bold"> {{ translate('Event_Name')}} </span>: {{ ($orderData['eventid']['event_name']??"") }}
               </div>
               <ul class="nav nav-tabs nav--tabs d-flex justify-content-start mt-3 border-top border-bottom py-2"
                  role="tablist">
                  <li class="nav-item">
                     <a class="nav-link __inline-27 active" href="#order_summary" data-toggle="tab" role="tab">
                        {{translate('order_summary')}}
                     </a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link __inline-27" href="#all_order" data-toggle="tab" role="tab">
                        {{ translate('Pass') }}
                     </a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link __inline-27" href="#event_review" data-toggle="tab" role="tab">
                        {{ translate('Review_add') }}
                     </a>
                  </li>
               </ul>
               <div class="tab-content px-lg-3">
                  <div class="tab-pane fade show active text-justify" id="order_summary" role="tabpanel">
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
                                                      <h6 class="fs-13 font-bold text-capitalize">{{translate('payment_info')}}</h6>
                                                   </div>
                                                   <div class="fs-12">
                                                      <span class="text-muted text-capitalize">{{translate('payment_status')}}</span>:
                                                      @if ($orderData['transaction_status'] == 1)
                                                      <span class="text-success text-capitalize">{{ translate('paid') }}</span>
                                                      @else
                                                      <span class="text-success text-capitalize">{{ translate('unpaid') }} </span>
                                                      @endif
                                                   </div>
                                                   <div class="mt-2 fs-12">
                                                      <span class="text-muted text-capitalize">{{translate('payment_method')}}</span> :<span class="text-primary text-capitalize">
                                                         @if($orderData['transaction_id'] == 'wallet')
                                                         {{ translate('Wallet') }}
                                                         @else
                                                         {{ translate('online') }}
                                                         @endif
                                                      </span>
                                                   </div>
                                                   @if($orderData['status'] == 3)
                                                   <div class="mt-2 fs-12">
                                                      <span class="text-muted text-capitalize">{{translate('Refund_status')}}</span> :<span class="text-primary text-capitalize">Refunded</span>
                                                   </div>
                                                   @endif
                                                   <div class="mt-2 fs-12">
                                                      <span class="text-muted text-capitalize">{{translate('Event_name')}}</span> : <span>{{$orderData['eventid']['event_name']}}</span> <br>
                                                      <?php
                                                      $venueData = [];
                                                      $langs = str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale());

                                                      if ($orderData['eventid'] && !empty($orderData['eventid']['all_venue_data'])) {
                                                         $allVenues = json_decode($orderData['eventid']['all_venue_data'], true);
                                                         $venueData = collect($allVenues)->firstWhere('id', $orderData['venue_id']);
                                                      }
                                                      ?>
                                                      <span class="text-muted text-capitalize">{{translate('Event_address')}}</span> : <span>{{ ((!empty($venueData[$langs . '_event_venue_full_address']??'')) ? ucwords($venueData[$langs . '_event_venue_full_address']??'') : ucwords($venueData[$langs . '_event_venue']??'')); }}</span><br>
                                                      <span class="text-muted text-capitalize">{{translate('Event_date')}}</span> : <span>{{date('d M, Y',strtotime($venueData['date']))}} {{ ($venueData['start_time']??'')}}</span><br>
                                                      <span class="text-muted text-capitalize">{{translate('booking_date')}}</span> : <span>{{date('d M, Y H:i A',strtotime($orderData['created_at']))}}</span>
                                                   </div>
                                                </div>
                                             </td>
                                             <td class="order_table_td">
                                                <div class="">
                                                   <div class="py-2">
                                                      <h6 class="fs-13 font-bold text-capitalize">
                                                         {{translate('User_info')}}:
                                                      </h6>
                                                   </div>
                                                   <div class="">
                                                      <span class="text-capitalize fs-12">
                                                         <span class="text-capitalize">
                                                            <span
                                                               class="min-w-60px">{{translate('name')}}</span> : &nbsp;{{($orderData['userData']['name']??"")}}
                                                         </span>
                                                         <br>
                                                         <span class="text-capitalize">
                                                            <span
                                                               class="min-w-60px">{{translate('phone')}}</span> : &nbsp;{{ ($orderData['userData']['phone']??"")}},
                                                         </span>
                                                         <br>
                                                         <span style="    text-transform: lowercase;">
                                                            <span
                                                               class="min-w-60px">{{translate('Email')}}</span> : &nbsp;{{($orderData['userData']['email']??"")}},
                                                         </span>
                                                      </span>
                                                   </div>
                                                </div>
                                             </td>
                                          </tr>
                                       </thead>
                                    </table>
                                 </div>
                                 <div class="payment mb-3 table-responsive d-none d-lg-block">
                                    <table class="table table-borderless min-width-600px">
                                       <thead class="thead-light text-capitalize">
                                          <tr class="fs-13 font-semibold">
                                             <th class="px-5">{{ translate('package') }}</th>
                                             <th>{{ translate('people_qty') }}</th>
                                             <th>{{ translate('price') }}</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          @if($orderData['orderitem'])
                                          @foreach($orderData['orderitem'] as $ev_index)
                                          <tr>
                                             <td>{{ (\App\Models\EventPackage::where('id',$ev_index['package_id'])->first()['package_name']??"") }}</td>
                                             <td>{{ $ev_index['no_of_seats']}}</td>
                                             <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount:((double)$ev_index['amount']??0)), currencyCode: getCurrencyCode())  }}</td>
                                          </tr>
                                          @endforeach
                                          @endif
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                           <div class="row d-flex justify-content-end mt-2">
                              <div class="col-md-8 col-lg-5">
                                 <div class="bg-white border-sm rounded">
                                    <div class="card-body ">
                                       <table class="calculation-table table table-borderless mb-0">
                                          <tbody class="totals">
                                             <tr>
                                                <td>
                                                   <div class="text-start">
                                                      <span class="font-semibold">{{translate('item')}}</span>
                                                   </div>
                                                </td>
                                                <td>
                                                   <div class="text-end">
                                                      <span class="font-semibold">{{translate('Price')}}</span>
                                                   </div>
                                                </td>
                                             </tr>
                                             <tr class="border-top">
                                                <td>
                                                   <div class="text-start">
                                                      <span class="product-qty">{{translate('subtotal')}}</span>
                                                   </div>
                                                </td>
                                                <td>
                                                   <div class="text-end">
                                                      <span class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($orderData['amount'] + $orderData['coupon_amount'])), currencyCode: getCurrencyCode()) }}</span>
                                                   </div>
                                                </td>
                                             </tr>
                                             <tr class="border-top">
                                                <td>
                                                   <div class="text-start">
                                                      <span class="product-qty">{{translate('coupon_discount')}}</span>
                                                   </div>
                                                </td>
                                                <td>
                                                   <div class="text-end">
                                                      <span class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($orderData['coupon_amount'])), currencyCode: getCurrencyCode()) }}</span>
                                                   </div>
                                                </td>
                                             </tr>
                                             <tr class="border-top">
                                                <td>
                                                   <div class="text-start">
                                                      <span class="font-weight-bold">
                                                         <strong>{{translate('total_Price')}}</strong>
                                                      </span>
                                                   </div>
                                                </td>
                                                <td>
                                                   <div class="text-end">
                                                      <span class="font-weight-bold amount">
                                                         {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $orderData['amount']), currencyCode: getCurrencyCode()) }}
                                                      </span>
                                                   </div>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade text-justify" id="all_order" role="tabpanel">
                     @if($ticket > 0)
                     <div class="table-responsive">
                        <table class="table __table __table-2 text-center">
                           <thead class="thead-light">
                              <tr>
                                 <td class="tdBorder">
                                    <div>
                                       <span class="d-block spandHeadO text-start text-capitalize">
                                          {{ translate('Member') }}
                                       </span>
                                    </div>
                                 </td>
                                 <td class="tdBorder">
                                    <div>
                                       <span class="d-block spandHeadO">
                                          {{ translate('Action') }}
                                       </span>
                                    </div>
                                 </td>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              $listOrders = json_decode($orderData['orderitem'][0]['user_information'], true);
                              ?>

                              @for($i = 0; $i < $ticket; $i++)
                                 <tr>
                                 <td class="bodytr">
                                    <div class="media-order">
                                       <div class="cont text-start">
                                          <span class="fs-12 font-weight-medium">
                                             {{ translate("Name") }} : <span class="font-weight-bolder">{{ $listOrders[$i]['name']??"" }} ({{ $listOrders[$i]['phone']??"" }})</span><br>
                                             @if($listOrders[$i]['aadhar']??"")
                                             { Aadhar N. <span class="font-weight-bold">{{ $listOrders[$i]['aadhar']??"" }}</span> }
                                             @endif


                                          </span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="bodytr">
                                    <div class="__btn-grp-sm flex-nowrap">
                                       <a href="{{route('event-order-details2',[$orderData['id'],($i+1)])}}" title="{{ translate('download_Pass') }}" class="btn-outline-success text-success __action-btn btn-shadow rounded-full">
                                          <i class="tio-download-to"></i>
                                       </a>
                                    </div>
                                 </td>
                                 </tr>
                                 @endfor
                           </tbody>
                        </table>
                     </div>
                     @endif
                  </div>
                  {{-- Review_add --}}
                  <div class="tab-pane fade  text-justify" id="event_review" role="tabpanel">
                     @php
                     $getEventReview = \App\Models\EventsReview::where(['order_id'=>$orderData['id'], 'user_id'=>$orderData['user_id'],'event_id'=>$orderData['event_id']])->first();
                     @endphp
                     @if(!$getEventReview || $getEventReview['is_edited'] == 0)
                     <form action="{{ route('add-event-review')}}" method="post">
                        @csrf
                        <div class="col-12 form-group text-center">
                           <label>{{ translate('Give_Your_Rating_&_Feedback') }}</label>
                           <div class="star-rating" id="starRating">
                              @for ($i = 1; $i <= 5; $i++)
                                 <i class="far fa-star" data-index="{{ $i }}"></i>
                                 @endfor
                           </div>
                           <input type="hidden" name="star" id="ratingInput" value="0">
                        </div>
                        <div class="col-12 mt-2">
                           <input type="hidden" value="{{$orderData['event_id'] }}" name="event_id">
                           <input type="hidden" value="{{$orderData['id'] }}" name="order_id">
                           <label for="message" class="form-label">{{ translate('Message') }}</label>
                           <textarea name="message" class="form-control">{{ $getEventReview['comment']??'' }}</textarea>
                        </div>
                        <div class="col-12 mt-2">
                           <button type="submit" class="btn btn-sm btn-primary float-end">{{ translate('Save') }}</button>
                        </div>
                     </form>
                     @else
                     <section class="rating__card text-center">
                        <blockquote class="rating__card__quote">“{{ $getEventReview['comment'] }}”</blockquote>
                        <div class="rating__card__stars">
                           @for ($i = 1; $i <= 5; $i++)
                              @if ($i <=$getEventReview['star'])
                              <i class="fa fa-star star-rating text-warning star-rating-display-contents"></i>
                              @else
                              <i class="fa fa-star-o star-rating star-rating-display-contents"></i>
                              @endif
                              @endfor
                              <br>
                              <span class="rating__card__stars__name">{{ $getEventReview['userData']['name'] }}</span>
                        </div>
                        <p class="rating__card__bottomText">{{ date('h:i A, d M Y', strtotime($getEventReview['created_at'])) }}</p>
                     </section>
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
<!-- jQuery Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
      const stars = document.querySelectorAll('#starRating i');
      const ratingInput = document.getElementById('ratingInput');

      let currentRating = 0;

      stars.forEach(star => {
         star.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-index'));

            if (index === currentRating) {
               currentRating = 0;
            } else {
               currentRating = index;
            }

            ratingInput.value = currentRating;

            stars.forEach((s, i) => {
               if (i < currentRating) {
                  s.classList.remove('far');
                  s.classList.add('fas', 'filled');
               } else {
                  s.classList.remove('fas', 'filled');
                  s.classList.add('far');
               }
            });
         });
      });
   });
</script>