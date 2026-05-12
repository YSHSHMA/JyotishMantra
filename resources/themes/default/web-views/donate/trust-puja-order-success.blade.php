@extends('layouts.front-end.app')
@section('title', 'Trust Puja' )
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
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
                            <path d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z" fill="white"></path>
                        </svg>
                        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium  ">Puja Order</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5 mb-5 rtl __inline-53 text-align-direction">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10 col-lg-10">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <i class="fa fa-check-circle __text-60px __color-0f9d58"></i>
                    </div>
                    <div class="row text-center">
                        <div class="col-6 col-md-6 form-group">Order Id</div>
                        <div class="col-6 col-md-6 form-group">{{ $order['order_id'] }}</div>
                        <div class="col-6 col-md-6 form-group">Puja Name</div>
                        <div class="col-6 col-md-6 form-group">{{ $order['puja_name'] }}</div>
                        <div class="col-6 col-md-6 form-group">Total Amount</div>
                        <div class="col-6 col-md-6 form-group">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (($order['tax_amount']??0) + $order['pprice']??0)), currencyCode: getCurrencyCode()) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
@endpush