@extends('layouts.front-end.app')

@section('title', translate('kundali'))

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
@endpush

@section('content')
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ translate('kundali') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ translate('kundali') }}</li>
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
                <div class="row">
                    <div class="col-md-6">
                        <table class="table kundli-basic-details">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" colspan="2"><i class="fa fa-user fa-lg"></i>&nbsp; Basic Detail
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row"><b>Name</b></th>
                                    <td>{{ isset($userData['username'])?$userData['username']:(isset($userData['name'])?$userData['name']:'') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Birth Date & Time</b></th>
                                    <td>{{ isset($savedDOB)?date('d/m/Y',strtotime($savedDOB)):$userData['dob'] }} | {{ $userData['time'] }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Birth Place</b></th>
                                    <td>{{ isset($userData['places'])?$userData['places']:(isset($userData['city'])?$userData['city']:'') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table kundli-basic-details">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" colspan="2"><i class="fa fa-file-text fa-lg"></i>&nbsp; Your
                                        Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row"><b>Nakshatra</b></th>
                                    <td>{{ !empty($astroData['Naksahtra'])?$astroData['Naksahtra']:'' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Ascendant</b></th>
                                    <td>{{ !empty($astroData['ascendant'])?$astroData['ascendant']:'' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Sign</b></th>
                                    <td>{{ !empty($astroData['sign'])?$astroData['sign']:'' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- start tabs -->
                <div class="tabbable-responsive my-3">
                    <div class="tabbable">
                        <ul class="nav nav-pills nav-justified" id="linxea-avenir" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab"
                                    aria-controls="first" aria-selected="true"
                                    style="color: #222 !important; font-weight: 600;">{{ translate('सामान्य') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="chart-tab" data-toggle="tab" href="#chart" role="tab"
                                    aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('चार्ट') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="dasha-tab" data-toggle="tab" href="#dasha" role="tab"
                                    aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('दशा') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="phal-tab" data-toggle="tab" href="#phal" role="tab"
                                    aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('फल') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="sujhav-tab" data-toggle="tab" href="#sujhav"
                                    role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('सुझाव') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="dosh-tab" data-toggle="tab" href="#dosh"
                                    role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('दोष') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="lal-kitab-tab" data-toggle="tab" href="#lal-kitab"
                                    role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('लाल किताब') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end tabs -->
                <div class="tab-content">
                    {{-- generalTab --}}
                    @include('web-views.kundali.partials.general-tab')

                    {{-- chart tab --}}
                    @include('web-views.kundali.partials.chart-tab')

                    {{-- dasha tab --}}
                    @php
                        $currentYDasha = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/current_yogini_dasha',
                                'en',
                                $apiData,
                            ),
                            true,
                        );
                        $majorYDasha = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/major_yogini_dasha',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                        $currentVDasha = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/current_vdasha',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                        $majorVDasha = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/major_vdasha',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                    @endphp
                    @include('web-views.kundali.partials.dasha-tab')

                    {{-- phal tab --}}
                    @php
                        $lagnaResult = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/general_ascendant_report',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                        $nakshatraResult = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/daily_nakshatra_prediction',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                    @endphp
                    @include('web-views.kundali.partials.phal-tab')

                    {{-- sujhav tab --}}
                    @php
                        $gemSuggestion = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/basic_gem_suggestion',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                        $rudrakshaSuggestion = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/rudraksha_suggestion',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                        $prayerSuggestion = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/puja_suggestion',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                    @endphp
                    @include('web-views.kundali.partials.sujhav-tab')

                    {{-- dosh tab --}}
                    @php
                        $manglikDosha = json_decode(
                            App\Utils\ApiHelper::astroApi('https://json.astrologyapi.com/v1/manglik', 'hi', $apiData),
                            true,
                        );
                        $pitraDosha = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/pitra_dosha_report',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                        $kalsarpDosha = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/kalsarpa_details',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                        $sadhesatiDosha = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/sadhesati_current_status',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                    @endphp
                    @include('web-views.kundali.partials.dosha-tab')

                    {{-- lal kitab tab --}}
                    @php
                        $lalkitabRin = json_decode(
                            App\Utils\ApiHelper::astroApi(
                                'https://json.astrologyapi.com/v1/lalkitab_debts',
                                'hi',
                                $apiData,
                            ),
                            true,
                        );
                    @endphp
                    @include('web-views.kundali.partials.lalkitab-tab')

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

    {{-- phal tab --}}
    <script>
        var data = {
            day: "{{ $apiData['day'] }}",
            month: "{{ $apiData['month'] }}",
            year: "{{ $apiData['year'] }}",
            hour: "{{ $apiData['hour'] }}",
            min: "{{ $apiData['min'] }}",
            lat: "{{ $apiData['lat'] }}",
            lon: "{{ $apiData['lon'] }}",
            tzone: "{{ $apiData['tzone'] }}",
        };
    </script>

    {{-- grah bhav phal --}}
    <script>
        planetResult("sun");

        function planetNameChange() {
            let planet_name = "";
            planet_name = $('#planetsname').val();
            planetResult(planet_name);
        }

        function planetResult(get_planet_name) {
            var url = 'https://json.astrologyapi.com/v1/general_house_report/' + get_planet_name;
            astroApi(url, 'hi', data, function(response) {
                if (response == 0) {
                    toastr.error('An error occured', {
                        closeButton: true,
                        progressBar: true
                    });
                } else {
                    $('#planetname').text(response.planet);
                    $('#planetdetail').html(response.house_report);
                }
            });
        }
    </script>

    {{-- chart --}}
    <script>
        sendData(data);

        var options = {
            lineColor: 'orange',
            planetColor: 'green',
            signColor: 'blue',
            width: $('.col-sm-6').width()
        };

        $('#NorthChartSelect').on('change', function(e) {
            var optionSelected = $("option:selected", this);
            var valueSelected = this.value;
            getNorthHoroCharts(options, valueSelected);
        });

        $('#SouthChartSelect').on('change', function(e) {
            var optionSelected = $("option:selected", this);
            var valueSelected = this.value;
            getSouthHoroCharts(options, valueSelected);
        });

        getNorthHoroCharts(options, 'D1');
        getSouthHoroCharts(options, 'D1');
    </script>

    {{-- lal kitab remedies --}}
    <script>
        lalkitabRemedies("Sun");

        function lalkitabRemediesplanetChange() {
            let planetchanged = $('#lalkitabRemediesplanet').val();
            lalkitabRemedies(planetchanged);
        }

        function lalkitabRemedies(get_planet_name) {
            let remedieslist = "";
            $('#lalkitabRemediesremedies').html("");

            var url = 'https://json.astrologyapi.com/v1/lalkitab_remedies/' + get_planet_name;
            astroApi(url, 'hi', data, function(response) {
                if (response == 0) {
                    toastr.error('An error occured', {
                        closeButton: true,
                        progressBar: true
                    });
                } else {
                    $('#lalkitabRemediesplanetname').text(response.planet);
                    $('#lalkitabRemedieshouse').text(response.house);
                    $('#lalkitabRemediesdescription').text(response.lal_kitab_desc);

                    $.each(response.lal_kitab_remedies, function(key, value) {
                        remedieslist += '<li class="list-item bg-transparent border-0 p-1">' + value +
                            '</li>';
                    });
                    $('#lalkitabRemediesremedies').append(remedieslist);
                }
            });
        }
    </script>
@endpush
