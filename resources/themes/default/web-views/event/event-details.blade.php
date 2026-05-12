@extends('layouts.front-end.app')
@section('title', translate('Event'))
@push('css_or_js')
<link rel="stylesheet"
   href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/product-details.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />


<style>
   .owl-nav .owl-prev,
   .owl-nav .owl-next {
      font-size: 24px;
      color: #fff;
      background: #333;
      border-radius: 50%;
      padding: 10px;
      position: absolute;
      top: 40%;
      cursor: pointer;
      z-index: 1000;
   }

   .owl-nav .owl-prev {
      left: -6px;
   }

   .owl-nav .owl-next {
      right: -6px;
   }

   a.section-link.active {
      color: #ffffff !important;
      background: var(--base) !important;
      font-weight: bold;
   }

   a.section-link {
      border-radius: 100px !important;
      padding: 9px 17px;
      /* font-size: 13px; */
      text-decoration: none;
   }

   .user-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
   }

   .review-comment {
      display: inline-block;
      word-wrap: break-word;
      width: 100%;
   }

   .read-more-btn {
      color: #007bff;
      cursor: pointer;
      font-size: 14px;
      display: block;
      margin-top: 10px;
   }

   .countdown {
      display: flex;
      justify-content: center;
      gap: 13px;
      margin-right: 13rem;
   }

   .countdown>div {
      display: flex;
      flex-wrap: nowrap;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      margin-top: 4px;
      box-shadow: 2px 2px 3px #fe9802;
      width: 62px;
      height: 45px;
      padding: 4px;
      font-size: 12px;
      border-radius: 5px;
   }

   .number {
      font-weight: 500;
      font-size: 25px;
      color: var(--web-primary);
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

   .border-bar {
      --tw-border-opacity: 1;
      border-color: #5f6672 !important;
   }

   .border {
      border-width: 1px;
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


   .otp-input-fields {
      margin: auto;
      max-width: 400px;
      width: auto;
      display: flex;
      justify-content: center;
      gap: 20px;
      padding: 10px;
   }

   .otp-input-fields input {
      height: 42px;
      width: 41px;
      background-color: transparent;
      border-radius: 4px;
      border: 1px solid #2f8f1f;
      text-align: center;
      outline: none;
      font-size: 18px;
      /* Firefox */
   }

   .otp-input-fields input::-webkit-outer-spin-button,
   .otp-input-fields input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
   }

   .otp-input-fields input[type=number] {
      -moz-appearance: textfield;
   }

   .otp-input-fields input:focus {
      border-width: 2px;
      border-color: #287a1a;
      font-size: 20px;
   }

   .product-preview-item {
      /* height: 60% !important; */
      aspect-ratio: unset;
   }

   .section-content {
      padding-bottom: 25px;
   }

   /* Make scrollbar very thin */
   ::-webkit-scrollbar {
      width: 2px;
      /* Change to 2px if 1px is too small to be visible */
      height: 2px;
   }

   /* Change scrollbar track */
   ::-webkit-scrollbar-track {
      background: transparent;
   }

   /* Change scrollbar thumb */
   ::-webkit-scrollbar-thumb {
      background: #888;
      /* Change color */
      border-radius: 10px;
   }

   /* Hide scrollbar when not scrolling */
   ::-webkit-scrollbar-thumb:hover {
      background: #555;
   }

   .inclu::before {
      content: '';
      position: absolute;
      left: 0;
      background: #e74c3c;
      height: 30px;
      width: 5px;
   }

   @media (max-width: 768px) {
      .navbar_section1 {
         font-size: 9px;
         top: 0px !important;
      }

      #breadcrum-container {
         font-size: 11px;
      }

      .venue-font-size {
         font-size: 8px;
      }

      .navbar_section1 a.section-link {
         padding: 6px 6px;
      }
   }

   .button-sticky {
      border-radius: 5px 5px 0 0;
      border: 1px solid rgba(20, 85, 172, 0.05);
      box-shadow: 0 -7px 30px 0 rgba(0, 113, 220, 0.1);
      position: sticky;
      bottom: 0;
      left: 0;
      z-index: 1000;
      transition: all 150ms ease-in-out;
   }

   @media (max-width: 320px) {
      .otp-input-fields input {
         height: 35px;
         width: 35px;
      }

      .otp-input-fields {
         gap: 9px;
      }
   }
</style>
<style>
   .booking-flow {
      display: flex;
      flex-direction: column;
      gap: 30px;
   }

   .step {
      background: white;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      padding: 25px;
      transition: transform 0.3s ease;
   }

   .step:hover {
      transform: translateY(-5px);
   }


   /* Venue Selection */
   .venues-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
   }

   .venue-card {
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-align: center;
   }

   .venue-card:hover {
      border-color: #ff9200;
      transform: scale(1.03);
   }

   .venue-card.selected {
      border-color: #ff9200;
      background-color: #ffd0915c;
   }

   .venue-icon {
      font-size: 2.5rem;
      color: #ff9200;
      margin-bottom: 15px;
   }

   .venue-name {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 10px;
      color: #2c3e50;
   }

   .venue-capacity {
      color: #7f8c8d;
      font-size: 0.9rem;
   }

   /* Date Selection */
   .dates-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 15px;
   }

   .date-card {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
   }

   .date-card:hover {
      border-color: #ff9200;
   }

   .date-card.selected {
      background-color: #ffd0915c;
      /* color: white; */
      border-color: #ff9200;
   }

   .date-day {
      font-size: 1.1rem;
      font-weight: 600;
   }

   .date-full {
      font-size: 0.9rem;
      margin: 5px 0;
   }

   .date-time {
      font-size: 0.8rem;
      color: #7f8c8d;
   }

   /* .date-card.selected .date-time {
      color: #e0e0e0;
   } */

   /* Package Selection */
   .packages-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
   }

   .package-card {
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 25px 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
   }

   .package-card:hover {
      transform: translateY(-5px);
   }

   .package-card.selected {
      border-color: #ff9200;

   }

   .package-badge {
      position: absolute;
      top: 0;
      right: 0;
      background: #3498db;
      color: white;
      padding: 5px 15px;
      font-size: 0.8rem;
      border-bottom-left-radius: 8px;
   }

   .package-name {
      font-size: 1rem;
      font-weight: 600;
   }

   .package-price {
      font-size: 1.4rem;
      font-weight: 700;
   }

   .package-free .package-price {
      color: #2ecc71;
   }

   .package-gold .package-price {
      color: #f39c12;
   }

   .package-platinum .package-price {
      color: #9b59b6;
   }

   .package-features {
      list-style: none;
      text-align: left;
      margin-top: 10px;
      height: 153px;
      overflow: auto;
   }

   .package-features li {
      padding: 8px 0;
      border-bottom: 1px dashed #eee;
   }

   .package-features li:last-child {
      border-bottom: none;
   }

   .package-features li i {
      margin-right: 10px;
      color: #2ecc71;
   }

   /* Action Buttons */
   .action-buttons {
      display: flex;
      justify-content: space-between;
      margin-bottom: 13px;
   }

   .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
   }

   .btn-prev {
      /* background: #e0e0e0; */
      color: #333;
   }

   .btn-prev:hover {
      /* background: #d0d0d0; */
   }

   .btn-book {
      background: #2ecc71;
      color: white;
      width: 100%;
   }

   .btn-book:hover {
      background: #27ae60;
   }

   /* Summary Section */
   .summary {
      background: white;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      padding: 25px;
      margin-top: 30px;
   }

   .summary-title {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #2c3e50;
      text-align: center;
   }

   .summary-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 25px;
   }

   .summary-item {
      padding: 15px;
      background: #f8f9fa;
      border-radius: 8px;
   }

   .summary-label {
      font-weight: 600;
      color: #7f8c8d;
      margin-bottom: 5px;
   }

   .summary-value {
      font-size: 1.1rem;
      color: #2c3e50;
   }

   /* Responsive Design */
   @media (max-width: 768px) {

      .venues-grid,
      .packages-container {
         grid-template-columns: 1fr;
      }

      .dates-container {
         grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
      }

      .action-buttons {
         flex-direction: column;
         gap: 15px;
      }

      .btn {
         width: 100%;
      }
   }

   @media (max-width: 480px) {
      h1 {
         font-size: 2rem;
      }

      .step {
         padding: 15px;
      }

      .dates-container {
         grid-template-columns: 1fr 1fr;
      }
   }

   /* Step visibility */
   .step {
      display: none;
   }

   .step.active {
      display: block;
   }
