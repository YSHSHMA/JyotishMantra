<!DOCTYPE html>
<html lang="hi">

<head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Mahakal • Booking Form</title>
   <!-- Bootstrap 5 -->
   <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/roboto-font.css') }}">
   <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/bootstrapnew.min.css') }}" rel="stylesheet" />
   <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/puja-single.css') }}" rel="stylesheet" />
   <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
   <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
   <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
   <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
   
   <style>
      .city-list {
         position: absolute;
         z-index: 99;
         text-align: left;
         width: 97%;
         overflow-x: hidden;
         height: 170px;
      }

      .tracking-wrapper {
         background: #fff7e6;
         border-radius: 12px;
         padding: 20px;
      }

      /* MAIN FLEX */
      .tracking-steps {
         display: flex;
         justify-content: space-between;
         align-items: center;
         position: relative;
      }

      /* STEP */
      .step {
         text-align: center;
         width: 33%;
         position: relative;
      }

      .step span {
         display: block;
         margin-top: 8px;
         font-size: 13px;
         font-weight: 600;
         color: #777;
      }

      /* CIRCLE */
      .circle {
         width: 38px;
         height: 38px;
         border-radius: 50%;
         background: #e0e0e0;
         margin: 0 auto;
         display: flex;
         align-items: center;
         justify-content: center;
         color: #777;
         font-size: 16px;
         transition: 0.4s;
      }

      /* ACTIVE STEP */
      .step.active .circle {
         background: #ffcc80;
         color: #c47903;
         transform: scale(1.1);
         animation: pulse 1.2s infinite ease-in-out;
      }

      .step.active span {
         color: #c47903;
      }

      /* LINE */
      .line {
         flex: 1;
         height: 4px;
         background: #dcdcdc;
         margin: 0 10px;
         border-radius: 5px;
         position: relative;
      }

      .line.active {
         background: linear-gradient(90deg, #ffe0b2, #ffb74d);
         animation: glow 1.2s infinite alternate;
      }

      /* ANIMATIONS */
      @keyframes pulse {
         0% {
            transform: scale(1);
         }

         50% {
            transform: scale(1.15);
         }

         100% {
            transform: scale(1);
         }
      }

      @keyframes glow {
         from {
            box-shadow: 0 0 5px #ffcc80;
         }

         to {
            box-shadow: 0 0 15px #ff9800;
         }
      }

      /* MOBILE */
      @media (max-width: 768px) {
         .tracking-steps {
            flex-direction: column;
            gap: 20px;
         }

         .line {
            width: 4px;
            height: 40px;
            margin: 0 auto;
         }
      }

      .counselling-card {
         background: #fff;
         border-radius: 15px;
         padding: 20px 25px;
         box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      }

      .form-label {
         font-weight: 600;
         color: #444;
         font-size: 14px;
      }

      .form-control,
      select {
         border-radius: 10px !important;
         padding: 10px 14px !important;
         font-size: 14px;
      }

      #btnStickyProceed {
         border-radius: 10px;
         font-weight: 600;
         padding: 12px;
         font-size: 16px;
         color: #fff;
      }

      @media (max-width: 768px) {
         .counselling-card {
            padding: 18px;
         }

         .form-label {
            font-size: 13px;
         }

         #btnStickyProceed {
            width: 100%;
            margin-top: 12px;
         }
      }

      /* Desktop: Keep Normal */
      .proceed-btn-wrapper {
         position: relative;
      }

      /* Mobile: Make Sticky Bottom */
      @media (max-width: 768px) {
         .proceed-btn-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 12px;
            background: #fff;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.08);
            z-index: 999;
         }

         #btnStickyProceed {
            width: 100%;
            margin: 0;
         }

         body {
            padding-bottom: 80px !important;
            /* prevent overlap */
         }
      }
      
   </style>
   <script type="text/javascript">
      function preventBack() {
         window.history.forward();
      }
      setTimeout("preventBack()", 0);
      window.onunload = function() {
         null
      };
   </script>
</head>

