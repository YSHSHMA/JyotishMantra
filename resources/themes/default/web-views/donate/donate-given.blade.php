@extends('layouts.front-end.app')
@section('title', translate('Donate') )
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<style>
    .one-line-show {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .donor1 .input-group .btn:focus {
        z-index: 3;
    }

    .cat label input {
        position: absolute;
        display: none;
        color: #fff !important;
    }

    .txtemv_4 {
        width: 50px;
        background-color: #f4f9fe;
        border: 1px solid #d5e8fc;
        border-radius: 5px;
        text-align: center;
        height: 40px;
    }

    .on_amt_enter {
        background-color: #f4f9fe;
        border: 1px solid #d5e8fc;
        border-radius: 5px;
        text-align: center;
        height: 40px;
    }

    .product_don_icon .product_1000 ul {
        display: flex;
        flex-wrap: wrap;
        -moz-box-pack: justify;
        justify-content: space-between;
    }

    .product_don_icon .product_1000 ul li {
        width: 47%;
    }


    /*  */
    /* 
===============================================    
==============================================
    */
</style>
<style>
    .rotate-animation {
        display: inline-block;
        transition: transform 0.5s ease-in-out, background-color 2s ease-in-out;
        background-color: #007bff;
        /* Bootstrap primary button color */
        color: white;
    }

    .rotate-animation:hover {
        transform: rotate(360deg) scale(1.1);
        background-color: #36bb36 !important;
        color: white !important;
        border-color: #36bb36 !important;
    }
</style>
<style>
    /* Prograss */
    @media (min-width: 768px) {
        .md\:top-\[68px\] {
            top: 68px;
        }
    }

    .most_donated {
        font-size: 7px;
        border-color: #42b842 !important;
        border-top: 0;
        padding: 8px 6px 1px 4px;
        border: 1px solid;
        border-radius: 11px;
        border-top: 0;
        text-wrap: nowrap;
    }

    @media (min-width: 768px) {
        .most_donated {
            font-size: 10px;
            margin: 0px 0px 0px -44px;
            position: absolute;
            text-wrap: nowrap;
        }
    }

    @media (max-width: 767px) {
        .new-button-css {
            font-size: 9px !important;
        }

        .new-button-css:before {
            padding: 0 9px !important;
        }

        .donation-option {
            font-size: 10px;
            padding: 9px 5px;
        }
    }

    .new-button-css {
        opacity: 1;
        outline: 0;
        color: #fff;
        line-height: 40px;
        position: relative;
        text-align: center;
        letter-spacing: 1px;
        display: inline-block;
        text-decoration: none;
        font-family: 'Open Sans';
        text-transform: uppercase;
        /* font-size: 14px; */

        &:hover {

            &:after {
                opacity: 1;
                transform: translateY(0) rotateX(0);
            }

            &:before {
                opacity: 0;
                transform: translateY(50%) rotateX(90deg);
            }
        }

        &:after {
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            color: white;
            display: block;
            transition: 0.5s;
            position: absolute;
            background: #cc7a00;
            content: attr(data-back);
            transform: translateY(-50%) rotateX(90deg);
            border-radius: 9px;
        }

        &:before {
            top: 0;
            left: 0;
            opacity: 1;
            color: white;
            display: block;
            padding: 0 16px;
            line-height: 40px;
            position: relative;
            background: #ff9200;
            content: attr(data-front);
            transform: translateY(0) rotateX(0);
            transition: 0.5s;
            border-radius: 9px;
        }
    }

    .check_box {
        display: none;
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

    .donation-options {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .donation-option {
        flex: 1;
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 4px;
        text-align: center;
        cursor: pointer;
    }

    .donation-option.active {
        border-color: #ff9200;
        background-color: #ff920094;
        color: white;
    }
</style>
<style>
    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 138px;
        height: 3px;
        background-color: #ff9200;
    }

    .quantity-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #ff9200;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .quantity-controls {
        justify-content: space-evenly;
    }

    .card-img-container {
        width: 100%;
        height: 150px;
        overflow: hidden;
        border-radius: 10px 10px 0 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .card-img-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .add-donate-frequency::-webkit-scrollbar {
        height: 1px;
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
                            <path d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z" fill="white"></path>
                        </svg>
                        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium  ">{{ translate('Add Details') }}</div>
                    </div>
                    <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z" fill="#9CA3AF"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z" fill="#9CA3AF"></path>
                        </svg>
                    </div>
                    <div class="d-flex justify-center items-center">
                        <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                            <path d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z" fill="white"></path>
                        </svg>
                        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">{{ translate('Donate')}}</div>
                    </div>
                    <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
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
<div class="container mt-3 rtl my-3 text-align-direction" id="cart-summary">
    <div class="row g-3 mx-max-md-0">
        <section class="col-12 col-md-12 col-lg-6  px-0 px-md-2">
            <div class="row">
                @if(count($images) > 1)
                <div class="col-12 p-4">
                    <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach ($images as $index => $img)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <img src="{{ $img }}" class="d-block w-100 img-fluid" alt="Image {{ $index }}">
                            </div>
                            @endforeach
                        </div>

                        @if(count($images) > 1)
                        <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                        @endif
                    </div>
                </div>
                @else
                <img src="{{ $images[0] ?? '' }}" class="img-fluid w-100 p-2" alt="Image">
                @endif
            </div>
            @if($donateList['type'] == 'outsite')
            <div class="d-flex" style="justify-content: space-around;">
                @if(!empty($donateList['twelve_a_number']))
                <div class="card mb-3 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="tio-document_text_outlined">document_text_outlined</i>
                        </div>
                        <div class="ml-3">
                            <small class="text-muted">80G Number</small><br>
                            <h5 class="mb-0 font-weight-bold">
                                {{ $donateList['twelve_a_number'] ?? '' }}
                            </h5>
                        </div>
                    </div>
                </div>
                @endif
                @if(!empty($donateList['astin_g_number']))
                <div class="card mb-3 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="tio-document_text_outlined">document_text_outlined</i>
                        </div>
                        <div class="ml-3">
                            <small class="text-muted">12A Number</small><br>
                            <h5 class="mb-0 font-weight-bold">
                                {{ $donateList['astin_g_number'] ?? '' }}
                            </h5>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
            <?php
            $DonateProduct = [];
            ?>
            @if($donateList['set_json'] && json_decode($donateList['set_json'],true))
            <?php $langs = app()->getLocale();
            $getArray = json_decode($donateList['set_json'], true);
            $DonateProduct = $getArray[$langs] ?? [];
            ?>
            @endif
            @if($DonateProduct && count($DonateProduct) > 0)
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-12 card mt-1">
                        <div class="card-body p-0">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle">
                                <tbody>
                                    <tr>
                                        <td class="__w-45">
                                            <div class="container-fluid p-0">
                                                <div class="row mb-2">
                                                    <div class="col-3 from-group col-sm-3 text-center mt-2"><a class="new-button-css" data-back="{{ translate('Donate')}}" data-front="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: 101), currencyCode: getCurrencyCode())}}" onclick="$('.cut_copy_paste_block').val(101);$('#div_add_pluse_button_2_4').css('display', 'block');$('#div_all_button_2_4').css('display', 'none');$('.check_box').prop('checked', false);$('.input_val').val(1);calculator(); "></a></div>
                                                    <div class="col-3 from-group col-sm-3 text-center mt-2"><a class="new-button-css" data-back="{{ translate('Donate')}}" data-front="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: 501), currencyCode: getCurrencyCode())}}" onclick="$('.cut_copy_paste_block').val(501);$('#div_add_pluse_button_2_4').css('display', 'block');$('#div_all_button_2_4').css('display', 'none');$('.check_box').prop('checked', false);$('.input_val').val(1);calculator(); "></a>
                                                        <div class="">
                                                            <small class="most_donated font-weight-bold">Mostly donated</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 from-group col-sm-3 text-center mt-2"><a class="new-button-css" data-back="{{ translate('Donate')}}" data-front="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: 1001), currencyCode: getCurrencyCode())}}" onclick="$('.cut_copy_paste_block').val(1001);$('#div_add_pluse_button_2_4').css('display', 'block');$('#div_all_button_2_4').css('display', 'none');$('.check_box').prop('checked', false);$('.input_val').val(1);calculator(); "></a>
                                                        <div class="">
                                                            <small class="most_donated  font-weight-bold">Mostly donated</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 from-group col-sm-3 text-center mt-2"><a class="new-button-css" data-back="{{ translate('Donate')}}" data-front="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: 5001), currencyCode: getCurrencyCode())}}" onclick="$('.cut_copy_paste_block').val(5001); $('#div_add_pluse_button_2_4').css('display', 'block');$('#div_all_button_2_4').css('display', 'none');$('.check_box').prop('checked', false);$('.input_val').val(1);calculator();"></a></div>
                                                </div>
                                                <div class="row mt-4">
                                                    <div class="col-12 mt-2 mb-2">
                                                        <hr>
                                                    </div>
                                                    <div class="col-6 form-group col-sm-6 text-center mt-2 mx-auto">
                                                        <label class="w-100">Enter Custom Amount</label>
                                                        <input min="0" type="number" class="form-control cut_copy_paste_block on_amt_enter 4" oninput="validInt(this)" value="501" onpaste="return false" style="text-transform: capitalize;" placeholder="Enter Amount" maxlength="10">
                                                        <input type="hidden" class="form-control cut_product_amount_block on_amt_enter 4" style="text-transform: capitalize;">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 my-3">
                    <h3 class="section-title">{{ translate('Show More Support') }}</h3>
                </div>
                @foreach($DonateProduct as $inn=>$jsons)
                <div class="col-md-4 my-1">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="card-img-container">
                                <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/' .($jsons['image']??''), type: 'product') }}" alt="">
                            </div>
                            <h6 class="font-weight-bolder text-warning mb-1 one-line-show">{{ $jsons['set_title'] }}</h6>
                            <span class="font-weight-bolder text-danger mb-1 one-line-show">{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($jsons['set_amount']??0)), currencyCode: getCurrencyCode())}}/{{ ($jsons['set_number']??"") }}{{ ($jsons['set_unit']??"") }}</span>
                            <div class="quantity-controls d-flex">
                                <button class="quantity-btn" onclick="decrementQuantity('{{ ($jsons['id']??0) }}',this)" data-type="{{ $jsons['set_title'] }}" data-amount="{{ ($jsons['set_amount']??0) }}">-</button>
                                <div class="quantity-display mt-1 font-weight-bolder h4" id="quantity-1">0</div>
                                <button class="quantity-btn" onclick="incrementQuantity('{{ ($jsons['id']??0) }}',this)" data-type="{{ $jsons['set_title'] }}" data-amount="{{ ($jsons['set_amount']??0) }}" data-subtitle="{{ ($jsons['set_amount']??0) }}/{{ ($jsons['set_number']??'') }}{{ ($jsons['set_unit']??'') }}">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </section>

        <section class="col-lg-6 px-max-md-0">
            <div class="cards">
                <div class="card-header" id="">
                    <div class="details __h-100 mb-1">
                        <span class="mb-2 __inline-24">
                            {{ ucwords($donateList[str_replace('_', '-', app()->getLocale()).'_trust_name']) }}</span>
                    </div>
                    <div class="details __h-100 mb-2">
                        <span class="mb-2 __inline-24">{{ ucwords($donateList['name']) }}</span>
                    </div>
                    <?php
                    $porpose_name = '';
                    if (isset($donateList['purpose_id']) && !empty($donateList['purpose_id'])) {
                        $perpouses  = \App\Models\DonateCategory::where('id', $donateList['purpose_id'])->first();
                        $porpose_name = $perpouses['name'];
                    }
                    ?>
                </div>
            </div>
            @if(empty($DonateProduct) && 1 > count($DonateProduct))
            <div class="col-lg-12">
                <div class="col-12 card mt-1" id="productList">
                    <div class="card-body p-0">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle">
                            <tbody>
                                <tr>
                                    <td class="__w-45">
                                        <div class="container-fluid p-0">
                                            <div class="row g-2">
                                                <div class="col-sm-12 col-md-6 d-flex flex-wrap align-items-start">
                                                    <div class="me-3 mb-2">
                                                        <a class="position-relative overflow-hidden">
                                                            @if($donateList['type'] == 'outsite')
                                                            <img class="rounded img-fluid" style="width: 62px;" src="{{ $trust_image }}" alt="Product">
                                                            @else
                                                            <img class="rounded img-fluid" style="width: 62px;" src="{{ $images[0] ?? '' }}" alt="Product">
                                                            @endif
                                                        </a>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="text-break">
                                                            <a class="fw-bold d-block">
                                                                {{ ucwords($porpose_name ?? $donateList[str_replace('_', '-', app()->getLocale()).'_trust_name']) }}
                                                            </a>
                                                            <a class="small d-block">{{ ucwords($donateList['set_title']) }}</a>
                                                        </div>
                                                        <div class="fw-semibold mt-2 productPrice">₹00.0</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-6">
                                                    <div class="row g-2">
                                                        @if($donateList['set_type'] == 1)
                                                        <div class="col-12" id="div_add_pluse_button_2_4">
                                                            <label class="w-100 btn btn--primary btn-sm text-white d-flex align-items-center justify-content-center" style="padding: 4px 12px;">
                                                                <input name="pkg_4" class="check_box me-2" onclick="fun_pagkg_div_button_hide_show()" type="checkbox" value="{{ $donateList['set_amount'] }}">
                                                                ₹{{ $donateList['set_amount'] }}/{{ $donateList['set_number'] > 0 ? $donateList['set_number'] : '' }} {{ $donateList['set_unit'] }}
                                                            </label>
                                                        </div>
                                                        <div class="col-12 text-center bbb" id="div_all_button_2_4" style="display: none;">
                                                            <div class="input-group justify-content-center">
                                                                <span class="input-group-btn">
                                                                    <button type="button" value="minus" class="btn btn---primary btn-number" data-type="minus" data-field="quant[4]">
                                                                        <i class="fa fa-minus"></i>
                                                                    </button>
                                                                </span>
                                                                <input disabled type="text" name="quant[4]" class="form-control text-center input-number input_val txtemv_4 mx-1" value="1" min="1" max="50" style="max-width: 60px;">
                                                                <span class="input-group-btn">
                                                                    <button type="button" value="plus" class="btn btn---primary btn-number" data-type="plus" data-field="quant[4]">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 text-center"><small>OR</small></div>
                                                        @endif
                                                        <div class="col-12 mt-1"><input min="11" type="number" class="form-control cut_copy_paste_block on_amt_enter 4" oninput="validInt(this)" value="501" onpaste="return false" style="text-transform: capitalize;" placeholder="Enter Amount" maxlength="10"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-12 mt-2 mb-2">
                                                    <hr>
                                                </div>
                                                <div class="col-3 col-sm-3 text-center mt-2"><a class="new-button-css" data-back="{{ translate('Donate')}}" data-front="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: 101), currencyCode: getCurrencyCode())}}" onclick="$('.cut_copy_paste_block').val(101);$('#div_add_pluse_button_2_4').css('display', 'block');$('#div_all_button_2_4').css('display', 'none');$('.check_box').prop('checked', false);$('.input_val').val(1);calculator(); "></a></div>
                                                <div class="col-3 col-sm-3 text-center mt-2"><a class="new-button-css" data-back="{{ translate('Donate')}}" data-front="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: 501), currencyCode: getCurrencyCode())}}" onclick="$('.cut_copy_paste_block').val(501);$('#div_add_pluse_button_2_4').css('display', 'block');$('#div_all_button_2_4').css('display', 'none');$('.check_box').prop('checked', false);$('.input_val').val(1);calculator(); "></a>
                                                    <div class="">
                                                        <small class="most_donated font-weight-bold">Mostly donated</small>
                                                    </div>
                                                </div>
                                                <div class="col-3 col-sm-3 text-center mt-2"><a class="new-button-css" data-back="{{ translate('Donate')}}" data-front="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: 1001), currencyCode: getCurrencyCode())}}" onclick="$('.cut_copy_paste_block').val(1001);$('#div_add_pluse_button_2_4').css('display', 'block');$('#div_all_button_2_4').css('display', 'none');$('.check_box').prop('checked', false);$('.input_val').val(1);calculator(); "></a>
                                                    <div class="">
                                                        <small class="most_donated  font-weight-bold">Mostly donated</small>
                                                    </div>
                                                </div>
                                                <div class="col-3 col-sm-3 text-center mt-2"><a class="new-button-css" data-back="{{ translate('Donate')}}" data-front="{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: 5001), currencyCode: getCurrencyCode())}}" onclick="$('.cut_copy_paste_block').val(5001); $('#div_add_pluse_button_2_4').css('display', 'block');$('#div_all_button_2_4').css('display', 'none');$('.check_box').prop('checked', false);$('.input_val').val(1);calculator();"></a></div>
                                            </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif


            <aside class="col-lg-12 pt-2 pt-lg-2 px-max-md-0 order-summery-aside" style="position: sticky;top: 9rem;">
                <div class="__cart-total __cart-total_sticky">
                    <div class="cart_total p-0">
                        <?php $getWalletAmount = (\App\Models\User::where('id', auth('customer')->id())->first()['wallet_balance'] ?? 0); ?>
                        @if($getWalletAmount > 0)
                        <div class="row">
                            <div class="col-12 text-end">
                                <input type="checkbox" onclick="wallet_checked(this)" class="wallet_checked" value="1" data-amount="{{ (\App\Models\User::where('id',auth('customer')->id())->first()['wallet_balance']??0)  }}" checked onload="calculator()">&nbsp;{{ translate('apply_Wallet')}}
                            </div>
                        </div>
                        <div class="col-12 my-1">
                            <hr>
                        </div>
                        @endif
                        @if($DonateProduct && count($DonateProduct) > 0)
                        <div class="row show_all_products">
                            <div class="col-12 text-center my-2">
                                <span>{{ translate('Add Product') }} ?</span>
                            </div>
                        </div>
                        <div class="col-12 my-1">
                            <hr>
                        </div>
                        @endif
                        <div class="row mt-4 add-donate-frequency {{ (((\App\Models\User::where('id',auth('customer')->id())->first()['wallet_balance']??0) > 0)?'d-none':'' )}}" style="overflow: scroll;">
                            <div class="col-12 form-group">
                                <label class="font-weight-bolder">Donation Frequency</label>
                                <div class="donation-options">
                                    <div class="donation-option  {{ (($getWalletAmount > 0)?'active':'') }}" data-frequency="one_time">One Time</div>
                                    <div class="donation-option  {{ (($getWalletAmount > 0)?'':'active') }}" data-frequency="weekly">Weekly</div>
                                    <div class="donation-option" data-frequency="monthly">Monthly</div>
                                    <div class="donation-option" data-frequency="quarterly">Quarterly</div>
                                    <div class="donation-option" data-frequency="yearly">Yearly</div>
                                </div>
                                <input type="hidden" id="frequency" name="frequency" value="{{ (($getWalletAmount > 0)?'one_time':'weekly') }}">
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="cart_title font-weight-bold">{{ translate('Donate Amount')}}</span>
                            <span class="cart_value cart-amount-show" data-amount='0'>₹ 00.0</span>
                        </div>

                        <div class="d-none show_user_wallet_amount">
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="cart_title text-success font-weight-bold">
                                    <img width="20" src="{{ theme_asset(path: 'public/assets/back-end/img/admin-wallet.png')}}" style="margin-top: -9px;">Wallet Amount <small>({{ webCurrencyConverter(amount:(\App\Models\User::where('id',auth('customer')->id())->first()['wallet_balance']??0))  }})</small>
                                </span>
                                <span class="cart_value text-success user_wallet_amount"> {{ webCurrencyConverter(amount: (\App\Models\User::where('id',auth('customer')->id())->first()['wallet_balance']??0))  }} </span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between mt-2">
                                <span class="cart_title text-success font-weight-bold user_wallet_am_remaining_text font-weight-bold" style="color: darkred !important;">{{ translate('Remaining Amount')}}</span>
                                <span class="cart_value text-success user_wallet_amount_remaining" style="color: darkred !important;"> </span>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="justify-content-between d-flex">
                            <span class="cart_title text-primary font-weight-bold">{{ translate('Final Amount')}}</span>
                            <span class="cart_value" id="mainProductPrice"></span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="mt-4">
                        <button type="submit" class="btn btn--primary btn-block name_change_continues paynowdonate d-none d-sm-block">{{ translate('Proceed to Checkout')}}</button>
                    </div>
                </div>
            </aside>
        </section>
    </div>
