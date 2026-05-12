"use strict";

function astroApi(url, lang, data, callback) {
    var userId = '6030';
    var apiKey = 'e9c5f9c214dc6ef8f7b3e44ee550ac25';
    var auth = "Basic " + ethereumjs.Buffer.Buffer.from(userId + ":" + apiKey).toString("base64");
    $.ajax({
        type: "post",
        url: url,
        data: JSON.stringify(data),
        dataType: "json",
        headers: {
            "authorization": auth,
            "Content-Type": 'application/json',
            "Accept-Language": lang
        },
        success: function (response) {
            callback(response);
        }, function(err) {
            callback(0);
        }
    });
}