</style>
@endpush
@section('content')
<div class="w-full h-full sticky md:top-[68px] top-0 z-20">
   <div class="bg-bar w-full">
      <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
         <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
            <div class="d-flex justify-center items-center pt-3 pb-3">
               <div class="d-flex justify-center items-center">
                  <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                     <path
                        d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                        fill="white"></path>
                  </svg>
                  <div
                     class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium  ">
                     {{ translate('Add Details') }}
                  </div>
               </div>
               <div class="px-2 shrink-0 flex text-next">
                  <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                        fill="#9CA3AF"></path>
                     <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                        fill="#9CA3AF"></path>
                  </svg>
               </div>
               <div class="d-flex justify-center items-center">
                  <div
                     class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">
                     2
                  </div>
                  <div
                     class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
                     {{ translate('Event') }}
                  </div>
               </div>
               <div class="px-2 shrink-0 flex text-next">
                  <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                        fill="#9CA3AF"></path>
                     <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                        fill="#9CA3AF"></path>
                  </svg>
               </div>
               <div class="d-flex justify-center items-center">
                  <div
                     class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">
                     3
                  </div>
                  <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
                     {{ translate('Make Payment') }}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="container mt-3 rtl text-align-direction" id="cart-summary">
   <div class="__inline-23">
      <div class="container mt-4 rtl text-align-direction p-1">
         <div class="row">
            <div class="col-md-12">
               <div class="row">
                  <div class="col-lg-6 col-md-4 col-12" style="height: fit-content;">
                     <div class="cz-product-gallery">
                        <div class="cz-preview">
                           <div id="sync1" class="owl-carousel owl-theme product-thumbnail-slider">
                              @if ($eventData['images'] != null && json_decode($eventData['images']) > 0)
                              @foreach (json_decode($eventData['images']) as $key => $photo)
                              <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}" id="image{{ $key }}">
                                 <img class="cz-image-zoom img-responsive w-100" src="{{ getValidImage(path: 'storage/app/public/event/events/' . $photo, type: 'product') }}" data-zoom="{{ getValidImage(path: 'storage/app/public/event/events/' . $photo, type: 'product') }}" alt="{{ translate('product') }}" width="">
                              </div>
                              @endforeach
                              @endif
                           </div>
                        </div>

                        <div class="cz">
                           <div class="table-responsive __max-h-515px" data-simplebar>
                              <div class="d-flex">
                                 <div id="sync2" class="owl-carousel owl-theme product-thumb-slider">
                                    @if ($eventData['images'] != null && json_decode($eventData['images']) > 0)
                                    @foreach (json_decode($eventData['images']) as $key => $photo)
                                    <div class="">
                                       <a class="product-preview-thumb {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center" id="preview-img{{ $key }}" href="#image{{ $key }}">
                                          <img alt="{{ translate('product') }}" src="{{ getValidImage(path: 'storage/app/public/event/events/' . $photo, type: 'product') }}">
                                       </a>
                                    </div>
                                    @endforeach
                                    @endif
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="col-lg-6 col-md-8 col-12 mt-md-0 mt-sm-3 web-direction">
                     <div class="details __h-100">
                        <span class="mb-2 __inline-24">{{ $eventData['event_name'] }}</span>
                        <span class="">
                           <p class="card-text">
                              <i class="fa fa-map-marker"
                                 style="color: var(--primary-clr); margin-right: 5px;"></i>
                              <?php
                              $date_upcommining = '';
                              $time_upcommining = '';
                              $endevent_venues = '';
                              $endevent_venues1 = '';
                              $langs = str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale());

                              if (!empty($eventData['all_venue_data']) && json_decode($eventData['all_venue_data'], true)) {
                                 foreach (json_decode($eventData['all_venue_data'], true) as $check) {
                                    $currentDateTime = new DateTime();
                                    $eventDateTime = DateTime::createFromFormat('d-m-Y h:i A', date('d-m-Y', strtotime($check['date'])) . ' ' . date('h:i A', strtotime($check['start_time'])));
                                    $endevent_venues1 = !empty($check[$langs . '_event_venue_full_address'] ?? '') ? $check[$langs . '_event_venue_full_address'] ?? '' : $check[$langs . '_event_venue']; //$check[$langs . '_event_venue'];
                                    if ($eventDateTime && $eventDateTime > $currentDateTime) {
                                       $endevent_venues = !empty($check[$langs . '_event_venue_full_address'] ?? '') ? $check[$langs . '_event_venue_full_address'] ?? '' : $check[$langs . '_event_venue']; //$check[$langs . '_event_venue'];
                                       $date_upcommining = date('d M,Y', strtotime($check['date']));
                                       $time_upcommining = date('H:i:s', strtotime($check['start_time']));
                                       break;
                                    }
                                 }
                              } ?>
                              <span id="full-address">{{ $endevent_venues == '' ? $endevent_venues1 : $endevent_venues }}</span>
                           </p>
                        </span>
                        <!-- Timere Section -->
                        <div class="flex flex-col">
                           <div class="mt-2"><strong>
                                 @if ($eventData['informational_status'] == 0)
                                 {{ translate('Event booking will close on') }}
                                 @else
                                 {{ translate('Event will be start on') }}
                                 @endif
                                 :
                                 {{ $date_upcommining }}
                                 <input type="hidden" name="date" id="fullDate"
                                    value="{{ $date_upcommining }}">
                                 <input type="hidden" name="dates" id="fullDates"
                                    value="{{ $date_upcommining }}">
                                 <input type="hidden" name="time" id="fullTime"
                                    value="{{ $time_upcommining }}">
                              </strong>
                           </div>
                           <div class="mt-2">
                              <div class="flex relative w-full">
                                 <div class="row flex w-full justify-between flex-1">
                                    <div class="float-start countdown">
                                       <div>
                                          <span class="number days"></span>
                                          <span>{{ translate('Days') }}</span>
                                       </div>
                                       <div>
                                          <span class="number hours"></span>
                                          <span>{{ translate('Hour') }}</span>
                                       </div>
                                       <div>
                                          <span class="number minutes"></span>
                                          <span>{{ translate('Mins') }}</span>
                                       </div>
                                       <div>
                                          <span class="number seconds"></span>
                                          <span>{{ translate('Secs') }}</span>
                                       </div>
                                    </div>
                                    <span class='countdown_message text-success font-weight-bold'></span>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="flex flex-col">
                           <div class="flex flex-wrap justify-center items-center">
                              <div class="mb-4">
                                 <div class="mt-4 font-weight-bold">
                                    {{ translate('Click on Interested to stay updated about this event') }}.
                                 </div>
                                 <form action="{{ route('event-interested') }}" method="post"
                                    class='event-interested'>
                                    @csrf
                                    <div class="df-ao df-bv df-h">
                                       <div class="df-lf">
                                          <div class="df-h df-lg df-u mt-2">
                                             <div class="text-center">
                                                <a class='h3 ml-2 float-left'>
                                                   <i class="fa fa-thumbs-up text-success"
                                                      aria-hidden="true"></i>
                                                   <small
                                                      style="font-size: 18px;">{{ $eventData['event_interested'] }}</small>
                                                </a>
                                                @if (auth('customer')->check())
                                                @if (\App\Models\EventInterest::where('user_id', auth('customer')->id())->where('event_id', $eventData['id'])->exists())
                                                <button type='button'
                                                   class="btn btn-outline-success"
                                                   data-package_id="{{ $eventData['id'] }}"
                                                   data-venue_id=""
                                                   data-link="{{ route('event-interested') }}"><i
                                                      class="fa fa-check"
                                                      aria-hidden="true"></i>&nbsp;{{ translate('Interested') }}</button>
                                                @else
                                                <button type='button'
                                                   class="auth-book-now btn btn-outline-danger"
                                                   data-package_id="{{ $eventData['id'] }}"
                                                   data-venue_id=""
                                                   data-link="{{ route('event-interested') }}">{{ translate('Interested') }}?</button>
                                                @endif
                                                @else
                                                <button type='button'
                                                   class="participate-btn btn btn-outline-danger"
                                                   data-package_id="{{ $eventData['id'] }}"
                                                   data-venue_id=""
                                                   data-link="{{ route('event-interested') }}">{{ translate('Interested') }}?</button>
                                                @endif
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </form>
                                 <div class="small font-weight-bold">
                                    <p>
                                       <br>
                                       {{ translate('People have shown') }}
                                       <br>
                                       {{ translate('interest recently') }}
                                    </p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @if ($eventData['informational_status'] == 0)
                        <!-- Profile Icon -->
                        <div class="flex flex-col">
                           <div class="flex flex-wrap justify-center items-center">
                              <div class="w-full">
                                 <div class="w-full tray mb-3">
                                    <?php $totals_booking_user = \App\Models\EventOrder::where('event_id', $eventData['id'])->where('transaction_status', 1)->count();
                                    if ($totals_booking_user <= 5) {
                                       $show_user = 1;
                                    } else {
                                       $show_user = 2;
                                    }
                                    ?>
                                    @if ($show_user == 1)
                                    @for ($ip = 0; $ip < $totals_booking_user; $ip++)
                                       <div class="relative circle-img-container">
                                       <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                          style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $ip . '.jpg') }}')">
                                       </div>
                                 </div>
                                 @endfor
                                 @else
                                 @php
                                 $uniqueUsers = range(0, 13);
                                 shuffle($uniqueUsers);
                                 $selectedUsers = array_slice($uniqueUsers, 0, 5);
                                 @endphp
                                 @foreach ($selectedUsers as $random_user)
                                 <div class="relative circle-img-container">
                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                       style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $random_user . '.jpg') }}')">
                                    </div>
                                 </div>
                                 @endforeach
                                 @endif
                              </div>
                           </div>
                        </div>
                        <div class="mt-[1px] mb-3 md:mb-0 flex flex-col">
                           <div class="flex flex-row mt-2 flex-nowrap break-normal leading-normal">
                              <div class="flex">
                                 <div class=""><span
                                       class=" inline-flex break-normal">{{ translate('Till now') }}</span>
                                    <span
                                       class=" font-bold text-#F18912 ml-1 break-normal">{{ \App\Models\EventOrder::where('event_id', $eventData['id'])->where('transaction_status', 1)->count() }}+<span
                                          class=" ml-1 mr-1 inline-flex break-normal">{{ translate('Customers') }}</span></span><span
                                       class="text-">{{ translate('have successfully booked their religious events on Mahakal.com and benefited from our services') }}.</span>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- Button -->
                        <div role="button" class="d-sm-block d-none">
                           <a href="#packages" id='pujaPackageButton' class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold package_view" data-toggle="tab" role="tab">{{ translate('Book Now') }}</a>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- </div>
   <div class="container mt-4 rtl text-align-direction p-1"> -->
      <div class="row mt-2">
         <div class="col-md-12">
            <div class="card card-body px-2 pb-3 mb-3 __rounded-10 pt-3">
               <div class="navbar_section1 section-links d-flex justify-content-between mt-3 border-top border-bottom py-2 mb-4"
                  style="overflow: auto;text-wrap: nowrap;">
                  <a class="section-link ml-2 active"
                     href="#event_about">{{ translate('about_Event') }}</a>
                  <a class="section-link ml-2" href="#artist_about">{{ translate('about_Artist') }}</a>
                  <a class="section-link" href="#event_schedule">{{ translate('event_schedule') }}</a>
                  <a class="section-link" href="#event_attend">{{ translate('event_Attend') }}</a>
                  <a class="section-link" href="#event_team_condition">{{ translate('Terms_and_Conditions') }}</a>
                  @php
                  $langs =
                  str_replace('_', '-', app()->getLocale()) == 'in'
                  ? 'hi'
                  : str_replace('_', '-', app()->getLocale());
                  @endphp
                  <a class="section-link" href="#packages">
                     @if ($eventData['informational_status'] == 0)
                     {{ translate('packages') }}
                     @else
                     {{ translate('venues') }}
                     @endif
                  </a>
                  @if ($eventData['informational_status'] == 0)
                  <a class="section-link mr-2" href="#review_user">{{ translate('Reviews') }}</a>
                  @endif
                  @if ($faqs && count($faqs) > 0)
                  <a class="section-link mr-2" href="#faqs_user">{{ translate('faqs') }}</a>
                  @endif
               </div>
               <div class="content-sections px-lg-3">
                  <!-- Inclusion Section -->
                  <div class="section-content active" id="event_about">
                     <div class="row mt-2 p-2"
                        style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                        {!! $eventData['event_about'] !!}
                     </div>
                  </div>
                  <div class="section-content" id="artist_about">
                     <div class="row mt-2 p-2"
                        style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                        <div class="row">
                           <div class="col-md-12">
                              <h5 class="inclu font-weight-bolder" style="color:#e74c3c">
                                 &nbsp;{{ translate('about_Artist') }}
                              </h5>
                           </div>
                           <div class="col-md-4 text-center">
                              <img src="{{ getValidImage(path: 'storage/app/public/event/events/' . ($eventData['eventArtist']['image'] ?? ''), type: 'product') }}"
                                 alt="" class="img-fluid" style="max-width: 54%;"><br><br>
                              <span
                                 class="font-weight-bolder">{{ $eventData['eventArtist']['name'] ?? '' }}</span>
                              <span>{!! $eventData['eventArtist']['profession'] ?? '' !!}</span>
                           </div>
                           <div class="col-md-8">
                              <span>{!! $eventData['eventArtist']['description'] ?? '' !!}</span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="section-content" id="event_schedule">
                     <div class="row mt-2 p-2"
                        style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                        <div class="col-md-12">
                           <h5 class="inclu font-weight-bolder" style="color:#e74c3c">
                              &nbsp;{{ translate('event_schedule') }}
                           </h5>
                        </div>
                        <div class="col-md-12">
                           {!! $eventData['event_schedule'] !!}
                        </div>
                     </div>
                  </div>
                  <div class="section-content" id="event_attend">
                     <div class="row mt-2 p-2"
                        style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                        <div class="col-md-12">
                           <h5 class="inclu font-weight-bolder" style="color:#e74c3c">
                              &nbsp;{{ translate('event_Attend') }}
                           </h5>
                        </div>
                        <div class="col-md-12">
                           {!! $eventData['event_attend'] !!}
                        </div>
                     </div>
                  </div>
                  <div class="section-content" id="event_team_condition">
                     <div class="row mt-2 p-2"
                        style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                        <div class="col-md-12">
                           <h5 class="inclu font-weight-bolder" style="color:#e74c3c">
                              &nbsp;{{ translate('Terms_and_Conditions') }}
                           </h5>
                        </div>
                        <div class="col-md-12">
                           {!! $eventData['event_team_condition'] !!}
                        </div>
                     </div>
                  </div>
                  @php
                  $langs = str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale());
                  @endphp
                  <div class="section-content packagesTabLink" id="packages">
                     <div class="row mt-2 p-2" style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                        @if ($eventData['informational_status'] == 0)
                        <div class="col-12"><span class="h6 font-weight-bold"></span></div>
                        <div class="col-12 feature-product-title mt-0 text-center font-weight-bold">
                           {{ translate('Choose your Preferred Venue') }}
                           <h4 class="mt-2 height-10">
                              <span class="divider">&nbsp;</span>
                           </h4>
                        </div>
                        <div class="col-12 mt-2">

                           <div class="booking-flow">
                              <div class="step active" id="step-venue">
                                 <div class="venues-grid">
                                    <?php
                                    $langs = str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale());
                                    if (!empty($eventData['all_venue_data']) && ($venues = json_decode($eventData['all_venue_data'], true))) {
                                       usort($venues, function ($a, $b) {
                                          $aDateTime = strtotime($a['date'] . ' ' . $a['start_time']);
                                          $bDateTime = strtotime($b['date'] . ' ' . $b['start_time']);
                                          return $aDateTime <=> $bDateTime;
                                       });
                                       $unique_list = [];
                                       $unique_list2 = [];
                                       $unique_datestore = [];
                                       $unique_packagestore = [];

                                       foreach ($venues as $val) {
                                          $venueAddress = !empty($val[$langs . '_event_venue_full_address'] ?? '') ? $val[$langs . '_event_venue_full_address'] : $val[$langs . '_event_venue'];
                                          $venueAddress2 = !empty($val['en_event_venue_full_address'] ?? '') ? $val['en_event_venue_full_address'] : $val['en_event_venue'];
                                          $currentDateTime = new DateTime();
                                          $eventStartDate = new DateTime($val['date'] . ' ' . $val['start_time']);
                                          $eventEndDate = new DateTime($val['date'] . ' ' . $val['end_time']);

                                          if ($currentDateTime > $eventEndDate) {
                                             continue;
                                          }
                                          $key = strtolower(str_replace(' ', '-', $venueAddress2));
                                          $unique_datestore[$key]['name'] = $venueAddress2;
                                          $unique_datestore[$key]['dates'][] = [
                                             "day"  => date("d M", strtotime($val['date'])),
                                             "full" => date("d M, Y", strtotime($val['date'])),
                                             "key" => $key . "-" . date("d-M-Y", strtotime($val['date'])),
                                             "time" => date("h:i A", strtotime($val['start_time'])) . ' - ' . date("h:i A", strtotime($val['end_time']))
                                          ];
                                          if (!empty($val['package_list'])) {
                                             foreach ($val['package_list'] as $venu) {
                                                $unique_packagestore[$key . "-" . date("d-M-Y", strtotime($val['date']))]['name'] = $venueAddress2;
                                                $unique_packagestore[$key . "-" . date("d-M-Y", strtotime($val['date']))]['date'] = date("d M, Y", strtotime($val['date']));
                                                $unique_packagestore[$key . "-" . date("d-M-Y", strtotime($val['date']))]['data'][] = [
                                                   'id' => $venu['package_name'],
                                                   "venue_id" => $val['id'],
                                                   "package_name" => (\App\Models\EventPackage::where('id', $venu['package_name'])->first()['package_name'] ?? ''),
                                                   "description" => (\App\Models\EventPackage::where('id', $venu['package_name'])->first()['description'] ?? ''),
                                                   "available" => (($venu['available'] <= 0) ? 0 : 1),
                                                   "price" => $venu['price_no'],
                                                   "available_count" => $venu['available'],
                                                ];
                                             }
                                          }

                                          if (in_array($venueAddress, $unique_list)) {
                                             continue;
                                          }
                                          $unique_list[] = $venueAddress;
                                          $unique_list2[] = $venueAddress2;
                                       }
                                    }
                                    if ($unique_list) {
                                       foreach ($unique_list as $kadd => $addes) {
                                    ?>
                                          <div class="venue-card" data-venue="{{ strtolower(str_replace(' ', '-',$unique_list2[$kadd])) }}">
                                             <a onclick="showStep('date')">
                                                <h3 class="venue-name">{{ $addes }}</h3>
                                             </a>
                                          </div>
                                    <?php
                                       }
                                    }
                                    ?>
                                 </div>
                              </div>

                              <!-- Step 2: Date Selection -->
                              <div class="step" id="step-date">
                                 <div class="action-buttons">
                                    @if(($unique_list??"") && count($unique_list) > 1)
                                    <a class="btn-prev d-block d-sm-none" onclick="showStep('venue')"><i class="fa fa-chevron-left" aria-hidden="true"></i><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                                    <button class="btn btn-prev d-none d-sm-block" onclick="showStep('venue')"><i class="fa fa-chevron-left" aria-hidden="true"></i><i class="fa fa-chevron-left" aria-hidden="true"></i> {{ translate('Change Venue') }}</button>
                                    @elseif(($unique_list??"") && count($unique_list) == 1)
                                    <span class="font-weight-bolder">{{ $unique_list[0] }}</span>
                                    @endif
                                 </div>
                                 <div class="dates-container">
                                 </div>
                              </div>
                              <!-- Step 3: Package Selection -->
                              <div class="step" id="step-package">
                                 <div class="action-buttons">
                                    @if(($unique_list??"") && count($unique_list) == 1 && ($unique_packagestore??'') && count($unique_packagestore) == 1)
                                    <span class="font-weight-bolder">{{ $unique_list[0]??"" }} ( {{($unique_packagestore[(array_keys($unique_packagestore)[0]??"")]['date'] ??"") }} )</span>
                                    @else
                                    <a class="btn-prev d-block d-sm-none" onclick="showStep('date')"><i class="fa fa-chevron-left" aria-hidden="true"></i><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                                    <button class="btn btn-prev d-none d-sm-block" onclick="showStep('date')"><i class="fa fa-chevron-left" aria-hidden="true"></i><i class="fa fa-chevron-left" aria-hidden="true"></i> {{ translate('Change Date') }}</button>
                                    @endif
                                 </div>
                                 <div class="packages-container">
                                 </div>
                              </div>
                           </div>
                        </div>
                        @else
                        <?php
                        $unique_datestore = [];
                        $unique_packagestore = []; ?>
                        <div class="col-12"><span class="h6 font-weight-bold"></span></div>
                        <div class="col-12 feature-product-title mt-0 text-center font-weight-bold">
                           {{ translate('all_venues') }}
                           <h4 class="mt-2 height-10">
                              <span class="divider">&nbsp;</span>
                           </h4>
                        </div>
                        <div class="col-12 mt-2">
                           <div class="table-responsive">
                              <table class='table-borderless table'>
                                 <thead>
                                    <tr>
                                       <th>{{ translate('address') }}</th>
                                       <th>{{ translate('Date') }}</th>
                                       <th>{{ translate('Time') }}</th>
                                       <th>{{ translate('Duration') }}</th>
                                    </tr>
                                 </thead>
                                 <tbody class="venue-font-size">
                                    @php
                                    $langs =
                                    str_replace('_', '-', app()->getLocale()) == 'in'
                                    ? 'hi'
                                    : str_replace('_', '-', app()->getLocale());
                                    @endphp
                                    @if (!empty($eventData['all_venue_data']) && ($venues = json_decode($eventData['all_venue_data'], true)))
                                    @php
                                    usort($venues, function ($a, $b) {
                                    $aDateTime = strtotime(
                                    $a['date'] . ' ' . $a['start_time'],
                                    );
                                    $bDateTime = strtotime(
                                    $b['date'] . ' ' . $b['start_time'],
                                    );
                                    return $aDateTime <=> $bDateTime;
                                       });
                                       @endphp
                                       @foreach ($venues as $val)
                                       @php
                                       $currentDateTime = new DateTime();
                                       $eventStartDate = new DateTime(
                                       $val['date'] . ' ' . $val['start_time'],
                                       );
                                       $eventEndDate = new DateTime(
                                       $val['date'] . ' ' . $val['end_time'],
                                       );
                                       if ($currentDateTime > $eventEndDate) {
                                       continue;
                                       }
                                       if ($currentDateTime < $eventStartDate) {
                                          $rowClass='text-info' ;
                                          } elseif (
                                          $currentDateTime>= $eventStartDate &&
                                          $currentDateTime <= $eventEndDate
                                             ) {
                                             $rowClass='text-warning' ;
                                             }
                                             @endphp
                                             <tr class="{{ $rowClass }}">
                                             <td>
                                                {{ !empty($check[$langs . '_event_venue_full_address'] ?? '') ? $check[$langs . '_event_venue_full_address'] ?? '' : $check[$langs . '_event_venue'] }}
                                             </td>
                                             <td>{{ date('d M, Y', strtotime($val['date'] ?? '')) }}
                                             </td>
                                             <td>{{ $val['start_time'] ?? '' }} -
                                                {{ $val['end_time'] ?? '' }}
                                             </td>
                                             <td>{{ $val['event_duration'] ?? '' }}</td>
                                             </tr>
                                             @endforeach
                                             @endif
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        @endif
                     </div>
                  </div>
                  @if ($eventData['informational_status'] == 0)
                  <div class="section-content" id="review_user">
                     <div class="row p-2 mt-2"
                        style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                        <div class="col-lg-4 px-max-md-0">
                           <div class="suggestion-card">
                              <div class="web-text-primary">
                                 <div class="text-capitalize">
                                    <p class="text-capitalize mb-0">
                                       <a class='h3'>
                                          {{ round($event_review['averageStar'], 1) }}&nbsp;
                                       </a>
                                       <big>
                                          @for ($i = 1; $i <= 5; $i++)
                                             @if ($event_review['averageStar']>= $i)
                                             <i class="fa fa-star text-warning"></i>
                                             @elseif($event_review['averageStar'] >= $i - 0.9)
                                             <i class="fa fa-star-half-o text-warning"></i>
                                             @else
                                             <i class="fa fa-star-o text-muted"></i>
                                             @endif
                                             @endfor
                                       </big>
                                    </p>
                                    <a class='small'>
                                       &nbsp;{{ !empty($event_review['list']) && count($event_review['list']) > 0 ? count($event_review['list']) : 0 }}
                                       {{ translate('Reviews') }}
                                    </a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-8 d-md-block px-max-md-0">
                           @if (!empty($event_review['list']) && count($event_review['list']) > 0)
                           <div class="owl-theme owl-carousel review-slider">
                              @foreach ($event_review['list'] as $counselling)
                              @if ($counselling['comment'])
                              <div class="card product-single-hover shadow-none rtl">
                                 <div class="card-body position-relative">
                                    <div class=" d-flex align-items-center">
                                       <!-- User Icon -->
                                       <img src="{{ getValidImage(path: 'storage/app/public/profile/' . ($counselling['userData']['image'] ?? ''), type: 'product') }}"
                                          alt="User Icon" class="user-icon"
                                          style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                       <!-- User Name -->
                                       <div>
                                          <p class="fw-bold m-0">
                                             {{ $counselling['userData']['name'] ?? 'user name' }}
                                          </p>
                                          <p class="m-0">
                                             <big class="small">
                                                @for ($i = 1; $i <= 5; $i++)
                                                   @if ($counselling['star']>= $i)
                                                   <i
                                                      class="fa fa-star text-warning"></i>
                                                   @elseif($counselling['star'] >= $i - 0.9)
                                                   <i
                                                      class="fa fa-star-half-o text-warning"></i>
                                                   @else
                                                   <i
                                                      class="fa fa-star-o text-muted"></i>
                                                   @endif
                                                   @endfor
                                             </big>
                                          </p>
                                       </div>
                                    </div>
                                    <div class="single-product-details min-height-unset"
                                       style="height: 100px; overflow: hidden;">
                                       <div>
                                          <a
                                             class="text-capitalize fw-semibold review-comment">
                                             {{ $counselling['comment'] ?? '' }}
                                             @php $filePath = 'storage/event/comment/' . ($counselling['image']??''); @endphp
                                             @if ($counselling['image'] && file_exists($filePath))
                                             <img alt="{{ translate('product') }}"
                                                src="{{ getValidImage(path: 'storage/app/public/event/comment/' . $counselling['image'], type: 'product') }}"
                                                class='border border-light'
                                                style="width:50px">
                                             @endif
                                          </a>
                                       </div>
                                       <a onclick="read(this)"
                                          class="read-more-btn">{{ translate('Read more') }}</a>
                                    </div>
                                 </div>
                              </div>
                              @endif
                              @endforeach
                           </div>
                           @else
                           <div class="text-center text-capitalize">
                              <img class="mw-100"
                                 src="{{ asset('assets/front-end/img/icons/empty-review.svg') }}"
                                 alt="">
                              <p class="text-capitalize">
                                 <small>No review given yet!</small>
                              </p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  @endif
                  @if ($faqs && count($faqs) > 0)
                  <div class="section-content" id="faqs_user">
                     <div class="row p-2 mt-2"
                        style="background: white; box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.0509803922); border-radius: 5px; border-bottom: 3px solid transparent;">
                        <div class="col-md-12">
                           <h5 class="inclu font-weight-bolder" style="color:#e74c3c">
                              &nbsp;{{ translate('faqs') }}
                           </h5>
                        </div>
                        <div class="col-12">
                           @foreach ($faqs as $faq)
                           <div class="row pt-2 specification">
                              <div class="col-12 col-md-12 col-lg-12">
                                 <div class="accordion" id="accordionExample">
                                    <div class="cards">
                                       <div class="card-header"
                                          id="heading{{ $faq->id }}">
                                          <h2 class="mb-0">
                                             <button
                                                class="btn btn-link btn-block  text-left btnClr"
                                                type="button" data-toggle="collapse"
                                                data-target="#collapse{{ $faq->id }}"
                                                aria-expanded="true"
                                                aria-controls="collapseOne"
                                                style="white-space: normal;">
                                                {{ $faq->question }}
                                             </button>
                                          </h2>
                                       </div>
                                       <div id="collapse{{ $faq->id }}"
                                          class="collapse"
                                          aria-labelledby="heading{{ $faq->id }}"
                                          data-parent="#accordionExample">
                                          <div class="card-body">
                                             {!! $faq->detail !!}
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
                  @endif


               </div>
            </div>


         </div>
      </div>
      @if ($eventData['informational_status'] == 0)
      <div class="button-sticky bg-white d-sm-none bottom-package-show">
         <div class="d-flex flex-column gap-1 py-2">
            <div class="d-flex gap-3 justify-content-center" role="button">
               <a href="#packages" onclick="PackageOpens()" class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center package_view" data-toggle="tab" role="tab">
                  <span class="font-bold">{{ translate('Select venues') }}</span>
               </a>
            </div>
         </div>
      </div>
      @endif
      <div class="row">
         @if ($eventData['youtube_video'] != null && str_contains($eventData['youtube_video'], 'youtube.com/embed/'))
         <!-- <div class="col-12 mb-4">
               <iframe width="420" height="315" src="{{-- $eventData['youtube_video'] --}}"> </iframe>
               </div> -->
         <div class="col-12 rtl text-align-direction">
            <style>
               .resp-iframe__container {
                  position: relative;
                  overflow: hidden;
                  border-radius: 1rem;
               }

               .resp-iframe__embed {
                  position: absolute;
                  top: 0;
                  width: 100%;
                  height: 100%;
                  border: 0;
               }
            </style>
            <div class="resp-iframe">
               <div class="resp-iframe__container">
                  <iframe width="420" height="315" src="{{ $eventData['youtube_video'] }}"
                     frameborder="0"
                     allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                     allowfullscreen="">
                  </iframe>
               </div>
            </div>
         </div>
         @endif
      </div>
   </div>
   <div class="modal fade rtl text-align-direction" id="participateModal" tabindex="-1" role="dialog"
      aria-labelledby="participateModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <div class="flex justify-center items-center my-3">
                  <span class="text-18 font-bold ml-2">{{ translate('Fill in your details') }}</span>
               </div>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <hr class="bg-[#E6E4EB] w-full">
            <div class="modal-body flex justify-content-center">
               <div id="recaptcha-container"></div>
               <div class="w-full mt-1 px-2">
                  <span
                     class="block text-[16px] font-bold text-gray-900 dark:text-white">{{ translate('Enter Your Whatsapp Mobile Number') }}</span>
                  <span
                     class="text-[12px] font-normal text-[#707070]">{{ translate('your updated Event Information will be sent to the WhatsApp number given below') }}...</span>
                  <!-- Model Form -->
                  <div class="w-full mr-9 px-0 pt-3">
                     <form class="needs-validation_" id="lead-store-form" action="" method="get">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $eventData['id'] }}">
                        <input type="hidden" name="package_id" class='package_id_model'>
                        <input type="hidden" name="venue_id" class='venue_id_model'>
                        <input type="hidden" name="verify_otp" id="phone-number-valid">
                        <div class="row">
                           <div class="col-md-12" id="phone-div">
                              <div class="form-group">
                                 <label
                                    class="form-label font-semibold">{{ translate('your_phone_number') }}
                                    <small class="text-primary">(
                                       *{{ translate('country_code_is_must_like_for_IND') }}
                                       91)</small>
                                 </label>
                                 <input
                                    class="form-control text-align-direction phone-input-with-country-picker"
                                    type="tel"
                                    value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                    id="person-number"
                                    placeholder="{{ translate('enter_phone_number') }}" required
                                    {{ isset($customer['phone']) ? 'readonly' : '' }}
                                    oninput="this.value=this.value.slice(0,10)">
                                 <input type="hidden" class="country-picker-phone-number w-50"
                                    value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                    name="person_phone" readonly>
                                 <p id="number-validation" class="text-danger" style="display: none">
                                    {{ translate('Enter Your Valid Mobile Number') }}
                                 </p>
                              </div>
                           </div>
                           <div class="col-md-12" id="name-div">
                              <div class="form-group">
                                 <label
                                    class="form-label font-semibold">{{ translate('your_name') }}</label>
                                 <input class="form-control text-align-direction"
                                    value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? ' ' . $customer['l_name'] : '' }}"
                                    type="text" name="person_name" id="person-name"
                                    placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                    required {{ isset($customer['f_name']) ? 'readonly' : '' }} onkeyup="this.value = this.value.replace(/[^a-zA-Z\s]/g, '').replace(/\b\w/g, l => l.toUpperCase());">
                                 <p id="name-validation" class="text-danger" style="display: none">
                                    Enter Your Name
                                 </p>
                              </div>
                           </div>
                           <div class="col-md-12" id="otp-input-div" style="display: none;">
                              <div class="form-group text-center">
                                 <label
                                    class="form-label font-semibold ">{{ translate('enter_OTP') }}</label>
                                 <div class="otp-input-fields">
                                    <input type="number" id="otp1"
                                       class="otp__digit otp__field__1" inputmode="number">
                                    <input type="number" id="otp2"
                                       class="otp__digit otp__field__2" inputmode="number">
                                    <input type="number" id="otp3"
                                       class="otp__digit otp__field__3" inputmode="number">
                                    <input type="number" id="otp4"
                                       class="otp__digit otp__field__4" inputmode="number">
                                    <input type="number" id="otp5"
                                       class="otp__digit otp__field__5" inputmode="number">
                                    <input type="number" id="otp6"
                                       class="otp__digit otp__field__6" inputmode="number">
                                 </div>
                                 <p id="otpValidation" class="text-danger"></p>
                              </div>
                           </div>
                           <div class="mx-auto mt-1 __max-w-356" id="send-otp-btn-div">
                              <button type="button"
                                 class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold one-time-otp-sends" id="send-otp-btn"> {{ translate('send_OTP') }} </button>
                              {{--
                                 <p id="failedOtpValidation" class="text-danger mt-2"></p>
                                 --}}
                           </div>
                           <div class="mx-auto mt-1 __max-w-356" id="verify-otp-btn-div"
                              style="display: none">
                              <div class="d-flex">
                                 <button type="button"
                                    class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold me-2"
                                    id="otp-back-btn">
                                    {{ translate('back') }} </button>
                                 <button type="submit"
                                    class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                    id="verify-otp-btn">
                                    {{ translate('verify_OTP') }} </button>
                              </div>
                           </div>
                        </div>
                        <div class="text-center mt-3" id="resend-div" style="display: none;">
                           <p id="resend-otp-timer-text" style="display: none"> Resend OTP in <span
                                 id="resend-otp-timer"></span></p>
                           <p id="resend-otp-btn-text" style="display: none">Didn't get the code? <a
                                 href="javascript:0" id="resend-otp-btn" style="color: blue;">Resend
                                 Otp</a>
                           </p>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
