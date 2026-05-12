@extends('layouts.front-end.app')
@section('title', translate("Event_Booking"))
<meta name="csrf-token" content="{{ csrf_token() }}">
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/payment.css') }}">
<script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
<script src="https://js.stripe.com/v3/"></script>
<link rel="stylesheet"
   href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<style type="text/css">
   #productList {
   background-color: white;
   border-radius: 6px;
   box-shadow: 2px 2px 2px 2px #f3f3f3;
   }
   /* Prograss */
   @media (min-width: 768px) {
   .md\:top-\[68px\] {
   top: 68px;
   }
   }
   .w-full {
   width: 100%;
   }
   .z-20 {
   z-index: 20;
   }
   .top-0 {
   top: 0;
   }
   .sticky {
   position: sticky;
   }
   .bg-bar {
   --tw-bg-opacity: 1;
   background-color: #f3f4f6;
   }
   .scrollbar-hide {
   -ms-overflow-style: none;
   scrollbar-width: none;
   }
   .overflow-x-scroll {
   overflow-x: scroll;
   }
   .max-w-screen-xl {
   max-width: 1280px;
   }
   .justify-center {
   justify-content: center;
   }
   .items-center {
   align-items: center;
   }
   .px-2 {
   padding-left: .5rem;
   padding-right: .5rem;
   }
   .shrink-0 {
   flex-shrink: 0;
   }
   .text-next {
   --tw-text-opacity: 1;
   color: #1573DF;
   }
   .text-disable {
   --tw-text-opacity: 1;
   color: #5f6672;
   }
   .rounded-full {
   border-radius: 9999px;
   }
   .circle-img-container:hover .circle-img {
   top: -8px;
   left: 0px;
   width: 40px;
   height: 43px;
   z-index: 10;
   max-height: 146px;
   }
   .circle-img-container .circle-img {
   width: 40px;
   height: 43px;
   overflow: hidden;
   position: absolute;
   left: 0;
   top: 0;
   transition: all 0.12s;
   margin-left: -20px;
   background-color: white;
   }
   .rounded-full {
   border-radius: 9999px;
   }
   .bg-center {
   background-position: center;
   }
   .bg-cover {
   background-size: cover;
   }
   .w-full {
   width: 100%;
   }
   .circle-img-container {
   width: 33px;
   height: 40px;
   position: relative;
   }
   .tray {
   text-align: center;
   display: flex;
   flex-wrap: none;
   align-items: center;
   justify-content: center;
   margin-right: 20rem;
   justify-content: center;
   margin-top: 12px;
   }
   .responsive-bg {
   padding-top: 6rem !important;
   padding-bottom: 7rem !important;
   /* background:url("{{ asset('assets/front-end/img/slider/events.jpg') }}") no-repeat; */
   background:url("{{ asset('public/assets/front-end/img/slider/events.jpg') }}") no-repeat;
   background-size: cover;
   background-position: center center;
   }
   @media (max-width: 768px) {
   .responsive-bg {
   padding-top: 2.91rem !important;
   padding-bottom: 3rem !important;
   /* background:url("{{ asset('assets/front-end/img/slider/events1.jpg') }}") no-repeat; */
   background:url("{{ asset('public/assets/front-end/img/slider/events1.jpg') }}") no-repeat;
   background-size: cover;
   background-position: center center;
   }
   .font-size-ten {
   font-size: 9px;
   }
   .font-size-towal {
   font-size: 12px;
   }
   }
   .stm {
   position: relative;
   margin: 0px 0;
   text-align: center;
   z-index: 1;
   width: 99%;
   float: left;
   }
   .stm .lay {
   position: relative;
   background: #ffffff;
   padding: 6px 8px;
   font-size: 11px;
   border: 1px solid #e4eef9;
   font-weight: 500;
   top: -14px;
   border-radius: 2px;
   }
   /* Buttons */
   /* .btn-continue:hover {
   transform: scale(1.03);
   } */
   .btn-outline-primary {
   color: #e63946;
   }
   /* .btn-outline-primary:hover {
   background-color: var(--web-primary) !important;
   border-color: var(--web-primary) !important;
   color: #fff !important;
   } */
   #addDevoteeBtn {
   display: flex;
   align-items: center;
   justify-content: center;
   gap: 6px;
   margin-left: 3rem;
   font-weight: 500;
   padding: 6px 12px;
   font-size: 14px;
   }
   /* Card Layout */
   .right-section {
   display: flex;
   justify-content: flex-end;
   width: 100%;
   }
   .right-section .pay-card {
   width: 90%;
   background: #ffeccc;
   padding: 1.2rem;
   border-radius: 15px;
   box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
   height: auto;
   }
   /* Package + People */
   .package-name {
   font-size: 14px;
   margin-bottom: 5px;
   }
   .people-count {
   font-size: 14px;
   color: #444;
   margin-bottom: 5px;
   }
   /* Coupon Input */
   .input_coupon_code {
   font-size: 14px;
   outline: none !important;
   }
   .coupan_apply_text {
   background: var(--web-primary);
   color: #fff;
   border: none;
   font-size: 12px;
   padding: 4px 12px;
   }
   .coupan_apply_text:hover {
   opacity: 0.9;
   }
   /* Coupon Discount */
   .Coupon_apply_discount_css {
   font-size: 14px;
   color: #e63946;
   font-weight: 600;
   }
   /* Wallet */
   .wallet-text {
   font-size: 15px;
   font-weight: 500;
   color: #333;
   }
   .form-check-input {
   width: 18px;
   height: 18px;
   cursor: pointer;
   }
   /* Final Amount */
   .cart_title {
   font-size: 14px;
   font-weight: 500;
   color: #333;
   }
   .cart_value {
   font-size: 14px;
   font-weight: 600;
   color: #000;
   }
   /* Responsive */
   @media(max-width: 768px) {
   .right-section {
   justify-content: center;
   }
   .right-section .pay-card {
   width: 100%;
   }
   #addDevoteeBtn {
   width: 100%;
   }
   }
