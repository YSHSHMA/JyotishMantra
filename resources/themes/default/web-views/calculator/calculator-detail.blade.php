@extends('layouts.front-end.app')

@section('title', translate($calculator->name))

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
    <link href="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/css/jquery.fancybox.min.css') }}"
        rel="stylesheet" type="text/css" />
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
            background-color: #fff;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            text-align: left;
            /* Initially hidden */
        }

        .calculator-slider {
            display: flex !important;
        }
    </style>
@endpush

@section('content')

    {{-- modal --}}
    @include('web-views.calculator.partials.modal')

    {{-- main page --}}
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ ucwords(translate($calculator->name)) }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ ucwords(translate($calculator->name)) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <section class="cal_about_wrapper as_padderTop60 as_padderBottom60">
        <div class="container">
            <div class="">
                <div class="cal-title mt-0 mb-2">
                    {{ translate('calculator') }}
                    <h4 class="mt-2 height-10">
                        <span class="divider">&nbsp;</span>
                    </h4>
                </div>
                <div class="owl-carousel owl-theme p-2 calculator-slider">
                    @foreach ($calculatorList as $calc)
                        <div class="text-center __m-5px __cate-item">
                            <a href="{{ route('calculator', [$calc->slug]) }}">
                                <div class="__img">
                                    <img alt="{{ $calc->name }}"
                                        src="{{ getValidImage(path: "storage/app/public/calculator-img/$calc->logo", type: 'calculator') }}"
                                        class="{{ request()->is('calculator/' . $calc->slug) ? 'cal-active' : '' }}">
                                </div>
                                <p
                                    class="text-center fs-13 font-semibold mt-2 {{ request()->is('calculator/' . $calc->slug) ? 'cal-active-p' : '' }}">
                                    {{ translate($calc->name) }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="as_aboutimg text-right">
                        <img alt="{{ $calculator->name }}" src="{{ getValidImage(path: "storage/app/public/calculator-img/$calculator->detail_image", type: 'calculator') }}" class="img-fluid">
                        <span class="as_play"><a data-fancybox="" href="{{ $calculator->url }}"><img src="{{ asset('public/assets/front-end/img/play.png') }}" alt=""></a></span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="as_about_detail">
                        {!! $calculator['description'] !!}
                        <a href="#go" class="as_btn"><b>GO</b></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="as_form_wrapper as_padderTop60 as_padderBottom60" id="go">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="card border-0 box-shadow col-sm-6 offset-md-3">
                        <div class="card-body mybgcolor">
                            <h2 class="h5 mb-4 text-center font-weight-bolder mt-2 myformtitle">
                                {{ ucwords(translate($calculator->name)) }}</h2>
                            <div class="for-padding">
                                <form id="calculatorForm">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="hidden" id="slug" value="{{ $calculator->slug }}">
                                                <input class="form-control" id="username" value="" type="text"
                                                    name="username" required autocomplete="off"
                                                    placeholder="पूरा नाम दर्ज करें">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select name="gender" id="gender" class="form-control">
                                                    <option value="Male">
                                                        Male
                                                    </option>
                                                    <option value="Female">
                                                        Female
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input class="form-control" id="datepicker" name="dob" required=""
                                                    autocomplete="off" placeholder="जन्म तारीख दर्ज करें">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input class="form-control" id="timepicker" name="time"
                                                    required="" autocomplete="off" placeholder="जन्म समय दर्ज करें">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select name="country" id="country" onchange="countrychange()"
                                                    class="form-control">
                                                    @foreach ($country as $countryName)
                                                        <option value="{{ $countryName->name }}"
                                                            {{ $countryName->name == 'India' ? 'selected' : '' }}>
                                                            {{ $countryName->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input class="form-control" type="text" id="places" value=""
                                                    name="places" required="" autocomplete="off"
                                                    placeholder="जन्म स्थान दर्ज करें">
                                                <div class="city-list">
                                                    <ul id="citylist" class="list-group">
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 text-center my-4">
                                            <button class="btn as_btn" type="submit">
                                                <i class="czi-arrow-left-circle mr-2 ml-n1"></i>
                                                {{ translate('SUBMIT') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/api.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/vendor/fancybox/js/jquery.fancybox.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            //  Set caption from card text
            $('.as_play a').fancybox();
        });
    </script>
    {{-- datepicker --}}
    <script>
        $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd/mm/yyyy',
            modal: true,
            footer: true
        });
    </script>

    {{-- time picker --}}
    <script>
        $('#timepicker').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });
        document.getElementById('timepicker').addEventListener('click', function() {
            var innerButton = this.nextElementSibling.querySelector('button');
            innerButton.click();
        });
    </script>

    {{-- global variable --}}
    <script>
        let latitude = "";
        let longitude = "";
        let timezone = "";
    </script>

    {{-- city load --}}
    <script>
        $("#places").keyup(function() {
            var length = $("#places").val().length;
            $("#citylist").html(""); // Clear previous results

            if (length > 1) {
                let countryName = $("#country").val();
                let cityName = $("#places").val();
                let city = "";

                var data = {
                    country: countryName,
                    name: cityName,
                };

                // Show the dropdown
                $(".city-list").show();

                $.ajax({
                    type: "post",
                    url: "https://geo.vedicrishi.in/places/",
                    data: JSON.stringify(data),
                    dataType: "json",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    success: function(response) {
                        let city = "";
                        $.each(response, function(key, value) {
                            city +=
                                `<li class="list-group-item" style="cursor: pointer;" onclick="citydata(${value.latitude}, ${value.longitude}, '${value.place.replace(/'/g, "\\'")}')">${value.place}</li>`;
                        });
                        $("#citylist").append(city);
                    },
                    error: function() {
                        console.log("Error loading cities.");
                    },
                });
            } else {
                // Hide the dropdown if input length is <= 1
                $(".city-list").hide();
            }
        });

        // Hide city list on clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest("#places, .city-list").length) {
                $(".city-list").hide(); // Hide dropdown
            }
        });

        // Prevent dropdown from closing if clicked inside
        $(".city-list").on("click", function(event) {
            event.stopPropagation();
        });
    </script>

    {{-- lat lon and place --}}
    <script>
        function citydata(lat, lon, place) {
            $('#places').val(place);
            $('#citylist').html("");

            latitude = lat;
            longitude = lon;
            var url = 'https://json.astrologyapi.com/v1/timezone_with_dst';
            var data = {
                latitude: lat,
                longitude: lon,
                date: new Date(),
            };
            astroApi(url, 'en', data, function(value) {
                if (value == 0) {
                    toastr.error('An error occured', {
                        closeButton: true,
                        progressBar: true
                    });
                } else {
                    timezone = value.timezone;
                }
            });
        }
    </script>

    {{-- country change --}}
    <script>
        function countrychange() {
            $("#places").val("");
            $('#citylist').html("");
        }
    </script>

    <script>
        $('#calculatorForm').submit(function(e) {
            e.preventDefault();

            var url = "";
            var slug = $('#slug').val();

            if (slug == 'rashi-namakshar') {
                url = "https://json.astrologyapi.com/v1/astro_details";
            } else if (slug == 'kalsarp-dosha') {
                url = "https://json.astrologyapi.com/v1/kalsarpa_details";
            } else if (slug == 'manglik-dosha') {
                url = "https://json.astrologyapi.com/v1/manglik";
            } else if (slug == 'pitra-dosha') {
                url = "https://json.astrologyapi.com/v1/pitra_dosha_report";
            } else if (slug == 'mool-ank') {
                url = "https://json.astrologyapi.com/v1/numero_table";
            } else if (slug == 'gem-suggestion') {
                url = "https://json.astrologyapi.com/v1/basic_gem_suggestion";
            } else if (slug == 'rudraksha-suggestion') {
                url = "https://json.astrologyapi.com/v1/rudraksha_suggestion";
            } else if (slug == 'prayer-suggestion') {
                url = "https://json.astrologyapi.com/v1/puja_suggestion";
            }

            var time = $('#timepicker').timepicker().val().split(":");
            var hour = time[0];
            var min = time[1];

            var dob = $('#datepicker').datepicker().val().split("/");
            var day = dob[0];
            var month = dob[1];
            var year = dob[2];

            var data = {
                day: day,
                month: month,
                year: year,
                hour: hour,
                min: min,
                lat: latitude,
                lon: longitude,
                tzone: timezone,
            };

            astroApi(url, 'hi', data, function(value) {
                if (value == 0) {
                    toastr.error('An error occured', {
                        closeButton: true,
                        progressBar: true
                    });
                } else {
                    if (slug == 'rashi-namakshar') {
                        $('#rashi').text(value.sign);
                        $('#namakshar').text(value.name_alphabet);
                        $('#namakshar-modal').modal('show');
                    } else if (slug == 'kalsarp-dosha') {
                        $('#one_line').text(value.one_line);
                        if (value.present == true) {
                            $('#result').text("Yes");
                            $('#conclusion').text(value.type);
                            $('#ks_report').html(value.report.report);
                        } else {
                            $('#result').text("No");
                            $('#type').hide();
                            $('#report').hide();
                        }
                        $('#kalsarp-modal').modal('show');
                    } else if (slug == 'manglik-dosha') {
                        var houselist = "";
                        var aspectlist = "";
                        $.each(value.manglik_present_rule.based_on_house, function(key, value) {
                            houselist +=
                                '<li class="list-group-item bg-transparent pb-0 border-0">' +
                                value + '</li>';
                        });
                        $('#house').append(houselist);

                        $.each(value.manglik_present_rule.based_on_aspect, function(key, value) {
                            aspectlist +=
                                '<li class="list-group-item bg-transparent pb-0 border-0">' +
                                value + '</li>';
                        });
                        $('#aspect').append(aspectlist);

                        $('#mangalikPer').text(value.percentage_manglik_after_cancellation + '%');
                        $('#status').text(value.manglik_status);
                        $('#report').text(value.manglik_report);
                        $('#mangalik-modal').modal('show');
                    } else if (slug == 'pitra-dosha') {
                        var effectslist = "";
                        var remedieslist = "";

                        $('#pitra-conclusion').text(value.conclusion);

                        if (value.is_pitri_dosha_present == true) {
                            $('#pitra-result').text("हाँ");
                            $('#rules_matched').text(value.rules_matched);
                            $('#pitra_dosha').text(value.what_is_pitri_dosha);

                            $.each(value.effects, function(key, value) {
                                effectslist +=
                                    '<li class="list-group-item bg-transparent border-0">' + value +
                                    '</li>';
                            });
                            $('#effects').append(effectslist);

                            $.each(value.remedies, function(key, value) {
                                remedieslist +=
                                    '<li class="list-group-item bg-transparent border-0">' + value +
                                    '</li>';
                            });
                            $('#remedies').append(remedieslist);
                        } else {
                            $('#pitra-result').text("नहीं");
                            $('#rules').hide();
                            $('#pitra').hide();
                            $('#peffects').hide();
                            $('#premedies').hide();
                        }
                        $('#pitra-modal').modal('show');
                    } else if (slug == 'mool-ank') {
                        $('#mook-ank').text(value.radical_number);
                        $('#moolank-modal').modal('show');
                    } else if (slug == 'gem-suggestion') {
                        $('#lifeimg').attr('src', '{{ asset('public/assets/front-end/img') }}/gems/' +
                            value.LIFE.name.trimEnd() + '.jpg');
                        $('#lifegemname').text(value.LIFE.name);
                        $('#lifesubstitude').text(value.LIFE.semi_gem);
                        $('#lifefinger').text(value.LIFE.wear_finger);
                        $('#lifeweight').text(value.LIFE.weight_caret);
                        $('#lifeday').text(value.LIFE.wear_day);
                        $('#lifedeity').text(value.LIFE.gem_deity);
                        $('#lifemetal').text(value.LIFE.wear_metal);

                        $('#beneficimg').attr('src', '{{ asset('public/assets/front-end/img') }}/gems/' +
                            value.BENEFIC.name.trimEnd() + '.jpg');
                        $('#beneficgemname').text(value.BENEFIC.name);
                        $('#beneficsubstitude').text(value.BENEFIC.semi_gem);
                        $('#beneficfinger').text(value.BENEFIC.wear_finger);
                        $('#beneficweight').text(value.BENEFIC.weight_caret);
                        $('#beneficday').text(value.BENEFIC.wear_day);
                        $('#beneficdeity').text(value.BENEFIC.gem_deity);
                        $('#beneficmetal').text(value.BENEFIC.wear_metal);

                        $('#luckyimg').attr('src', '{{ asset('public/assets/front-end/img') }}/gems/' +
                            value.LUCKY.name.trimEnd() + '.jpg');
                        $('#luckygemname').text(value.LUCKY.name);
                        $('#luckysubstitude').text(value.LUCKY.semi_gem);
                        $('#luckyfinger').text(value.LUCKY.wear_finger);
                        $('#luckyweight').text(value.LUCKY.weight_caret);
                        $('#luckyday').text(value.LUCKY.wear_day);
                        $('#luckydeity').text(value.LUCKY.gem_deity);
                        $('#luckymetal').text(value.LUCKY.wear_metal);

                        $('#gem-modal').modal('show');
                    } else if (slug == 'rudraksha-suggestion') {
                        $('#rudrakshaname').text(value.name);
                        $('#rrudrakshaname').text(value.name);
                        $('#rudrakshadetail').text(value.detail);
                        $('#rudraksha-modal').modal('show');
                    } else if (slug == 'prayer-suggestion') {
                        var poojalist = "";
                        $('#summary').text(value.summary);
                        $.each(value.suggestions, function(key, value) {
                            poojalist +=
                                '<li class="list-group-item bg-transparent border-0 pb-0"><h5 class="mb-0 font-weight-bolder">' +
                                value.title + '</h5></li>' +
                                '<li class="list-group-item bg-transparent border-0">' + value
                                .summary + '</li>';
                        });
                        $('#poojasuggest').append(poojalist);
                        $('#pooja-modal').modal('show');
                    }
                }
            });

            // vimshottari dasha
            if (slug == 'vimshottari-dasha') {
                var mVdhasha_body = "";
                currentDashaUrl = "https://json.astrologyapi.com/v1/current_vdasha";
                majorDashaUrl = "https://json.astrologyapi.com/v1/major_vdasha";

                astroApi(currentDashaUrl, 'hi', data, function(value) {
                    if (value == 0) {
                        toastr.error('An error occured', {
                            closeButton: true,
                            progressBar: true
                        });
                    } else {
                        $('#cVdasha_mj_planet').text(value.major.planet);
                        $('#cVdasha_mj_start').text(value.major.start);
                        $('#cVdasha_mj_end').text(value.major.end);
                        $('#cVdasha_mi_planet').text(value.minor.planet);
                        $('#cVdasha_mi_start').text(value.minor.start);
                        $('#cVdasha_mi_end').text(value.minor.end);
                        $('#cVdasha_smi_planet').text(value.sub_minor.planet);
                        $('#cVdasha_smi_start').text(value.sub_minor.start);
                        $('#cVdasha_smi_end').text(value.sub_minor.end);
                        $('#cVdasha_ssmi_planet').text(value.sub_sub_minor.planet);
                        $('#cVdasha_ssmi_start').text(value.sub_sub_minor.start);
                        $('#cVdasha_ssmi_end').text(value.sub_sub_minor.end);
                        $('#cVdasha_sssmi_planet').text(value.sub_sub_sub_minor.planet);
                        $('#cVdasha_sssmi_start').text(value.sub_sub_sub_minor.start);
                        $('#cVdasha_sssmi_end').text(value.sub_sub_sub_minor.end);
                    }
                });
                astroApi(majorDashaUrl, 'hi', data, function(value) {
                    if (value == 0) {
                        toastr.error('An error occured', {
                            closeButton: true,
                            progressBar: true
                        });
                    } else {
                        $.each(value, function(key, value) {
                            mVdhasha_body += '<tr>' +
                                '<td>' + value.planet + '</td>' +
                                '<td>' + value.start + '</td>' +
                                '<td>' + value.end + '</td>' +
                                '</tr>';
                        });
                        $('#mVdhasha_tbody').append(mVdhasha_body);
                    }
                });

                $('#vimshottari-modal').modal('show');
            }

            $('#calculatorForm')[0].reset();
            latitude = "";
            longitude = "";
            timezone = "";
        });
    </script>

    <script type="text/javascript">
        // Slideshow 4
        $("#slider4").responsiveSlides({
            auto: true,
            pager: false,
            nav: true,
            speed: 500,
            namespace: "callbacks",
            before: function() {
                $('.events').append("<li>before event fired.</li>");
            },
            after: function() {
                $('.events').append("<li>after event fired.</li>");
            }
        });
    </script>
@endpush