@endsection
@push('script')
</script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
<!-- Firbase CDN -->
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>
<script>
   $('.participate-btn').click(function(e) {
      e.preventDefault();
      $('#participateModal').modal('show');
      $(".package_id_model").val($(this).data('package_id'));
      $(".venue_id_model").val($(this).data('venue_id'));
      $("#lead-store-form").attr('action', $(this).data('link'));
   });

   $('#pujaPackageButton').on('click', function(event) {
      event.preventDefault();
      PackageOpens()
   });

   function PackageOpens() {
      if (!$('.packagesTabLink').hasClass('active')) {
         $('.nav-link').removeClass('active');
         $('.tab-pane.fade').removeClass('active show');
         $('.tab-pane.fade').removeClass('show');
         $('.packagesTabLink').addClass('active');
         $('.packagesTabLink').tab('show');
         $('.tab-pane.fade#packages').addClass('active');
         $('.tab-pane.fade#packages').addClass('show');
      }
      $('html, body').animate({
         scrollTop: $('.packagesTabLink').offset().top
      }, 50);
   }
</script>
<script>
   const firebaseConfig = {
      apiKey: "{{ env('FIREBASE_APIKEY') }}",
      authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
      projectId: "{{ env('FIREBASE_PRODJECTID') }}",
      storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
      messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
      appId: "{{ env('FIREBASE_APPID') }}",
      measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
   };
   firebase.initializeApp(firebaseConfig);
