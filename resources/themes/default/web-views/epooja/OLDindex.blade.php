{{-- @php
    dd($epooja);
@endphp --}}
@extends('layouts.front-end.app')
@section('title', $epooja['service_name'])
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
        .product-preview-item{
            height: 60%!important;
        }
    </style>
@endpush

@section('content')
    <div class="w-full h-full sticky md:top-[68px] top-0 z-20" style="top: 84px;">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto" id="breadcrum-container-outer">
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
                        <div class="col-lg-6 col-md-4 col-12">
                            <div class="cz-product-gallery">
                                <div class="cz-preview">
                                    <div id="sync1" class="owl-carousel owl-theme product-thumbnail-slider">
                                        @if ($epooja->images != null && json_decode($epooja->images) > 0)
                                            @if (json_decode($epooja->colors) && $epooja->color_image)
                                                @foreach (json_decode($epooja->color_image) as $key => $photo)
                                                    @if ($photo->color != null)
                                                        <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                                            id="image{{ $photo->color }}">
                                                            <img class="img-responsive w-100"                                                                src="{{ getValidImage(path: 'storage/app/public/pooja/' . $photo->image_name, type: 'product') }}"
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
                        $nextPoojaDay = getNextPoojaDay($poojaw,$timedadat);
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
                        //     $event_date = json_decode($epooja->schedule);
                        //     for(var i=0; i < 11; i++){
                        //         if()
                        //     }
                        
                        // }
                        $current_date = date('Y-m-d');
                        $earliestDate = null;
                        $earliestTime = null;
                        if (isset($epooja->schedule) && !empty($epooja->schedule)) {
                            $event_date = json_decode($epooja->schedule);
                            usort($event_date, function($a, $b) {
                                return strtotime($a->schedule) - strtotime($b->schedule);
                            });
                            foreach ($event_date as $entry) {
                                $dt = date('Y-m-d',strtotime($entry->schedule));
                                if(strtotime($dt) > strtotime($current_date)){
                                    $earliestDate= $dt;
                                    break;
                                }
                            }
                        
                        }            
                        @endphp


                        <div class="col-lg-6 col-md-8 col-12 mt-md-0 mt-sm-3 web-direction">
                            <div class="details __h-100">
                                <span class="text-[12px] font-bold  line-clamp-2 text-ellipsis mb-0"
                                    style="color:#d61f69;">{!! $epooja->short_benifits !!}</span>
                                <div class="w-[200px] h-[0.5px] bg-gradient-to-r from-[#D61F69] ... mt-[6px] md:mt-0">
                                </div>
                                <span class="mb-2 __inline-24">{{ $epooja->name }}</span>
                                <div class="flex flex-col">
                                    <div class="flex items-center space-x-1 pt-[16px] md:pt-2">
                                        <?php
                                        $poojaVenue='';
                                        $venue = json_decode($epooja->pooja_venue);
                                        if (is_array($venue) || is_object($venue)) {
                                            foreach ($venue as $address) {
                                                $poojaVenue= $address;
                                            }
                                        }
                                        
                                    ?>
                                    <span class="">
                                    <p class="card-text"><i class="fa fa-map-marker" style="color: var(--primary-clr); margin-right: 5px;"></i>
                                        {{ substr($poojaVenue, 0, 40) }}
                                        @if (strlen($poojaVenue) > 40)
                                            <span id="full-address" style="display: none;">{{ $poojaVenue }}</span>
                                            <a href="#" id="show-full-address">+1 more</a>
                                        @endif
                                    </p></span>
                                        @if($epooja->pooja_type == '0')                                     
                                            <span class="mb-2"> <i class="fa fa-calendar" aria-hidden="true" style="color: var(--primary-clr);"></i>
                                            </span> {{ date('d F', strtotime($nextDate)) }}, {{ @ucFirst(date('l', strtotime($nextDate))) }}, <span id="tithi"></span>
                                            <span id="naksh"></span>
                                        @else
                                          
                                       <span class="mb-2"> <i class="fa fa-calendar" aria-hidden="true" style="color: var(--primary-clr);"></i></span>
                                            <?php if ($earliestDate !== null): ?> {{date('d F',strtotime($earliestDate)) }}, <?php else: ?> <?php endif; ?>
                                                {{ date('l',strtotime($earliestDate))}},{{ date('h:i:s A', strtotime($earliestTime)) }} <span id="tithi"></span>
                                                <span id="naksh"></span>
                                            @endif
                                    </div>
                                </div>
                                <!-- Timere Section -->
                                <div class="flex flex-col">
                                    <div class="mt-2"><strong>Puja booking will close in:
                                        @if($epooja->pooja_type == '0')    
                                             {{ date('d M Y', strtotime($nextDate.' -24 hours')) }}
                                            @else
                                            {{ date('d M Y', strtotime($earliestDate.' -1 day')) }}
                                            @endif
                                        </strong></div>
                                    <div class="mt-2">
                                        <div class="flex relative w-full">
                                            <div class="flex w-full justify-between flex-1">
                                                <div class="countdown">
                                                    <div>
                                                        <span class="number days"></span>
                                                        <span>Days</span>
                                                    </div>
                                                    <div>
                                                        <span class="number hours"></span>
                                                        <span>Hour</span>
                                                    </div>
                                                    <div>
                                                        <span class="number minutes"></span>
                                                        <span>Mins</span>
                                                    </div>
                                                    <div>
                                                        <span class="number seconds"></span>
                                                        <span>Secs</span>
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
                                            <div class="w-full tray mb-3">
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
                                            <div class=""><span class=" inline-flex break-normal">Till
                                                    now</span><span
                                                    class=" font-bold text-#F18912 ml-1 break-normal">{{ \App\Models\Service_order::where('type', 'pooja')->count() }}+<span
                                                        class=" ml-1 mr-1 inline-flex break-normal">Devotees</span></span><span
                                                    class="text-">have participated in Pujas conducted by<strong>
                                                        mahakal.com </strong>Puja Seva.</span></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Button -->
                                <div id="" role="button">
                                    <a href="#packages" id="pujaPackageButton"
                                        class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"
                                        data-toggle="tab" role="tab">
                                        <span class="font-bold">Select puja package</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 rtl text-align-direction">
                    <div class="row">
                        <div class="col-12">
                            <ul class="nav nav-tabs nav--tabs d-flex justify-content-between mt-3 border-top border-bottom py-2"
                                role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link __inline-27 active" href="#about_pooja" data-toggle="tab"
                                        role="tab">
                                        {{ translate('about_pooja') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link __inline-27" href="#benefits" data-toggle="tab" role="tab">
                                        {{ translate('benefits') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link __inline-27" href="#process" data-toggle="tab" role="tab">
                                        {{ translate('process') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link __inline-27" href="#temple_details" data-toggle="tab"
                                        role="tab">
                                        {{ translate('temple_details') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link __inline-27 "id="packagesTabLink" href="#packages"
                                        data-toggle="tab" role="tab">
                                        {{ translate('packages') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link __inline-27" href="#reviews" data-toggle="tab" role="tab">
                                        {{ translate('reviews') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link __inline-27" href="#faqs" data-toggle="tab" role="tab">
                                        {{ translate('faqs') }}
                                    </a>
                                </li>
                            </ul>
                            <div class="px-4 pb-3 mb-3 mr-0 mr-md-2 __review-overview __rounded-10 pt-3">
                                <div class="tab-content px-lg-3">
                                    <!-- About Me -->
                                    @include('web-views.epooja.partials.aboutMe')
                                    <!-- Benefits -->
                                    @include('web-views.epooja.partials.benifites')
                                    <!-- Process -->
                                    @include('web-views.epooja.partials.process')
                                    <!-- Temple Details -->
                                    @include('web-views.epooja.partials.temple')
                                    <!-- Packages -->
                                    <div class="tab-pane fade show" id="packages" role="tabpanel">
                                        <div class="portfolio">
                                            @php
                                                $selected_packages_data = [];
                                                $packageIds = json_decode($epooja['packages_id']);
                                            @endphp
                                            @if (count($packageIds) > 0)

                                                <div class="portfolio-wrapper">
                                                    <div class="row d-flex justify-content-between mx-1 mb-3">
                                                        <div>
                                                            <span class="font-bold pl-1">Select puja package</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        @foreach($packageIds as $key => $pac)
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
                                    <!-- Review -->
                                    <div class="tab-pane fade show" id="reviews" role="tabpanel">
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8">
                                                <div class="row pt-2 pb-3">
                                                    <div class="col-lg-2 col-md-4">
                                                        <div class="row d-flex justify-content-center align-items-center">
                                                            <div class="col-12 d-flex justify-content-center align-items-center">
                                                                <h2 class="overall_review mb-2">
                                                                    <!-- Content for overall review -->
                                                                   
                                                                </h2>
                                                            </div>
                                                            <div class="col-12 d-flex justify-content-center align-items-center mt-2">
                                                                <span class="text-center">
                                                                    <!-- Additional content if needed -->
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   <div class="col-lg-8 col-md-7 pt-sm-3 pt-md-0">
                                                            @php
                                                                $serviceReviews = $serviceReview ?? 0; // Ensure $serviceReview is available and not null
                                                                $reviewCounts = $reviewCounts ?? ['excellent' => 0, 'good' => 0, 'average' => 0, 'below_average' => 0, 'poor' => 0];

                                                                // Calculate overall rating
                                                                $sumRatings = 5 * $reviewCounts['excellent'] + 4 * $reviewCounts['good'] + 3 * $reviewCounts['average'] + 2 * $reviewCounts['below_average'] + 1 * $reviewCounts['poor'];
                                                                $overallRating = $serviceReviews > 0 ? number_format($sumRatings / $serviceReviews, 1) : 0;

                                                                // Ensure $overallRating is numeric
                                                                $overallRating = is_numeric($overallRating) ? $overallRating : 0;

                                                                // Calculate stars for display
                                                                $fullStars = floor($overallRating); // Full stars
                                                                $halfStar = round($overallRating - $fullStars); // Half star 
                                                            @endphp

                                                            <!-- Display progress bars for each review category -->
                                                            @foreach (['excellent', 'good', 'average', 'below_average', 'poor'] as $key)
                                                                <div class="d-flex align-items-center mb-2 text-body font-size-sm">
                                                                    <div class="__rev-txt">
                                                                        <span class="d-inline-block align-middle">{{ translate($key) }}</span>
                                                                    </div>
                                                                    <div class="w-0 flex-grow">
                                                                        <div class="progress text-body __h-5px">
                                                                            <div class="progress-bar" role="progressbar" style="width: {{ $serviceReviews > 0 ? ($reviewCounts[$key] / $serviceReviews) * 100 : 0 }}%;"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-1 text-body">
                                                                        <span class="{{ Session::get('direction') === 'rtl' ? 'mr-3 float-left' : 'ml-3 float-right' }}">
                                                                            {{ $reviewCounts[$key] }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                </div>
                                            </div>


                                              <!-- Display overall rating stars -->
                                           <div class="col-lg-4 col-md-4">
                                        <div class="text-center text-capitalize">
                                            <p class="text-capitalize">
                                              
                                                     <big>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $fullStars)
                                                            <i class="fa fa-star text-primary"></i> 
                                                        @elseif ($i <= $fullStars + $halfStar)
                                                            <i class="fa fa-star-half-alt text-primary"></i>
                                                        @else
                                                            <i class="fa fa-star text-muted"></i> 

                                                        @endif
                                                    @endfor
                                                    </big>
                                                    <h1>
                                                    ({{ $overallRating }})
                                                </h1>
                                            </p>
                                        </div>
                                    </div>
                                    </div>
                                    </div>
                                    <!-- FAQs -->
                                    <div class="tab-pane fade show" id="faqs" role="tabpanel">
                                        @foreach ($Faqs as $faq)
                                            @include('web-views.epooja.partials.faq', ['faq' => $faq])
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
        </div>
        <div class="modal fade rtl text-align-direction" id="queryModel" tabindex="-1" role="dialog"
            aria-labelledby="queryModel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="flex justify-center items-center my-3">
                            <span class="text-18 font-bold mr-2"> 
                                Fill your details for Puja</span>
                        </div>
                       
                    </div>
                    <hr class="bg-[#E6E4EB] w-full">
                    <div class="modal-body flex justify-content-center">
                        <div id="recaptcha-container"></div>
                        <div class="w-full mt-1 px-2">
                            {{-- <span class="block text-[16px] font-bold text-gray-900 dark:text-white">Enter Your Whatsapp
                                Mobile Number</span>
                            <span class="text-[12px] font-normal text-[#707070]">Your Puja booking updates like Puja
                                Photos, Videos and other details will be sent on WhatsApp on below number.</span> --}}
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
                                    @if($epooja->pooja_type == '0')
                                    <input type="hidden" name="booking_date" id="poojaBook" value="{{ date('Y-m-d', strtotime($nextDate)) }}" placeholder="Pooja Weekly">
                                    @else
                                    <input type="hidden" name="booking_date" id="poojaBook" value="{{ date('Y-m-d', strtotime($earliestDate)) }}" placeholder="Events">
                                    @endif
                                  
                                    <div class="row">
                                        <div class="col-md-12" id="phone-div">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                    <small class="text-primary">( *
                                                        {{ translate('country_code_is_must_like_for_IND') }} 91 )</small>
                                                </label>
                                                <input class="form-control text-align-direction phone-input-with-country-picker"
                                                    type="tel" value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                                    name="person_phone" id="person-number"
                                                    placeholder="{{ translate('enter_phone_number') }}"
                                                    inputmode="number" required maxlength="10" minlength="10"
                                                    {{ isset($customer['phone']) ? 'readonly' : '' }} input-mode="number">

                                                <input type="hidden" class="country-picker-phone-number w-50"
                                                    name="person_phone" readonly>

                                                <p id="number-validation" class="text-danger" style="display: none">Enter
                                                    Your Valid Mobile Number</p>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="name-div">
                                            <div class="form-group">
                                                <label  class="form-label font-semibold">{{ translate('your_name') }}</label>
                                                <input class="form-control text-align-direction"
                                                    value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? $customer['l_name'] : '' }}"
                                                    type="text" name="person_name" id="person-name"
                                                    placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                                    inputmode="name" required
                                                    {{ isset($customer['f_name']) ? 'readonly' : '' }} input-mode="text">
                                                <p id="name-validation" class="text-danger" style="display: none">Enter
                                                    Your Name</p>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="otp-input-div" style="display: none;">
                                            <div class="form-group text-center">
                                                <div id="masked-number">An OTP has been sent to ********<span id="number"></span></div>
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

                                        <div class="mx-auto mt-1 __max-w-356" id="send-otp-btn-div">
                                            <button type="button" class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                id="send-otp-btn"> {{ translate('book_puja') }} </button>
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
                                        <p id="resend-otp-btn-text" style="display: none">Didn't get the code?
                                         <a href="javascript:0" id="resend-otp-btn" style="color: blue;">Resend OTP</a>
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
    
@endsection
@if($epooja->pooja_type == '0')
<input type="hidden" name="date" id="fullDate" value="{{ date('Y-m-d', strtotime($nextDate.' -24 hours')) }}">
<input type="hidden" name="dates" id="fullDates" value="{{ date('Y-m-d', strtotime($nextDate.' -24 hours')) }}">
<input type="hidden" name="time" id="fullTime" value="{{ date('H:i:s', strtotime($epooja->pooja_time)) }}">
@else
<input type="hidden" name="date" id="fullDate" value="{{ date('Y-m-d', strtotime($earliestDate.' -1 day')) }}">
<input type="hidden" name="dates" id="fullDates" value="{{ date('Y-m-d', strtotime($earliestDate.' -1 day')) }}">
<input type="hidden" name="time" id="fullTime" value="{{ date('H:i:s',strtotime('23:59:00')) }}">
@endif

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
    <!-- Firbase OTP -->
    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyBrrMSAtiASPJGKt0aAQqpIYXoFUG4QGv8",
            authDomain: "rizrv-65a76.firebaseapp.com",
            projectId: "rizrv-65a76",
            storageBucket: "rizrv-65a76.appspot.com",
            messagingSenderId: "832249240595",
            appId: "1:832249240595:web:083a86aed6f3f50fa133e6",
            measurementId: "G-GXM9FXY2WM"
        };
        firebase.initializeApp(firebaseConfig);
    </script>
    <!-- Validation -->
    <!-- Firbase OTP -->
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>
    <script>
        @if($epooja->pooja_type == '0')
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
        $('#send-otp-btn').click(function(e) {
            e.preventDefault();
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            var phoneNumber = '+91 ' + $('#person-number').val();
            sendotp();
            $(this).text('Please Wait ...');
            $(this).prop('disabled', true);
            toastr.success('Please Wait...');
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
                $(this).text('Please Wait ...');
                $(this).prop('disabled', true);
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
                        otpTimer();
                        confirmationResult = confirmation;
                        toastr.success('OTP sent successfully');
                        $('#send-otp-btn').prop('disabled', true);
                        $('#resend-div').show();
                    })
                    .catch(function(error) {
                        console.error('OTP sending error:', error);
                        toastr.success('Failed to send OTP. Please try again.');
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
                confirmationResult.confirm(otp).then(function(result) {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        $('#QueryForm').submit();
                    })
                    .catch(function(error) {
                        $('#otpValidation').text('Incorrect Otp');
                        // $('#submit').text('Submit');
                        // $('#submit').prop('disabled', false);
                    });
            }


        });

        $('#otp-back-btn').click(function (e) { 
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
    </script>
    {{-- mobile no blur --}}
    <script>
        $('#person-number').blur(function (e) { 
            e.preventDefault();
            var code = $('.iti__selected-dial-code').text();
            var mobile = $(this).val();
            var no = code+''+mobile;
            console.log(code);
            console.log(mobile);
            $.ajax({
                type: "get",
                url: "{{url('account-service-order-user-name')}}"+"/"+no,
                success: function (response) {
                    console.log(response);
                    if(response.status==200){
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);                        
                    }
                }
            });
        });
    </script>
@endpush
