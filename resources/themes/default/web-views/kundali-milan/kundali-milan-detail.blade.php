@extends('layouts.front-end.app')

@section('title', translate('kundali-matching'))

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
                    <h1 class="innerpage-title">{{ translate('kundali_matching') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ translate('kundali_matching') }}</li>
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
                    <div class="col-md-5">
                        <div class="text-center">
                            <div class="badge badge-pill badge-warning new-badge">{{ @ucwords($usersData['male_name']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <img src="{{ asset('public/assets/front-end/img/maleFemale.png') }}" class="img-fluid"
                                style="width: 58px;margin-bottom: 10px;">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="text-center">
                            <div class="badge badge-pill badge-warning new-badge">{{ @ucwords($usersData['female_name']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table kundli-basic-details">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" colspan="2"><i class="fa fa-male fa-lg"></i>&nbsp; Basic Detail
                                        &nbsp;<span class="badge badge-warning">Male</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row"><b>Name</b></th>
                                    <td>{{ $usersData['male_name'] }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Birth Date & Time</b></th>
                                    <td>{{ isset($savedMaleDOB)?date('d/m/Y',strtotime($savedMaleDOB)):$usersData['male_dob'] }} | {{ $usersData['male_time'] }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Birth Place</b></th>
                                    <td>{{ isset($usersData['male_place'])?$usersData['male_place']:(isset($usersData['male_city'])?$usersData['male_city']:'') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Janam Rashi</b></th>
                                    <td>{{ $astroData['male_astro_details']['sign'] }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Rashi Lord</b></th>
                                    <td>{{ $astroData['male_astro_details']['SignLord'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table kundli-basic-details">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" colspan="2"><i class="fa fa-female fa-lg"></i>&nbsp; Basic Detail
                                        &nbsp;<span class="badge badge-warning">Female</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row"><b>Name</b></th>
                                    <td>{{ $usersData['female_name'] }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Birth Date & Time</b></th>
                                    <td>{{ isset($savedFemaleDOB)?date('d/m/Y',strtotime($savedFemaleDOB)):$usersData['female_dob'] }} | {{ $usersData['female_time'] }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Birth Place</b></th>
                                    <td>{{ isset($usersData['female_place'])?$usersData['female_place']:(isset($usersData['female_city'])?$usersData['female_city']:'') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Janam Rashi</b></th>
                                    <td>{{ $astroData['female_astro_details']['sign'] }}</td>
                                </tr>
                                <tr>
                                    <th scope="row"><b>Rashi Lord</b></th>
                                    <td>{{ $astroData['female_astro_details']['SignLord'] }}</td>
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
                                <a class="nav-link active" id="birth-info-tab" data-toggle="tab" href="#birth-info"
                                    role="tab" aria-controls="first" aria-selected="true"
                                    style="color: #222 !important; font-weight: 600;">{{ translate('जन्म विवरण') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="grah-info-tab" data-toggle="tab" href="#grah-info"
                                    role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('ग्रहों का विवरण') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="kundali-tab" data-toggle="tab" href="#kundali"
                                    role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('कुंडली विवरण') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="ashtkut-tab" data-toggle="tab" href="#ashtkut"
                                    role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('अष्टकूट गुण मिलान') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="dashakut-tab" data-toggle="tab" href="#dashakut"
                                    role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('दशकूट गुण मिलान') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="manglik-info-tab" data-toggle="tab"
                                    href="#manglik-info" role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('मांगलिक विवरण') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="matching-info-tab" data-toggle="tab"
                                    href="#matching-info" role="tab" aria-controls="second" aria-selected="false"
                                    style="color: #222 !important; font-weight: 600;">
                                    {{ translate('मिलान विवरण') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end tabs -->
                <div class="tab-content">
                    {{-- birth-infoTab --}}
                    @include('web-views.kundali-milan.partials.birth-tab')

                    {{-- grah-info tab --}}
                    @include('web-views.kundali-milan.partials.grah-tab')

                    {{-- chart tab --}}
                    @include('web-views.kundali-milan.partials.chart-tab')

                    {{-- ashtakoot tab --}}
                    @php
                        $ashtakootData = json_decode(App\Utils\ApiHelper::astroApi('https://json.astrologyapi.com/v1/match_ashtakoot_points','hi',$apiData),true);
                    @endphp
                    @include('web-views.kundali-milan.partials.ashtakoot-tab')

                    {{-- dashakut tab --}}
                    @php
                        $dashakootData = json_decode(App\Utils\ApiHelper::astroApi('https://json.astrologyapi.com/v1/match_dashakoot_points','hi',$apiData),true);
                    @endphp
                    @include('web-views.kundali-milan.partials.dashakoot-tab')

                    {{-- manglik-info tab --}}
                    @php
                        $manglikData = json_decode(App\Utils\ApiHelper::astroApi('https://json.astrologyapi.com/v1/match_manglik_report','hi',$apiData),true);
                    @endphp
                    @include('web-views.kundali-milan.partials.manglik-tab')

                    {{-- matching-info tab --}}
                    @php
                        $matchData = json_decode(App\Utils\ApiHelper::astroApi('https://json.astrologyapi.com/v1/match_making_report','hi',$apiData),true);
                    @endphp
                    @include('web-views.kundali-milan.partials.matching-tab')

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

    <script>
        var data = {
            m_day: "{{ $apiData['m_day'] }}",
            m_month: "{{ $apiData['m_month'] }}",
            m_year: "{{ $apiData['m_year'] }}",
            m_hour: "{{ $apiData['m_hour'] }}",
            m_min: "{{ $apiData['m_min'] }}",
            m_lat: "{{ $apiData['m_lat'] }}",
            m_lon: "{{ $apiData['m_lon'] }}",
            m_tzone: "{{ $apiData['m_tzone'] }}",
            f_day: "{{ $apiData['f_day'] }}",
            f_month: "{{ $apiData['f_month'] }}",
            f_year: "{{ $apiData['f_year'] }}",
            f_hour: "{{ $apiData['f_hour'] }}",
            f_min: "{{ $apiData['f_min'] }}",
            f_lat: "{{ $apiData['f_lat'] }}",
            f_lon: "{{ $apiData['f_lon'] }}",
            f_tzone: "{{ $apiData['f_tzone'] }}"
        };
    </script>

    {{-- grah tab --}}
    <script>
        //start convert to degree
        function zeroPad(num, places) {
            var zero = places - num.toString().length + 1;
            return Array(+(zero > 0 && zero)).join("0") + num;
        }

        function convertToDegreeFormat(degree) {
            var deg = zeroPad(parseInt(degree), 2);
            var min = zeroPad(parseInt((degree - deg) * 60), 2);
            var sec = zeroPad(parseInt(((degree - deg) * 60 - min) * 60), 2);
            return deg + ": " + min + ": " + sec;
        }
        //end convert to degree

        $(document).ready(function() {
            var male_planet_list = "";
            var female_planet_list = "";

            var url = 'https://json.astrologyapi.com/v1/match_planet_details/';
            astroApi(url, 'hi', data, function(response) {
                if (response == 0) {
                    toastr.error('An error occured', {
                        closeButton: true,
                        progressBar: true
                    });
                } else {
                    $.each(response.male_planet_details, function(key, value) {
                        male_planet_list += '<tr>' +
                            '<td>' + value.name + '</td>' +
                            '<td>' + (value.isRetro=='true' ? 'R' : '-') + '</td>' +
                            '<td>' + value.sign + '</td>' +
                            '<td>' + convertToDegreeFormat(value.normDegree) + '</td>' +
                            '<td>' + value.signLord + '</td>' +
                            '<td>' + value.nakshatra + '</td>' +
                            '<td>' + value.nakshatraLord + '</td>' +
                            '<td>' + value.house + '</td>' +
                            '</tr>';
                    });
                    $('#tb_male_planet').append(male_planet_list);

                    $.each(response.female_planet_details, function(key, value) {
                        female_planet_list += '<tr>' +
                            '<td>' + value.name + '</td>' +
                            '<td>' + (value.isRetro=='true' ? 'R' : '-') + '</td>' +
                            '<td>' + value.sign + '</td>' +
                            '<td>' + convertToDegreeFormat(value.normDegree) + '</td>' +
                            '<td>' + value.signLord + '</td>' +
                            '<td>' + value.nakshatra + '</td>' +
                            '<td>' + value.nakshatraLord + '</td>' +
                            '<td>' + value.house + '</td>' +
                            '</tr>';
                    });
                    $('#tb_female_planet').append(female_planet_list);
                }
            });
        });
    </script>

    {{-- chart --}}
    <script>
        var m_data = {
            day: {{ $apiData['m_day'] }},
            month: {{ $apiData['m_month'] }},
            year: {{ $apiData['m_year'] }},
            hour: {{ $apiData['m_hour'] }},
            min: {{ $apiData['m_min'] }},
            lat: {{ $apiData['m_lat'] }},
            lon: {{ $apiData['m_lon'] }},
            tzone: {{ $apiData['m_tzone'] }},
        };

        var f_data = {
            day: {{ $apiData['f_day'] }},
            month: {{ $apiData['f_month'] }},
            year: {{ $apiData['f_year'] }},
            hour: {{ $apiData['f_hour'] }},
            min: {{ $apiData['f_min'] }},
            lat: {{ $apiData['f_lat'] }},
            lon: {{ $apiData['f_lon'] }},
            tzone: {{ $apiData['f_tzone'] }}
        };

        sendMaleData(m_data);
        sendFemaleData(f_data);

        var options = {
            lineColor: '#FC8100',
            planetColor: '#555',
            signColor: '#555',
            isForMatching: false,
            width: $('.col-sm-6').width()
        };

        getMaleCharts(options, $("#maleChartSelect").val(), 'maleHoroscopeCharts', true);
        getFemaleCharts(options, $("#femaleChartSelect").val(), 'femaleHoroscopeCharts', true);

        $('#maleChartSelect').on('change', function(e) {
            var valueSelected = this.value;
            getMaleCharts(options, valueSelected, 'maleHoroscopeCharts', true);
        });

        $('#femaleChartSelect').on('change', function(e) {
            var valueSelected = this.value;
            getFemaleCharts(options, valueSelected, 'femaleHoroscopeCharts', true);
        });
    </script>
@endpush