</script>
<script>
   $('.participate-btn').click(function(e) {
      e.preventDefault();
      $('#participateModal').modal('show');
      $(".package_id_model").val($(this).data('package_id'));
      $(".venue_id_model").val($(this).data('venue_id'));
      $("#lead-store-form").attr('action', $(this).data('link'));
   });

   var confirmationResult;
   var appVerifier = "";
   var sendOtpCount = 1;
   $('#send-otp-btn').click(function(e) {
      e.preventDefault();
      var name = $('#person-name').val();
      var number = $('#person-number').val();

      var phoneNumber = $('.country-picker-phone-number')
         .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
      sendotp();
   });


   function sendotp() {
      var name = $('#person-name').val();
      var number = $('#person-number').val();
      if (number == "" || number.length != 10) {
         $('#number-validation').show();
      } else if (name == "") {
         $('#number-validation').hide();
         $('#name-validation').show();
      } else {
         toastr.success('please wait...');
         $('#send-otp-btn').text('Please Wait ...');
         $('#send-otp-btn').prop('disabled', true);
         var phoneNumber = $('.country-picker-phone-number')
            .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
         if (appVerifier == "") {
            appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
               size: 'invisible'
            });
         }

         firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
               $('#name-validation').hide();
               $('#number-validation').hide();
               $('#send-otp-btn-div').css('display', 'none');
               $('#phone-div').css('display', 'none');
               $('#name-div').css('display', 'none');
               $('#otp-input-div').css('display', 'block');
               $('#verify-otp-btn-div').css('display', 'block');
               if (sendOtpCount == 1) {
                  sendOtpCount = 2;
                  otpTimer();
               }
               confirmationResult = confirmation;
               toastr.success('otp sent successfully');
               $('#resend-div').show();
            })
            .catch(function(error) {
               toastr.error('Failed to send OTP. Please try again');
               $('#send-otp-btn').text('Send OTP');
               $('#send-otp-btn').prop('disabled', false);
               console.error('OTP sending error:', error);
            });
      }
   }

   // otp timer
   function otpTimer() {
      $('#resend-otp-timer-text').css('display', 'block');
      $('#resend-otp-btn-text').css('display', 'none');
      var resendOtpTimer = 30;
      var interval = setInterval(() => {
         resendOtpTimer--;
         $('#resend-otp-timer').text(resendOtpTimer);
         if (resendOtpTimer <= 0) {
            $('#resend-otp-timer-text').css('display', 'none');
            $('#resend-otp-btn-text').css('display', 'block');
            clearInterval(interval);
         }
      }, 1000);
   }

   // resend otp
   $('#resend-otp-btn').click(function(e) {
      e.preventDefault();

      var phoneNumber = $('.country-picker-phone-number')
         .val(); //$('.iti__selected-flag').text()+' ' + $('#person-number').val();
      if (!appVerifier) {
         appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
            size: 'invisible'
         });
      }
      firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
            confirmationResult = confirmation;
            otpTimer();
            toastr.success('OTP resent successfully');
         })
         .catch(function(error) {
            toastr.error('Failed to send OTP. Please try again');
         });
   });

   $('#verify-otp-btn').click(function(e) {
      e.preventDefault();
      toastr.success('please wait...');
      var name = $('#person-name').val();
      var number = $('.country-picker-phone-number').val(); //$('#person-number').val();
      var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
         $('#otp6').val();
      if (confirmationResult) {
         confirmationResult.confirm(otp).then(function(result) {
               $('#participateModal').modal('hide');
               $(this).text('Please Wait ...');
               $(this).prop('disabled', true);
               // $(".package_id_model").val($(".package_id").val());
               var check = $(".interested_model").val();
               $('#lead-store-form').submit();
            })
            .catch(function(error) {
               $('#otpValidation').text('Incorrect OTP');
               $('.otp-input-fields input').val('');
               $('.otp-input-fields input:first').focus();
               // $('#submit').text('Submit');
               // $('#submit').prop('disabled', false);
            });
      }


   });

   $('#otp-back-btn').click(function(e) {
      e.preventDefault();

      $('#send-otp-btn-div').css('display', 'block');
      $('#phone-div').css('display', 'block');
      $('#name-div').css('display', 'block');
      $('#otp-input-div').css('display', 'none');
      $('#verify-otp-btn-div').css('display', 'none');
      $('#send-otp-btn').prop('disabled', false);
      $('#send-otp-btn').text('Send OTP');
      $('#resend-div').hide();
   });
