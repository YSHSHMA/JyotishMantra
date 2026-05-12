@extends('layouts.front-end.app')
@section('title', translate('Pandit Book Karein – Pooja aur Anushthan ke liye Anubhavi Pandit'))
@php
    use App\Utils\Helpers;
    use function App\Utils\displayStarRating;
@endphp
@push('css_or_js')
    <meta name="description"
        content="Ghar baithe online pandit book karein pooja, havan, vivaah, griha pravesh aur anya dharmik kaaryon ke liye. Anubhavi aur vishwasniya pandit uplabdh Hindi ved mantraon ke saath.">
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

        .offlinepooja-name {
            font-weight: 700;
            font-size: 17px;
        }

        .opooja-name {
            font-weight: 700;
            font-size: 17px;
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

        /* banner image */
        .banner {
            width: 100%;
            height: 180px;
            /* default desktop height */
            overflow: hidden;
        }

        .banner img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            /* keeps full image, no crop */
        }

        /* ✅ Tablet view */
        @media (max-width: 1024px) {
            .banner {
                height: 140px;
            }
        }

        /* ✅ Mobile view */
        @media (max-width: 768px) {
            .banner {
                height: 100px;
            }
        }

        /* ✅ Small mobile */
        @media (max-width: 480px) {
            .banner {
                height: 80px;
            }
        }
    </style>
@endpush

