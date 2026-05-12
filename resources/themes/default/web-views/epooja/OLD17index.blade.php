@extends('layouts.front-end.app')
@section('title', $epooja['meta_title'])
@php
    use Carbon\Carbon;
    use App\Utils\Helpers;
    use function App\Utils\getNextPoojaDay;
@endphp
@push('css_or_js')
    <meta name="description" content="{{ $epooja->slug }}">
    <meta name="keywords" content="@foreach (explode(' ', $epooja['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
    @if ($epooja['meta_image'] != null)
        <meta property="og:image" content="{{ dynamicStorage('storage/app/public/pooja/meta') }}/{{ $epooja->meta_image }}" />
        <meta property="twitter:card"
            content="{{ dynamicStorage('storage/app/public/pooja/meta') }}/{{ $epooja->meta_image }}" />
    @else
        <meta property="og:image"
            content="{{ dynamicStorage('storage/app/public/product/thumbnail') }}/{{ $epooja->thumbnail }}" />
        <meta property="twitter:card"
            content="{{ dynamicStorage('storage/app/public/product/thumbnail') }}/{{ $epooja->thumbnail }}" />
    @endif

    @if ($epooja['meta_title'] != null)
        <meta property="og:title" content="{{ $epooja->meta_title }}" />
        <meta property="twitter:title" content="{{ $epooja->meta_title }}" />
    @else
        <meta property="og:title" content="{{ $epooja->name }}" />
        <meta property="twitter:title" content="{{ $epooja->name }}" />
    @endif
    <meta property="og:url" content="{{ route('product', [$epooja->slug]) }}">
    @if ($epooja['meta_description'] != null)
        <meta property="twitter:description" content="{!! Str::limit($epooja['meta_description'], 55) !!}">
        <meta property="og:description" content="{!! Str::limit($epooja['meta_description'], 55) !!}">
    @else
        <meta property="og:description"
            content="@foreach (explode(' ', $epooja['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
        <meta property="twitter:description"
            content="@foreach (explode(' ', $epooja['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
    @endif
    <meta property="twitter:url" content="{{ route('product', [$epooja->slug]) }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/poojadetails.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/product-details.css') }}" />
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
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
    </style>
@endpush