</script>
<!-- OTP SECTION -->
<script type="text/javascript">
   var otp_inputs = document.querySelectorAll(".otp__digit")
   var mykey = "0123456789".split("")
   otp_inputs.forEach((_) => {
      _.addEventListener("keyup", handle_next_input)
   })

   function handle_next_input(event) {
      let current = event.target
      let index = parseInt(current.classList[1].split("__")[2])
      current.value = event.key

      if (event.keyCode == 8 && index > 1) {
         current.previousElementSibling.focus()
      }
      if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
         var next = current.nextElementSibling;
         next.focus()
      }
      var _finalKey = ""
      for (let {
            value
         }
         of otp_inputs) {
         _finalKey += value
      }
   }
</script>
{{-- mobile no blur --}}
<script>
   $('#person-number').blur(function(e) {
      e.preventDefault();
      var code1 = $('.country-picker-phone-number').val();
      var mobile = $(this).val();
      if (mobile.length <= 9) {
         toastr.error("Invalid phone number.");
         $('.one-time-otp-sends').prop('disabled', true);
         return;
      } else {
         $('.one-time-otp-sends').prop('disabled', false);
      }
      $.ajax({
         type: "get",
         url: "{{ url('account-counselling-order-user-name') }}" + "/" + code1,
         success: function(response) {
            if (response.status == 200) {
               var name = response.user.f_name + ' ' + response.user.l_name;
               $('#person-name').val(name);
               $('#person-name').prop('readonly', true);
               $('#phone-number-valid').val(1);
               $('.one-time-otp-sends').attr('id', 'send-otp-btn');
               $('.one-time-otp-sends').text("{{ translate('send_OTP') }}");
            } else {
               $('#person-name').val('');
               $('#person-name').prop('readonly', false);
               $('#phone-number-valid').val(0);
               $('.one-time-otp-sends').attr('id', 'invalid-user-login-booking');
               $('.one-time-otp-sends').text("{{ translate('book_now') }}");
            }
         }
      });
   });

   $(document).on('click', '#invalid-user-login-booking', function(e) {
      e.preventDefault();
      $('#vip-darshan-box').slideUp(200, function() {
         $('#vip-darshan-wrapper').hide();
      });
      $('#participateModal').modal('hide');
      $(this).text('Please Wait ...');
      $(this).prop('disabled', true);
      $('#lead-store-form').submit();
   });
