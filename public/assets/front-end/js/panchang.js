"use strict";

// datepicker
$('#datepicker').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'dd/mm/yyyy',
    modal: true,
    footer: true
});

//start convert degree format
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
//end convert degree format

// default data at page load
var globalDate = new Date();
var latitude = 23.1764665;
var longitude = 75.78851629999997;
var timezone = 5.5;
var apiData = "";

$('#datepicker').val(('0' + globalDate.getDate()).slice(-2)+'/'+(globalDate.getMonth() + 1)+'/'+globalDate.getFullYear());
$('#places').val("Ujjain, Madhya Pradesh");

// hindi date
hindiDate(globalDate);
function hindiDate(date){
    var mydate = date;
    var year = mydate.getYear();
    if (year < 1000)
        year += 1900
    var day = mydate.getDay()
    var month = mydate.getMonth()
    var daym = mydate.getDate()
    if (daym < 10)
        daym = "0" + daym
    var dayarray = new Array("&#2352;&#2357;&#2367;&#2357;&#2366;&#2352;", "&#2360;&#2379;&#2350;&#2357;&#2366;&#2352;", "&#2350;&#2306;&#2327;&#2354;&#2357;&#2366;&#2352;", "&#2348;&#2369;&#2343;&#2357;&#2366;&#2352;", "&#2327;&#2369;&#2352;&#2369;&#2357;&#2366;&#2352;", "&#2358;&#2369;&#2325;&#2381;&#2352;&#2357;&#2366;&#2352;", "&#2358;&#2344;&#2367;&#2357;&#2366;&#2352;");
    var montharray = new Array("&#2332;&#2344;&#2357;&#2352;&#2368;", "&#2347;&#2364;&#2352;&#2357;&#2352;&#2368;", "&#2350;&#2366;&#2352;&#2381;&#2330;", "&#2309;&#2346;&#2381;&#2352;&#2376;&#2354;", "&#2350;&#2312;", "&#2332;&#2370;&#2344;", "&#2332;&#2369;&#2354;&#2366;&#2312;", "&#2309;&#2327;&#2360;&#2381;&#2340;", "&#2360;&#2367;&#2340;&#2350;&#2381;&#2348;&#2352;", "&#2309;&#2325;&#2381;&#2340;&#2369;&#2348;&#2352;", "नवंबर", "&#2342;&#2367;&#2360;&#2350;&#2381;&#2348;&#2352;");

    document.getElementById('panchang-hindi-date').innerHTML = "&nbsp;" + dayarray[day] + ", " + daym + " " + montharray[month] + " , " + year + "&nbsp;";
}

//function call
apiData = {
    day: globalDate.getDate(),
    month: globalDate.getMonth() + 1,
    year: globalDate.getFullYear(),
    hour: globalDate.getHours(),
    min: globalDate.getMinutes(),
    lat: latitude,
    lon: longitude,
    tzone: timezone,
};
panchang(apiData);
planetPosition(apiData);
horaMuhurat(apiData);

// city load
$("#places").keyup(function () {
    var length = $('#places').val().length;
    $('#citylist').html("");
    if (length > 1) {
        $('#city-div').css('display', 'block');
        let countryName = $("#country").val();
        let cityName = $("#places").val();
        let city = "";

        var data = {
            country: countryName,
            name: cityName,
        }

        $.ajax({
            type: "post",
            url: "https://geo.vedicrishi.in/places/",
            data: JSON.stringify(data),
            dataType: "json",
            headers: {
                "Content-Type": 'application/json'
            },
            success: function (response) {
                $.each(response, function (key, value) {
                    city +=
                        `<li class="list-group-item" style="cursor: pointer;" onclick="citydata(${value.latitude},${value.longitude},'${value.place}')">${value.place}</li>`;
                });
                $('#citylist').append(city);
            }
        });
    }
});

// lat lon and place
function citydata(lat, lon, place) {
    $('#city-div').css('display', 'none');
    $('#places').val(place);
    $('#citylist').html("");

    var url = 'https://json.astrologyapi.com/v1/timezone_with_dst';
    var data = {
        latitude: lat,
        longitude: lon,
        date: new Date(),
    };
    astroApi(url, 'en', data, function (response) {
        if (response == 0) {
            toastr.error('An error occured', {
                closeButton: true,
                progressBar: true
            });
        } else {
            latitude = lat;
            longitude = lon;
            timezone = parseFloat(response.timezone);
            var cityTime = new Date().toTimeString().split(':');

            apiData = {
                day: globalDate.getDate(),
                month: globalDate.getMonth() + 1,
                year: globalDate.getFullYear(),
                hour: parseInt(cityTime['0']),
                min: parseInt(cityTime['1']),
                lat: latitude,
                lon: longitude,
                tzone: timezone,
            };
            panchang(apiData);
            planetPosition(apiData);
            horaMuhurat(apiData);
        }
    });
}

