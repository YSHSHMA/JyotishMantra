@extends('layouts.back-end.app')

@section('title', translate('add_temple'))

@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<!--<link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">-->
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<script src="https://maps.googleapis.com/maps/api/js?key={{$googleMapsApiKey}}&libraries=places"></script>
<style>
    .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
            {{ translate('add_temple') }}
        </h2>
    </div>

    <form class="product-form text-start" action="{{ route('admin.temple.add') }}" method="POST" enctype="multipart/form-data" id="services_form">
        @csrf
        <div class="card">
            <div class="px-4 pt-3">
                <ul class="nav nav-tabs w-fit-content mb-4">
                    @foreach ($languages as $lang)
                    <li class="nav-item">
                        <span class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer" id="{{ $lang }}-link">{{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body">
                <input type="hidden" name="longitude">
                <input type="hidden" name="latitude">

                @foreach ($languages as $key=>$lang)
                <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form" id="{{ $lang }}-form">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_cities">{{ translate('temple_name') }}
                                    ({{ strtoupper($lang) }})
                                </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="name[]" id="{{ $lang }}_name" class="form-control getAddress_google" placeholder="{{ translate('temple_name') }}" data-option="{{$key}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color">{{ translate('temple_category_name') }} </label>
                                <select name="category_id" class="js-select2-custom form-control temple_category_data" placeholder="{{ translate('temple_category_name') }}">
                                    @if($templecategory)
                                    @foreach ($templecategory as $key => $value)
                                    <option value="{{ $value['id']}}"> {{$value['name']}} </option>

                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('country_select') }}</label>
                                <select class="js-select2-custom form-control action-get-request-onchange country_select_geolocation" name="country_id" required>
                                    <option value="{{ old('id') }}" selected disabled>{{ translate('country_select') }}</option>
                                    @foreach ($countryList as $countryItem)
                                    <option value="{{ $countryItem['id'] }}">{{ $countryItem['name'] }}</option>
                                    @endforeach
                                </select>
                                <input type='hidden' class='country_id_short_name' name='country_id_short_name'>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('state_select') }}</label>
                                <select class="js-select2-custom form-control action-get-request-onchange state_select_geolocation" name="state_id" onchange="getRequestFunctionality1(this.value)" required>
                                    <option value="{{ old('id') }}" selected disabled>{{ translate('select_State') }}</option>
                                    @foreach ($stateList as $stateItem)
                                    <option value="{{ $stateItem['id'] }}">{{ $stateItem['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- DISTRICT --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="title-color">{{ translate('district_select') }}</label>
                                <select class="js-select2-custom form-control sub-districts-select district_select_geolocation"
                                        name="district_id"
                                        required>
                                    <option selected disabled>{{ translate('select_District') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ translate('cities_select') }}</label>
                                <select class="js-select2-custom form-control action-get-request-onchange1 City_select_geolocation sub-cities-select" name="city_id" required>
                                    <option value="{{ null }}" selected disabled>
                                        {{ translate('select_Cities') }}
                                    </option>
                                </select>
                                <input type='hidden' name='city_latitude' class='city_latitude'>
                                <input type='hidden' name='city_longitude' class='city_longitude'>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_short_description">{{ translate('short_description') }}
                                    ({{ strtoupper($lang) }})
                                </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="short_description[]" id="{{ $lang }}_short_description" class="form-control ckeditor" placeholder="short descscription"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_expect_details">{{ translate('expect_details') }}
                                    ({{ strtoupper($lang) }})
                                </label>
                                <textarea {{ $lang == $defaultLanguage ? 'required' : '' }} name="expect_details[]" id="{{ $lang }}_expect_details" class="form-control ckeditor" placeholder="Expect Details"></textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $lang }}_tips_details">{{ translate('tips_details') }}
                                ({{ strtoupper($lang) }})</label>
                            <textarea class="ckeditor" id="tips_details{{ $lang }}" name="tips_details[]" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $lang }}_details">{{ translate('temple_description') }}
                                ({{ strtoupper($lang) }})</label>
                            <textarea class="ckeditor" id="editor{{ $lang }}" name="details[]" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="title-color" for="{{ $lang }}_more_details">{{ translate('more_details') }}
                                ({{ strtoupper($lang) }})</label>
                            <textarea class="ckeditor" id="editor{{ $lang }}" name="more_details[]" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="title-color" for="{{ $lang }}_temple_services">{{ translate('temple_services') }}
                                ({{ strtoupper($lang) }})</label>
                            <textarea class="ckeditor" id="temple_services{{ $lang }}" name="temple_services[]" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $lang }}_temple_aarti">{{ translate('temple_aarti') }}
                                ({{ strtoupper($lang) }})</label>
                            <textarea class="ckeditor" id="temple_aarti{{ $lang }}" name="temple_aarti[]" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $lang }}_tourist_place">{{ translate('tourist_place') }}
                                ({{ strtoupper($lang) }})</label>
                            <textarea class="ckeditor" id="tourist_place{{ $lang }}" name="tourist_place[]" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="title-color" for="{{ $lang }}_temple_local_food">{{ translate('temple_local_food') }}
                                ({{ strtoupper($lang) }})</label>
                            <textarea class="ckeditor" id="temple_local_food{{ $lang }}" name="temple_local_food[]" required></textarea>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_facilities">{{ translate('facilities') }}
                                    ({{ strtoupper($lang) }})
                                </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="facilities[]" id="{{ $lang }}_facilities" class="form-control" placeholder="{{ translate('facilities') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_tips_restrictions">{{ translate('tips_&_restrictions') }}
                                    ({{ strtoupper($lang) }})
                                </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="tips_restrictions[]" id="{{ $lang }}_tips_restrictionsr" class="form-control" placeholder="{{ translate('tips__&_restrictions') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="{{ $lang }}_temple_known">{{ translate('temple_known') }}
                                    ({{ strtoupper($lang) }})
                                </label>
                                <input type="text" {{ $lang == $defaultLanguage ? 'required' : '' }} name="temple_known[]" id="{{ $lang }}temple_known" class="form-control" placeholder="{{ translate('temple_known') }}">
                            </div>
                        </div>
                    </div>

                </div>
                @endforeach


            </div>
        </div>

        <div class="card mt-3 rest-part">
            <div class="card-header">
                <div class="d-flex gap-2">
                    <i class="tio-user-big"></i>
                    <h4 class="mb-0">{{ translate('general_setup') }}</h4>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-4 col-lg-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('opening_time') }}</label>
                            <input type="text" name="opening_time" id="opentime" class="form-control" required placeholder="{{ translate('opening_time') }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('closeing_time') }}</label>
                            <input type="text" name="closeing_time" id="closetime" class="form-control" required placeholder="{{ translate('closeing_time') }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('require_time') }}</label>
                            <input type="text" name="require_time" class="form-control" required placeholder="{{ translate('require_time') }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('entry_fee') }}</label>
                            <input type="text" name="entry_fee" class="form-control" required placeholder="{{ translate('entry_fee') }}">
                        </div>
                    </div>



                    {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color d-flex align-items-center gap-2">
                                    {{ translate('search_tags') }}
                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_the_temple_search_tag_for_this_temple_that_customers_can_use_to_search_quickly') }}">
                        <img width="16" src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                    </span>
                    </label>
                    <input type="text" class="form-control" placeholder="{{ translate('enter_tag') }}" name="tags" data-role="tagsinput">
                </div>
            </div> --}}
            <div class="col-md-6">
                <div class="d-flex gap-2">
                    <i class="tio-user-big"></i>
                    <h4 class="mb-0">{{ translate('temple_video') }}</h4>
                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_the_YouTube_video_link_here._Only_the_YouTube-embedded_link_is_supported') }}.">
                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                    </span>
                </div>
                <div class="mb-3">
                    <label class="title-color mb-0">{{ translate('youtube_video_link') }}</label>
                    <span class="text-info">
                        ({{ translate('optional_please_provide_embed_link_not_direct_link') }}.)</span>
                </div>
                <input type="text" name="video_url" placeholder="{{ translate('ex') . ': https://www.youtube.com/embed/5R06LRdUCSE' }}" class="form-control">
            </div>
        </div>
