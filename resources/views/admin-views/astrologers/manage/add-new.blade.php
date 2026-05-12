@extends('layouts.back-end.app')
@section('title', translate('register'))
@section('content')
@push('css_or_js')
<style>
   .select2-selection__choice {
   background-color: rebeccapurple !important;
   }
</style>
@endpush
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
         {{ translate('register') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.astrologers.manage.add-new') }}" method="post"
                  enctype="multipart/form-data" class="needs-validation" novalidate>
                  @csrf
                  <div class="row">
                     <div class="col-12">
                        <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                           <li class="nav-item col-2" role="presentation">
                              <button class="nav-link w-100 active" id="personal-tab" data-toggle="pill"
                                 data-target="#personal" type="button" role="tab"
                                 aria-controls="personal" aria-selected="true">Personal Detail</button>
                           </li>
                        </ul>
                     </div>
                     <div class="col-12">
                        <div class="tab-content" id="pills-tabContent">
                           <div class="tab-pane fade show active" id="personal" role="tabpanel"
                              aria-labelledby="personal-tab">
                              @include('admin-views.astrologers.partials.manage-personal-tab')
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="d-flex gap-3 justify-content-end">
                     <button type="reset" id="reset"
                        class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/astrologer.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initAutocomplete" async></script>
{{-- search place using google map --}}
<script>
   let autocomplete;
   
   function initAutocomplete() {
       const input = document.getElementById("validationCustom09");
       const options = {
           componentRestrictions: { country: "IN" }
       }
       autocomplete = new google.maps.places.Autocomplete(input, options);
       autocomplete.addListener("place_changed", onPlaceChange)
   }
   
   function onPlaceChange() {
   const place = autocomplete.getPlace();
   const addressComponents = place.address_components;
   
   let latitude = place.geometry.location.lat();
   let longitude = place.geometry.location.lng();
   let address = place.formatted_address;
   let state = '';
   let city = '';
   let postalCode = '';
   
   addressComponents.forEach(component => {
       const componentType = component.types[0];
   
       switch (componentType) {
           case 'administrative_area_level_1':
               state = component.long_name;
               break;
           case 'locality':
               city = component.long_name;
               break;
           case 'postal_code':
               postalCode = component.long_name;
               break;
       }
   });
   
   $('#state').val(state);
   $('#city').val(city);
   $('#pincode').val(postalCode);
   $('#latitude').val(latitude);
   $('#longitude').val(longitude);
   }
</script>
{{-- match account no --}}
<script>
   $('#confirm-account-no').blur(function (e) { 
       e.preventDefault();
       
       var acc = $('#account-no').val();
       var confirmAcc = $('#confirm-account-no').val();
   
       if(acc != confirmAcc){
           $('#account-validate').show();
           $('#submit-btn').prop('disabled',true);
       }
       else{
           $('#account-validate').hide();
           $('#submit-btn').prop('disabled',false);
       }
   });
</script>
{{-- pancard validation --}}
<script>
   function validatePAN(pan) {
       const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
       return panRegex.test(pan);
   }
   
   $('#pancard').blur(function (e) { 
       e.preventDefault();
       
       var pancard = $(this).val();
       if (validatePAN(pancard)) {
           $('#pancard-validate').hide();
           $('#submit-btn').prop('disabled',false);
       } else {
           $('#pancard-validate').show();
           $('#submit-btn').prop('disabled',true);
       }
   
   });
</script>
{{-- check email --}}
<script>
   $('#email').blur(function(e) { 
       e.preventDefault();
   
       var email = $(this).val();
       $.ajax({
           type: "get",
           url: "{{url('admin/astrologers/manage/check/email')}}"+'/'+email,
           success: function (response) {
               if (response.status == 200) {
                   $('#email-validate').show();
                   $('#submit-btn').prop('disabled',true);
               } else {
                   $('#email-validate').hide();
                   $('#submit-btn').prop('disabled',false);
               }
           }
       });
   });
</script>
{{-- check mobile no --}}
<script>
   $('#mobile-no').blur(function(e) { 
       e.preventDefault();
   
       var mobileno = $(this).val();
       $.ajax({
           type: "get",
           url: "{{url('admin/astrologers/manage/check/mobileno')}}"+'/'+mobileno,
           success: function (response) {
               if (response.status == 200) {
                   $('#mobile-no-validate').show();
                   $('#submit-btn').prop('disabled',true);
               } else {
                   $('#mobile-no-validate').hide();
                   $('#submit-btn').prop('disabled',false);
               }
           }
       });
   });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const dob = document.getElementById('dob');
    const type = document.getElementById('type');
    const salaryDiv = document.getElementById('salary-div');
    const salaryInput = document.getElementById('salary-input');

    // Toggle salary field visibility
    function toggleSalary() {
        if (type.value === 'freelancer') {
            salaryDiv.style.display = 'none';
            salaryInput.value = '';
        } else {
            salaryDiv.style.display = 'block';
        }
    }
    type.addEventListener('change', toggleSalary);
    toggleSalary(); // on load

    form.addEventListener('submit', function (e) {
        let valid = true;

        // Clear previous messages
        document.querySelectorAll('.invalid-feedback.dynamic').forEach(el => el.remove());
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        requiredFields.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback dynamic d-block';
                errorDiv.innerText = 'This field is required.';
                input.parentElement.appendChild(errorDiv);
            }
        });


        // Password match
        if (password.value !== confirmPassword.value) {
            valid = false;
            confirmPassword.classList.add('is-invalid');
            confirmPassword.insertAdjacentHTML('afterend', '<div class="invalid-feedback dynamic d-block">Passwords do not match.</div>');
        }

        // DOB age check
        const dobDate = new Date(dob.value);
        const cutoff = new Date();
        cutoff.setFullYear(cutoff.getFullYear() - 14);
        if (!dob.value || dobDate > cutoff) {
            valid = false;
            dob.classList.add('is-invalid');
            dob.insertAdjacentHTML('afterend', '<div class="invalid-feedback dynamic d-block">You must be at least 14 years old.</div>');
        }

        // Email/Mobile AJAX error indicators
        if ($('#email-validate').is(':visible') || $('#mobile-no-validate').is(':visible')) {
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});
</script>


@endpush