</script>
{{-- auth book now btn click --}}
<script>
   $('.auth-book-now').click(function(e) {
      e.preventDefault();
      $("#lead-store-form").attr('action', $(this).data('link'));
      $(".package_id_model").val($(this).data('package_id'));
      $(".venue_id_model").val($(this).data('venue_id'));
      $('#lead-store-form').submit();
   });
</script>
<script type="text/javascript">
   $(document).ready(function() {
      startCountdown();

      function startCountdown() {
         var dateGet = $('#fullDate').val();
         var timeGet = $('#fullTime').val();

         var dateTimeString = dateGet + ' ' + timeGet;
         var newDate = new Date(dateTimeString).getTime();

         if (isNaN(newDate)) {
            console.error("Invalid date or time format!");
            return;
         }

         const countdown = setInterval(() => {
            const now = new Date().getTime();
            const diff = newDate - now;

            if (diff <= 0) {
               clearInterval(countdown);
               $(".countdown_message").text("Event Starts Live");
               $(".countdown").addClass("d-none");
               return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            $(".seconds").text(seconds < 10 ? '0' + seconds : seconds);
            $(".minutes").text(minutes < 10 ? '0' + minutes : minutes);
            $(".hours").text(hours < 10 ? '0' + hours : hours);
            $(".days").text(days < 10 ? '0' + days : days);
         }, 1000);
      }
   });
</script>
<script>
   // function renderOwlCarouselSlider() {
   //     var sync1 = $(".product-thumbnail-slider");
   //     var sync3 = $("#sync3");
   //     var thumbnailItemClass = ".owl-item";

   //     // Main carousel
   //     var slides = sync1
   //         .owlCarousel({
   //             items: 1,
   //             loop: true,
   //             autoplay: true,
   //             margin: 30,
   //             stagePadding: 30,
   //             autoplayHoverPause: false,
   //             nav: false,
   //             smartSpeed: 450,
   //             animateOut: "slideOutDown",
   //             animateIn: "flipInX",
   //             startPosition: 12,
   //             items: 1,
   //             loop: true, // Enable loop
   //             // margin: 0,
   //             mouseDrag: true,
   //             touchDrag: true,
   //             pullDrag: false,
   //             scrollPerPage: true,
   //             autoplay: true, // Enable autoplay
   //             autoplayTimeout: 3000, // 3 seconds interval
   //             // autoplayHoverPause: true, 
   //             // nav: true, 
   //             navText: [
   //                 '<i class="fa fa-chevron-left"></i>', // Left arrow
   //                 '<i class="fa fa-chevron-right"></i>' // Right arrow
   //             ],
   //             dots: false,
   //             rtl: themeDirection && themeDirection.toString() === 'rtl',
   //         })
   //         .on("changed.owl.carousel", syncPosition);

   //     // Thumbnails carousel
   //     var thumbs = sync3
   //         .owlCarousel({
   //             startPosition: 12,
   //             items: 4,
   //             loop: true, // Enable loop for thumbnails
   //             margin: 10,
   //             autoplay: false,
   //             nav: true, // Enable navigation arrows for thumbnails
   //             navText: [
   //                 '<i class="fa fa-chevron-left"></i>', // Left arrow
   //                 '<i class="fa fa-chevron-right"></i>' // Right arrow
   //             ],
   //             dots: false,
   //             rtl: themeDirection && themeDirection.toString() === 'rtl',
   //             responsive: {
   //                 576: {
   //                     items: 4,
   //                 },
   //                 768: {
   //                     items: 4,
   //                 },
   //                 992: {
   //                     items: 4,
   //                 },
   //                 1200: {
   //                     items: 5,
   //                 },
   //                 1400: {
   //                     items: 5,
   //                 },
   //             },
   //             onInitialized: function(e) {
   //                 var thumbnailCurrentItem = $(e.target)
   //                     .find(thumbnailItemClass)
   //                     .eq(this._current);
   //                 thumbnailCurrentItem.addClass("synced");
   //             },
   //         })
   //         .on("click", thumbnailItemClass, function(e) {
   //             e.preventDefault();
   //             var duration = 500;
   //             var itemIndex = $(e.target).parents(thumbnailItemClass).index();
   //             sync1.trigger("to.owl.carousel", [itemIndex, duration, true]);
   //         })
   //         .on("changed.owl.carousel", function(el) {
   //             var number = el.item.index;
   //             var owl_slider = sync1.data("owl.carousel");
   //             owl_slider.to(number, 500, true);
   //         });

   //     function syncPosition(el) {
   //         var owl_slider = $(this).data("owl.carousel");

   //         // Make sure owl_slider is initialized
   //         if (typeof owl_slider === 'undefined') {
   //             return; // Exit the function if undefined
   //         }

   //         var loop = owl_slider.options.loop;

   //         if (loop) {
   //             var count = el.item.count - 1;
   //             var current = Math.round(el.item.index - el.item.count / 2 - 0.5);
   //             if (current < 0) {
   //                 current = count;
   //             }
   //             if (current > count) {
   //                 current = 0;
   //             }
   //         } else {
   //             var current = el.item.index;
   //         }

   //         var owl_thumbnail = sync3.data("owl.carousel");

   //         // Make sure owl_thumbnail is initialized
   //         if (typeof owl_thumbnail === 'undefined') {
   //             return; // Exit the function if undefined
   //         }
   //         var itemClass = "." + owl_thumbnail.options.itemClass;

   //         var thumbnailCurrentItem = sync3.find(itemClass).removeClass("synced").eq(current);
   //         thumbnailCurrentItem.addClass("synced");

   //         if (!thumbnailCurrentItem.hasClass("active")) {
   //             var duration = 500;
   //             sync3.trigger("to.owl.carousel", [current, duration, true]);
   //         }
   //     }

   // }

   // Call the function
   renderOwlCarouselSlider();
</script>
<script>
   function read(el) {
      var parentDiv = $(el).closest('.single-product-details');
      var commentDiv = parentDiv.find('.review-comment');
      if (parentDiv.css('height') === '100px') {
         parentDiv.css('height', 'auto'); // Expand
         commentDiv.css('-webkit-line-clamp', '10');
         $(el).text("{{ translate('Read less') }}");
      } else {
         parentDiv.css('height', '100px'); // Collapse
         commentDiv.css('-webkit-line-clamp', '1');
         $(el).text("{{ translate('Read more') }}");
      }
   }

   $(document).ready(function() {
      $('.section-link').on('click', function(e) {
         e.preventDefault();

         const targetId = $(this).attr('href');
         $('html, body').animate({
            scrollTop: $(targetId).offset().top - $('.navbar_section1').outerHeight() - 100
         }, 200);

      });

      $(window).on('scroll', function() {
         const scrollTop = $(window).scrollTop() + $('.navbar_section1').outerHeight() + 210;
         if (scrollTop > 900) {
            $('.navbar-stuck-toggler').removeClass('show');
            $('.navbar-stuck-menu').removeClass('show');
            $(".navbar_section1").css({
               'position': 'sticky',
               'top': '83px',
               'right': '3px',
               'left': '3px',
               'background-color': '#fff',
               'z-index': '1000',
               'box-shadow': '0 2px 10px rgba(0, 0, 0, 0.1)',
               'overflow': 'auto',
               'text-wrap': 'nowrap',
            });
            $('#breadcrum-container').removeClass('d-flex');
            $('#breadcrum-container').addClass('d-none');
         } else {
            $('#breadcrum-container').removeClass('d-none');
            $('#breadcrum-container').addClass('d-flex');
            $(".navbar_section1").css({
               'position': 'static',
               'box-shadow': 'none',
               'text-wrap': 'nowrap',
            });
         }
         $('.section-content').each(function() {
            const sectionTop = $(this).offset().top;
            const sectionBottom = sectionTop + $(this).outerHeight();
            const sectionId = $(this).attr('id');
            const navLink = $(`.section-link[href="#${sectionId}"]`);

            if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
               $('.section-link').removeClass('active');
               navLink.addClass('active');
               if (sectionId == 'packages') {
                  $(".bottom-package-show").addClass("d-none");
               } else {
                  $(".bottom-package-show").removeClass("d-none");
               }
            }
         });

      });
   });
   $(document).ready(function() {
      const $stickyElement = $('.button-sticky');
      const $offsetElement = $('.partial-pooja');

      $(window).on('scroll', function() {
         const elementOffset = $offsetElement.offset().top;
         const scrollTop = $(window).scrollTop();

         if (scrollTop >= elementOffset) {
            $stickyElement.addClass('stick');
         } else {
            $stickyElement.removeClass('stick');
         }
      });
   });
