@extends('layouts.back-end.app')

@section('title', translate('add_cities'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>

@endpush
@section('content')

<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/videosubcategory.png') }}" alt="">
            {{ translate('Add_Citie') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.cities.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- Language tabs -->
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($language as $lang)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}" id="{{$lang}}-link">
                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="col-12">
                            <input type="hidden" name='latitude'>
                            <input type="hidden" name="longitude">

                            @foreach($language as $key=>$lang)
                            <div class="row {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                <div class='col-md-6 form-group'>
                                    <label class="title-color" for="name">{{ translate('cities_name') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="city[]" class="form-control getAddress_google" id="{{$lang}}_cities" autocomplete="off" placeholder="{{ translate('cities_name') }}" required="{{ $lang == $defaultLanguage? 'required':''}}" data-option='{{$key}}'>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label class="title-color" for="name">{{ translate('Country_name') }}<span class="text-danger">*</span></label>
                                    <select name="country_id" class="js-select2-custom form-control country_select_geolocation">
                                        <option value="" disabled selected>Select Country</option>
                                        @foreach($country_list as $con)
                                        <option value="{{$con->id}}">{{ $con->name}}</option>
                                        @endforeach
                                    </select>
                                    <input type='hidden' class='country_id_short_name' name='country_id_short_name'>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label class="title-color" for="name">{{ translate('state_name') }}<span class="text-danger">*</span></label>
                                    <select name="state_id" class="js-select2-custom form-control state_select_geolocation">
                                        <option value="" disabled selected>Select State</option>
                                        @foreach($state_list as $va)
                                        <option value="{{$va->id}}">{{ $va->name}}</option>
                                        @endforeach
                                    </select>
                                    
                                </div>
                                <div class="col-md-6 form-group">

                                    <label class="title-color" for="name">{{ translate('short_description') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="short_desc[]" class="form-control" id="{{$lang}}_short_desc" placeholder="{{ translate('short_description') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}">
                                    <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                </div>
                                <div class="col-md-6 form-group">

                                    <label class="title-color" for="name">{{ translate('Famous_For') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="famous_for[]" class="form-control" id="{{$lang}}_famous_for" placeholder="{{ translate('Famous For') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}">

                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="title-color" for="name">{{ translate('description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor form-control" name="description[]" id="editoren"></textarea>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="title-color" for="name">{{ translate('Festivals_and_events') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor form-control" name="festivals_and_events[]"></textarea>
                                </div>
                            </div>
                            @endforeach

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                    <div>
                                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('cities_image') }}</label>
                                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_cities_image') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="custom_upload_input">
                                                        <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_gst_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                            <i class="tio-delete"></i>
                                                        </span>
                                                        <div class="img_area_with_preview position-absolute z-index-2">
                                                            <img id="pre_gst_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
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
                                <div class="col-md-8">
                                <div class="additional_image_column">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                    <div>
                                                        <label for="name"
                                                            class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_slider_images') }}</label>
                                                        <span
                                                            class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                            title="{{ translate('Upload_any_slider_image_here') }}.">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span>
                                                    </div>
                                                </div>
                                                <p class="text-muted">{{ translate('upload_slider_images') }}</p>
                                                <div class="row g-2" id="additional_Image_Section">
                                                    <div class="col-sm-12 col-md-4">
                                                        <div class="custom_upload_input position-relative border-dashed-2">
                                                            <input type="file" name="images[]" class="custom-upload-input-file action-add-more-image" data-index="1" data-imgpreview="additional_Image_1" accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*" data-target-section="#additional_Image_Section">

                                                            <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                                <i class="tio-delete"></i>
                                                            </span>

                                                            <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-75">
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
                            </div>

                        </div>
                        <!-- Buttons for form actions -->
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>
<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="message-upload-image" data-text="{{ translate('Upload_Image') }}"></span>

@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
@endpush

@push('script_2')
<script>
    $(document).ready(function() {
        const inputElement = $('input[type="text"].form-control.getAddress_google[data-option="0"]')[0]; // Get the input element itself
        const autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['(cities)']
        });

        $(".getAddress_google").on('input', function() {
            var check = $(this).attr('data-option');
            if (check == 0) {
                if (($(this).val()).length < 2) {
                    $('input[type="hidden"][name="latitude"]').val('');
                    $('input[type="hidden"][name="longitude"]').val('');
                    $('.country_select_geolocation').val('').trigger('change.select2');
                    $('.country_id_short_name').val('');
                    $('.state_select_geolocation').val('').trigger('change.select2');
                   
                }
                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        $(this).val('');
                        return;
                    }
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    $(this).val(place.name);
                    $('input[type="hidden"][name="latitude"]').val(lat);
                    $('input[type="hidden"][name="longitude"]').val(lng);
                    let state = '';
                    let country ='';
                    let country_name ='';
                    for (const component of place.address_components) {
                        const componentType = component.types[0];
                        switch (componentType) {
                            case 'administrative_area_level_1':
                                state = component.long_name;
                                break;
                            case 'country':
                                country = component.long_name;
                                country_name = component.short_name;
                                break;
                        }
                    }
                    const countrySelect = $('.country_select_geolocation');
                let countryOption = null;
                countrySelect.find('option').each(function() {
                    if ($(this).text().trim().toLowerCase() === country.toLowerCase()) {
                        countryOption = $(this);
                        return false; 
                    }
                });
                if (countryOption) {
                    countrySelect.val(countryOption.val()).trigger('change.select2');
                } else {
                    const newCountryOption = new Option(country, country, true, true);
                    countrySelect.append(newCountryOption).trigger('change.select2');
                }
                $('.country_id_short_name').val(country_name);

                
                //
                const stateSelect = $('.state_select_geolocation');
                let stateOption = null;
                stateSelect.find('option').each(function() {
                    if ($(this).text().trim().toLowerCase() === state.toLowerCase()) {
                        stateOption = $(this);
                        return false; 
                    }
                });
                if (stateOption) {
                    stateSelect.val(stateOption.val()).trigger('change.select2');
                } else {
                    const newstateOption = new Option(state, state, true, true);
                    stateSelect.append(newstateOption).trigger('change.select2');
                }
                

                });
            }
        });
    });
</script>


@endpush