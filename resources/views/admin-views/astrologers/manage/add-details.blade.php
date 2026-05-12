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
      <div class="col-12">
         <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
            <li class="nav-item col-2" role="presentation">
               <button class="nav-link w-100 active" id="doc-tab" data-toggle="pill"
                  data-target="#doc" type="button" role="tab"
                  aria-controls="doc" aria-selected="true">Document</button>
            </li>
            <li class="nav-item col-2" role="presentation">
               <button class="nav-link w-100" id="skill-tab" data-toggle="pill"
                  data-target="#skill" type="button" role="tab" aria-controls="skill"
                  aria-selected="false">Skill Detail</button>
            </li>
            <li class="nav-item col-2" role="presentation">
               <button class="nav-link w-100" id="other-tab" data-toggle="pill"
                  data-target="#other" type="button" role="tab" aria-controls="other"
                  aria-selected="false">Other Detail</button>
            </li>
            <li class="nav-item col-2" role="presentation">
               <button class="nav-link w-100" id="charge-tab" data-toggle="pill"
                  data-target="#charge" type="button" role="tab" aria-controls="charge"
                  aria-selected="false">Service Charge</button>
            </li>
         </ul>
      </div>
      <div class="col-12">
         <div class="tab-content" id="pills-tabContent">
            {{-- Document Tab --}}
            <div class="tab-pane fade show active" id="doc" role="tabpanel" aria-labelledby="doc-tab">
               <form class="astrologer-form" action="{{ route('admin.astrologers.manage.update-detail', ['id' => $astrologer->id]) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="tab" value="doc">
                  <input type="hidden" name="astrologer_id" value="{{$astrologer->id}}">
                  @include('admin-views.astrologers.partials.manage-doc-tab')
                  <div class="d-flex justify-content-start gap-3 mt-3">
                     <button type="submit" class="btn btn--primary px-4 action-btn">
                     {{ $astrologer->adharcard ? 'Update' : 'Save' }}
                     </button>
                     <button type="button" class="btn btn--secondary skip-tab">Skip</button>
                  </div>
               </form>
            </div>
            {{-- Skill Detail Tab --}}
            <div class="tab-pane fade" id="skill" role="tabpanel" aria-labelledby="skill-tab">
               <form class="astrologer-form" action="{{ route('admin.astrologers.manage.update-detail', ['id' => $astrologer->id]) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="tab" value="skill">
                  <input type="hidden" name="astrologer_id" value="{{$astrologer->id}}">
                  @include('admin-views.astrologers.partials.manage-skill-tab')
                  <div class="d-flex justify-content-start gap-3 mt-3">
                     <button type="submit" class="btn btn--primary px-4 action-btn">
                     {{ $astrologer->primary_skills ? 'Update' : 'Save' }}
                     </button>
                     <button type="button" class="btn btn-secondary back-tab">Back</button>
                     <button type="button" class="btn btn--secondary skip-tab">Skip</button>
                  </div>
               </form>
            </div>
            {{-- Other Detail Tab --}}
            <div class="tab-pane fade" id="other" role="tabpanel" aria-labelledby="other-tab">
               <form class="astrologer-form" action="{{ route('admin.astrologers.manage.update-detail', ['id' => $astrologer->id]) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="tab" value="other">
                  <input type="hidden" name="astrologer_id" value="{{$astrologer->id}}">
                  @include('admin-views.astrologers.partials.manage-other-tab')
                  <div class="d-flex justify-content-start gap-3 mt-3">
                     <button type="submit" class="btn btn--primary px-4 action-btn">
                     {{ $astrologer->highest_qualification ? 'Update' : 'Save' }}
                     </button>
                     <button type="button" class="btn btn-secondary back-tab">Back</button>
                     <button type="button" class="btn btn--secondary skip-tab">Skip</button>
                  </div>
               </form>
            </div>
            {{-- Service Charge Tab --}}
            <div class="tab-pane fade" id="charge" role="tabpanel" aria-labelledby="charge-tab">
               <form class="astrologer-form" action="{{ route('admin.astrologers.manage.update-detail', ['id' => $astrologer->id]) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="tab" value="charge">
                  <input type="hidden" name="astrologer_id" value="{{$astrologer->id}}">
                  @include('admin-views.astrologers.partials.manage-charge-tab')
                  <div class="d-flex justify-content-start gap-3 mt-3">
                     <button type="submit" class="btn btn--primary px-4 action-btn">
                     {{ $astrologer->consultation_fee ? 'Update' : 'Save' }}
                     </button>
                     <button type="button" class="btn btn-secondary back-tab">Back</button>
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

    {{-- primary skill change --}}
    <script>
        otherSkill();
        $('#primary-skill').change(function(e) {
            e.preventDefault();
            var type = $('#type').val();

            otherSkill();

            if ($('#primary-skill').val() == 4 && type=='freelancer') {
                $('#astrologer-charge-div').show();
            } else {
                $('#astrologer-charge-div').hide();
            }

            // if primary skill is pandit
            if ($('#primary-skill').val() == 3) {
                $('#pandit-charge-div').show();
                $('#pandit-div').show();
                // $('#astrologer-charge-div').hide();
                $('#consultation-charge-div').hide();
                $('#other-skill-div').show();
            } else {
                $('#other-skill').val('');
                $('#other-skill-div').hide();
                // $('#astrologer-charge-div').show();
                $('#consultation-charge-div').show();
                $('#pandit-charge-div').hide();
                $('#pandit-div').hide();
                $('#pandit-category').val('').trigger('change');
                $('#poojaw').val('').trigger('change');
                $('#panda').val('');
                $('#gotra').val('');
                $('#primary-mandir').val('');
                $('#primary-mandir-location').val('');
                $('#is-offlinepooja').prop('checked', false);
                $('.offlinepooja-charge-input').val('');
                $('.offlinepooja-charge-input').prop('disabled', true);
                $('.offlinepooja-time-input').val('');
                $('.offlinepooja-time-input').prop('disabled', true);
                $('.offlinepooja-charge-checkbox').prop('checked', false);
                $('#offlinepooja-div').hide();
            }
        });

        function otherSkill() {
            var skillId = $('#primary-skill').val();
            var skills = {!! json_encode($skills) !!};
            var options = "";

            $('#other-skill').html('');
            $.each(skills, function(key, value) {
                if (value.id != skillId) {
                    options += `<option value="${value.id}">${value.name}</option>`;
                }
            });
            $('#other-skill').append(options);
        }

        $('#other-skill').change(function(e) {
            var otherSkills = $('#other-skill').val();
            
            if (otherSkills.includes("4")) {
                $('#consultation-charge-div').show();
            } else {
                $('.consultation-charge-checkbox').prop('checked',false);
                $('.consultation-charge-input').attr('readonly',false);
                $('.consultation-charge-input').attr('disabled',true);
                $('.consultation-charge-input').val('');
                $('#consultation-charge-div').hide();
            }
        });
    </script>

    {{-- pandit pooja category append as type and salary div hide/show --}}
    <script>
        var typePageLoad = $('#type').val();
        panditCategoryLoad(typePageLoad);
        
        $('#type').change(function (e) { 
            e.preventDefault();
            
            var typeOnChange = $(this).val();
            panditCategoryLoad(typeOnChange);

            //start salary div hide/show
            if(typeOnChange == 'in house'){
                $('#salary-div').show();
            } else{
                $('#salary-div').hide();
                $('#salary-input').val('');
            }
            //end salary div hide/show
        });

        function panditCategoryLoad(type){            
            $('#pandit-category').html('');
            var panditCategoryList = "";
            var panditCategories = @json($panditCategories);
            $.each(panditCategories, function (key, value) { 
                var option = value.name == 'Vip Pooja'?'Customer Basis':value.name;
                if(type == 'freelancer'){
                    if(option!='Customer Basis' && option!='Anushthan' && option!='Chadhava'){
                        panditCategoryList += `<option value="${value.id}">${option}</option>`;
                    }
                }
                else{
                    panditCategoryList += `<option value="${value.id}">${option}</option>`;
                }
            });
            $('#pandit-category').append(panditCategoryList);
            
            $('#pooja-list-heading').hide();
            $('#vip-pooja-list-heading').hide();
            $('#anushthan-list-heading').hide();
            $('#chadhava-list-heading').hide();
            $('#pooja-list').html('');
            $('#vip-pooja-list').html('');
            $('#anushthan-list').html('');
            $('#chadhava-list').html('');

        }

    </script>

    {{-- pandit pooja category change --}}
    <script>
        $('#pandit-category').change(function(e) {
            e.preventDefault();

            let id = $(this).val();
            let list = "";
            let vipPoojaList = "";
            let anushthanList = "";
            let chadhavaList = "";
            let data = {
                _token: '{{ csrf_token() }}',
                id: id
            };
            $.ajax({
                type: "POST",
                url: "{{ route('admin.astrologers.manage.pandit.pooja') }}",
                data: data,
                success: function(response) {
                    if (response.status == 200) {
                        if (response.pooja.length > 0) {
                            $('#pooja-list').html('');
                            $('#pooja-list-heading').show();
                            $.each(response.pooja, function(key, value) {
                                list += `<div class="my-2 col-12">
                                    <div class="row">
                                    <input type="hidden" name="pooja_charge_id[]" id="pooja-charge-id-input${value.id}" class="form-control" value="${value.id}" disabled>
                                    <div class="col-4" style="align-self: center">${value.name}</div>
                                    <div class="col-3">
                                        <input type="number" name="pooja_charge[]" id="pooja-charge-input${value.id}" class="form-control" placeholder="Enter Price" disabled>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="pooja_time[]" id="pooja-time-input${value.id}" class="form-control" placeholder="Enter Time" disabled>
                                    </div>
                                    <div class="col-2" style="text-align: right; align-self: center;">
                                        <div class="custom-control custom-switch mr-2">
                                            <input type="checkbox"
                                                class="custom-control-input pooja-charge-checkbox"
                                                id="poojaChargeCustomSwitch${value.id}" data-id="${value.id}">
                                            <label class="custom-control-label"
                                                for="poojaChargeCustomSwitch${value.id}"></label>
                                        </div>
                                    </div></div></div>`;
                            });
                            $('#pooja-list').append(list);
                        } else{
                            $('#pooja-list').html('');
                            $('#pooja-list-heading').hide();
                        }

                        // vip pooja
                        if (response.vipPooja.length > 0) {
                            $('#vip-pooja-list').html('');
                            $('#vip-pooja-list-heading').show();
                            $.each(response.vipPooja, function(key, value) {
                                vipPoojaList += `<div class="my-2 col-12">
                                    <div class="row">
                                    <input type="hidden" name="vip_pooja_charge_id[]" id="vip-pooja-charge-id-input${value.id}" class="form-control" value="${value.id}" disabled>
                                    <div class="col-4" style="align-self: center">${value.name}</div>
                                    <div class="col-3">
                                        <input type="number" name="vip_pooja_charge[]" id="vip-pooja-charge-input${value.id}" class="form-control" placeholder="Enter Price" disabled>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="vip_pooja_time[]" id="vip-pooja-time-input${value.id}" class="form-control" placeholder="Enter Time" disabled>
                                    </div>
                                    <div class="col-2" style="text-align: right; align-self: center;">
                                        <div class="custom-control custom-switch mr-2">
                                            <input type="checkbox"
                                                class="custom-control-input vip-pooja-charge-checkbox"
                                                id="vipPoojaChargeCustomSwitch${value.id}" data-id="${value.id}">
                                            <label class="custom-control-label"
                                                for="vipPoojaChargeCustomSwitch${value.id}"></label>
                                        </div>
                                    </div></div></div>`;
                            });
                            $('#vip-pooja-list').append(vipPoojaList);
                        } else{
                            $('#vip-pooja-list').html('');
                            $('#vip-pooja-list-heading').hide();
                        }

                        // anushthan
                        if (response.anushthan.length > 0) {
                            $('#anushthan-list').html('');
                            $('#anushthan-list-heading').show();
                            $.each(response.anushthan, function(key, value) {
                                anushthanList += `<div class="my-2 col-12">
                                    <div class="row">
                                    <input type="hidden" name="anushthan_charge_id[]" id="anushthan-charge-id-input${value.id}" class="form-control" value="${value.id}" disabled>
                                    <div class="col-4" style="align-self: center">${value.name}</div>
                                    <div class="col-3">
                                        <input type="number" name="anushthan_charge[]" id="anushthan-charge-input${value.id}" class="form-control" placeholder="Enter Price" disabled>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="anushthan_time[]" id="anushthan-time-input${value.id}" class="form-control" placeholder="Enter Time" disabled>
                                    </div>
                                    <div class="col-2" style="text-align: right; align-self: center;">
                                        <div class="custom-control custom-switch mr-2">
                                            <input type="checkbox"
                                                class="custom-control-input anushthan-charge-checkbox"
                                                id="anushthanChargeCustomSwitch${value.id}" data-id="${value.id}">
                                            <label class="custom-control-label"
                                                for="anushthanChargeCustomSwitch${value.id}"></label>
                                        </div>
                                    </div></div></div>`;
                            });
                            $('#anushthan-list').append(anushthanList);
                        } else{
                            $('#anushthan-list').html('');
                            $('#anushthan-list-heading').hide();
                        }

                        // chadhava
                        if (response.chadhava.length > 0) {
                            $('#chadhava-list').html('');
                            $('#chadhava-list-heading').show();
                            $.each(response.chadhava, function(key, value) {
                                chadhavaList += `<div class="my-2 col-12">
                                    <div class="row">
                                    <input type="hidden" name="chadhava_charge_id[]" id="chadhava-charge-id-input${value.id}" class="form-control" value="${value.id}" disabled>
                                    <div class="col-4" style="align-self: center">${value.name}</div>
                                    <div class="col-3">
                                        <input type="number" name="chadhava_charge[]" id="chadhava-charge-input${value.id}" class="form-control" placeholder="Enter Price" disabled>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="chadhava_time[]" id="chadhava-time-input${value.id}" class="form-control" placeholder="Enter Time" disabled>
                                    </div>
                                    <div class="col-2" style="text-align: right; align-self: center;">
                                        <div class="custom-control custom-switch mr-2">
                                            <input type="checkbox"
                                                class="custom-control-input chadhava-charge-checkbox"
                                                id="chadhavaChargeCustomSwitch${value.id}" data-id="${value.id}">
                                            <label class="custom-control-label"
                                                for="chadhavaChargeCustomSwitch${value.id}"></label>
                                        </div>
                                    </div></div></div>`;
                            });
                            $('#chadhava-list').append(chadhavaList);
                        } else{
                            $('#chadhava-list').html('');
                            $('#chadhava-list-heading').hide();
                        }
                    }
                }
            });
        });
    </script>

    {{-- pooja charge checkbox --}}
    <script>
        $(document).on('change', '.pooja-charge-checkbox', function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).data('id');
            var type = $('#type').val();

            if (isChecked) {
                $('#pooja-charge-input' + id).removeAttr('disabled');
                $('#pooja-time-input' + id).removeAttr('disabled');
                $('#pooja-time-input' + id).val('0 min');
                $('#pooja-charge-id-input' + id).removeAttr('disabled');
                if(type == 'in house'){
                    $('#pooja-charge-input' + id).attr('readonly', true);
                    $('#pooja-charge-input' + id).val(0);
                }
            } else {
                if(type == 'in house'){
                    $('#pooja-charge-input' + id).attr('readonly', false);
                }
                $('#pooja-charge-input' + id).val("");
                $('#pooja-charge-input' + id).attr('disabled', true);
                $('#pooja-time-input' + id).val("");
                $('#pooja-time-input' + id).attr('disabled', true);
                $('#pooja-charge-id-input' + id).attr('disabled', true);
            }
        });
    </script>

    {{-- vip pooja charge checkbox --}}
    <script>
        $(document).on('change', '.vip-pooja-charge-checkbox', function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).data('id');
            var type = $('#type').val();

            if (isChecked) {
                $('#vip-pooja-charge-input' + id).removeAttr('disabled');
                $('#vip-pooja-time-input' + id).removeAttr('disabled');
                $('#vip-pooja-time-input' + id).val('0 min');
                $('#vip-pooja-charge-id-input' + id).removeAttr('disabled');
                if(type == 'in house'){
                    $('#vip-pooja-charge-input' + id).attr('readonly', true);
                    $('#vip-pooja-charge-input' + id).val(0);
                }
            } else {
                if(type == 'in house'){
                    $('#vip-pooja-charge-input' + id).attr('readonly', false);
                }
                $('#vip-pooja-charge-input' + id).val("");
                $('#vip-pooja-charge-input' + id).attr('disabled', true);
                $('#vip-pooja-time-input' + id).val("");
                $('#vip-pooja-time-input' + id).attr('disabled', true);
                $('#vip-pooja-charge-id-input' + id).attr('disabled', true);
            }
        });
    </script>

    {{-- anushthan charge checkbox --}}
    <script>
        $(document).on('change', '.anushthan-charge-checkbox', function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).data('id');
            var type = $('#type').val();

            if (isChecked) {
                $('#anushthan-charge-input' + id).removeAttr('disabled');
                $('#anushthan-time-input' + id).removeAttr('disabled');
                $('#anushthan-time-input' + id).val('0 min');
                $('#anushthan-charge-id-input' + id).removeAttr('disabled');
                if(type == 'in house'){
                    $('#anushthan-charge-input' + id).attr('readonly', true);
                    $('#anushthan-charge-input' + id).val(0);
                }
            } else {
                if(type == 'in house'){
                    $('#anushthan-charge-input' + id).attr('readonly', false);
                }
                $('#anushthan-charge-input' + id).val("");
                $('#anushthan-charge-input' + id).attr('disabled', true);
                $('#anushthan-time-input' + id).val("");
                $('#anushthan-time-input' + id).attr('disabled', true);
                $('#anushthan-charge-id-input' + id).attr('disabled', true);
            }
        });
    </script>

    {{-- chadhava charge checkbox --}}
    <script>
        $(document).on('change', '.chadhava-charge-checkbox', function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).data('id');
            var type = $('#type').val();

            if (isChecked) {
                $('#chadhava-charge-input' + id).removeAttr('disabled');
                $('#chadhava-time-input' + id).removeAttr('disabled');
                $('#chadhava-time-input' + id).val('0 min');
                $('#chadhava-charge-id-input' + id).removeAttr('disabled');
                if(type == 'in house'){
                    $('#chadhava-charge-input' + id).attr('readonly', true);
                    $('#chadhava-charge-input' + id).val(0);
                }
            } else {
                if(type == 'in house'){
                    $('#chadhava-charge-input' + id).attr('readonly', false);
                }
                $('#chadhava-charge-input' + id).val("");
                $('#chadhava-charge-input' + id).attr('disabled', true);
                $('#chadhava-time-input' + id).val("");
                $('#chadhava-time-input' + id).attr('disabled', true);
                $('#chadhava-charge-id-input' + id).attr('disabled', true);
            }
        });
    </script>

    {{-- consultation charge checkbox --}}
    <script>
        $('.consultation-charge-checkbox').change(function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).data('id');
            var type = $('#type').val();

            if (isChecked) {
                $('#consultation-charge-input' + id).attr('disabled', false);
                $('#consultation-charge-id-input' + id).attr('disabled', false);
                if(type == 'in house'){
                    $('#consultation-charge-input' + id).attr('readonly', true);
                    $('#consultation-charge-input' + id).val(0);
                }
            } else {
                if(type == 'in house'){
                    $('#consultation-charge-input' + id).attr('readonly', false);
                }
                $('#consultation-charge-input' + id).val("");
                $('#consultation-charge-input' + id).attr('disabled', true);
                $('#consultation-charge-id-input' + id).attr('disabled', true);
            }
        });
    </script>

    {{-- is offline pooja checkbox and list --}}
    <script>
        $('#is-offlinepooja').change(function () {
            if ($(this).is(':checked')) {
                $('#offlinepooja-div').show();
            } else {
                $('.offlinepooja-charge-input').val('');
                $('.offlinepooja-charge-input').prop('disabled', true);
                $('.offlinepooja-time-input').val('');
                $('.offlinepooja-time-input').prop('disabled', true);
                $('.offlinepooja-charge-checkbox').prop('checked', false);
                $('#offlinepooja-div').hide();
            }
        });

        $('.offlinepooja-charge-checkbox').change(function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).data('id');
            var type = $('#type').val();

            if (isChecked) {
                $('#offlinepooja-charge-input' + id).attr('disabled', false);
                $('#offlinepooja-time-input' + id).attr('disabled', false);
                $('#offlinepooja-time-input' + id).val('0 min');
                $('#offlinepooja-charge-id-input' + id).attr('disabled', false);
                if(type == 'in house'){
                    $('#offlinepooja-charge-input' + id).attr('readonly', true);
                    $('#offlinepooja-charge-input' + id).val(0);
                }
            } else {
                if(type == 'in house'){
                    $('#offlinepooja-charge-input' + id).attr('readonly', false);
                }
                $('#offlinepooja-charge-input' + id).val("");
                $('#offlinepooja-charge-input' + id).attr('disabled', true);
                $('#offlinepooja-time-input' + id).val("");
                $('#offlinepooja-time-input' + id).attr('disabled', true);
                $('#offlinepooja-charge-id-input' + id).attr('disabled', true);
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


<script>
   $(document).ready(function () {
       const tabOrder = ['doc', 'skill', 'other', 'charge'];
   
       // Save via AJAX
       $('form.astrologer-form').on('submit', function (e) {
           e.preventDefault();
           const form = $(this);
           const formData = new FormData(this);
           const currentTab = form.find('input[name="tab"]').val();
           const submitBtn = form.find('.action-btn');
   
           submitBtn.prop('disabled', true).text('Saving...');
           form.find('.is-invalid').removeClass('is-invalid');
           form.find('.invalid-feedback').remove();
   
           $.ajax({
               url: form.attr('action'),
               method: "POST",
               data: formData,
               contentType: false,
               processData: false,
               success: function () {
                    if (currentTab === 'charge') {
                        window.location.href = "{{ route('admin.astrologers.pending.list') }}";
                    } else {
                        toastr.success("Saved successfully");
                        submitBtn.text('Update').prop('disabled', false);
                        goToNextTab(currentTab);
                    }
                },
   
               error: function (xhr) {
                   submitBtn.prop('disabled', false).text('Save');
                   if (xhr.status === 422) {
                       const errors = xhr.responseJSON.errors;
                       for (const field in errors) {
                           const input = form.find(`[name="${field}"]`);
                           if (input.length > 0) {
                               input.addClass('is-invalid');
                               input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                           }
                       }
                   } else {
                       toastr.error("Something went wrong.");
                       console.error(xhr.responseText);
                   }
               }
           });
       });
   
       // Skip button
       $(document).on('click', '.skip-tab', function () {
           const currentTab = $(this).closest('form').find('input[name="tab"]').val();
           goToNextTab(currentTab);
       });
   
       // Back button
       $(document).on('click', '.back-tab', function () {
           const currentTab = $(this).closest('form').find('input[name="tab"]').val();
           goToPreviousTab(currentTab);
       });
   
       function goToNextTab(currentTab) {
           const index = tabOrder.indexOf(currentTab);
           if (index !== -1 && index + 1 < tabOrder.length) {
               const nextTab = tabOrder[index + 1];
               $(`#${nextTab}-tab`).tab('show');
           }
       }
   
       function goToPreviousTab(currentTab) {
           const index = tabOrder.indexOf(currentTab);
           if (index > 0) {
               const prevTab = tabOrder[index - 1];
               $(`#${prevTab}-tab`).tab('show');
           }
       }
   });
</script>

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
          'use strict';
          window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
              form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                  event.preventDefault();
                  event.stopPropagation();
                }
                form.classList.add('was-validated');
              }, false);
            });
          }, false);
        })();
        </script>

        
    {{-- highest qualification --}}
    <script>
        function qualification(that){
            var qualification = $(that).val();
            $('#other-qualification-text').val('');
            if(qualification == 'others'){
                $('#other-qualification').css('display','block');
            }else{
                $('#other-qualification').css('display','none');
            }
        }
    </script>

    {{-- is kundali make checkbox --}}
    <script>
        $('#is-kundali-make').change(function () {
            var type = $('#type').val();

            if ($(this).is(':checked')) {
                $('#kundali-making-charge-div').show();
                if(type == 'in house'){
                    $('#kundali-making-charge-input').attr('readonly', true);
                    $('#kundali-making-charge-input').val(0);
                }
            } else {
                if(type == 'in house'){
                    $('#kundali-making-charge-input').attr('readonly', false);
                }
                $('#kundali-making-charge-input').val('');
                $('#kundali-making-charge-div').hide();
            }
        });
    </script>
    <script>
    document.querySelectorAll('.image-preview-before-upload').forEach(input => {
        input.addEventListener('change', function () {
            const target = this.dataset.preview;
            const file = this.files[0];
            if (file && target) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector(target).src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
 </script>
@endpush