</script>
<script>
   // renderOwlCarouselSlider();

   function renderOwlCarouselSlider() {
      var sync1 = $(".product-thumbnail-slider");
      var thumbnailItemClass = ".owl-item";
      var slides = sync1.owlCarousel({
         startPosition: 12,
         items: 1,
         loop: true,
         autoplay: true,
         margin: 30, // Updated margin
         mouseDrag: true,
         touchDrag: true,
         pullDrag: false,
         stagePadding: 30, // Added stagePadding
         scrollPerPage: true,
         autoplayHoverPause: false,
         nav: false,
         dots: false,
         smartSpeed: 450, // Added smartSpeed for smooth transitions
         animateOut: "slideOutDown", // Added custom animation
         animateIn: "flipInX", // Added custom animation
         navText: [
            '<i class="fa fa-chevron-left"></i>', // Left arrow
            '<i class="fa fa-chevron-right"></i>' // Right arrow
         ],
         rtl: themeDirection && themeDirection.toString() === "rtl",
      }).on("changed.owl.carousel", syncPosition);
   }
</script>
<script>
   $('#mobilePackageSlider').owlCarousel({
      loop: true,
      margin: 10,
      nav: true,
      dots: true,
      items: 1
   });
</script>


<script>
   const venueData = @json($unique_datestore);

   const packageData = @json($unique_packagestore);
   let userSelection = {
      venue: null,
      date: null,
      package: null
   };

   // Initialize the page
   document.addEventListener('DOMContentLoaded', function() {
      const venueCards = document.querySelectorAll('.venue-card');
      venueCards.forEach(card => {
         card.addEventListener('click', function() {
            venueCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            userSelection.venue = this.getAttribute('data-venue');
            updateDateOptions();
         });
         <?php
         if (($unique_list ?? "")) {
            if (count($unique_list ?? []) == 1) { ?>
               showStep('date');
         <?php }
         } ?>
      });

      const packageCards = document.querySelectorAll('.package-card');
      packageCards.forEach(card => {
         card.addEventListener('click', function() {
            packageCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            userSelection.package = this.getAttribute('data-package');
         });
      });
      document.querySelector('.venue-card').click();
      document.querySelector('.package-card').click();
   });

   // Update date options based on selected venue
   function updateDateOptions() {
      const datesContainer = document.querySelector('.dates-container');
      datesContainer.innerHTML = '';

      if (userSelection.venue && venueData[userSelection.venue]) {
         const dates = venueData[userSelection.venue].dates;

         dates.forEach(date => {
            const dateCard = document.createElement('div');
            dateCard.className = 'date-card';
            dateCard.setAttribute('data-date', date.full);

            dateCard.innerHTML = `
                        <div class="date-day">${date.day}</div>
                        <div class="date-full">${date.full}</div>
                        <div class="date-time">${date.time}</div>
                    `;

            dateCard.addEventListener('click', function() {
               document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
               this.classList.add('selected');
               userSelection.date = {
                  key: date.key,
                  full: date.full,
                  time: date.time
               };
               updatePackageOptions();
               showStep('package');
            });

            datesContainer.appendChild(dateCard);
         });

         if (dates.length > 0) {
            <?php if($unique_packagestore && count(array_keys($unique_packagestore)??[]) == 1){ ?>
            datesContainer.querySelector('.date-card').click();
            <?php } ?>
         }
      }
   }

   // Show the specified step
   function showStep(step) {
      document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
      document.getElementById(`step-${step}`).classList.add('active');
      if (step === 'summary') {
         updateSummary();
      }
   }

   function updateSummary() {
      if (userSelection.venue && venueData[userSelection.venue]) {
         document.getElementById('summary-venue').textContent = venueData[userSelection.venue].name;
      }

      if (userSelection.date) {
         document.getElementById('summary-date').textContent =
            `${userSelection.date.full} (${userSelection.date.time})`;
      }

      if (userSelection.package && packageData[userSelection.package]) {
         document.getElementById('summary-package').textContent = packageData[userSelection.package].name;
         document.getElementById('summary-cost').textContent = `$${packageData[userSelection.package].price}`;
      }
   }

   function resetBooking() {
      userSelection = {
         venue: null,
         date: null,
         package: null
      };

      // Reset UI selections
      document.querySelectorAll('.venue-card, .date-card, .package-card').forEach(card => {
         card.classList.remove('selected');
      });

      showStep('venue');

      document.querySelector('.venue-card').click();
      document.querySelector('.package-card').click();
   }

   function updatePackageOptions() {
      const packagesContainer = document.querySelector('.packages-container');
      packagesContainer.innerHTML = '';
      if (userSelection.date && userSelection.venue) {
         const packageKey = userSelection.date.key;
         console.log(packageData);

         if (packageData[packageKey] && packageData[packageKey].data) {
            const packages = packageData[packageKey].data;

            packages.forEach(pkg => {
               if (!pkg.available) return;

               const packageCard = document.createElement('div');
               packageCard.className = `package-card package-${pkg.package_name.toLowerCase().replace(' ', '-')}`;
               packageCard.setAttribute('data-package-id', pkg.id);

               // Determine badge based on package type
               let badge = '';


               const features = pkg.description ?
                  pkg.description.split(',').map(feature =>
                     `${feature.trim()}`
                  ).join('') :
                  '<li><i class="fas fa-check"></i> Basic venue access</li>';

               let buttonHtml = '';
               if (pkg.available_count <= 0) {
                  buttonHtml = `
            <button type="button" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold" disabled>
                Sold Out
            </button>
        `;
               } else {
                  const isAuthenticated = "{{ auth('customer')->check() ? 'true' : 'false' }}";

                  if (isAuthenticated == 'true') {
                     buttonHtml = `
                <a href="javascript:void(0);"
                   data-package_id="${pkg.id}"
                   data-venue_id="${pkg.venue_id}"
                   onclick="authbooknow(this)"
                   class="auth-book-now btn btn--primary btn-block btn-shadow mt-2 font-weight-bold"
                   data-link="{{ route('events-leads', [$eventData['id']]) }}">
                   {{ translate('Book Now') }}
                </a>
            `;
                  } else {
                     buttonHtml = `
                <a href="javascript:void(0);"
                   data-package_id="${pkg.id}"
                   data-venue_id="${pkg.venue_id}"
                   onclick="participatebook(this)"
                   class="participate-btn btn btn--primary btn-block btn-shadow mt-4 font-weight-bold"
                   data-link="{{ route('events-leads', [$eventData['id']]) }}">
                   {{ translate('Book Now') }}
                </a>
            `;
                  }
               }

               packageCard.innerHTML = `
                    ${badge}
                    <h3 class="package-name">${pkg.package_name}</h3>
                    <div class="package-price">${Number(pkg.price).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} )}</div>
                    ${pkg.available_count > 0 ? 
                        `<div class="package-availability"><marquee style=" font-size: 14px;color:#aa1405;" class="font-weight-bolder">Only ${pkg.available_count} tickets remaining.<small> {{ translate('Book now before they’re gone')}}!</small></marquee></div>` : 
                        ''}
                    <ul class="package-features">
                        ${features}
                    </ul>
                    <div class="package-button">
            ${buttonHtml}
        </div>
                `;

               packageCard.addEventListener('click', function() {
                  document.querySelectorAll('.package-card').forEach(c => c.classList.remove('selected'));
                  this.classList.add('selected');
                  userSelection.package = {
                     id: pkg.package_name,
                     name: pkg.package_name,
                     price: pkg.price,
                     available_count: pkg.available_count
                  };
               });
               packagesContainer.appendChild(packageCard);
            });

            // Auto-select first available package
            if (packagesContainer.children.length > 0) {
               packagesContainer.querySelector('.package-card').click();
            } else {
               packagesContainer.innerHTML = `
                    <div class="no-packages">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Packages Available</h3>
                        <p>There are no available packages for the selected date.</p>
                    </div>
                `;
            }
         } else {
            packagesContainer.innerHTML = `
                <div class="no-packages">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>No Package Data</h3>
                    <p>No package information available for the selected date.</p>
                </div>
            `;
         }
      }
   }

   const participateBtn = packageCard.querySelector('.participate-btn, .auth-book-now');
   if (participateBtn) {
      participateBtn.addEventListener('click', function(e) {
         e.stopPropagation(); // Prevent card selection when clicking button

         // Get the selected package data
         const selectedPackage = userSelection.package;
         if (!selectedPackage) {
            alert('Please select this package first by clicking on the card');
            return;
         }
         handleBooking(selectedPackage, userSelection.venue, userSelection.date);
      });
   }

   function participatebook(that) {
      $('#participateModal').modal('show');
      $(".package_id_model").val($(that).data('package_id'));
      $(".venue_id_model").val($(that).data('venue_id'));
      $("#lead-store-form").attr('action', $(that).data('link'));
   }

   function authbooknow(that) {
      $("#lead-store-form").attr('action', $(that).data('link'));
      $(".package_id_model").val($(that).data('package_id'));
      $(".venue_id_model").val($(that).data('venue_id'));
      $('#lead-store-form').submit();
   }
</script>
@endpush