</style>
@endpush
@section('content')
@php
$final_price_val = 0;
@endphp
<div class="w-full h-full sticky md:top-[68px] top-0 z-20">
   <div class="bg-bar w-full">
      <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
         <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
            <div class="bg-bar w-full">
               <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
                  <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                     <div class="d-flex justify-center items-center pt-3 pb-3 font-size-ten">
                        <div class="d-flex justify-center items-center">
                           <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                              <path d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z" fill="white"></path>
                           </svg>
                           <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium  ">{{translate('Add Details')}}</div>
                        </div>
                        <div class="px-2 shrink-0 flex text-next">
                           <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z" fill="#9CA3AF"></path>
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z" fill="#9CA3AF"></path>
                           </svg>
                        </div>
                        <div class="d-flex justify-center items-center">
                           <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                              <path d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z" fill="white"></path>
                           </svg>
                           <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">{{ translate('Event')}}</div>
                        </div>
                        <div class="px-2 shrink-0 flex text-next">
                           <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z" fill="#9CA3AF"></path>
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z" fill="#9CA3AF"></path>
                           </svg>
                        </div>
                        <div class="d-flex justify-center items-center">
                           <div class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">3</div>
                           <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">{{ translate('Make Payment')}}</div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@php
$langs = (str_replace('_', '-', app()->getLocale()) == 'in') ? 'hi' : str_replace('_', '-', app()->getLocale());
$date_upcommining = '';
$time_upcommining = '';
$Venue = '';
if (!empty($eventData['all_venue_data']) && json_decode($eventData['all_venue_data'], true)) {
foreach (json_decode($eventData['all_venue_data'], true) as $check) {
$currentDateTime = new DateTime();
$eventDateTime = DateTime::createFromFormat(
'd-m-Y h:i A',
date('d-m-Y', strtotime($check['date'])) . ' ' . date('h:i A', strtotime($check['start_time']))
);
if ($eventDateTime && $eventDateTime > $currentDateTime) {
$Venue = !empty($check[$langs . '_event_venue_full_address'] ?? '')
? ucwords($check[$langs . '_event_venue_full_address'])
: ucwords($check[$langs . '_event_venue'] ?? '');
$date_upcommining = date('d M,Y ,l', strtotime($check['date']));
$time_upcommining = date('H:i:s', strtotime($check['start_time']));
break;
}
}
}
$PoojaTime = 5;
foreach ([2, 4] as $key => $week) {
$currentTimestamp = time();
$nextTimestamp = strtotime($week, $currentTimestamp);
$nextDate = date('d M', $nextTimestamp);
$fullDate = date('m-d-Y', $nextTimestamp);
$fullDates = date('y-m-d', $nextTimestamp);
$nextDates[] = $fullDate;
$fullDates = $fullDate;
}
@endphp
<div class="__inline-23">
   <div class="container rtl mb-3 py-3" id="cart-summary">
      <div class="row g-3 mx-max-md-0">
         <section class="col-lg-12">
            <div class="cards" style="border-radius: 10px;border: 1px solid #e4eef9;">
               <div class="card-header">
                  <div class="details __h-100">
                     <div class="row">
                        <!-- Left Column (Event Name, Venue, Date) -->
                        <div class="col-md-6">
                           <span class="mb-2 __inline-24">
                           {{ ucwords($eventData['event_name'] ?? "") }}
                           </span>
                           <div class="d-flex justify-content-between">
                              <span><b>{{ translate('Next Upcoming Event Venue') }}</b></span>
                           </div>
                           <!-- Venue -->
                           <div class="d-flex align-items-center mb-2 mt-2">
                              <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/temple.png') }}"
                                 alt="" style="width:24px;height:24px;" class="me-2">
                              <span>{{ $Venue }}</span>
                           </div>
                           <!-- Upcoming Date -->
                           <div class="d-flex align-items-center">
                              <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/date.png') }}"
                                 alt="" style="width:24px;height:24px;" class="me-2">
                              <span>{{ $date_upcommining }}</span>
                           </div>
                           <div class="row mt-3">
                              <!-- Members -->
                              <div class="col-md-6">
                                 <div class="card flex-fill">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                       <h6 class="mb-0">Visitor Details</h6>
                                       <i class="bi bi-plus-circle-fill text-primary"></i>
                                    </div>
                                    <div class="card-body p-0">
                                       <ul id="memberList" class="list-group list-group-flush">
                                          <!-- Members will be rendered here dynamically -->
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                              <!-- Aadhaar KYC Members -->
                              <div class="col-md-6">
                                 <div class="card flex-fill">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                       <h6 class="mb-0">Member List</h6>
                                       <i class="bi bi-plus-circle-fill text-success"></i>
                                    </div>
                                    <div class="card-body p-0">
                                       @php
                                       use App\Models\EventLeads;
                                       $eventLead = EventLeads::find($getLeads->id);
                                       $verifiedMembers = collect();
                                       if ($eventLead && $eventLead->user_information) {
                                       $members = json_decode($eventLead->user_information, true);
                                       if (is_array($members)) {
                                       $verifiedMembers = collect($members)
                                       ->where('aadhar_verify_status', '1') // सिर्फ verified वाले
                                       ->unique('aadhar') // Aadhaar unique
                                       ->values();
                                       }
                                       }
                                       @endphp
                                       <ul class="list-group list-group-flush">
                                          @forelse($verifiedMembers as $member)
                                          <li class="list-group-item d-flex justify-content-between align-items-center">
                                             <div>
                                                @if(!empty($member['name']))
                                                <strong>{{ $member['name'] }}</strong><br>
                                                @endif
                                                @if(!empty($member['phone']))
                                                {{ $member['phone'] }}<br>
                                                @endif
                                                @if(!empty($member['aadhar']))
                                                ********{{ substr($member['aadhar'], -4) }}
                                                @endif
                                             </div>
                                             <button type="button"
                                                class="btn btn-outline-primary addKycMemberBtn"
                                                data-name="{{ $member['name'] }}"
                                                data-phone="{{ $member['phone'] }}"
                                                data-aadhar="{{ $member['aadhar'] }}">
                                             <span>➕</span>
                                             </button>
                                          </li>
                                          @empty
                                          <li class="list-group-item text-muted">No Aadhaar Verified Members found</li>
                                          @endforelse
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- Right Column (Price Box with Button) -->
                        <div class="col-md-6">
                           <div class="right-section">
                              <div class="pay-card">
                                 <!--  Package + For People + Button -->
                                 <div class="row align-items-center w-100 mb-3">
                                    <div class="col-8">
                                       @if(!empty($eventData) && json_decode($eventData['all_venue_data'],true))
                                       @foreach (json_decode($eventData['all_venue_data'],true) as $product)
                                       @if(!empty($product['package_list']) && $product['id'] == $getLeads['venue_id'])
                                       @foreach($product['package_list'] as $pacl)
                                       <div class="package-name">
                                          <span><b>{{ \App\Models\EventPackage::where('id',$pacl['package_name'])->first()['package_name'] ?? '' }}</span></b>
                                       </div>
                                       @endforeach
                                       @endif
                                       @endforeach
                                       @endif
                                       @php
                                       $person =$getLeads['qty'];
                                       @endphp
                                       <div class="people-count">
                                          {{ $getLeads->qty}}
                                       </div>
                                    </div>
                                    <div class="col-4 text-end">
                                       <button type="button"
                                          class="btn btn-outline-primary"
                                          data-toggle="modal"
                                          data-target="#devoteeModal"
                                          id="addDevoteeBtn">
                                       <span>➕</span> Member
                                       </button>                              
                                    </div>
                                 </div>
                                 <!--  Wallet Apply -->
                                 @if((\App\Models\User::where('id',auth('customer')->id())->first()['wallet_balance']??0) > 0)
                                 <div class="row w-100 mb-3">
                                    <div class="col-12 text-end">
                                       <div class="form-check d-inline-block">
                                          <label class="form-check-label" for="applyWallet">
                                          {{ translate('Apply Wallet') }}
                                          </label>
                                          <input type="checkbox" 
                                             class="form-check-input wallet_checked ms-2" 
                                             id="applyWallet"
                                             onclick="wallet_calculation()" 
                                             value="1"
                                             data-amount="{{ (\App\Models\User::where('id',auth('customer')->id())->first()['wallet_balance'] ?? 0) }}">
                                       </div>
                                    </div>
                                 </div>
                                 @endif
                                 <!--  Coupon Apply -->
                                 @php
                                 $final_price_val +=$getLeads['total_amount'];
                                 @endphp
                                 <div class="row w-100 mb-3">
                                    <div class="col-12">
                                       <form class="needs-validation" action="javascript:" method="post" novalidate id="coupon-code-events-ajax">
                                          <div class="d-flex form-control rounded-pill ps-3 p-1">
                                             <img width="24" src="{{theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg')}}" alt="" onclick="couponList()">
                                             <input type="hidden" class="user_id" name="user_id" value="{{ auth('customer')->check() ? auth('customer')->user()->id : $userId->id }}">
                                             <input type="hidden" class="coupon_amount" name="amount" value="{{$final_price_val}}" id="mainProductPriceInput">
                                             <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100 input_coupon_code"
                                                type="text" name="coupon_code" placeholder="{{translate('coupon_code')}}">
                                             <button class="btn btn--primary rounded-pill text-uppercase py-1 fs-12 coupan_apply_text"
                                                type="button" id="events-coupon-code" onclick="apply_coupan()">
                                             {{translate('apply')}}
                                             </button>
                                          </div>
                                          <div class="invalid-feedback">{{translate('please_provide_coupon_code')}}</div>
                                       </form>
                                       <span id="route-coupon-events" data-url="{{ url('/api/v1/event/eventcoupon') }}"></span>
                                       <!-- Coupon Discount Row (Shown dynamically) -->
                                       <div class="d-flex justify-content-between mt-2 d-none Coupon_apply_discount_css">
                                          <span class="cart_title">{{translate('coupon_discount')}}</span>
                                          <span class="cart_value Coupon_apply_discount"> - {{ webCurrencyConverter(amount: ($getLeads['coupon_amount']??0)) }} </span>
                                       </div>
                                    </div>
                                 </div>
                                 <!--  show actual wallet balance -->
                                 @if((\App\Models\User::where('id',auth('customer')->id())->first()['wallet_balance']??0) > 0)
                                 <div class="show_user_wallet_amount wallet_info" style="display:none;">
                                    <div class="row align-items-center w-100 mb-3">
                                       <div class="col-8">
                                          <div class="d-flex justify-content-between">
                                             <span class="cart_title text-success font-weight-bold">
                                             <img width="20" src="{{ theme_asset(path: 'public/assets/back-end/img/admin-wallet.png')}}" style="margin-top: -9px;"> 
                                             Wallet Balance
                                             </span>
                                          </div>
                                       </div>
                                       <div class="col-4 text-end">
                                          <small>(<span class="wallet_balance">{{ webCurrencyConverter(amount:(\App\Models\User::where('id',auth('customer')->id())->first()['wallet_balance']??0)) }}</span>)</small>
                                       </div>
                                    </div>
                                    <!--  show wallet amount pay -->
                                    <div class="row align-items-center w-100 mb-3">
                                       <div class="col-8">
                                          <div class="d-flex justify-content-between">
                                             <span class="cart_title text-dark font-weight-bold">Amount Paid (via Wallet)</span>
                                          </div>
                                       </div>
                                       <div class="col-4 text-end">
                                          <span class="cart_value text-dark wallet_used">- ₹0.00</span>
                                       </div>
                                    </div>
                                    <!--  show remaing amount pay -->
                                    <div class="row align-items-center w-100 mb-3">
                                       <div class="col-8">
                                          <div class="d-flex justify-content-between">
                                             <span class="cart_title text-danger font-weight-bold">Remaining Amount to Pay</span>
                                          </div>
                                       </div>
                                       <div class="col-4 text-end">
                                          <span class="cart_value text-danger user_wallet_amount_remaining">₹0.00</span>
                                       </div>
                                    </div>
                                 </div>
                                 @endif
                                 @if($getLeads['amount'] > 0)
                                 <!-- Final Amount -->
                                 <div class="row w-100 mt-2">
                                    <div class="col-12">
                                       <div class="d-flex justify-content-between">
                                          <span class="cart_title text-primary font-weight-bold">Final Amount</span>
                                          <span class="cart_value final_amount" id="mainProductPrice">
                                          {{ webCurrencyConverter(amount: $getLeads->total_amount ?? 0) }}
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row w-100 mt-2">
                                    <div class="col-12">
                                       <button type="button" 
                                          id="proceedCheckoutBtn"
                                          class="btn btn--primary btn-block name_change_continues"
                                          onclick="AddMemberList('razor_pay_form-submit')"
                                          disabled>
                                       {{ translate('Proceed_To_Checkout')}}
                                       </button>
                                    </div>
                                 </div>
                                 @else
                                 <button type="button" 
                                    id="proceedCheckoutBtn"
                                    class="btn btn--primary btn-block name_change_continues"
                                    onclick="AddMemberList('{{ $getLeads['amount'] > 0 ? 'razor_pay_form-submit' : 'booking_free' }}')"
                                    disabled>
                                 {{ translate('Proceed_To_Checkout') }}
                                 </button>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>
      </div>
   </div>
