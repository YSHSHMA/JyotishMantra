"use strict";

$('#rashi-lang').change(function (e) { 
    e.preventDefault();
    
    var rashiLang = $(this).val();
    if(rashiLang == 'en'){
        $('#personal-life-heading').text('Personal Life');
        $('#profession-heading').text('Profession');
        $('#health-heading').text('Health');
        $('#travel-heading').text('Travel');
        $('#luck-heading').text('Luck');
        $('#emotion-heading').text('Emotion');
        dailyRashiApi('en');

        $('#monthHindiDetail').hide();
        $('#monthEnglishDetail').show();
        $('#yearHindiDetail').hide();
        $('#yearEnglishDetail').show();
    }
    else{
        $('#personal-life-heading').text('व्यक्तिगत जीवन');
        $('#profession-heading').text('व्यापार/व्यवसाय');
        $('#health-heading').text('स्वास्थ्य');
        $('#travel-heading').text('यात्रा');
        $('#luck-heading').text('भाग्य');
        $('#emotion-heading').text('भावनाएं');
        dailyRashiApi('hi');

        $('#monthHindiDetail').show();
        $('#monthEnglishDetail').hide();
        $('#yearHindiDetail').show();
        $('#yearEnglishDetail').hide();
    }
});

function dailyRashiApi(lang){
    var rashiSlug = $('#rashi-slug').val();
    var url = 'https://json.astrologyapi.com/v1/sun_sign_prediction/daily/'+rashiSlug;
    var data = {
        'tzone': 5.5
    };
    astroApi(url, lang, data, function (response) {
        if (response == 0) {
            toastr.error('An error occured', {
                closeButton: true,
                progressBar: true
            });
        } else {
            $('#personal-life').text(response.prediction.personal_life);
            $('#profession').text(response.prediction.profession);
            $('#health').text(response.prediction.health);
            $('#travel').text(response.prediction.travel);
            $('#luck').text(response.prediction.luck);
            $('#emotion').text(response.prediction.emotions);
        }
    });
}