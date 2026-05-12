@extends('layouts.front-end.app')

@section('title', translate('Online Panchang - Hindi Panchang, Tithi & Muhurat | Hindu Panchang'))

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
        content="Mahakal.com par aaj ka jyotishiya panchang dekhein, jismein tithi, vaar, nakshatra, yog, karan aur shubh Ganesh ki jankari poori tarah se uplabdh hai">

    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <style>
        .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .city-list {
            position: absolute;
            z-index: 99;
            text-align: left;
            width: 300px;
            overflow-x: hidden;
            height: 170px;
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
    {{-- muhurat modal --}}
    <div class="modal" tabindex="-1" id="muhurat-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="muhurat-modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-5">
                        <img src="#" alt="" id="muhurat-modal-image" style="width: 50%; height:auto;">
                    </div>
                    <p>Date: <span><b id="muhurat-modal-date">September 15, 2024, Sunday</b></span></p>
                    <p>Muhurat: <span><b id="muhurat-modal-muhurat">06:12 PM to 06:07 AM</b></span></p>
                    <p>Nakshatra: <span><b id="muhurat-modal-nakshatra">Dhanishtha</b></span></p>
                    <p>Tithi: <span><b id="muhurat-modal-tithi">Trayodashi</b></span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- fast festival modal --}}
    <div class="modal" tabindex="-1" id="fastfestival-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fastfestival-modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-5">
                        <img src="#" alt="" id="fastfestival-modal-image" style="width: 50%; height:auto;">
                    </div>
                    <p id="fastfestival-modal-hidescription"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- main --}}
    {{-- <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ translate('panchang') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ translate('panchang') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="image-box">
        <img src="{{ asset('public/assets/front-end/img/panchang-banner.png') }}" alt="panchang">
    </div>

    <div class="container py-5 rtl text-align-direction">
        <!--<h2 class="text-center mb-3 headerTitle">{{ translate('return_policy') }}</h2>-->
        <div class="card __card">
            <div class="card-body text-justify">
                <form class="mt-3" action="#" method="post">
                    <div class="row justify-content-md-center">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" id="datepicker" class="form-control hasDatepicker"
                                    placeholder="Enter Date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="country" id="country" onchange="countryChange()" class="form-control">
                                    @foreach ($country as $countryName)
                                        <option value="{{ $countryName->name }}"
                                            {{ $countryName->name == 'India' ? 'selected' : '' }}>
                                            {{ $countryName->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input class="form-control pac-target-input" type="text" id="places"
                                    autocomplete="off" placeholder="स्थान दर्ज करें">
                                <div class="city-list" id="city-div" style="display: none;">
                                    <ul id="citylist" class="list-group" style="position: absolute; z-index:1;">
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto text-center">
                            <button type="button" id="prevbutton" class="btn btn-primary"> &lt;&lt; </button>
                            <button type="button" id="todaybutton" class="btn btn-primary">आज</button>
                            <button type="button" id="nextbutton" class="btn btn-primary">&gt;&gt;</button>
                        </div>
                    </div>
                </form>
                <!-- start tabs -->
                <div class="tabbable-responsive my-3">
                    <div class="tabbable">
                        <ul class="nav nav-pills mb-3 justify-content-center" id="linxea-avenir" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="panchang-info-tab" data-toggle="tab"
                                    href="#panchang-info" role="tab" aria-controls="first" aria-selected="true"
                                    style="color: #222 !important; font-weight: 600;">{{ translate('पंचांग') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="grah-position-info-tab" data-toggle="tab"
                                    href="#grah-position-info" role="tab" aria-controls="second"
                                    aria-selected="false" style="color: #222 !important; font-weight: 600;">
                                    {{ translate('ग्रहो की स्थिति') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="hora-tab" data-toggle="tab" href="#hora"
                                    role="tab" aria-controls="third" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('होरा') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end tabs -->
                <div class="tab-content">
                    {{-- panchang-infoTab --}}
                    @include('web-views.panchang.partials.panchang-tab')

                    {{-- grah-position-infoTab --}}
                    @include('web-views.panchang.partials.grah-tab')

                    {{-- horaTab --}}
                    @include('web-views.panchang.partials.hora-tab')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/panchang.js') }}"></script>

    {{-- muhurat modal --}}
    <script>
        function muhuratModal(that) {
            var title = $(that).data('title');
            var image = "{{ asset('public/assets/front-end/img/muhurat/') }}" + "/" + $(that).data('image');
            var date = $(that).data('date');
            var muhurat = $(that).data('muhurat');
            var nakshatra = $(that).data('nakshatra');
            var tithi = $(that).data('tithi');
            $('#muhurat-modal-title').text(title);
            $('#muhurat-modal-image').attr('src', image);
            $('#muhurat-modal-date').text(date);
            $('#muhurat-modal-muhurat').text(muhurat);
            $('#muhurat-modal-nakshatra').text(nakshatra);
            $('#muhurat-modal-tithi').text(tithi);
            $('#muhurat-modal').modal('show');
        }
    </script>

    {{-- fast festival modal --}}
    <script>
        function fastFestivalModal(that) {
            var title = $(that).data('title');
            var image = $(that).data('image');
            var hidescription = $(that).data('hidescription');
            console.log(hidescription);
            $('#fastfestival-modal-title').text(title);
            $('#fastfestival-modal-image').attr('src', image);
            $('#fastfestival-modal-hidescription').html(hidescription);
            $('#fastfestival-modal').modal('show');
        }
    </script>
@endpush