</div>
<form method="post" class="digital_payment" id="razor_pay_form" action="{{ route('donate-payment-request')}}">
    @csrf
    <div class="Details">
        <input type="hidden" name="leads_id" value="{{ $leads_id }}">
        <input type="hidden" name="person_name" value="{{ $customer['name']}}">
        <input type="hidden" name="person_phone" value="{{ $customer['phone']}}">
        <input type="hidden" name="set_qty" value="" class='show_qty'>
        <input type="hidden" name="wallet_type" class="user-wallet-adds" value="0">
    </div>
</form>

<button class="text-white font-semibold py-3 px-6 rounded-0 shadow-lg transition-colors duration-200 w-100 btn btn--primary btn-block paynowdonate d-block d-sm-none"
    style="background-color:#FF7700; position:fixed; bottom:0; left:0; z-index:9999;"
    onmouseover="this.style.backgroundColor='#e66600'"
    onmouseout="this.style.backgroundColor='#FF7700'">
   ( <span class="cart-amount-show"></span> ) <span class="name_change_continues">{{ translate('book_Now') }}</span>
</button>
@endsection
@push('script')
</script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const DonateProductCart = [];
    document.querySelectorAll('.donation-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.donation-option').forEach(opt => {
                opt.classList.remove('active');
            });
            this.classList.add('active');
            document.getElementById('frequency').value = this.dataset.frequency;
            calculator();
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#NewNumberAdd").change(function() {
            if ($(this).is(":checked")) {
                $("#newPhoneAdd").show();
                $("#newPhoneAdd input[name='newPhone']").prop("required", true);
            } else {
                $("#newPhoneAdd").hide();
                $("#newPhoneAdd input[name='newPhone']").prop("required", false);
            }
        });
        // Check the Gutra 
        $(document).ready(function() {
            $("#gotraCheck").change(function() {
                if ($(this).is(":checked")) {
                    $("#GotraId").prop("readonly", true).val("kasypa");
                } else {
                    $("#GotraId").prop("readonly", false).val("");
                }
            });
        });
        // add the condition button YES ANd NO
        $(".hideable-div").hide();
        $("button.yes-btn").click(function() {
            $('#is_prashad').val(1);
            $(".hideable-div").show();
            $(this).css("background-color", "#00FF00");
            $("button.no-btn").css("background-color", "orange");
        });
        $("button.no-btn").click(function() {
            $('#is_prashad').val(0);
            $(".hideable-div").hide();
            $(this).css("background-color", "#FF0000");
            $("button.yes-btn").css("background-color", "orange");
        });

    });