// country change
function countryChange() {
    $("#places").val("");
    $('#citylist').html("");
}


//date picker change
$('#datepicker').change(function (e) {
    e.preventDefault();

    var dateSplit = $('#datepicker').val().split('/');
    globalDate = new Date(dateSplit['1'] + '/' + dateSplit['0'] + '/' + dateSplit['2']);
    var datePickerTime = new Date().toTimeString().split(':');

    apiData = {
        day: globalDate.getDate(),
        month: globalDate.getMonth() + 1,
        year: globalDate.getFullYear(),
        hour: parseInt(datePickerTime['0']),
        min: parseInt(datePickerTime['1']),
        lat: latitude,
        lon: longitude,
        tzone: timezone,
    };
    hindiDate(globalDate);
    panchang(apiData);
    planetPosition(apiData);
    horaMuhurat(apiData);
});

// previous button click
$('#prevbutton').click(function (e) {
    e.preventDefault();

    globalDate = new Date(globalDate.setDate(globalDate.getDate() - 1));
    var prevbtnTime = new Date().toTimeString().split(':');
    $('#datepicker').val(('0' + globalDate.getDate()).slice(-2)+'/'+(globalDate.getMonth() + 1)+'/'+globalDate.getFullYear());

    apiData = {
        day: globalDate.getDate(),
        month: globalDate.getMonth() + 1,
        year: globalDate.getFullYear(),
        hour: parseInt(prevbtnTime['0']),
        min: parseInt(prevbtnTime['1']),
        lat: latitude,
        lon: longitude,
        tzone: timezone,
    };
    hindiDate(globalDate);
    panchang(apiData);
    planetPosition(apiData);
    horaMuhurat(apiData);
});

// today button
$('#todaybutton').click(function (e) {
    e.preventDefault();

    globalDate = new Date();
    var todaybtnTime = new Date().toTimeString().split(':');
    $('#datepicker').val(('0' + globalDate.getDate()).slice(-2)+'/'+(globalDate.getMonth() + 1)+'/'+globalDate.getFullYear());

    apiData = {
        day: globalDate.getDate(),
        month: globalDate.getMonth() + 1,
        year: globalDate.getFullYear(),
        hour: parseInt(todaybtnTime['0']),
        min: parseInt(todaybtnTime['1']),
        lat: latitude,
        lon: longitude,
        tzone: timezone,
    };
    hindiDate(globalDate);
    panchang(apiData);
    planetPosition(apiData);
    horaMuhurat(apiData);
});

// next button
$('#nextbutton').click(function (e) {
    e.preventDefault();

    globalDate = new Date(globalDate.setDate(globalDate.getDate() + 1));
    var nextbtnTime = new Date().toTimeString().split(':');
    $('#datepicker').val(('0' + globalDate.getDate()).slice(-2)+'/'+(globalDate.getMonth() + 1)+'/'+globalDate.getFullYear());

    apiData = {
        day: globalDate.getDate(),
        month: globalDate.getMonth() + 1,
        year: globalDate.getFullYear(),
        hour: parseInt(nextbtnTime['0']),
        min: parseInt(nextbtnTime['1']),
        lat: latitude,
        lon: longitude,
        tzone: timezone,
    };
    hindiDate(globalDate);
    panchang(apiData);
    planetPosition(apiData);
    horaMuhurat(apiData);
});


