"use strict";

// datepicker
$('#datepicker').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'dd/mm/yyyy',
    modal: true,
    footer: true
});

// default data at page load
var globalDate = new Date();
var latitude = 23.1764665;
var longitude = 75.78851629999997;
var timezone = 5.5;
var apiData = "";

$('#datepicker').val(('0' + globalDate.getDate()).slice(-2) + '/' + (globalDate.getMonth() + 1) + '/' + globalDate.getFullYear());
$('#places').val("Ujjain, Madhya Pradesh");

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

chaughadiya(apiData);

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

            chaughadiya(apiData);
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

    chaughadiya(apiData);
});

// previous button click
$('#prevbutton').click(function (e) {
    e.preventDefault();

    globalDate = new Date(globalDate.setDate(globalDate.getDate() - 1));
    var prevbtnTime = new Date().toTimeString().split(':');
    $('#datepicker').val(('0' + globalDate.getDate()).slice(-2) + '/' + (globalDate.getMonth() + 1) + '/' + globalDate.getFullYear());

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

    chaughadiya(apiData);
});

// today button
$('#todaybutton').click(function (e) {
    e.preventDefault();

    globalDate = new Date();
    var todaybtnTime = new Date().toTimeString().split(':');
    $('#datepicker').val(('0' + globalDate.getDate()).slice(-2) + '/' + (globalDate.getMonth() + 1) + '/' + globalDate.getFullYear());

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

    chaughadiya(apiData);
});

// next button
$('#nextbutton').click(function (e) {
    e.preventDefault();

    globalDate = new Date(globalDate.setDate(globalDate.getDate() + 1));
    var nextbtnTime = new Date().toTimeString().split(':');
    $('#datepicker').val(('0' + globalDate.getDate()).slice(-2) + '/' + (globalDate.getMonth() + 1) + '/' + globalDate.getFullYear());

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

    chaughadiya(apiData);
});

//chaughadiya function

function chaughadiya(apiData) {

    var daycolor = "";
    var nightcolor = "";
    var tdDayChaughadiya = "";
    var tdNightChaughadiya = "";

    var chaughadiyaUrl = 'https://json.astrologyapi.com/v1/chaughadiya_muhurta';
    astroApi(chaughadiyaUrl, 'hi', apiData, function (response) {
        if (response == 0) {
            toastr.error('An error occured', {
                closeButton: true,
                progressBar: true
            });
        } else {

            $('#tb-day-chaughadiya').html("");
            $('#tb-night-chaughadiya').html("");

            $.each(response.chaughadiya.day, function (cdkey, cdvalue) {
                tdDayChaughadiya += '<tr>';
                $($(cdvalue).get().reverse()).each(function (cdkeys, cdvalues) {
                    if (cdvalues['muhurta'] == "अमृत" || cdvalues['muhurta'] == "शुभ" || cdvalues['muhurta'] == "लाभ") {
                        daycolor = "text-success";
                    }
                    else if (cdvalues['muhurta'] == "चर") {
                        daycolor = "text-info";
                    }
                    else {
                        daycolor = "text-danger";
                    }

                    tdDayChaughadiya += '<td class="' + daycolor + '"> <b>' + cdvalues['muhurta'] + '</b></td>';
                    tdDayChaughadiya += '<td class="' + daycolor + '"> <b>' + cdvalues['time'] + ' </b></td>';
                });
                tdDayChaughadiya += '</tr>';
            });
            $('#tb-day-chaughadiya').append(tdDayChaughadiya);

            $.each(response.chaughadiya.night, function (cnkey, cnvalue) {
                tdNightChaughadiya += '<tr>';
                $($(cnvalue).get().reverse()).each(function (cnkeys, cnvalues) {
                    if (cnvalues['muhurta'] == "अमृत" || cnvalues['muhurta'] == "शुभ" || cnvalues['muhurta'] == "लाभ") {
                        nightcolor = "text-success";
                    }
                    else if (cnvalues['muhurta'] == "चर") {
                        nightcolor = "text-info";
                    }
                    else {
                        nightcolor = "text-danger";
                    }
                    tdNightChaughadiya += '<td class="' + nightcolor + '"> <b>' + cnvalues['muhurta'] + '</b></td>';
                    tdNightChaughadiya += '<td class="' + nightcolor + '"> <b>' + cnvalues['time'] + '</b></td>';
                });
                tdNightChaughadiya += '</tr>';

            });
            $('#tb-night-chaughadiya').append(tdNightChaughadiya);
        }
    });
}