</script>
<script>
    $(document).ready(function() {
        if ($('input[name="is_prashad"]:checked').val() === 'yes' && $('input[name="newnumber"]:checked').length > 0) {
            $('#sankalp_check').validate({
                rules: {
                    members: {
                        required: true
                    },
                    gotra: {
                        required: true
                    }
                },
                messages: {
                    members: {
                        required: "Please enter your Family Member"
                    },
                    gotra: {
                        required: "Please enter your Gotra"
                    }
                },
            });
        }
    });

    //  this page
    function validInt(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        let numValue = parseInt(value) || 0;
        if (numValue.toString().length < 3) {
            setTimeout(() => {
                input.value = numValue;
                if (input.value < 1) {
                    numValue = 0;
                }
                input.value = numValue;
                calculator();
            }, 100);
        } else {
            input.value = numValue;
            calculator();
        }
    }

    function fun_pagkg_div_button_hide_show() {
        $("#div_add_pluse_button_2_4").css('display', 'none');
        $("#div_all_button_2_4").css('display', 'block');
        $(".cut_copy_paste_block").val('');
        calculator()
    }

    $('.cut_copy_paste_block').on("input", function(e) {
        e.preventDefault();
        $(".input_val").val(1);
        $("#div_add_pluse_button_2_4").css('display', 'block');
        $("#div_all_button_2_4").css('display', 'none');
        $('.check_box').prop('checked', false);
        calculator();
    });

    $('.btn-number').click(function(e) {
        e.preventDefault();
        var type = $(this).attr('data-type');
        $(".cut_copy_paste_block").val('');
        var input = $("input[name='" + $(this).attr('data-field') + "']");
        var currentVal = parseInt(input.val()); //(parseInt(input.val()) > 0) ? parseInt(input.val()) : 1;
        if (!isNaN(currentVal)) {
            if (type === 'minus') {
                if (currentVal > parseInt(input.attr('min'))) {
                    input.val(currentVal - 1);
                }
                if (1 > parseInt(input.val())) {
                    $("#div_add_pluse_button_2_4").css('display', 'block');
                    $("#div_all_button_2_4").css('display', 'none');
                    $('.check_box').prop('checked', false);
                } else if (1 == parseInt(input.val())) {
                    $('button[data-type="minus"]').css('display', 'none');
                } else {
                    $('button[data-type="minus"]').css('display', '');
                }
            } else if (type === 'plus') {
                $('button[data-type="minus"]').css('display', '');
                if (currentVal < parseInt(input.attr('max'))) {
                    input.val(currentVal + 1);
                    // $(this).closest('.input-group').find("[data-type='minus']").removeAttr('disabled');
                }
                if (parseInt(input.val()) == input.attr('max')) {
                    toastr.error(`${parseInt(input.val())} max value use`);
                }
            }
        } else {
            input.val(1);
        }
        calculator();
    });
    calculator();

    function calculator() {
        var result = 0;
        let flat = 0;
        if (Object.keys(DonateProductCart).length > 0) {
            let prodList = $('.cut_product_amount_block').val();
            flat = Number($('.cut_copy_paste_block').val()) + Number(prodList);
        } else {
            flat = ($('.cut_copy_paste_block').val());
        }
        var check = $('.check_box').val();
        if ($('.check_box').is(':checked')) {
            var check = parseInt($('.check_box').val());
            var inputNumber = parseInt($('.input-number').val());
            result = check * inputNumber;
            if (isNaN(result)) {
                result = 0;
            }
            $(".cart-amount-show").text(`₹${result}`);
            $(".productPrice").text(`${inputNumber} x ₹${check}`);
            $(".show_qty").val(inputNumber);
        } else {
            result = parseInt(flat);
            if (isNaN(result)) {
                result = 0;
            }
            $(".show_qty").val(0);
            $(".cart-amount-show").text(`₹${result}`);
            $(".productPrice").text(`₹${result}`);
        }
        $('.btnDonateamount').data('amount', result);
        $('.inputDonateamount').val(result);
        $.ajax({
            url: "{{ route('donate-lead-update') }}",
            data: {
                amount: result,
                donateproductcart: DonateProductCart,
                'lead_id': "{{ $leadId }}",
                'frequency': $("#frequency").val(),
                "_token": "{{ csrf_token() }}"
            },
            dataType: "json",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#loading').removeClass('d--none');
                $('#loading').css('index', 1000);
            },
            success: function(data) {
                $('#loading').addClass('d--none');
            }
        });
        var isChecked = $('.wallet_checked').prop('checked');
        let walletAmount = $('.wallet_checked').data('amount');
        if (isChecked) {
            $(".show_user_wallet_amount").removeClass('d-none');
            $(".user-wallet-adds").val(1);
            if (walletAmount >= result) {
                $(".user_wallet_amount_remaining").text(`${(0 - 0).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
                $(".name_change_continues").text(`{{ translate('donate_now')}}`);
                $(".user_wallet_amount").text(`${(result - 0).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
                $('#mainProductPrice').text(`${(0 - 0).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
            } else {
                $(".user_wallet_amount").text(`${(walletAmount - 0).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"})}`);
                $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout')}}`);
                let remainingAmount = result - walletAmount;
                let formattedAmount = remainingAmount.toLocaleString("en-US", {
                    style: "currency",
                    currency: "{{getCurrencyCode()}}"
                });
                $(".user_wallet_amount_remaining").text(`-${formattedAmount}`);
                $('#mainProductPrice').text(`${formattedAmount}`);
            }
        } else {
            $(".show_user_wallet_amount").addClass('d-none');
            $(".user-wallet-adds").val(0);
            $(".name_change_continues").text(`{{ translate('Proceed_To_Checkout')}}`);
            let formattedAmount1 = (result - 0).toLocaleString("en-US", {
                style: "currency",
                currency: "{{getCurrencyCode()}}"
            });
            $('#mainProductPrice').text(`${formattedAmount1}`);
        }

    }
    // model validation
    $(".pan_number_validation").change(function() {

        var regExp = /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/;
        var txtpan = $(this).val();
        if (txtpan.length == 10) {
            if (txtpan.match(regExp)) {

            } else {
                alert('Not a valid PAN number...!');
                event.preventDefault();
                $(this).val('');
                $(this).focus();
            }
        } else {
            alert('Please enter 10 digits for a valid PAN number...!');
            event.preventDefault();
            $(this).val('');
            $(this).focus();
        }
    });

    $(".mobile_number_validation").change(function() {

        var txt_mobile_number = $(this).val();

        if (txt_mobile_number.length < 10) {
            alert('Please enter 10 digits for a valid mobile number...!');
            $(this).val('');
            $(this).focus();
            return;
        }
    });
</script>
<script>
    document.getElementById('razor_pay_form').addEventListener('submit', function(event) {
        var amount = $('.inputDonateamount').val();
        if (amount <= 0 || amount === "") {
            event.preventDefault(); // Prevent form submission
            toastr.error('{{ translate("The payment amount must be greater than 0")}}.');
        } else {
            return false;
        }
    });

    function wallet_checked() {
        calculator();
        var isChecked = $('.wallet_checked').prop('checked');
        if (isChecked) {
            $('.add-donate-frequency').addClass('d-none');
            $('.donation-option[data-frequency="one_time"]').click();
        } else {
            $('.add-donate-frequency').removeClass('d-none');
            $('.donation-option[data-frequency="weekly"]').click();
        }

    }

    $('.paynowdonate').click(function() {
        $('#razor_pay_form').submit();
    })

    function incrementQuantity(key, btn) {
        let display = $(btn).closest('.quantity-controls').find('.quantity-display');
        let currentQty = parseInt(display.text());
        currentQty++;
        display.text(currentQty);
        let title = $(btn).data('type');
        let amount = $(btn).data('amount');
        let subtitle = $(btn).data('subtitle');
        DonateProductCart[key] = {
            id: key,
            title: title,
            subtitle: subtitle,
            amount: amount,
            qty: currentQty,
        };
        console.log("Updated Cart:", DonateProductCart);
        addAllproduct();
    }

    function decrementQuantity(key, btn) {
        let display = $(btn).closest('.quantity-controls').find('.quantity-display');
        let currentQty = parseInt(display.text());
        if (currentQty > 0) currentQty--;
        display.text(currentQty);
        if (currentQty === 0) {
            delete DonateProductCart[key];
        } else {
            DonateProductCart[key].qty = currentQty;
        }
        console.log("Updated Cart:", DonateProductCart);
        addAllproduct();
    }

    function addAllproduct() {
        let total = 0;
        let newproduct = $('.show_all_products');
        newproduct.empty();
        Object.keys(DonateProductCart).forEach(key => {
            let item = DonateProductCart[key];
            let amount = parseFloat(item.amount) || 0;
            let qty = parseInt(item.qty) || 0;
            let totalPrice = amount * qty;
            let html = `
        <div class="col-12 p-2">
            <div class="d-flex align-items-center border rounded p-2 bg-light">
                
                <div class="flex-grow-1">
                    <div class="font-weight-bold small mb-1">
                    <h5 class="m-0">${item.subtitle}</h5>
                    <small > ${item.title}</small>
                    </div>

                    <div class="small text-muted">
                        Qty: <strong>${qty}</strong>  
                        &nbsp; | &nbsp;  
                        Price: <strong>${totalPrice}</strong>
                    </div>
                </div>

                <div class="text-right ml-3">
                    <span class="badge badge-success p-2">
                        ₹ ${totalPrice}
                    </span>
                </div>
            </div>
        </div>`;
            newproduct.append(html);
            total += amount * qty;
        });
        if (Object.keys(DonateProductCart).length < 1) {
            newproduct.html(`<div class="col-12 text-center my-2">
                                <span>{{ translate('Add Product') }} ?</span>
                            </div>`);
        }
        $('.cut_product_amount_block').val(total);
        calculator();
    }
</script>
@endpush