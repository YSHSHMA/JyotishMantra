@extends('layouts.front-end.app')

@section('title', translate('astrology_counseling'))

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
    <link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    {{-- main page --}}
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ ucwords(translate('astrology_counseling')) }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ ucwords(translate('astrology_counseling')) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <section class="cal_about_wrapper as_padderTop60 as_padderBottom60">
        <div class="container">
          <div class="row">
            <div class="col-lg-3 col-sm-4 col-xs-6">
                <div class="card mb-3">
                    <div class="product-single-hover shadow-none rtl">
                        <div class="overflow-hidden position-relative">
                            <div class="inline_product clickable">
                                <a href="">
                                    <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/paramarsh1.jpg')) }}"
                                    alt="">
                                </a>
                                <div class="quick-view">
                                    <a class="btn-circle stopPropagation action-product-quick-view"
                                        href="javascript:" data-product-id="">
                                        <i class="czi-eye align-middle"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="single-product-details min-height-unset">
                                <div>
                                    <a href="#" class="text-capitalize fw-semibold">
                                        फ़ोन पर ज्योतिष जी से बात
                                    </a>
                                </div>
                                <div class="justify-content-between">
                                    <div class="product-price">
                                        <del class="category-single-product-price">
                                        ₹2100
                                        </del>
                                        <span class="text-accent text-dark">
                                            ₹1500
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-4 col-xs-6">
                <div class="card mb-3">
                    <div class="product-single-hover shadow-none rtl">
                        <div class="overflow-hidden position-relative">
                            <div class="inline_product clickable">
                                <a href="">
                                    <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/paramarsh2.png')) }}"
                                    alt="">
                                </a>
                                <div class="quick-view">
                                    <a class="btn-circle stopPropagation action-product-quick-view"
                                        href="javascript:" data-product-id="">
                                        <i class="czi-eye align-middle"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="single-product-details min-height-unset">
                                <div>
                                    <a href="#" class="text-capitalize fw-semibold">
                                        फ़ोन पर ज्योतिष जी से बात
                                    </a>
                                </div>
                                <div class="justify-content-between">
                                    <div class="product-price">
                                        <del class="category-single-product-price">
                                        ₹2100
                                        </del>
                                        <span class="text-accent text-dark">
                                            ₹1500
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-4 col-xs-6">
                <div class="card mb-3">
                    <div class="product-single-hover shadow-none rtl">
                        <div class="overflow-hidden position-relative">
                            <div class="inline_product clickable">
                                <a href="">
                                    <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/paramarsh3.png')) }}"
                                    alt="">
                                </a>
                                <div class="quick-view">
                                    <a class="btn-circle stopPropagation action-product-quick-view"
                                        href="javascript:" data-product-id="">
                                        <i class="czi-eye align-middle"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="single-product-details min-height-unset">
                                <div>
                                    <a href="#" class="text-capitalize fw-semibold">
                                        फ़ोन पर ज्योतिष जी से बात
                                    </a>
                                </div>
                                <div class="justify-content-between">
                                    <div class="product-price">
                                        <del class="category-single-product-price">
                                        ₹2100
                                        </del>
                                        <span class="text-accent text-dark">
                                            ₹1500
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-4 col-xs-6">
                <div class="card mb-3">
                    <div class="product-single-hover shadow-none rtl">
                        <div class="overflow-hidden position-relative">
                            <div class="inline_product clickable">
                                <a href="">
                                    <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/paramarsh4.png')) }}"
                                    alt="">
                                </a>
                                <div class="quick-view">
                                    <a class="btn-circle stopPropagation action-product-quick-view"
                                        href="javascript:" data-product-id="">
                                        <i class="czi-eye align-middle"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="single-product-details min-height-unset">
                                <div>
                                    <a href="#" class="text-capitalize fw-semibold">
                                        फ़ोन पर ज्योतिष जी से बात
                                    </a>
                                </div>
                                <div class="justify-content-between">
                                    <div class="product-price">
                                        <del class="category-single-product-price">
                                        ₹2100
                                        </del>
                                        <span class="text-accent text-dark">
                                            ₹1500
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-4 col-xs-6">
                <div class="card mb-3">
                    <div class="product-single-hover shadow-none rtl">
                        <div class="overflow-hidden position-relative">
                            <div class="inline_product clickable">
                                <a href="">
                                    <img src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/paramarsh5.png')) }}"
                                    alt="">
                                </a>
                                <div class="quick-view">
                                    <a class="btn-circle stopPropagation action-product-quick-view"
                                        href="javascript:" data-product-id="">
                                        <i class="czi-eye align-middle"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="single-product-details min-height-unset">
                                <div>
                                    <a href="#" class="text-capitalize fw-semibold">
                                        फ़ोन पर ज्योतिष जी से बात
                                    </a>
                                </div>
                                <div class="justify-content-between">
                                    <div class="product-price">
                                        <del class="category-single-product-price">
                                        ₹2100
                                        </del>
                                        <span class="text-accent text-dark">
                                            ₹1500
                                        </span>
                                    </div>
                                </div>
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
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
<!-- <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script> -->
<script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>

@endpush