<body>
   @php
   $ecommerceLogo = getWebConfig('company_web_logo');
   @endphp
   <header class="container-fluid py-3 bg-white shadow-sm fixed-top">
      <div class="row align-items-center justify-content-between">
         <div class="col-6">
            <div class="guru-title">
               <span class="small-text">Sacred Rituals by</span>
               <h3>{{ $gurujiname->name }}</h3>
            </div>
         </div>
         <div class="col-6 d-flex justify-content-end">
            <div class="powered-logo text-end">
               <span class="powered-text">Powered by</span>
               <a href="{{ route('guruji.individual', ['name' => Str::slug($gurujiname->name)]) }}">
                  <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}" alt="Mahakal.com" class="site-logo">
               </a>
            </div>
         </div>
      </div>
      <div class="collapse bg-light p-2 mt-2 d-md-none" id="mobileSteps">
         <h6 class="text-warning mb-1">{{ translate('Online Booking Form') }}</h6>
         <p class="text-secondary small mb-0">
            <i class="fas fa-box"></i> {{ translate('Package Selection') }}
            <span class="mx-1">→</span>
            <i class="fas fa-user"></i> {{ translate('Details') }}
            <span class="mx-1">→</span>
            <i class="fas fa-check-circle"></i> {{ translate('Confirmation') }}
            <span class="mx-1">→</span>
            <i class="fas fa-credit-card"></i> {{ translate('Payment') }}
         </p>
      </div>
   </header>
   <main style="margin-top:120px;">
      @if($orderDetail->type == 'panditcounselling')
      @include('web-views.guruji.partial.counselling-sankalp')
      @elseif($orderDetail->type == 'panditpooja')
      @include('web-views.guruji.partial.pooja-sankalp')
      @endif
   </main>
   <!-- Loader -->
   <div id="fullPageLoader"
        style="position: fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(1px);     
            -webkit-backdrop-filter: blur(1px);
            z-index: 999999;
            display:none;
            align-items:center;
            justify-content:center;
            flex-direction: column;">

        <div class="spinner-border text-light" style="width: 4rem; height: 4rem;"></div>
   </div>
   <!-- Success Modal -->
   <div class="modal fade" id="successModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
            <div class="display-6 mb-3"></div>
            <h5>पेमेंट सफल!</h5>
            <p>आपकी बुकिंग कन्फर्म हो गई है।</p>
            <button class="btn btn-warning" data-bs-dismiss="modal">ठीक है</button>
         </div>
      </div>
   </div>
   <!-- JS -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
   <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap.bundle.min.js') }}"></script>
   <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
   <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
   <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
   <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
   <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
        async></script>
        
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
   <script>
      // datepicker
      var today, datepicker;
      today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
      $('#datepicker').datepicker({
         uiLibrary: 'bootstrap4',
         format: 'dd/mm/yyyy',
         modal: true,
         footer: true,
         maxDate: today
      });

      // time picker
      $('#timepicker').timepicker({
         uiLibrary: 'bootstrap4',
         modal: true,
         footer: true
      });

      var $timepicker = $('#timepicker').timepicker({
         uiLibrary: 'bootstrap4',
         modal: true,
         footer: true
      });
   </script>
   <script>
      // city load
      $("#places").keyup(function() {
         var length = $('#places').val().length;
         $('#citylist').html("");
         if (length > 1) {
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
               success: function(response) {
                  $('.city-list').removeClass('d-none');
                  $.each(response, function(key, value) {
                     city +=
                        `<li class="list-group-item" style="cursor: pointer;" onclick="citydata('${value.place}')">${value.place}</li>`;
                  });
                  $('#citylist').append(city);
               }
            });
         }
      });

      // lat lon and place
      function citydata(place) {
         $('#places').val(place);
         $('#citylist').html("");
         $('.city-list').addClass('d-none');
      }

      // country change
      function countrychange() {
         $("#places").val("");
         $('#citylist').html("");
      }
   </script>
   <script>
      document.addEventListener('keydown', function(e) {
         if (
            (e.ctrlKey && (e.key === '+' || e.key === '-' || e.key === '=')) ||
            e.key === 'Meta'
         ) {
            e.preventDefault();
         }
      });
      window.addEventListener('wheel', function(e) {
         if (e.ctrlKey) {
            e.preventDefault();
         }
      }, {
         passive: false
      });
      let lastTouch = 0;
      document.addEventListener('touchend', function(e) {
         let now = new Date().getTime();
         if (now - lastTouch <= 300) {
            e.preventDefault();
         }
         lastTouch = now;
      }, false);
   </script>
   <script>
      $(document).ready(function() {
     

         $('#gotraCheck').change(function() {
            if ($(this).is(':checked')) {
               $('#GotraId').val('kashyapa').prop('readonly', true);
            } else {
               $('#GotraId').val('').prop('readonly', false);
            }
         });

         $('#NewNumberAdd').change(function() {
            if ($(this).is(':checked')) {
               $('#newPhoneAdd').slideDown();
            } else {
               $('#newPhoneAdd').slideUp();
               $('#newPhoneAdd input').val('');
            }
         });
      });
   </script>
   