</div>
</div>

<div class="mt-3 rest-part">
    <div class="row g-2">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('temple_logo') }}</label>
                                <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_temple_logo_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="custom_upload_input">
                                <input type="file" name="logo" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_img_viewer1" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                    <i class="tio-delete"></i>
                                </span>

                                <div class="img_area_with_preview position-absolute z-index-2">
                                    <img id="pre_img_viewer1" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
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
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('temple_thumbnail') }}</label>
                                <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="custom_upload_input">
                                <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                    <i class="tio-delete"></i>
                                </span>

                                <div class="img_area_with_preview position-absolute z-index-2">
                                    <img id="pre_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
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
    </div>
</div>

        <div class="mt-3 rest-part">
            <div class="row g-2">

        <div class="col-md-12">
            <div class="card mt-1 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="tio-user-big"></i>
                        <h4 class="mb-0">
                            {{ translate('seo_section') }}
                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ translate('add_meta_titles_descriptions_and_images_for_products') . ', ' . translate('this_will_help_more_people_to_find_them_on_search_engines_and_see_the_right_details_while_sharing_on_other_social_platforms') }}">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
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
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ translate('add_the_products_title_name_taglines_etc_here') . ' ' . translate('this_title_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </label>
                                <input type="text" name="meta_title" placeholder="{{ translate('meta_Title') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="title-color">
                                    {{ translate('meta_Description') }}
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ translate('write_a_short_description_of_the_InHouse_shops_product') . ' ' . translate('this_description_will_be_seen_on_Search_Engine_Results_Pages_and_while_sharing_the_products_link_on_social_platforms') . ' [ ' . translate('character_Limit') }} : 100 ]">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </label>
                                <textarea rows="4" type="text" name="meta_description" class="form-control"></textarea>
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
                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Meta Thumbnail'] }}</span>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_Meta_Image_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}.">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                            </span>
                                        </div>

                                    </div>

                                    <div>
                                        <div class="custom_upload_input">
                                            <input type="file" name="meta_image" class="custom-upload-input-file meta-img action-upload-color-image" id="" data-imgpreview="pre_meta_image_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                <img id="pre_meta_image_viewer" class="h-auto bg-white onerror-add-class-d-none" alt="" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}">
                                            </div>
                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                    <img alt="" class="w-65" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
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




