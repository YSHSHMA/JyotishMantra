@extends('layouts.front-end.app')
@section('title', $anushthan['meta_title'])
@php
    $currentTime = \Carbon\Carbon::now();
    $startTime = \Carbon\Carbon::createFromTime(4, 0, 0);
    $endTime = \Carbon\Carbon::createFromTime(18, 0, 0);
@endphp
@push('css_or_js')
    <meta name="description" content="{{ $anushthan->meta_description }}">
    <meta name="keywords"
        content="@foreach (explode(' ', $anushthan['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
    @if ($anushthan['meta_image'] != null)
        <meta property="og:image"
            content="{{ dynamicStorage('storage/app/public/pooja/meta') }}/{{ $anushthan->meta_image }}" />
        <meta property="twitter:card"
            content="{{ dynamicStorage('storage/app/public/pooja/meta') }}/{{ $anushthan->meta_image }}" />
    @else
        <meta property="og:image"
            content="{{ dynamicStorage('storage/app/public/product/thumbnail') }}/{{ $anushthan->thumbnail }}" />
        <meta property="twitter:card"
            content="{{ dynamicStorage('storage/app/public/product/thumbnail') }}/{{ $anushthan->thumbnail }}" />
    @endif

    @if ($anushthan['meta_title'] != null)
        <meta property="og:title" content="{{ $anushthan->meta_title }}" />
        <meta property="twitter:title" content="{{ $anushthan->meta_title }}" />
    @else
        <meta property="og:title" content="{{ $anushthan->name }}" />
        <meta property="twitter:title" content="{{ $anushthan->name }}" />
    @endif
    <meta property="og:url" content="{{ route('product', [$anushthan->slug]) }}">
    @if ($anushthan['meta_description'] != null)
        <meta property="twitter:description" content="{!! Str::limit($anushthan['meta_description'], 55) !!}">
        <meta property="og:description" content="{!! Str::limit($anushthan['meta_description'], 55) !!}">
    @else
        <meta property="og:description"
            content="@foreach (explode(' ', $anushthan['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
        <meta property="twitter:description"
            content="@foreach (explode(' ', $anushthan['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
    @endif
    <meta property="twitter:url" content="{{ route('product', [$anushthan->slug]) }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/poojadetails.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/product-details.css') }}" />
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <style>
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

        @media only screen and (max-width: 600px) {
            a.section-link {
                padding: 5px 8px;
                font-size: 10px;
            }
            .w-70 {
                width: 100% !important;
            }

            .font-10 {
                font-size: 14px;
                font-weight: 700;
            }

            .circle-img-container {
                width: 23px !important;
            }
        }

        .w-70 {
            width: 70%;
        }
        

        .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
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
            height: 50px;
            width: 50px;
            background-color: transparent;
            border-radius: 4px;
            border: 1px solid #2f8f1f;
            text-align: center;
            outline: none;
            font-size: 18px;
            /* Firefox */
        }

        #timer {
            font-size: 18px;
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

        .partial-pooja {
            background: white;
            box-shadow: 0px 3px 6px rgb(0 0 0 / 29%);
            border-radius: 5px;
            border-top: 2px solid #fe9802;
        }

        /* table responsive */
        .package-content-container {
            position: relative;
            height: 545px;
            overflow: hidden;
        }

        .package-content-slide {
            position: absolute;
            width: 100%;
            transition: transform 0.5s ease-in-out;
        }

        .tabCard.active {
            /* border-color: #007bff !important; */
            background-color: #e6e6e6;
        }

        .tabCard .verified-badge {
            display: none;
        }

        .card-radio:checked+.tabCard .verified-badge {
            display: flex;
        }

        .verified-badge {
            width: 20px;
            height: 20px;
            background-color: #06992e;
            /* Deep orange-red */
            color: white;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            /* Makes it a perfect circle */
            position: absolute;
            top: 2px;
            right: 2px;
            z-index: 2;
        }

        /* Responsive sizing */
        @media (max-width: 768px) {
            .tabCard {
                max-width: 80px;
            }
        }

        @media (max-width: 576px) {
            .tabCard {
                max-width: 70px;
            }

            .card-radio:checked+.tabCard::after {
                width: 16px;
                height: 16px;
                font-size: 12px;
            }
        }

        @media (min-width: 769px) {
            .tabCard {
                max-width: 120px;
            }
        }

        .tablinst {
            max-width: 80rem;
        }

        .w-full {
            width: 100%;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .gap-4 {
            gap: 1rem;
        }

        .grid-cols-3 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .w-full {
            width: 100%;
        }

        .grid {
            display: grid;
        }

        .button-sticky {
            border-radius: 5px 5px 0 0;
            border: 1px solid rgba(20, 85, 172, 0.05);
            box-shadow: 0 -7px 30px 0 rgba(0, 113, 220, 0.1);
            position: sticky;
            bottom: 0;
            left: 0;
            z-index: 99;
            transition: all 150ms ease-in-out;
        }

        .font-weight-bold-800 {
            font-weight: 800 !important;
        }

        @media (max-width: 768px) {
            .otp-input-fields input {
                height: 40px;
                width: 40px;
            }

            .otp-input-fields {
                gap: 9px;
            }
        }
    </style>
@endpush
@section('content')
    <div class="w-full h-full sticky top-0 z-20 d-none d-md-block" style="top: 84px;">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.aushthan.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="__inline-23">
        <div class="container mt-4 rtl text-align-direction">
            <div class="row {{ Session::get('direction') === 'rtl' ? '__dir-rtl' : '' }}">
                <div class="col-lg-12 col-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-4 col-12">
                            <div class="cz-product-gallery">
                                <div class="cz-preview">
                                    <div id="sync1" class="owl-carousel owl-theme product-thumbnail-slider">
                                        @if ($anushthan->images != null && json_decode($anushthan->images) > 0)
                                            @foreach (json_decode($anushthan->images) as $key => $photo)
                                                <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                                    id="image{{ $key }}">
                                                    <img class="cz-image-zoom img-responsive w-100"
                                                        src="{{ getValidImage(path: 'storage/app/public/pooja/vip/' . $photo, type: 'product') }}"
                                                        data-zoom="{{ getValidImage(path: 'storage/app/public/pooja/vip/' . $photo, type: 'product') }}"
                                                        alt="{{ translate('product') }}" width="">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <button type="button" data-product-id="{{ $anushthan['id'] }}"
                                        class="btn __text-18px border wishList-pos-btn d-sm-none product-action-add-wishlist">
                                        <i class="fa fa-heart wishlist_icon_{{ $anushthan['id'] }} web-text-primary"
                                            aria-hidden="true"></i>
                                    </button>
                                    <div class="sharethis-inline-share-buttons share--icons text-align-direction">
                                    </div>
                                </div>
                                <div class="cz">
                                    <div class="table-responsive __max-h-515px" data-simplebar>
                                        <div class="d-flex">
                                            <div id="sync2" class="owl-carousel owl-theme product-thumb-slider">
                                                @if ($anushthan->images != null && json_decode($anushthan->images) > 0)
                                                    @foreach (json_decode($anushthan->images) as $key => $photo)
                                                        <div class="">
                                                            <a class="product-preview-thumb {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                                                                id="preview-img{{ $key }}"
                                                                href="#image{{ $key }}">
                                                                <img alt="{{ translate('product') }}"
                                                                    src="{{ getValidImage(path: 'storage/app/public/pooja/vip/' . $photo, type: 'product') }}">
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
                                <span class="text-12 font-bold  line-clamp-2 text-ellipsis mb-0"
                                    style="color:#fe9802;">{{ strtoupper($anushthan->pooja_heading) }}
                                </span>
                                <div class="w-bar h-bar bg-gradient mt-2"></div>
                                <h5 class="card-title font-weight-700">{{ $anushthan->name }}</h5>
                                <span class="text-16 mt-2 pb-2">{{ $anushthan->short_benifits }}</span>

                                <!-- Profile Icon -->
                                 <div class="flex flex-col">
                                    <div class="flex flex-wrap justify-center items-center">
                                        <div class="w-70 d-flex justify-content-between">
                                            <div class="tray mb-3 ml-3 mr-0">
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
                                            </div>
                                            <!-- Ratings Display -->
                                            @foreach ($anushthanGet as $service)
                                                @php
                                                    $avgRating = (!empty($service->review_avg_rating) && $service->review_avg_rating > 0) ? $service->review_avg_rating : 5.5;
                                                    $fullStars = floor($avgRating);
                                                    $halfStar = $avgRating - $fullStars >= 0.5 ? 1 : 0;
                                                @endphp

                                                <div class="font-10">
                                                    <p
                                                        class="text-sm mt-4 font-medium border-b border-dashed border-primary font-weight-bold">
                                                        <i class="fas fa-star text-primary"></i>
                                                        {{ number_format($avgRating, 1) }}/5 (1K+
                                                        ratings)
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <!-- Count number of People -->
                                <div class="mt-[1px] mb-3 md:mb-0 flex flex-col">
                                    <div class="flex flex-row mt-2 flex-nowrap break-normal leading-normal">
                                        <div class="flex">
                                            <div class=""><span
                                                    class=" inline-flex break-normal">{{ translate('Till_now') }}</span><span
                                                    class=" font-bold text-#F18912 ml-1 break-normal">10000 +<span
                                                        class=" ml-1 mr-1 inline-flex break-normal">
                                                        {{ translate('devotees') }}</span></span><span
                                                    class="text-">{{ translate('have_participated_in_the_puja_organized_by_mahakal.com_puja_service') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="" role="button" class="d-none d-sm-block">
                                        <a href="#packages" id="AnushthanPackageButton"
                                            class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"  data-toggle="tab" role="tab">
                                            <span class="font-bold">{{ translate('select_puja_package') }}</span>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid" style="padding-left: 0 !important;padding-right:0 !important;">
            <div class="row">
                <div class="col-12">
                    <div class="navbar_section1 section-links w-100 mt-3 border-top border-bottom py-2 mb-4"
                        style="overflow: auto;">
                        <div class="d-flex justify-content-around">
                            <a class="section-link ml-2 active" href="#about_pooja">{{ translate('about_pooja') }}</a>
                            <a class="section-link" href="#benefits">{{ translate('benefits') }}</a>
                            <a class="section-link" href="#process">{{ translate('process') }}</a>
                            <a class="section-link" href="#temple_details"> {{ translate('temple_details') }}</a>
                            <a class="section-link" href="#packagesTabLink">{{ translate('packages') }}</a>
                            <a class="section-link" href="#reviews">{{ translate('reviews') }}</a>
                            <a class="section-link" href="#faqs"> {{ translate('faqs') }}</a>
                        </div>
                    </div>
                    <div class="px-4 pb-3 mb-3 __rounded-10 pt-3">
                        <div class="content-sections px-lg-3">
                            <!-- Inclusion Section -->
                            <div class="section-content active" id="about_pooja">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <!-- About Me -->
                                    <div class="ck-rendered-content">
                                        @include('web-views.aushthan.partials.aboutMe')
                                    </div>
                                </div>
                            </div>
                            <div class="section-content" id="benefits">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <!-- Benefits -->
                                    <div class="ck-rendered-content">
                                        @include('web-views.aushthan.partials.benifites')
                                    </div>
                                </div>
                            </div>
                            <div class="section-content" id="process">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <!-- Process -->
                                    <div class="ck-rendered-content">
                                        @include('web-views.aushthan.partials.process')
                                    </div>
                                </div>
                            </div>
                            <div class="section-content" id="temple_details">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <!-- Temple Details -->
                                    <div class="ck-rendered-content">
                                        @include('web-views.aushthan.partials.temple')
                                    </div>
                                </div>
                            </div>
                            <div class="section-content" id="packagesTabLink" href="#packages">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <div class="portfolio d-none d-sm-block">
                                        @php
                                            $selected_packages_data = [];
                                            $packageIds = json_decode($anushthan['packages_id']);
                                        @endphp
                                        @if (count($packageIds) > 0)

                                            <div class="portfolio-wrapper">

                                                <div class="row">
                                                    @foreach ($packageIds as $key => $pac)
                                                        @include('web-views.aushthan.partials.package', [
                                                            'package' => $pac,
                                                        ])
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center p-4">
                                                <img class="mb-3 w-160"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                                    alt="">
                                                <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- View Table --}}
                                    <div class="portfolio d-lg-none">
                                        <div class="mb-3 w-full tablinst mx-auto">
                                            <div class="grid grid-cols-3 w-full mb-2 gap-4"
                                                style="scroll-snap-type: x mandatory;">
                                                @foreach ($packageIds as $key => $pac)
                                                    @php
                                                        $package = \App\Models\Package::where(
                                                            'id',
                                                            $pac->package_id,
                                                        )->first();
                                                        $isFirst = $loop->first;
                                                        $radioName = 'package_selection';
                                                        $isChecked = $isFirst ? 'checked' : '';
                                                    @endphp

                                                    <input type="radio" name="{{ $radioName }}"
                                                        id="tab-{{ $package->id }}" class="d-none card-radio"
                                                        {{ $isChecked }}>

                                                    <label for="tab-{{ $package->id }}"
                                                        class="tabCard text-center shadow-sm position-relative m-1"
                                                        style="flex: 0 0 auto; width: 100%; max-width: 100px; scroll-snap-align: start; cursor: pointer; border:;"
                                                        data-title="{{ $package->title }}"
                                                        data-price="{{ $pac->package_price }}"
                                                        data-person="{{ $package->person }}"
                                                        data-desc="{{ $package->description }}"
                                                        data-color="{{ $package->color ?? '#f5f5f5' }}"
                                                        data-img="{{ $package->image ?? asset('default-image.png') }}"
                                                        data-package-id="{{ $package->id }}"
                                                        data-route="{{ route('anushthan.lead.store') }}"
                                                        data-auth-check="{{ auth('customer')->check() ? 'true' : 'false' }}"
                                                        data-customer-phone="{{ auth('customer')->check() ? auth('customer')->user()->phone : '' }}"
                                                        data-customer-name="{{ auth('customer')->check() ? auth('customer')->user()->f_name . ' ' . auth('customer')->user()->l_name : '' }}">

                                                        {{-- Meta Badge --}}
                                                        <div class="verified-badge">
                                                            <i class="fas fa-check"></i>
                                                        </div>

                                                        {{-- Package Image --}}
                                                        <img src="{{ getValidImage(path: 'storage/app/logo/' . $package->image, type: 'product') ?? asset('default-image.png') }}"
                                                            alt="Package Image" style="height: 100px; object-fit: cover;">

                                                        {{-- Price Bar --}}
                                                        <div class="fw-bold text-white py-1"
                                                            style="background: linear-gradient(to bottom, {{ $package->color }}, #fe9802); border-radius: 0 0 5px 5px;">
                                                            ₹{{ $pac->package_price }}
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12">
                                            <div id="packageContent" class="package-content-container">
                                                <!-- Content will be dynamically inserted here by JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="section-content" id="reviews">
                                <div class="row mt-2 p-2 partial-pooja pb-4">
                                    <!-- Review -->
                                    <div class="col-12">
                                        @foreach ($anushthanGet as $anushthanreview)
                                           @include('web-views.aushthan.partials.review', [
                                                'anushthanGet' => $anushthanreview,
                                            ])
                                         @endforeach
                                    </div>
                                </div>
                            </div>
                           
                            <div class="section-content" id="faqs">
                                <div class="row mt-2 p-2 partial-pooja">
                                    @foreach ($Faqs as $faq)
                                        <div class="col-12">
                                            @include('web-views.epooja.partials.faq', [
                                                'faq' => $faq,
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- yOUTUBE vIDEO cODE lIVE --}}
                    <div class="px-4 pb-3 mb-3 __rounded-10 pt-3">
                        @include('web-views.aushthan.partials._pujavideo')
                    </div>
            </div>
        </div>
    </div>
    <div class="button-sticky bg-white d-sm-none">
        <div class="d-flex flex-column gap-1 py-2">
            <div class="d-flex gap-3 justify-content-center" role="button">
                <a href="#packages" id="AnushthanPackageButton1"
                    class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"
                    data-toggle="tab" role="tab">
                    <span class="font-bold">{{ translate('select_puja_package') }}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="modal fade rtl text-align-direction" id="AnushthanModel" tabindex="-1" role="dialog"
        aria-labelledby="AnushthanModel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="modal-header">
                    <div class="flex justify-center items-center my-3">
                        <span class="text-18 font-bold mr-2">
                            {{ translate('Fill_your_details_for_Anushthan') }}</span>
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
                            class="block text-[16px] font-bold text-gray-900 dark:text-white">{{ translate('enter_Your_Whatsapp_Mobile_Number') }}</span>
                        <span
                            class="text-[12px] font-normal text-[#707070]">{{ translate('Your_pooja_booking_updates_will_be_sent_on_below_WhatsApp_number') }}</span>
                        <div class="w-full mr-9 px-0 pt-2">
                            <form class="needs-validation_" id="AnushthanForm"
                                action="{{ route('anushthan.lead.store') }}" method="post">
                                @csrf
                                @php
                                    if (auth('customer')->check()) {
                                        $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                    }

                                @endphp
                                <input type="hidden" name="service_id" value="{{ $anushthan->id }}">
                                <input type="hidden" name="product_id" value="{{ $anushthan->product_id }}">
                                <input type="hidden" name="package_id" id="packagesId">
                                <input type="hidden" name="package_name" id="packagesName">
                                <input type="hidden" name="package_price" id="packagesPrice">
                                <input type="hidden" name="noperson" id="packagesPerson">
                                <input type="hidden" name="verify_otp" id="verifyOTP" value="0">
                                <div class="row">
                                    <div class="col-md-12" id="phone-div">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                <small class="text-primary">(
                                                    *{{ translate('country_code_is_must_like_for_IND') }} 91
                                                    )</small>
                                            </label>
                                            <input
                                                class="form-control text-align-direction phone-input-with-country-picker"
                                                type="tel"
                                                value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                                name="person_phone" id="person-number"
                                                placeholder="{{ translate('enter_phone_number') }}" inputmode="number"
                                                required maxlength="10" minlength="10"
                                                {{ isset($customer['phone']) ? 'readonly' : '' }} input-mode="number">
                                            <input type="hidden" class="country-picker-phone-number w-50"
                                                name="person_phone" id="PhoneNumber" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-12" id="name-div">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('your_name') }}</label>
                                            <input class="form-control text-align-direction"
                                                value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? $customer['l_name'] : '' }}"
                                                type="text" name="person_name" id="person-name"
                                                placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                                inputmode="name" required
                                                {{ isset($customer['f_name']) ? 'readonly' : '' }} input-mode="text">

                                        </div>
                                    </div>
                                    <div class="col-md-12" id="slot-div">
                                        <div class="form-group">
                                            <label
                                                class="form-label font-semibold">{{ translate('Booking_Date') }}</label>
                                            <div id="booking-date-container"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-12" id="otp-input-div" style="display: none;">
                                        <div class="form-group text-center">
                                            <div id="masked-number">An OTP has been sent to ********<span
                                                    id="number"></span></div>
                                            {{-- <label class="form-label font-semibold ">{{ translate('enter_OTP') }}</label> --}}
                                            <div class="otp-input-fields">
                                                <input type="number" id="otp1" class="otp__digit otp__field__1"
                                                    inputmode="number">
                                                <input type="number" id="otp2" class="otp__digit otp__field__2"
                                                    inputmode="number">
                                                <input type="number" id="otp3" class="otp__digit otp__field__3"
                                                    inputmode="number">
                                                <input type="number" id="otp4" class="otp__digit otp__field__4"
                                                    inputmode="number">
                                                <input type="number" id="otp5" class="otp__digit otp__field__5"
                                                    inputmode="number">
                                                <input type="number" id="otp6" class="otp__digit otp__field__6"
                                                    inputmode="number">
                                            </div>
                                            <p id="otpValidation" class="text-danger"></p>

                                        </div>

                                    </div>
                                    {{-- with mobile number register start --}}
                                    {{-- <div class="mx-auto mt-1" id="withoutOTPButton" style="display: none;">
                                        <button type="submit"
                                            class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold">
                                            {{ translate('Book Puja') }}</button>
                                    </div> --}}
                                    {{-- with mobile number register end --}}
                                    <div class="mx-auto mt-1" id="send-otp-btn-div">
                                        <button type="button" class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                            id="send-otp-btn"> {{ translate('book_puja') }} </button>
                                        <button type="button" class="d-none btn btn--primary btn-block btn-shadow mt-1 font-weight-bold" id="withoutOTP"> {{ translate('book_puja') }}  </button>
                                        <p class="text-center mt-2" style="font-size: 10px;">
                                            {{ translate('By_tapping_the_"Book Pooja"_button_below_,_you_will_send_an_OTP_to_ your_mobile_number') }}
                                        </p>
                                    </div>

                                    <div class="mx-auto mt-1 " id="verify-otp-btn-div" style="display: none">
                                        <div class="d-flex">
                                            <button type="button"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold me-2"
                                                id="otp-back-btn">
                                                {{ translate('back') }} </button>
                                            <button type="submit"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                id="verify-otp-btn" disabled>
                                                {{ translate('verify_OTP') }} </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3" id="resend-div" style="display: none;">
                                    <p id="resend-otp-timer-text" style="display: none"> Resend OTP in <span
                                            id="resend-otp-timer"></span></p>
                                    <p id="resend-otp-btn-text" style="display: none">Didn't get the code?
                                        <a href="javascript:0" id="resend-otp-btn" style="color: blue;">Resend
                                            Otp</a>
                                    </p>
                                    <br>


                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Already with login this pooja --}}
    <div class="modal fade rtl text-align-direction" id="WithAnushthanLoginModel" tabindex="-1" role="dialog"
        aria-labelledby="WithAnushthanLoginModel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="flex justify-center items-center my-3">
                        <span class="text-18 font-bold mr-2">Selecte Booking Date</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr class="bg-[#E6E4EB] w-full">
                <div class="modal-body flex justify-content-center">
                    <div class="w-full mt-1 px-2">
                        <span
                            class="text-[12px] font-normal text-[#707070]">{{ translate('Please_choose_your_preferred_booking_date_._Once_selected_,_review_all_details_carefully_before_confirming_ your_appointment') }}</span>
                        <div class="w-full mr-9 px-0">
                            <form class="needs-validation_" id="alreadyLogin"
                                action="{{ route('anushthan.lead.store') }}" method="post">
                                @csrf
                                @php
                                    if (auth('customer')->check()) {
                                        $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                    }
                                @endphp
                                <input type="hidden" name="service_id" value="{{ $anushthan->id }}">
                                <input type="hidden" name="product_id" value="{{ $anushthan->product_id }}">
                                <input type="hidden" name="package_id" id="packagesIds">
                                <input type="hidden" name="package_name" id="packagesNames">
                                <input type="hidden" name="package_price" id="packagesPrices">
                                <input type="hidden" name="noperson" id="packagesPersons">
                                <input class="form-control text-align-direction phone-input-with-country-picker"
                                    type="hidden" value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                    name="person_phone" id="person-number"
                                    placeholder="{{ translate('enter_phone_number') }}" inputmode="number" required
                                    maxlength="10" minlength="10" {{ isset($customer['phone']) ? 'readonly' : '' }}
                                    input-mode="number">
                                <input type="hidden" class="country-picker-phone-number w-50" name="person_phone"
                                    id="PhoneNumber" readonly>
                                <input class="form-control text-align-direction"
                                    value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? $customer['l_name'] : '' }}"
                                    type="hidden" name="person_name" id="person-name"
                                    placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                    inputmode="name" required {{ isset($customer['f_name']) ? 'readonly' : '' }}
                                    input-mode="text">
                                <div class="row">
                                    <div class="col-md-12" id="slot-div">
                                        <div class="form-group">
                                            <label
                                                class="form-label font-semibold">{{ translate('Booking Date') }}</label>
                                            <div id="booking-date-already"></div>
                                        </div>
                                    </div>
                                    <div class="mx-auto mt-1">
                                        <button type="submit"
                                            class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                            name="alreadyLogin">
                                            {{ translate('book_puja') }}
                                        </button>
                                        <p class="text-center mt-2" style="font-size: 10px;">
                                            {{ translate('By_tapping_the_"Book Pooja"_button_below_,_you_will_send_an_OTP_to_ your_mobile_number') }}
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>
    <!-- <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script> -->
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <!-- Firbase OTP -->
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

    <!-- Firbase OTP -->

    <script>
        function participateModel(that) {
            var id = $(that).data('id');
            var name = $(that).data('name');
            var price = $(that).data('price');
            var person = $(that).data('person');
            $('#packagesId').val(id);
            $('#packagesName').val(name);
            $('#packagesPrice').val(price);
            $('#packagesPerson').val(person);
            const $bookingDateContainer = $('#booking-date-container');
            let packageIdValue = $(that).data('id');
            console.log(packageIdValue);
            $bookingDateContainer.empty();
            if (packageIdValue == '7') {

                $bookingDateContainer.append(`
                <input class="form-control hasDatepicker text-align-direction" type="text" name="booking_date" id="bookingdate" placeholder="Booking Slot Date" input-mode="text" autocomplete="off" required>
            `);
            } else {
                $bookingDateContainer.append(`
                <input class="form-control text-align-direction" type="text" name="booking_date" id="booking-date" value="{{ date('Y-m-d', strtotime('+1 hour')) }}" required readonly>
            `);
            }
            datePicker();
            $('#AnushthanModel').modal('show');
        }
    </script>
    {{-- Date time picker Selcted --}}
    <script>
        function anushthanLoginModel(that) {
            var id = $(that).data('id');
            var name = $(that).data('name');
            var price = $(that).data('price');
            var person = $(that).data('person');
            $('#packagesIds').val(id);
            $('#packagesNames').val(name);
            $('#packagesPrices').val(price);
            $('#packagesPersons').val(person);
            const $bookingDateAlready = $('#booking-date-already');
            let packageIdValue = $(that).data('id');
            console.log(packageIdValue);
            $bookingDateAlready.empty();
            if (packageIdValue == '7') {

                $bookingDateAlready.append(`
                        <input class="form-control hasDatepicker text-align-direction" type="text" name="booking_date" id="bookingdate" placeholder="Booking Slot Date" input-mode="text" autocomplete="off" required>
                    `);
            } else {
                $bookingDateAlready.append(`
                        <input class="form-control text-align-direction" type="text" name="booking_date" id="booking-date" value="{{ date('Y-m-d', strtotime('+1 hour')) }}" required readonly>
                    `);
            }
            datePicker();
            $('#WithAnushthanLoginModel').modal('show');
        }
    </script>
    <script>
        function datePicker(that) {
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(today.getDate());
            $('#bookingdate').datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy/mm/dd',
                modal: true,
                footer: true,
                minDate: tomorrow,
                todayHighlight: true
            });
        }
    </script>
    {{-- Date time picker Selcted --}}
    <script>
        // OTP SEND THE MODEL
        var confirmationResult;
        var appVerifier = "";
        $('#send-otp-btn').click(function(e) {
            e.preventDefault();
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            var phoneNumber = '+91 ' + $('#person-number').val();
            sendotp();
        });


        function sendotp() {
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            var bookingDate = $('#bookingdate').val();
            if (number == "" || number.length != 10) {
                toastr.error('Please fill the number feild');
            } else if (name == "") {
                toastr.error('Please fill the name feild');
            } else if (bookingDate == "") {
                toastr.error('Please Select the Slot Date.');
            } else {
                $('#send-otp-btn').text('Please Wait ...');
                $('#send-otp-btn').prop('disabled', true);
                toastr.success('Please Wait....');
                var phoneNumber = '+91 ' + $('#person-number').val();
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
                        $('#slot-div').css('display', 'none');
                        $('#otp-input-div').css('display', 'block');
                        $('#verify-otp-btn-div').css('display', 'block');
                        otpTimer();
                        confirmationResult = confirmation;
                        toastr.success('otp sent successfully');
                        $('#send-otp-btn').prop('disabled', true);
                        $('#resend-div').show();
                    })
                    .catch(function(error) {
                        console.error('OTP sending error:', error);
                        toastr.error('Failed to send OTP. Please try again.');
                        $('#send-otp-btn').text('Book Pooja');
                        $('#send-otp-btn').prop('disabled', false);
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
            var phoneNumber = '+91 ' + $('#person-number').val();
            if (!appVerifier) {
                appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    size: 'invisible'
                });
            }
            firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                    confirmationResult = confirmation;
                    otpTimer();
                    toastr.success('otp resent successfully');
                })
                .catch(function(error) {
                    toastr.success('Failed to send OTP. Please try again.');
                });
        });

        $('#verify-otp-btn').click(function(e) {
            e.preventDefault();
            toastr.success('Please Wait....');
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
                $('#otp6').val();
            if (confirmationResult) {

                confirmationResult.confirm(otp).then(function(result) {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        $('#AnushthanModel').modal('hide');
                        $('#AnushthanForm').submit();
                    })
                    .catch(function(error) {
                        $('#otpValidation').text('Incorrect Otp');
                    });
            }
        });

        $('#otp-back-btn').click(function(e) {
            e.preventDefault();
            $('#send-otp-btn-div').css('display', 'block');
            $('#phone-div').css('display', 'block');
            $('#name-div').css('display', 'block');
            $('#slot-div').css('display', 'block');
            $('#otp-input-div').css('display', 'none');
            $('#verify-otp-btn-div').css('display', 'none');
            $('#send-otp-btn').prop('disabled', false);
            $('#send-otp-btn').text('Book Pooja');
            $('#resend-div').hide();
        });
    </script>
    <script>
        $('#AnushthanPackageButton').on('click', function(event) {
            event.preventDefault();
            if (!$('#packagesTabLink').hasClass('active')) {
                $('.nav-link').removeClass('active');
                $('#packagesTabLink').addClass('active');
                $('#packagesTabLink').tab('show');
            }
            $('html, body').animate({
                scrollTop: $('#packagesTabLink').offset().top
            }, 50);
        });
        $('#AnushthanPackageButton1').on('click', function(event) {
            event.preventDefault();
            if (!$('#packagesTabLink').hasClass('active')) {
                $('.nav-link').removeClass('active');
                $('#packagesTabLink').addClass('active');
                $('#packagesTabLink').tab('show');
            }
            $('html, body').animate({
                scrollTop: $('#packagesTabLink').offset().top
            }, 50);
        });
    </script>
    <!-- OTP SECTION -->
    <script type="text/javascript">
        var otp_inputs = document.querySelectorAll(".otp__digit");
        const verifyOtpBtn = document.getElementById('verify-otp-btn');

        function checkOtpFields() {
            let allFilled = true;
            otp_inputs.forEach(field => {
                if (field.value === '') {
                    allFilled = false;
                }
            });
            verifyOtpBtn.disabled = !allFilled;
        }
        otp_inputs.forEach(field => {
            field.addEventListener('input', checkOtpFields);
        });
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
    <script>
        $(document).ready(function() {
            $('#show-full-address').on('click', function(e) {
                e.preventDefault();
                $('#full-address').slideDown();
                $(this).hide();
            });
        });
        // Without OTP LOGIN
        $('#withoutOTP').click(function(e) {
            e.preventDefault();
            $('#AnushthanForm').submit();
        });
        // Without OTP LOGIN
    </script>
    {{-- mobile no blur --}}
    <script>
        $('#person-number').blur(function(e) {
            e.preventDefault();
            var code = $('.iti__selected-dial-code').text();
            var mobile = $(this).val();
            var no = code + '' + mobile;
            $.ajax({
                type: "get",
                url: "{{ url('account-service-order-user-name') }}" + "/" + no,
                success: function(response) {
                    if (response.status == 200) {
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);
                        $('#verifyOTP').val(1);
                    } else {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        $('#send-otp-btn').addClass('d-none');
                        $('#withoutOTP').removeClass('d-none');
                    }
                }
            });
        });
    </script>
    <script>
        function read(el) {
            var parentDiv = $(el).closest('.single-review-details');
            var commentDiv = parentDiv.find('.review-comment');
            if (parentDiv.css('height') === '100px') {
                parentDiv.css('height', 'auto'); // Expand
                commentDiv.css('-webkit-line-clamp', '10');
                $(el).text('Read less...');
            } else {
                parentDiv.css('height', '100px'); // Collapse
                commentDiv.css('-webkit-line-clamp', '3');
                $(el).text('Read more...');
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            function getNavbarOffset() {
                return $('.navbar_section1').outerHeight() || 0;
            }

            $('.section-link').on('click', function(e) {
                e.preventDefault();

                const targetId = $(this).attr('href');
                $('html, body').animate({
                    scrollTop: $(targetId).offset().top - getNavbarOffset() - 20
                }, 200);
            });

            $(window).on('scroll resize', function() {
                const scrollTop = $(window).scrollTop() + getNavbarOffset() + 100;

                $(".navbar_section1").css({
                    'position': 'sticky',
                    'top': window.innerWidth <= 768 ? '0' : '8.7rem',
                    'left': '0',
                    'right': '0',
                    'background-color': '#fff',
                    'z-index': '1000',
                    'box-shadow': scrollTop > 900 ? '0 2px 10px rgba(0, 0, 0, 0.1)' : 'none',
                    'overflow': 'auto',
                });

                $('.section-content').each(function() {
                    const sectionTop = $(this).offset().top;
                    const sectionBottom = sectionTop + $(this).outerHeight();
                    const sectionId = $(this).attr('id');
                    const navLink = $(`.section-link[href="#${sectionId}"]`);

                    if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                        $('.section-link').removeClass('active');
                        navLink.addClass('active');
                        if (sectionId == 'packagesTabLink') {
                            $('.string-limit').addClass('d-none');
                        } else {
                            $('.string-limit').removeClass('d-none');
                        }
                    }
                });
            });

            $(window).trigger('scroll');
        });
    </script>
    <script>
        // Define global variables
        const authCheck = "{{ auth('customer')->check() ? 'true' : 'false' }}";
        const customerPhone = "{{ auth('customer')->check() ? auth('customer')->user()->phone : '' }}";
        const customerName =
            "{{ auth('customer')->check() ? auth('customer')->user()->f_name . ' ' . auth('customer')->user()->l_name : '' }}";
        const serviceId = "{{ $anushthan->id }}";
        const productId = "{{ $anushthan->product_id }}";
        const route = "{{ route('anushthan.lead.store') }}";
        $(document).ready(function() {
            const firstTab = $('.tabCard').first();
            firstTab.addClass('active');
            firstTab.css('border', `1px solid ${firstTab.data('color')}`); // Set initial border
            updatePackageContent(firstTab);

            // Handle tab clicks
            $('.tabCard').on('click', function() {
                $('.tabCard').removeClass('active').css('border', '1px solid transparent');
                $(this).addClass('active').css('border',
                `1px solid ${$(this).data('color')}`); // Use dynamic color
                updatePackageContent($(this));
            });

            function updatePackageContent(el) {
                const packageData = {
                    title: el.data('title'),
                    price: el.data('price'),
                    person: el.data('person'),
                    desc: el.data('desc'),
                    color: el.data('color'),
                    img: el.data('img'),
                    packageId: el.data('package-id')
                };
                $('#packageContent').css('background', `linear-gradient(to bottom, ${packageData.color}, #ffffff)`);
                let participationSection;

                if (authCheck == "true") {
                    // User is logged in - show direct form
                    participationSection = `
                        <a href="javascript:void(0);"
                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold {{ $currentTime->between($startTime, $endTime) ? '' : 'disabled' }}"
                        {{ !$currentTime->between($startTime, $endTime) ? 'aria-disabled="true"' : '' }}
                         data-id="${packageData.packageId}" 
                        data-name="${packageData.title}"
                        data-price="${packageData.price}" 
                        data-person="${packageData.person}"
                        onclick="anushthanLoginModel(this)">Book Now</a>
                    `;
                } else {
                    // User is not logged in - show modal trigger
                    participationSection = `
                       <a href="javascript:void(0);"
                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold {{ $currentTime->between($startTime, $endTime) ? '' : 'disabled' }}"
                        {{ !$currentTime->between($startTime, $endTime) ? 'aria-disabled="true"' : '' }}
                        data-id="${packageData.packageId}" 
                        data-name="${packageData.title}"
                        data-price="${packageData.price}" 
                        data-person="${packageData.person}"
                        onclick="participateModel(this)">Book Now</a>
                    `;
                }
                // Create HTML content
                const contentHTML = `
            <div class="">
                <div class="mb-lg-0 rounded-lg shadow" style="background: linear-gradient(to bottom, ${packageData.color}, #ffffff);">
                    <div class="text-center pt-3">
                        <h5 class="card-title text-uppercase text-center font-bold" style="line-height: 1.5em;">
                         ${packageData.title}</h5>
                    <h4 class="font-weight-bold-800">Rs. ${packageData.price}</h4>
                        <span class="h6 text-center" style="line-height: 1.5em;min-height: 3em;">
                            ${packageData.person > 1 ? 'Pooja for ' + packageData.person + ' People' : 'Pooja for ' + packageData.person + ' Person'}
                        </span>
                    </div>
                    <hr class="text-GRAY-20 mx-3">
                    <div class="rounded-bottom p-2">
                        <div class="mb-5" style="margin-bottom:1rem!important;height: 280px;overflow: auto">
                            <div class="flex flex-col package-Information">
                                <div class="fs-sm" style="display: flex; flex-direction: column">
                                    <span style="flex-direction: row; align-items: start; width: 100%;padding:16px" class="">
                                        ${packageData.desc}
                                    </span>
                                </div>
                            </div>
                        </div>
                        ${participationSection}
                    </div>
                </div>
            </div>
        `;

                $('#packageContent').html(contentHTML);
            }
        });
    </script>
@endpush
