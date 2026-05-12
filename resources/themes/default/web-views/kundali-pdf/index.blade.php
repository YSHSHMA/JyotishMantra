@extends('layouts.front-end.app')

@section('title', translate('Kundali PDF Download Karein: Janm Kundali Report Hindi Mein'))

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
<meta property="description"
    content="Apni kundali ki satik jankari ab PDF mein paayen. Janm tithi aur samay ke aadhar par banvayein vistrit kundali report Hindi mein ">
<style>
    .feature-product-title {
        text-align: center;
        font-size: 22px;
        margin-top: 15px;
        font-style: normal;
        font-weight: 700;
    }

    .button-title img {
        width: 80px;
        margin-bottom: 10px;
    }

    .button-title .text {
        color: #fff;
        font-size: 20px;
        font-weight: 600;
    }
</style>
<style>
    .responsive-bg {
        padding-top: 4rem !important;
        padding-bottom: 4rem !important;
        background:url("{{ asset('public/assets/front-end/img/slider/kundali-pdf.jpg') }}") no-repeat;
        background-size:cover;
        background-position:center center;
    }

    @media (max-width: 768px) {
        .responsive-bg {
            padding-top: 1rem !important;
            padding-bottom: 2rem !important;
            background:url("{{ asset('public/assets/front-end/img/slider/kundali-pdf1.jpg') }}") no-repeat;
        background-size:cover;
        background-position:center center;
        }
    }
</style>
@endpush

@section('content')
<div class="inner-page-bg center bg-bla-7 responsive-bg">
    <div class="container">
        <div class="row all-text-white">
            <div class="col-md-12 align-self-center">
                <h1 class="innerpage-title">{{ translate('kundali') }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white">
                            <i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">{{ translate('kundali') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container py-2 rtl text-align-direction">
    <!--<h2 class="text-center mb-3 headerTitle">{{ translate('return_policy') }}</h2>-->
    <div class="">
        <div class="card-body text-justify">
            <div class="row">
                <div class="col-md-12">
                    <div class="feature-product-title mt-0">
                        जन्म पत्रिका PDF
                        <h4 class="mt-2 height-10">
                            <span class="divider">&nbsp;</span>
                        </h4>
                    </div>

                </div>
            </div>

            <div class="row">
                @foreach ($kundali_info as $key => $vals)
                <div class="col-md-6">
                    <div class="card mb-3">
                        <a href="{{ route('kundali-pdf.information', ['type'=>$vals['name'],'id'=>$vals['id']]) }}">
                            <img src="{{ getValidImage(path: 'storage/app/public/birthjournal/image/' . $vals['image'], type: 'backend-product') }}"
                                class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title font-weight-bold text-center">{!! $vals['short_description'] !!}</h5>
                            </div>
                        </a>
                    </div>

                </div>
                @endforeach
            </div>


        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>
<script type="text/javascript" src="{{ theme_asset(path: 'public/assets/front-end/js/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ theme_asset(path: 'public/assets/front-end/js/kundaliChart.js') }}"></script>
<script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
</script>

<script></script>


<script></script>
@endpush