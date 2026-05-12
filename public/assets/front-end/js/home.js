"use strict";

updateFlashDealProgressBar();
setInterval(updateFlashDealProgressBar, 10000);

$(document).ready(function () {
  var directionFromSession = $("#direction-from-session").data("value");
  directionFromSession = directionFromSession ? directionFromSession : "ltr";

  $(".flash-deal-slider").owlCarousel({
    loop: false,
    autoplay: true,
    center: false,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 1.1,
      },
      360: {
        items: 1.2,
      },
      375: {
        items: 1.4,
      },
      480: {
        items: 1.8,
      },
      576: {
        items: 2,
      },
      768: {
        items: 3,
      },
      992: {
        items: 4,
      },
      1200: {
        items: 4,
      },
    },
  });

  $(".parmarsh-slider").owlCarousel({
    loop: true,
    autoplay: true,
    center: true,
    margin: 20,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 1,
      },
      360: {
        items: 1,
      },
      375: {
        items: 2,
      },
      540: {
        items: 2,
      },
      576: {
        items: 2,
      },
      768: {
        items: 3,
      },
      992: {
        items: 4,
      },
      1200: {
        items: 4,
      },
    },
  });

  $(".offlinepooja-slider").owlCarousel({
    loop: false,
    autoplay: true,
    center: false,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 1.1,
      },
      360: {
        items: 1.2,
      },
      375: {
        items: 1.4,
      },
      480: {
        items: 1.8,
      },
      576: {
        items: 2,
      },
      768: {
        items: 3,
      },
      992: {
        items: 6,
      },
      1200: {
        items: 6,
      },
    },
  });

  $(".flash-deal-slider-mobile").owlCarousel({
    loop: false,
    autoplay: true,
    center: true,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 1.1,
      },
      360: {
        items: 1.2,
      },
      375: {
        items: 1.4,
      },
      480: {
        items: 1.8,
      },
      576: {
        items: 2,
      },
      768: {
        items: 3,
      },
      992: {
        items: 4,
      },
      1200: {
        items: 4,
      },
    },
  });

  $("#featured_products_list").owlCarousel({
    loop: true,
    autoplay: true,
    margin: 20,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 1,
      },
      360: {
        items: 1,
      },
      375: {
        items: 1,
      },
      540: {
        items: 2,
      },
      576: {
        items: 2,
      },
      768: {
        items: 3,
      },
      992: {
        items: 4,
      },
      1200: {
        items: 6,
      },
    },
  });

  $(".new-arrivals-product").owlCarousel({
    loop: true,
    autoplay: true,
    margin: 20,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 1,
      },
      360: {
        items: 1.02,
      },
      375: {
        items: 1.02,
      },
      540: {
        items: 2,
      },
      576: {
        items: 2,
      },
      768: {
        items: 2,
      },
      992: {
        items: 2,
      },
      1200: {
        items: 4,
      },
      1400: {
        items: 4,
      },
    },
  });

  $(".chadhava-product").owlCarousel({
    loop: false,
    autoplay: true,
    center: false,
    autoplayTimeout: 5000,
    margin: 10,
    nav: false,
    dots: false,
    autoplayHoverPause: false,
    responsive: {
      0: { items: 2 },     // chhoti screen pe 2 dikhe
      540: { items: 3 },   // medium pe 3
      768: { items: 4 },   // tablet pe 4
      992: { items: 5 },   // desktop pe 5
      1200: { items: 6 }   // bada screen pe 6
    }
  });
  
  $(".category-wise-product-slider").each(function () {
    $(this).owlCarousel({
      loop: true,
      autoplay: true,
      margin: 20,
      nav: true,
      navText:
        directionFromSession === "rtl"
          ? [
              "<i class='czi-arrow-right'></i>",
              "<i class='czi-arrow-left'></i>",
            ]
          : [
              "<i class='czi-arrow-left'></i>",
              "<i class='czi-arrow-right'></i>",
            ],
      dots: false,
      autoplayHoverPause: true,
      rtl: directionFromSession === "rtl",
      ltr: directionFromSession === "ltr",
      responsive: {
        0: {
          items: 1.2,
        },
        375: {
          items: 1.4,
        },
        425: {
          items: 2,
        },
        576: {
          items: 3,
        },
        768: {
          items: 4,
        },
        992: {
          items: 5,
        },
        1200: {
          items: 6,
        },
      },
      onInitialized: checkNavigationButtons,
    });
  });

  function checkNavigationButtons(event) {
    var itemCount = event.item.count;
    let owlNav = $(".owl-nav");
    itemCount > 1 ? owlNav.show() : owlNav.hide();
  }

  $(".hero-slider").owlCarousel({
    loop: true,
    autoplay: true,
    margin: 20,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: true,
    autoplayHoverPause: true,
    autoplaySpeed: 1500,
    slideTransition: "linear",
    items: 1,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
  });

  $(".brands-slider").owlCarousel({
    loop: false,
    autoplay: true,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 4,
      },
      360: {
        items: 5,
      },
      576: {
        items: 6,
      },
      768: {
        items: 7,
      },
      992: {
        items: 9,
      },
      1200: {
        items: 11,
      },
      1400: {
        items: 12,
      },
    },
  });
  $(".live-darshan").owlCarousel({
    loop: false,
    autoplay: true,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 4,
      },
      360: {
        items: 3,
      },
      576: {
        items: 3,
      },
      768: {
        items: 6,
      },
      992: {
        items: 9,
      },
      1200: {
        items: 4,
      },
      1400: {
        items: 4,
      },
    },
  });
  $(".canvaz-section").owlCarousel({
    loop: false,
    autoplay: true,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 4,
      },
      360: {
        items: 5,
      },
      576: {
        items: 6,
      },
      768: {
        items: 7,
      },
      992: {
        items: 9,
      },
      1200: {
        items: 6,
      },
      1400: {
        items: 6,
      },
    },
  });
  $(".review-slider").owlCarousel({
    loop: false,
    autoplay: true,
    center: false,
    autoplayTimeout: 5000,
    margin: 10,
    nav: false,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: true,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 1.1,
      },
      360: {
        items: 1.2,
      },
      375: {
        items: 1.4,
      },
      480: {
        items: 1.8,
      },
      576: {
        items: 4,
      },
      768: {
        items: 3,
      },
      992: {
        items: 4,
      },
      1200: {
        items: 4,
      },
    },
  });
  $(".rashis-slider").owlCarousel({
    loop: false,
    autoplay: true,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 4,
      },
      360: {
        items: 3,
      },
      576: {
        items: 3,
      },
      768: {
        items: 6,
      },
      992: {
        items: 9,
      },
      1200: {
        items: 11,
      },
      1400: {
        items: 12,
      },
    },
  });
  $(".feedback-slider").owlCarousel({
    loop: false,
    autoplay: true,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 1,
      },
      360: {
        items: 1,
      },
      576: {
        items: 1,
      },
      768: {
        items: 2,
      },
      992: {
        items: 2,
      },
      1200: {
        items: 3,
      },
      1400: {
        items: 3,
      },
    },
  });
  $(".calculator-slider").owlCarousel({
    loop: false,
    autoplay: true,
    margin: 10,
    nav: true,
    navText:
      directionFromSession === "rtl"
        ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
        : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
    dots: false,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 4,
      },
      360: {
        items: 3,
      },
      576: {
        items: 3,
      },
      768: {
        items: 6,
      },
      992: {
        items: 9,
      },
      1200: {
        items: 9,
      },
      1400: {
        items: 9,
      },
    },
  });

  $(".footer-banner-slider").owlCarousel({
    loop: true,
    autoplay: true,
    margin: 10,
    nav: false,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    autoplayHoverPause: true,
    items: 1,
  });

  $("#category-slider, #top-seller-slider").owlCarousel({
    loop: false,
    autoplay: true,
    margin: 20,
    nav: false,
    dots: true,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 2,
      },
      360: {
        items: 3,
      },
      375: {
        items: 3,
      },
      540: {
        items: 4,
      },
      576: {
        items: 5,
      },
      768: {
        items: 6,
      },
      992: {
        items: 8,
      },
      1200: {
        items: 10,
      },
      1400: {
        items: 11,
      },
    },
  });

  $(".categories--slider").owlCarousel({
    loop: false,
    autoplay: true,
    margin: 20,
    nav: false,
    dots: false,
    autoplayHoverPause: true,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 3,
      },
      360: {
        items: 3.2,
      },
      375: {
        items: 3.5,
      },
      540: {
        items: 4,
      },
      576: {
        items: 5,
      },
      768: {
        items: 6,
      },
      992: {
        items: 8,
      },
      1200: {
        items: 10,
      },
      1400: {
        items: 11,
      },
    },
  });

  const othersStore = $(".others-store-slider").owlCarousel({
    responsiveClass: true,
    nav: false,
    dots: false,
    loop: true,
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    smartSpeed: 600,
    rtl: directionFromSession === "rtl",
    ltr: directionFromSession === "ltr",
    responsive: {
      0: {
        items: 1.3,
        margin: 10,
      },
      480: {
        items: 2,
        margin: 26,
      },
      768: {
        items: 2,
        margin: 26,
      },
      992: {
        items: 3,
        margin: 26,
      },
      1200: {
        items: 4,
        margin: 26,
      },
    },
  });

  $(".store-next").on("click", function () {
    othersStore.trigger("next.owl.carousel", [600]);
  });

  $(".store-prev").on("click", function () {
    othersStore.trigger("prev.owl.carousel", [600]);
  });
});

