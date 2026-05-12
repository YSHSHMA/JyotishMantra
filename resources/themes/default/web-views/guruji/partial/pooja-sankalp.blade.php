<section class="container mb-2">
   @php
      $submitted_data = [];
      $is_update = 0;

      if (!empty($orderDetail) && $orderDetail->order_id) {
         $submitted_data = [
               'newPhone'    => $orderDetail->newPhone ?? '',
               'members'     => isset($orderDetail->members) ? json_decode($orderDetail->members, true) : array_fill(0, $orderDetail['packages']['person'] ?? 1, ''),
               'gotra'       => $orderDetail->gotra ?? '',
               'is_prashad'  => $orderDetail->prashad ?? 0,
               'house_no'    => $orderDetail->house_no ?? '',
               'landmark'    => $orderDetail->landmark ?? '',
               'area'        => $orderDetail->area ?? '',
               'state'       => $orderDetail->state ?? '',
               'city'        => $orderDetail->city ?? '',
               'pincode'     => $orderDetail->pincode ?? '',
         ];

         $is_update = !empty($orderDetail->members) ? 1 : 0;
       }

      $totalPersons = $orderDetail['packages']['person'] ?? 1;
      $members = $submitted_data['members'] ?? array_fill(0, $totalPersons, '');
   @endphp

   @php
      // Check if any address field has value
      $address_filled = false;
      foreach(['house_no','landmark','area','state','city','pincode'] as $field){
         if(!empty($submitted_data[$field])){
               $address_filled = true;
               break;
         }
      }

      // If address is filled, force is_prashad = 1
      $is_prashad = $address_filled ? 1 : ($submitted_data['is_prashad'] ?? 0);
   @endphp

    <div class="row g-3">
      <!-- LEFT SIDE: FORM -->
      <div class="col-md-6 text-start order-1 order-md-1">
         <div class="card">
            <div class="flash_deal_product rtl cursor-pointer mb-2">
               <div class="d-flex p-3">
                  <div class="d-flex align-items-center justify-content-center p-3">
                     <div class="flash-deals-background-image image-default-bg-color">
                        <img src="{{ dynamicStorage(path: 'storage/app/public/pooja/thumbnail/' . $orderDetail['services']['thumbnail']) }}"
                           class="__img-125px" alt="">
                     </div>
                  </div>
                  <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex align-items-center">
                     <div>
                        <h1 class="flash-product-title" style="font-size:15px;font-weight:600;line-height:20px;margin-bottom:8px;">
                           {{ $orderDetail['services']['name'] }}
                        </h1>
                        <div class="d-flex gap-2 align-items-center">
                           <img src="{{ theme_asset('public/assets/front-end/img/track-order/date.png') }}" style="width:20px;height:20px;">
                           <strong>{{ date('d', strtotime($orderDetail->booking_date)) }},
                              {{ translate(date('F', strtotime($orderDetail->booking_date))) }},
                              {{ translate(date('l', strtotime($orderDetail->booking_date))) }}</strong>
                        </div>
                        <div class="d-flex gap-2 align-items-center mt-1">
                           <i class="fa fa-inr text-warning"></i>
                           <strong>{{ $orderDetail->pay_amount ?? '' }}</strong>
                        </div>
                        <div class="d-flex gap-2 align-items-center mt-1">
                           <i class="fa fa-truck text-warning"></i>
                           <strong>{{ $orderDetail->order_id ?? '' }}</strong>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
             <!-- ================= Tracking Section Added ================= -->
            <div class="tracking-wrapper mt-4">
               <h5 class="fw-bold text-warning mb-3">{{ translate('Order_Tracking') }}</h5>
               @if ($orderDetail->status == 2)
               <div class="text-center p-4">
                  <img src="{{ asset('public/assets/front-end/img/cancel-order.gif') }}" width="120">
                  <h5 class="text-danger mt-3 fw-bold">{{ translate('Order_Cancelled') }}</h5>
                  <p class="text-muted">{{ translate('Your_order_has_been_cancelled') }}</p>
               </div>
               @elseif ($orderDetail->status == 1)
               <div class="tracking-steps">
                  <div class="step active">
                     <div class="circle"><i class="fa fa-cogs"></i></div>
                     <span>{{ translate('Processing') }}</span>
                  </div>
                  <div class="line active"></div>
                  <div class="step active">
                     <div class="circle"><i class="fa fa-check-circle"></i></div>
                     <span>{{ translate('Completed') }}</span>
                  </div>
               </div>
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
      
      <!-- RIGHT SIDE: BOOKING INFORMATION -->
      <div class="col-md-6 text-start order-2 order-md-2">
         <div class="card">
            <h4 class="mt-4 fw-bold text-start text-md-center text-warning">
               {{ translate('Enter_your_details_for_pandit_pooja') }}
            </h4>
            <hr class="my-2">
            <div class="mx-auto __max-w-760 card-body">
               <h4 class="fw-bold text-start text-md-left text-warning">
                  {{ translate('Your_WhatsApp_Number') }}
               </h4>
               <span>{{ translate('Your Pooja booking updates like photos, videos and other details will be sent on WhatsApp to the number below') }}</span>

               <form action="{{ route('guruji.yajman.store') }}" method="post" id="poojaForm">
                  @csrf

                  <input type="hidden" name="order_id" value="{{ $orderDetail['order_id'] }}">

                  <div class="row g-3">

                     <!-- PHONE -->
                     <div class="col-12">
                        <label class="form-label">{{ translate('phone_number') }}
                           <small class="text-primary">(* +91 required)</small>
                        </label>
                        <input class="form-control" type="tel"
                           value="{{ $orderDetail['customers']['phone'] ?? '' }}" readonly>
                        <input type="hidden" name="person_phone" value="{{ $orderDetail['customers']['phone'] ?? '' }}">
                     </div>

                     <!-- DIFFERENT CALLING NUMBER -->
                     <div class="col-12">
                        <div class="form-check">
                           <input type="checkbox" class="form-check-input" {{ !empty($submitted_data['newPhone']) ? 'checked' : '' }}  onclick="  let f = document.getElementById('newPhoneBox');  let i = document.querySelector('input[name=newPhone]');
                           if(this.checked){
                              f.style.display='block';
                              i.readOnly=false;
                           } else {
                              f.style.display='none';
                              i.value='';
                              i.readOnly=true;
                           }
                                    "
                              >
                              <label class="form-check-label" for="NewNumberAdd">
                                    {{ translate('I_have_a_different_number_for_calling') }}
                              </label>
                        </div>
                     </div>

                     <div class="col-12" id="newPhoneBox" 
                        style="{{ empty($submitted_data['newPhone']) ? 'display:none;' : 'display:block;' }}">
                           
                        <label class="form-label font-semibold">
                              {{ translate('Enter_new_your_Calling_Number') }}
                        </label>

                        <input 
                              class="form-control"
                              type="tel"
                              name="newPhone"
                              placeholder="{{ translate('enter_new_phone_number') }}"
                              value="{{ $submitted_data['newPhone'] ?? '' }}"
                              {{ empty($submitted_data['newPhone']) ? 'readonly' : '' }}
                              oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                           >
                     </div>

                     <!-- FAMILY MEMBER NAMES -->
                     @for ($person = 0; $person < $totalPersons; $person++)
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="form-label font-semibold">{{ translate('Family_Member') }} *</label>
                              <input class="form-control text-align-direction" type="text"
                                 name="members[]"
                                 value="{{ $members[$person] ?? '' }}"
                                 placeholder="{{ translate('Family_Member') }} {{ $person + 1 }}"
                                 {{ $is_update ? 'readonly' : 'required' }}
                                 autocomplete="off" pattern="^[A-Za-z\s]+$"
                                 title="Only letters and spaces are allowed">
                           </div>
                        </div>
                     @endfor

                     <!-- GOTRA -->
                     <div class="col-12">
                        <label class="form-label">{{ translate('Gotra') }} *</label>
                        <input class="form-control" type="text" name="gotra" id="GotraId"
                           pattern="^[A-Za-z\s]+$"
                           title="Only letters and spaces are allowed"
                           value="{{ $submitted_data['gotra'] ?? '' }}" placeholder="{{ translate('Gotra') }}"
                           {{ $is_update ? 'readonly' : 'required' }}>
                        <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="gotraCheck"
                           {{ old('gotra', $submitted_data['gotra'] ?? false) ? 'checked' : '' }}>

                           <label class="form-check-label" for="gotraCheck">{{ translate('I_do_not_know_my_gotra') }}</label>
                        </div>
                     </div>

                     <!-- PRASAD OPTION -->
                     <div class="col-12">
                        <label class="form-label">{{ translate('Do_you_want_to_get_puja_prasad') }}?</label>

                        <!-- YES BUTTON -->
                        <button type="button"
                           class="btn yes-btn {{ $is_prashad == 1 ? 'btn-warning active' : 'btn-outline-dark' }}"
                           {{ ($is_update == 1 && $is_prashad == 0) ? 'disabled' : '' }}>
                           {{ translate('Yes') }}
                        </button>

                        <!-- NO BUTTON -->
                        <button type="button"
                           class="btn no-btn {{ $is_prashad == 0 ? 'btn-warning active' : 'btn-outline-dark' }}"
                           {{ ($is_update == 1 && $is_prashad == 1) ? 'disabled' : '' }}>
                           {{ translate('No') }}
                        </button>

                        <input type="hidden" name="is_prashad" id="is_prashad" value="{{ $is_prashad }}">
                     </div>

                     <!-- ADDRESS FIELDS -->
                     @foreach(['house_no','landmark','area','state','city','pincode'] as $field)
                        <div class="col-md-6 hideable-div">
                           <input class="form-control"
                                    type="{{ $field == 'pincode' ? 'number' : 'text' }}"
                                    name="{{ $field }}"
                                    id="{{ $field }}"
                                    placeholder="{{ translate(ucfirst(str_replace('_',' ',$field))) }}"
                                    value="{{ $submitted_data[$field] ?? '' }}"
                                    {{ $is_update ? 'readonly' : '' }}>
                        </div>
                     @endforeach

                     <input type="hidden" name="latitude" id="latitude">
                     <input type="hidden" name="longitude" id="longitude">

                     <!-- HIDDEN FIELDS -->
                     <input type="hidden" name="product_id" value="853">
                     <input type="hidden" name="booking_date" value="{{ $orderDetail->created_at }}">
                     <input type="hidden" name="type" value="pooja">
                     <input type="hidden" name="payment_type" value="P">
                     <input type="hidden" name="warehouse_id" value="61202">
                     <input type="hidden" name="seller_id" value="14">
                     <input type="hidden" name="service_id" value="{{ $orderDetail['services']['id'] }}">
                     <input type="hidden" name="user_id" value="{{ $orderDetail['customers']['id'] }}">
                  </div>

                  <!-- Button -->
                  <div class="col-12 mt-2 text-center">
                     <div class="proceed-btn-wrapper">
                        @if($is_update == 0)
                           <button type="submit" id="btnStickyProceed" class="btn w-100" style="background:#FF6F00; border-color:#FF6F00;">
                              {{ translate('Proceed_to_book') }} →
                           </button>
                        @else
                           <a href="{{ route('guruji.individual', ['name' => Str::slug($gurujiname->name)]) }}" class="btn btn-secondary w-100">
                              ← {{ translate('Back to Home') }}
                           </a>
                        @endif
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>    
    </div>

   <!-- Loader -->
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

        <div class="spinner-border text-light" style="width: 4rem; height: 4rem;"></div>
   </div>
 </section>
 
