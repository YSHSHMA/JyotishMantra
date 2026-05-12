@extends('layouts.front-end.app')
@section('title', translate('Sabhi Pooja Sevaayein – Ghar Baithe Online Pooja Book Karein | Mahakal.com'))
@php
    use App\Utils\Helpers;
    use function App\Utils\getNextPoojaDay;
    use function App\Utils\getNextChadhavaDay;
    use function App\Utils\displayStarRating;
    use Illuminate\Support\Collection;
    $groupedChadhava = $chadhavaData->groupBy(function ($item) {
        return $item->getNextAvailableDate()?->format('d-m-Y');
    });
@endphp
@push('css_or_js')
    <meta property="og:image"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
    <meta name="description"
        content="Mahakal.com par sabhi pooja sevaayein jaise Griha Pravesh, Mangal Dosh Nivaran, Rudrabhishek, Vivaah Pooja aur anya dharmik anushthaan online book karein. Anubhavi panditon ke saath ghar par pooja karaayein.">
    <meta property="og:title" content="Terms & conditions of {{ $web_config['name']->value }} " />
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <meta property="twitter:card"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
    <meta property="twitter:title" content="Terms & conditions of {{ $web_config['name']->value }}" />
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta
        property="twitter:description"content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!--poojafilter-css-->
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/animationbutton.css') }}">
    <style>
        .fixed-search {
            position: sticky;
            top: 138px;
            /* Adjust depending on your navbar height */
            background: #fff;
            z-index: 999;
            border-bottom: 1px solid #eee;
        }

        #pujaSearchInput:focus {
            border-color: #fe9802 !important;
            box-shadow: 0 0 0 0.2rem rgba(254, 152, 2, 0.25);
        }

        /* For icon inside input */
        #pujaSearchInput {
            padding-left: 2.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .fixed-search {
                top: 65px;
            }

            #pujaSearchInput {
                font-size: 16px;
            }
        }


        .pooja-menu {
            position: sticky;
            top: 83px;
            left: 0;
            right: 0;
            background-color: white;
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);  */
            z-index: 9;
        }

        .form-control:focus {
            border-color: #fe9802 !important;
        }

        .search-icon {
            margin-left: 10rem;
        }

        @media only screen and (max-width: 768px) {
            .search-icon {
                margin-left: 5rem;
            }
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

        @keyframes slideBackground {
            0% {
                background-position: 100% 0;
            }

            100% {
                background-position: -100% 0;
            }
        }

        .gold {
            color: #fe9802;
        }

        .chadhava-image {
            height: 240px !important;
        }

        .chadhava-badge {
            top: 200px !important;
        }

        @media (max-width: 767px) {

            .fixed-search,
            {
            font-size: 14px;
            padding: 8px 14px;
        }
        }

        .czi-search {
            margin-left: 7px;
        }

        #no-tour-message {
            font-size: 18px;
            color: #ff0000;
            font-weight: bold;
        }
           .loader-icon-search {
        display: none;
        font-size: 1.5rem;
        /* Adjust size as needed */
    }
    </style>
@endpush

