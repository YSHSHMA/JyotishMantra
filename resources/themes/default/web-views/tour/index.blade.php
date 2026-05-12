@extends('layouts.front-end.app')

@section('title', translate('Dharmik Yatra Packages – Ujjain Mahakal Darshan aur Teerth Yatra Book Karein'))
@php
use App\Utils\Helpers;
use function App\Utils\displayStarRating;
@endphp
@push('css_or_js')
<meta property="og:image"
    content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta property="twitter:card"
    content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta name="description" content="Mahakal.com par Ujjain Mahakal Darshan, Omkareshwar, Maihar, Chitrakoot aur anya teerth sthal ke liye pavitra yatra packages book karein. Aaramdayak aur bhaktimay yatra anubhav paayein.">

<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/style.css') }}">

<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">

<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
    rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<style>
    .ribbon {
        width: 150px;
        height: 150px;
        overflow: hidden;
        position: absolute;
        top: -11px;
        left: -26px;
        /* top: -10px;
        left: -10px; */
    }

    .ribbon span {
        position: absolute;
        display: block;
        width: 200px;
        padding: 2px 0;
        color: #fff;
        font-weight: bold;
        text-align: center;
        font-size: 14px;
        transform: rotate(-45deg);
        top: 30px;
        left: -40px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
    }

    .gold {
        color: #FF7700;
    }

    #no-tour-message {
        font-size: 18px;
        color: #ff0000;
        font-weight: bold;
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

    .carousel-tour-visit .item {
        margin: 5px;
    }

    .vertical-activity-card__photo img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }


    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .nav-link.location.active {
        font-weight: bold;
    }

    .nav-link.location {
        color: black !important;
    }

    svg text {
        font-family: Lora;
        letter-spacing: 10px;
        stroke: #fe9802;
        font-size: 50px;
        font-weight: 700;
        stroke-width: 2;
        animation: textAnimate 3s infinite alternate;
    }

    @keyframes textAnimate {
        0% {
            stroke-dasharray: 0 50%;
            stroke-dashoffset: 10%;
            fill: hsl(35.99deg 100% 57.62%)
        }

        50% {
            stroke-dasharray: 20% 0;
            stroke-dashoffstet: -20%;
            fill: hsla(19, 6%, 7%, 0%)
        }

        100% {
            stroke-dasharray: 50% 0;
            stroke-dashoffstet: -20%;
            fill: hsla(189, 68%, 75%, 0%)
        }

    }

    .loader-icon-search {
        display: none;
        font-size: 1.5rem;
        /* Adjust size as needed */
    }

    .one-line-show {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .responsive-bg {
        padding-top: 6rem !important;
        padding-bottom: 7rem !important;
        background:url("{{ asset('public/assets/front-end/img/slider/yatra-booking.jpg') }}") no-repeat;
        /*background:url("{{ asset('assets/front-end/img/slider/yatra-booking.jpg') }}") no-repeat;*/
        background-size: cover;
        background-position: center center;
    }

    @media (max-width: 768px) {
        .responsive-bg {
            padding-top: 2.91rem !important;
            padding-bottom: 3rem !important;
            background:url("{{ asset('assets/front-end/img/slider/yatra-booking1.jpg') }}") no-repeat;
            /* background:url("{{ asset('public/assets/front-end/img/slider/yatra-booking1.jpg') }}") no-repeat; */
            background-size: cover;
            background-position: center center;
        }

        .single-product-details {
            font-size: 11px;
        }

        .pooja-calendar {
            font-size: 9px;
        }
    }

    .puja-image {
        aspect-ratio: 16 / 9;
        width: 100%;
        height: auto;
        object-fit: cover;
        background-color: #f5f5f5;
        border-radius: 0.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .puja-image:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
    }

    .tour-card {
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.3s ease;
    }

    .tour-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .tour-title-css {
        font-size: 1rem;
        font-weight: 600;
        padding: 0.75rem 1rem;
        /* border-bottom: 1px solid #f0f0f0; */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tour-details {
        justify-content: space-between;
        align-items: flex-start;
        padding: 0px 1rem 0.75rem 1rem;
        gap: 1rem;
    }

    /* Amenities vertical list */
    .tour-amenities {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .amenity-item {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #374151;
    }

    .amenity-item img {
        width: 30px;
        height: 30px;
        margin-right: 0.5rem;
    }

    /* Price section */
    .tour-price {
        min-width: 90px;
    }

    .tour-price .current {
        font-size: 1rem;
        font-weight: bold;
        color: #16a34a;
    }

    .tour-price .discount {
        font-size: 0.75rem;
        font-weight: 600;
        color: #dc2626;
    }

    .tour-price .old {
        font-size: 0.75rem;
        color: #9ca3af;
        text-decoration: line-through;
    }

    /* Extra info */
    .tour-info {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
        color: #4b5563;
    }

    /* Toggle button */
    .view-toggle {
        display: none;
        /* hidden by default */
        text-align: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: #FF7722;
        margin: 0.5rem 0;
        cursor: pointer;
        position: relative;
    }

    .view-toggle::after {
        content: ' ▼';
        font-size: 0.7rem;
    }

    .view-toggle.active::after {
        content: ' ▲';
    }

    /* Footer button */
    .tour-footer {
        padding: 0.75rem 1rem;
    }

    .tour-footer a {
        display: block;
        width: 100%;
        text-align: center;
        background: #FF7722;
        color: white;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.6rem 0;
        border-radius: 0.5rem;
        transition: background 0.3s ease;
    }

    .tour-footer a:hover {
        background: #e8691d;
    }

    .filter-tooltip {
        position: absolute;
        top: 40px;
        /* just below the button */
        right: 0;
        /* align to the right edge of the button */
        width: 200px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 10px;
        display: none;
        z-index: 999;
    }

    .filter-tooltip.active {
        display: block;
    }

    .filter-tooltip::before {
        content: "";
        position: absolute;
        top: -8px;
        right: 15px;
        border-width: 8px;
        border-style: solid;
        border-color: transparent transparent #fff transparent;
    }

    .range-slider {
        position: relative;
        height: 40px;
        width: 100%;
    }

    .range-slider input[type="range"] {
        position: absolute;
        width: 100%;
        height: 6px;
        top: 15px;
        background: transparent;
        pointer-events: none;
        -webkit-appearance: none;
        margin: 0;
        z-index: 1;
    }

    .range-slider input[type="range"]::-webkit-slider-thumb {
        pointer-events: all;
        width: 18px;
        height: 18px;
        background: #007bff;
        border-radius: 50%;
        cursor: pointer;
        -webkit-appearance: none;
        border: none;
        z-index: 3;
    }

    .range-slider input[type="range"]::-webkit-slider-runnable-track {
        height: 6px;
        border-radius: 3px;
        background: #ddd;
        border: none;
    }

    .range-slider input[type="range"]::-moz-range-thumb {
        pointer-events: all;
        width: 18px;
        height: 18px;
        background: #007bff;
        border-radius: 50%;
        cursor: pointer;
        border: none;
    }

    .range-slider input[type="range"]::-moz-range-track {
        height: 6px;
        border-radius: 3px;
        background: #ddd;
        border: none;
    }

    .range-slider .range-track {
        position: absolute;
        top: 15px;
        height: 6px;
        border-radius: 3px;
        background: #007bff;
        z-index: 1;
        left: 0;
        width: 50%;
    }
</style>
@endpush

@section('content')
{{-- main page --}}
<!-- <div class="inner-page-bg center bg-bla-7 responsive-bg">
    <div class="container">
        <div class="row all-text-white">
            <div class="col-md-12 align-self-center">
            </div>
        </div>
    </div>
</div> -->
<div class="image-box">
    <img src="{{ asset('public/assets/front-end/img/tour-banners.png') }}" alt="tour banner">
</div>
<div class="container-fluid">
    <div class="row">
        <div
            tyle="position: absolute;    top: 0;    left: 0;    right: 0;    bottom: 0;    background-color: rgb(52 51 51 / 38%);     z-index: 1;">
        </div>
        <div class="col-md-12 mt-4" style="position: relative; z-index: 2;">
            <div class="row justify-content-center align-items-center" style="position: relative; z-index: 2;">
                <div class="col-6 position-relative">
                    <!-- <input type="text" name='search' class="form-control border-0 fw-bold search_key" style="margin-top: -44px;position: absolute;" placeholder="{{ translate('search_by_Tour_Name') }}" autocomplete="off"> -->
                    <input class="form-control border-0 fw-bold search_key" type="search" name="name" style="margin-top: -24px;position: absolute;" placeholder="{{ translate('search_by_Tour_Name') }}" autocomplete="off" onkeyup="applyFilters()">
                    <!-- {{-- <div id="loader" class="loader-icon-search"
                        style="position: absolute; left: 10px; top: 10px; display: none;">
                        <i class="fa fa-spinner fa-spin"></i>
                    </div>
                    <form action="{{ route('tour.tour-visit') }}" method="get">
                        <input type="hidden" name="id" class="search_ids" value="">
                        <ul id="search-results" class="list-group position-absolute w-100" style="top: 5px;"></ul>
                    </form>--}} -->
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <ul id="filters" class="clearfix">
                <li><span class="filter all_tours" data-filter=".all_tours"
                        onclick="toggleSubcategories('all_tours','all_tours')">{{ translate('All_Tour_Packages') }}</span>
                </li>
                @if ($headers)
                @foreach ($headers as $va)
                @php
                if ($loop->index == 0) {
                $get_first_name = $va['slug'];
                }
                @endphp
                <li><span
                        class="filter {{ $va['slug'] }} {{ $va['slug'] == 'citie_tour' || $va['slug'] == 'cities_tour' ? 'category_in_cities' : '' }}"
                        data-filter=".{{ $va['slug'] }}"
                        onclick="toggleSubcategories(`{{ $va['slug'] }}`,`{{ $va['name'] }}`)">{{ $va['name'] }}</span>
                </li>
                @endforeach
                @endif
                <li class="float-right d-flex gap-2">
                    <a href="{{route('tour.all-vendor')}}" class="btn btn-sm btn--primary">{{translate('all_vendor')}}</a>

                    <div class="input-group-overlay search-form-mobile text-align-direction">
                        <div class="d-flex align-items-center gap-2">
                            <a id="filterToggle" class="btn btn--primary btn-sm me-4">
                                <i class="fa fa-filter"></i> {{ translate('filter') }}
                            </a>
                            <div id="filterTooltip" class="filter-tooltip">
                                <div class="filter-group">
                                    <label class="filter-label">{{ translate('Sort by Price') }}</label>
                                    <div class="radio-group">
                                        <label class="radio-label d-flex m-0">
                                            <input type="radio" name="priceSort" value="" class="sort-radio" checked>
                                            <span class="radiomark"></span>
                                            {{ translate('Default') }}
                                        </label>
                                        <label class="radio-label d-flex m-0">
                                            <input type="radio" name="priceSort" value="lowtohigh" class="sort-radio">
                                            <span class="radiomark"></span>
                                            {{ translate('Low to High') }}
                                        </label>
                                        <label class="radio-label d-flex m-0">
                                            <input type="radio" name="priceSort" value="hightolow" class="sort-radio">
                                            <span class="radiomark"></span>
                                            {{ translate('High to Low') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="filter-group mt-3">
                                    <label class="filter-label">{{ translate('Filter by Price Range') }}</label>
                                    <div class="range-slider position-relative">
                                        <input type="range" id="rangeMin" min="0" max="100000" value="10000" step="100">
                                        <input type="range" id="rangeMax" min="0" max="100000" value="60000" step="100">
                                        <div class="range-values text-center mt-3 d-flex gap-2">
                                            <span id="rangeMinVal" style="margin: -10px 0px 0px 0px;padding: 0px;">₹10,000</span>
                                            <span style="margin: -10px 0px 0px 0px;padding: 0px;">-</span>
                                            <span id="rangeMaxVal" style="margin: -10px 0px 0px 0px;padding: 0px;">₹60,000</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-group">
                                    <label class="filter-label">{{ translate('Type') }}</label>
                                    <div class="radio-group">
                                        <label class="radio-label d-flex m-0">
                                            <input type="radio" name="priceType" value="" class="type-radio" checked>
                                            <span class="radiomark"></span>
                                            {{ translate('All') }}
                                        </label>
                                        <label class="radio-label d-flex m-0">
                                            <input type="radio" name="priceType" value="per_person" class="type-radio">
                                            <span class="radiomark"></span>
                                            {{ translate('Per Person wise') }}
                                        </label>
                                        <label class="radio-label d-flex m-0">
                                            <input type="radio" name="priceType" value="cabs" class="type-radio">
                                            <span class="radiomark"></span>
                                            {{ translate('Cab wise') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="filter-group">
                                    <label class="filter-label">{{ translate('Filter by Plan') }}</label>
                                    <div class="radio-group">
                                        <label class="radio-label d-flex m-0">
                                            <input type="radio" name="filterplan" value="" class="type-radio" checked>
                                            <span class="radiomark"></span>
                                            {{ translate('All') }}
                                        </label>
                                        @php
                                        $Filterplans = [
                                        0 => ['name' => 'Basic'],
                                        1 => ['name' => 'Standard'],
                                        2 => ['name' => 'Premium'],
                                        3 => ['name' => 'Golden'],
                                        4 => ['name' => 'Luxury'],
                                        5 => ['name' => 'Only Cab'],
                                        ];
                                        @endphp
                                        @foreach($Filterplans as $npl)
                                        <label class="radio-label d-flex m-0">
                                            <input type="radio" name="filterplan" value="{{ $npl['name'] }}" class="type-radio">
                                            <span class="radiomark"></span>
                                            {{ $npl['name'] }}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="list-group list-group-horizontal clearfix cities_tour-none citie_tour-none"
                style="text-wrap: nowrap;overflow: auto;padding:8px 0px 8px 0px;">
                @if ($state_name)
                @foreach ($state_name as $key => $pur)
                <?php
                $getStates = \App\Models\TourVisits::select('cities_name', 'state_name')->where('id', $pur['id'])->first();
                ?>
                <li class="list-group-item p-0 border-0 filter_state_name rounded text-center"
                    style="margin-left:1%;padding: 1px 6px 0px 6px !important;cursor: pointer;"
                    onclick="new_chanage()"
                    data-filter=".{{ Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}">
                    <span class=" square-btn btn-sm subcategory"> {{ $pur['state_name'] }}</span>
                </li>
                @endforeach
                @endif
            </ul>
            <div class="container">
                <div id="portfoliolist" class="row">
                    <?php /*
                        @if (!empty($getDataAll) && count($getDataAll) > 0)
                        @foreach ($getDataAll as $use)
                        <?php
                        $getStates = \App\Models\TourVisits::select('cities_name', 'state_name')->where('id', $use['id'])->first();
                        ?>
                        <div
                            class="portfolio {{ $use['tour_type'] }} all_tours {{ $getStates['cities_name'] }} {{ Illuminate\Support\Str::Slug($getStates['cities_name'], '_') }} {{ Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}    {{ \Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}_{{ $use['tour_type'] }} {{ \Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}_all_tours">
                            <div class="portfolio-wrapper">
                                <div class="card">
                                    @if (!empty($use['number_of_day']))
                                    <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                        <span class="direction-ltr blink d-block">
                                            <?php
                                            $numb_days_en = '';
                                            $numb_days_in = '';
                                            if ($use['number_of_day'] == 0.5) {
                                                $numb_days_en = translate('Half Day');
                                            } elseif ($use['number_of_day'] > 0 && ($use['number_of_night'] ?? 0) <= 0) {
                                                $numb_days_en = ($use['number_of_day'] ?? '') . " " . translate("day");
                                            } else {
                                                $numb_days_en = ($use['number_of_day'] ?? '') . " " . translate("day") . "/" . ($use['number_of_night'] ?? '') . " " . translate("night");
                                            }
                                            ?>
                                            {{ $numb_days_en }}
                                        </span>
                                    </span>
                                    @endif
                                    <a href="{{ route('tour.tourvisit', [($use['slug']??'')]) }}">
                                        <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $use['tour_image'], type: 'product') }}"
                                            class="card-img-top puja-image" alt="...">
                                    </a>
                                    <div class="card-body">
                                        <h5 class="font-weight-700 pooja-heading underborder card-title clamp-2-lines" title="{{ $use['tour_name'] }}">
                                            {{ $use['tour_name'] }} &nbsp;
                                        </h5>
                                        <span class="card-title d-none"><?php echo $use->getRawOriginal('tour_name') ?></span>
                                        <div class="mt-2 single-product-details min-height-unset">
                                            <div class="row px-3">
                                                <div class="col-12 text-left" style="display: ruby;">
                                                    <?php
                                                    $price_minst = 0;
                                                    if (!empty($use['cab_list_price'])) {
                                                        $decodedPrices = json_decode($use['cab_list_price'], true);
                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedPrices)) {
                                                            $prices = array_column($decodedPrices, 'price');
                                                            $price_minst = !empty($prices) ? min($prices) : 0;
                                                        }
                                                    }
                                                    $package_tourone = [];
                                                    $include_package_amount = 0;
                                                    ?>
                                                    <?php
                                                    if (!empty($use['package_list_price']) && json_decode($use['package_list_price'], true)) {
                                                        foreach (json_decode($use['package_list_price'], true) as $keyk => $plis) {
                                                            $tourPackages = \App\Models\TourPackage::where('id', $plis['package_id'])->first();
                                                            if ($tourPackages && !isset($package_tourone[$tourPackages['type']])) {
                                                                $package_tourone[$tourPackages['type']] = [
                                                                    'package_id' => $plis['package_id'],
                                                                    'image' => theme_asset(path: 'public/assets/front-end/img/' . ($tourPackages['type'] . '.png' ?? '')),
                                                                ];
                                                            }
                                                            $include_package_amount += $plis['pprice'] ?? 0;
                                                        }
                                                    }
                                                    ?>
                                                    <?php $includePackages = json_decode($use['is_included_package'], true); ?>
                                                    @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['sightseen']??0) == 1))
                                                    <div class="px-2 text-center">
                                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/sightseeing.png') }}"
                                                            style="width: 49px; height: 42px; margin-bottom: 0px;">
                                                        <div class="ico-nem"><span
                                                                class="font-weight-bold" style="font-size:0.6rem;">{{ translate('sightseeing') }}</span>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['cab']??0) == 1))
                                                    <div class="px-2 text-center">
                                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/car.png') }}"
                                                            style="width: 49px; height: 42px; margin-bottom: 0px;">
                                                        <div class="ico-nem"><span
                                                                class="small font-weight-bold" style="font-size:0.6rem;">{{ translate('transport') }}</span>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if(($use['is_person_use'] == 1 && ($includePackages['food']??0) == 1))
                                                    <div class="px-2 text-center">
                                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/foods.png') }}"
                                                            style="width: 49px; height: 42px; margin-bottom: 0px;">
                                                        <div class="ico-nem"><span
                                                                class=" font-weight-bold" style="font-size:0.6rem;">{{ translate('foods') }}</span>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if(($use['is_person_use'] == 1 && ($includePackages['hotel']??0) == 1))
                                                    <div class="px-2 text-center">
                                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/hotel.png') }}"
                                                            style="width: 49px; height: 42px; margin-bottom: 0px;">
                                                        <div class="ico-nem"><span
                                                                class=" font-weight-bold" style="font-size:0.6rem;">{{ translate('hotel') }}</span>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <?php if (!empty($package_tourone)) {
                                                        ksort($package_tourone); ?>
                                                        <?php foreach ($package_tourone as $key => $t_va) { ?>
                                                            <div class="px-2">
                                                                <img src="<?= htmlspecialchars($t_va['image']) ?>"
                                                                    style="width: 49px; height: 42px; margin-bottom: 4px;">
                                                                <div class="ico-nem"><span
                                                                        class="small font-weight-bold"><?= htmlspecialchars(translate($key)) ?></span>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div>
                                                <small class="ml-2 one-line-show mt-2">
                                                    @if ($use['use_date'] == 1 || $use['use_date'] == 4 || $use['use_date'] == 2)
                                                    {{ translate('pickup_From') }} :
                                                    {{ $use['pickup_location'] ?? '' }}
                                                    @endif
                                                </small>
                                            </div>
                                            <p class="ml-2 mb-1">
                                                @if ($use['use_date'] == 1)
                                                <?php
                                                $dates_formatt = explode(' - ', $use['startandend_date'] ?? '');
                                                $start_date1 = isset($dates_formatt[0]) ? \Carbon\Carbon::parse($dates_formatt[0])->format('d M,Y') : '';
                                                $end_date2 = isset($dates_formatt[1]) ? \Carbon\Carbon::parse($dates_formatt[1])->format('d M,Y') : '';
                                                ?>
                                                <span class="fw-semibold card-title"> Date : {{ $start_date1 }}
                                                    To {{ $end_date2 }}</span>
                                                @else
                                                &nbsp;
                                                @endif
                                            </p>
                                            @php
                                            $price_minst = 0;
                                            if (
                                            !empty($use['cab_list_price']) &&
                                            json_decode($use['cab_list_price'], true)
                                            ) {
                                            $prices = array_column(
                                            json_decode($use['cab_list_price'], true),
                                            'price',
                                            );
                                            $price_minst = min($prices);
                                            }
                                            @endphp
                                            @if ($use['use_date'] == 1 || $use['use_date'] == 2 || $use['use_date'] == 3 || $use['use_date'] == 4)
                                            <a class="text-capitalize fw-semibold ml-2 card-title">{{ translate('minimum_price') }}
                                                :
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($price_minst ?? 0) + ($include_package_amount ?? 0)), currencyCode: getCurrencyCode()) }}
                                            </a>
                                            @else
                                            <a class="text-capitalize fw-semibold ml-2 card-title">{{ translate('minimum_price') }}
                                                :
                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $price_minst ?? 0), currencyCode: getCurrencyCode()) }}
                                            </a>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <!-- Devotees Count -->
                                            <div class="d-flex align-items-center">
                                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                    alt="Users" class="colored-icon"
                                                    style="width: 24px; height: 24px; margin-right: 5px;">
                                                <span class="pooja-calendar">10000+ People</span>
                                            </div>

                                            <!-- Star Rating -->
                                            <div class="d-flex align-items-center">
                                                {!! displayStarRating($use['review_avg_star'] ?? 5) !!}
                                                <span class="ml-2">({{ number_format($use->review_avg_star ?? 5, 1) }})</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('tour.tourvisit', [($use['slug']??'')]) }}"
                                            class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('book_Now') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                        */
                    ?>
                    <br>

                    @if (!empty($getDataAll) && count($getDataAll) > 0)
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach ($getDataAll as $use)
                        @php
                        $extraText = '';
                        if(in_array($use['use_date'], [1,2,4])) {
                        $extraText .= "<div><strong>".translate('pickup_From').":</strong> ".($use['pickup_location'] ?? '')."</div>";
                        }

                        if(in_array($use['use_date'], [1])) {
                        $dateRange = explode(' - ', $use['startandend_date']);
                        $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
                        $endDate = isset($dateRange[1]) ? $dateRange[1] : '';

                        if ($startDate && $endDate) {
                        $start = new DateTime($startDate);
                        $end = new DateTime($endDate);
                        $difference = $start->diff($end)->days;

                        $dateDisplay = "";
                        if (isset($use['customized_type']) && isset($use['customized_dates'])) {
                        $customizedType = $use['customized_type'];
                        $customizedDates = json_decode($use['customized_dates']??"[]",true);

                        switch ($customizedType) {
                        case 1:
                        $today = new DateTime();
                        $nextDates = [];
                        foreach ($customizedDates as $day) {
                        $next = new DateTime("next " . $day);
                        $nextDates[] = $next;
                        }
                        usort($nextDates, function ($a, $b) {
                        return $a <=> $b;
                            });
                            $nextDay = $nextDates[0]->format("d/m/Y");
                            $nextendDay = $nextDates[0]->modify("+".$difference." days")->format("d/m/Y");

                            $extraText .= "<div><strong>".translate('Tour_Date').":</strong> ".$nextDay." - ".$nextendDay."</div>";
                            break;

                            case 2:
                            $dayNumbers = array_map(function($date) {
                            return (int)date('d', strtotime($date));
                            }, $customizedDates);
                            $today = new DateTime();
                            $nextDates = [];
                            foreach ($dayNumbers as $dayNumber) {
                            $next = new DateTime($today->format('Y-m-' . sprintf('%02d', $dayNumber)));
                            if ($next < $today) {
                                $next->modify('+1 month');
                                }
                                $nextDates[] = $next;
                                }
                                usort($nextDates, function ($a, $b) {
                                return $a <=> $b;
                                    });

                                    $nextDay = $nextDates[0]->format("d/m/Y");
                                    $nextendDay = $nextDates[0]->modify("+".$difference." days")->format("d/m/Y");

                                    $extraText .= "<div><strong>".translate('Tour_Date').":</strong> ".$nextDay." - ".$nextendDay."</div>";
                                    break;

                                    case 3:
                                    $today = new DateTime();
                                    $nextDates = [];
                                    foreach ($customizedDates as $dateStr) {
                                    $date = DateTime::createFromFormat('Y-m-d', $dateStr);
                                    $monthDay = $date->format('m-d');
                                    $currentYearDate = DateTime::createFromFormat('Y-m-d', $today->format('Y') . '-' . $monthDay);
                                    if ($currentYearDate < $today) {
                                        $currentYearDate->modify('+1 year');
                                        }
                                        $nextDates[] = $currentYearDate;
                                        }
                                        usort($nextDates, function ($a, $b) {
                                        return $a <=> $b;
                                            });
                                            $nextDay = $nextDates[0]->format("d/m/Y");
                                            $formattedEndDay = $nextDates[0]->modify("+".$difference." days")->format("d/m/Y");;

                                            $extraText .= "<div><strong>".translate('Tour_Date').":</strong> ".$nextDay." - ".$formattedEndDay."</div>";
                                            break;
                                            }
                                            }
                                            }
                                            }

                                            if ($use['number_of_day'] == 0.5) {
                                            $numb_days_en = translate('Half Day');
                                            } elseif ($use['number_of_day'] > 0 && ($use['number_of_night'] ?? 0) <= 0) {
                                                $numb_days_en=($use['number_of_day'] ?? '' ) . "D" ;
                                                } else {
                                                $numb_days_en=($use['number_of_day'] ?? '' ) . "D/" . ($use['number_of_night'] ?? '' ) . "N" ;
                                                }

                                                $getStates=\App\Models\TourVisits::select('cities_name', 'state_name' )->where('id', $use['id'])->first();

                                                // Price
                                                $price_minst = 0;
                                                if (!empty($use['cab_list_price'])) {
                                                $decodedPrices = json_decode($use['cab_list_price'], true);
                                                if (is_array($decodedPrices)) {
                                                $prices = array_column($decodedPrices, 'price');
                                                $price_minst = !empty($prices) ? min($prices) : 0;
                                                }
                                                }
                                                $include_package_amount = 0;
                                                $package_tourone = [];
                                                if (!empty($use['package_list_price']) && json_decode($use['package_list_price'], true) && ($use['is_person_use'] == 0 && in_array($use['use_date'], [1, 2, 3, 4]))) {
                                                foreach (json_decode($use['package_list_price'], true) as $plis) {
                                                $tourPackages = \App\Models\TourPackage::find($plis['package_id']);
                                                if ($tourPackages && !isset($package_tourone[$tourPackages['type']])) {
                                                $package_tourone[$tourPackages['type']] = theme_asset('public/assets/front-end/img/' . $tourPackages['type'] . '.png');
                                                }
                                                $include_package_amount += $plis['pprice'] ?? 0;
                                                }
                                                }
                                                $includePackages = json_decode($use['is_included_package'], true);
                                                @endphp
                                                <div class="tour-card all_tours {{ $use['tour_type'] }} 
                                {{ $getStates['cities_name'] }} 
                                {{ Illuminate\Support\Str::Slug($getStates['cities_name'], '_') }} 
                                {{ Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}    
                                {{ \Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}_{{ $use['tour_type'] }} 
                                {{ \Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}_all_tours">
                                                    <!-- Image -->
                                                    <div class="relative flex items-center justify-center min-h-[11rem] text-center" style="min-height: 10.65rem; background: lavenderblush;">
                                                        @php
                                                        $plans = [
                                                        0 => ['name' => 'Basic', 'style' => 'linear-gradient(45deg, #6c757d, #495057)'],
                                                        1 => ['name' => 'Standard', 'style' => 'linear-gradient(45deg, #28a745, #218838)'],
                                                        2 => ['name' => 'Premium', 'style' => 'linear-gradient(45deg, #007bff, #0056b3)'],
                                                        3 => ['name' => 'Golden', 'style' => 'linear-gradient(45deg, #FFD700, #FFA500)'],
                                                        4 => ['name' => 'Luxury', 'style' => 'linear-gradient(45deg, #dc3545, #b02a37)'],
                                                        5 => ['name' => 'Only Cab', 'style' => 'linear-gradient(45deg,#dc3545, #4b0ec3)'],
                                                        ];

                                                        $selectedPlan = $use['plan_type'] ?? 0;
                                                        @endphp
                                                        <div class="ribbon">
                                                            <span style="background: {{ $plans[$selectedPlan]['style'] }};z-index:1000">
                                                                {{ translate($plans[$selectedPlan]['name']) }}
                                                            </span>
                                                        </div>
                                                        <a href="{{ route('tour.tourvisit', [$use['slug'] ?? '']) }}">
                                                            <img src="{{ getValidImage('storage/app/public/tour_and_travels/tour_visit/' . $use['tour_image'], 'product') }}" alt="{{ $use['tour_name'] }}" class="puja-image">
                                                        </a>
                                                        @if (!empty($use['number_of_day']))
                                                        <span class="p-1 pl-2 pr-2 font-bold fs-13 d-flex" style="{{ ((($font_page??0) == 1) ? 'top:14rem':'bottom: 6px')}}; inset-inline-start: 9px;position: absolute; background: #FF7722;color:white;z-index: 3;border-radius: 4px !important; white-space: nowrap;opacity: 0.8;">
                                                            @if (($use['is_person_use']??0) == 1)
                                                            <i class="fa fa-user-group"></i>&nbsp;{{ translate('Person wise')}}
                                                            @else
                                                            <i class="fa fa-car"></i>&nbsp;{{ translate('Cab wise')}}
                                                            @endif
                                                        </span>
                                                        </span>
                                                        @endif
                                                        @if (!empty($use['number_of_day']))
                                                        <span class="p-1 pl-2 pr-2 font-bold fs-13 d-flex" style="{{ ((($font_page??0) == 1) ? 'top:14rem':'bottom: 6px')}};inset-inline-end: 9px;position: absolute; background: #FF7722;color:white;z-index: 3;border-radius: 4px !important; white-space: nowrap;">
                                                            <span class="direction-ltr blink d-block">
                                                                {{ $numb_days_en }}
                                                            </span>
                                                        </span>
                                                        @endif
                                                    </div>

                                                    <!-- Title -->
                                                    <div class="d-flex gap-2 justify-content-center pooja-heading underborder p-2">
                                                        @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['sightseen']??0) == 1)) <div class="amenity-item"><img src="{{ theme_asset('public/assets/front-end/img/sightseeing.png') }}"></div>@endif
                                                        @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['cab']??0) == 1)) <div class="amenity-item"><img src="{{ theme_asset('public/assets/front-end/img/car.png') }}"></div>@endif
                                                        @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['food']??0) == 1)) <div class="amenity-item"><img src="{{ theme_asset('public/assets/front-end/img/foods.png') }}"></div>@endif
                                                        @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['hotel']??0) == 1)) <div class="amenity-item"><img src="{{ theme_asset('public/assets/front-end/img/hotel.png') }}"></div>@endif
                                                    </div>
                                                    <span class="card-title tour-title d-none"><?php echo $getStates['cities_name'] ?? "" ?></span>
                                                    <span class="card-title tour-title d-none"><?php echo $getStates['state_name'] ?? "" ?></span>
                                                    <span class="tour-cab-person d-none"><?php echo ((($use['is_person_use'] ?? 0) == 1) ? 'per_person' : 'cabs') ?></span>

                                                    <h3 class="card-title tour-title-css tour-title m-0">{{ $use['tour_name'] }}</h3>

                                                    <!-- Amenities + Price -->
                                                    <div class="tour-details">
                                                        <div class="d-flex align-items-center">
                                                            <span class="small font-weight-bolder">{{ translate('Starting_from') }} </span>
                                                        </div>
                                                        <div class="tour-price">
                                                            <?php $total_package_price = (($price_minst ?? 0) + ($include_package_amount ?? 0)); ?>
                                                            <span class="d-none amount-total-amount">{{ ($total_package_price??0) }}</span>
                                                            <div><span class="current">{{ setCurrencySymbol(usdToDefaultCurrency($total_package_price), getCurrencyCode()) }}</span> <span class="old">{{ setCurrencySymbol(($total_package_price / (1 - (($use['percentage_off'] ?? 0) / 100))), getCurrencyCode()) }}</span> (<span class="discount">{{$use['percentage_off']??0}}% OFF</span>)</div>
                                                            <div class="d-flex align-items-center">
                                                                {!! \App\Utils\displayStarRating(($use->review_avg_star ?? 5)) !!}
                                                                <span class="ml-2">({{ number_format(($use->review_avg_star ?? 5), 1) }})</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Toggle -->
                                                    @if(trim($extraText) != '')
                                                    <div class="tour-info extra-info hidden">{!! $extraText !!}</div>
                                                    <div class="tour-footer flex space-x-2 mt-0">
                                                        <a href="{{ route('tour.tourvisit', [$use['slug'] ?? '']) }}"
                                                            class="flex-1 bg-[#FF7722] text-white text-center py-2 rounded-md font-bold">
                                                            {{ translate('book_Now') }}
                                                        </a>
                                                        <button class="view-toggle-btn w-10 h-10 flex items-center justify-center bg-gray-100 rounded-md border border-gray-300"
                                                            onclick="toggleView(this)" title="View More">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
                                                    @else
                                                    <!-- Book now -->
                                                    <div class="tour-footer">
                                                        <a href="{{ route('tour.tourvisit', [$use['slug'] ?? '']) }}">{{ translate('book_Now') }}</a>
                                                    </div>
                                                    @endif
                                                </div>
                                                @endforeach
                    </div>
                    @endif
                    <script>
                        function toggleView(button) {
                            const card = button.closest('.tour-card');
                            const extraInfo = card.querySelector('.extra-info');

                            if (extraInfo.classList.contains('hidden')) {
                                extraInfo.classList.remove('hidden');
                                button.title = "View Less";
                                button.innerHTML = "<i class='fa fa-eye-slash'></i>";
                            } else {
                                extraInfo.classList.add('hidden');
                                button.title = "View More";
                                button.innerHTML = "<i class='fa fa-eye'></i>";
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<style>
    .clamp-2-lines {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
</script>
<!-- <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script> -->
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
<script>
    // $(".search_key").keyup(function() {
    //     var name = $(".search_key").val();

    //     if (name.length > 3) {
    //         // Show loader
    //         $("#loader").show();

    //         $.ajax({
    //             url: "{{ url('api/v1/tour/search') }}",
    //             data: {
    //                 name: name,
    //                 role: "web",
    //                 "lang": "{{ str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale()) }}",
    //             },
    //             dataType: "json",
    //             type: "post",
    //             success: function(response) {
    //                 $("#loader").hide();

    //                 // Check if results were found
    //                 if (response.status === 1) {
    //                     var resultHtml = response.data;
    //                     $("#search-results").html(resultHtml);
    //                 } else {
    //                     $("#search-results").html(
    //                         '<li class="list-group-item">No Results Found</li>');
    //                 }
    //             },
    //             error: function() {
    //                 $("#loader").hide(); // Hide loader on error
    //                 $("#search-results").html('<li class="list-group-item">Tour Not Available. Try another destination</li>');
    //             }
    //         });
    //     } else {
    //         $("#search-results").html('');
    //     }
    // });
</script>

<script>
    // $(document).ready(function() {
    //     var filterList = {
    //         init: function() {
    //             $('#portfoliolist').mixItUp({
    //                 selectors: {
    //                     target: '.tour-card',
    //                     filter: '.filter'
    //                 },
    //                 load: {
    //                     filter: '.all_tours'
    //                 }
    //             });
    //         }
    //     };

    //     filterList.init();
    //     $('input[type="search"]').on('keyup', function() {
    //         var searchText = $(this).val().toLowerCase();
    //         var activeCategory = $('.filter.active').data('filter');
    //         var found = false; // Track if any matching tour is found

    //         $('#portfoliolist .tour-card').each(function() {
    //             var matchesSearch = $(this).find('.tour-title').text().toLowerCase().indexOf(
    //                 searchText) > -1;
    //             var matchesCategory = activeCategory === '.all_tours' || $(this).hasClass(
    //                 activeCategory.replace('.', ''));

    //             if (matchesSearch && matchesCategory) {
    //                 $(this).show();
    //                 found = true;
    //             } else {
    //                 $(this).hide();
    //             }
    //         });

    //         toggleNoTourMessage(found);
    //     });

    //     // Filter click event
    //     $('.filter').on('click', function() {
    //         $('.filter').removeClass('active');
    //         $('.filter_state_name').removeClass('active');
    //         $(this).addClass('active');

    //         var activeCategory = $(this).data('filter');
    //         var searchText = $('input[type="search"]').val().toLowerCase();
    //         var found = false;

    //         $('#portfoliolist .tour-card').each(function() {
    //             var matchesSearch = $(this).find('.tour-title').text().toLowerCase().indexOf(searchText) > -1;
    //             var matchesCategory = activeCategory === '.all_tours' || $(this).hasClass(
    //                 activeCategory.replace('.', ''));

    //             if (matchesSearch && matchesCategory) {
    //                 $(this).show();
    //                 found = true;
    //             } else {
    //                 $(this).hide();
    //             }
    //         });
    //         toggleNoTourMessage(found);
    //     });

    //     $('.filter_state_name').on('click', function() {
    //         $('.filter_state_name').removeClass('active');
    //         $(this).addClass('active');

    //         var activeCategory = $(this).data('filter');
    //         var activeCategory2 =
    //             `${activeCategory}_${($('.filter.active').data('filter')).replace('.', '')}`;
    //         var searchText = $('input[type="search"]').val().toLowerCase();
    //         var found = false;

    //         $('#portfoliolist .tour-card').each(function() {
    //             var matchesSearch = $(this).find('.tour-title').text().toLowerCase().indexOf(
    //                 searchText) > -1;
    //             var matchesCategory = activeCategory2 === '.all_tours' || $(this).hasClass(
    //                 activeCategory2.replace('.', ''));

    //             if (matchesSearch && matchesCategory) {
    //                 $(this).show();
    //                 found = true;
    //             } else {
    //                 $(this).hide();
    //             }
    //         });

    //         toggleNoTourMessage(found);
    //     });

    //     // Function to toggle "No Tours Available" message
    //     function toggleNoTourMessage(found) {
    //         if (!found) {
    //             if ($('#no-tour-message').length === 0) {
    //                 $('#portfoliolist').append(
    //                     '<div id="no-tour-message" class="col-12 text-center my-3 text-danger">No Tours Available</div>'
    //                 );
    //             }
    //         } else {
    //             $('#no-tour-message').remove();
    //         }
    //     }
    // });
    const $cards = $('#portfoliolist .tour-card');
    const originalOrder = $cards.clone();
    $(document).ready(function() {
        const $search = $('.search_key');
        // Event listeners
        $search.on('keyup', applyFilters);
        $('.filter').on('click', function() {
            $('.filter').removeClass('active');
            $('.filter_state_name').removeClass('active');
            $(this).addClass('active');
            applyFilters();
        });
        $('.filter_state_name').on('click', function() {
            $('.filter_state_name').removeClass('active');
            $(this).addClass('active');
            applyFilters();
        });
        $('input[name="priceType"], input[name="priceSort"], input[name="filterplan"]').on('change', applyFilters);
        // applyFilters();
    });

    function toggleNoTourMessage(found) {
        if (!found) {
            if ($('#no-tour-message').length === 0) {
                $('#portfoliolist').append(
                    '<div id="no-tour-message" class="col-12 text-center my-3 text-danger">No Tours Available</div>'
                );
            }
        } else {
            $('#no-tour-message').remove();
        }
    }

    function applyFilters() {
        const searchText = ($('.search_key').val() ?? '').toString().toLowerCase().trim();
        const activeCategory = $('.filter.active').data('filter') || '.all_tours';
        const activeState = $('.filter_state_name.active').data('filter') || '';
        const priceType = $('input[name="priceType"]:checked').val();
        const Filterplan = $('input[name="filterplan"]:checked').val();
        const sortType = $('input[name="priceSort"]:checked').val();

        const minPrice = parseFloat($('#rangeMin').val()) || 0;
        const maxPrice = parseFloat($('#rangeMax').val()) || Infinity;

        let $cards = originalOrder.clone();

        $cards = $cards.filter(function() {
            const $card = $(this);

            const displayTitle = $card.find('.tour-title').text().toLowerCase();
            const rawTitle = $card.find('span.card-title').text().toLowerCase();

            const matchesSearch =
                searchText === '' ||
                displayTitle.includes(searchText) ||
                rawTitle.includes(searchText);

            const matchesCategory =
                activeCategory === '.all_tours' ||
                $card.hasClass(activeCategory.replace('.', ''));

            const matchesState =
                activeState === '' || $card.hasClass(activeState.replace('.', ''));

            return matchesSearch && matchesCategory && matchesState;
        });

        // ✅ 2. Price Type Filter
        if (priceType) {
            $cards = $cards.filter(function() {
                const type = $(this).find('.tour-cab-person').text().trim();
                return type === priceType;
            });
        }

        if (Filterplan) {
            $cards = $cards.filter(function() {
                const type = $(this).find('.ribbon').text().trim();
                return type === Filterplan;
            });
        }

        $cards = $cards.filter(function() {
            const txt = $(this).find('.tour-price .current').text() || '0';
            const price = parseFloat(txt.replace(/[^0-9.-]+/g, '')) || 0;
            return price >= minPrice && price <= maxPrice;
        });

        if (sortType === 'lowtohigh' || sortType === 'hightolow') {
            $cards = $cards.sort(function(a, b) {
                const getPrice = el => {
                    const txt = $(el).find('.tour-price .current').text() || '0';
                    const num = parseFloat(txt.replace(/[^0-9.-]+/g, ''));
                    return isNaN(num) ? 0 : num;
                };
                const pa = getPrice(a);
                const pb = getPrice(b);
                return sortType === 'lowtohigh' ? pa - pb : pb - pa;
            });
        }

        // ✅ 4. Update the grid
        $('#portfoliolist .grid').html($cards);

        // ✅ 5. No tours found message
        toggleNoTourMessage($cards.length > 0);
    }

    $('#minPrice, #maxPrice').on('input', function() {
        applyFilters();
    });
    // $(document).ready(function() {
    //     var filterList = {
    //         init: function() {
    //             $('#portfoliolist').mixItUp({
    //                 selectors: {
    //                     target: '.tour-card',
    //                     filter: '.filter'
    //                 },
    //                 load: {
    //                     filter: '.all_tours'
    //                 }
    //             });
    //         }
    //     };

    //     filterList.init();

    //     // Search filter function
    //     $('input[type="search"]').on('keyup', function() {
    //         var searchText = $(this).val().toLowerCase();
    //         var activeCategory = $('.filter.active').data('filter');

    //         $('#portfoliolist .tour-card').each(function() {
    //             var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1;

    //             // Adjusted logic to match dynamic class names
    //             var matchesCategory = activeCategory === '.all_tours' || $(this).hasClass(activeCategory.replace('.', ''));

    //             $(this).toggle(matchesSearch && matchesCategory);
    //         });
    //     });

    //     // Filter click event
    //     $('.filter').on('click', function() {
    //         $('.filter').removeClass('active');
    //         $('.filter_state_name').removeClass('active');
    //         $(this).addClass('active');

    //         var activeCategory = $(this).data('filter');
    //         var searchText = $('input[type="search"]').val().toLowerCase();

    //         $('#portfoliolist .tour-card').each(function() {
    //             var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1;
    //             var matchesCategory = activeCategory === '.all_tours' || $(this).hasClass(activeCategory.replace('.', ''));
    //             console.log(activeCategory);
    //             $(this).toggle(matchesSearch && matchesCategory);
    //         });
    //     });


    //     // $('.filter_state_name').on('click', function() {
    //     //     $('.filter_state_name').removeClass('active');
    //     //     $(this).addClass('active');

    //     //     var activeCategory = $(this).data('filter');
    //     //     var activeCategory2 = `${activeCategory}_${($('.filter.active').data('filter')).replace('.', '')}`;
    //     //     var searchText = $('input[type="search"]').val().toLowerCase();

    //     //     $('#portfoliolist .tour-card').each(function() {
    //     //         var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1;
    //     //         var matchesCategory = activeCategory2 === '.all_tours' || $(this).hasClass(activeCategory2.replace('.', ''));

    //     //         $(this).toggle(matchesSearch && matchesCategory);
    //     //     });
    //     // });
    //     $('.filter_state_name').on('click', function() {
    //         $('.filter_state_name').removeClass('active');
    //         $(this).addClass('active');

    //         var activeCategory = $(this).data('filter');
    //         var activeCategory2 = `${activeCategory}_${($('.filter.active').data('filter')).replace('.', '')}`;
    //         var searchText = $('input[type="search"]').val().toLowerCase();
    //         var found = false; // Variable to track if any matching tours are found

    //         $('#portfoliolist .tour-card').each(function() {
    //             var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1;
    //             var matchesCategory = activeCategory2 === '.all_tours' || $(this).hasClass(activeCategory2.replace('.', ''));

    //             if (matchesSearch && matchesCategory) {
    //                 $(this).show();
    //                 found = true;
    //             } else {
    //                 $(this).hide();
    //             }
    //         });

    //         // Check if any tour is visible, if not, show a message
    //         if (!found) {
    //             if ($('#no-tour-message').length === 0) {
    //                 $('#portfoliolist').append('<div id="no-tour-message" class="col-12 text-center my-3">No Tours Available</div>');
    //             }
    //         } else {
    //             $('#no-tour-message').remove();
    //         }
    //     });

    // });

    // $(document).ready(function() {
    //     var filterList = {
    //         init: function() {
    //             $('#portfoliolist').mixItUp({
    //                 selectors: {
    //                     target: '.tour-card',
    //                     filter: '.filter'
    //                 },
    //                 load: {
    //                     filter: '.all_tours'
    //                 }
    //             });
    //         }
    //     };

    //     filterList.init();

    //     // Search filter function
    // $('input[type="search"]').on('keyup', function() {
    //     var searchText = $(this).val().toLowerCase();
    //     var activeCategory = $('.filter.active').data('filter');

    //     $('#portfoliolist .tour-card').each(function() {
    //         var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1;
    //         var matchesCategory = $(this).is(activeCategory);

    //         $(this).toggle(matchesSearch && matchesCategory);
    //     });
    // });


    //     $('.filter').on('click', function() {
    //         $('.filter').removeClass('active');
    //         $(this).addClass('active');

    //         var activeCategory = $(this).data('filter');
    //         var searchText = $('input[type="search"]').val().toLowerCase();
    //         $('#portfoliolist .tour-card').each(function() {
    //             var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1;
    //             var matchesCategory = $(this).is(activeCategory);

    //             $(this).toggle(matchesSearch && matchesCategory);
    //         });
    //     });
    // });

    function toggleSubcategories(type, name) {
        //     //$('.head-title-name-chnage').text(name.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase()));
        //    // if (type === 'cities_tour') {
        //         //$('.citie_tour-none').removeClass('d-none');
        //        // $('.cities_tour-none').removeClass('d-none');
        //    // } else if (type === 'citie_tour') {
        //        // $('.citie_tour-none').removeClass('d-none');
        //         //$('.cities_tour-none').removeClass('d-none');
        //     //} else {
        //     // $('.citie_tour-none').addClass('d-none');
        //     //$('.cities_tour-none').addClass('d-none');
        //     //}
    }

    // function new_chanage(){
    //     setTimeout(() => $('.category_in_cities').addClass('active'), 100);
    // }

    $('#filterToggle').on('click', function(e) {
        e.stopPropagation();
        $('#filterTooltip').toggleClass('active');
    });


    $(document).on('click', function(e) {
        if (!$(e.target).closest('#filterTooltip, #filterToggle').length) {
            $('#filterTooltip').removeClass('active');
        }
    });
</script>
<script>
    const rangeMin = document.getElementById('rangeMin');
    const rangeMax = document.getElementById('rangeMax');
    const rangeMinVal = document.getElementById('rangeMinVal');
    const rangeMaxVal = document.getElementById('rangeMaxVal');

    // Create dynamic colored track
    const rangeSlider = document.querySelector('.range-slider');
    const rangeTrack = document.createElement('div');
    rangeTrack.className = 'range-track';
    rangeSlider.appendChild(rangeTrack);

    function updateRange() {
        let min = parseInt(rangeMin.value);
        let max = parseInt(rangeMax.value);

        // Prevent overlap
        if (min > max - 1000) {
            rangeMin.value = max - 1000;
            min = max - 1000;
        }

        const percent1 = (min / rangeMin.max) * 100;
        const percent2 = (max / rangeMax.max) * 100;

        rangeTrack.style.left = percent1 + '%';
        rangeTrack.style.width = (percent2 - percent1) + '%';
        rangeMinVal.textContent = `₹${min.toLocaleString()}`;
        rangeMaxVal.textContent = `₹${max.toLocaleString()}`;
        applyFilters();
    }

    rangeMin.addEventListener('input', updateRange);
    rangeMax.addEventListener('input', updateRange);

    function setDynamicRangeValues() {
        const prices = $('.tour-price .amount-total-amount').map(function() {
            const txt = $(this).text() || '0';
            const num = parseFloat(txt.replace(/[^0-9.-]+/g, ''));
            return isNaN(num) ? 0 : num;
        }).get();
        if (prices.length === 0) return;
        const minValue = 0;
        const maxValue = Math.max(...prices) + 100;
        $('#rangeMin, #rangeMax').attr({
            min: minValue,
            max: maxValue,
            step: 100
        });
        $('#rangeMin').val(minValue);
        $('#rangeMax').val(maxValue);
        $('#rangeMinVal').text(`₹${minValue.toLocaleString()}`);
        $('#rangeMaxVal').text(`₹${maxValue.toLocaleString()}`);
        updateRange();
       
    }

    $(document).ready(function() {
        setDynamicRangeValues();
    });
</script>
@endpush