<script>
   document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('poojaForm');
    const loader = document.getElementById('fullPageLoader');

    if (form) {
        form.addEventListener('submit', function (e) {
            // Check if the form is valid
            if (form.checkValidity()) {
                // ✅ Form is valid → show loader
                loader.style.display = 'flex';
            } else {
                e.preventDefault();
                form.reportValidity(); 
            }
        });
      }
      if (typeof isUpdate !== "undefined" && isUpdate === "1") {
        loader.style.display = 'none';
      }
   });
</script>

<script>
   document.addEventListener('DOMContentLoaded', function () {
      const yesBtn = document.querySelector('.yes-btn');
      const noBtn = document.querySelector('.no-btn');
      const hiddenInput = document.getElementById('is_prashad');

      yesBtn.addEventListener('click', function () {
         hiddenInput.value = 1;
         yesBtn.classList.add('btn-warning', 'active');
         yesBtn.classList.remove('btn-outline-dark');
         noBtn.classList.remove('btn-warning', 'active');
         noBtn.classList.add('btn-outline-dark');
      });

      noBtn.addEventListener('click', function () {
         hiddenInput.value = 0;
         noBtn.classList.add('btn-warning', 'active');
         noBtn.classList.remove('btn-outline-dark');
         yesBtn.classList.remove('btn-warning', 'active');
         yesBtn.classList.add('btn-outline-dark');
      });
   });
</script>