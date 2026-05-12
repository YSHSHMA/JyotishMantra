@extends('layouts.back-end.app')

@section('title', translate('update'))

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
                {{ translate('update') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.astrologers.manage.update', $astrologer['id']) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item col-2" role="presentation">
                                            <button class="nav-link w-100 active" id="personal-tab" data-toggle="pill"
                                                data-target="#personal" type="button" role="tab"
                                                aria-controls="personal" aria-selected="true">Personal Detail</button>
                                        </li>
                                        <li class="nav-item col-2" role="presentation">
                                            <button class="nav-link w-100" id="doc-tab" data-toggle="pill"
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
                                        <div class="tab-pane fade show active" id="personal" role="tabpanel"
                                            aria-labelledby="personal-tab">
                                            @include('admin-views.astrologers.partials.update-manage-personal-tab')
                                        </div>

                                        <div class="tab-pane fade show" id="doc" role="tabpanel"
                                            aria-labelledby="doc-tab">
                                            @include('admin-views.astrologers.partials.update-manage-doc-tab')
                                        </div>

                                        <div class="tab-pane fade show" id="skill" role="tabpanel"
                                            aria-labelledby="skill-tab">
                                            @include('admin-views.astrologers.partials.update-manage-skill-tab')
                                        </div>

                                        <div class="tab-pane fade show" id="other" role="tabpanel"
                                            aria-labelledby="other-tab">
                                            @include('admin-views.astrologers.partials.update-manage-other-tab')
                                        </div>
                                        <div class="tab-pane fade show" id="charge" role="tabpanel"
                                            aria-labelledby="charge-tab">
                                            @include('admin-views.astrologers.partials.update-manage-charge-tab')
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- <div class="d-flex gap-3 justify-content-end">
                                <button type="reset" id="reset"
                                    class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                            </div> --}}
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
            const input = document.getElementById("google-address");
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
        var skillId = $('#primary-skill').val();
        var otherSkills = "{{ $astrologer['other_skills'] }}";
        var skills = {!! json_encode($skills) !!};
        // $('#other-skill').html('');

        // $(document).ready(function() {
        //     $.each(skills, function(key, value) {
        //         if (value.id != skillId) {
        //             var selected = otherSkills.includes(value.id) ? 'selected' : '';
        //             $('#other-skill').append(
        //                 `<option value="${value.id}" ${selected}>${value.name}</option>`);
        //         }
        //     });
        // });

        $('#primary-skill').change(function(e) {
            e.preventDefault();
            $('#other-skill').html('');
            var changeSkillId = $('#primary-skill').val();
            $.each(skills, function(key, value) {
                if (value.id != changeSkillId) {
                    $('#other-skill').append(`<option value="${value.id}">${value.name}</option>`);
                }
            });
            

            // if primary skill is pandit
            if ($('#primary-skill').val() == 3) {
                $('#pandit-div').show();
            } else {
                $('#pandit-div').hide();
                $('#pandit-category').val('').trigger('change');
                $('#pooja').val('').trigger('change');
                $('#panda').val('');
                $('#gotra').val('');
                $('#primary-mandir').val('');
                $('#primary-mandir-location').val('');
            }
        });

        $('#other-skill').change(function(e) {
            var otherSkills = $('#other-skill').val();
            
            if (otherSkills.includes("4")) {
                $('#consultation-charge-div').show();
            } else {
                $('.consultation-charge-checkbox').prop('checked',false);
                $('.consultation-charge-input').val('');
                $('.consultation-charge-input').attr('readonly',false);
                $('.consultation-charge-input').attr('disabled',true);
                $('#consultation-charge-div').hide();
            }
        });
    </script>

    {{-- pandit pooja category change --}}
    <script>
        // var selectedPooja = "{{ $astrologer['is_pandit_pooja'] }}";
        var poojaArr = [];
        var selectedPoojaa = JSON.parse({!! json_encode($astrologer['is_pandit_pooja']) !!});
        $.each(selectedPoojaa, function (keys, values) { 
             poojaArr.push(values.id);
        });

        $(document).ready(function() {
            if (poojaArr != 'null') {
                let id = $('#pandit-category').val();
                let options = "";
                let data = {
                    _token: '{{ csrf_token() }}',
                    id: id
                };
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.astrologers.manage.pandit.pooja') }}",
                    data: data,
                    success: function(response) {
                        $('#pooja').html('');
                        if (response.status == 200) {
                            if (response.pooja.length > 0) {
                                $.each(response.pooja, function(key, value) {
                                    var selected = poojaArr.includes(value.id) ?
                                        'selected' : '';
                                    options +=
                                        `<option value="${value.id}" ${selected}>${value.name}</option>`;
                                });
                                $('#pooja').append(options);
                            }
                        }
                    }
                });
            }
        });

        // $('#pandit-category').change(function(e) {
        //     e.preventDefault();

        //     let changeId = $(this).val();
        //     let changeOptions = "";
        //     let changeData = {
        //         _token: '{{ csrf_token() }}',
        //         id: changeId
        //     };
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ route('admin.astrologers.manage.pandit.pooja') }}",
        //         data: changeData,
        //         success: function(response) {
        //             $('#pooja').html('');
        //             if (response.status == 200) {
        //                 if (response.pooja.length > 0) {
        //                     $.each(response.pooja, function(key, value) {
        //                         changeOptions +=
        //                             `<option value="${value.id}">${value.name}</option>`;
        //                     });
        //                     $('#pooja').append(changeOptions);
        //                 }
        //             }
        //         }
        //     });
        // });
    </script>

    {{-- pandit pooja category change --}}
    <script>
        $(document).ready(function () {
            $('#pandit-category').trigger('change');
        });
        $('#pandit-category').change(function(e) {
            e.preventDefault();
            let poojaChargeArr = JSON.parse({!! json_encode($astrologer['is_pandit_pooja']) !!});
            let poojaTimeArr = JSON.parse({!! json_encode($astrologer['is_pandit_pooja_time']) !!});
            let vipPoojaChargeArr = JSON.parse({!! json_encode($astrologer['is_pandit_vippooja']) !!});
            let vipPoojaTimeArr = JSON.parse({!! json_encode($astrologer['is_pandit_vippooja_time']) !!});
            let anushthanChargeArr = JSON.parse({!! json_encode($astrologer['is_pandit_anushthan']) !!});
            let anushthanTimeArr = JSON.parse({!! json_encode($astrologer['is_pandit_anushthan_time']) !!});
            let chadhavaChargeArr = JSON.parse({!! json_encode($astrologer['is_pandit_chadhava']) !!});
            let chadhavaTimeArr = JSON.parse({!! json_encode($astrologer['is_pandit_chadhava_time']) !!});
            var type = $('#type').val();

            let id = $(this).val();
            let list = "";
            let listVip = "";
            let listAnushthan = "";
            let listChadhava = "";
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
                        // pooja
                        if (response.pooja.length > 0) {
                            $('#pooja-list-heading').show();
                            $('#pooja-list').html('');
                            $.each(response.pooja, function(key, value) {
                                let isChargeSet = poojaChargeArr === null?'':poojaChargeArr.hasOwnProperty(value.id);
                                let chargeValue = isChargeSet ? poojaChargeArr[value.id] : '';
                                let timeValue = isChargeSet ? poojaTimeArr[value.id] : '';
                                list += `<div class="my-2 col-12">
                                    <div class="row">
                                    <input type="hidden" name="pooja_charge_id[]" id="pooja-charge-id-input${value.id}" class="form-control" value="${value.id}" ${isChargeSet ? '' : 'disabled'}>
                                    <div class="col-4" style="align-self: center">${value.name}</div>
                                    <div class="col-3">
                                        <input type="number" name="pooja_charge[]" id="pooja-charge-input${value.id}" class="form-control" placeholder="Enter Price" value="${chargeValue}" ${isChargeSet ? (type=='in house'?'readonly':'') : 'disabled'}>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="pooja_time[]" id="pooja-time-input${value.id}" class="form-control" placeholder="Enter Time" value="${timeValue}" ${isChargeSet ? '' : 'disabled'}>
                                    </div>
                                    <div class="col-2" style="text-align: right; align-self: center;">
                                        <div class="custom-control custom-switch mr-2">
                                            <input type="checkbox"
                                                class="custom-control-input pooja-charge-checkbox"
                                                id="poojaChargeCustomSwitch${value.id}" data-id="${value.id}" ${isChargeSet ? 'checked' : ''}>
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

                        // vipPooja
                        if (response.vipPooja.length > 0) {
                            $('#vip-pooja-list-heading').show();
                            $('#vip-pooja-list').html('');
                            $.each(response.vipPooja, function(key, valueVip) {
                                let isChargeSetVip = vipPoojaChargeArr === null?'':vipPoojaChargeArr.hasOwnProperty(valueVip.id);
                                let chargeValueVip = isChargeSetVip ? vipPoojaChargeArr[valueVip.id] : '';
                                let timeValueVip = isChargeSetVip ? vipPoojaTimeArr[valueVip.id] : '';
                                listVip += `<div class="my-2 col-12">
                                    <div class="row">
                                    <input type="hidden" name="vip_pooja_charge_id[]" id="vip-pooja-charge-id-input${valueVip.id}" class="form-control" value="${valueVip.id}" ${isChargeSetVip ? '' : 'disabled'}>
                                    <div class="col-4" style="align-self: center">${valueVip.name}</div>
                                    <div class="col-3">
                                        <input type="number" name="vip_pooja_charge[]" id="vip-pooja-charge-input${valueVip.id}" class="form-control" placeholder="Enter Price" value="${chargeValueVip}" ${isChargeSetVip ? (type=='in house'?'readonly':'') : 'disabled'}>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="vip_pooja_time[]" id="vip-pooja-time-input${valueVip.id}" class="form-control" placeholder="Enter Time" value="${timeValueVip}" ${isChargeSetVip ? '' : 'disabled'}>
                                    </div>
                                    <div class="col-2" style="text-align: right; align-self: center;">
                                        <div class="custom-control custom-switch mr-2">
                                            <input type="checkbox"
                                                class="custom-control-input vip-pooja-charge-checkbox"
                                                id="vipPoojaChargeCustomSwitch${valueVip.id}" data-id="${valueVip.id}" ${isChargeSetVip ? 'checked' : ''}>
                                            <label class="custom-control-label"
                                                for="vipPoojaChargeCustomSwitch${valueVip.id}"></label>
                                        </div>
                                    </div></div></div>`;
                            });
                            $('#vip-pooja-list').append(listVip);
                        } else{
                            $('#vip-pooja-list').html('');
                            $('#vip-pooja-list-heading').hide();
                        }

                        // anushthan
                        if (response.anushthan.length > 0) {
                            $('#anushthan-list-heading').show();
                            $('#anushthan-list').html('');
                            $.each(response.anushthan, function(key, anushthanValue) {
                                let isChargeSetAnushthan = anushthanChargeArr === null?'':anushthanChargeArr.hasOwnProperty(anushthanValue.id);
                                let chargeValueAnushthan = isChargeSetAnushthan ? anushthanChargeArr[anushthanValue.id] : '';
                                let timeValueAnushthan = isChargeSetAnushthan ? anushthanTimeArr[anushthanValue.id] : '';
                                listAnushthan += `<div class="my-2 col-12">
                                    <div class="row">
                                    <input type="hidden" name="anushthan_charge_id[]" id="anushthan-charge-id-input${anushthanValue.id}" class="form-control" value="${anushthanValue.id}" ${isChargeSetAnushthan ? '' : 'disabled'}>
                                    <div class="col-4" style="align-self: center">${anushthanValue.name}</div>
                                    <div class="col-3">
                                        <input type="number" name="anushthan_charge[]" id="anushthan-charge-input${anushthanValue.id}" class="form-control" placeholder="Enter Price" value="${chargeValueAnushthan}" ${isChargeSetAnushthan ? (type=='in house'?'readonly':'') : 'disabled'}>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="anushthan_time[]" id="anushthan-time-input${anushthanValue.id}" class="form-control" placeholder="Enter Time" value="${timeValueAnushthan}" ${isChargeSetAnushthan ? '' : 'disabled'}>
                                    </div>
                                    <div class="col-2" style="text-align: right; align-self: center;">
                                        <div class="custom-control custom-switch mr-2">
                                            <input type="checkbox"
                                                class="custom-control-input anushthan-charge-checkbox"
                                                id="anushthanChargeCustomSwitch${anushthanValue.id}" data-id="${anushthanValue.id}" ${isChargeSetAnushthan ? 'checked' : ''}>
                                            <label class="custom-control-label"
                                                for="anushthanChargeCustomSwitch${anushthanValue.id}"></label>
                                        </div>
                                    </div></div></div>`;
                            });
                            $('#anushthan-list').append(listAnushthan);
                        } else{
                            $('#anushthan-list').html('');
                            $('#anushthan-list-heading').hide();
                        }

                        // chadhava
                        if (response.chadhava.length > 0) {
                            $('#chadhava-list-heading').show();
                            $('#chadhava-list').html('');
                            $.each(response.chadhava, function(key, chadhavaValue) {
                                let isChargeSetChadhava = chadhavaChargeArr === null?'':chadhavaChargeArr.hasOwnProperty(chadhavaValue.id);
                                let chargeValueChadhava = isChargeSetChadhava ? chadhavaChargeArr[chadhavaValue.id] : '';
                                let timeValueChadhava = isChargeSetChadhava ? chadhavaTimeArr[chadhavaValue.id] : '';
                                listChadhava += `<div class="my-2 col-12">
                                    <div class="row">
                                    <input type="hidden" name="chadhava_charge_id[]" id="chadhava-charge-id-input${chadhavaValue.id}" class="form-control" value="${chadhavaValue.id}" ${isChargeSetChadhava ? '' : 'disabled'}>
                                    <div class="col-4" style="align-self: center">${chadhavaValue.name}</div>
                                    <div class="col-3">
                                        <input type="number" name="chadhava_charge[]" id="chadhava-charge-input${chadhavaValue.id}" class="form-control" placeholder="Enter Price" value="${chargeValueChadhava}" ${isChargeSetChadhava ? (type=='in house'?'readonly':'') : 'disabled'}>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="chadhava_time[]" id="chadhava-time-input${chadhavaValue.id}" class="form-control" placeholder="Enter Time" value="${timeValueChadhava}" ${isChargeSetChadhava ? '' : 'disabled'}>
                                    </div>
                                    <div class="col-2" style="text-align: right; align-self: center;">
                                        <div class="custom-control custom-switch mr-2">
                                            <input type="checkbox"
                                                class="custom-control-input chadhava-charge-checkbox"
                                                id="chadhavaChargeCustomSwitch${chadhavaValue.id}" data-id="${chadhavaValue.id}" ${isChargeSetChadhava ? 'checked' : ''}>
                                            <label class="custom-control-label"
                                                for="chadhavaChargeCustomSwitch${chadhavaValue.id}"></label>
                                        </div>
                                    </div></div></div>`;
                            });
                            $('#chadhava-list').append(listChadhava);
                        } else{
                            $('#chadhava-list').html('');
                            $('#chadhava-list-heading').hide();
                        }
                    }
                }
            });
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

    {{-- pooja charge checkbox --}}
    <script>
        $(document).on('change', '.pooja-charge-checkbox', function() {
            var isChecked = $(this).prop('checked');
            var id = $(this).data('id');
            var type = $('#type').val();
            if (isChecked) {
                $('#pooja-charge-input' + id).removeAttr('disabled');
                $('#pooja-charge-id-input' + id).removeAttr('disabled');
                $('#pooja-time-input' + id).attr('disabled', false);
                $('#pooja-time-input' + id).val('0 min');
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
                $('#pooja-charge-id-input' + id).attr('disabled', true);
                $('#pooja-time-input' + id).val("");
                $('#pooja-time-input' + id).attr('disabled', true);
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
                $('#vip-pooja-charge-id-input' + id).removeAttr('disabled');
                $('#vip-pooja-time-input' + id).attr('disabled', false);
                $('#vip-pooja-time-input' + id).val('0 min');
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
                $('#vip-pooja-charge-id-input' + id).attr('disabled', true);
                $('#vip-pooja-time-input' + id).val("");
                $('#vip-pooja-time-input' + id).attr('disabled', true);
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
                $('#anushthan-charge-id-input' + id).removeAttr('disabled');
                $('#anushthan-time-input' + id).attr('disabled', false);
                $('#anushthan-time-input' + id).val('0 min');
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
                $('#anushthan-charge-id-input' + id).attr('disabled', true);
                $('#anushthan-time-input' + id).val("");
                $('#anushthan-time-input' + id).attr('disabled', true);
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
                $('#chadhava-charge-id-input' + id).removeAttr('disabled');
                $('#chadhava-time-input' + id).attr('disabled', false);
                $('#chadhava-time-input' + id).val('0 min');
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
                $('#chadhava-charge-id-input' + id).attr('disabled', true);
                $('#chadhava-time-input' + id).val("");
                $('#chadhava-time-input' + id).attr('disabled', true);
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
                    $('#consultation-charge-input' + id).val(0);
                    $('#consultation-charge-input' + id).attr('readonly', true);
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
                $('.kundali-making-charge-div').show();
                if(type == 'in house'){
                    $('#kundali-making-charge-input').val(0);
                    $('#kundali-making-commission-input').val(0);
                     $('#kundali-making-charge-input-pro').val(0);
                } else {
                    $('#kundali-making-commission-input').val(5);
                    $('#kundali-making-commission-input-pro').val(5);
                }
            } else {
                $('#kundali-making-charge-input').val('');
                $('#kundali-making-charge-input-pro').val('');
                $('#kundali-making-commission-input').val('');
                $('.kundali-making-charge-div').hide();
            }
        });
    </script>
@endpush
