@extends('layouts.front-end.app')
@section('title', translate("Welcome to Mahakal.com, World's Largest Devotional Platform"))
@php
use App\Utils\Helpers;
use function App\Utils\getNextPoojaDay;
use function App\Utils\getNextChadhavaDay;
use function App\Utils\displayStarRating;

@endphp
@push('css_or_js')
<meta name="description" content="Welcome to Mahakal.com, World's trusted platform for online darshan, aarti booking, puja services, puja items, and spiritual shopping. Buy online with trust and spiritual assurance.">
<meta property="og:image"
    content="{{ theme_asset(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="og:title" content="Welcome To {{ $web_config['name']->value }} Home" />
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<meta property="twitter:card"
    content="{{ theme_asset(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
<meta property="twitter:title" content="Welcome To {{ $web_config['name']->value }} Home" />
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:description"
    content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}" />
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/rSliders/responsiveslides.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/rSliders/demo.css') }}">
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/animationbutton.css') }}">
<style>
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    .gold {
        color: #fe9802;
    }

    .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    .city-list {
        position: absolute;
        z-index: 99;
        background-color: #fff;
        border: 1px solid #ddd;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        /* Initially hidden */
    }

    .two-lines-only {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.5em;
        min-height: 3em;
    }

    .newpadding {
        padding: 5px 1.25rem 1.25rem;
    }

    .one-lines-only {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .Stars {
        --star-size: 20px;
        --star-color: #000;
        --star-background: #fc0;
        --percent: calc(var(--rating) / 5 * 100%);
        display: inline-block;
        font-size: var(--star-size);
        font-family: Times; // make sure ★ appears correctly
        line-height: 1;

        &::before {
            content: '★★★★★';
            letter-spacing: 3px;
            background: linear-gradient(90deg, var(--star-background) var(--percent), var(--star-color) var(--percent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    }

    /* read more */
    .review-text {
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Show 2 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .review-text.expanded {
        -webkit-line-clamp: unset; /* Remove clamp */
        max-height: none;
    }
</style>
@endpush
@section('content')
@php($decimalPointSettings = !empty(getWebConfig(name: 'decimal_point_settings')) ? getWebConfig(name: 'decimal_point_settings') : 0)
<div class="__inline-61">
    <!-- slider section -->
    <section class="slider-section">
        <div class="">
            <div class="row">
                <div class="callbacks_container">
                    <ul class="rslides" id="slider4">
                        @if (count($banners) > 0)
                        @foreach ($banners as $banner)
                        <li>
                            <a class="d-block" href="{{ $banner['url'] }}">
                                <img

                                    src="{{ asset('storage/app/public/banner/' . $banner['photo']) }}"
                                    alt="Mahakal Banners"
                                    onerror="this.onerror=null; this.src='{{ asset('public/assets/default-banner.png') }}';" />
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- end slider section  -->
    <!-- rashi section -->
    <section class="rashi-section">
    <div class="container-fluid">
        <div class="row align-items-center g-0">
            
            <!-- Left: Title -->
            <div class="col-md-3 col-12">
                <div class="rashi-title-box" style="background-image:url('./public/assets/rashifal-background-home.jpg'); back">
                    <h2 class="rashi-title mb-1">
                        <span>{{ translate('rashi_phal') }}</span>
                    </h2>
                    <div class="rashi-title-line"></div>
                </div>
            </div>

            <!-- Right: Slider -->
            <div class="col-md-9 col-12">
                <div class="owl-carousel owl-theme rashis-slider">
                    @foreach ($rashis as $rashi)
                    <div class="__cate-item">
                        <a href="{{ route('rashi-detail', [$rashi->slug]) }}">
                            <div class="__img" style="height:5rem;">
                                <img alt="{{ $rashi->name }}"
                                    src="{{ getValidImage(path: "storage/app/public/rashi/$rashi->image", type: 'rashi') }}">
                            </div>
                            <p class="text-center fs-13 font-semibold mt-2">{{ $rashi->name }}</p>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>

<style>
.rashi-section {
    padding-top: 0;
    padding-bottom: 0;
}
.rashi-title-box {
    background-image: ('./public/assets/rashifal-background-home.jpg');
    padding: 2rem;
    background-size:cover;
    color: #fff;
    border-radius: 8px;
    text-align: center;
}
.rashi-title {
    font-size: 1.4rem;
    font-weight: bold;
    text-shadow: 2px 4px 3px black;
    color: #fff;
}
.rashi-title-line {
    height: 3px;
    width: 40px;
    background: #fff;
    margin: 3px auto 0;
    border-radius: 2px;
}
.__cate-item {
    text-align: center;
    padding: 5px; /* spacing between items */
}
.__img img {
    border-radius: 8px;
    transition: transform 0.3s ease;
    width: 100%;
}
.__img img:hover {
    transform: scale(1.05);
}

/* Owl Carousel spacing fix */
.rashis-slider .owl-stage {
    display: flex;
}
.rashis-slider .owl-item {
    padding: 0 8px; /* space between items */
}

</style>

<!-- Trial for Slider -->

    <!-- end rashi section  -->
    <!--start vedik jyotish-->
    <section class="vedik-jyotish">
        <div class="container-fluid rtl px-0 px-md-3">
            <div class="__inline-62 pt-3">
                <div class="feature-product-title mt-0 mb-4">
                    {{ translate('astrology') }}
                    <h4 class="mt-2 height-10">
                        <span class="divider">&nbsp;</span>
                    </h4>
                </div>
                <div class="row g-3 mx-max-md-0">
                    <div class="col-lg-6 px-max-md-0">
                        <div class="feature-product-title mt-0 new-title">
                            {{ translate('today_panchang') }}
                        </div>
                        <div class="card card __shadow">
                            <div class="card-body p-xl-35">
                                <!--<div class="row d-flex justify-content-between mx-1 mb-3">-->
                                <!--<div class="text-center">-->
                                <!--   <h3 class="font-bold pl-1">Vedik Jyotishi</h3>-->
                                <!--</div>-->
                                <!--</div>-->
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <a href="{{ route('panchang') }}"><img
                                                src="{{ theme_asset(path: 'public/assets/front-end/img/panchangg.jpg') }}"
                                                class="img-fluid border-rad"></a>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <a href="{{ route('chaughadiya') }}"><img
                                                src="{{ theme_asset(path: 'public/assets/front-end/img/chaughadiya.jpg') }}"
                                                class="img-fluid border-rad"></a>
                                    </div>
                                    @foreach ($kundaliPdf as $key => $pdf)
                                    <div class="col-lg-6 mb-4">
                                        <a href="{{ route('kundali-pdf.information', ['type'=>$pdf['name'],'id'=>$pdf['id']]) }}"><img
                                                src="{{ asset('storage/app/public/birthjournal/image/' . $pdf['image']) }}"
                                                class="img-fluid border-rad"></a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 px-max-md-0">
                        <div class="feature-product-title mt-0 new-title">
                            {{ translate('view_free_horoscope') }}
                        </div>
                        <div class="card card __shadow">
                            <div class="card-body p-xl-35">
                                <!--<div class="row d-flex justify-content-between mx-1 mb-3">-->
                                <!--<div class="text-center">-->
                                <!--   <span class="font-bold pl-1">Kundali</span>-->
                                <!--</div>-->
                                <!--</div>-->
                                <ul class="nav nav-pills nav-justified" id="linxea-avenir" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link font-weight-bold active" id="kundali-tab" data-toggle="tab"
                                            href="#kundali" role="tab" aria-controls="first"
                                            aria-selected="true" style="color: #222 !important">कुण्डली</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link font-weight-bold text-dark" id="kundali-milan-tab"
                                            data-toggle="tab" href="#kundali-milan" role="tab"
                                            aria-controls="second" aria-selected="false"
                                            style="color: #222 !important">
                                            कुण्डली मिलान</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="kundali" role="tabpanel"
                                        aria-labelledby="kundali-tab">
                                        <form class="shadow-lg p-4" action="{{ route('kundali') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="latitude" id="kundali-latitude">
                                            <input type="hidden" name="longitude" id="kundali-longitude">
                                            <input type="hidden" name="timezone" id="kundali-timezone">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <!-- <label>Full Name</label> -->
                                                        <input class="form-control" id="username" value=""
                                                            type="text" name="username" required=""
                                                            autocomplete="off" placeholder="पूरा नाम दर्ज करें">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <input class="form-control hasDatepicker" id="datepicker"
                                                            type="text" name="dob" required=""
                                                            autocomplete="off" placeholder="जन्म तारीख दर्ज करें">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <input class="form-control" id="timepicker" type="text"
                                                            name="time" required="" autocomplete="off"
                                                            placeholder="जन्म समय दर्ज करें">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <select name="country" id="country"
                                                            onchange="countrychange()" class="form-control">
                                                            @foreach ($country as $countryName)
                                                            <option value="{{ $countryName->name }}"
                                                                {{ $countryName->name == 'India' ? 'selected' : '' }}>
                                                                {{ $countryName->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <input class="form-control" type="text" id="places"
                                                            value="" name="places" required=""
                                                            autocomplete="off" placeholder="जन्म स्थान दर्ज करें">
                                                        <div class="city-list">
                                                            <ul id="citylist" class="list-group">
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row text-center d-block mt-2">
                                                <button class="btn btn--primary btn-block btn-shadow" type="submit">
                                                    <i class="czi-arrow-left-circle mr-2 ml-n1"></i>
                                                    कुण्डली देखे
                                                </button>
                                                <div class="col-12 mt-3">
                                                    <div class="row">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="kundali-milan" role="tabpanel"
                                        aria-labelledby="kundali-milan-tab">
                                        <form class="shadow-lg p-4" action="{{ route('kundali.milan') }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="male_latitude" id="male-latitude">
                                            <input type="hidden" name="male_longitude" id="male-longitude">
                                            <input type="hidden" name="male_timezone" id="male-timezone">
                                            <input type="hidden" name="female_latitude" id="female-latitude">
                                            <input type="hidden" name="female_longitude" id="female-longitude">
                                            <input type="hidden" name="female_timezone" id="female-timezone">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h5 class="gender-title">Male <i class="fa fa-male"></i>
                                                                पुरुष
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <!-- <label>Full Name</label> -->
                                                                <input class="form-control" id="m_name"
                                                                    value="" type="text" name="male_name"
                                                                    required="" autocomplete="off"
                                                                    placeholder="पूरा नाम दर्ज करें">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <input class="form-control hasDatepicker"
                                                                    id="male-datepicker" type="text"
                                                                    name="male_dob" required="" autocomplete="off"
                                                                    placeholder="जन्म तारीख दर्ज करें">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <input class="form-control" id="male-timepicker"
                                                                    type="text" name="male_time" required=""
                                                                    autocomplete="off"
                                                                    placeholder="जन्म समय दर्ज करें">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <select name="male_country" id="male-country"
                                                                    onchange="maleCountryChange()"
                                                                    class="form-control">
                                                                    @foreach ($country as $countryName)
                                                                    <option value="{{ $countryName->name }}"
                                                                        {{ $countryName->name == 'India' ? 'selected' : '' }}>
                                                                        {{ $countryName->name }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <input class="form-control" type="text"
                                                                    id="male-place" name="male_place" required=""
                                                                    autocomplete="off"
                                                                    placeholder="जन्म स्थान दर्ज करें">
                                                                <ul id="male-city-list" class="list-group"
                                                                    style="position: absolute; z-index:1">
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h5 class="gender-title">Female <i
                                                                    class="fa fa-female"></i> महिला </h5>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <!-- <label>Full Name</label> -->
                                                                <input class="form-control" id="female-name"
                                                                    value="" type="text" name="female_name"
                                                                    required="" autocomplete="off"
                                                                    placeholder="पूरा नाम दर्ज करें">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <input class="form-control hasDatepicker"
                                                                    id="female-datepicker" type="text"
                                                                    name="female_dob" required=""
                                                                    autocomplete="off"
                                                                    placeholder="जन्म तारीख दर्ज करें">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <input class="form-control" id="female-timepicker"
                                                                    type="text" name="female_time" required=""
                                                                    autocomplete="off"
                                                                    placeholder="जन्म समय दर्ज करें">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <select name="female_country" id="female-country"
                                                                    onchange="femaleCountryChange()"
                                                                    class="form-control">
                                                                    @foreach ($country as $countryName)
                                                                    <option value="{{ $countryName->name }}"
                                                                        {{ $countryName->name == 'India' ? 'selected' : '' }}>
                                                                        {{ $countryName->name }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <input class="form-control" type="text"
                                                                    id="female-place" value=""
                                                                    name="female_place" required=""
                                                                    autocomplete="off"
                                                                    placeholder="जन्म स्थान दर्ज करें">
                                                                <ul id="female-city-list" class="list-group"
                                                                    style="position: absolute; z-index:1">
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row text-center d-block mt-2">
                                                <button class="btn btn--primary btn-block btn-shadow" type="submit">
                                                    <i class="czi-arrow-left-circle mr-2 ml-n1"></i>
                                                    मिलान देखे
                                                </button>
                                                <div class="col-12 mt-3">
                                                    <div class="row">
                                                    </div>
                                                </div>
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
    </section>
    <!--end vedik jyotish-->
    {{-- start shop category --}}
    @if ($categories->count() > 0)
    <section class="py-2 rtl">
        <div class="container-fluid">
            <div class="__inline-62">
                <div class="card __shadow h-100 max-md-shadow-0">
                    <div class="card-body">
                        <div class="feature-product-title mt-0">
                            {{ translate('shopping_categories') }}
                            <h4 class="mt-2 height-10">
                                <span class="divider"> </span>
                            </h4>
                        </div>
                        <div class="flash-deal-view-all-web row d-flex justify-content-end mb-3">
                            <a class="text-capitalize view-all-text web-text-primary"
                                href="{{ route('categories') }}">{{ translate('view_all') }}
                                <i
                                    class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1' }}"></i>
                            </a>
                        </div>
                        <div class="d-none d-md-block">
                            <div class="owl-carousel owl-theme px-4 category-slider">
                                @foreach ($categories as $key => $category)
                                @if ($category['id'] != 33 && $category['id'] != 39)
                                {{-- @if ($key < 10) --}}
                                <div class="text-center __m-5px __cate-item">
                                    <a
                                        href="{{ route('products-slug', ['slug' => $category['slug'], 'data_from' => 'category', 'page' => 1]) }}">
                                        <div class="__img">
                                            <img alt="{{ $category->name }}"
                                                src="{{ getValidImage(path: 'storage/app/public/category/' . $category->icon, type: 'category') }}">
                                        </div>
                                        <p class="text-center fs-13 font-semibold mt-2">
                                            {{ Str::limit($category->name, 12) }}
                                        </p>
                                    </a>
                                </div>
                                {{-- @endif --}}
                                @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="d-md-none">
                            {{--
                            <div class="owl-theme owl-carousel categories--slider mt-3">
                                --}}
                                    <div class="owl-carousel owl-theme px-4 rashis-slider">
                                        @foreach ($categories as $key => $category)
                                            {{-- @if ($key < 10) --}}
                                            <div class="text-center m-0 __cate-item w-100">
                                                <a
                                                    href="{{ route('products-slug', ['slug' => $category['slug'], 'data_from' => 'category', 'page' => 1]) }}">
                                                    <div class="__img mw-100 h-auto">
                                                        <img alt="{{ $category->name }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/category/' . $category->icon, type: 'category') }}">
                                                    </div>
                                                    <p class="text-center small mt-2">
                                                        {{ Str::limit($category->name, 12) }}
                                                    </p>
                                                </a>
                                            </div>
                                            {{-- @endif --}}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                                    
                    </div>
                </div>
            </section>
        @endif
        {{-- end shop category --}}
        <!-- astrology_counseling section -->
        <section class="overflow-hidden py-3">
            <div class="container-fluid px-0 px-md-3">
                <div class="suggestion-section __inline-62 pt-3">
                    <div class="container">
                    <div class="feature-product-title mt-0">
                        {{ translate('astrology_counseling') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>
                    <div class="flash-deal-view-all-web row d-flex justify-content-end ">
                        <a class="text-capitalize view-all-text web-text-primary"
                            href="{{ route('counselling.astrology', ['type' => 'counselling']) }}">
                            View all
                            <i class="czi-arrow-right ml-1 mr-n1"></i>
                        </a>
                    </div>
                    <?php /*
                    <div class="row mx-max-md-0">
                        <div class="col-lg-4 px-max-md-0">
                            <div class="suggestion-card">
                                <div class="flash-deal-text web-text-primary">
                                    <img src="{{ asset('public/assets/front-end/img/jyotish-paramarsh.png') }}"
                                        alt="" class="img-fluid suggestion-img">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 px-max-md-0">
                            <div class="owl-theme owl-carousel parmarsh-slider">
                                @if (count($counsellings) > 0)
                                    @foreach ($counsellings as $counselling)
                                        @if ($counselling['sub_category_id'] == 40)
                                            <div class="product-single-hover shadow-none rtl">
                                                <div class="overflow-hidden position-relative">
                                                    <div class="inline_product clickable">
                                                        <a
                                                            href="{{ route('counselling.details', $counselling['slug']) }}">
                                                            <img src="{{ asset('storage/app/public/pooja/thumbnail/' . $counselling['thumbnail']) }}"
                                                                alt=""
                                                                style="object-fit: contain !important; height: 100% !important;">
                                                        </a>
                                                        <div class="quick-view">
                                                            <a class="btn-circle stopPropagation action-product-quick-view"
                                                                href="{{ route('counselling.details', $counselling['slug']) }}"
                                                                data-product-id="">
                                                                <i class="czi-eye align-middle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="single-product-details min-height-unset">
                                                        <!-- Star Rating -->
                                                        <div class="d-flex align-items-center">
                                                            {!! displayStarRating($counselling->review_avg_rating ?? 5) !!}
                                                            <span
                                                                class="ml-2">({{ number_format($counselling->review_avg_rating ?? 5, 1) }})</span>
                                                        </div>
                                                        <div>
                                                            <a href="{{ route('counselling.details', $counselling['slug']) }}"
                                                                class="text-capitalize fw-semibold">
                                                                {{ $counselling['name'] }}
                                                            </a>
                                                        </div>
                                                        <div class="justify-content-between">
                                                            <div class="product-price">
                                                                <del class="category-single-product-price color-danger"
                                                                    style="color: red !important">
                                                                    ₹{{ $counselling['counselling_main_price'] }}
                                                                </del>
                                                                <span class="text-accent text-dark">
                                                                    ₹{{ $counselling['counselling_selling_price'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <!-- Devotees Count -->
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                                    alt="Users" class="colored-icon"
                                                                    style="width: 24px; height: 24px; margin-right: 5px;">
                                                                <span
                                                                    class="pooja-calendar">1000q{{-- 10000 + $counselling->pooja_order_review_count --}}+
                                                                    Devotees</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    */ ?>
                    <!-- ======= Updated Slider NEWDESIGN ======= -->
                    <div class="container-fluid">
                        <div class="paramarsh-slider owl-carousel">
                            @foreach ($counsellings as $counselling)
                                @if ($counselling['sub_category_id'] == 40)
                                    <div class="paramarsh-card">
                                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                            <a href="{{ route('counselling.details', $counselling['slug']) }}">
                                                <img 
                                                    src="{{ asset('storage/app/public/pooja/thumbnail/' . $counselling['thumbnail']) }}"
                                                    style="width:100%; height:auto;"
                                                    alt="{{ $counselling['name'] }}"
                                                    class="card-img-top"
                                                    onerror="this.onerror=null; this.src='{{ asset('public/assets/default-image-all.png') }}';" />
                                            </a>
                                            <div class="card-body text-center ">
                                                <h6 class="fw-semibold mb-2 text-dark  clamp-2-lines">{{ $counselling['name'] }}</h6>

                                                <!-- Star Rating -->
                                                <div class="text-warning mb-1">
                                                    {!! displayStarRating($counselling->review_avg_rating ?? 5) !!}
                                                    <small class="text-muted">({{ number_format($counselling->review_avg_rating ?? 5, 1) }})</small>
                                                </div>

                                                <!-- Pricing -->
                                            <div class="price mb-2 align-items-baseline gap-2 text-center">
                                                    <span class="fw-bold text-dark" style="font-size: 1.3rem;">
                                                        <span style="font-size: 0.9rem; vertical-align: super;">₹</span>{{ $counselling['counselling_selling_price'] }}
                                                    </span>
                                                    <span class="text-muted" style="font-size: 0.85rem; text-decoration: line-through; ">
                                                        <span style="font-size: 0.85rem; color:red;">₹{{ $counselling['counselling_main_price'] }}</span>
                                                    </span>
                                                </div>
                                                <!-- Devotees -->
                                                <div class="text-muted" style="font-size: 0.85rem;">
                                                    <i class="tio-users"></i> 10000+ Devotees
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <style>
                        .paramarsh-card {
                            padding: 10px;
                            transition: transform 0.3s ease, box-shadow 0.3s ease;
                        }

                        .paramarsh-card:hover {
                            transform: scale(1.12);
                            z-index: 2;
                        }

                        .paramarsh-card .card {
                            border-radius: 16px;
                            overflow: hidden;
                        }

                        .card-img-top {
                            width: 100%;
                            height: 180px;
                            object-fit: cover;
                            border-top-left-radius: 1rem;
                            border-top-right-radius: 1rem;
                        }

                        .price del {
                            margin-right: 6px;
                            font-size: 0.9rem;
                            color: red;
                        }
                    </style>
                    <!-- ======= Updated Slider NEWDESIGN ends ======= -->
                </div>
                    </div>
            </div>
        </section>
        <!-- end astrology_counseling section  -->
        <!--start calculartor-section-->
        <section class="calculartor-section">
            <div class="container-fluid">
                <div class="__inline-62 pt-3">
                    <div class="container">
                    <div class="feature-product-title mt-0 mb-2">
                        {{ translate('calculator') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>
                    <div class="owl-carousel owl-theme p-2 calculator-slider">
                        @foreach ($calculators as $calculator)
                            <div class="text-center __m-5px __cate-item">
                                <a href="{{ route('calculator', [$calculator->slug]) }}">
                                    <div class="__img">
                                        <img alt="{{ $calculator->name }}"
                                            src="{{ getValidImage(path: "storage/app/public/calculator-img/$calculator->logo", type: 'calculator') }}">
                                    </div>
                                    <p class="text-center fs-13 font-semibold mt-2">{{ translate($calculator->name) }}</p>
                                </a>
                            </div>
                        @endforeach
                        {{-- <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('rashi_namakshar')])}}">
                                <div class="__img">
                                    <img alt="{{ translate('rashi_namakshar')}}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/dharmikproduct.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('rashi_namakshar')}}</p>
                            </a>
                        </div>
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('kalsarp_dosha')])}}">
                                <div class="__img">
                                    <img alt="{{ translate('kalsarp_dosha')}}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/birth-detail/kalsarp-dosh.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('kalsarp_dosha')}}</p>
                            </a>
                        </div>
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('manglik_dosha')])}}">
                                <div class="__img">
                                    <img alt="{{translate('manglik_dosha')}}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/birth-detail/mangal-dosh.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('manglik_dosha')}}</p>
                            </a>
                        </div>
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('pitra_dosha')])}}">
                                <div class="__img">
                                    <img alt="{{ translate('pitra_dosha') }}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/birth-detail/pitradosha.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('pitra_dosha')}}</p>
                            </a>
                        </div>
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('vimshottari_dasha')])}}">
                                <div class="__img">
                                    <img alt="{{ translate('vimshottari_dasha') }}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/birth-detail/vimshottari-dasha.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('vimshottari_dasha')}}</p>
                            </a>
                        </div>
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('mool_ank')])}}">
                                <div class="__img">
                                    <img alt="{{ translate('mool_ank') }}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/birth-detail/numerology-table.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('mool_ank')}}</p>
                            </a>
                        </div>
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('gem_suggestion')])}}">
                                <div class="__img">
                                    <img alt="{{ translate('gem_suggestion') }}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/birth-detail/gems-suggestion.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('gem_suggestion')}}</p>
                            </a>
                        </div>
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('rudraksha_suggestion')])}}">
                                <div class="__img">
                                    <img alt="{{ translate('rudraksha_suggestion') }}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/birth-detail/rudraksh-suggestion.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('rudraksha_suggestion')}}</p>
                            </a>
                        </div>
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{route('calculator', [translate('prayer_suggestion')])}}">
                                <div class="__img">
                                    <img alt="{{ translate('prayer_suggestion') }}"
                                    src="https://www.astrorobo.com/public/assets/front-end/img/birth-detail/puja-suggestion.png">
                                </div>
                                <p class="text-center fs-13 font-semibold mt-2">{{translate('prayer_suggestion')}}</p>
                            </a>
                        </div> --}}
                    </div>
                        </div>
                </div>
            </div>
        </section>
        <!--end calculartor-section-->
        {{-- start shop featured product --}}
        @if ($featured_products->count() > 0)
            <div class="container-fluid py-3 rtl px-0 px-md-3">
                <div class="__inline-62 pt-3">
                    <div class="container">
                    <div class="feature-product-title mt-0">
                        {{ translate('featured_products') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>
                    <div class="text-end px-3 d-none d-md-block">
                        <a class="text-capitalize view-all-text web-text-primary"
                            href="{{ route('products-slug', ['slug' => $category['slug'], 'data_from' => 'featured', 'page' => 1]) }}">
                            {{ translate('view_all') }}
                            <i
                                class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                        </a>
                    </div>
                    <div class="feature-product">
                        <div class="carousel-wrap p-1">
                            <div class="owl-carousel owl-theme" id="featured_products_list">
                                @foreach ($featured_products as $product)
                                    <div>
                                        @include('web-views.partials._feature-product', [
                                            'product' => $product,
                                            'decimal_point_settings' => $decimalPointSettings,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-center pt-2 d-md-none">
                            <a class="text-capitalize view-all-text web-text-primary"
                                href="{{ route('products-slug', ['slug' => $category['slug'], 'data_from' => 'featured', 'page' => 1]) }}">
                                {{ translate('view_all') }}
                                <i
                                    class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                            </a>
                        </div>
                    </div>
                                        </div>
                </div>
            </div>
        @endif
        {{-- end shop featured product --}}
        <!--auspicious_moment-->
        <section class="overflow-hidden py-3">
            <div class="container-fluid px-0 px-md-3">
                <div class="suggestion-section __inline-62 pt-3">
                    <div class="container">
                    <div class="feature-product-title mt-0">
                        {{ translate('auspicious_time') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>
                    <div class="flash-deal-view-all-web row d-flex justify-content-end mb-3">
                        <a class="text-capitalize view-all-text web-text-primary"
                            href="{{ route('counselling.astrology', ['type' => 'muhurat']) }}">
                            View all
                            <i class="czi-arrow-right ml-1 mr-n1"></i>
                        </a>
                    </div>
                    <?php /*
                    <div class="row g-3 mx-max-md-0">
                        <div class="col-lg-8 px-max-md-0">
                            <div class="owl-theme owl-carousel parmarsh-slider">
                                @if (count($counsellings) > 0)
                                    @foreach ($counsellings as $counselling)
                                        @if ($counselling['sub_category_id'] == 41)
                                            <div class="product-single-hover shadow-none rtl">
                                                <div class="overflow-hidden position-relative">
                                                    <div class="inline_product clickable">
                                                        <a
                                                            href="{{ route('counselling.details', $counselling['slug']) }}">
                                                            <img src="{{ asset('storage/app/public/pooja/thumbnail/' . $counselling['thumbnail']) }}"
                                                                alt=""
                                                                style="object-fit: contain !important; height: 100% !important;">
                                                        </a>
                                                        <div class="quick-view">
                                                            <a class="btn-circle stopPropagation action-product-quick-view"
                                                                href="{{ route('counselling.details', $counselling['slug']) }}"
                                                                data-product-id="">
                                                                <i class="czi-eye align-middle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="single-product-details min-height-unset">
                                                        <!-- Star Rating -->
                                                        <div class="d-flex align-items-center">
                                                            {!! displayStarRating($counselling->review_avg_rating ?? 5) !!}
                                                            <span
                                                                class="ml-2">({{ number_format($counselling->review_avg_rating ?? 5, 1) }}/5)</span>
                                                        </div>
                                                        <div>
                                                            <a href="{{ route('counselling.details', $counselling['slug']) }}"
                                                                class="text-capitalize fw-semibold">
                                                                {{ $counselling['name'] }}
                                                            </a>
                                                        </div>
                                                        <div class="justify-content-between">
                                                            <div class="product-price">
                                                                <del class="category-single-product-price color-danger"
                                                                    style="color: red !important">
                                                                    ₹{{ $counselling['counselling_main_price'] }}
                                                                </del>
                                                                <span class="text-accent text-dark">
                                                                    ₹{{ $counselling['counselling_selling_price'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <!-- Devotees Count -->
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                                    alt="Users" class="colored-icon"
                                                                    style="width: 24px; height: 24px; margin-right: 5px;">
                                                                <span
                                                                    class="pooja-calendar">{{ 10000 + $counselling->pooja_order_review_count }}+
                                                                    Devotees</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 px-max-md-0">
                            <div class="suggestion-card">
                                <div class="flash-deal-text web-text-primary">
                                    <img src="{{ asset('public/assets/front-end/img/shubhmuhurat.png') }}" alt=""
                                        class="img-fluid suggestion-img">
                                </div>
                            </div>
                        </div>
                    </div>
                    */ ?>
                    <!-- ======= Updated Slider NEWDESIGN ======= -->
                                <!-- ======= Parmarsh Slider 2: Sub-category ID 41 ======= -->
                    <div class="row g-3 mx-max-md-0">
                        <div class="col-lg-12 px-max-md-0">
                            <div class="owl-carousel paramarsh-slider">
                                @foreach ($counsellings as $counselling)
                                    @if ($counselling['sub_category_id'] == 41)
                                        <div class="paramarsh-card">
                                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                                <a href="{{ route('counselling.details', $counselling['slug']) }}">
                                                    <img src="{{ asset('storage/app/public/pooja/thumbnail/' . $counselling['thumbnail']) }}"
                                                        alt="{{ $counselling['name'] }}"
                                                        class="card-img-top"
                                                        style="width:100%; height:auto;"
                                                        onerror="this.onerror=null; this.src='{{ asset('public/assets/default-image-all.png') }}';" />
                                                </a>
                                                <div class="card-body text-center">
                                                    <h6 class="fw-semibold mb-2 text-dark clamp-2-lines">{{ $counselling['name'] }}</h6>

                                                    <!-- Star Rating -->
                                                    <div class="text-warning mb-1">
                                                        {!! displayStarRating($counselling->review_avg_rating ?? 5) !!}
                                                        <small class="text-muted">({{ number_format($counselling->review_avg_rating ?? 5, 1) }}/5)</small>
                                                    </div>

                                                
                                                    <!-- Pricing -->
                                            <div class="price mb-2 align-items-baseline gap-2 text-center">
                                                    <span class="fw-bold text-dark" style="font-size: 1.3rem;">
                                                        <span style="font-size: 0.9rem; vertical-align: super;">₹</span>{{ $counselling['counselling_selling_price'] }}
                                                    </span>
                                                    <span class="text-muted" style="font-size: 0.85rem; text-decoration: line-through;">
                                                        ₹<span style="font-size: 0.85rem;">{{ $counselling['counselling_main_price'] }}</span>
                                                    </span>
                                                </div>

                                                    <!-- Devotees -->
                                                    <!-- <div class="text-muted" style="font-size: 0.85rem;">
                                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                            alt="Users" class="me-1" style="width: 20px; height: 20px;">
                                                        {{ 10000 + $counselling->pooja_order_review_count }}+ Devotees
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- ======= Parmarsh Slider 2 ends ======= -->
                    <!-- ======= Updated Slider NEWDESIGN Ends ======= -->
                                        </div>
                </div>
            </div>
        </section>
        <!-- end auspicious_moment section  -->
        <!-- live-darshan section -->
        <!-- <section class="live-darshan-section">
                <div class="container-fluid">
                    <div class="__inline-62 pt-3">
                        <div class="feature-product-title mt-0 mb-2">
                            {{ translate('live_darshan') }}
                            <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                            </h4>
                        </div>
                        <div class="owl-carousel owl-theme px-4 pt-2 live-darshan">
                            <div class="product-single-hover shadow-none rtl">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="live-darshan-image-height"
                                            src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-video align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="single-product-details min-height-56">
                                        <div class="text-center">
                                            <a href="#" class="text-capitalize fw-semibold">
                                                काशी विश्वनाथ मंदिर
                                            </a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="live-darshan-image-height"
                                            src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/Jagannath-puri.jpg')) }}"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-video align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="single-product-details min-height-56">
                                        <div class="text-center">
                                            <a href="#" class="text-capitalize fw-semibold">
                                                श्री जगन्नाथ मन्दिर
                                            </a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="live-darshan-image-height"
                                            src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/ganga-aarti.jpeg')) }}"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-video align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="single-product-details min-height-56">
                                        <div class="text-center">
                                            <a href="#" class="text-capitalize fw-semibold">
                                                गंगा आरती
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="live-darshan-image-height"
                                            src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/amarnath-cave.jpg')) }}"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-video align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="single-product-details min-height-56">
                                        <div class="text-center">
                                            <a href="#" class="text-capitalize fw-semibold">
                                                अमरनाथ गुफा मंदिर
                                            </a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="live-darshan-image-height"
                                            src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kedarnath.jpg')) }}"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-video align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="single-product-details min-height-56">
                                        <div class="text-center">
                                            <a href="#" class="text-capitalize fw-semibold">
                                                श्री बदरीनाथ केदारनाथ मंदिर
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> -->
<!-- end live-darshan section  -->
<!-- canvaz section -->
<!-- <section class="canvaz mt-3">
                <div class="container-fluid">
                    <div class="__inline-62 pt-3">
                        <div class="feature-product-title mt-0 mb-2">
                            {{ translate('devotional') }}
                            <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                            </h4>
                        </div>
                        <div class="owl-carousel owl-theme px-4 pt-2 canvaz-section">
                            <div class="product-single-hover shadow-none rtl mb-4">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="" src="https://canvaz.in//public/img/16430834681.jpg"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-search align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl mb-4">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="" src="https://canvaz.in//public/img/16430834531.jpg"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-search align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl mb-4">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="" src="https://canvaz.in//public/img/16430840621.jpg"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-search align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl mb-4">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="" src="https://canvaz.in//public/img/16430840921.jpg"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-search align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl mb-4">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="" src="https://canvaz.in//public/img/16430841061.jpg"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-search align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl mb-4">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="" src="https://canvaz.in//public/img/16430841481.jpg"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-search align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-single-hover shadow-none rtl mb-4">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <span class="for-discount-value-null"></span>
                                        <a href="">
                                            <img class="" src="https://canvaz.in//public/img/16430841331.jpg"
                                            alt="">
                                        </a>
                                        <div class="quick-view">
                                            <a class="btn-circle stopPropagation action-product-quick-view" href="javascript:"
                                                data-product-id="">
                                                <i class="czi-search align-middle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> -->
        <!-- end canvaz section  -->
        <!--offline pooja section-->
        <section class="overflow-hidden py-3">
            <div class="container-fluid px-0 px-md-3">
                
                <div class="suggestion-section __inline-62 pt-3">
                    <div class="container"> 
                    <div class="feature-product-title mt-0">
                        {{ translate('book_your_pandit') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider"> </span>
                        </h4>
                    </div>
                    <div class="flash-deal-view-all-web row d-flex justify-content-end mb-3">
                        <a class="text-capitalize view-all-text web-text-primary"
                            href="{{ route('offline.pooja.all') }}">
                            View all
                            <i class="czi-arrow-right ml-1 mr-n1"></i>
                        </a>
                    </div>
                    <div class="row g-3 mx-max-md-0">
                        <?php /*
                        {{-- <div class="col-lg-4 px-max-md-0">
                            <div class="suggestion-card">
                                <div class="flash-deal-text web-text-primary">
                                    <img src="{{ asset('public/assets/front-end/img/1683262024419.jpg') }}"
                                    alt="" class="img-fluid suggestion-img">
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-lg-12 px-max-md-0">
                            <div class="owl-theme owl-carousel offlinepooja-slider">
                                @if (count($offlinepoojas) > 0)
                                    @foreach ($offlinepoojas as $offlinepooja)
                                        <div class="product-single-hover shadow-none rtl">
                                            <div class="overflow-hidden position-relative">
                                                <div class="inline_product clickable">
                                                    <a href="{{ route('offline.pooja.detail', $offlinepooja->slug) }}">
                                                        <img src="{{ asset('storage/app/public/offlinepooja/thumbnail/' . $offlinepooja['thumbnail']) }}"
                                                            alt=""
                                                            style="object-fit: contain !important; height: 100% !important;">
                                                    </a>
                                                    <div class="quick-view">
                                                        <a class="btn-circle stopPropagation action-product-quick-view"
                                                            href="{{ route('offline.pooja.detail', $offlinepooja->slug) }}"
                                                            data-product-id="">
                                                            <i class="czi-eye align-middle"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="single-product-details min-height-unset">
                                                    <div class="d-flex align-items-center">
                                                        {!! displayStarRating($offlinepooja->review_avg_rating ?? 5) !!}
                                                        <span
                                                            class="ml-2">({{ number_format($offlinepooja->review_avg_rating ?? 5, 1) }}/5)</span>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('offline.pooja.detail', $offlinepooja->slug) }}"
                                                            class="text-capitalize fw-semibold">
                                                            {{ $offlinepooja['name'] }}
                                                        </a>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <!-- Devotees Count -->
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                                    alt="Users" class="colored-icon"
                                                                    style="width: 24px; height: 24px; margin-right: 5px;">
                                                                <span
                                                                    class="">{{ 10000 + $offlinepooja->offline_pooja_order_count }}+
                                                                    Devotees</span>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        */ ?>
                        <!-- ======= Updated Slider NEWDESIGN ======= -->
                        <div class="col-lg-12 px-max-md-0">
                            <div class="owl-carousel offlinepooja-slider">
                                @foreach ($offlinepoojas as $offlinepooja)
                                    <div class="paramarsh-card">
                                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                            <a href="{{ route('offline.pooja.detail', $offlinepooja->slug) }}">
                                                <img src="{{ asset('storage/app/public/offlinepooja/thumbnail/' . $offlinepooja['thumbnail']) }}"
                                                    alt="{{ $offlinepooja['name'] }}"
                                                    class="card-img-top"
                                                    style="width:100%; height:auto;"
                                                    onerror="this.onerror=null; this.src='{{ asset('public/assets/default-image-all.png') }}';" />
                                            </a>
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-2 text-dark clamp-2-lines">{{ $offlinepooja['name'] }}</h6>

                                                <!-- Star Rating -->
                                                <div class="text-warning mb-1">
                                                    {!! displayStarRating($offlinepooja->review_avg_rating ?? 5) !!}
                                                    <small class="text-muted">({{ number_format($offlinepooja->review_avg_rating ?? 5, 1) }}/5)</small>
                                                </div>

                                                <!-- Devotees -->
                                                <div class="text-muted" style="font-size: 0.85rem;">
                                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                        alt="Users" class="me-1" style="width: 20px; height: 20px; float:left; margin-right:0.5rem">
                                                    10K+ Devotees
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- ======= Updated Slider NEWDESIGN Ends======= -->

                    </div>
                </div>
                                        </div>
            </div>
        </section>
        <!-- end offline pooja section  -->
        <!--start pooja section -->
        <section class="pooja-section">
            <div class="container-fluid pb-3 rtl px-0 px-md-3">
                <div class="__inline-62 pt-3">
<div class="container"> 
                    <div class="feature-product-title mt-0">
                        {{ translate('puja_booking') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>
                    <?php
                    $dataFilterString = '';
                    foreach ($subcategory as $subcat) {
                        $dataFilterString = '.' . $subcat->slug;
                        break;
                    }
                    ?>
                    <ul id="filters" class="clearfix">
                        <!-- <li><span class="filter active" data-filter="{{ $dataFilterString }}">All</span></li> -->
                        @foreach ($subcategory as $item)
                        @if ($item->slug == 'chadhava') @continue; @endif
                            <li><span class="filter {{ $loop->index == 0 ? 'active' : '' }}"
                                    data-filter=".{{ $item->slug }}">{{ @Ucwords($item->name) }}</span></li>
                        @endforeach
                        {{-- VIP POOJA & ANUSHTHAN 22/07/2024 --}}
                        <!-- <li><span class="filter" data-filter=".chadhava">Chadhava</span></li>
                            <li><span class="filter" data-filter=".vipPooja">VIP Pooja</span></li>
                            <li><span class="filter" data-filter=".anushthan">Anushthan</span></li> -->
                        <li class="float-right">
                            <a class="text-capitalize view-all-text web-text-primary" href="{{ route('all-puja') }}">
                                View all<i class="czi-arrow-right ml-1 mr-n1"></i>
                            </a>
                        </li>
                    </ul>
                    <div id="portfoliolist">
                        @foreach ($pooja as $poojaD)
                            @if ($poojaD->type == 'weekly')
                                @include('web-views.partials._pooja_weekly', ['poojaD' => $poojaD])
                            @else
                                @include('web-views.partials._pooja_special', ['poojaD' => $poojaD])
                            @endif
                        @endforeach
                        {{-- VIP POOJA --}}
                        @foreach ($vippooja as $vip)
                            <div class="portfolio vip-puja" data-cat="vip-puja">
                                <div class="portfolio-wrapper">
                                    <div class="card">
                                        <span class="for-discount-value p-1 pl-2 pr-2 font-bold fs-13 pooja-badge">
                                            <span class="direction-ltr blink d-block">{{ translate('VIP_pooja') }}</span>
                                        </span>
                                        @if (!empty($vip->thumbnail))
                                            <a href="{{ route('vip.details', $vip['slug']) }}"><img
                                                    src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $vip->thumbnail) }}"
                                                    class="card-img-top puja-image" alt="{{ $vip->thumbnail }}"></a>
                                        @else
                                            <a href="{{ route('vip.details', $vip['slug']) }}"><img
                                                    src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                                    class="card-img-top puja-image" alt="..."></a>
                                        @endif
                                        <div class="card-body">
                                            <p class="pooja-heading underborder two-lines-only">
                                                {{ strtoupper($vip->pooja_heading) }}
                                            </p>
                                            <div class="w-bar h-bar bg-gradient mt-2"></div>
                                            <p class="pooja-name two-lines-only">{{ $vip->name }}</p>
                                            <p class="card-text two-lines-only">{{ $vip->short_benifits }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <!-- Devotees Count -->
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                        alt="Users" class="colored-icon"
                                                        style="width: 24px; height: 24px; margin-right: 5px;">
                                                    <span
                                                        class="pooja-calendar">{{ 10000 + $vip->pooja_order_review_count }}+
                                                        Devotees</span>
                                                </div>
                                                <!-- Star Rating -->
                                                <div class="d-flex align-items-center">
                                                    {!! displayStarRating($vip->review_avg_rating ?? 5) !!}
                                                    <span
                                                        class="ml-2">({{ number_format($vip->review_avg_rating ?? 5, 1) }}/5)</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('vip.details', $vip['slug']) }}" class="animated-button mt-2">
                                                <span class="text-wrapper">
                                                    <span class="text-slide">{{ translate('GO_PARTICIPATE') }}</span>
                                                    <span class="text-slide">{{ translate('Limited_slots!') }}</span>
                                                </span>
                                                <span class="icon">
                                                   <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}" alt="arrow" />
                                                </span> 
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @foreach ($anushthan as $anusvip)
                            {{-- Anushthan POOJA --}}
                            <div class="portfolio anushthan" data-cat="anushthan">
                                <div class="portfolio-wrapper">
                                    <div class="card">
                                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                            <span class="direction-ltr blink d-block">{{ translate('anushthan') }}</span>
                                        </span>
                                        @if (!empty($anusvip->thumbnail))
                                            <a href="{{ route('anushthan.details', $anusvip->slug) }}"><img
                                                    src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $anusvip->thumbnail) }}"
                                                    class="card-img-top puja-image" alt="{{ $anusvip->thumbnail }}"></a>
                                        @else
                                            <a href="{{ route('anushthan.details', $anusvip->slug) }}"><img
                                                    src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                                    class="card-img-top puja-image" alt="..."></a>
                                        @endif
                                        <div class="card-body">
                                            <p class="pooja-heading underborder two-lines-only">
                                                {{ strtoupper($anusvip->pooja_heading) }}
                                            </p>
                                            <p class="pooja-name two-lines-only">{{ $anusvip->name }}</p>
                                            <p class="card-text two-lines-only">{{ $anusvip->short_benifits }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <!-- Devotees Count -->
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                        alt="Users" class="colored-icon"
                                                        style="width: 24px; height: 24px; margin-right: 5px;">
                                                    <span
                                                        class="pooja-calendar">{{ 10000 + $anusvip->pooja_order_review_count }}+
                                                        Devotees</span>
                                                </div>
                                                <!-- Star Rating -->
                                                <div class="d-flex align-items-center">
                                                    {!! displayStarRating($anusvip->review_avg_rating ?? 5) !!}
                                                    <span
                                                        class="ml-2">({{ number_format($anusvip->review_avg_rating ?? 5, 1) }}/5)</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('anushthan.details', $anusvip->slug) }}" class="animated-button mt-2">
                                                <span class="text-wrapper">
                                                    <span class="text-slide">{{ translate('GO_PARTICIPATE') }}</span>
                                                    <span class="text-slide">{{ translate('Limited_slots!') }}</span>
                                                </span>
                                                <span class="icon">
                                                   <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}" alt="arrow" />
                                                </span> 
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <?php /*
                        {{-- Chadhava --}}
                        @foreach ($chadhavaData as $chadhava)
                            <div class="portfolio chadhava" data-cat="chadhava">
                                <div class="portfolio-wrapper">
                                    <div class="card">
                                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                            <span class="direction-ltr blink d-block">{{ translate('chadhava') }}</span>
                                        </span>
                                        @if (!empty($chadhava->thumbnail))
                                            <a href="{{ route('chadhava.details', $chadhava->slug) }}"><img
                                                    src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $chadhava->thumbnail) }}"
                                                    class="card-img-top puja-image"
                                                    alt="{{ $chadhava->thumbnail }}"></a>
                                        @else
                                            <a href="{{ route('chadhava.details', $chadhava->slug) }}"><img
                                                    src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                                    class="card-img-top puja-image" alt="..."></a>
                                        @endif
                                        <div class="card-body">
                                            <p class="pooja-heading underborder two-lines-only">
                                                {{ strtoupper($chadhava->pooja_heading) }}
                                            </p>
                                            <div class="w-bar h-bar bg-gradient mt-2"></div>
                                            <a href="{{ route('chadhava.details', $chadhava->slug) }}">
                                                <p class="pooja-name two-lines-only">{{ $chadhava->name }}</p>
                                            </a>
                                            <p class="card-text two-lines-only">{{ $chadhava->short_details }}</p>
                                            <div class="d-flex">
                                                <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}"
                                                    alt="" style="width:24px;height:24px;">
                                                <p class="pooja-venue one-lines-only">{{ $chadhava->chadhava_venue }}</p>
                                            </div>
                                            <?php
                                            $ChadhavanextDate = '';
                                            if (!empty($chadhava->chadhava_week)) {
                                                $ChadhavaWeek = json_decode($chadhava->chadhava_week);
                                                $nextChadhavaDay = \App\Utils\getNextChadhavaDay($ChadhavaWeek);
                                                // print_r($nextChadhavaDay) ;die;
                                                if ($nextChadhavaDay) {
                                                    $ChadhavanextDate = '';
                                                    $ChadhavanextDate = $nextChadhavaDay->format('Y-m-d H:i:s');
                                                }
                                            }
                                            $startDate = $chadhava->start_date;
                                            $endDate = $chadhava->end_date;
                                            $currentDate = time();
                                            $formattedDates = [];
                                            $ChadhavaearliestDate = '';
                                            
                                            if ($startDate && $endDate && $startDate <= $endDate) {
                                                $currentDateIter = $startDate->copy();
                                                while ($currentDateIter <= $endDate) {
                                                    $formattedDates[] = $currentDateIter->format('Y-m-d');
                                            
                                                    $currentDateIter->addDay();
                                                }
                                            
                                                foreach ($formattedDates as $date) {
                                                    if (strtotime($date) > $currentDate) {
                                                        $ChadhavaearliestDate = date('d M, l', strtotime($date));
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            @if ($chadhava->chadhava_type == 0)
                                                <div class="d-flex">
                                                    <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                                        alt="" style="width:24px;height:24px;">
                                                    <p class="pooja-calendar">
                                                        {{ date('d', strtotime($ChadhavanextDate)) }},
                                                        {{ translate(date('F', strtotime($ChadhavanextDate))) }} ,
                                                        {{ translate(date('l', strtotime($ChadhavanextDate))) }}
                                                    </p>
                                                </div>
                                            @else
                                                <div class="d-flex">
                                                    <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                                        alt="" style="width:24px;height:24px;">
                                                    <p class="pooja-calendar">
                                                        {{ date('d', strtotime($ChadhavaearliestDate)) }},
                                                        {{ translate(date('F', strtotime($ChadhavaearliestDate))) }} ,
                                                        {{ translate(date('l', strtotime($ChadhavaearliestDate))) }}
                                                    </p>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between align-items-center">
                                                <!-- Devotees Count -->
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                        alt="Users" class="colored-icon"
                                                        style="width: 24px; height: 24px; margin-right: 5px;">
                                                    <span
                                                        class="pooja-calendar">{{ 10000 + $chadhava->pooja_order_review_count }}+
                                                        Devotees</span>
                                                </div>
                                                <!-- Star Rating -->
                                                <div class="d-flex align-items-center">
                                                    {!! displayStarRating($chadhava->review_avg_rating ?? 5) !!}
                                                    <span
                                                        class="ml-2">({{ number_format($chadhava->review_avg_rating ?? 5, 1) }}/5)</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('chadhava.details', $chadhava->slug) }}" class="animated-button mt-2">
                                                <span class="text-wrapper">
                                                    <span class="text-slide">{{ translate('GO_PARTICIPATE') }}</span>
                                                    <span class="text-slide">{{ translate('Limited_slots!') }}</span>
                                                </span>
                                                <span class="icon">
                                                   <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}" alt="arrow" />
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach */ ?>
                    </div>
                </div>
                </div>
            </div>
        </section>
        <!--end pooja section-->
        <section class="pooja-section">
            <div class="container-fluid pb-3 rtl px-0 px-md-3">
                <div class="__inline-62 pt-3">
                    <div class="container"> 
                    <div class="feature-product-title mt-0">
                        {{ translate('chadhava_booking') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider"> </span>
                        </h4>
                    </div>
                    <div class="row pt-3 pb-3">
                        @foreach ($chadhavaData as $chadhava)
                            <div class="col-md-4">
                                <div class="card">
                                    <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                        <span class="direction-ltr blink d-block">{{ translate('chadhava') }}</span>
                                    </span>
                                    @if (!empty($chadhava->thumbnail))
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}"><img
                                                src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $chadhava->thumbnail) }}"
                                                class="card-img-top puja-image" alt="{{ $chadhava->thumbnail }}"
                                                style="height: unset"></a>
                                    @else
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}"><img
                                                src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                                class="card-img-top puja-image" alt="..."></a>
                                    @endif
                                    <div class="card-body">
                                        <p class="pooja-heading underborder two-lines-only">
                                            {{ strtoupper($chadhava->pooja_heading) }}
                                        </p>
                                        <div class="w-bar h-bar bg-gradient mt-2"></div>
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}">
                                            <p class="pooja-name two-lines-only">{{ $chadhava->name }}</p>
                                        </a>
                                        <p class="card-text two-lines-only">{{ $chadhava->short_details }}</p>
                                        <div class="d-flex">
                                            <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}"
                                                alt="" style="width:24px;height:24px;">
                                            <p class="pooja-venue one-lines-only">{{ $chadhava->chadhava_venue }}</p>
                                        </div>
                                        <?php
                                        $ChadhavanextDate = '';
                                        if (!empty($chadhava->chadhava_week)) {
                                            $ChadhavaWeek = json_decode($chadhava->chadhava_week);
                                            $nextChadhavaDay = \App\Utils\getNextChadhavaDay($ChadhavaWeek);
                                            // print_r($nextChadhavaDay) ;die;
                                            if ($nextChadhavaDay) {
                                                $ChadhavanextDate = '';
                                                $ChadhavanextDate = $nextChadhavaDay->format('Y-m-d H:i:s');
                                            }
                                        }
                                        $startDate = $chadhava->start_date;
                                        $endDate = $chadhava->end_date;
                                        $currentDate = time();
                                        $formattedDates = [];
                                        $ChadhavaearliestDate = '';
                                        
                                        if ($startDate && $endDate && $startDate <= $endDate) {
                                            $currentDateIter = $startDate->copy();
                                            while ($currentDateIter <= $endDate) {
                                                $formattedDates[] = $currentDateIter->format('Y-m-d');
                                        
                                                $currentDateIter->addDay();
                                            }
                                        
                                            foreach ($formattedDates as $date) {
                                                if (strtotime($date) > $currentDate) {
                                                    $ChadhavaearliestDate = date('d M, l', strtotime($date));
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        @if ($chadhava->chadhava_type == 0)
                                            <div class="d-flex">
                                                <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                                    alt="" style="width:24px;height:24px;">
                                                <p class="pooja-calendar">
                                                    {{ date('d', strtotime($ChadhavanextDate)) }},
                                                    {{ translate(date('F', strtotime($ChadhavanextDate))) }} ,
                                                    {{ translate(date('l', strtotime($ChadhavanextDate))) }}
                                                </p>
                                            </div>
                                        @else
                                            <div class="d-flex">
                                                <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                                    alt="" style="width:24px;height:24px;">
                                                <p class="pooja-calendar">
                                                    {{ date('d', strtotime($ChadhavaearliestDate)) }},
                                                    {{ translate(date('F', strtotime($ChadhavaearliestDate))) }} ,
                                                    {{ translate(date('l', strtotime($ChadhavaearliestDate))) }}
                                                </p>
                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <!-- Devotees Count -->
                                            <div class="d-flex align-items-center">
                                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                    alt="Users" class="colored-icon"
                                                    style="width: 24px; height: 24px; margin-right: 5px;">
                                                <span
                                                    class="pooja-calendar">{{ 10000 + $chadhava->pooja_order_review_count }}+
                                                    Devotees</span>
                                            </div>
                                            <!-- Star Rating -->
                                            <div class="d-flex align-items-center">
                                                {!! displayStarRating($chadhava->review_avg_rating ?? 5) !!}
                                                <span
                                                    class="ml-2">({{ number_format($chadhava->review_avg_rating ?? 5, 1) }}/5)</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}" class="animated-button mt-2"> 
                                            <span class="text-wrapper">
                                                <span class="text-slide">{{ translate('GO_PARTICIPATE') }}</span>
                                                <span class="text-slide">{{ translate('Limited_slots!') }}</span>
                                            </span>
                                            <span class="icon">
                                               <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}" alt="arrow" />
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                                    </div>
                </div>
            </div>
        </section>
        <!--End Chadhava section-->
        <!--start donate section -->
<section class="donate-section">
    <style>
        .category-filter {
            padding: 5px 20px;
            text-decoration: none;
            color: #666;
            cursor: pointer;
            border-radius: 6px;
        }

        .category-filter.active {
            background: #fe9802;
            color: #fff;
        }
    </style>
    <div class="container-fluid pb-3 rtl px-0 px-md-3">
        <div class="__inline-62 pt-3">
            <div class="container"> 
            <div class="feature-product-title mt-0">
                {{ translate('donate_to_charity') }}
                <h4 class="mt-2 height-10">
                    <span class="divider">&nbsp;</span>
                </h4>
            </div>
            <div class='ml-2'>
                @if (($donateadss->groupBy('type')->map->count()['outsite'] ?? 0) > 0)
                <button class="category-filter btn active-category" data-category="outsite">{{ translate('Ads') }} </button>
                @endif
                @if ($categoryList)
                @foreach ($categoryList as $vals)
                <button class="category-filter btn"
                    data-category="{{ $vals['slug'] }}">{{ $vals['name'] }}</button>
                @endforeach
                @endif
                <button
                    class="category-filter btn {{ ($donateadss->groupBy('type')->map->count()['outsite'] ?? 0) == 0 ? 'active-category' : '' }}"
                    data-category="inhouse">{{ translate('in house') }}</button>
            </div>
            <div class="text-end px-3 d-none d-md-block">
                <a class="text-capitalize view-all-text web-text-primary" href="{{ route('all-donate') }}">
                    {{ translate('view_all') }}
                    <i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                </a>
            </div>
            <div class="feature-product p-0">
                <div class="portfoliolist_donate p-1">
                    <div class="donateFilter row ">                      
                        @if (isset($donateadss) && !empty($donateadss) && count($donateadss) > 0)
                        @foreach ($donateadss as $product)
                        <div class="col-md-4 portfolioDonate portfolio ads_donates {{ $product['type'] }}"
                            data-cat="{{ $product['type'] }}">
                            <div class="portfolio-wrapper">
                                <div class="card">
                                    <span
                                        class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                        <?php
                                        $perpouses = \App\Models\DonateCategory::where('id', $product['purpose_id'])->first();
                                        $product['p_type'] = $perpouses['name'] ?? $product['p_type'];
                                        ?>
                                        <span
                                            class="direction-ltr blink d-block">{{ $product['p_type'] }}
                                        </span>
                                    </span>
                                    <a
                                        href="{{ route('all-donate_ads', ($product['slug']??'')) }}"><img
                                            src="{{ getValidImage('storage/app/public/donate/ads/' . $product['image'], 'banner') }}"
                                            class="card-img-top puja-image" alt="..."></a>
                                    <div class="card-body">
                                        <p class="pooja-heading underborder">
                                            {{ \App\Models\DonateTrust::where('id', $product['trust_id'])->first()['trust_name'] ?? translate('Mahakal.com_Trust') }}
                                            &nbsp;
                                        </p>
                                        <span class="h4 fs-18 two-lines-only"
                                            style="font-weight: 600 ">{{ $product['name'] ?? '' }}</span>
                                        <a href="{{ route('all-donate_ads', ($product['slug']??'')) }}"
                                            class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('donate_now') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                        @if (isset($trustDonate) && !empty($trustDonate) && count($trustDonate) > 0)
                        @foreach ($trustDonate as $trust)
                        <div class="col-md-4 portfolioDonate portfolio {{ ($trust['category']['slug']??'') }}" data-cat="{{ ($trust['category']['slug']??'') }}">
                            <div class="portfolio-wrapper">
                                <div class="card">
                                    <a href="{{ route('all-donate_trust', ($trust['slug']??'')) }}">
                                        <img src="{{ getValidImage('storage/app/public/donate/trust/' . ($trust['theme_image'] ?? ''), 'banner') }}" class="card-img-top puja-image" alt="..."></a>
                                    <div class="card-body">
                                        <span
                                            class="pooja-heading underborder two-lines-only">{{ $trust['trust_name'] ?? '' }}</span>
                                        <a href="{{ route('all-donate_trust', ($trust['slug']??'')) }}"
                                            class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('donate_now') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="text-center pt-2 d-md-none">
                    <a class="text-capitalize view-all-text web-text-primary" href="{{ route('all-donate') }}">
                        {{ translate('view_all') }}
                        <i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                    </a>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>
<!--end donate section-->
        <!-- start to event section -->
        <section class="event-section">
            <div class="container-fluid py-1 rtl px-0 px-md-3">
                
                <div class="__inline-62 pt-3">
                    <div class="container"> 
                    <div class="feature-product-title mt-0">
                        {{ translate('event_booking') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>
                    <style>
                        .btn.active-category,
                        .btn.event-active-category {
                            background-color: #fe9802;
                            color: #fff;
                        }
                    </style>
                    <div class='ml-2'>
                        <!-- <button class="event-category-filter btn" data-category="all">{{ translate('all') }}</button> -->
                        @if (isset($eventCategory) && !empty($eventCategory) && count($eventCategory) > 0)
                        @foreach ($eventCategory as $cat_name)
                        <button
                            class="event-category-filter btn {{ $loop->index == 0 ? 'event-active-category' : '' }}"
                            data-event_category="{{ Str::slug($cat_name['category_name']) }}">{{ ucwords($cat_name['category_name']) }}</button>
                        @endforeach
                        @endif
                    </div>
                    <div class="text-end px-3 d-none d-md-block">
                        <a class="text-capitalize view-all-text web-text-primary event_locations"
                            href="{{ route('event') }}">
                            {{ translate('view_all') }}
                            <i
                                class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                        </a>
                    </div>
                    <?php
                     $date_upcommining = '';
                     $time_upcommining = '';
                     $venue_name = '';
                     $venue_name1 = '';
                     $date_upcommining1 = '';
                     $time_upcommining1 = '';
                     $venuePrices = [];
                $eventList_upcom = [];
                $langs = (str_replace('_', '-', app()->getLocale()) == 'in') ? 'hi' : str_replace('_', '-', app()->getLocale());
                if ($upcommining_event) {
                    foreach ($upcommining_event as $newp) {
                        if (!empty($newp['all_venue_data']) && json_decode($newp['all_venue_data'], true)) {
                            $venuePrices = [];
                            foreach (json_decode($newp['all_venue_data'], true) as $check) {
                                $currentDateTime = new DateTime();
                                $eventDateTime = DateTime::createFromFormat('d-m-Y h:i A', date('d-m-Y', strtotime($check['date'])) . ' ' . date('h:i A', strtotime($check['start_time'])));
                                if ($eventDateTime && $eventDateTime > $currentDateTime) {
                                    $venue_name =  ((!empty($check[$langs . '_event_venue_full_address']??'')) ? ($check[$langs . '_event_venue_full_address']??'') : $check[$langs . '_event_venue']);//$check[$langs . '_event_venue'];
                                    $date_upcommining = date('d M,Y', strtotime($check['date']));
                                    $time_upcommining = date('h:i A', strtotime($check['start_time']));
                                    if (!empty($check['package_list'])) {
                                        $venuePrices = array_filter(array_column($check['package_list'], 'price_no'), 'is_numeric');
                                    }
                                    break;
                                }

                            $venue_name1 = ((!empty($check[$langs . '_event_venue_full_address'] ?? '')) ? ($check[$langs . '_event_venue_full_address'] ?? '') : $check[$langs . '_event_venue']); //$check[$langs . '_event_venue']; 
                            $date_upcommining1 = date('d M,Y', strtotime($check['date']));
                            $time_upcommining1 = date('h:i A', strtotime($check['start_time']));
                        }
                        $eventList_upcom[] = [
                            'event' => $newp,
                            'venue_name' => (($venue_name == '') ? $venue_name1 : $venue_name ?? ""),
                            'date' => $eventDateTime->format('Y-m-d H:i:s'),
                            'formatted_date' => ($date_upcommining == '') ? $date_upcommining1 : ($date_upcommining ?? ""),
                            'formatted_time' => ($time_upcommining == '') ? $time_upcommining1 : ($time_upcommining ?? ""),
                            'venuePrices' => $venuePrices,
                            'informational_status' => $newp['informational_status'] ?? 1
                        ];
                    }
                }
            }
            usort($eventList_upcom, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            ?>
            <div class="feature-product p-0">
                <div class="portfoliolist_event p-1">
                    <div class="EventFilter row">
                        @if (isset($eventList_upcom) && !empty($eventList_upcom) && count($eventList_upcom) > 0)
                        @foreach ($eventList_upcom as $products)
                        <?php $product = $products['event'] ?>
                        <div class="col-md-4 portfolioEvents portfolio {{ Str::slug($product['categorys']['category_name']??'') }}"
                            data-cat="{{ Str::slug($product['categorys']['category_name']??'') }}">
                            <div class="portfolio-wrapper">
                                <div class="card">
                                    <a href="{{ route('event-details', ($product['slug']??'')) }}"><img
                                            src="{{ getValidImage('storage/app/public/event/events/' . $product['event_image'], 'banner') }}"
                                            class="card-img-top puja-image" alt="..."></a>
                                    <div class="card-body">
                                        <p class="font-weight-700 pooja-heading underborder">
                                            {{ $product['event_name'] ?? '' }}
                                        </p>
                                        <div class="font-weight-bold"><i class="tio-poi text-warning"></i>
                                            {{ $products['venue_name'] ?? '' }}
                                        </div>
                                        <div class="font-weight-bold"><i
                                                class="tio-event text-warning"></i>
                                            {{ translate('date') }} :{{ $products['formatted_date'] ?? '' }}
                                        </div>
                                        @if ($product['informational_status'] == 0)
                                        @if (!empty($products['venuePrices']))
                                        <p class="font-weight-bold"><i
                                                class="tio-mma text-warning"></i>
                                            {{ translate('Tickets_starts_from') }}:
                                            {{ min($products['venuePrices']) }}/-
                                        </p>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <!-- Devotees Count -->
                                            <div class="d-flex align-items-center">
                                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                    alt="Users" class="colored-icon"
                                                    style="width: 24px; height: 24px; margin-right: 5px;">
                                                <span class="pooja-calendar">{{ 10000 + $product['event_order_review_count']??0 }} + People</span>
                                            </div>

                                            <!-- Star Rating -->
                                            <div class="d-flex align-items-center">
                                                {!! displayStarRating($product['review_avg_star']??0) !!}
                                                <span
                                                    class="ml-2">({{ number_format($product['review_avg_star'] ?? 5, 1) }}/5)</span>
                                            </div>
                                        </div>
                                                @else
                                                <p class="font-weight-bold"><i
                                                        class="tio-info_outined text-warning">info_outined</i>
                                                    {{ translate('only_Information') }}
                                                </p>
                                                @endif
                                                <a href="{{ route('event-details', ($product['slug']??'')) }}"
                                                    class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('book_now') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="text-center pt-2 d-md-none">
                            <a class="text-capitalize view-all-text web-text-primary event_locations"
                                href="{{ route('event') }}">
                                {{ translate('view_all') }}
                                <i
                                    class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
        <!-- end to event section -->
        <!-- start to tour section -->
        <section class="tour-section">
            <div class="container-fluid py-1 rtl px-0 px-md-3">
                <div class="__inline-62 pt-3">
                    <div class="container"> 
                    <div class="feature-product-title mt-0">
                        {{ translate('tour_booking') }}
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>
                    <style>
                        .btn.active-category,
                        .btn.tour-active-category {
                            background-color: #fe9802;
                            color: #fff;
                        }
                    </style>
                    <div class='ml-2 d-flex' style="overflow-x: scroll;">
                        @if (isset($tourCategory) && !empty($tourCategory) && count($tourCategory) > 0)
                        @foreach ($tourCategory as $cat_name)
                        <button
                            class="tour-category-filter btn {{ $loop->index == 0 ? 'tour-active-category' : '' }}"
                            data-tour_category="{{ $cat_name['slug'] }}">{{ ucwords($cat_name['name']) }}</button>
                        @endforeach
                        @endif
                    </div>
                    <div class="text-end px-3 d-none d-md-block">
                        <a class="text-capitalize view-all-text web-text-primary" href="{{ route('tour.index') }}">
                            {{ translate('view_all') }}
                            <i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                        </a>
                    </div>
                    <div class="feature-product p-0">
                        <div class="portfoliolist_tour p-1">                           
                            @include('web-views.tour.partials.tour-card',['getDataAll'=>$getDataAll,'font_page'=>1,'font_page2'=>1])
                        </div>
                        <div class="text-center pt-2 d-md-none">
                            <a class="text-capitalize view-all-text web-text-primary"
                                href="{{ route('tour.index') }}">
                                {{ translate('view_all') }}
                                <i class="czi-arrow-{{ Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1' }}"></i>
                            </a>
                        </div>
                    </div>
                                                                </div>
                </div>
            </div>
        </section>
        <!-- end to tour section -->
        {{-- start shop lastest product and new arrival section --}}
        @if ($deal_of_the_day)
            @include('web-views.partials._deal-of-the-day', [
                'decimal_point_settings' => $decimalPointSettings,
            ])
        @endif
        <section class="new-arrival-section">
            <div class="container-fluid rtl mt-4">
                @if ($latest_products->count() > 0)
                    <div class="section-header">
                        <div class="arrival-title d-block">
                            <div class="text-capitalize">
                                {{ translate('new_arrivals') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="container-fluid rtl mb-3 overflow-hidden">
                <div class="py-2">
                    <div class="new_arrival_product">
                        <div class="carousel-wrap">
                            <div class="owl-carousel owl-theme new-arrivals-product">
                                @foreach ($latest_products as $key => $product)
                                    @include('web-views.partials._product-card-2', [
                                        'product' => $product,
                                        'decimal_point_settings' => $decimalPointSettings,
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid rtl px-0 px-md-3">
                <div class="row g-3 mx-max-md-0">
                    @if ($bestSellProduct->count() > 0)
                        @include('web-views.partials._best-selling')
                    @endif
                    @if ($topRated->count() > 0)
                        @include('web-views.partials._top-rated')
                    @endif
                </div>
            </div>
        </section>
        {{-- end shop lastest product and new arrival section --}}
        <!--news-blog-->
        <section class="news-blog mt-3">
            <div class="container-fluid rtl px-0 px-md-3">
                <div class="row g-3 mx-max-md-0">
                    <div class="col-lg-8 px-max-md-0">
                        <div class="card card __shadow h-100">
                            <div class="card-body p-xl-35">
                                <div class="row d-flex justify-content-between mx-1 mb-3">
                                    <div>
                                        <img class="size-30"
                                            src="{{ theme_asset(path: 'public/assets/front-end/png/best-sellings.png') }}"
                                            alt="">
                                        <span class="font-bold pl-1">Latest Blog </span>
                                    </div>
                                    <div>
                                        <a class="text-capitalize view-all-text web-text-primary"
                                            href="{{ url('/blog') }}">View all
                                            <i class="czi-arrow-right ml-1 mr-n1"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    @foreach ($latest_blog as $post)
                                        <div class="col-sm-6">
                                            <a class="news-blog-block" target="_blank"
                                                href="{{ url($post->lang_id == 1 ? 'blog/en' : 'blog', $post->title_slug) }}">
                                                <div class="d-flex flex-wrap">
                                                    <div class="news-blog-block-image" style='aspect-ratio:auto; width:35%;'>
                                                        <img class="rounded"
                                                            src="{{ asset('/blog/' . $post->image_mid) }}"
                                                            alt="news" style="max-height=7rem; aspect-ratio: auto; max-height:7rem;"
                                                            >
                                                    </div>
                                                    <div class="news-blog-block-details">
                                                        <h6 class="widget-title">
                                                            <span class="ptr fw-semibold">
                                                                {{ $post->title }}
                                                            </span>
                                                        </h6>
                                                        <div
                                                            class="widget-meta d-flex flex-wrap gap-8 align-items-center row-gap-0">
                                                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->content)) }}
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="widget-product-meta d-flex flex-wrap gap-16 align-items-center row-gap-0">
                                                            <span class="__color-9B9B9B __text-12px">
                                                                <i class="czi-time"></i>
                                                                {{ \Carbon\Carbon::parse($post->created_at)->format('d-m-y') }}
                                                            </span>
                                                            <span class="__color-9B9B9B __text-12px">
                                                                <i class="czi-message"></i> {{ $post->comment_count }}
                                                            </span>
                                                            <span class="__color-9B9B9B __text-12px">
                                                                <i class="czi-eye">{{ $post->hit }}</i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 px-max-md-0">
                        <div class="card card __shadow h-100">
                            <div class="card-body p-xl-35">
                                <div class="row d-flex justify-content-between mx-1 mb-3">
                                    <div>
                                        <img class="size-30"
                                            src="{{ theme_asset(path: 'public/assets/front-end/png/top-rated.png') }}"
                                            alt="">
                                        <span class="font-bold pl-1">{{ translate('top_blog') }}</span>
                                    </div>
                                    <div>
                                        <a class="text-capitalize view-all-text web-text-primary"
                                            href="{{ url('/blog') }}">View
                                            all
                                            <i class="czi-arrow-right ml-1 mr-n1"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    @foreach ($blog_posts as $post)
                                        <div class="col-sm-12">
                                            <a class="news-blog-block" target="_blank" href="{{ url($post->lang_id == 1 ? 'blog/en' : 'blog', $post->title_slug) }}">
                                            <div class="d-flex flex-wrap">
                                                <div class="news-blog-block-image" style="aspect-ratio:auto; width:35%">
                                                    <img class="rounded" src="{{ asset(empty($post->image_mid)?'public/images/default.png':'/blog/' . $post->image_mid) }}" alt="image" style="max-height=7rem; aspect-ratio: auto; max-height:7rem;"
                                                    >
                                                </div>
                                                <div class="blog-block-details">
                                                    <h6 class="widget-title clamp-2-lines">
                                            <span class="ptr fw-semibold">
                                                {{ $post->title }}
                                            </span>
                                        </h6>

                                        <div class="widget-meta d-flex flex-wrap gap-8 align-items-center row-gap-0 clamp-2-lines">   
                                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->content)) }}</p>
                                        </div>
                                                    <div class="widget-product-meta d-flex flex-wrap gap-16 align-items-center row-gap-0">
                                                        <span class="__color-9B9B9B __text-12px">
                                                            <i class="czi-time"></i> {{ \Carbon\Carbon::parse($post->created_at)->format('d-m-y') }}
                                                        </span>
                                                        <span class="__color-9B9B9B __text-12px">
                                                            <i class="czi-message"></i> {{ $post->comment_count }}
                                                        </span>
                                                        <span class="__color-9B9B9B __text-12px">
                                                            <i class="czi-eye">{{ $post->hit }}</i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <style>
