@extends('layouts.front-end.app')

@section('title', translate($rashi['name']) ." राशि फल – जानिए आज का राशिफल | Mahakal.com")

@push('css_or_js')
    <meta name="description" content="Mahakal.com par janiye {{translate($rashi['name'])}} Rashi ka dainik rashifal, shubh samay, prem, swasthya aur career se judi jyotishiya salah. Apni rashi ke anusar din ki sahi shuruaat karein.">
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
@endpush

@section('content')
<input type="hidden" name="" id="rashi-slug" value="{{$rashi['slug']}}">
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ translate($rashi['name'])}}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ translate($rashi['name']) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    
    <div class="container">
        <div class="row px-2 rashi-header d-flex justify-content-between align-items-center">
            <img src="{{ asset($imagesrc) }}" class="img-fluid" width="40"><span id="hindi-date"
                class="text-white h5 mb-0"> </span>
            <select class="form-select rashi-lang" id="rashi-lang">
                <option value="hi">Hindi</option>
                <option value="en">English</option>
            </select>
        </div>
    </div>
    <div class="container py-5 rtl text-align-direction">
        <!--<h2 class="text-center mb-3 headerTitle">{{ translate('return_policy') }}</h2>-->
        <div class="card __card">
            <div class="card-body text-justify">
                <!-- start tabs -->
                <div class="tabbable-responsive my-3">
                    <div class="tabbable">
                        <ul class="nav nav-pills nav-justified" id="linxea-avenir" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="daily-tab" data-toggle="tab" href="#daily" role="tab"
                                    aria-controls="first" aria-selected="true"
                                    style="color: #222 !important; font-weight: 600;">{{ translate('daily') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="month-tab" data-toggle="tab" href="#month" role="tab"
                                    aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('monthly') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="varshik-tab" data-toggle="tab" href="#varshik"
                                    role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('yearly') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end tabs -->
                <div class="tab-content">
                    {{-- daily rashifal --}}
                    @include('web-views.rashi.partials.dailyrashi-tab')
                    
                    {{-- monthly rashifal --}}
                    @include('web-views.rashi.partials.monthlyrashi-tab')
                    
                    {{-- yearly rashifal --}}
                    @include('web-views.rashi.partials.yearlyrashi-tab')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/helper.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/rashi.js') }}"></script>
@endpush