</div>
<div class="modal fade" id="coupon-modal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">Coupons</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body row g-3" id="modal-body">
         </div>
      </div>
   </div>
</div>
@if($getLeads['amount'] > 0)
<form method="post" class="digital_payment razor_pay_form-submit" id="razor_pay_form" action="{{ route('event-payment-request',[$ids,'lead'=>$getLeads]) }}" enctype="multipart/form-data">
   @csrf
   <div class="Details">
      <input type="hidden" value="" class='Coupon_apply_discount'>
      <input type="hidden" id="mainProductPriceInput" value="{{$final_price_val}}">
      <input type="hidden" name="wallet_type" class="user-wallet-adds" value="1">
      <input type="hidden" name="lead" value="{{ $getLeads->id }}">
   </div>
   </div>
</form>
@else
<form method="post" class="booking_free" 
   id="bookingForm" 
   action="{{ route('event-booking-free',[$ids,'lead'=>$getLeads]) }}">
   @csrf
   <input type="hidden" name="lead" value="{{ $getLeads->id }}">
</form>
@endif
<!-- Add New Member Modal -->
<div class="modal fade" id="devoteeModal" tabindex="-1" role="dialog" aria-labelledby="devoteeModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="border-radius: 15px;">
         <!-- Modal Header -->
         <div class="modal-header">
            <h5 class="modal-title" id="devoteeModalLabel">Devotee Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
         </div>
         <!-- Modal Body -->
         <div class="modal-body">
            <form id="devoteeForm">
               @if(($eventData['required_aadhar_status'] ?? 0) == 1)
               <!-- Phone Number -->
               <div class="form-group">
                  <label class="form-label font-semibold">Phone Number
                  <small class="text-primary">( *Country code is must, like 91 for India )</small>
                  </label>
                  <input class="form-control" 
                     type="tel" 
                     name="phone" 
                     id="person-number"
                     placeholder="Enter phone number"
                     maxlength="10"
                     oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
               </div>
               <!-- Aadhaar Number -->
               <div class="form-group">
                  <label for="aadhar_Number1">Aadhaar Number <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="aadhar_Number1" pattern="\d{12}"
                     oninput="this.value = this.value.replace(/\D/g, '').slice(0, 12)"
                     placeholder="Enter 12-digit Aadhaar number">
               </div>
               <!-- Hidden Full Name  -->
               <input type="hidden" id="fullName" name="fullName">
               <!-- Verify Button -->
               <div class="form-group">
                  <button type="button" class="btn btn-warning text-white" onclick="aadharsendOtp()">Verify</button>
               </div>
               <!-- Aadhaar OTP -->
               <div class="form-group d-none aadhar_otp_form">
                  <label for="aadhar_otp">Aadhaar OTP <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="aadhar_otp" pattern="\d{6}"
                     oninput="this.value = this.value.replace(/\D/g, '').slice(0, 6)"
                     placeholder="Enter Aadhaar OTP">
                  <input type="hidden" id="aadhar_request_id">
                  <div class="mt-2">
                     <button type="button" class="btn btn-warning text-white" id="verify_otp_btn" onclick="aadharverifyOtp()">Verify OTP</button>
                  </div>
               </div>
               @else
               <!-- Full Name -->
               <div class="form-group valid_aadhar_use">
                  <label for="fullName">Full Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="fullName" required
                     onkeyup="this.value = this.value.replace(/[^a-zA-Z\s]/g, '').replace(/\b\w/g, l => l.toUpperCase());">
               </div>
               <!-- Phone Number -->
               <div class="form-group valid_aadhar_use" id="phone-group">
                  <label class="form-label font-semibold">Phone Number
                  <small class="text-primary">( *Country code is must, like 91 for India )</small>
                  </label>
                  <input class="form-control text-align-direction phone-input-with-country-picker"
                  type="tel"
                  id="person-number"
                  placeholder="Enter phone number"
                  maxlength="10"
                  oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                  <input type="hidden" class="country-picker-phone-number w-50" readonly>
               </div>
               <!-- Aadhaar Number (Optional) -->
               <div class="form-group valid_aadhar_use">
                  <label for="aadhar_Number">Aadhaar Number (Optional)</label>
                  <input type="text" class="form-control" id="aadhar_Number" pattern="\d{12}"
                     oninput="this.value = this.value.replace(/\D/g, '').slice(0, 12)"
                     placeholder="Enter 12-digit Aadhaar number">
               </div>
               @endif
            </form>
         </div>
         <!-- Modal Footer -->
         <!-- <div class="modal-footer">
            <button type="submit" form="devoteeForm" class="btn btn-warning text-white">Save</button>
         </div> -->
         <div class="modal-footer">
            <button type="submit" form="devoteeForm" 
               class="btn btn-warning text-white {{ ($eventData['required_aadhar_status'] ?? 0) == 1 ? 'd-none' : '' }}" 
               id="saveMemberBtn">
               Save
            </button>
         </div>
      </div>
   </div>
