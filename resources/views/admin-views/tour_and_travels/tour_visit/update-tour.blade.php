@extends('layouts.back-end.app')
@php
use App\Utils\Helpers;
@endphp
@section('title', translate('edit_Tour'))

@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .step {
        display: none;
    }

    .step.active {
        display: block;
    }

    .bg-color-set {
        background-color: #073b74;
    }

    .toast-top-full-width {
        top: 20px;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        text-align: center;
        z-index: 9999;
        pointer-events: none;
        /* Allows clicking through empty space */
    }

    #toast-container>.toast {
        display: inline-block;
        min-width: 50%;
        max-width: 100%;
        text-align: center;
        pointer-events: all;
    }

    .title-color {
        color: #2c3e50;
    }

    .date-controls {
        display: none;
        margin-top: 20px;
        padding: 20px;
        border-radius: 8px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .date-option {
        margin-bottom: 15px;
    }

    .date-option label {
        font-weight: 500;
    }

    .highlight {
        background-color: #e8f4fd;
        border-left: 4px solid #3498db;
    }

    .weekday-btn {
        margin: 5px;
        padding: 8px 15px;
    }

    .selected-day {
        background-color: #3498db !important;
        color: white !important;
    }

    .date-preview {
        margin-top: 15px;
        padding: 10px;
        background-color: #f1f8ff;
        border-radius: 5px;
    }

    .date-badge {
        margin: 3px;
        padding: 5px 10px;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('edit_Tour') }}
        </h2>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <!-- Progress Bar -->
            <div class="mb-5">
                <div class="d-flex justify-content-between mb-2">
                    <span>Step 1: Basic Information</span>
                    <span>Step 2: Pricing & Packages</span>
                    <span>Step 3: Media & Finalize</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <!-- Form Steps -->
            <form id="tourForm" class="needs-validation" novalidate enctype="multipart/form-data">
                @csrf
                <input type="hidden" class="tour_insert_id" value="{{ $getData['id'] }}">
                <div class="step active" id="step1">
                    <div class="card">
                        <div class="card-header bg-color-set">
                            <h4 class="mb-0 text-white">Basic Tour Information</h4> <span class="text-white"><input type="checkbox" onclick="stepinput('step1',this)" id="newinputCheckbox1" value="1">&nbsp;Do not update the form</span>
                        </div>
                        <div class="card-body">
                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs mb-4" id="langTabs" role="tablist">
                                @foreach ($languages as $lang)
                                <li class="nav-item">
                                    <span class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer" id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                                </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="langTabContent">
                                @foreach ($languages as $key=>$lang)
                                <?php
                                $translate = [];
                                if (count($getData['translations'])) {
                                    foreach ($getData['translations'] as $translations) {
                                        if ($translations->locale == $lang && $translations->key == 'tour_name') {
                                            $translate[$lang]['tour_name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'description') {
                                            $translate[$lang]['description'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'cities_name') {
                                            $translate[$lang]['cities_name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'country_name') {
                                            $translate[$lang]['country_name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'state_name') {
                                            $translate[$lang]['state_name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'part_located') {
                                            $translate[$lang]['part_located'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'highlights') {
                                            $translate[$lang]['highlights'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'inclusion') {
                                            $translate[$lang]['inclusion'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'exclusion') {
                                            $translate[$lang]['exclusion'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'terms_and_conditions') {
                                            $translate[$lang]['terms_and_conditions'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'cancellation_policy') {
                                            $translate[$lang]['cancellation_policy'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'notes') {
                                            $translate[$lang]['notes'] = $translations->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form" id="{{ $lang }}-form">
                                    <div class="row mt-4">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}tour_name">{{ translate('tour_name') }} ({{ strtoupper($lang) }}) </label>
                                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="tour_name[]" id="{{ $lang }}tour_name" class="form-control @error('tour_name.'.$loop->index) is-invalid @enderror" value="{{ old('tour_name.'.$loop->index,($lang == $defaultLanguage ? $getData['tour_name'] : $translate[$lang]['tour_name'] ?? '') ) }}" placeholder="{{ translate('tour_name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}tour_type">{{ translate('tour_type') }} </label>
                                                <select {{ $lang == $defaultLanguage ? 'required' : '' }} name="tour_type" id="{{ $lang }}tour_type" class="form-control @error('tour_type') is-invalid @enderror tour_types" onchange="$('.tour_types').val(this.value)">
                                                    @if(!empty($typeList) && count($typeList))
                                                    @foreach($typeList as $val)
                                                    <option value="{{$val['slug']}}" {{ (( old('tour_type',$getData['tour_type']) == $val['slug'] )?'selected':'' )}}> {{ $val['name'] }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_traveller_name">{{ translate('traveller') }} </label>
                                                <select {{ $lang == $defaultLanguage ? 'required' : '' }} name="created_id" id="{{ $lang }}_traveller_name" class="form-control @error('created_id') is-invalid @enderror created_id" onchange="$('.created_id').val(this.value)">
                                                    <option value="" selected disabled>Select Traveller</option>
                                                    <option value="0" {{ ((old('created_id',$getData['created_id']) == '0' )?'selected':'' ) }}>All Traveller</option>
                                                    @if(!empty($travelar_list) && count($travelar_list) > 0)
                                                    @foreach($travelar_list as $val)
                                                    <option value="{{ $val['id']}}" {{ ((old('created_id',$getData['created_id']) == $val['id'] )?'selected':'' ) }}>{{$val['company_name']}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_cities_name">{{ translate('cities_name') }} </label>
                                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="cities_name[]" id="{{ $lang }}_cities_name" class="form-control @error('cities_name.'.$loop->index) is-invalid @enderror getAddress_google" value="{{ old('cities_name.'.$loop->index,($lang == $defaultLanguage ? $getData['cities_name'] : $translate[$lang]['cities_name'] ?? '') ) }}" placeholder="{{ translate('cities_name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_country_name">{{ translate('country_name') }} </label>
                                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="country_name[]" aria-readonly="readonly" readonly id="{{ $lang }}_country_name" class="form-control @error('country_name.'.$loop->index) is-invalid @enderror " value="{{ old('country_name.'.$loop->index,($lang == $defaultLanguage ? $getData['country_name'] : $translate[$lang]['country_name'] ?? '') ) }}" placeholder="{{ translate('country_name') }}" data-toggle="tooltip" role='tooltip' data-title='Please Select Cities'>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_state_name">{{ translate('state_name') }} </label>
                                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="state_name[]" aria-readonly="readonly" readonly id="{{ $lang }}_state_name" class="form-control @error('state_name.'.$loop->index) is-invalid @enderror" value="{{ old('state_name.'.$loop->index,($lang == $defaultLanguage ? $getData['state_name'] : $translate[$lang]['state_name'] ?? '') ) }}" placeholder="{{ translate('state_name') }}" data-toggle="tooltip" role='tooltip' data-title='Please Select Cities'>
                                                <input type="hidden" name='lat' class="lat_location" value="{{ old('lat', $getData['lat']) }}">
                                                <input type="hidden" name='long' class="long_location" value="{{ old('long', $getData['long']) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_ex_distance">{{ translate('1km_ex_distance_fee') }} </label>
                                                <input type="text" name="ex_distance" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.ex_distance_fee').val(this.value)" class="ex_distance_fee form-control @error('ex_distance') is-invalid @enderror " value="{{ old('ex_distance',$getData['ex_distance']) }}" placeholder="{{ translate('1km_ex_distance_fee') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="number_of_day">{{ translate('number_of_days') }} (Helf day: 0.5)</label>
                                                <input type="text" name="number_of_day" class="form-control @error('number_of_day') is-invalid @enderror number_of_day_number" value="{{ old('number_of_day',($getData['number_of_day']??'')) }}" placeholder="{{ translate('number_of_day') }}" required onkeyup="validateInputValue(this);$('.number_of_day_number').val(this.value)">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color" for="number_of_night">{{ translate('number_of_nights') }} </label>
                                                <input type="text" name="number_of_night" class="form-control @error('number_of_night') is-invalid @enderror number_of_night_number" value="{{ old('number_of_night',($getData['number_of_night']??'')) }}" placeholder="{{ translate('number_of_night') }}" required onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.number_of_night_number').val(this.value)">
                                            </div>
                                        </div>
                                        <div class='col-md-4 form-group'>
                                            <label class="title-color font-weight-bold h3" for="Language">{{ translate('percentage_off') }}</label>
                                            <input type="text" class="form-control" name="percentage_off" value="{{ old('percentage_off',$getData['percentage_off']) }}" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)" placeholder="Please Enter Off Percentage">
                                        </div>
                                        <div class='col-md-4 form-group'>
                                            <label for="plan_type">Choose Plan Type</label>
                                            <select id="plan_type" name="plan_type" class="form-control">
                                                <option value="0" {{ old('plan_type',$getData['plan_type']) == 0 ? 'selected' : '' }}>Basic</option>
                                                <option value="1" {{ old('plan_type',$getData['plan_type']) == 1 ? 'selected' : '' }}>Standard</option>
                                                <option value="2" {{ old('plan_type',$getData['plan_type']) == 2 ? 'selected' : '' }}>Premium</option>
                                                <option value="3" {{ old('plan_type',$getData['plan_type']) == 3 ? 'selected' : '' }}>Golden</option>
                                                <option value="4" {{ old('plan_type',$getData['plan_type']) == 4 ? 'selected' : '' }}>Luxury</option>
                                                <option value="5" {{ old('plan_type',$getData['plan_type']) == 5 ? 'selected' : '' }}>only Cab</option>
                                            </select>
                                        </div>
                                        <div class='col-md-4 form-group'>
                                            <label class="title-color" for="Language">{{ translate('YouTube_link') }}</label>
                                            <input type="text" class="form-control youtubelink_input" name="youtube_link" value="{{ old('youtube_link',$getData['youtube_link']) }}" oninput="$('.youtubelink_input').val(this.value)" required placeholder="Please Enter full Youtube link">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_description">{{ translate('description') }} ({{ strtoupper($lang) }}) </label>
                                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="description[]" id="{{ $lang }}_description" class="form-control ckeditor @error('description.'.$loop->index) is-invalid @enderror">{{ old('description.'.$loop->index,($lang == $defaultLanguage ? $getData['description'] : $translate[$lang]['description'] ?? '')) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_highlights">{{ translate('highlights') }} ({{ strtoupper($lang) }}) </label>
                                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="highlights[]" id="{{ $lang }}_highlights" class="form-control ckeditor @error('highlights.'.$loop->index) is-invalid @enderror">{{ old('highlights.'.$loop->index,($lang == $defaultLanguage ? $getData['highlights'] : $translate[$lang]['highlights'] ?? '')) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_Inclusion">{{ translate('Inclusion') }} ({{ strtoupper($lang) }}) </label>
                                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="inclusion[]" id="{{ $lang }}_Inclusion" class="form-control ckeditor @error('inclusion.'.$loop->index) is-invalid @enderror">{{ old('inclusion.'.$loop->index,($lang == $defaultLanguage ? $getData['inclusion'] : $translate[$lang]['inclusion'] ?? '')) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_exclusion">{{ translate('exclusion') }} ({{ strtoupper($lang) }}) </label>
                                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="exclusion[]" id="{{ $lang }}_exclusion" class="form-control ckeditor @error('exclusion.'.$loop->index) is-invalid @enderror">{{ old('exclusion.'.$loop->index,($lang == $defaultLanguage ? $getData['exclusion'] : $translate[$lang]['exclusion'] ?? '')) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_terms_and_conditions">{{ translate('terms_and_conditions') }} ({{ strtoupper($lang) }}) </label>
                                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="terms_and_conditions[]" id="{{ $lang }}_terms_and_conditions" class="form-control ckeditor @error('terms_and_conditions.'.$loop->index) is-invalid @enderror">{{ old('terms_and_conditions.'.$loop->index,($lang == $defaultLanguage ? $getData['terms_and_conditions'] : $translate[$lang]['terms_and_conditions'] ?? '')) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_cancellation_policy">{{ translate('cancellation_policy ') }} ({{ strtoupper($lang) }}) </label>
                                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="cancellation_policy[]" id="{{ $lang }}_cancellation_policy" class="form-control ckeditor @error('cancellation_policy.'.$loop->index) is-invalid @enderror">{{ old('cancellation_policy.'.$loop->index,($lang == $defaultLanguage ? $getData['cancellation_policy'] : $translate[$lang]['cancellation_policy'] ?? '')) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="title-color" for="{{ $lang }}_notes">{{ translate('notes ') }} ({{ strtoupper($lang) }}) </label>
                                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="notes[]" id="{{ $lang }}_notes" class="form-control ckeditor @error('notes.'.$loop->index) is-invalid @enderror">{{ old('notes.'.$loop->index,($lang == $defaultLanguage ? $getData['notes'] : $translate[$lang]['notes'] ?? '')) }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <div></div> <!-- Empty div for spacing -->
                        <button type="button" class="btn btn-primary btn-action" id="nextToStep2">Save & Next <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 2: Pricing & Packages -->
                <div class="step" id="step2">
                    <div class="card">
                        <div class="card-header bg-color-set">
                            <h4 class="mb-0 text-white">Pricing & Packages</h4> <span class="text-white"><input type="checkbox" onclick="stepinput('step2',this)" id="newinputCheckbox2" value="1">&nbsp;Do not update the form</span>
                        </div>
                        <div class="card-body">
                            <div class="form-section">
                                <div class="row">
                                    <div class='col-md-12 form-group'>
                                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('Package') }}&nbsp;&nbsp;&nbsp; <input type="checkbox" name="is_person_use" value="1" {{ ((old('is_person_use',$getData['is_person_use']) == 1)?'checked':'' ) }} onclick="if(this.checked) {  
                                                $('.cab_divShow').addClass('d-none');  
                                                $('.persons_divShow').removeClass('d-none');
                                                $('.persons_transport_divShow').removeClass('d-none');  
                                                $('.package_divShow').addClass('d-none');  
                                                $(`select[name='use_date'] option[value='3']`).hide();
                                                $(`select[name='use_date']`).val('0');
                                                $('.person_package_includes').removeClass('d-none'); 
                                                if($(`input[name='include_package[food]']`).is(':checked')) {
                                                    $('.per_person_packageInclucde').removeClass('d-none');
                                                } else {
                                                    $('.per_person_packageInclucde').addClass('d-none');
                                                }
                                                    if($(`input[name='include_package[hotel]']`).is(':checked')) {
                                                    $('.per_person_packageInclucdeHotel').removeClass('d-none');
                                                } else {
                                                    $('.per_person_packageInclucdeHotel').addClass('d-none');
                                                }
                                            } else {  
                                                $('.cab_divShow').removeClass('d-none');  
                                                $('.persons_divShow').addClass('d-none'); 
                                                $('.persons_transport_divShow').addClass('d-none');  
                                                $('.package_divShow').removeClass('d-none'); 
                                                $(`select[name='use_date'] option[value='3']`).show();
                                                $('.person_package_includes').addClass('d-none'); 
                                                $('.per_person_packageInclucde').addClass('d-none');
                                                $('.per_person_packageInclucdeHotel').addClass('d-none');
                                            };$('select[name=\'use_date\']').trigger('change');">&nbsp;<small>Use person</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class='col-md-3 form-group'>
                                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('choose_Tour_Plan') }}</label>
                                        <a class="btn btn-sm btn-info" onclick="$('.infotourTypeShow').modal('show')">INFO</a>
                                        <select class="form-control" name='use_date' onchange="use_date_functions(this)">
                                            <option value="0" {{((old('use_date',$getData['use_date']) == 0)?'selected':'' )}}>Cities Tour</option>
                                            <option value="1" {{((old('use_date',$getData['use_date']) == 1)?'selected':'' )}}>Special Tour(With Date)</option>
                                            <option value="4" {{((old('use_date',$getData['use_date']) == 4)?'selected':'' )}}>Special Tour(Without Date)</option>
                                            <option value="2" {{((old('use_date',$getData['use_date']) == 2)?'selected':'' )}}>Daily Tour(With Address)</option>
                                            <option value="3" {{((old('use_date',$getData['use_date']) == 3)?'selected':'' )}} {{ ((old('is_person_use',$getData['is_person_use']) == 1) ? 'style=display:none;' :'' )}}>Daily Tour(WithOut Address)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group  {{((old('use_date',$getData['use_date']) == 0)?'d-none':'' )}} {{((old('use_date',$getData['use_date']) == 2)?'d-none':'' )}}  {{((old('use_date',$getData['use_date']) == 4)?'d-none':'' )}}  {{((old('use_date',$getData['use_date']) == 3)?'d-none':'' )}} use_interested_and_not daily_tour_full_comman">
                                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('start_date_and_end_date') }}</label>
                                        <input type="text" class="form-control all_select_data start_date_end_date" data-point='8' value="{{ old('startandend_date',($getData['startandend_date']??''))}}" name='startandend_date' placeholder="{{ translate('enter_start_to_end_date') }}">
                                    </div>
                                    <div class="col-md-3 form-group  {{((old('use_date',$getData['use_date']) == 0)?'d-none':'' )}} {{((old('use_date',$getData['use_date']) == 2)?'d-none':'' )}}  {{((old('use_date',$getData['use_date']) == 4)?'d-none':'' )}}  {{((old('use_date',$getData['use_date']) == 3)?'d-none':'' )}} use_interested_and_not daily_tour_full_comman">
                                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('pickup_time') }}</label>
                                        <input type="text" class="form-control pickup_times" value="{{ old('pickup_time',($getData['pickup_time']??'')) }}" name='pickup_time' placeholder="{{ translate('pickup_time') }}" readonly>
                                    </div>
                                    <div class="col-md-3 form-group  {{((old('use_date',$getData['use_date']) == 0)?'d-none':'' )}}  {{((old('use_date',$getData['use_date']) == 3)?'d-none':'' )}} use_interested_and_not daily_tour_full_address">
                                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('pickup_location') }}</label>
                                        <input type="text" class="form-control pickup_location_get" value="{{ old('pickup_location',($getData['pickup_location']??'')) }}" name='pickup_location' placeholder="{{ translate('pickup_location') }}">
                                        <input type="hidden" class="pick_up_lat_location" name='pickup_lat' value="{{ old('pickup_lat',($getData['pickup_lat']??'')) }}">
                                        <input type="hidden" class="pick_up_long_location" name='pickup_long' value="{{ old('pickup_long',($getData['pickup_long']??'')) }}">
                                    </div>
                                </div>
                                <div class="row  {{((old('use_date',$getData['use_date']) == 0)?'d-none':'' )}} {{((old('use_date',$getData['use_date']) == 2)?'d-none':'' )}}  {{((old('use_date',$getData['use_date']) == 4)?'d-none':'' )}}  {{((old('use_date',$getData['use_date']) == 3)?'d-none':'' )}} use_interested_and_not daily_tour_full_comman">
                                    <div class="col-md-4">
                                        <!-- satish -->
                                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('add_Customized_Date') }}</label>
                                        <select class="form-control" name='customized_type' id="customized_type">
                                            <option value="0">Select Customiz</option>
                                            <option value="1" {{((old('customized_type',$getData['customized_type']??'') == 1)?'selected':'' )}}>Weekly</option>
                                            <option value="2" {{((old('customized_type',$getData['customized_type']??'') == 2)?'selected':'' )}}>Monthly</option>
                                            <option value="3" {{((old('customized_type',$getData['customized_type']??'') == 3)?'selected':'' )}}>Yearly</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="hidden" name="customized_weekly" class="customized_weekly" value="{{ ((($getData['customized_type']??'') == 1) ? $getData['customized_dates'] : '' ) }}">
                                        <input type="hidden" name="customized_monthly" class="customized_monthly" value="{{ ((($getData['customized_type']??'') == 2) ? $getData['customized_dates'] : '') }}">
                                        <input type="hidden" name="customized_yearly" class="customized_yearly" value="{{ ((($getData['customized_type']??'') == 3) ? $getData['customized_dates'] : '') }}">
                                        <div id="weekly-controls" class="date-controls" style="display : {{((old('customized_type',$getData['customized_type']??'') == 1)?'block':'none' )}}">
                                            <h5 class="title-color">Weekly Date Selection</h5>
                                            <p class="text-muted">Select specific days of the week</p>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="date-option">
                                                        <label>Select Days:</label>
                                                        <div class="mt-2">
                                                            <button type="button" class="btn btn-outline-primary weekday-btn" data-day="1">Monday</button>
                                                            <button type="button" class="btn btn-outline-primary weekday-btn" data-day="2">Tuesday</button>
                                                            <button type="button" class="btn btn-outline-primary weekday-btn" data-day="3">Wednesday</button>
                                                            <button type="button" class="btn btn-outline-primary weekday-btn" data-day="4">Thursday</button>
                                                            <button type="button" class="btn btn-outline-primary weekday-btn" data-day="5">Friday</button>
                                                            <button type="button" class="btn btn-outline-primary weekday-btn" data-day="6">Saturday</button>
                                                            <button type="button" class="btn btn-outline-primary weekday-btn" data-day="0">Sunday</button>
                                                        </div>
                                                    </div>
                                                    <div class="date-preview">
                                                        <h6>Selected Days:</h6>
                                                        <div id="selected-days-container"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="monthly-controls" class="date-controls" style="display : {{((old('customized_type',$getData['customized_type']??'') == 2)?'block':'none' )}}">
                                            <h5 class="title-color">Monthly Date Selection</h5>
                                            <p class="text-muted">Select specific dates in a month</p>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="date-option">
                                                        <label for="month-selector">Select Month:</label>
                                                        <input type="text" id="month-selector" class="form-control" placeholder="Select a month">
                                                    </div>
                                                    <div class="date-option mt-3">
                                                        <label>Select Dates:</label>
                                                        <div id="date-picker-container" class="mt-2"></div>
                                                    </div>
                                                    <div class="date-preview">
                                                        <h6>Selected Dates:</h6>
                                                        <div id="selected-dates-container"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Yearly Date Selection - Multiple Dates -->
                                        <div id="yearly-controls" class="date-controls" style="display : {{((old('customized_type',$getData['customized_type']??'') == 3)?'block':'none' )}}">
                                            <h5 class="title-color">Yearly Date Selection</h5>
                                            <p class="text-muted">Select multiple individual dates throughout the year</p>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="date-option">
                                                        <label for="multi-date-picker">Select Dates:</label>
                                                        <input type="text" id="multi-date-picker" class="form-control" placeholder="Select multiple dates">
                                                    </div>
                                                    <div class="date-preview">
                                                        <h6>Selected Dates:</h6>
                                                        <div id="multi-dates-container"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group person_package_includes {{ ((old('is_person_use',$getData['is_person_use']) == 1)?'':'d-none' ) }}">
                                        <div class="d-flex">
                                            <?php $includePackages = json_decode($getData['is_included_package'], true); ?>
                                            <input type="checkbox" name="include_package[sight_seen]" id="" {{ old('include_package.sight_seen',$includePackages['sightseen']??0 == 1) ? 'checked' : '' }}>&nbsp;Sight-Seen &nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" name="include_package[cab]" id="" {{ old('include_package.cab',$includePackages['cab']??0 == 1) ? 'checked' : '' }}>&nbsp;Transportion &nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" name="include_package[food]" id="" {{ old('include_package.food',$includePackages['food']??0 == 1) ? 'checked' : '' }} onclick="if(this.checked) { $('.per_person_packageInclucde').removeClass('d-none') }else{ $('.per_person_packageInclucde').addClass('d-none') }">&nbsp;Food &nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" name="include_package[hotel]" id="" {{ old('include_package.hotel',$includePackages['hotel']??0 == 1) ? 'checked' : '' }} onclick="if(this.checked) { $('.per_person_packageInclucdeHotel').removeClass('d-none') }else{ $('.per_person_packageInclucdeHotel').addClass('d-none') }">&nbsp;Accomadation &nbsp;&nbsp;&nbsp;
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group add_persons_append_multi persons_divShow  {{ ((old('is_person_use',$getData['is_person_use']) == 1)?'':'d-none' ) }}">
                                        <div class="row">
                                            <div class="col-12">
                                                <span class="font-weight-bolder text-danger">Enter a Include {{(\App\Models\ServiceTax::first()['tour_tax']??0)}}% GST Amount</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class='col-4'>
                                                <label class="title-color fw-bolder" for="Language">{{ translate('people_MIN') }}</label>
                                            </div>
                                            <div class='col-4'>
                                                <label class="title-color fw-bolder" for="Language">{{ translate('people_MAX') }}</label>
                                            </div>
                                            <div class="col-4">
                                                <label class="title-color fw-bolder" for="Language">{{ translate('per_head') }} <a class='btn btn--primary btn-sm p-1 mt-2' onclick="add_new_persons_html()"><i class='tio-add'></i></a>
                                                </label>
                                            </div>
                                        </div>
                                        <?php
                                        $totalRows_cab = old('total_rows_cab', count(json_decode($getData['cab_list_price'] ?? '[]', true) ?? []));
                                        $oldData = json_decode($getData['cab_list_price'] ?? "[]", true);
                                        ?>
                                        <?php if (!empty($oldData)) {
                                            for ($i = 0; $i < $totalRows_cab; $i++) { ?>
                                                <div class="row mt-2 group-row">
                                                    <div class='col-3'>
                                                        <input type='text' class="form-control " name="min_person[]" value="{{ old('min_person.' . $i, $oldData[$i]['min'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );validatePersonPrice(this)" placeholder="{{ translate('enter_Min') }}">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type='text' class="form-control " name="max_person[]" value="{{ old('max_person.' . $i, $oldData[$i]['max'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );validatePersonPrice(this)" placeholder="{{ translate('enter_Max') }}">
                                                    </div>
                                                    <div class="col-2 p-0">
                                                        <input type='text' class="form-control base-price" name="package_price[]" value="{{ old('package_price.' . $i, $oldData[$i]['basic'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('basic_Price') }}">
                                                    </div>
                                                    <div class="col-2 p-0">
                                                        <input type='text' class="form-control included-price" name="person_price[]" value="{{ old('person_price.' . $i, $oldData[$i]['price'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('enter_Price') }}">
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <button type="button" class="btn btn-danger btn-sm p-1 mt-2 remove-row"><i class='tio-remove'></i></button>
                                                    </div>
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>
                                    <div class="col-md-6 form-group add_cab_append_multi cab_divShow {{ ((old('is_person_use',$getData['is_person_use']) == 1)?'d-none':'' ) }}">
                                        <div class="row">
                                            <div class="col-12">
                                                <span class="font-weight-bolder text-danger">Enter a Include {{(\App\Models\ServiceTax::first()['tour_tax']??0)}}% GST Amount</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class='col-6'>
                                                <label class="title-color fw-bolder" for="Language">{{ translate('cab_name') }}</label>
                                            </div>
                                            <div class="col-6">
                                                <label class="title-color fw-bolder" for="Language">{{ translate('Price') }} <a class='btn btn--primary btn-sm p-1 mt-2' onclick="add_new_cab_html()"><i class='tio-add'></i></a></label>
                                            </div>
                                        </div>
                                        <input type="hidden" id="total_rows_cab" name="total_rows_cab" value="{{ old('total_rows_cab', count(json_decode($getData['cab_list_price'] ?? '[]', true) ?? [])) }}">
                                        <?php
                                        $totalRows_cab = old('total_rows_cab', count(json_decode($getData['cab_list_price'] ?? '[]', true) ?? []));
                                        $oldData = json_decode($getData['cab_list_price'], true);
                                        for ($i = 0; $i < $totalRows_cab; $i++) { ?>
                                            <div class="row mt-2">
                                                <div class='col-6 p-0 pr-1'>
                                                    <select class="form-control point_trigger16{{$i}}" name="cab_id[{{ $i }}]" onchange="select_value(this)" data-point='point_trigger16{{$i}}'>
                                                        <option value="" selected disabled>{{ translate('Select_cab') }}</option>
                                                        @if($cab_list)
                                                        @foreach($cab_list as $cabs)
                                                        <option value="{{ $cabs['id'] }}" {{ (collect(old('cab_id.' . $i, $oldData[$i]['cab_id'] ?? ''))->contains($cabs['id'])) ? 'selected' : '' }}>{{ $cabs['name'] }} -({{ $cabs['seats'] }} seat)</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-5 p-0 pr-1">
                                                    <input type='text' class="form-control   point_trigger46{{$i}}" name="price[{{ $i }}]" value="{{ old('price.' . $i, $oldData[$i]['price'] ?? '') }}" onkeyup="select_value(this);this.value = this.value.replace(/[^0-9]/g, '' )" data-point='point_trigger46{{$i}}' placeholder="{{ translate('enter_Price') }}">
                                                </div>
                                                <div class="col-1 p-0">
                                                    <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
                                                    <a class='btn btn--primary btn-sm p-1 mt-2 cab-ex-distance-charge{{ $i }} {{((old("use_date",$getData["use_date"]) == 3)?"":"d-none" )}} specialTourwithoutdate' onclick="cab_ex_distance_model('{{ $i }}')"><i class="tio-bonnet_open"> bonnet_open </i></a>
                                                    <input type="hidden" class="from-control cab-json-show{{ $i }}" name="excharge[{{ $i }}]" value="{{ json_encode(old('excharge.' . $i, $oldData[$i]['exprice'] ?? [])) }}">
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="col-md-6 form-group add_persons_transport_append_multi persons_transport_divShow  {{ ((old('is_person_use',$getData['is_person_use']) == 1)? ((old('use_date',$getData['use_date']) == 0)?'d-none':'' ) :'d-none' ) }} ">
                                        <div class="row">
                                            <div class="col-12">
                                                <span class="font-weight-bolder text-danger">Enter a Include {{(\App\Models\ServiceTax::first()['tour_transport_tax']??0)}}% GST Amount</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class='col-2 px-1'>
                                                <label class="title-color fw-bolder small" for="Language">{{ translate('start_Person') }}</label>
                                            </div>
                                            <div class='col-2 px-1'>
                                                <label class="title-color fw-bolder small" for="Language">{{ translate('end_Person') }}</label>
                                            </div>
                                            <div class="col-2 px-1">
                                                <label class="title-color fw-bolder small" for="Language">{{ translate('pick') }}</label>
                                            </div>
                                            <div class="col-2 px-1">
                                                <label class="title-color fw-bolder small" for="Language">{{ translate('drop') }}</label>
                                            </div>
                                            <div class="col-2 px-1">
                                                <label class="title-color fw-bolder small" for="Language">{{ translate('both') }}</label>
                                            </div>
                                            <div class="col-2 px-1">
                                                <a class='btn btn--primary btn-sm p-1 mt-2' onclick="add_new_person_transport_html()"><i class='tio-add'></i></a>
                                            </div>
                                        </div>
                                        <input type="hidden" id="total_rows_transports" name="total_rows_transports" value={{ old('total_rows_transports', count(json_decode($getData['ex_transport_price'] ?? '[""]', true) ?? [''])) }}>
                                        <?php
                                        $totalRowsTransport = old('total_rows_transports', count(json_decode($getData['ex_transport_price'] ?? '[""]', true) ?? [""]));
                                        $oldDataTransport = json_decode($getData['ex_transport_price'], true);
                                        if ($totalRowsTransport) {
                                            for ($it = 0; $it < $totalRowsTransport; $it++) { ?>
                                                <div class="row mt-2">
                                                    <div class='col-2 px-1'>
                                                        <input type='text' class="form-control px-2" name="start_person[]" value="{{ old('start_person.' . $it, $oldDataTransport[$it]['min'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );validateStartEndPersonsWithLastEnd(this)" placeholder="{{ translate('number') }}">
                                                    </div>
                                                    <div class="col-2 px-1">
                                                        <input type='text' class="form-control px-2" name="end_person[]" value="{{ old('end_person.' . $it, $oldDataTransport[$it]['max'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );validateStartEndPersonsWithLastEnd(this)" placeholder="{{ translate('number') }}">
                                                    </div>
                                                    <div class='col-2 px-1'>
                                                        <input type='text' class="form-control px-2" name="person_pick[]" value="{{ old('person_pick.' . $it, $oldDataTransport[$it]['pick'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('pick') }}">
                                                    </div>
                                                    <div class="col-2 px-1">
                                                        <input type='text' class="form-control px-2" name="person_drop[]" value="{{ old('person_drop.' . $it, $oldDataTransport[$it]['drop'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('drop') }}">
                                                    </div>
                                                    <div class="col-2 px-1">
                                                        <input type='text' class="form-control px-2" name="person_both[]" value="{{ old('person_both.' . $it, $oldDataTransport[$it]['both'] ?? '') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('both') }}">
                                                    </div>
                                                    <div class="col-2 px-1">
                                                        <button type="button" class="btn btn-danger btn-sm p-1 mt-2 remove-row"><i class='tio-remove'></i></button>
                                                    </div>
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>

                                    <div class="col-md-6 add_package_append_multi  package_divShow  {{ ((old('is_person_use',$getData['is_person_use']) == 1)?'d-none':'' ) }}">
                                        <div class=" row">
                                            <div class='col-6'>
                                                <label class="title-color fw-bolder" for="Language">{{ translate('package_name') }}</label>
                                            </div>
                                            <div class="col-6">
                                                <label class="title-color fw-bolder" for="Language">{{ translate('Price') }}</label>
                                                <a class='btn btn--primary btn-sm p-1 mt-2 float-end' onclick="add_new_package_html()"><i class='tio-add'></i></a>
                                            </div>
                                        </div>
                                        <input type="hidden" id="total_rows_package" name="total_rows_package" value="{{ old('total_rows_package', count(json_decode($getData['package_list_price'] ?? '[]', true) ?? [])) }}">
                                        <?php
                                        $totalRows_package = old('total_rows_package', count(json_decode($getData['package_list_price'] ?? '[]', true) ?? []));
                                        $oldData = json_decode($getData['package_list_price'] ?? "[]", true);
                                        for ($i = 0; $i < $totalRows_package; $i++) { ?>
                                            <div class="row mt-2">
                                                <div class='col-4 p-0 pr-1'>
                                                    <select class="form-control point_trigger26{{$i}}" name="package_id[{{ $i }}]" onchange="select_value(this)" data-point='point_trigger26{{$i}}'>
                                                        <option value="">Select Packages</option>
                                                        @if($package_list)
                                                        @foreach($package_list as $packval)
                                                        <option value="{{ $packval['id'] }}" {{ (collect(old('package_id.' . $i, $oldData[$i]['package_id'] ?? []))->contains($packval['id'])) ? 'selected' : '' }}>{{ $packval['name'] }} -({{ $packval['seats'] }} people)</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-1 p-0 pr-1">
                                                    <input type='text' class="p-0 pl-1 form-control pointNumber_trigger48{{$i}}" value="{{ old('pnumber.' . $i, $oldData[$i]['day'] ?? '0') }}" name="pnumber[{{ $i }}]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.point_trigger48{{$i}}').val($('.pointamount_trigger48{{$i}}').val() * $('.pointNumber_trigger48{{$i}}').val())" data-point='pointNumber_trigger48{{$i}}' placeholder="{{ translate('number_of_day_and_stay') }}">
                                                </div>
                                                <div class="col-2 p-0 pr-1">
                                                    <input type='text' class="form-control pointamount_trigger48{{$i}}" name="pperson[{{ $i }}]" value="{{ old('pperson.' . $i, $oldData[$i]['per_price'] ?? '0') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.point_trigger48{{$i}}').val(this.value * $('.pointNumber_trigger48{{$i}}').val())" data-point='pointamount_trigger48{{$i}}' placeholder="{{ translate('enter_per_days') }}">
                                                </div>
                                                <div class="col-4 p-0 pr-1">
                                                    <input type='text' class="form-control point_trigger48{{$i}}" readonly name="pprice[{{ $i }}]" value="{{ old('pprice.' . $i, $oldData[$i]['pprice'] ?? '') }}" onkeyup="select_value(this);this.value = this.value.replace(/[^0-9]/g, '' )" data-point='point_trigger48{{$i}}' placeholder="{{ translate('enter_Price') }}">
                                                </div>

                                                <div class="col-1 p-0">
                                                    <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                    </div>

                                    <div class="col-md-6 form-group per_person_packageInclucde {{ old('include_package.food',$includePackages['food']??0 == 1) ? '' : 'd-none' }}">
                                        <div class="row">
                                            <div class='col-6'>
                                                <label class="title-color fw-bolder" for="Language">{{ translate('food_list') }}</label>
                                            </div>
                                            <div class="col-6">
                                                <label class="title-color fw-bolder" for="Language">{{ translate('Price') }}</label>
                                                <a class='btn btn--primary btn-sm p-1 mt-2 float-end' onclick="food_package_perperson();"><i class='tio-add'></i></a>
                                            </div>
                                        </div>
                                        @php
                                        $totalRows_package = old('total_rows_package', count(json_decode($getData['package_list_price'] ?? '[]', true) ?? []));
                                        $oldData = json_decode($getData['package_list_price']??"[]", true);
                                        @endphp
                                        @for ($ji = 0; $ji < $totalRows_package; $ji++)
                                            @if(collect($package_list)->where('type', 'foods')->contains('id', $oldData[$ji]['package_id']))
                                            <div class="row mt-2 food-row">
                                                <div class='col-4 p-0 pr-1'>
                                                    <select class="form-control foods_trigger26{{$ji}}" name="food_package_id[{{ $ji }}]" onchange="select_value(this);removeSelected_Options()" data-point='foods_trigger26{{$ji}}'>
                                                        <option value="">Select Packages</option>
                                                        @if($package_list)
                                                        @foreach($package_list as $packval)
                                                        @if($packval['type'] == 'foods')
                                                        <option value="{{ $packval['id'] }}" {{ (collect(old('food_package_id.' . $ji, $oldData[$ji]['package_id'] ?? []))->contains($packval['id'])) ? 'selected' : '' }}>{{ $packval['name'] }} -({{ $packval['seats'] }} people)</option>
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-1 p-0 pr-1">
                                                    <input type='text' class="p-0 pl-1 form-control foodsNumber_trigger48{{$ji}} change-price-key" value="{{ old('food_pnumber.' . $ji, $oldData[$ji]['day'] ?? '0') }}" name="food_pnumber[{{ $ji }}]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.foods_trigger48{{$ji}}').val($('.foodsamount_trigger48{{$ji}}').val() * $('.foodsNumber_trigger48{{$ji}}').val())" data-point='foodsNumber_trigger48{{$ji}}' placeholder="{{ translate('number_of_day_and_stay') }}">
                                                </div>
                                                <div class="col-2 p-0 pr-1">
                                                    <input type='text' class="form-control foodsamount_trigger48{{$ji}} change-price-key" name="food_pperson[{{ $ji }}]" value="{{ old('food_pperson.' . $ji, $oldData[$ji]['per_price'] ?? '0') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.foods_trigger48{{$ji}}').val(this.value * $('.foodsNumber_trigger48{{$ji}}').val())" data-point='foodsamount_trigger48{{$ji}}' placeholder="{{ translate('enter_per_days') }}">
                                                </div>
                                                <div class="col-3 p-0 pr-1">
                                                    <input type='text' class="form-control foods_trigger48{{$ji}} row-total" readonly name="food_pprice[{{ $ji }}]" value="{{ old('food_pprice.' . $ji, $oldData[$ji]['pprice'] ?? '') }}" onkeyup="select_value(this);this.value = this.value.replace(/[^0-9]/g, '' )" data-point='foods_trigger48{{$ji}}' placeholder="{{ translate('enter_Price') }}">
                                                </div>
                                                <div class="col-1 p-0 pr-1">
                                                    <input type='checkbox' class="foods_pack_checkedtrigger4{{ $ji }} mt-3 include-item" name="food_check[{{ $ji }}]" value="1" onclick="" data-point='foods_pack_checkedtrigger4{{ $ji }}' {{ ((old('food_check.' . $ji,($oldData[$ji]['included']??0) ) == 1 ) ? 'checked' :'' )}}>
                                                </div>
                                                <div class="col-1 p-0">
                                                    <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
                                                </div>
                                            </div>
                                            @endif
                                            @endfor
                                    </div>
                                    <div class="col-md-6 form-group per_person_packageInclucdeHotel {{ old('include_package.hotel',$includePackages['hotel']??0 == 1) ? '' : 'd-none' }}">
                                        <div class="row">
                                            <div class='col-6'>
                                                <label class="title-color fw-bolder" for="Language">{{ translate('hotel_list') }}</label>
                                            </div>
                                            <div class="col-6">
                                                <label class="title-color fw-bolder" for="Language">{{ translate('Price') }}</label>
                                                <a class='btn btn--primary btn-sm p-1 mt-2 float-end' onclick="hotel_package_perperson();"><i class='tio-add'></i></a>
                                            </div>
                                        </div>
                                        @for ($i = 0; $i < $totalRows_package; $i++)
                                            @if(collect($package_list)->where('type', 'hotel')->contains('id', $oldData[$i]['package_id']))
                                            <div class="row mt-2 hotel-row">
                                                <div class='col-4 p-0 pr-1'>
                                                    <select class="form-control hotal_trigger26{{$i}}" name="hotal_package_id[{{ $i }}]" onchange="select_value(this);removeSelected_Options()" data-point='hotal_trigger26{{$i}}'>
                                                        <option value="">Select Packages</option>
                                                        @if($package_list)
                                                        @foreach($package_list as $packval)
                                                        @if($packval['type'] == 'hotel')
                                                        <option value="{{ $packval['id'] }}" {{ (collect(old('hotal_package_id.' . $i, $oldData[$i]['package_id'] ?? []))->contains($packval['id'])) ? 'selected' : '' }}>{{ $packval['name'] }} -({{ $packval['seats'] }} people)</option>
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-1 p-0 pr-1">
                                                    <input type='text' class="p-0 pl-1 form-control hotalNumber_trigger48{{$i}} change-price-key" value="{{ old('hotal_pnumber.' . $i, $oldData[$i]['day'] ?? '0') }}" name="hotal_pnumber[{{ $i }}]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.hotals_trigger48{{$i}}').val($('.hotalsamount_trigger48{{$i}}').val() * $('.hotalNumber_trigger48{{$i}}').val())" data-point='hotalNumber_trigger48{{$i}}' placeholder="{{ translate('number_of_day_and_stay') }}">
                                                </div>
                                                <div class="col-2 p-0 pr-1">
                                                    <input type='text' class="form-control hotalsamount_trigger48{{$i}} change-price-key" name="hotal_pperson[{{ $i }}]" value="{{ old('hotal_pperson.' . $i, $oldData[$i]['per_price'] ?? '0') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.hotals_trigger48{{$i}}').val(this.value * $('.hotalNumber_trigger48{{$i}}').val())" data-point='hotalsamount_trigger48{{$i}}' placeholder="{{ translate('enter_per_days') }}">
                                                </div>
                                                <div class="col-3 p-0 pr-1">
                                                    <input type='text' class="form-control hotals_trigger48{{$i}} row-total" readonly name="hotal_pprice[{{ $i }}]" value="{{ old('hotal_pprice.' . $i, $oldData[$i]['pprice'] ?? '') }}" onkeyup="select_value(this);this.value = this.value.replace(/[^0-9]/g, '' )" data-point='hotals_trigger48{{$i}}' placeholder="{{ translate('enter_Price') }}">
                                                </div>
                                                <div class="col-1 p-0 pr-1">
                                                    <input type='checkbox' class="hotals_pack_trigger4{{ $i }} mt-3 include-item" name="hotal_check[{{ $i }}]" value="1" onclick="" data-point='hotals_pack_trigger4{{ $i }}' {{ ((old('hotal_check.' . $i,$oldData[$i]['included']??0) == 1 ) ? 'checked' :'' )}}>
                                                </div>
                                                <div class="col-1 p-0">
                                                    <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
                                                </div>
                                            </div>
                                            @endif
                                            @endfor

                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <label class="title-color font-weight-bold h3" for="Language">{{ translate('time_slot(Optional)') }} <a class="btn btn--primary btn-sm p-1 mt-2" onclick="time_slot_add()"><i class="tio-add"></i></a></label>
                                    </div>
                                </div>
                                <div id="time_slot_container">
                                    <?php
                                    $timeSlots = old('time_slot') ?? json_decode($getData['time_slot'] ?? '[]', true);
                                    if (is_array($timeSlots)) {
                                        foreach ($timeSlots as $index => $timeSlot) { ?>
                                            <div class="row time_slot_add_html mt-2">
                                                <div class="col-md-3">
                                                    <input type="text" name="time_slot[]" class="times_slot_pick form-control" value="{{ $timeSlot }}" data-index="{{ $index }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <a class="btn btn-danger btn-sm p-1 mt-2" onclick="time_slot_remove(this)"><i class="tio-remove"></i></a>
                                                </div>
                                            </div>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-primary btn-action" id="backToStep1"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="button" class="btn btn-primary btn-action" id="nextToStep3">Save & Next <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 3: Media & Finalize -->
                <div class="step" id="step3">
                    <div class="card">
                        <div class="card-header bg-color-set">
                            <h4 class="mb-0 text-white">Media & Finalize</h4> <span class="text-white"><input type="checkbox" onclick="stepinput('step3',this)" id="newinputCheckbox3" value="1">&nbsp;Do not update the form</span>
                        </div>
                        <div class="card-body">
                            <div class="form-section">

                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('tour_image') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_Tour_image') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="tour_image" multiple class="custom-upload-input-file action-upload-color-image" id="tour_image_update" data-imgpreview="pre_tour_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_tour_img_viewer" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $getData['tour_image'], type: 'backend-product') }}" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp,' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="additional_image_column col-md-9">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                    <div>
                                                        <label for="name"
                                                            class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                                        <span
                                                            class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                            title="{{ translate('upload_any_additional_images_for_this_product_from_here') }}.">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span>
                                                    </div>

                                                </div>
                                                <p class="text-muted">{{ translate('upload_additional_images') }}</p>
                                                <div class="coba-area">

                                                    <div class="row g-2" id="additional_Image_Section">

                                                        @if (!empty($getData['image']) && json_decode($getData['image'],true))
                                                        @foreach (json_decode($getData['image'],true) as $key => $photo)
                                                        @php($unique_id = rand(1111, 9999))

                                                        <div class="col-sm-12 col-md-4">
                                                            <div
                                                                class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                                <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                                    href="{{ route('admin.tour_visits.delete-image', ['id' => $getData['id'], 'name' => $photo]) }}">
                                                                    <i class="tio-delete"></i>
                                                                </a>
                                                                <div
                                                                    class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                    <img id="additional_Image_{{ $unique_id }}"
                                                                        alt=""
                                                                        class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                        src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $photo, type: 'backend-product') }}">
                                                                </div>
                                                                <div
                                                                    class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                    <div
                                                                        class="d-flex flex-column justify-content-center align-items-center">
                                                                        <img alt=""
                                                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                            class="w-75">
                                                                        <h3 class="text-muted">{{ translate('Upload_Image') }}
                                                                        </h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        @endif

                                                        <div class="col-sm-12 col-md-4">
                                                            <div class="custom_upload_input position-relative border-dashed-2">
                                                                <input type="file" name="images[]"
                                                                    class="custom-upload-input-file action-add-more-image" data-index="1"
                                                                    data-imgpreview="additional_Image_1"
                                                                    accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                    data-target-section="#additional_Image_Section">

                                                                <span
                                                                    class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                                    <i class="tio-delete"></i>
                                                                </span>

                                                                <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                    <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none"
                                                                        alt="" src="">
                                                                </div>
                                                                <div
                                                                    class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                    <div
                                                                        class="d-flex flex-column justify-content-center align-items-center">
                                                                        <img alt=""
                                                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                            class="w-75">
                                                                        <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card mt-1 rest-part">
                                            <div class="card-header">
                                                <div class="d-flex gap-2">
                                                    <i class="tio-document"></i>
                                                    <h4 class="mb-0">
                                                        {{ translate('itinerary') }}
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top" title="Add itinerary Pdf Format Upload">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                        </span>
                                                    </h4>
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label for="itineraryPdf" class="form-label text-dark font-weight-bold">Upload Itinerary (PDF)</label>
                                                        <input type="file" class="form-control" name="itineraryupload" id="itineraryPdf" accept="application/pdf">
                                                    </div>

                                                    <!-- PDF Preview Section -->
                                                    <div class="col-md-12">
                                                        <div id="pdfPreviewContainer" class="mt-3" {{ (($getData['itineraryupload'] ?? '' ) ? '' : ' style="display:none;" ' ) }}>
                                                            <label class="font-weight-bold text-dark">Preview:</label>
                                                            <iframe id="pdfPreview" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $getData['itineraryupload'], type: 'backend-product') }}" width="100%" height="500px" style="border:1px solid #ccc; border-radius:8px;"></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card mt-1 rest-part">
                                            <div class="card-header">
                                                <div class="d-flex gap-2">
                                                    <i class="tio-user-big"></i>
                                                    <h4 class="mb-0">
                                                        {{ translate('seo_section') }}
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="{{ translate('add_meta_titles_descriptions_and_images_for_tour') . ', ' . translate('this_will_help_more_people_to_find_them_on_search_engines_and_see_the_right_details_while_sharing_on_other_social_platforms') }}">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span>
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label class="title-color">
                                                                {{ translate('meta_Title') }}
                                                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    title="{{ translate('add_the_tour_title_name_taglines_etc_here') . ' ' . translate('this_title_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                        alt="">
                                                                </span>
                                                            </label>
                                                            <input type="text" name="meta_title" placeholder="{{ translate('meta_Title') }}" value="{{ $getData['meta_title']}}" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="title-color">
                                                                {{ translate('meta_Description') }}
                                                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    title="{{ translate('write_a_short_description_of_the_tour') . ' ' . translate('this_description_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                        alt="">
                                                                </span>
                                                            </label>
                                                            <textarea rows="4" type="text" name="meta_description" class="form-control">{{ $getData['meta_description']}}</textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="d-flex justify-content-center">
                                                            <div class="form-group w-100">
                                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                                    <div>
                                                                        <label class="title-color" for="meta_Image">
                                                                            {{ translate('meta_Image') }}
                                                                        </label>
                                                                        <span
                                                                            class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Meta Thumbnail'] }}</span>
                                                                        <span class="input-label-secondary cursor-pointer"
                                                                            data-toggle="tooltip"
                                                                            title="{{ translate('add_Meta_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}.">
                                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                                alt="">
                                                                        </span>
                                                                    </div>

                                                                </div>

                                                                <div>
                                                                    <div class="custom_upload_input">
                                                                        <input type="file" name="meta_image"
                                                                            class="custom-upload-input-file meta-img action-upload-color-image"
                                                                            id="" data-imgpreview="pre_meta_image_viewer"
                                                                            accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                                                        <span
                                                                            class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                            <i class="tio-delete"></i>
                                                                        </span>

                                                                        <div class="img_area_with_preview position-absolute z-index-2">
                                                                            <img id="pre_meta_image_viewer"
                                                                                class="h-auto bg-white onerror-add-class-d-none"
                                                                                alt=""
                                                                                src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $getData['meta_image'], type: 'backend-product') }}">
                                                                        </div>
                                                                        <div
                                                                            class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                            <div
                                                                                class="d-flex flex-column justify-content-center align-items-center">
                                                                                <img alt="" class="w-65"
                                                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                                <h3 class="text-muted">{{ translate('Upload_Image') }}
                                                                                </h3>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-primary btn-action" id="backToStep2"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="submit" class="btn btn-success btn-action" id="submitForm">Submit Tour <i class="fas fa-check ms-2"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>


<div class="modal fade exDistanceModal" tabindex="-1" aria-labelledby="exDistanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exDistanceModalLabel">Ex-Distance Charge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Start</th>
                            <th>End</th>
                            <th>Charge</th>
                            <th>driver</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="distanceChargeTable">
                    </tbody>
                </table>
                <button type="button" class="btn btn-success btn-sm" onclick="addNewChargeRow()">+ Add Row</button>
                <button type="button" class="btn btn-primary btn-sm float-end" data-dismiss="modal" aria-label="Close">save</button>
            </div>
        </div>
    </div>
</div>

<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>
<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
<div class="modal fade infotourTypeShow" role="dialog" aria-label="modal order">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <i class="tio-clear" aria-hidden="true"></i>
                </button>
                <h4 class="modal-title">Tour Info</h4>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tour-options">
                                <div class="col-md-12 tour-option">
                                    <h5>1️⃣ Cities Tour</h5>
                                    <ol>
                                        <li>Users can book private trips by selecting the number of cabs required.</li>
                                        <li>Food and hotel options will be provided separately (not included in the package).</li>
                                        <li>The total amount will be calculated based on individual selections.</li>
                                    </ol>
                                </div>

                                <div class="col-md-12 tour-option">
                                    <h5>2️⃣ Special Tour (Fixed Date)</h5>
                                    <ol>
                                        <li>Users can book a complete package with a fixed date.</li>
                                        <li>The package includes food and hotel, which cannot be modified.</li>
                                        <li> Users can choose their preferred vehicle type.</li>
                                        <li> Users need to select the number of persons/tickets.</li>
                                    </ol>
                                </div>

                                <div class="col-md-12 tour-option">
                                    <h5>3️⃣ Special Tour (Flexible Date)</h5>
                                    <ol>
                                        <li>Users can book a complete package but with the flexibility to select their own date and time.</li>
                                        <li>The package includes food and hotel, which cannot be modified.</li>
                                        <li>Users can choose their preferred vehicle type.</li>
                                        <li> Users need to select the number of persons/tickets.</li>
                                    </ol>
                                </div>

                                <div class="col-md-12 tour-option">
                                    <h5>4️⃣ Daily Tour (Fixed Pickup Location)</h5>
                                    <ol>
                                        <li>Pickup location is predefined and cannot be changed.</li>
                                        <li> Users can select their own date and time for travel.</li>
                                        <li>A complete package is included.</li>
                                        <li>If the vehicle has 7 seats, users can select up to 7 persons only. </li>
                                        <li> If more than 7 persons need to travel (e.g., 8 persons), the user will need to book two separate times or choose a bigger vehicle (if available).</li>
                                    </ol>
                                </div>
                                <div class="col-md-12 tour-option">
                                    <h5>5️⃣ Daily Tour (Custom Pickup Location)</h5>
                                    <ol>
                                        <li>Users can select their own pickup location, date, and time.</li>
                                        <li> A complete package is included.</li>
                                        <li>If the vehicle has 7 seats, users can select up to 7 persons only.</li>
                                        <li> If more than 7 persons need to travel (e.g., 8 persons), the user will need to book two separate times or choose a bigger vehicle (if available).</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
{{-- ck editor --}}
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Step navigation
        const steps = document.querySelectorAll('.step');
        const progressBar = document.querySelector('.progress-bar');
        let currentStep = 1;
        const totalSteps = 3;

        // Show current step
        function showStep(stepNumber) {
            steps.forEach(step => step.classList.remove('active'));
            document.getElementById(`step${stepNumber}`).classList.add('active');

            // Update progress bar
            const progress = (stepNumber / totalSteps) * 100;
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);

            currentStep = stepNumber;
        }

        // Next button handlers
        document.getElementById('nextToStep2').addEventListener('click', function() {
            if (validateStep1()) {
                if ($("#newinputCheckbox1").is(':checked') == false) {
                    saveStepData(1);
                }
                showStep(2);
            }
        });

        document.getElementById('nextToStep3').addEventListener('click', function() {
            if (validateStep2()) {
                if ($("#newinputCheckbox2").is(':checked') == false) {
                    saveStepData(2);
                }
                showStep(3);
            }
        });

        // Back button handlers
        document.getElementById('backToStep1').addEventListener('click', function() {
            showStep(1);
        });

        document.getElementById('backToStep2').addEventListener('click', function() {
            showStep(2);
        });

        // Form submission
        document.getElementById('tourForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if ($("#newinputCheckbox3").is(':checked') == false) {
                saveStepData(3);
            } else {
                toastr.success('Tour Updated successfully! Redirecting...', 'Success', {
                    timeOut: 2000,
                    progressBar: true,
                    closeButton: true,
                    onHidden: function() {
                        window.location.href = `{{ route(\App\Enums\ViewPaths\Admin\TourVisitPath::TRAVELLIST[REDIRECT]) }}`;
                    }
                });
            }
        });

        // Step validation functions
        function validateStep1() {
            const form = document.getElementById('step1');
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalid = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            if (firstInvalid) {
                firstInvalid.focus();
            }
            return isValid;
        }

        function validateStep2() {
            const form = document.getElementById('step2');
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalid = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            if (firstInvalid) {
                firstInvalid.focus();
            }
            return isValid;
            return true;
        }

        function validateStep3() {
            const mainImage = document.getElementById('tour_image_update');
            if (!mainImage.files.length) {
                mainImage.classList.add('is-invalid');
                return false;
            }
            mainImage.classList.remove('is-invalid');
            return true;
        }

        // Save step data via AJAX
        function saveStepData(step) {
            const formData = new FormData();
            const stepFields = document.getElementById(`step${step}`).querySelectorAll('input, select, textarea');

            stepFields.forEach(field => {
                if (field.type === 'file') {
                    if (field.files.length > 0) {
                        for (let i = 0; i < field.files.length; i++) {
                            formData.append(field.name, field.files[i]);
                        }
                    }
                } else if (field.type === 'checkbox') {
                    formData.append(field.name, field.checked ? field.value : '');
                } else if (field.classList.contains('ckeditor')) {
                    const editor = CKEDITOR.instances[field.id];
                    if (editor) {
                        formData.append(field.name, editor.getData());
                    } else {
                        formData.append(field.name, field.value);
                    }
                } else {
                    formData.append(field.name, field.value);
                }
            });
            formData.append('step', step);
            formData.append('id', $(".tour_insert_id").val());
            formData.append('_token', "{{ csrf_token() }}");
            $("#loading").removeClass('d--none');
            fetch('{{ route("admin.tour_visits.edit") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(`Step ${step} data saved successfully`);
                        $(".tour_insert_id").val(data.tour_id);
                        toastr.success(data.message || `Step ${step} saved successfully!`);
                        if (step == 3) {
                            toastr.success('Tour Updated successfully! Redirecting...', 'Success', {
                                timeOut: 2000,
                                progressBar: true,
                                closeButton: true,
                                onHidden: function() {
                                    window.location.href = `{{ route(\App\Enums\ViewPaths\Admin\TourVisitPath::TRAVELLIST[REDIRECT]) }}`;
                                }
                            });
                        } else {
                            $("#loading").addClass('d--none');
                        }
                    } else {
                        $("#loading").addClass('d--none');
                        showStep(step);
                        console.error('Error saving step data:', data.message);
                        toastr.error(data.message || 'Error saving data. Please try again.');
                    }
                })
                .catch(error => {
                    $("#loading").addClass('d--none');
                    showStep(step);
                    console.error('Error:', error);
                    toastr.error('Network error occurred. Please check your connection and try again.');
                });
        }
    });
</script>
<script>
    function cab_ex_distance_model(id) {
        let inputValue = $(".cab-json-show" + id).val();
        let chargeData = [];

        // Parse JSON from input if available
        if (inputValue.trim() !== "") {
            try {
                chargeData = JSON.parse(inputValue);
            } catch (e) {
                console.error("Invalid JSON data", e);
            }
        } else {
            chargeData = [{
                start: 20,
                end: 30,
                charge: 0,
                driver: 0
            }];
        }

        populateDistanceChargeTable(id, chargeData, 'open');
        $(".exDistanceModal").attr("data-id", id);
        $(".exDistanceModal").modal("show");
    }

    function populateDistanceChargeTable(id, chargeData) {
        let tableBody = $("#distanceChargeTable");
        tableBody.empty();

        chargeData.forEach((item, index) => {
            let nextStart = chargeData[index + 1] ? chargeData[index + 1].start : '';

            tableBody.append(`
            <tr id="row-${id}-${index}">
                <td><input type="number" class="form-control" value="${item.start}" readonly></td>
                <td>
                    <input type="number" class="form-control end-value" value="${item.end}"  onkeyup="updateEndValue(${index}, ${id})" id="end-${id}-${index}">
                </td>
                <td>
                    <input type="number" class="form-control charge-value" value="${item.charge}" oninput="updateCharge(${index}, ${id})" id="charge-${id}-${index}">
                </td>
                <td>
                    <input type="number" class="form-control charge-value" value="${item.driver}" oninput="updateCharge(${index}, ${id})" id="driver-${id}-${index}">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" 
                        onclick="removeChargeRow(${index}, ${id})">X</button>
                </td>
            </tr>
        `);
        });

        $(".cab-json-show" + id).val(JSON.stringify(chargeData));
    }

    function addNewChargeRow() {
        let id = $(".exDistanceModal").attr("data-id");
        let inputValue = $(".cab-json-show" + id).val();
        let chargeData = inputValue ? JSON.parse(inputValue) : [];

        let lastEntry = chargeData.length > 0 ? chargeData[chargeData.length - 1] : {
            end: 30
        };
        let newStart = lastEntry.end + 1;
        let newEnd = newStart + 10;

        if (newEnd > 250) {
            alert("Maximum distance limit (250 km) reached.");
            return;
        }

        chargeData.push({
            start: newStart,
            end: newEnd,
            charge: 0,
            driver: 0
        });
        populateDistanceChargeTable(id, chargeData);
    }

    function removeChargeRow(index, id) {
        let inputValue = $(".cab-json-show" + id).val();
        let chargeData = inputValue ? JSON.parse(inputValue) : [];

        if (chargeData.length === 1) {
            alert("At least one row is required!");
            return;
        }

        chargeData.splice(index, 1);
        populateDistanceChargeTable(id, chargeData);
    }

    function updateCharge(index, id) {
        let inputValue = $(".cab-json-show" + id).val();
        let chargeData = inputValue ? JSON.parse(inputValue) : [];

        if (!chargeData[index]) {
            console.error("Error: Trying to update non-existent index:", index);
            return;
        }

        let chargeValue = document.getElementById(`charge-${id}-${index}`).value;
        let driverValue = document.getElementById(`driver-${id}-${index}`).value;

        chargeData[index].charge = parseFloat(chargeValue) || 0;
        chargeData[index].driver = parseFloat(driverValue) || 0;

        $(".cab-json-show" + id).val(JSON.stringify(chargeData));
    }

    function updateEndValue(index, id) {
        let inputValue = $(".cab-json-show" + id).val();
        let chargeData = inputValue ? JSON.parse(inputValue) : [];

        if (!chargeData[index]) {
            console.error("Error: Trying to update non-existent index:", index);
            return;
        }

        let newEndValue = document.getElementById(`end-${id}-${index}`).value.trim();

        if (newEndValue === "") {
            for (let i = index; i < chargeData.length; i++) {
                chargeData[i].start = i === index ? chargeData[i].start : null;
                chargeData[i].end = null;
            }
            populateDistanceChargeTable(id, chargeData);
            return;
        }

        newEndValue = parseInt(newEndValue);

        if (isNaN(newEndValue) || newEndValue > 250) {
            alert("Invalid value! End value must be a number and cannot exceed 250 km.");
            document.getElementById(`end-${id}-${index}`).value = chargeData[index].end;
            return;
        }

        chargeData[index].end = newEndValue;

        for (let i = index + 1; i < chargeData.length; i++) {
            if (chargeData[i - 1].end !== null) {
                chargeData[i].start = chargeData[i - 1].end + 1;
            } else {
                chargeData[i].start = null;
                chargeData[i].end = null;
            }
        }
        populateDistanceChargeTable(id, chargeData);
    }
</script>

<script>
    // time picker
    $('#opentime').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });
    $('#closetime').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });

    initSample();
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.ckeditor').ckeditor();
    });
