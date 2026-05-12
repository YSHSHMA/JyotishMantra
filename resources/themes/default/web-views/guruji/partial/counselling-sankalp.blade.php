<section class="container mb-2">
   @php

   $is_update = 0;

   if (!empty($userInfo)) {
   if (
   !empty($userInfo->name) &&
   !empty($userInfo->dob) &&
   !empty($userInfo->time)
   ) {
   $is_update = 1;
   }
   }
   @endphp
   <div class="row g-3">
      <div class="col-md-6 text-start">
         <div class="card">
            <h4 class="mt-4 fw-bold text-start text-md-center text-warning">
               {{ translate('Enter_your_details_for_counselling') }}
            </h4>
            <hr class="my-2">

            <div class="mx-auto __max-w-760 card-body">
               <h4 class="fw-bold text-warning">
                  {{ translate('Your_WhatsApp_Number') }}
               </h4>
               <span>
                  {{ translate('Your_detailed_consultancy_report_will_be_delivered_as_a_PDF_on_WhatsApp_to_the_number_below') }}.
               </span>

               <div class="counselling-card mt-3">
                  <form action="{{ route('guruji.yajman.store') }}" method="post" id="counsellingForm">
                     @csrf

                     <input type="hidden" name="order_id" value="{{ $orderDetail['order_id'] }}">

                     <h4 class="fw-bold text-center text-warning mb-3">
                        {{ translate('Enter_Your_Details') }}
                     </h4>

                     <div class="row g-3">

                        {{-- PHONE --}}
                        <div class="col-12">
                           <label class="form-label">
                              {{ translate('phone_number') }}
                              <small class="text-primary">(* +91 required)</small>
                           </label>

                           @php
                           $customerPhone = $orderDetail['customers']['phone'];
                           @endphp

                           <input class="form-control" type="tel" value="{{ $customerPhone }}" readonly>
                           <input type="hidden" name="person_phone" value="{{ $customerPhone }}">
                        </div>

                        {{-- NAME --}}
                        <div class="col-md-6">
                           <label class="form-label">{{ translate('name') }}</label>
                           <input class="form-control" type="text" name="name"
                              value="{{ $userInfo->name ?? $orderDetail['customers']['f_name'].' '.$orderDetail['customers']['l_name'] }}"
                              {{ $is_update ? 'readonly' : '' }} required>
                        </div>

                        {{-- GENDER --}}
                        <div class="col-md-6">
                           <label class="form-label">{{ translate('gender') }}</label>
                           <select name="gender" class="form-control" {{ $is_update ? 'disabled' : '' }}>
                              <option value="male" {{ ($userInfo->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                              <option value="female" {{ ($userInfo->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                           </select>
                        </div>

                        {{-- DOB --}}
                        <div class="col-md-6">
                           <label class="form-label">{{ translate('DOB') }}</label>
                           <input class="form-control" type="text" name="dob" id="datepicker"
                              value="{{ $userInfo->dob ?? '' }}"
                              {{ $is_update ? 'readonly' : '' }} required>
                        </div>

                        {{-- TIME --}}
                        <div class="col-md-6">
                           <label class="form-label">{{ translate('Birth_Time') }}</label>
                           <input class="form-control" type="text" name="time" id="timepicker"
                              value="{{ $userInfo->time ?? '' }}"
                              onclick="{{ $is_update ? '' : '$timepicker.open()' }}"
                              {{ $is_update ? 'readonly' : '' }} required>
                        </div>

                        {{-- COUNTRY --}}
                        <div class="col-md-6">
                           <label class="form-label">{{ translate('Country') }}</label>
                           <select name="country" id="country"
                              onchange="{{ $is_update == 0 ? 'countrychange()' : '' }}"
                              class="form-control" {{ $is_update == 1 ? 'disabled' : '' }}>
                              @foreach ($country as $countryName)
                              <option value="{{ $countryName->name }}"
                                 {{ ($userInfo->country ?? 'India') == $countryName->name ? 'selected' : '' }}> {{ $countryName->name }}
                              </option>
                              @endforeach
                           </select>
                        </div>

                        {{-- CITY --}}
                        <div class="col-md-6">
                           <label class="form-label">{{ translate('city') }}</label>

                           <input class="form-control" type="text" id="places"
                              name="places"
                              value="{{ $userInfo->city ?? '' }}"
                              placeholder="{{ translate('Select_City') }}"
                              autocomplete="off"
                              {{ $is_update == 1 ? 'readonly' : '' }} required>

                           <div class="city-list d-none">
                              <ul id="citylist" class="list-group"></ul>
                           </div>
                        </div>

                        {{-- BUTTON --}}
                        <div class="col-12 mt-2 text-center">
                           @if($is_update == 0)
                           <button type="submit" class="btn w-100" style="background:#FF6F00;">
                              {{ translate('Proceed_to_book') }} →
                           </button>
                           @else
                           <a href="{{ route('guruji.individual', ['name' => Str::slug($gurujiname->name)]) }}"
                              class="btn btn-secondary w-100">
                              ← {{ translate('Back_to_Home') }}
                           </a>
                           @endif
                        </div>

                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
      <!-- Booking Informations -->
      <div class="col-md-6 text-start">
         <div class="card">
            <div class="flash_deal_product rtl cursor-pointer mb-2">
               <div class="d-flex p-3">
                  <div class="d-flex align-items-center justify-content-center p-3">
                     <div class="flash-deals-background-image image-default-bg-color">
                        <img src="{{ dynamicStorage(path: 'storage/app/public/pooja/thumbnail/' . $orderDetail['services']['thumbnail']) }}"
                           class="__img-125px" alt="{{ translate('') }}">
                     </div>
                  </div>
                  <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex align-items-center">
                     <div>
                        <h1 class="flash-product-title" style="font-size:15px;font-weight: 600;line-height:20px;margin-bottom:8px;">
                           {{ $orderDetail['services']['name'] }}
                        </h1>
                        <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0">
                           <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/date.png') }}" style="width:20px;height:20px;">
                           <strong>
                           {{ date('d', strtotime($orderDetail->created_at)) }},
                           {{ translate(date('F', strtotime($orderDetail->created_at))) }} ,
                           {{ translate(date('l', strtotime($orderDetail->created_at))) }}
                           </strong>
                        </div>
                        <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0 mt-1">
                           <i class="fa fa-inr text-warning"></i>
                           <strong>{{ $orderDetail->pay_amount }}</strong>
                        </div>
                        <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0 mt-1">
                           <i class="fa fa-truck text-warning"></i>
                           <strong>{{ $orderDetail->order_id }}</strong>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- ================= Tracking Section Added ================= -->
            <div class="tracking-wrapper mt-4">
               <h5 class="fw-bold text-warning mb-3">{{ translate('Order_Tracking') }}</h5>
               {{-- CANCELLED (STATUS = 2) --}}
               @if ($orderDetail->status == 2)
               <div class="text-center p-4">
                  <img src="{{ asset('public/assets/front-end/img/cancel-order.gif') }}" width="120">
                  <h5 class="text-danger mt-3 fw-bold">{{ translate('Order_Cancelled') }}</h5>
                  <p class="text-muted">{{ translate('Your_order_has_been_cancelled') }}</p>
               </div>
               {{-- 🟢 COMPLETED + SHOW DOWNLOAD BUTTON --}}
               @elseif ($orderDetail->status == 1)
               <div class="tracking-steps">
                  {{-- Step 1 --}}
                  <div class="step active">
                     <div class="circle"><i class="fa fa-cogs"></i></div>
                     <span>{{ translate('Processing') }}</span>
                  </div>
                  <div class="line active"></div>
                  {{-- Step 2 – Completed --}}
                  <div class="step active">
                     <div class="circle"><i class="fa fa-check-circle"></i></div>
                     <span>{{ translate('Completed') }}</span>
                     {{-- Download Report Button Here --}}
                     @if (!empty($order['counselling_report']) && $order['counselling_report_verified'] == 1)
                     <div class="mt-3">
                        <a href="{{ asset('storage/app/public/consultation-order-report/' . $order['counselling_report']) }}"
                           download=""
                           class="btn btn-warning btn-sm fw-bold px-3">
                           <i class="fa fa-download me-1"></i> {{ translate('Download_Report') }}
                        </a>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
            {{-- NORMAL TRACKING FOR OTHER STATUS --}}
            @else
            <div class="tracking-steps">
               <div class="step {{ $orderDetail->status >= 0 ? 'active' : '' }}">
                  <div class="circle"><i class="fa fa-cogs"></i></div>
                  <span>{{ translate('Processing') }}</span>
               </div>
               <div class="line {{ $orderDetail->status >= 1 ? 'active' : '' }}"></div>
               <div class="step {{ $orderDetail->status >= 1 ? 'active' : '' }}">
                  <div class="circle"><i class="fa fa-check-circle"></i></div>
                  <span>{{ translate('Completed') }}</span>
               </div>
            </div>
            @endif
         </div>
      </div>
   </div>

   <!-- Full Page Loader -->
   <div id="fullPageLoader"
      style="position: fixed; top:0; left:0; width:100%; height:100%;
      background: rgba(0,0,0,0.4);
      backdrop-filter: blur(1px);
      -webkit-backdrop-filter: blur(1px);
      z-index: 999999;
      display:none;
      align-items:center;
      justify-content:center;
      flex-direction: column;">

      <div class="spinner-border text-light" style="width:4rem;height:4rem;"></div>
   </div>

</section>

<script>
   document.addEventListener('DOMContentLoaded', function() {

      const form = document.getElementById('counsellingForm');
      const loader = document.getElementById('fullPageLoader');

      if (form) {
         form.addEventListener('submit', function(e) {

            //  sirf valid form par loader
            if (form.checkValidity()) {
               loader.style.display = 'flex';
            } else {
               e.preventDefault();
               form.reportValidity();
            }
         });
      }

   });
</script>