// user kundali form

// datepicker
$("#datepicker").datepicker({
  uiLibrary: "bootstrap4",
  format: "dd/mm/yyyy",
  modal: true,
  footer: true,
  maxDate: new Date(),
});

// time picker
$("#timepicker").timepicker({
  uiLibrary: "bootstrap4",
  modal: true,
  footer: true,
});
document.getElementById("timepicker").addEventListener("click", function () {
  var innerButton = this.nextElementSibling.querySelector("button");
  innerButton.click();
});

// city load
// $("#places").keyup(function () {
//   var length = $("#places").val().length;
//   $("#citylist").html("");
//   if (length > 1) {
//     let countryName = $("#country").val();
//     let cityName = $("#places").val();
//     let city = "";

//     var data = {
//       country: countryName,
//       name: cityName,
//     };

//     $.ajax({
//       type: "post",
//       url: "https://geo.vedicrishi.in/places/",
//       data: JSON.stringify(data),
//       dataType: "json",
//       headers: {
//         "Content-Type": "application/json",
//       },
//       success: function (response) {
//         $.each(response, function (key, value) {
//           city += `<li class="list-group-item" style="cursor: pointer;" onclick="citydata(${value.latitude},${value.longitude},'${value.place}')">${value.place}</li>`;
//         });
//         $("#citylist").append(city);
//       },
//     });
//   }
// });
// City load
$("#places").keyup(function () {
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
      success: function (response) {
        let city = "";
        $.each(response, function (key, value) {
          city += `<li class="list-group-item" style="cursor: pointer;" onclick="citydata(${
            value.latitude
          }, ${value.longitude}, '${value.place.replace(/'/g, "\\'")}')">${
            value.place
          }</li>`;
        });
        $("#citylist").append(city);
      },
      error: function () {
        console.log("Error loading cities.");
      },
    });
  } else {
    // Hide the dropdown if input length is <= 1
    $(".city-list").hide();
  }
});

