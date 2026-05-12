@extends('layouts.front-end.app')

@section('title', translate('Chaughadiya | Aaj Ka Choghadiya |  शुभ मुहूर्त और अशुभ समय जानें'))

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
    <meta name="description" content="Aaj ka Chaughadiya dekhkar jaanein din ke shubh aur ashubh muhurat. Yatra, kharidaari, aur pooja ke liye sahi samay chunein aur apna din safal banaayein.">
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
    </style>
@endpush

@section('content')
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ translate('chaugadiya') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ translate('chaugadiya') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
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
                <!-- end tabs -->

                <div class="tab-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tab-details-block">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">दिन का चौघड़िया</h6>
                                        <div class="kundli-basic-details1 tableFixHead">
                                            <table class="table">
                                                <thead class="thead-light">
                                                    <th><b>चौघड़िया का नाम</b></th>
                                                    <th><b>समय</b></th>
                                                </thead>
                                                <tbody class="bg-white" id="tb-day-chaughadiya">
                                                    <tr>
                                                        <td class="text-danger"> <b>रोग</b></td>
                                                        <td class="text-danger"> <b>05:54:09 - 07:31:39 </b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-danger"> <b>उद्वेग</b></td>
                                                        <td class="text-danger"> <b>07:31:39 - 09:09:09 </b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-info"> <b>चर</b></td>
                                                        <td class="text-info"> <b>09:09:09 - 10:46:39 </b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-success"> <b>लाभ</b></td>
                                                        <td class="text-success"> <b>10:46:39 - 12:24:10 </b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-success"> <b>अमृत</b></td>
                                                        <td class="text-success"> <b>12:24:10 - 14:01:40 </b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-danger"> <b>काल</b></td>
                                                        <td class="text-danger"> <b>14:01:40 - 15:39:10 </b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-success"> <b>शुभ</b></td>
                                                        <td class="text-success"> <b>15:39:10 - 17:16:40 </b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-danger"> <b>रोग</b></td>
                                                        <td class="text-danger"> <b>17:16:40 - 18:54:10 </b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">रात का चौघड़िया</h6>
                                        <div class="kundli-basic-details1 tableFixHead">
                                            <table class="table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th><b>चौघड़िया का नाम</b></th>
                                                        <th><b>समय</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white" id="tb-night-chaughadiya">
                                                    <tr>
                                                        <td class="text-danger"> <b>काल</b></td>
                                                        <td class="text-danger"> <b>18:54:10 - 20:16:40</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-success"> <b>लाभ</b></td>
                                                        <td class="text-success"> <b>20:16:40 - 21:39:10</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-danger"> <b>उद्वेग</b></td>
                                                        <td class="text-danger"> <b>21:39:10 - 23:01:40</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-success"> <b>शुभ</b></td>
                                                        <td class="text-success"> <b>23:01:40 - 24:24:10</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-success"> <b>अमृत</b></td>
                                                        <td class="text-success"> <b>24:24:10 - 25:46:39</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-info"> <b>चर</b></td>
                                                        <td class="text-info"> <b>25:46:39 - 27:09:09</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-danger"> <b>रोग</b></td>
                                                        <td class="text-danger"> <b>27:09:09 - 28:31:39</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-danger"> <b>काल</b></td>
                                                        <td class="text-danger"> <b>28:31:39 - 05:54:09</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <h6 class="font-weight-bold">चौघड़िया के प्रकार</h6>
                                        <div class="kundli-basic-details1 tableFixHead">
                                            <ul style="list-style: none; line-height: 2; padding: 30px;">
                                                <li>
                                                    <h5>चौघड़िया मूल तौर पर सात प्रकार की होती हैं।</h5>
                                                </li>
                                                <li class="text-success"><b>- अमृत, शुभ और लाभ सबसे शुभ चौघड़िया मानी गयी
                                                        हैं।</b></li>
                                                <li class="text-danger"><b>- उद्वेग, काल और रोग अशुभ चौघड़िया मानी गयी
                                                        हैं।</b></li>
                                                <li class="text-info"><b>- चर एक शुभ चौघड़िया है।</b></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/chaughadiya.js') }}"></script>
@endpush