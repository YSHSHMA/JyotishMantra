@extends('layouts.front-end.app')

@section('title', translate('Tours'))

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
<meta name="description"
    content="Mahakal.com par Ujjain Mahakal Darshan, Omkareshwar, Maihar, Chitrakoot aur anya teerth sthal ke liye pavitra yatra packages book karein. Aaramdayak aur bhaktimay yatra anubhav paayein.">

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
        background: url("{{ asset('public/assets/front-end/img/slider/yatra-booking.jpg') }}") no-repeat;
        /*background:url("{{ asset('assets/front-end/img/slider/yatra-booking.jpg') }}") no-repeat;*/
        background-size: cover;
        background-position: center center;
    }

    @media (max-width: 768px) {
        .responsive-bg {
            padding-top: 2.91rem !important;
            padding-bottom: 3rem !important;
            background: url("{{ asset('assets/front-end/img/slider/yatra-booking1.jpg') }}") no-repeat;
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
@endpush

@section('content')
{{-- main page --}}
<div class="container-fluid">
    <div class="container py-4 __inline-67">
        <div class="rtl">
            <div class="bg-white __shop-banner-main">
                <img class="__shop-page-banner" alt=""
                    src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $vendor['banner'], type: 'wide-banner') }}">

                <div
                    class="position-relative z-index-99 rtl w-100 text-align-direction d-none d-md-block max-width-500px">
                    <div class="__rounded-10 bg-white position-relative">
                        <div class="d-flex flex-wrap justify-content-between seller-details">
                            <div class="d-flex align-items-center p-2 flex-grow-1">
                                <div class="">
                                    <div class="position-relative">

                                        <img class="w-90px rounded" alt=""
                                            src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $vendor['image'], type: 'shop') }}">
                                    </div>
                                </div>
                                <div
                                    class="__w-100px flex-grow-1 {{ Session::get('direction') === 'rtl' ? ' pr-2 pr-sm-4' : ' pl-2 pl-sm-4' }}">

                                    <div class="font-weight-bolder mb-2">
                                        {{-- @if ($shop['id'] != 0) --}}
                                        {{ $vendor->company_name }}
                                        <br>
                                        <small
                                            class="fw-bold pt-0 mt-0">{{ $vendor['state'] . ' ' . $vendor['city'] }}</small>
                                        {{-- @else
                                        {{ $web_config['name']->value }}
                                        @endif --}}
                                    </div>

                                    <div class="d-flex flex-column gap-1">
                                        <div class="fs-12">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <=$vendor['avg_rating'])
                                                <i class="tio-star text-warning"></i>
                                                @elseif (
                                                $vendor['avg_rating'] != 0 &&
                                                $i <= (int) $vendor['avg_rating'] + 1 &&
                                                    $vendor['avg_rating']>= (int) $vendor['avg_rating'] + 0.3)
                                                    <i class="tio-star-half text-warning"></i>
                                                    @else
                                                    <i class="tio-star-outlined text-warning"></i>
                                                    @endif
                                                    @endfor
                                                    <span class="ml-1">({{ round($vendor['avg_rating'], 1) }})</span>
                                                    <span class="__inline-69"></span>
                                                    <span class="text-nowrap fs-13 font-semibold text-base">
                                                        {{ $vendor['review_count'] }} {{ translate('reviews') }}
                                                    </span>
                                        </div>

                                        <div class="d-flex flex-wrap py-1 fs-12 web-text-primary">

                                            <span class="text-nowrap">{{ $getDataAll->count() }}
                                                {{ translate('tours') }}</span>
                                        </div>

                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="position-relative z-index-99 rtl w-100 text-align-direction d-md-none border mt-3">
            <div class="__rounded-10 bg-white position-relative">
                <div class="d-flex flex-wrap justify-content-between seller-details">
                    <div class="d-flex align-items-center p-2 flex-grow-1">
                        <div class="">
                            <div class="position-relative">

                                <img class="w-90px rounded" alt=""
                                    src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $vendor['image'], type: 'shop') }}">
                            </div>
                        </div>
                        <div
                            class="__w-100px flex-grow-1 {{ Session::get('direction') === 'rtl' ? ' pr-2 pr-sm-4' : ' pl-2 pl-sm-4' }}">

                            <div class="font-weight-bolder mb-2">
                                {{-- @if ($shop['id'] != 0) --}}
                                {{ $vendor->company_name }}
                                <br>
                                <small
                                    class="fw-bold pt-0 mt-0">{{ $vendor['state'] . ' ' . $vendor['city'] }}</small>
                                {{-- @else
                                        {{ $web_config['name']->value }}
                                @endif --}}
                            </div>

                            <div class="d-flex flex-column gap-1">
                                <div class="fs-12">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <=$vendor['avg_rating'])
                                        <i class="tio-star text-warning"></i>
                                        @elseif (
                                        $vendor['avg_rating'] != 0 &&
                                        $i <= (int) $vendor['avg_rating'] + 1 &&
                                            $vendor['avg_rating']>= (int) $vendor['avg_rating'] + 0.3)
                                            <i class="tio-star-half text-warning"></i>
                                            @else
                                            <i class="tio-star-outlined text-warning"></i>
                                            @endif
                                            @endfor
                                            <span class="ml-1">({{ round($vendor['avg_rating'], 1) }})</span>
                                            <span class="__inline-69"></span>
                                            <span class="text-nowrap fs-13 font-semibold text-base">
                                                {{ $vendor['review_count'] }} {{ translate('reviews') }}
                                            </span>
                                </div>

                                <div class="d-flex flex-wrap py-1 fs-12 web-text-primary">

                                    <span class="text-nowrap">{{ $getDataAll->count() }}
                                        {{ translate('tours') }}</span>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row">

        <div class="col-md-12">
            <ul id="filters" class="float-right d-flex gap-2">
                <li class="">
                    <input class="form-control border-1 fw-bold" type="search" id="searchable" name="name"
                        placeholder="{{ translate('search_by_Tour_Name') }}" autocomplete="off">
                </li>
                <li class="">
                    <div class="">
                        <div class="d-flex align-items-center gap-2">
                            <a id="filterToggle" class="btn btn--primary">
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
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="container py-4 __inline-67">
                <div id="portfoliolist" class="">
                    <br>
                    @include('web-views.tour.partials.tour-card',['getDataAll'=>$getDataAll,'font_page'=>0])
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
    $(".search_key").keyup(function() {
        var name = $(".search_key").val();

        if (name.length > 3) {
            // Show loader
            $("#loader").show();

            $.ajax({
                url: "{{ url('api/v1/tour/search') }}",
                data: {
                    name: name,
                    role: "web",
                    "lang": "{{ str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale()) }}",
                },
                dataType: "json",
                type: "post",
                success: function(response) {
                    $("#loader").hide();

                    // Check if results were found
                    if (response.status === 1) {
                        var resultHtml = response.data;
                        $("#search-results").html(resultHtml);
                    } else {
                        $("#search-results").html(
                            '<li class="list-group-item">No Results Found</li>');
                    }
                },
                error: function() {
                    $("#loader").hide(); // Hide loader on error
                    $("#search-results").html(
                        '<li class="list-group-item">Tour Not Available. Try another destination</li>'
                    );
                }
            });
        } else {
            $("#search-results").html(''); // Clear results if input length is less than 3
        }
    });
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
    $(document).ready(function() {
        const $cards = $('#portfoliolist .tour-card');
        // const $search = $('input[type="search"]');
        const $search = $('#searchable');

        function applyFilters() {
            let searchText = $search.val().toLowerCase();
            let activeCategory = $('.filter.active').data('filter') || '.all_tours';
            let activeState = $('.filter_state_name.active').data('filter') || '';

            // Build final filter selector
            let selector = '';
            if (activeCategory === '.all_tours') {
                selector = '.tour-card';
            } else {
                selector = activeCategory;
            }
            if (activeState) {
                selector = `${activeState}_${activeCategory.replace('.', '')}`;
            }

            // Hide all at once, then show only matched
            let $matched = $cards.filter(function() {
                let title = $(this).find('.tour-title').text().toLowerCase();
                let matchesSearch = title.includes(searchText);
                let matchesCategory = (selector === '.tour-card') || $(this).hasClass(selector.replace(
                    '.', ''));
                return matchesSearch && matchesCategory;
            });

            $cards.hide(); // hide everything once
            $matched.show(); // show only matched cards

            toggleNoTourMessage($matched.length > 0);
        }

        // Event listeners
        $search.on('keyup', applyFilters);
        $('.filter').on('click', function() {
            $('.filter').removeClass('active');
            $(this).addClass('active');
            applyFilters();
        });
        $('.filter_state_name').on('click', function() {
            $('.filter_state_name').removeClass('active');
            $(this).addClass('active');
            applyFilters();
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
    //     $('input[type="search"]').on('keyup', function() {
    //         var searchText = $(this).val().toLowerCase();
    //         var activeCategory = $('.filter.active').data('filter');

    //         $('#portfoliolist .tour-card').each(function() {
    //             var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1;
    //             var matchesCategory = $(this).is(activeCategory);

    //             $(this).toggle(matchesSearch && matchesCategory);
    //         });
    //     });


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
    $(function() {
        const $grid = $('#portfoliolist .grids');
        const originalOrder = $grid.children().clone();

        function applyFilters() {
            const priceType = $('input[name="priceType"]:checked').val();
            const sortType = $('input[name="priceSort"]:checked').val();

            let $cards = originalOrder.clone();
            if (priceType) {
                $cards = $cards.filter(function() {
                    const type = $(this).find('.tour-cab-person').text().trim();
                    return type === priceType;
                });
            }
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

            $grid.html($cards);
        }

        $('input[name="priceType"], input[name="priceSort"]').on('change', applyFilters);
        applyFilters();
    });
</script>
@endpush