</div>
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>
<script>
   var t = new Date("{{ date('Y-m-d', strtotime($fullDates)) }}");
</script>
<script>
    $('#devoteeModal').on('shown.bs.modal', function () {
        if ("{{ $eventData['required_aadhar_status'] ?? 0 }}" == 1) {
            $('#saveMemberBtn').addClass('d-none');
        }
    });
</script>
<script>
   // Wallet calculation
   function wallet_calculation() {
     let totalAmount = parseFloat($("#mainProductPriceInput").val() || 0);
     let walletBalance = parseFloat($('.wallet_checked').data('amount') || 0);
     let isChecked = $('.wallet_checked').prop('checked');
   
     let walletUsed = 0;
     let finalAmount = totalAmount;
     let remaining = 0;
   
     if (isChecked && walletBalance > 0) {
        $(".user-wallet-adds").val(1);
        walletUsed = Math.min(walletBalance, totalAmount);
        finalAmount = totalAmount - walletUsed;
        remaining = finalAmount;
        $(".wallet_info").show();
     } else {
        $(".user-wallet-adds").val(0);
        walletUsed = 0;
        finalAmount = totalAmount;
        remaining = 0;
        $(".wallet_info").hide();
     }
   
     $(".wallet_used").text(`- ₹${walletUsed.toFixed(2)}`);
     $(".user_wallet_amount_remaining").text(`₹${remaining.toFixed(2)}`);
     $(".final_amount").text(`₹${finalAmount.toFixed(2)}`);
     $('#mainProductPrice').text(`₹${finalAmount.toFixed(2)}`);
   }
   
   $('.wallet_checked').on('change', function () {
     wallet_calculation();
   });