// Hide city list on clicking outside
$(document).click(function (event) {
  if (!$(event.target).closest("#places, .city-list").length) {
    $(".city-list").hide(); // Hide dropdown
  }
});

// Prevent dropdown from closing if clicked inside
$(".city-list").on("click", function (event) {
  event.stopPropagation();
});

// lat lon and place
function citydata(lat, lon, place) {
  $("#places").val(place);
  $("#citylist").html("");

  var url = "https://json.astrologyapi.com/v1/timezone_with_dst";
  var data = {
    latitude: lat,
    longitude: lon,
    date: new Date(),
  };
  astroApi(url, "en", data, function (value) {
    if (value == 0) {
      toastr.error("An error occured", {
        closeButton: true,
        progressBar: true,
      });
    } else {
      $("#kundali-latitude").val(lat);
      $("#kundali-longitude").val(lon);
      $("#kundali-timezone").val(value.timezone);
    }
  });
}

// country change
function countrychange() {
  $("#places").val("");
  $("#citylist").html("");
}

// user kundali milan form

// male datepicker
$("#male-datepicker").datepicker({
  uiLibrary: "bootstrap4",
  format: "dd/mm/yyyy",
  modal: true,
  footer: true,
  maxDate: new Date(),
});

// male time picker
$("#male-timepicker").timepicker({
  uiLibrary: "bootstrap4",
  modal: true,
  footer: true,
});
document
  .getElementById("male-timepicker")
  .addEventListener("click", function () {
    var innerButton = this.nextElementSibling.querySelector("button");
    innerButton.click();
  });