@section('content')
    <div class="w-full h-full sticky top-0 z-20 d-none d-md-block" style="top: 84px;">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.epooja.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="__inline-23">
        <div class="container mt-4 rtl text-align-direction">
            <div class="row {{ Session::get('direction') === 'rtl' ? '__dir-rtl' : '' }}">
                <div class="col-lg-12 col-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-4 col-12" style="height: fit-content;">
                            <div class="cz-product-gallery">
                                <div class="cz-preview">
                                    <div id="sync1" class="owl-carousel owl-theme product-thumbnail-slider">
                                        @if ($epooja->images != null && json_decode($epooja->images) > 0)
                                            @if (json_decode($epooja->colors) && $epooja->color_image)
                                                @foreach (json_decode($epooja->color_image) as $key => $photo)
                                                    @if ($photo->color != null)
                                                        <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                                            id="image{{ $photo->color }}">
                                                            <img class="img-responsive w-100"
                                                                src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo->image_name, type: 'product') }}"
                                                                data-zoom="{{ getValidImage(path: 'storage/app/public/product/' . $photo->image_name, type: 'product') }}"
                                                                alt="{{ translate('product') }}" width="">

                                                        </div>
                                                    @else
                                                        <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                                            id="image{{ $key }}">
                                                            <img class="img-responsive w-100"
                                                                src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo->image_name, type: 'product') }}"
                                                                data-zoom="{{ getValidImage(path: 'storage/app/public/product/' . $photo->image_name, type: 'product') }}"
                                                                alt="{{ translate('product') }}" width="">

                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach (json_decode($epooja->images) as $key => $photo)
                                                    <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                                        id="image{{ $key }}">
                                                        <img class="cz-image-zoom img-responsive w-100"
                                                            src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo, type: 'product') }}"
                                                            data-zoom="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo, type: 'product') }}"
                                                            alt="{{ translate('product') }}" width="">

                                                    </div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <button type="button" data-product-id="{{ $epooja['id'] }}"
                                        class="btn __text-18px border wishList-pos-btn d-sm-none product-action-add-wishlist">
                                        <i class="fa fa-heart wishlist_icon_{{ $epooja['id'] }} web-text-primary"
                                            aria-hidden="true"></i>
                                    </button>
                                    <div class="sharethis-inline-share-buttons share--icons text-align-direction">
                                    </div>
                                </div>
                                <div class="cz">
                                    <div class="table-responsive __max-h-515px" data-simplebar>
                                        <div class="d-flex">
                                            <div id="sync2" class="owl-carousel owl-theme product-thumb-slider">
                                                @if ($epooja->images != null && json_decode($epooja->images) > 0)
                                                    @if (json_decode($epooja->colors) && $epooja->color_image)
                                                        @foreach (json_decode($epooja->color_image) as $key => $photo)
                                                            @if ($photo->color != null)
                                                                <div class="">
                                                                    <a class="product-preview-thumb color-variants-preview-box-{{ $photo->color }} {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                                                                        id="preview-img{{ $photo->color }}"
                                                                        href="#image{{ $photo->color }}">
                                                                        <img alt="{{ translate('product') }}"
                                                                            src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo->image_name, type: 'product') }}">
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="">
                                                                    <a class="product-preview-thumb {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                                                                        id="preview-img{{ $key }}"
                                                                        href="#image{{ $key }}">
                                                                        <img alt="{{ translate('product') }}"
                                                                            src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo->image_name, type: 'product') }}">
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @foreach (json_decode($epooja->images) as $key => $photo)
                                                            <div class="">
                                                                <a class="product-preview-thumb {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                                                                    id="preview-img{{ $key }}"
                                                                    href="#image{{ $key }}">
                                                                    <img alt="{{ translate('product') }}"
                                                                        src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo, type: 'product') }}">
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $nextDate = '';
                            $poojaw = json_decode($epooja->week_days);
                            $timedadat = date('H:i:s', strtotime($epooja->pooja_time));
                            $nextPoojaDay = getNextPoojaDay($poojaw, $timedadat);
                            // print_r($nextPoojaDay) ;die;
                            if ($nextPoojaDay) {
                                $nextDate = $nextPoojaDay->format('Y-m-d H:i:s');
                            }
                            // echo $nextDate;
                            // die;
                            // $current_year = date('Y');
                            // $current_date = date('Y-m-d');
                            // $earliestDate = null;
                            // $earliestTime = null;
                            // if (isset($epooja->schedule) && !empty($epooja->schedule)) {
                            // $event_date = json_decode($epooja->schedule);
                            // for(var i=0; i < 11; i++){
                            // if()
                            // }

                            // }
                            $current_date = date('Y-m-d');
                            $earliestDate = null;
                            $earliestTime = null;
                            if (isset($epooja->schedule) && !empty($epooja->schedule)) {
                                $event_date = json_decode($epooja->schedule);
                                usort($event_date, function ($a, $b) {
                                    return strtotime($a->schedule) - strtotime($b->schedule);
                                });
                                foreach ($event_date as $entry) {
                                    $dt = date('Y-m-d', strtotime($entry->schedule));
                                    if (strtotime($dt) >= strtotime($current_date)) {
                                        $earliestDate = $dt;
                                        break;
                                    }
                                }
                            }
                        @endphp


                        <div class="col-lg-6 col-md-8 col-12 mt-md-0 mt-sm-3 web-direction" style="height: fit-content;">
                            <div class="details __h-100">
                                <span class="text-12 font-bold  line-clamp-2 text-ellipsis mb-0"
                                    style="color:#fe9802;">{{ strtoupper($epooja->pooja_heading) }}
                                </span>
                                <div class="w-bar h-bar bg-gradient mt-2"></div>
                                <span class="mb-2 __inline-24">{{ $epooja->name }}</span><br />
                                <span class="text-16 mt-2 mb-2">{{ $epooja->short_benifits }}</span>

                                <div class="flex flex-col">
                                    <div class="flex items-center space-x-1 pt-2">
                                        <div class="d-flex">
                                            <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}"
                                                alt="" style="width:24px;height:24px;">
                                            {{ $epooja->pooja_venue }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1 pt-2">
                                        @if ($epooja->pooja_type == '0')
                                            <div class="d-flex">
                                                <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                                    alt="" style="width:24px;height:24px;">
                                                <p class="pooja-calendar">{{ date('d', strtotime($nextDate)) }},
                                                    {{ translate(date('F', strtotime($nextDate))) }} ,
                                                    {{ translate(date('l', strtotime($nextDate))) }}</p>
                                            </div>
                                        @else
                                            <div class="d-flex">
                                                <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                                    alt="" style="width:24px;height:24px;">
                                                <p class="pooja-calendar">
                                                    <?php if ($earliestDate !== null): ?> {{ date('d', strtotime($earliestDate)) }},
                                                    {{ translate(date('F', strtotime($earliestDate))) }}
                                                    <?php else: ?> <?php endif; ?>
                                                    {{ translate(date('l, h.i A', strtotime($earliestDate))) }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- Timere Section -->
                                <div class="flex flex-col">
                                    <div class="mt-2"><strong> {{ translate('Puja_Booking_will_be_closed') }}:
                                            @if ($epooja->pooja_type == '0')
                                                {{ date('d-m-y', strtotime($nextDate . '-24 hours')) }}
                                            @else
                                                {{ date('d-m-y', strtotime($earliestDate . '-1 day')) }}
                                            @endif
                                        </strong></div>

                                    <div class="mt-2">
                                        <div class="flex relative w-full">
                                            <div class="flex w-full justify-between flex-1">
                                                <div class="countdown">
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Profile Icon -->
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap justify-center items-center">
                                        <div class="w-full">
                                            <div class="tray mb-3" style="margin-right: 22rem;">
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/1.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/2.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/3.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/4.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/5.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/6.jpg')">
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
                                            <div class=""><span class=" inline-flex break-normal">
                                                    {{ translate('Till_now') }}</span><span
                                                    class=" font-bold text-#F18912 ml-1 break-normal">{{ 10000 + \App\Models\Service_order::where('type', 'pooja')->where('service_id', $epooja->id)->count() }}+<span
                                                        class=" ml-1 mr-1 inline-flex break-normal">{{ translate('Devotees') }}</span></span><span
                                                    class="text-">{{ translate('have_experienced_the_divine_blessings_by_participating_in_Puja_services_through_Mahakal.com') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Button -->
                                <div id="" role="button">
                                    <a href="#packages" id="pujaPackageButton"
                                        class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"
                                        data-toggle="tab" role="tab">
                                        <span class="font-bold">{{ translate('Select_puja_package') }}</span>
                                    </a>
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
                            <a class="section-link ml-2 active" href="#about_pooja">{{ translate('about_puja') }}</a>

                            <a class="section-link" href="#benefits">{{ translate('benefits') }}</a>
                            <a class="section-link" href="#process">{{ translate('process') }}</a>
                            <a class="section-link" href="#temple_details">{{ translate('temple_details') }}</a>
                            <a class="section-link" href="#packagesTabLink"
                                href="#packages">{{ translate('packages') }}</a>
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
                                    @include('web-views.epooja.partials.aboutMe')
                                </div>
                            </div>
                            <div class="section-content" id="benefits">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <!-- Benefits -->
                                    @include('web-views.epooja.partials.benifites')
                                </div>
                            </div>

                            <div class="section-content" id="process">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <!-- Process -->
                                    @include('web-views.epooja.partials.process')
                                </div>
                            </div>
                            <div class="section-content" id="temple_details">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <!-- Temple Details -->
                                    @include('web-views.epooja.partials.temple')
                                </div>
                            </div>
                            <div class="section-content" id="packagesTabLink" href="#packages">
                                <div class="row mt-2 p-3"
                                    style="background: white;box-shadow: 0px 3px 6px rgb(0 0 0 / 29%); border-radius: 5px; border-top: 2px solid #fe9802;">
                                    <div class="portfolio">
                                        @php
                                            $selected_packages_data = [];
                                            $packageIds = json_decode($epooja['packages_id']);
                                        @endphp
                                        @if (count($packageIds) > 0)

                                            <div class="portfolio-wrapper">
                                                <div class="row">
                                                    @foreach ($packageIds as $key => $pac)
                                                        @include('web-views.epooja.partials.package', [
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
                                </div>
                            </div>
                            <div class="section-content" id="reviews">
                                <div class="row mt-2 p-2 partial-pooja">
                                    <!-- Review -->
                                    <div class="col-12">
                                        @include('web-views.epooja.partials.review')
                                    </div>
                                </div>
                            </div>
                            <div class="section-content" id="faqs">
                                <div class="row mt-2 p-2 partial-pooja">
                                    @foreach ($Faqs as $faq)
                                        <div class="col-12">

                                            @include('web-views.epooja.partials.faq', ['faq' => $faq])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 rtl text-align-direction">
                @include('web-views.epooja.partials._pujavideo')
            </div>
        </div>
        <div class="modal fade rtl text-align-direction" id="queryModel" tabindex="-1" role="dialog"
            aria-labelledby="queryModel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
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
                            <span
                                class="block text-[16px] font-bold text-gray-900 dark:text-white">{{ translate('enter_Your_Whatsapp_Mobile_Number') }}</span>
                            <span
                                class="text-[12px] font-normal text-[#707070]">{{ translate('Your_puja_booking_updates_will_be_sent_on_below_WhatsApp_number') }}</span>
                            <!-- Model Form -->
                            <div class="w-full mr-9 px-0 pt-2">
                                <form class="needs-validation_" id="QueryForm"
                                    action="{{ route('poojastore', $epooja->slug) }}" method="GET">
                                    @csrf
                                    @php
                                        if (auth('customer')->check()) {
                                            $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                        }
                                    @endphp
                                    <input type="hidden" name="service_id" value="{{ $epooja->id }}">
                                    <input type="hidden" name="product_id" value="{{ $epooja->product_id }}">
                                    <input type="hidden" name="package_id" id="packagesId">
                                    <input type="hidden" name="package_name" id="packagesName">
                                    <input type="hidden" name="package_price" id="packagesPrice">
                                    <input type="hidden" name="noperson" id="packagesPerson">
                                    @if ($epooja->pooja_type == '0')
                                        <input type="hidden" name="booking_date" id="poojaBook"
                                            value="{{ date('Y-m-d', strtotime($nextDate)) }}" placeholder="Puja Weekly">
                                    @else
                                        <input type="hidden" name="booking_date" id="poojaBook"
                                            value="{{ date('Y-m-d', strtotime($earliestDate)) }}" placeholder="Events">
                                    @endif

                                    <div class="row">
                                        <div class="col-md-12" id="phone-div">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                    <small class="text-primary">( *
                                                        {{ translate('country_code_is_must_like_for_IND') }} 91
                                                        )</small>
                                                </label>
                                                <input
                                                    class="form-control text-align-direction phone-input-with-country-picker"
                                                    type="tel"
                                                    value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                                    name="person_phone" id="person-number"
                                                    placeholder="{{ translate('phone_number') }}" inputmode="number"
                                                    required maxlength="10" minlength="10"
                                                    {{ isset($customer['phone']) ? 'readonly' : '' }}
                                                    input-mode="number">

                                                <input type="hidden" class="country-picker-phone-number w-50"
                                                    name="person_phone" id="phone-code" readonly>

                                                <p id="number-validation" class="text-danger" style="display: none">
                                                    Enter
                                                    Your Valid Mobile Number</p>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="name-div">
                                            <div class="form-group">
                                                <label
                                                    class="form-label font-semibold">{{ translate('your_name') }}</label>
                                                <input class="form-control text-align-direction"
                                                    value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? $customer['l_name'] : '' }}"
                                                    type="text" name="person_name" id="person-name"
                                                    placeholder="{{ translate('Ex') }}: {{ translate('your_name') }}!"
                                                    inputmode="name" required
                                                    {{ isset($customer['f_name']) ? 'readonly' : '' }} input-mode="text">
                                                <p id="name-validation" class="text-danger" style="display: none">
                                                    Enter
                                                    Your Name</p>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="otp-input-div" style="display: none;">
                                            <div class="form-group text-center">
                                                <div id="masked-number">
                                                    {{ translate('An_OTP_has_been_sent_to') }}
                                                    ********<span id="number"></span></div>
                                                {{--
                                                <label class="form-label font-semibold ">{{ translate('enter_OTP') }}</label> --}}
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
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                style="width:445px">
                                                {{ translate('Book Puja') }}</button>
                                        </div> --}}
                                        {{-- with mobile number register end --}}
                                        <div class="mx-auto mt-1" id="send-otp-btn-div">
                                            <button type="button"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                id="send-otp-btn" style="width:445px">
                                                {{ translate('book_puja') }}
                                            </button>
                                            <p class="text-center mt-2" style="font-size: 10px;">
                                                {{ translate('By_tapping_the_"Book Puja"_button_below_,_you_will_send_an_OTP_to_ your_mobile_number') }}
                                            </p>
                                        </div>

                                        <div class="mx-auto mt-1 __max-w-356" id="verify-otp-btn-div"
                                            style="display: none ;width:445px">
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
                                        <p id="resend-otp-timer-text" style="display: none">
                                            {{ translate('Resend_OTP_in') }} <span id="resend-otp-timer"></span>
                                        </p>
                                        <p id="resend-otp-btn-text" style="display: none">
                                            {{ translate('Did_not_get_the_code?') }}
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
    </div>
    @if ($epooja->pooja_type == '0')
        <input type="hidden" name="date" id="fullDate"
            value="{{ date('Y-m-d', strtotime($nextDate . ' -24 hours')) }}">
        <input type="hidden" name="dates" id="fullDates"
            value="{{ date('Y-m-d', strtotime($nextDate . ' -24 hours')) }}">
        <input type="hidden" name="time" id="fullTime"
            value="{{ date('H:i:s', strtotime($epooja->pooja_time)) }}">
    @else
        <input type="hidden" name="date" id="fullDate"
            value="{{ date('Y-m-d', strtotime($earliestDate . ' -1 day')) }}">
        <input type="hidden" name="dates" id="fullDates"
            value="{{ date('Y-m-d', strtotime($earliestDate . ' -1 day')) }}">
        <input type="hidden" name="time" id="fullTime" value="{{ date('H:i:s', strtotime('23:59:00')) }}">
    @endif
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>
    <!-- <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script> -->
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
    <!-- Validation -->
    <!-- Firbase OTP -->
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>
    <script>
        @if ($epooja->pooja_type == '0')
            var t = new Date("{{ date('Y-m-d', strtotime($nextDate)) }}");
        @else
            var t = new Date("{{ date('Y-m-d', strtotime($earliestDate)) }}");
        @endif
        panchang.calculate(t, function() {
            document.getElementById("tithi").innerHTML = panchang.Tithi.name;
            document.getElementById("naksh").innerHTML = panchang.Nakshatra.name;
        });
    </script>
    <script type="text/javascript">
        var dateGet = $('#fullDate').val();
        var timeGet = $('#fullTime').val();
        const newDate = new Date(dateGet + ' ' + timeGet).getTime();
        // const newDate = new Date('07-13-2024 09:00:00').getTime()
        const countdown = setInterval(() => {
            const date = new Date().getTime()
            const diff = newDate - date
            const days = Math.floor(diff % (1000 * 60 * 60 * 24 * (365.25 / 12)) / (1000 * 60 * 60 * 24))
            const hours = Math.floor(diff % (1000 * 60 * 60 * 24) / (1000 * 60 * 60))
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
            const seconds = Math.floor((diff % (1000 * 60)) / 1000)
            document.querySelector(".seconds").innerHTML = seconds < 10 ? '0' + seconds : seconds
            document.querySelector(".minutes").innerHTML = minutes < 10 ? '0' + minutes : minutes
            document.querySelector(".hours").innerHTML = hours < 10 ? '0' + hours : hours
            document.querySelector(".days").innerHTML = days < 10 ? '0' + days : days

            if (diff <= 0) {
                clearInterval(countdown)
                location.reload();
                // document.querySelector(".countdown").innerHTML = 'Next Weekend'
            }

        }, 1000)
    </script>
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
            $('#queryModel').modal('show');
        }
        // OTP SEND THE MODEL
        var confirmationResult;
        var appVerifier = "";
        var sendOtpCount = 1;
        $('#send-otp-btn').click(function(e) {
            e.preventDefault();
            var name = $('#person-name').val();
            var phoneNumber = $('#phone-code').val();
            // var number = $('#person-number').val();
            // var phoneNumber = '+91' + $('#person-number').val();
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
                $('#send-otp-btn').text('Please Wait ...');
                $('#send-otp-btn').prop('disabled', true);
                toastr.success('please wait...');
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
                        $('#otp-input-div').css('display', 'block');
                        $('#verify-otp-btn-div').css('display', 'block');
                        if (sendOtpCount == 1) {
                            sendOtpCount = 2;
                            otpTimer();
                        }
                        confirmationResult = confirmation;
                        toastr.success('OTP sent successfully');
                        $('#send-otp-btn').prop('disabled', true);
                        $('#resend-div').show();
                    })
                    .catch(function(error) {
                        toastr.error('Failed to send OTP. Please try again.');
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

            var phoneNumber = '+91 ' + $('#person-number').val();
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
                    toastr.success('Failed to send OTP. Please try again.');
                });
        });

        $('#verify-otp-btn').click(function(e) {
            e.preventDefault();
            toastr.success('Please Wait...');
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
                $('#otp6').val();
            if (confirmationResult) {
                $('#queryModel').modal('hide');
                confirmationResult.confirm(otp).then(function(result) {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        $('#QueryForm').submit();
                    })
                    .catch(function(error) {
                        $('#otpValidation').text('Incorrect OTP');
                        // $('#submit').text('Submit');
                        // $('#submit').prop('disabled', false);
                    });
            }


        });
        // Without OTP LOGIN
        $('#withoutOTP').click(function(e) {
            e.preventDefault();
            if (confirmationResult) {
                $('#queryModel').modal('hide');
                confirmationResult.confirm(otp).then(function(result) {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        $('#QueryForm').submit();
                    })
                    .catch(function(error) {
                        $('#person-name').text(
                            'Mobile Number Not Resgiter Pleae frist fall Register the Mahakal.com web and App'
                        );
                    });
            }


        });
        // Without OTP LOGIN

        $('#otp-back-btn').click(function(e) {
            e.preventDefault();
            $('#send-otp-btn-div').css('display', 'block');
            $('#phone-div').css('display', 'block');
            $('#name-div').css('display', 'block');
            $('#otp-input-div').css('display', 'none');
            $('#verify-otp-btn-div').css('display', 'none');
            $('#send-otp-btn').prop('disabled', false);
            $('#resend-div').hide();
            $('#send-otp-btn').text('Book Pooja');
        });
    </script>
    <script>
        $('#pujaPackageButton').on('click', function(event) {
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
                    // console.log(response);
                    if (response.status == 200) {
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);
                        // $('#send-otp-btn').css('display', 'none');
                        // $('#withoutOTPButton').css('display', 'block');

                    }
                }
            });
        });
    </script>
    {{-- show pooja venue --}}
    <script>
        // JavaScript function to toggle remaining addresses
        function showRemainingAddresses(that) {
            var id = $(that).data('id');
            var remainingDiv = document.getElementById('remainingAddresses' + id);
            if (remainingDiv.style.display === 'none') {
                remainingDiv.style.display = 'block';
            } else {
                remainingDiv.style.display = 'none';
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
                    }
                });
            });

            $(window).trigger('scroll');
        });
    </script>
@endpush
