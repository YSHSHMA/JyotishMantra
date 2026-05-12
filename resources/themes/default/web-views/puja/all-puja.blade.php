@extends('layouts.front-end.app')

 @section('title', translate('Sabhi Pooja Sevaayein – Ghar Baithe Online Pooja Book Karein | Mahakal.com'))

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
    <meta property="twitter:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!--poojafilter-css-->
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
        rel="stylesheet" type="text/css" />
@endpush

@section('content')
    {{-- main page --}}
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ ucwords(translate('all_puja')) }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ ucwords(translate('all_puja')) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <section class="cal_about_wrapper as_padderTop60 as_padderBottom60">
        <div class="container-fluid rtl px-0 px-md-3">
            <div class="__inline-62 pt-3">
                <ul id="filters" class="clearfix">
                    <li><span class="filter active" data-filter="all">All</span></li>
                    <li><span class="filter" data-filter=".app">Samsya Nivaran</span></li>
                    <li><span class="filter" data-filter=".card">Dosh Nivaran Pooja</span></li>
                    <li><span class="filter" data-filter=".icon">Paath&Jaap Pooja</span></li>
                    <li><span class="filter" data-filter=".logo">Other</span></li>
                    <li class="float-right">
                        <!-- <div class="input-group-overlay search-form-mobile text-align-direction">
                                    <form action="" type="submit" class="search_form">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-control appended-form-control search-bar-input" type="search" autocomplete="off" placeholder="Search for items..." name="name" value="">
                                            
                                            <span class="close-search-form-mobile fs-14 font-semibold text-muted d-md-none" type="submit">
                                                Cancel
                                            </span>
                                        </div>
                                    </form>
                                </div> -->
                    </li>
                </ul>
                <div id="portfoliolist">
                    <div class="portfolio app" data-cat="app">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                                    class="card-img-top puja-image" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio app" data-cat="app">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/amarnath-cave.jpg')) }}"
                                    class="card-img-top puja-image" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio app" data-cat="app">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/ganga-aarti.jpeg')) }}"
                                    class="card-img-top puja-image" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio card" data-cat="card">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/Jagannath-puri.jpg')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio card" data-cat="card">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kedarnath.jpg')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio card" data-cat="card">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/Anant-Kaal-Sarp-Dosh.jpg')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio icon" data-cat="icon">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kaal-sarp-dosh.jpg')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio icon" data-cat="icon">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/YRKSP101-1.jpg')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio icon" data-cat="icon">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kal-sarp-pooja.png')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio logo" data-cat="logo">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kaal-sarp-dosh-2.jpeg')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio logo" data-cat="logo">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/Your-Kaal-Sarp-Dosh.jpg')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio logo" data-cat="logo">
                        <div class="portfolio-wrapper">
                            <div class="card">
                                <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/KAAL_SARP_DOSH-PUJA.jpg')) }}"
                                    class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-700">Kaal Sarp Dosha Nivaran Puja and Rahu Ketu
                                        Shanti Yagya...</h5>
                                    <p class="card-text"><i class="fa fa-map-marker"></i> Shri Takshakeshwar Tirth
                                        Temple, Prayagraj, Uttar Pradesh</p>
                                    <p class="card-text"><i class="fa fa-calendar"></i> 3 April, Wednesday, Chaitra
                                        Krishna Navami</p>
                                    <a href="#"
                                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">GO
                                        PARTICIPATE </a>
                                </div>
                            </div>
                        </div>
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
                            filter: 'all'
                        }
                    });

                }

            };

            // Run the show!
            filterList.init();


        });
    </script>
@endpush