.clamp-2-lines {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;  
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
        <!--end-news-blog-->
        <!-- <section class="app-download mt-4">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-md-6 col-lg-5 mb-lg-5 mb-3 pb-md-5 pb-0">
                <h4 class="mb-2 font-weight-700">Welcome to Mahakal App !</h4>
                <p class="description">Talk to Astrologers for Free! Download the app now</p>
                <ul class="nav align-items-center mt-5">
                    <li class="me-3"><a href="#" target="_blank" rel="noopener"><img
                        src="{{ theme_asset(path: 'public/assets/front-end/img/play_store_logo.svg') }}"
                    class="img-fluid" alt="image"></a></li>
                    <li><a href="#" target="_blank" rel="noopener"><img
                        src="{{ theme_asset(path: 'public/assets/front-end/img/app_store_logo1.svg') }}"
                    class="img-fluid" alt="image"></a></li>
                </ul>
            </div>
            <div class="col-md-6 col-lg-7 text-center"><img
                src="{{ theme_asset(path: 'public/assets/front-end/img/mahakal-logo.gif') }}"
            class="img-fluid human-shape" alt="image" loading="lazy" style="width: 40%;"></div>
        </div>
    </div>
    </section> -->
        {{-- start feedback --}}
        {{-- @if (count($feedback) > 0)
            <section class="rashi-section pb-3 mt-3">
                <div class="container-fluid">
                    <div class="__inline-62 pt-3">
                        <div class="feature-product-title mt-0 mb-2">
                            {{ translate('what_our_client_say') }}
                            <h4 class="mt-2">
                                <span class="divider"> </span>
                            </h4>
                        </div>
                        <div class="owl-carousel owl-theme px-4 feedback-slider">
                            @foreach ($feedback as $item)
                                @if($item->user)                            
                                <div class="item shadow">
                                    <div class="profile">
                                        <img src="{{ $item->user->image != 'def.png' ? theme_asset(path: 'storage/app/public/profile/' . $item->user->image) : 'https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/4.jpg' }}"
                                            alt="">
                                        <div class="information">
                                            <p>{{ Str::ucfirst($item->user->f_name . ' ' . $item->user->l_name) }}</p>
                                        </div>
                                    </div>
                                    <p>{{ $item->message }}</p>
                                    <div class="icon">
                                        <i class="fa fa-quote-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif --}}
        {{-- end feedback --}}

        {{-- start general reviews --}}
        @if (count($reviews) > 0)
        <section class="rashi-section pb-3 mt-3">
            <div class="container-fluid">
                <div class="__inline-62 pt-3">
                    <div class="feature-product-title mt-0 mb-2 text-center">
                        <h3 class="fw-bold">{{ translate('what_our_client_say') }}</h3>
                        <h4 class="mt-2"><span class="divider"></span></h4>
                    </div>
        
                    <div class="owl-carousel owl-theme px-4 feedback-slider">
                        @foreach ($reviews as $review)
                            @include('web-views.partials._review', ['review' => $review])
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        @endif
        {{-- end general reviews --}}
    </div>
    <span id="direction-from-session" data-value="{{ session()->get('direction') }}"></span>
@endsection
@push('script')
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
</script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
<script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"
        async defer></script>
<script type="text/javascript">
    // Slideshow 4
    $("#slider4").responsiveSlides({
        auto: true,
        pager: false,
        nav: true,
        speed: 500,
        namespace: "callbacks",
        before: function() {
            $('.events').append("<li>before event fired.</li>");
        },
        after: function() {
            $('.events').append("<li>after event fired.</li>");
        }
    });
</script>
<script type="text/javascript">
    var filterList = {

        init: function() {

            // MixItUp plugin
            // http://mixitup.io
            $('#portfoliolist').mixItUp({
                selectors: {
                    target: '.portfolio',
                    filter: '.filter'
                },
                load: {
                    filter: '{{ $dataFilterString }}'
                }
            });

        }

    };

    // Run the show!
    filterList.init();
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

    window.onload = function () {
        getlocation();
    };

    function getlocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            console.log("Geolocation is not supported by this browser.");
        }
    };

    function showPosition(position) {
        const lat = position.coords.latitude;
        const long = position.coords.longitude;
        $(".event_locations").attr('href', `{{ route('event') }}?lat=${lat}&long=${long}`);
        const geocoder = new google.maps.Geocoder();
        const latlng = { lat: parseFloat(lat), lng: parseFloat(long) };

        geocoder.geocode({ location: latlng }, function (results, status) {
            if (status === "OK" && results[0]) {
                let city = null;
                let address = results[0].formatted_address;
                results[0].address_components.forEach(component => {
                    if (component.types.includes("locality")) {
                        city = component.long_name;
                    }
                });

                if (!city) {
                    results[0].address_components.forEach(component => {
                        if (component.types.includes("administrative_area_level_1")) {
                            city = component.long_name;
                        }
                    });
                }

                if (city) {
                    saveLocation(city, lat, long, address);
                }
            } else {
                console.log("Geocoder failed due to: " + status);
            }
        });
    }

    function saveLocation(city, lat, long, address) {
        let existingData = JSON.parse(localStorage.getItem("location"));

        const locationData = {
            city: city,
            lat: lat,
            long: long,
            address: address
        };

        if (existingData) {
            localStorage.setItem("location", JSON.stringify(locationData));
            console.log("Location updated:", locationData);
        } else {
            localStorage.setItem("location", JSON.stringify(locationData));
            console.log("Location saved:", locationData);
        }
    }


    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                console.log("User denied the request for Geolocation.");
                localStorage.removeItem("location");
                break;
            case error.POSITION_UNAVAILABLE:
                console.log("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                console.log("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                console.log("An unknown error occurred.");
                break;
        }
    }
