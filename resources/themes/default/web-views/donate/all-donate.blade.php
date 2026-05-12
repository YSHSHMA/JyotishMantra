@extends('layouts.front-end.app')

@section('title', translate('दान करें – महाकाल सेवा में भाग लें और पुण्य प्राप्त करें | Mahakal.com'))
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
<meta name="description" content="Mahakal.com पर धार्मिक अनुष्ठान, अन्नदान, गौसेवा और मंदिर सेवा के लिए दान करें। आपका छोटा सा सहयोग भी बड़ा पुण्य बना सकता है। आज ही ऑनलाइन दान करें।">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .list-group-item.active {
        z-index: 2;
        color: #000;
        background-color: #ffffff;
        border-color: #fe696a;
    }

    .padding-set-progress {
        padding: 23px;
    }

    @media (max-width: 500px) {
        .padding-set-progress {
            padding: 0px;
        }
    }
</style>
<style>
    .responsive-bg {
        padding-top: 4rem !important;
        padding-bottom: 4rem !important;
        background:url("{{ asset('public/assets/front-end/img/slider/donation1.jpg') }}") no-repeat;
        background-size: cover;
        background-position: center center;
    }

    @media (max-width: 768px) {
        .responsive-bg {
            padding-top: 1rem !important;
            padding-bottom: 2rem !important;
            background:url("{{ asset('public/assets/front-end/img/slider/donation.jpg') }}") no-repeat;
            background-size: cover;
            background-position: center center;
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

    .one-lines-only {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        border-image: linear-gradient(to left, #b60000, #ffffff) 0 0 1;
    }
</style>
@endpush

@section('content')

<!-- <div class="inner-page-bg center bg-bla-7 responsive-bg">
    <div class="container">
        <div class="row all-text-white">
            <div class="col-md-12 align-self-center">
                <h1 class="innerpage-title">{{ ucwords(translate('Donate')) }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">{{ ucwords(translate('Donate')) }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div> -->

    <div class="image-box">
        <img src="{{ asset('public/assets/front-end/img/donate-banners.png') }}" alt="donate banner">
    </div>
<!-- <section class="cal_about_wrapper as_padderTop60 as_padderBottom60"> -->
<section class="col-lg-12">
    <!-- <div class="container-fluid rtl px-0 px-md-3"> -->
    <div class="__inline-62 pt-3">
        <ul id="filters" class="clearfix">
            <li><span class="filter active category" data-filter=".outdonate" onclick="toggleSubcategories('category')">{{ translate('Ads')}}</span></li>
            @if($categories)
            @foreach($categories as $val)
            <li><span class="filter" data-filter=".{{ $val['slug']}}" onclick="toggleSubcategories('notuse')">{{ $val['name']}}</span></li>
            @endforeach
            @endif
            @if($donateList)
            <li><span class="filter subcategory" data-filter=".indonate" onclick="toggleSubcategories('subcategory')">{{ translate('Mahakal.com') }}</span></li>
            @endif
            <li class="float-right">
                <div class="input-group-overlay search-form-mobile text-align-direction">
                    <div class="d-flex align-items-center gap-2">
                        <input class="form-control" type="search" autocomplete="off" placeholder="Search for items..." name="name" value="">
                    </div>
                </div>
            </li>
        </ul>
        <ul class="list-group list-group-horizontal clearfix categoryd-none" style="overflow-y: scroll;">
            @if($purpose)
            <li class="list-group-item p-0 border-0 filter rounded text-center" style="margin-left:1%;padding: 1px 6px 0px 6px !important;" onclick="$('.category').addClass('active')" data-filter=".all">
                <span class="d-block">
                    <i class="tio-medal" style="font-size: 40px;color:#fe9802"></i>
                </span>
                <span class=" square-btn btn-sm subcategory d-block">
                    {{ translate('All') }}
                </span>
            </li>
            @foreach($purpose as $pur)
            <li class="list-group-item p-0 border-0 filter rounded text-center" style="margin-left:1%;padding: 1px 6px 0px 6px !important;" onclick="$('.category').addClass('active')" data-filter=".{{ $pur['slug'] }}all">
                <span class="d-block">
                    <img src="{{ getValidImage(path: 'storage/app/public/donate/purpose/' . $pur['image'], type: 'product') }}" alt="" style="width: 40px; height: 40px;">
                </span>
                <span class=" square-btn btn-sm subcategory d-block">
                    {{ $pur['name'] }}
                </span>
            </li>
            @endforeach
            @endif
        </ul>
        <ul class="list-group list-group-horizontal clearfix subcategoryd-none d-none">
            @if($purpose)
            @foreach($purpose as $pur)
            <li class="list-group-item p-0 border-0 filter rounded text-center" style="margin-left:1%;padding: 1px 6px 0px 6px !important;" onclick="$('.category').addClass('active')" data-filter=".{{ $pur['slug'] }}">
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ getValidImage(path: 'storage/app/public/donate/purpose/' . $pur['image'], type: 'product') }}" alt="" style="width: 40px; height: 40px;">
                    <span class="square-btn btn-sm subcategory">
                        {{ $pur['name'] }}
                    </span>
                </div>
            </li>

            @endforeach
            @endif
        </ul>

        <div class="row mt-2 px-2" id="portfoliolist" style="height: auto;">
            @if($donateList)
            @foreach($donateList as $newp)
            <div class="portfolio {{ Str::slug($newp['showvalue']) }} {{ $newp['Purpose']['slug'] }} all all" data-man_category="{{ Str::slug($newp['showvalue']) }}">
                <div class="portfolio-wrapper">
                    <div class="card">
                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                            <?php
                            $perpouses  = \App\Models\DonateCategory::where('id', $newp['purpose_id'])->first();
                            $newp['p_type'] = $perpouses['name'] ?? $newp['p_type'];

                            $target = $newp['set_requirement_amount'];
                            $collected = \App\Models\DonateAllTransaction::where('type', 'donate_ads')->where('ads_id', $newp['id'])->where('amount_status', 1)->sum('amount');
                            $progress = $target > 0 ? round(($collected / $target) * 100) : 0;
                            ?>
                            <span class="direction-ltr blink d-block">{{ $newp['p_type'] }}</span>
                        </span>

                        <a href="{{ route('all-donate_ads',[($newp['slug']??'')])}}">
                            <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/' . $newp['image'], type: 'product') }}" class="card-img-top puja-image" alt="...">
                        </a>

                        <div class="card-body">
                            <h5 class="font-weight-700 pooja-heading one-lines-only">
                                {{ (\App\Models\DonateTrust::where('id',$newp['trust_id'])->first()['trust_name'] ?? translate('Mahakal.com_Trust')) }}
                            </h5>
                            <h6 class="font-weight-700 two-lines-only">{{ ucwords(($newp['name']??""))}}</h6>
                            @if($target > 0)
                            <div class="progress mb-2" style="height: 18px; border-radius: 50px; background: #f1f1f1;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $progress }}%
                                </div>
                            </div>
                            <small class="d-block text-muted">
                                <b>{{ webCurrencyConverter(amount: (float)$collected ?? 0) }}</b> Received Out Of <b>{{ webCurrencyConverter(amount: (float)$target ?? 0) }}</b>
                            </small>
                            @else
                            <div class="padding-set-progress"> </div>
                            @endif
                            <a href="{{ route('all-donate_ads',[($newp['slug']??'')])}}"
                                class="btn btn--primary btn-block btn-shadow mt-3 font-weight-bold">
                                {{ translate('donate_now')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @endif
            @if($trustDonate)
            @foreach($trustDonate as $trust)
            <div class="portfolio {{Str::slug($trust['c_name'])}}" data-cat="{{Str::slug($trust['c_name'])}}">
                <div class="portfolio-wrapper">
                    <div class="card">
                        <a href="{{ route('all-donate_trust',[($trust['slug']??'')])}}">
                            <img src="{{ getValidImage(path: 'storage/app/public/donate/trust/' . ($trust['theme_image']??''), type: 'product') }}" class="card-img-top puja-image" alt="...">
                        </a>
                        <div class="card-body">
                            <h6 class="pooja-heading underborder font-weight-700 two-lines-only">{{ ucwords(($trust['trust_name']??""))}}</h6>

                            <a href="{{ route('all-donate_trust',[($trust['slug']??'')])}}" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">{{ translate('donate_now')}} </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
            @if($donateinhouse)
            @foreach($donateinhouse as $inhou)
            <div class="portfolio {{ Str::slug($inhou['showvalue']) }} {{ $inhou['p_type'] }}" data-cat="{{ $inhou['showvalue'] }}">
                <div class="portfolio-wrapper">
                    <div class="card">
                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                            <?php
                            $perpouses  = \App\Models\DonateCategory::where('id', $inhou['purpose_id'])->first();
                            $inhou['p_type'] = $perpouses['name'] ?? $inhou['p_type'];

                            // Example values
                            $target = $inhou['target_amount'] ?? 50000;
                            $collected = $inhou['collected_amount'] ?? 5000;
                            $progress = $target > 0 ? round(($collected / $target) * 100) : 0;
                            ?>
                            <span class="direction-ltr blink d-block">{{ $inhou['p_type'] }}</span>
                        </span>

                        <a href="{{ route('all-donate_ads',[($inhou['slug']??'')])}}">
                            <img src="{{ getValidImage(path: 'storage/app/public/donate/ads/' . $inhou['image'], type: 'product') }}"
                                class="card-img-top puja-image" alt="...">
                        </a>

                        <div class="card-body">
                            <h5 class="font-weight-700 pooja-heading underborder one-lines-only">
                                {{ translate('Mahakal.com_Trust') }} &nbsp;
                            </h5>
                            <h6 class="font-weight-700 two-lines-only">{{ ucwords(($inhou['name']??""))}}</h6>

                            @if($target > 0)
                            <div class="progress mb-2" style="height: 18px; border-radius: 50px; background: #f1f1f1;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $progress }}%
                                </div>
                            </div>
                            <small class="d-block text-muted">
                                <b>{{ webCurrencyConverter(amount: (float)$collected ?? 0) }}</b> Received Out Of <b>{{ webCurrencyConverter(amount: (float)$target ?? 0) }}</b>
                            </small>
                            @else
                            <div class="padding-set-progress"> </div>
                            @endif
                            <a href="{{ route('all-donate_ads',[($inhou['slug']??'')])}}"
                                class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">
                                {{ translate('donate_now')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @endif
        </div>
    </div>
    <!-- </div> -->
</section>
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
    $(document).ready(function() {
        var filterList = {
            init: function() {
                $('#portfoliolist').mixItUp({
                    selectors: {
                        target: '.portfolio',
                        filter: '.filter'
                    },
                    load: {
                        filter: '.outdonate'
                    }
                });
            }
        };

        filterList.init();

        // Search filter function
        $('input[type="search"]').on('keyup', function() {
            var searchText = $(this).val().toLowerCase(); // Get the search input value and convert to lowercase
            var activeCategory = $('.filter.active').data('filter'); // Get the active category filter

            // Filter items based on both search text and active category
            $('#portfoliolist .portfolio').each(function() {
                var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1; // Check if the item matches the search text
                var matchesCategory = $(this).is(activeCategory); // Check if the item matches the active category

                // Show or hide the item based on both filters
                $(this).toggle(matchesSearch && matchesCategory);
            });
        });


        $('.filter').on('click', function() {
            $('.filter').removeClass('active');
            $(this).addClass('active');

            var activeCategory = $(this).data('filter');
            var searchText = $('input[type="search"]').val().toLowerCase();


            $('#portfoliolist .portfolio').each(function() {
                var matchesSearch = $(this).find('.card-title').text().toLowerCase().indexOf(searchText) > -1;
                var matchesCategory = $(this).is(activeCategory);

                $(this).toggle(matchesSearch && matchesCategory);
            });
        });
    });

    // $(function() {
    //     var filterList = {
    //         init: function() {
    //             $('#portfoliolist').mixItUp({
    //                 selectors: {
    //                     target: '.portfolio',
    //                     filter: '.filter'
    //                 },
    //                 load: {
    //                     filter: '.outdonate'
    //                 }
    //             });
    //         }
    //     };
    //     filterList.init();
    // });



    function toggleSubcategories(type) {
        if (type === 'category') {
            $('.categoryd-none').removeClass('d-none');
            $('.subcategoryd-none').addClass('d-none');
        } else if (type === 'subcategory') {
            $('.subcategoryd-none').removeClass('d-none');
            $('.categoryd-none').addClass('d-none');
        } else {
            $('.subcategoryd-none').addClass('d-none');
            $('.categoryd-none').addClass('d-none');
        }
    }
</script>

@endpush