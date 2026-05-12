@extends('layouts.front-end.app')
@section('title', translate('all_pooja'))
@php
    use App\Utils\Helpers;
    use function App\Utils\getNextPoojaDay;
    use function App\Utils\getNextChadhavaDay;
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
    <meta
        property="twitter:description"content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!--poojafilter-css-->
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
            .fixed-search {
            position: sticky;
            top: 84px;
            left: 0; 
            right: 0;
            background-color: white;
            padding: 10px 20px; 
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);  */
            z-index: 1000; 
        }
        .pooja-menu{
            position: sticky;
            top: 172px;
            left: 0; 
            right: 0;
            background-color: white;
            /* box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);  */
            z-index: 1000; 
        }

        .pooja-search {
            margin: 0 auto;
            max-width: 600px; 
        }
    </style>
@endpush

@section('content')
    {{-- main page --}}
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ ucwords(translate('Upcoming_Poojas_On_Mahakal.com')) }}</h1>
                    <span class="font-normal font-normal">
                        {{ translate('Book_puja_online_in_your_name_and_gotra_receive_the_puja _video_along_with_the_tirth_prasad_and_gain_blessings_from_the_Divine') }}</span>
                    
                       
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Fixed search form -->
    <div class="fixed-search">
        <div class="container">
            <div class="row pt-3 pb-2">
                <div class="col-md-6 offset-3">
                    <div class="pooja-search">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-control form-control-md" type="search" autocomplete="off"
                                placeholder="Search for puja name mahakal com" name="name">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="cal_about_wrapper">
        <div class="container-fluid rtl px-0 px-md-3">
        
            <div class="__inline-62 pt-2">
                <?php
                $dataFilterString = '';
                foreach ($subcategory as $subcat) {
                    $dataFilterString .= '.' . $subcat->slug . ',';
                }
                $dataFilterString = rtrim($dataFilterString, ', ');
                
                ?>
                <ul id="filters" class="clearfix pooja-menu">
                    <li><span class="filter active" data-filter="{{ $dataFilterString }}">All</span></li>
                    @foreach ($subcategory as $item)
                        <li><span class="filter" data-filter=".{{ $item->slug }}">{{ @Ucwords($item->name) }}</span>
                        </li>
                    @endforeach

                </ul>
                <div id="portfoliolist">
                    @foreach ($PoojaShow as $poojaD)
                        @if ($poojaD->pooja_type == '0')
                            @include('web-views.partials._pooja_weekly', ['poojaD' => $poojaD])
                        @else
                            @if (!empty($poojaD['schedule']))
                                @include('web-views.partials._pooja_special', ['poojaD' => $poojaD])
                            @endif
                        @endif
                    @endforeach

                    {{-- VIP POOJA --}}
                    @foreach ($vippooja as $vip)
                        <div class="portfolio vip-pooja" data-cat="vip-pooja">
                            <div class="portfolio-wrapper">
                                <div class="card">
                                    <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                                        <span class="direction-ltr blink d-block">{{ translate('vip_pooja') }}</span>
                                    </span>
                                    @if (!empty($vip->thumbnail))
                                        <a href="{{ route('vip.details', $vip->slug) }}"><img
                                                src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $vip->thumbnail) }}"
                                                class="card-img-top puja-image" alt="{{ $vip->thumbnail }}"></a>
                                    @else
                                        <a href="{{ route('vip.details', $vip->slug) }}"><img
                                                src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                                class="card-img-top puja-image" alt="..."></a>
                                    @endif
                                    <div class="card-body">
                                        <p class="pooja-heading underborder">{{ strtoupper($vip->pooja_heading) }}
                                        </p>
                                        <div class="w-bar h-bar bg-gradient mt-2"></div>
                                        <p class="pooja-name">{{ Str::words($vip->name, 20, '...') }}</p>
                                        <p class="card-text">{{ $vip->short_benifits }}</p>

                                        <a href="{{ route('vip.details', $vip['slug']) }}"
                                            class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">
                                            {{ translate('GO_PARTICIPATE') }} </a>
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
                                    <span class="for-discount-value  pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
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
                                        <p class="pooja-heading underborder">{{ strtoupper($anusvip->pooja_heading) }}
                                        </p>
                                        <div class="w-bar h-bar bg-gradient mt-2"></div>
                                        <p class="pooja-name">{{ Str::words($anusvip->name, 20, '...') }} </p>
                                        <p class="card-text">{{ $anusvip->short_benifits }}</p>

                                        <a href="{{ route('anushthan.details', $anusvip['slug']) }}"
                                            class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">
                                            {{ translate('GO_PARTICIPATE') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                                                class="card-img-top puja-image" alt="{{ $chadhava->thumbnail }}"></a>
                                    @else
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}"><img
                                                src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                                class="card-img-top puja-image" alt="..."></a>
                                    @endif
                                    <div class="card-body">
                                        <p class="pooja-heading underborder">{{ strtoupper($chadhava->pooja_heading) }}
                                        </p>
                                        <div class="w-bar h-bar bg-gradient mt-2"></div>
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}">
                                            <p class="pooja-name">{{ Str::words($chadhava->name, 20, '...') }}</p>
                                        </a>
                                        <p class="card-text">{{ $chadhava->short_details }}</p>
                                        <p class="pooja-venue"><i
                                                class="fa fa-map-marker"></i>{{ $chadhava->chadhava_venue }}</p>
                                        @php

                                            $chadhavaDetails = '';
                                            $ChadhavaWeek = json_decode($chadhava->chadhava_week);
                                            $nextChadhavaDay = getNextChadhavaDay($ChadhavaWeek);
                                            if ($nextChadhavaDay) {
                                                $ChadhavanextDate = $nextChadhavaDay->format('Y-m-d H:i:s');
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

                                        @endphp
                                        @if ($chadhava->chadhava_type == 0)
                                            <p class="pooja-calendar"><i class="fa fa-calendar"></i>
                                                {{ date('d', strtotime($ChadhavanextDate)) }},
                                                {{ translate(date('F', strtotime($ChadhavanextDate))) }} ,
                                                {{ translate(date('l', strtotime($ChadhavanextDate))) }}</p>
                                        @else
                                            <p class="pooja-calendar"><i class="fa fa-calendar"></i>
                                                {{ date('d', strtotime($ChadhavaearliestDate)) }},
                                                {{ translate(date('F', strtotime($ChadhavaearliestDate))) }} ,
                                                {{ translate(date('l', strtotime($ChadhavaearliestDate))) }}
                                            </p>
                                        @endif
                                        <a href="{{ route('chadhava.details', $chadhava->slug) }}"
                                            class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">
                                            {{ translate('GO_PARTICIPATE') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            $('input[type="search"]').on('keyup', function() {
                var searchText = $(this).val()
                    .toLowerCase();
                var activeCategory = $('.filter.active').data('filter');

                $('#portfoliolist .portfolio').each(function() {
                    var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(
                        searchText) > -1;
                    var matchesCategory = $(this).is(
                        activeCategory);

                    $(this).toggle(matchesSearch && matchesCategory);
                });
            });

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
    </script>
@endpush