// male city load
$("#male-place").keyup(function () {
  var maleLength = $("#male-place").val().length;
  $("#male-city-list").html("");
  if (maleLength > 1) {
    let malecountryName = $("#male-country").val();
    let malecityName = $("#male-place").val();
    let malecity = "";

    var maledata = {
      country: malecountryName,
      name: malecityName,
    };

    $.ajax({
      type: "post",
      url: "https://geo.vedicrishi.in/places/",
      data: JSON.stringify(maledata),
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
      },
      success: function (response) {
        $.each(response, function (key, value) {
          malecity += `<li class="list-group-item" style="cursor: pointer;" onclick="maleCityData(${value.latitude},${value.longitude},'${value.place}')">${value.place}</li>`;
        });
        $("#male-city-list").append(malecity);
      },
    });
  }
});

// male lat lon and place
function maleCityData(lat, lon, place) {
  $("#male-place").val(place);
  $("#male-city-list").html("");

  var url = "https://json.astrologyapi.com/v1/timezone_with_dst";
  var maledata = {
    latitude: lat,
    longitude: lon,
    date: new Date(),
  };
  astroApi(url, "en", maledata, function (value) {
    if (value == 0) {
      toastr.error("An error occured", {
        closeButton: true,
        progressBar: true,
      });
    } else {
      $("#male-latitude").val(lat);
      $("#male-longitude").val(lon);
      $("#male-timezone").val(value.timezone);
    }
  });
}

// mele country change
function maleCountryChange() {
  $("#male-place").val("");
  $("#male-city-list").html("");
}

// female datepicker
$("#female-datepicker").datepicker({
  uiLibrary: "bootstrap4",
  format: "dd/mm/yyyy",
  modal: true,
  footer: true,
  maxDate: new Date(),
});

// female time picker
$("#female-timepicker").timepicker({
  uiLibrary: "bootstrap4",
  modal: true,
  footer: true,
});
document
  .getElementById("female-timepicker")
  .addEventListener("click", function () {
    var innerButton = this.nextElementSibling.querySelector("button");
    innerButton.click();
  });

// female city load
$("#female-place").keyup(function () {
  var femaleLength = $("#female-place").val().length;
  $("#female-city-list").html("");
  if (femaleLength > 1) {
    let femalecountryName = $("#female-country").val();
    let femalecityName = $("#female-place").val();
    let femalecity = "";

    var femaledata = {
      country: femalecountryName,
      name: femalecityName,
    };

    $.ajax({
      type: "post",
      url: "https://geo.vedicrishi.in/places/",
      data: JSON.stringify(femaledata),
      dataType: "json",
      headers: {
        "Content-Type": "application/json",
      },
      success: function (response) {
        $.each(response, function (key, value) {
          femalecity += `<li class="list-group-item" style="cursor: pointer;" onclick="femaleCityData(${value.latitude},${value.longitude},'${value.place}')">${value.place}</li>`;
        });
        $("#female-city-list").append(femalecity);
      },
    });
  }
});