<div class="row justify-content-end gap-3 mt-3 mx-1">
    <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
    <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
</div>
</form>
</div>

<span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
<span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
<span id="message-no-word" data-text="{{ translate('no') }}"></span>

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
    $('.delete_file_input').on('click', function() {
        let $parentDiv = $(this).parent().parent();
        $parentDiv.find('input[type="file"]').val('');
        $parentDiv.find('.img_area_with_preview img').addClass("d-none");
        $(this).removeClass('d-flex');
        $(this).hide();
    });
</script>

<script>
    $(document).ready(function() {
        const inputElement = $('input[type="text"].form-control.getAddress_google[data-option="0"]')[0];
        const autocomplete = new google.maps.places.Autocomplete(inputElement, {
            types: ['establishment']
        });

        function geocodeAddress(address, callback) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                address: address
            }, (results, status) => {
                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                    const location = results[0].geometry.location;
                    callback(location.lat(), location.lng());
                } else {
                    callback(null, null);
                }
            });
        }

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                $(inputElement).val('');
                return;
            }
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            $(inputElement).val(place.name);
            $('input[type="hidden"][name="latitude"]').val(lat);
            $('input[type="hidden"][name="longitude"]').val(lng);

            let city = '',
                state = '',
                country = '',
                country_name = '';
            let cityLat = '';
            let cityLng = '';
            for (const component of place.address_components) {
                const componentType = component.types[0];
                switch (componentType) {
                    case 'locality':
                        city = component.long_name;
                        geocodeAddress(city, (lat, lng) => {
                            cityLat = lat;
                            cityLng = lng;
                            $('.city_latitude').val(cityLat);
                            $('.city_longitude').val(cityLng);
                        });
                        break;
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

            getRequestFunctionality1($('.state_select_geolocation').val(), city);

        });
        $(".getAddress_google").on('input', function() {
            var check = $(this).attr('data-option');
            if (check == 0) {
                if ($(this).val().length < 2) {
                    $('input[type="hidden"][name="latitude"]').val('');
                    $('input[type="hidden"][name="longitude"]').val('');
                    $('.country_select_geolocation').val('').trigger('change.select2');
                    $('.country_id_short_name').val('');
                    $('.state_select_geolocation').val('').trigger('change.select2');
                    $('.City_select_geolocation').val('').trigger('change.select2');
                }
            }
        });
    });
</script>

<script>
    $(".temple_category_data").on('change', function() {
        $('.temple_category_data').val($(this).val()).trigger('change.select2');
    })


    function getRequestFunctionality1(id, cities_option = null) {
        $.get({
            url: "<?php echo url('admin/temple/get-cities?id=') ?>" + id,
            dataType: 'json',
            beforeSend: function() {
                $('#loading').fadeIn();
            },
            success: function(data) {
                $('.sub-cities-select').empty().append(data.select_tag);
                $('.sub-districts-select').empty().append(data.district_tag).trigger('change.select2');
                if (cities_option != null) {
                    // $('.City_select_geolocation').val(
                    //     $('.City_select_geolocation option').filter(function() {
                    //         return ($(this).text()).toUpperCase() === cities_option.toUpperCase();
                    //     }).val()
                    // ).trigger('change.select2');
                    const citiesSelect = $('.City_select_geolocation');
                    let citiesOption = null;
                    citiesSelect.find('option').each(function() {
                        if ($(this).text().trim().toLowerCase() === cities_option.toLowerCase()) {
                            citiesOption = $(this);
                            return false;
                        }
                    });
                    if (citiesOption) {
                        citiesSelect.val(citiesOption.val()).trigger('change.select2');
                    } else {
                        const newcitiesOption = new Option(cities_option, cities_option, true, true);
                        citiesSelect.append(newcitiesOption).trigger('change.select2');
                    }
                }
            },
            complete: function() {
                $('#loading').fadeOut();
            }
        })
    }
</script>


{{-- Select State and show the city --}}
{{-- $(document).ready(function() {
        alert("hello");
        $('#state_id').on('change', function() {
            var stateId = $(this).val();
            $.ajax({
                type: 'GET',
                url: '{{ url('get-cities/') }}' + stateId,
data: { state_id: stateId },
dataType: 'json',
success: function(data) {
$('#city_id').empty();
$.each(data, function(index, city) {
$('#city_id').append('<option value="' + city.id + '">' + city.city + '</option>');
});
}
});
});
}); --}}

{{-- $(document).ready(function () {
        setTimeout(function () {
            let states = $("#cities").val();
            let cities = $("#sub-cities-select").attr("data-id");
            getRequestFunctionality('{{ route('admin.temple.get-cities') }}?state_id=' + states + '&cities=' + cities, 'sub-cities-select', 'select');
}, 100)
}); --}}

@endpush