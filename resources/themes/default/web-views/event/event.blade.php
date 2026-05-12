@extends('layouts.front-end.app')

@section('title', translate('Dharmik Karyakram aur Utsav – Mahakal.com Par Vishesh Aayojan Book Karein'))
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
<meta name="description" content="Mahakal.com par bhajan sandhya, sankirtan, katha, havan, saamaahik poojan aur anya dharmik karyakramon ki jaankari paayein aur apne shahar mein aayojan book karein. Divyata se bhare aayojan mein bhaag lein.">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/animationbutton.css') }}">
<style>
    #filters li span.active {
        background: linear-gradient(to top, #fe9802 30%, transparent 39%);
    }

    .gold {
        color: #fe9802;
    }

    .responsive-bg {
        padding-top: 6rem !important;
        padding-bottom: 7rem !important;
        background:url("{{ asset('public/assets/front-end/img/slider/events.jpg') }}") no-repeat;
        /* background:url("{{ asset('assets/front-end/img/slider/events.jpg') }}") no-repeat; */
        background-size: cover;
        background-position: center center;
    }

    @media (max-width: 768px) {
        .responsive-bg {
            padding-top: 2.91rem !important;
            padding-bottom: 3rem !important;
            /* background:url("{{ asset('assets/front-end/img/slider/events1.jpg') }}") no-repeat; */
            background:url("{{ asset('public/assets/front-end/img/slider/events1.jpg') }}") no-repeat;
            background-size: cover;
            background-position: center center;
        }

        /* .single-product-details{
            font-size: 11px;
        }
         */
        .font-size-set {
            font-size: 12px;
        }

        .pooja-calendar {
            font-size: 9px;
            font-weight: 500;
        }
    }

    .one-line-show {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('content')

<div class="inner-page-bg center bg-bla-7 py-4 responsive-bg">
    <div class="container">
        <div class="row all-text-white">
            <div class="col-md-12 align-self-center">
                <h1 class="innerpage-title">{{ ucwords(translate('all_Event')) }}</h1>
                <small class="innerpage-title"><a href="{{ url('/') }}" class="text-white"><i class="fa fa-home"></i> Home</a> > {{ ucwords(translate('all_Event')) }}</small>
                <div class="mt-2">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-12 d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">
                                <div class="input-group w-50 d-none d-sm-flex" style="opacity: 0.6;">
                                    <input type="text" class="form-control border-0 fw-bold getAddress_google" placeholder="{{ translate('search_by_location') }}" style="color: #4b566b;">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>

                                <div class="input-group w-100 d-sm-none" style="opacity: 0.6;">
                                    <input type="text" class="form-control border-0 fw-bold getAddress_google" placeholder="{{ translate('search_by_location') }}" style="color: #4b566b;">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                                <input type='hidden' name="lat" class='lat_event'>
                                <input type='hidden' name="long" class='long_event'>
                            </div>
                        </div>


                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- <section class="cal_about_wrapper as_padderTop60 as_padderBottom60"> -->
<div class="__inline-62 py-3">
    <div class="container-fluid rtl">
        <ul id="filters" class="clearfix">
            <li>
                <span class="filter active  text-center" data-filter=".all">
                    <span class="d-block">
                        <i class="tio-medal" style="font-size: 40px;color:#fe9802"></i>
                    </span>
                    All
                </span>
            </li>
            @if($categories)
            @foreach($categories as $val)
            <li>
                <span class="filter  text-center" data-filter=".{{Str::slug($val['category_name'])}}">
                    <span class="d-block">
                        <img src="{{ getValidImage(path: 'storage/app/public/event/category/' . $val['image'], type: 'product') }}" class="card-img-top puja-image" style="width: 40px; height: 40px;" alt="...">
                    </span>
                    {{ $val['category_name']}}
                </span>
            </li>
            @endforeach
            @endif
            <li class="float-right">
                <div class="input-group-overlay search-form-mobile text-align-direction">
                    <div class="d-flex align-items-center gap-2">
                        <input class="form-control" type="search" autocomplete="off" placeholder="Search for Event Name..." name="search_key">
                    </div>
                </div>
            </li>
        </ul>
        <!-- <ul class="list-group list-group-horizontal clearfix mt-2">
            <li class="list-group-item p-0 border-0 filter_subcategory" style="margin-left:1%">
                <span class="btn btn-outline-secondary square-btn btn-sm subcategory" style="padding: 4px 17px;" data-filter=".upcommingall">{{ translate('Upcomming')}}</span>
            </li>
            <li class="list-group-item p-0 border-0 filter_subcategory" style="margin-left:1%">
                <span class="btn btn-outline-secondary square-btn btn-sm subcategory" style="padding: 4px 17px;" data-filter=".runningall">{{ translate('Running')}}</span>
            </li>
            <li class="list-group-item p-0 border-0 filter_subcategory" style="margin-left:1%">
                <span class="btn btn-outline-secondary square-btn btn-sm subcategory" style="padding: 4px 17px;" data-filter=".liveall">{{ translate('Live')}}</span>
            </li>
        </ul> -->

        <?php
        $langs = (str_replace('_', '-', app()->getLocale()) == 'in') ? 'hi' : str_replace('_', '-', app()->getLocale());
        $eventList = [];
        if ($eventData) {
            foreach ($eventData as $newp) {
                if (!empty($newp['all_venue_data']) && json_decode($newp['all_venue_data'], true)) {
                    $venuePrices = [];
                    foreach (json_decode($newp['all_venue_data'], true) as $check) {
                        $currentDateTime = new DateTime();
                        $eventDateTime = DateTime::createFromFormat('d-m-Y h:i A', date('d-m-Y', strtotime($check['date'])) . ' ' . date('h:i A', strtotime($check['start_time'])));
                        if ($eventDateTime && $eventDateTime > $currentDateTime) {
                            $venue_name =  ((!empty($check[$langs . '_event_venue_full_address'] ?? '')) ? ($check[$langs . '_event_venue_full_address'] ?? '') : $check[$langs . '_event_venue']); //$check[$langs . '_event_venue'];
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
                    $eventList[] = [
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
        usort($eventList, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        ?>
        <div id="portfoliolist" class="row">
            @foreach($eventList as $eventData)
            <?php $newp = $eventData['event']; ?>
            <div class="portfolio {{ Str::slug($newp['categorys']['category_name'] ?? '') }} {{ $newp['showvalue'] ?? '' }}" data-man_category="{{ Str::slug($newp['categorys']['category_name'] ?? '') }}">
                <div class="portfolio-wrapper">
                    <div class="card">
                        <a href="{{ route('event-details',[($newp['slug']??'')])}}">
                            <img src="{{ getValidImage(path: 'storage/app/public/event/events/' . $newp['event_image'], type: 'product') }}" class="card-img-top puja-image" alt="...">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title font-weight-700 pooja-heading underborder one-line-show">{{ ucwords(($newp['event_name'] ?? "")) }}</h5>
                            <div class="one-line-show"><i class="tio-poi text-warning"></i><span class="font-size-set"> {{ $eventData['venue_name'] }}</span></div>
                            <div class="font-weight-bold one-line-show"><i class="tio-event text-warning"></i> {{ translate('date')}} : {{ $eventData['formatted_date'] }} {{ $eventData['formatted_time'] }}</div>
                            @if(($eventData['informational_status'] == 0))
                            @if (!empty($eventData['venuePrices']))
                            <span><i class="tio-mma text-warning"></i> {{ translate('Tickets_starts_from')}}:</span><span class="font-weight-bolder"> {{ min($eventData['venuePrices']) }}/-</span>
                            @endif
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Devotees Count -->
                                <div class="d-flex align-items-center">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                        alt="Users" class="colored-icon"
                                        style="width: 24px; height: 24px; margin-right: 5px;">
                                    <span class="pooja-calendar">{{ 10000 + $newp['event_order_review_count']??0 }} + People</span>
                                </div>

                                <!-- Star Rating -->
                                <div class="d-flex align-items-center">
                                    {!! displayStarRating($newp['review_avg_star']??0) !!}
                                    <span
                                        class="ml-2">({{ number_format($newp['review_avg_star'] ?? 0, 1) }}/5)</span>
                                </div>
                            </div>
                            @else
                            <span class="font-weight-bold one-line-show"><i class="tio-info_outined text-warning">info_outined</i> {{ translate('only_Information')}}</span>
                            <div class="d-flex justify-content-between align-items-center">&nbsp;</div>
                            @endif
                            <a href="{{ route('event-details',[($newp['slug']??'')])}}" class="text-white animated-button mt-2">
                                @if(($eventData['informational_status'] == 0))
                                <span class="text-wrapper">
                                    <span class="text-slide">{{ translate('book_now')}}</span>
                                    <span class="text-slide">{{ translate('limited_slots')}}!</span>
                                </span>
                                @else
                                <span class="text-wrapper d-inline mb-4">
                                    <span class="text-slide">{{ translate('Know_About_The_Event')}}</span>
                                    <span class="text-slide">{{ translate('Know_About_The_Event')}}</span>
                                </span>
                                @endif
                                <span class="icon">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}" alt="arrow">
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
<!-- </section> -->

@endsection
@push('script')
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
<!-- <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script> -->
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>


<script type="text/javascript">
    // $(document).ready(function() {
    //     var activeParentCategory = '.all';
    //     filterItems();
    //     $('.filter').on('click', function() {
    //         activeParentCategory = $(this).data('filter');
    //         $('.filter').removeClass('active');
    //         $(this).addClass('active');
    //         $('.subcategory').removeClass('active');
    //         filterItems();
    //     });


    //     $('.subcategory').on('click', function() {
    //         $('.subcategory').removeClass('active');
    //         $(this).addClass('active');

    //         filterItems();
    //     });

    //     function filterItems() {
    //         var activeSubCategory = $('.subcategory.active').data('filter') || '';
    //         console.log(activeSubCategory);
    //         if (activeParentCategory === '.all' && activeSubCategory === '') {
    //             $('.portfolio').fadeIn();
    //         } else {
    //             $('.portfolio').hide();
    //             if (activeParentCategory !== '.all' && activeSubCategory !== '') {
    //                 $(activeParentCategory + activeSubCategory).fadeIn();
    //             } else if (activeParentCategory !== '.all') {
    //                 $(activeParentCategory).fadeIn();
    //             } else if (activeSubCategory !== '') {
    //                 $(activeSubCategory).fadeIn();
    //             }
    //         }
    //     }
    // });




    $(document).ready(function() {
        var activeParentCategory = '.all';
        filterItems();

        // Filter by category
        $('.filter').on('click', function() {
            activeParentCategory = $(this).data('filter');
            $('.filter').removeClass('active');
            $(this).addClass('active');
            $('.subcategory').removeClass('active');
            filterItems();
        });

        // Filter by subcategory
        $('.subcategory').on('click', function() {
            $('.subcategory').removeClass('active');
            $(this).addClass('active');
            filterItems();
        });

        // Search input event
        $('input[name="search_key"]').on('keyup', function() {
            filterItems();
        });

        function filterItems() {
            var activeSubCategory = $('.subcategory.active').data('filter') || '';
            // var searchTerm = $('input[name="search_key"]').val().toLowerCase();

            var searchTerm = $('input[name="search_key"]').val();
            if (searchTerm) {
                searchTerm = searchTerm.toLowerCase();
            } else {
                searchTerm = '';
            }

            console.log(activeSubCategory);
            console.log(searchTerm);

            $('.portfolio').hide();

            $('.portfolio').filter(function() {
                var categoryMatch = true;
                var subCategoryMatch = true;

                if (activeParentCategory !== '.all') {
                    categoryMatch = $(this).is(activeParentCategory);
                }

                if (activeSubCategory !== '') {
                    subCategoryMatch = $(this).is(activeSubCategory);
                }

                var nameMatch = $(this).find('.card-title').text().toLowerCase().includes(searchTerm);

                // Return true if it matches all conditions
                return categoryMatch && subCategoryMatch && nameMatch;
            }).fadeIn(); // Fade in matched items
        }
    });


    $(".getAddress_google").each(function() {
        let inputElement = this;
        let autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['establishment'],
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                $(inputElement).val('');
                return;
            }
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            $(".lat_event").val(lat);
            $('.long_event').val(lng);
        });
    });
</script>

@endpush