</script>
<script type="text/javascript">
    $('.onfillup').on('input', function() {
        let val = $(this).val();
        let point = $(this).data('point');
        $(`.onfillup[data-point="${point}"]`).val(val);
    });


    $(document).ready(function() {
        $('.select2-multiple').select2({
            placeholder: "Select Package",
            allowClear: true
        });
    });
    let pointCounter = 1;

    function add_new_cab_html() {
        var totalRows = parseInt(document.getElementById('total_rows_cab').value) + 1;
        document.getElementById('total_rows_cab').value = totalRows;

        var newRow = `
        <div class="row mt-2">
            <div class='col-6 p-0 pr-1'>
                <select class="form-control point_trigger1${totalRows}" name="cab_id[${totalRows}]" onchange="select_value(this)" data-point='point_trigger1${totalRows}'>
                    <option value="" selected disabled>{{ translate('Select_cab') }}</option>
                    @foreach($cab_list as $cabs)
                    <option value="{{ $cabs['id'] }}">{{ $cabs['name'] }} ({{ $cabs['seats'] }} seat)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-5 p-0 pr-1">
                <input type='text' class="form-control   point_trigger4${totalRows}" name="price[${totalRows}]" value="" onkeyup="select_value(this);this.value = this.value.replace(/[^0-9]/g, '' )" data-point='point_trigger4${totalRows}' placeholder="{{ translate('enter_Price') }}">
            </div>
            <div class="col-1 p-0">
                <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>`;
        let classadd = 'd-none';
        if ("{{ old('use_date',$getData['use_date']) }}" == 3 || $('select[name="use_date"]').val() == 3) {
            classadd = ''
        }
        newRow += `  <a class='btn btn--primary btn-sm p-1 mt-2 cab-ex-distance-charge${totalRows} ${classadd} specialTourwithoutdate' onclick="cab_ex_distance_model('${totalRows}')"><i class="tio-bonnet_open"> bonnet_open </i></a>
                <input type="hidden" class="from-control cab-json-show${totalRows}" name="excharge[${totalRows}]">
            </div>
        </div>
    `;
        $('.add_cab_append_multi').append(newRow);
    }

    function add_new_package_html() {
        var totalRows = parseInt(document.getElementById('total_rows_package').value) + 1;
        document.getElementById('total_rows_package').value = totalRows;
        var newRow = `
        <div class="row mt-2">
           
            <div class='col-4 p-0 pr-1'>
                <select class="form-control point_trigger21${totalRows}" name="package_id[${totalRows}]" onchange="select_value(this)" data-point='point_trigger21${totalRows}'>
                <option value="">Select Packages</option>
                    @if($package_list)
                    @foreach($package_list as $packval)
                    <option value="{{ $packval['id'] }}">{{ $packval['name'] }} -({{ $packval['seats'] }} people)</option>
                    @endforeach
                    @endif
                </select>
            </div>
           <div class="col-1 p-0 pr-1">
                        <input type='text' class="p-0 pl-1 form-control pointNumber_trigger49${totalRows}" value="1" name="pnumber[${totalRows}]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.point_trigger49${totalRows}').val($('.pointamount_trigger48${totalRows}').val() * $('.pointNumber_trigger49${totalRows}').val())" data-point='pointNumber_trigger49${totalRows}' placeholder="{{ translate('number_of_day_and_stay') }}">
                    </div>
                    <div class="col-2 p-0 pr-1">
                        <input type='text' class="form-control pointamount_trigger49${totalRows}" value="0" name="pperson[${totalRows}]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.point_trigger49${totalRows}').val(this.value * $('.pointNumber_trigger49${totalRows}').val())" data-point='pointamount_trigger49${totalRows}' placeholder="{{ translate('enter_per_days') }}">
                    </div>
                    <div class="col-4 p-0 pr-1">
                <input type='text' class="form-control   point_trigger49${totalRows}" readonly name="pprice[${totalRows}]" value="" onkeyup="select_value(this);this.value = this.value.replace(/[^0-9]/g, '' )" data-point='point_trigger49${totalRows}' placeholder="{{ translate('enter_Price') }}">
            </div>
            <div class="col-1 p-0">
                <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
            </div>
        </div>
    `;
        $('.add_package_append_multi').append(newRow);
        removeSelectedOptions();
    }

    function food_package_perperson() {
        var totalRows = parseInt(document.getElementById('total_rows_package').value) + 1;
        document.getElementById('total_rows_package').value = totalRows;
        var newRow = `
        <div class="row mt-2 food-row">           
            <div class='col-4 p-0 pr-1'>
                <select class="form-control food_options1${totalRows}" name="food_package_id[${totalRows}]" onchange="select_value(this);removeSelected_Options()" data-point='food_options1${totalRows}'>
                <option value="">Select Packages</option>
                @if($package_list)
                @foreach($package_list as $packVals)
                @if($packVals['type'] == 'foods')
                    <option value="{{ $packVals['id'] }}" data-type="{{ $packVals['type'] }}">{{ $packVals['name'] }} - ({{ $packVals['seats']}} people)</option>
                    @endif
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-1 p-0 pr-1">
                                <input type='text' class="p-0 pl-1 form-control food_optionp22${totalRows} change-price-key"  name="food_pnumber[${totalRows}]" value="1" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.food_optionshow${totalRows}').val($('.food_option_days1${totalRows}').val() * $('.food_optionp22${totalRows}').val())" data-point='food_optionp22${totalRows}' placeholder="{{ translate('number_of_day_and_stay') }}">
                            </div>                            
                            <div class="col-2 p-0 pr-1">
                                <input type='text' class="form-control food_option_days1${totalRows} change-price-key" name="food_pperson[${totalRows}]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.food_optionshow${totalRows}').val(this.value * $('.food_optionp22${totalRows}').val())" data-point='food_option_days1${totalRows}' placeholder="{{ translate('enter_per_days') }}">
                            </div>
            <div class="col-3 p-0 pr-1">
                <input type='text' class="form-control food_optionshow${totalRows} row-total" readonly name="food_pprice[${totalRows}]" value="" onkeyup="select_value(this);this.value = this.value.replace(/[^0-9]/g, '' )" data-point='food_optionshow${totalRows}' placeholder="{{ translate('enter_Price') }}">
            </div>
             <div class="col-1 p-0 pr-1">
                                <input type='checkbox' class="foods_pack_checkedoption${totalRows} mt-3 include-item" name="food_check[${totalRows}]" value="1" onclick="" data-point='foods_pack_checkedoption${totalRows}'>
                            </div>
            <div class="col-1 p-0">
                <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
            </div>
        </div>
        `;
        $('.per_person_packageInclucde').append(newRow);
        removeSelected_Options();
    }

    function hotel_package_perperson() {
        var totalRows = parseInt(document.getElementById('total_rows_package').value) + 1;
        document.getElementById('total_rows_package').value = totalRows;
        var newRow = `
        <div class="row mt-2 hotel-row">           
            <div class='col-4 p-0 pr-1'>
                <select class="form-control hotals_options1${totalRows}" name="hotal_package_id[${totalRows}]" onchange="select_value(this);removeSelected_Options()" data-point='hotals_options1${totalRows}'>
                <option value="">Select Packages</option>
                @if($package_list)
                @foreach($package_list as $packVals)
                @if($packVals['type'] == 'hotel')
                    <option value="{{ $packVals['id'] }}" data-type="{{ $packVals['type'] }}">{{ $packVals['name'] }} - ({{ $packVals['seats']}} people)</option>
                    @endif
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-1 p-0 pr-1">
                                <input type='text' class="p-0 pl-1 form-control hotals_optionp22${totalRows} change-price-key"  name="hotal_pnumber[${totalRows}]" value="1" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.hotals_optionshow${totalRows}').val($('.hotals_option_days1${totalRows}').val() * $('.hotals_optionp22${totalRows}').val())" data-point='hotals_optionp22${totalRows}' placeholder="{{ translate('number_of_day_and_stay') }}">
                            </div>                            
                            <div class="col-2 p-0 pr-1">
                                <input type='text' class="form-control hotals_option_days1${totalRows} change-price-key" name="hotal_pperson[${totalRows}]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );$('.hotals_optionshow${totalRows}').val(this.value * $('.hotals_optionp22${totalRows}').val())" data-point='hotals_option_days1${totalRows}' placeholder="{{ translate('enter_per_days') }}">
                            </div>
            <div class="col-3 p-0 pr-1">
                <input type='text' class="form-control hotals_optionshow${totalRows} row-total" readonly name="hotal_pprice[${totalRows}]" value="" onkeyup="select_value(this);this.value = this.value.replace(/[^0-9]/g, '' )" data-point='hotals_optionshow${totalRows}' placeholder="{{ translate('enter_Price') }}">
            </div>
            <div class="col-1 p-0 pr-1">
                                <input type='checkbox' class="hotals_pack_optiontrigger${totalRows} mt-3 include-item" name="hotal_check[${totalRows}]" value="1" onclick="" data-point='hotals_pack_optiontrigger${totalRows}'>
                            </div>
            <div class="col-1 p-0">
                <a class='btn btn-danger btn-sm p-1 mt-2' onclick="remove_html(this)"><i class='tio-remove'></i></a>
            </div>
        </div>
        `;
        $('.per_person_packageInclucdeHotel').append(newRow);
        removeSelected_Options();
    }


    function remove_html(that) {
        $(that).closest('.row').remove();
    }

    function select_value(that) {
        var point = $(that).data('point');
        $(`.${point}`).val($(`.${point}`).val());
    }
    initializeDateRangePicker(false)

    function initializeDateRangePicker(isSingleDate) {
        var initialDateRange = "{{ old('startandend_date',($getData['startandend_date']??''))}}";
        var today = moment().startOf('day');
        var startDate, endDate;
        if (initialDateRange) {
            var dates = initialDateRange.split(' - ');
            startDate = moment(dates[0], 'YYYY-MM-DD');
            endDate = moment(dates[1], 'YYYY-MM-DD');
        } else {
            startDate = moment().startOf('day');
            endDate = moment().endOf('day');
        }
        $('.start_date_end_date').daterangepicker({
            singleDatePicker: isSingleDate,
            startDate: startDate,
            endDate: endDate,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(start, end) {
            $('.datePickers').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: today,
                // minDate: start.format('YYYY-MM-DD'),
                maxDate: end.format('YYYY-MM-DD')
            });
        });
        if (initialDateRange && initialDateRange.includes(' - ')) {
            $('.datePickers').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                minDate: today,
                // minDate: startDate.format('YYYY-MM-DD'),
                maxDate: endDate.format('YYYY-MM-DD')
            });
        }
    }

    $('.pickup_times').timepicker({
        uiLibrary: 'bootstrap4',
        format: 'hh:MM TT', // Correct format for time display (12-hour with AM/PM)
        modal: true,
        footer: true
    });

    $(".getAddress_google").each(function() {
        let inputElement = this;
        let autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['establishment'],
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                $(inputElement).val('');
                return;
            }
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();

            let addressComponents = place.address_components;
            let city = '';
            let state = '';
            let country = '';
            let partOfCity = '';
            let neighborhood = '';
            console.log(addressComponents);
            addressComponents.forEach(component => {
                const types = component.types;
                if (types.includes('locality')) {
                    city = component.long_name;
                }
                if (types.includes('administrative_area_level_1')) {
                    state = component.long_name;
                }
                if (types.includes('country')) {
                    country = component.long_name;
                }
                if (types.includes('sublocality_level_1')) {
                    partOfCity = component.long_name; // Sub-locality or area within the city
                }
                if (types.includes('neighborhood')) {
                    neighborhood = component.long_name; // Neighborhood name, if available
                }
            });
            $("#en_state_name").val(state);
            $("#en_country_name").val(country);
            $("#en_cities_name").val(city);
            $(".lat_location").val(lat);
            $(".long_location").val(lng);
            var points = $(inputElement).data('point');
            getHindiAddress(lat, lng, points, inputElement);
        });
    });

    function getHindiAddress(lat, lng, points, inputElement) {
        const apiKey = '{{$googleMapsApiKey}}';
        const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&language=hi&key=${apiKey}`;

        $.getJSON(geocodeUrl, function(data) {
            if (data.status === 'OK' && data.results.length > 0) {
                let fullAddress = '';
                let city = '';
                let state = '';
                let country = '';
                let streetNumber = '';
                let streetName = '';
                console.log(data.results);

                data.results[0].address_components.forEach(function(component) {
                    const componentType = component.types[0];
                    switch (componentType) {
                        case 'street_number':
                            streetNumber = component.long_name; // Extract street number
                            break;
                        case 'route':
                            streetName = component.long_name; // Extract street name
                            break;
                        case 'locality':
                        case 'sublocality':
                            city = component.long_name; // Extract city name
                            break;
                        case 'administrative_area_level_1':
                            state = component.long_name; // Extract state name
                            break;
                        case 'country':
                            country = component.long_name; // Extract country name
                            break;
                    }
                });

                // Construct the full address in Hindi
                fullAddress = [streetNumber, streetName, city, state, country].filter(Boolean).join(', ');


                $("#in_state_name").val(state);
                $("#in_country_name").val(country);
                $("#in_cities_name").val(city);
            } else {
                console.error('Geocoding API error:', data.status);
            }
        });
    }

    $(".pickup_location_get").each(function() {
        let inputElement = this;
        let autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['establishment'],
        });
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                $(inputElement).val('');
                return;
            }
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();

            $(".pick_up_lat_location").val(lat);
            $(".pick_up_long_location").val(lng);
        });
    });
</script>

<script>
    $('.times_slot_pick').each(function() {
        $(this).timepicker({
            uiLibrary: 'bootstrap4',
            format: 'hh:MM TT',
            modal: true,
            footer: true
        });
    });

    function time_slot_add() {
        let html = `
        <div class="row time_slot_add_html mt-2">
            <div class="col-md-3">
                <input type="text" name="time_slot[]" readonly class="times_slot_pick form-control">
                </div>
                <div class="col-md-3">
                <a class="btn btn-danger btn-sm p-1 mt-2" onclick="time_slot_remove(this)"><i class="tio-remove"></i></a>
            </div>
        </div>
    `;
        document.getElementById('time_slot_container').insertAdjacentHTML('beforeend', html);

        $('.times_slot_pick').last().timepicker({
            uiLibrary: 'bootstrap4',
            format: 'hh:MM TT',
            modal: true,
            footer: true
        });
    }

    function time_slot_remove(element) {
        // Remove the parent row when the remove button is clicked
        element.closest('.time_slot_add_html').remove();
    }

    function use_date_functions(that) {
        if (that.value == 0) {
            $('.persons_transport_divShow').children().hide();
        } else {
            $('.persons_transport_divShow').children().show();
        }

        if (that.value == 0) {
            $('.use_interested_and_not').addClass('d-none');
        } else if (that.value == 2 || that.value == 4) {
            $('.daily_tour_full_comman').addClass('d-none');
            $('.daily_tour_full_address').removeClass('d-none');
        } else if (that.value == 3) {
            $('.use_interested_and_not').addClass('d-none');
        } else {
            $('.use_interested_and_not').removeClass('d-none');
        }

        if (that.value == 3) {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-full-width",
                "timeOut": "10000",
            };
            toastr.warning("Please must Add The Extra Charges- Toll Tax, Driver charges etc", "WARNING");
            $('.specialTourwithoutdate').removeClass('d-none');
        } else {
            $('.specialTourwithoutdate').addClass('d-none');
        }

    }



    function removeSelectedOptions() {
        let selectedValues = [];

        // Collect all selected values
        document.querySelectorAll("select[name^='package_id']").forEach(select => {
            if (select.value) {
                selectedValues.push(select.value);
            }
        });

        document.querySelectorAll("select[name^='package_id']").forEach(select => {
            let currentValue = select.value;

            select.querySelectorAll("option").forEach(option => {
                if (option.value && selectedValues.includes(option.value) && option.value !== currentValue) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            });
        });
    }

    removeSelected_Options();

    // function removeSelected_Options() {
    //     let selectedValues = [];
    //     document.querySelectorAll("select[name^='food_package_id']").forEach(select => {
    //         if (select.value) {
    //             selectedValues.push(select.value);
    //         }
    //     });
    //     document.querySelectorAll("select[name^='food_package_id']").forEach(select => {
    //         let currentValue = select.value;
    //         select.querySelectorAll("option").forEach(option => {
    //             if (option.value && selectedValues.includes(option.value) && option.value !== currentValue) {
    //                 option.disabled = true;
    //             } else {
    //                 option.disabled = false;
    //             }
    //         });
    //     });
    //     let selectedValues_array = [];
    //     document.querySelectorAll("select[name^='hotal_package_id']").forEach(select => {
    //         if (select.value) {
    //             selectedValues_array.push(select.value);
    //         }
    //     });
    //     document.querySelectorAll("select[name^='hotal_package_id']").forEach(select => {
    //         let currentValue = select.value;
    //         select.querySelectorAll("option").forEach(option => {
    //             if (option.value && selectedValues_array.includes(option.value) && option.value !== currentValue) {
    //                 option.disabled = true;
    //             } else {
    //                 option.disabled = false;
    //             }
    //         });
    //     });
    // }
    function processGroup(prefix, rowClass) {
        const selects = Array.from(document.querySelectorAll(`select[name^='${prefix}']`));
        if (!selects.length) return;

        const taken = [];
        selects.forEach(sel => {
            const row = sel.closest(`.${rowClass}`);
            const included = !!row?.querySelector('.include-item')?.checked;
            if (!included && sel.value) {
                taken.push(sel.value);
            }
        });


        selects.forEach(sel => {
            const row = sel.closest(`.${rowClass}`);
            const included = !!row?.querySelector('.include-item')?.checked;
            const current = sel.value;

            sel.querySelectorAll('option').forEach(opt => {
                if (!opt.value) {
                    opt.disabled = false;
                    return;
                }

                if (included) {
                    opt.disabled = false;
                } else {
                    opt.disabled = (taken.includes(opt.value) && opt.value !== current);
                }
            });

            // Always keep current option enabled
            if (current) {
                const curOpt = sel.querySelector(`option[value="${CSS.escape(current)}"]`);
                if (curOpt) curOpt.disabled = false;
            }
        });
    }

    function removeSelected_Options() {
        processGroup('food_package_id', 'food-row');
        processGroup('hotel_package_id', 'hotel-row');
        processGroup('hotal_package_id', 'hotel-row');
    }

    document.addEventListener('DOMContentLoaded', removeSelected_Options);
    document.addEventListener('change', function(e) {
        if (e.target.matches("select[name^='food_package_id'], select[name^='hotel_package_id'], select[name^='hotal_package_id'], .include-item")) {
            removeSelected_Options();
        }
    });
</script>
<script>
    function add_new_persons_html() {
        let min = parseInt($("input[name='min_person[]']").last().val()) || 0;
        let max = parseInt($("input[name='max_person[]']").last().val()) || 0;

        if (min > max) {
            alert("Minimum person cannot be greater than maximum person.");
            return;
        }

        let html = `
    <div class="row mt-2 group-row">
        <div class='col-3'>
            <input type='text' class="form-control" name="min_person[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );validatePersonPrice(this)" placeholder="Enter Min">
        </div>
        <div class="col-4">
            <input type='text' class="form-control" name="max_person[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );validatePersonPrice(this)" placeholder="Enter Max">
        </div>
        <div class="col-2 p-0">
        <input type='text' class="form-control base-price" name="package_price[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('basic_Price') }}">
        </div>
        <div class="col-2 p-0">
            <input type='text' class="form-control included-price" name="person_price[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="Enter Price">
        </div>
        <div class="col-1 p-0">
            <button type="button" class="btn btn-danger btn-sm p-1 mt-2 remove-row"><i class='tio-remove'></i></button>
        </div>
    </div>`;
        $('.add_persons_append_multi').append(html);
    }
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.row').remove();
    });

    $(document).on('change', "input[name='min_person[]']", function() {
        let index = $("input[name='min_person[]']").index(this);

        if (index > 0) {
            let prevMax = parseInt($("input[name='max_person[]']").eq(index - 1).val()) || 0;
            let currentMin = parseInt($(this).val()) || 0;

            if (currentMin <= prevMax) {
                toastr.error("Min people must be greater than previous row’s Max.");
                $(this).val(prevMax + 1);
            }
        }
    });

    function validateInputValue(input) {
        let val = input.value;
        val = val.replace(/[^0-9.]/g, '');
        val = val.replace(/(\..*)\./g, '$1');
        if (val.includes('.')) {
            setTimeout(() => {
                if ($('.number_of_day_number').val().replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').includes('.')) {
                    $('.number_of_day_number').val('0.5');
                }
            }, 300);
        } else {
            val = val.replace(/^0+(?!$)/, '');
        }
        input.value = val;
    }


    function add_new_person_transport_html() {
        var html = `
        <div class="row mt-2">
                            <div class='col-2 px-1'>
                                <input type='text' class="form-control px-2" name="start_person[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );validateStartEndPersonsWithLastEnd(this)" placeholder="{{ translate('number') }}">
                            </div>
                            <div class="col-2 px-1">
                                <input type='text' class="form-control px-2" name="end_person[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' );validateStartEndPersonsWithLastEnd(this)" placeholder="{{ translate('number') }}" >
                            </div>
                            <div class='col-2 px-1'>
                                <input type='text' class="form-control px-2" name="person_pick[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('pick') }}">
                            </div>
                            <div class="col-2 px-1">
                                <input type='text' class="form-control px-2" name="person_drop[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('drop') }}">
                            </div>
                            <div class="col-2 px-1">
                                <input type='text' class="form-control px-2" name="person_both[]" onkeyup="this.value = this.value.replace(/[^0-9]/g, '' )" placeholder="{{ translate('both') }}">
                            </div>
                            <div class="col-2 px-1">
                                <button type="button" class="btn btn-danger btn-sm p-1 mt-2 remove-row"><i class='tio-remove'></i></button>
                            </div>
                        </div>`;

        $('.add_persons_transport_append_multi').append(html);

    }

    function validateStartEndPersonsWithLastEnd(currentInput) {
        const row = currentInput.closest('.row');
        if (!row) {
            console.warn('No .row parent found.');
            return false;
        }
        const startInput = row.querySelector('input[name="start_person[]"]');
        const endInput = row.querySelector('input[name="end_person[]"]');
        if (!startInput || !endInput) {
            console.warn('Start or End input not found in this row.');
            return false;
        }
        const startVal = parseInt(startInput.value, 10);
        const endVal = parseInt(endInput.value, 10);
        startInput.classList.remove('is-invalid');
        endInput.classList.remove('is-invalid');
        let valid = true;
        if (isNaN(startVal) || isNaN(endVal)) {
            startInput.classList.add('is-invalid');
            endInput.classList.add('is-invalid');
            valid = false;
        } else if (startVal > endVal) {
            startInput.classList.add('is-invalid');
            endInput.classList.add('is-invalid');
            valid = false;
        }

        return valid;
    }

    function validatePersonPrice(currentInput) {
        const row = currentInput.closest('.row');
        if (!row) {
            console.warn('No .row parent found.');
            return false;
        }
        const startInput = row.querySelector('input[name="min_person[]"]');
        const endInput = row.querySelector('input[name="max_person[]"]');
        if (!startInput || !endInput) {
            console.warn('Start or End input not found in this row.');
            return false;
        }
        const startVal = parseInt(startInput.value, 10);
        const endVal = parseInt(endInput.value, 10);
        startInput.classList.remove('is-invalid');
        endInput.classList.remove('is-invalid');
        let valid = true;
        if (isNaN(startVal) || isNaN(endVal)) {
            startInput.classList.add('is-invalid');
            endInput.classList.add('is-invalid');
            valid = false;
        } else if (startVal > endVal) {
            startInput.classList.add('is-invalid');
            endInput.classList.add('is-invalid');
            valid = false;
        }

        return valid;
    }
</script>
<script>
    (function() {
        function num(v) {
            v = String(v || '').replace(/[^\d.]/g, '');
            return parseFloat(v) || 0;
        }

        function recalc() {
            let extras = 0;
            $('.food-row, .hotel-row').each(function() {
                let $r = $(this);
                let rowTotal = num($r.find('.row-total').val());
                if ($r.find('.include-item').is(':checked')) {
                    extras += rowTotal;
                }
            });

            // 2) अब ऊपर वाले base per head rows में included price add करो
            $('.group-row').each(function() {
                let $g = $(this);
                let base = num($g.find('.base-price').val());
                let final = base + extras;
                $g.find('.included-price').val(final || '');
            });
        }

        // Trigger calculation on change/keyup
        $(document).on('keyup change',
            '.change-price-key,.row-total,.include-item,.base-price', recalc);
        $(recalc);
    })();
</script>
<script>
    $('#newinputCheckbox1').click();
    $('#newinputCheckbox2').click();
    $('#newinputCheckbox3').click();

    function stepinput(stepId, checkbox) {
        const elements = document.querySelectorAll(`#${stepId} input, #${stepId} textarea, #${stepId} select`);

        elements.forEach(el => {
            if (el === checkbox) return;

            const isDisabled = checkbox.checked;
            el.disabled = isDisabled;
            el.readOnly = isDisabled;

            // Optional visual style
            if (isDisabled) el.classList.add('disabled-field');
            else el.classList.remove('disabled-field');

            // 🧠 CKEditor check
            if (el.classList.contains('ckeditor') || el.id in CKEDITOR.instances) {
                const editor = CKEDITOR.instances[el.id];
                if (editor) {
                    if (isDisabled) {
                        editor.setReadOnly(true);
                    } else {
                        editor.setReadOnly(false);
                    }
                }
            }
        });
    }

    document.getElementById('itineraryPdf').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('pdfPreviewContainer');
        const pdfFrame = document.getElementById('pdfPreview');

        if (file && file.type === 'application/pdf') {
            const fileURL = URL.createObjectURL(file);
            pdfFrame.src = fileURL;
            previewContainer.style.display = 'block';
        } else {
            pdfFrame.src = '';
            previewContainer.style.display = 'none';
            alert('Please upload a valid PDF file.');
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('customized_type');
        const weeklyControls = document.getElementById('weekly-controls');
        const monthlyControls = document.getElementById('monthly-controls');
        const yearlyControls = document.getElementById('yearly-controls');

        // Variables to track selections
        let selectedDays = [];
        let selectedDates = [];
        let multiSelectedDates = [];

        // Get pre-filled data from hidden inputs
        const weeklyData = JSON.parse($('.customized_weekly').val() || '[]');
        const monthlyData = JSON.parse($('.customized_monthly').val() || '[]');
        const yearlyData = JSON.parse($('.customized_yearly').val() || '[]');
        const customizedType = "{{ old('customized_type', $getData['customized_type'] ?? 0) }}";

        // Initialize date pickers
        const monthSelector = flatpickr("#month-selector", {
            mode: "single",
            dateFormat: "F Y",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    generateDatePicker(selectedDates[0]);
                }
            }
        });

        const multiDatePicker = flatpickr("#multi-date-picker", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            defaultDate: yearlyData, // Pre-fill yearly dates
            onChange: function(selectedDates, dateStr, instance) {
                multiSelectedDates = selectedDates;
                updateMultiDatesDisplay();
            }
        });

        // Initialize with pre-filled data
        function initializeWithData() {
            // Set the dropdown value
            typeSelect.value = customizedType;

            // Pre-fill weekly data
            if (weeklyData.length > 0) {
                const dayMapping = {
                    'Sunday': '0',
                    'Monday': '1',
                    'Tuesday': '2',
                    'Wednesday': '3',
                    'Thursday': '4',
                    'Friday': '5',
                    'Saturday': '6'
                };

                weeklyData.forEach(dayName => {
                    const dayCode = dayMapping[dayName];
                    if (dayCode !== undefined) {
                        selectedDays.push(dayCode);
                        // Highlight the button
                        const button = document.querySelector(`.weekday-btn[data-day="${dayCode}"]`);
                        if (button) {
                            button.classList.add('selected-day');
                        }
                    }
                });
                updateSelectedDaysDisplay();
            }

            // Pre-fill monthly data
            if (monthlyData.length > 0) {
                selectedDates = monthlyData;
                updateSelectedDatesDisplay();

                // If we have monthly data, set the month selector to the first date
                if (monthlyData[0]) {
                    const firstDate = new Date(monthlyData[0]);
                    monthSelector.setDate(firstDate, true);
                    // Generate the date picker grid
                    setTimeout(() => generateDatePicker(firstDate), 100);
                }
            }

            // Pre-fill yearly data is handled by flatpickr defaultDate
            if (yearlyData.length > 0) {
                multiSelectedDates = yearlyData.map(dateStr => new Date(dateStr));
                updateMultiDatesDisplay();
            }

            // Show the appropriate controls based on type
            triggerTypeChange();
        }

        // Trigger the type change to show correct controls
        function triggerTypeChange() {
            // Hide all controls first
            weeklyControls.style.display = 'none';
            monthlyControls.style.display = 'none';
            yearlyControls.style.display = 'none';

            // Show the selected control
            switch (customizedType) {
                case '1':
                    weeklyControls.style.display = 'block';
                    break;
                case '2':
                    monthlyControls.style.display = 'block';
                    break;
                case '3':
                    yearlyControls.style.display = 'block';
                    break;
            }
        }

        // Handle selection change
        typeSelect.addEventListener('change', function() {
            // Hide all controls first
            weeklyControls.style.display = 'none';
            monthlyControls.style.display = 'none';
            yearlyControls.style.display = 'none';

            // Show the selected control
            switch (this.value) {
                case '1':
                    weeklyControls.style.display = 'block';
                    break;
                case '2':
                    monthlyControls.style.display = 'block';
                    break;
                case '3':
                    yearlyControls.style.display = 'block';
                    break;
            }
        });

        // Weekly selection - day buttons
        document.querySelectorAll('.weekday-btn').forEach(button => {
            button.addEventListener('click', function() {
                const day = this.getAttribute('data-day');

                if (this.classList.contains('selected-day')) {
                    // Remove from selection
                    this.classList.remove('selected-day');
                    selectedDays = selectedDays.filter(d => d !== day);
                } else {
                    // Add to selection
                    this.classList.add('selected-day');
                    selectedDays.push(day);
                }

                updateSelectedDaysDisplay();
            });
        });

        // Update display for selected days
        function updateSelectedDaysDisplay() {
            const container = document.getElementById('selected-days-container');
            container.innerHTML = '';

            if (selectedDays.length === 0) {
                container.innerHTML = '<span class="text-muted">No days selected</span>';
                $('.customized_weekly').val('');
                return;
            }

            const dayNames = {
                '0': 'Sunday',
                '1': 'Monday',
                '2': 'Tuesday',
                '3': 'Wednesday',
                '4': 'Thursday',
                '5': 'Friday',
                '6': 'Saturday'
            };
            let CreateArray = [];
            selectedDays.forEach(day => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary date-badge text-white';
                badge.textContent = dayNames[day];
                container.appendChild(badge);
                CreateArray.push(dayNames[day]);
            });
            $('.customized_weekly').val(JSON.stringify(CreateArray));
        }

        // Generate date picker for a specific month
        function generateDatePicker(date) {
            const container = document.getElementById('date-picker-container');
            container.innerHTML = '';

            const year = date.getFullYear();
            const month = date.getMonth();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Create a grid of days
            const grid = document.createElement('div');
            grid.className = 'd-flex flex-wrap';

            for (let day = 1; day <= daysInMonth; day++) {
                const dayButton = document.createElement('button');
                dayButton.type = 'button';
                dayButton.className = 'btn btn-outline-secondary m-1 px-1';
                dayButton.style.width = '40px';
                dayButton.textContent = day;

                // Format date string consistently
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                // Check if this date is already selected
                if (selectedDates.includes(dateStr)) {
                    dayButton.classList.add('btn-primary');
                    dayButton.classList.add('text-white');
                }

                dayButton.addEventListener('click', function() {
                    if (this.classList.contains('btn-primary')) {
                        // Remove from selection
                        this.classList.remove('btn-primary');
                        this.classList.remove('text-white');
                        this.classList.add('btn-outline-secondary');
                        selectedDates = selectedDates.filter(d => d !== dateStr);
                    } else {
                        // Add to selection
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('btn-primary');
                        this.classList.add('text-white');
                        selectedDates.push(dateStr);
                    }

                    updateSelectedDatesDisplay();
                });

                grid.appendChild(dayButton);
            }

            container.appendChild(grid);
        }

        // Update display for selected dates
        function updateSelectedDatesDisplay() {
            const container = document.getElementById('selected-dates-container');
            container.innerHTML = '';

            if (selectedDates.length === 0) {
                container.innerHTML = '<span class="text-muted">No dates selected</span>';
                $('.customized_monthly').val('');
                return;
            }
            let CreateArray = [];
            selectedDates.forEach(dateStr => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary date-badge text-white';
                badge.textContent = formatDate(dateStr);
                container.appendChild(badge);
                CreateArray.push(dateStr);
            });
            $('.customized_monthly').val(JSON.stringify(CreateArray));
        }

        // Update display for multi selected dates
        function updateMultiDatesDisplay() {
            const container = document.getElementById('multi-dates-container');
            container.innerHTML = '';

            if (multiSelectedDates.length === 0) {
                container.innerHTML = '<span class="text-muted">No dates selected</span>';
                $('.customized_yearly').val('');
                return;
            }
            let CreateArray = [];
            multiSelectedDates.forEach(date => {
                let dateStr1 = new Date(date);
                let badge1 = document.createElement('span');
                badge1.className = 'badge bg-primary date-badge text-white';
                badge1.textContent = formatDate(dateStr1);
                container.appendChild(badge1);
                CreateArray.push(dateStr1.toLocaleDateString('sv-SE', {
                    timeZone: 'Asia/Kolkata',
                    year: 'numeric',
                    month: 'numeric',
                    day: 'numeric'
                }));
            });
            $('.customized_yearly').val(JSON.stringify(CreateArray));
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-IN', {
                timeZone: 'Asia/Kolkata',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Initialize with pre-filled data
        initializeWithData();
    });
</script>
@endpush