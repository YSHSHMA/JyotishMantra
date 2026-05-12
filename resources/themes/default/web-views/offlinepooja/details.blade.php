@extends('layouts.front-end.app')
@section('title', $details['meta_title'])
@push('css_or_js')
    <meta name="description" content="{{ $details->meta_description }}">
    <meta name="keywords" content="@foreach (explode(' ', $details['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
    @if ($details['meta_image'] != null)
        <meta property="og:image"
            content="{{ dynamicStorage('storage/app/public/offlinepooja/meta') }}/{{ $details->meta_image }}" />
        <meta property="twitter:card"
            content="{{ dynamicStorage('storage/app/public/offlinepooja/meta') }}/{{ $details->meta_image }}" />
    @else
        <meta property="og:image"
            content="{{ dynamicStorage('storage/app/public/offlinepooja/thumbnail') }}/{{ $details->thumbnail }}" />
        <meta property="twitter:card"
            content="{{ dynamicStorage('storage/app/public/offlinepooja/thumbnail') }}/{{ $details->thumbnail }}" />
    @endif

    @if ($details['meta_title'] != null)
        <meta property="og:title" content="{{ $details->meta_title }}" />
        <meta property="twitter:title" content="{{ $details->meta_title }}" />
    @else
        <meta property="og:title" content="{{ $details->name }}" />
        <meta property="twitter:title" content="{{ $details->name }}" />
    @endif
    <meta property="og:url" content="{{ route('product', [$details->slug]) }}">
    @if ($details['meta_description'] != null)
        <meta property="twitter:description" content="{!! Str::limit($details['meta_description'], 55) !!}">
        <meta property="og:description" content="{!! Str::limit($details['meta_description'], 55) !!}">
    @else
        <meta property="og:description"
            content="@foreach (explode(' ', $details['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
        <meta property="twitter:description"
            content="@foreach (explode(' ', $details['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
    @endif
    <meta property="twitter:url" content="{{ route('product', [$details->slug]) }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/poojadetails.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/product-details.css') }}" />
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <style>
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

        a.section-link.active {
            color: #ffffff !important;
            background: var(--base) !important;
            font-weight: bold;

        }

        a.section-link {
            border-radius: 100px !important;
            padding: 9px 17px;
            font-size: 13px;
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            a.section-link {
                padding: 5px 8px;
                font-size: 8px;
            }

            .btnClr {
                font-size: small;
            }
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



        @media (max-width: 576px) {
            .package-title {
                font-size: 1.1rem;
            }

            .package-price {
                font-size: 1rem;
            }

            .package-person {
                font-size: 0.9rem;
            }
        }

        /* table responsive */
        .package-content-container {
            position: relative;
            height: 500px;
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
            z-index: 1000;
            transition: all 150ms ease-in-out;
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
    <div class="w-full h-full sticky md:top-[68px] top-0 z-20 d-none d-md-block">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.offlinepooja.partials.statusbar')
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
                                        @if ($details->images != null && json_decode($details->images) > 0)
                                            @foreach (json_decode($details->images) as $key => $photo)
                                                <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                                    id="image{{ $key }}">
                                                    <img class="cz-image-zoom img-responsive w-100"
                                                        src="{{ getValidImage(path: 'storage/app/public/offlinepooja/' . $photo, type: 'product') }}"
                                                        data-zoom="{{ getValidImage(path: 'storage/app/public/offlinepooja/' . $photo, type: 'product') }}"
                                                        alt="{{ translate('product') }}" width="">
                                                </div>
                                            @endforeach

                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <button type="button" data-product-id="{{ $details['id'] }}"
                                        class="btn __text-18px border wishList-pos-btn d-sm-none product-action-add-wishlist">
                                        <i class="fa fa-heart wishlist_icon_{{ $details['id'] }} web-text-primary"
                                            aria-hidden="true"></i>
                                    </button>
                                    <div class="sharethis-inline-share-buttons share--icons text-align-direction">
                                    </div>
                                </div>
                                <div class="cz">
                                    <div class="table-responsive __max-h-515px" data-simplebar>
                                        <div class="d-flex">
                                            <div id="sync2" class="owl-carousel owl-theme product-thumb-slider">
                                                @if ($details->images != null && json_decode($details->images) > 0)
                                                    @foreach (json_decode($details->images) as $key => $photo)
                                                        <div class="">
                                                            <a class="product-preview-thumb {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                                                                id="preview-img{{ $key }}"
                                                                href="#image{{ $key }}">
                                                                <img alt="{{ translate('product') }}"
                                                                    src="{{ getValidImage(path: 'storage/app/public/offlinepooja/' . $photo, type: 'product') }}">
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
                                <div class="w-bar h-bar bg-gradient mt-2"></div>
                                <h4 class="card-title font-weight-700 font-bold">{{ $details->name }}</h4>
                                <span class="text-16 mt-2 pb-2">{{ $details->short_benifits }}</span>


                                <!-- Profile Icon -->
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap justify-center items-center">
                                        <div class="w-full">
                                            <div class="w-full tray mb-3">
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image: url('{{ asset('public/assets/front-end/img/random-user/one.png') }}')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image: url('{{ asset('public/assets/front-end/img/random-user/two.png') }}')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image: url('{{ asset('public/assets/front-end/img/random-user/three.png') }}')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image: url('{{ asset('public/assets/front-end/img/random-user/four.jpg') }}')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image: url('{{ asset('public/assets/front-end/img/random-user/five.png') }}')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image: url('{{ asset('public/assets/front-end/img/random-user/six.png') }}')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Count number of People -->
                                <div class="mt-[1px] mb-3 md:mb-0 flex flex-col">
                                    <div class="flex flex-row mt-2 flex-nowrap break-normal leading-normal">
                                        <div class="flex">
                                            <div class="">
                                                <span class=" inline-flex break-normal">
                                                    {{ translate('Join') }}</span><span
                                                    class=" font-bold text-#F18912 ml-1 break-normal">{{ 10000 + \App\Models\OfflinePoojaOrder::where('service_id', $details->id)->count() }}<span
                                                        class=" ml-1 mr-1 inline-flex break-normal">+</span></span><span
                                                    class="text-">{{ translate('devotees_who_have_already_booked_expert_pandits_for_their_pooja_through_Mahakal.com!') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Button -->
                                <div id="" role="button" class="d-none d-sm-block">
                                    <a href="#packages" id="OfflinePoojaPackageButton"
                                        class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"
                                        data-toggle="tab" role="tab">
                                        <span class="font-bold">{{ translate('Select_package') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="container-fluid" style="padding-left: 0 !important;padding-right:0 !important;">
                    <div class="row">
                        <div class="col-12">
                            <div class="navbar_section1 section-links w-100 mt-3 border-top border-bottom py-2 mb-4"
                                style="overflow: auto;">
                                <div class="d-flex justify-content-around">
                                    <a class="section-link ml-2 active"
                                        href="#about_pooja">{{ translate('about_puja') }}</a>

                                    <a class="section-link" href="#benefits">{{ translate('benefits') }}</a>
                                    <a class="section-link" href="#process">{{ translate('process') }}</a>
                                    <a class="section-link"
                                        href="#terms_conditions">{{ translate('terms_conditions') }}</a>
                                    <a class="section-link" href="#packagesTabLink"
                                        href="#packages">{{ translate('packages') }}</a>
                                    <a class="section-link" href="#policyTabLink">{{ translate('policy') }}</a>
                                    <a class="section-link" href="#reviews">{{ translate('reviews') }}</a>
                                    <a class="section-link" href="#faqs">{{ translate('faqs') }}</a>
                                </div>
                            </div>
                            {{-- <div class="card card-body px-4 pb-3 mb-3 __rounded-10 pt-3"> --}}
                            <div class="px-4 pb-3 mb-3 __rounded-10 pt-3">
                                <div class="content-sections px-lg-3">
                                    <!-- Inclusion Section -->
                                    <div class="section-content active" id="about_pooja">
                                        <div class="row mt-2 p-2 partial-pooja">
                                            <!-- About Me -->
                                            @include('web-views.offlinepooja.partials.aboutMe')
                                        </div>
                                    </div>
                                    <div class="section-content" id="benefits">
                                        <div class="row mt-2 p-2 partial-pooja">
                                            <!-- Benefits -->
                                            @include('web-views.offlinepooja.partials.benifites')
                                        </div>
                                    </div>

                                    <div class="section-content" id="process">
                                        <div class="row mt-2 p-2 partial-pooja">
                                            <!-- Process -->
                                            @include('web-views.offlinepooja.partials.process')
                                        </div>
                                    </div>
                                    <div class="section-content" id="terms_conditions">
                                        <div class="row mt-2 p-2 partial-pooja">
                                            <!-- Temple Details -->
                                            @include('web-views.offlinepooja.partials.temple')
                                        </div>
                                    </div>
                                    <div class="section-content" id="packagesTabLink" href="#packages">
                                        <div class="row mt-2 p-3"
                                            style="background: white;box-shadow: 0px 3px 6px rgb(0 0 0 / 29%); border-radius: 5px; border-top: 2px solid #fe9802;">
                                            <div class="portfolio  d-none d-sm-block">
                                                @php
                                                    $selected_packages_data = [];
                                                    $packageIds = json_decode($details['package_details']);
                                                @endphp
                                                @if (count($packageIds) > 0)

                                                    <div class="portfolio-wrapper">
                                                        <div class="row">
                                                            @foreach ($packageIds as $key => $pac)
                                                                @include(
                                                                    'web-views.offlinepooja.partials.package',
                                                                    [
                                                                        'package' => $pac,
                                                                    ]
                                                                )
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

                                            {{-- Table responsive --}}
                                            <div class="portfolio d-sm-none">
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
                                                                data-price="{{ $pac->price }}"
                                                                data-percent="{{ $pac->percent }}"
                                                                data-person="{{ $package->person }}"
                                                                data-desc="{{ $package->description }}"
                                                                data-color="{{ $package->color ?? '#f5f5f5' }}"
                                                                data-img="{{ $package->image ?? asset('default-image.png') }}"
                                                                data-package-id="{{ $package->id }}"
                                                                data-route="{{ route('poojastore', $details->slug) }}"
                                                                data-booking-date=""
                                                                data-auth-check="{{ auth('customer')->check() ? 'true' : 'false' }}"
                                                                data-customer-phone="{{ auth('customer')->check() ? auth('customer')->user()->phone : '' }}"
                                                                data-customer-name="{{ auth('customer')->check() ? auth('customer')->user()->f_name . ' ' . auth('customer')->user()->l_name : '' }}">

                                                                <div class="verified-badge">
                                                                    <i class="fas fa-check"></i>
                                                                </div>

                                                                <img src="{{ getValidImage(path: 'storage/app/logo/' . $package->image, type: 'product') ?? asset('default-image.png') }}"
                                                                    alt="Package Image"
                                                                    style="height: 100px; object-fit: cover;">

                                                                <div class="fw-bold text-white py-1"
                                                                    style="background: linear-gradient(to bottom, {{ $package->color }}, #fe9802); border-radius: 0 0 5px 5px;">
                                                                    ₹{{ $pac->price }}
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 col-md-12">
                                                    <div id="packageContent" class="package-content-container">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section-content reviewsTab" id="policyTabLink">
                                        <div class="row mt-2 p-2 partial-pooja">
                                            <!-- Review -->
                                            <div class="col-12">
                                                @include('web-views.offlinepooja.partials.policy')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section-content reviewsTab" id="reviews">
                                        <div class="row mt-2 p-2 partial-pooja">
                                            <!-- Review -->
                                            <div class="col-12">
                                                @include('web-views.offlinepooja.partials.review')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section-content" id="faqs">
                                        <div class="row mt-2 p-2 partial-pooja">
                                            @foreach ($Faqs as $faq)
                                                <div class="col-12">

                                                    @include('web-views.offlinepooja.partials.faq', [
                                                        'faq' => $faq,
                                                    ])
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($details->video_url))
                <div class="row">
                    <div class="col-12 rtl text-align-direction">
                        @include('web-views.offlinepooja.partials._pujavideo')
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="button-sticky bg-white d-sm-none">
        <div class="d-flex flex-column gap-1 py-2">
            <div class="d-flex gap-3 justify-content-center" role="button">
                <a href="#packages" id="pujaPackageButton1"
                    class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"
                    data-toggle="tab" role="tab">
                    <span class="font-bold">{{ translate('select_puja_package') }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="modal fade rtl text-align-direction" id="OfflinePoojaModel" tabindex="-1" role="dialog"
        aria-labelledby="OfflinePoojaModel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="flex justify-center items-center my-3">
                        <span class="text-18 font-bold mr-2">
                            {{ translate('Fill_your_details_for_Puja') }}</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr class="bg-[#E6E4EB] w-full">
                <div class="modal-body flex justify-content-center">
                    <div id="recaptcha-container"></div>
                    <div class="w-full mt-1 px-2">
                        <div class="w-full mr-9 px-0 pt-2">
                            <form class="needs-validation_" id="OfflinePoojaForm"
                                action="{{ route('offline.pooja.lead.store') }}" method="post">
                                @csrf
                                @php
                                    if (auth('customer')->check()) {
                                        $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                    }

                                @endphp
                                <input type="hidden" name="service_id" value="{{ $details->id }}">
                                {{-- <input type="hidden" name="product_id" value="{{ $details->product_id }}"> --}}
                                <input type="hidden" name="package_id" id="packagesId">
                                <input type="hidden" name="package_name" id="packagesName">
                                <input type="hidden" name="package_main_price" id="packagesMainPrice">
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
                                                placeholder="{{ translate('phone_number') }}" inputmode="number" required
                                                maxlength="10" minlength="10"
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
                                                type="text" name="person_name"
                                                id="person-name"placeholder="{{ translate('Ex') }}: {{ translate('your_name') }}!"
                                                inputmode="name" required
                                                {{ isset($customer['f_name']) ? 'readonly' : '' }} input-mode="text">

                                        </div>
                                    </div>

                                    <div class="col-md-12" id="otp-input-div" style="display: none;">
                                        <div class="form-group text-center">
                                            <div id="masked-number">{{ translate('An_OTP_has_been_sent_to') }}
                                                ********<span id="number"></span></div>
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



                                    {{-- with otp login button --}}
                                    <div class="mx-auto mt-1 __max-w-356" id="send-otp-btn-div">
                                        <button type="button"
                                            class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                            id="send-otp-btn"> {{ translate('book_puja') }} </button>
                                        <button type="button"
                                            class="d-none btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                            id="withoutOTP"> {{ translate('book_puja') }}
                                        </button>
                                    </div>

                                    <div class="mx-auto mt-1 __max-w-356" id="verify-otp-btn-div" style="display: none">
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
                                    <p id="resend-otp-timer-text" style="display: none">{{ translate('Resend_OTP_in') }}
                                        <span id="resend-otp-timer"></span>
                                    </p>
                                    <p id="resend-otp-btn-text" style="display: none">
                                        {{ translate('Did_not_get_the_code') }}
                                        <a href="javascript:0" id="resend-otp-btn"
                                            style="color: blue;">{{ translate('Resend_OTP') }}</a>
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
    <div class="modal fade rtl text-align-direction" id="WithLoginModel" tabindex="-1" role="dialog"
        aria-labelledby="WithLoginModel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="flex justify-center items-center my-3">
                        <span class="text-18 font-bold mr-2">{{ translate('Booking_Date') }}</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr class="bg-[#E6E4EB] w-full">
                <div class="modal-body flex justify-content-center">
                    <div class="w-full mt-1 px-2">
                        <div class="w-full mr-9 px-0">
                            <form class="needs-validation_" id="alreadyLogin"
                                action="{{ route('offline.pooja.lead.store') }}" method="post">
                                @csrf
                                @php
                                    if (auth('customer')->check()) {
                                        $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                    }
                                @endphp
                                <input type="hidden" name="service_id" value="{{ $details->id }}">
                                <input type="hidden" name="product_id" value="{{ $details->product_id }}">
                                <input type="hidden" name="package_id" id="packagesIds">
                                <input type="hidden" name="package_name" id="packagesNames">
                                <input type="hidden" name="package_main_price" id="packagesMainPrices">
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
                                    type="hidden" name="person_name"
                                    id="person-name"placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                    inputmode="name" required {{ isset($customer['f_name']) ? 'readonly' : '' }}
                                    input-mode="text">
                                <div class="row">
                                    <div class="col-md-12" id="slot-div">
                                        <div class="form-group">
                                            <label
                                                class="form-label font-semibold">{{ translate('Booking_Date') }}</label>
                                            <div id="booking-date-already"></div>
                                        </div>
                                    </div>
                                    <div class="mx-auto mt-1 __max-w-356">
                                        <button type="submit"
                                            class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                            name="alreadyLogin"> {{ translate('book_puja') }} </button>
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
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
    {{-- <script src="https://www.gstatic.com/firebasejs/10.13.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.13.1/firebase-analytics.js"></script> --}}
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script>
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
            var percent = $(that).data('percent');
            var person = $(that).data('person');
            var finalPrice = price * (percent / 100);
            $('#packagesId').val(id);
            $('#packagesName').val(name);
            $('#packagesMainPrice').val(price);
            $('#packagesPrice').val(finalPrice);
            $('#packagesPerson').val(person);
            $('#OfflinePoojaModel').modal('show');
        }
    </script>
    {{-- Date time picker Selcted --}}
    <script>
        function alreadyLoginModel(that) {
            var id = $(that).data('id');
            var name = $(that).data('name');
            var price = $(that).data('price');
            var percent = $(that).data('percent');
            var person = $(that).data('person');
            var finalPrice = price * (percent / 100);
            $('#packagesIds').val(id);
            $('#packagesNames').val(name);
            $('#packagesMainPrices').val(price);
            $('#packagesPrices').val(finalPrice);
            $('#packagesPersons').val(person);
            $('#alreadyLogin').submit();
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
                $(this).text('Please Wait ...');
                $(this).prop('disabled', true);
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
                        $('#OfflinePoojaModel').modal('hide');
                        $('#OfflinePoojaForm').submit();
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
            $('#slot-div').css('display', 'none');
            $('#otp-input-div').css('display', 'none');
            $('#verify-otp-btn-div').css('display', 'none');
            $('#send-otp-btn').prop('disabled', false);
            $('#send-otp-btn').text('Book Pooja');
            $('#resend-div').hide();
        });
    </script>
    <script>
        $('#OfflinePoojaPackageButton').on('click', function(event) {
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
            $('#OfflinePoojaForm').submit();
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
            console.log(code);
            console.log(mobile);
            $.ajax({
                type: "get",
                url: "{{ url('account-service-order-user-name') }}" + "/" + no,
                success: function(response) {
                    if (response.status == 200) {
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);
                        $('#verifyOTP').val(1);
                    } else {
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
        function toggleComment(element) {
            const container = element.closest('.single-review-details');
            const shortComment = container.querySelector('.short-comment');
            const fullComment = container.querySelector('.full-comment');

            if (fullComment.classList.contains('d-none')) {
                shortComment.classList.add('d-none');
                fullComment.classList.remove('d-none');
                element.textContent = 'Read Less...';
            } else {
                shortComment.classList.remove('d-none');
                fullComment.classList.add('d-none');
                element.textContent = 'Read More...';
            }
        }
    </script>
    <script>
        // Define global variables
        const authCheck = "{{ auth('customer')->check() ? 'true' : 'false' }}";
        const customerPhone = "{{ auth('customer')->check() ? auth('customer')->user()->phone : '' }}";
        const customerName =
            "{{ auth('customer')->check() ? auth('customer')->user()->f_name . ' ' . auth('customer')->user()->l_name : '' }}";
        const serviceId = "{{ $details->id }}";
        const productId = "{{ $details->product_id }}";
        const route = "{{ route('offline.pooja.lead.store') }}";
        const bookingDate = "";

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
                    percent: el.data('percent'),
                    person: el.data('person'),
                    desc: el.data('desc'),
                    color: el.data('color'),
                    img: el.data('img'),
                    packageId: el.data('package-id')
                };

                // Set background gradient
                $('#packageContent').css('background', `linear-gradient(to bottom, ${packageData.color}, #ffffff)`);
                let participationSection;

                if (authCheck == "true") {
                    // User is logged in - show direct form
                    participationSection = `
                        <form class="needs-validation_" id="QueryForm" name="QueryForm" action="${route}" method="POST">
                            @csrf
                            <input type="hidden" name="service_id" value="${serviceId}">
                            <input type="hidden" name="product_id" value="${productId}">
                            <input type="hidden" name="package_id" value="${packageData.packageId}">
                            <input type="hidden" name="package_name" value="${packageData.title}">
                            <input type="hidden" name="package_main_price" value="${packageData.price}">
                            <input type="hidden" name="package_price" value="${packageData.price * (packageData.percent / 100)}">
                            <input type="hidden" name="noperson" value="${packageData.person}">
                            <input type="hidden" name="person_phone" value="${customerPhone}">
                            <input type="hidden" name="person_name" value="${customerName}">
                            <input type="hidden" name="booking_date" value="${bookingDate}">
                            <button type="submit" name="QueryForm" class="btn btn-primary btn-block btn-shadow mt-4 font-weight-bold">GO PARTICIPATE</button>
                        </form>
                    `;
                } else {
                    // User is not logged in - show modal trigger
                    participationSection = `
                        <a href="javascript:void(0);" class="btn btn-primary btn-block btn-shadow mt-4 font-weight-bold"
                            data-id="${packageData.packageId}" 
                            data-name="${packageData.title}"
                            data-price="${packageData.price}" 
                            data-percent="${packageData.percent}" 
                            data-person="${packageData.person}"
                            onclick="participateModel(this)">GO PARTICIPATE</a>
                    `;
                }
                // Create HTML content
                const contentHTML = `
            <div class="">
                <div class="mb-lg-0 rounded-lg shadow" style="background: linear-gradient(to bottom, ${packageData.color}, #ffffff);">
                    <div class="text-center pt-3">
                        <h5 class="card-title text-uppercase text-center font-bold" style="line-height: 1.5em;">
                         ${packageData.title}</h5>
                        <span class="h5 fw-bold text-center" style="line-height: 1.5em;min-height: 3em;">
                            ${'Pooja Amount RS.'+packageData.price}
                        </span><br>
                        <span class="h6 fw-bold text-center" style="line-height: 1.5em;min-height: 3em;">
                            ${'Booking Amount RS.'+packageData.price * (packageData.percent / 100) }
                        </span>
                    </div>
                    <hr class="text-GRAY-20 mx-3">
                    <div class="rounded-bottom p-2">
                        <div class="mb-5" style="margin-bottom:6rem!important;height: 220px;overflow:auto">
                            <div class="flex flex-col package-Information" style="font-size: 14px;">
                                <div style="display: flex; flex-direction: column">
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
    <script>
        // $('#pujaPackageButton').on('click', function(event) {
        //     event.preventDefault();
        //     if (!$('#packagesTabLink').hasClass('active')) {
        //         $('.nav-link').removeClass('active');
        //         $('#packagesTabLink').addClass('active');
        //         $('#packagesTabLink').tab('show');
        //     }
        //     $('html, body').animate({
        //         scrollTop: $('#packagesTabLink').offset().top
        //     }, 50);
        // });

        $('#pujaPackageButton1').on('click', function(event) {
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
@endpush