</script>
<script>
   function updateProceedButton() {
    let proceedBtn = document.getElementById("proceedCheckoutBtn");
    if (!proceedBtn) return;
   
    if (existingMembers.length > 0) {
        proceedBtn.removeAttribute("disabled");
    } else {
        proceedBtn.setAttribute("disabled", "disabled");
    }
   }
   
</script>
<script>
   $.ajaxSetup({
     headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
     }
   });
   
   const MultiArrayPush = [];
   
   function updateFinalAmount(totalAmount) {
     totalAmount = parseFloat(totalAmount) || 0;
     $('#mainProductPrice').text(`₹${totalAmount.toFixed(2)}`);
     $('#mainProductPriceInput').val(totalAmount);
     wallet_calculation();
   }
   
   // Render member list dynamically
   function renderMemberList(members) {
      let memberList = document.getElementById("memberList");
      memberList.innerHTML = "";

      members.forEach((member, index) => {
         let li = document.createElement("li");
         li.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");

         li.innerHTML = `
               <div class="d-flex flex-column">
                  <strong>${member.name ?? 'Unknown'}</strong>
                  ${member.phone ? `<span>📞 ${member.phone}</span>` : ''}
                  ${member.aadhar ? `<span>🆔 ${member.aadhar}</span>` : ''}
               </div>
         `;

         // ✅ Remove button (icon only)
         let btn = document.createElement("button");
         btn.classList.add("btn", "btn-outline-danger", "btn-sm", "removeMemberBtn");
         btn.innerHTML = '<i class="fa fa-times"></i>'; 
         btn.setAttribute("data-index", index);
         if (member.aadhar) {
               btn.setAttribute("data-aadhar", member.aadhar);
         }

         li.appendChild(btn);
         memberList.appendChild(li);
      });
   }

   // Initial render from backend
   let existingMembers = [];
   @if(!empty($getLeads->user_information))
   try {
     existingMembers = JSON.parse(@json($getLeads->user_information));
     renderMemberList(existingMembers);
   } catch(e) {
     existingMembers = [];
   }
   @endif
   updateProceedButton();
   
   // Form submit
   $('#devoteeForm').on('submit', function(e) {
   e.preventDefault();
   
   let lead_id = "{{ $getLeads->id ?? '' }}";
   let devoteesData = [];
   
   @if(($eventData['required_aadhar_status'] ?? 0) == 1)
       let name = $('#fullName').val().trim();
       let phone = $('#person-number').val().trim();
       let aadhar = $('#aadhar_Number1').val().trim();
       devoteesData.push({ 
           name, 
           phone, 
           aadhar, 
           verify: 0,
           aadhar_verify_status: 0
       });
   @else
       let name = $('#fullName').val().trim();
       let phone = $('#person-number').val().trim();
       let aadhar = $('#aadhar_Number').val().trim();
       if (!name || !phone) {
           toastr.error('Please fill Name and Phone.');
           return;
       }
       devoteesData.push({ 
           name,
           phone, 
           aadhar,
           verify: 0, 
           aadhar_verify_status: 0
       });
   @endif
   
   // --- Merge OTP verified members first (replace existing default entries if verified) ---
   if (MultiArrayPush.length > 0) {
   MultiArrayPush.forEach(member => {
       const memberAad = String(member.aadhar || '').trim();
       const idx = devoteesData.findIndex(d => String(d.aadhar || '').trim() === memberAad);
   
       // normalize incoming flags to numbers
       const memberVerify = parseInt(member.verify) === 1 ? 1 : 0;
       // if API said verify=1 or incoming aadhar_verify_status=1, treat as verified
       const memberAadharStatus = parseInt(member.aadhar_verify_status) === 1 || memberVerify === 1 ? 1 : 0;
   
       if (idx === -1) {
           // no existing entry -> push verified (or normalized) member
           devoteesData.push({
               name: member.name || '',
               phone: member.phone || '',
               aadhar: memberAad,
               verify: 0,
               aadhar_verify_status: memberAadharStatus
           });
       } else {
           // existing entry found -> upgrade/overwrite only if incoming is verified
           if (memberVerify === 1 || memberAadharStatus === 1) {
               devoteesData[idx] = Object.assign({}, devoteesData[idx], {
                   name: member.name || devoteesData[idx].name || '',
                   phone: member.phone || devoteesData[idx].phone || '',
                   aadhar: memberAad,
                   verify: 0,
                   aadhar_verify_status: 1
               });
           }
           // otherwise keep existing entry as-is (don't downgrade)
       }
   });
   
   // reset after merge
   MultiArrayPush.length = 0;
   }
   
   
   // --- DUPLICATE CHECK ONLY AGAINST EXISTING DATABASE MEMBERS ---
   for (let i = 0; i < devoteesData.length; i++) {
       let newAadhaar = devoteesData[i].aadhar?.trim();
       if (newAadhaar) {
           if (Array.isArray(existingMembers) && existingMembers.some(member => member.aadhar?.trim() === newAadhaar)) {
               toastr.error('This Aadhaar number already exists.');
               return;
           }
       }
   }
   
   // Update backend
   $.ajax({
       url: "{{ route('event-booking-leads-qty-update') }}",
       type: "POST",
       dataType: "json",
       data: {
           lead_id: lead_id,
           type: "members",
           MultiArrayPush: devoteesData
       },
       success: function(response) {
           toastr.success(response.message);
   
           // Update qty
           let newQty = parseInt(response.qty ?? devoteesData.length);
           $('#cart_quantity_web' + lead_id).val(newQty);
   
           // Update total amount
           let totalAmount = parseFloat(response.total_amount ?? 0);
           $("#mainProductPriceInput").val(totalAmount);
           updateFinalAmount(totalAmount); // ✅ already calls wallet_calculation()
   
           // Close modal & reset form
           $('#devoteeModal').modal('hide');
           $('#devoteeForm')[0].reset();
   
           // Append new members to existingMembers and render
           existingMembers = existingMembers.concat(devoteesData);
           renderMemberList(existingMembers);
   
           // Update people count dynamically
          $('.people-count').text(existingMembers.length);
          updateProceedButton();
   
       },
       error: function(xhr) {
           toastr.error("Database update failed");
           console.error(xhr.responseText);
       }
   });
   });
   
   // ------------------ ADD KYC MEMBER BUTTON ------------------
   $(document).on('click', '.addKycMemberBtn', function (e) {
     e.preventDefault();
   
     const name = $(this).data('name') || '';
     const phone = $(this).data('phone') || '';
     const aadhar = String($(this).data('aadhar') || '').trim();
   
     if (!aadhar) {
        toastr.error("Aadhaar is required.");
        return;
     }
   
     // Duplicate check only against local existingMembers
     const exists = existingMembers.some(m => String(m.aadhar || '').trim() === aadhar);
     if (exists) {
        toastr.error("This Aadhaar is already added.");
        return;
     }
   
     // const newMember = { name, phone, aadhar };
     const newMember = { name, phone, aadhar, verify: 1, aadhar_verify_status: 1 };
   
   
     // Send **only this new member** to backend
     $.ajax({
        url: "{{ route('event-booking-leads-qty-update') }}",
        type: "POST",
        data: {
              _token: '{{ csrf_token() }}',
              lead_id: "{{ $getLeads->id }}",
              type: "members",
              MultiArrayPush: [newMember]
        },
        success: function(res) {
              toastr.success("Member added successfully.");
   
              // Add to local array after backend merge
              existingMembers.push(newMember);
   
              // Render updated list
              renderMemberList(existingMembers);
   
              // Update qty / total
              $('#cartQty').text(res.qty);
              $('#peopleCount').text(res.qty);
              updateFinalAmount(res.total_amount); // ✅ auto wallet recalc
   
              updateProceedButton();
        },
        error: function(xhr) {
              console.error(xhr.responseText);
              toastr.error("Something went wrong!");
        }
     });
   });
   
   $(document).on('click', '.removeMemberBtn', function(e) {
      e.preventDefault();

      const aadhaar = $(this).data('aadhar') || null;
      const index = $(this).data('index');

      $.post("{{ route('event-booking-leads-qty-update') }}", {
         lead_id: "{{ $getLeads->id }}",
         type: "remove_member",
         aadhaar: aadhaar,
         index: index
      }, function(res) {
         toastr.success(res.message);

         existingMembers = res.user_information ? JSON.parse(res.user_information) : [];

         $('#cartQty').val(res.qty);
         $('.people-count').text(res.qty);
         updateFinalAmount(res.total_amount);

         if (existingMembers.length === 0) {
               $('#memberList').html('<li class="list-group-item text-muted">No Members found</li>');
         } else {
               renderMemberList(existingMembers);
         }

         updateProceedButton();
      }, "json").fail(function(xhr) {
         console.error(xhr.responseText);
         toastr.error("Failed to remove member.");
      });
   });


   // Aadhaar OTP send
   function aadharsendOtp() {
     let aadhaarNumber = $('#aadhar_Number1').val().trim();
     if (!/^\d{12}$/.test(aadhaarNumber)) {
        toastr.error('Please enter a valid 12-digit Aadhaar Number.');
        return;
     }
   
     $.ajax({
        url: "{{ url('api/v1/darshan/aadhar-send-otp') }}",
        type: "POST",
        data: { aadhaar_number: aadhaarNumber },
        dataType: "json",
        success: function(data) {
              if (data.status == 1) {
                 toastr.success(data.message);
                 $('.aadhar_otp_form').removeClass('d-none');
                 $('#verify_aadhaar_btn').hide(); // hide verify button when OTP is open
                 if (data.data?.name) $('#fullName').val(data.data.name);
                 $('#aadhar_request_id').val(data.request_id);
              } else if (data.status == 2) {
                 toastr.success(data.message);
                 $("#saveMemberBtn").removeClass("d-none");
                 if (data.data?.name) $('#fullName').val(data.data.name);
   
                 let isDuplicate = MultiArrayPush.some(devotee => String(devotee.aadhar || '').trim() === String(data.data.aadhar || '').trim());
                 if (!isDuplicate) {
                    let verifyStatus = parseInt(data.data.verify) === 1 ? 1 : 0;
   
                    // status==2 means API says KYC already done -> set aadhar_verify_status = 1
                    MultiArrayPush.push({
                          aadhar: String(data.data.aadhar || '').trim(),
                          verify: 0,
                          name: data.data.name || '',
                          phone: $('#person-number').val().trim() || '',
                          aadhar_verify_status: (data.data.verify == 1 ? 1 : 0)  
                    });
   
                    console.log("Pushed Devotee (status=2):", MultiArrayPush[MultiArrayPush.length - 1]);
                 }
              else {
                    toastr.error('This Aadhaar number already exists.');
                 }
              } else {
                 toastr.error(data.message);
                 $('.aadhar_otp_form').addClass('d-none');
              }
        },
        error: function(xhr) {
              toastr.error('An error occurred while sending OTP. Please try again.');
              console.error(xhr.responseText);
        }
     });
   }
   
   // Aadhaar OTP verify
   function aadharverifyOtp() {
     let aadhaarNumber = $('#aadhar_Number1').val().trim();
     if (!/^\d{12}$/.test(aadhaarNumber)) {
        toastr.error('Please enter a valid 12-digit Aadhaar Number.');
        return;
     }
   
     let otp = $('#aadhar_otp').val().trim();
     if (!/^\d{6}$/.test(otp)) {
        toastr.error('Please enter a valid 6-digit OTP.');
        return;
     }
   
     $.ajax({
        url: "{{ url('api/v1/darshan/aadhar-otp-verify') }}",
        type: "POST",
        data: {
              otp: otp,
              request_id: $('#aadhar_request_id').val(),
              phone_no: $('#person-number').val().trim(),
              user_id: "{{ auth('customer')->id() }}",
        },
        dataType: "json",
        success: function(data) {
              if (data.status == 1) {
                 toastr.success(data.message);
                 // Aadhaar verified -> Save button show karo
                 $("#saveMemberBtn").removeClass("d-none");
                 if (data.data?.name) $('#fullName').val(data.data.name);
   
                 let isDuplicate = MultiArrayPush.some(devotee => devotee.aadhar.trim() === aadhaarNumber);
                 if (!isDuplicate) {
                    MultiArrayPush.push({
                          aadhar: aadhaarNumber,
                          verify: 0,
                          name: data.data.name,
                          phone: $('#person-number').val().trim(),
                          aadhar_verify_status: 1
                    });
                 }
              } else {
                 toastr.error(data.message);
              }
        },
        error: function(xhr) {
              toastr.error('OTP verification failed. Please try again.');
              console.error(xhr.responseText);
        }
     });
   }
