@extends('layouts.front-end.app')

@section('title', 'Consultancy')
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
    <meta property="twitter:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!--poojafilter-css-->
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/animationbutton.css') }}">
    <style>
        .gold {
            color: #fe9802;
        }

        .pooja-menu {
            position: sticky;
            top: 8.7rem;
            left: 0;
            right: 0;
            background-color: white;
            z-index: 9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
        }

        .pooja-search {
            display: flex;
            justify-content: right;
            align-items: center;
            width: 100%;
        }

        .pooja-search input {
            width: 60% !important;
        }

        .search-icon {
            margin-right: 10px;
            cursor: pointer;
        }

        .offlinepoojafilter {
            padding: 5px 10px;
            cursor: pointer;
        }

        /* .two-lines-only {
                                                                                display: -webkit-box;
                                                                                -webkit-line-clamp: 2;
                                                                                -webkit-box-orient: vertical;
                                                                                overflow: hidden;
                                                                                text-overflow: ellipsis;
                                                                                line-height: 1.5em;
                                                                                min-height: 3em;
                                                                            } */

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

        .offlinepooja-name {
            font-weight: 700;
            font-size: 17px;
        }

        .opooja-name {
            font-weight: 700;
            font-size: 17px;
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
    </style>

    {{-- star css --}}
    <style>
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
    </style>
@endpush

@section('content')
    {{-- main page --}}
    {{-- <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ @ucwords(translate('consultancy')) }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ @ucwords(translate('consultancy')) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="image-box">
        <img src="{{ asset('public/assets/front-end/img/consultancy-banner.png') }}" alt="banner">
        <div class="col-md-12 align-self-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"></li>
                            <li class="breadcrumb-item"></li>
                        </ol>
                    </nav>
                </div>
    </div>

    <section class="cal_about_wrapper">
        <div class="container-fluid rtl px-0 px-md-3">
            <div class="__inline-62">

                {{-- <div id="search-div">
                    <a href="javascript:0" class="btn btn-sm btn--primary" onclick="searchShow()">
                        <i class="fa fa-search"></i>
                    </a>
                </div> --}}
                {{-- <div id="search-bar-container" class="pooja-search"
                        style="width: 100%; display: none; justify-content: center; align-items: center;padding-top:10px;">
                        <input class="form-control" type="search" autocomplete="off" id="search-text"
                            placeholder="{{translate('search_for_your_couselling')}}" name="name" value=""
                            style="width: 100px;">
                        <a href="javascript:0" class="btn btn--primary ml-2" onclick="searchHide()">
                            <i class="fa fa-multiply"></i>
                        </a>
                    </div> --}}

                <div id="search-bar-container" class="pooja-search"
                    style="width: 100%; justify-content: center; align-items: center;padding-top:10px;">
                    <input class="form-control" type="search" autocomplete="off" id="search-text"
                        placeholder="{{ translate('search_for_your_counselling') }}" style="width: 100px;">
                </div>

                <div class="pooja-menu w-100 row">
                    <ul id="offlinepoojafilters" class="clearfix"
                        style="display: flex; align-items: center; justify-content: space-between;height:50px; width: 100%">
                        <li style="flex: 1;">
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <span class="offlinepoojafilter active" data-filter=".all">All</span>
                                <span class="offlinepoojafilter" style="padding: 10px !important;"
                                    data-filter=".consultation">{{ 'Consultation' }}</span>
                                <span class="offlinepoojafilter" style="padding: 10px !important;"
                                    data-filter=".muhurat">{{ 'Muhurat' }}</span>
                            </div>
                        </li>

                        {{-- </ul> --}}
                        {{-- <ul id="filters" class="clearfix"> --}}
                        <li class="float-right">
                            <div class="input-group-overlay search-form-mobile text-align-direction">
                                <div class="d-flex align-items-center gap-2">
                                    <a id="filterToggle" class="btn btn--primary btn-sm me-4">
                                        <i class="fa fa-filter"></i> {{ translate('filter') }}
                                    </a>
                                    <div id="filterTooltip" class="filter-tooltip">
                                        <select id="filtertype" class="form-control">
                                            <option value="all">{{ translate('All') }}</option>
                                            <option value="consultation">{{ translate('consultation') }}</option>
                                            <option value="muhurat">{{ translate('muhurat') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>

                </div>

                <div id="offlinepoojaportfoliolist">
                    {{-- all list --}}
                    <div id="counselling-more-div">
                        @foreach ($counselling->take(20) as $pooja)
                            <div class="offlinepoojaportfolio all" data-cat="all" id="all-list">
                                <div class="portfolio-wrapper">
                                    <div class="card">
                                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                            <span
                                                class="direction-ltr blink d-block">{{ translate($pooja->sub_category_id == 40 ? 'consultation' : 'muhurat') }}</span>
                                        </span>
                                        <img src="{{ isset(json_decode($pooja['images'])[0]) ? asset('storage/app/public/pooja/' . json_decode($pooja['images'])[0]) : asset('def.png') }}"
                                            class="card-img-top puja-image" alt="{{ $pooja->thumbnail }}">
                                        <div class="card-body">
                                            <div class="w-bar h-bar bg-gradient mt-2"></div>
                                            <p class="offlinepooja-name">{{ $pooja->name }}</p>
                                            <div class="justify-content-between">
                                                <div class="product-price">
                                                    <del class="category-single-product-price color-danger"
                                                        style="color: red !important">
                                                        ₹{{ $pooja['counselling_main_price'] }}
                                                    </del>
                                                    <span class="text-accent text-dark">
                                                        ₹{{ $pooja['counselling_selling_price'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mt-2">
                                                {!! displayStarRating($pooja->review_avg_rating ?? 5) !!}
                                                <span
                                                    class="ml-2">({{ number_format($pooja->review_avg_rating ?? 5, 1) }})</span>
                                            </div>

                                            {{-- </div> --}}
                                            <a href="{{ route('counselling.details', $pooja['slug']) }}"
                                                class="animated-button mt-2">

                                                <span class="text-wrapper">
                                                    <span class="text-slide">{{ translate('Book_now') }}</span>
                                                    <span class="text-slide">{{ translate('Book_now!') }}</span>
                                                </span>
                                                <span class="icon">
                                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}"
                                                        alt="arrow" />
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if (count($counselling) > 20)
                            <div class="w-100 text-center mb-4 offlinepoojaportfolio all">
                                <button class="btn btn--primary" onclick="loadMore()" id="counselling-more-btn">Load More</button>
                                <div class="spinner-border" role="status" id="counselling-spinner"
                                    style="display: none;">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- consultation list --}}
                    <div>
                        @foreach ($consultation as $pooja)
                            <div class="offlinepoojaportfolio consultation" data-cat="consultation">
                                <div class="portfolio-wrapper">
                                    <div class="card">
                                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                            <span
                                                class="direction-ltr blink d-block">{{ translate('consultation') }}</span>
                                        </span>
                                        <img src="{{ isset(json_decode($pooja['images'])[0]) ? asset('storage/app/public/pooja/' . json_decode($pooja['images'])[0]) : asset('def.png') }}"
                                            class="card-img-top puja-image" alt="{{ $pooja->thumbnail }}">
                                        <div class="card-body">
                                            <div class="w-bar h-bar bg-gradient mt-2"></div>
                                            <p class="offlinepooja-name">{{ $pooja->name }}</p>
                                            <div class="justify-content-between">
                                                <div class="product-price">
                                                    <del class="category-single-product-price color-danger"
                                                        style="color: red !important">
                                                        ₹{{ $pooja['counselling_main_price'] }}
                                                    </del>
                                                    <span class="text-accent text-dark">
                                                        ₹{{ $pooja['counselling_selling_price'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- <div class="d-flex align-items-center">
                                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                alt="Users" class="colored-icon"
                                                style="width: 24px; height: 24px; margin-right: 5px;">
                                            <span class="">{{ 10000 + $pooja->pooja_order_review_count }}+
                                                Devotees</span>
                                        </div> --}}
                                            <div class="d-flex align-items-center mt-2">
                                                {!! displayStarRating($pooja->review_avg_rating ?? 5) !!}
                                                <span
                                                    class="ml-2">({{ number_format($pooja->review_avg_rating ?? 5, 1) }})</span>
                                            </div>

                                            {{-- </div> --}}
                                            <a href="{{ route('counselling.details', $pooja['slug']) }}"
                                                class="animated-button mt-2">

                                                <span class="text-wrapper">
                                                    <span class="text-slide">{{ translate('Book_now') }}</span>
                                                    <span class="text-slide">{{ translate('Book_now!') }}</span>
                                                </span>
                                                <span class="icon">
                                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}"
                                                        alt="arrow" />
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- muhurat list --}}
                    <div>
                        @foreach ($muhurat as $pooja)
                            <div class="offlinepoojaportfolio muhurat" data-cat="muhurat">
                                <div class="portfolio-wrapper">
                                    <div class="card">
                                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                            <span class="direction-ltr blink d-block">{{ translate('muhurat') }}</span>
                                        </span>
                                        <img src="{{ isset(json_decode($pooja['images'])[0]) ? asset('storage/app/public/pooja/' . json_decode($pooja['images'])[0]) : asset('def.png') }}"
                                            class="card-img-top puja-image" alt="{{ $pooja->thumbnail }}">
                                        <div class="card-body">
                                            <div class="w-bar h-bar bg-gradient mt-2"></div>
                                            <p class="offlinepooja-name">{{ $pooja->name }}</p>
                                            <div class="justify-content-between">
                                                <div class="product-price">
                                                    <del class="category-single-product-price color-danger"
                                                        style="color: red !important">
                                                        ₹{{ $pooja['counselling_main_price'] }}
                                                    </del>
                                                    <span class="text-accent text-dark">
                                                        ₹{{ $pooja['counselling_selling_price'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- <div class="d-flex align-items-center">
                                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                alt="Users" class="colored-icon"
                                                style="width: 24px; height: 24px; margin-right: 5px;">
                                            <span class="">{{ 10000 + $pooja->pooja_order_review_count }}+
                                                Devotees</span>
                                        </div> --}}
                                            <div class="d-flex align-items-center mt-2">
                                                {!! displayStarRating($pooja->review_avg_rating ?? 5) !!}
                                                <span
                                                    class="ml-2">({{ number_format($pooja->review_avg_rating ?? 5, 1) }})</span>
                                            </div>

                                            {{-- </div> --}}
                                            <a href="{{ route('counselling.details', $pooja['slug']) }}"
                                                class="animated-button mt-2">

                                                <span class="text-wrapper">
                                                    <span class="text-slide">{{ translate('Book_now') }}</span>
                                                    <span class="text-slide">{{ translate('Book_now!') }}</span>
                                                </span>
                                                <span class="icon">
                                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}"
                                                        alt="arrow" />
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <!-- <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script> -->
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>

    <script type="text/javascript">
        $('input[type="search"]').on('keyup', function() {
            var searchText = $(this).val().toLowerCase().trim();
            var found = false;

            if (searchText === '') {

                $('.offlinepoojaportfolio').show();
                return;
            }

            $('.offlinepoojaportfolio').each(function() {
                var matchesSearch = $(this).find('.offlinepooja-name').text().toLowerCase().indexOf(
                    searchText) > -1;

                if (matchesSearch) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });

        });
    </script>

    {{-- load more --}}
    <script>
        let allData = [];
        let card = "";
        const counsellingDetailsUrl = "{{ url('counselling/details') }}";

        function loadMore() {
            $("#counselling-more-btn").hide();
            $("#counselling-spinner").show();

            $.ajax({
                type: "GET",
                url: "{{ url('counselling/load-more') }}",
                success: function(response) {
                    if (response.status) {
                        allData = response.counselling;
                        var cardData = appendData();
                        $("#counselling-spinner").hide();
                        $("#counselling-div").append(cardData);
                    } else {
                        $("#counselling-more-btn").show();
                        $("#counselling-spinner").hide();
                        toaster.error(response.message);
                        return;
                    }
                }
            });
        }

        function appendData() {
            $.each(allData, function(index, pooja) {
                card += `<div class="offlinepoojaportfolio all" data-cat="all" style="display:inline-block">
                <div class="portfolio-wrapper">
                    <div class="card">
                        <img src="${pooja.images ? '/storage/app/public/pooja/' + JSON.parse(pooja.images)[0] : '/def.png'}"
                             class="card-img-top puja-image" alt="${pooja.thumbnail}">
                        <div class="card-body">
                            <div class="w-bar h-bar bg-gradient mt-2"></div>
                            <p class="offlinepooja-name">${pooja.name}</p>
                            <div class="justify-content-between">
                                <div class="product-price">
                                    <del class="category-single-product-price color-danger" style="color:red">
                                        ₹${pooja.counselling_main_price}
                                    </del>
                                    <span class="text-accent text-dark">
                                        ₹${pooja.counselling_selling_price}
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                ${displayStars(pooja.review_avg_rating ?? 5)}
                                <span class="ml-2">(${Number(pooja.review_avg_rating ?? 5).toFixed(1)})</span>
                            </div>
                            <a href="${counsellingDetailsUrl}/${pooja.slug}" class="animated-button mt-2">
                                <span class="text-wrapper">
                                    <span class="text-slide">Book_now</span>
                                    <span class="text-slide">Book_now!</span>
                                </span>
                                <span class="icon">
                                    <img src="/assets/front-end/img/track-order/arrow-white-icon.svg" alt="arrow"/>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                </div>
            `;
            });
            $("#counselling-more-div").append(card);
        }

        function displayStars(rating) {
            rating = parseFloat(rating) || 0;

            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(rating)) {
                    stars += '<i class="fas fa-star gold"></i>'; // full star
                } else if (i - rating < script && rating % 1 !== 0) {
                    stars += '<i class="fas fa-star-half-alt gold"></i>'; // half star
                } else {
                    stars += '<i class="fas fa-star-half-alt"></i>'; // empty star
                }
            }
            return stars;
        }
    </script>

    <script>
        // filter
        $('#filterToggle').on('click', function(e) {
            e.stopPropagation();
            $('#filterTooltip').toggleClass('active');
        });


        $(document).on('click', function(e) {
            if (!$(e.target).closest('#filterTooltip, #filterToggle').length) {
                $('#filterTooltip').removeClass('active');
            }
        });

        var mixer = $('#offlinepoojaportfoliolist').mixItUp({
            selectors: {
                target: '.offlinepoojaportfolio',
                filter: '.offlinepoojafilter',
            }
        });
        mixer.mixItUp('filter', '.all');

        $('#filtertype').on('change', function() {
            var filterVal = $(this).val();
            mixer.mixItUp('filter', '.' + filterVal);
        });
    </script>
@endpush