<script>
   $('#pooja_check').submit(function(e) {
      e.preventDefault();

      let formData = new FormData(this);

      if ($('#gotraCheck').is(':checked')) {
         formData.set('gotra', '');
      }

      $.ajax({
         url: $(this).attr('action') || "{{ route('guruji.yajman.store') }}",
         type: "POST",
         data: formData,
         processData: false,
         contentType: false,
         success: function(res) {

            Swal.fire({
               icon: 'success',
               title: res.message || 'Details Saved Successfully!',
               confirmButtonText: 'OK'
            }).then(() => {

               // BUTTON CHANGE (FINAL WORKING VERSION)
               $('.proceed-btn-wrapper').html(`
                  <a href="{{ route('guruji.individual', ['name' => Str::slug($gurujiname->name)]) }}" 
                     class="btn btn-secondary w-100">
                     ← {{ translate('Back_to_Home') }}
                  </a>
               `);
            });
         }
      });
   });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form   = document.getElementById('counsellingForm');
    const btn    = document.getElementById('btnStickyProceed');
    const loader = document.getElementById('fullPageLoader');

    btn.addEventListener('click', function (e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
            return;
        }
        loader.style.display = 'flex';
    });
});
</script>

<script>
$(document).ready(function () {

   function togglePrashad(value) {
      value = parseInt(value); 
      $('#is_prashad').val(value);
      const fields = $('.hideable-div input');
      if (value === 1) {
         $('.hideable-div').slideDown();
         fields.prop('required', true);

         $('.yes-btn').addClass('btn-warning active').removeClass('btn-outline-dark');
         $('.no-btn').removeClass('btn-warning active').addClass('btn-outline-dark');

      } else {
         $('.hideable-div').slideUp();
         fields.prop('required', false);

         $('.no-btn').addClass('btn-warning active').removeClass('btn-outline-dark');
         $('.yes-btn').removeClass('btn-warning active').addClass('btn-outline-dark');
      }
   }

   togglePrashad($('#is_prashad').val());

   $('.yes-btn').on('click', function () {
      togglePrashad(1);
   });

   $('.no-btn').on('click', function () {
      togglePrashad(0);
   });

});
</script>


<script>
let autocomplete;

function initAutocomplete() {
   const input = document.getElementById("area");
   if (!input) return;

   autocomplete = new google.maps.places.Autocomplete(input, {
      componentRestrictions: { country: "IN" }
   });

   autocomplete.addListener("place_changed", onPlaceChange);
}

function onPlaceChange() {
   const place = autocomplete.getPlace();
   if (!place.address_components) return;

   let state = '', city = '', postalCode = '';

   place.address_components.forEach(component => {
      const type = component.types[0];

      if (type === 'administrative_area_level_1') state = component.long_name;
      if (type === 'locality') city = component.long_name;
      if (type === 'postal_code') postalCode = component.long_name;
   });

   $('#state').val(state);
   $('#city').val(city);
   $('#pincode').val(postalCode);

   $('#latitude').val(place.geometry.location.lat());
   $('#longitude').val(place.geometry.location.lng());
}
</script>


</body>

</html>