</script>
<script type="text/javascript">
   // Total Payment
   
   function addEventProduct(that) {
      var lead_id = $(that).data('lead_id');
      var venue_id = $(that).data('venue_id');
      var package_id = $(that).data('package_id');
      $.ajax({
         url: "{{ route('event-booking-leads-update') }}",
         method: 'POST',
         data: {
            _token: '{{ csrf_token() }}',
            lead_id: lead_id,
            venue_id: venue_id,
            package_id: package_id,
         },
         success: function(response) {
            location.reload();
         },
         error: function(xhr, status, error) {}
      });
   
   }
</script>
<script>
   // =============================
   // Coupon List Modal
   // =============================
   function couponList() {
      $.ajax({
         type: "POST",
         url: "{{ route('coupon.coupon-list-type') }}",
         _token: '{{ csrf_token() }}',
         data: { type: "event" },
         success: function(response) {
            $('#modal-body').html('');
            if (response.status == 200 && response.coupons.length > 0) {
               let body = '';
               $.each(response.coupons, function(key, value) {
                  let expireDate = new Date(value.expire_date);
                  let formattedDate = expireDate.toLocaleString('en-GB', {
                     day: 'numeric', month: 'short', year: 'numeric'
                  });
   
                  body += `
                     <div class="col-lg-6">
                        <div class="ticket-box">
                           <div class="ticket-start">
                              <img width="30" src="{{ asset('public/assets/front-end/img/icons/dollar.png') }}" alt="">
                              <h2 class="ticket-amount">
                                 ${value.discount_type === 'percentage' ? value.discount+'%' : '₹'+value.discount}
                              </h2>
                              <p>On All Events</p>
                           </div>
                           <div class="ticket-border"></div>
                           <div class="ticket-end">
                              <button class="ticket-welcome-btn couponid click-to-copy-coupon couponid-${value.code}" 
                                 data-value="${value.code}" onclick="copyToClipboard(this)">
                                 ${value.code}
                              </button>
                              <button class="ticket-welcome-btn couponid-hide d-none couponhideid-${value.code}">Copied</button>
                              <h6>Valid till ${formattedDate}</h6>
                              <p class="m-0">Available from minimum purchase ₹${value.min_purchase}</p>
                           </div>
                        </div>
                     </div>`;
               });
               $('#modal-body').append(body);
               $('#coupon-modal').modal('show');
            } else {
               $('#modal-body').css({
                  'display': 'flex',
                  'justify-content': 'center',
                  'padding': '50px 0',
                  'color': 'red'
               }).text('Coupons not available');
               $('#coupon-modal').modal('show');
            }
         },
         error: function(xhr) {
            toastr.error('Coupon fetch failed');
            console.error(xhr);
         }
      });
   }
   
   // =============================
   // Copy coupon code into input
   // =============================
   function copyToClipboard(button) {
      const value = button.dataset.value;
      if ($('.input_coupon_code').val() === '') {
         $('.input_coupon_code').val(value);
         $('#coupon-modal').modal('hide');
      } else {
         navigator.clipboard.writeText(value)
            .then(() => toastr.success("Copied to clipboard"))
            .catch(() => toastr.error("Failed to copy"));
      }
   }
   
   // =============================
   // Apply Coupon
   // =============================
   function apply_coupan() {
      $.ajaxSetup({
         headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      });
   
      let original_amount = parseFloat($("input[name='amount']").val() || 0);
      let couponCode = $('.input_coupon_code').val();
   
      if (!couponCode) {
         toastr.error('Enter coupon code');
         return;
      }
      if (original_amount < 1) {
         toastr.error('Ensure amount ≥ 1');
         return;
      }
   
      $.post($('#route-coupon-events').data('url'), {
         amount: original_amount,
         user_id: "{{ auth('customer')->check() ? auth('customer')->user()->id : 0 }}",
         coupon_code: couponCode
      }, function(data) {
         let coupon_amount = parseFloat(data.data?.coupon_amount || 0);
         let final_amount = original_amount - coupon_amount;
         if (final_amount < 0) final_amount = 0;
   
         if (data.status == 1) {
            $(".coupan_apply_text")
               .text("{{ translate('remove') }}")
               .attr("onclick", "removeCoupon()");
            $(".Coupon_apply_discount").val(coupon_amount).text(`- ₹${coupon_amount.toFixed(2)}`);
            $(".Coupon_apply_id").val(data.data.coupon_id);
            $(".Coupon_apply_discount_css").addClass('d-flex').removeClass('d-none');
   
            $("#mainProductPriceInput").val(final_amount);
            $('#mainProductPrice').text(`₹${final_amount.toFixed(2)}`);
   
            toastr.success(data.message);
   
            // ✅ update DB after coupon applied
            updateLeadCoupon(data.data.coupon_id, coupon_amount, final_amount);
         } else {
            resetCouponUI(original_amount);
            toastr.error(data.message);
         }
      });
   }
   
   // =============================
   // Remove Coupon
   // =============================
   function removeCoupon() {
      let original_amount = parseFloat($("input[name='amount']").val() || 0);
   
      // ✅ DB reset call
      updateLeadCoupon(null, 0, original_amount);
   
      // ✅ Reset UI
      resetCouponUI(original_amount);
   
      toastr.info("Coupon removed successfully");
   }
   
   // =============================
   // Reset Coupon UI Helper
   // =============================
   function resetCouponUI(original_amount) {
      $(".coupan_apply_text")
         .text("{{ translate('apply') }}")
         .attr("onclick", "apply_coupan()");
      $(".Coupon_apply_discount_css").addClass("d-none").removeClass("d-flex");
      $(".Coupon_apply_discount").text('').val(0);
      $(".Coupon_apply_id").val('');
   
      $("#mainProductPriceInput").val(original_amount);
      $('#mainProductPrice').text(`₹${original_amount.toFixed(2)}`);
   }
   
   // =============================
   // Update only coupon fields in lead
   // =============================
   function updateLeadCoupon(coupon_id, coupon_amount, final_amount) {
      let lead_id = "{{ $getLeads['id'] }}";
   
      $.ajax({
         url: "{{ route('event-booking-leads-qty-update') }}",
         type: "POST",
         data: {
            _token: '{{ csrf_token() }}',
            lead_id: lead_id,
            coupon_id: coupon_id,
            coupon_amount: coupon_amount,
            total_amount: final_amount
         },
         success: function (response) {
            console.log("Lead coupon updated successfully", response);
   
            // ✅ UI sync if backend sends correct total
            if(response.total_amount){
               $("#mainProductPriceInput").val(response.total_amount);
               $('#mainProductPrice').text(`₹${parseFloat(response.total_amount).toFixed(2)}`);
            }
         },
         error: function (xhr) {
            toastr.error("Lead update failed");
            console.error(xhr);
         }
      });
   }
</script>
<script>
   function AddMemberList(className) {
       $.ajax({
           url: "{{ route('event-booking-leads-qty-update') }}",
           method: 'POST',
           data: {
               _token: '{{ csrf_token() }}',
               lead_id: "{{ $getLeads['id'] }}",
               MultiArrayPush: MultiArrayPush,
               type: "members"
           },
           beforeSend: function() {
               $('#loading').removeClass('d--none').css('z-index', 1000);
           },
           success: function(response) {
               @if($getLeads['amount'] > 0)
                   // Paid booking → Razorpay form submit
                   $(`.${className}`).submit();
               @else
                   // Free booking → Direct submit to free booking form
                   $('#bookingForm').submit();
               @endif
   
               $('#loading').addClass('d--none');
           },
           error: function(xhr, status, error) {
               console.error(xhr.responseText);
               $('#loading').addClass('d--none');
           }
       });
   }
</script>
@endpush