@section('content')
    {{-- main page --}}
    <!-- <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ ucwords(translate('Explore_Upcoming_puja_services_on_Mahakal.com')) }}
                    </h1>
                    <span class="font-normal font-normal">
                        {{ translate('book_puja_online_in_your_name_and_gotra_receive_the_puja_video_along_with_the_tirth_prasad_and_gain_blessings_from_the_divine') }}</span>
                </div>
            </div>
        </div>
    </div> -->
    <div class="image-box">
        <img src="{{ asset('public/assets/front-end/img/chadhava-banners.png') }}" alt="chadhava banner">
    </div>
    <!-- Fixed search form -->
    <div class="fixed-search position-sticky top-0 z-999 bg-white shadow-sm py-2" style="z-index:999;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-10 col-12">
                    <div class="position-relative">
                        <div id="loader" class="loader-icon-search"
                            style="position: absolute; left: 10px; top: 10px; display: none;">
                            <i class="fa fa-spinner fa-spin"></i>
                        </div>
                        
                            <input class="form-control form-control-lg ps-5" type="search" autocomplete="off"
                                placeholder="Search Pooja Name, Place, Day, Date (e.g. Shiv, Pooja, Somvar)" name="name"
                                id="chadhdhavaInput">
                            <ul id="search-results" class="list-group position-absolute w-100" style="top: 49px;"></ul>
                       

                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="cal_about_wrapper">
        <div class="container-fluid rtl px-0 px-md-3">
            <div class="__inline-62 pt-2">
                <div class="container">
                <ul id="filters" class="clearfix">
                    <li class="float-right">
                        <div class="input-group-overlay search-form-mobile text-align-direction">
                            <div class="d-flex align-items-center gap-2">
                                <input class="form-control" type="search" autocomplete="off"
                                    placeholder="Search for items..." name="name" value="">
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="row card-container pt-3 pb-3 chadhava-title">
                    {{-- Chadhava --}}
                    @foreach ($chadhavaData as $chadhava)

                        @php
                            $nextDate = $chadhava->getNextAvailableDate();
                        @endphp
                        @if ($nextDate)
                            <div class="col-md-4 pb-3 searchable-card all_chadhava">
                                <div class="card">
                                    <span class="for-discount-value chadhava-badge p-1 pl-2 pr-2 font-bold fs-13">
                                        <span class="direction-ltr blink d-block">{{ translate('chadhava') }}</span>
                                    </span>
                                    @if (!empty($chadhava->thumbnail))
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}">
                                            <img src="{{ getValidImage(path: 'storage/app/public/chadhava/thumbnail/' . $chadhava->thumbnail) }}"
                                                class="card-img-top chadhava-image" alt="{{ $chadhava->thumbnail }}"></a>
                                    @else
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}">
                                            <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                                class="card-img-top chadhava-image" alt="..."></a>
                                    @endif
                                    <div class="card-body newpadding">
                                        <p class="pooja-heading chadhava-title underborder">{{ strtoupper($chadhava->pooja_heading) }}
                                        </p>
                                        <span class="chadhava-title d-none">{{ $chadhava->getRawOriginal('pooja_heading')}}</span>
                                        <div class="w-bar h-bar bg-gradient mt-2"></div>
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}">
                                            <p class="pooja-name chadhava-title two-lines-only">
                                                {{ Str::words($chadhava->name, 20, '...') }}</p>
                                        </a>
                                        <span class="chadhava-title d-none">{{ $chadhava->getRawOriginal('name')}}</span>
                                        <p class="card-text two-lines-only">{{ $chadhava->short_details }}</p>
                                        <span class="chadhava-title d-none">{{ $chadhava->getRawOriginal('short_details')}}</span>
                                        <div class="d-flex">
                                            <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}"
                                                alt="" style="width:24px;height:24px;">
                                            <p class="pooja-venue chadhava-title one-lines-only">
                                                {{ $chadhava->chadhava_venue }}
                                            </p>
                                            <span class="chadhava-title d-none">{{ $chadhava->getRawOriginal('chadhava_venue')}}</span>
                                        </div>


                                        <div class="d-flex">
                                            <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                                alt="" style="width:24px;height:24px;">
                                            <p class="pooja-calendar chadhava-title">
                                                {{ $nextDate->format('d') }},
                                                {{ translate($nextDate->format('F')) }} ,
                                                {{ translate($nextDate->format('l')) }}</p>
                                        </div>

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
                                                {!! displayStarRating($chadhava->review_avg_rating ?? 0) !!}
                                                <span
                                                    class="ml-2">({{ number_format($chadhava->review_avg_rating ?? 0, 1) }}/5)</span>
                                            </div>
                                        </div>

                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}"
                                            class="animated-button mt-2">
                                            <span class="text-wrapper">
                                                <span class="text-slide">{{ translate('GO_PARTICIPATE') }}</span>
                                                <span class="text-slide">{{ translate('Limited_slots!') }}</span>
                                            </span>
                                            <span class="icon">
                                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}"
                                                    alt="arrow" />
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
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
    <script>
        $("#chadhdhavaInput").keyup(function() {
            var name = $("#chadhdhavaInput").val();

            if (name.length > 3) {
                // Show loader
                $("#loader").show();

                $.ajax({
                    url: "{{ url('api/v1/pooja/chadhavasearch') }}",
                    data: {
                        name: name,
                        role: "web",
                        "lang": "{{ str_replace('', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('', '-', app()->getLocale()) }}",
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
                            '<li class="list-group-item">Chadhava Not Available. Try another date name</li>'
                        );
                    }
                });
            } else {
                $("#search-results").html(''); // Clear results if input length is less than 3
            }
        });
    </script>
    <script>
  

        $('input[type="search"]').on('keyup', function () {
            var searchText = $(this).val().toLowerCase().trim();
            var found = false;

            if (searchText === '') {
              
                $('.searchable-card').show();
                $('#noResultsFound').hide(); // Make sure this matches your ID
                return;
            }

            $('.searchable-card').each(function () {
                var matchesSearch = $(this).find('.chadhava-title').text().toLowerCase().indexOf(searchText) > -1;

                if (matchesSearch) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });

            toggleNoTourMessage(found);
        });

        function toggleNoTourMessage(found) {
            if (found) {
                $('#noResultsFound').hide();
            } else {
                $('#noResultsFound').show();
            }
        }

    </script>
@endpush