</script>
<script type="module">
    // Import Firebase modules
    import {
        initializeApp
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js';
    import {
        getMessaging,
        getToken
    } from 'https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging.js';

    // Firebase configuration
    const firebaseConfig = {
        apiKey: "{{ env('FIREBASE_APIKEY') }}",
        authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
        projectId: "{{ env('FIREBASE_PRODJECTID') }}",
        storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
        appId: "{{ env('FIREBASE_APPID') }}",
        measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    // Register Service Worker
    navigator.serviceWorker.register("{{ asset('public/firebase/sw.js') }}")
        .then((registration) => {
            console.log("Service Worker registered successfully:", registration);
            return getToken(messaging, {
                serviceWorkerRegistration: registration,
                vapidKey: "{{ env('VAPID_KEY') }}"
            });
        })
        .then((token) => {
            if (token) {
                console.log("FCM Token:", token);
                $.ajax({
                    url: "{{ url('api/v1/fcm_token_Update') }}",
                    data: {
                        'token': token,
                        'user_id': "{{ auth('customer')->id() ?? 0 }}"
                    },
                    dataType: "json",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {

                    }
                });
            } else {
                console.warn("No FCM token available. Request notification permission.");
            }
        })
        .catch((error) => {
            console.error("Error while retrieving FCM token:", error);
        });
</script>
<!-- ======= Updated Slider NEWDESIGN ======= -->
<script>
    $(document).ready(function() {
        $('.paramarsh-slider').owlCarousel({
            loop: true,
            margin: 16,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            smartSpeed: 600,
            dots: false,
            nav: false,
            responsive: {
                0: {
                    items: 1.2
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                },
                1200: {
                    items: 5
                },
                1400: {
                    items: 6
                }
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.offlinepooja-slider').owlCarousel({
            loop: true,
            margin: 16,
            autoplay: true,
            autoplayTimeout: 2000,
            autoplayHoverPause: true,
            smartSpeed: 700,
            dots: false,
            nav: false,
            responsive: {
                0: {
                    items: 1.2
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                },
                1200: {
                    items: 5
                },
                1400: {
                    items: 6
                }
            }
        });
    });
</script>

{{-- read more --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".read-more-btn").forEach(function (btn) {
            btn.addEventListener("click", function () {
                let targetId = this.getAttribute("data-target");
                let content = document.getElementById(targetId);
    
                content.classList.toggle("expanded");
    
                if (content.classList.contains("expanded")) {
                    this.textContent = "Read Less";
                } else {
                    this.textContent = "Read More";
                }
            });
        });
    });
</script>

@endpush