@section('content')
    {{-- main page --}}
    {{-- <div class="inner-page-bg center bg-bla-7 py-4"
        style="height: 200px; overflow: hidden;">
        <img src="{{ asset('public/assets/front-end/img/offlinepooja-bg.png') }}" style="width: 100%; height: 100%; object-fit: cover; " alt="">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ ucwords(translate('Upcoming_Pandit_&_Pooja_Services_on_Mahakal.com')) }}
                    </h1>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="image-box">
        <img src="{{ asset('public/assets/front-end/img/offlinepooja-banner.png') }}" alt="banner">
    </div>

    <section class="cal_about_wrapper">
        <div class="container-fluid rtl px-0 px-md-3">

            <div class="__inline-62 pt-2">
                <?php
                $offlinepoojadataFilterString = '.all';
                ?>
                <div class="pooja-menu w-100 row">
                    <ul id="offlinepoojafilters" class="clearfix"
                        style="display: flex; align-items: center; justify-content: space-between;height:50px; width: 100%">
                        <li style="flex: 1;">
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <span class="offlinepoojafilter active"
                                    data-filter="{{ $offlinepoojadataFilterString }}">All</span>
                                @foreach ($offlinepoojaCategory as $item)
                                    <span class="offlinepoojafilter" style="padding: 10px !important;"
                                        data-filter=".{{ str_replace(' ', '', $item->name) }}">{{ @Ucwords($item->name) }}</span>
                                @endforeach
                            </div>
                        </li>
                        <div id="search-div">
                            <a href="javascript:0" class="btn btn-sm btn--primary" onclick="searchShow()">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </ul>
                    <div id="search-bar-container" class="pooja-search"
                        style="width: 100%; display: none; justify-content: center; align-items: center;padding-top:10px;">
                        <input class="form-control" type="search" autocomplete="off" id="search-text"
                            placeholder="Search your Puja for pandit booking" name="name" value=""
                            style="width: 100px;">
                        <a href="javascript:0" class="btn btn--primary ml-2" onclick="searchHide()">
                            <i class="fa fa-multiply"></i>
                        </a>
                    </div>
                </div>
                <div id="offlinepoojaportfoliolist">
                    @foreach ($allOfflinepooja as $pooja)
                        <div class="offlinepoojaportfolio all" data-cat="all">
                            <div class="portfolio-wrapper">
                                <a href="{{ route('offline.pooja.detail', $pooja->slug) }}">
                                <div class="card">
                                    <img src="{{ getValidImage(path: 'storage/app/public/offlinepooja/thumbnail/' . $pooja->thumbnail) }}"
                                        class="card-img-top puja-image" alt="{{ $pooja->thumbnail }}">
                                    <div class="card-body">
                                        <div class="w-bar h-bar bg-gradient mt-2"></div>
                                        <p class="two-lines-only offlinepooja-name">{{ $pooja->name }}</p>
                                        {{-- <div class="d-flex justify-content-between align-items-center"> --}}
                                        <!-- Devotees Count -->
                                        <div class="d-flex align-items-center">
                                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                                alt="Users" class="colored-icon"
                                                style="width: 24px; height: 24px; margin-right: 5px;">
                                            <span class="">{{ 10000 + $pooja->offline_pooja_order_count }}+
                                                Devotees</span>
                                        </div>
                                        <div class="d-flex align-items-center mt-2">
                                            {!! displayStarRating($pooja->review_avg_rating ?? 5) !!}
                                            <span
                                                class="ml-2">({{ number_format($pooja->review_avg_rating ?? 5, 1) }}/5)</span>
                                        </div>

                                        {{-- </div> --}}
                                        <a href="{{ route('offline.pooja.detail', $pooja->slug) }}"
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
                                </a>
                            </div>
                        </div>
                    @endforeach

                    @foreach ($offlinepoojas as $key => $offlinepooja)
                        @foreach ($offlinepooja as $pooja)
                            <div class="offlinepoojaportfolio {{ str_replace(' ', '', $key) }}"
                                data-cat="{{ str_replace(' ', '', $key) }}">
                                <div class="portfolio-wrapper">
                                    <div class="card">
                                        <img src="{{ getValidImage(path: 'storage/app/public/offlinepooja/thumbnail/' . $pooja['thumbnail']) }}"
                                            class="card-img-top puja-image" alt="{{ $pooja['thumbnail'] }}">
                                        <div class="card-body">
                                            <p class="two-lines-only opooja-name">{{ $pooja['name'] }}</p>
                                            <div class="Stars" style="--rating: {{ $pooja->review_avg_rating }};"
                                                aria-label="Rating of this product is 2.3 out of 5."></div>
                                            <div class=""><i class="fa fa-user"></i> <span
                                                    style="font-size: 14px">{{ 10000 + $pooja->offline_pooja_order_count }}+
                                                    Devotees</span></div>
                                            <a href="{{ route('offline.pooja.detail', $pooja['slug']) }}"
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
                            </div>
                        @endforeach
                    @endforeach
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
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            var offlinePoojafilterList = {
                init: function() {
                    // MixItUp plugin initialization
                    $('#offlinepoojaportfoliolist').mixItUp({
                        selectors: {
                            target: '.offlinepoojaportfolio',
                            filter: '.offlinepoojafilter',
                        },
                        load: {
                            filter: '{{ $offlinepoojadataFilterString }}',
                        },
                    });
                },
            };

            // Run the initialization
            offlinePoojafilterList.init();

            // Search filtering logic
            $('input[type="search"]').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                var activeCategory = $('.offlinepoojafilter.active').data('offlinepoojafilter') || '*';

                $('#offlinepoojaportfoliolist .offlinepoojaportfolio').each(function() {
                    var $this = $(this);
                    var poojaName = $this.find('.offlinepooja-name').text().toLowerCase();
                    var matchesSearch = poojaName.includes(searchText);
                    var matchesCategory = activeCategory === '*' || $this.is(activeCategory);

                    $this.toggle(matchesSearch && matchesCategory);
                });
            });

            // Category filter logic
            // $('.offlinepoojafilter').on('click', function() {
            //     $('.offlinepoojafilter').removeClass('active');
            //     $(this).addClass('active');
            //     var activeCategory = $(this).data('offlinepoojafilter') || '*';
            //     var searchText = $('input[type="search"]').val().toLowerCase();

            //     $('#offlinepoojaportfoliolist .offlinepoojaportfolio').each(function() {
            //         var $this = $(this);
            //         var poojaName = $this.find('.offlinepooja-name').text().toLowerCase();
            //         var matchesSearch = poojaName.includes(searchText);
            //         var matchesCategory = activeCategory === '*' || $this.is(activeCategory);

            //         $this.toggle(matchesSearch && matchesCategory);
            //     });
            // });
        });
    </script>
    <script>
        function showRemainingAddresses(that) {
            var id = $(that).data('id');
            var remainingDiv = document.getElementById('remainingAddresses' + id);
            if (remainingDiv.style.display === 'none') {
                remainingDiv.style.display = 'block';
            } else {
                remainingDiv.style.display = 'none';
            }
        }
        $(document).ready(function() {
            $('#search-icon').click(function() {
                $('#search-bar-container').toggle();
                $('#search-bar').focus();
            });
        });

        function searchShow() {
            $('#search-div').hide();
            $('#search-bar-container').show();
        }

        function searchHide() {
            $('#search-div').show();
            $('#search-text').val('');
            $('#search-bar-container').hide();
        }
    </script>
@endpush