// female lat lon and place
function femaleCityData(lat, lon, place) {
  $("#female-place").val(place);
  $("#female-city-list").html("");

  var url = "https://json.astrologyapi.com/v1/timezone_with_dst";
  var femaledata = {
    latitude: lat,
    longitude: lon,
    date: new Date(),
  };
  astroApi(url, "en", femaledata, function (value) {
    if (value == 0) {
      toastr.error("An error occured", {
        closeButton: true,
        progressBar: true,
      });
    } else {
      $("#female-latitude").val(lat);
      $("#female-longitude").val(lon);
      $("#female-timezone").val(value.timezone);
    }
  });
}

// mele country change
function femaleCountryChange() {
  $("#female-place").val("");
  $("#female-city-list").html("");
}

//  $('.category-filter').on('click', function() {
//         var category = $(this).data('category');
//         filterItems(category);
//     });
//     function filterItems(category) {
//         if (category === 'all') {
//             $('.portfolioDonate').show();
//         } else {
//             $('.portfolioDonate').each(function() {
//                 if ($(this).data('cat') === category) {
//                     $(this).show();
//                 } else {
//                     $(this).hide();
//                 }
//             });
//         }
//     }

////////////////////////////////////////////////////////////

$(".category-filter").on("click", function () {
  var category = $(this).data("category");
  $(".category-filter").removeClass("active-category");
  $(this).addClass("active-category");
  filterItems(category);
});
$(document).ready(function () {
  var category = $(".active-category").data("category");
  filterItems(category);
});
function filterItems(category) {
  $(".portfolioDonate").each(function () {
    if ($(this).data("cat") === category) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
}

////////////////////////////////////////////////////////
//event
$(".event-category-filter").on("click", function () {
  var category = $(this).data("event_category");
  $(".event-category-filter").removeClass("event-active-category");
  $(this).addClass("event-active-category");
  filterEventItems(category);
});
$(document).ready(function () {
  var category = $(".event-active-category").data("event_category");
  filterEventItems(category);
});
function filterEventItems(category) {
  $(".portfolioEvents").stop(true, true).fadeOut(340);
  $(".portfolioEvents").each(function () {
    if ($(this).data("cat") === category) {
      $(this).stop(true, true).fadeIn(340);
    }
  });
}
//TOUR
$(".tour-category-filter").on("click", function () {
  var category = $(this).data("tour_category");
  $(".tour-category-filter").removeClass("tour-active-category");
  $(this).addClass("tour-active-category");
  filterTourItems(category);
});
$(document).ready(function () {
  var category = $(".tour-active-category").data("tour_category");
  filterTourItems(category);
});
function filterTourItems(category) {
  $(".portfolioTour").stop(true, true).fadeOut(340);
  $(".portfolioTour").each(function () {
    if ($(this).data("cat") === category) {
      $(this).stop(true, true).fadeIn(340);
    }
  });
}
////////////////////////////////////////////////////
$(".category-slider").owlCarousel({
  loop: false,
  autoplay: true,
  margin: 10,
  nav: true,
  navText:
    directionFromSession === "rtl"
      ? ["<i class='czi-arrow-right'></i>", "<i class='czi-arrow-left'></i>"]
      : ["<i class='czi-arrow-left'></i>", "<i class='czi-arrow-right'></i>"],
  dots: false,
  rtl: directionFromSession === "rtl",
  ltr: directionFromSession === "ltr",
  autoplayHoverPause: true,
  responsive: {
    0: {
      items: 4,
    },
    360: {
      items: 3,
    },
    576: {
      items: 3,
    },
    768: {
      items: 6,
    },
    992: {
      items: 9,
    },
    1200: {
      items: 11,
    },
    1400: {
      items: 12,
    },
  },
});