//panchang function
function panchang(apiData) {
    var panchangUrl = 'https://json.astrologyapi.com/v1/advanced_panchang/sunrise';
    astroApi(panchangUrl, 'hi', apiData, function (response) {
        if (response == 0) {
            toastr.error('An error occured', {
                closeButton: true,
                progressBar: true
            });
        } else {
            $('#tithi-name').text(response.tithi.details.tithi_name);
            $('#paksha-name').text(response.paksha);
            $('#panchang-day').text(response.day);
            $('#purnimanta').text(response.hindu_maah.purnimanta);
            $('#ritu').text(response.ritu);
            $('#vikramsamvat-year').text(response.vikram_samvat);
            $('#vikramsamvat-name').text(response.vkram_samvat_name);
            $('#sunrise').text(response.sunrise);
            $('#sunset').text(response.sunset);
            $('#moonrise').text(response.moonrise);
            $('#moonset').text(response.moonset);
            $('#abhijeet-start').text(response.abhijit_muhurta.start);
            $('#abhijeet-end').text(response.abhijit_muhurta.end);
            $('#rahu-start').text(response.rahukaal.start);
            $('#rahu-end').text(response.rahukaal.end);
            $('#yamghanta-start').text(response.yamghant_kaal.start);
            $('#yamghanta-end').text(response.yamghant_kaal.end);
            $('#guli-start').text(response.guliKaal.start);
            $('#guli-end').text(response.guliKaal.end);

            $('#paksha-detail-name').text(response.paksha);
            $('#tithi-detail-name').text(response.tithi.details.tithi_name);
            $('#tithi-detail-hour').text(response.tithi.end_time.hour);
            $('#tithi-detail-min').text(response.tithi.end_time.minute);
            $('#tithi-detail-sec').text(response.tithi.end_time.second);
            $('#nakshatra-detail-name').text(response.nakshatra.details.nak_name);
            $('#nakshatra-detail-hour').text(response.nakshatra.end_time.hour);
            $('#nakshatra-detail-min').text(response.nakshatra.end_time.minute);
            $('#nakshatra-detail-sec').text(response.nakshatra.end_time.second);
            $('#yoga-detail-name').text(response.yog.details.yog_name);
            $('#yoga-detail-hour').text(response.yog.end_time.hour);
            $('#yoga-detail-min').text(response.yog.end_time.minute);
            $('#yoga-detail-sec').text(response.yog.end_time.second);
            $('#karana-detail-name').text(response.karan.details.karan_name);
            $('#karana-detail-hour').text(response.karan.end_time.hour);
            $('#karana-detail-min').text(response.karan.end_time.minute);
            $('#karana-detail-sec').text(response.karan.end_time.second);
            $('#amanta-detail-name').text(response.hindu_maah.amanta);
            $('#purnimanta-detail-name').text(response.hindu_maah.purnimanta);
            $('#vikramsamvat-detail-year').text(response.vikram_samvat);
            $('#vikramsamvat-detail-name').text(response.vkram_samvat_name);
            $('#shakasamvat-detail-year').text(response.shaka_samvat);
            $('#shakasamvat-detail-name').text(response.shaka_samvat_name);
            $('#sun-detail-sign').text(response.sun_sign);
            $('#moon-detail-sign').text(response.moon_sign);
            $('#dishashool-detail-name').text(response.disha_shool);
            $('#adhikmas-detail').text(response.hindu_maah.adhik_status == true ? 'हाँ' : 'नहीं');
            $('#ritu-detail-name').text(response.ritu);
            $('#ayana-detail-name').text(response.ayana);
        }
    });
}

//planate function
function planetPosition(apiData) {
    var planateData = '';
    var planetUrl = 'https://json.astrologyapi.com/v1/planet_panchang/sunrise';
    astroApi(planetUrl, 'hi', apiData, function (response) {
        if (response == 0) {
            toastr.error('An error occured', {
                closeButton: true,
                progressBar: true
            });
        } else {
            $('#panchang-planate').html("");
            $.each(response, function (key, value) {
                planateData += '<tr>' +
                    '<td>' + value.name + '</td>' +
                    '<td>' + (value.isRetro == 'true' ? 'R' : '-') + '</td>' +
                    '<td>' + value.sign + '</td>' +
                    '<td>' + value.sign_lord + '</td>' +
                    '<td>' + convertToDegreeFormat(value.normDegree) + '</td>' +
                    '<td>' + value.nakshatra + '</td>' +
                    '<td>' + value.nakshatra_lord + '</td>' +
                    '</tr>';
            });
            $('#panchang-planate').append(planateData);
        }
    });
}

//hora function
function horaMuhurat(apiData) {
    var horaDay = "";
    var horaNight = "";
    var horaUrl = 'https://json.astrologyapi.com/v1/hora_muhurta';
    astroApi(horaUrl, 'hi', apiData, function (response) {
        if (response == 0) {
            toastr.error('An error occured', {
                closeButton: true,
                progressBar: true
            });
        } else {
            $('#hora-day').html("");
            $('#hora-night').html("");

            $.each(response.hora.day, function (hdkey, hdvalue) {
                horaDay += '<tr>';
                $($(hdvalue).get().reverse()).each(function (hdkeys, hdvalues) {
                    horaDay += '<td>' + hdvalues['hora'] + '</td>';
                    horaDay += '<td>' + hdvalues['time'] + '</td>';
                });
                horaDay += '</tr>';
            });
            $('#hora-day').append(horaDay);

            $.each(response.hora.night, function (hnkey, hnvalue) {
                horaNight += '<tr>';
                $($(hnvalue).get().reverse()).each(function (hnkeys, hnvalues) {
                    horaNight += '<td>' + hnvalues['hora'] + '</td>';
                    horaNight += '<td>' + hnvalues['time'] + '</td>';
                });
                horaNight += '</tr>';
            });
            $('#hora-night').append(horaNight);
        }
    });
}
