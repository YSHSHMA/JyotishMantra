@extends('layouts.back-end.app')

@section('title', translate('edit_Restaurant'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

<style>
    .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }
</style>
@endpush
@section('content')

<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/videosubcategory.png') }}" alt="">
            {{ translate('edit_Restaurant') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new video subcategory -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.temple.restaurants.edit',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
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
                            <input type="hidden" name='latitude' value="{{ $getData['latitude']}}">
                            <input type="hidden" name="longitude" value="{{ $getData['longitude']}}" >
                            @foreach($language as $key=>$lang)
                            <?php
                if (count($getData['translations'])) {
                    $translate = [];
                    foreach ($getData['translations'] as $translation) {
                        if ($translation->locale == $lang && $translation->key == 'restaurant_name') {
                            $translate[$lang]['restaurant_name'] = $translation->value;
                        }
                        if ($translation->locale == $lang && $translation->key == 'description') {
                            $translate[$lang]['description'] = $translation->value;
                        }
                        if ($translation->locale == $lang && $translation->key == 'menu_highlights') {
                            $translate[$lang]['menu_highlights'] = $translation->value;
                        }
                        if ($translation->locale == $lang && $translation->key == 'more_details') {
                            $translate[$lang]['more_details'] = $translation->value;
                        }
                        
                    }
                }?>
                            <div class="row {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                <div class='col-md-6 form-group'>
                                    <label class="title-color" for="restaurant_name">{{ translate('restaurant_name') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="restaurant_name[]" class="form-control getAddress_google" id="{{$lang}}_restaurant_name" autocomplete="off"  value="{{ $translate[$lang]['restaurant_name'] ?? $getData['restaurant_name'] }}" placeholder="{{ translate('restaurant_name') }}" required="{{ $lang == $defaultLanguage? 'required':''}}" data-option='{{$key}}'>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="name">{{ translate('Country_name') }}<span class="text-danger">*</span></label>
                                    <select name="country_id" class="js-select2-custom form-control Country_select_geolocation">
                                        <option value=""></option>
                                        @foreach($country as $va)
                                        <option value="{{$va->id}}" {{ (($getData['country_id'] == $va->id)?"selected":"") }}>{{ $va->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="name">{{ translate('state_name') }}<span class="text-danger">*</span></label>
                                    <select name="state_id" class="js-select2-custom form-control state_select_geolocation">
                                        <option value=""></option>
                                        @foreach($stateList as $va)
                                        <option value="{{$va->id}}" {{ (($getData['state_id'] == $va->id)?"selected":"") }}>{{ $va->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="name">{{ translate('City_name') }}<span class="text-danger">*</span></label>
                                    <select name="cities_id" class="js-select2-custom form-control City_select_geolocation">
                                        <option value=""></option>
                                        @foreach($citiesList as $va)
                                        <option value="{{$va->id}}" {{ (($getData['cities_id'] == $va->id)?"selected":"") }}>{{ $va->city}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="name">{{ translate('zipcode') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="zipcode" value="{{ $getData['zipcode'] }}" class="form-control zipcode" id="{{$lang}}_zipcode" placeholder="{{ translate('Zipcode') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="phone_number">{{ translate('phone_number') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="phone_no" value="{{ $getData['phone_no'] }}" class="form-control showallData" data-point='1' id="{{$lang}}_phone_number" placeholder="{{ translate('phone_number') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}"  oninput="this.value = this.value.replace(/\D/g, '').slice(0, 12)">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="email">{{ translate('email_id') }}<span class="text-danger">*</span></label>
                                    <input type="email" name="email_id" value="{{ $getData['email_id']}}" class="form-control showallData" data-point='2' id="{{$lang}}_email" placeholder="{{ translate('email_id') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}">
                                    <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="email">{{ translate('Website_link') }}<span class="text-danger">*</span></label>
                                    <input type="text" name="website_link" value="{{ $getData['website_link']}}" class="form-control showallData" data-point='3' id="{{$lang}}Website_link" placeholder="{{ translate('Website_link') }}" autocomplete="off" required="{{$lang == $defaultLanguage? 'required':''}}">
                                    
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="name">{{ translate('open_time') }}<span class="text-danger">*</span></label>
                                    <input type='text' class="form-control opentime" value="{{ $getData['open_time']}}" readonly name="open_time">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="name">{{ translate('close_time') }}<span class="text-danger">*</span></label>
                                    <input type='text' class="form-control closetime" readonly value="{{ $getData['close_time'] }}" name="close_time">
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="title-color" for="name">{{ translate('youtube_video') }}<span class="text-danger">*</span></label>
                                    <input class="form-control showallData" data-point='4' name="youtube_video" value="{{ $getData['youtube_video'] }}"  placeholder="{{ translate('youtube_video') }}" require>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="name">{{ translate('description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor form-control" name="description[]" id="editoren">{{ $translate[$lang]['description'] ?? $getData['description'] }}</textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="title-color" for="name">{{ translate('Menu_Highlights') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor form-control" name="menu_highlights[]" >{{ $translate[$lang]['menu_highlights'] ?? $getData['menu_highlights'] }}</textarea>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="title-color" for="name">{{ translate('more_details') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                    <textarea class="ckeditor form-control" name="more_details[]">{{ $translate[$lang]['more_details'] ?? $getData['more_details'] }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                <div>
                                                    <label for="name"
                                                        class="title-color text-capitalize font-weight-bold mb-0">{{ translate('restaurant_thumbnail') }}</label>
                                                    <span
                                                        class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                        title="{{ translate('add_your_restaurant_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="custom_upload_input">
                                                    <input type="file" name="image"
                                                        class="custom-upload-input-file action-upload-color-image" id=""
                                                        data-imgpreview="pre_img_viewer"
                                                        accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                                    <span
                                                        class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                        <i class="tio-delete"></i>
                                                    </span>

                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                        <img id="pre_img_viewer" class="h-auto aspect-1 bg-white  {{ (($getData['image'])?'':'d-none') }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/temple/restaurant/' .$getData['image'] , type: 'backend-product') }}" alt="">
                                                    </div>
                                                    <div
                                                        class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                                            <img alt="" class="w-75"
                                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
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
<!-- Hidden HTML element for delete route -->
<span id="route-admin-videosubcategory-delete" data-url="{{ route('admin.videosubcategory.delete') }}"></span>
<!-- Toast message for video deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="video-deleted-message" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            {{ translate('Video deleted') }}
        </div>
    </div>
</div>


@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    "use strict";
    let getYesWord = $('#message-yes-word').data('text');
    let getCancelWord = $('#message-cancel-word').data('text');
    let messageAreYouSureDeleteThis = $('#message-are-you-sure-delete-this').data('text');
    let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');

    $('.videocategory-delete-button').on('click', function() {
        let videocategoryId = $(this).attr("id");
        Swal.fire({
            title: messageAreYouSureDeleteThis,
            text: messageYouWillNotAbleRevertThis,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: getYesWord,
            cancelButtonText: getCancelWord,
            type: 'warning',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                // Send AJAX request to delete video category
                $.ajax({
                    url: $('#route-admin-videocategory-delete').data('url'),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Include CSRF token
                        id: videocategoryId
                    },
                    success: function(response) {
                        // Show success message
                        toastr.success('Videocategory deleted successfully', '', {
                            positionClass: 'toast-bottom-left'
                        });
                        // Reload the page
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            }
        });
    });
</script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
@endpush

@push('script_2')
<script>
     // time picker
     $('.opentime').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });
    $('.closetime').timepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        footer: true
    });
    $(document).ready(function() {
        // Initialize autocomplete with the input element for hotel name search
        const inputElement = $('input[type="text"].form-control.getAddress_google[data-option="0"]')[0];
        const autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['establishment']
        });

        $(".getAddress_google").on('input', function() {
            var check = $(this).attr('data-option');
            if (check == 0) {
                if ($(this).val().length < 2) {
                    $('input[type="hidden"][name="latitude"]').val('');
                    $('input[type="hidden"][name="longitude"]').val('');


                    $('.state_select_geolocation').val('').trigger('change.select2');
                    $('.City_select_geolocation').val('').trigger('change.select2');
                    $('.Country_select_geolocation').val('').trigger('change.select2');
                    $('input[type="text"][name="zipcode"].form-control.zipcode').val('');
                }

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        $(this).val('');
                        return;
                    }

                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                   
                    $('input[type="hidden"][name="latitude"]').val(lat);
                    $('input[type="hidden"][name="longitude"]').val(lng);

                    // Initialize variables for the address components
                    let zipcode = '',
                        city = '',
                        state = '',
                        country = '';

                    // Extract address components
                    for (const component of place.address_components) {
                        const componentType = component.types[0];
                        switch (componentType) {
                            case 'postal_code':
                                zipcode = component.long_name;
                                break;
                            case 'locality':
                                city = component.long_name;
                                break;
                            case 'administrative_area_level_1':
                                state = component.long_name;
                                break;
                            case 'country':
                                country = component.long_name;
                                break;
                        }
                    }


                   
                    $('input[type="text"][name="zipcode"].form-control.zipcode').val(zipcode);
                    $('.Country_select_geolocation').val($('.Country_select_geolocation option').filter(function() {
                        return ($(this).text()).toUpperCase() === country.toUpperCase();
                    }).val()).trigger('change.select2');
                    $('.City_select_geolocation').val(
                        $('.City_select_geolocation option').filter(function() {
                            return ($(this).text()).toUpperCase() === city.toUpperCase();
                        }).val()
                    ).trigger('change.select2');

                    $('.state_select_geolocation').val(
                        $('.state_select_geolocation option').filter(function() {
                            return ($(this).text()).toUpperCase() === state.toUpperCase();
                        }).val()
                    ).trigger('change.select2');
                });
            }
        });
    });
</script>

<script>

$(".showallData").on('input',function(){
   var point = $(this).data('point');
    $(`input[class="form-control showallData"][data-point='${point}']`).val($(this).val());
})
</script>


@endpush